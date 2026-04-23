<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    private const PRICE_PER_HOUR = 450000;

    public function index(): View
    {
        $query = Reservation::query()->with('user')->latest();

        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $reservations = $query->get();

        return view('reservations.index', compact('reservations'));
    }

    public function create(): View
    {
        return view('reservations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateReservation($request);
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'Pending';
        $validated = $this->fillCalculatedReservationValues($validated);

        Reservation::create($validated);

        return redirect()->route('reservations.index')->with('success', 'Reservasi berhasil dibuat dan menunggu konfirmasi admin.');
    }

    public function show(Reservation $reservation): View
    {
        $this->authorizeReservationAccess($reservation);

        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation): View
    {
        $this->authorizeReservationAccess($reservation, true);

        return view('reservations.edit', compact('reservation'));
    }

    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorizeReservationAccess($reservation, true);

        $validated = $this->validateReservation($request, false, auth()->user()->isAdmin());
        $validated = $this->fillCalculatedReservationValues($validated);

        $reservation->update($validated);

        return redirect()->route('reservations.index')->with(
            'success',
            auth()->user()->isAdmin()
                ? 'Reservasi berhasil diperbarui.'
                : 'Pesanan berhasil diperbarui dan menunggu konfirmasi admin.'
        );
    }

    public function confirm(Reservation $reservation): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat mengonfirmasi pesanan.');

        $reservation->update(['status' => 'Dikonfirmasi']);

        return redirect()->route('reservations.index')->with('success', 'Pesanan berhasil dikonfirmasi.');
    }

    public function reject(Reservation $reservation): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat menolak pesanan.');

        $reservation->update(['status' => 'Dibatalkan']);

        return redirect()->route('reservations.index')->with('success', 'Pesanan berhasil ditolak.');
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $this->authorizeReservationAccess($reservation, true);

        $reservation->delete();

        return redirect()->route('reservations.index')->with('success', 'Reservasi berhasil dihapus.');
    }

    private function validateReservation(Request $request, bool $validateFutureDate = true, bool $isAdmin = false): array
    {
        $dateRules = ['required', 'date'];

        if ($validateFutureDate) {
            $dateRules[] = 'after_or_equal:today';
        }

        $rules = [
            'nama_pemesan' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'regex:/^[0-9]+$/', 'digits_between:10,15'],
            'tanggal_reservasi' => $dateRules,
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'nama_lapangan' => ['required', 'string', 'max:255'],
            'durasi' => ['required', 'numeric', 'min:1'],
            'harga' => ['required', 'numeric', 'min:0'],
        ];

        if ($isAdmin) {
            $rules['status'] = ['required', 'in:' . implode(',', array_keys(Reservation::STATUS_OPTIONS))];
        }

        return $request->validate($rules, [
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'no_hp.digits_between' => 'Nomor HP harus terdiri dari 10 sampai 15 digit.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'status.in' => 'Status reservasi tidak valid.',
            'harga.numeric' => 'Harga harus berupa angka.',
        ]);
    }

    private function fillCalculatedReservationValues(array $validated): array
    {
        $start = strtotime($validated['jam_mulai']);
        $end = strtotime($validated['jam_selesai']);
        $durationInHours = ($end - $start) / 3600;

        $validated['durasi'] = $durationInHours;
        $validated['harga'] = $durationInHours * self::PRICE_PER_HOUR;

        return $validated;
    }

    private function authorizeReservationAccess(Reservation $reservation, bool $forUpdate = false): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($reservation->user_id !== $user->id) {
            throw new AuthorizationException('Anda tidak dapat mengakses reservasi ini.');
        }

        if ($forUpdate && $reservation->status !== 'Pending') {
            throw new AuthorizationException('Pesanan yang sudah diproses admin tidak dapat diubah.');
        }
    }
}

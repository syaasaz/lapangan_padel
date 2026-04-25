<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LapanganController extends Controller
{
    public function index(): View
    {
        $lapangans = Lapangan::latest()->get();

        return view('lapangans.index', compact('lapangans'));
    }

    public function create(): View
    {
        return view('lapangans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Lapangan::create($this->validateLapangan($request));

        return redirect()->route('lapangans.index')->with('success', 'Data lapangan berhasil ditambahkan.');
    }

    public function edit(Lapangan $lapangan): View
    {
        return view('lapangans.edit', compact('lapangan'));
    }

    public function update(Request $request, Lapangan $lapangan): RedirectResponse
    {
        $lapangan->update($this->validateLapangan($request));

        return redirect()->route('lapangans.index')->with('success', 'Data lapangan berhasil diperbarui.');
    }

    public function destroy(Lapangan $lapangan): RedirectResponse
    {
        $lapangan->delete();

        return redirect()->route('lapangans.index')->with('success', 'Data lapangan berhasil dihapus.');
    }

    private function validateLapangan(Request $request): array
    {
        return $request->validate([
            'nama_lapangan' => ['required', 'string', 'max:255'],
            'jenis_lapangan' => ['required', 'string', 'max:255'],
            'harga_per_jam' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:' . implode(',', array_keys(Lapangan::STATUS_OPTIONS))],
        ], [
            'nama_lapangan.required' => 'Nama lapangan wajib diisi.',
            'jenis_lapangan.required' => 'Jenis lapangan wajib diisi.',
            'harga_per_jam.required' => 'Harga per jam wajib diisi.',
        ]);
    }
}

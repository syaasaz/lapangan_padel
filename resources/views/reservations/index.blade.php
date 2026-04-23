@extends('layouts.app')

@section('content')
    @php
        $isAdmin = auth()->user()->isAdmin();
    @endphp

    <section class="page-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="page-title">Daftar Reservasi</h1>
                <p class="page-subtitle mb-0">
                    {{ $isAdmin ? 'Admin meninjau dan mengonfirmasi pesanan yang masuk.' : 'Pantau status pesanan lapangan yang sudah Anda buat.' }}
                </p>
            </div>
            <div class="page-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-light">Dashboard</a>
                <a href="{{ route('reservations.create') }}" class="btn btn-primary">{{ $isAdmin ? 'Tambah Reservasi' : 'Pesan Lapangan' }}</a>
            </div>
        </div>
    </section>

    <section class="content-card">
        <div class="table-responsive">
            <table class="table table-clean">
                <thead>
                    <tr>
                        <th>No</th>
                        @if ($isAdmin)
                            <th>User</th>
                        @endif
                        <th>Nama Pemesan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Lapangan</th>
                        <th>Durasi</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservations as $reservation)
                        @php
                            $statusClass = match ($reservation->status) {
                                'Pending' => 'status-pending',
                                'Dikonfirmasi' => 'status-confirmed',
                                'Selesai' => 'status-finished',
                                'Dibatalkan' => 'status-cancelled',
                                default => 'status-pending',
                            };
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            @if ($isAdmin)
                                <td>{{ $reservation->user->name ?? '-' }}</td>
                            @endif
                            <td>
                                <div class="fw-semibold">{{ $reservation->nama_pemesan }}</div>
                                <small class="text-muted">{{ $reservation->no_hp }}</small>
                            </td>
                            <td>{{ $reservation->tanggal_reservasi->format('d M Y') }}</td>
                            <td>{{ $reservation->jam_mulai->format('H:i') }} - {{ $reservation->jam_selesai->format('H:i') }}</td>
                            <td>{{ $reservation->nama_lapangan }}</td>
                            <td>{{ $reservation->durasi }} jam</td>
                            <td>Rp {{ number_format((float) $reservation->harga, 0, ',', '.') }}</td>
                            <td><span class="status-pill {{ $statusClass }}">{{ $reservation->status }}</span></td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end flex-wrap gap-2">
                                    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-light btn-sm">Detail</a>

                                    @if ($isAdmin)
                                        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-primary btn-sm">Kelola</a>
                                        @if ($reservation->status === 'Pending')
                                            <form action="{{ route('reservations.confirm', $reservation) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm">Konfirmasi</button>
                                            </form>
                                            <form action="{{ route('reservations.reject', $reservation) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Tolak</button>
                                            </form>
                                        @endif
                                    @elseif ($reservation->status === 'Pending')
                                        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <button
                                            type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cancelReservationModal"
                                            data-cancel-url="{{ route('reservations.destroy', $reservation) }}"
                                            data-reservation-name="{{ $reservation->nama_pemesan }}"
                                        >
                                            Batalkan
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 10 : 9 }}" class="text-center text-muted-soft py-4">Belum ada reservasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @unless ($isAdmin)
        <div class="modal fade" id="cancelReservationModal" tabindex="-1" aria-labelledby="cancelReservationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-sm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelReservationModalLabel">Batalkan Pesanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Apakah Anda ingin membatalkan pesanan ini?</p>
                        <p class="text-muted mb-0" id="cancelReservationText"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                        <form method="POST" id="cancelReservationForm" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endunless
@endsection

@unless ($isAdmin)
    @push('scripts')
        <script>
            (() => {
                const cancelModal = document.getElementById('cancelReservationModal');

                if (!cancelModal) {
                    return;
                }

                const cancelForm = document.getElementById('cancelReservationForm');
                const cancelText = document.getElementById('cancelReservationText');

                cancelModal.addEventListener('show.bs.modal', (event) => {
                    const trigger = event.relatedTarget;

                    if (!trigger) {
                        return;
                    }

                    cancelForm.action = trigger.getAttribute('data-cancel-url') || '';
                    cancelText.textContent = 'Pesanan atas nama ' + (trigger.getAttribute('data-reservation-name') || '-') + ' akan dihapus dari daftar Anda.';
                });
            })();
        </script>
    @endpush
@endunless

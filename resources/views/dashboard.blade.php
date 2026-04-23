@extends('layouts.app')

@section('content')
    <section class="page-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="page-title">{{ $isAdmin ? 'Dashboard Admin' : 'Dashboard' }}</h1>
                <p class="page-subtitle mb-0">
                    {{ $isAdmin ? 'Ringkasan data reservasi yang masuk ke sistem.' : 'Ringkasan reservasi yang terkait dengan akun Anda.' }}
                </p>
            </div>
            <div class="page-actions">
                <a href="{{ route('reservations.index') }}" class="btn btn-light">Lihat Reservasi</a>
                <a href="{{ route('reservations.create') }}" class="btn btn-primary">Tambah Reservasi</a>
            </div>
        </div>
    </section>

    <section class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="section-label">{{ $isAdmin ? 'Total Reservasi' : 'Reservasi Saya' }}</div>
                <div class="stats-value">{{ $totalReservations }}</div>
                <div class="text-muted-soft mt-2">Total data reservasi yang tersedia saat ini.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="section-label">Pending</div>
                <div class="stats-value">{{ $pendingReservations }}</div>
                <div class="text-muted-soft mt-2">Reservasi yang masih menunggu proses.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="section-label">Dikonfirmasi</div>
                <div class="stats-value">{{ $confirmedReservations }}</div>
                <div class="text-muted-soft mt-2">Reservasi yang sudah disetujui.</div>
            </div>
        </div>
    </section>

    <section class="content-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <div class="section-label">Data Terbaru</div>
                <h2 class="h4 mb-0">{{ $isAdmin ? 'Reservasi Terbaru' : 'Reservasi Terbaru Saya' }}</h2>
            </div>
            <a href="{{ route('reservations.index') }}" class="btn btn-light">Lihat Semua</a>
        </div>

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
                        <th>Lapangan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestReservations as $reservation)
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
                            <td>{{ $reservation->nama_pemesan }}</td>
                            <td>{{ $reservation->tanggal_reservasi->format('d M Y') }}</td>
                            <td>{{ $reservation->nama_lapangan }}</td>
                            <td><span class="status-pill {{ $statusClass }}">{{ $reservation->status }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 6 : 5 }}" class="text-center text-muted-soft py-4">Belum ada data reservasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

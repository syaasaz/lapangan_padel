@extends('layouts.app')

@section('content')
    @php
        $statusClass = match ($reservation->status) {
            'Pending' => 'status-pending',
            'Dikonfirmasi' => 'status-confirmed',
            'Selesai' => 'status-finished',
            'Dibatalkan' => 'status-cancelled',
            default => 'status-pending',
        };
    @endphp

    <section class="page-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="page-title">Detail Reservasi</h1>
                <p class="page-subtitle mb-0">Admin dapat memeriksa detail reservasi, mengubah data, dan memproses status dari halaman ini.</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('reservations.index') }}" class="btn btn-light">Kembali</a>
                <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-primary">Kelola</a>
                @if ($reservation->status === 'Pending')
                    <form action="{{ route('reservations.confirm', $reservation) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">Konfirmasi</button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <section class="detail-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <div class="section-label">Nama Pemesan</div>
                <h2 class="h4 mb-0">{{ $reservation->nama_pemesan }}</h2>
            </div>
            <span class="status-pill {{ $statusClass }}">{{ $reservation->status }}</span>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Pemilik Akun</div>
                <div class="detail-value">{{ $reservation->user->name ?? '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Nomor HP</div>
                <div class="detail-value">{{ $reservation->no_hp }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Tanggal Reservasi</div>
                <div class="detail-value">{{ $reservation->tanggal_reservasi->format('d M Y') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Jam Main</div>
                <div class="detail-value">{{ $reservation->jam_mulai->format('H:i') }} - {{ $reservation->jam_selesai->format('H:i') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Nama Lapangan</div>
                <div class="detail-value">{{ $reservation->nama_lapangan }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Jenis Lapangan</div>
                <div class="detail-value">{{ $reservation->lapangan->jenis_lapangan ?? '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Durasi</div>
                <div class="detail-value">{{ $reservation->durasi }} jam</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Harga</div>
                <div class="detail-value">Rp {{ number_format((float) $reservation->harga, 0, ',', '.') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">{{ $reservation->status }}</div>
            </div>
        </div>
    </section>
@endsection

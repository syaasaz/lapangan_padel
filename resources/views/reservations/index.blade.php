@extends('layouts.app')

@section('content')
    <section class="page-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="page-title">Daftar Reservasi</h1>
                <p class="page-subtitle mb-0">Admin dapat menambah, mengubah, mengonfirmasi, atau membatalkan reservasi dari halaman ini.</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-light">Dashboard</a>
                <a href="{{ route('reservations.create') }}" class="btn btn-primary">Tambah Reservasi</a>
            </div>
        </div>
    </section>

    <section class="content-card">
        <div class="table-responsive">
            <table class="table table-clean">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>User</th>
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
                            <td>{{ $reservation->user->name ?? '-' }}</td>
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
                                    <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-primary btn-sm">Edit</a>
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
                                    <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus reservasi ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted-soft py-4">Belum ada reservasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

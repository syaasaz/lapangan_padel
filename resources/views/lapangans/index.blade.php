@extends('layouts.app')

@section('content')
    <section class="page-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="page-title">Data Lapangan</h1>
                <p class="page-subtitle mb-0">Kelola daftar lapangan dan tarif sewa yang tersedia untuk sistem reservasi.</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-light">Dashboard</a>
                <a href="{{ route('lapangans.create') }}" class="btn btn-primary">Tambah Lapangan</a>
            </div>
        </div>
    </section>

    <section class="content-card">
        <div class="table-responsive">
            <table class="table table-clean">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lapangan</th>
                        <th>Jenis</th>
                        <th>Harga/Jam</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lapangans as $lapangan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $lapangan->nama_lapangan }}</td>
                            <td>{{ $lapangan->jenis_lapangan }}</td>
                            <td>Rp {{ number_format((float) $lapangan->harga_per_jam, 0, ',', '.') }}</td>
                            <td>
                                <span class="status-pill {{ $lapangan->status === 'tersedia' ? 'status-confirmed' : 'status-cancelled' }}">
                                    {{ \App\Models\Lapangan::STATUS_OPTIONS[$lapangan->status] }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <a href="{{ route('lapangans.edit', $lapangan) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form action="{{ route('lapangans.destroy', $lapangan) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus data lapangan ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted-soft py-4">Belum ada data lapangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('content')
    <section class="page-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="page-title">Edit Lapangan</h1>
                <p class="page-subtitle mb-0">Perbarui informasi lapangan dan status ketersediaannya.</p>
            </div>
            <a href="{{ route('lapangans.index') }}" class="btn btn-light">Kembali</a>
        </div>
    </section>

    <section class="form-card">
        <form method="POST" action="{{ route('lapangans.update', $lapangan) }}">
            @csrf
            @method('PUT')
            @include('lapangans.form', ['lapangan' => $lapangan])

            <div class="page-actions mt-4">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('lapangans.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </section>
@endsection

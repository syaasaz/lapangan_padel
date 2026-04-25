@extends('layouts.app')

@section('content')
    <section class="page-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="page-title">Tambah Lapangan</h1>
                <p class="page-subtitle mb-0">Masukkan data lapangan baru yang bisa dipilih pada saat booking.</p>
            </div>
            <a href="{{ route('lapangans.index') }}" class="btn btn-light">Kembali</a>
        </div>
    </section>

    <section class="form-card">
        <form method="POST" action="{{ route('lapangans.store') }}">
            @csrf
            @include('lapangans.form')

            <div class="page-actions mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('lapangans.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </section>
@endsection

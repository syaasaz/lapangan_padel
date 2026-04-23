@extends('layouts.app')

@section('content')
    <section class="page-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="page-title">Pesan Lapangan</h1>
                <p class="page-subtitle mb-0">Isi detail pesanan Anda. Durasi dan harga akan dihitung otomatis dari jam bermain.</p>
            </div>
            <a href="{{ route('reservations.index') }}" class="btn btn-light">Kembali ke Daftar</a>
        </div>
    </section>

    <section class="form-card">
        <form method="POST" action="{{ route('reservations.store') }}">
            @csrf
            @include('reservations.form')

            <div class="page-actions mt-4">
                <button type="submit" class="btn btn-primary">Kirim Pesanan</button>
                <a href="{{ route('reservations.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </section>
@endsection

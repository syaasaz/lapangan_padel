@extends('layouts.app')

@section('content')
    <section class="page-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="page-title">Kelola Reservasi</h1>
                <p class="page-subtitle mb-0">Admin dapat mengubah detail reservasi dan memperbarui statusnya dari halaman ini.</p>
            </div>
            <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-light">Lihat Detail</a>
        </div>
    </section>

    <section class="form-card">
        <form method="POST" action="{{ route('reservations.update', $reservation) }}">
            @csrf
            @method('PUT')
            @include('reservations.form', ['reservation' => $reservation])

            <div class="page-actions mt-4">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </section>
@endsection

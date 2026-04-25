@php
    $selectedHargaPerJam = old('harga_per_jam', isset($lapangan) ? (string) (float) $lapangan->harga_per_jam : '');
@endphp

<div class="row g-4">
    <div class="col-md-6">
        <label for="nama_lapangan" class="form-label">Nama Lapangan</label>
        <input type="text" class="form-control @error('nama_lapangan') is-invalid @enderror" id="nama_lapangan" name="nama_lapangan" value="{{ old('nama_lapangan', $lapangan->nama_lapangan ?? '') }}" required>
        @error('nama_lapangan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="jenis_lapangan" class="form-label">Jenis Lapangan</label>
        <input type="text" class="form-control @error('jenis_lapangan') is-invalid @enderror" id="jenis_lapangan" name="jenis_lapangan" value="{{ old('jenis_lapangan', $lapangan->jenis_lapangan ?? '') }}" required>
        @error('jenis_lapangan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="harga_per_jam_tampil" class="form-label">Harga Per Jam</label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="text" class="form-control @error('harga_per_jam') is-invalid @enderror" id="harga_per_jam_tampil" value="{{ $selectedHargaPerJam !== '' ? number_format((float) $selectedHargaPerJam, 0, ',', '.') : '' }}" inputmode="numeric" placeholder="450.000" required>
        </div>
        <input type="hidden" id="harga_per_jam" name="harga_per_jam" value="{{ $selectedHargaPerJam }}">
        @error('harga_per_jam')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">Status</label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            @foreach (\App\Models\Lapangan::STATUS_OPTIONS as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $lapangan->status ?? 'tersedia') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            const visiblePrice = document.getElementById('harga_per_jam_tampil');
            const hiddenPrice = document.getElementById('harga_per_jam');

            if (!visiblePrice || !hiddenPrice) {
                return;
            }

            function formatRupiahInput(value) {
                const digits = value.replace(/\D/g, '');

                if (!digits) {
                    return '';
                }

                return new Intl.NumberFormat('id-ID').format(Number(digits));
            }

            visiblePrice.addEventListener('input', () => {
                const digits = visiblePrice.value.replace(/\D/g, '');

                hiddenPrice.value = digits;
                visiblePrice.value = formatRupiahInput(visiblePrice.value);
            });
        })();
    </script>
@endpush

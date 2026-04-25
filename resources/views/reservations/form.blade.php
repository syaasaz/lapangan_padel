@php
    $isEdit = isset($reservation);
    $isAdmin = auth()->user()->isAdmin();
    $selectedDuration = old('durasi', isset($reservation) ? rtrim(rtrim(number_format((float) $reservation->durasi, 2, '.', ''), '0'), '.') : '');
    $selectedPrice = old('harga', isset($reservation) ? (string) (float) $reservation->harga : '');
    $selectedLapanganId = old('lapangan_id', $reservation->lapangan_id ?? '');
@endphp

<div class="row g-4">
    <div class="col-md-6">
        <label for="nama_pemesan" class="form-label">Nama Pemesan</label>
        <input type="text" class="form-control @error('nama_pemesan') is-invalid @enderror" id="nama_pemesan" name="nama_pemesan" value="{{ old('nama_pemesan', $reservation->nama_pemesan ?? '') }}" required>
        @error('nama_pemesan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="no_hp" class="form-label">No HP</label>
        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', $reservation->no_hp ?? '') }}" required>
        @error('no_hp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="tanggal_reservasi" class="form-label">Tanggal Reservasi</label>
        <input type="date" class="form-control @error('tanggal_reservasi') is-invalid @enderror" id="tanggal_reservasi" name="tanggal_reservasi" value="{{ old('tanggal_reservasi', isset($reservation) ? $reservation->tanggal_reservasi->format('Y-m-d') : '') }}" required>
        @error('tanggal_reservasi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="jam_mulai" class="form-label">Jam Mulai</label>
        <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', isset($reservation) ? $reservation->jam_mulai->format('H:i') : '') }}" required>
        @error('jam_mulai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="jam_selesai" class="form-label">Jam Selesai</label>
        <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', isset($reservation) ? $reservation->jam_selesai->format('H:i') : '') }}" required>
        @error('jam_selesai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="lapangan_id" class="form-label">Pilih Lapangan</label>
        <select class="form-select @error('lapangan_id') is-invalid @enderror" id="lapangan_id" name="lapangan_id" required>
            <option value="">Pilih lapangan</option>
            @foreach ($lapangans as $lapangan)
                <option
                    value="{{ $lapangan->id }}"
                    data-price="{{ (float) $lapangan->harga_per_jam }}"
                    @selected((string) $selectedLapanganId === (string) $lapangan->id)
                >
                    {{ $lapangan->nama_lapangan }} - {{ $lapangan->jenis_lapangan }} ({{ \App\Models\Lapangan::STATUS_OPTIONS[$lapangan->status] }})
                </option>
            @endforeach
        </select>
        @error('lapangan_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label for="durasi_tampil" class="form-label">Durasi</label>
        <input type="text" class="form-control @error('durasi') is-invalid @enderror" id="durasi_tampil" value="{{ $selectedDuration !== '' ? $selectedDuration . ' jam' : '' }}" readonly>
        <input type="hidden" id="durasi" name="durasi" value="{{ $selectedDuration }}">
        @error('durasi')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small class="text-muted">Durasi dihitung otomatis dari jam mulai dan jam selesai.</small>
    </div>

    <div class="col-md-3">
        <label for="harga_tampil" class="form-label">Harga</label>
        <input type="text" class="form-control @error('harga') is-invalid @enderror" id="harga_tampil" value="{{ $selectedPrice !== '' ? 'Rp ' . number_format((float) $selectedPrice, 0, ',', '.') : '' }}" readonly>
        <input type="hidden" id="harga" name="harga" value="{{ $selectedPrice }}">
        @error('harga')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small class="text-muted">Harga mengikuti tarif lapangan yang dipilih.</small>
    </div>

    <div class="col-md-6">
        @if ($isAdmin)
            <label for="status" class="form-label">Status</label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                @foreach (\App\Models\Reservation::STATUS_OPTIONS as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $reservation->status ?? 'Pending') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @else
            <label class="form-label">Status</label>
            <input type="text" class="form-control" value="{{ $isEdit ? $reservation->status : 'Pending' }}" readonly>
            <small class="text-muted">Status diproses oleh admin.</small>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            const jamMulai = document.getElementById('jam_mulai');
            const jamSelesai = document.getElementById('jam_selesai');
            const lapanganSelect = document.getElementById('lapangan_id');
            const durasiInput = document.getElementById('durasi');
            const durasiTampil = document.getElementById('durasi_tampil');
            const hargaInput = document.getElementById('harga');
            const hargaTampil = document.getElementById('harga_tampil');

            if (!jamMulai || !jamSelesai || !lapanganSelect || !durasiInput || !durasiTampil || !hargaInput || !hargaTampil) {
                return;
            }

            function formatDuration(value) {
                const normalized = Number(value);

                if (Number.isInteger(normalized)) {
                    return normalized + ' jam';
                }

                return normalized.toFixed(2).replace(/\.?0+$/, '') + ' jam';
            }

            function formatRupiah(value) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
            }

            function resetCalculatedFields() {
                durasiInput.value = '';
                durasiTampil.value = '';
                hargaInput.value = '';
                hargaTampil.value = '';
            }

            function updateCalculatedFields() {
                if (!jamMulai.value || !jamSelesai.value) {
                    resetCalculatedFields();
                    return;
                }

                const selectedOption = lapanganSelect.options[lapanganSelect.selectedIndex];
                const pricePerHour = Number(selectedOption?.getAttribute('data-price'));

                if (!selectedOption || !pricePerHour) {
                    resetCalculatedFields();
                    return;
                }

                const [startHour, startMinute] = jamMulai.value.split(':').map(Number);
                const [endHour, endMinute] = jamSelesai.value.split(':').map(Number);
                const start = (startHour * 60) + startMinute;
                const end = (endHour * 60) + endMinute;

                if (end <= start) {
                    resetCalculatedFields();
                    return;
                }

                const durationInHours = (end - start) / 60;
                const totalPrice = durationInHours * pricePerHour;

                durasiInput.value = durationInHours;
                durasiTampil.value = formatDuration(durationInHours);
                hargaInput.value = totalPrice;
                hargaTampil.value = formatRupiah(totalPrice);
            }

            jamMulai.addEventListener('input', updateCalculatedFields);
            jamSelesai.addEventListener('input', updateCalculatedFields);
            lapanganSelect.addEventListener('change', updateCalculatedFields);
            updateCalculatedFields();
        })();
    </script>
@endpush

@extends('layouts.app')

@section('title', 'Permohonan Ganti Perangkat - JagaWarga RW 02')

@section('content')
<div class="max-w-lg mx-auto py-6 space-y-6">

    <!-- Tombol Kembali -->
    <div>
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Halaman Masuk</span>
        </a>
    </div>

    <!-- Kartu Utama Formulir Ganti Perangkat -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xl space-y-5">
        
        <div class="text-center space-y-1.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-400 text-white flex items-center justify-center mx-auto shadow-md shadow-orange-500/20 text-2xl">
                📱
            </div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Permohonan Ganti Perangkat</h1>
            <p class="text-xs text-slate-500">Khusus Akun Warga RW 02 yang mengganti HP atau perangkat lama hilang/rusak</p>
        </div>

        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs leading-relaxed space-y-1">
            <div class="font-bold flex items-center gap-1">
                <span>🛡️</span>
                <span>Kebijakan Verifikasi Keamanan Data</span>
            </div>
            <p class="text-[11px] text-amber-800">
                Untuk mencegah penyalahgunaan tombol darurat kentongan, pergantian perangkat akun warga mewajibkan verifikasi <strong>Nama Ibu Kandung</strong> sesuai data kependudukan RT/RW.
            </p>
        </div>

        @if(session('warning'))
        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold text-center">
            ⚠️ {{ session('warning') }}
        </div>
        @endif

        @if($errors->any())
        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold space-y-1">
            @foreach($errors->all() as $error)
                <p>&bull; {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form action="{{ route('device.reset.post') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="device_id" id="hidden-device-id" value="">

            <div>
                <label for="nik" class="block text-xs font-bold text-slate-700 mb-1">
                    Nomor Induk Kependudukan (NIK 16 Digit) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nik" id="nik" value="{{ old('nik') }}" required maxlength="16" minlength="16" pattern="\d{16}" placeholder="3201010101010001" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-mono">
                <p class="text-[10px] text-slate-400 mt-1">NIK kepala keluarga atau anggota keluarga yang terdaftar.</p>
            </div>

            <div>
                <label for="nama_ibu" class="block text-xs font-bold text-slate-700 mb-1">
                    Nama Ibu Kandung <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_ibu" id="nama_ibu" value="{{ old('nama_ibu') }}" required placeholder="Nama lengkap ibu kandung untuk verifikasi" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                <p class="text-[10px] text-slate-400 mt-1">Digunakan oleh Pengurus RT/RW sebagai data pencocokan keamanan.</p>
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">
                    Nomor WhatsApp / HP Saat Ini <span class="text-rose-500">*</span>
                </label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                <p class="text-[10px] text-slate-400 mt-1">Nomor aktif yang digunakan di HP baru ini.</p>
            </div>

            <div>
                <label for="alasan" class="block text-xs font-bold text-slate-700 mb-1">
                    Alasan Pergantian Perangkat (Opsional)
                </label>
                <textarea name="alasan" id="alasan" rows="2" placeholder="Contoh: HP lama rusak / ganti ponsel baru / layar pecah" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">{{ old('alasan') }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs shadow-md shadow-amber-600/20 transition cursor-pointer flex items-center justify-center gap-2">
                    <span>Kirim Permohonan ke Pengurus RW</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                </button>
            </div>
        </form>

        <div class="pt-2 border-t border-slate-100 text-center">
            <p class="text-[11px] text-slate-400">
                Setelah mengirimkan permohonan, Pengurus RT atau RW akan memverifikasi permohonan Anda. Anda juga dapat menghubungi Ketua RT Anda untuk persetujuan cepat.
            </p>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    (function setDeviceId() {
        let deviceId = localStorage.getItem('jagawarga_device_id');
        if (!deviceId) {
            deviceId = 'dev_' + ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
            );
            localStorage.setItem('jagawarga_device_id', deviceId);
        }
        document.getElementById('hidden-device-id').value = deviceId;
        document.cookie = "jagawarga_device_id=" + deviceId + "; path=/; max-age=" + (60 * 60 * 24 * 365) + "; SameSite=Lax";
    })();
</script>
@endpush

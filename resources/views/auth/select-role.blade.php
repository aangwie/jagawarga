@extends('layouts.app')

@section('title', 'Pilih Peran Akses - JagaWarga RW 02')

@section('content')
<div class="max-w-xl mx-auto py-6 space-y-6">

    <!-- Kartu Sambutan & Profil Pengguna -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <div class="absolute -right-6 -bottom-6 w-48 h-48 rounded-full bg-emerald-500/10 blur-2xl"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-emerald-500/20">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-500/30">
                        Akun Terverifikasi
                    </span>
                    <h1 class="text-base sm:text-lg font-black tracking-tight mt-0.5">{{ $user->name }}</h1>
                    <p class="text-xs text-slate-300">NIK: <span class="font-mono text-emerald-400">{{ $user->nik }}</span> &bull; RT {{ $user->rt_id ?? '01' }} / RW {{ $user->rw_id ?? '02' }}</p>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 text-xs font-semibold transition cursor-pointer border border-white/10 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    <span>Ganti Akun</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Notifikasi / Error -->
    @if(session('info'))
    <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs font-semibold flex items-center gap-2">
        <span>ℹ️</span>
        <span>{{ session('info') }}</span>
    </div>
    @endif

    @if(session('warning'))
    <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold flex items-center gap-2">
        <span>⚠️</span>
        <span>{{ session('warning') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold flex items-center gap-2">
        <span>🚫</span>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Alert Khusus: Penguncian Perangkat Warga -->
    @if(session('device_error'))
    @php $devErr = session('device_error'); @endphp
    <div class="p-5 rounded-3xl bg-rose-50 border-2 border-rose-300 text-rose-950 space-y-3 shadow-md animate-in fade-in">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-2xl bg-rose-200 text-rose-800 flex items-center justify-center shrink-0 text-xl font-bold">
                📱
            </div>
            <div>
                <h3 class="font-black text-sm text-rose-900">{{ $devErr['title'] ?? 'Perangkat Tidak Cocok' }}</h3>
                <p class="text-xs text-rose-800 mt-0.5 leading-relaxed">{{ $devErr['message'] ?? 'Akun warga ini terkunci pada perangkat lain.' }}</p>
                <div class="mt-2 text-[11px] bg-white/80 p-2.5 rounded-xl border border-rose-200 font-mono space-y-0.5 text-slate-700">
                    <div>Perangkat Terdaftar: <strong class="text-slate-900">{{ $devErr['registered_device'] }}</strong></div>
                    <div>Waktu Pendaftaran: <strong class="text-slate-900">{{ $devErr['registered_at'] }}</strong></div>
                </div>
            </div>
        </div>
        <div class="pt-2 border-t border-rose-200/80 flex items-center justify-between flex-wrap gap-2">
            <span class="text-[11px] text-rose-700 font-medium">Apakah Anda mengganti telepon genggam?</span>
            <a href="{{ route('device.reset') }}" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                <span>Ajukan Ganti Perangkat Sekarang</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </a>
        </div>
    </div>
    @endif

    <!-- Formulir Pemilihan Peran -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-lg space-y-5">
        <div>
            <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Langkah Terakhir</span>
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Pilih Peran untuk Sesi Ini</h2>
            <p class="text-xs text-slate-500">Anda memiliki hak akses multi-peran. Pilih salah satu peran di bawah untuk melanjutkan ke portal terkait:</p>
        </div>

        <form id="form-select-role" action="{{ route('role.select.post') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="device_id" id="hidden-device-id" value="{{ $currentDeviceId }}">

            <div class="space-y-2.5">
                @foreach($roles as $index => $r)
                <label for="role-{{ $r->name }}" class="role-card block p-4 rounded-2xl border-2 transition cursor-pointer relative overflow-hidden group {{ $index === 0 ? 'border-emerald-500 bg-emerald-50/40' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                    <input type="radio" name="role" id="role-{{ $r->name }}" value="{{ $r->name }}" class="hidden peer" {{ $index === 0 ? 'checked' : '' }} onchange="highlightRoleCard(this)">
                    
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-xl shrink-0 bg-slate-100 group-hover:scale-105 transition">
                                {{ $r->icon }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-black text-sm text-slate-900 group-hover:text-emerald-700 transition">{{ $r->display_name }}</h3>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md border {{ $r->badge_color }}">
                                        {{ strtoupper($r->name) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $r->description }}</p>

                                @if($r->name === 'warga')
                                <div class="mt-2.5 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-800 bg-emerald-100/70 px-2.5 py-1 rounded-lg w-fit">
                                    <span>🔒</span>
                                    <span>Kebijakan Keamanan: 1 Akun Warga Terikat ke 1 Perangkat</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Checkmark Indicator -->
                        <div class="check-circle w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition {{ $index === 0 ? 'border-emerald-600 bg-emerald-600 text-white' : 'text-transparent' }}">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-700/20 transition cursor-pointer flex items-center justify-center gap-2 group">
                    <span>Masuk ke Sistem dengan Peran Terpilih</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </button>
            </div>
        </form>

        <div class="text-center pt-2 border-t border-slate-100">
            <p class="text-[11px] text-slate-400">
                Anda dapat beralih peran kapan saja melalui menu profil di bagian kanan atas tanpa perlu keluar akun.
            </p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Sinkronisasi Device ID dari localStorage
    (function initDeviceBinding() {
        let deviceId = localStorage.getItem('jagawarga_device_id');
        if (!deviceId) {
            deviceId = 'dev_' + ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
            );
            localStorage.setItem('jagawarga_device_id', deviceId);
        }
        
        // Update hidden field dan cookie
        document.getElementById('hidden-device-id').value = deviceId;
        document.cookie = "jagawarga_device_id=" + deviceId + "; path=/; max-age=" + (60 * 60 * 24 * 365) + "; SameSite=Lax";
    })();

    // UI Feedback pemilihan kartu peran
    function highlightRoleCard(radio) {
        document.querySelectorAll('.role-card').forEach(card => {
            card.classList.remove('border-emerald-500', 'bg-emerald-50/40');
            card.classList.add('border-slate-200', 'bg-white');
            const circle = card.querySelector('.check-circle');
            if (circle) {
                circle.classList.remove('border-emerald-600', 'bg-emerald-600', 'text-white');
                circle.classList.add('border-slate-300', 'text-transparent');
            }
        });

        const activeCard = radio.closest('.role-card');
        if (activeCard) {
            activeCard.classList.remove('border-slate-200', 'bg-white');
            activeCard.classList.add('border-emerald-500', 'bg-emerald-50/40');
            const circle = activeCard.querySelector('.check-circle');
            if (circle) {
                circle.classList.remove('border-slate-300', 'text-transparent');
                circle.classList.add('border-emerald-600', 'bg-emerald-600', 'text-white');
            }
        }
    }
</script>
@endpush

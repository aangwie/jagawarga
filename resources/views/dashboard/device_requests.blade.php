@extends('layouts.app')

@section('title', 'Permohonan Ganti Perangkat Warga - JagaWarga RW 02')

@section('content')
<div class="space-y-6">

    <!-- Header Panel -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-5 sm:p-6 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <div class="absolute -right-6 -bottom-6 w-48 h-48 rounded-full bg-amber-500/10 blur-2xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/30 flex items-center justify-center text-2xl shadow-inner text-amber-400">
                    📱
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-[10px] font-extrabold uppercase tracking-wider border border-amber-500/30">
                            Keamanan Perangkat
                        </span>
                        @if($stats['pending'] > 0)
                        <span class="px-2 py-0.5 rounded-md bg-rose-500 text-white text-[10px] font-bold animate-pulse">
                            {{ $stats['pending'] }} Menunggu Persetujuan
                        </span>
                        @endif
                    </div>
                    <h1 class="text-lg sm:text-xl font-black tracking-tight mt-1">Permohonan Ganti Perangkat Warga</h1>
                    <p class="text-xs text-slate-300 mt-0.5">Verifikasi identitas dan setujui permohonan pergantian HP akun warga.</p>
                </div>
            </div>

            <a href="{{ route('users.index') }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition border border-white/10 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <span>Lihat Manajemen Pengguna</span>
            </a>
        </div>
    </div>

    <!-- Feedback Notifikasi -->
    @if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center">
        ✅ {{ session('success') }}
    </div>
    @endif

    @if(session('info'))
    <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs font-semibold text-center">
        ℹ️ {{ session('info') }}
    </div>
    @endif

    <!-- Statistik Permohonan -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('device.requests.index', ['status' => 'pending']) }}" class="rounded-2xl p-4 bg-amber-50 border border-amber-200 shadow-xs hover:shadow-md transition {{ $status === 'pending' ? 'ring-2 ring-amber-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-lg">⏳</span>
                <span class="text-2xl font-black text-amber-900">{{ $stats['pending'] }}</span>
            </div>
            <p class="text-[10px] font-bold text-amber-800 mt-1 uppercase tracking-wider">Menunggu Persetujuan</p>
        </a>

        <a href="{{ route('device.requests.index', ['status' => 'approved']) }}" class="rounded-2xl p-4 bg-emerald-50 border border-emerald-200 shadow-xs hover:shadow-md transition {{ $status === 'approved' ? 'ring-2 ring-emerald-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-lg">✅</span>
                <span class="text-2xl font-black text-emerald-900">{{ $stats['approved'] }}</span>
            </div>
            <p class="text-[10px] font-bold text-emerald-800 mt-1 uppercase tracking-wider">Disetujui</p>
        </a>

        <a href="{{ route('device.requests.index', ['status' => 'rejected']) }}" class="rounded-2xl p-4 bg-rose-50 border border-rose-200 shadow-xs hover:shadow-md transition {{ $status === 'rejected' ? 'ring-2 ring-rose-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-lg">🚫</span>
                <span class="text-2xl font-black text-rose-900">{{ $stats['rejected'] }}</span>
            </div>
            <p class="text-[10px] font-bold text-rose-800 mt-1 uppercase tracking-wider">Ditolak</p>
        </a>

        <a href="{{ route('device.requests.index', ['status' => 'semua']) }}" class="rounded-2xl p-4 bg-slate-50 border border-slate-200 shadow-xs hover:shadow-md transition {{ $status === 'semua' ? 'ring-2 ring-slate-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-lg">📋</span>
                <span class="text-2xl font-black text-slate-900">{{ $stats['total'] }}</span>
            </div>
            <p class="text-[10px] font-bold text-slate-700 mt-1 uppercase tracking-wider">Semua Riwayat</p>
        </a>
    </div>

    <!-- Tabel Permohonan -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[10px] font-extrabold uppercase">Data</span>
                <h2 class="text-sm font-bold text-slate-900">Daftar Permohonan Ganti Perangkat</h2>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-bold">{{ $requests->count() }} pengajuan</span>
            </div>
        </div>

        @if($requests->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">Pemohon & NIK</th>
                        <th class="text-left px-4 py-3">Verifikasi Ibu Kandung</th>
                        <th class="text-left px-4 py-3">Nomor WhatsApp</th>
                        <th class="text-left px-4 py-3">Alasan / Info HP</th>
                        <th class="text-center px-4 py-3">Status</th>
                        <th class="text-center px-4 py-3">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($requests as $req)
                    @php
                        $userDb = $req->user;
                        $namaIbuDb = $userDb?->nama_ibu;
                        $isMatch = $namaIbuDb && strtolower(trim($namaIbuDb)) === strtolower(trim($req->nama_ibu));
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3.5 text-slate-500 whitespace-nowrap">
                            <div class="font-semibold text-slate-700">{{ $req->created_at->translatedFormat('d M Y') }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $req->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-slate-900">{{ $userDb?->name ?? 'Warga Tidak Ditemukan' }}</div>
                            <div class="text-[11px] font-mono text-emerald-700 font-semibold">{{ $req->nik }}</div>
                            @if($userDb)
                            <div class="text-[10px] text-slate-400">RT {{ $userDb->rt_id ?? '01' }} / RW {{ $userDb->rw_id ?? '02' }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-slate-800">{{ $req->nama_ibu }}</div>
                            @if($namaIbuDb)
                                @if($isMatch)
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <span>✓</span> Cocok dengan Data Warga
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-1.5 py-0.2 rounded bg-rose-100 text-rose-800 border border-rose-200" title="Data di DB: {{ $namaIbuDb }}">
                                    <span>⚠️</span> Beda dg DB ({{ $namaIbuDb }})
                                </span>
                                @endif
                            @else
                            <span class="text-[10px] text-slate-400 italic">Data ibu di DB belum diisi</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $req->phone)) }}" target="_blank" class="text-emerald-700 hover:underline font-bold flex items-center gap-1">
                                <span>💬</span>
                                <span>{{ $req->phone }}</span>
                            </a>
                        </td>
                        <td class="px-4 py-3.5 max-w-xs">
                            <div class="text-slate-700 text-xs italic">{{ $req->alasan ?: '-' }}</div>
                            <div class="text-[10px] text-slate-400 truncate mt-0.5" title="{{ $req->new_device_info }}">
                                📱 {{ $req->new_device_info ?: 'Perangkat Baru' }}
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            @if($req->status === 'pending')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                Menunggu
                            </span>
                            @elseif($req->status === 'approved')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Disetujui
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                Ditolak
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            @if($req->status === 'pending')
                            <div class="flex items-center justify-center gap-1.5">
                                <form action="{{ route('device.requests.approve', $req->id) }}" method="POST" onsubmit="return confirm('Setujui dan reset ikatan perangkat untuk {{ $userDb?->name ?? $req->nik }}?')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold shadow-xs transition cursor-pointer flex items-center gap-1">
                                        <span>✓</span>
                                        <span>Setujui</span>
                                    </button>
                                </form>

                                <form action="{{ route('device.requests.reject', $req->id) }}" method="POST" onsubmit="return confirm('Tolak permohonan ini?')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-600 hover:text-rose-700 text-[11px] font-bold border border-slate-200 transition cursor-pointer">
                                        <span>✕</span>
                                    </button>
                                </form>
                            </div>
                            @else
                            <div class="text-[10px] text-slate-400">
                                @if($req->approver)
                                Oleh: {{ $req->approver->name }}<br>
                                @endif
                                {{ $req->approved_at?->format('d/m/y H:i') }}
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12 text-slate-400 space-y-2">
            <span class="text-4xl">📭</span>
            <p class="text-xs font-semibold">Tidak ada permohonan ganti perangkat dengan status ini.</p>
        </div>
        @endif
    </div>

</div>
@endsection

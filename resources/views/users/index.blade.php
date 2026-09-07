@extends('layouts.app')

@section('title', 'Manajemen Pengguna - JagaWarga RW 02')

@push('styles')
<!-- DataTables CSS & Styling -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<style>
    /* DataTables Custom Theme Styling */
    .dataTables_wrapper {
        padding: 0.75rem 0;
        font-size: 0.75rem;
    }
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 0.85rem;
        padding-left: 1.25rem;
        color: #475569;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .dataTables_wrapper .dataTables_length select {
        padding: 0.35rem 2rem 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: bold;
        border-radius: 0.75rem;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        color: #1e293b;
        outline: none;
    }
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 0.85rem;
        padding-right: 1.25rem;
        color: #475569;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        padding: 0.4rem 0.85rem;
        font-size: 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid #cbd5e1;
        outline: none;
        margin-left: 0.5rem;
        background-color: #fff;
        color: #1e293b;
        transition: all 0.2s;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
    }
    .dataTables_wrapper .dataTables_info {
        font-size: 0.75rem;
        color: #64748b;
        padding: 1rem 1.25rem 0.5rem 1.25rem;
        font-weight: 500;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding: 1rem 1.25rem 0.5rem 1.25rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.3rem 0.75rem !important;
        font-size: 0.75rem !important;
        border-radius: 0.5rem !important;
        border: 1px solid #e2e8f0 !important;
        margin: 0 2px !important;
        background: #f8fafc !important;
        color: #475569 !important;
        font-weight: 600 !important;
        transition: all 0.15s;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #7c3aed !important;
        color: white !important;
        border-color: #7c3aed !important;
        font-weight: 800 !important;
        box-shadow: 0 1px 3px rgba(124, 58, 237, 0.3);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #ede9fe !important;
        color: #6d28d9 !important;
        border-color: #ddd6fe !important;
    }
    table.dataTable.no-footer {
        border-bottom: 1px solid #f1f5f9 !important;
    }
    table.dataTable thead th {
        border-bottom: 1px solid #e2e8f0 !important;
        vertical-align: middle;
    }
    table.dataTable thead .sorting,
    table.dataTable thead .sorting_asc,
    table.dataTable thead .sorting_desc {
        background-position: right 0.5rem center !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-5 sm:p-6 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <div class="absolute -right-6 -bottom-6 w-48 h-48 rounded-full bg-violet-600/20 blur-2xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-violet-600/30 border border-violet-400/40 flex items-center justify-center text-2xl shadow-inner">
                    👥
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 rounded-md bg-violet-500/20 text-violet-300 text-[10px] font-extrabold uppercase tracking-wider border border-violet-500/30">
                            Admin Panel &bull; Multi-Role
                        </span>
                    </div>
                    <h1 class="text-lg sm:text-xl font-black tracking-tight mt-1">Manajemen Pengguna & Perangkat</h1>
                    <p class="text-xs text-slate-300 mt-0.5">Kelola akun warga, peran multi-role, dan status ikatan perangkat HP.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('device.requests.index') }}" class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition border border-white/10 flex items-center gap-2">
                    <span>📱 Permohonan Ganti HP</span>
                    @php $pendingDevs = \App\Models\DeviceResetRequest::where('status', 'pending')->count(); @endphp
                    @if($pendingDevs > 0)
                    <span class="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[9px] font-black animate-pulse">{{ $pendingDevs }}</span>
                    @endif
                </a>
                <button type="button" onclick="openModal('modal-import')" class="px-3.5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold shadow-md transition cursor-pointer flex items-center gap-1.5 border border-violet-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    <span>Import Warga (Excel)</span>
                </button>
                <button type="button" onclick="openModal('modal-tambah')" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition cursor-pointer flex items-center gap-2 border border-emerald-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    <span>Tambah Pengguna Baru</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Feedback --}}
    @if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('warning'))
    <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold text-center">
        ⚠️ {{ session('warning') }}
    </div>
    @endif
    @if(session('error'))
    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center">
        🚫 {{ session('error') }}
    </div>
    @endif

    {{-- Statistik Cepat --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2.5">
        @php
            $statCards = [
                ['label' => 'Total Semua', 'value' => $stats['total'], 'icon' => '👥', 'bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'text' => 'text-slate-800', 'role' => 'semua'],
                ['label' => 'Warga', 'value' => $stats['warga'], 'icon' => '🏠', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'role' => 'warga'],
                ['label' => 'Petugas Ronda', 'value' => $stats['petugas_ronda'], 'icon' => '🛡️', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'role' => 'petugas_ronda'],
                ['label' => 'Ketua RT', 'value' => $stats['rt'], 'icon' => '🏘️', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'role' => 'rt'],
                ['label' => 'Pengurus RW', 'value' => $stats['rw'], 'icon' => '🗺️', 'bg' => 'bg-violet-50', 'border' => 'border-violet-200', 'text' => 'text-violet-800', 'role' => 'rw'],
                ['label' => 'Bhabinkamtibmas', 'value' => $stats['bhabinkamtibmas'], 'icon' => '⭐', 'bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-800', 'role' => 'bhabinkamtibmas'],
                ['label' => 'Nakes Puskesmas', 'value' => $stats['nakes_puskesmas'] ?? 0, 'icon' => '🩺', 'bg' => 'bg-teal-50', 'border' => 'border-teal-200', 'text' => 'text-teal-800', 'role' => 'nakes_puskesmas'],
            ];
        @endphp
        @foreach($statCards as $card)
        <a href="{{ route('users.index', ['role' => $card['role'], 'q' => $search]) }}" class="rounded-2xl p-3.5 {{ $card['bg'] }} border {{ $card['border'] }} shadow-xs hover:shadow-md transition cursor-pointer {{ $filterRole === $card['role'] ? 'ring-2 ring-violet-500 ring-offset-1' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-lg">{{ $card['icon'] }}</span>
                <span class="text-xl font-black {{ $card['text'] }}">{{ $card['value'] }}</span>
            </div>
            <p class="text-[10px] font-bold {{ $card['text'] }} mt-1 uppercase tracking-wide">{{ $card['label'] }}</p>
        </a>
        @endforeach
    </div>

    {{-- BAR AKSI MASSAL (MUNCUL OTOMATIS SAAT CHECKBOX DIPILIH) --}}
    <div id="bulk-action-bar" class="hidden transition-all duration-300 bg-gradient-to-r from-rose-950 via-slate-900 to-rose-950 rounded-3xl p-4 sm:p-5 text-white shadow-xl border border-rose-800/80 flex flex-col sm:flex-row items-center justify-between gap-3 animate-in fade-in">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-rose-600/30 border border-rose-500/40 flex items-center justify-center text-rose-300 text-lg shadow-inner shrink-0">
                ☑️
            </div>
            <div>
                <p class="text-xs sm:text-sm font-black text-white flex items-center gap-2">
                    <span id="selected-count-badge" class="px-2.5 py-0.5 rounded-full bg-rose-600 text-white text-xs font-black shadow-xs">0</span>
                    <span>Warga Terpilih untuk Dihapus Massal</span>
                </p>
                <p class="text-[10px] sm:text-[11px] text-rose-200 mt-0.5">Daftar warga yang dicentang dapat dihapus sekaligus secara aman dari sistem.</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
            <button type="button" onclick="clearSelectedUsers()" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition cursor-pointer border border-white/15">
                Batal Pilih
            </button>
            <button type="button" onclick="openBulkDeleteModal()" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-md hover:shadow-lg transition cursor-pointer flex items-center gap-2 border border-rose-500">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                <span>Hapus (<span id="btn-count">0</span>) Warga Terpilih</span>
            </button>
        </div>
    </div>

    {{-- Tabel Pengguna (DataTables) --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-violet-100 text-violet-800 text-[10px] font-extrabold uppercase">Data</span>
                <h2 class="text-sm font-bold text-slate-900">Daftar Pengguna Sistem</h2>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-bold">{{ $users->count() }} orang</span>
            </div>
            <div class="text-[11px] text-slate-400 font-medium hidden sm:block">
                Interaktif &bull; Cari & Urutkan Kolom
            </div>
        </div>

        @if($users->count() > 0)
        <div class="overflow-x-auto p-2 sm:p-3">
            <table id="table-users" class="w-full text-xs hover">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <th class="text-center px-3 py-3 w-10 !pr-3" data-orderable="false" data-searchable="false">
                            <input type="checkbox" id="check-all-users" class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 cursor-pointer border-slate-300 align-middle" title="Pilih Semua Warga di Halaman Ini">
                        </th>
                        <th class="text-left px-3 py-3 w-10">#</th>
                        <th class="text-left px-4 py-3">Nama Lengkap</th>
                        <th class="text-left px-4 py-3">NIK & Ibu</th>
                        <th class="text-left px-4 py-3">Email & HP</th>
                        <th class="text-left px-4 py-3">Peran (Roles)</th>
                        <th class="text-left px-4 py-3">Perangkat Warga</th>
                        <th class="text-left px-4 py-3">RT/RW & Alamat</th>
                        <th class="text-center px-4 py-3 w-20" data-orderable="false" data-searchable="false">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $i => $u)
                    <tr class="hover:bg-slate-50/80 transition" id="user-row-{{ $u->id }}">
                        <td class="text-center px-3 py-3">
                            @if(Auth::id() === $u->id)
                                <input type="checkbox" disabled class="w-4 h-4 rounded text-slate-300 opacity-40 cursor-not-allowed border-slate-300" title="Akun Anda Sendiri (Dilindungi dari penghapusan)">
                            @else
                                <input type="checkbox" class="user-row-checkbox w-4 h-4 rounded text-rose-600 focus:ring-rose-500 cursor-pointer border-slate-300 transition" value="{{ $u->id }}" data-name="{{ $u->name }}" data-nik="{{ $u->nik }}" onchange="handleUserCheckboxChange(this)">
                            @endif
                        </td>
                        <td class="px-3 py-3 text-slate-400 font-mono">{{ $i + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-black text-[11px] shrink-0 bg-violet-600">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 text-[11px]">{{ $u->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $u->no_rumah ? 'No. ' . $u->no_rumah : '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-mono text-slate-800 text-[11px] font-bold">{{ $u->nik ?? '-' }}</span>
                                @if($u->is_nik_verified)
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200" title="NIK Terverifikasi Sah">
                                        <span>✓</span> Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200" title="Belum Terverifikasi">
                                        <span>⚠️</span> Belum Verif
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] text-slate-400">Ibu: {{ $u->nama_ibu ?: '-' }}</span>
                                <form action="{{ route('users.toggle_nik', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Ubah status verifikasi NIK {{ $u->name }}?')">
                                    @csrf
                                    <button type="submit" class="text-[9px] font-bold text-violet-600 hover:text-violet-800 underline cursor-pointer">
                                        {{ $u->is_nik_verified ? 'Cabut' : 'Verifikasi' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-slate-800 text-[11px]">{{ $u->email }}</div>
                            <div class="text-[10px] text-slate-500">{{ $u->phone ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @forelse($u->roles as $r)
                                <span class="px-1.5 py-0.5 rounded-md text-[9px] font-extrabold border {{ $r->badge_color }}">
                                    {{ $r->display_name }}
                                </span>
                                @empty
                                <span class="px-1.5 py-0.5 rounded-md text-[9px] font-extrabold border bg-slate-100 text-slate-700">
                                    {{ strtoupper($u->role) }}
                                </span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($u->registered_device_id)
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1 text-emerald-800 text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 border border-emerald-200" title="{{ $u->device_info }}">
                                        <span>📱</span> Terikat
                                    </span>
                                    <form action="{{ route('users.reset_device', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Reset perangkat akun {{ $u->name }}? User dapat mendaftarkan HP baru.')">
                                        @csrf
                                        <button type="submit" class="px-1.5 py-0.5 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[9px] font-black transition cursor-pointer" title="Lepas ikatan perangkat">
                                            Reset
                                        </button>
                                    </form>
                                </div>
                                <div class="text-[9px] text-slate-400 mt-0.5">{{ $u->device_registered_at?->format('d/m/y H:i') }}</div>
                            @else
                                <span class="text-slate-400 text-[10px] italic">Belum Ada HP</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            <div class="font-bold text-[10px]">RT {{ $u->rt_id ?? '-' }} / RW {{ $u->rw_id ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400 truncate max-w-[120px]">{{ $u->alamat ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button"
                                    onclick="editUser({{ json_encode($u->load('roles')) }})"
                                    class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition cursor-pointer border border-blue-200" title="Edit Pengguna">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                @if(Auth::id() !== $u->id)
                                <button type="button"
                                    onclick="hapusUser({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                    class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 transition cursor-pointer border border-rose-200" title="Hapus">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12 text-slate-400">
            <span class="text-4xl">👥</span>
            <p class="text-xs font-semibold mt-2">Tidak ada pengguna yang sesuai dengan filter.</p>
        </div>
        @endif
    </div>
</div>

{{-- MODAL: TAMBAH PENGGUNA --}}
<div id="modal-tambah" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-tambah')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto border border-slate-200">
        <div class="sticky top-0 bg-white rounded-t-3xl border-b border-slate-100 px-5 py-4 flex items-center justify-between z-10">
            <div>
                <h3 class="text-sm font-black text-slate-900">Tambah Pengguna Baru</h3>
                <p class="text-[10px] text-slate-500">Daftarkan akun warga, petugas, atau pengurus RT/RW baru.</p>
            </div>
            <button type="button" onclick="closeModal('modal-tambah')" class="p-1 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form action="{{ route('users.store') }}" method="POST" class="p-5 space-y-3">
            @csrf
            <input type="hidden" name="current_role" value="{{ $filterRole }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Nama lengkap sesuai KTP" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">NIK (16 digit) <span class="text-rose-500">*</span></label>
                    <input type="text" name="nik" required maxlength="16" minlength="16" pattern="\d{16}" placeholder="3201010101010001" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Ibu Kandung</label>
                    <input type="text" name="nama_ibu" placeholder="Verifikasi ganti perangkat" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="nama@jagawarga.local" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                    <input type="tel" name="phone" placeholder="08xxxxxxxxxx" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div class="sm:col-span-2 bg-emerald-50/70 p-3 rounded-2xl border border-emerald-200/80">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_nik_verified" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Status NIK Terverifikasi Sah</span>
                            <span class="text-[10px] text-slate-500 block">Aktifkan agar warga dapat menggunakan Tombol Kentongan Online 24 Jam di dalam radius wilayah.</span>
                        </div>
                    </label>
                </div>

                {{-- Multiple Role Selection --}}
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1.5">Peran Pengguna (Multi-Role) <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-slate-50 p-3 rounded-2xl border border-slate-200">
                        @foreach($availableRoles as $r)
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-white border border-slate-200 hover:border-violet-400 cursor-pointer text-xs transition">
                            <input type="checkbox" name="roles[]" value="{{ $r->name }}" {{ $r->name === 'warga' ? 'checked' : '' }} class="rounded text-violet-600 focus:ring-violet-500">
                            <span class="font-bold text-slate-800 text-[11px]">{{ $r->icon }} {{ $r->display_name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Pengguna dapat memiliki lebih dari 1 peran sekaligus (misal Warga + Petugas Ronda).</p>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">RT</label>
                    <select name="rt_id" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 bg-white">
                        <option value="01">RT 01</option>
                        <option value="02">RT 02</option>
                        <option value="03">RT 03</option>
                        <option value="04">RT 04</option>
                        <option value="05">RT 05</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">RW</label>
                    <input type="text" name="rw_id" value="02" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Alamat</label>
                    <input type="text" name="alamat" placeholder="Jl. Kenanga No. 12" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">No. Rumah</label>
                    <input type="text" name="no_rumah" placeholder="12" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Password <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required minlength="6" value="password" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                    <p class="text-[10px] text-slate-400 mt-1">Default: <code class="bg-slate-100 px-1 rounded font-mono">password</code></p>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-tambah')" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">💾 Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: EDIT PENGGUNA --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-edit')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto border border-slate-200">
        <div class="sticky top-0 bg-white rounded-t-3xl border-b border-slate-100 px-5 py-4 flex items-center justify-between z-10">
            <div>
                <h3 class="text-sm font-black text-slate-900">Edit Data Pengguna & Peran</h3>
                <p class="text-[10px] text-slate-500">Perbarui informasi akun, hak peran (multi-role), dan data keamanan.</p>
            </div>
            <button type="button" onclick="closeModal('modal-edit')" class="p-1 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form id="form-edit-user" method="POST" class="p-5 space-y-3">
            @csrf
            @method('PUT')
            <input type="hidden" name="current_role" value="{{ $filterRole }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="edit-name" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">NIK (16 digit) <span class="text-rose-500">*</span></label>
                    <input type="text" name="nik" id="edit-nik" required maxlength="16" minlength="16" pattern="\d{16}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Ibu Kandung</label>
                    <input type="text" name="nama_ibu" id="edit-nama_ibu" placeholder="Verifikasi ganti perangkat" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" id="edit-email" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                    <input type="tel" name="phone" id="edit-phone" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div class="sm:col-span-2 bg-emerald-50/70 p-3 rounded-2xl border border-emerald-200/80">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_nik_verified" id="edit-is_nik_verified" value="1" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Status NIK Terverifikasi Sah</span>
                            <span class="text-[10px] text-slate-500 block">Syarat wajib bagi warga untuk dapat mengaktifkan Kentongan Online (selain radius koordinat RW).</span>
                        </div>
                    </label>
                </div>

                {{-- Edit Multiple Roles --}}
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1.5">Peran Pengguna (Multi-Role) <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-slate-50 p-3 rounded-2xl border border-slate-200" id="edit-roles-container">
                        @foreach($availableRoles as $r)
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-white border border-slate-200 hover:border-violet-400 cursor-pointer text-xs transition">
                            <input type="checkbox" name="roles[]" value="{{ $r->name }}" id="edit-role-{{ $r->name }}" class="edit-role-checkbox rounded text-violet-600 focus:ring-violet-500">
                            <span class="font-bold text-slate-800 text-[11px]">{{ $r->icon }} {{ $r->display_name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">RT</label>
                    <select name="rt_id" id="edit-rt_id" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 bg-white">
                        <option value="01">RT 01</option>
                        <option value="02">RT 02</option>
                        <option value="03">RT 03</option>
                        <option value="04">RT 04</option>
                        <option value="05">RT 05</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">RW</label>
                    <input type="text" name="rw_id" id="edit-rw_id" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Alamat</label>
                    <input type="text" name="alamat" id="edit-alamat" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">No. Rumah</label>
                    <input type="text" name="no_rumah" id="edit-no_rumah" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Password Baru <span class="text-slate-400 font-normal">(kosongkan jika tidak ingin mengubah)</span></label>
                    <input type="password" name="password" minlength="6" placeholder="Kosongkan jika tidak diubah" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-edit')" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: IMPORT DATA WARGA (EXCEL) --}}
<div id="modal-import" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-import')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-200">
        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-emerald-700 to-teal-700 px-5 py-4 text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-lg font-black shadow-inner">
                    📥
                </div>
                <div>
                    <h3 class="text-sm font-black tracking-tight">Import Data Warga dari Excel</h3>
                    <p class="text-[10px] text-emerald-100">Tambah banyak data warga sekaligus dengan format Excel (.xlsx, .xls) atau .csv</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modal-import')" class="text-white/80 hover:text-white p-1 rounded-lg cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- Section 1: Formulir Unggah File Excel --}}
        <div id="import-form-section">
            <form id="form-import-excel" action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" onsubmit="handleImportSubmit(event)" class="p-5 space-y-4">
                @csrf

                {{-- Kartu Info Ketentuan Kolom --}}
                <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-3.5 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-emerald-950 flex items-center gap-1.5 text-[11px]">
                            <span>📋</span> Ketentuan Kolom File Excel:
                        </span>
                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-emerald-200 text-emerald-900">
                            Default: Role Warga
                        </span>
                    </div>
                    <ul class="text-[11px] text-emerald-900 space-y-1 list-disc list-inside">
                        <li><strong>Nama Lengkap</strong> <span class="text-rose-600 font-bold">*Wajib</span></li>
                        <li><strong>NIK 16 Digit</strong> <span class="text-rose-600 font-bold">*Wajib & Unik</span></li>
                        <li><strong>Password</strong>: Otomatis default <code class="bg-emerald-100 px-1 py-0.2 rounded font-mono font-bold text-emerald-800">[password]</code> jika dikosongkan.</li>
                        <li><strong>Email</strong>: Otomatis dibuatkan <code class="bg-emerald-100 px-1 py-0.2 rounded font-mono text-[10px]">warga_{nik}@jagawarga.local</code> jika dikosongkan.</li>
                        <li><strong>Otomatis Terverifikasi</strong>: Seluruh warga hasil import langsung berstatus <em>NIK Terverifikasi Sah</em> agar siap membunyikan Kentongan Online.</li>
                    </ul>
                </div>

                {{-- Tombol Download Template Excel --}}
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 flex items-center justify-between gap-3">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">Belum punya format file?</span>
                        <span class="text-[10px] text-slate-500 block">Unduh template standar Excel yang sudah dilengkapi contoh data pengisian.</span>
                    </div>
                    <a href="{{ route('users.template') }}" class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition shrink-0 flex items-center gap-1.5 border border-emerald-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span>Unduh Template (.xlsx)</span>
                    </a>
                </div>

                {{-- Input File Excel --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Pilih File Excel / Spreadsheet <span class="text-rose-500">*</span></label>
                    <input type="file" name="excel_file" required accept=".xlsx,.xls,.csv" class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-200 rounded-xl p-1 bg-white cursor-pointer focus:outline-none focus:border-emerald-500">
                    <p class="text-[10px] text-slate-400">Format yang didukung: <strong>.xlsx, .xls, .csv</strong> (Maks. 10 MB)</p>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modal-import')" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">Batal</button>
                    <button type="submit" id="btn-submit-import" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition cursor-pointer flex items-center gap-1.5">
                        <span>🚀</span> <span>Mulai Import Data</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Section 2: Tampilan Progress Bar Realtime (0% s/d 100%) --}}
        <div id="import-progress-section" class="hidden p-6 space-y-5">
            <div class="text-center space-y-2">
                <div class="w-16 h-16 rounded-3xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-3xl shadow-inner border border-emerald-200 animate-pulse">
                    ⚡
                </div>
                <h3 class="text-base font-black text-slate-900">Memproses Import Data Warga</h3>
                <p id="import-status-text" class="text-xs text-slate-500">Menyiapkan berkas Excel dan membuka koneksi database...</p>
            </div>

            {{-- Indikator Persentase & Label Tahap --}}
            <div class="flex items-center justify-between text-xs font-bold px-1">
                <span id="import-step-text" class="text-slate-600">Langkah 1/4: Unggah Berkas</span>
                <span id="import-percentage-text" class="text-2xl font-black text-emerald-600 font-mono">0%</span>
            </div>

            {{-- Progress Bar --}}
            <div class="w-full bg-slate-100 rounded-full h-4 p-0.5 border border-slate-200 shadow-inner overflow-hidden">
                <div id="import-progress-bar" class="h-full rounded-full bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 transition-all duration-300 ease-out" style="width: 0%;">
                </div>
            </div>

            {{-- Timeline Mini 4 Tahap --}}
            <div class="grid grid-cols-4 gap-1.5 text-[10px] text-center font-bold">
                <div id="step-badge-1" class="p-1.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 transition">1. Unggah</div>
                <div id="step-badge-2" class="p-1.5 rounded-lg bg-slate-50 text-slate-400 border border-slate-200 transition">2. Validasi</div>
                <div id="step-badge-3" class="p-1.5 rounded-lg bg-slate-50 text-slate-400 border border-slate-200 transition">3. Akun & NIK</div>
                <div id="step-badge-4" class="p-1.5 rounded-lg bg-slate-50 text-slate-400 border border-slate-200 transition">4. Selesai</div>
            </div>

            {{-- Kotak Hasil / Feedback --}}
            <div id="import-result-box" class="hidden">
            </div>
        </div>
    </div>
</div>

{{-- MODAL: KONFIRMASI HAPUS --}}
<div id="modal-hapus" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-hapus')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm border border-slate-200 p-6 text-center space-y-4">
        <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto text-2xl shadow-inner">🗑️</div>
        <h3 class="text-sm font-black text-slate-900">Hapus Pengguna?</h3>
        <p class="text-xs text-slate-500">Anda akan menghapus akun <strong id="hapus-nama" class="text-slate-800"></strong> dari sistem JagaWarga. Tindakan ini tidak dapat dibatalkan.</p>
        <form id="form-hapus-user" method="POST" class="flex justify-center gap-2">
            @csrf
            @method('DELETE')
            <input type="hidden" name="current_role" value="{{ $filterRole }}">
            <button type="button" onclick="closeModal('modal-hapus')" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">Batal</button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">🗑️ Ya, Hapus</button>
        </form>
    </div>
</div>

{{-- MODAL: KONFIRMASI HAPUS MASSAL --}}
<div id="modal-bulk-hapus" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-bulk-hapus')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md border border-slate-200 p-6 space-y-4">
        <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto text-2xl shadow-inner">
            🗑️
        </div>
        <div class="text-center">
            <h3 class="text-base font-black text-slate-900">Hapus Warga Terpilih?</h3>
            <p class="text-xs text-slate-500 mt-1">
                Anda akan menghapus <strong id="bulk-delete-count-text" class="text-rose-600 font-black">0</strong> data warga yang dicentang secara permanen dari sistem JagaWarga RW 02.
            </p>
        </div>

        {{-- Daftar Preview Nama Warga yang Akan Dihapus --}}
        <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200 max-h-44 overflow-y-auto space-y-1 text-xs">
            <span class="text-[10px] uppercase font-black text-slate-400 block tracking-wider mb-1.5">Daftar Warga yang Dipilih:</span>
            <div id="bulk-delete-preview-list" class="space-y-1 divide-y divide-slate-100">
                {{-- Diisi secara dinamis oleh JavaScript --}}
            </div>
        </div>

        <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900">
            ⚠️ <strong>Perhatian:</strong> Riwayat patroli, laporan, dan permohonan perangkat terkait warga ini akan ikut dibersihkan. Akun Anda sendiri dilindungi dan tidak akan terhapus.
        </div>

        <form id="form-bulk-delete" action="{{ route('users.bulk_destroy') }}" method="POST">
            @csrf
            <input type="hidden" name="current_role" value="{{ $filterRole }}">
            <div id="bulk-ids-container">
                {{-- Diisi input hidden selected_ids[] secara dinamis --}}
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-bulk-hapus')" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition cursor-pointer flex items-center gap-1.5 border border-rose-500">
                    <span>🗑️</span> <span>Ya, Hapus Semua Terpilih</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<!-- CDN jQuery & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    // State Checklist Warga Terpilih (id -> { name, nik })
    const selectedUsers = new Map();
    let dtUsers = null;

    $(document).ready(function() {
        // Inisialisasi DataTable pada tabel pengguna
        if ($('#table-users').length > 0 && !$.fn.DataTable.isDataTable('#table-users')) {
            dtUsers = $('#table-users').DataTable({
                responsive: true,
                order: [[2, 'asc']], // Urutkan default berdasarkan kolom Nama Lengkap
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                columnDefs: [
                    { orderable: false, targets: [0, 8] }, // Checkbox dan Aksi tidak bisa disort
                    { searchable: false, targets: [0, 8] }  // Checkbox dan Aksi tidak masuk filter teks
                ],
                language: {
                    search: "🔍 Cari Warga:",
                    searchPlaceholder: "Ketik nama, NIK, HP, RT...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ s/d _END_ dari total _TOTAL_ warga",
                    infoEmpty: "Tidak ada data warga yang tersedia",
                    infoFiltered: "(disaring dari _MAX_ total warga)",
                    zeroRecords: "Tidak ada data warga yang cocok dengan pencarian",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Berikutnya &rarr;",
                        previous: "&larr; Sebelumnya"
                    }
                }
            });

            // Sinkronisasi status centang saat DataTables redraw (pindah halaman, sorting, filtering)
            dtUsers.on('draw', function() {
                syncCheckboxesOnDraw();
            });
        }

        // Event listener checkbox "Pilih Semua" di header tabel
        $('#check-all-users').on('change', function() {
            const isChecked = this.checked;
            $('.user-row-checkbox:not(:disabled)').each(function() {
                const id = this.value;
                const name = $(this).data('name');
                const nik = $(this).data('nik');

                this.checked = isChecked;
                if (isChecked) {
                    selectedUsers.set(id, { name: name, nik: nik });
                } else {
                    selectedUsers.delete(id);
                }
            });
            updateSelectionUI();
        });
    });

    // Event listener checkbox baris individu
    function handleUserCheckboxChange(cb) {
        const id = cb.value;
        const name = cb.getAttribute('data-name');
        const nik = cb.getAttribute('data-nik');

        if (cb.checked) {
            selectedUsers.set(id, { name: name, nik: nik });
        } else {
            selectedUsers.delete(id);
        }

        const visibleCheckboxes = $('.user-row-checkbox:not(:disabled)');
        const checkedCount = visibleCheckboxes.filter(':checked').length;
        $('#check-all-users').prop('checked', visibleCheckboxes.length > 0 && checkedCount === visibleCheckboxes.length);

        updateSelectionUI();
    }

    // Sinkronisasi status checkbox saat redraw DataTable
    function syncCheckboxesOnDraw() {
        let allChecked = true;
        let hasAny = false;

        $('.user-row-checkbox:not(:disabled)').each(function() {
            hasAny = true;
            if (selectedUsers.has(this.value)) {
                this.checked = true;
            } else {
                this.checked = false;
                allChecked = false;
            }
        });

        $('#check-all-users').prop('checked', hasAny && allChecked);
    }

    // Perbarui tampilan Bar Aksi Massal & Counter
    function updateSelectionUI() {
        const totalSelected = selectedUsers.size;
        const bar = document.getElementById('bulk-action-bar');
        const countBadge = document.getElementById('selected-count-badge');
        const btnCount = document.getElementById('btn-count');

        if (countBadge) countBadge.innerText = totalSelected;
        if (btnCount) btnCount.innerText = totalSelected;

        if (totalSelected > 0) {
            if (bar) bar.classList.remove('hidden');
        } else {
            if (bar) bar.classList.add('hidden');
            $('#check-all-users').prop('checked', false);
        }
    }

    // Batalkan semua pilihan
    function clearSelectedUsers() {
        selectedUsers.clear();
        $('.user-row-checkbox').prop('checked', false);
        $('#check-all-users').prop('checked', false);
        updateSelectionUI();
    }

    // Buka Modal Konfirmasi Hapus Massal
    function openBulkDeleteModal() {
        if (selectedUsers.size === 0) {
            alert('Silakan pilih minimal 1 data warga untuk dihapus.');
            return;
        }

        const countText = document.getElementById('bulk-delete-count-text');
        const previewList = document.getElementById('bulk-delete-preview-list');
        const idsContainer = document.getElementById('bulk-ids-container');

        if (countText) countText.innerText = selectedUsers.size + ' orang';

        if (previewList && idsContainer) {
            previewList.innerHTML = '';
            idsContainer.innerHTML = '';

            let index = 1;
            selectedUsers.forEach((data, id) => {
                // Tambahkan hidden input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_ids[]';
                input.value = id;
                idsContainer.appendChild(input);

                // Tambahkan item preview ke modal
                if (index <= 15) {
                    const item = document.createElement('div');
                    item.className = 'flex items-center justify-between text-slate-800 py-1 text-xs';
                    item.innerHTML = `
                        <span class="font-bold text-slate-800">${index}. ${data.name}</span>
                        <span class="font-mono text-slate-500 text-[10px]">${data.nik || '-'}</span>
                    `;
                    previewList.appendChild(item);
                }
                index++;
            });

            if (selectedUsers.size > 15) {
                const more = document.createElement('div');
                more.className = 'text-center text-[10px] text-slate-400 italic py-1';
                more.innerText = `... dan ${selectedUsers.size - 15} warga lainnya`;
                previewList.appendChild(more);
            }
        }

        openModal('modal-bulk-hapus');
    }

    // --- LOGIKA PROGRESS BAR IMPORT DATA WARGA (0% s/d 100%) ---
    let isImporting = false;
    let importXhr = null;

    function resetImportModal() {
        isImporting = false;
        const formSection = document.getElementById('import-form-section');
        const progressSection = document.getElementById('import-progress-section');
        const form = document.getElementById('form-import-excel');
        const submitBtn = document.getElementById('btn-submit-import');

        if (formSection) formSection.classList.remove('hidden');
        if (progressSection) progressSection.classList.add('hidden');
        if (form) form.reset();
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>🚀</span> <span>Mulai Import Data</span>';
        }

        const progressBar = document.getElementById('import-progress-bar');
        const percentText = document.getElementById('import-percentage-text');
        const resultBox = document.getElementById('import-result-box');
        const statusText = document.getElementById('import-status-text');
        const stepText = document.getElementById('import-step-text');

        if (progressBar) {
            progressBar.style.width = '0%';
            progressBar.className = 'h-full rounded-full bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 transition-all duration-300 ease-out';
        }
        if (percentText) {
            percentText.innerText = '0%';
            percentText.className = 'text-2xl font-black text-emerald-600 font-mono';
        }
        if (statusText) statusText.innerText = 'Menyiapkan berkas Excel dan membuka koneksi database...';
        if (stepText) stepText.innerText = 'Langkah 1/4: Unggah Berkas';
        if (resultBox) {
            resultBox.classList.add('hidden');
            resultBox.innerHTML = '';
        }

        updateStepBadges(1);
    }

    function updateStepBadges(activeStep) {
        for (let i = 1; i <= 4; i++) {
            const badge = document.getElementById('step-badge-' + i);
            if (!badge) continue;
            if (i < activeStep) {
                badge.className = 'p-1.5 rounded-lg bg-emerald-100 text-emerald-900 border border-emerald-300 font-black transition';
            } else if (i === activeStep) {
                badge.className = 'p-1.5 rounded-lg bg-emerald-500 text-white border border-emerald-600 font-black shadow-xs transition';
            } else {
                badge.className = 'p-1.5 rounded-lg bg-slate-50 text-slate-400 border border-slate-200 font-bold transition';
            }
        }
    }

    function handleImportSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('form-import-excel');
        const fileInput = form.querySelector('input[name="excel_file"]');

        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Silakan pilih file Excel terlebih dahulu.');
            return;
        }

        const file = fileInput.files[0];
        const formData = new FormData(form);

        isImporting = true;

        // Beralih dari tampilan formulir ke progress bar
        document.getElementById('import-form-section').classList.add('hidden');
        document.getElementById('import-progress-section').classList.remove('hidden');

        const progressBar = document.getElementById('import-progress-bar');
        const percentText = document.getElementById('import-percentage-text');
        const statusText = document.getElementById('import-status-text');
        const stepText = document.getElementById('import-step-text');
        const resultBox = document.getElementById('import-result-box');

        let currentPercent = 0;
        let targetPercent = 25;

        function renderProgress(val, status, step, activeStepBadge) {
            currentPercent = Math.min(Math.max(val, 0), 100);
            if (progressBar) progressBar.style.width = currentPercent + '%';
            if (percentText) percentText.innerText = Math.round(currentPercent) + '%';
            if (status && statusText) statusText.innerText = status;
            if (step && stepText) stepText.innerText = step;
            if (activeStepBadge) updateStepBadges(activeStepBadge);
        }

        renderProgress(10, 'Mengunggah berkas ' + file.name + '...', 'Langkah 1/4: Unggah Berkas (0% - 35%)', 1);

        // Timer peningkatan persentase berkala saat proses server berjalan
        let progressTimer = setInterval(() => {
            if (currentPercent < targetPercent) {
                currentPercent += 1.5;
                renderProgress(currentPercent);
            }
        }, 50);

        importXhr = new XMLHttpRequest();
        importXhr.open('POST', form.action, true);
        importXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        importXhr.setRequestHeader('Accept', 'application/json');

        // Lacak progres pengunggahan berkas nyata (0% s/d 35%)
        importXhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                const uploadRatio = event.loaded / event.total;
                const computedPercent = Math.round(10 + (uploadRatio * 25));
                targetPercent = Math.max(targetPercent, computedPercent);
                if (uploadRatio >= 1) {
                    targetPercent = 70;
                    renderProgress(45, 'Membaca baris data Excel & memvalidasi NIK 16 digit...', 'Langkah 2/4: Validasi Format & Kolom (35% - 70%)', 2);
                }
            }
        };

        importXhr.onload = function() {
            clearInterval(progressTimer);

            if (importXhr.status >= 200 && importXhr.status < 300) {
                let res;
                try {
                    res = JSON.parse(importXhr.responseText);
                } catch (err) {
                    res = { success: true, message: 'Data warga berhasil diimpor ke sistem!' };
                }

                renderProgress(88, 'Mendaftarkan warga & mengesahkan status NIK...', 'Langkah 3/4: Sinkronisasi Database (70% - 90%)', 3);

                setTimeout(() => {
                    renderProgress(100, 'Import Berhasil 100%! Seluruh akun warga telah siap.', 'Langkah 4/4: Selesai (100%)', 4);
                    isImporting = false;

                    if (resultBox) {
                        resultBox.classList.remove('hidden');
                        let summaryHtml = `
                            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 text-xs space-y-1.5 animate-in fade-in">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">🎉</span>
                                    <strong class="text-sm font-black text-emerald-900">Import Selesai!</strong>
                                </div>
                                <p class="text-[11px] text-emerald-800">${res.message}</p>
                        `;
                        if (res.warning) {
                            summaryHtml += `<p class="text-[10px] text-amber-800 font-semibold bg-amber-100/60 p-1.5 rounded-lg">⚠️ ${res.warning}</p>`;
                        }
                        summaryHtml += `
                                <p class="text-[10px] text-emerald-700 font-bold mt-1">🔄 Memperbarui tabel warga secara otomatis...</p>
                            </div>
                        `;
                        resultBox.innerHTML = summaryHtml;
                    }

                    // Muat ulang halaman ke index setelah 1.5 detik
                    setTimeout(() => {
                        window.location.href = res.redirect_url || window.location.pathname;
                    }, 1400);
                }, 550);

            } else {
                isImporting = false;
                let errMessage = 'Terjadi kesalahan saat memproses file Excel.';
                try {
                    const errRes = JSON.parse(importXhr.responseText);
                    if (errRes.message) errMessage = errRes.message;
                } catch (e) {}

                if (progressBar) {
                    progressBar.className = 'h-full rounded-full bg-rose-600 transition-all duration-300';
                }
                if (percentText) {
                    percentText.innerText = '!';
                    percentText.className = 'text-2xl font-black text-rose-600 font-mono';
                }
                if (statusText) statusText.innerText = 'Gagal: ' + errMessage;
                if (stepText) stepText.innerText = 'Terjadi Kesalahan';

                if (resultBox) {
                    resultBox.classList.remove('hidden');
                    resultBox.innerHTML = `
                        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xl shrink-0">🚫</span>
                                <span class="font-medium">${errMessage}</span>
                            </div>
                            <button type="button" onclick="resetImportModal()" class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shrink-0 cursor-pointer shadow-xs transition">
                                Coba Lagi
                            </button>
                        </div>
                    `;
                }
            }
        };

        importXhr.onerror = function() {
            clearInterval(progressTimer);
            isImporting = false;
            if (progressBar) progressBar.className = 'h-full rounded-full bg-rose-600';
            if (percentText) {
                percentText.innerText = '!';
                percentText.className = 'text-2xl font-black text-rose-600 font-mono';
            }
            if (statusText) statusText.innerText = 'Koneksi ke server terputus.';
            if (stepText) stepText.innerText = 'Koneksi Gagal';

            if (resultBox) {
                resultBox.classList.remove('hidden');
                resultBox.innerHTML = `
                    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-center justify-between gap-3">
                        <span>Gagal menghubungi server. Periksa koneksi Anda.</span>
                        <button type="button" onclick="resetImportModal()" class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shrink-0 cursor-pointer shadow-xs transition">
                            Coba Lagi
                        </button>
                    </div>
                `;
            }
        };

        importXhr.send(formData);
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        if (id === 'modal-import' && isImporting) {
            if (!confirm('Proses import sedang berlangsung. Apakah Anda yakin ingin membatalkan?')) {
                return;
            }
            if (importXhr) importXhr.abort();
            isImporting = false;
        }

        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = '';

        if (id === 'modal-import') {
            resetImportModal();
        }
    }

    function editUser(user) {
        const form = document.getElementById('form-edit-user');
        form.action = '/users/' + user.id;
        document.getElementById('edit-name').value = user.name || '';
        document.getElementById('edit-nik').value = user.nik || '';
        document.getElementById('edit-nama_ibu').value = user.nama_ibu || '';
        document.getElementById('edit-email').value = user.email || '';
        document.getElementById('edit-phone').value = user.phone || '';
        document.getElementById('edit-rt_id').value = user.rt_id || '01';
        document.getElementById('edit-rw_id').value = user.rw_id || '02';
        document.getElementById('edit-alamat').value = user.alamat || '';
        document.getElementById('edit-no_rumah').value = user.no_rumah || '';
        document.getElementById('edit-is_nik_verified').checked = !!user.is_nik_verified;

        // Reset semua checkbox role di modal edit
        document.querySelectorAll('.edit-role-checkbox').forEach(cb => cb.checked = false);

        // Centang role yang dimiliki oleh user
        if (user.roles && user.roles.length > 0) {
            user.roles.forEach(r => {
                const cb = document.getElementById('edit-role-' + r.name);
                if (cb) cb.checked = true;
            });
        } else if (user.role) {
            const cb = document.getElementById('edit-role-' + user.role);
            if (cb) cb.checked = true;
        }

        openModal('modal-edit');
    }

    function hapusUser(id, nama) {
        document.getElementById('form-hapus-user').action = '/users/' + id;
        document.getElementById('hapus-nama').innerText = nama;
        openModal('modal-hapus');
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['modal-tambah', 'modal-edit', 'modal-hapus', 'modal-import', 'modal-bulk-hapus'].forEach(id => {
                const el = document.getElementById(id);
                if (el && !el.classList.contains('hidden')) {
                    closeModal(id);
                }
            });
        }
    });
</script>
@endpush

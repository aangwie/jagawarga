@extends('layouts.app')

@section('title', 'Manajemen Pengguna - JagaWarga RW 02')

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
                            Admin Panel
                        </span>
                    </div>
                    <h1 class="text-lg sm:text-xl font-black tracking-tight mt-1">Manajemen Pengguna Sistem</h1>
                    <p class="text-xs text-slate-300 mt-0.5">Kelola akun warga, petugas ronda, ketua RT, pengurus RW, dan Bhabinkamtibmas.</p>
                </div>
            </div>
            <button type="button" onclick="openModal('modal-tambah')" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition cursor-pointer flex items-center gap-2 border border-emerald-500">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                <span>Tambah Pengguna Baru</span>
            </button>
        </div>
    </div>

    {{-- Feedback --}}
    @if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold text-center">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold text-center">
        🚫 {{ session('error') }}
    </div>
    @endif

    {{-- Statistik Cepat --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
            $statCards = [
                ['label' => 'Total Semua', 'value' => $stats['total'], 'icon' => '👥', 'bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'text' => 'text-slate-800', 'role' => 'semua'],
                ['label' => 'Warga', 'value' => $stats['warga'], 'icon' => '🏠', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'role' => 'warga'],
                ['label' => 'Petugas Ronda', 'value' => $stats['petugas_ronda'], 'icon' => '🛡️', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'role' => 'petugas_ronda'],
                ['label' => 'Ketua RT', 'value' => $stats['rt'], 'icon' => '🏘️', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'role' => 'rt'],
                ['label' => 'Pengurus RW', 'value' => $stats['rw'], 'icon' => '🗺️', 'bg' => 'bg-violet-50', 'border' => 'border-violet-200', 'text' => 'text-violet-800', 'role' => 'rw'],
                ['label' => 'Bhabinkamtibmas', 'value' => $stats['bhabinkamtibmas'], 'icon' => '⭐', 'bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-800', 'role' => 'bhabinkamtibmas'],
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

    {{-- Pencarian --}}
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <form action="{{ route('users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-center">
            <input type="hidden" name="role" value="{{ $filterRole }}">
            <div class="flex-1 w-full">
                <input type="text" name="q" value="{{ $search }}" placeholder="🔍 Cari nama, email, NIK, nomor HP, atau alamat..." class="w-full text-xs px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-200">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold transition cursor-pointer">Cari</button>
                @if($search)
                <a href="{{ route('users.index', ['role' => $filterRole]) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer border border-slate-200">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Pengguna --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-violet-100 text-violet-800 text-[10px] font-extrabold uppercase">Data</span>
                <h2 class="text-sm font-bold text-slate-900">Daftar Pengguna</h2>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-bold">{{ $users->count() }} orang</span>
            </div>
        </div>

        @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                        <th class="text-left px-4 py-3">#</th>
                        <th class="text-left px-4 py-3">Nama Lengkap</th>
                        <th class="text-left px-4 py-3">NIK</th>
                        <th class="text-left px-4 py-3">Email</th>
                        <th class="text-left px-4 py-3">No HP / WA</th>
                        <th class="text-left px-4 py-3">Peran</th>
                        <th class="text-left px-4 py-3">RT / RW</th>
                        <th class="text-left px-4 py-3">Alamat</th>
                        <th class="text-center px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $i => $u)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-4 py-3 text-slate-400 font-mono">{{ $i + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-black text-[11px] shrink-0
                                    {{ match($u->role) {
                                        'rw' => 'bg-violet-600',
                                        'rt' => 'bg-emerald-600',
                                        'petugas_ronda' => 'bg-amber-600',
                                        'bhabinkamtibmas' => 'bg-indigo-600',
                                        default => 'bg-blue-600'
                                    } }}">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 text-[11px]">{{ $u->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $u->no_rumah ? 'No. ' . $u->no_rumah : '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-600 text-[10px]">{{ $u->nik ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $u->phone ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $roleBadge = match($u->role) {
                                    'rw' => ['bg' => 'bg-violet-100 text-violet-800 border-violet-200', 'label' => 'Pengurus RW'],
                                    'rt' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'label' => 'Ketua RT'],
                                    'petugas_ronda' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'label' => 'Petugas Ronda'],
                                    'bhabinkamtibmas' => ['bg' => 'bg-indigo-100 text-indigo-800 border-indigo-200', 'label' => 'Bhabinkamtibmas'],
                                    default => ['bg' => 'bg-blue-100 text-blue-800 border-blue-200', 'label' => 'Warga'],
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold border {{ $roleBadge['bg'] }}">{{ $roleBadge['label'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">RT {{ $u->rt_id ?? '-' }} / RW {{ $u->rw_id ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-500 max-w-[150px] truncate">{{ $u->alamat ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button"
                                    onclick="editUser({{ json_encode($u) }})"
                                    class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition cursor-pointer border border-blue-200" title="Edit">
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
        <div class="p-8 text-center">
            <p class="text-slate-400 text-sm">Tidak ada pengguna ditemukan{{ $search ? ' untuk pencarian "' . $search . '"' : '' }}.</p>
        </div>
        @endif
    </div>

</div>

{{-- MODAL: TAMBAH PENGGUNA BARU --}}
<div id="modal-tambah" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-tambah')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-slate-200">
        <div class="sticky top-0 bg-white rounded-t-3xl border-b border-slate-100 px-5 py-4 flex items-center justify-between z-10">
            <div>
                <h3 class="text-sm font-black text-slate-900">Tambah Pengguna Baru</h3>
                <p class="text-[10px] text-slate-500">Isi data lengkap untuk mendaftarkan pengguna ke sistem JagaWarga.</p>
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
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="nama@jagawarga.local" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                    <input type="tel" name="phone" placeholder="08xxxxxxxxxx" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Peran (Role) <span class="text-rose-500">*</span></label>
                    <select name="role" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 bg-white">
                        <option value="warga">🏠 Warga Lingkungan</option>
                        <option value="petugas_ronda">🛡️ Petugas Ronda</option>
                        <option value="rt">🏘️ Ketua RT</option>
                        <option value="rw">🗺️ Pengurus RW</option>
                        <option value="bhabinkamtibmas">⭐ Bhabinkamtibmas</option>
                    </select>
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
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-slate-200">
        <div class="sticky top-0 bg-white rounded-t-3xl border-b border-slate-100 px-5 py-4 flex items-center justify-between z-10">
            <div>
                <h3 class="text-sm font-black text-slate-900">Edit Data Pengguna</h3>
                <p class="text-[10px] text-slate-500">Perbarui informasi pengguna. Kosongkan password jika tidak ingin mengubah.</p>
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
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" id="edit-email" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                    <input type="tel" name="phone" id="edit-phone" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Peran (Role) <span class="text-rose-500">*</span></label>
                    <select name="role" id="edit-role" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-violet-500 bg-white">
                        <option value="warga">🏠 Warga Lingkungan</option>
                        <option value="petugas_ronda">🛡️ Petugas Ronda</option>
                        <option value="rt">🏘️ Ketua RT</option>
                        <option value="rw">🗺️ Pengurus RW</option>
                        <option value="bhabinkamtibmas">⭐ Bhabinkamtibmas</option>
                    </select>
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

{{-- MODAL: KONFIRMASI HAPUS --}}
<div id="modal-hapus" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-hapus')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm border border-slate-200 p-6 text-center space-y-4">
        <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto text-2xl">🗑️</div>
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

@endsection

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = '';
    }

    function editUser(user) {
        const form = document.getElementById('form-edit-user');
        form.action = '/users/' + user.id;
        document.getElementById('edit-name').value = user.name || '';
        document.getElementById('edit-nik').value = user.nik || '';
        document.getElementById('edit-email').value = user.email || '';
        document.getElementById('edit-phone').value = user.phone || '';
        document.getElementById('edit-role').value = user.role || 'warga';
        document.getElementById('edit-rt_id').value = user.rt_id || '01';
        document.getElementById('edit-rw_id').value = user.rw_id || '02';
        document.getElementById('edit-alamat').value = user.alamat || '';
        document.getElementById('edit-no_rumah').value = user.no_rumah || '';
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
            ['modal-tambah', 'modal-edit', 'modal-hapus'].forEach(id => closeModal(id));
        }
    });
</script>
@endpush

@extends('layouts.app')

@section('title', 'Pengaturan Sistem & Pembaruan Web - JagaWarga RW 02')

@section('content')
<div class="space-y-6">

    {{-- Header Pengaturan Sistem --}}
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-5 sm:p-6 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <div class="absolute -right-6 -bottom-6 w-48 h-48 rounded-full bg-violet-600/20 blur-2xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-violet-600/30 border border-violet-400/40 flex items-center justify-center text-2xl shadow-inner">
                    ⚙️
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md bg-violet-500/20 text-violet-300 text-[10px] font-extrabold uppercase tracking-wider border border-violet-500/30">
                            Sistem & Pemeliharaan
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-500/30 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Auto-Update GitHub PAT
                        </span>
                    </div>
                    <h1 class="text-lg sm:text-xl font-black tracking-tight mt-1">Pengaturan Web & Pembaruan Sistem</h1>
                    <p class="text-xs text-slate-300 mt-0.5">Kelola token GitHub PAT, sinkronisasi pembaruan aplikasi, log terminal, dan symlink media storage.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5 border border-slate-700 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Feedback Alerts --}}
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold flex items-center gap-2 animate-in fade-in">
        <span class="text-base">✅</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold flex items-center gap-2 animate-in fade-in">
        <span class="text-base">🚫</span>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- BARIS 1: PENGATURAN GITHUB PAT & STATUS PEMBARUAN LIVE --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- FORM KONFIGURASI GITHUB PAT (7 Kolom) --}}
        <div class="lg:col-span-7 bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Konfigurasi GitHub PAT</h2>
                        <p class="text-[11px] text-slate-500">Kredensial token untuk sinkronisasi pembaruan repositori</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded bg-violet-100 text-violet-800 text-[10px] font-bold">Tabel: pengaturan</span>
            </div>

            <form action="{{ route('settings.github.save') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Input Repositori --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Repositori GitHub <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs font-mono">
                            github.com/
                        </span>
                        <input type="text" name="github_repo" value="{{ old('github_repo', $githubConfig['github_repo']) }}" required
                            placeholder="aangwie/jagawarga"
                            class="w-full text-xs pl-24 pr-3 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-200 font-mono">
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">Format: <code class="font-mono text-violet-700">username/nama-repo</code> (contoh: <code class="font-mono text-violet-700">aangwie/jagawarga</code>)</p>
                </div>

                {{-- Input Branch --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Branch Target <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="github_branch" value="{{ old('github_branch', $githubConfig['github_branch']) }}" required
                        placeholder="main"
                        class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-200 font-mono">
                    <p class="text-[10px] text-slate-500 mt-1">Gunakan branch produksi utama (biasanya <code class="font-mono">main</code> atau <code class="font-mono">master</code>).</p>
                </div>

                {{-- Input GitHub Personal Access Token (PAT) --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">
                            GitHub Personal Access Token (PAT)
                        </label>
                        @if(!empty($githubConfig['github_pat']))
                        <span class="text-[10px] px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Token Tersimpan
                        </span>
                        @else
                        <span class="text-[10px] px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 font-bold">
                            Belum Ada Token
                        </span>
                        @endif
                    </div>

                    <div class="relative">
                        <input type="password" id="pat-input" name="github_pat"
                            placeholder="{{ !empty($maskedPat) ? 'Kosongkan jika tidak ingin mengubah token (' . $maskedPat . ')' : 'ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' }}"
                            class="w-full text-xs pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-200 font-mono">
                        <button type="button" onclick="togglePatVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer" title="Lihat/Sembunyikan Token">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @if(!empty($maskedPat))
                    <p class="text-[10px] text-slate-500 mt-1">Token aktif tersimpan: <span class="font-mono text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded">{{ $maskedPat }}</span></p>
                    @endif
                </div>

                {{-- Info Bantuan Pembuatan PAT --}}
                <div class="p-3 rounded-2xl bg-indigo-50/70 border border-indigo-100 text-[11px] text-indigo-900 space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <span>💡</span>
                        <span>Cara Membuat GitHub PAT (Personal Access Token):</span>
                    </div>
                    <ol class="list-decimal list-inside text-[10px] space-y-0.5 text-indigo-800 ml-1">
                        <li>Buka GitHub &rarr; <strong>Settings</strong> &rarr; <strong>Developer settings</strong> &rarr; <strong>Personal access tokens</strong> &rarr; <strong>Tokens (classic)</strong>.</li>
                        <li>Klik <strong>Generate new token</strong>, beri deskripsi (misal: <em>JagaWarga Auto-Update</em>).</li>
                        <li>Centang scope <strong><code>repo</code></strong> (Full control of private repositories).</li>
                        <li>Salin token yang berawalan <code class="bg-indigo-100 px-1 rounded">ghp_...</code> dan tempelkan pada formulir di atas.</li>
                    </ol>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold transition shadow-md hover:shadow-violet-500/20 cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- PANEL STATUS PEMBARUAN & AKSI UPDATE (5 Kolom) --}}
        <div class="lg:col-span-5 space-y-6">

            {{-- Kartu Status Commit & Pembaruan --}}
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            🚀
                        </span>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Status & Aksi Update</h2>
                            <p class="text-[11px] text-slate-500">Sinkronisasi kode lokal dengan GitHub</p>
                        </div>
                    </div>
                </div>

                {{-- Status Git Lokal --}}
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Branch Lokal:</span>
                        <span class="font-mono font-bold text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-200">{{ $localGitInfo['branch'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Commit SHA Lokal:</span>
                        <span class="font-mono font-bold text-violet-700 bg-violet-50 px-2 py-0.5 rounded border border-violet-200">{{ $localGitInfo['short_sha'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Pesan Commit Terakhir:</span>
                        <span class="font-semibold text-slate-700 text-right truncate max-w-[180px]" title="{{ $localGitInfo['commit_message'] }}">{{ $localGitInfo['commit_message'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Waktu Update Terakhir:</span>
                        <span id="label-last-update" class="font-semibold text-slate-700 text-right">
                            {{ $githubConfig['last_update_at'] ? date('d/m/Y H:i', strtotime($githubConfig['last_update_at'])) : 'Belum pernah update' }}
                        </span>
                    </div>
                </div>

                {{-- Hasil Pengecekan GitHub API --}}
                <div id="check-result-box" class="hidden p-3.5 rounded-2xl border text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700">Commit GitHub:</span>
                        <span id="remote-sha" class="font-mono font-bold px-2 py-0.5 rounded"></span>
                    </div>
                    <div class="text-[11px] text-slate-600" id="remote-message"></div>
                    <div class="text-[10px] text-slate-400" id="remote-author-date"></div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="space-y-2 pt-2">
                    {{-- Tombol Cek Update --}}
                    <button type="button" id="btn-check-update" onclick="checkGitHubUpdate()" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center justify-center gap-2 border border-slate-200 cursor-pointer">
                        <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span id="btn-check-text">Cek Pembaruan di GitHub</span>
                    </button>

                    {{-- Tombol Eksekusi Update --}}
                    <button type="button" id="btn-execute-update" onclick="confirmExecuteUpdate()" class="w-full px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-md hover:shadow-emerald-500/20 flex items-center justify-center gap-2 cursor-pointer border border-emerald-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                        <span id="btn-update-text">Perbarui Web Sekarang (Git Pull)</span>
                    </button>
                </div>
            </div>

            {{-- Kartu Symlink Storage --}}
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                            🔗
                        </span>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Symlink Storage Public</h2>
                            <p class="text-[11px] text-slate-500">Tautan folder penyimpanan media publik</p>
                        </div>
                    </div>
                    @if($storageLinkStatus['connected'])
                    <span id="badge-storage-status" class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold flex items-center gap-1 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Terhubung (Aktif)
                    </span>
                    @else
                    <span id="badge-storage-status" class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-extrabold flex items-center gap-1 border border-rose-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Belum Terhubung
                    </span>
                    @endif
                </div>

                <div class="space-y-2 text-xs">
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5 text-[11px]">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Asal (Target):</span>
                            <span class="font-mono text-slate-700 truncate max-w-[200px]" title="{{ $storageLinkStatus['target_dir'] }}">storage/app/public</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Tautan Publik:</span>
                            <span class="font-mono text-slate-700 truncate max-w-[200px]" title="{{ $storageLinkStatus['public_link'] }}">public/storage</span>
                        </div>
                    </div>
                </div>

                <button type="button" id="btn-storage-link" onclick="generateStorageLink()" class="w-full px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-md hover:shadow-blue-500/20 flex items-center justify-center gap-2 cursor-pointer border border-blue-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    <span id="btn-storage-text">Hubungkan Symlink Storage (storage:link)</span>
                </button>
            </div>

        </div>

    </div>

    {{-- BARIS 2: TERMINAL CONSOLE LOG HASIL PEMBARUAN --}}
    <div class="bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-800 shadow-xl space-y-3 text-slate-200">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                </div>
                <div class="h-4 w-px bg-slate-700 mx-1"></div>
                <h3 class="text-xs font-mono font-bold text-slate-300">Terminal Console &mdash; Log Eksekusi Pembaruan Web</h3>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="copyTerminalLog()" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-mono transition border border-slate-700 flex items-center gap-1 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                    <span id="copy-btn-text">Salin Log</span>
                </button>
                <button type="button" onclick="clearTerminalLog()" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-mono transition border border-slate-700 cursor-pointer">
                    Bersihkan Layar
                </button>
            </div>
        </div>

        {{-- Area Tampilan Terminal --}}
        <div class="relative bg-slate-950 rounded-2xl p-4 border border-slate-800/80 shadow-inner font-mono text-xs overflow-hidden">
            <pre id="terminal-console" class="overflow-x-auto overflow-y-auto max-h-72 text-emerald-400 whitespace-pre-wrap leading-relaxed select-text">{{ $githubConfig['last_update_log'] ?: "[Terminal Siaga] Tekan tombol 'Perbarui Web Sekarang' untuk memulai proses git pull, pembersihan cache artisan, dan migrasi database.\nLog eksekusi akan tercetak di sini secara detail." }}</pre>
        </div>
    </div>

    {{-- BARIS 3: INFORMASI LINGKUNGAN SERVER --}}
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Informasi Lingkungan Server & Runtime</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-[10px] text-slate-500 font-bold uppercase">Versi PHP</p>
                <p class="text-sm font-black text-slate-800 mt-0.5">{{ $systemInfo['php_version'] }}</p>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-[10px] text-slate-500 font-bold uppercase">Laravel</p>
                <p class="text-sm font-black text-violet-700 mt-0.5">v{{ $systemInfo['laravel_version'] }}</p>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-[10px] text-slate-500 font-bold uppercase">Database Driver</p>
                <p class="text-sm font-black text-slate-800 mt-0.5 uppercase">{{ $systemInfo['db_driver'] }}</p>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-[10px] text-slate-500 font-bold uppercase">Lingkungan App</p>
                <p class="text-sm font-black text-emerald-700 mt-0.5 uppercase">{{ $systemInfo['app_env'] }}</p>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 col-span-2">
                <p class="text-[10px] text-slate-500 font-bold uppercase">Sistem Operasi</p>
                <p class="text-xs font-bold text-slate-800 mt-0.5 truncate" title="{{ $systemInfo['server_os'] }}">{{ $systemInfo['server_os'] }}</p>
            </div>
        </div>
    </div>

</div>

{{-- MODAL KONFIRMASI UPDATE WEB --}}
<div id="modal-confirm-update" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-center space-y-4 animate-in zoom-in-95">
        <div class="w-16 h-16 rounded-3xl bg-amber-100 text-amber-700 text-3xl flex items-center justify-center mx-auto shadow-inner">
            🚀
        </div>
        <div>
            <h3 class="text-base font-extrabold text-slate-900">Konfirmasi Pembaruan Web</h3>
            <p class="text-xs text-slate-500 mt-1">
                Aplikasi akan menarik perubahan kode terbaru dari repositori <strong class="text-slate-800 font-mono">{{ $githubConfig['github_repo'] }}</strong> branch <strong class="text-slate-800 font-mono">{{ $githubConfig['github_branch'] }}</strong>, serta mengeksekusi migrasi database dan optimasi cache.
            </p>
        </div>
        <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 text-left">
            ⚠️ <strong>Catatan:</strong> Pastikan koneksi internet stabil. Proses biasanya memerlukan waktu 5–20 detik.
        </div>
        <div class="flex gap-2 pt-2">
            <button type="button" onclick="closeModal('modal-confirm-update')" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                Batal
            </button>
            <button type="button" onclick="startUpdateProcess()" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-md cursor-pointer">
                Ya, Mulai Update
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePatVisibility() {
        const input = document.getElementById('pat-input');
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function confirmExecuteUpdate() {
        openModal('modal-confirm-update');
    }

    // Pengecekan GitHub API via AJAX
    function checkGitHubUpdate() {
        const btn = document.getElementById('btn-check-update');
        const btnText = document.getElementById('btn-check-text');
        const resultBox = document.getElementById('check-result-box');
        const remoteSha = document.getElementById('remote-sha');
        const remoteMessage = document.getElementById('remote-message');
        const remoteAuthorDate = document.getElementById('remote-author-date');

        btn.disabled = true;
        btnText.innerHTML = '⏳ Menghubungi GitHub API...';

        fetch('{{ route("settings.github.check") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async (res) => {
            const data = await res.json();
            resultBox.classList.remove('hidden');

            if (res.ok && data.success) {
                remoteSha.innerText = data.commit_sha;
                remoteMessage.innerText = `"${data.commit_message}"`;
                remoteAuthorDate.innerText = `Oleh ${data.commit_author} pada ${data.commit_date}`;

                if (data.is_up_to_date) {
                    resultBox.className = 'p-3.5 rounded-2xl border border-emerald-200 bg-emerald-50 text-xs space-y-2';
                    remoteSha.className = 'font-mono font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800';
                    alert(`✅ ${data.message}`);
                } else {
                    resultBox.className = 'p-3.5 rounded-2xl border border-amber-200 bg-amber-50 text-xs space-y-2';
                    remoteSha.className = 'font-mono font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-800';
                    alert(`🚀 ${data.message} Silakan klik tombol 'Perbarui Web Sekarang'.`);
                }
            } else {
                resultBox.className = 'p-3.5 rounded-2xl border border-rose-200 bg-rose-50 text-xs space-y-2';
                remoteSha.className = 'font-mono font-bold px-2 py-0.5 rounded bg-rose-100 text-rose-800';
                remoteSha.innerText = 'Error';
                remoteMessage.innerText = data.message || 'Gagal mengecek pembaruan GitHub.';
                remoteAuthorDate.innerText = '';
                alert(`🚫 Gagal: ${data.message}`);
            }
        })
        .catch((err) => {
            alert('Kesalahan jaringan: ' + err.message);
        })
        .finally(() => {
            btn.disabled = false;
            btnText.innerHTML = 'Cek Pembaruan di GitHub';
        });
    }

    // Eksekusi Update Web Realtime via AJAX
    function startUpdateProcess() {
        closeModal('modal-confirm-update');

        const btn = document.getElementById('btn-execute-update');
        const btnText = document.getElementById('btn-update-text');
        const consoleEl = document.getElementById('terminal-console');

        btn.disabled = true;
        btnText.innerHTML = '⏳ Sedang Memperbarui Web...';

        consoleEl.innerText = `[${new Date().toLocaleTimeString()}] Mengirim permintaan pembaruan ke server...\n[${new Date().toLocaleTimeString()}] Menghubungkan ke GitHub melalui Personal Access Token...\nHarap tunggu sebentar, proses sedang berjalan di background...`;

        fetch('{{ route("settings.github.update") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async (res) => {
            const data = await res.json();
            if (data.logs) {
                consoleEl.innerText = data.logs;
            }

            if (data.last_update_at) {
                document.getElementById('label-last-update').innerText = data.last_update_at;
            }

            if (res.ok && data.success) {
                alert(`🎉 ${data.message}`);
            } else {
                alert(`⚠️ ${data.message || 'Pembaruan selesai dengan catatan.'}`);
            }
        })
        .catch((err) => {
            consoleEl.innerText += `\n[ERROR JARINGAN] ${err.message}`;
            alert('Koneksi terputus saat memperbarui: ' + err.message);
        })
        .finally(() => {
            btn.disabled = false;
            btnText.innerHTML = 'Perbarui Web Sekarang (Git Pull)';
        });
    }

    // Generate Symlink Storage via AJAX
    function generateStorageLink() {
        const btn = document.getElementById('btn-storage-link');
        const btnText = document.getElementById('btn-storage-text');
        const badge = document.getElementById('badge-storage-status');
        const consoleEl = document.getElementById('terminal-console');

        btn.disabled = true;
        btnText.innerHTML = '⏳ Menghubungkan Symlink...';

        fetch('{{ route("settings.storage_link") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async (res) => {
            const data = await res.json();

            if (data.log) {
                consoleEl.innerText += `\n\n=== SYMLINK STORAGE LOG ===\n${data.log}`;
            }

            if (res.ok && data.success) {
                badge.className = 'px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold flex items-center gap-1 border border-emerald-200';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Terhubung (Aktif)';
                alert(`✅ ${data.message}`);
            } else {
                alert(`🚫 ${data.message}`);
            }
        })
        .catch((err) => {
            alert('Gagal menghubungi server: ' + err.message);
        })
        .finally(() => {
            btn.disabled = false;
            btnText.innerHTML = 'Hubungkan Symlink Storage (storage:link)';
        });
    }

    function copyTerminalLog() {
        const text = document.getElementById('terminal-console').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('copy-btn-text');
            btn.innerText = 'Tersalin!';
            setTimeout(() => { btn.innerText = 'Salin Log'; }, 2000);
        });
    }

    function clearTerminalLog() {
        document.getElementById('terminal-console').innerText = '[Terminal Dibersihkan] Siap menerima log eksekusi berikutnya.';
    }
</script>
@endpush
@endsection

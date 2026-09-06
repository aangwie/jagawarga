<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class SettingController extends Controller
{
    /**
     * Halaman Utama Pengaturan Web
     */
    public function index()
    {
        $githubConfig = Pengaturan::getGithubConfig();

        // Samarkan token PAT untuk keamanan tampilan
        $maskedPat = '';
        if (!empty($githubConfig['github_pat'])) {
            $rawPat = $githubConfig['github_pat'];
            $len = strlen($rawPat);
            if ($len > 8) {
                $maskedPat = substr($rawPat, 0, 4) . str_repeat('*', max(4, $len - 8)) . substr($rawPat, -4);
            } else {
                $maskedPat = '********';
            }
        }

        // Cek info Git lokal
        $localGitInfo = $this->getLocalGitInfo();

        // Cek status symlink storage
        $storageLinkStatus = $this->checkStorageLinkStatus();

        // Info lingkungan sistem
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_os' => PHP_OS_FAMILY . ' (' . php_uname('s') . ' ' . php_uname('r') . ')',
            'db_driver' => config('database.default'),
            'app_env' => config('app.env'),
            'storage_public_path' => storage_path('app/public'),
            'public_storage_path' => public_path('storage'),
        ];

        return view('settings.index', compact(
            'githubConfig',
            'maskedPat',
            'localGitInfo',
            'storageLinkStatus',
            'systemInfo'
        ));
    }

    /**
     * Simpan Konfigurasi GitHub PAT
     */
    public function saveGithub(Request $request)
    {
        $validated = $request->validate([
            'github_repo' => 'required|string|max:255',
            'github_branch' => 'required|string|max:100',
            'github_pat' => 'nullable|string|max:255',
        ]);

        // Bersihkan nama repo (jika user memasukkan full URL)
        $cleanRepo = $this->sanitizeRepoName($validated['github_repo']);

        Pengaturan::set('github_repo', $cleanRepo, 'github', 'Repository GitHub (owner/repo)');
        Pengaturan::set('github_branch', trim($validated['github_branch']), 'github', 'Branch Target');

        // Hanya perbarui PAT jika pengguna mengisi input baru
        if (!empty($validated['github_pat'])) {
            Pengaturan::set('github_pat', trim($validated['github_pat']), 'github', 'GitHub Personal Access Token');
        }

        return redirect()->route('settings.index')
            ->with('success', 'Konfigurasi GitHub PAT berhasil disimpan ke tabel pengaturan database!');
    }

    /**
     * Cek Commit Terbaru di GitHub API via PAT
     */
    public function checkUpdate(Request $request)
    {
        $config = Pengaturan::getGithubConfig();
        $pat = $config['github_pat'];
        $repo = $config['github_repo'];
        $branch = $config['github_branch'] ?: 'main';

        if (empty($repo)) {
            return response()->json([
                'success' => false,
                'message' => 'Nama repositori GitHub belum diatur.',
            ], 422);
        }

        $cleanRepo = $this->sanitizeRepoName($repo);
        $url = "https://api.github.com/repos/{$cleanRepo}/commits/{$branch}";

        try {
            $headers = [
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'JagaWarga-App',
            ];

            if (!empty($pat)) {
                $headers['Authorization'] = 'Bearer ' . $pat;
            }

            $response = Http::timeout(10)->withHeaders($headers)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $sha = $data['sha'] ?? '';
                $shortSha = substr($sha, 0, 7);
                $message = $data['commit']['message'] ?? '-';
                $author = $data['commit']['author']['name'] ?? '-';
                $date = $data['commit']['author']['date'] ?? '-';

                $localInfo = $this->getLocalGitInfo();
                $isUpToDate = (!empty($localInfo['short_sha']) && $localInfo['short_sha'] === $shortSha);

                return response()->json([
                    'success' => true,
                    'repo' => $cleanRepo,
                    'branch' => $branch,
                    'commit_sha' => $shortSha,
                    'full_sha' => $sha,
                    'commit_message' => $message,
                    'commit_author' => $author,
                    'commit_date' => $date,
                    'local_sha' => $localInfo['short_sha'] ?? 'Unknown',
                    'is_up_to_date' => $isUpToDate,
                    'message' => $isUpToDate
                        ? 'Aplikasi Anda sudah versi terbaru (' . $shortSha . ').'
                        : 'Pembaruan baru tersedia di GitHub (' . $shortSha . ').',
                ]);
            } else {
                $status = $response->status();
                $body = $response->json();
                $msg = $body['message'] ?? 'Gagal mengakses GitHub API';

                if ($status === 401) {
                    $msg = 'Autentikasi Gagal: Token GitHub PAT tidak valid atau kedaluwarsa.';
                } elseif ($status === 404) {
                    $msg = "Repositori '{$cleanRepo}' atau branch '{$branch}' tidak ditemukan. Jika repositori private, pastikan GitHub PAT memiliki izin (scope) 'repo'.";
                }

                return response()->json([
                    'success' => false,
                    'message' => $msg . " (HTTP {$status})",
                ], $status);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan koneksi ke GitHub: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eksekusi Update Web dari GitHub & Catat Log Terminal
     */
    public function executeUpdate(Request $request)
    {
        $config = Pengaturan::getGithubConfig();
        $pat = $config['github_pat'];
        $repo = $config['github_repo'];
        $branch = $config['github_branch'] ?: 'main';

        if (empty($repo)) {
            return response()->json([
                'success' => false,
                'message' => 'Repositori GitHub belum dikonfigurasi.',
            ], 422);
        }

        $cleanRepo = $this->sanitizeRepoName($repo);
        $logs = [];
        $startTime = microtime(true);
        $logs[] = "[" . date('Y-m-d H:i:s') . "] === MEMULAI PROSES PEMBARUAN APLIKASI JAGAWARGA ===";
        $logs[] = "[" . date('Y-m-d H:i:s') . "] Target Repositori : https://github.com/{$cleanRepo}";
        $logs[] = "[" . date('Y-m-d H:i:s') . "] Target Branch     : {$branch}";
        $logs[] = "[" . date('Y-m-d H:i:s') . "] Metode Auth       : " . (!empty($pat) ? "GitHub PAT Terdeteksi" : "Publik (Tanpa PAT)");

        $isSuccess = false;

        try {
            // Cek apakah direktori .git tersedia
            $hasGitDir = is_dir(base_path('.git'));

            if ($hasGitDir) {
                $logs[] = "[" . date('Y-m-d H:i:s') . "] Menjalankan 'git pull' melalui remote terautentikasi...";

                // Siapkan URL remote dengan PAT
                if (!empty($pat)) {
                    $remoteUrl = "https://{$pat}@github.com/{$cleanRepo}.git";
                } else {
                    $remoteUrl = "https://github.com/{$cleanRepo}.git";
                }

                // Jalankan git pull
                $gitProcess = Process::fromShellCommandline("git pull \"{$remoteUrl}\" {$branch}", base_path());
                $gitProcess->setTimeout(180);
                $gitProcess->run();

                $rawOutput = $gitProcess->getOutput() . $gitProcess->getErrorOutput();

                // Sensor token PAT dari log terminal agar tidak bocor
                if (!empty($pat)) {
                    $rawOutput = str_replace($pat, 'ghp_****************', $rawOutput);
                }

                $logs[] = trim($rawOutput) ?: "[git] No output returned.";

                if ($gitProcess->isSuccessful() || str_contains($rawOutput, 'Already up to date') || str_contains($rawOutput, 'Updating')) {
                    $isSuccess = true;
                    $logs[] = "[" . date('Y-m-d H:i:s') . "] Git pull selesai dengan sukses.";
                } else {
                    $logs[] = "[" . date('Y-m-d H:i:s') . "] PERINGATAN: Git pull menghasilkan exit code non-zero.";
                    // Coba fallback unduh arsip jika git gagal
                    $isSuccess = $this->fallbackZipDownload($cleanRepo, $branch, $pat, $logs);
                }
            } else {
                $logs[] = "[" . date('Y-m-d H:i:s') . "] Folder .git tidak ditemukan. Melakukan update via arsip GitHub API...";
                $isSuccess = $this->fallbackZipDownload($cleanRepo, $branch, $pat, $logs);
            }

            // Jalankan artisan maintenance
            $logs[] = "------------------------------------------------------------";
            $logs[] = "[" . date('Y-m-d H:i:s') . "] Menjalankan 'php artisan optimize:clear'...";
            Artisan::call('optimize:clear');
            $logs[] = trim(Artisan::output());

            $logs[] = "[" . date('Y-m-d H:i:s') . "] Menjalankan 'php artisan migrate --force'...";
            Artisan::call('migrate', ['--force' => true]);
            $logs[] = trim(Artisan::output());

            $duration = round(microtime(true) - $startTime, 2);
            $logs[] = "============================================================";
            $logs[] = "[" . date('Y-m-d H:i:s') . "] Status Akhir   : " . ($isSuccess ? "SUKSES" : "SELESAI DENGAN CATATAN");
            $logs[] = "[" . date('Y-m-d H:i:s') . "] Total Durasi   : {$duration} detik";

        } catch (\Throwable $e) {
            $isSuccess = false;
            $logs[] = "------------------------------------------------------------";
            $logs[] = "[" . date('Y-m-d H:i:s') . "] FATAL ERROR: " . $e->getMessage();
        }

        $fullLog = implode("\n", $logs);

        // Simpan log ke tabel pengaturan
        Pengaturan::set('last_update_at', now()->toDateTimeString(), 'github', 'Waktu Terakhir Update Web');
        Pengaturan::set('last_update_status', $isSuccess ? 'success' : 'failed', 'github', 'Status Terakhir Update');
        Pengaturan::set('last_update_log', $fullLog, 'github', 'Log Terminal Pembaruan Terakhir');

        return response()->json([
            'success' => $isSuccess,
            'message' => $isSuccess ? 'Pembaruan web berhasil diselesaikan!' : 'Proses update selesai dengan peringatan/catatan.',
            'last_update_at' => now()->translatedFormat('d F Y, H:i:s'),
            'logs' => $fullLog,
        ]);
    }

    /**
     * Hubungkan Storage ke Folder Public (Symlink Storage)
     */
    public function generateStorageLink(Request $request)
    {
        $logs = [];
        $logs[] = "[" . date('Y-m-d H:i:s') . "] Menjalankan perintah pembuatan symlink storage...";

        $publicStorage = public_path('storage');
        $targetStorage = storage_path('app/public');

        // Pastikan folder target storage/app/public ada
        if (!File::isDirectory($targetStorage)) {
            File::makeDirectory($targetStorage, 0755, true);
            $logs[] = "Direktori target '{$targetStorage}' berhasil disiapkan.";
        }

        try {
            // Jalankan artisan storage:link
            Artisan::call('storage:link');
            $artisanOutput = trim(Artisan::output());
            $logs[] = $artisanOutput ?: "Artisan storage:link selesai dieksekusi.";

            // Periksa ulang apakah symlink berhasil terhubung
            $isConnected = $this->checkStorageLinkStatus()['connected'];

            if ($isConnected) {
                return response()->json([
                    'success' => true,
                    'message' => 'Symlink Storage BERHASIL DIBUAT! Folder public/storage kini terhubung dengan storage/app/public.',
                    'log' => implode("\n", $logs),
                ]);
            } else {
                // Coba buat symlink secara manual menggunakan PHP symlink
                if (@symlink($targetStorage, $publicStorage)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Symlink Storage BERHASIL DIHUBUNGKAN via native symlink!',
                        'log' => implode("\n", $logs),
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat symlink otomatis. Pastikan web server memiliki izin membuat link simbolik (Run as Administrator jika di Windows).',
                    'log' => implode("\n", $logs),
                ], 500);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat symlink: ' . $e->getMessage(),
                'log' => $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================================
    // PRIVATE HELPER METHODS
    // =========================================================================

    /**
     * Sanitasi format nama repo (misal dari URL menjadi owner/repo)
     */
    private function sanitizeRepoName(string $input): string
    {
        $clean = trim($input);
        $clean = preg_replace('#^https?://github\.com/#i', '', $clean);
        $clean = preg_replace('#\.git$#i', '', $clean);
        return trim($clean, '/');
    }

    /**
     * Cek status ketersediaan dan validitas symlink storage
     */
    private function checkStorageLinkStatus(): array
    {
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');

        $exists = file_exists($linkPath);
        $isLink = is_link($linkPath);

        // Di windows, file_exists atau is_dir pada junction/link yang valid menghasilkan true
        $connected = ($isLink || (is_dir($linkPath) && $exists));

        return [
            'exists' => $exists,
            'is_link' => $isLink,
            'connected' => $connected,
            'target_dir' => $targetPath,
            'public_link' => $linkPath,
        ];
    }

    /**
     * Dapatkan informasi commit Git lokal dari folder .git
     */
    private function getLocalGitInfo(): array
    {
        $gitDir = base_path('.git');
        if (!is_dir($gitDir)) {
            return [
                'branch' => 'Unknown',
                'short_sha' => 'Unknown',
                'full_sha' => 'Unknown',
                'commit_message' => 'Tidak menggunakan Git',
                'commit_date' => '-',
            ];
        }

        $branch = 'main';
        $headFile = $gitDir . DIRECTORY_SEPARATOR . 'HEAD';

        if (file_exists($headFile)) {
            $headContent = trim(file_get_contents($headFile));
            if (str_starts_with($headContent, 'ref: refs/heads/')) {
                $branch = str_replace('ref: refs/heads/', '', $headContent);
            }
        }

        $sha = '';
        $refFile = $gitDir . DIRECTORY_SEPARATOR . 'refs' . DIRECTORY_SEPARATOR . 'heads' . DIRECTORY_SEPARATOR . $branch;
        if (file_exists($refFile)) {
            $sha = trim(file_get_contents($refFile));
        }

        // Jika tidak ada di refs/heads, cek di packed-refs
        if (empty($sha)) {
            $packedRefs = $gitDir . DIRECTORY_SEPARATOR . 'packed-refs';
            if (file_exists($packedRefs)) {
                $lines = file($packedRefs, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (str_contains($line, 'refs/heads/' . $branch)) {
                        $parts = explode(' ', trim($line));
                        $sha = $parts[0] ?? '';
                        break;
                    }
                }
            }
        }

        $commitMsg = '';
        $commitDate = '-';
        $commitMsgFile = $gitDir . DIRECTORY_SEPARATOR . 'COMMIT_EDITMSG';
        if (file_exists($commitMsgFile)) {
            $commitMsg = trim(file_get_contents($commitMsgFile));
            $commitDate = date('Y-m-d H:i:s', filemtime($commitMsgFile));
        }

        return [
            'branch' => $branch,
            'short_sha' => !empty($sha) ? substr($sha, 0, 7) : 'Unknown',
            'full_sha' => $sha ?: 'Unknown',
            'commit_message' => $commitMsg ?: '-',
            'commit_date' => $commitDate,
        ];
    }

    /**
     * Fallback pembaruan kode via unduhan Zipball GitHub API
     */
    private function fallbackZipDownload(string $repo, string $branch, ?string $pat, array &$logs): bool
    {
        $logs[] = "[" . date('Y-m-d H:i:s') . "] Menjalankan fallback: mengunduh arsip zipball dari GitHub API...";

        if (!class_exists('ZipArchive')) {
            $logs[] = "[" . date('Y-m-d H:i:s') . "] Ekstensi PHP ZipArchive tidak aktif. Fallback dibatalkan.";
            return false;
        }

        $zipUrl = "https://api.github.com/repos/{$repo}/zipball/{$branch}";

        $headers = [
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'JagaWarga-App',
        ];

        if (!empty($pat)) {
            $headers['Authorization'] = 'Bearer ' . $pat;
        }

        $response = Http::timeout(120)->withHeaders($headers)->get($zipUrl);

        if (!$response->successful()) {
            $logs[] = "[" . date('Y-m-d H:i:s') . "] Gagal mengunduh zipball (HTTP " . $response->status() . ")";
            return false;
        }

        $tempZip = storage_path('app/temp_update_' . time() . '.zip');
        file_put_contents($tempZip, $response->body());
        $logs[] = "[" . date('Y-m-d H:i:s') . "] Arsip berhasil diunduh ke storage temporer (" . round(strlen($response->body()) / 1024, 2) . " KB).";

        $zip = new \ZipArchive();
        if ($zip->open($tempZip) === true) {
            $extractTemp = storage_path('app/temp_extract_' . time());
            @mkdir($extractTemp, 0755, true);
            $zip->extractTo($extractTemp);
            $zip->close();
            @unlink($tempZip);

            // GitHub zipball memiliki root subfolder (misal: user-repo-sha)
            $subfolders = array_diff(scandir($extractTemp), ['.', '..']);
            $firstFolder = reset($subfolders);
            $sourceDir = $firstFolder ? $extractTemp . DIRECTORY_SEPARATOR . $firstFolder : $extractTemp;

            // Salin file yang diperbarui ke base_path (kecuali file sensitif .env, storage, vendor)
            $filesUpdated = 0;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                $subPath = substr($item->getPathname(), strlen($sourceDir) + 1);

                // Lewati folder sensitif dan dependensi
                if (
                    str_starts_with($subPath, '.env') ||
                    str_starts_with($subPath, 'storage') ||
                    str_starts_with($subPath, 'vendor') ||
                    str_starts_with($subPath, '.git')
                ) {
                    continue;
                }

                $destPath = base_path($subPath);

                if ($item->isDir()) {
                    if (!is_dir($destPath)) {
                        @mkdir($destPath, 0755, true);
                    }
                } else {
                    @copy($item->getPathname(), $destPath);
                    $filesUpdated++;
                }
            }

            // Bersihkan temp extraction
            File::deleteDirectory($extractTemp);

            $logs[] = "[" . date('Y-m-d H:i:s') . "] Berhasil menyalin {$filesUpdated} file ke direktori aplikasi.";
            return true;
        }

        $logs[] = "[" . date('Y-m-d H:i:s') . "] Gagal membuka arsip ZIP update.";
        return false;
    }
}

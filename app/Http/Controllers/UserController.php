<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller
{
    /**
     * Daftar Pengguna dengan Filter Role & Pencarian
     */
    public function index(Request $request)
    {
        $filterRole = $request->get('role', 'semua');
        $search = $request->get('q', '');

        $query = User::with('roles');

        // Filter berdasarkan role (Many-to-Many atau kolom legacy)
        if ($filterRole !== 'semua' && in_array($filterRole, ['warga', 'petugas_ronda', 'rt', 'rw', 'bhabinkamtibmas', 'nakes_puskesmas'])) {
            $query->where(function ($q) use ($filterRole) {
                $q->whereHas('roles', function ($sub) use ($filterRole) {
                    $sub->where('name', $filterRole);
                })->orWhere('role', $filterRole);
            });
        }

        // Pencarian kata kunci
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('nama_ibu', 'like', "%{$search}%")
                  ->orWhere('rt_id', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->get();
        $availableRoles = Role::all();

        // Statistik per role (menghitung multi-role yang dimiliki)
        $stats = [
            'total' => User::count(),
            'warga' => User::whereHas('roles', fn($q) => $q->where('name', 'warga'))->orWhere('role', 'warga')->count(),
            'petugas_ronda' => User::whereHas('roles', fn($q) => $q->where('name', 'petugas_ronda'))->orWhere('role', 'petugas_ronda')->count(),
            'rt' => User::whereHas('roles', fn($q) => $q->where('name', 'rt'))->orWhere('role', 'rt')->count(),
            'rw' => User::whereHas('roles', fn($q) => $q->where('name', 'rw'))->orWhere('role', 'rw')->count(),
            'bhabinkamtibmas' => User::whereHas('roles', fn($q) => $q->where('name', 'bhabinkamtibmas'))->orWhere('role', 'bhabinkamtibmas')->count(),
            'nakes_puskesmas' => User::whereHas('roles', fn($q) => $q->where('name', 'nakes_puskesmas'))->orWhere('role', 'nakes_puskesmas')->count(),
        ];

        return view('users.index', compact('users', 'stats', 'filterRole', 'search', 'availableRoles'));
    }

    /**
     * Tambah Pengguna Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nik' => 'required|string|size:16|unique:users,nik',
            'phone' => 'nullable|string|max:20',
            'nama_ibu' => 'nullable|string|max:100',
            'roles' => 'nullable|array',
            'roles.*' => 'in:warga,petugas_ronda,rt,rw,bhabinkamtibmas,nakes_puskesmas',
            'role' => 'nullable|string|in:warga,petugas_ronda,rt,rw,bhabinkamtibmas,nakes_puskesmas',
            'rt_id' => 'nullable|string|max:5',
            'rw_id' => 'nullable|string|max:5',
            'alamat' => 'nullable|string|max:255',
            'no_rumah' => 'nullable|string|max:10',
            'password' => 'required|string|min:6',
        ]);

        $roles = $request->input('roles', []);
        if (empty($roles) && !empty($validated['role'])) {
            $roles = [$validated['role']];
        }
        if (empty($roles)) {
            $roles = ['warga'];
        }

        $validated['role'] = $roles[0];
        $validated['password'] = Hash::make($validated['password']);
        $validated['rw_id'] = $validated['rw_id'] ?? '02';
        $validated['is_nik_verified'] = $request->has('is_nik_verified') ? $request->boolean('is_nik_verified') : true;
        $validated['nik_verified_at'] = $validated['is_nik_verified'] ? now() : null;

        try {
            $user = User::create($validated);
            $user->syncRoles($roles);

            return redirect()->route('users.index', ['role' => $request->get('current_role', 'semua')])
                ->with('success', 'Pengguna baru "' . $user->name . '" berhasil ditambahkan dengan ' . count($roles) . ' peran.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan pengguna: ' . $e->getMessage());
        }
    }

    /**
     * Update Data Pengguna
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'nik' => ['required', 'string', 'size:16', Rule::unique('users', 'nik')->ignore($user->id)],
            'is_nik_verified' => 'nullable|boolean',
            'phone' => 'nullable|string|max:20',
            'nama_ibu' => 'nullable|string|max:100',
            'roles' => 'nullable|array',
            'roles.*' => 'in:warga,petugas_ronda,rt,rw,bhabinkamtibmas,nakes_puskesmas',
            'role' => 'nullable|string|in:warga,petugas_ronda,rt,rw,bhabinkamtibmas,nakes_puskesmas',
            'rt_id' => 'nullable|string|max:5',
            'rw_id' => 'nullable|string|max:5',
            'alamat' => 'nullable|string|max:255',
            'no_rumah' => 'nullable|string|max:10',
            'password' => 'nullable|string|min:6',
        ]);

        $roles = $request->input('roles', []);
        if (empty($roles) && !empty($validated['role'])) {
            $roles = [$validated['role']];
        }
        if (!empty($roles)) {
            $validated['role'] = $roles[0];
        }

        // Jika password diisi, hash; jika kosong, abaikan
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['rw_id'] = $validated['rw_id'] ?? '02';

        if ($request->has('is_nik_verified')) {
            $validated['is_nik_verified'] = $request->boolean('is_nik_verified');
            $validated['nik_verified_at'] = $validated['is_nik_verified'] ? ($user->nik_verified_at ?: now()) : null;
        }

        try {
            $user->update($validated);
            if (!empty($roles)) {
                $user->syncRoles($roles);
            }

            return redirect()->route('users.index', ['role' => $request->get('current_role', 'semua')])
                ->with('success', 'Data pengguna "' . $user->name . '" berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data pengguna: ' . $e->getMessage());
        }
    }

    /**
     * Toggle / Verifikasi NIK Pengguna
     */
    public function toggleNikVerification(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $newStatus = !$user->is_nik_verified;

        $user->update([
            'is_nik_verified' => $newStatus,
            'nik_verified_at' => $newStatus ? now() : null,
        ]);

        $statusMsg = $newStatus ? 'berhasil diverifikasi sah' : 'status verifikasinya telah dicabut';
        return back()->with('success', "NIK pengguna {$user->name} ({$user->nik}) {$statusMsg}.");
    }

    /**
     * Hapus Pengguna
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Proteksi: tidak bisa menghapus akun sendiri
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        try {
            $name = $user->name;
            $user->delete();
            return redirect()->route('users.index', ['role' => $request->get('current_role', 'semua')])
                ->with('success', 'Pengguna "' . $name . '" berhasil dihapus dari sistem.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Banyak Pengguna Sekaligus (Bulk Delete)
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'integer|exists:users,id',
        ]);

        $myId = Auth::id();
        $idsToDelete = array_values(array_filter($validated['selected_ids'], function ($id) use ($myId) {
            return (int)$id !== (int)$myId;
        }));

        if (empty($idsToDelete)) {
            return back()->with('error', 'Tidak ada pengguna yang dapat dihapus (akun Anda sendiri tidak dapat dihapus).');
        }

        try {
            $count = count($idsToDelete);

            DB::transaction(function () use ($idsToDelete) {
                User::whereIn('id', $idsToDelete)->delete();
            });

            $redirectParams = [];
            if ($request->filled('current_role') && $request->get('current_role') !== 'semua') {
                $redirectParams['role'] = $request->get('current_role');
            }

            return redirect()->route('users.index', $redirectParams)
                ->with('success', "Sebanyak {$count} data warga terpilih berhasil dihapus dari sistem.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus data warga terpilih: ' . $e->getMessage());
        }
    }

    /**
     * Unduh Template Excel untuk Import Data Warga
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Warga');

        // Header Kolom
        $headers = [
            'A1' => 'No',
            'B1' => 'Nama Lengkap (Wajib)',
            'C1' => 'NIK 16 Digit (Wajib)',
            'D1' => 'Password (Opsional - Default: password)',
            'E1' => 'Email (Opsional)',
            'F1' => 'Nomor WhatsApp / HP (Opsional)',
            'G1' => 'Nama Ibu Kandung (Opsional)',
            'H1' => 'RT (Opsional - Default: 01)',
            'I1' => 'RW (Opsional - Default: 02)',
            'J1' => 'No Rumah (Opsional)',
            'K1' => 'Alamat Lengkap (Opsional)',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style Header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'], // Emerald 600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '047857'],
                ],
            ],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Contoh Baris 1 (Lengkap)
        $sheet->setCellValue('A2', 1);
        $sheet->setCellValue('B2', 'Bambang Hariyanto');
        $sheet->setCellValueExplicit('C2', '3201010101010101', DataType::TYPE_STRING);
        $sheet->setCellValue('D2', 'password');
        $sheet->setCellValue('E2', 'bambang@warga.jagawarga.local');
        $sheet->setCellValueExplicit('F2', '081234567801', DataType::TYPE_STRING);
        $sheet->setCellValue('G2', 'Siti Aminah');
        $sheet->setCellValueExplicit('H2', '01', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('I2', '02', DataType::TYPE_STRING);
        $sheet->setCellValue('J2', '14A');
        $sheet->setCellValue('K2', 'Jl. Kenanga No. 14A');

        // Contoh Baris 2 (Minimal: Nama & NIK saja, lainnya kosong dan otomatis default)
        $sheet->setCellValue('A3', 2);
        $sheet->setCellValue('B3', 'Dewi Lestari');
        $sheet->setCellValueExplicit('C3', '3201010101010102', DataType::TYPE_STRING);
        $sheet->setCellValue('D3', ''); // Kosong -> default: password
        $sheet->setCellValue('E3', ''); // Kosong -> auto generate email
        $sheet->setCellValueExplicit('F3', '081234567802', DataType::TYPE_STRING);
        $sheet->setCellValue('G3', 'Rukmini');
        $sheet->setCellValueExplicit('H3', '02', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('I3', '02', DataType::TYPE_STRING);
        $sheet->setCellValue('J3', '22');
        $sheet->setCellValue('K3', 'Jl. Mawar No. 22');

        // Style Baris Data Contoh
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A2:K3')->applyFromArray($dataStyle);
        $sheet->getStyle('A2:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H2:I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto Size Kolom A-K
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_warga_jagawarga.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Proses Import Data Warga dari File Excel (.xlsx, .xls, .csv)
     * Default Role: warga
     * Default Password: password
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|max:10240', // Maksimal 10MB
        ]);

        $file = $request->file('excel_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format file tidak didukung! Mohon unggah file Excel (.xlsx, .xls) atau .csv.',
                ], 422);
            }
            return back()->with('error', 'Format file tidak didukung! Mohon unggah file Excel (.xlsx, .xls) atau .csv.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membaca isi file Excel: ' . $e->getMessage(),
                ], 422);
            }
            return back()->with('error', 'Gagal membaca isi file Excel: ' . $e->getMessage());
        }

        if (count($rows) <= 1) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File Excel kosong atau hanya berisi baris judul kolom.',
                ], 422);
            }
            return back()->with('error', 'File Excel kosong atau hanya berisi baris judul kolom.');
        }

        $wargaRole = Role::firstOrCreate(['name' => 'warga'], [
            'display_name' => 'Warga Lingkungan',
            'description' => 'Akses Panic Button (Kentongan), Pelaporan Kejadian Warga, dan Pengisian Buku Tamu.',
            'icon' => '🏠',
            'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200'
        ]);

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $loopIndex = 0;
        foreach ($rows as $rowKey => $row) {
            $loopIndex++;
            $rowNumber = is_numeric($rowKey) ? (int)$rowKey : $loopIndex;

            // Lewati baris 1 (header kolom)
            if ($rowNumber === 1) {
                continue;
            }

            // Kolom A: No, B: Nama, C: NIK, D: Password, E: Email, F: Phone, G: Nama Ibu, H: RT, I: RW, J: No Rumah, K: Alamat
            $nama = trim((string)($row['B'] ?? ''));
            $nikRaw = trim((string)($row['C'] ?? ''));
            $nik = preg_replace('/[^0-9]/', '', $nikRaw);

            // Lewati baris jika nama dan NIK sama-sama kosong
            if (empty($nama) && empty($nik)) {
                continue;
            }

            if (empty($nama)) {
                $skipped++;
                $errors[] = "Baris #$rowNumber: Nama lengkap wajib diisi.";
                continue;
            }

            if (empty($nik) || strlen($nik) !== 16) {
                $skipped++;
                $errors[] = "Baris #$rowNumber ($nama): NIK harus 16 digit angka (terbaca: " . ($nikRaw ?: 'kosong') . ").";
                continue;
            }

            // Password: jika kosong, default ke 'password' sesuai instruksi user
            $passwordInput = trim((string)($row['D'] ?? ''));
            $password = !empty($passwordInput) ? $passwordInput : 'password';

            // Email: jika kosong, generate unik berbasis NIK
            $emailInput = trim((string)($row['E'] ?? ''));
            if (!empty($emailInput) && filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
                $email = $emailInput;
            } else {
                $email = "warga_{$nik}@jagawarga.local";
            }

            $phone = trim((string)($row['F'] ?? ''));
            $namaIbu = trim((string)($row['G'] ?? ''));
            $rtId = trim((string)($row['H'] ?? '01'));
            $rwId = trim((string)($row['I'] ?? '02'));
            $noRumah = trim((string)($row['J'] ?? ''));
            $alamat = trim((string)($row['K'] ?? ''));

            // Cek apakah akun dengan NIK ini sudah terdaftar
            $existingUser = User::where('nik', $nik)->first();

            if ($existingUser) {
                // Perbarui data pengguna
                $updateData = [
                    'name' => $nama,
                    'is_nik_verified' => true,
                    'nik_verified_at' => $existingUser->nik_verified_at ?: now(),
                ];

                if (!empty($phone)) $updateData['phone'] = $phone;
                if (!empty($namaIbu)) $updateData['nama_ibu'] = $namaIbu;
                if (!empty($rtId)) $updateData['rt_id'] = $rtId;
                if (!empty($rwId)) $updateData['rw_id'] = $rwId;
                if (!empty($noRumah)) $updateData['no_rumah'] = $noRumah;
                if (!empty($alamat)) $updateData['alamat'] = $alamat;
                if (!empty($passwordInput)) $updateData['password'] = Hash::make($passwordInput);

                $existingUser->update($updateData);

                // Pastikan role warga terikat
                if (!$existingUser->hasRole('warga')) {
                    $existingUser->roles()->syncWithoutDetaching([$wargaRole->id]);
                }

                $updated++;
            } else {
                // Jika email sudah dipakai akun lain, buat email unik
                if (User::where('email', $email)->exists()) {
                    $email = "warga_{$nik}_" . substr(uniqid(), -4) . "@jagawarga.local";
                }

                $newUser = User::create([
                    'name' => $nama,
                    'nik' => $nik,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'warga', // Default role warga
                    'is_nik_verified' => true,
                    'nik_verified_at' => now(),
                    'phone' => $phone ?: null,
                    'nama_ibu' => $namaIbu ?: null,
                    'rt_id' => $rtId ?: '01',
                    'rw_id' => $rwId ?: '02',
                    'no_rumah' => $noRumah ?: null,
                    'alamat' => $alamat ?: null,
                ]);

                $newUser->syncRoles(['warga']);
                $imported++;
            }
        }

        $resultMsg = "Import data warga berhasil! Ditambahkan: $imported orang";
        if ($updated > 0) {
            $resultMsg .= ", Diperbarui: $updated orang";
        }
        if ($skipped > 0) {
            $resultMsg .= ", Dilewati: $skipped baris.";
        } else {
            $resultMsg .= ". Seluruh warga otomatis berstatus terverifikasi NIK dan siap menggunakan Kentongan Online.";
        }

        if (!empty($errors)) {
            $errText = implode(' ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $errText .= ' (dan ' . (count($errors) - 3) . ' baris lainnya)';
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $resultMsg,
                    'warning' => "Peringatan baris data: $errText",
                    'imported' => $imported,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'errors' => $errors,
                    'redirect_url' => route('users.index'),
                ]);
            }

            return redirect()->route('users.index')->with('success', $resultMsg)->with('warning', "Peringatan baris data: $errText");
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $resultMsg,
                'imported' => $imported,
                'updated' => $updated,
                'skipped' => $skipped,
                'redirect_url' => route('users.index'),
            ]);
        }

        return redirect()->route('users.index')->with('success', $resultMsg);
    }
}

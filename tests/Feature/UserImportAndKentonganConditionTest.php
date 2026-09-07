<?php

namespace Tests\Feature;

use App\Models\PanicAlert;
use App\Models\Role;
use App\Models\RwSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class UserImportAndKentonganConditionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Pastikan role warga ada
        Role::firstOrCreate(['name' => 'warga'], [
            'display_name' => 'Warga Lingkungan',
            'description' => 'Akses Panic Button, Laporan, Buku Tamu.',
            'icon' => '🏠',
            'badge_color' => 'bg-emerald-100 text-emerald-800'
        ]);

        RwSetting::ensureTableExists();
        PanicAlert::ensureTableExists();

        // Bersihkan data user test sebelumnya
        User::where('email', 'like', '%@testing.local')->delete();
    }

    protected function tearDown(): void
    {
        User::where('email', 'like', '%@testing.local')->delete();
        parent::tearDown();
    }

    /**
     * Uji Admin RW dapat mengunduh template Excel untuk import warga
     */
    public function test_admin_can_download_excel_template(): void
    {
        $admin = User::where('email', 'rw02@jagawarga.local')->first();
        $this->assertNotNull($admin);
        $this->actingAs($admin);
        session(['active_role' => 'rw']);

        $response = $this->get(route('users.template'));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));
    }

    /**
     * Uji Admin dapat mengimport warga dari Excel dengan default role warga dan default password
     */
    public function test_admin_can_import_warga_from_excel(): void
    {
        $admin = User::where('email', 'rw02@jagawarga.local')->first();
        $this->actingAs($admin);
        session(['active_role' => 'rw']);

        // Buat file Excel sementara menggunakan PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Baris Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Lengkap (Wajib)');
        $sheet->setCellValue('C1', 'NIK 16 Digit (Wajib)');
        $sheet->setCellValue('D1', 'Password (Opsional - Default: password)');
        $sheet->setCellValue('E1', 'Email (Opsional)');

        // Baris 2: Agus (Password kosong -> harus default 'password', email kosong -> auto-generated)
        $sheet->setCellValue('A2', 1);
        $sheet->setCellValue('B2', 'Agus Import Testing');
        $sheet->setCellValueExplicit('C2', '3201010101017788', DataType::TYPE_STRING);
        $sheet->setCellValue('D2', ''); // default password
        $sheet->setCellValue('E2', ''); // auto-email

        // Baris 3: Siti (Password kustom 'rahasia123', email ditentukan)
        $sheet->setCellValue('A3', 2);
        $sheet->setCellValue('B3', 'Siti Import Testing');
        $sheet->setCellValueExplicit('C3', '3201010101017799', DataType::TYPE_STRING);
        $sheet->setCellValue('D3', 'rahasia123');
        $sheet->setCellValue('E3', 'siti.custom@testing.local');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_import_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'test_import.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->post(route('users.import'), [
            'excel_file' => $uploadedFile,
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        // Verifikasi Akun Agus
        $agus = User::where('nik', '3201010101017788')->first();
        $this->assertNotNull($agus);
        $this->assertEquals('Agus Import Testing', $agus->name);
        $this->assertEquals('warga_3201010101017788@jagawarga.local', $agus->email);
        $this->assertTrue($agus->is_nik_verified);
        $this->assertTrue($agus->hasRole('warga'));
        $this->assertTrue(Hash::check('password', $agus->password));

        // Verifikasi Akun Siti
        $siti = User::where('nik', '3201010101017799')->first();
        $this->assertNotNull($siti);
        $this->assertEquals('Siti Import Testing', $siti->name);
        $this->assertEquals('siti.custom@testing.local', $siti->email);
        $this->assertTrue($siti->is_nik_verified);
        $this->assertTrue($siti->hasRole('warga'));
        $this->assertTrue(Hash::check('rahasia123', $siti->password));

        // Bersihkan file sementara
        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }

    /**
     * KONDISI 1: Guest / belum login tidak dapat mengaktifkan Kentongan Online (HTTP 401)
     */
    public function test_guest_cannot_trigger_panic_button(): void
    {
        $response = $this->postJson(route('api.panic'), [
            'latitude' => -6.208800,
            'longitude' => 106.845600,
            'kategori' => 'pencurian',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'requires_login' => true,
        ]);
    }

    /**
     * KONDISI 1: User dengan NIK belum terverifikasi ditolak saat klik Kentongan Online (HTTP 403)
     */
    public function test_unverified_user_cannot_trigger_panic_button(): void
    {
        $unverified = User::where('email', 'unverified@jagawarga.local')->first();
        if (!$unverified) {
            $unverified = User::create([
                'name' => 'Warga Belum Verif',
                'email' => 'unverified@jagawarga.local',
                'password' => Hash::make('password'),
                'nik' => '3201010101010999',
                'role' => 'warga',
                'is_nik_verified' => false,
            ]);
            $unverified->syncRoles(['warga']);
        } else {
            $unverified->update(['is_nik_verified' => false]);
        }

        $this->actingAs($unverified);
        $setting = RwSetting::getActiveSetting();

        $response = $this->postJson(route('api.panic'), [
            'latitude' => $setting->center_latitude,
            'longitude' => $setting->center_longitude,
            'kategori' => 'kebakaran',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'unverified' => true,
        ]);
    }

    /**
     * KONDISI 2: User terverifikasi tetapi berada di LUAR radius ditolak (HTTP 422)
     */
    public function test_verified_user_outside_geofence_cannot_trigger_panic_button(): void
    {
        $warga = User::where('email', 'warga@jagawarga.local')->first();
        $this->assertNotNull($warga);
        $warga->update(['is_nik_verified' => true]);

        $this->actingAs($warga);

        // Koordinat jauh di luar RW 02 (jarak > 3000 meter)
        $response = $this->postJson(route('api.panic'), [
            'latitude' => -6.300000,
            'longitude' => 106.845600,
            'kategori' => 'medis',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'out_of_radius' => true,
        ]);
    }

    /**
     * KONDISI DUAL TERPENUHI: User terverifikasi DAN berada di dalam radius RW 02 berhasil membunyikan kentongan (HTTP 200)
     */
    public function test_verified_user_inside_geofence_can_trigger_panic_button(): void
    {
        $warga = User::where('email', 'warga@jagawarga.local')->first();
        $this->assertNotNull($warga);
        $warga->update(['is_nik_verified' => true]);

        $this->actingAs($warga);

        $setting = RwSetting::getActiveSetting();

        // Koordinat persis di titik pusat RW 02 (jarak 0 meter)
        $response = $this->postJson(route('api.panic'), [
            'latitude' => $setting->center_latitude,
            'longitude' => $setting->center_longitude,
            'kategori' => 'pencurian',
            'catatan' => 'Uji kentongan dual-condition',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('panic_alerts', [
            'user_id' => $warga->id,
            'status' => 'aktif',
            'kategori' => 'pencurian',
        ]);
    }

    /**
     * Uji Admin dapat melakukan toggle verifikasi NIK pengguna
     */
    public function test_admin_can_toggle_user_nik_verification(): void
    {
        $admin = User::where('email', 'rw02@jagawarga.local')->first();
        $this->actingAs($admin);
        session(['active_role' => 'rw']);

        $warga = User::where('email', 'warga@jagawarga.local')->first();
        $initialStatus = $warga->is_nik_verified;

        // Toggle pertama
        $res1 = $this->post(route('users.toggle_nik', $warga->id));
        $res1->assertSessionHas('success');
        $warga->refresh();
        $this->assertEquals(!$initialStatus, $warga->is_nik_verified);

        // Toggle kedua (kembali ke awal)
        $res2 = $this->post(route('users.toggle_nik', $warga->id));
        $res2->assertSessionHas('success');
        $warga->refresh();
        $this->assertEquals($initialStatus, $warga->is_nik_verified);
    }

    /**
     * Uji Admin dapat menghapus beberapa data warga terpilih sekaligus (Bulk Delete)
     */
    public function test_admin_can_bulk_delete_selected_users(): void
    {
        $admin = User::where('email', 'rw02@jagawarga.local')->first();
        $this->actingAs($admin);
        session(['active_role' => 'rw']);

        // Buat 3 user percobaan
        $u1 = User::create([
            'name' => 'Warga Hapus 1',
            'email' => 'hapus1@testing.local',
            'nik' => '3201010101990001',
            'password' => Hash::make('password'),
            'role' => 'warga',
        ]);
        $u2 = User::create([
            'name' => 'Warga Hapus 2',
            'email' => 'hapus2@testing.local',
            'nik' => '3201010101990002',
            'password' => Hash::make('password'),
            'role' => 'warga',
        ]);
        $u3 = User::create([
            'name' => 'Warga Pertahankan 3',
            'email' => 'pertahankan3@testing.local',
            'nik' => '3201010101990003',
            'password' => Hash::make('password'),
            'role' => 'warga',
        ]);

        // Hapus u1 dan u2 via bulk delete
        $response = $this->post(route('users.bulk_destroy'), [
            'selected_ids' => [$u1->id, $u2->id],
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $u1->id]);
        $this->assertDatabaseMissing('users', ['id' => $u2->id]);
        $this->assertDatabaseHas('users', ['id' => $u3->id]);
    }

    /**
     * Uji Bulk Delete melindungi akun admin yang sedang login agar tidak terhapus
     */
    public function test_bulk_delete_protects_currently_logged_in_admin(): void
    {
        $admin = User::where('email', 'rw02@jagawarga.local')->first();
        $this->actingAs($admin);
        session(['active_role' => 'rw']);

        $u = User::create([
            'name' => 'Warga Hapus Test',
            'email' => 'hapus_test@testing.local',
            'nik' => '3201010101990004',
            'password' => Hash::make('password'),
            'role' => 'warga',
        ]);

        // Kirim ID admin bersamaan dengan ID warga lain
        $response = $this->post(route('users.bulk_destroy'), [
            'selected_ids' => [$admin->id, $u->id],
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        // Admin tetap ada, sedangkan warga lain terhapus
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        $this->assertDatabaseMissing('users', ['id' => $u->id]);
    }

    /**
     * Uji halaman /users merender elemen DataTable, bar aksi massal, dan modal konfirmasi
     */
    public function test_users_index_page_contains_datatable_and_bulk_elements(): void
    {
        $admin = User::where('email', 'rw02@jagawarga.local')->first();
        $this->actingAs($admin);
        session(['active_role' => 'rw']);

        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('table-users');
        $response->assertSee('check-all-users');
        $response->assertSee('bulk-action-bar');
        $response->assertSee('modal-bulk-hapus');
        $response->assertSee('jquery.dataTables.min.js');
    }

    /**
     * Uji modal import data pada /users memuat elemen visual progressbar (0% - 100%)
     */
    public function test_users_index_page_contains_import_progressbar_elements(): void
    {
        $admin = User::where('email', 'rw02@jagawarga.local')->first();
        $this->actingAs($admin);
        session(['active_role' => 'rw']);

        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('import-form-section');
        $response->assertSee('import-progress-section');
        $response->assertSee('import-progress-bar');
        $response->assertSee('import-percentage-text');
        $response->assertSee('handleImportSubmit');
    }

    /**
     * Uji request AJAX import mengembalikan response JSON yang mendukung pelacakan progress
     */
    public function test_ajax_import_returns_json_response_with_progress_details(): void
    {
        $admin = User::where('email', 'rw02@jagawarga.local')->first();
        $this->actingAs($admin);
        session(['active_role' => 'rw']);

        $testNik = '3201' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
        User::where('nik', $testNik)->delete();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Lengkap (Wajib)');
        $sheet->setCellValue('C1', 'NIK 16 Digit (Wajib)');
        $sheet->setCellValue('D1', 'Password (Opsional - Default: password)');

        $sheet->setCellValue('A2', 1);
        $sheet->setCellValue('B2', 'Budi Progress Test');
        $sheet->setCellValueExplicit('C2', $testNik, DataType::TYPE_STRING);
        $sheet->setCellValue('D2', '');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_ajax_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'test_ajax.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->post(route('users.import'), [
            'excel_file' => $uploadedFile,
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'imported' => 1,
        ]);
        $this->assertNotNull($response->json('redirect_url'));

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }
}

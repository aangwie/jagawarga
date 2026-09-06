<?php

namespace Tests\Feature;

use App\Models\DeviceResetRequest;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class MultiRoleAndDeviceBindingTest extends TestCase
{
    /**
     * Uji alur login mengarahkan ke halaman pemilihan peran
     */
    public function test_login_redirects_to_select_role_page(): void
    {
        $response = $this->post('/login', [
            'login' => 'ronda@jagawarga.local',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('role.select'));
        $this->assertAuthenticated();
        $this->assertFalse(session()->has('active_role'));
    }

    /**
     * Uji halaman select role dapat dibuka oleh pengguna yang telah login
     */
    public function test_authenticated_user_can_view_select_role_page(): void
    {
        $user = User::where('email', 'ronda@jagawarga.local')->first();
        $this->actingAs($user);

        $response = $this->get('/select-role');
        $response->assertStatus(200);
        $response->assertSee('Pilih Peran untuk Sesi Ini');
        $response->assertSee('Petugas Ronda');
        $response->assertSee('Warga Lingkungan');
    }

    /**
     * Uji pemilihan peran petugas ronda mengarahkan ke /ronda
     */
    public function test_user_can_select_petugas_ronda_role_and_access_ronda(): void
    {
        $user = User::where('email', 'ronda@jagawarga.local')->first();
        $this->actingAs($user);

        $response = $this->post('/select-role', [
            'role' => 'petugas_ronda',
            'device_id' => 'dev_test_device_1',
        ]);

        $response->assertRedirect('/ronda');
        $this->assertEquals('petugas_ronda', session('active_role'));

        // Coba akses rute /ronda yang diproteksi
        $rondaResponse = $this->get('/ronda');
        $rondaResponse->assertStatus(200);
    }

    /**
     * Uji penguncian 1 perangkat untuk peran warga
     */
    public function test_warga_role_is_locked_to_first_registered_device(): void
    {
        $warga = User::where('email', 'warga@jagawarga.local')->first();
        $warga->resetDevice();
        $this->actingAs($warga);

        // 1. Pilih role warga dengan Device A
        $res1 = $this->post('/select-role', [
            'role' => 'warga',
            'device_id' => 'dev_device_A',
        ]);

        $res1->assertRedirect('/warga');
        $warga->refresh();
        $this->assertEquals('dev_device_A', $warga->registered_device_id);

        // 2. Coba pilih role warga dari Device B (berbeda)
        $res2 = $this->from('/select-role')->post('/select-role', [
            'role' => 'warga',
            'device_id' => 'dev_device_B_different',
        ]);

        // Harus gagal dan menampilkan error perangkat
        $res2->assertSessionHas('device_error');
    }

    /**
     * Uji pengajuan permohonan ganti perangkat
     */
    public function test_warga_can_submit_device_reset_request(): void
    {
        $warga = User::where('email', 'warga@jagawarga.local')->first();

        $response = $this->post('/device-reset', [
            'nik' => $warga->nik,
            'nama_ibu' => $warga->nama_ibu ?: 'Siti Aminah',
            'phone' => '089876543210',
            'alasan' => 'HP lama tercebur ke air',
            'device_id' => 'dev_new_phone_123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('device_reset_requests', [
            'nik' => $warga->nik,
            'status' => 'pending',
            'new_device_id' => 'dev_new_phone_123',
        ]);
    }

    /**
     * Uji admin dapat menyetujui permohonan ganti perangkat
     */
    public function test_admin_can_approve_device_reset_request(): void
    {
        $admin = User::where('email', 'rw02@jagawarga.local')->first();
        $this->actingAs($admin);
        session(['active_role' => 'rw']);

        $req = DeviceResetRequest::latest()->first();
        $this->assertNotNull($req);

        $response = $this->post(route('device.requests.approve', $req->id));
        $response->assertSessionHas('success');

        $req->refresh();
        $this->assertEquals('approved', $req->status);
    }

    /**
     * Uji pengguna dengan peran Nakes Puskesmas dapat login, memilih role nakes, dan mengakses dashboard
     */
    public function test_nakes_puskesmas_can_select_role_and_access_dashboard(): void
    {
        $nakes = User::where('email', 'nakes@jagawarga.local')->first();
        $this->assertNotNull($nakes);
        $this->actingAs($nakes);

        // Akses select-role
        $res = $this->get('/select-role');
        $res->assertStatus(200);
        $res->assertSee('Nakes Puskesmas');

        // Pilih role nakes_puskesmas
        $postRes = $this->post('/select-role', [
            'role' => 'nakes_puskesmas',
            'device_id' => 'dev_nakes_test',
        ]);

        $postRes->assertRedirect('/dashboard');
        $this->assertEquals('nakes_puskesmas', session('active_role'));

        // Akses dashboard
        $dashRes = $this->get('/dashboard');
        $dashRes->assertStatus(200);
    }
}

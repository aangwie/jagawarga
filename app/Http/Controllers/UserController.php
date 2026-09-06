<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
}

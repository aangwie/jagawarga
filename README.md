# 🛡️ JagaWarga RW — Sistem Integrasi Keamanan Warga Digital (PWA)

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Leaflet](https://img.shields.io/badge/Leaflet-1.9.4-199900?style=for-the-badge&logo=leaflet&logoColor=white)](https://leafletjs.com)
[![PWA Ready](https://img.shields.io/badge/PWA-Enabled-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white)](https://web.dev/progressive-web-apps/)

**JagaWarga RW** adalah aplikasi web berbasis **Progressive Web App (PWA)** yang dirancang untuk mengintegrasikan keamanan warga, pos ronda (poskamling), pengurus RT/RW, dan Bhabinkamtibmas ke dalam satu platform digital modern yang responsif, cepat, dan mudah diakses langsung melalui peramban ponsel tanpa perlu mengunduh aplikasi dari Play Store atau App Store.

---

## 📌 Daftar Isi
- [Latar Belakang & Konsep Utama](#-latar-belakang--konsep-utama)
- [Fitur Utama](#-fitur-utama)
- [Arsitektur Hak Akses & Halaman](#-arsitektur-hak-akses--halaman)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Kredensial Akun Pengguna (Seeder)](#-kredensial-akun-pengguna-seeder)
- [Panduan Instalasi & Menjalankan Aplikasi](#-panduan-instalasi--menjalankan-aplikasi)
- [Panduan Penggunaan Lengkap](#-panduan-penggunaan-lengkap)
  - [1. Sisi Warga (Publik)](#1-sisi-warga-publik)
  - [2. Sisi Petugas Ronda Poskamling](#2-sisi-petugas-ronda-poskamling)
  - [3. Sisi Command Center Pengurus RW](#3-sisi-command-center-pengurus-rw)
- [Mekanisme Geofencing Radius (300 Meter)](#-mekanisme-geofencing-radius-300-meter)
- [Struktur Direktori Proyek](#-struktur-direktori-proyek)

---

## 💡 Latar Belakang & Konsep Utama

Sistem keamanan lingkungan tradisional sering menghadapi kendala seperti sinyal kentongan fisik yang tidak terdengar oleh seluruh warga, ronda malam yang sulit dipantau secara nyata (*blind spot*), pendataan tamu 2x24 jam yang tidak tercatat rapi, serta lambatnya koordinasi penanganan insiden darurat.

**JagaWarga RW** menjawab tantangan tersebut dengan menghadirkan:
1. **Kentongan Online**: Suara alarm akustik dan siaran notifikasi instan ke pos ronda dan pengurus saat terjadi bahaya (*maling, kebakaran, medis*).
2. **Geofencing Radius Keamanan**: Tombol panic hanya dapat digunakan di dalam radius wilayah RW (default: **300 meter**) untuk mencegah *false alarm* dari luar lingkungan.
3. **Patroli Ronda Anti-Fraud**: Scan QR checkpoint di titik rawan menggunakan kamera web HP yang diverifikasi dengan waktu nyata dan koordinat GPS petugas.
4. **Buku Tamu Digital 2x24 Jam**: Wajib lapor mandiri bagi pendatang baru sebelum diverifikasi Ketua RT.
5. **Command Center & Heatmap Kerawanan**: Visualisasi peta insiden interaktif (*Leaflet Heatmap*) dan otomasi pengingat jadwal ronda via WhatsApp Gateway.

---

## ✨ Fitur Utama

### 1. 🚨 Kentongan Online (Tombol Darurat Digital dengan Audio Spesifik per Kejadian)
- **Tombol Sentuh Raksasa**: Animasi denyut radar merah beresonansi tinggi (*panic-pulse*).
- **Perbedaan Suara & Ritme Kentongan Khas Nusantara per Kategori Kejadian**:
  - 🚨 **Maling / Curanmor**: Ketukan bertubi-tubi sangat cepat rapat (*Doro Muluk / Titir Maling*, interval 120ms) dipadukan nada alarm maling tajam berdenyut + siaran suara otomatis (*Voice Broadcast*): *"Perhatian! Ada maling atau pencurian di lingkungan warga! Warga segera siaga kepung lokasi!"*.
  - 🔥 **Bahaya Kebakaran**: Ketukan rangkap ganda bergaung (*Titir Ganda: Tang-Tang... Tang-Tang...*) dipadukan sirene pemadam kebakaran melolong naik-turun berkala + siaran suara otomatis: *"Perhatian! Bahaya kebakaran! Bawa air dan alat pemadam, segera bantu lokasi!"*.
  - 🚑 **Darurat Medis / Ambulans**: Sirene dua nada khas ambulans (*WEE-WOO Hi-Lo Siren*, 750Hz - 540Hz) diselingi ketukan kentongan bulat ritmis lambat + siaran suara otomatis: *"Panggilan darurat medis! Pertolongan pertama dan ambulans dibutuhkan!"*.
  - ⚠️ **Siaga Lingkungan / Lainnya**: Ketukan kentongan panggilan pos ronda siaga (*Tong... Tong... Tong-Tong-Tong*) diselingi nada chime peringatan + siaran suara otomatis: *"Perhatian! Peringatan siaga keamanan lingkungan RW 02!"*.
- **Sintesis Audio Web Audio API & Web Speech API**: Menghasilkan resonansi kayu kentongan, variasi sirene, dan pengumuman vokal bahasa Indonesia secara *real-time* langsung dari peramban ponsel tanpa perlu mengunduh file MP3/WAV berat.
- **Fitur Tes Suara Tiap Kategori**: Pengguna dapat mendengarkan sampel bunyi masing-masing kategori melalui tombol **"🔊 Tes Bunyi"** di formulir warga atau dropdown **"Tes Suara"** di bilah navigasi atas.
- **Validasi Geofencing 300 Meter**: Tombol terkunci otomatis (*disabled/grayscale*) jika warga berada di luar jangkauan wilayah RW.
- **Tombol Simulasi Jarak**: Mempermudah demonstrasi (*🟢 Posko <300m, 🔴 Luar Wilayah >300m, 📍 GPS Asli*).

### 2. 📸 Lapor Cepat Kejadian Warga
- Formulir pengaduan lingkungan ramah ponsel.
- Opsi unggah foto bukti langsung dari galeri atau kamera HP.
- Pelacakan status penanganan laporan secara transparan (*Menunggu, Diproses, Selesai*).

### 3. 🪪 Buku Tamu Digital (Wajib Lapor 2x24 Jam)
- Registrasi mandiri tamu atau kerabat menginap.
- Pencatatan identitas: Nama, No. WhatsApp, alamat asal, nama warga yang dikunjungi, dan tujuan kunjungan.
- Notifikasi langsung ke Ketua RT setempat untuk verifikasi fisik.

### 4. 🔦 Presensi Ronda & Pemindai QR Checkpoint (`html5-qrcode`)
- Kamera pemindai QR aktif langsung di peramban web HP tanpa aplikasi pihak ketiga.
- Verifikasi koordinat GPS ganda saat scan untuk memastikan petugas benar-benar berada di titik rawan poskamling.
- Indikator progres patroli malam (*progress bar* rute checkpoint).
- Formulir catatan jaga malam dan log patroli berkala.

### 5. 🗺️ Command Center & Peta Kerawanan Leaflet.js
- **Heatmap Densitas Insiden**: Gradien warna *Leaflet.heat* (Merah: Area Rawan, Kuning: Sedang, Hijau: Kondusif) berdasarkan agregat laporan insiden dan tombol panic.
- **Visualisasi Lingkaran Geofence**: Menampilkan batas radius 300 meter jangkauan siaga RW 02.
- **Konfigurasi Titik Pusat & Radius**: Admin RW dapat mengubah titik tengah posko dan jarak radius meter langsung dari dashboard dengan fitur geser pin marker (*draggable*) dan preview peta interaktif.

### 6. 📅 Manajemen Jadwal Ronda & WhatsApp Reminder
- Matriks jadwal giliran ronda warga per hari (Senin s/d Minggu).
- Modal tambah dan hapus jadwal ronda.
- **WhatsApp Reminder Gateway**: Integrasi pesan pengingat jadwal otomatis ke nomor HP warga (*Fonnte/Wablas compatible*).

### 7. 📊 Analitik Kamtibmas Chart.js
- **Grafik Batang**: Tingkat keaktifan patroli checkpoint ronda mingguan.
- **Grafik Donat**: Proporsi kategori insiden lingkungan.
- **4 Kartu Metrik Ringkas**: Total Warga, Petugas Ronda Aktif, Total Laporan, dan Tamu Terdata.

### 8. 📹 Pemantauan CCTV Lingkungan
- Pemutar streaming langsung kamera IP/CCTV lorong lingkungan dan gerbang gapura via format HLS (*.m3u8*).

---

## 🔐 Arsitektur Hak Akses & Halaman

Aplikasi menerapkan pemisahan hak akses berbasis peran (*Role-Based Access Control / RBAC*) dengan dukungan middleware terproteksi:

| URL Rute | Hak Akses | Peran (Role) | Fungsi Utama |
|---|---|---|---|
| `GET /` | **Publik** | Semua Pengunjung | Beranda utama PWA, Kentongan Online Geofence, status wilayah, live CCTV |
| `GET /warga` | **Publik** | Semua Pengunjung | Portal Warga: Tombol Panic Darurat, Lapor Cepat Foto, Buku Tamu 2x24 Jam |
| `GET /login` | **Publik** | Tamu (Guest) | Halaman login dengan formulir kredensial dan kartu **Demo 1-Klik** |
| `POST /login` | **Publik** | Tamu (Guest) | Proses autentikasi via Email / NIK dan Password |
| `POST /logout`| **Terproteksi**| Pengguna Login | Mengakhiri sesi pengguna |
| `GET /ronda` | **Terproteksi**| `petugas_ronda`, `rt`, `rw`, `bhabinkamtibmas` | Modul Petugas: Scan QR Kamera HP, Presensi GPS, Log Jaga Malam |
| `GET /dashboard` | **Terproteksi**| `rt`, `rw`, `bhabinkamtibmas` | Command Center: Peta Heatmap, Pengaturan Geofence, Jadwal, WhatsApp Gateway |

### Endpoint API:
- `POST /api/panic` &mdash; *Publik* (Kirim sinyal darurat dengan validasi radius Geofence).
- `POST /api/lapor` &mdash; *Publik* (Simpan pengaduan kejadian warga).
- `POST /api/buku-tamu` &mdash; *Publik* (Simpan registrasi tamu 2x24 jam).
- `GET /api/settings/public` &mdash; *Publik* (Membaca konfigurasi titik koordinat & radius geofence RW).
- `POST /api/presensi` &mdash; *Terproteksi* (Simpan scan QR presensi ronda).
- `POST /api/jadwal` &mdash; *Terproteksi* (Tambah jadwal ronda warga).
- `DELETE /api/jadwal/{id}` &mdash; *Terproteksi* (Hapus jadwal ronda).
- `POST /api/wa-reminder` &mdash; *Terproteksi* (Kirim pesan pengingat WhatsApp).
- `POST /api/settings/geofence` &mdash; *Terproteksi* (Perbarui koordinat & radius geofence RW).

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: [Laravel 11](https://laravel.com/) (PHP 8.2+)
- **Basis Data**: MySQL (10 tabel terstruktur dengan Eloquent ORM)
- **Frontend / Styling**: [Tailwind CSS v4](https://tailwindcss.com/) dengan skema warna Emerald, Slate, dan Violet, didukung tipografi Google Fonts *Plus Jakarta Sans*.
- **PWA & Offline Capability**:
  - `manifest.webmanifest` untuk dukungan *Add to Home Screen*.
  - `sw.js` (Service Worker) untuk caching antarmuka dan fallback `offline.html`.
- **Geospasial & Peta**:
  - [Leaflet.js v1.9.4](https://leafletjs.com/) (Peta interaktif OpenStreetMap).
  - [Leaflet.heat](https://github.com/Leaflet/Leaflet.heat) (Layer visualisasi kepadatan titik insiden).
- **Pemindai QR**: [html5-qrcode v2.3.8](https://github.com/mebjas/html5-qrcode) (Akses kamera smartphone langsung dari browser).
- **Grafik Statistik**: [Chart.js](https://www.chartjs.org/) (Grafik batang kehadiran ronda dan donat insiden).
- **Audio Synthesizer**: *Web Audio API native browser* (Tanpa file WAV/MP3 eksternal).

---

## 👥 Kredensial Akun Pengguna (Seeder)

Seluruh akun telah disiapkan dalam `DatabaseSeeder.php` dengan kata sandi default: **`password`**.

| Peran (Role) | Nama Pengguna | Email Login | NIK | Akses Halaman |
|---|---|---|---|---|
| **Admin Pengurus RW** | Pak Gunawan (Ketua RW 02) | `rw02@jagawarga.local` | `3201010101010007` | `/dashboard`, `/ronda`, `/warga`, `/` |
| **Ketua RT 01** | Pak Bambang (Ketua RT 01) | `rt01@jagawarga.local` | `3201010101010005` | `/dashboard`, `/ronda`, `/warga`, `/` |
| **Ketua RT 02** | Pak Heri (Ketua RT 02) | `rt02@jagawarga.local` | `3201010101010006` | `/dashboard`, `/ronda`, `/warga`, `/` |
| **Petugas Ronda 1** | Pak Joko Ronda | `ronda@jagawarga.local` | `3201010101010003` | `/ronda`, `/warga`, `/` |
| **Petugas Ronda 2** | Kang Asep Patroli | `asep@jagawarga.local` | `3201010101010004` | `/ronda`, `/warga`, `/` |
| **Bhabinkamtibmas** | Aipda Wahyudi | `bhabin@jagawarga.local` | `3201010101010008` | `/dashboard`, `/ronda`, `/warga`, `/` |
| **Warga 1** | Budi Santoso | `warga@jagawarga.local` | `3201010101010001` | `/warga`, `/` |
| **Warga 2** | Siti Rahayu | `siti@jagawarga.local` | `3201010101010002` | `/warga`, `/` |

> 💡 **Fitur Cepat (Demo Login 1-Klik)**:
> Pada halaman [http://localhost:8000/login](http://localhost:8000/login), Anda cukup mengklik salah satu kartu peran (*Petugas Ronda, Admin RW, Ketua RT, Bhabin, atau Warga*) untuk langsung masuk tanpa perlu mengetik email & password.

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### 1. Prasyarat Sistem
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`)
- Composer
- MySQL Server (misal: XAMPP, Laragon, atau MySQL bawaan)
- Node.js & NPM (opsional, jika ingin mengompilasi Vite)

### 2. Langkah Pemasangan

1. **Buka Direktori Proyek**:
   ```bash
   cd "d:/AANG/PROYEK LARAVEL/jagawarga"
   ```

2. **Pasang Dependensi Composer**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`)**:
   Pastikan pengaturan database di file `.env` sudah sesuai dengan MySQL lokal Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=jagawarga
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Buat Database MySQL**:
   Buat database bernama `jagawarga` di MySQL Anda (misal via phpMyAdmin atau terminal MySQL).

5. **Generate Kunci Aplikasi**:
   ```bash
   php artisan key:generate
   ```

6. **Jalankan Migrasi & Data Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Perintah ini akan membuat seluruh 10 tabel basis data dan mengisinya dengan akun demo, titik checkpoint, CCTV, riwayat laporan, jadwal ronda, serta pengaturan geofence default (300 meter).*

7. **Jalankan Server Lokal Laravel**:
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan di: **[http://127.0.0.1:8000](http://127.0.0.1:8000)** atau **[http://localhost:8000](http://localhost:8000)**.

---

## 📖 Panduan Penggunaan Lengkap

### 1. Sisi Warga (Publik)
Akses: **[http://localhost:8000/warga](http://localhost:8000/warga)** atau **[http://localhost:8000/](http://localhost:8000/)**

#### Menggunakan Tombol Panic (Kentongan Online):
1. Pilih salah satu kategori bahaya (*Maling / Curanmor, Kebakaran, Darurat Medis, atau Lainnya*).
2. Sistem otomatis membaca koordinat GPS perangkat Anda dan menghitung jaraknya ke pusat Posko RW 02.
3. **Jika Anda berada di dalam radius (< 300 meter)**:
   - Tombol akan berdenyut merah dengan tulisan **"DARURAT KENTONGAN"**.
   - Klik tombol: Alarm sirene dan ketukan bambu kentongan digital akan berbunyi seketika, dan sinyal disiarkan ke pos ronda.
4. **Jika Anda berada di luar radius (> 300 meter)**:
   - Tombol otomatis menjadi abu-abu (*grayscale*) dan terkunci dengan tulisan **"TERKUNCI / DI LUAR RADIUS 300M"**.
   - Jika ditekan, muncul peringatan bahwa Anda berada di luar jangkauan wilayah keamanan RW 02 demi mencegah alarm palsu.
5. **Uji Simulasi Cepat**:
   - Klik tombol **🟢 Posko (< 300m)** untuk simulasi berada di posko (tombol aktif).
   - Klik tombol **🔴 Luar Wilayah (> 300m)** untuk simulasi berada di luar wilayah (tombol terkunci).
   - Klik tombol **📍 GPS Asli** untuk kembali ke koordinat sensor perangkat Anda.

#### Mengirim Pengaduan Lapor Cepat:
1. Masukkan Judul Laporan (contoh: *Lampu PJU Padam di Lorong RT 02*).
2. Tulis kronologi atau keterangan singkat.
3. Lampirkan foto bukti (opsional).
4. Klik **"Kirim Laporan ke Pengurus RT"**.

#### Mengisi Buku Tamu Digital 2x24 Jam:
1. Isi Nama Tamu sesuai KTP, No. WhatsApp aktif, dan Alamat Domisili Asal.
2. Tentukan warga yang dikunjungi dan tujuan kunjungan.
3. Klik **"Daftarkan Tamu"**. Data otomatis diteruskan ke Ketua RT untuk validasi.

---

### 2. Sisi Petugas Ronda Poskamling
Akses: **[http://localhost:8000/ronda](http://localhost:8000/ronda)**  
*(Wajib login sebagai `petugas_ronda`, `rt`, `rw`, atau `bhabinkamtibmas`)*

1. Masuk melalui halaman login menggunakan akun ronda (atau gunakan **Demo 1-Klik: Petugas Ronda**).
2. **Pemindaian QR Checkpoint**:
   - Klik tombol **"Buka Kamera Scan QR"**. Izinkan akses kamera browser Anda.
   - Arahkan kamera ke stiker QR Checkpoint di pos ronda atau titik rawan (kode: `JW-CKP-001-POSRW`, `JW-CKP-002-GAPURA`, `JW-CKP-003-TAMAN`, `JW-CKP-004-GARDU`).
   - Kamera otomatis mendeteksi kode QR, mencatat koordinat GPS fisik petugas, dan memperbarui persentase progres patroli malam ini.
   - *Alternatif tanpa kamera fisik*: Klik tombol **"Verifikasi Hadir"** pada kartu checkpoint untuk simulasi presensi patroli.
3. **Pencatatan Situasi Keamanan**:
   - Tulis catatan kondisi lingkungan pada formulir *Log Jaga Malam* dan klik kirim.

---

### 3. Sisi Command Center Pengurus RW
Akses: **[http://localhost:8000/dashboard](http://localhost:8000/dashboard)**  
*(Wajib login sebagai `rw`, `rt`, atau `bhabinkamtibmas`)*

#### Memantau Peta Kerawanan (Heatmap Leaflet.js):
1. Perhatikan peta interaktif di bagian atas dashboard.
2. Gradien warna merah menandakan konsentrasi titik laporan insiden dan pemicuan tombol panic.
3. Klik penanda titik checkpoint atau posko untuk melihat detail nama titik, penanggung jawab RT, dan kode QR.
4. Lingkaran hijau transparan pada peta menggambarkan batas radius jangkauan siaga RW aktif.

#### Mengatur Titik Pusat & Radius Geofence Tombol Panic:
1. Gulir ke **Seksi 4: Pengaturan Titik Pusat & Radius Geofence**.
2. Anda dapat menentukan koordinat pusat RW melalui 3 cara:
   - Mengetik angka Latitude & Longitude secara manual.
   - Mengklik tombol **"Deteksi Lokasi GPS Saya"** untuk mengambil posisi pengurus saat ini.
   - Menggeser marker pin ungu (*draggable*) langsung di preview peta mini.
3. Atur jarak radius aktif melalui **slider radius** (misal dari 300m diubah menjadi 500m atau 1000m).
4. Klik **"💾 Simpan Pengaturan Geofence"**.
5. Batas baru otomatis tersimpan di database dan langsung berlaku pada validasi tombol panic seluruh warga.

#### Mengelola Jadwal Ronda & WhatsApp Reminder:
1. Klik tombol **"+ Tambah Jadwal Ronda"** untuk menugaskan giliran warga pada hari tertentu.
2. Pada tabel jadwal ronda, klik tombol **"📱 Kirim WA"** di samping nama warga untuk menyimulasikan pengiriman pesan pengingat jadwal ronda H-1 langsung ke nomor WhatsApp warga bersangkutan.

---

## 🌐 Mekanisme Geofencing Radius (300 Meter)

Aplikasi menerapkan perlindungan ganda (*two-layer validation*) berbasis rumus **Haversine Great-Circle Distance**:

$$\Delta\sigma = 2 \arcsin\left(\sqrt{\sin^2\left(\frac{\Delta\phi}{2}\right) + \cos(\phi_1)\cos(\phi_2)\sin^2\left(\frac{\Delta\lambda}{2}\right)}\right)$$

$$d = R \cdot \Delta\sigma \quad (\text{dengan } R = 6.371.000\text{ meter})$$

1. **Lapisan 1: Sisi Klien (JavaScript di Browser Ponsel)**:
   - Menghitung jarak realtime dari koordinat sensor GPS ponsel pelapor ke titik pusat RW (`center_latitude`, `center_longitude`).
   - Jika jarak $> 300$ meter, tombol darurat seketika dikunci dan menampilkan pesan:
     > *"🚫 DI LUAR JANGKAUAN (Jarak: X meter > Batas: 300m)"*
2. **Lapisan 2: Sisi Server (PHP Laravel Controller)**:
   - Endpoint `POST /api/panic` memvalidasi kembali koordinat yang dikirim.
   - Jika jarak pelapor melebihi radius konfigurasi basis data (`panic_radius_meters`), server mengembalikan status HTTP `422 Unprocessable Content` dan menolak pembuatan alert:
     ```json
     {
       "success": false,
       "out_of_radius": true,
       "distance": 520,
       "max_radius": 300,
       "message": "Posisi Anda berada di luar jangkauan wilayah RW 02 (Jarak: 520m, Batas: 300m)."
     }
     ```

---

## 📁 Struktur Direktori Proyek

```plaintext
jagawarga/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Login, Demo 1-Klik, & Logout
│   │   │   ├── DashboardRwController.php   # Command Center, Geofence Settings, & Jadwal
│   │   │   ├── JagaWargaController.php     # Beranda & Fallback Demo Portal
│   │   │   ├── RondaController.php         # Scan QR Checkpoint & Presensi Patroli
│   │   │   └── WargaController.php         # Modul Warga, Geofence Panic, Lapor, & Buku Tamu
│   │   └── Middleware/
│   │       └── EnsureUserRole.php          # RBAC Middleware Pengecekan Peran
│   └── Models/
│       ├── BukuTamu.php                    # Model Tamu Wajib Lapor 2x24 Jam
│       ├── CctvLingkungan.php              # Model Live Streaming IP Camera Lingkungan
│       ├── Checkpoint.php                  # Model Titik QR Patroli Ronda
│       ├── JadwalRonda.php                 # Model Jadwal Giliran Ronda Warga
│       ├── LaporanKejadian.php             # Model Pengaduan & Laporan Kejadian Warga
│       ├── PanicAlert.php                  # Model Sinyal Bahaya Kentongan Online
│       ├── PresensiRonda.php               # Model Presensi GPS Patroli Checkpoint
│       ├── RwSetting.php                   # Model Konfigurasi Koordinat & Radius Geofence
│       └── User.php                        # Model Pengguna & Role Akses
├── database/
│   ├── migrations/                         # 14 File Migrasi Skema Basis Data
│   └── seeders/
│       └── DatabaseSeeder.php              # Seeder Akun, Titik Checkpoint, CCTV, & Geofence
├── public/
│   ├── manifest.webmanifest               # Manifest PWA (Nama, Ikon, Tema)
│   ├── sw.js                              # Service Worker Caching & Offline
│   ├── offline.html                       # Tampilan Cadangan Saat Internet Terputus
│   └── favicon.svg                        # Ikon Perisai JagaWarga RW
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php             # Halaman Login & Kartu Demo 1-Klik
│       ├── dashboard/
│       │   └── index.blade.php             # Command Center, Leaflet Heatmap, & Geofence Form
│       ├── layouts/
│       │   └── app.blade.php               # Master Layout PWA, Navigasi, & Audio Kentongan
│       ├── ronda/
│       │   └── index.blade.php             # Modul Ronda, Kamera QR html5-qrcode, & Presensi
│       ├── warga/
│       │   └── index.blade.php             # Portal Warga, Kentongan Geofence, Lapor, & Tamu
│       └── welcome.blade.php               # Beranda Interaktif JagaWarga RW
└── routes/
    └── web.php                             # Definisi Rute Publik, Terproteksi, & API
```

---

## 📄 Lisensi & Hak Cipta

Aplikasi web **JagaWarga RW** dikembangkan sebagai solusi digital integrasi keamanan lingkungan warga dan pos kamling. Proyek ini dilisensikan di bawah lisensi terbuka [MIT License](https://opensource.org/licenses/MIT).

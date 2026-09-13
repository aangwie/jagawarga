# 🛡️ JagaWarga RW — Sistem Integrasi Keamanan Warga Digital (PWA)

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Leaflet](https://img.shields.io/badge/Leaflet-1.9.4-199900?style=for-the-badge&logo=leaflet&logoColor=white)](https://leafletjs.com)
[![PWA Ready](https://img.shields.io/badge/PWA-Enabled-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white)](https://web.dev/progressive-web-apps/)

**JagaWarga RW** adalah aplikasi web berbasis **Progressive Web App (PWA)** yang dirancang untuk mengintegrasikan keamanan warga, pos ronda (poskamling), pengurus RT/RW, Bhabinkamtibmas, dan Nakes Puskesmas ke dalam satu platform digital modern yang responsif, cepat, dan mudah diakses langsung melalui peramban ponsel tanpa perlu mengunduh aplikasi dari Play Store atau App Store.

---

## 📌 Daftar Isi

- [Latar Belakang & Konsep Utama](#-latar-belakang--konsep-utama)
- [Fitur Utama](#-fitur-utama)
  - [1. Kentongan Online (Tombol Darurat Digital)](#1--kentongan-online-tombol-darurat-digital-dengan-audio-spesifik-per-kejadian)
  - [2. Lapor Cepat Kejadian Warga](#2--lapor-cepat-kejadian-warga)
  - [3. Buku Tamu Digital 2x24 Jam (WNI & WNA)](#3--buku-tamu-digital-wajib-lapor-2x24-jam-wni--wna)
  - [4. Presensi Ronda & Pemindai QR Checkpoint](#4--presensi-ronda--pemindai-qr-checkpoint)
  - [5. Command Center & Peta Kerawanan Heatmap](#5-%EF%B8%8F-command-center--peta-kerawanan-heatmap-leafletjs)
  - [6. Manajemen Jadwal Ronda & WhatsApp Reminder](#6--manajemen-jadwal-ronda--whatsapp-reminder)
  - [7. Analitik Kamtibmas Chart.js](#7--analitik-kamtibmas-chartjs)
  - [8. Pemantauan CCTV Lingkungan (CRUD & Upload Video)](#8--pemantauan-cctv-lingkungan-crud--upload-video)
  - [9. Manajemen Titik Rawan Patroli (Checkpoint CRUD)](#9--manajemen-titik-rawan-patroli-checkpoint-crud)
  - [10. Sistem Multi-Peran (Multi-Role RBAC)](#10--sistem-multi-peran-multi-role-rbac)
  - [11. Pengikatan Perangkat Warga (1 Akun = 1 HP)](#11--pengikatan-perangkat-warga-1-akun--1-hp)
  - [12. Verifikasi NIK Warga](#12--verifikasi-nik-warga)
  - [13. Manajemen Pengguna (User Management)](#13--manajemen-pengguna-user-management)
  - [14. Import Data Warga via Excel](#14--import-data-warga-via-excel)
  - [15. Manajemen Permohonan Ganti Perangkat](#15--manajemen-permohonan-ganti-perangkat)
  - [16. Riwayat Kentongan (Edit & Hapus)](#16--riwayat-kentongan-edit--hapus)
  - [17. Siaran Real-Time SSE (Server-Sent Events)](#17--siaran-real-time-sse-server-sent-events)
  - [18. PWA & Registrasi Perangkat](#18--pwa--registrasi-perangkat)
  - [19. Pengaturan Sistem & Auto-Update GitHub](#19-%EF%B8%8F-pengaturan-sistem--auto-update-github)
- [Arsitektur Hak Akses & Halaman](#-arsitektur-hak-akses--halaman)
- [Teknologi yang Digunakan](#%EF%B8%8F-teknologi-yang-digunakan)
- [Kredensial Akun Pengguna (Seeder)](#-kredensial-akun-pengguna-seeder)
- [Panduan Instalasi & Menjalankan Aplikasi](#-panduan-instalasi--menjalankan-aplikasi)
- [Panduan Penggunaan Lengkap](#-panduan-penggunaan-lengkap)
  - [1. Sisi Warga (Publik)](#1-sisi-warga-publik)
  - [2. Sisi Petugas Ronda Poskamling](#2-sisi-petugas-ronda-poskamling)
  - [3. Sisi Command Center Pengurus RW](#3-sisi-command-center-pengurus-rw)
  - [4. Sisi Manajemen Pengguna (Admin)](#4-sisi-manajemen-pengguna-admin)
  - [5. Sisi Permohonan Ganti Perangkat](#5-sisi-permohonan-ganti-perangkat)
  - [6. Sisi Pengaturan Sistem](#6-sisi-pengaturan-sistem)
- [Mekanisme Geofencing Radius (300 Meter)](#-mekanisme-geofencing-radius-300-meter)
- [Mekanisme Keamanan Perangkat (1 Akun = 1 HP)](#-mekanisme-keamanan-perangkat-1-akun--1-hp)
- [Struktur Direktori Proyek](#-struktur-direktori-proyek)
- [Lisensi & Hak Cipta](#-lisensi--hak-cipta)

---

## 💡 Latar Belakang & Konsep Utama

Sistem keamanan lingkungan tradisional sering menghadapi kendala seperti sinyal kentongan fisik yang tidak terdengar oleh seluruh warga, ronda malam yang sulit dipantau secara nyata (*blind spot*), pendataan tamu 2x24 jam yang tidak tercatat rapi, serta lambatnya koordinasi penanganan insiden darurat.

**JagaWarga RW** menjawab tantangan tersebut dengan menghadirkan:
1. **Kentongan Online**: Suara alarm akustik dan siaran notifikasi instan ke pos ronda dan pengurus saat terjadi bahaya (*maling, kebakaran, medis*).
2. **Geofencing Radius Keamanan**: Tombol panic hanya dapat digunakan di dalam radius wilayah RW (default: **300 meter**) oleh warga yang akun & NIK-nya telah terverifikasi untuk mencegah *false alarm*.
3. **Patroli Ronda Anti-Fraud**: Scan QR checkpoint di titik rawan menggunakan kamera web HP yang diverifikasi dengan waktu nyata dan koordinat GPS petugas.
4. **Buku Tamu Digital 2x24 Jam**: Wajib lapor mandiri bagi pendatang baru (WNI & WNA) sebelum diverifikasi Ketua RT.
5. **Command Center & Heatmap Kerawanan**: Visualisasi peta insiden interaktif (*Leaflet Heatmap*) dan otomasi pengingat jadwal ronda via WhatsApp Gateway.
6. **Multi-Peran & Keamanan Perangkat**: Satu akun bisa memegang beberapa peran sekaligus, dan akun Warga diikat ke satu perangkat HP terdaftar untuk mencegah penyalahgunaan.
7. **Manajemen Lengkap**: CRUD pengguna, titik patroli, CCTV, jadwal ronda, dan auto-update web dari GitHub.

---

## ✨ Fitur Utama

### 1. 🚨 Kentongan Online (Tombol Darurat Digital dengan Audio Spesifik per Kejadian)

- **Tombol Sentuh Raksasa**: Animasi denyut radar merah beresonansi tinggi (*panic-pulse*).
- **Validasi 3 Lapis Sebelum Aktivasi**:
  1. **Autentikasi**: Warga wajib login — tombol tidak dapat digunakan oleh pengunjung anonim.
  2. **Verifikasi NIK**: Hanya warga yang NIK-nya telah diverifikasi sah oleh pengurus RW/RT yang dapat mengaktifkan kentongan. Akun dengan status *Belum Terverifikasi* akan ditolak dengan pesan jelas.
  3. **Geofencing Radius**: Koordinat GPS perangkat wajib berada dalam radius aktif (default 300 meter).
- **Perbedaan Suara & Ritme Kentongan Khas Nusantara per Kategori Kejadian**:
  - 🚨 **Maling / Curanmor**: Ketukan bertubi-tubi sangat cepat rapat (*Doro Muluk / Titir Maling*, interval 120ms) dipadukan nada alarm maling tajam berdenyut + siaran suara otomatis (*Voice Broadcast*): *"Perhatian! Ada maling atau pencurian di lingkungan warga! Warga segera siaga kepung lokasi!"*.
  - 🔥 **Bahaya Kebakaran**: Ketukan rangkap ganda bergaung (*Titir Ganda: Tang-Tang... Tang-Tang...*) dipadukan sirene pemadam kebakaran melolong naik-turun berkala + siaran suara otomatis: *"Perhatian! Bahaya kebakaran! Bawa air dan alat pemadam, segera bantu lokasi!"*.
  - 🚑 **Darurat Medis / Ambulans**: Sirene dua nada khas ambulans (*WEE-WOO Hi-Lo Siren*, 750Hz - 540Hz) diselingi ketukan kentongan bulat ritmis lambat + siaran suara otomatis: *"Panggilan darurat medis! Pertolongan pertama dan ambulans dibutuhkan!"*.
  - ⚠️ **Siaga Lingkungan / Lainnya**: Ketukan kentongan panggilan pos ronda siaga (*Tong... Tong... Tong-Tong-Tong*) diselingi nada chime peringatan + siaran suara otomatis: *"Perhatian! Peringatan siaga keamanan lingkungan RW 02!"*.
- **Sintesis Audio Web Audio API & Web Speech API**: Menghasilkan resonansi kayu kentongan, variasi sirene, dan pengumuman vokal bahasa Indonesia secara *real-time* langsung dari peramban ponsel tanpa perlu mengunduh file MP3/WAV berat.
- **Fitur Tes Suara Tiap Kategori**: Pengguna dapat mendengarkan sampel bunyi masing-masing kategori melalui tombol **"🔊 Tes Bunyi"** di formulir warga atau dropdown **"Tes Suara"** di bilah navigasi atas.
- **Validasi Geofencing Ganda (Client + Server)**: Tombol terkunci otomatis (*disabled/grayscale*) jika warga berada di luar jangkauan wilayah RW, dan server memvalidasi ulang pada endpoint `POST /api/panic`.
- **Tombol Simulasi Jarak**: Mempermudah demonstrasi (*🟢 Posko <300m, 🔴 Luar Wilayah >300m, 📍 GPS Asli*).
- **Broadcast ke Perangkat PWA**: Setelah sinyal darurat tersimpan, sistem menghitung jumlah perangkat PWA terdaftar yang siap menerima notifikasi dan melaporkannya kepada pengirim.

### 2. 📸 Lapor Cepat Kejadian Warga

- Formulir pengaduan lingkungan ramah ponsel.
- Opsi unggah foto bukti langsung dari galeri atau kamera HP (maks. 5 MB).
- Pencatatan koordinat GPS lokasi kejadian secara otomatis.
- Pelacakan status penanganan laporan secara transparan (*Menunggu, Diproses, Selesai*).
- Riwayat 10 laporan terbaru ditampilkan di portal warga.

### 3. 🪪 Buku Tamu Digital (Wajib Lapor 2x24 Jam, WNI & WNA)

- Registrasi mandiri tamu atau kerabat menginap.
- **Dukungan Kewarganegaraan Ganda**:
  - **WNI**: Wajib mengisi NIK (Nomor Induk Kependudukan 16 digit).
  - **WNA**: Wajib mengisi Nomor Paspor / Dokumen Imigrasi.
- Pencatatan identitas lengkap: Nama, Kewarganegaraan, NIK/Paspor, No. WhatsApp, Alamat Asal, Nama Warga yang Dikunjungi, Tujuan Kunjungan, RT Tujuan, Tanggal Tiba, dan Tanggal Keluar.
- Validasi otomatis sisi server berdasarkan kewarganegaraan (NIK wajib untuk WNI, Paspor wajib untuk WNA).
- Notifikasi langsung ke Ketua RT setempat untuk verifikasi fisik.

### 4. 🔦 Presensi Ronda & Pemindai QR Checkpoint

- Kamera pemindai QR aktif langsung di peramban web HP menggunakan library `html5-qrcode` tanpa aplikasi pihak ketiga.
- Verifikasi koordinat GPS ganda saat scan untuk memastikan petugas benar-benar berada di titik rawan poskamling.
- **Progress Bar Patroli Malam**: Menampilkan persentase rute checkpoint yang telah diselesaikan malam ini (misal: 2 dari 4 checkpoint = 50%).
- Riwayat presensi malam ini ditampilkan lengkap dengan nama petugas, titik checkpoint, waktu scan, dan catatan.
- Formulir catatan jaga malam dan log patroli berkala.
- **Tombol Verifikasi Hadir**: Alternatif presensi tanpa kamera fisik (untuk simulasi demo).

### 5. 🗺️ Command Center & Peta Kerawanan Heatmap Leaflet.js

- **Heatmap Densitas Insiden**: Gradien warna *Leaflet.heat* (Merah: Area Rawan, Kuning: Sedang, Hijau: Kondusif) berdasarkan agregat data dari:
  - Titik Panic Alert / Kentongan Darurat (intensitas tinggi: 1.0).
  - Titik Laporan Kejadian Warga (intensitas sedang: 0.6).
- **Visualisasi Lingkaran Geofence**: Menampilkan batas radius aktif jangkauan siaga RW.
- **Marker Titik Checkpoint Interaktif**: Menampilkan nama titik, penanggung jawab RT, kode QR, tingkat kerawanan, dan link Google Maps.
- **Konfigurasi Titik Pusat & Radius**: Admin RW dapat mengubah titik tengah posko dan jarak radius meter langsung dari dashboard dengan fitur geser pin marker (*draggable*) dan preview peta interaktif.
- **Kartu Metrik Ringkas**: Total Warga, Petugas Ronda Aktif, Total Laporan, Total Panic Alert, Total Checkpoint, dan Tamu Terdata.
- **Tabel Riwayat Terbaru**: 5 data terbaru dari Panic Alert, Laporan Kejadian, dan Buku Tamu ditampilkan langsung di dashboard.

### 6. 📅 Manajemen Jadwal Ronda & WhatsApp Reminder

- Matriks jadwal giliran ronda warga per hari (Senin s/d Minggu) per RT.
- Modal tambah dan hapus jadwal ronda.
- **WhatsApp Reminder Gateway**: Integrasi pesan pengingat jadwal otomatis ke nomor HP warga (*Fonnte/Wablas compatible*).
- Template pesan pengingat siap pakai dengan informasi jadwal, waktu, dan pesan dari Pengurus RW.

### 7. 📊 Analitik Kamtibmas Chart.js

- **Grafik Batang**: Tingkat keaktifan patroli checkpoint ronda mingguan.
- **Grafik Donat**: Proporsi kategori insiden lingkungan.
- **Kartu Metrik Ringkas**: Total Warga, Petugas Ronda Aktif, Total Laporan, dan Tamu Terdata.

### 8. 📹 Pemantauan CCTV Lingkungan (CRUD & Upload Video)

- **Dua Mode Input CCTV**:
  - **Link URL Stream**: Masukkan URL stream kamera IP/CCTV (format HLS `.m3u8`, YouTube, atau URL video lainnya).
  - **Upload File Video**: Unggah rekaman video CCTV langsung dari perangkat (format MP4, WebM, OGG, MOV, MKV — maks. 50 MB).
- **CRUD Lengkap**: Tambah, edit, dan hapus kamera CCTV dari dashboard.
- Pengelolaan per RT dengan status aktif/nonaktif.
- Pemutar streaming langsung di halaman beranda dan dashboard.

### 9. 📍 Manajemen Titik Rawan Patroli (Checkpoint CRUD)

- **CRUD Lengkap**: Tambah, edit, dan hapus titik rawan patroli langsung dari dashboard.
- Setiap checkpoint mencakup: Nama Titik, Koordinat GPS (Latitude/Longitude), Deskripsi, Tingkat Kerawanan (🟢 Aman, 🟡 Sedang, 🔴 Rawan), RT, Urutan Patroli, dan Kode QR.
- **Auto-Generate Kode QR**: Kode QR unik dihasilkan otomatis berdasarkan nama titik (format: `JW-CKP-XXX-SLUG`).
- **Link Google Maps**: Setiap titik memiliki link langsung ke Google Maps untuk navigasi GPS.

### 10. 🎭 Sistem Multi-Peran (Multi-Role RBAC)

- Satu akun pengguna dapat memiliki **beberapa peran sekaligus** (contoh: Ketua RT sekaligus Petugas Ronda dan Warga).
- **Layar Pemilihan Peran (Select Role)**: Setelah login, pengguna diarahkan ke layar pemilihan peran yang menampilkan semua peran yang dimiliki. Pengguna memilih peran mana yang ingin digunakan untuk sesi saat ini.
- **Switch Role dari Navbar**: Pengguna dapat berganti peran aktif kapan saja melalui header navigasi tanpa perlu logout ulang.
- **6 Peran yang Tersedia**:

  | Ikon | Nama Peran | Kode Peran | Deskripsi |
  |---|---|---|---|
  | 🏠 | Warga Lingkungan | `warga` | Akses Panic Button (Kentongan), Pelaporan Kejadian, Buku Tamu |
  | 🛡️ | Petugas Ronda | `petugas_ronda` | Portal Poskamling, Scanner QR Checkpoint, Presensi Ronda |
  | 🏘️ | Ketua RT | `rt` | Validasi Tamu, Monitoring RT, Command Center Dashboard |
  | 🗺️ | Pengurus RW (Admin) | `rw` | Command Center, Manajemen Pengguna, CCTV, Pengaturan Sistem |
  | ⭐ | Bhabinkamtibmas | `bhabinkamtibmas` | Monitoring Kamtibmas Kepolisian di Wilayah RW |
  | 🩺 | Nakes Puskesmas | `nakes_puskesmas` | Respon Medis Darurat, Penanganan Ambulans, Pemantauan Kesehatan |

### 11. 🔐 Pengikatan Perangkat Warga (1 Akun = 1 HP)

- **Kebijakan Keamanan**: Akun Warga hanya boleh digunakan pada 1 (satu) perangkat HP/browser yang terdaftar.
- **Registrasi Otomatis**: Saat pertama kali memilih peran "Warga", perangkat aktif otomatis terdaftar sebagai perangkat resmi akun tersebut.
- **Penolakan Perangkat Baru**: Jika warga login dari perangkat berbeda, akses ke peran Warga akan **ditolak** dengan pesan jelas yang menampilkan informasi perangkat terdaftar.
- **Alur Pengajuan Ganti Perangkat**: Warga yang ingin berpindah perangkat dapat mengajukan permohonan melalui formulir khusus (`/device-reset`) yang diverifikasi pengurus.
- Peran non-warga (Petugas Ronda, RT, RW, dll.) **tidak terikat perangkat** dan bebas digunakan di manapun.

### 12. ✅ Verifikasi NIK Warga

- Setiap akun warga memiliki status verifikasi NIK (`is_nik_verified`).
- **Hanya warga dengan NIK terverifikasi** yang dapat menggunakan Tombol Panic / Kentongan Online.
- Pengurus RW/RT dapat **mengaktifkan atau mencabut status verifikasi** langsung dari halaman Manajemen Pengguna melalui tombol toggle.
- Akun demo *"Doni Belum Verifikasi"* disediakan untuk menguji alur penolakan kentongan.

### 13. 👥 Manajemen Pengguna (User Management)

- **Halaman Khusus Pengguna** (`/users`): Daftar seluruh pengguna dengan filter berdasarkan peran dan pencarian kata kunci.
- **Statistik Per Peran**: Menampilkan jumlah pengguna per peran (Warga, Petugas Ronda, RT, RW, Bhabin, Nakes).
- **CRUD Pengguna Lengkap**:
  - **Tambah Pengguna**: Formulir lengkap dengan nama, email, NIK (16 digit), password, no. HP, nama ibu kandung, RT/RW, alamat, no. rumah, dan pilihan multi-peran.
  - **Edit Pengguna**: Perbarui seluruh data termasuk peran dan status verifikasi NIK.
  - **Hapus Pengguna**: Hapus individual dengan proteksi agar tidak bisa menghapus akun sendiri.
  - **Hapus Massal (Bulk Delete)**: Pilih beberapa warga sekaligus dan hapus dalam satu operasi (dengan proteksi akun sendiri).
- **Toggle Verifikasi NIK**: Verifikasi atau cabut status NIK langsung dari tombol di tabel pengguna.
- **Reset Perangkat**: Reset ikatan perangkat warga langsung dari manajemen pengguna.

### 14. 📥 Import Data Warga via Excel

- **Unduh Template Excel**: Template `.xlsx` siap pakai dengan header kolom terformat, contoh data, dan styling profesional (warna Emerald branding JagaWarga).
- **Format yang Didukung**: `.xlsx`, `.xls`, `.csv` (maks. 10 MB).
- **Kolom Template**:

  | Kolom | Keterangan | Wajib? |
  |---|---|---|
  | A | No | - |
  | B | Nama Lengkap | ✅ Wajib |
  | C | NIK 16 Digit | ✅ Wajib |
  | D | Password | Opsional (default: `password`) |
  | E | Email | Opsional (auto-generate jika kosong) |
  | F | Nomor WhatsApp / HP | Opsional |
  | G | Nama Ibu Kandung | Opsional |
  | H | RT | Opsional (default: `01`) |
  | I | RW | Opsional (default: `02`) |
  | J | No Rumah | Opsional |
  | K | Alamat Lengkap | Opsional |

- **Perilaku Import**:
  - Jika NIK sudah ada: Data diperbarui (*update*).
  - Jika NIK belum ada: Akun baru dibuat dengan peran default `warga`.
  - Email auto-generate jika kosong: `warga_<NIK>@jagawarga.local`.
  - Semua warga yang diimport otomatis berstatus NIK terverifikasi.
- **Laporan Hasil**: Menampilkan jumlah data ditambahkan, diperbarui, dan dilewati beserta alasan error per baris.

### 15. 📱 Manajemen Permohonan Ganti Perangkat

- **Formulir Pengajuan Warga** (`/device-reset`): Warga yang ingin mengganti HP mengisi NIK, Nama Ibu Kandung (verifikasi), No. HP baru, dan alasan.
- **Panel Admin** (`/device-requests`): Pengurus melihat daftar permohonan masuk dengan filter status (Menunggu, Disetujui, Ditolak).
- **Statistik Permohonan**: Total permohonan, jumlah menunggu, disetujui, dan ditolak.
- **Aksi Admin**:
  - **Setujui**: Reset perangkat lama dan daftarkan perangkat baru yang diajukan warga.
  - **Tolak**: Berikan catatan penolakan.
- **Pencegahan Duplikat**: Warga tidak dapat mengajukan permohonan baru jika sudah ada permohonan yang berstatus *pending*.

### 16. 📝 Riwayat Kentongan (Edit & Hapus)

- Pengurus (RT, RW, Bhabinkamtibmas, Nakes) dapat **mengedit jenis kejadian (kategori) dan keterangan (catatan)** pada riwayat kentongan yang telah tercatat.
- Pengurus juga dapat **menghapus rekaman riwayat kentongan** dari sistem.
- **Daftar Riwayat JSON**: Endpoint `GET /api/panic/data` menyediakan seluruh riwayat kentongan dalam format JSON lengkap dengan badge, ikon, koordinat, link Google Maps, dan nama pelapor.
- **Cek Kentongan Aktif**: Endpoint `GET /api/panic/latest-active` memeriksa apakah ada kentongan berstatus aktif dalam 15 menit terakhir.

### 17. 📡 Siaran Real-Time SSE (Server-Sent Events)

- **Endpoint `GET /api/panic/stream`**: Koneksi SSE (*Server-Sent Events*) yang memungkinkan peramban menerima data kentongan darurat baru secara *real-time* tanpa polling.
- Setiap 2 detik, server memeriksa apakah ada Panic Alert aktif terbaru (dalam 15 menit terakhir).
- Jika terdeteksi alert baru, data lengkap dikirimkan ke semua perangkat yang mendengarkan stream.
- Jika tidak ada alert, server mengirim heartbeat untuk menjaga koneksi tetap hidup.

### 18. 📲 PWA & Registrasi Perangkat

- **Progressive Web App (PWA)**: Dukungan *Add to Home Screen*, Service Worker caching, dan halaman fallback offline (`offline.html`).
- **Registrasi Perangkat PWA**: Setiap perangkat yang mengunjungi JagaWarga otomatis mendaftarkan diri melalui `POST /api/pwa/register-device` (menyimpan device ID, status instalasi PWA, izin notifikasi, info browser, dan push subscription).
- **Statistik Perangkat**: Endpoint `GET /api/pwa/status` menampilkan jumlah total perangkat, yang terinstal PWA, yang mengizinkan notifikasi, dan yang aktif dalam 24 jam terakhir.
- **Manifest & Service Worker**: File `manifest.webmanifest` dan `sw.js` tersedia untuk dukungan pemasangan PWA penuh.

### 19. ⚙️ Pengaturan Sistem & Auto-Update GitHub

- **Halaman Pengaturan** (`/settings`): Panel administrasi untuk konfigurasi teknis web.
- **Konfigurasi GitHub PAT**: Simpan nama repositori, branch target, dan GitHub Personal Access Token ke database (`tabel pengaturan`).
- **Cek Pembaruan**: Tombol **"Cek Update"** membandingkan commit SHA lokal dengan commit terbaru di GitHub via API.
- **Auto-Update Web dari GitHub**:
  - **Metode 1 (Git Pull)**: Jika folder `.git` tersedia, eksekusi `git pull` dengan autentikasi PAT.
  - **Metode 2 (Fallback ZIP)**: Jika Git tidak tersedia, unduh arsip zipball dari GitHub API, ekstrak, dan salin file terbaru (kecuali `.env`, `storage`, `vendor`, `.git`).
  - Setelah update, otomatis menjalankan `php artisan optimize:clear` dan `php artisan migrate --force`.
  - **Log Terminal**: Seluruh proses dicatat dalam log terminal yang ditampilkan di UI dan disimpan ke database.
  - **Sensor Token PAT**: Token PAT otomatis disensor dari output log agar tidak bocor.
- **Symlink Storage**: Tombol untuk menghubungkan `public/storage` ke `storage/app/public` dengan fallback native symlink.
- **Info Sistem**: Menampilkan versi PHP, Laravel, OS server, driver database, dan lingkungan aplikasi.

---

## 🔐 Arsitektur Hak Akses & Halaman

Aplikasi menerapkan pemisahan hak akses berbasis peran (*Role-Based Access Control / RBAC*) dengan dukungan middleware terproteksi dan sistem multi-role:

### Halaman Web

| URL Rute | Hak Akses | Peran (Role) | Fungsi Utama |
|---|---|---|---|
| `GET /` | **Publik** | Semua Pengunjung | Beranda utama PWA, Kentongan Online Geofence, status wilayah, live CCTV |
| `GET /warga` | **Publik** | Semua Pengunjung | Portal Warga: Tombol Panic Darurat, Lapor Cepat Foto, Buku Tamu 2x24 Jam |
| `GET /login` | **Publik** | Tamu (Guest) | Halaman login dengan formulir kredensial dan kartu **Demo 1-Klik** |
| `POST /login` | **Publik** | Tamu (Guest) | Proses autentikasi via Email / NIK dan Password |
| `GET /device-reset` | **Publik** | Tamu (Guest) | Formulir pengajuan ganti perangkat warga |
| `GET /select-role` | **Terproteksi** | Pengguna Login | Layar pemilihan peran aktif setelah login |
| `POST /switch-role` | **Terproteksi** | Pengguna Login | Pergantian peran aktif dari navbar |
| `POST /logout` | **Terproteksi** | Pengguna Login | Mengakhiri sesi pengguna |
| `GET /ronda` | **Terproteksi** | `petugas_ronda`, `rt`, `rw`, `bhabinkamtibmas`, `nakes_puskesmas` | Modul Petugas: Scan QR Kamera HP, Presensi GPS, Log Jaga Malam |
| `GET /dashboard` | **Terproteksi** | `rt`, `rw`, `bhabinkamtibmas`, `nakes_puskesmas` | Command Center: Peta Heatmap, Geofence, Jadwal, CCTV, Checkpoint |
| `GET /users` | **Terproteksi** | `rt`, `rw`, `bhabinkamtibmas`, `nakes_puskesmas` | Manajemen Pengguna: CRUD, Import Excel, Bulk Delete, Verifikasi NIK |
| `GET /device-requests` | **Terproteksi** | `rt`, `rw`, `bhabinkamtibmas`, `nakes_puskesmas` | Permohonan Ganti Perangkat Warga |
| `GET /settings` | **Terproteksi** | `rt`, `rw`, `bhabinkamtibmas`, `nakes_puskesmas` | Pengaturan Sistem: GitHub PAT, Auto-Update, Symlink Storage |

### Endpoint API Publik

| Endpoint | Metode | Fungsi |
|---|---|---|
| `POST /api/panic` | POST | Kirim sinyal darurat (validasi login + NIK + geofence radius) |
| `GET /api/panic/data` | GET | Daftar riwayat seluruh kentongan (JSON) |
| `GET /api/panic/latest-active` | GET | Cek kentongan aktif terkini (15 menit terakhir) |
| `GET /api/panic/stream` | GET | Real-time SSE stream kentongan darurat |
| `POST /api/lapor` | POST | Simpan pengaduan kejadian warga |
| `POST /api/buku-tamu` | POST | Simpan registrasi tamu 2x24 jam (WNI/WNA) |
| `GET /api/settings/public` | GET | Baca konfigurasi geofence RW (koordinat & radius) |
| `POST /api/pwa/register-device` | POST | Daftarkan perangkat PWA |
| `GET /api/pwa/status` | GET | Statistik perangkat PWA terdaftar |

### Endpoint API Terproteksi

| Endpoint | Metode | Fungsi |
|---|---|---|
| `POST /api/presensi` | POST | Simpan scan QR presensi ronda |
| `POST /api/jadwal` | POST | Tambah jadwal ronda warga |
| `DELETE /api/jadwal/{id}` | DELETE | Hapus jadwal ronda |
| `POST /api/wa-reminder` | POST | Kirim pesan pengingat WhatsApp |
| `POST /api/settings/geofence` | POST | Perbarui koordinat & radius geofence RW |
| `POST /api/checkpoints` | POST | Tambah titik rawan patroli |
| `PUT /api/checkpoints/{id}` | PUT | Edit titik rawan patroli |
| `DELETE /api/checkpoints/{id}` | DELETE | Hapus titik rawan patroli |
| `POST /api/cctv` | POST | Tambah kamera CCTV (link/upload) |
| `POST /api/cctv/{id}` | POST | Edit kamera CCTV (link/upload) |
| `DELETE /api/cctv/{id}` | DELETE | Hapus kamera CCTV |
| `PUT /api/panic/{id}` | PUT | Edit riwayat kentongan (kategori & catatan) |
| `DELETE /api/panic/{id}` | DELETE | Hapus riwayat kentongan |
| `POST /users` | POST | Tambah pengguna baru |
| `PUT /users/{id}` | PUT | Edit data pengguna |
| `DELETE /users/{id}` | DELETE | Hapus pengguna |
| `POST /users/{id}/reset-device` | POST | Reset perangkat pengguna |
| `POST /users/{id}/toggle-nik` | POST | Toggle verifikasi NIK |
| `GET /users/template/excel` | GET | Unduh template Excel import warga |
| `POST /users/import` | POST | Import data warga dari file Excel |
| `POST /users/bulk-delete` | POST | Hapus banyak pengguna sekaligus |
| `POST /device-requests/{id}/approve` | POST | Setujui permohonan ganti perangkat |
| `POST /device-requests/{id}/reject` | POST | Tolak permohonan ganti perangkat |
| `POST /settings/github` | POST | Simpan konfigurasi GitHub PAT |
| `POST /settings/github/check` | POST | Cek pembaruan dari GitHub |
| `POST /settings/github/update` | POST | Eksekusi auto-update dari GitHub |
| `POST /settings/storage-link` | POST | Buat symlink storage |

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: [Laravel 11](https://laravel.com/) (PHP 8.2+)
- **Basis Data**: MySQL (25 file migrasi, tabel terstruktur dengan Eloquent ORM)
- **Frontend / Styling**: [Tailwind CSS v4](https://tailwindcss.com/) dengan skema warna Emerald, Slate, dan Violet, didukung tipografi Google Fonts *Plus Jakarta Sans*.
- **PWA & Offline Capability**:
  - `manifest.webmanifest` untuk dukungan *Add to Home Screen*.
  - `sw.js` (Service Worker) untuk caching antarmuka dan fallback `offline.html`.
  - Tabel `pwa_devices` untuk pelacakan perangkat PWA terdaftar dan izin notifikasi.
- **Geospasial & Peta**:
  - [Leaflet.js v1.9.4](https://leafletjs.com/) (Peta interaktif OpenStreetMap).
  - [Leaflet.heat](https://github.com/Leaflet/Leaflet.heat) (Layer visualisasi kepadatan titik insiden).
- **Pemindai QR**: [html5-qrcode v2.3.8](https://github.com/mebjas/html5-qrcode) (Akses kamera smartphone langsung dari browser).
- **Grafik Statistik**: [Chart.js](https://www.chartjs.org/) (Grafik batang kehadiran ronda dan donat insiden).
- **Audio Synthesizer**: *Web Audio API native browser* (Tanpa file WAV/MP3 eksternal).
- **Import/Export Excel**: [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) (Template Excel, import `.xlsx/.xls/.csv`).
- **Real-Time Streaming**: Server-Sent Events (SSE) native PHP untuk siaran kentongan darurat.
- **Auto-Update**: GitHub REST API v3 via Laravel HTTP Client, Symfony Process untuk `git pull`.

---

## 👥 Kredensial Akun Pengguna (Seeder)

Seluruh akun telah disiapkan dalam `DatabaseSeeder.php` dengan kata sandi default: **`password`**.

| Peran (Role) | Nama Pengguna | Email Login | NIK | Multi-Role | Akses Halaman |
|---|---|---|---|---|---|
| **Admin Pengurus RW** | Pak Gunawan (Ketua RW 02) | `rw02@jagawarga.local` | `3201010101010007` | 🗺️ RW + 🏘️ RT + 🏠 Warga | `/dashboard`, `/users`, `/settings`, `/ronda`, `/warga` |
| **Ketua RT 01** | Pak Bambang (Ketua RT 01) | `rt01@jagawarga.local` | `3201010101010005` | 🏘️ RT + 🛡️ Ronda + 🏠 Warga | `/dashboard`, `/users`, `/ronda`, `/warga` |
| **Ketua RT 02** | Pak Heri (Ketua RT 02) | `rt02@jagawarga.local` | `3201010101010006` | 🏘️ RT + 🏠 Warga | `/dashboard`, `/users`, `/warga` |
| **Petugas Ronda 1** | Pak Joko Ronda | `ronda@jagawarga.local` | `3201010101010003` | 🛡️ Ronda + 🏠 Warga | `/ronda`, `/warga` |
| **Petugas Ronda 2** | Kang Asep Patroli | `asep@jagawarga.local` | `3201010101010004` | 🛡️ Ronda + 🏠 Warga | `/ronda`, `/warga` |
| **Bhabinkamtibmas** | Aiptu Hendro Prasetyo | `bhabin@jagawarga.local` | `3201010101010008` | ⭐ Bhabin + 🏠 Warga | `/dashboard`, `/users`, `/ronda`, `/warga` |
| **Nakes Puskesmas** | dr. Sarah Amalia | `nakes@jagawarga.local` | `3201010101010009` | 🩺 Nakes + 🏠 Warga | `/dashboard`, `/users`, `/ronda`, `/warga` |
| **Warga 1** | Budi Santoso | `warga@jagawarga.local` | `3201010101010001` | 🏠 Warga | `/warga` |
| **Warga 2** | Siti Rahayu | `siti@jagawarga.local` | `3201010101010002` | 🏠 Warga | `/warga` |
| **Warga Belum Verifikasi** | Doni Belum Verifikasi | `unverified@jagawarga.local` | `3201010101010999` | 🏠 Warga (NIK ❌) | `/warga` (Kentongan Terkunci) |

> 💡 **Fitur Cepat (Demo Login 1-Klik)**:
> Pada halaman [http://localhost:8000/login](http://localhost:8000/login), Anda cukup mengklik salah satu kartu peran (*Petugas Ronda, Admin RW, Ketua RT, Bhabin, Nakes, Warga, atau Warga Belum Verifikasi*) untuk langsung masuk tanpa perlu mengetik email & password. Setelah login, Anda akan diarahkan ke layar pemilihan peran.

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### 1. Prasyarat Sistem

- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `zip`)
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
   *Perintah ini akan membuat seluruh 25 file migrasi basis data dan mengisinya dengan: 10 akun demo (termasuk multi-role dan akun belum terverifikasi), 4 titik checkpoint patroli, 3 kamera CCTV, riwayat presensi, jadwal ronda mingguan, laporan kejadian contoh, riwayat kentongan, data buku tamu, dan pengaturan geofence default (300 meter).*

7. **Hubungkan Storage (Opsional)**:
   ```bash
   php artisan storage:link
   ```
   *Atau lakukan melalui halaman Pengaturan Sistem setelah login sebagai Admin RW.*

8. **Jalankan Server Lokal Laravel**:
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan di: **[http://127.0.0.1:8000](http://127.0.0.1:8000)** atau **[http://localhost:8000](http://localhost:8000)**.

---

## 📖 Panduan Penggunaan Lengkap

### 1. Sisi Warga (Publik)

Akses: **[http://localhost:8000/warga](http://localhost:8000/warga)** atau **[http://localhost:8000/](http://localhost:8000/)**

#### Menggunakan Tombol Panic (Kentongan Online):

> ⚠️ **Syarat Wajib**: Warga harus login dan NIK-nya telah terverifikasi oleh pengurus RW/RT. Warga yang belum login atau NIK-nya belum terverifikasi akan mendapat pesan penolakan yang jelas.

1. Login terlebih dahulu melalui `/login` (gunakan **Demo 1-Klik: Warga** untuk kemudahan).
2. Pada layar pemilihan peran, pilih **🏠 Warga Lingkungan**.
3. Pilih salah satu kategori bahaya (*Maling / Curanmor, Kebakaran, Darurat Medis, atau Lainnya*).
4. Sistem otomatis membaca koordinat GPS perangkat Anda dan menghitung jaraknya ke pusat Posko RW 02.
5. **Jika Anda berada di dalam radius (< 300 meter)**:
   - Tombol akan berdenyut merah dengan tulisan **"DARURAT KENTONGAN"**.
   - Klik tombol: Alarm sirene dan ketukan bambu kentongan digital akan berbunyi seketika, dan sinyal disiarkan ke pos ronda serta seluruh perangkat PWA warga.
6. **Jika Anda berada di luar radius (> 300 meter)**:
   - Tombol otomatis menjadi abu-abu (*grayscale*) dan terkunci dengan tulisan **"TERKUNCI / DI LUAR RADIUS 300M"**.
   - Jika ditekan, muncul peringatan bahwa Anda berada di luar jangkauan wilayah keamanan RW 02 demi mencegah alarm palsu.
7. **Uji Simulasi Cepat**:
   - Klik tombol **🟢 Posko (< 300m)** untuk simulasi berada di posko (tombol aktif).
   - Klik tombol **🔴 Luar Wilayah (> 300m)** untuk simulasi berada di luar wilayah (tombol terkunci).
   - Klik tombol **📍 GPS Asli** untuk kembali ke koordinat sensor perangkat Anda.

#### Menguji Penolakan Kentongan (Akun Belum Terverifikasi):

1. Login menggunakan **Demo 1-Klik: Warga Belum Verifikasi** (akun `Doni Belum Verifikasi`).
2. Coba aktifkan tombol panic — sistem akan menolak dengan pesan: *"Aktivasi Kentongan Online ditolak: NIK Anda belum terverifikasi oleh pengurus RW/RT."*

#### Mengirim Pengaduan Lapor Cepat:

1. Masukkan Judul Laporan (contoh: *Lampu PJU Padam di Lorong RT 02*).
2. Tulis kronologi atau keterangan singkat.
3. Lampirkan foto bukti (opsional, maks. 5 MB).
4. Klik **"Kirim Laporan ke Pengurus RT"**.

#### Mengisi Buku Tamu Digital 2x24 Jam:

1. Pilih Kewarganegaraan: **WNI** atau **WNA**.
2. **Jika WNI**: Isi NIK (16 digit). **Jika WNA**: Isi Nomor Paspor / Dokumen Imigrasi.
3. Isi Nama Tamu sesuai KTP/Paspor, No. WhatsApp aktif, Alamat Domisili Asal.
4. Tentukan warga yang dikunjungi, tujuan kunjungan, dan RT tujuan.
5. Isi tanggal tiba dan perkiraan tanggal keluar.
6. Klik **"Daftarkan Tamu"**. Data otomatis diteruskan ke Ketua RT untuk validasi.

---

### 2. Sisi Petugas Ronda Poskamling

Akses: **[http://localhost:8000/ronda](http://localhost:8000/ronda)**
*(Wajib login sebagai `petugas_ronda`, `rt`, `rw`, `bhabinkamtibmas`, atau `nakes_puskesmas`)*

1. Masuk melalui halaman login menggunakan akun ronda (atau gunakan **Demo 1-Klik: Petugas Ronda**).
2. Pada layar pemilihan peran, pilih **🛡️ Petugas Ronda**.
3. **Pemindaian QR Checkpoint**:
   - Klik tombol **"Buka Kamera Scan QR"**. Izinkan akses kamera browser Anda.
   - Arahkan kamera ke stiker QR Checkpoint di pos ronda atau titik rawan (kode: `JW-CKP-001-POSRW`, `JW-CKP-002-GAPURA`, `JW-CKP-003-TAMAN`, `JW-CKP-004-GARDU`).
   - Kamera otomatis mendeteksi kode QR, mencatat koordinat GPS fisik petugas, dan memperbarui persentase progres patroli malam ini.
   - *Alternatif tanpa kamera fisik*: Klik tombol **"Verifikasi Hadir"** pada kartu checkpoint untuk simulasi presensi patroli.
4. **Pantau Progress Bar Patroli**: Lihat berapa persen checkpoint yang telah dikunjungi malam ini (misal: 2/4 titik = 50%).
5. **Riwayat Presensi**: Lihat log presensi malam ini dengan detail nama petugas, titik yang dipindai, waktu, dan catatan.
6. **Pencatatan Situasi Keamanan**:
   - Tulis catatan kondisi lingkungan pada formulir *Log Jaga Malam* dan klik kirim.

---

### 3. Sisi Command Center Pengurus RW

Akses: **[http://localhost:8000/dashboard](http://localhost:8000/dashboard)**
*(Wajib login sebagai `rw`, `rt`, `bhabinkamtibmas`, atau `nakes_puskesmas`)*

#### Memantau Peta Kerawanan (Heatmap Leaflet.js):

1. Perhatikan peta interaktif di bagian atas dashboard.
2. Gradien warna merah menandakan konsentrasi titik laporan insiden dan pemicuan tombol panic.
3. Klik penanda titik checkpoint atau posko untuk melihat detail nama titik, penanggung jawab RT, kode QR, dan tingkat kerawanan.
4. Lingkaran hijau transparan pada peta menggambarkan batas radius jangkauan siaga RW aktif.

#### Mengatur Titik Pusat & Radius Geofence Tombol Panic:

1. Gulir ke **Seksi Pengaturan Titik Pusat & Radius Geofence**.
2. Anda dapat menentukan koordinat pusat RW melalui 3 cara:
   - Mengetik angka Latitude & Longitude secara manual.
   - Mengklik tombol **"Deteksi Lokasi GPS Saya"** untuk mengambil posisi pengurus saat ini.
   - Menggeser marker pin ungu (*draggable*) langsung di preview peta mini.
3. Atur jarak radius aktif melalui **slider radius** (misal dari 300m diubah menjadi 500m atau 1000m).
4. Klik **"💾 Simpan Pengaturan Geofence"**.
5. Batas baru otomatis tersimpan di database dan langsung berlaku pada validasi tombol panic seluruh warga.

#### Mengelola Jadwal Ronda & WhatsApp Reminder:

1. Klik tombol **"+ Tambah Jadwal Ronda"** untuk menugaskan giliran warga pada hari tertentu dan RT tertentu.
2. Pada tabel jadwal ronda, klik tombol **"📱 Kirim WA"** di samping nama warga untuk mengirim pesan pengingat jadwal ronda H-1 langsung ke nomor WhatsApp warga bersangkutan.

#### Mengelola Titik Rawan Patroli (Checkpoint):

1. Klik **"+ Tambah Titik Rawan"** untuk menambahkan checkpoint baru.
2. Isi Nama Titik, Koordinat GPS, Deskripsi, Tingkat Kerawanan (Aman/Sedang/Rawan), RT, dan Urutan Patroli.
3. Kode QR unik akan dihasilkan otomatis.
4. Untuk mengedit: klik ikon ✏️ pada kartu checkpoint. Untuk menghapus: klik ikon 🗑️.

#### Mengelola Kamera CCTV:

1. Klik **"+ Tambah CCTV"** dan pilih tipe input:
   - **Link URL**: Masukkan URL stream CCTV (HLS `.m3u8`, YouTube, dsb.).
   - **Upload Video**: Unggah file video dari perangkat (MP4/WebM/OGG/MOV/MKV, maks. 50 MB).
2. Isi nama lokasi, RT, dan status (Aktif/Nonaktif).
3. Untuk mengedit atau menghapus: gunakan tombol aksi pada kartu CCTV.

---

### 4. Sisi Manajemen Pengguna (Admin)

Akses: **[http://localhost:8000/users](http://localhost:8000/users)**
*(Wajib login sebagai `rw`, `rt`, `bhabinkamtibmas`, atau `nakes_puskesmas`)*

#### Melihat Daftar Pengguna:

1. Gunakan tab filter peran di bagian atas: *Semua, Warga, Petugas Ronda, Ketua RT, Pengurus RW, Bhabinkamtibmas, Nakes Puskesmas*.
2. Gunakan kolom pencarian untuk mencari berdasarkan nama, email, NIK, nomor HP, alamat, atau nama ibu.
3. Statistik per peran ditampilkan dalam kartu ringkasan di bagian atas.

#### Menambah Pengguna Baru:

1. Klik tombol **"+ Tambah Pengguna"**.
2. Isi formulir: Nama, Email, NIK (16 digit), Password, No. HP, Nama Ibu Kandung, RT/RW, Alamat, No. Rumah.
3. Pilih satu atau lebih peran (multi-role) menggunakan checkbox.
4. Centang **"NIK Terverifikasi"** jika data sudah dikonfirmasi.
5. Klik **"Simpan"**.

#### Mengedit Pengguna:

1. Klik ikon ✏️ pada baris pengguna di tabel.
2. Perbarui data yang diinginkan. Password hanya akan diubah jika kolom password diisi ulang.
3. Klik **"Simpan Perubahan"**.

#### Menghapus Pengguna:

- **Hapus Individual**: Klik ikon 🗑️ pada baris pengguna. Anda tidak dapat menghapus akun yang sedang Anda gunakan sendiri.
- **Hapus Massal**: Centang beberapa pengguna menggunakan checkbox, kemudian klik **"Hapus Terpilih"**. Akun Anda sendiri akan otomatis dikecualikan.

#### Verifikasi NIK:

- Klik tombol toggle ✅/❌ pada kolom "Status NIK" untuk mengubah status verifikasi langsung.

#### Reset Perangkat:

- Klik tombol 📱 pada kolom aksi untuk mereset ikatan perangkat warga, sehingga warga dapat mendaftarkan perangkat baru.

#### Import Data Warga dari Excel:

1. Klik **"📥 Unduh Template Excel"** untuk mengunduh template `.xlsx` siap pakai.
2. Isi data warga di Excel sesuai format template (minimal: Nama Lengkap & NIK 16 digit).
3. Klik **"📤 Import dari Excel"** dan unggah file Excel yang telah diisi.
4. Sistem akan memproses file, menambahkan warga baru, dan memperbarui warga yang NIK-nya sudah ada.
5. Laporan hasil import ditampilkan (jumlah ditambahkan, diperbarui, dilewati, dan error per baris).

---

### 5. Sisi Permohonan Ganti Perangkat

#### Warga (Pengajuan):

1. Buka halaman **[http://localhost:8000/device-reset](http://localhost:8000/device-reset)**.
2. Isi NIK (16 digit), Nama Ibu Kandung (sebagai verifikasi identitas), Nomor HP/WhatsApp baru, dan Alasan pergantian.
3. Klik **"Kirim Permohonan"**.
4. Permohonan akan masuk ke antrean pengurus RW/RT untuk diverifikasi.

#### Pengurus (Persetujuan):

Akses: **[http://localhost:8000/device-requests](http://localhost:8000/device-requests)**

1. Lihat daftar permohonan dengan filter status: *Menunggu, Disetujui, Ditolak*.
2. Untuk menyetujui: Klik **"✅ Setujui"** — perangkat lama warga akan direset dan perangkat baru otomatis didaftarkan.
3. Untuk menolak: Klik **"❌ Tolak"** dan berikan catatan alasan penolakan.

---

### 6. Sisi Pengaturan Sistem

Akses: **[http://localhost:8000/settings](http://localhost:8000/settings)**
*(Wajib login sebagai `rw`, `rt`, `bhabinkamtibmas`, atau `nakes_puskesmas`)*

#### Konfigurasi GitHub PAT (Auto-Update Web):

1. Masukkan **Nama Repositori** GitHub (format: `owner/repo` atau URL lengkap).
2. Tentukan **Branch Target** (default: `main`).
3. Masukkan **GitHub Personal Access Token (PAT)** jika repositori bersifat private (token ditampilkan tersamar setelah disimpan).
4. Klik **"💾 Simpan Konfigurasi"**.

#### Mengecek Pembaruan:

1. Klik **"🔍 Cek Update"** — sistem akan membandingkan commit SHA lokal dengan commit terbaru di GitHub.
2. Jika ada pembaruan tersedia, informasi commit (SHA, pesan, author, tanggal) akan ditampilkan.

#### Menjalankan Auto-Update:

1. Klik **"🚀 Jalankan Update"** — sistem akan mengunduh kode terbaru dan menjalankan migrasi database otomatis.
2. Log terminal lengkap ditampilkan secara real-time.
3. Proses meliputi: `git pull` (atau fallback unduh ZIP), `php artisan optimize:clear`, dan `php artisan migrate --force`.

#### Menghubungkan Storage:

1. Klik **"🔗 Hubungkan Storage"** untuk membuat symlink `public/storage` → `storage/app/public`.
2. Status koneksi ditampilkan (terhubung/tidak).

---

## 🌐 Mekanisme Geofencing Radius (300 Meter)

Aplikasi menerapkan perlindungan **tiga lapis** (*three-layer validation*) pada Tombol Panic:

### Lapis 1: Verifikasi Identitas (Server)

- **Autentikasi Wajib**: Endpoint `POST /api/panic` menolak permintaan dari pengguna yang belum login (HTTP `401 Unauthorized`).
- **Verifikasi NIK**: Warga yang NIK-nya belum terverifikasi oleh pengurus ditolak (HTTP `403 Forbidden`) dengan pesan:
  > *"Aktivasi Kentongan Online ditolak: NIK Anda belum terverifikasi oleh pengurus RW/RT."*

### Lapis 2: Validasi Geofencing Klien (JavaScript di Browser Ponsel)

- Menghitung jarak realtime dari koordinat sensor GPS ponsel pelapor ke titik pusat RW (`center_latitude`, `center_longitude`) menggunakan rumus **Haversine Great-Circle Distance**:

$$\Delta\sigma = 2 \arcsin\left(\sqrt{\sin^2\left(\frac{\Delta\phi}{2}\right) + \cos(\phi_1)\cos(\phi_2)\sin^2\left(\frac{\Delta\lambda}{2}\right)}\right)$$

$$d = R \cdot \Delta\sigma \quad (\text{dengan } R = 6.371.000\text{ meter})$$

- Jika jarak $> 300$ meter, tombol darurat seketika dikunci dan menampilkan pesan:
  > *"🚫 DI LUAR JANGKAUAN (Jarak: X meter > Batas: 300m)"*

### Lapis 3: Validasi Geofencing Server (PHP Laravel Controller)

- Endpoint `POST /api/panic` memvalidasi kembali koordinat yang dikirim.
- Wajibkan izin lokasi perangkat aktif (koordinat latitude/longitude tidak boleh kosong).
- Jika jarak pelapor melebihi radius konfigurasi basis data (`panic_radius_meters`), server mengembalikan status HTTP `422 Unprocessable Content`:
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

## 🔒 Mekanisme Keamanan Perangkat (1 Akun = 1 HP)

Untuk mencegah penyalahgunaan akun warga (misalnya: satu akun digunakan oleh banyak orang di perangkat berbeda untuk membuat alarm palsu), JagaWarga menerapkan sistem pengikatan perangkat:

### Alur Pendaftaran Perangkat

1. **Login**: Warga masuk menggunakan email/NIK dan password.
2. **Pilih Peran "Warga"**: Saat memilih peran Warga di layar pemilihan peran:
   - **Perangkat Pertama**: Jika belum ada perangkat terdaftar, perangkat aktif saat ini otomatis didaftarkan sebagai perangkat resmi (device ID disimpan melalui cookie browser yang bertahan 1 tahun).
   - **Perangkat Berbeda**: Jika sudah ada perangkat terdaftar dan cookie device ID tidak cocok, akses **ditolak** dengan pesan:
     > *"Akun Warga Anda sudah terdaftar di perangkat lain. Kebijakan keamanan membatasi 1 akun warga hanya boleh digunakan pada 1 perangkat terdaftar."*

### Alur Pergantian Perangkat

1. Warga mengunjungi `/device-reset` dan mengisi formulir pengajuan (NIK, Nama Ibu Kandung, No. HP Baru, Alasan).
2. Permohonan masuk ke antrean pengurus di `/device-requests`.
3. Pengurus memverifikasi identitas dan menyetujui/menolak permohonan.
4. Jika disetujui, perangkat lama direset dan perangkat baru otomatis terikat.

### Perlindungan Middleware

- Middleware `EnsureUserRole` memeriksa konsistensi perangkat pada setiap request:
  - Jika peran aktif adalah `warga` dan device ID cookie tidak cocok dengan perangkat terdaftar, sesi dibatalkan dan pengguna diarahkan kembali ke layar pemilihan peran.

---

## 📁 Struktur Direktori Proyek

```plaintext
jagawarga/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php           # Login, Demo 1-Klik, Select Role, Switch Role, Device Reset, & Logout
│   │   │   ├── DashboardRwController.php    # Command Center, Geofence, Jadwal, Checkpoint CRUD, CCTV CRUD
│   │   │   ├── DeviceResetController.php    # Permohonan Ganti Perangkat (Approve/Reject/Reset)
│   │   │   ├── JagaWargaController.php      # Beranda & Fallback Demo Portal
│   │   │   ├── PanicAlertController.php     # Riwayat Kentongan, SSE Stream, Edit & Hapus Alert
│   │   │   ├── PwaController.php            # Registrasi Perangkat PWA & Statistik
│   │   │   ├── RondaController.php          # Scan QR Checkpoint & Presensi Patroli
│   │   │   ├── SettingController.php        # Pengaturan Sistem, GitHub PAT, Auto-Update, Symlink
│   │   │   ├── UserController.php           # Manajemen Pengguna CRUD, Import Excel, Bulk Delete, NIK Toggle
│   │   │   └── WargaController.php          # Modul Warga, Kentongan Geofence, Lapor, & Buku Tamu WNI/WNA
│   │   └── Middleware/
│   │       └── EnsureUserRole.php           # RBAC Middleware: Peran Aktif, Validasi Perangkat, Pengalihan Cerdas
│   └── Models/
│       ├── BukuTamu.php                     # Model Tamu Wajib Lapor 2x24 Jam (WNI & WNA)
│       ├── CctvLingkungan.php               # Model CCTV Lingkungan (Link & Upload Video)
│       ├── Checkpoint.php                   # Model Titik QR Patroli Ronda (dengan Tingkat Kerawanan)
│       ├── DeviceResetRequest.php           # Model Permohonan Ganti Perangkat Warga
│       ├── JadwalRonda.php                  # Model Jadwal Giliran Ronda Warga
│       ├── LaporanKejadian.php              # Model Pengaduan & Laporan Kejadian Warga
│       ├── PanicAlert.php                   # Model Sinyal Bahaya Kentongan Online
│       ├── Pengaturan.php                   # Model Pengaturan Sistem (GitHub PAT, dsb.)
│       ├── PresensiRonda.php                # Model Presensi GPS Patroli Checkpoint
│       ├── PwaDevice.php                    # Model Perangkat PWA Terdaftar
│       ├── Role.php                         # Model Peran Pengguna (Multi-Role)
│       ├── RwSetting.php                    # Model Konfigurasi Koordinat & Radius Geofence
│       ├── TitikQr.php                      # Model Titik QR (Legacy)
│       └── User.php                         # Model Pengguna: Multi-Role, Device Binding, NIK Verification
├── database/
│   ├── migrations/                          # 25 File Migrasi Skema Basis Data
│   └── seeders/
│       └── DatabaseSeeder.php               # Seeder: 6 Roles, 10 Akun Multi-Role, Checkpoints, CCTV, Jadwal, & Geofence
├── public/
│   ├── manifest.webmanifest                 # Manifest PWA (Nama, Ikon, Tema)
│   ├── sw.js                                # Service Worker Caching & Offline
│   ├── offline.html                         # Tampilan Cadangan Saat Internet Terputus
│   └── favicon.svg                          # Ikon Perisai JagaWarga RW
├── resources/
│   └── views/
│       ├── auth/
│       │   ├── login.blade.php              # Halaman Login & Kartu Demo 1-Klik (7 Peran)
│       │   ├── select-role.blade.php         # Layar Pemilihan Peran Aktif (Multi-Role)
│       │   └── device-reset.blade.php        # Formulir Pengajuan Ganti Perangkat Warga
│       ├── dashboard/
│       │   ├── index.blade.php              # Command Center, Heatmap, Geofence, Jadwal, CCTV, Checkpoint
│       │   └── device_requests.blade.php     # Panel Permohonan Ganti Perangkat (Admin)
│       ├── layouts/
│       │   └── app.blade.php                # Master Layout PWA, Navigasi, Switch Role, & Audio Kentongan
│       ├── ronda/
│       │   └── index.blade.php              # Modul Ronda, Kamera QR html5-qrcode, Presensi, & Progress
│       ├── settings/
│       │   └── index.blade.php              # Pengaturan Sistem, GitHub PAT, Auto-Update, & Symlink
│       ├── users/
│       │   └── index.blade.php              # Manajemen Pengguna CRUD, Import Excel, Bulk Delete
│       ├── warga/
│       │   └── index.blade.php              # Portal Warga, Kentongan 3-Lapis, Lapor, & Buku Tamu WNI/WNA
│       └── welcome.blade.php                # Beranda Interaktif JagaWarga RW (PWA)
└── routes/
    └── web.php                              # Definisi Rute: Publik, Autentikasi, Terproteksi, & API
```

---

## 📄 Lisensi & Hak Cipta

Aplikasi web **JagaWarga RW** dikembangkan sebagai solusi digital integrasi keamanan lingkungan warga dan pos kamling. Proyek ini dilisensikan di bawah lisensi terbuka [MIT License](https://opensource.org/licenses/MIT).

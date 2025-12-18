# Sistem Manajemen Perpustakaan Simbad

Sebuah website perpustakaan modern dengan fitur lengkap, desain responsif (Tema Terang), dan menggunakan Bahasa Indonesia.

## Fitur Utama
- **Autentikasi**: Masuk & Daftar (Admin & Anggota).
- **Dasbor**: Statistik real-time buku dan peminjaman.
- **Manajemen Buku**:
    - **Admin**: Tambah, Edit, Hapus Buku.
    - **Anggota**: Cari Buku dan **Pinjam** Buku.
- **Peminjaman**:
    - Sistem stok otomatis berkurang saat dipinjam.
    - Admin dapat memproses pengembalian buku.
- **Desain**: Tampilan "Light Mode" yang bersih dan premium.

## Panduan Instalasi

1.  **Database**:
    - Buat database `simbad_db` di phpMyAdmin.
    - Import file `database/database.sql` (struktur tabel baru: `buku`, `pengguna`, `kategori`, `peminjaman`).

2.  **Konfigurasi**:
    - Cek `config/config.php` jika ada perubahan akses database.

3.  **Akses Web**:
    - Buka `http://localhost/Simbad/`
    - **Akun Admin**:
        - Username: `admin`
        - Password: `password123`
    - **Akun Anggota**:
        - Register sendiri atau gunakan: `budi` / `password123`

## Struktur File
- `assets/`: CSS (Tema Terang) dan Gambar.
- `auth/`: Logika Masuk/Daftar (Bahasa Indonesia).
- `config/`: Koneksi Database.
- `database/`: File SQL.
- `includes/`: Sidebar.
- `uploads/`: Gambar Sampul Buku.

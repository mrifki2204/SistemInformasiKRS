# 📚 Sistem Informasi KRS (Kartu Rencana Studi)

Sistem Informasi KRS adalah aplikasi web untuk mengelola kartu rencana studi mahasiswa secara efektif dan efisien. Aplikasi ini dibangun menggunakan PHP dan MySQL dengan antarmuka yang modern dan responsif.

## ✨ Fitur

- 📊 Dashboard dengan statistik total mahasiswa dan rata-rata IPK
- 👥 Manajemen data mahasiswa (tambah, edit, hapus)
- 📝 Input dan pengelolaan KRS mahasiswa
- 🔍 Pencarian mahasiswa real-time
- 📱 Antarmuka responsif untuk desktop dan mobile
- ⚡ Validasi data secara real-time
- 🎯 Perhitungan otomatis SKS maksimal berdasarkan IPK

## 💻 Teknologi yang Digunakan

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5.3
- Font Awesome 6.0
- SweetAlert2
- JavaScript (Vanilla)

## 🚀 Fitur Utama

### Manajemen Mahasiswa
- Input data mahasiswa baru dengan validasi NIM
- Perhitungan otomatis SKS maksimal (24 SKS untuk IPK ≥ 3.0, 20 SKS untuk IPK < 3.0)
- Tampilan data dalam format tabel yang mudah dibaca
- Fitur pencarian real-time

### Pengelolaan KRS
- Input mata kuliah untuk KRS
- Validasi jumlah SKS berdasarkan IPK
- Lihat riwayat KRS mahasiswa
- Edit dan hapus mata kuliah

### Antarmuka Pengguna
- Dashboard informatif dengan statistik
- Loading indicator untuk feedback visual
- Notifikasi toast untuk update status
- Modal konfirmasi untuk aksi penting
- Responsive design untuk semua ukuran layar

## 🛠️ Instalasi

1. Clone repository ini
```bash
git clone https://github.com/username/SistemInformasiKRS.git
```

2. Import database
```sql
krs.sql
```

3. Konfigurasi koneksi database
- Buka file `koneksi.php`
- Sesuaikan parameter koneksi:
```php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'your_database';
```

4. Jalankan di web server
- Pastikan web server (Apache/Nginx) sudah berjalan
- Akses melalui browser: `http://localhost/SistemInformasiKRS`

## 📝 Penggunaan

1. Login ke sistem
2. Pada dashboard, anda dapat melihat statistik umum
3. Untuk menambah mahasiswa:
   - Klik tombol "Tambah Mahasiswa"
   - Isi form dengan data yang valid
   - Sistem akan memvalidasi format NIM dan IPK
4. Untuk mengelola KRS:
   - Pilih mahasiswa dari daftar
   - Klik tombol "Input KRS" atau "Lihat KRS"
   - Tambah atau edit mata kuliah sesuai kebutuhan

## 🔒 Keamanan

- Validasi input untuk mencegah SQL Injection
- Sanitasi data sebelum ditampilkan
- Konfirmasi untuk aksi penting (hapus data)
- Pengecekan hak akses

## 🤝 Kontribusi

Kontribusi dan saran sangat diterima. Untuk perubahan besar, silakan buka issue terlebih dahulu untuk mendiskusikan perubahan yang diinginkan.

## 👨‍💻 Author

Muhammad Rifki Kurniawan

## 📧 Kontak

Jika ada pertanyaan atau masukan, silakan hubungi melalui:
- Email: mrifki2204@gmail.com

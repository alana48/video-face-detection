# Video Face Detection

Proyek ini adalah aplikasi yang menggunakan **CodeIgniter 3** dan **face-api.js** untuk mendeteksi wajah pada materi video. Program ini merupakan bagian dari Learning Management System (LMS) yang memungkinkan video hanya dapat diputar jika wajah terdeteksi di depan kamera. Jika tidak ada wajah yang terdeteksi atau wajah yang terdaftar, video akan berhenti.

## Fitur Utama

- **Pendeteksian Wajah:** Video akan diputar hanya jika wajah terdeteksi di depan kamera.
- **Registrasi Wajah:** Pengguna dapat mendaftarkan wajah mereka ke dalam sistem.
- **Pencocokan Wajah:** Sistem akan mengenali siapa yang berada di depan kamera.
- **Kontrol Video:** Video tidak dapat dijeda, dipercepat, atau dilewati jika wajah tidak terdeteksi atau wajah yang terdaftar tidak dikenali.

## Menu Sistem

1. **Regis Face:** Menu untuk mendaftarkan wajah baru ke dalam sistem.
2. **List Face:** Menampilkan daftar wajah yang sudah terdaftar.
3. **Match Face:** Menu untuk mencocokkan wajah yang terdeteksi dengan wajah yang sudah terdaftar.
4. **Video Detection:** Halaman utama yang menampilkan video yang hanya akan diputar jika wajah terdeteksi.

## Teknologi yang Digunakan

- **CodeIgniter 3:** Framework PHP untuk backend.
- **face-api.js:** Library JavaScript untuk mendeteksi dan mengenali wajah menggunakan deep learning di browser.
- **JavaScript, HTML, CSS:** Untuk antarmuka pengguna (frontend).

## Persyaratan Sistem

Sebelum memulai, pastikan Anda sudah menginstal perangkat lunak berikut:

- PHP (versi 7.4 atau lebih tinggi)
- Composer
- Web server seperti Apache atau Nginx
- MySQL atau MariaDB (opsional jika menggunakan database)
- Node.js (untuk menjalankan face-api.js)

## Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/alana48/video-face-detection.git
```

### 2. Konfigurasi Database
Jika Anda menggunakan database untuk menyimpan data wajah, buatlah database baru dan sesuaikan pengaturan di application/config/database.php sesuai dengan kredensial Anda.

### 3. Menjalankan Aplikasi
Pastikan server web Anda telah berjalan. Jika menggunakan Apache, pastikan mod_rewrite diaktifkan. Akses aplikasi Anda melalui browser di http://localhost/ atau alamat sesuai konfigurasi.

## Penggunaan
### 1. Regis Face
Untuk mendaftarkan wajah, buka halaman "Regis Face" di aplikasi. Sistem akan menggunakan kamera untuk menangkap wajah pengguna, yang kemudian disimpan ke dalam database.

### 2. List Face
Pada menu ini, Anda dapat melihat daftar wajah yang terdaftar di sistem.

### 3. Match Face
Menu ini digunakan untuk mencocokkan wajah yang terdeteksi dengan data wajah yang terdaftar. Jika wajah terdeteksi dan cocok dengan salah satu data, video akan diputar.

### 4. Video Detection
Halaman utama di mana video hanya dapat diputar jika wajah terdeteksi di depan kamera. Video tidak bisa dipercepat, dijeda, atau dilewati kecuali wajah yang terdaftar berada di depan kamera.

## Kontribusi
Kami sangat menghargai kontribusi dari komunitas! Jika Anda ingin berkontribusi, silakan ikuti langkah-langkah berikut:

#### 1. Fork repo ini.

#### 2. Buat cabang (branch) untuk fitur atau perbaikan yang Anda buat.

#### 3. Lakukan perubahan dan pastikan semuanya bekerja dengan baik.

#### 4. Buat pull request untuk menggabungkan perubahan Anda ke cabang utama.

## Lisensi
Proyek ini dilisensikan di bawah MIT License.
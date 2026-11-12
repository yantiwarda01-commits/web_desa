# 🏛️ Website Desa Sejahtera

Website resmi desa berbasis **PHP Native + MySQL** dengan fitur pengajuan surat online dan panel admin lengkap.

---

## 📋 Fitur Utama

### 🌐 **Landing Page (Public)**
- **Header/Navbar** responsif dengan navigasi lengkap
- **Hero Section** dengan statistik desa (jumlah penduduk, RT/RW, dll)
- **Profil Desa** (Sejarah, Visi & Misi, Geografis, Demografi)
- **Perangkat Desa** (Kepala Desa, Sekretaris, Kaur, dll)
- **Berita & Pengumuman** terbaru dari desa
- **Layanan Administrasi** (jenis-jenis surat yang tersedia)
- **Form Pengajuan Surat Online** untuk warga
- **Kontak & Lokasi** kantor desa
- **Footer** dengan tautan navigasi dan social media

### ✍️ **Pengajuan Surat Online**
Warga bisa mengajukan berbagai jenis surat secara online:
- Surat Keterangan Domisili
- Surat Pengantar KTP/KK
- Surat Keterangan Tidak Mampu
- Surat Keterangan Usaha
- Surat Pengantar SKCK
- Surat Keterangan Ahli Waris
- Surat Keterangan Pindah
- Surat Izin Keramaian
- Dan lainnya

**Fitur Pengajuan:**
- Form lengkap dengan validasi
- Upload berkas pendukung (opsional)
- Nomor pengajuan otomatis
- Status tracking (Pending, Diproses, Selesai, Ditolak)

### 🔐 **Panel Admin**
Dashboard lengkap untuk mengelola website desa:

**1. Dashboard**
- Statistik pengajuan surat (Pending, Diproses, Selesai)
- Total berita published
- Daftar pengajuan terbaru
- Daftar berita terbaru

**2. Kelola Pengajuan Surat**
- Lihat semua pengajuan dengan filter & search
- Detail pengajuan lengkap
- Update status pengajuan
- Tambah catatan admin
- Hapus pengajuan

**3. Kelola Berita & Pengumuman** (placeholder)
- Tambah/Edit/Hapus berita
- Kategori berita (Program Desa, Prestasi, Pendidikan, dll)
- Status publish/draft
- Upload gambar berita

**4. Kelola Profil Desa** (placeholder)
- Update data desa
- Edit visi & misi
- Edit struktur perangkat desa
- Update kontak informasi

---

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP Native (Procedural + Functions)
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Icons:** Font Awesome 6.5
- **Fonts:** Google Fonts (Poppins)
- **Styling:** Custom CSS (Responsive Design)

---

## 📦 Instalasi

### 1️⃣ **Persiapan Lingkungan**

Pastikan sistem Anda sudah terinstall:
- **XAMPP / WAMP / MAMP** (PHP 7.4+, MySQL 5.7+, Apache)
- **Web Browser** (Chrome, Firefox, Edge)

### 2️⃣ **Import Database**

1. Buka **phpMyAdmin** (http://localhost/phpmyadmin)
2. Buat database baru atau jalankan perintah SQL:
   ```sql
   CREATE DATABASE db_desa_sejahtera;
   ```
3. Import file `database.sql`:
   - Klik database `db_desa_sejahtera`
   - Tab **Import** > **Choose File** > Pilih file `database.sql`
   - Klik **Go**

**✅ Database sudah terisi dengan:**
- Tabel lengkap (admin, profil_desa, berita, pengajuan_surat, dll)
- Data dummy (seed data) dalam Bahasa Indonesia
- 2 akun admin
- 6 berita contoh
- 5 pengajuan surat contoh
- Struktur perangkat desa

### 3️⃣ **Konfigurasi Database**

Edit file `config/database.php` sesuaikan dengan konfigurasi MySQL Anda:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Kosongkan jika tidak pakai password
define('DB_NAME', 'db_desa_sejahtera');
```

### 4️⃣ **Jalankan Aplikasi**

1. Copy folder `Web Desa` ke dalam `htdocs` (XAMPP) atau `www` (WAMP)
2. Start Apache & MySQL dari XAMPP/WAMP Control Panel
3. Buka browser dan akses:

**🌐 Landing Page:**
```
http://localhost/Web%20Desa/index-landing.php
```

**🔐 Admin Login:**
```
http://localhost/Web%20Desa/admin/login.php
```

**Akun Admin Default:**
- **Username:** `admin`
- **Password:** `admin123`

**Akun Operator:**
- **Username:** `operator`
- **Password:** `admin123`

> ⚠️ **PENTING:** Ganti password default setelah login pertama kali!

---

## 📁 Struktur Folder

```
Web Desa/
│
├── admin/                      # Panel Admin
│   ├── includes/               # Header & Footer admin
│   │   ├── header.php
│   │   └── footer.php
│   ├── index.php               # Dashboard admin
│   ├── login.php               # Login admin
│   ├── logout.php              # Logout admin
│   ├── pengajuan.php           # Kelola pengajuan surat
│   ├── berita.php              # Kelola berita (placeholder)
│   └── profil.php              # Kelola profil desa (placeholder)
│
├── assets/                     # Asset statis
│   ├── css/
│   │   └── admin.css           # Stylesheet admin panel
│   ├── js/
│   │   └── admin.js            # JavaScript admin panel
│   └── uploads/                # Folder upload file
│
├── config/
│   └── database.php            # Konfigurasi koneksi database
│
├── includes/
│   ├── functions.php           # Fungsi helper umum
│   └── session.php             # Session management
│
├── pages/                      # Halaman publik tambahan
│   ├── berita-detail.php       # Detail berita (placeholder)
│   └── profil.php              # Halaman profil desa (placeholder)
│
├── index.html                  # Landing page HTML statis (contoh)
├── index-landing.php           # Landing page PHP dinamis
├── pengajuan.php               # Form pengajuan surat online
├── style.css                   # Stylesheet landing page
├── script.js                   # JavaScript landing page
├── database.sql                # File SQL schema + seed data
└── README.md                   # Dokumentasi ini
```

---

## 🎯 Cara Menggunakan

### **Untuk Warga (Public)**

1. Buka `index-landing.php`
2. Lihat informasi desa, berita, dan profil perangkat
3. Klik **"Ajukan Surat"** atau menu **Layanan**
4. Isi formulir pengajuan dengan lengkap
5. Submit pengajuan
6. Catat **Nomor Pengajuan** untuk tracking

### **Untuk Admin**

1. Login ke admin panel (`admin/login.php`)
2. Di **Dashboard** lihat statistik dan pengajuan terbaru
3. Masuk ke menu **Pengajuan Surat**
4. Filter/search pengajuan yang ingin dikelola
5. Klik tombol **mata** untuk melihat detail
6. Update status pengajuan:
   - **Pending** → **Diproses** → **Selesai**
   - Atau **Ditolak** jika tidak memenuhi syarat
7. Tambahkan catatan admin jika diperlukan
8. Klik **Update Status**

---

## 🔒 Keamanan

- ✅ **Prepared Statements** untuk mencegah SQL Injection
- ✅ **Password Hashing** dengan bcrypt
- ✅ **Sanitize Input** pada semua form
- ✅ **Session Management** dengan timeout otomatis
- ✅ **CSRF Protection** ready (bisa ditambahkan token)
- ✅ **XSS Prevention** dengan htmlspecialchars

---

## 🚀 Pengembangan Lebih Lanjut

Beberapa fitur yang bisa dikembangkan:

### **Admin Panel:**
- ✏️ CRUD lengkap untuk Berita & Pengumuman
- ✏️ CRUD untuk Profil Desa & Perangkat Desa
- 📊 Export pengajuan ke Excel/PDF
- 📧 Notifikasi email ke warga saat status berubah
- 📱 WhatsApp notification (integrasi API)
- 📷 Upload gambar galeri kegiatan desa
- 👥 Manajemen user admin (tambah/edit/hapus admin)
- 🔐 Ganti password admin

### **Public:**
- 🔍 Cek status pengajuan online (tracking dengan nomor)
- 💬 Form kontak kirim pesan langsung
- 🗺️ Integrasi Google Maps untuk lokasi kantor
- 📱 Progressive Web App (PWA) untuk mobile
- 🌙 Dark mode toggle
- 🔔 Notifikasi browser untuk pengumuman penting

---

## 📞 Support & Kontak

Jika ada pertanyaan atau butuh bantuan:

- **Developer:** Tim IT Desa Sejahtera
- **Email:** developer@desasejahtera.id
- **Website:** https://desasejahtera.id

---

## 📄 Lisensi

Project ini bebas digunakan untuk keperluan **non-komersial** dan **edukasi**.  
Untuk penggunaan komersial, silakan hubungi developer.

---

## 🙏 Terima Kasih

Terima kasih telah menggunakan **Website Desa Sejahtera**!  
Semoga aplikasi ini bermanfaat untuk meningkatkan pelayanan publik di desa Anda.

**Dibuat dengan ❤️ untuk kemajuan desa di Indonesia**

---

## 📝 Changelog

**Version 1.0.0** (November 2026)
- ✅ Landing page dinamis dengan PHP
- ✅ Form pengajuan surat online
- ✅ Admin panel dengan dashboard
- ✅ Kelola pengajuan surat (view, update status, delete)
- ✅ Database schema lengkap dengan seed data
- ✅ Responsive design (mobile, tablet, desktop)
- ✅ Security implemented (prepared statements, password hashing)

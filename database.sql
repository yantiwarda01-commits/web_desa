-- ========================================
-- DATABASE SCHEMA: WEBSITE DESA SEJAHTERA
-- ========================================

DROP DATABASE IF EXISTS db_desa_sejahtera;
CREATE DATABASE db_desa_sejahtera CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_desa_sejahtera;

-- ========================================
-- TABEL: admin
-- ========================================
CREATE TABLE admin (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  level ENUM('super_admin', 'admin') DEFAULT 'admin',
  terakhir_login DATETIME,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ========================================
-- TABEL: profil_desa
-- ========================================
CREATE TABLE profil_desa (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nama_desa VARCHAR(100) NOT NULL,
  kecamatan VARCHAR(100),
  kabupaten VARCHAR(100),
  provinsi VARCHAR(100),
  kode_pos VARCHAR(10),
  alamat_kantor TEXT,
  telepon VARCHAR(20),
  email VARCHAR(100),
  website VARCHAR(100),
  visi TEXT,
  misi TEXT,
  sejarah TEXT,
  luas_wilayah VARCHAR(50),
  jumlah_penduduk INT,
  jumlah_kk INT,
  jumlah_rt INT,
  jumlah_rw INT,
  logo VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ========================================
-- TABEL: perangkat_desa
-- ========================================
CREATE TABLE perangkat_desa (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nip VARCHAR(30),
  nama_lengkap VARCHAR(100) NOT NULL,
  jabatan VARCHAR(100) NOT NULL,
  pendidikan VARCHAR(50),
  alamat TEXT,
  telepon VARCHAR(20),
  email VARCHAR(100),
  foto VARCHAR(255),
  urutan INT DEFAULT 0,
  status ENUM('aktif', 'non_aktif') DEFAULT 'aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ========================================
-- TABEL: berita
-- ========================================
CREATE TABLE berita (
  id INT PRIMARY KEY AUTO_INCREMENT,
  judul VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  kategori ENUM('program_desa', 'prestasi', 'pendidikan', 'infrastruktur', 'kesehatan', 'umum') DEFAULT 'umum',
  konten TEXT NOT NULL,
  ringkasan VARCHAR(500),
  gambar VARCHAR(255),
  penulis VARCHAR(100),
  views INT DEFAULT 0,
  status ENUM('draft', 'published') DEFAULT 'published',
  tanggal_publish DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ========================================
-- TABEL: pengumuman
-- ========================================
CREATE TABLE pengumuman (
  id INT PRIMARY KEY AUTO_INCREMENT,
  judul VARCHAR(255) NOT NULL,
  konten TEXT NOT NULL,
  jenis ENUM('penting', 'biasa', 'info') DEFAULT 'biasa',
  tanggal_mulai DATE,
  tanggal_selesai DATE,
  status ENUM('aktif', 'non_aktif') DEFAULT 'aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ========================================
-- TABEL: pengajuan_surat
-- ========================================
CREATE TABLE pengajuan_surat (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nomor_pengajuan VARCHAR(50) UNIQUE NOT NULL,
  jenis_surat ENUM(
    'surat_domisili',
    'surat_pengantar_ktp',
    'surat_pengantar_kk',
    'surat_tidak_mampu',
    'surat_kelahiran',
    'surat_keterangan_usaha',
    'surat_pengantar_skck',
    'surat_keterangan_ahli_waris',
    'surat_keterangan_pindah',
    'surat_izin_keramaian',
    'lainnya'
  ) NOT NULL,
  nik VARCHAR(16) NOT NULL,
  nama_pemohon VARCHAR(100) NOT NULL,
  tempat_lahir VARCHAR(100),
  tanggal_lahir DATE,
  jenis_kelamin ENUM('L', 'P'),
  pekerjaan VARCHAR(100),
  alamat TEXT,
  no_telepon VARCHAR(20),
  email VARCHAR(100),
  keperluan TEXT,
  keterangan_tambahan TEXT,
  berkas_pendukung VARCHAR(255),
  status ENUM('pending', 'diproses', 'selesai', 'ditolak') DEFAULT 'pending',
  catatan_admin TEXT,
  tanggal_selesai DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ========================================
-- TABEL: galeri
-- ========================================
CREATE TABLE galeri (
  id INT PRIMARY KEY AUTO_INCREMENT,
  judul VARCHAR(255) NOT NULL,
  deskripsi TEXT,
  kategori VARCHAR(50),
  gambar VARCHAR(255) NOT NULL,
  tanggal_upload DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ========================================
-- INDEXES
-- ========================================
CREATE INDEX idx_berita_slug ON berita(slug);
CREATE INDEX idx_berita_kategori ON berita(kategori);
CREATE INDEX idx_berita_status ON berita(status);
CREATE INDEX idx_pengajuan_status ON pengajuan_surat(status);
CREATE INDEX idx_pengajuan_nik ON pengajuan_surat(nik);
CREATE INDEX idx_pengumuman_status ON pengumuman(status);
CREATE INDEX idx_perangkat_status ON perangkat_desa(status);

-- ========================================
-- SEED DATA: admin
-- Password: admin123 (hashed dengan bcrypt)
-- ========================================
INSERT INTO admin (username, password, nama_lengkap, email, level) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Desa', 'admin@desasejahtera.id', 'super_admin'),
('operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operator Desa', 'operator@desasejahtera.id', 'admin');

-- ========================================
-- SEED DATA: profil_desa
-- ========================================
INSERT INTO profil_desa (
  nama_desa, kecamatan, kabupaten, provinsi, kode_pos,
  alamat_kantor, telepon, email, website,
  visi, misi, sejarah,
  luas_wilayah, jumlah_penduduk, jumlah_kk, jumlah_rt, jumlah_rw
) VALUES (
  'Desa Sejahtera',
  'Maju Jaya',
  'Kabupaten Contoh',
  'Jawa Tengah',
  '12345',
  'Jl. Raya Desa Sejahtera No. 1, Kec. Maju Jaya, Kab. Contoh',
  '+62 812-3456-7890',
  'info@desasejahtera.id',
  'https://desasejahtera.id',
  'Mewujudkan Desa Sejahtera yang Mandiri, Berbudaya, dan Berdaya Saing Menuju Masyarakat yang Adil dan Makmur',
  'Meningkatkan kualitas pelayanan publik kepada masyarakat; Mengembangkan ekonomi desa berbasis potensi lokal; Memperkuat nilai gotong royong dan kebersamaan; Meningkatkan infrastruktur dan sarana prasarana desa; Memberdayakan masyarakat melalui pendidikan dan pelatihan',
  'Desa Sejahtera didirikan pada tanggal 17 Agustus 1945 oleh para pejuang lokal yang berkomitmen untuk membangun komunitas yang mandiri dan gotong royong. Awalnya desa ini merupakan wilayah pertanian yang subur dengan mayoritas penduduk bekerja sebagai petani. Seiring perkembangan zaman, Desa Sejahtera terus bertransformasi menjadi desa yang modern namun tetap mempertahankan nilai-nilai budaya dan kearifan lokal.',
  '450 Ha',
  4820,
  1250,
  12,
  4
);

-- ========================================
-- SEED DATA: perangkat_desa
-- ========================================
INSERT INTO perangkat_desa (nip, nama_lengkap, jabatan, pendidikan, alamat, telepon, email, urutan, status) VALUES
('196701011990031001', 'Bapak Ahmad Santoso, S.IP', 'Kepala Desa', 'S1 Ilmu Pemerintahan', 'Dusun Makmur RT 01 RW 01', '081234567890', 'kades@desasejahtera.id', 1, 'aktif'),
('197205151995031002', 'Ibu Siti Aminah, S.Sos', 'Sekretaris Desa', 'S1 Sosiologi', 'Dusun Makmur RT 02 RW 01', '081234567891', 'sekdes@desasejahtera.id', 2, 'aktif'),
('197808102000031003', 'Bapak Budi Hartono, SE', 'Kaur Keuangan', 'S1 Ekonomi', 'Dusun Sejahtera RT 01 RW 02', '081234567892', 'keuangan@desasejahtera.id', 3, 'aktif'),
('198103202005031004', 'Ibu Dewi Lestari, S.Pd', 'Kaur Umum', 'S1 Pendidikan', 'Dusun Sejahtera RT 03 RW 02', '081234567893', 'umum@desasejahtera.id', 4, 'aktif'),
('198506152010031005', 'Bapak Eko Prasetyo, S.Kom', 'Kaur Perencanaan', 'S1 Komputer', 'Dusun Maju RT 01 RW 03', '081234567894', 'perencanaan@desasejahtera.id', 5, 'aktif'),
('198209102012031006', 'Bapak Hendra Gunawan', 'Kepala Dusun Makmur', 'SMA', 'Dusun Makmur RT 03 RW 01', '081234567895', NULL, 6, 'aktif'),
('198705222015031007', 'Bapak Joko Susilo', 'Kepala Dusun Sejahtera', 'SMA', 'Dusun Sejahtera RT 02 RW 02', '081234567896', NULL, 7, 'aktif'),
('199001152017031008', 'Bapak Rizal Firmansyah', 'Kepala Dusun Maju', 'SMA', 'Dusun Maju RT 02 RW 03', '081234567897', NULL, 8, 'aktif');

-- ========================================
-- SEED DATA: berita
-- ========================================
INSERT INTO berita (judul, slug, kategori, konten, ringkasan, penulis, views, status, tanggal_publish) VALUES
(
  'Peluncuran Program Pertanian Organik Desa Sejahtera Tahun 2026',
  'peluncuran-program-pertanian-organik-2026',
  'program_desa',
  '<p>Desa Sejahtera secara resmi meluncurkan program pertanian organik perdana yang melibatkan 120 petani lokal dengan dukungan penuh dari Dinas Pertanian Kabupaten Contoh.</p><p>Program ini bertujuan untuk meningkatkan produktivitas pertanian sekaligus menjaga kelestarian lingkungan. Para petani akan mendapatkan pelatihan intensif, bantuan bibit unggul, dan pendampingan teknis dari para ahli pertanian organik.</p><p>"Kami optimis program ini akan membawa dampak positif bagi perekonomian warga sekaligus menjadikan Desa Sejahtera sebagai sentra pertanian organik di wilayah ini," ujar Kepala Desa Ahmad Santoso, S.IP dalam sambutannya.</p><p>Program ini akan berlangsung selama 12 bulan dengan target peningkatan produktivitas hingga 30% dan peningkatan pendapatan petani sebesar 25%.</p>',
  'Desa Sejahtera secara resmi meluncurkan program pertanian organik perdana yang melibatkan 120 petani lokal dengan dukungan penuh dari Dinas Pertanian Kabupaten...',
  'Admin Desa',
  1234,
  'published',
  '2026-11-05'
),
(
  'Desa Sejahtera Raih Penghargaan Desa Digital Terbaik Kabupaten 2026',
  'penghargaan-desa-digital-terbaik-2026',
  'prestasi',
  '<p>Desa Sejahtera berhasil meraih penghargaan bergengsi sebagai Desa Digital Terbaik Tingkat Kabupaten Contoh tahun 2026. Penghargaan ini diberikan oleh Bupati Contoh dalam acara peringatan Hari Jadi Kabupaten yang ke-175.</p><p>Penghargaan ini diraih berkat inovasi pelayanan publik berbasis digital yang telah diterapkan sejak tahun 2024, termasuk layanan pengajuan surat online, sistem informasi desa, dan aplikasi pengaduan warga yang responsif.</p><p>"Penghargaan ini adalah motivasi bagi kami untuk terus berinovasi dan meningkatkan kualitas pelayanan kepada masyarakat," ungkap Sekretaris Desa Siti Aminah, S.Sos.</p><p>Desa Sejahtera juga akan menjadi desa percontohan bagi desa-desa lain dalam implementasi sistem desa digital di wilayah Kabupaten Contoh.</p>',
  'Desa Sejahtera berhasil meraih penghargaan sebagai Desa Digital Terbaik Tingkat Kabupaten tahun 2026 berkat inovasi pelayanan berbasis digital...',
  'Admin Desa',
  856,
  'published',
  '2026-11-01'
),
(
  'Pembukaan PAUD Gratis untuk Warga Kurang Mampu Tahun Ajaran 2026/2027',
  'pembukaan-paud-gratis-2026-2027',
  'pendidikan',
  '<p>Pemerintah Desa Sejahtera dengan bangga mengumumkan pembukaan Program Pendidikan Anak Usia Dini (PAUD) gratis bagi keluarga kurang mampu untuk tahun ajaran 2026/2027.</p><p>Program ini merupakan wujud komitmen pemerintah desa dalam meningkatkan akses pendidikan berkualitas sejak dini. PAUD Sejahtera Ceria akan menampung hingga 50 anak usia 3-5 tahun dengan tenaga pengajar profesional dan bersertifikat.</p><p>Fasilitas yang disediakan meliputi ruang belajar yang nyaman, alat permainan edukatif, buku-buku bacaan anak, serta konsumsi bergizi setiap hari. Seluruh biaya ditanggung oleh APBDes.</p><p>"Pendidikan adalah hak semua anak. Kami ingin memastikan tidak ada anak di desa ini yang tertinggal dalam mendapatkan pendidikan dasar yang layak," tegas Kepala Desa Ahmad Santoso.</p><p>Pendaftaran dibuka mulai 1 November 2026 di Kantor Desa Sejahtera dengan membawa Kartu Keluarga dan Akta Kelahiran.</p>',
  'Pemerintah Desa Sejahtera membuka Program PAUD gratis bagi keluarga kurang mampu untuk tahun ajaran 2026/2027 dengan kapasitas 50 anak...',
  'Admin Desa',
  672,
  'published',
  '2026-10-28'
),
(
  'Proyek Pavingisasi Jalan Dusun RT 03 Dimulai Bulan Ini',
  'pavingisasi-jalan-dusun-rt03-november-2026',
  'infrastruktur',
  '<p>Pemerintah Desa Sejahtera memulai proyek pavingisasi jalan sepanjang 1,2 kilometer di wilayah Dusun Maju RT 03 RW 03 pada minggu pertama November 2026.</p><p>Proyek ini merupakan bagian dari program pembangunan infrastruktur desa tahun 2026 dengan anggaran sebesar Rp 450 juta yang bersumber dari Dana Desa dan ADD (Alokasi Dana Desa).</p><p>Kepala Desa Ahmad Santoso menjelaskan bahwa proyek ini diharapkan dapat meningkatkan akses warga terhadap fasilitas umum dan memperlancar distribusi hasil pertanian ke pasar.</p><p>"Jalan yang baik adalah kunci kemajuan desa. Dengan jalan yang layak, aktivitas ekonomi warga akan lebih produktif," ujarnya saat meninjau lokasi proyek.</p><p>Proyek ini ditargetkan selesai dalam waktu 3 bulan dengan melibatkan kontraktor lokal dan tenaga kerja dari warga setempat untuk membuka lapangan kerja.</p>',
  'Pemerintah Desa memulai proyek pavingisasi jalan sepanjang 1,2 km di Dusun Maju RT 03 dengan anggaran Rp 450 juta dari Dana Desa...',
  'Admin Desa',
  543,
  'published',
  '2026-10-20'
),
(
  'Kegiatan Posyandu Balita Rutin Bulan November 2026',
  'kegiatan-posyandu-balita-november-2026',
  'kesehatan',
  '<p>Posyandu Balita Desa Sejahtera menggelar kegiatan rutin bulanan pada tanggal 15 November 2026 di tiga lokasi berbeda sesuai jadwal masing-masing RW.</p><p>Kegiatan ini meliputi pemeriksaan kesehatan balita, penimbangan, pemberian vitamin A, imunisasi, dan konseling gizi bagi ibu-ibu. Pelayanan dilakukan oleh tenaga kesehatan dari Puskesmas Maju Jaya dan kader posyandu desa.</p><p>Ibu Dewi Lestari, S.Pd selaku Kaur Umum yang membidangi kesehatan mengimbau seluruh ibu yang memiliki balita untuk aktif mengikuti posyandu.</p><p>"Kesehatan anak adalah investasi masa depan. Mari kita pantau tumbuh kembang anak-anak kita secara rutin melalui posyandu," ajaknya.</p><p>Selain layanan kesehatan, posyandu juga menyediakan makanan tambahan bergizi untuk balita yang hadir.</p>',
  'Posyandu Balita Desa Sejahtera menggelar kegiatan rutin pada 15 November 2026 dengan layanan pemeriksaan kesehatan, imunisasi, dan konseling gizi...',
  'Admin Desa',
  421,
  'published',
  '2026-10-15'
),
(
  'Gotong Royong Bersih Desa Menyambut HUT RI ke-81',
  'gotong-royong-bersih-desa-hut-ri-81',
  'umum',
  '<p>Warga Desa Sejahtera kompak melaksanakan kegiatan gotong royong bersih desa pada hari Minggu, 10 Agustus 2026 sebagai persiapan menyambut Hari Ulang Tahun Kemerdekaan Republik Indonesia yang ke-81.</p><p>Kegiatan yang dimulai pukul 07.00 WIB ini diikuti oleh ratusan warga dari berbagai kalangan, mulai dari anak-anak hingga orang tua. Seluruh wilayah desa dibersihkan, mulai dari jalan raya, selokan, taman desa, hingga fasilitas umum lainnya.</p><p>Kepala Desa Ahmad Santoso yang turut turun langsung dalam kegiatan ini mengapresiasi semangat gotong royong warga.</p><p>"Semangat gotong royong adalah jati diri bangsa kita. Mari kita jaga tradisi baik ini untuk generasi mendatang," ucapnya sembari menyapu halaman balai desa.</p><p>Setelah kegiatan bersih desa, dilanjutkan dengan makan bersama dan persiapan lomba-lomba 17 Agustusan yang akan diselenggarakan di lapangan desa.</p>',
  'Warga Desa Sejahtera kompak melaksanakan gotong royong bersih desa menyambut HUT RI ke-81 dengan partisipasi ratusan warga dari berbagai kalangan...',
  'Admin Desa',
  789,
  'published',
  '2026-08-11'
);

-- ========================================
-- SEED DATA: pengumuman
-- ========================================
INSERT INTO pengumuman (judul, konten, jenis, tanggal_mulai, tanggal_selesai, status) VALUES
(
  'Pendaftaran PAUD Gratis Tahun Ajaran 2026/2027 Dibuka',
  'Pemerintah Desa Sejahtera membuka pendaftaran PAUD gratis untuk keluarga kurang mampu. Pendaftaran dibuka mulai 1 November 2026 di Kantor Desa. Syarat: KK, Akta Kelahiran, Surat Keterangan Tidak Mampu.',
  'penting',
  '2026-11-01',
  '2026-11-30',
  'aktif'
),
(
  'Jadwal Pelayanan Administrasi Bulan November 2026',
  'Pelayanan administrasi di Kantor Desa Sejahtera buka setiap hari Senin-Jumat pukul 08.00-15.00 WIB (istirahat 12.00-13.00). Khusus hari Jumat tutup pukul 11.00-13.00 untuk istirahat Jumat.',
  'info',
  '2026-11-01',
  '2026-11-30',
  'aktif'
),
(
  'Himbauan Waspada Demam Berdarah',
  'Dinas Kesehatan Kabupaten menghimbau warga untuk waspada terhadap penyebaran demam berdarah. Lakukan 3M Plus: Menguras, Menutup, Mengubur, plus fogging rutin setiap minggu. Segera ke puskesmas jika mengalami gejala demam tinggi mendadak.',
  'penting',
  '2026-10-15',
  '2026-12-31',
  'aktif'
),
(
  'Pembayaran PBB Dapat Dilakukan di Kantor Desa',
  'Mulai bulan November 2026, pembayaran Pajak Bumi dan Bangunan (PBB) dapat dilakukan di Kantor Desa Sejahtera setiap hari kerja. Bawa SPPT PBB asli untuk proses pembayaran.',
  'biasa',
  '2026-11-01',
  '2027-03-31',
  'aktif'
);

-- ========================================
-- SEED DATA: pengajuan_surat (Contoh)
-- ========================================
INSERT INTO pengajuan_surat (
  nomor_pengajuan, jenis_surat, nik, nama_pemohon, 
  tempat_lahir, tanggal_lahir, jenis_kelamin, pekerjaan, 
  alamat, no_telepon, keperluan, status
) VALUES
(
  'REG-20261110-001',
  'surat_domisili',
  '3301012199010001',
  'Budi Santoso',
  'Semarang',
  '1990-01-21',
  'L',
  'Wiraswasta',
  'Dusun Makmur RT 01 RW 01, Desa Sejahtera',
  '081234567801',
  'Keperluan pendaftaran NPWP di Kantor Pajak',
  'pending'
),
(
  'REG-20261109-002',
  'surat_pengantar_skck',
  '3301012199205002',
  'Siti Nurhaliza',
  'Semarang',
  '1992-05-12',
  'P',
  'Karyawan Swasta',
  'Dusun Sejahtera RT 02 RW 02, Desa Sejahtera',
  '081234567802',
  'Melamar pekerjaan di PT. Maju Jaya',
  'diproses'
),
(
  'REG-20261108-003',
  'surat_keterangan_usaha',
  '3301012198803003',
  'Ahmad Fauzi',
  'Semarang',
  '1988-03-15',
  'L',
  'Pedagang',
  'Dusun Maju RT 03 RW 03, Desa Sejahtera',
  '081234567803',
  'Mengajukan pinjaman modal usaha di Bank BRI',
  'selesai'
),
(
  'REG-20261107-004',
  'surat_tidak_mampu',
  '3301012200101004',
  'Dewi Lestari',
  'Semarang',
  '2001-01-10',
  'P',
  'Pelajar',
  'Dusun Makmur RT 02 RW 01, Desa Sejahtera',
  '081234567804',
  'Pengajuan beasiswa kuliah S1 di Universitas Negeri Semarang',
  'selesai'
),
(
  'REG-20261106-005',
  'surat_pengantar_ktp',
  '3301012199512005',
  'Eko Prasetyo',
  'Semarang',
  '1995-12-20',
  'L',
  'Buruh Harian',
  'Dusun Sejahtera RT 01 RW 02, Desa Sejahtera',
  '081234567805',
  'Pembuatan KTP baru karena hilang',
  'pending'
);

-- ========================================
-- SEED DATA: galeri
-- ========================================
INSERT INTO galeri (judul, deskripsi, kategori, gambar, tanggal_upload) VALUES
('Kegiatan Gotong Royong Warga Desa', 'Gotong royong membersihkan lingkungan desa dalam rangka menyambut HUT RI', 'Kegiatan', 'gotong-royong.jpg', '2026-08-11'),
('Perayaan HUT Desa Sejahtera ke-79', 'Perayaan hari jadi Desa Sejahtera yang ke-79 dengan berbagai lomba dan hiburan', 'Acara', 'hut-desa.jpg', '2026-08-17'),
('Kegiatan Posyandu Balita', 'Pelayanan kesehatan balita rutin bulanan di Posyandu Desa Sejahtera', 'Kesehatan', 'posyandu.jpg', '2026-10-15'),
('Musyawarah Perencanaan Pembangunan Desa (Musrenbangdes)', 'Musrenbangdes membahas program pembangunan tahun 2027', 'Rapat', 'musrenbang.jpg', '2026-09-20'),
('Lomba 17 Agustusan', 'Berbagai lomba tradisional dalam rangka memeriahkan HUT RI ke-81', 'Acara', 'lomba-agustus.jpg', '2026-08-17');

-- ========================================
-- VIEWS untuk Dashboard Admin
-- ========================================

-- View untuk statistik pengajuan surat
CREATE VIEW v_statistik_pengajuan AS
SELECT 
  COUNT(*) as total_pengajuan,
  SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
  SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
  SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
  SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak,
  SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as pengajuan_hari_ini
FROM pengajuan_surat;

-- ========================================
-- STORED PROCEDURES
-- ========================================

DELIMITER //

-- Procedure untuk generate nomor pengajuan otomatis
CREATE PROCEDURE generate_nomor_pengajuan(OUT nomor VARCHAR(50))
BEGIN
  DECLARE tanggal_str VARCHAR(8);
  DECLARE urutan INT;
  
  SET tanggal_str = DATE_FORMAT(CURDATE(), '%Y%m%d');
  
  SELECT COALESCE(MAX(CAST(SUBSTRING(nomor_pengajuan, -3) AS UNSIGNED)), 0) + 1
  INTO urutan
  FROM pengajuan_surat
  WHERE DATE(created_at) = CURDATE();
  
  SET nomor = CONCAT('REG-', tanggal_str, '-', LPAD(urutan, 3, '0'));
END //

DELIMITER ;

-- ========================================
-- SELESAI
-- ========================================

SELECT 'Database berhasil dibuat dan diisi dengan data awal!' as status;

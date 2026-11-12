<?php
/**
 * Landing Page - Website Desa Sejahtera
 * Halaman utama dengan PHP Backend
 */

// Enable error logging but hide from display in production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');

require_once 'includes/session.php';
require_once 'includes/functions.php';

// Get data dari database with error handling
try {
    $profil = getProfilDesa();
    $berita = getBeritaTerbaru(6);
    $pengumuman = getPengumumanAktif();
    $perangkat = getPerangkatDesa();
} catch (Exception $e) {
    error_log("Error fetching data in index-landing.php: " . $e->getMessage());
    // Set default values if database fails
    $profil = null;
    $berita = [];
    $pengumuman = [];
    $perangkat = [];
}

// Set default values if data is null
if ($profil === null || empty($profil)) {
    $profil = [
        'nama_desa' => 'Desa Sejahtera',
        'kecamatan' => 'Maju Jaya',
        'kabupaten' => 'Kabupaten Makmur',
        'provinsi' => 'Provinsi Berkembang',
        'alamat' => 'Jl. Kemerdekaan No. 123',
        'kode_pos' => '12345',
        'telepon' => '(021) 1234567',
        'email' => 'info@desasejahtera.id',
        'website' => 'www.desasejahtera.id',
        'visi' => 'Terwujudnya Desa Sejahtera yang Maju, Mandiri, dan Berdaya',
        'misi' => 'Meningkatkan kualitas hidup masyarakat desa',
        'latitude' => '-6.200000',
        'longitude' => '106.816666'
    ];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?php echo sanitize($profil['nama_desa'] ?? 'Website Desa Sejahtera'); ?> - Pelayanan Administrasi Online dan Informasi Desa" />
  <title><?php echo sanitize($profil['nama_desa'] ?? 'Desa Sejahtera'); ?> - Website Resmi</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>

  <?php
  // Flash message
  $flash = getFlashMessage();
  if ($flash): ?>
  <div class="toast show <?php echo $flash['type'] === 'success' ? 'success' : ''; ?>" id="flashToast">
    <?php echo sanitize($flash['message']); ?>
  </div>
  <script>
    setTimeout(() => {
      document.getElementById('flashToast').classList.remove('show');
    }, 4000);
  </script>
  <?php endif; ?>

  <!-- ===== HEADER / NAVBAR ===== -->
  <header class="header" id="header">
    <nav class="navbar container">
      <div class="nav-logo">
        <div class="logo-icon">
          <i class="fas fa-landmark"></i>
        </div>
        <div class="logo-text">
          <span class="logo-title"><?php echo sanitize($profil['nama_desa'] ?? 'Desa Sejahtera'); ?></span>
          <span class="logo-subtitle">Kec. <?php echo sanitize($profil['kecamatan'] ?? 'Maju Jaya'); ?></span>
        </div>
      </div>

      <ul class="nav-menu" id="navMenu">
        <li><a href="#home" class="nav-link active">Beranda</a></li>
        <li><a href="#profil" class="nav-link">Profil</a></li>
        <li><a href="#layanan" class="nav-link">Layanan</a></li>
        <li><a href="#berita" class="nav-link">Berita</a></li>
        <li><a href="#perangkat" class="nav-link">Perangkat</a></li>
        <li><a href="#kontak" class="nav-link">Kontak</a></li>
      </ul>

      <div class="nav-actions">
        <a href="pengajuan.php" class="btn btn-primary">
          <i class="fas fa-file-alt"></i> Ajukan Surat
        </a>
        <button class="hamburger" id="hamburger" aria-label="Toggle menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </nav>
  </header>

  <!-- ===== HERO SECTION ===== -->
  <section class="hero" id="home">
    <div class="hero-bg-shapes">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
    </div>
    <div class="container hero-content">
      <div class="hero-text">
        <span class="hero-badge">
          <i class="fas fa-star"></i> Desa Digital Terbaik 2024
        </span>
        <h1 class="hero-title">
          Selamat Datang di <br/>
          <span class="highlight"><?php echo sanitize($profil['nama_desa'] ?? 'Desa Sejahtera'); ?></span>
          <br/>yang Maju & Berdaya
        </h1>
        <p class="hero-desc">
          Desa kami berkomitmen untuk memberikan pelayanan publik terbaik,
          transparan, dan mudah diakses oleh seluruh warga. Bersama membangun
          desa yang mandiri dan sejahtera.
        </p>
        <div class="hero-buttons">
          <a href="pengajuan.php" class="btn btn-primary btn-lg">
            <i class="fas fa-concierge-bell"></i> Ajukan Surat Online
          </a>
          <a href="#profil" class="btn btn-outline btn-lg">
            <i class="fas fa-info-circle"></i> Tentang Desa
          </a>
        </div>
        <div class="hero-stats">
          <div class="stat">
            <span class="stat-number"><?php echo number_format($profil['jumlah_penduduk'] ?? 4820); ?></span>
            <span class="stat-label">Jumlah Penduduk</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat">
            <span class="stat-number"><?php echo $profil['jumlah_rt'] ?? 12; ?> / <?php echo $profil['jumlah_rw'] ?? 4; ?></span>
            <span class="stat-label">RT / RW</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat">
            <span class="stat-number">98%</span>
            <span class="stat-label">Kepuasan Warga</span>
          </div>
        </div>
      </div>
      <div class="hero-image">
        <div class="hero-img-wrapper">
          <div class="hero-img-bg"></div>
          <div class="hero-img-card">
            <div class="village-illustration">
              <div class="illustration-sky">
                <div class="sun"></div>
                <div class="cloud cloud-1"></div>
                <div class="cloud cloud-2"></div>
              </div>
              <div class="illustration-ground">
                <div class="building balai-desa">
                  <div class="building-roof"></div>
                  <div class="building-body">
                    <div class="window"></div>
                    <div class="door"></div>
                    <div class="window"></div>
                  </div>
                  <div class="building-label">Balai Desa</div>
                </div>
                <div class="building rumah">
                  <div class="building-roof small"></div>
                  <div class="building-body small">
                    <div class="door small"></div>
                  </div>
                </div>
                <div class="tree">
                  <div class="tree-top"></div>
                  <div class="tree-trunk"></div>
                </div>
                <div class="tree tree-2">
                  <div class="tree-top"></div>
                  <div class="tree-trunk"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="floating-card card-pengumuman">
            <i class="fas fa-bell"></i>
            <span>Pengumuman Terbaru</span>
          </div>
          <div class="floating-card card-online">
            <i class="fas fa-wifi"></i>
            <span>Layanan Online 24/7</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== PENGUMUMAN STICKY (jika ada) ===== -->
  <?php if (!empty($pengumuman)): ?>
  <section class="info-box" style="margin: -2rem auto 0; max-width: 1200px; padding: 0 1.5rem;">
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 1rem; padding: 1rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
      <div style="font-size: 1.5rem; color: #f59e0b; flex-shrink: 0;"><i class="fas fa-bullhorn"></i></div>
      <div style="flex: 1;">
        <strong style="color: #92400e; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">📢 Pengumuman Penting:</strong>
        <?php foreach (array_slice($pengumuman, 0, 2) as $p): ?>
        <p style="color: #92400e; font-size: 0.85rem; margin-bottom: 0.4rem;">
          <strong><?php echo sanitize($p['judul']); ?></strong> - 
          <?php echo truncateText(sanitize($p['konten']), 120); ?>
        </p>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===== PROFIL DESA ===== -->
  <section class="profil section" id="profil">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Profil Kami</span>
        <h2 class="section-title">Mengenal <span class="highlight"><?php echo sanitize($profil['nama_desa'] ?? 'Desa Sejahtera'); ?></span></h2>
        <p class="section-desc"><?php echo truncateText(sanitize($profil['sejarah'] ?? 'Desa Sejahtera berdiri sejak tahun 1945'), 150); ?></p>
      </div>
      <div class="profil-grid">
        <div class="profil-card">
          <div class="profil-icon green">
            <i class="fas fa-history"></i>
          </div>
          <h3>Sejarah Desa</h3>
          <p><?php echo truncateText(sanitize($profil['sejarah'] ?? 'Desa Sejahtera didirikan pada 17 Agustus 1945'), 100); ?></p>
        </div>
        <div class="profil-card">
          <div class="profil-icon blue">
            <i class="fas fa-map-marked-alt"></i>
          </div>
          <h3>Letak Geografis</h3>
          <p>Terletak di Kecamatan <?php echo sanitize($profil['kecamatan'] ?? 'Maju Jaya'); ?>, dengan luas wilayah <?php echo sanitize($profil['luas_wilayah'] ?? '450 Ha'); ?></p>
        </div>
        <div class="profil-card">
          <div class="profil-icon orange">
            <i class="fas fa-bullseye"></i>
          </div>
          <h3>Visi & Misi</h3>
          <p><?php echo truncateText(sanitize($profil['visi'] ?? 'Mewujudkan desa yang mandiri dan berdaya saing'), 100); ?></p>
        </div>
        <div class="profil-card">
          <div class="profil-icon purple">
            <i class="fas fa-users"></i>
          </div>
          <h3>Demografi</h3>
          <p>Jumlah Penduduk: <strong><?php echo number_format($profil['jumlah_penduduk'] ?? 4820); ?></strong> jiwa | 
             KK: <strong><?php echo number_format($profil['jumlah_kk'] ?? 1250); ?></strong></p>
        </div>
      </div>

      <!-- Kepala Desa Highlight -->
      <?php 
      $kepala = null;
      foreach ($perangkat as $p) {
        if (stripos($p['jabatan'], 'Kepala Desa') !== false) {
          $kepala = $p;
          break;
        }
      }
      if ($kepala): 
      ?>
      <div class="kades-card">
        <div class="kades-info">
          <div class="kades-avatar">
            <?php if (!empty($kepala['foto']) && file_exists('assets/uploads/' . $kepala['foto'])): ?>
              <img src="assets/uploads/<?php echo sanitize($kepala['foto']); ?>" alt="<?php echo sanitize($kepala['nama_lengkap']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            <?php else: ?>
              <i class="fas fa-user-tie"></i>
            <?php endif; ?>
          </div>
          <div class="kades-detail">
            <span class="kades-label">Kepala Desa</span>
            <h3><?php echo sanitize($kepala['nama_lengkap']); ?></h3>
            <?php if (!empty($kepala['pendidikan'])): ?>
            <p><?php echo sanitize($kepala['pendidikan']); ?></p>
            <?php endif; ?>
            <blockquote>"Bersama warga, kita wujudkan desa yang mandiri, inovatif, dan bermartabat."</blockquote>
          </div>
        </div>
        <div class="kades-contacts">
          <?php if (!empty($kepala['telepon'])): ?>
          <a href="tel:<?php echo sanitize($kepala['telepon']); ?>" class="contact-pill">
            <i class="fas fa-phone"></i> <?php echo sanitize($kepala['telepon']); ?>
          </a>
          <?php endif; ?>
          <?php if (!empty($kepala['email'])): ?>
          <a href="mailto:<?php echo sanitize($kepala['email']); ?>" class="contact-pill">
            <i class="fas fa-envelope"></i> <?php echo sanitize($kepala['email']); ?>
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ===== LAYANAN SECTION ===== -->
  <section class="layanan section" id="layanan">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Layanan Publik</span>
        <h2 class="section-title">Layanan <span class="highlight">Administrasi</span> Desa</h2>
        <p class="section-desc">Semua layanan administratif kini tersedia secara online maupun langsung di kantor desa kami.</p>
      </div>
      <div class="layanan-grid">
        <div class="layanan-card">
          <div class="layanan-icon"><i class="fas fa-id-card"></i></div>
          <h3>Surat Keterangan Domisili</h3>
          <p>Proses pengurusan surat domisili cepat dan mudah.</p>
          <div class="layanan-meta">
            <span class="badge-time"><i class="far fa-clock"></i> 1 Hari Kerja</span>
            <span class="badge-free"><i class="fas fa-tag"></i> Gratis</span>
          </div>
          <a href="pengajuan.php" class="btn btn-sm btn-primary">Ajukan Sekarang</a>
        </div>
        <div class="layanan-card featured">
          <div class="featured-label">Populer</div>
          <div class="layanan-icon"><i class="fas fa-file-signature"></i></div>
          <h3>Surat Pengantar KTP/KK</h3>
          <p>Layanan pengantar untuk pembuatan KTP dan KK.</p>
          <div class="layanan-meta">
            <span class="badge-time"><i class="far fa-clock"></i> 1 Hari Kerja</span>
            <span class="badge-free"><i class="fas fa-tag"></i> Gratis</span>
          </div>
          <a href="pengajuan.php" class="btn btn-sm btn-primary">Ajukan Sekarang</a>
        </div>
        <div class="layanan-card">
          <div class="layanan-icon"><i class="fas fa-home"></i></div>
          <h3>Surat Keterangan Tidak Mampu</h3>
          <p>Untuk keperluan beasiswa dan bantuan sosial.</p>
          <div class="layanan-meta">
            <span class="badge-time"><i class="far fa-clock"></i> 2 Hari Kerja</span>
            <span class="badge-free"><i class="fas fa-tag"></i> Gratis</span>
          </div>
          <a href="pengajuan.php" class="btn btn-sm btn-primary">Ajukan Sekarang</a>
        </div>
        <div class="layanan-card">
          <div class="layanan-icon"><i class="fas fa-briefcase"></i></div>
          <h3>Surat Keterangan Usaha</h3>
          <p>Untuk UMKM dan usaha kecil yang membutuhkan legalitas.</p>
          <div class="layanan-meta">
            <span class="badge-time"><i class="far fa-clock"></i> 2 Hari Kerja</span>
            <span class="badge-free"><i class="fas fa-tag"></i> Gratis</span>
          </div>
          <a href="pengajuan.php" class="btn btn-sm btn-primary">Ajukan Sekarang</a>
        </div>
        <div class="layanan-card">
          <div class="layanan-icon"><i class="fas fa-user-shield"></i></div>
          <h3>Surat Pengantar SKCK</h3>
          <p>Pengantar untuk pembuatan SKCK di Polsek.</p>
          <div class="layanan-meta">
            <span class="badge-time"><i class="far fa-clock"></i> 1 Hari Kerja</span>
            <span class="badge-free"><i class="fas fa-tag"></i> Gratis</span>
          </div>
          <a href="pengajuan.php" class="btn btn-sm btn-primary">Ajukan Sekarang</a>
        </div>
        <div class="layanan-card">
          <div class="layanan-icon"><i class="fas fa-scroll"></i></div>
          <h3>Layanan Surat Lainnya</h3>
          <p>Berbagai surat keterangan sesuai kebutuhan warga.</p>
          <div class="layanan-meta">
            <span class="badge-time"><i class="far fa-clock"></i> 1-3 Hari Kerja</span>
            <span class="badge-free"><i class="fas fa-tag"></i> Gratis</span>
          </div>
          <a href="pengajuan.php" class="btn btn-sm btn-primary">Ajukan Sekarang</a>
        </div>
      </div>

      <div class="info-box">
        <div class="info-box-icon"><i class="fas fa-info-circle"></i></div>
        <div class="info-box-text">
          <strong>Jam Pelayanan:</strong> Senin–Jumat, 08.00–15.00 WIB | 
          <strong>Istirahat:</strong> 12.00–13.00 WIB | 
          <strong>Sabtu–Minggu:</strong> Libur
        </div>
      </div>
    </div>
  </section>

  <!-- ===== BERITA SECTION ===== -->
  <section class="berita section" id="berita">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Informasi Terkini</span>
        <h2 class="section-title">Berita & <span class="highlight">Pengumuman</span></h2>
        <p class="section-desc">Ikuti perkembangan terbaru kegiatan dan informasi penting dari desa kami.</p>
      </div>
      
      <?php if (!empty($berita)): ?>
      <div class="berita-grid">
        <!-- Berita Utama -->
        <?php $featured = $berita[0]; 
        $kategori = getKategoriBerita($featured['kategori']);
        ?>
        <article class="berita-card berita-featured">
          <div class="berita-img-placeholder featured-img">
            <i class="fas fa-<?php 
              echo $featured['kategori'] === 'program_desa' ? 'tractor' : 
                   ($featured['kategori'] === 'prestasi' ? 'award' : 
                   ($featured['kategori'] === 'pendidikan' ? 'graduation-cap' : 'newspaper')); 
            ?>"></i>
          </div>
          <div class="berita-content">
            <span class="berita-kategori <?php echo $kategori['class']; ?>"><?php echo $kategori['text']; ?></span>
            <h3><?php echo sanitize($featured['judul']); ?></h3>
            <p><?php echo sanitize($featured['ringkasan']) ?: truncateText(stripHtmlButKeepBreaks($featured['konten']), 150); ?></p>
            <div class="berita-meta">
              <span><i class="far fa-calendar"></i> <?php echo formatTanggal($featured['tanggal_publish']); ?></span>
              <span><i class="far fa-eye"></i> <?php echo number_format($featured['views']); ?> views</span>
            </div>
            <a href="pages/berita-detail.php?slug=<?php echo $featured['slug']; ?>" class="btn btn-sm btn-outline">Baca Selengkapnya</a>
          </div>
        </article>

        <!-- Berita Kecil -->
        <div class="berita-side">
          <?php 
          $colors = ['orange', 'blue', 'purple', 'green'];
          foreach (array_slice($berita, 1, 3) as $index => $item): 
            $kat = getKategoriBerita($item['kategori']);
          ?>
          <article class="berita-card berita-small">
            <div class="berita-img-placeholder small-img <?php echo $colors[$index % 4]; ?>">
              <i class="fas fa-<?php 
                echo $item['kategori'] === 'prestasi' ? 'award' : 
                     ($item['kategori'] === 'pendidikan' ? 'graduation-cap' : 
                     ($item['kategori'] === 'infrastruktur' ? 'tools' : 'newspaper')); 
              ?>"></i>
            </div>
            <div class="berita-content">
              <span class="berita-kategori <?php echo $kat['class']; ?>"><?php echo $kat['text']; ?></span>
              <h4><?php echo sanitize($item['judul']); ?></h4>
              <div class="berita-meta">
                <span><i class="far fa-calendar"></i> <?php echo formatTanggal($item['tanggal_publish']); ?></span>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="text-center mt-4">
        <a href="pages/berita.php" class="btn btn-outline">
          <i class="fas fa-newspaper"></i> Lihat Semua Berita
        </a>
      </div>
      <?php else: ?>
      <p class="text-center" style="color: #9ca3af;">Belum ada berita yang dipublikasikan.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- ===== PERANGKAT DESA SECTION ===== -->
  <section class="profil section" id="perangkat" style="background: var(--white);">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Struktur Organisasi</span>
        <h2 class="section-title">Perangkat <span class="highlight">Desa</span></h2>
        <p class="section-desc">Tim pemerintahan desa yang profesional dan siap melayani masyarakat.</p>
      </div>
      
      <?php if (!empty($perangkat)): ?>
      <div class="profil-grid">
        <?php 
        $icons = ['user-tie', 'user-circle', 'user-graduate', 'user-cog', 'user-check', 'user-clock', 'user-shield', 'user'];
        foreach (array_slice($perangkat, 0, 8) as $index => $p): 
        ?>
        <div class="profil-card">
          <div class="profil-icon <?php echo ['green', 'blue', 'orange', 'purple'][$index % 4]; ?>">
            <?php if (!empty($p['foto']) && file_exists('assets/uploads/' . $p['foto'])): ?>
              <img src="assets/uploads/<?php echo sanitize($p['foto']); ?>" alt="<?php echo sanitize($p['nama_lengkap']); ?>" style="width: 52px; height: 52px; object-fit: cover; border-radius: 0.75rem;">
            <?php else: ?>
              <i class="fas fa-<?php echo $icons[$index % 8]; ?>"></i>
            <?php endif; ?>
          </div>
          <h3><?php echo sanitize($p['nama_lengkap']); ?></h3>
          <p><strong><?php echo sanitize($p['jabatan']); ?></strong></p>
          <?php if (!empty($p['pendidikan'])): ?>
          <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;"><?php echo sanitize($p['pendidikan']); ?></p>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <p class="text-center" style="color: #9ca3af;">Data perangkat desa belum tersedia.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- ===== KONTAK SECTION ===== -->
  <section class="kontak section" id="kontak">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Hubungi Kami</span>
        <h2 class="section-title">Kontak & <span class="highlight">Lokasi</span></h2>
        <p class="section-desc">Kami siap membantu dan melayani Anda. Jangan ragu untuk menghubungi kami.</p>
      </div>
      <div class="kontak-grid">
        <!-- Info Kontak -->
        <div class="kontak-info">
          <div class="kontak-info-card">
            <div class="kontak-item">
              <div class="kontak-icon green"><i class="fas fa-map-marker-alt"></i></div>
              <div>
                <strong>Alamat Kantor</strong>
                <p><?php echo nl2br(sanitize($profil['alamat_kantor'] ?? 'Jl. Raya Desa Sejahtera No. 1')); ?></p>
              </div>
            </div>
            <?php if (!empty($profil['telepon'])): ?>
            <div class="kontak-item">
              <div class="kontak-icon blue"><i class="fas fa-phone-alt"></i></div>
              <div>
                <strong>Telepon / WhatsApp</strong>
                <p><?php echo sanitize($profil['telepon']); ?></p>
              </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($profil['email'])): ?>
            <div class="kontak-item">
              <div class="kontak-icon orange"><i class="fas fa-envelope"></i></div>
              <div>
                <strong>Email</strong>
                <p><?php echo sanitize($profil['email']); ?></p>
              </div>
            </div>
            <?php endif; ?>
            <div class="kontak-item">
              <div class="kontak-icon purple"><i class="far fa-clock"></i></div>
              <div>
                <strong>Jam Operasional</strong>
                <p>Senin – Jumat: 08.00 – 15.00 WIB</p>
              </div>
            </div>
          </div>

          <div class="map-placeholder" onclick="window.open('https://maps.google.com/?q=<?php echo urlencode($profil['nama_desa'] ?? 'Desa Sejahtera'); ?>', '_blank')">
            <i class="fas fa-map-marked-alt"></i>
            <p>Peta Lokasi Kantor Desa</p>
            <small>Klik untuk membuka di Google Maps</small>
          </div>
        </div>
        
        <!-- Call to Action -->
        <div class="kontak-form-wrapper">
          <h3>Ajukan Surat Online</h3>
          <p style="font-size: 0.9rem; color: #6b7280; margin-bottom: 1.5rem;">
            Tidak perlu datang ke kantor! Ajukan berbagai jenis surat secara online kapan saja, di mana saja.
          </p>
          <a href="pengajuan.php" class="btn btn-primary btn-block btn-lg">
            <i class="fas fa-file-signature"></i> Mulai Pengajuan Surat
          </a>
          <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
            <p style="font-size: 0.85rem; color: #9ca3af; text-align: center;">
              <i class="fas fa-shield-alt"></i> Data Anda aman dan terenkripsi
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== FOOTER ===== -->
  <footer class="footer">
    <div class="footer-top">
      <div class="container footer-grid">
        <div class="footer-brand">
          <div class="nav-logo footer-logo">
            <div class="logo-icon white"><i class="fas fa-landmark"></i></div>
            <div class="logo-text">
              <span class="logo-title"><?php echo sanitize($profil['nama_desa'] ?? 'Desa Sejahtera'); ?></span>
              <span class="logo-subtitle">Kec. <?php echo sanitize($profil['kecamatan'] ?? 'Maju Jaya'); ?></span>
            </div>
          </div>
          <p class="footer-desc">
            Website resmi <?php echo sanitize($profil['nama_desa'] ?? 'Desa Sejahtera'); ?>. 
            Melayani dengan sepenuh hati untuk kemajuan bersama.
          </p>
          <div class="social-links">
            <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="#" class="social-link" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
          </div>
        </div>

        <div class="footer-col">
          <h4>Navigasi Cepat</h4>
          <ul class="footer-links">
            <li><a href="#home"><i class="fas fa-chevron-right"></i> Beranda</a></li>
            <li><a href="#profil"><i class="fas fa-chevron-right"></i> Profil Desa</a></li>
            <li><a href="#layanan"><i class="fas fa-chevron-right"></i> Layanan</a></li>
            <li><a href="#berita"><i class="fas fa-chevron-right"></i> Berita</a></li>
            <li><a href="#perangkat"><i class="fas fa-chevron-right"></i> Perangkat Desa</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>Layanan Utama</h4>
          <ul class="footer-links">
            <li><a href="pengajuan.php"><i class="fas fa-chevron-right"></i> Surat Domisili</a></li>
            <li><a href="pengajuan.php"><i class="fas fa-chevron-right"></i> Pengantar KTP/KK</a></li>
            <li><a href="pengajuan.php"><i class="fas fa-chevron-right"></i> Surat Tidak Mampu</a></li>
            <li><a href="pengajuan.php"><i class="fas fa-chevron-right"></i> Surat SKCK</a></li>
            <li><a href="pengajuan.php"><i class="fas fa-chevron-right"></i> Surat Usaha</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>Kontak Kami</h4>
          <ul class="footer-contact-list">
            <li>
              <i class="fas fa-map-marker-alt"></i>
              <span><?php echo sanitize($profil['alamat_kantor'] ?? 'Jl. Raya Desa Sejahtera No. 1'); ?></span>
            </li>
            <?php if (!empty($profil['telepon'])): ?>
            <li>
              <i class="fas fa-phone"></i>
              <span><?php echo sanitize($profil['telepon']); ?></span>
            </li>
            <?php endif; ?>
            <?php if (!empty($profil['email'])): ?>
            <li>
              <i class="fas fa-envelope"></i>
              <span><?php echo sanitize($profil['email']); ?></span>
            </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container footer-bottom-content">
        <p>&copy; <?php echo date('Y'); ?> <?php echo sanitize($profil['nama_desa'] ?? 'Desa Sejahtera'); ?>. Semua hak cipta dilindungi.</p>
        <p>Dibuat dengan <i class="fas fa-heart" style="color:#ef4444"></i> untuk warga desa | <a href="admin/login.php" style="color: rgba(255,255,255,0.6);">Admin Login</a></p>
      </div>
    </div>
  </footer>

  <button class="back-to-top" id="backToTop" aria-label="Kembali ke atas">
    <i class="fas fa-arrow-up"></i>
  </button>

  <script src="script.js"></script>
</body>
</html>

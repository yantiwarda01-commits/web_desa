<?php
/**
 * Form Pengajuan Surat Online
 * Website Desa Sejahtera
 */

require_once 'includes/session.php';
require_once 'includes/functions.php';

$profil = getProfilDesa();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_surat = sanitize($_POST['jenis_surat'] ?? '');
    $nik = sanitize($_POST['nik'] ?? '');
    $nama = sanitize($_POST['nama_pemohon'] ?? '');
    $tempat_lahir = sanitize($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = sanitize($_POST['tanggal_lahir'] ?? '');
    $jenis_kelamin = sanitize($_POST['jenis_kelamin'] ?? '');
    $pekerjaan = sanitize($_POST['pekerjaan'] ?? '');
    $alamat = sanitize($_POST['alamat'] ?? '');
    $no_telepon = sanitize($_POST['no_telepon'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $keperluan = sanitize($_POST['keperluan'] ?? '');
    $keterangan = sanitize($_POST['keterangan_tambahan'] ?? '');
    
    // Validasi input
    $errors = [];
    
    if (empty($jenis_surat)) $errors[] = "Jenis surat harus dipilih";
    if (empty($nik) || strlen($nik) != 16) $errors[] = "NIK harus 16 digit";
    if (empty($nama)) $errors[] = "Nama pemohon harus diisi";
    if (empty($tempat_lahir)) $errors[] = "Tempat lahir harus diisi";
    if (empty($tanggal_lahir)) $errors[] = "Tanggal lahir harus diisi";
    if (empty($jenis_kelamin)) $errors[] = "Jenis kelamin harus dipilih";
    if (empty($alamat)) $errors[] = "Alamat harus diisi";
    if (empty($no_telepon)) $errors[] = "No. telepon harus diisi";
    if (empty($keperluan)) $errors[] = "Keperluan harus diisi";
    
    if (empty($errors)) {
        // Generate nomor pengajuan
        $nomor_pengajuan = generateNomorPengajuan();
        
        // Handle upload file jika ada
        $berkas_pendukung = null;
        if (isset($_FILES['berkas_pendukung']) && $_FILES['berkas_pendukung']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadFile(
                $_FILES['berkas_pendukung'],
                __DIR__ . '/assets/uploads',
                ['jpg', 'jpeg', 'png', 'pdf'],
                5242880 // 5MB
            );
            
            if ($upload['success']) {
                $berkas_pendukung = $upload['filename'];
            }
        }
        
        // Insert ke database
        $inserted = executeQuery(
            "INSERT INTO pengajuan_surat (
                nomor_pengajuan, jenis_surat, nik, nama_pemohon, 
                tempat_lahir, tanggal_lahir, jenis_kelamin, pekerjaan,
                alamat, no_telepon, email, keperluan, keterangan_tambahan,
                berkas_pendukung, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')",
            [
                $nomor_pengajuan, $jenis_surat, $nik, $nama,
                $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $pekerjaan,
                $alamat, $no_telepon, $email, $keperluan, $keterangan,
                $berkas_pendukung
            ],
            'ssssssssssssss'
        );
        
        if ($inserted) {
            setFlashMessage('success', "Pengajuan surat berhasil! Nomor pengajuan Anda: <strong>$nomor_pengajuan</strong>. Silakan catat nomor ini untuk tracking.");
            redirect('index-landing.php#layanan');
        } else {
            $errors[] = "Terjadi kesalahan saat menyimpan data. Silakan coba lagi.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengajuan Surat Online - <?php echo sanitize($profil['nama_desa'] ?? 'Desa Sejahtera'); ?></title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body { background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%); min-height: 100vh; }
    .pengajuan-container { max-width: 800px; margin: 3rem auto; padding: 0 1.5rem; }
    .form-card { background: white; border-radius: 1.5rem; padding: 2.5rem; box-shadow: 0 10px 40px rgba(0,0,0,.1); }
    .form-header { text-align: center; margin-bottom: 2rem; }
    .form-header h1 { font-size: 1.75rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; }
    .form-header p { color: #6b7280; font-size: 0.9rem; }
    .alert { padding: 1rem 1.25rem; border-radius: 0.75rem; margin-bottom: 1.5rem; }
    .alert-danger { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
    .alert ul { margin: 0.5rem 0 0 1.25rem; }
    .form-section { margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid #e5e7eb; }
    .form-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .form-section-title { font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .form-section-title i { color: #16a34a; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 640px) { .form-row-2 { grid-template-columns: 1fr; } }
    .btn-back { display: inline-flex; align-items: center; gap: 0.5rem; color: #6b7280; margin-bottom: 1.5rem; font-size: 0.9rem; transition: color 0.3s; }
    .btn-back:hover { color: #16a34a; }
  </style>
</head>
<body>
  
  <div class="pengajuan-container">
    <a href="index-landing.php" class="btn-back">
      <i class="fas fa-arrow-left"></i> Kembali ke Beranda
    </a>
    
    <div class="form-card">
      <div class="form-header">
        <h1><i class="fas fa-file-signature" style="color: #16a34a;"></i> Pengajuan Surat Online</h1>
        <p>Isi formulir di bawah ini dengan lengkap dan benar. Semua field bertanda * wajib diisi.</p>
      </div>
      
      <?php if (isset($errors) && !empty($errors)): ?>
      <div class="alert alert-danger">
        <strong><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan:</strong>
        <ul>
          <?php foreach ($errors as $error): ?>
          <li><?php echo $error; ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
      
      <form method="POST" action="" enctype="multipart/form-data">
        <!-- Section 1: Jenis Surat -->
        <div class="form-section">
          <h3 class="form-section-title"><i class="fas fa-file-alt"></i> Jenis Surat</h3>
          <div class="form-group">
            <label for="jenis_surat">Pilih Jenis Surat <span style="color: #ef4444;">*</span></label>
            <select id="jenis_surat" name="jenis_surat" required>
              <option value="">-- Pilih Jenis Surat --</option>
              <option value="surat_domisili">Surat Keterangan Domisili</option>
              <option value="surat_pengantar_ktp">Surat Pengantar KTP</option>
              <option value="surat_pengantar_kk">Surat Pengantar Kartu Keluarga</option>
              <option value="surat_tidak_mampu">Surat Keterangan Tidak Mampu</option>
              <option value="surat_kelahiran">Surat Pengantar Kelahiran</option>
              <option value="surat_keterangan_usaha">Surat Keterangan Usaha</option>
              <option value="surat_pengantar_skck">Surat Pengantar SKCK</option>
              <option value="surat_keterangan_ahli_waris">Surat Keterangan Ahli Waris</option>
              <option value="surat_keterangan_pindah">Surat Keterangan Pindah</option>
              <option value="surat_izin_keramaian">Surat Izin Keramaian</option>
              <option value="lainnya">Lainnya</option>
            </select>
          </div>
        </div>
        
        <!-- Section 2: Data Pemohon -->
        <div class="form-section">
          <h3 class="form-section-title"><i class="fas fa-user"></i> Data Pemohon</h3>
          
          <div class="form-group">
            <label for="nik">NIK (Nomor Induk Kependudukan) <span style="color: #ef4444;">*</span></label>
            <input type="text" id="nik" name="nik" placeholder="16 digit NIK" maxlength="16" pattern="[0-9]{16}" required />
            <small style="color: #6b7280; font-size: 0.8rem; display: block; margin-top: 0.25rem;">Sesuai dengan KTP</small>
          </div>
          
          <div class="form-group">
            <label for="nama_pemohon">Nama Lengkap <span style="color: #ef4444;">*</span></label>
            <input type="text" id="nama_pemohon" name="nama_pemohon" placeholder="Masukkan nama lengkap sesuai KTP" required />
          </div>
          
          <div class="form-row-2">
            <div class="form-group">
              <label for="tempat_lahir">Tempat Lahir <span style="color: #ef4444;">*</span></label>
              <input type="text" id="tempat_lahir" name="tempat_lahir" placeholder="Contoh: Semarang" required />
            </div>
            <div class="form-group">
              <label for="tanggal_lahir">Tanggal Lahir <span style="color: #ef4444;">*</span></label>
              <input type="date" id="tanggal_lahir" name="tanggal_lahir" required />
            </div>
          </div>
          
          <div class="form-row-2">
            <div class="form-group">
              <label for="jenis_kelamin">Jenis Kelamin <span style="color: #ef4444;">*</span></label>
              <select id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="">-- Pilih --</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div class="form-group">
              <label for="pekerjaan">Pekerjaan</label>
              <input type="text" id="pekerjaan" name="pekerjaan" placeholder="Contoh: Wiraswasta" />
            </div>
          </div>
          
          <div class="form-group">
            <label for="alamat">Alamat Lengkap <span style="color: #ef4444;">*</span></label>
            <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap sesuai KTP" required></textarea>
          </div>
          
          <div class="form-row-2">
            <div class="form-group">
              <label for="no_telepon">No. Telepon / WhatsApp <span style="color: #ef4444;">*</span></label>
              <input type="tel" id="no_telepon" name="no_telepon" placeholder="08xxxxxxxxxx" required />
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" placeholder="email@example.com" />
            </div>
          </div>
        </div>
        
        <!-- Section 3: Keperluan -->
        <div class="form-section">
          <h3 class="form-section-title"><i class="fas fa-clipboard-list"></i> Keperluan & Keterangan</h3>
          
          <div class="form-group">
            <label for="keperluan">Keperluan Surat <span style="color: #ef4444;">*</span></label>
            <textarea id="keperluan" name="keperluan" rows="3" placeholder="Jelaskan keperluan Anda mengajukan surat ini" required></textarea>
            <small style="color: #6b7280; font-size: 0.8rem; display: block; margin-top: 0.25rem;">Contoh: Untuk keperluan pendaftaran NPWP di Kantor Pajak</small>
          </div>
          
          <div class="form-group">
            <label for="keterangan_tambahan">Keterangan Tambahan (Opsional)</label>
            <textarea id="keterangan_tambahan" name="keterangan_tambahan" rows="2" placeholder="Informasi tambahan jika diperlukan"></textarea>
          </div>
          
          <div class="form-group">
            <label for="berkas_pendukung">Upload Berkas Pendukung (Opsional)</label>
            <input type="file" id="berkas_pendukung" name="berkas_pendukung" accept=".jpg,.jpeg,.png,.pdf" />
            <small style="color: #6b7280; font-size: 0.8rem; display: block; margin-top: 0.25rem;">
              Format: JPG, PNG, PDF | Maksimal 5MB
            </small>
          </div>
        </div>
        
        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-lg btn-block">
          <i class="fas fa-paper-plane"></i> Kirim Pengajuan
        </button>
        
        <p style="text-align: center; color: #6b7280; font-size: 0.85rem; margin-top: 1rem;">
          <i class="fas fa-info-circle"></i> Pengajuan Anda akan diproses dalam 1-3 hari kerja
        </p>
      </form>
    </div>
  </div>
  
  <script>
    // Auto-format NIK input (hanya angka)
    document.getElementById('nik').addEventListener('input', function(e) {
      this.value = this.value.replace(/\D/g, '').slice(0, 16);
    });
    
    // Auto-format phone number
    document.getElementById('no_telepon').addEventListener('input', function(e) {
      this.value = this.value.replace(/\D/g, '').slice(0, 13);
    });
  </script>
  
</body>
</html>

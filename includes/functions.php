<?php
/**
 * File Functions Umum
 * Website Desa Sejahtera
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Sanitize input dari user
 * @param mixed $data
 * @return mixed
 */
function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
        return $data;
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Redirect ke halaman tertentu
 * @param string $url
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Set flash message
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Get dan hapus flash message
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Generate slug dari string
 * @param string $string
 * @return string
 */
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = preg_replace('/^-+|-+$/', '', $string);
    return $string;
}

/**
 * Format tanggal Indonesia
 * @param string $date
 * @param bool $showTime
 * @return string
 */
function formatTanggal($date, $showTime = false) {
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $tanggal = date('j', $timestamp);
    $bulanAngka = date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    
    $result = $tanggal . ' ' . $bulan[$bulanAngka] . ' ' . $tahun;
    
    if ($showTime) {
        $result .= ' ' . date('H:i', $timestamp) . ' WIB';
    }
    
    return $result;
}

/**
 * Format nama hari dalam Bahasa Indonesia
 * @param string $date
 * @return string
 */
function formatHari($date) {
    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    
    $hariInggris = date('l', strtotime($date));
    return $hari[$hariInggris];
}

/**
 * Format angka ke Rupiah
 * @param int $angka
 * @return string
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Upload file
 * @param array $file $_FILES['field_name']
 * @param string $targetDir
 * @param array $allowedTypes
 * @param int $maxSize (dalam bytes)
 * @return array ['success' => bool, 'filename' => string, 'message' => string]
 */
function uploadFile($file, $targetDir, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'], $maxSize = 2097152) {
    $result = [
        'success' => false,
        'filename' => '',
        'message' => ''
    ];
    
    // Cek apakah ada error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'Error saat upload file';
        return $result;
    }
    
    // Cek ukuran file
    if ($file['size'] > $maxSize) {
        $result['message'] = 'Ukuran file terlalu besar. Maksimal ' . ($maxSize / 1048576) . 'MB';
        return $result;
    }
    
    // Cek ekstensi file
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedTypes)) {
        $result['message'] = 'Tipe file tidak diizinkan. Hanya: ' . implode(', ', $allowedTypes);
        return $result;
    }
    
    // Generate nama file unik
    $newFilename = uniqid() . '_' . time() . '.' . $fileExtension;
    $targetPath = $targetDir . '/' . $newFilename;
    
    // Buat direktori jika belum ada
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Pindahkan file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $result['success'] = true;
        $result['filename'] = $newFilename;
        $result['message'] = 'File berhasil diupload';
    } else {
        $result['message'] = 'Gagal memindahkan file';
    }
    
    return $result;
}

/**
 * Hapus file
 * @param string $filepath
 * @return bool
 */
function deleteFile($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Truncate text dengan kata utuh
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $lastSpace = strrpos($text, ' ');
    
    if ($lastSpace !== false) {
        $text = substr($text, 0, $lastSpace);
    }
    
    return $text . $suffix;
}

/**
 * Get profil desa
 * @return array|null
 */
function getProfilDesa() {
    return fetchOne("SELECT * FROM profil_desa LIMIT 1");
}

/**
 * Get berita terbaru
 * @param int $limit
 * @return array
 */
function getBeritaTerbaru($limit = 5) {
    return fetchAll(
        "SELECT * FROM berita WHERE status = 'published' ORDER BY tanggal_publish DESC LIMIT ?",
        [$limit],
        'i'
    );
}

/**
 * Get pengumuman aktif
 * @return array
 */
function getPengumumanAktif() {
    return fetchAll(
        "SELECT * FROM pengumuman 
         WHERE status = 'aktif' 
         AND (tanggal_selesai IS NULL OR tanggal_selesai >= CURDATE())
         ORDER BY jenis DESC, created_at DESC"
    );
}

/**
 * Get perangkat desa aktif
 * @return array
 */
function getPerangkatDesa() {
    return fetchAll(
        "SELECT * FROM perangkat_desa WHERE status = 'aktif' ORDER BY urutan ASC"
    );
}

/**
 * Generate nomor pengajuan otomatis
 * @return string
 */
function generateNomorPengajuan() {
    $tanggal = date('Ymd');
    
    $lastNumber = fetchOne(
        "SELECT nomor_pengajuan FROM pengajuan_surat 
         WHERE DATE(created_at) = CURDATE() 
         ORDER BY id DESC LIMIT 1"
    );
    
    if ($lastNumber) {
        $lastUrutan = (int)substr($lastNumber['nomor_pengajuan'], -3);
        $urutan = $lastUrutan + 1;
    } else {
        $urutan = 1;
    }
    
    return 'REG-' . $tanggal . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
}

/**
 * Cek apakah user sudah login
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Require login (redirect jika belum login)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('../admin/login.php');
    }
}

/**
 * Get nama badge status pengajuan
 * @param string $status
 * @return array ['class' => string, 'text' => string]
 */
function getStatusBadge($status) {
    $badges = [
        'pending' => ['class' => 'warning', 'text' => 'Pending'],
        'diproses' => ['class' => 'info', 'text' => 'Diproses'],
        'selesai' => ['class' => 'success', 'text' => 'Selesai'],
        'ditolak' => ['class' => 'danger', 'text' => 'Ditolak']
    ];
    
    return $badges[$status] ?? ['class' => 'secondary', 'text' => 'Unknown'];
}

/**
 * Get nama kategori berita dengan badge class
 * @param string $kategori
 * @return array
 */
function getKategoriBerita($kategori) {
    $kategoris = [
        'program_desa' => ['class' => 'success', 'text' => 'Program Desa'],
        'prestasi' => ['class' => 'warning', 'text' => 'Prestasi'],
        'pendidikan' => ['class' => 'info', 'text' => 'Pendidikan'],
        'infrastruktur' => ['class' => 'primary', 'text' => 'Infrastruktur'],
        'kesehatan' => ['class' => 'danger', 'text' => 'Kesehatan'],
        'umum' => ['class' => 'secondary', 'text' => 'Umum']
    ];
    
    return $kategoris[$kategori] ?? ['class' => 'secondary', 'text' => 'Lainnya'];
}

/**
 * Strip HTML tags tapi pertahankan <br> dan <p>
 * @param string $html
 * @return string
 */
function stripHtmlButKeepBreaks($html) {
    $html = str_replace(['<br>', '<br/>', '<br />'], "\n", $html);
    $html = str_replace('</p>', "\n\n", $html);
    $html = strip_tags($html);
    return $html;
}

/**
 * Pagination helper
 * @param int $totalRecords
 * @param int $recordsPerPage
 * @param int $currentPage
 * @return array
 */
function paginate($totalRecords, $recordsPerPage = 10, $currentPage = 1) {
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $recordsPerPage;
    
    return [
        'total_records' => $totalRecords,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'records_per_page' => $recordsPerPage,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

<?php
define('ADMIN_PAGE', true);
$page_title = 'Dashboard';

include 'includes/header.php';

// Get statistik
$stats = fetchOne("SELECT * FROM v_statistik_pengajuan");
$total_berita = fetchOne("SELECT COUNT(*) as total FROM berita WHERE status = 'published'");
$total_perangkat = fetchOne("SELECT COUNT(*) as total FROM perangkat_desa WHERE status = 'aktif'");
$pengumuman_aktif = fetchOne("SELECT COUNT(*) as total FROM pengumuman WHERE status = 'aktif'");

// Get pengajuan terbaru
$pengajuan_terbaru = fetchAll(
    "SELECT * FROM pengajuan_surat ORDER BY created_at DESC LIMIT 5"
);

// Get berita terbaru
$berita_terbaru = fetchAll(
    "SELECT * FROM berita ORDER BY created_at DESC LIMIT 5"
);
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo number_format($stats['pending'] ?? 0); ?></h3>
            <p>Pengajuan Pending</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-spinner"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo number_format($stats['diproses'] ?? 0); ?></h3>
            <p>Sedang Diproses</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo number_format($stats['selesai'] ?? 0); ?></h3>
            <p>Selesai</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo number_format($total_berita['total'] ?? 0); ?></h3>
            <p>Berita Published</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> Pengajuan Surat Terbaru</h3>
        <a href="pengajuan.php" class="btn btn-sm btn-primary">
            <i class="fas fa-list"></i> Lihat Semua
        </a>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Nama Pemohon</th>
                    <th>Jenis Surat</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pengajuan_terbaru)): ?>
                    <?php foreach ($pengajuan_terbaru as $p): 
                        $badge = getStatusBadge($p['status']);
                    ?>
                    <tr>
                        <td><strong><?php echo sanitize($p['nomor_pengajuan']); ?></strong></td>
                        <td><?php echo sanitize($p['nama_pemohon']); ?></td>
                        <td><?php echo ucwords(str_replace('_', ' ', $p['jenis_surat'])); ?></td>
                        <td><?php echo formatTanggal($p['created_at'], true); ?></td>
                        <td><span class="badge badge-<?php echo $badge['class']; ?>"><?php echo $badge['text']; ?></span></td>
                        <td>
                            <a href="pengajuan.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--gray-500);">
                            Belum ada pengajuan
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-newspaper"></i> Berita Terbaru</h3>
        <a href="berita.php" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Tambah Berita
        </a>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Tanggal Publish</th>
                    <th>Views</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($berita_terbaru)): ?>
                    <?php foreach ($berita_terbaru as $b): 
                        $kategori = getKategoriBerita($b['kategori']);
                    ?>
                    <tr>
                        <td><strong><?php echo sanitize($b['judul']); ?></strong></td>
                        <td><span class="badge badge-<?php echo $kategori['class']; ?>"><?php echo $kategori['text']; ?></span></td>
                        <td><?php echo formatTanggal($b['tanggal_publish']); ?></td>
                        <td><i class="far fa-eye"></i> <?php echo number_format($b['views']); ?></td>
                        <td><span class="badge badge-<?php echo $b['status'] === 'published' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($b['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--gray-500);">
                            Belum ada berita
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

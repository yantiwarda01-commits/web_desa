<?php
define('ADMIN_PAGE', true);
$page_title = 'Kelola Berita & Pengumuman';

include 'includes/header.php';

// Get all berita
$berita = fetchAll("SELECT * FROM berita ORDER BY created_at DESC");
?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-newspaper"></i> Daftar Berita</h3>
        <button class="btn btn-primary" onclick="alert('Fitur Tambah Berita sedang dalam pengembangan')">
            <i class="fas fa-plus"></i> Tambah Berita
        </button>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Tanggal Publish</th>
                    <th>Views</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($berita)): ?>
                    <?php foreach ($berita as $b): 
                        $kategori = getKategoriBerita($b['kategori']);
                    ?>
                    <tr>
                        <td><strong><?php echo sanitize($b['judul']); ?></strong><br>
                            <small style="color: var(--gray-500);"><?php echo truncateText(stripHtmlButKeepBreaks($b['konten']), 80); ?></small>
                        </td>
                        <td><span class="badge badge-<?php echo $kategori['class']; ?>"><?php echo $kategori['text']; ?></span></td>
                        <td><?php echo sanitize($b['penulis'] ?? '-'); ?></td>
                        <td><?php echo formatTanggal($b['tanggal_publish']); ?></td>
                        <td><i class="far fa-eye"></i> <?php echo number_format($b['views']); ?></td>
                        <td><span class="badge badge-<?php echo $b['status'] === 'published' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($b['status']); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="alert('Fitur Edit sedang dalam pengembangan')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Yakin hapus?')) alert('Fitur Delete sedang dalam pengembangan')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--gray-500);">
                            Belum ada berita
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="alert alert-info" style="margin-top: 1.5rem;">
    <i class="fas fa-info-circle"></i>
    <div>
        <strong>Note:</strong> Halaman ini adalah placeholder. Fitur CRUD Berita lengkap akan dikembangkan di versi selanjutnya.
        Anda bisa menambah/edit berita langsung melalui database untuk saat ini.
    </div>
</div>

<?php include 'includes/footer.php'; ?>

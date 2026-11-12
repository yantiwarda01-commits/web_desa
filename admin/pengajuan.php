<?php
define('ADMIN_PAGE', true);
$page_title = 'Kelola Pengajuan Surat';

include 'includes/header.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int)$_POST['id'];
    $status = sanitize($_POST['status']);
    $catatan = sanitize($_POST['catatan_admin'] ?? '');
    
    $updated = executeQuery(
        "UPDATE pengajuan_surat SET status = ?, catatan_admin = ?, tanggal_selesai = IF(? = 'selesai', CURDATE(), NULL) WHERE id = ?",
        [$status, $catatan, $status, $id],
        'sssi'
    );
    
    if ($updated) {
        setFlashMessage('success', 'Status pengajuan berhasil diupdate');
    } else {
        setFlashMessage('error', 'Gagal mengupdate status');
    }
    
    redirect('pengajuan.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $deleted = executeQuery("DELETE FROM pengajuan_surat WHERE id = ?", [$id], 'i');
    
    if ($deleted) {
        setFlashMessage('success', 'Pengajuan berhasil dihapus');
    } else {
        setFlashMessage('error', 'Gagal menghapus pengajuan');
    }
    
    redirect('pengajuan.php');
}

// Filter
$filter_status = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$where = "1=1";
$params = [];
$types = '';

if ($filter_status !== 'all') {
    $where .= " AND status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if (!empty($search)) {
    $where .= " AND (nomor_pengajuan LIKE ? OR nama_pemohon LIKE ? OR nik LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

// Get data
$pengajuan = fetchAll(
    "SELECT * FROM pengajuan_surat WHERE $where ORDER BY created_at DESC",
    $params,
    $types
);

// Get detail if ID provided
$detail = null;
if (isset($_GET['id'])) {
    $detail = fetchOne("SELECT * FROM pengajuan_surat WHERE id = ?", [(int)$_GET['id']], 'i');
}

?>

<?php if ($detail): ?>
<!-- Modal Detail -->
<div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: white; border-radius: 1rem; max-width: 700px; width: 100%; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.2rem; font-weight: 700;">Detail Pengajuan</h3>
            <a href="pengajuan.php" style="color: var(--gray-500); font-size: 1.5rem;">&times;</a>
        </div>
        
        <div style="padding: 1.5rem;">
            <table style="width: 100%; font-size: 0.9rem;">
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600; width: 35%;">No. Pengajuan</td>
                    <td><?php echo sanitize($detail['nomor_pengajuan']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">Jenis Surat</td>
                    <td><?php echo ucwords(str_replace('_', ' ', $detail['jenis_surat'])); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">NIK</td>
                    <td><?php echo sanitize($detail['nik']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">Nama Pemohon</td>
                    <td><strong><?php echo sanitize($detail['nama_pemohon']); ?></strong></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">TTL</td>
                    <td><?php echo sanitize($detail['tempat_lahir']) . ', ' . formatTanggal($detail['tanggal_lahir']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">Jenis Kelamin</td>
                    <td><?php echo $detail['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">Pekerjaan</td>
                    <td><?php echo sanitize($detail['pekerjaan'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">Alamat</td>
                    <td><?php echo nl2br(sanitize($detail['alamat'])); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">No. Telepon</td>
                    <td><?php echo sanitize($detail['no_telepon']); ?></td>
                </tr>
                <?php if (!empty($detail['email'])): ?>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">Email</td>
                    <td><?php echo sanitize($detail['email']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">Keperluan</td>
                    <td><?php echo nl2br(sanitize($detail['keperluan'])); ?></td>
                </tr>
                <?php if (!empty($detail['keterangan_tambahan'])): ?>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">Keterangan Tambahan</td>
                    <td><?php echo nl2br(sanitize($detail['keterangan_tambahan'])); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">Tanggal Pengajuan</td>
                    <td><?php echo formatTanggal($detail['created_at'], true); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 600;">Status</td>
                    <td><?php $b = getStatusBadge($detail['status']); ?>
                        <span class="badge badge-<?php echo $b['class']; ?>"><?php echo $b['text']; ?></span>
                    </td>
                </tr>
            </table>
            
            <form method="POST" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
                <input type="hidden" name="id" value="<?php echo $detail['id']; ?>">
                
                <div class="form-group">
                    <label>Update Status</label>
                    <select name="status" class="form-control">
                        <option value="pending" <?php echo $detail['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="diproses" <?php echo $detail['status'] === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                        <option value="selesai" <?php echo $detail['status'] === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="ditolak" <?php echo $detail['status'] === 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Catatan Admin</label>
                    <textarea name="catatan_admin" class="form-control" rows="3"><?php echo sanitize($detail['catatan_admin'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" name="update_status" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Status
                </button>
                <a href="pengajuan.php" class="btn btn-secondary">Tutup</a>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Filter & Search -->
<div class="card">
    <form method="GET" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <label>Cari</label>
            <input type="text" name="search" class="form-control" placeholder="No. Pengajuan, Nama, atau NIK" value="<?php echo htmlspecialchars($search); ?>" />
        </div>
        
        <div class="form-group" style="margin-bottom: 0; min-width: 180px;">
            <label>Filter Status</label>
            <select name="status" class="form-control">
                <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>Semua Status</option>
                <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="diproses" <?php echo $filter_status === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                <option value="selesai" <?php echo $filter_status === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                <option value="ditolak" <?php echo $filter_status === 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary" style="margin-bottom: 0;">
            <i class="fas fa-search"></i> Cari
        </button>
        
        <?php if (!empty($search) || $filter_status !== 'all'): ?>
        <a href="pengajuan.php" class="btn btn-secondary" style="margin-bottom: 0;">
            <i class="fas fa-times"></i> Reset
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Daftar Pengajuan Surat (<?php echo count($pengajuan); ?>)</h3>
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
                <?php if (!empty($pengajuan)): ?>
                    <?php foreach ($pengajuan as $p): 
                        $badge = getStatusBadge($p['status']);
                    ?>
                    <tr>
                        <td><strong><?php echo sanitize($p['nomor_pengajuan']); ?></strong></td>
                        <td><?php echo sanitize($p['nama_pemohon']); ?><br>
                            <small style="color: var(--gray-500);">NIK: <?php echo sanitize($p['nik']); ?></small>
                        </td>
                        <td><?php echo ucwords(str_replace('_', ' ', $p['jenis_surat'])); ?></td>
                        <td><?php echo formatTanggal($p['created_at']); ?><br>
                            <small style="color: var(--gray-500);"><?php echo date('H:i', strtotime($p['created_at'])); ?> WIB</small>
                        </td>
                        <td><span class="badge badge-<?php echo $badge['class']; ?>"><?php echo $badge['text']; ?></span></td>
                        <td>
                            <a href="?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-secondary" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" title="Hapus" data-confirm="Yakin ingin menghapus pengajuan ini?">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--gray-500);">
                            <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 0.5rem;"></i>
                            Tidak ada pengajuan ditemukan
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

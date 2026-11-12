<?php
define('ADMIN_PAGE', true);
$page_title = 'Kelola Profil Desa';

include 'includes/header.php';

$profil = getProfilDesa();
$perangkat = getPerangkatDesa();
?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-building"></i> Profil Desa</h3>
        <button class="btn btn-primary" onclick="alert('Fitur Edit Profil sedang dalam pengembangan')">
            <i class="fas fa-edit"></i> Edit Profil
        </button>
    </div>
    
    <div style="padding: 1rem;">
        <table style="width: 100%; font-size: 0.9rem;">
            <tr>
                <td style="padding: 0.5rem; font-weight: 600; width: 25%;">Nama Desa</td>
                <td style="padding: 0.5rem;"><?php echo sanitize($profil['nama_desa'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Kecamatan</td>
                <td style="padding: 0.5rem;"><?php echo sanitize($profil['kecamatan'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Kabupaten</td>
                <td style="padding: 0.5rem;"><?php echo sanitize($profil['kabupaten'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Provinsi</td>
                <td style="padding: 0.5rem;"><?php echo sanitize($profil['provinsi'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Alamat Kantor</td>
                <td style="padding: 0.5rem;"><?php echo nl2br(sanitize($profil['alamat_kantor'] ?? '-')); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Telepon</td>
                <td style="padding: 0.5rem;"><?php echo sanitize($profil['telepon'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Email</td>
                <td style="padding: 0.5rem;"><?php echo sanitize($profil['email'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Luas Wilayah</td>
                <td style="padding: 0.5rem;"><?php echo sanitize($profil['luas_wilayah'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Jumlah Penduduk</td>
                <td style="padding: 0.5rem;"><?php echo number_format($profil['jumlah_penduduk'] ?? 0); ?> jiwa</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Jumlah KK</td>
                <td style="padding: 0.5rem;"><?php echo number_format($profil['jumlah_kk'] ?? 0); ?> KK</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">RT / RW</td>
                <td style="padding: 0.5rem;"><?php echo $profil['jumlah_rt'] ?? 0; ?> RT / <?php echo $profil['jumlah_rw'] ?? 0; ?> RW</td>
            </tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users"></i> Perangkat Desa (<?php echo count($perangkat); ?>)</h3>
        <button class="btn btn-primary" onclick="alert('Fitur Tambah Perangkat sedang dalam pengembangan')">
            <i class="fas fa-plus"></i> Tambah Perangkat
        </button>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Pendidikan</th>
                    <th>Telepon</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($perangkat)): ?>
                    <?php foreach ($perangkat as $p): ?>
                    <tr>
                        <td><?php echo sanitize($p['nip'] ?? '-'); ?></td>
                        <td><strong><?php echo sanitize($p['nama_lengkap']); ?></strong></td>
                        <td><?php echo sanitize($p['jabatan']); ?></td>
                        <td><?php echo sanitize($p['pendidikan'] ?? '-'); ?></td>
                        <td><?php echo sanitize($p['telepon'] ?? '-'); ?></td>
                        <td><span class="badge badge-<?php echo $p['status'] === 'aktif' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="alert('Fitur Edit sedang dalam pengembangan')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--gray-500);">Belum ada data perangkat</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    <div>
        <strong>Note:</strong> Halaman ini adalah placeholder. Fitur CRUD Profil & Perangkat Desa lengkap akan dikembangkan di versi selanjutnya.
        Anda bisa menambah/edit data langsung melalui database untuk saat ini.
    </div>
</div>

<?php include 'includes/footer.php'; ?>

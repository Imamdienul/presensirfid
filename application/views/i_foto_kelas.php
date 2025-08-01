<?php $this->load->view('include/header.php'); ?>

<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group float-right">
                        <ol class="breadcrumb hide-phone p-0 m-0">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?= base_url('kelas'); ?>">Daftar Kelas</a></li>
                            <li class="breadcrumb-item"><a href="<?= base_url('kelas/lihat_kelas?id_kelas=' . $kelas->id); ?>"><?= $kelas->kelas; ?></a></li>
                            <li class="breadcrumb-item active">Foto Kelas</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Foto Kelas: <?= $kelas->kelas; ?></h4>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <?= $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <?= $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 col-xl-12">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mt-0 header-title">Galeri Foto Kelas</h4>
                            <div>
                                <a href="<?= base_url('kelas/lihat_kelas?id_kelas=' . $kelas->id); ?>" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Kembali ke Detail Kelas
                                </a>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadFotoModal">
                                    <i class="fa fa-upload"></i> Upload Foto Baru
                                </button>
                            </div>
                        </div>

                        <?php if (empty($foto_files)): ?>
                            <div class="text-center p-5">
                                <i class="fa fa-images fa-5x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada foto di kelas ini</h5>
                                <p class="text-muted">Klik tombol "Upload Foto Baru" untuk menambahkan foto</p>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <small class="text-muted">Total: <?= count($foto_files); ?> foto</small>
                            </div>
                            
                            <div class="row" id="foto-gallery">
                                <?php foreach ($foto_files as $index => $foto): ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4" data-filename="<?= $foto['filename']; ?>">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <img src="<?= $foto['url']; ?>" 
                                                     class="img-fluid mb-2 foto-thumbnail" 
                                                     style="height: 200px; width: 100%; object-fit: cover; cursor: pointer;"
                                                     onclick="openModal('<?= $foto['url']; ?>', '<?= $foto['filename']; ?>')">
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted"><?= substr($foto['filename'], 0, 20); ?><?= strlen($foto['filename']) > 20 ? '...' : ''; ?></small>
                                                    <button class="btn btn-danger btn-sm" onclick="hapusFoto('<?= $foto['filename']; ?>')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <small class="text-muted d-block"><?= formatBytes($foto['size']); ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Foto -->
<div class="modal fade" id="uploadFotoModal" tabindex="-1" role="dialog" aria-labelledby="uploadFotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadFotoModalLabel">Upload Foto Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadFotoForm" enctype="multipart/form-data">
                    <input type="hidden" name="id_kelas" value="<?= $kelas->id; ?>">
                    
                    <div class="form-group">
                        <label for="foto_files">Pilih Foto-foto:</label>
                        <input type="file" class="form-control-file" id="foto_files" name="foto_files[]" multiple accept="image/*" required>
                        <small class="form-text text-muted">
                            Format yang diizinkan: JPG, JPEG, PNG, GIF, BMP. Maksimal 5MB per file.
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <div id="file_preview" class="row"></div>
                    </div>
                    
                    <div class="progress" id="upload_progress" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    
                    <div id="upload_result" class="mt-3"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnUpload">Upload Foto</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Foto -->
<div class="modal fade" id="viewFotoModal" tabindex="-1" role="dialog" aria-labelledby="viewFotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewFotoModalLabel">Preview Foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" style="max-height: 500px;">
                <p id="modalFilename" class="mt-2 text-muted"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <a id="downloadLink" href="" download="" class="btn btn-success">
                    <i class="fa fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<?php
function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}
?>


<?php $this->load->view('include/footer.php'); ?>
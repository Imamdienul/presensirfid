<?php $this->load->view('include/header.php'); ?>

<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group float-right">
                        <ol class="breadcrumb hide-phone p-0 m-0">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Daftar Kelas</a></li>
                            <li class="breadcrumb-item active"><?= isset($kelas->kelas) ? $kelas->kelas : 'Detail Kelas'; ?></li>
                        </ol>
                    </div>
                    <h4 class="page-title">Detail Kelas</h4>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <?= $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 col-xl-12">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mt-0 header-title">Data Murid Kelas: <?= isset($kelas->kelas) ? $kelas->kelas : '-'; ?></h4>
                            <div>
                                <?php if(isset($kelas->id)): ?>
                                    <a href="<?= base_url(); ?>kelas/rekap_absen/<?= $kelas->id; ?>" class="btn btn-primary">Rekap Absen</a>
                                    
                                    <!-- Tombol Upload Foto Kelas -->
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#uploadFotoModal">
                                        <i class="fa fa-upload"></i> Upload Foto Kelas
                                    </button>
                                    
                                    <!-- Tombol Lihat Foto Kelas -->
                                    <a href="<?= base_url(); ?>kelas/lihat_foto_kelas/<?= $kelas->id; ?>" class="btn btn-info">
                                        <i class="fa fa-images"></i> Lihat Foto Kelas
                                    </a>
                                    
                                    <form method="post" action="<?= base_url('card/generate_cards'); ?>" style="display:inline;">
                                        <input type="hidden" name="cetak_semua" value="1">
                                        <input type="hidden" name="kelas_id" value="<?= $kelas->id ?>">
                                        <button type="submit" class="btn btn-success">Cetak Semua Kartu</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>

                        <table id="datatable-buttons" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Kode Presensi Manual</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($murid)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Data tidak ditemukan</td>
                                    </tr>
                                <?php else: ?>
                                    <?php 
                                    $no = 1;
                                    foreach ($murid as $row): 
                                        if (!empty($row->nama)): 
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row->nama; ?></td>
                                            <td><?= $row->kelas; ?></td>
                                            <td><?= $row->id_siswa; ?></td>
                                            
                                            <td>
                                                <a href="<?= base_url(); ?>kelas/detail_murid/<?= $row->id_siswa; ?>" class="btn btn-success btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url(); ?>kelas/hapus_murid/<?= $row->id_siswa; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus murid ini?');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Foto Kelas -->
<div class="modal fade" id="uploadFotoModal" tabindex="-1" role="dialog" aria-labelledby="uploadFotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadFotoModalLabel">Upload Foto ke Kelas: <?= isset($kelas->kelas) ? $kelas->kelas : '-'; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadFotoForm" enctype="multipart/form-data">
                    <input type="hidden" name="id_kelas" value="<?= isset($kelas->id) ? $kelas->id : ''; ?>">
                    
                    <div class="form-group">
                        <label for="foto_files">Pilih Foto-foto (Bisa pilih banyak file sekaligus):</label>
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



<?php $this->load->view('include/footer.php'); ?>
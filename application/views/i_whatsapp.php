<?php
$this->load->view('include/header.php');
?>
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group float-right">
                        <ol class="breadcrumb hide-phone p-0 m-0">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">whatsapp</a></li>
                            <li class="breadcrumb-item active">Konfigurasi WhatsApp</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Konfigurasi WhatsApp</h4>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row">
            <div class="col-md-12 col-xl-12">
                <div class="card mb-30">
                    <div class="card-body">
                        <div class="general-label">
                            <?php echo $this->session->flashdata('pesan'); ?>
                            
                            <!-- Form Konfigurasi WhatsApp -->
                            <form action="<?= base_url(); ?>whatsapp/updateWhatsappConfig" method="post" id="whatsappForm">
                                
                                <!-- API Key -->
                                <div class="row mb-3">
                                    <label for="api_key" class="col-sm-2 col-form-label">API Key WhatsApp</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" name="api_key" value="<?= isset($whatsapp_config->api_key) ? $whatsapp_config->api_key : ''; ?>" placeholder="Masukkan API Key WhatsApp" id="api_key" required>
                                        <small class="form-text text-muted">API Key dari penyedia layanan WhatsApp Gateway</small>
                                    </div>
                                </div>

                                <!-- API URL -->
                                <div class="row mb-3">
                                    <label for="api_url" class="col-sm-2 col-form-label">API URL</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="url" name="api_url" value="<?= isset($whatsapp_config->api_url) ? $whatsapp_config->api_url : 'https://whatsapp.gisaka.media/api/send-message'; ?>" placeholder="https://whatsapp.gisaka.media/api/send-message" id="api_url" required>
                                        <small class="form-text text-muted">URL endpoint untuk mengirim pesan WhatsApp</small>
                                    </div>
                                </div>

                                <!-- Status Aktif -->
                                <div class="row mb-3">
                                    <label for="status" class="col-sm-2 col-form-label">Status</label>
                                    <div class="col-sm-10">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="status" id="status" value="1" <?= (isset($whatsapp_config->status) && $whatsapp_config->status == 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="status">
                                                Aktifkan Notifikasi WhatsApp
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">Centang untuk mengaktifkan pengiriman notifikasi WhatsApp</small>
                                    </div>
                                </div>

                                <hr>

                                <!-- Test WhatsApp -->
                                <h5>Test Konfigurasi WhatsApp</h5>
                                <div class="row mb-3">
                                    <label for="test_phone" class="col-sm-2 col-form-label">Nomor HP Test</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" name="test_phone" placeholder="08123456789" id="test_phone">
                                        <small class="form-text text-muted">Nomor HP untuk test pengiriman (format: 08123456789)</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="test_message" class="col-sm-2 col-form-label">Pesan Test</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" name="test_message" rows="3" placeholder="Pesan test untuk WhatsApp" id="test_message">Halo, ini adalah pesan test dari sistem absensi sekolah. Jika Anda menerima pesan ini, konfigurasi WhatsApp sudah berhasil!</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-10">
                                        <button type="button" class="btn btn-info" id="testWhatsappBtn">
                                            <i class="fas fa-paper-plane"></i> Test Kirim WhatsApp
                                        </button>
                                        <div id="testResult" class="mt-2"></div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Submit Button -->
                                <div class="row mb-3">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Simpan Konfigurasi
                                        </button>
                                        <button type="reset" class="btn btn-secondary">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-30">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Konfigurasi</h5>
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Petunjuk Penggunaan:</h6>
                            <ul class="mb-0">
                                <li><strong>API Key:</strong> Dapatkan dari penyedia layanan WhatsApp Gateway Anda</li>
                                <li><strong>API URL:</strong> URL endpoint yang disediakan oleh layanan WhatsApp Gateway</li>
                                <li><strong>Status:</strong> Aktifkan untuk mengirim notifikasi WhatsApp secara otomatis</li>
                                <li><strong>Test:</strong> Gunakan fitur test untuk memastikan konfigurasi berfungsi dengan baik</li>
                            </ul>
                        </div>
                        
                        <?php if (isset($whatsapp_config) && $whatsapp_config): ?>
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Status Konfigurasi:</h6>
                            <p class="mb-1"><strong>Status:</strong> 
                                <?= $whatsapp_config->status == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Nonaktif</span>'; ?>
                            </p>
                            <p class="mb-1"><strong>Terakhir Diperbarui:</strong> 
                                <?= isset($whatsapp_config->updated_at) ? date('d/m/Y H:i:s', strtotime($whatsapp_config->updated_at)) : '-'; ?>
                            </p>
                            <p class="mb-0"><strong>API URL:</strong> <?= $whatsapp_config->api_url; ?></p>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Belum Dikonfigurasi</h6>
                            <p class="mb-0">Konfigurasi WhatsApp belum diatur. Silakan isi form di atas untuk mengaktifkan notifikasi WhatsApp.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Konfirmasi Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Penyimpanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menyimpan konfigurasi WhatsApp ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmButton">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view('include/footer.php');
?>

<script>
$(document).ready(function() {
    // Handle form submission with confirmation
    $('#whatsappForm').on('submit', function(event) {
        event.preventDefault();
        $('#confirmationModal').modal('show');
    });

    // Handle confirmation button click
    $('#confirmButton').click(function() {
        $('#whatsappForm').off('submit').submit();
    });

    // Test WhatsApp functionality
    $('#testWhatsappBtn').click(function() {
        var phone = $('#test_phone').val();
        var message = $('#test_message').val();
        
        if (!phone || !message) {
            $('#testResult').html('<div class="alert alert-danger">Nomor HP dan pesan tidak boleh kosong!</div>');
            return;
        }
        
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengirim...');
        
        $.ajax({
            url: '<?= base_url(); ?>whatsapp/testWhatsapp',
            type: 'POST',
            data: {
                test_phone: phone,
                test_message: message
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#testResult').html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    $('#testResult').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#testResult').html('<div class="alert alert-danger">Terjadi kesalahan saat mengirim pesan test</div>');
            },
            complete: function() {
                $('#testWhatsappBtn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Test Kirim WhatsApp');
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
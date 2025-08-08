<?php $this->load->view('include/header.php'); ?>

<div class="page-content-wrapper">
    <div class="container-fluid">
        <!-- Page title and breadcrumb -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group float-right">
                        <ol class="breadcrumb hide-phone p-0 m-0">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Daftar Kelas</a></li>
                            <li class="breadcrumb-item active">Detail Murid</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Detail Murid</h4>
                </div>
            </div>
        </div>
        <!-- End page title and breadcrumb -->

        <div class="row">
            <!-- Details column -->
            <div class="col-md-9 col-xl-9">
                <div class="card m-b-30">
                    <div class="card-body">
                        <h1 class="mt-0 header-title d-flex justify-content-between align-items-center">
                            <?= $murid->nama; ?>
                            <a href="<?= base_url() ?>/kelas/edit_siswa/<?= $murid->id_siswa ?>" class="btn btn-info btn-sm">
                                Edit <i class="fa fa-pencil"></i>
                            </a>
                        </h1>
                        <div class="text-center mb-3">
                            <img src="<?= base_url(); ?>./uploads/foto_siswa/<?= $murid->foto; ?>" class="img-circle" width="auto" height="100px" alt="User Image">
                        </div>
                        
                        <!-- Alert untuk pesan -->
                        <div id="phone-message" style="display: none;" class="alert"></div>
                        <div id="whatsapp-message" style="display: none;" class="alert"></div>
                        
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th scope="row">NISN:</th>
                                        <td><?= $murid->nisn; ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">NIK:</th>
                                        <td><?= $murid->nik; ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">NOMOR TELP ORANG TUA:</th>
                                        <td>
                                            <!-- Form untuk edit nomor telepon -->
                                            <form id="phone-form" style="display: inline-block;">
                                                <div class="input-group" style="width: 600px;">
                                                    <input type="hidden" id="murid-id" value="<?= $murid->id_siswa ?>">
                                                    <input type="text" id="phone-input" class="form-control" value="<?= $murid->telp; ?>" maxlength="15">
                                                    <div class="input-group-append">
                                                        <button type="button" id="save-phone" class="btn btn-success btn-sm">
                                                            <i class="fa fa-save"></i> Save
                                                        </button>
                                                        <!-- Tombol WhatsApp dengan dropdown -->
                                                        <div class="btn-group" role="group">
                                                            <button type="button" id="whatsapp-btn" class="btn btn-whatsapp btn-sm" onclick="kirimWhatsappVerifikasi('<?= $murid->telp; ?>', '<?= $murid->nama; ?>')">
                                                                <i class="fab fa-whatsapp"></i> Verifikasi Ortu
                                                            </button>
                                                            <button type="button" class="btn btn-whatsapp btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <span class="sr-only">Toggle Dropdown</span>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item" href="#" onclick="kirimWhatsappVerifikasi('<?= $murid->telp; ?>', '<?= $murid->nama; ?>')">
                                                                    <i class="fab fa-whatsapp"></i> Verifikasi Orang Tua
                                                                </a>
                                                                <a class="dropdown-item" href="#" onclick="showCustomMessageModal()">
                                                                    <i class="fa fa-comment"></i> Pesan Custom
                                                                </a>
                                                                <a class="dropdown-item" href="#" onclick="kirimWhatsappAbsensi('<?= $murid->telp; ?>', '<?= $murid->nama; ?>')">
                                                                    <i class="fa fa-calendar"></i> Notifikasi Absensi
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tempat, Tanggal Lahir:</th>
                                        <td><?=$murid->tempat_lahir . ", " . $murid->tanggal_lahir; ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Kelas:</th>
                                        <td><?= $murid->kelas; ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Alamat:</th>
                                        <td><?= $murid->alamat; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End details column -->
        </div>
        <!-- End murid details and photos -->

        <div class="text-center">
            <h4 class="header-title" style="margin-bottom: 20px; text-transform: uppercase;">Kartu Siswa</h4>
        </div>
        <div class="text-center">
            <!-- Kartu dengan ukuran standar KTP -->
            <div class="card kartu-siswa">
                <!-- Barcode NISN -->
                <div class="barcode">
                    <img src="data:image/png;base64,<?= $barcode; ?>" alt="Barcode">
                </div>
                
                <!-- Foto Murid -->
                <div class="photo" style="background-image: url('<?= base_url('uploads/foto_siswa/'.$murid->foto); ?>');"></div>
                
                <!-- Detail Murid -->
                <div class="details">
                    <table>
                        <tr>
                            <th><strong>Nama</strong></th>
                            <td><strong>: <?= $murid->nama; ?></strong></td>
                        </tr>
                        <tr>
                            <th><strong>TTL</strong></th>
                            <td><strong>: <?= $murid->tempat_lahir . ", " . $murid->tanggal_lahir; ?></strong></td>
                        </tr>
                       
                        <tr>
                            <th><strong>NISN</strong></th>
                            <td><strong>: <?= $murid->nisn; ?></strong></td>
                        </tr>
                        <tr>
                            <th><strong>Alamat</strong></th>
                            <td><strong>: <?= $murid->alamat; ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- End Kartu Siswa Section -->
    </div>
</div>

<!-- Modal untuk pesan custom -->
<div class="modal fade" id="customMessageModal" tabindex="-1" role="dialog" aria-labelledby="customMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customMessageModalLabel">Kirim Pesan WhatsApp Custom</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="customMessage">Pesan:</label>
                    <textarea class="form-control" id="customMessage" rows="5" placeholder="Tulis pesan Anda di sini..."></textarea>
                </div>
                <div class="form-group">
                    <small class="text-muted">
                        Pesan akan dikirim ke: <strong><?= $murid->telp; ?></strong><br>
                        Orang tua dari: <strong><?= $murid->nama; ?></strong>
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-whatsapp" onclick="kirimWhatsappCustom()">
                    <i class="fab fa-whatsapp"></i> Kirim WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .page-content-wrapper {
        max-height: 100vh;
        overflow-y: auto;
        padding: 10px;
        margin-top: 70px;
    }

    /* Style untuk tombol WhatsApp */
    .btn-whatsapp {
        background-color: #25D366;
        border-color: #25D366;
        color: white;
    }

    .btn-whatsapp:hover {
        background-color: #128C7E;
        border-color: #128C7E;
        color: white;
    }

    .btn-whatsapp:focus, .btn-whatsapp.focus {
        box-shadow: 0 0 0 0.2rem rgba(37, 211, 102, 0.5);
    }

    .dropdown-menu .dropdown-item {
        padding: 8px 16px;
    }

    .dropdown-menu .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .card.kartu-siswa {
        position: relative;
        width: 85.6mm;
        height: 54mm;
        background-image: url('<?php echo base_url(get_settings('path_template_card')); ?>');
        background-size: cover;
        background-position: center;
        border-radius: 8px;
        border: 2px solid #000;
        box-sizing: border-box;
        margin: 0 auto;
        page-break-inside: avoid;
    }

    .barcode {
        position: absolute;
        top: 68px;
        left: 10px;
        width: 120px;
        height: 30px;
    }

    .barcode img {
        width: 100%;
        height: auto;
    }

    .photo {
        position: absolute;
        top: 95px;
        left: 23px;
        width: 60px;
        height: 80px;
        background-size: cover;
        background-position: center;
        border-radius: 5px;
        border: 2px solid #fff;
    }

    .details {
        position: absolute;
        top: 99px;
        left: 100px;
        color: black;
        text-align: left;
        font-family: Arial, sans-serif;
        text-transform: uppercase;
        line-height: 1;
    }

    .details table {
        border-collapse: collapse;
        width: 100%;
    }

    .details th, .details td {
        padding: 2px;
        text-align: left;
        font-size: 8px;
    }

    .details th {
        font-weight: bold;
    }

    @media print {
        body {
            margin: 0;
            padding: 0;
        }

        .page-content-wrapper {
            width: 100%;
            height: 100%;
            overflow-y: visible;
        }

        .card.kartu-siswa {
            page-break-inside: avoid;
            margin-bottom: 0px;
            background-image: url('<?= base_url('assets/images/template.png'); ?>') !important;
            background-size: cover;
            background-position: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .photo {
            background-image: url('<?= base_url('uploads/foto_siswa/'.$murid->foto); ?>') !important;
            background-size: cover;
            background-position: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @page {
            margin: 0;
        }
    }
</style>

<script>
// Fungsi untuk mengirim WhatsApp verifikasi orang tua
function kirimWhatsappVerifikasi(nomorTelp, namaSiswa) {
    if (!nomorTelp || nomorTelp.trim() === '') {
        showAlert('whatsapp-message', 'danger', 'Nomor telepon orang tua belum diisi!');
        return;
    }
    
    const message = `Selamat pagi/siang/sore. Saya dari sekolah ingin mengkonfirmasi, apakah benar ini adalah nomor orang tua dari ${namaSiswa}? 

Nomor ini akan digunakan untuk notifikasi absensi dan informasi penting terkait siswa. 

Mohon konfirmasinya. Terima kasih.`;
    
    kirimWhatsappAPI(nomorTelp, message, 'Pesan verifikasi berhasil dikirim!');
}

// Fungsi untuk mengirim WhatsApp notifikasi absensi
function kirimWhatsappAbsensi(nomorTelp, namaSiswa) {
    if (!nomorTelp || nomorTelp.trim() === '') {
        showAlert('whatsapp-message', 'danger', 'Nomor telepon orang tua belum diisi!');
        return;
    }
    
    const today = new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const message = `Yth. Orang tua/wali ${namaSiswa},

Ini adalah notifikasi absensi untuk hari ${today}.

Mohon pastikan ${namaSiswa} hadir tepat waktu di sekolah.

Terima kasih atas perhatiannya.

Salam,
Tim Sekolah`;
    
    kirimWhatsappAPI(nomorTelp, message, 'Notifikasi absensi berhasil dikirim!');
}

// Fungsi untuk menampilkan modal pesan custom
function showCustomMessageModal() {
    $('#customMessageModal').modal('show');
}

// Fungsi untuk mengirim WhatsApp dengan pesan custom
function kirimWhatsappCustom() {
    const customMessage = document.getElementById('customMessage').value.trim();
    const nomorTelp = '<?= $murid->telp; ?>';
    
    if (!customMessage) {
        alert('Pesan tidak boleh kosong!');
        return;
    }
    
    if (!nomorTelp || nomorTelp.trim() === '') {
        alert('Nomor telepon orang tua belum diisi!');
        return;
    }
    
    $('#customMessageModal').modal('hide');
    kirimWhatsappAPI(nomorTelp, customMessage, 'Pesan custom berhasil dikirim!');
}

// Fungsi utama untuk mengirim WhatsApp melalui API
function kirimWhatsappAPI(nomorTelp, message, successMessage) {
    // Show loading
    showAlert('whatsapp-message', 'info', 'Mengirim pesan WhatsApp...');
    
    // Format nomor telepon
    let phoneNumber = nomorTelp.replace(/\D/g, '');
    if (phoneNumber.startsWith('0')) {
        phoneNumber = '62' + phoneNumber.substring(1);
    } else if (!phoneNumber.startsWith('62')) {
        phoneNumber = '62' + phoneNumber;
    }
    
    // Kirim request ke controller
    $.ajax({
        url: '<?= base_url('kelas/send_whatsapp') ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            phone: phoneNumber,
            message: message
        },
        success: function(response) {
            if (response.status === 'success') {
                showAlert('whatsapp-message', 'success', successMessage);
            } else {
                showAlert('whatsapp-message', 'danger', response.message || 'Gagal mengirim pesan WhatsApp');
            }
        },
        error: function(xhr, status, error) {
            showAlert('whatsapp-message', 'danger', 'Terjadi kesalahan saat mengirim pesan');
        }
    });
}

// Fungsi untuk menampilkan alert
function showAlert(elementId, type, message) {
    const alertElement = document.getElementById(elementId);
    alertElement.className = `alert alert-${type}`;
    alertElement.textContent = message;
    alertElement.style.display = 'block';
    
    // Hide alert after 5 seconds
    setTimeout(() => {
        alertElement.style.display = 'none';
    }, 5000);
}

// Update fungsi save phone untuk mengupdate tombol WhatsApp juga
document.addEventListener('DOMContentLoaded', function() {
    const savePhoneBtn = document.getElementById('save-phone');
    const phoneInput = document.getElementById('phone-input');
    
    if (savePhoneBtn) {
        savePhoneBtn.addEventListener('click', function() {
            const muridId = document.getElementById('murid-id').value;
            const newPhone = phoneInput.value.trim();
            
            if (!newPhone) {
                showAlert('phone-message', 'danger', 'Nomor telepon tidak boleh kosong!');
                return;
            }
            
            // Validasi format nomor
            const cleanPhone = newPhone.replace(/[^0-9]/g, '');
            if (cleanPhone.length < 10 || cleanPhone.length > 15) {
                showAlert('phone-message', 'danger', 'Format nomor telepon tidak valid (10-15 digit)');
                return;
            }
            
            // Show loading
            showAlert('phone-message', 'info', 'Menyimpan nomor telepon...');
            savePhoneBtn.disabled = true;
            
            $.ajax({
                url: '<?= base_url('kelas/update_phone') ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    id_siswa: muridId,
                    telp: newPhone
                },
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('phone-message', 'success', response.message);
                        
                        // Update onclick attributes for WhatsApp buttons
                        const namaSiswa = '<?= $murid->nama; ?>';
                        document.getElementById('whatsapp-btn').setAttribute('onclick', `kirimWhatsappVerifikasi('${newPhone}', '${namaSiswa}')`);
                    } else {
                        showAlert('phone-message', 'danger', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('phone-message', 'danger', 'Terjadi kesalahan sistem');
                },
                complete: function() {
                    savePhoneBtn.disabled = false;
                }
            });
        });
    }
});
</script>

<?php $this->load->view('include/footer.php'); ?>
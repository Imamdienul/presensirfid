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
                                                        <!-- Tombol WhatsApp -->
                                                        <button type="button" id="whatsapp-btn" class="btn btn-whatsapp btn-sm ml-2" onclick="hubungiOrangTua('<?= $murid->telp; ?>', '<?= $murid->nama; ?>')">
                                                            <i class="fab fa-whatsapp"></i> Hubungi
                                                        </button>
                                                        <!-- Tombol Kirim Notifikasi Test -->
                                                        <button type="button" id="test-notif-btn" class="btn btn-primary btn-sm ml-2">
                                                            <i class="fa fa-paper-plane"></i> Test Notifikasi
                                                        </button>
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

                        <!-- Card untuk Test WhatsApp -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fab fa-whatsapp text-success"></i> Test Notifikasi WhatsApp</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Jenis Notifikasi</label>
                                            <select id="notif-type" class="form-control">
                                                <option value="masuk">Absensi Masuk</option>
                                                <option value="keluar">Absensi Keluar</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mode Absensi</label>
                                            <select id="manual-mode" class="form-control">
                                                <option value="false">RFID (Otomatis)</option>
                                                <option value="true">Manual</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="send-whatsapp-test" class="btn btn-success btn-block">
                                    <i class="fab fa-whatsapp"></i> Kirim Test Notifikasi WhatsApp
                                </button>
                            </div>
                        </div>

                        <!-- Preview Pesan -->
                        <div id="message-preview" class="card mt-3" style="display: none;">
                            <div class="card-header">
                                <h6><i class="fa fa-eye"></i> Preview Pesan</h6>
                            </div>
                            <div class="card-body">
                                <pre id="preview-content" style="white-space: pre-wrap; font-family: monospace; background: #f8f9fa; padding: 15px; border-radius: 5px;"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End details column -->
        </div>

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

    /* Loading spinner */
    .btn-loading {
        position: relative;
    }

    .btn-loading:after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        margin: auto;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function hubungiOrangTua(nomorTelp, namaSiswa) {
    // Validasi nomor telepon
    if (!nomorTelp || nomorTelp.trim() === '') {
        alert('Nomor telepon orang tua belum diisi!');
        return;
    }
    
    // Format nomor telepon untuk WhatsApp
    let phoneNumber = nomorTelp.replace(/\D/g, ''); // Hapus karakter non-digit
    
    // Jika nomor dimulai dengan 0, ganti dengan 62
    if (phoneNumber.startsWith('0')) {
        phoneNumber = '62' + phoneNumber.substring(1);
    }
    // Jika belum ada kode negara, tambahkan 62
    else if (!phoneNumber.startsWith('62')) {
        phoneNumber = '62' + phoneNumber;
    }
    
    // Pesan untuk verifikasi orang tua
    const message = `Selamat pagi/siang/sore. Saya dari sekolah ingin mengkonfirmasi, apakah benar ini adalah nomor orang tua dari ${namaSiswa}? 

Nomor ini akan digunakan untuk notifikasi absensi dan informasi penting terkait siswa. 

Mohon konfirmasinya. Terima kasih.`;
    
    // Encode pesan untuk URL
    const encodedMessage = encodeURIComponent(message);
    
    // URL WhatsApp
    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
    
    // Buka WhatsApp
    window.open(whatsappUrl, '_blank');
}

function showMessage(type, message) {
    const alertDiv = $('#whatsapp-message');
    alertDiv.removeClass('alert-success alert-danger alert-warning alert-info');
    alertDiv.addClass('alert-' + type);
    alertDiv.text(message);
    alertDiv.show();
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        alertDiv.fadeOut();
    }, 5000);
}

function sendWhatsAppNotification(phone, nama, keterangan, isManual = false) {
    const button = $('#send-whatsapp-test');
    const originalText = button.html();
    
    // Show loading
    button.prop('disabled', true);
    button.addClass('btn-loading');
    button.html('<i class="fa fa-spinner fa-spin"></i> Mengirim...');
    
    // Prepare data
    const postData = {
        phone: phone,
        nama: nama,
        student_id: $('#murid-id').val(),
        keterangan: keterangan,
        is_manual: isManual
    };
    
    // AJAX call to your existing API endpoint
    $.ajax({
        url: '<?= base_url("api/test_whatsapp") ?>',
        type: 'GET',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                showMessage('success', 'Notifikasi WhatsApp berhasil dikirim!');
                
                // Show preview
                $('#preview-content').text(response.message_sent);
                $('#message-preview').show();
                
                console.log('API Response:', response);
            } else {
                showMessage('danger', 'Gagal mengirim notifikasi: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.error('Response:', xhr.responseText);
            showMessage('danger', 'Terjadi kesalahan saat mengirim notifikasi: ' + error);
        },
        complete: function() {
            // Reset button
            button.prop('disabled', false);
            button.removeClass('btn-loading');
            button.html(originalText);
        }
    });
}

// Document ready
$(document).ready(function() {
    // Save phone button
    $('#save-phone').click(function() {
        const newPhone = $('#phone-input').val();
        const namaSiswa = '<?= $murid->nama; ?>';
        $('#whatsapp-btn').attr('onclick', `hubungiOrangTua('${newPhone}', '${namaSiswa}')`);
        showMessage('info', 'Nomor telepon berhasil diperbarui!');
    });
    
    // Test notification button
    $('#test-notif-btn').click(function() {
        const phone = $('#phone-input').val();
        if (!phone || phone.trim() === '') {
            showMessage('warning', 'Mohon isi nomor telepon terlebih dahulu!');
            return;
        }
        
        const nama = '<?= $murid->nama; ?>';
        const keterangan = $('#notif-type').val();
        const isManual = $('#manual-mode').val() === 'true';
        
        sendWhatsAppNotification(phone, nama, keterangan, isManual);
    });
    
    // Send WhatsApp test button
    $('#send-whatsapp-test').click(function() {
        const phone = $('#phone-input').val();
        if (!phone || phone.trim() === '') {
            showMessage('warning', 'Mohon isi nomor telepon terlebih dahulu!');
            return;
        }
        
        const nama = '<?= $murid->nama; ?>';
        const keterangan = $('#notif-type').val();
        const isManual = $('#manual-mode').val() === 'true';
        
        sendWhatsAppNotification(phone, nama, keterangan, isManual);
    });
    
    // Preview message when options change
    $('#notif-type, #manual-mode').change(function() {
        $('#message-preview').hide();
    });
});
</script>

<?php $this->load->view('include/footer.php'); ?>
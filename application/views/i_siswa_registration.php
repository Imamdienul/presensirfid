<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Daftar</title>
    <meta content="Admin Dashboard" name="description" />
    <meta content="Mannatthemes" name="author" />
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/gi.png">
    
    <style>
                .bg-custom-purple {
            background-color: #252f75;
        }
        .text-custom-purple {
            color: #252f75;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f7fafc;
        }
        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
        }
        .form-card {
            width: 100%;
            max-width: 28rem;
            background: white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .header {
            padding: 1.5rem;
            background-color: #252f75;
            color: white;
        }
        .header h1 {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }
        .header p {
            font-size: 1.125rem;
            margin: 0.5rem 0 0 0;
        }
        .form-content {
            padding: 1.5rem;
        }
        .logo-container {
            text-align: center;
            padding: 0.75rem 0 1rem 0;
        }
        .logo {
            height: 5rem;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            color: #374151;
            font-size: 0.875rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .form-input, .form-select {
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            width: 100%;
            padding: 0.5rem 0.75rem;
            color: #374151;
            line-height: 1.25;
            box-sizing: border-box;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #252f75;
            box-shadow: 0 0 0 3px rgba(37, 47, 117, 0.1);
        }
        .submit-btn {
            width: 100%;
            background-color: #252f75;
            color: white;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.15s;
        }
        .submit-btn:hover {
            background-color: #1e2460;
        }
        .submit-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 47, 117, 0.1);
        }
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #34d399;
            color: #047857;
        }
        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #f87171;
            color: #dc2626;
        }
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
        .login-link a {
            color: #252f75;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.15s;
        }
        .login-link a:hover {
            color: #1e2460;
        }
        .file-input-help {
            color: #6b7280;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        .phone-input {
            position: relative;
        }
        .phone-prefix {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 0.875rem;
            pointer-events: none;
        }
        .phone-input .form-input {
            padding-left: 2.5rem;
        }

        /* Mobile responsive */
        @media (max-width: 640px) {
            .container {
                padding: 0.5rem;
            }
            .header {
                padding: 1rem;
            }
            .header h1 {
                font-size: 1.25rem;
            }
            .header p {
                font-size: 1rem;
            }
            .form-content {
                padding: 1rem;
            }
            .form-label {
                margin-bottom: 0.25rem;
            }
        }
        /* Style untuk galeri foto */
        .photo-gallery {
            display: none;
            margin-top: 10px;
        }
        
        .photo-gallery.show {
            display: block;
        }
        
        .gallery-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background-color: #f9f9f9;
        }
        
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
        }
        
        .photo-item {
            position: relative;
            cursor: pointer;
            border: 3px solid transparent;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .photo-item:hover {
            border-color: #007bff;
            transform: scale(1.05);
        }
        
        .photo-item.selected {
            border-color: #28a745;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }
        
        .photo-item img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            display: block;
        }
        
        .photo-item .checkmark {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #28a745;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .photo-item.selected .checkmark {
            display: flex;
        }
        
        .loading-message {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .no-photos-message {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
        
        .gallery-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-card">
            <div class="header">
                <h1><?php echo get_settings('app_name'); ?></h1>
                <p>Silahkan Daftar</p>
            </div>

            <div class="form-content">
                <div class="logo-container">
                    <img src="<?php echo base_url(get_settings('logo_path')); ?>" alt="logo" class="logo">
                </div>

                <?php if ($is_success): ?>
                    <div class="alert alert-success">
                        Registrasi Berhasil! Terima kasih telah mendaftar.
                    </div>
                <?php else: ?>
                    <?php if (isset($upload_error)): ?>
                        <div class="alert alert-error">
                            <?= $upload_error ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($validation_errors) && !empty($validation_errors)): ?>
                        <div class="alert alert-error">
                            <ul>
                                <?php foreach ($validation_errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php echo form_open('register/submit', ['class' => 'registration-form']); ?>
                        
                        <div class="form-group">
                            <label for="nama" class="form-label">Nama:</label>
                            <input class="form-input" type="text" name="nama" id="nama" required placeholder="Contoh: IMAM DIENUL BAYAN">
                        </div>

                        <div class="form-group">
                            <label for="tempat" class="form-label">Tempat Lahir:</label>
                            <input class="form-input" type="text" name="tempat" id="tempat" required placeholder="Contoh: Majalengka">
                        </div>

                        <div class="form-group">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
                            <input class="form-input" type="date" name="tanggal_lahir" id="tanggal_lahir" required>
                        </div>

                        <div class="form-group">
                            <label for="kelas" class="form-label">Kelas:</label>
                            <select class="form-select" id="kelas" name="id_kelas" required>
                                <option value="">--Pilih Kelas--</option>
                                <?php foreach ($kelas as $row): ?>
                                    <option value="<?= $row->id ?>"><?= $row->kelas ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nisn" class="form-label">NISN:</label>
                            <input class="form-input" type="number" name="nisn" id="nisn" required placeholder="Contoh: 1212838392">
                        </div>

                        <div class="form-group">
                            <label for="telp" class="form-label">No. HP Orang Tua:</label>
                            <div class="phone-input">
                                <span class="phone-prefix">+62</span>
                                <input class="form-input" type="tel" name="telp" id="telp" required placeholder="812345678" pattern="[0-9]{8,13}" title="Masukkan nomor HP tanpa +62 (8-13 digit)">
                            </div>
                            <div class="file-input-help">Masukkan nomor HP tanpa kode negara (+62)</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="alamat" class="form-label">Alamat:</label>
                            <input class="form-input" type="text" name="alamat" id="alamat" required placeholder="Contoh: Desa Cibeureum, Kec. Talaga, Kab. Majalengka">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Foto:</label>
                            <div class="file-input-help">Pilih kelas terlebih dahulu untuk melihat galeri foto</div>
                            
                            <!-- Hidden input untuk menyimpan foto yang dipilih -->
                            <input type="hidden" name="selected_photo" id="selected_photo" required>
                            
                            <!-- Galeri foto -->
                            <div class="photo-gallery" id="photo-gallery">
                                <div class="gallery-title" id="gallery-title"></div>
                                <div class="gallery-container" id="gallery-container">
                                    <div class="loading-message" id="loading-message">Memuat foto...</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="submit-btn" type="submit">Daftar</button>
                        </div>
                    <?php echo form_close(); ?>
                <?php endif; ?>
                
                <div class="login-link">
                    <a href="<?= base_url(); ?>siswal">
                        <i class="fas fa-sign-in-alt"></i> Sudah punya akun?
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load FontAwesome secara async
        const fontAwesome = document.createElement('link');
        fontAwesome.rel = 'stylesheet';
        fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
        document.head.appendChild(fontAwesome);

        // Format nomor telepon otomatis
        document.getElementById('telp').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = value.substring(1);
            }
            if (value.startsWith('62')) {
                value = value.substring(2);
            }
            e.target.value = value;
        });

        // Event listener untuk pemilihan kelas
        document.getElementById('kelas').addEventListener('change', function() {
            const selectedKelas = this.value;
            const photoGallery = document.getElementById('photo-gallery');
            const galleryContainer = document.getElementById('gallery-container');
            const loadingMessage = document.getElementById('loading-message');
            const galleryTitle = document.getElementById('gallery-title');
            const selectedPhotoInput = document.getElementById('selected_photo');
            
            // Reset pilihan foto sebelumnya
            selectedPhotoInput.value = '';
            
            if (selectedKelas) {
                // Tampilkan galeri dan loading
                photoGallery.classList.add('show');
                galleryContainer.innerHTML = '<div class="loading-message">Memuat foto...</div>';
                
                // AJAX request untuk mengambil foto berdasarkan kelas
                fetch('<?= base_url('register/get_photos_by_class') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id_kelas=' + encodeURIComponent(selectedKelas)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        galleryTitle.textContent = 'Pilih Foto dari Kelas ' + data.kelas + ':';
                        
                        if (data.photos && data.photos.length > 0) {
                            let photosHtml = '<div class="photo-grid">';
                            data.photos.forEach(function(photo) {
                                photosHtml += `
                                    <div class="photo-item" data-filename="${photo.filename}">
                                        <img src="${photo.path}" alt="Foto" loading="lazy">
                                        <div class="checkmark">✓</div>
                                    </div>
                                `;
                            });
                            photosHtml += '</div>';
                            galleryContainer.innerHTML = photosHtml;
                            
                            // Event listener untuk pemilihan foto
                            document.querySelectorAll('.photo-item').forEach(function(item) {
                                item.addEventListener('click', function() {
                                    // Remove selected class from all items
                                    document.querySelectorAll('.photo-item').forEach(function(otherItem) {
                                        otherItem.classList.remove('selected');
                                    });
                                    
                                    // Add selected class to clicked item
                                    this.classList.add('selected');
                                    
                                    // Set hidden input value
                                    selectedPhotoInput.value = this.dataset.filename;
                                });
                            });
                        } else {
                            galleryContainer.innerHTML = '<div class="no-photos-message">Tidak ada foto tersedia untuk kelas ini</div>';
                        }
                    } else {
                        galleryContainer.innerHTML = '<div class="no-photos-message">Error: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    galleryContainer.innerHTML = '<div class="no-photos-message">Terjadi kesalahan saat memuat foto</div>';
                });
            } else {
                // Sembunyikan galeri jika tidak ada kelas yang dipilih
                photoGallery.classList.remove('show');
            }
        });

        // Validasi form sebelum submit
        document.querySelector('.registration-form').addEventListener('submit', function(e) {
            const telp = document.getElementById('telp').value;
            const selectedPhoto = document.getElementById('selected_photo').value;
            
            if (telp.length < 8 || telp.length > 13) {
                e.preventDefault();
                alert('Nomor HP harus antara 8-13 digit');
                return false;
            }
            
            if (!selectedPhoto) {
                e.preventDefault();
                alert('Silakan pilih foto terlebih dahulu');
                return false;
            }
        });
    </script>
</body>

</html>
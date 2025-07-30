<footer class="footer">
    Keep Innovating 💡 | © 2024 Imam Dienul
</footer>

</div>
</div>

<script src="<?= base_url(); ?>assets/js/jquery.min.js"></script>
<script src="<?= base_url(); ?>assets/js/popper.min.js"></script>
<script src="<?= base_url(); ?>assets/js/bootstrap-material-design.js"></script>
<script src="<?= base_url(); ?>assets/js/modernizr.min.js"></script>
<script src="<?= base_url(); ?>assets/js/detect.js"></script>
<script src="<?= base_url(); ?>assets/js/fastclick.js"></script>
<script src="<?= base_url(); ?>assets/js/jquery.slimscroll.js"></script>
<script src="<?= base_url(); ?>assets/js/jquery.blockUI.js"></script>
<script src="<?= base_url(); ?>assets/js/waves.js"></script>
<script src="<?= base_url(); ?>assets/js/jquery.nicescroll.js"></script>
<script src="<?= base_url(); ?>assets/js/jquery.scrollTo.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/dropify/js/dropify.min.js"></script>
<script src="<?= base_url(); ?>assets/pages/upload-init.js"></script>

<script src="<?= base_url(); ?>assets/plugins/carousel/owl.carousel.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/fullcalendar/vanillaCalendar.js"></script>
<script src="<?= base_url(); ?>assets/plugins/peity/jquery.peity.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="<?= base_url(); ?>assets/plugins/chartist/js/chartist.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/chartist/js/chartist-plugin-tooltip.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/metro/MetroJs.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/raphael/raphael.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/morris/morris.min.js"></script>

<script src="<?= base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/datatables/jszip.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/datatables/pdfmake.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/datatables/vfs_fonts.js"></script>
<script src="<?= base_url(); ?>assets/plugins/datatables/buttons.html5.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/datatables/buttons.print.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/datatables/buttons.colVis.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/datatables/dataTables.responsive.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/d3/d3.min.js"></script>
<script src="<?= base_url(); ?>assets/plugins/c3/c3.min.js"></script>
<script src="<?= base_url(); ?>assets/pages/c3-chart-init.js"></script>
<script src="<?= base_url(); ?>assets/pages/datatables.init.js"></script>
<script src="<?= base_url(); ?>assets/js/app.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('nav-search');
        const searchResults = document.getElementById('search-results');

        const menuItems = [{
                name: 'Menu Dashboard',
                url: '<?= base_url(); ?>dashboard'
            },
            {
                name: 'Menu Kelas',
                url: '<?= base_url(); ?>kelas'
            },
            {
                name: 'Menu Wali Kelas',
                url: '<?= base_url(); ?>walikelas/list_walikelas'
            },
            {
                name: 'Menu Data Siswa',
                url: '<?= base_url(); ?>siswa'
            },
            {
                name: 'Menu RFID',
                url: '<?= base_url(); ?>siswa/siswanew'
            },
            {
                name: 'Menu Riwayat Kehadiran',
                url: '<?= base_url(); ?>absensi'
            },
            {
                name: 'Menu Alpa',
                url: '<?= base_url(); ?>alfa'
            },
            {
                name: 'Menu Perizinan',
                url: '<?= base_url(); ?>izin'
            },
            {
                name: 'Menu Admin',
                url: '<?= base_url(); ?>users'
            },
            {
                name: 'Menu Device',
                url: '<?= base_url(); ?>devices'
            },
            {
                name: 'Menu Waktu Operasional',
                url: '<?= base_url(); ?>setting'
            },
            {
                name: 'Menu SQL Command',
                url: '<?= base_url(); ?>sql'
            },
            {
                name: 'Menu Waktu Libur',
                url: '<?= base_url(); ?>kelas/manage_holidays'
            },
            {
                name: 'Menu APP Settings',
                url: '<?= base_url(); ?>settings'
            },
            {
                name: 'Menu OTA',
                url: '<?= base_url(); ?>ota'
            }
        ];

        if (searchInput && searchResults) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const filteredItems = menuItems.filter(item =>
                    item.name.toLowerCase().includes(searchTerm)
                );

                displayResults(filteredItems);
            });

            function displayResults(items) {
                searchResults.innerHTML = '';

                if (items.length === 0 || searchInput.value.trim() === '') {
                    searchResults.style.display = 'none';
                    return;
                }

                items.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.textContent = item.name;
                    div.addEventListener('click', () => {
                        window.location.href = item.url;
                    });
                    searchResults.appendChild(div);
                });

                searchResults.style.display = 'block';
            }

            document.addEventListener('click', function(event) {
                if (!searchResults.contains(event.target) && event.target !== searchInput) {
                    searchResults.style.display = 'none';
                }
            });
        }
    });

    $(document).ready(function() {
        if ($('#phone-input').length && $('#save-phone').length) {
            var originalPhone = $('#phone-input').val();
            
            $('#save-phone').click(function() {
                var muridId = $('#murid-id').val();
                var newPhone = $('#phone-input').val().trim();
                
                if (newPhone === '') {
                    showMessage('Nomor telepon tidak boleh kosong!', 'danger');
                    return;
                }
                
                var cleanPhone = newPhone.replace(/[\s\-]/g, '');
                var phoneRegex = /^[\+]?[0-9]{10,15}$/;
                
                if (!phoneRegex.test(cleanPhone)) {
                    showMessage('Format nomor telepon tidak valid! Gunakan 10-15 digit angka (boleh diawali +).', 'danger');
                    return;
                }
                
                if (newPhone === originalPhone) {
                    showMessage('Tidak ada perubahan pada nomor telepon.', 'info');
                    return;
                }
                
                $('#save-phone').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
                
                $.ajax({
                    url: '<?= base_url("kelas/update_phone") ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_siswa: muridId,
                        telp: newPhone
                    },
                    success: function(response) {
                        if (response && response.status === 'success') {
                            showMessage('Nomor telepon berhasil diupdate!', 'success');
                            originalPhone = newPhone;
                        } else {
                            var errorMsg = response && response.message ? response.message : 'Terjadi kesalahan yang tidak diketahui';
                            showMessage('Gagal mengupdate: ' + errorMsg, 'danger');
                            $('#phone-input').val(originalPhone);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Ajax Error:', xhr.responseText);
                        showMessage('Terjadi kesalahan sistem. Silakan coba lagi.', 'danger');
                        $('#phone-input').val(originalPhone);
                    },
                    complete: function() {
                        $('#save-phone').prop('disabled', false).html('<i class="fa fa-save"></i> Save');
                    }
                });
            });
            
            $('#phone-input').keypress(function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#save-phone').click();
                }
            });
            
            $('#phone-input').keyup(function(e) {
                if (e.which === 27) {
                    $(this).val(originalPhone);
                    showMessage('Perubahan dibatalkan', 'info');
                }
            });
        }
        
        function showMessage(message, type) {
            if ($('#phone-message').length) {
                var alertClass = 'alert-' + type;
                $('#phone-message')
                    .removeClass('alert-success alert-danger alert-info alert-warning')
                    .addClass('alert ' + alertClass)
                    .html('<strong>' + message + '</strong>')
                    .show();
                
                if (type !== 'danger') {
                    setTimeout(function() {
                        $('#phone-message').fadeOut();
                    }, 4000);
                }
            }
        }
    });
</script>
</body>
</html>

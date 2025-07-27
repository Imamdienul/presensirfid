<?php
$this->load->view('include/header.php');

if ($set == "absensi") {
?>
  <div class="page-content-wrapper">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <div class="page-title-box">
            <div class="btn-group float-right">
              <ol class="breadcrumb hide-phone p-0 m-0">
                <li class="breadcrumb-item"><a href="#">Absensi</a></li>
                <li class="breadcrumb-item"><a href="#">Siswa</a></li>
                <li class="breadcrumb-item active">Absensi Siswa</li>
              </ol>
            </div>
            <h4 class="page-title">Absensi</h4>
          </div>
        </div>
      </div>

      <!-- Alert untuk notifikasi -->
      <div id="alertContainer" style="display: none;">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <span id="alertMessage"></span>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>

      <!-- Loading Indicator -->
      <div id="loadingIndicator" class="text-center" style="display: none;">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <p>Memuat data absensi...</p>
      </div>

      <!-- Absensi Masuk Table -->
      <div class="row">
        <div class="col-12">
          <div class="card m-b-30">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mt-0 header-title">
                  <b>Absensi Masuk</b>
                  <b class="text-danger"><?= date("d M Y", time()); ?></b>
                  <span class="badge badge-info ml-2" id="countMasuk">0</span>
                </h4>
                <div>
                  <button id="refreshBtn" class="btn btn-sm btn-info mr-2">
                    <i class="fas fa-sync-alt"></i> Refresh
                  </button>
                  <button id="autoRefreshToggle" class="btn btn-sm btn-success">
                    <i class="fas fa-play"></i> Auto Refresh ON
                  </button>
                </div>
              </div>

              <div class="table-responsive">
                <table id="absensiMasukTable" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 50px;">No</th>
                      <th>Oleh</th>
                      <th>Nama</th>
                      <th>Kelas</th>
                      <th>Keterangan</th>
                      <th>Waktu</th>
                    </tr>
                  </thead>
                  <tbody id="absensiMasukBody">
                    <!-- Data akan dimuat via AJAX -->
                    <tr>
                      <td colspan="6" class="text-center">
                        <div class="spinner-border spinner-border-sm" role="status">
                          <span class="sr-only">Loading...</span>
                        </div>
                        Memuat data...
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Absensi Keluar Table -->
      <div class="row">
        <div class="col-12">
          <div class="card m-b-30">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mt-0 header-title">
                  <b>Absensi Keluar</b>
                  <b class="text-danger"><?= date("d M Y", time()); ?></b>
                  <span class="badge badge-info ml-2" id="countKeluar">0</span>
                </h4>
              </div>

              <div class="table-responsive">
                <table id="absensiKeluarTable" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 50px;">No</th>
                      <th>Oleh</th>
                      <th>Nama</th>
                      <th>Kelas</th>
                      <th>Keterangan</th>
                      <th>Waktu</th>
                    </tr>
                  </thead>
                  <tbody id="absensiKeluarBody">
                    <!-- Data akan dimuat via AJAX -->
                    <tr>
                      <td colspan="6" class="text-center">
                        <div class="spinner-border spinner-border-sm" role="status">
                          <span class="sr-only">Loading...</span>
                        </div>
                        Memuat data...
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Status koneksi -->
      <div class="row">
        <div class="col-12">
          <div class="text-center">
            <small class="text-muted">
              Status: <span id="connectionStatus" class="badge badge-success">Terhubung</span>
              | Terakhir update: <span id="lastUpdateTime">-</span>
            </small>
          </div>
        </div>
      </div>

    </div> <!-- container-fluid -->
  </div> <!-- page-content-wrapper -->

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      let lastUpdate = 0;
      let autoRefreshInterval;
      let isAutoRefreshOn = true;

      // Initial load
      loadAbsensiData();

      // Start auto refresh
      startAutoRefresh();

      // Refresh button
      $('#refreshBtn').click(function() {
        loadAbsensiData();
      });

      // Auto refresh toggle
      $('#autoRefreshToggle').click(function() {
        if (isAutoRefreshOn) {
          stopAutoRefresh();
        } else {
          startAutoRefresh();
        }
      });

      function startAutoRefresh() {
        isAutoRefreshOn = true;
        $('#autoRefreshToggle').removeClass('btn-danger').addClass('btn-success')
          .html('<i class="fas fa-play"></i> Auto Refresh ON');

        // Auto refresh setiap 30 detik
        autoRefreshInterval = setInterval(function() {
          loadAbsensiData();
        }, 30000);
      }

      function stopAutoRefresh() {
        isAutoRefreshOn = false;
        $('#autoRefreshToggle').removeClass('btn-success').addClass('btn-danger')
          .html('<i class="fas fa-pause"></i> Auto Refresh OFF');

        if (autoRefreshInterval) {
          clearInterval(autoRefreshInterval);
        }
      }

      function loadAbsensiData() {
        $.ajax({
          url: '<?= base_url("absensi/get_absensi_ajax"); ?>',
          type: 'GET',
          dataType: 'json',
          timeout: 10000, // 10 detik timeout
          beforeSend: function() {
            $('#refreshBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
            $('#connectionStatus').removeClass('badge-success badge-danger').addClass('badge-warning').text('Memuat...');
          },
          success: function(response) {
            if (response.status === 'success') {
              updateAbsensiMasuk(response.absensimasuk);
              updateAbsensiKeluar(response.absensikeluar);
              lastUpdate = response.timestamp;

              // Update status
              $('#connectionStatus').removeClass('badge-warning badge-danger').addClass('badge-success').text('Terhubung');
              $('#lastUpdateTime').text(new Date().toLocaleTimeString());

              // Hide alert if visible
              $('#alertContainer').fadeOut();
            } else {
              showErrorMessage('Gagal memuat data: ' + (response.message || 'Unknown error'));
            }
          },
          error: function(xhr, status, error) {
            console.error('Error loading absensi data:', error);
            $('#connectionStatus').removeClass('badge-success badge-warning').addClass('badge-danger').text('Terputus');

            let errorMsg = 'Gagal memuat data absensi';
            if (status === 'timeout') {
              errorMsg = 'Koneksi timeout. Periksa koneksi internet Anda.';
            } else if (status === 'error') {
              errorMsg = 'Terjadi kesalahan jaringan.';
            }

            showErrorMessage(errorMsg);
          },
          complete: function() {
            $('#refreshBtn').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Refresh');
          }
        });
      }

      function updateAbsensiMasuk(data) {
        let tbody = $('#absensiMasukBody');
        tbody.empty();

        if (data && data.length > 0) {
          data.forEach(function(item, index) {
            let row = `
                        <tr class="fade-in">
                            <td>${index + 1}</td>
                            <td>${item.nama_devices || '-'}</td>
                            <td><strong>${item.nama || '-'}</strong></td>
                            <td><span class="badge badge-primary">${item.kelas || '-'}</span></td>
                            <td><span class="badge badge-success">${item.keterangan || '-'}</span></td>
                            <td>${formatDateTime(item.created_at)}</td>
                        </tr>
                    `;
            tbody.append(row);
          });

          // Update counter
          $('#countMasuk').text(data.length);
        } else {
          tbody.html(`
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            <i class="fas fa-inbox"></i> Belum ada data absensi masuk hari ini
                        </td>
                    </tr>
                `);
          $('#countMasuk').text('0');
        }
      }

      function updateAbsensiKeluar(data) {
        let tbody = $('#absensiKeluarBody');
        tbody.empty();

        if (data && data.length > 0) {
          data.forEach(function(item, index) {
            let row = `
                        <tr class="fade-in">
                            <td>${index + 1}</td>
                            <td>${item.nama_devices || '-'}</td>
                            <td><strong>${item.nama || '-'}</strong></td>
                            <td><span class="badge badge-primary">${item.kelas || '-'}</span></td>
                            <td><span class="badge badge-warning">${item.keterangan || '-'}</span></td>
                            <td>${formatDateTime(item.created_at)}</td>
                        </tr>
                    `;
            tbody.append(row);
          });

          // Update counter
          $('#countKeluar').text(data.length);
        } else {
          tbody.html(`
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            <i class="fas fa-inbox"></i> Belum ada data absensi keluar hari ini
                        </td>
                    </tr>
                `);
          $('#countKeluar').text('0');
        }
      }

      function formatDateTime(timestamp) {
        if (!timestamp) return '-';

        let date = new Date(timestamp * 1000);
        let options = {
          year: 'numeric',
          month: 'short',
          day: '2-digit',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit'
        };

        return date.toLocaleDateString('id-ID', options);
      }

      function showErrorMessage(message) {
        $('#alertMessage').text(message || 'Terjadi kesalahan saat memuat data');
        $('#alertContainer').fadeIn();

        // Auto hide after 5 seconds
        setTimeout(function() {
          $('#alertContainer').fadeOut();
        }, 5000);
      }

      // Handle visibility change untuk pause/resume auto refresh
      document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
          // Tab tidak aktif, hentikan auto refresh
          if (isAutoRefreshOn) {
            clearInterval(autoRefreshInterval);
          }
        } else {
          // Tab aktif kembali, mulai auto refresh
          if (isAutoRefreshOn) {
            loadAbsensiData(); // Load immediately
            startAutoRefresh();
          }
        }
      });

      // Handle window focus/blur
      $(window).on('focus', function() {
        if (isAutoRefreshOn) {
          loadAbsensiData();
        }
      });
    });
  </script>

  <style>
    .fade-in {
      animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .table th {
      background-color: #f8f9fa;
      border-top: none;
    }

    .badge {
      font-size: 0.75em;
    }

    #connectionStatus {
      font-size: 0.7em;
    }

    .card {
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .spinner-border-sm {
      width: 1rem;
      height: 1rem;
    }
  </style>

<?php
} else if ($set == "last-absensi") {
  // View untuk last absensi (jika ada)
?>
  <div class="page-content-wrapper">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <div class="page-title-box">
            <div class="btn-group float-right">
              <ol class="breadcrumb hide-phone p-0 m-0">
                <li class="breadcrumb-item"><a href="<?= base_url('absensi'); ?>">Absensi</a></li>
                <li class="breadcrumb-item active">Riwayat Absensi</li>
              </ol>
            </div>
            <h4 class="page-title">Riwayat Absensi</h4>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card m-b-30">
            <div class="card-body">
              <h4 class="mt-0 header-title">
                Periode: <b class="text-primary"><?= $tanggal ?? 'Tidak tersedia'; ?></b>
              </h4>

              <div class="row mt-3">
                <div class="col-md-6">
                  <h5>Absensi Masuk (<?= isset($datamasuk) ? count($datamasuk) : 0; ?>)</h5>
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama</th>
                          <th>Kelas</th>
                          <th>Waktu</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (isset($datamasuk) && count($datamasuk) > 0): ?>
                          <?php foreach ($datamasuk as $index => $item): ?>
                            <tr>
                              <td><?= $index + 1; ?></td>
                              <td><?= $item->nama ?? '-'; ?></td>
                              <td><?= $item->kelas ?? '-'; ?></td>
                              <td><?= date('d/m/Y H:i:s', $item->created_at); ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="4" class="text-center">Tidak ada data</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="col-md-6">
                  <h5>Absensi Keluar (<?= isset($datakeluar) ? count($datakeluar) : 0; ?>)</h5>
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama</th>
                          <th>Kelas</th>
                          <th>Waktu</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (isset($datakeluar) && count($datakeluar) > 0): ?>
                          <?php foreach ($datakeluar as $index => $item): ?>
                            <tr>
                              <td><?= $index + 1; ?></td>
                              <td><?= $item->nama ?? '-'; ?></td>
                              <td><?= $item->kelas ?? '-'; ?></td>
                              <td><?= date('d/m/Y H:i:s', $item->created_at); ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="4" class="text-center">Tidak ada data</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
}

$this->load->view('include/footer.php');
?>
<?php
$this->load->view('include/header.php');

if ($set == "list-users") {
?>
  <div class="page-content-wrapper">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <div class="page-title-box">
            <div class="btn-group float-right">
              <ol class="breadcrumb hide-phone p-0 m-0">
                <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url('users'); ?>">User</a></li>
                <li class="breadcrumb-item active">List Users</li>
              </ol>
            </div>
            <h4 class="page-title">List Users</h4>
          </div>
          <a href="<?php echo base_url('users/add'); ?>" class="btn btn-success mb-3">
            <i class="fa fa-user-plus"></i> Tambah User
          </a>
        </div>
        <div class="clearfix"></div>
      </div>

      <!-- Flash Messages -->
      <?php if ($success = $this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fa fa-check-circle"></i> <?php echo $success; ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <?php if ($error = $this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-12">
          <div class="card m-b-30">
            <div class="card-body">
              <h4 class="mt-0 header-title">Daftar User</h4>
              <div class="table-responsive">
                <table id="datatable-buttons" class="table table-striped table-bordered w-100">
                  <thead>
                    <tr>
                      <th width="5%">No</th>
                      <th width="15%">Avatar</th>
                      <th width="20%">Nama</th>
                      <th width="20%">Email</th>
                      <th width="15%">Username</th>
                      <th width="10%">Status</th>
                      <th width="15%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($users)): ?>
                      <tr>
                        <td colspan="7" class="text-center">
                          <i class="fa fa-info-circle"></i> Data user tidak ditemukan
                        </td>
                      </tr>
                    <?php else: ?>
                      <?php $no = 1;
                      foreach ($users as $user): ?>
                        <tr>
                          <td><?php echo $no++; ?></td>
                          <td class="text-center">
                            <img src="<?php echo base_url('assets/images/' . ($user->avatar ? $user->avatar : 'default.png')); ?>"
                              alt="Avatar" class="rounded-circle" width="40" height="40">
                          </td>
                          <td><?php echo htmlspecialchars($user->nama, ENT_QUOTES, 'UTF-8'); ?></td>
                          <td><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></td>
                          <td><?php echo htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8'); ?></td>
                          <td>
                            <?php if (isset($user->status)): ?>
                              <span class="badge badge-<?php echo $user->status === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($user->status); ?>
                              </span>
                            <?php else: ?>
                              <span class="badge badge-success">Active</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php if ($user->id_user != 1): ?>
                              <div class="btn-group" role="group">
                                <a href="<?php echo base_url('users/edit/' . $user->id_user); ?>"
                                  class="btn btn-sm btn-warning" title="Edit User">
                                  <i class="fa fa-pencil"></i>
                                </a>
                                <a href="<?php echo base_url('users/delete/' . $user->id_user); ?>"
                                  class="btn btn-sm btn-danger"
                                  onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')"
                                  title="Hapus User">
                                  <i class="fa fa-trash"></i>
                                </a>
                              </div>
                            <?php else: ?>
                              <span class="badge badge-info">Admin</span>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
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

<?php } elseif ($set == "add-users") { ?>

  <div class="page-content-wrapper user-editor">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <div class="page-title-box">
            <div class="btn-group float-right">
              <ol class="breadcrumb hide-phone p-0 m-0">
                <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url('users'); ?>">User</a></li>
                <li class="breadcrumb-item active">Tambah User</li>
              </ol>
            </div>
            <h4 class="page-title">Tambah User</h4>
          </div>
          <a href="<?php echo base_url('users'); ?>" class="btn btn-secondary mb-3">
            <i class="fa fa-arrow-left"></i> Kembali
          </a>
        </div>
        <div class="clearfix"></div>
      </div>

      <!-- Flash Messages -->
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fa fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <i class="fa fa-user-plus"></i> Form Tambah User
              </h5>
            </div>
            <div class="card-body">
              <?php echo form_open_multipart(base_url('users/save'), ['id' => 'form-add-user']); ?>
              <div class="form-group row">
                <label for="nama" class="col-sm-3 col-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="nama" id="nama" class="form-control"
                    placeholder="Masukkan nama lengkap"
                    value="<?php echo set_value('nama'); ?>" required>
                </div>
              </div>

              <div class="form-group row">
                <label for="email" class="col-sm-3 col-form-label">Email <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="email" name="email" id="email" class="form-control"
                    placeholder="Masukkan alamat email"
                    value="<?php echo set_value('email'); ?>" required>
                </div>
              </div>

              <div class="form-group row">
                <label for="username" class="col-sm-3 col-form-label">Username <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="username" id="username" class="form-control"
                    placeholder="Masukkan username (min. 4 karakter)"
                    value="<?php echo set_value('username'); ?>" required>
                </div>
              </div>

              <div class="form-group row">
                <label for="password" class="col-sm-3 col-form-label">Password <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="password" name="password" id="password" class="form-control"
                    placeholder="Masukkan password (min. 6 karakter)" required>
                  <small class="form-text text-muted">Password minimal 6 karakter</small>
                </div>
              </div>

              <div class="form-group row">
                <label for="image" class="col-sm-3 col-form-label">Avatar</label>
                <div class="col-sm-9">
                  <div class="custom-file">
                    <input type="file" name="image" id="image" class="custom-file-input"
                      accept="image/jpeg,image/jpg,image/png,image/gif">
                    <label class="custom-file-label" for="image">Pilih file gambar</label>
                  </div>
                  <small class="form-text text-muted">
                    Format: JPG, JPEG, PNG, GIF | Max: 2MB | Opsional
                  </small>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-sm-9 offset-sm-3">
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Simpan User
                  </button>
                  <a href="<?php echo base_url('users'); ?>" class="btn btn-secondary">
                    <i class="fa fa-times"></i> Batal
                  </a>
                </div>
              </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php } elseif ($set == "edit-users") { ?>

  <div class="page-content-wrapper user-editor">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <div class="page-title-box">
            <div class="btn-group float-right">
              <ol class="breadcrumb hide-phone p-0 m-0">
                <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url('users'); ?>">User</a></li>
                <li class="breadcrumb-item active">Edit User</li>
              </ol>
            </div>
            <h4 class="page-title">Edit User</h4>
          </div>
          <a href="<?php echo base_url('users'); ?>" class="btn btn-secondary mb-3">
            <i class="fa fa-arrow-left"></i> Kembali
          </a>
        </div>
        <div class="clearfix"></div>
      </div>

      <!-- Flash Messages -->
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fa fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <i class="fa fa-edit"></i> Form Edit User
              </h5>
            </div>
            <div class="card-body">
              <?php echo form_open_multipart(base_url('users/update'), ['id' => 'form-edit-user']); ?>
              <input type="hidden" name="id" value="<?php echo $user->id_user; ?>">

              <div class="form-group row">
                <label for="nama" class="col-sm-3 col-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="nama" id="nama" class="form-control"
                    placeholder="Masukkan nama lengkap"
                    value="<?php echo set_value('nama', $user->nama); ?>" required>
                </div>
              </div>

              <div class="form-group row">
                <label for="email" class="col-sm-3 col-form-label">Email <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="email" name="email" id="email" class="form-control"
                    placeholder="Masukkan alamat email"
                    value="<?php echo set_value('email', $user->email); ?>" required>
                </div>
              </div>

              <div class="form-group row">
                <label for="username" class="col-sm-3 col-form-label">Username <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="username" id="username" class="form-control"
                    placeholder="Masukkan username (min. 4 karakter)"
                    value="<?php echo set_value('username', $user->username); ?>" required>
                </div>
              </div>

              <div class="form-group row">
                <label for="password" class="col-sm-3 col-form-label">Password Baru</label>
                <div class="col-sm-9">
                  <input type="password" name="password" id="password" class="form-control"
                    placeholder="Kosongkan jika tidak ingin mengubah password">
                  <small class="form-text text-muted">
                    Kosongkan jika tidak ingin mengubah password. Min. 6 karakter jika diisi.
                  </small>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Avatar Saat Ini</label>
                <div class="col-sm-9">
                  <div class="mb-2">
                    <img src="<?php echo base_url('assets/images/' . ($user->avatar ?: 'default.png')); ?>"
                      alt="Current Avatar" class="img-thumbnail" width="100" height="100">
                  </div>
                </div>
              </div>

              <div class="form-group row">
                <label for="image" class="col-sm-3 col-form-label">Ganti Avatar</label>
                <div class="col-sm-9">
                  <div class="custom-file">
                    <input type="file" name="image" id="image" class="custom-file-input"
                      accept="image/jpeg,image/jpg,image/png,image/gif">
                    <label class="custom-file-label" for="image">Pilih file gambar baru</label>
                  </div>
                  <small class="form-text text-muted">
                    Format: JPG, JPEG, PNG, GIF | Max: 2MB | Kosongkan jika tidak ingin mengubah
                  </small>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-sm-9 offset-sm-3">
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Update User
                  </button>
                  <a href="<?php echo base_url('users'); ?>" class="btn btn-secondary">
                    <i class="fa fa-times"></i> Batal
                  </a>
                </div>
              </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php } ?>

<script>
  // Custom file input label
  document.addEventListener("DOMContentLoaded", function() {

    $('.custom-file-input').on('change', function() {
      let fileName = $(this).val().split('\\').pop();
      $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Pilih file gambar');
    });

    // Form validation
    $('#form-add-user, #form-edit-user').on('submit', function(e) {
      let isValid = true;

      // Check required fields
      $(this).find('input[required]').each(function() {
        if (!$(this).val().trim()) {
          isValid = false;
          $(this).addClass('is-invalid');
        } else {
          $(this).removeClass('is-invalid');
        }
      });

      // Check password length if provided
      let password = $('#password').val();
      if (password && password.length < 6) {
        isValid = false;
        $('#password').addClass('is-invalid');
        alert('Password minimal 6 karakter');
      }

      // Check username length
      let username = $('#username').val();
      if (username && username.length < 4) {
        isValid = false;
        $('#username').addClass('is-invalid');
        alert('Username minimal 4 karakter');
      }

      if (!isValid) {
        e.preventDefault();
        return false;
      }
    });
  });
</script>

<style>
  .user-editor .bmd-form-group {
    padding-top: 2.5rem !important;
  }
</style>

<?php
$this->load->view('include/footer.php');
?>
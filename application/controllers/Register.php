<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Siswa');
        $this->load->helper(array('form', 'url'));
        $this->load->library(array('upload', 'session'));
        date_default_timezone_set("Asia/Jakarta");
    }
    
    public function index() {
        $data['kelas'] = $this->Siswa->get_kelas();
        
        // Set default value for is_success
        $data['is_success'] = $this->session->flashdata('registered') ?? false;
        
        $this->load->view('i_siswa_registration', $data);
    }
    
    // Method baru untuk mengambil foto berdasarkan kelas
    public function get_photos_by_class() {
        $id_kelas = $this->input->post('id_kelas');
        
        if (!$id_kelas) {
            echo json_encode(['status' => 'error', 'message' => 'Kelas tidak dipilih']);
            return;
        }
        
        // Ambil nama kelas
        $kelas_info = $this->Siswa->get_kelas_by_id($id_kelas);
        if (!$kelas_info) {
            echo json_encode(['status' => 'error', 'message' => 'Kelas tidak ditemukan']);
            return;
        }
        
        $nama_kelas = $kelas_info->kelas;
        $foto_dir = './uploads/foto/' . $nama_kelas . '/';
        
        $photos = array();
        
        if (is_dir($foto_dir)) {
            $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
            $files = scandir($foto_dir);
            
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, $allowed_ext)) {
                        $photos[] = array(
                            'filename' => $file,
                            'path' => base_url('uploads/foto/' . $nama_kelas . '/' . $file)
                        );
                    }
                }
            }
        }
        
        echo json_encode(array(
            'status' => 'success',
            'photos' => $photos,
            'kelas' => $nama_kelas
        ));
    }
    
    public function submit() {
        // Manual validation
        $errors = array();
        
        $nama = trim($this->input->post('nama'));
        $tempat = trim($this->input->post('tempat'));
        $tanggal_lahir = $this->input->post('tanggal_lahir');
        $id_kelas = $this->input->post('id_kelas');
        $nisn = $this->input->post('nisn');
        $telp = $this->input->post('telp');
        $alamat = trim($this->input->post('alamat'));
        $selected_photo = $this->input->post('selected_photo'); // Foto yang dipilih dari galeri
        
        // Validasi manual
        if (empty($nama)) {
            $errors[] = 'Nama wajib diisi';
        }
        if (empty($tempat)) {
            $errors[] = 'Tempat lahir wajib diisi';
        }
        if (empty($tanggal_lahir)) {
            $errors[] = 'Tanggal lahir wajib diisi';
        }
        if (empty($id_kelas)) {
            $errors[] = 'Kelas wajib dipilih';
        }
        if (empty($nisn) || !is_numeric($nisn)) {
            $errors[] = 'NISN wajib diisi dan harus berupa angka';
        }
        if (empty($telp) || !is_numeric($telp) || strlen($telp) < 8 || strlen($telp) > 13) {
            $errors[] = 'No. HP orang tua wajib diisi (8-13 digit)';
        }
        if (empty($alamat)) {
            $errors[] = 'Alamat wajib diisi';
        }
        if (empty($selected_photo)) {
            $errors[] = 'Foto wajib dipilih';
        }
        
        // Cek duplikasi NISN
        if (!empty($nisn)) {
            $existing_nisn = $this->Siswa->get_siswa_by_nisn($nisn);
            if ($existing_nisn) {
                $errors[] = 'NISN sudah terdaftar';
            }
        }

        if (!empty($errors)) {
            $data['kelas'] = $this->Siswa->get_kelas();
            $data['validation_errors'] = $errors;
            $data['is_success'] = false;
            $this->load->view('i_siswa_registration', $data);
            return;
        }

        // Proses foto yang dipilih
        $file_name = null;
        $upload_error = '';
        
        if ($selected_photo) {
            // Ambil informasi kelas
            $kelas_info = $this->Siswa->get_kelas_by_id($id_kelas);
            $nama_kelas = $kelas_info->kelas;
            
            // Path foto sumber
            $source_path = './uploads/foto/' . $nama_kelas . '/' . $selected_photo;
            
            if (file_exists($source_path)) {
                // Buat direktori foto_siswa jika belum ada
                $target_dir = './uploads/foto_siswa/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                // Generate nama file baru
                $file_extension = pathinfo($selected_photo, PATHINFO_EXTENSION);
                $file_name = strtolower(str_replace(' ', '_', $nama)) . '_' . time() . '.' . $file_extension;
                $target_path = $target_dir . $file_name;
                
                // Copy file ke direktori foto_siswa
                if (!copy($source_path, $target_path)) {
                    $upload_error = 'Gagal menyalin foto';
                }
            } else {
                $upload_error = 'File foto tidak ditemukan';
            }
        }

        if ($upload_error) {
            $data['kelas'] = $this->Siswa->get_kelas();
            $data['upload_error'] = $upload_error;
            $data['is_success'] = false;
            $this->load->view('i_siswa_registration', $data);
        } else {
            // Format nomor telepon dengan menambahkan +62
            if (!empty($telp)) {
                // Remove leading zeros or +62
                $telp = ltrim($telp, '0');
                if (substr($telp, 0, 2) == '62') {
                    $telp = substr($telp, 2);
                }
                // Add +62 prefix
                $telp = '+62' . $telp;
            }

            // Process form data
            $data = array(
                'nama' => $nama,
                'tempat_lahir' => $tempat,
                'tanggal_lahir' => $tanggal_lahir, 
                'id_kelas' => $id_kelas,
                'nisn' => $nisn,
                'telp' => $telp,
                'alamat' => $alamat,
                'foto' => $file_name, // Hanya nama file yang disimpan
            );

            if ($this->Siswa->insert_siswa($data)) {
                $this->session->set_flashdata('registered', true);
                // Redirect back to the form
                redirect('register');
            } else {
                $data['kelas'] = $this->Siswa->get_kelas();
                $data['upload_error'] = 'Gagal menyimpan data. Silakan coba lagi.';
                $data['is_success'] = false;
                $this->load->view('i_siswa_registration', $data);
            }
        }
    }
}
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

        $captured_photo = $this->input->post('captured_photo');
        $upload_error = '';
        $file_name = null;

        if ($captured_photo) {
            // Handle photo captured from the camera
            $file_name = strtolower(str_replace(' ', '_', $nama)) . '_' . time() . '.png';
            $file_path = './uploads/' . $file_name;
            $captured_photo = str_replace('data:image/png;base64,', '', $captured_photo);
            $captured_photo = str_replace(' ', '+', $captured_photo);
            $image_data = base64_decode($captured_photo);
            
            if (!file_put_contents($file_path, $image_data)) {
                $upload_error = 'Gagal menyimpan foto dari kamera';
            }
        } else {
            // Handle uploaded file
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = 2048; // 2MB limit
            $config['max_width'] = 2000;
            $config['max_height'] = 2000;
            $config['file_name'] = strtolower(str_replace(' ', '_', $nama)) . '_' . time(); 

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('foto')) {
                $upload_error = $this->upload->display_errors();
            } else {
                $upload_data = $this->upload->data();
                $file_name = $upload_data['file_name'];
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
                'foto' => $file_name,
               
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

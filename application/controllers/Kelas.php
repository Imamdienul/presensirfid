<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Picqer\Barcode\BarcodeGeneratorPNG;

class Kelas extends CI_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->model('m_data');
        $this->load->library('user_agent');
        date_default_timezone_set("asia/jakarta");
    }

public function index()
{
    if (!$this->session->userdata('userlogin')) {
        redirect('login');
        return;
    }

    if ($this->input->post('kelas')) {
        $nama_kelas = $this->input->post('kelas');
        $kelas = ['kelas' => $nama_kelas];
        $this->m_data->insert_kelas($kelas);

        $nama_kelas_clean = strtoupper(str_replace([' ', '-', '/'], '_', $nama_kelas));
        $directory_path = './uploads/foto/' . $nama_kelas_clean . '/';

        if (!is_dir($directory_path)) {
            mkdir($directory_path, 0777, true);
        }

        $data['message'] = "Berhasil menambahkan kelas dan membuat direktori.";
    }

    $data['kelas'] = $this->m_data->get_kelas();
    $data['m_data'] = $this->m_data;
    $this->load->view('i_kelas', $data);
}

    // FUNGSI BARU UNTUK UPLOAD FOTO KE DIREKTORI KELAS
    public function upload_foto_kelas() {
        if (!$this->session->userdata('userlogin')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
            return;
        }

        $id_kelas = $this->input->post('id_kelas');
        
        if (!$id_kelas) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID Kelas tidak ditemukan']));
            return;
        }

        // Get kelas data
        $kelas = $this->m_data->find_kelas($id_kelas);
        if (!$kelas) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Data kelas tidak ditemukan']));
            return;
        }

        // Create directory path
        $nama_kelas_clean = strtoupper(str_replace([' ', '-', '/'], '_', $kelas->kelas));
        $directory_path = './uploads/foto/' . $nama_kelas_clean . '/';

        if (!is_dir($directory_path)) {
            mkdir($directory_path, 0777, true);
        }

        // Check if files were uploaded
        if (empty($_FILES['foto_files']['name'][0])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Tidak ada file yang dipilih']));
            return;
        }

        $uploaded_files = [];
        $failed_files = [];
        $total_files = count($_FILES['foto_files']['name']);

        // Configure upload settings
        $config['upload_path'] = $directory_path;
        $config['allowed_types'] = 'jpg|jpeg|png|gif|bmp';
        $config['max_size'] = 5120; // 5MB
        $config['encrypt_name'] = false;

        $this->load->library('upload');

        // Process each file
        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['foto_files']['error'][$i] !== UPLOAD_ERR_OK) {
                $failed_files[] = $_FILES['foto_files']['name'][$i] . ' (Error: ' . $_FILES['foto_files']['error'][$i] . ')';
                continue;
            }

            // Create individual file array for CodeIgniter upload library
            $_FILES['single_file']['name'] = $_FILES['foto_files']['name'][$i];
            $_FILES['single_file']['type'] = $_FILES['foto_files']['type'][$i];
            $_FILES['single_file']['tmp_name'] = $_FILES['foto_files']['tmp_name'][$i];
            $_FILES['single_file']['error'] = $_FILES['foto_files']['error'][$i];
            $_FILES['single_file']['size'] = $_FILES['foto_files']['size'][$i];

            // Set filename
            $file_extension = pathinfo($_FILES['foto_files']['name'][$i], PATHINFO_EXTENSION);
            $config['file_name'] = $nama_kelas_clean . '_' . time() . '_' . ($i + 1) . '.' . $file_extension;

            $this->upload->initialize($config);

            if ($this->upload->do_upload('single_file')) {
                $upload_data = $this->upload->data();
                $uploaded_files[] = $upload_data['file_name'];
            } else {
                $failed_files[] = $_FILES['foto_files']['name'][$i] . ' (' . strip_tags($this->upload->display_errors()) . ')';
            }
        }

        // Prepare response
        $response = [
            'status' => 'success',
            'message' => 'Upload selesai',
            'uploaded_count' => count($uploaded_files),
            'failed_count' => count($failed_files),
            'uploaded_files' => $uploaded_files,
            'failed_files' => $failed_files,
            'directory' => $directory_path
        ];

        if (count($uploaded_files) > 0 && count($failed_files) > 0) {
            $response['message'] = 'Upload sebagian berhasil. ' . count($uploaded_files) . ' file berhasil, ' . count($failed_files) . ' file gagal.';
        } elseif (count($uploaded_files) > 0) {
            $response['message'] = 'Semua file berhasil diupload. Total: ' . count($uploaded_files) . ' file.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Semua file gagal diupload.';
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // FUNGSI UNTUK MELIHAT FOTO-FOTO DI DIREKTORI KELAS
    public function lihat_foto_kelas($id_kelas = null) {
        if (!$this->session->userdata('userlogin')) {
            redirect('login');
            return;
        }

        if (!$id_kelas) {
            $this->session->set_flashdata('error', 'ID Kelas tidak ditemukan');
            redirect(base_url('kelas'));
            return;
        }

        $kelas = $this->m_data->find_kelas($id_kelas);
        if (!$kelas) {
            $this->session->set_flashdata('error', 'Data kelas tidak ditemukan');
            redirect(base_url('kelas'));
            return;
        }

        // Get photos from directory
        $nama_kelas_clean = strtoupper(str_replace([' ', '-', '/'], '_', $kelas->kelas));
        $directory_path = './uploads/foto/' . $nama_kelas_clean . '/';
        
        $foto_files = [];
        if (is_dir($directory_path)) {
            $files = scandir($directory_path);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
            
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($file_extension, $allowed_extensions)) {
                        $foto_files[] = [
                            'filename' => $file,
                            'path' => $directory_path . $file,
                            'url' => base_url('uploads/foto/' . $nama_kelas_clean . '/' . $file),
                            'size' => filesize($directory_path . $file)
                        ];
                    }
                }
            }
        }

        $data = [
            'kelas' => $kelas,
            'foto_files' => $foto_files,
            'directory_path' => $directory_path
        ];

        $this->load->view('i_foto_kelas', $data);
    }

    // FUNGSI UNTUK HAPUS FOTO DARI DIREKTORI KELAS
    public function hapus_foto_kelas() {
        if (!$this->session->userdata('userlogin')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
            return;
        }

        $id_kelas = $this->input->post('id_kelas');
        $filename = $this->input->post('filename');

        if (!$id_kelas || !$filename) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']));
            return;
        }

        $kelas = $this->m_data->find_kelas($id_kelas);
        if (!$kelas) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Data kelas tidak ditemukan']));
            return;
        }

        $nama_kelas_clean = strtoupper(str_replace([' ', '-', '/'], '_', $kelas->kelas));
        $file_path = './uploads/foto/' . $nama_kelas_clean . '/' . $filename;

        if (file_exists($file_path)) {
            if (unlink($file_path)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'success', 'message' => 'Foto berhasil dihapus']));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menghapus foto']));
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'File tidak ditemukan']));
        }
    }
    
    public function detail_murid($id_murid = null) {
        if (!$this->session->userdata('userlogin')) {
            return;
        }

        if (!$id_murid) {
            echo "Insert ID murid";
            return;
        }

        $this->load->model('m_data');
        $murid = $this->m_data->find_murid($id_murid);

        if (!$murid) {
            echo "Murid tidak ditemukan";
            return;
        }

        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($murid[0]->nisn, $generator::TYPE_CODE_128));

        $this->load->view('i_detail_murid', [
            "murid" => $murid[0],
            "barcode" => $barcode
        ]);
    }

	public function tambah_kelas(){
		if(!$this->session->userdata('userlogin'))   
		{
			return ;
		}
	}

    public function update_phone() {
        if (!$this->session->userdata('userlogin')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
            return;
        }

        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Method not allowed']));
            return;
        }

        $id_siswa = $this->input->post('id_siswa');
        $telp = $this->input->post('telp');

        if (empty($id_siswa) || empty($telp)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']));
            return;
        }

        $telp = trim($telp);
        $clean_phone = str_replace([' ', '-'], '', $telp);

        if (!preg_match('/^[\+]?[0-9]{10,15}$/', $clean_phone)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Format nomor telepon tidak valid (10-15 digit)']));
            return;
        }

        $siswa = $this->m_data->get_siswa_byid($id_siswa);
        if (!$siswa) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Data siswa tidak ditemukan']));
            return;
        }

        $update_data = array(
            'telp' => $telp
        );

        try {
            $result = $this->m_data->updatesiswa($id_siswa, $update_data);
            if ($result) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'success', 'message' => 'Nomor telepon berhasil diupdate']));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal mengupdate database']));
            }
        } catch (Exception $e) {
            log_message('error', 'Error updating phone: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem']));
        }
    }

	public function lihat_kelas() {
		if(!$this->session->userdata('userlogin')) {
			redirect(base_url('login'));
			return;
		}

		$id_kelas = $this->input->get('id_kelas');
		if(!$id_kelas) {
			$this->session->set_flashdata('error', 'ID Kelas tidak ditemukan');
			redirect(base_url('kelas'));
			return;
		}

		$kelas = $this->m_data->find_kelas($id_kelas);
		if(!$kelas) {
			$this->session->set_flashdata('error', 'Data kelas tidak ditemukan');
			redirect(base_url('kelas'));
			return;
		}

		$murid = $this->m_data->get_murid($id_kelas);
		
		$data = [
			'kelas' => $kelas,
			'murid' => $murid
		];

		$this->load->view('i_kelas_detail', $data);
	}

	public function hapus_kelas() {
		if (!$this->session->userdata('userlogin')) {
			redirect(base_url('login'));
			return;
		}

		$id_kelas = $this->input->get('id_kelas');

		if (!$id_kelas) {
			show_error('ID Kelas tidak ditemukan!', 400);
			return;
		}

		$this->m_data->hapus_kelas($id_kelas);
		redirect(base_url('kelas'));
	}

    public function rekap_absen($id_kelas = null) {
        if(!$this->session->userdata('userlogin')) {
            return ;
        }

        if(!$id_kelas){
            echo "insert id kelas";
            return;
        }

        $kelas = $this->m_data->find_kelas($id_kelas);

        if (!$kelas) {
            echo "kelas tidak ditemukan";
            return;
        }

        $rekap_absen = [];
        if (isset($_GET["tanggalMulai"]) && isset($_GET["tanggalSelesai"])) {
            $tanggal_mulai = strtotime($this->input->get('tanggalMulai'));
            $tanggal_selesai = strtotime($this->input->get('tanggalSelesai')) + 86400;

            $rekap_absen = $this->m_data->rekap_absen($id_kelas, $tanggal_mulai, $tanggal_selesai);
            $holidays = $this->m_data->get_holidays($tanggal_mulai, $tanggal_selesai);
        }

        $this->load->view('i_detail_absen', [
            "kelas" => $kelas,
            "rekap_absen" => $rekap_absen,
            "holidays" => isset($holidays) ? $holidays : [],
        ]);
    }

    public function manage_holidays() {
        if(!$this->session->userdata('userlogin')) {
            return ;
        }

        if($this->input->post()) {
            $tanggal = $this->input->post('tanggal');
            $keterangan = $this->input->post('keterangan');
            
            $this->m_data->add_holiday($tanggal, $keterangan);
            $this->session->set_flashdata('success', 'Hari libur berhasil ditambahkan');
            redirect('kelas/manage_holidays');
        }

        $holidays = $this->m_data->get_all_holidays();

        if ($this->agent->is_mobile()) {
            $this->load->view('mobile/i_manage_holidays_mobile', ['holidays' => $holidays]);
        } else {
            $this->load->view('i_manage_holidays', ['holidays' => $holidays]);
        }
    }

    public function hapus_murid($id=null) {
		if($this->session->userdata('userlogin')) { 
			if($this->m_data->siswa_del($id)){
				$this->session->set_flashdata("pesan", "<div class=\"alert alert-success\" id=\"alert\"><i class=\"glyphicon glyphicon-ok\"></i> Data berhasil di hapus</div>");
			}else{
				$this->session->set_flashdata("pesan", "<div class=\"alert alert-danger\" id=\"alert\"><i class=\"glyphicon glyphicon-ok\"></i> Data gagal di hapus</div>");
			}
			redirect('kelas/lihat_kelas?id_kelas=' . $this->input->post('id_kelas'));
		}
	}

    public function delete_holiday($id) {
        if(!$this->session->userdata('userlogin')) {
            return ;
        }

        $this->m_data->delete_holiday($id);
        $this->session->set_flashdata('success', 'Hari libur berhasil dihapus');
        redirect('kelas/manage_holidays');
    }

public function edit_siswa($id = null) {
    if ($this->session->userdata('userlogin')) {
        if (isset($id)) {
            $siswa = $this->m_data->get_siswa_byid($id);
            if (isset($siswa)) {
                foreach ($siswa as $key => $value) {
                    $data['id'] = $value->id_siswa;
                    $data['nama'] = $value->nama;
                    $data['nisn'] = $value->nisn;
                    $data['telp'] = $value->telp;
                    $data['tempat_lahir'] = $value->tempat_lahir;
                    $data['tanggal_lahir'] = $value->tanggal_lahir;
                    $data['kelas'] = $value->id_kelas != null ? $this->m_data->find_kelas($value->id_kelas) : null;
                    $data['alamat'] = $value->alamat;
                    $data['foto'] = $value->foto;
                }
                $data['list_kelas'] = $this->m_data->get_kelas();
                $this->load->view('i_edit_siswa', $data);
            } else {
                redirect('kelas/lihat_kelas?id_kelas=' . $this->input->post('kelas_id'));
            }
        } else {
            redirect('kelas/lihat_kelas?id_kelas=' . $this->input->post('kelas_id'));
        }
    } else {
        redirect(base_url() . 'login');
    }
}

public function save_edit_siswa() {
    if ($this->session->userdata('userlogin')) {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
            $siswa_lama = $this->m_data->get_siswa_byid($id);
            $foto_lama = '';
            if ($siswa_lama) {
                foreach ($siswa_lama as $value) {
                    $foto_lama = $value->foto;
                    break;
                }
            }
            

            $foto = $foto_lama;
            if (!empty($_FILES['foto']['name'])) {
                if (!is_dir('./uploads/foto_siswa/')) {
                    mkdir('./uploads/foto_siswa/', 0777, true);
                }
                
                $config['upload_path'] = './uploads/foto/siswa/';
                $config['allowed_types'] = '*';
                $config['file_name'] = strtolower(str_replace(' ', '_', $this->input->post('nama'))) . '_' . time();
                
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                
                if ($this->upload->do_upload('foto')) {
                    $upload_data = $this->upload->data();
                    $foto = $upload_data['file_name'];
   
                    if ($foto_lama && file_exists('./uploads/foto_siswa/' . $foto_lama) && $foto_lama != 'default.jpg') {
                        @unlink('./uploads/foto_siswa/' . $foto_lama);
                    }
                } else {
                    $error = $this->upload->display_errors();
                    if (strpos($error, 'upload_path_does_not_exist') !== false) {
                        mkdir('./uploads/foto_siswa/', 0777, true);
                        if ($this->upload->do_upload('foto')) {
                            $upload_data = $this->upload->data();
                            $foto = $upload_data['file_name'];
                            
     
                            if ($foto_lama && file_exists('./uploads/foto_siswa/' . $foto_lama) && $foto_lama != 'default.jpg') {
                                @unlink('./uploads/foto_siswa/' . $foto_lama);
                            }
                        } else {
                            $this->session->set_flashdata('pesan', '<div class="alert alert-warning" id="alert"><i class="glyphicon glyphicon-warning-sign"></i> Foto gagal diupload. Data lain tetap diupdate.</div>');
       
                            $foto = $foto_lama;
                        }
                    } else {
                        $this->session->set_flashdata('pesan', '<div class="alert alert-warning" id="alert"><i class="glyphicon glyphicon-warning-sign"></i> Foto gagal diupload. Data lain tetap diupdate.</div>');
                      
                        $foto = $foto_lama;
                    }
                }
            }

            
            $update_data = array(
                'nama' => $this->input->post('nama'),
                'nisn' => $this->input->post('nisn'),
                'telp' => $this->input->post('telp'),
                'tempat_lahir' => $this->input->post('tempat_lahir'),
                'tanggal_lahir' => $this->input->post('tanggal_lahir'),
                'id_kelas' => $this->input->post('kelas_id'),
                'alamat' => $this->input->post('alamat'),
                'foto' => $foto
            );
            
            if ($this->m_data->updatesiswa($id, $update_data)) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" id="alert"><i class="glyphicon glyphicon-ok"></i> Data berhasil diupdate</div>');
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" id="alert"><i class="glyphicon glyphicon-remove"></i> Data gagal diupdate</div>');
            }
            
            redirect('kelas/lihat_kelas?id_kelas=' . $this->input->post('kelas_id'));
        } else {
            $this->session->set_flashdata('error', 'ID tidak ditemukan.');
            redirect('siswa');
        }
    } else {
        redirect(base_url() . 'login');
    }
}
}
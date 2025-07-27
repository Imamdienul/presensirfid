<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_user');
        $this->load->library(['bcrypt', 'form_validation', 'upload']);
        $this->load->helper(['form', 'url', 'file']);
        date_default_timezone_set("Asia/Jakarta");
        
        // Check if user is logged in for all methods except login
        if (!$this->session->userdata('userlogin')) {
            redirect('auth/login');
        }
    }

    public function index() {
        try {
            $data = [
                'set' => 'list-users',
                'users' => $this->m_user->get_users(),
                'title' => 'List Users'
            ];
            $this->load->view('i_users', $data);
        } catch (Exception $e) {
            log_message('error', 'Error in Users::index() - ' . $e->getMessage());
            show_error('Terjadi kesalahan saat memuat data users');
        }
    }
    
    public function add() {
        $data = [
            'set' => 'add-users',
            'title' => 'Tambah User'
        ];
        $this->load->view('i_users', $data);
    }
    
    public function save() {
        // Set validation rules
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[user.email]');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[4]|max_length[50]|is_unique[user.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('users/add');
            return;
        }

        try {
            $avatar_name = $this->_handle_image_upload();
            
            if ($avatar_name === false) {
                redirect('users/add');
                return;
            }

            $user_data = [
                'nama' => $this->input->post('nama', true),
                'email' => $this->input->post('email', true),
                'username' => $this->input->post('username', true),
                'password' => $this->bcrypt->hash_password($this->input->post('password')),
                'avatar' => $avatar_name,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->m_user->insert_user($user_data)) {
                $this->session->set_flashdata('success', 'User berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan user');
            }

        } catch (Exception $e) {
            log_message('error', 'Error in Users::save() - ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat menyimpan data');
        }

        redirect('users');
    }
    
    public function edit($id = null) {
        if (!$id || !is_numeric($id)) {
            show_404();
        }

        try {
            $user = $this->m_user->get_user_by_id($id);
            
            if (!$user) {
                $this->session->set_flashdata('error', 'User tidak ditemukan');
                redirect('users');
                return;
            }

            $data = [
                'set' => 'edit-users',
                'user' => $user,
                'title' => 'Edit User'
            ];
            
            $this->load->view('i_users', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in Users::edit() - ' . $e->getMessage());
            show_error('Terjadi kesalahan saat memuat data user');
        }
    }
    
    public function update() {
        $id = $this->input->post('id');
        
        if (!$id || !is_numeric($id)) {
            show_404();
        }

        // Set validation rules
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback__check_email_unique[' . $id . ']');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[4]|max_length[50]|callback__check_username_unique[' . $id . ']');
        
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('users/edit/' . $id);
            return;
        }

        try {
            $user_data = [
                'nama' => $this->input->post('nama', true),
                'email' => $this->input->post('email', true),
                'username' => $this->input->post('username', true),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update password if provided
            if ($this->input->post('password')) {
                $user_data['password'] = $this->bcrypt->hash_password($this->input->post('password'));
            }

            // Handle image upload
            if (!empty($_FILES['image']['name'])) {
                $new_avatar = $this->_handle_image_upload();
                if ($new_avatar !== false) {
                    // Delete old avatar
                    $old_user = $this->m_user->get_user_by_id($id);
                    if ($old_user && $old_user->avatar && $old_user->avatar !== 'default.png') {
                        $this->_delete_image($old_user->avatar);
                    }
                    $user_data['avatar'] = $new_avatar;
                } else {
                    redirect('users/edit/' . $id);
                    return;
                }
            }

            if ($this->m_user->update_user($id, $user_data)) {
                $this->session->set_flashdata('success', 'User berhasil diupdate');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengupdate user');
            }

        } catch (Exception $e) {
            log_message('error', 'Error in Users::update() - ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat mengupdate data');
        }

        redirect('users');
    }
    
    public function delete($id = null) {
        if (!$id || !is_numeric($id)) {
            show_404();
        }

        // Prevent deletion of admin user
        if ($id == 1) {
            $this->session->set_flashdata('error', 'Admin user tidak dapat dihapus');
            redirect('users');
            return;
        }

        try {
            $user = $this->m_user->get_user_by_id($id);
            
            if (!$user) {
                $this->session->set_flashdata('error', 'User tidak ditemukan');
                redirect('users');
                return;
            }

            // Delete avatar file
            if ($user->avatar && $user->avatar !== 'default.png') {
                $this->_delete_image($user->avatar);
            }

            if ($this->m_user->delete_user($id)) {
                $this->session->set_flashdata('success', 'User berhasil dihapus');
            } else {
                $this->session->set_flashdata('error', 'Gagal menghapus user');
            }

        } catch (Exception $e) {
            log_message('error', 'Error in Users::delete() - ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat menghapus data');
        }

        redirect('users');
    }

    // Private methods
    private function _handle_image_upload() {
        if (empty($_FILES['image']['name'])) {
            return 'default.png';
        }

        $config = [
            'upload_path' => './assets/images/',
            'allowed_types' => 'gif|jpg|jpeg|png',
            
            'file_name' => uniqid() . '_' . time()
        ];

        $this->upload->initialize($config);

        if ($this->upload->do_upload('image')) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        } else {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            return false;
        }
    }

    private function _delete_image($filename) {
        $path = './assets/images/' . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // Custom validation callbacks
    public function _check_email_unique($email, $id) {
        if ($this->m_user->is_email_exists($email, $id)) {
            $this->form_validation->set_message('_check_email_unique', 'Email sudah digunakan');
            return false;
        }
        return true;
    }

    public function _check_username_unique($username, $id) {
        if ($this->m_user->is_username_exists($username, $id)) {
            $this->form_validation->set_message('_check_username_unique', 'Username sudah digunakan');
            return false;
        }
        return true;
    }
}
<?php


class Whatsapp extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_api');
        $this->load->library('session');
    }

    public function index() {
        $data['whatsapp_config'] = $this->m_api->get_whatsapp_config();
        $this->load->view('i_whatsapp', $data);
    }

    public function updateWhatsappConfig() {
        $api_key = $this->input->post('api_key');
        $api_url = $this->input->post('api_url');
        $status = $this->input->post('status');
        
        if (empty($api_key) || empty($api_url)) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">API Key dan API URL tidak boleh kosong!</div>');
            redirect('whatsapp');
            return;
        }
        
        $data = array(
            'api_key' => $api_key,
            'api_url' => $api_url,
            'status' => $status ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        if ($this->m_api->check_whatsapp_config_exists()) {
            if ($this->m_api->update_whatsapp_config($data)) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-success">Konfigurasi WhatsApp berhasil diperbarui!</div>');
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal memperbarui konfigurasi WhatsApp!</div>');
            }
        } else {
            $data['id'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            if ($this->m_api->insert_whatsapp_config($data)) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-success">Konfigurasi WhatsApp berhasil disimpan!</div>');
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal menyimpan konfigurasi WhatsApp!</div>');
            }
        }
        
        redirect('whatsapp');
    }
    
    public function testWhatsapp() {
        $phone = $this->input->post('test_phone');
        $message = $this->input->post('test_message');
        
        if (empty($phone) || empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor HP dan pesan tidak boleh kosong']);
            return;
        }
        
        $wa_config = $this->m_api->get_whatsapp_config();
        
        if (!$wa_config || empty($wa_config->api_key)) {
            echo json_encode(['status' => 'error', 'message' => 'Konfigurasi WhatsApp belum diatur']);
            return;
        }
        
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        $body = array(
            "api_key" => $wa_config->api_key,
            "receiver" => $phone,
            "data" => array("message" => $message)
        );
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $wa_config->api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => [
                "Accept: */*",
                "Content-Type: application/json",
            ],
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $err]);
        } else {
            $response_data = json_decode($response, true);
            if ($response_data && isset($response_data['status']) && $response_data['status'] == 'success') {
                echo json_encode(['status' => 'success', 'message' => 'Pesan berhasil dikirim!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim pesan: ' . $response]);
            }
        }
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('M_Absensi');
        
        date_default_timezone_set("asia/jakarta");
    }
    
    public function index() {
        $data['set'] = "absensi";
        $today = strtotime("today");
        $tomorrow = strtotime("tomorrow");
        
        // Ambil data dengan join yang sudah dioptimasi
        $data['absensimasuk'] = $this->M_Absensi->get_absensi_optimized("masuk", $today, $tomorrow);
        $data['absensikeluar'] = $this->M_Absensi->get_absensi_optimized("keluar", $today, $tomorrow);
        
        $this->load->view('i_absensi', $data);
    }
    
    public function sse_updates() {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        
        // Set time limit untuk mencegah hanging
        set_time_limit(0);
        ignore_user_abort(true);
        
        $lastUpdate = $this->input->get('lastUpdate') ?: 0;
        $maxIterations = 20; // Batasi iterasi
        $iteration = 0;
        
        while ($iteration < $maxIterations && connection_status() == CONNECTION_NORMAL) {
            $today = strtotime("today");
            $tomorrow = strtotime("tomorrow");
            
            // Gunakan method yang sudah dioptimasi
            $absensimasuk = $this->M_Absensi->get_absensi_optimized("masuk", $today, $tomorrow);
            $absensikeluar = $this->M_Absensi->get_absensi_optimized("keluar", $today, $tomorrow);
            
            $latestUpdate = max(
                $this->getLatestTimestamp($absensimasuk),
                $this->getLatestTimestamp($absensikeluar)
            );
            
            if ($latestUpdate > $lastUpdate) {
                $lastUpdate = $latestUpdate;
                
                $data = json_encode([
                    'absensimasuk' => $absensimasuk,
                    'absensikeluar' => $absensikeluar,
                    'timestamp' => time()
                ]);
                
                echo "data: $data\n\n";
                
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }
            
            $iteration++;
            sleep(5); // Tingkatkan interval untuk mengurangi beban server
        }
    }
    
    private function getLatestTimestamp($data) {
        if (empty($data)) return 0;
        
        return array_reduce($data, function($carry, $item) {
            return max($carry, $item->created_at);
        }, 0);
    }
    
    public function lastabsensi() {
        if(!$this->session->userdata('userlogin')) {
            redirect(base_url().'login');
            return;
        }
        
        if (!isset($_POST['tanggal'])) {
            redirect(base_url().'absensi');
            return;
        }
        
        $tgl = $this->input->post('tanggal');
        $split1 = explode("-", $tgl);
        
        if (count($split1) != 2) {
            redirect(base_url().'absensi');
            return;
        }
        
        $ts1 = strtotime($split1[0]);
        $ts2 = strtotime($split1[1]) + 86400; // Tambah 1 hari
        
        if (!$ts1 || !$ts2) {
            redirect(base_url().'absensi');
            return;
        }
        
        $data['datamasuk'] = $this->M_Absensi->get_absensi_optimized("masuk", $ts1, $ts2);
        $data['datakeluar'] = $this->M_Absensi->get_absensi_optimized("keluar", $ts1, $ts2);
        $data['tanggal'] = date("d-M-Y", $ts1) . " - " . date("d-M-Y", $ts2 - 86400);
        $data['waktuabsensi'] = date("d-M-Y", $ts1) . "_" . date("d-M-Y", $ts2 - 86400);
        $data['set'] = "last-absensi";
        
        $this->load->view('v_absensi', $data);
    }
    
    // Method untuk AJAX loading data
    public function get_absensi_ajax() {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }
        
        $today = strtotime("today");
        $tomorrow = strtotime("tomorrow");
        
        $absensimasuk = $this->M_Absensi->get_absensi_optimized("masuk", $today, $tomorrow);
        $absensikeluar = $this->M_Absensi->get_absensi_optimized("keluar", $today, $tomorrow);
        
        $response = [
            'status' => 'success',
            'absensimasuk' => $absensimasuk,
            'absensikeluar' => $absensikeluar,
            'timestamp' => time()
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
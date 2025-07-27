<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Absensi extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    // Method yang dipanggil oleh controller
    public function get_absensi_optimized($keterangan, $start_time, $end_time) {
        $this->db->select('
            a.id_absensi,
            a.keterangan,
            a.created_at,
            a.foto,
            s.nama,
            s.nisn,
            s.kelas,
            d.nama_devices
        ');
        $this->db->from('absensi a');
        $this->db->join('siswa s', 'a.id_siswa = s.id_siswa', 'left');
        $this->db->join('devices d', 'a.id_devices = d.id_devices', 'left');
        $this->db->where('a.keterangan', $keterangan);
        $this->db->where('a.created_at >=', $start_time);
        $this->db->where('a.created_at <', $end_time);
        $this->db->order_by('a.created_at', 'DESC');
        
        $query = $this->db->get();
        return $query->result();
    }
    
    // Method untuk absensi masuk
    public function absen_masuk($nisn, $id_devices) {
        $id_siswa = $this->get_id_siswa_by_nisn($nisn);
        if (!$id_siswa) {
            return false;
        }
        
        $data = array(
            'id_devices' => $id_devices,
            'id_siswa' => $id_siswa,
            'keterangan' => 'masuk',
            'foto' => '', 
            'created_at' => time() 
        );
        
        return $this->db->insert('absensi', $data);
    }
    
    // Method untuk absensi keluar
    public function absen_keluar($nisn, $id_devices) {
        $id_siswa = $this->get_id_siswa_by_nisn($nisn);
        if (!$id_siswa) {
            return false;
        }
        
        $data = array(
            'id_devices' => $id_devices,
            'id_siswa' => $id_siswa,
            'keterangan' => 'keluar',
            'foto' => '',
            'created_at' => time()
        );
        
        return $this->db->insert('absensi', $data);
    }
    
    // Method untuk absensi izin
    public function absen_izin($nisn, $id_devices) {
        $id_siswa = $this->get_id_siswa_by_nisn($nisn);
        if (!$id_siswa) {
            return false;
        }
        
        $data = array(
            'id_devices' => $id_devices,
            'id_siswa' => $id_siswa,
            'keterangan' => 'izin',
            'foto' => '',
            'created_at' => time()
        );
        
        return $this->db->insert('absensi', $data);
    }
    
    // Method untuk absensi sakit
    public function absen_sakit($nisn, $id_devices) {
        $id_siswa = $this->get_id_siswa_by_nisn($nisn);
        if (!$id_siswa) {
            return false;
        }
        
        $data = array(
            'id_devices' => $id_devices,
            'id_siswa' => $id_siswa,
            'keterangan' => 'sakit',
            'foto' => '',
            'created_at' => time()
        );
        
        return $this->db->insert('absensi', $data);
    }
    
    // Helper method untuk mendapatkan ID siswa berdasarkan NISN
    private function get_id_siswa_by_nisn($nisn) {
        $this->db->select('id_siswa');
        $this->db->where('nisn', $nisn);
        $query = $this->db->get('siswa');
        $result = $query->row();
        return $result ? $result->id_siswa : null;
    }
    
    // Method untuk cek apakah sudah absen hari ini
    public function is_already_absent($nisn, $keterangan) {
        $today = date("Y-m-d");
        $beginning_of_today = strtotime('midnight', strtotime($today));
        $beginning_of_tomorrow = strtotime('+1 day', $beginning_of_today);
        
        $id_siswa = $this->get_id_siswa_by_nisn($nisn);
        if (!$id_siswa) {
            return false;
        }
        
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('keterangan', $keterangan);
        $this->db->where('created_at >=', $beginning_of_today);
        $this->db->where('created_at <', $beginning_of_tomorrow);
        $query = $this->db->get('absensi');
        
        return $query->num_rows() > 0;
    }
    
    // Method untuk cek NISN terdaftar
    public function is_registered_nisn($nisn) {
        $this->db->where('nisn', $nisn);
        $query = $this->db->get('siswa');
        return $query->num_rows() > 0;
    }
    
    // Method untuk mendapatkan data siswa berdasarkan NISN
    public function get_siswa_by_nisn($nisn) {
        $this->db->where('nisn', $nisn);
        $query = $this->db->get('siswa');
        return $query->row();
    }
    
    // Method untuk mendapatkan statistik absensi
    public function get_absensi_stats($date_start = null, $date_end = null) {
        if (!$date_start) $date_start = strtotime('today');
        if (!$date_end) $date_end = strtotime('tomorrow');
        
        $this->db->select('keterangan, COUNT(*) as jumlah');
        $this->db->where('created_at >=', $date_start);
        $this->db->where('created_at <', $date_end);
        $this->db->group_by('keterangan');
        $query = $this->db->get('absensi');
        
        return $query->result();
    }
}
?>
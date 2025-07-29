<?php
class Siswa extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_kelas() {
        $this->db->select('id, kelas');
        $this->db->from('kelas');
        $this->db->order_by('kelas', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    
    public function insert_siswa($data) {
        // Validasi data sebelum insert
        if (empty($data['nama']) || empty($data['nisn']) || empty($data['telp'])) {
            return false;
        }
        
        // Cek duplikasi NISN
        $this->db->where('nisn', $data['nisn']);
        $existing = $this->db->get('siswa');
        if ($existing->num_rows() > 0) {
            return false;
        }
        
        return $this->db->insert('siswa', $data);
    }
    
    public function get_siswa_by_nisn($nisn) {
        $this->db->where('nisn', $nisn);
        $query = $this->db->get('siswa');
        return $query->row();
    }
    
    public function update_siswa($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('siswa', $data);
    }
    
    public function delete_siswa($id) {
        $this->db->where('id', $id);
        return $this->db->delete('siswa');
    }
}
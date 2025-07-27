<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Absensi extends CI_Model {
    
    // Cache untuk menyimpan data kelas
    private $kelas_cache = [];
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    // Method asli (tetap ada untuk backward compatibility)
    function get_absensi($ket, $today, $tomorrow) {
        $this->db->select('*');
        $this->db->from('absensi');
        $this->db->join('devices', 'absensi.id_devices=devices.id_devices', 'inner');
        $this->db->join('siswa', 'absensi.id_siswa=siswa.id_siswa', 'inner');
        $this->db->where("keterangan", $ket);
        $this->db->where("created_at >=", $today);
        $this->db->where("created_at <", $tomorrow);
        $this->db->order_by('created_at', 'DESC'); // Urutkan terbaru dulu
        
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return [];
    }
    
    // Method yang dioptimasi dengan JOIN yang lebih efisien
    public function get_absensi_optimized($ket, $today, $tomorrow) {
        $this->db->select('
            absensi.id_absensi,
            absensi.id_siswa,
            absensi.id_devices,
            absensi.keterangan,
            absensi.created_at,
            devices.nama_devices,
            siswa.nama,
            siswa.id_kelas,
            kelas.kelas
        ');
        $this->db->from('absensi');
        $this->db->join('devices', 'absensi.id_devices = devices.id_devices', 'inner');
        $this->db->join('siswa', 'absensi.id_siswa = siswa.id_siswa', 'inner');
        $this->db->join('kelas', 'siswa.id_kelas = kelas.id', 'left'); // LEFT JOIN untuk handle null
        $this->db->where('absensi.keterangan', $ket);
        $this->db->where('absensi.created_at >=', $today);
        $this->db->where('absensi.created_at <', $tomorrow);
        $this->db->order_by('absensi.created_at', 'DESC');
        
        // Gunakan query caching untuk performa lebih baik
        $this->db->cache_on();
        $query = $this->db->get();
        $this->db->cache_off();
        
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return [];
    }
    
    // Method untuk mendapatkan absensi dengan pagination
    public function get_absensi_paginated($ket, $today, $tomorrow, $limit = 50, $offset = 0) {
        $this->db->select('
            absensi.id_absensi,
            absensi.id_siswa,
            absensi.id_devices,
            absensi.keterangan,
            absensi.created_at,
            devices.nama_devices,
            siswa.nama,
            siswa.id_kelas,
            kelas.kelas
        ');
        $this->db->from('absensi');
        $this->db->join('devices', 'absensi.id_devices = devices.id_devices', 'inner');
        $this->db->join('siswa', 'absensi.id_siswa = siswa.id_siswa', 'inner');
        $this->db->join('kelas', 'siswa.id_kelas = kelas.id', 'left');
        $this->db->where('absensi.keterangan', $ket);
        $this->db->where('absensi.created_at >=', $today);
        $this->db->where('absensi.created_at <', $tomorrow);
        $this->db->order_by('absensi.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return [];
    }
    
    // Method untuk count total data (untuk pagination)
    public function count_absensi($ket, $today, $tomorrow) {
        $this->db->from('absensi');
        $this->db->where('keterangan', $ket);
        $this->db->where('created_at >=', $today);
        $this->db->where('created_at <', $tomorrow);
        
        return $this->db->count_all_results();
    }
    
    // Optimasi method find_kelas dengan caching
    public function find_kelas($id_kelas) {
        if (isset($this->kelas_cache[$id_kelas])) {
            return $this->kelas_cache[$id_kelas];
        }
        
        $this->db->select('*');
        $this->db->from('kelas');
        $this->db->where('id_kelas', $id_kelas);
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $result = $query->row();
            $this->kelas_cache[$id_kelas] = $result;
            return $result;
        }
        
        return null;
    }
    
    // Method untuk mendapatkan data terbaru (untuk real-time update)
    public function get_latest_absensi($ket, $timestamp) {
        $this->db->select('
            absensi.id_absensi,
            absensi.id_siswa,
            absensi.id_devices,
            absensi.keterangan,
            absensi.created_at,
            devices.nama_devices,
            siswa.nama,
            siswa.id_kelas,
            kelas.kelas
        ');
        $this->db->from('absensi');
        $this->db->join('devices', 'absensi.id_devices = devices.id_devices', 'inner');
        $this->db->join('siswa', 'absensi.id_siswa = siswa.id_siswa', 'inner');
        $this->db->join('kelas', 'siswa.id_kelas = kelas.id', 'left');
        $this->db->where('absensi.keterangan', $ket);
        $this->db->where('absensi.created_at >', $timestamp);
        $this->db->order_by('absensi.created_at', 'DESC');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return [];
    }
    
    // Method asli yang sudah ada (tetap untuk compatibility)
    public function get_absensii($keterangan, $start, $end, $kelas_id = null) {
        $this->db->select('absensi.*')
                 ->from('absensi')
                 ->join('siswa', 'siswa.id_siswa = absensi.id_siswa')
                 ->where('created_at >=', $start)
                 ->where('created_at <=', $end)
                 ->where('keterangan', $keterangan);
        
        if ($kelas_id) {
            $this->db->where('siswa.id_kelas', $kelas_id);
        }
        
        return $this->db->get()->result();
    }
    
    // Clear cache method (untuk dipanggil saat ada update data kelas)
    public function clear_kelas_cache() {
        $this->kelas_cache = [];
    }
}
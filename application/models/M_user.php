<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_user extends CI_Model
{

    private $table = 'user';
    private $primary_key = 'id_user';

    /**
     * Get all users with optional filters
     */
    public function get_users($limit = null, $offset = null, $search = null)
    {
        $this->db->select('*');
        $this->db->from($this->table);

        if ($search) {
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('username', $search);
            $this->db->group_end();
        }

        $this->db->order_by($this->primary_key, 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result() : [];
    }

    /**
     * Get user by ID
     */
    public function get_user_by_id($id)
    {
        if (!$id || !is_numeric($id)) {
            return false;
        }

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where($this->primary_key, $id);
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->row() : false;
    }

    /**
     * Get user by username
     */
    public function get_user_by_username($username)
    {
        if (!$username) {
            return false;
        }

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('username', $username);
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->row() : false;
    }

    /**
     * Get user by email
     */
    public function get_user_by_email($email)
    {
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('email', $email);
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->row() : false;
    }

    /**
     * Insert new user
     */
    public function insert_user($data)
    {
        // Validate required fields
        $required_fields = ['nama', 'email', 'username', 'password'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                log_message('error', "Missing required field: {$field}");
                return false;
            }
        }

        // Set defaults
        if (!isset($data['avatar'])) {
            $data['avatar'] = 'default.png';
        }

        // if (!isset($data['status'])) {
        //     $data['status'] = 'active';
        // }

        // if (!isset($data['created_at'])) {
        //     $data['created_at'] = date('Y-m-d H:i:s');
        // }

        unset($data['status']);
        unset($data['created_at']);

        try {
            $this->db->trans_begin();
            $this->db->insert($this->table, $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            }

            $this->db->trans_commit();
            return $this->db->insert_id();
        } catch (Exception $e) {
            $this->db->trans_rollback();

            dd($e->getMessage());
            log_message('error', 'Error inserting user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user data
     */
    public function update_user($id, $data)
    {
        if (!$id || !is_numeric($id) || empty($data)) {
            return false;
        }

        // Set updated timestamp
        // $data['updated_at'] = date('Y-m-d H:i:s');
        unset($data['updated_at']);

        try {
            $this->db->trans_begin();
            $this->db->where($this->primary_key, $id);
            $this->db->update($this->table, $data);

            if ($this->db->trans_status() === FALSE || $this->db->affected_rows() == 0) {
                $this->db->trans_rollback();
                return false;
            }

            $this->db->trans_commit();
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error updating user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user
     */
    public function delete_user($id)
    {
        if (!$id || !is_numeric($id)) {
            return false;
        }

        // Prevent deletion of admin user (ID = 1)
        if ($id == 1) {
            return false;
        }

        try {
            $this->db->trans_begin();
            $this->db->where($this->primary_key, $id);
            $this->db->delete($this->table);

            if ($this->db->trans_status() === FALSE || $this->db->affected_rows() == 0) {
                $this->db->trans_rollback();
                return false;
            }

            $this->db->trans_commit();
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error deleting user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if username exists
     */
    public function is_username_exists($username, $exclude_id = null)
    {
        if (!$username) {
            return false;
        }

        $this->db->where('username', $username);

        if ($exclude_id && is_numeric($exclude_id)) {
            $this->db->where($this->primary_key . ' !=', $exclude_id);
        }

        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * Check if email exists
     */
    public function is_email_exists($email, $exclude_id = null)
    {
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $this->db->where('email', $email);

        if ($exclude_id && is_numeric($exclude_id)) {
            $this->db->where($this->primary_key . ' !=', $exclude_id);
        }

        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * Count total users
     */
    public function count_users($search = null)
    {
        if ($search) {
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('username', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results($this->table);
    }

    /**
     * Get active users only
     */
    public function get_active_users()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status', 'active');
        $this->db->order_by($this->primary_key, 'DESC');

        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result() : [];
    }

    /**
     * Update user status
     */
    public function update_status($id, $status)
    {
        if (!$id || !is_numeric($id) || !in_array($status, ['active', 'inactive'])) {
            return false;
        }

        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->update_user($id, $data);
    }

    /**
     * Update user password
     */
    public function update_password($id, $new_password)
    {
        if (!$id || !is_numeric($id) || !$new_password) {
            return false;
        }

        $data = [
            'password' => $new_password,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->update_user($id, $data);
    }

    /**
     * Update user avatar
     */
    public function update_avatar($id, $avatar_filename)
    {
        if (!$id || !is_numeric($id) || !$avatar_filename) {
            return false;
        }

        $data = [
            'avatar' => $avatar_filename,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->update_user($id, $data);
    }

    /**
     * Update last login timestamp
     */
    public function update_last_login($id)
    {
        if (!$id || !is_numeric($id)) {
            return false;
        }

        $data = [
            'last_login' => date('Y-m-d H:i:s')
        ];

        return $this->update_user($id, $data);
    }

    /**
     * Get users with pagination
     */
    public function get_users_paginated($limit, $offset, $search = null)
    {
        return $this->get_users($limit, $offset, $search);
    }

    /**
     * Search users
     */
    public function search_users($keyword)
    {
        if (!$keyword) {
            return [];
        }

        return $this->get_users(null, null, $keyword);
    }

    /**
     * Get user statistics
     */
    public function get_user_stats()
    {
        $stats = [];

        // Total users
        $stats['total'] = $this->db->count_all($this->table);

        // Active users
        $this->db->where('status', 'active');
        $stats['active'] = $this->db->count_all_results($this->table);

        // Inactive users
        $stats['inactive'] = $stats['total'] - $stats['active'];

        // Users registered this month
        $this->db->where('MONTH(created_at)', date('m'));
        $this->db->where('YEAR(created_at)', date('Y'));
        $stats['this_month'] = $this->db->count_all_results($this->table);

        return $stats;
    }
}

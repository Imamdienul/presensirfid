<?php

if (!function_exists('old')) {
    function old($key, $default = '')
    {
        $CI = &get_instance();
        $old_input = $CI->session->flashdata('old_input');
        return isset($old_input[$key]) ? htmlspecialchars($old_input[$key], ENT_QUOTES, 'UTF-8') : $default;
    }
}
if (!function_exists('set_old_input')) {
    function set_old_input($data)
    {
        $CI = &get_instance();
        $CI->session->set_flashdata('old_input', $data);
    }
}

<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Uploads URL
 * 
 * Create a local URL to your assets based on your basepath.
 *
 * @access      public
 * @param   	string
 * @return      string
 */
if (!function_exists('upload_url')) {
    function upload_url($uri = '', $group = FALSE) {
        $CI = & get_instance();
        
        if (!$dir = $CI->config->item('uploads_path')) {
            $dir = UPLOAD;
        }
        
        if ($group) {
            return $CI->config->base_url($dir . $group . '/' . $uri);
        } else {
            return $CI->config->base_url($dir . $uri);
        }
    }
}

/* End of file uploads_helper.php */
/* Location: ./application/helpers/assets_helper.php */
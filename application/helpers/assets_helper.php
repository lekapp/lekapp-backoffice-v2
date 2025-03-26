<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Asset URL
 * 
 * Create a local URL to your assets based on your basepath.
 *
 * @access      public
 * @param   	string
 * @return      string
 */
if (!function_exists('asset_url')) {
    function asset_url($uri = '', $group = FALSE) {
        $CI = & get_instance();
        
        if (!$dir = $CI->config->item('assets_path')) {
            $dir = ASSETS;
        }
        
        if ($group) {
            return $CI->config->base_url($dir . $group . '/' . $uri);
        } else {
            return $CI->config->base_url($dir . $uri);
        }
    }
}

if (!function_exists('asset_img')) {
    function asset_img($uri = '', $group = FALSE) {
        $CI = & get_instance();
        
        if (!$dir = $CI->config->item('assets_path')) {
            $dir = ASSETS . IMG;
        }
        
        if ($group) {
            return $CI->config->base_url($dir . $group . '/' . $uri);
        } else {
            return $CI->config->base_url($dir . $uri);
        }
    }
}

if (!function_exists('asset_js')) {
    function asset_js($uri = '', $group = FALSE) {
        $CI = & get_instance();
        
        if (!$dir = $CI->config->item('assets_path')) {
            $dir = ASSETS . JS;
        }
        
        if ($group) {
            return $CI->config->base_url($dir . $group . '/' . $uri);
        } else {
            return $CI->config->base_url($dir . $uri);
        }
    }
}

if (!function_exists('asset_css')) {
    function asset_css($uri = '', $group = FALSE) {
        $CI = & get_instance();
        
        if (!$dir = $CI->config->item('assets_path')) {
            $dir = ASSETS . CSS;
        }
        
        if ($group) {
            return $CI->config->base_url($dir . $group . '/' . $uri);
        } else {
            return $CI->config->base_url($dir . $uri);
        }
    }
}

if (!function_exists('asset_user')) {
    function asset_user($uri = '', $group = FALSE) {
        $CI = & get_instance();
        
        if (!$dir = $CI->config->item('assets_path')) {
            $dir = ASSETS . UPLOADS . USER;
        }
        
        if ($group) {
            return $CI->config->base_url($dir . $group . '/' . $uri);
        } else {
            return $CI->config->base_url($dir . $uri);
        }
    }
}

if (!function_exists('rm_asset_user')) {
    function rm_asset_user($dir = '') {
        if (is_dir($dir)) { 
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (filetype($dir."/".$object) == "dir") 
                        rm_asset_user($dir."/".$object); 
                    else unlink($dir."/".$object); 
                }
            }
            reset($objects); 
            rmdir($dir); 
        } 
    }
}


/* End of file assets_helper.php */
/* Location: ./application/helpers/assets_helper.php */
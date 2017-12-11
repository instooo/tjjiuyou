<?php
//register_shutdown_function(function(){ var_dump(error_get_last()); });
define('ROOT_PATH', dirname(__FILE__));
require_once 'lib/Log.php';
require_once 'lib/Model.php';

if (!function_exists('array_column')) {
    function array_column($arr, $col, $key = '') {
        if (!$arr) return false;
        $res = array();
        foreach ($arr as $val) {
            if ($key) $res[$val[$key]] = $val[$col];
            else $res[] = $val[$col];
        }
        return $res;
    }
}
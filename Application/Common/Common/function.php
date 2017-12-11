<?php
/**
 * 公共函数
 * Created by qinfan qf19910623@gmail.com.
 * Date: 2016/10/17
 */

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

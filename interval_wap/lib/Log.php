<?php
/**
 * Log日志类
 * Created by qinfan qf19910623@gmail.com
 * Date: 2016/9/26
 * Time: 10:18
 */
class Log{
    static function getLogPath($filename = '') {
        $filename = $filename?$filename:date('Ymd');
        $filename = trim($filename, '/');

        $filepath = ROOT_PATH.'/log/'.$filename;
        $dirname = dirname($filepath);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777,1);
        }

        return $filepath;
    }
    static function saveLog($content, $filename = '') {
        $filepath = Log::getLogPath($filename);
        return file_put_contents($filepath, $content."\r\n", FILE_APPEND);
    }
}
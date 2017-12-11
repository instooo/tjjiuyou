<?php
/**
 * 数据库模型
 * Created by qinfan qf19910623@gmail.com.
 * Date: 2016/9/23
 * Time: 9:33
 */
defined('API') or die('access denied');
class Model {
    public $db;
    public $error;
    public $dsn;
    public $sql;

    public function __construct($arr) {
        $this->error = '';
        $this->dsn = "mysql:dbname={$arr['dbname']};host={$arr['host']};port={$arr['port']};";
        try {
            $this->db = new PDO($this->dsn, $arr['username'], $arr['password'],array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ));
        }catch (Exception $e) {
            die('connect mysql server fail.');
        }
        $this->db->exec('SET NAMES utf8');
    }

    public function select($sql) {
        $this->sql = $sql;
        $result = $this->db->query($this->sql);
        if (false === $result) {
            $errinfo = $this->db->errorInfo();
            $this->error = 'Code:'.$errinfo[1].' Msg:'.$errinfo[2];
            return false;
        }
        $result->setFetchMode(PDO::FETCH_ASSOC);
        return  $result->fetchAll();
    }

    public function query($sql) {
        $this->sql = $sql;
        $rows = $this->db->exec($this->sql);
        if (false === $rows) {
            $errinfo = $this->db->errorInfo();
            $this->error = 'Code:'.$errinfo[1].' Msg:'.$errinfo[2];
            return false;
        }
        return $rows;
    }
}
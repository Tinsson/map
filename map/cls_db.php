<?php

class db
{
    var $query_count = 0;
    var $host;
    var $user;
    var $password;
    var $database;
    var $db;
    var $result;
    
    var $rs_type = MYSQLI_ASSOC;
    var $query_times = 0;
    var $conn_times = 0;
    var $unbuffered = false;
    
    var $query_list;
    var $debug = false;
    
    function __construct($config=array())
    {
        /*
        $this->host = $config['host'] ? $config['host'] : '127.0.0.1';
        $this->user = $config['user'] ? $config['user'] : 'root';
        $this->password = $config['password'] ? $config['password'] : ''; 
        $this->database = $config['database'] ? $config['database'] : 'map_application';
        $this->debug = $config['debug'] ? $config['debug'] : false;
        */
        $this->host = '127.0.0.1';
        $this->user = 'root';
        $this->password = '123456';
        $this->database = 'map_application';
        $this->debug = false;
        if ($this->database) {
            $flag = $this->connect($this->database);
            if (!$flag) {
                $this->db = false;
                return false;
            }
        }
        return true;
    }
    
    function connect($database='')
    {
        $start_time = $this->time_used();
        $this->db = new mysqli($this->host, $this->user, $this->password, $this->database);
        if (!$this->db) {
            return false;
        }
        $this->db->query("SET NAMES utf8");
        $end_time = $this->time_used();
        $this->conn_times += round($end_time - $start_time, 5);
        return true;
    }
    
    function query($sql)
    {
        if($this->debug) {
            $sqlkey = md5($sql);
            if($this->querylist) {
                $qlist = array_keys($this->querylist);
                if(in_array($sqlkey,$qlist)) {
                    $count = $this->querylist[$sqlkey]["count"] + 1;
                    $this->querylist[$sqlkey] = array("sql"=>$sql,"count"=>$count);
                } else {
                    $this->querylist[$sqlkey] = array("sql"=>$sql,"count"=>1);
                }
            } else{
                $this->querylist[$sqlkey] = array("sql"=>$sql,"count"=>1);
            }
        }
        $start_time = $this->time_used();
        $this->result = $this->db->query($sql);
        $this->query_count++;
        $end_time = $this->time_used();
        $this->query_times += round($end_time - $start_time, 5);
        if(!$this->result) {
            return false;
        }
        return $this->result;
    }
    
    function get_one($sql='')
    {
        $start_time = $this->time_used();
        $result = $sql ? $this->query($sql) : $this->result;
        if (!$result) {
            return false;
        } 
        $rows = $result->fetch_array($this->rs_type);
        $end_time = $this->time_used();
        $this->query_times += round($end_time - $start_time, 5);
        return $rows;
    }
    
    function time_used()
    {
        $time = explode(' ', microtime());
        $used_time = $time[0] + $time[1];
        return $used_time;
    }
    
    
}
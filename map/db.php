<?php
header('Content-type:text/html;charset=utf-8');
$db = new mysqli('127.0.0.1', 'root', '', 'map_application');
if (!$db) {
    die('System Error!');
}
$db->query('SET NAMES UTF-8');

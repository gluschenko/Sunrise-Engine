<?php
$config;

$config['root'] = dirname(__FILE__); //$_SERVER['DOCUMENT_ROOT'];
$config['engine_dir'] = "engine";
$config['engine_root'] = $config['root']."/".$config['engine_dir'];

$config['domain'] = $_SERVER['HTTP_HOST'];

//

$config['mysql_host'] = "localhost"; //Палево
$config['mysql_user'] = "user"; //Палево
$config['mysql_password'] = "password"; //Палево
$config['mysql_name'] = "database"; //Палево

$config['salt'] = "123"; //Палево
$config['sig_salt'] = "456"; //Палево
$config['admin_salt'] = "789"; //Палево

$config['admin_email'] = "user@gmail.com"; // Email администратора (ОБЯЗАТЕЛЬНО ИЗМЕНИТЬ НА СВОЙ)

?>
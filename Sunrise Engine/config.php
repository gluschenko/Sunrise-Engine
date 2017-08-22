<?php
$config;

$config['root'] = $_SERVER['DOCUMENT_ROOT'];
$config['engine_dir'] = "engine";
$config['engine_root'] = $_SERVER['DOCUMENT_ROOT']."/".$config['engine_dir'];

$config['domain'] = $_SERVER['HTTP_HOST'];

//

$config['mysql_host'] = "localhost"; //Палево
$config['mysql_user'] = "user"; //Палево
$config['mysql_password'] = "password"; //Палево
$config['mysql_name'] = "database"; //Палево

$config['salt'] = "hgfjdgjd"; //Палево
$config['sig_salt'] = "vbmvbmh"; //Палево
$config['admin_salt'] = "sfdsbbtfv"; //Палево

$config['admin_email'] = "user@gmail.com"; // Email администратора (ОБЯЗАТЕЛЬНО ИЗМЕНИТЬ НА СВОЙ)

?>
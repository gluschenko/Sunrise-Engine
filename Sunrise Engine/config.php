<?php
$config;

$config['root'] = $_SERVER['DOCUMENT_ROOT'];
$config['engine_dir'] = "engine";
$config['engine_root'] = $_SERVER['DOCUMENT_ROOT']."/".$config['engine_dir'];

$config['domain'] = $_SERVER['HTTP_HOST'];

//

$config['mysql_host'] = "localhost"; //Палево
$config['mysql_user'] = "rzcoll_admin"; //Палево
$config['mysql_password'] = "9gDhSGxD"; //Палево
$config['mysql_name'] = "a72922_rzcoll"; //Палево

$config['salt'] = "N_42"; //Палево
$config['sig_salt'] = "NM_42"; //Палево
$config['admin_salt'] = "NDMT_42"; //Палево

$config['admin_email'] = "qugluschenko@gmail.com"; // Email администратора (ОБЯЗАТЕЛЬНО ИЗМЕНИТЬ НА СВОЙ)

?>
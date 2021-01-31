<?php
//Alexander Gluschenko (15-07-2015)

/** Запускает сессию */
function InitSession()
{
	$lifetime = 3600 * 24 * 31;
	//ini_set("upload_tmp_dir", $_SERVER['DOCUMENT_ROOT']."/sessions"); - шо то - ху*ня, шо это - ху*ня...
	//ini_set("session.save_path", $_SERVER['DOCUMENT_ROOT']."/sessions");
	ini_set("session.gc_maxlifetime", $lifetime);
	ini_set("session.cookie_lifetime", $lifetime);
	session_start();
}

/** Возвращает параметр сессии */
function GetSession($index, $alt = 0)
{
	if(isset($_SESSION[$index]))return $_SESSION[$index];
	else return $alt;
}

?>
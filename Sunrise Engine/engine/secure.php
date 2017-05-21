<?php
//Протокол безопасности
//Усилен и обновлен 10-04-2016

//Alexander Gluschenko (15-07-2015)

/** Проверяет авторизацию администратора */
function isAdminLogged()
{
	$settings = GetSiteSettings();
	$session_hash = GetData("admin_session", "");
	
	return SessionHash($settings['admin_password'], $settings['admin_token']) == $session_hash;
}

/** Авторизует администратора */
function AdminAuth($password)
{
	$settings = GetSiteSettings();
	$password_hash = _SHA256($password);
	
	if($settings['admin_password'] == $password_hash)
	{
		SetData("admin_session", SessionHash($password_hash, $settings['admin_token']));
		
		return true;
	}
	
	return false;
}

/** Выход администратора */
function AdminLogout()
{
	SetData("admin_session", "");
	UnsetData("admin_session");
}

//

/** Возвращает ключ сессии */
function SessionHash($password, $token)
{
	$config = GetConfig();
	$salt = $config['admin_salt'];
	$ua = $_SERVER['HTTP_USER_AGENT'];
	
	return _SHA256($password."_".$token."_".$ua."_".$salt);
}

/** Возвращает пользователький ключ */
function UserSessionHash($id, $password_hash)
{
	$config = GetConfig();
	$salt = $config['salt'];
	$ua = $_SERVER['HTTP_USER_AGENT'];
	
	return _SHA256($id."_".$password_hash."_".$ua."_".$salt);
}

/** Генерирует ключ доступа */
function GenerateToken($fixed = "")
{
	if($fixed != "")return $fixed;
	return _SHA256(rand(0, 1000000)."_".rand(0, 1000000)."_".rand(0, 1000000));
}

//

/** Сбрасывает пароль администратора */
function ResetAdminPassword()
{
	$cfg = GetConfig();
	
	$new_password = GenerateRandomString(16);
	
	SaveSiteSettings(array("admin_password" => $new_password));
	
	SendMail($cfg['admin_email'], StringFormat("noreply@{0}", $cfg['domain']), "Сброс пароля панели управления", StringFormat("Новый пароль для доступа в панель управления: {0}", $new_password));
	
	//SaveSiteSettings(array("admin_password" => $cfg['fixed_password']));
	
	LogAction("Пароль администратора был сброшен", 0);
}

/** Сбрасывает ключ доступа */
function ResetAdminToken()
{
	SaveSiteSettings(array("admin_token" => GenerateToken()));
	
	LogAction("Токен администратора был сброшен", 0);
}

//

/** Возвращает хеш SHA256 */
function _SHA256($str)
{
	return hash("sha256", $str);
}

/** Возвращает хеш SHA384 */
function _SHA384($str)
{
	return hash("sha384", $str);
}

/** Возвращает хеш SHA512 */
function _SHA512($str)
{
	return hash("sha512", $str);
}

/** Возвращает хеш SHA1 */
function _SHA1($str)
{
	return hash("sha1", $str);
}

//

/** Генерирует подпись параметров */
function Sig($val1 = "", $val2 = "", $val3 = "", $val4 = "", $val5 = "")
{
	$cfg = GetConfig();
	$salt = $cfg['sig_salt'];
	$key = $val1."_".$val2."_".$val3."_".$val4."_".$val5."_".$salt;
	$sig = md5(md5($key));
	return $sig;
}

/** Сверяет подпись параметров */
function TrueSig($sig, $val1 = "", $val2 = "", $val3 = "", $val4 = "", $val5 = "")
{
	$c_sig = Sig($val1, $val2, $val3, $val4, $val5);
	return $sig == $c_sig;
}

//

/** Сохраняет куки */
function SetData($key, $value, $days = 365)
{
	setcookie($key, $value, time() + (3600 * 24 * $days), "/");
}

/** Получает куки */
function GetData($key, $alt = 0)
{
	if(isset($_COOKIE[$key]))
	{
		return $_COOKIE[$key];
	}
	return $alt;
}

/** Удаляет куки */
function UnsetData($key)
{
	setcookie($key, "", time() - 3600, "/");
}

?>
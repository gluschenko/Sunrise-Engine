<?php
//Alexander Gluschenko (15-07-2015)

$mysql_count = 0;

SQLConnect();

/** Подключается к БД */
function SQLConnect()
{
	$cfg = GetConfig();
	global $engine_config;
	
	$connection = mysqli_connect($cfg['mysql_host'], $cfg['mysql_user'], $cfg['mysql_password'], $cfg['mysql_name']) or die("Ошибка подключения к MySQL.");
	mysqli_query($connection, 'SET NAMES utf8') or die("Невозможно включить UTF-8.");
	
	$engine_config['mysqli_connection'] = $connection;
	
	/*$connect = mysql_connect($cfg['mysql_host'], $cfg['mysql_user'], $cfg['mysql_password']) or die("Ошибка подключения к MySQL.");
	mysql_query('SET NAMES utf8') or die("Невозможно включить UTF-8.");
	mysql_select_db($cfg['mysql_name'], $connect) or die("Невозможно получить данный о MySQL таблице.");*/
}

/** Солучает текущее подключение */
function SQLConnection()
{
	global $engine_config;
	return $engine_config['mysqli_connection'];
}

/** Исполняет запрос к БД */
function SQL($sql, $trace = true)
{
	global $mysql_count;
	global $engine_config;
	
	$connection = $engine_config['mysqli_connection'];
	$query = mysqli_query($connection, $sql);
	if($query)
	{
		$mysql_count++;
		return $query;
	}
	
	if($trace)echo("SQL error: ".mysqli_error($connection)."<br/>");
	
	/*$query = mysql_query($sql);
	if($query)
	{
		$mysql_count++;
		return $query;
	}
	
	if($trace)echo("SQL error: ".mysql_error()."<br/>");*/
}

/** Формализует результат запроса */
function SQLFetchAssoc($query)
{
	return mysqli_fetch_assoc($query);
}

/** Формализует результат запроса #2 */
function SQLFetchArray($query)
{
	return mysqli_fetch_array($query);
}

/** Возвращает идентификатор для новой записи */
function GetNewId($table, $field = "id")
{
	$query = SQL("SELECT `".$field."` FROM `".$table."` ORDER BY `".$table."`.`".$field."` DESC LIMIT 1");
	$row = SQLFetchAssoc($query);
	$newid = $row[$field] + 1;
	
	return $newid;
}

/** Режет лишние символы */
function SQLFilterText($text)
{
	return mysqli_real_escape_string(SQLConnection(), $text);
}

?>
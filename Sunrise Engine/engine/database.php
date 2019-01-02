<?php
//Alexander Gluschenko (18-09-2015)

/** Обновляет структуру БД */
function ApplyDatabase() //Обновляем БД
{
	global $engine_config;
	$tables = $engine_config['db_tables'];
	//
	foreach($tables as $key => $value)
	{
		CreateTable($key);
		//
		$table = $key;
		$fields = $tables[$table];
		//
		$existing_fields = GetFields($table);
		$existing_names = array();
		for($f = 0; $f < sizeof($existing_fields); $f++)
		{
			$existing_names[$f] = $existing_fields[$f]['name'];
		}
		//
		for($i = 0; $i < sizeof($fields); $i++)
		{
			if(in_array($fields[$i]['name'], $existing_names))
			{
				CreateOrChangeField($table, $fields[$i], true);
				//
				echo "Update: ".$table.".".$fields[$i]['name']."<br/>";
			}
			else
			{
				CreateOrChangeField($table, $fields[$i], false);
				//
				echo "Add: ".$table.".".$fields[$i]['name']."<br/>";
			}
		}
	}
	
	echo "Database is done!";
}

/** Добавляет таблицу БД в конфиг */
function AddTable($name, $fields)
{
	global $engine_config;
	
	$engine_config['db_tables'][$name] = $fields;
}

/** Возвращает поля таблицы из БД */
function GetFields($table)
{
	$query = SQL("SHOW COLUMNS FROM ".$table);
	
	$fields = array();
	$inc = 0;
	
	while($row = SQLFetchAssoc($query))
	{
		$row['Null'] = ($row['Null'] == "NO")? "NOT NULL" : $row['Null'];
		
		$fields[$inc] = TableField($row['Field'], $row['Type'], $row['Null']);
		$inc++;
	}
	
	return $fields;
}

/** Возвращает массив таблиц БД */
function GetTables()
{
	$query = SQL("SHOW TABLES;");
	$result = array();
	while($row = SQLFetchAssoc($query))
	{
		foreach($row as $key => $value)
		{
			$result[sizeof($result)] = $value;
		}
	}
	
	return $result;
}

/** Получает информацию о таблицах */
function GetTablesStatus()
{
	$query = SQL("SHOW TABLE STATUS;");
	$result = array();
	
	while($row = SQLFetchAssoc($query))
	{
		$result[sizeof($result)] = array(
			"name" => $row['Name'],
			"rows" => $row['Rows'],
			"collation" => $row['Collation'],
			"length" => $row['Data_length'] + $row['Index_length'],
			"max_length" => $row['Max_data_length'],
		);
	}
	
	return $result;
}

/** Возвращает структуру БД */
function GetDataBaseStructure()
{
	$db = array();
	
	$tables = GetTables();
	$tables_statuses = GetTablesStatus();
	
	for($i = 0; $i < sizeof($tables); $i++)
	{
		$statuses = array();
		for($s = 0; $s < sizeof($tables_statuses); $s++)
		{
			if($tables_statuses[$s]['name'] == $tables[$i])$statuses = $tables_statuses[$s];
		}
		//
		$db[$i] = array(
			"table_name" => $tables[$i],
			"fields" => GetFields($tables[$i]),
			"statuses" => $statuses,
		);
	}
	
	return $db;
}

/** Структура поля таблицы */
function TableField($name, $type = "int(11)", $null = "NOT NULL")
{
	return array(
		"name" => $name,
		"type" => $type,
		"null" => $null,
	);
}

/** Создает новую таблицу */
function CreateTable($name, $id_field = "id") //SQL требует хотя бы одно поле (в каждой таблице ID умолчанию теперь)
{
	SQL("CREATE TABLE IF NOT EXISTS ".$name." (".$id_field." INT NOT NULL, UNIQUE KEY(".$id_field.")) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
}

/** Создает или изменяет поле таблицы */
function CreateOrChangeField($table, $field_obj, $change = false)
{
	$name = $field_obj['name'];
	$type = $field_obj['type'];
	$null = $field_obj['null'];
	
	if(!$change)SQL("ALTER TABLE ".$table." ADD ".$name." ".$type." ".$null.";"); //Добавляем поле, когда его нет
	else SQL("ALTER TABLE ".$table." CHANGE ".$name." ".$name." ".$type." ".$null.";"); //Изменяем поле, когда оно уже есть
}

?>
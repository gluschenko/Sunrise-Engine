<?php
//Alexander Gluschenko (15-07-2015)

/** Склоняет слово по числу*/
function WordByNumber($word = "", $num = 1)
{
	//Все слова / По принципу: 1#2,3,4#5,6,7,8,9
	$words = array(
		array("пользователь", "пользователя", "пользователей"),
		array("запись", "записи", "записей"),
		array("день", "дня", "дней"),
		array("минута", "минуты", "минут"),
		array("файл", "файла", "файлов"),
	);
	//
	$wordarr = array();//Текущее слово
	for($i = 0; $i < sizeof($words); $i++)if($words[$i][0] == $word)$wordarr = $words[$i];
	//
	if(is_int($num))
	{
		if($num >= 20)$num = substr($num, strlen($num)-1);
		if($num >= 10 && $num <= 20)
		{
			return $wordarr[2];
		}
		
		if($num == 0)return $wordarr[2];
		if($num < 1)return $wordarr[1];
		if($num == 1)return $wordarr[0];
		if($num > 1 && $num < 5)return $wordarr[1];
		if($num >= 5 && $num < 10)return $wordarr[2];
	}
	else
	{
		return $wordarr[1];
	}
}

/** Обрезает текст на опреденный лимит*/
function CropText($text, $limit, $end = "...")
{
	if(mb_strlen($text, "UTF-8") > $limit)
	{
		$text = mb_substr($text, 0, $limit, "UTF-8").$end;
	}
	
	return $text;
}

/** Формирует строку из паттерна и значений: StringFormat("{0}: {1}", 1, 2);*/
function StringFormat($pattern, $s0 = "", $s1 = "", $s2 = "", $s3 = "", $s4 = "", $s5 = "", $s6 = "", $s7 = "", $s8 = "", $s9 = "")
{
	$strings_arr = array($s0, $s1, $s2, $s3, $s4, $s5, $s6, $s7, $s8, $s9);
	
	for($i = 0; $i < sizeof($strings_arr); $i++)
	{
		$pattern = str_replace("{".$i."}", $strings_arr[$i], $pattern);
	}
	
	return $pattern;
}

/** Определяет, содержит ли строка вхождение*/
function StringContains($str, $search)
{
	return strpos($str, $search) !== false;
}

/** Формирует строку из паттерна и ассоциаливных значений*/
function StringAssocFormat($pattern, $assoc_array)
{
	foreach($assoc_array as $key => $value)
	{
		$pattern = str_replace("{".$key."}", $value, $pattern);
	}
	
	return $pattern;
}

/** Нормализация имени файла*/
function NormalizeFileName($filename)
{
	$name_array = explode(".", $filename);
	if(sizeof($name_array) >= 2)
	{
		$extension = $name_array[sizeof($name_array) - 1];
		$name_array[sizeof($name_array) - 1] = strtolower($extension);
	}
	
	$filename = implode(".", $name_array);
	//
	$filename = str_replace(" ", "_", $filename);
	$filename = FilterText($filename, "translit");
	//
	
	return $filename;
}

/** Преобразует число байт в наиболее удобные единицы*/
function GetDataUnits($bytes)
{
	$units = array("B", "KB", "MB", "GB", "TB", "PB");
	
	for($i = 0; $bytes >= 1024 && $i < sizeof($units) - 1; $i++ ) 
	{
		$bytes /= 1024;
	}
	
	return round($bytes, 2)." ".$units[$i];
}

/** Превращает число в сокращенную форму */
function GetNumericSuffix($n)
{
	$units = array("", "K", "M", "G");
	
	for($i = 0; $n >= 1000 && $i < sizeof($units) - 1; $i++ ) 
	{
		$n /= 1000;
	}
	$n = round($n);
	
	return $n.$units[$i];
}

/** Алгоритмический выбор слова из множества, на основании входного слова */
function WordByWord($word, $words)
{
	if(TextLength($word) > 0 && sizeof($words) > 0)
	{
		$word_arr = str_split($word);
		$summ = 0;
		for($i = 0; $i < sizeof($word_arr); $i++)
		{
			if($i % 3 == 0)$summ += ord($word_arr[$i]);
			else $summ -= ord($word_arr[$i]);
		}
		$summ = abs($summ);
		$sig = $summ + sizeof($word_arr);
		return $words[$sig % sizeof($words)];
	}
	
	return "";
}

/** Вырезает HTML из текста */
function StripHTML($text, $allowable_tags = "")
{
	$out = strip_tags($text, $allowable_tags);
	$out = str_replace("	", "", $out);
	$out = str_replace("'", "", $out);
	$out = str_replace("\"", "", $out);
	$out = str_replace("\r", "", $out);
	$out = str_replace("\n", " ", $out);
	$out = SQLFilterText($out);
	return $out;
}

//

/** Применение фильтра к тексту*/
function FilterText($input_text, $methods)
{
	$engine_cfg = GetEngineConfig();
	//
	$methods_array = explode(" ", $methods);
	
	$out_text = $input_text;
	for($i = 0; $i < sizeof($methods_array); $i++)
	{
		$filter_func = $engine_cfg['filters'][$methods_array[$i]];
		$out_text = $filter_func($out_text);
	}
	//
	return $out_text;
}

/** Добавление нового фильтра*/
function AddFilter($name, $method)
{
	global $engine_config;
	
	$engine_config['filters'][$name] = $method;
}

/** Валидация текста*/
function isValidText($input_text, $methods)
{
	return $input_text == FilterText($input_text, $methods);
}

//

/** Длина строки в UTF-8*/
function TextLength($str)
{
	return mb_strlen($str, "UTF-8");
}

/** Разбивает строку на символы*/
function TextSplit($str)
{
	return preg_split("/(?<!^)(?!$)/u", $str);
}

/** Переводит UTF-8 в верхний редистр*/
function ToUpperCase($str)
{
	return mb_strtoupper($str, "UTF-8");
}

/** Переводит UTF-8 в нижний редистр*/
function ToLowerCase($str)
{
	return mb_strtolower($str, "UTF-8");
}

//

/** Преобразует текст по методам (обертка FilterText)*/
function ConvertText($text, $methods = "html")
{
	return FilterText($text, $methods);
}

/** Генерирует случайную последовательность символов */
function GenerateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/** Отсылает электронное письмо */
function SendMail($receiver, $author, $subject, $text)
{
	$config = GetConfig();
	
	$subject = $subject." (".$config['domain'].")";
	
	$headers = "Content-type: text/html; charset=utf-8 \r\n";
	$headers .= "From: ".$author."\r\n";
	$headers .= "Reply-To: ".$author."\r\n";
	$headers .= "X-Mailer: PHP/".phpversion()."\r\n";
	
	return mail($receiver, $subject, $text, $headers);
}


?>
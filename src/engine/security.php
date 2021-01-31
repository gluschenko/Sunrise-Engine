<?php

class Security
{
	public static function String($text, $no_sql = false, $hard_quotes = false)
	{
		if($hard_quotes)
		{
			$text = Security::NoQuotes($text);
		}
		
		if($no_sql)
		{
			$text = Security::NoSQL($text);
		}
		
		$text = Security::RealString($text);
		
		return $text;
	}
	
	public static function HashString($text)
	{
		return Security::NoQuotes($text);
	}
	
	public static function RealString($text)
	{
		return SQLRealEscapeString($text);
	}
	
	//
	
	static function NoQuotes($text)
	{
		$text = str_replace("'", "\\'", $text);
		$text = str_replace("\"", "\\\"", $text);
		$text = str_replace("`", "\\`", $text);
		
		return $text;
	}
	
	static function NoSQL($text)
	{
		$black_listed_words = array(
			"SELECT",
			"FROM",
			"WHERE",
			"UNION",
			"UPDATE",
			"DELETE",
			"DROP",
			"TRUNCATE",
			"OPTIMIZE",
			"DISTINCT",
			"SET",
			"LIMIT",
		);
		
		foreach($black_listed_words as $word)
		{
			$text = str_ireplace($word, "", $text);
		}
		
		return $text;
	}
	
	
	//
	
	public static function Int($text)
	{
		return intval($text);
	}
	
	public static function Float($text)
	{
		return floatval($text);
	}
	
	public static function Bit($text)
	{
		$n = intval($text);
		return $n == 0 ? 0 : 1;
	}
	
	public static function Test()
	{
		var_dump(Security::String("SSSS АААА \"ППППП\" `ИИИИИ` 'ВВВВВ' 2312434315353"));
		echo("<br/>");
		var_dump(Security::String("SELECT * FROM `fffff` WHERE `id`='1'"));
		echo("<br/>");
		var_dump(Security::String("UPDATE `ffff` SET `id`='1' WHERE `id`='1'"));
		echo("<br/>");
		var_dump(Security::Int("Лалка"));
		echo("<br/>");
		var_dump(Security::Int("1001"));
		echo("<br/>");
		var_dump(Security::Float("Л.алка"));
		echo("<br/>");
		var_dump(Security::Float("3.141592"));
		echo("<br/>");
		var_dump(Security::Bit("1"));
		echo("<br/>");
		var_dump(Security::Bit("0"));
		echo("<br/>");
		var_dump(Security::Bit("42"));
		echo("<br/>");
	}
}

?>
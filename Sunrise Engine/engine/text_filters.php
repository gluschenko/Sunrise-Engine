<?php
//Alexander Gluschenko (03-10-2015)

//Обработка имени
AddFilter("name", function($text){
	$chars = TextSplit(" ".$text); //Пробел впереди служит фактором большой буквы
	$text = "";
	
	for($i = 1; $i < sizeof($chars); $i++)
	{
		if($chars[$i - 1] == " ")$chars[$i] = mb_strtoupper($chars[$i], "UTF-8");
		$text .= $chars[$i];
	}
	
	return $text;
});

//Корректное отображение текста, введенного пользователем (примитивная типографика и подобие BB-кодов)
AddFilter("user_text", function($text){
	// Не понимаю я эти ваши регэкспы
	// Вставка ссылок
	//$text = preg_replace("#(https?|ftp)://\S+[^\s.,> )\];'\"!?]#", '<a class="link text" target="_blank" href="\\0">\\0</a>', $text);
	$text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/i", "$1$2[a]$3[t]$3[/a]", $text); // поиск http://
    $text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/i", "$1$2[a]http://$3[t]$3[/a]", $text); // поиск www
    $text = preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1[a]mailto:$2@$3[t]$2@$3[/a]", $text); // поиск почтового адреса
	//
	$keys = array(
		array("<<", "«"),
		array(">>", "»"),
		array("<", "&lt;"),
		array(">", "&gt;"),
		array("\n", "<br/>"),
		//
		array("--", "—"),
		//BB-коды
		array("[img]", "<img class='post_image' alt='' onclick='ImageBox(this.src);' src='"),
		array("[/img]", "' />"),
		array("[s]", "<s>"),
		array("[/s]", "</s>"),
		array("[b]", "<b>"),
		array("[/b]", "</b>"),
		array("[a]", "<a class='link' target='_blank' href='"),
		array("[t]", "'>"),
		array("[/a]", "</a>"),
		array("[iframe]", "<div><iframe style='width: 90%; height: 400px; margin: 5px; border: 0;' allowfullscreen src='https://www.youtube.com"),
		array("[/iframe]", "'></iframe></div>"),
		/*array("[audio]", "<div><audio controls src='"),
		array("[/audio]", "'></audio></div>"),*/
	);
	
	for($i = 0; $i < sizeof($keys); $i++)
	{
		$text = str_replace($keys[$i][0], $keys[$i][1], $text);
	}
	
	return $text;
});

//Экранирует всякую требуху, от которой не едет SQL (здесь можно ЧАСТИЧНО отразить атаку SQL injection)
AddFilter("database_text", function($text){
	$keys = array(
		array("'", "&apos;"), //&#39;
		array("\"", "&quot;"), //&#34;
	);
	
	for($i = 0; $i < sizeof($keys); $i++)
	{
		$text = str_replace($keys[$i][0], $keys[$i][1], $text);
	}
	
	return $text;
});

//Представление HTML в виде открытого текста
AddFilter("open_html", function($text){
	$keys = array(
		array("<", "&lt;"),
		array(">", "&gt;"),
		array("\n", "<br/>"),
		array("\t", "<span style='width: 25px; display: inline-block;'></span>"),
	);
	
	for($i = 0; $i < sizeof($keys); $i++)
	{
		$text = str_replace($keys[$i][0], $keys[$i][1], $text);
	}
	
	return $text;
});

AddFilter("closed_html", function($text){
	$keys = array(
		array("&lt;", "<"),
		array("&gt;", ">"),
	);
	
	for($i = 0; $i < sizeof($keys); $i++)
	{
		$text = str_replace($keys[$i][0], $keys[$i][1], $text);
	}
	
	return $text;
});

//Главный интрепретатор для всего движка (страницы, новости, бары)
AddFilter("html", function($text){
	
	$separators = array(
		"<image_c>", "</image_c>", "<img class='post_image' alt='' src='/engine/utils/compress_image.php?i={0}&width=400' onclick='ImageBox(\"{0}\");'/>",
		"<small_image_c>", "</small_image_c>", "<img class='small_post_image' alt='' src='/engine/utils/compress_image.php?i={0}&width=400' onclick='ImageBox(\"{0}\");'/>",
		"<big_image_c>", "</big_image_c>", "<img class='big_post_image' alt='' src='/engine/utils/compress_image.php?i={0}&width=400' onclick='ImageBox(\"{0}\");'/>",
		"<large_image_c>", "</large_image_c>", "<img class='large_post_image' alt='' src='/engine/utils/compress_image.php?i={0}&width=400' onclick='ImageBox(\"{0}\");'/>",
		"<wide_image_c>", "</wide_image_c>", "<img class='wide_post_image' alt='' src='/engine/utils/compress_image.php?i={0}&width=400' onclick='ImageBox(\"{0}\");'/>",
		
		"<image>", "</image>", "<img class='post_image' alt='' src='{0}' onclick='ImageBox(this.src);'/>",
		"<small_image>", "</small_image>", "<img class='small_post_image' alt='' src='{0}' onclick='ImageBox(this.src);'/>",
		"<big_image>", "</big_image>", "<img class='big_post_image' alt='' src='{0}' onclick='ImageBox(this.src);'/>",
		"<large_image>", "</large_image>", "<img class='large_post_image' alt='' src='{0}' onclick='ImageBox(this.src);'/>",
		"<wide_image>", "</wide_image>", "<img class='wide_post_image' alt='' src='{0}' onclick='ImageBox(this.src);'/>",
		
		"<banner>", "</banner>", "<div id='b_{random}' class='banner anti_padding_wide' style='background-image: url({0});' onclick='ImageBox(\"{0}\");'></div>
									<script>var f= function(e){ ResizeHeight('b_{random}', 250/800); }; window.addEventListener('resize', f); setTimeout(f, 10);</script>",
		"<small_banner>", "</small_banner>", "<div id='b_{random}' class='small_banner anti_padding_wide' style='background-image: url({0});' onclick='ImageBox(\"{0}\");'></div>
									<script>var f= function(e){ ResizeHeight('b_{random}', 100/800); }; window.addEventListener('resize', f); setTimeout(f, 10);</script>",
		"<middle_banner>", "</middle_banner>", "<div id='b_{random}' class='middle_banner anti_padding_wide' style='background-image: url({0});' onclick='ImageBox(\"{0}\");'></div>
									<script>var f= function(e){ ResizeHeight('b_{random}', 200/800); }; window.addEventListener('resize', f); setTimeout(f, 10);</script>",
		"<big_banner>", "</big_banner>", "<div id='b_{random}' class='big_banner anti_padding_wide' style='background-image: url({0});' onclick='ImageBox(\"{0}\");'></div>
									<script>var f= function(e){ ResizeHeight('b_{random}', 360/800); }; window.addEventListener('resize', f); setTimeout(f, 10);</script>",
		
		"<center>", "</center>", "<div style='text-align: center;'>{0}</div>",
		"<link>", "</link>", "<a class='link' href='{0}' target='_blank'>{1}</a>",
		"<inner_link>", "</inner_link>", "<a class='link' href='{0}' async>{1}</a>",
		
		"<header>", "</header>", "<div></div>
								  <div class='text header_underline'>{0}</div>
								  <div class='divider'></div>
								  <div class='space'></div>",
								  
		"<text>", "</text>", "<div class='text'>{0}</div>",
		"<small_text>", "</small_text>", "<div class='small_text'>{0}</div>",
		"<tiny_text>", "</tiny_text>", "<div class='mini_text'>{0}</div>",
		"<big_text>", "</big_text>", "<div class='title_text'>{0}</div>",
		"<huge_text>", "</huge_text>", "<div class='logo_text'>{0}</div>",
		
		"<embed_iframe>", "</embed_iframe>", "<iframe style='width: 85%; height: 460px; border: none; border-radius: 5px; resize: vertical;' allowfullscreen src='{0}'></iframe>",
		"<embed_audio>", "</embed_audio>", "<div><audio controls src='{0}'></audio></div>",
		
		"<youtube>", "</youtube>", "<iframe style='width: 85%; height: 460px; border: none; border-radius: 5px; resize: vertical;' allowfullscreen src='https://www.youtube.com/embed/{0}'></iframe>",
	);
	
	for($i = 0; $i < sizeof($separators); $i += 3)
	{
		$first_key = $separators[$i + 0];
		$second_key = $separators[$i + 1];
		$markup = $separators[$i + 2];
		
		$primary_text_array = explode($first_key, $text);
		
		for($c = 1; $c < sizeof($primary_text_array); $c++)
		{
			$secondary_text_array = explode($second_key, $primary_text_array[$c]);
			if(sizeof($secondary_text_array) > 0)
			{
				$rand = rand(1000, 10000000);
				
				$entry = $secondary_text_array[0];
				$entry_array = explode("|", $entry);
				
				for($e = 0; $e < 16; $e++)
				{
					if(!isset($entry_array[$e])) $entry_array[$e] = $entry_array[0];
				}
				
				$markup = str_replace("{random}", $rand, $markup);
				
				$secondary_text_array[0] = StringFormatArray($markup, $entry_array);
				
				$primary_text_array[$c] = implode("", $secondary_text_array);
			}
		}
		
		$text = implode("", $primary_text_array);
	}
	
	//
	
	$assocs = array(
		"[b]", "<b>",
		"[/b]", "</b>",
		/*"[i]", "<i>",
		"[/i]", "</i>",
		"[u]", "<u>",
		"[/u]", "</u>",
		"[s]", "<s>",
		"[/s]", "</s>",
		"[h1]", "<span class='title_text'>",
		"[/h1]", "</span>",
		"[h2]", "<span class='logo_text'>",
		"[/h2]", "</span>",*/
		
		"[img]", "<img class='post_image' alt='' src='",
		"[img_150]", "<img class='post_image' alt='' onclick='ImageBox(this.src);' src='",
		"[img_200]", "<img class='post_image' alt='' onclick='ImageBox(this.src);' src='",
		"[img_300]", "<img class='post_image' alt='' onclick='ImageBox(this.src);' src='",
		"[img_600]", "<img class='post_image' alt='' onclick='ImageBox(this.src);' src='",
		"[/img]", "' />",
		
		"[br]", "</br>",
		/*"[divider]", "<div class='space'></div><div class='divider'></div><div class='space'></div>",
		"[tab]", "<span style='width: 15px; display: inline-block;'></span>",
		"[div]", "<div class='space'></div><div class='divider'></div><div class='space'></div>",
		"[space]", "<div class='space'></div>",*/
		/*"[link]", "<a class='link' href='",
		"[t]", "'>",
		"[/link]", "</a>",
		"[button]", "<a class='button back4' href='",
		"[t]", "'>",
		"[/button]", "</a>",*/
		/*"[iframe]", "<iframe style='width: 85%; height: 500px; border: none; border-radius: 5px;' allowfullscreen src='",
		"[/iframe]", "'></iframe>",
		"[audio]", "<div><audio controls src='",
		"[/audio]", "'></audio></div>",*/
		/*"[center]", "<div style='text-align: center;'>",
		"[/center]", "</div>",*/
		
		"<space/>", "<div class='space'></div>",
		"<big_space/>", "<div class='big_space'></div>",
	);
	
	for($i = 0; $i < sizeof($assocs); $i += 2)
	{
		$text = str_replace($assocs[$i + 0], $assocs[$i + 1], $text);
	}
	
	return $text;
});

// Транслитерация: кот -> kot, гоблин -> goblin, щёлкать -> schelkat
AddFilter("translit", function($text){
	
	// Ассоциации букв: рус, лат, рус, лат, рус...
	$letters = array(
		"А", "A", "а", "a",
		"Б", "B", "б", "b",
		"В", "V", "в", "v",
		"Г", "G", "г", "g",
		"Д", "D", "д", "d",
		"Е", "E", "е", "e",
		"Ё", "E", "е", "e",
		"Ж", "ZH", "ж", "zh",
		"З", "Z", "з", "z",
		"И", "I", "и", "i",
		"Й", "Y", "й", "y",
		"К", "K", "к", "k",
		"Л", "L", "л", "l",
		"М", "M", "м", "m",
		"Н", "N", "н", "n",
		"О", "O", "о", "o",
		"П", "P", "п", "p",
		"Р", "R", "р", "r",
		"С", "S", "с", "s",
		"Т", "T", "т", "t",
		"У", "U", "у", "u",
		"Ф", "F", "ф", "f",
		"Х", "H", "х", "h",
		"Ц", "TS", "ц", "ts",
		"Ч", "CH", "ч", "ch",
		"Ш", "SH", "ш", "sh",
		"Щ", "SCH", "щ", "sch",
		"Ь", "", "ь", "",
		"Ы", "Y", "ы", "y",
		"Ъ", "", "ъ", "",
		"Э", "E", "э", "e",
		"Ю", "IU", "ю", "iu",
		"Я", "YA", "я", "ya",
	);
	
	for($i = 0; $i < sizeof($letters); $i += 2)
	{
		$text = str_replace($letters[$i], $letters[$i + 1], $text);
	}
	
	return $text;
});



?>
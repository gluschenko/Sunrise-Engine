<?php
//Alexander Gluschenko (15-07-2015)

/** Добавляет верстку в конфиг */
function AddMarkup($name, $m)
{
	global $engine_config;
	
	$engine_config['markups'][$name] = $m;
}

/** Добавляет шаблон в конфиг */
function AddTemplate($name, $m)
{
	global $engine_config;
	
	$engine_config['markup_templates'][$name] = $m;
}

/** Проверяет наличие шаблона */
function TemplateExists($name)
{
	global $engine_config;
	
	return isset($engine_config['markup_templates'][$name]);
}

/** Возвращает верстку из конфига */
function Markup($name, $alt = false)
{
	$cfg = GetEngineConfig();
	if($alt === false)$alt = StringFormat("\${0}\$", $name);
	
	if(isset($cfg['markups'][$name]))
	{
		$markup_text = str_replace("{random}", rand(0, 1000000), $cfg['markups'][$name]);
		$markup_text = InterpretMarkup($markup_text);
		
		return $markup_text;
	}
	return $alt;
}

//

/** Струкрутра бара сайта */
function GetDefaultBar()
{
	return array(
		"id" => "",
		"title" => "Default bar",
		"blocks" => array(),
	);
}

/** Структура блока в баре сайта */
function GetDefaultBlock()
{
	return array(
		"title" => "Block title",
		"type" => 0,
		"markup" => "",
		"links" => array(),
	);
}

/** Структура ссылки в блоке ссылок */
function GetDefaultLink()
{
	return array(
		"title" => "Link",
		"external" => 0,
		"href" => "/",
	);
}

/** Создает или редактирует бар */
function CreateOrEditBar($data)
{
	$config = GetConfig();
	//
	$path = $config['root']."/data/blocks/".$data['id']."_config.json";
	
	$bar = LoadJSON($path, GetDefaultBar());
	
	$bar = MergeArrays($data, $bar);
	//
	SaveJSON($path, $bar);
}

/** Удаляет бар */
function DeleteBar($id)
{
	$config = GetConfig();
	//
	DeleteFile($config['root']."/data/blocks/".$id."_config.json");
}

/** Загружает бар из .json */
function GetBar($id)
{
	$config = GetConfig();
	//
	$default_data = GetDefaultBar();
	//
	$data = LoadJSON($config['root']."/data/blocks/".$id."_config.json", $default_data);
	$data = MergeArrays($data, $default_data);
	//
	return $data;
}

//

/** Возвращает вёрстку бара */
function BlocksBar($bar_id, $markup_block_pattern = "{title}, {content}", $link_block_pattern = "{title}, {content}", $link_pattern = "<a href='{href}' {external}>{title}</a>")
{
	$bar_data = GetBar($bar_id);
	
	if($bar_data['id'] != "")
	{
		$m = "";
		for($i = 0; $i < sizeof($bar_data['blocks']); $i++)
		{
			$block_data = $bar_data['blocks'][$i];
			//
			$block_m = "";
			
			if($block_data['type'] == 0)$block_m = $markup_block_pattern;
			if($block_data['type'] == 1)$block_m = $link_block_pattern;
			//
			$block_m = str_replace("{title}", $block_data['title'], $block_m);
			//
			if($block_data['type'] == 0)
			{
				$block_body_markup = $block_data['markup'];
				$block_body_markup = InterpretMarkup($block_body_markup);
				
				$block_m = str_replace("{content}", $block_body_markup, $block_m);
			}
			
			if($block_data['type'] == 1)
			{
				$links_m = GetLinksList($block_data['links'], $link_pattern);
				
				$block_m = str_replace("{content}", $links_m, $block_m);
			}
			
			$m .= $block_m;
		}
	}
	else
	{
		$m = StringFormat("Bar: {0}", $bar_id);
	}
	
	return $m;
}

/** Возвращает вёрстку списка ссылок */
function GetLinksList($links, $link_pattern)
{
	$links_m = "";
	
	for($j = 0; $j < sizeof($links); $j++)
	{
		$link_m = $link_pattern;
		
		$link_m = str_replace("{title}", $links[$j]['title'], $link_m);
		$link_m = str_replace("{href}", $links[$j]['href'], $link_m);
		
		$target = "";
		if($links[$j]['external'] == 0)$target = "onclick='return NavAsync(this.href, true);'";
		if($links[$j]['external'] == 1)$target = "target='_blank'";
		
		$link_m = str_replace("{external}", $target, $link_m);
		//
		$links_m .= $link_m;
	}
	
	return $links_m;
}

//

/** Возвращает вёрстку лога событий */
function ActionMarkup($row)
{
	$id = $row['id'];
	$ip = $row['ip'];
	$date = GetTime($row['date']);
	$text = FilterText($row['text'], "user_text closed_html html");
	$type = $row['type'];
	$user_agent = $row['user_agent'];
	
	$fore = "";
	if($type == 0)$fore = "fore2";
	if($type == 1)$fore = "fore4";
	if($type == 2)$fore = "fore0";
	//
	$ip_array = explode(".", $ip);
	
	$ip_red = isset($ip_array[0])? $ip_array[0] : 0;
	$ip_green = isset($ip_array[1])? $ip_array[1] : 0;
	$ip_blue = isset($ip_array[2])? $ip_array[2] : 0;
	
	$m = "<div style='line-height: 24px; border-left: solid 5px rgb(".$ip_red.", ".$ip_green.", ".$ip_blue."); padding: 5px;' class='text back3' title='ID: ".$id.", UA: ".$user_agent."'>
		<div class='small_text bold ".$fore."' style='word-break: break-word;'>".$text."</div>
		<div class='mini_text ".$fore."'>".$ip.", ".GetOS($user_agent).", ".GetBrowser($user_agent).", ".$date."</div>
	</div>
	<div class='divider'></div>";
	
	return $m;
}

//

/** Вставляет разделы в текст (%section_name%) */
function InterpretSections($markup)
{
	//Механизм вставки страниц друг в друга по инструкции %main%
	$engine_config = GetEngineConfig();
	$settings = GetSettings();
	
	$sections = $engine_config['sections'];
	
	for($i = 0; $i < 10; $i++) //Итеративная глубина
	{
		foreach($sections as $key => $value)
		{
			if($sections[$key]['type'] == 1)
			{
				$page_tag = "%".$key."%";
				
				if(strpos($markup, $page_tag) !== false)
				{
					$section_markup = GetMarkup($sections[$key], $settings, false);
					
					$markup = str_replace($page_tag, $section_markup, $markup);
				}
			}
		}
	}
	
	return $markup;
}

/** Вставляет шаблоны в текст ($temp_name$) */
function InterpretTemplates($markup)
{
	//Механизм вставки шаблонов $temp_name$
	$engine_config = GetEngineConfig();
	$temps = $engine_config['markup_templates'];
	
	for($i = 0; $i < 10; $i++) //Итеративная глубина
	{
		foreach($temps as $key => $value)
		{
			if($key != "")
			{
				$template_tag = "$".$key."$";
				$markup = str_replace($template_tag, $temps[$key], $markup);
			}	
		}
	}
	
	return $markup;
}

/** Интерпретирует и сполняет все вставки в вёрстке */
function InterpretMarkup($markup)
{
	$markup = InterpretSections($markup);
	$markup = InterpretTemplates($markup);
	
	return $markup;
}

//

AddMarkup("admin_menu_markup", "
<div style='width: 98%; margin: auto; white-space: nowrap; overflow-x: auto;'>
	<div style='min-width: 800px; display: flex; justify-content: space-around;'>
		<a class='headmenu_button' href='/admin' onclick='return NavAsync(this.href, true);'>Главная</a>
		<a class='headmenu_button' href='/admin?act=settings' onclick='return NavAsync(this.href, true);'>Настройки</a>
		<a class='headmenu_button' href='/admin?act=pages' onclick='return NavAsync(this.href, true);'>Страницы</a>
		<a class='headmenu_button' href='/admin?act=nav' onclick='return NavAsync(this.href, true);'>Навигация</a>
		<a class='headmenu_button' href='/admin?act=upload' onclick='return NavAsync(this.href, true);'>Файлы</a>
		<a class='headmenu_button' href='/admin?act=modules' onclick='return NavAsync(this.href, true);'>Модули</a>
		<a class='headmenu_button' href='/' onclick='return NavAsync(this.href, true);'>Сайт</a>
		<a class='headmenu_button' onclick='AdminLogout();'>Выход</a>
	</div>
</div>
");

?>
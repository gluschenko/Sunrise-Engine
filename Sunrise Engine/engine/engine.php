<?php
//Alexander Gluschenko (15-07-2015)

require($_SERVER['DOCUMENT_ROOT']."/config.php");
require("engine_config.php");
//
$components = array(
	"sql",          // SQL
	"security",     // Безопасность
	"database",     // Функции БД
	"io",           // Работа с файлами
	//"sessions",   // Сессии (не используются, кажется)
	"secure",       // Система безопасности
	"api",          // Прикладной интерфейс
	"time",         // Время
	"layout",       // Вторичные каркасы
	"text",         // Тексты
	"search",       // Поиск
	
	"sections",           // Секции сайта
	"api_methods",        // Методы API
	"text_filters",       // Методы API фильтров
	"database_structure", // Описание структуры БД
);

Start(); // Запуск

/** Точка запуска движка */
function Start() 
{
	global $components;
	
	StartTimer();
	RequireComponents($components);
	InitSettings();
	RequireModules();
	//
	SetupPHP();
	SetupConfig();
	ApplyMarkupTemplates();
	//
	ExecuteLoadActions(); // Конечный запуск
	//
	CheckErrors();
}

/** Загрузка настроек в конфиг */
function InitSettings()
{
	global $engine_config;
	$engine_config['settings'] = GetSiteSettings();
}

/** Настройка php.ini */
function SetupPHP()
{
	//ini_set("upload_max_filesize", "128M");//htaccess
	//ini_set("post_max_size", "256M");
	ini_set("memory_limit", "512M");
	ini_set("max_input_time", 3600);
	ini_set("max_execution_time", 3600);
	
	ini_set("opcache.enable", 1);
	ini_set("opcache.save_comments", 1);
	ini_set("opcache.load_comments", 1);
}

/** Настройка конфига */
function SetupConfig()
{
	AddNodeVar("ServerTime", time());
	AddNodeVar("isAdminLogged", isAdminLogged());
	
	RequireDirectory("/files");
	RequireDirectory("/modules");
	RequireDirectory("/data");
	RequireDirectory("/data/blocks");
	RequireDirectory("/data/pages");
}

/** Исполняет события запуска */
function ExecuteLoadActions()
{
	global $engine_config;
	
	for($i = 0; $i < sizeof($engine_config['onload']); $i++)
	{
		$func = $engine_config['onload'][$i];
		$func();
	}
}

function CheckErrors()
{
	global $engine_config;
	$errors = $engine_config['error_stack'];
	
	if(sizeof($errors))
	{
		echo(implode("<br/>", $errors));
		exit();
	}
}

/** Включение компонентов движка */
function RequireComponents($names)
{
	for($i = 0; $i < sizeof($names); $i++)
	{
		if($names[$i] != "")require($names[$i].".php");
	}
}

/** Возвращает массив ссылок на точки входа модулей */
function GetModules()
{
	$config = GetConfig();
	$settings = GetSettings();
	$disabled_modules = $settings['disabled_modules']; //Получаем массив имен отключенных модулей
	//print_r($settings);
	
	$folders = GetFilesList($_SERVER['DOCUMENT_ROOT']."/modules", "");
	$output = array();
	
	for($i = 0; $i < sizeof($folders); $i++)
	{
		if(!in_array($folders[$i]['name'], $disabled_modules)) //Если модуль не отключен
		{
			$folder = $folders[$i]['name'];
			$output[sizeof($output)] = $_SERVER['DOCUMENT_ROOT']."/modules/".$folder."/".$folder.".php";
		}
	}
	
	return $output;
}

/** Включает модули, если их точки входа существуют */
function RequireModules()
{
	$modules = GetModules();
	
	for($i = 0; $i < sizeof($modules); $i++)
	{
		RequireIfExists($modules[$i]);
	}
}

//Страницы, навигация и прочее.

/** Разбирает пару строка-число вида /id1 */
function ParseSNFormula($url) //S - одно, N - в периоде
{
	$url_chars = str_split($url);
	
	$S = "";
	$N_str = "";
	$N_array = array();
	$N_offset = 0;
	
	for($i = 0; $i < sizeof($url_chars); $i++)
	{
		if(!isDigit($url_chars[$i]))
		{
			$S .= $url_chars[$i];
		}
		else
		{
			$N_offset = $i;
			break;
		}
	}
	
	if($N_offset != 0)
	{
		$N_str = substr($url, $N_offset, strlen($url) - 1);
		$N_array = explode("_", $N_str);
	}
	
	return array_merge(array($S), $N_array);
}

/** Вырезает имена секций из адреса */
function SplitURL($url = "")
{
	if($url == "")$url = $_SERVER['REQUEST_URI'];
	//
	$url_elements = explode("?", $url);
	$url = $url_elements[0];
	
	$url_arr = explode("/", $url);
	$out_arr = array();
	for($i = 1; $i < sizeof($url_arr); $i++)
	{
		if($url_arr[$i] == "")$url_arr[$i] = "main";
		
		$out_arr[$i - 1] = $url_arr[$i];
	}
	
	return $out_arr;
}

/** Структура секций движка */
function DefaultSection()
{
	return array(
		"type" => 0,
		"name" => "page".time(),
		"header" => "Новая страница",
		"title" => "Новая страница - {site_name}",
		"layout" => 0,
		"permission" => "",
		"keywords" => "",
		"description" => "",
		"section_dir" => "/engine/sections",
		"section_script" => "{0}/{1}.php",
		"section_markup" => "{0}/{1}_markup.php",
		"icon" => "",
		"preview_image" => "",
		"enabled" => 1,
		"is_public" => 1,
	);
}

/** Добавление секции в конфиг */
function AddSection($section_params) /*$type, $name, $layout, $permission, $header, $title, $keywords, $description*/
{
	global $engine_config;
	global $config;
	
	$section = DefaultSection();
	//
	$section = MergeArrays($section_params, $section);
	//
	$type = $section['type'];
	$name = $section['name'];
	$header = $section['header'];
	$title = $section['title'];
	$layout = $section['layout'];
	$section_dir = $section['section_dir'];
	//
	$section['title_wrap_enabled'] = $header != "";
	//
	$header_type = 0;
	if($layout == 0)$header_type = 1;
	if($layout == 1)$header_type = 1;
	if($layout == 2)$header_type = 0;
	if($layout == 3)$header_type = 2;
	if($layout == 4)$header_type = -1;
	if($layout == 5)$header_type = 1;
	
	$section['header_wrap_type'] = $header_type;
	//
	
	if($type == 0)
	{
		$section['section_script'] = StringFormat($section['section_script'], $section_dir, $name);
		$section['section_markup'] = StringFormat($section['section_markup'], $section_dir, $name);
	}
	
	if($type == 1)
	{
		$section['section_markup'] = StringFormat("/data/pages/{0}.php", $name);
		unset($section['section_script']);
	}
	
	//
	
	$section_data = MergeArrays($section, DefaultSection());
	if($section_data['enabled'] == 1 || isAdminLogged())
	{
		$engine_config['sections'][$name] = $section_data;
	}
}

/** Механизм выдачи секции */
function GetSection($URL, $settings)
{
	$URL_arr = SplitURL($URL);
	$URL_data = ParseSNFormula($URL_arr[0]);
	
	$section_name = $URL_data[0];
	
	//
	
	$section;
	if(SectionExists($URL_arr[0]))
	{
		$section = GetSectionConfig($URL, $settings);
	}
	elseif(SectionExists($section_name))
	{
		$section = GetSectionConfig($URL, $settings);
	}
	else
	{
		$section = GetSectionConfig("/error404", $settings);
	}
	
	//Кладем в базу факт просмотра
	PushView($section_name, $URL);
	
	//
	return $section;
}

/** Формирование секции из данных */
function GetSectionConfig($URL, $settings)
{
	global $config;
	global $engine_config;
	//
	$URL_arr = SplitURL($URL);
	$URL_data = ParseSNFormula($URL_arr[0]);
	//
	$section_name = "";
	if(SectionExists($URL_arr[0]))$section_name = $URL_arr[0];
	if(SectionExists($URL_data[0]))$section_name = $URL_data[0];
	//
	if(!SectionExists($section_name))exit("Section not found!");
	
	$section = $engine_config['sections'][$section_name];
	//
	$section['description'] = $section['description']." ".$settings['description'];
	$section['keywords'] = MergeRanges($settings['keywords'], $section['keywords']);
	//
	$section['url'] = $URL;
	$section['url_array'] = $URL_arr;
	$section['url_data'] = $URL_data;
	$section['request'] = RequestFromUrl($URL);
	
	if($section['icon'] == "")$section['icon'] = $settings['icon'];
	if($section['preview_image'] == "")$section['preview_image'] = $settings['preview_image'];
	//
	
	//Исполнение сопутствующего скрипта (можно добавлять в $section свои параметры конкретно для кажого раздела)
	if($section['type'] == 0)$section = RequireSectionIfExists($config['root'].$section['section_script'], $section); // Раньше надо было делать ссылку &$section
	
	$engine_config['current_section'] = $section;
	//
	$section['title'] = str_replace("{site_name}", $settings['site_name'], $section['title']);
	//
	return $section;
}

/** Проверка секции на существование */
function SectionExists($sect_name)
{
	$c = GetEngineConfig();
	return isset($c['sections'][$sect_name]);
}

/** Возвращает объект текущей секции, если таковая загружена */
function GetCurrentSection()
{
	global $engine_config;
	return $engine_config['current_section'];
}

/** Закрузка страниц в виде секций */
function LoadPages()
{
	$config = GetConfig();
	$config_files = GetFilesList($_SERVER['DOCUMENT_ROOT']."/data/pages", "_config.json", true, true);
	
	for($i = 0; $i < sizeof($config_files); $i++)
	{
		$page = $config_files[$i]['data'];
		$page = MergeArrays($page, DefaultSection());
		$page['type'] = 1;
		
		AddSection($page);
		
		//AddSection(1, $page['name'], $page['layout'], $page['permission'], $page['header'], $page['title'], $page['keywords'], $page['description']);
		
		/*AddSection(array(
			"type" => 1,
			"name" => $page['name'],
			"header" => $page['header'],
			"title" => $page['title'],
			"layout" => $page['layout'],
			"permission" => $page['permission'],
			"keywords" => $page['keywords'],
			"description" => $page['description'],
			"enabled" => $page['enabled'],
			
		));*/
	}
}

//

/** Создание каркаса по шаблону */
function CreateLayout($section, $settings)
{
	$config = GetConfig();
	$engine_config = GetEngineConfig();
	//
	ob_start();
	
	require($config['root']."/modules/".$settings['template']."/template.php");
	
	return ob_get_clean();
}

/** Генерация вёрстки */
function GetMarkup($section, $settings, $is_interpret = true)
{
	$config = GetConfig();
	$engine_config = GetEngineConfig();
	//
	$markup = "";
	
	if($section['type'] == 0)
	{
		ob_start();
		//RequireIfExists($config['root'].$section['section_markup'], "Markup file has not been found!");
		RequireSectionIfExists($config['root'].$section['section_markup'], $section, "Markup file has not been found!"); // &$section
		$markup = ob_get_clean();
	}
	if($section['type'] == 1)
	{
		$markup = ConvertText(GetFileContent($config['root'].$section['section_markup'], "Markup file has not been found!"));
		//
		if($is_interpret)
		{
			$markup = InterpretMarkup($markup);
		}
		
		if(isset($section['url']))
		{
			Search::Push($section['url'], $section['header'], StripHTML(StringFormat("{0} {1} {2}", $markup, $section['keywords'], $section['description'])));
		}
	}
	
	//Вставка передних и задних вставок
	$before_markup = "";
	if(TemplateExists($section['name'].":before"))$before_markup = InterpretMarkup("$".$section['name'].":before$");
	$after_markup = "";
	if(TemplateExists($section['name'].":after"))$after_markup = InterpretMarkup("$".$section['name'].":after$");
	
	$markup = $before_markup.$markup.$after_markup;
	//
	
	return $markup;
}

//Работа с шаблонными вставками сайта
/** Загрузка шаблонов в память */
function ApplyMarkupTemplates()
{
	global $engine_config;
	
	$templates = GetTemplates();
	$engine_config['markup_templates'] = $templates;
}

/** Получение шаблонов из .json */
function GetTemplates()
{
	$config = GetConfig();
	$engine_config = GetEngineConfig();
	
	$data = LoadJSON($config['root']."/data/markup_templates.json", array());
	$data = MergeArrays($data, $engine_config['markup_templates']);
	
	return $data;
}

/** Создаёт или меняет шаблон */
function CreateOrEditTemplate($template, $value)
{
	$config = GetConfig();
	$data = LoadJSON($config['root']."/data/markup_templates.json", array());
	
	$data[$template] = $value;
	
	SaveJSON($config['root']."/data/markup_templates.json", $data);
}

/** Удаляет шаблон */
function DeleteTemplate($template)
{
	$config = GetConfig();
	$data = LoadJSON($config['root']."/data/markup_templates.json", array());
	
	if(isset($data[$template]))unset($data[$template]);
	SaveJSON($config['root']."/data/markup_templates.json", $data);
}

//

/** Добавлет JS в конфиг */
function AddJS($path) //Добавление JS
{
	global $engine_config;
	$offset = sizeof($engine_config['javascripts']);
	$engine_config['javascripts'][$offset] = $path;
}

/** Добавляет CSS в конфиг */
function AddCSS($path) //Добавление CSS
{
	global $engine_config;
	$offset = sizeof($engine_config['stylesheets']);
	$engine_config['stylesheets'][$offset] = $path;
}

/** Создает произвольную ссылку в ПУ */
function AddAdminLink($href, $title, $external = 0) //Добавление дополнительных админских ссылок
{
	$link_obj = GetDefaultLink();
	$link_obj['title'] = $title;
	$link_obj['href'] = $href;
	$link_obj['external'] = $external;
	//
	global $engine_config;
	$offset = sizeof($engine_config['admin_links']);
	$engine_config['admin_links'][$offset] = $link_obj;
}

/** Добавляет узловую переменную */
function AddNodeVar($name, $value) //Добавление в конфиг серверной переменной
{
	global $engine_config;
	$engine_config['node_vars'][$name] = $value;
}

/** Добавляет событие в массив событий запуска */
function OnLoad($action) //Добавление в конфиг события загрузки
{
	global $engine_config;
	$count = sizeof($engine_config['onload']);
	$engine_config['onload'][$count] = $action;
}

/** Добавляет папку для создания движком */
function RequireDirectory($path)
{
	global $engine_config;
	$engine_config['folders'][] = $path;
}

function ThrowError($error)
{
	global $engine_config;
	$engine_config['error_stack'][] = $error;
}

/** Добавляет папки для создания движком */
function RequireDirectories($paths)
{
	foreach($paths as $path)
	{
		RequireDirectory($path);
	}
}

/** Выводит стили на страницу */
function DrawStyleSheets() //Вывод списка файлов стилей на страницу
{
	$engine_cfg = GetEngineConfig();
	
	for($i = 0; $i < sizeof($engine_cfg['stylesheets']); $i++)
	{
		Draw("<link rel='stylesheet' href='".$engine_cfg['stylesheets'][$i]."' type='text/css' />\n");
	}
}

/** Выводит скрипты на страницу */
function DrawJavaScripts() //Вывод списка скриптов на страницу
{
	$engine_cfg = GetEngineConfig();
	
	for($i = 0; $i < sizeof($engine_cfg['javascripts']); $i++)
	{
		Draw("<script src='".$engine_cfg['javascripts'][$i]."'></script>\n");
	}
}

/** Выводыт узловые переменные на страницу */
function DrawNodeVars() //Вывод в нод маcсив переменных с сервера
{
	$engine_cfg = GetEngineConfig();
	
	Draw("
	<script>
	var ServerData = NodeVars = ".ToJSON($engine_cfg['node_vars']).";
	</script>
	");
}

/** Добавлет подраздел в ПУ */
function AddAdminSection($name, $title, $path)
{
	global $engine_config;
	
	$engine_config['admin_sections'][$name] = array(
		"name" => $name,
		"title" => $title,
		"path" => $path,
	);
}


//Данные
/** Сохраняет событие в лог */
function LogAction($text, $type = 0)
{
	//type = 0 - зеленый (успешно)
	//type = 1 - красный (неудача)
	//type = 2 - пользовательская запись
	
	$newid = GetNewId("log", "id");
	//
	$time = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	//
	$query = SQL("INSERT INTO `log`(`id`, `ip`, `date`, `text`, `type`, `user_agent`) 
	VALUES ('$newid','$ip','$time','$text','$type', '$user_agent')", false);
	//
	return $newid;
}

/** Учитывает факт просмотра страницы */
function PushView($section, $url)
{
	$newid = GetNewId("views", "id");
	//
	$time = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$is_user_bot = isUserBot($user_agent);
	
	$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
	$startup_time = GetTimerDelta();
	//
	$day = GetDay($time);
	$month = GetMonth($time);
	$year = GetYear($time);
	
	$midnight = mktime(0, 0, 0, $month, $day, $year);
	$midnight_next = $midnight + (24 * 60 * 60);
	//
	$section = Security::String($section);
	$url = Security::String($url);
	$referer = Security::String($referer);
	$user_agent = Security::String($user_agent);
	//
	$new_unique = 0; // Проверка уникального по секции
	$query = SQL("SELECT COUNT(*) FROM `views` WHERE `date` >= '$midnight' AND `date` < '$midnight_next' AND `ip` = '$ip' AND `section` = '$section';");
	$num = SQLFetchArray($query);
	if($num[0] == 0)$new_unique = 1;
	//
	$all_new_unique = 0; // Проверка уникального по всей статистике
	$query = SQL("SELECT COUNT(*) FROM `views` WHERE `date` >= '$midnight' AND `date` < '$midnight_next' AND `ip` = '$ip';");
	$num = SQLFetchArray($query);
	if($num[0] == 0)$all_new_unique = 1;
	//
	if(!$is_user_bot)
	{
		ApplyViewsCounters($section, $day, $month, $year, 1, $new_unique, $startup_time, $all_new_unique);
	}
	//
	SQL("INSERT DELAYED INTO `views`(`id`, `ip`, `date`, `section`, `url`, `referer`, `user_agent`, `startup_time`) 
		VALUES ('$newid','$ip','$time','$section','$url','$referer','$user_agent','$startup_time')", false);
	//
}

/** Получает просмотры определенной секции или всего сайта */
function GetSectionViews($section = "all", $midnight = 0, $one_day = true)
{
	$by_section = "";
	$unique_field = "unique_users";
	if($section != "all")$by_section = "AND `section`='$section'";
	else $unique_field = "all_unique_users";
	//
	if($midnight == 0)$midnight = GetMidnight();
	
	$uptime = GetMidnight() + (3600 * 24);
	if($one_day)$uptime = $midnight + (3600 * 24);
	//
	$day = GetDay($midnight);
	$month = GetMonth($midnight);
	$year = GetYear($midnight);
	
	$nums = array();
	if($one_day)
	{
		$query = SQL("SELECT SUM(`views`), SUM(`".$unique_field."`), AVG(`startup_time`) FROM `views_counters` 
			WHERE `day`='$day' AND `month`='$month' AND `year`='$year' ".$by_section);
		$nums = SQLFetchArray($query);
	}
	else
	{
		$query = SQL("SELECT COUNT(*), COUNT(DISTINCT(`ip`)), AVG(`startup_time`) FROM `views` 
			WHERE `date` > '$midnight' AND `date` < '$uptime' ".$by_section." AND ".IgnoreBots("user_agent"));
		$nums = SQLFetchArray($query);
	}
	
	$views = $nums[0] != "" ? $nums[0] : 0;
	$unique = $nums[1] != "" ? $nums[1] : 0;
	$startup = round($nums[2]);
	//
	return array($views, $unique, $startup);
}

/** Обновлет счётчики статистики */
function ApplyViewsCounters($section, $day, $month, $year, $views, $unique_users, $startup_time, $all_unique_users)
{
	$get_stats_query = "SELECT * FROM `views_counters` WHERE `section`='$section' AND `day`='$day' AND `month`='$month' AND `year`='$year' LIMIT 1;";
	$query = SQL($get_stats_query);
	$stats = SQLFetchAssoc($query);
	$has_entry = $stats['id'] != "";
	
	if(!$has_entry)
	{
		$newid = GetNewId("views_counters", "id");
		$time = time();
		//
		SQL("INSERT INTO `views_counters`(`id`, `section`, `time`, `day`, `month`, `year`, `views`, `unique_users`, `startup_time`, `all_unique_users`) 
			VALUES ('$newid','$section','$time','$day','$month','$year','0','0','0','0')", false);
		//
		$query = SQL($get_stats_query);
		$stats = SQLFetchAssoc($query);
	}
	
	$startup_time = round(($stats['startup_time'] + $startup_time)/2);
	
	SQL("UPDATE `views_counters` SET `views` = (views + $views), `unique_users` = (unique_users + $unique_users), `startup_time` = $startup_time, `all_unique_users` = (all_unique_users + $all_unique_users)
		WHERE `section`='$section' AND `day`='$day' AND `month`='$month' AND `year`='$year';");
}

/** Исключение ботов из выборки стастики */
function IgnoreBots($field)
{
	$bot_tags = GetBotTags();
	$escaped_patterns = $bot_tags['escaped_patterns'];
	$required_patterns = $bot_tags['required_patterns'];
	//
	$entry = array();
	$entry[sizeof($entry)] = $field." != ''";
	
	for($i = 0; $i < sizeof($escaped_patterns); $i++)
	{
		$entry[sizeof($entry)] = $field." NOT LIKE '%".$escaped_patterns[$i]."%'";
	}
	
	for($i = 0; $i < sizeof($required_patterns); $i++)
	{
		$entry[sizeof($entry)] = $field." LIKE '%".$required_patterns[$i]."%'";
	}
	
	return "(".implode(" AND ", $entry).")";
}

/** Признаки ботов */
function GetBotTags()
{
	$escaped_patterns = array(
		"bot", "crawler", "search", "google", "yandex", "bing", "simplepie",
		"rambler", "mail", "sputnik", "bing", "ahrefs", "MJ12bot",
		"synapse", "wotbox", "sogou", "soso", "msn", "googlebot",
		"alexa", "baidu", "yahoo", "sv1", ".ua", "image",
		"matm", "vk", "facebook", ".ru", ".com",
		".net", ".to", ".co", ".cc", "www.", "curl", "lynx",
		"http", "ftp", "tcp", ".html", ".htm", ".asp", 
		".php", "icon", ".info", ".su", ".in", ".ch"
	);
	
	$required_patterns = array(
		"Mozilla/5.0",
	);
	
	return array(
		"escaped_patterns" => $escaped_patterns, 
		"required_patterns" => $required_patterns,
	);
}

/** Проверяет User Agent на признак бота */
function isUserBot($user_agent)
{
	$bot_tags = GetBotTags();
	$escaped_patterns = $bot_tags['escaped_patterns'];
	$required_patterns = $bot_tags['required_patterns'];
	
	$is_bot = false;
	
	for($i = 0; $i < sizeof($escaped_patterns); $i++)
	{
		if(stristr($user_agent, $escaped_patterns[$i]))$is_bot = true;
	}
	
	for($i = 0; $i < sizeof($required_patterns); $i++)
	{
		if(!stristr($user_agent, $required_patterns[$i]))$is_bot = true;
	}
	
	return $is_bot;
}

/** Получает статистику секции или сайта */
function GetStats($section = "all", $days = 31)
{
	$mids = GetMidnights($days);
	$views_data = array();
	
	for($i = 0; $i < sizeof($mids); $i++)
	{
		$views_data[$i] = GetSectionViews($section, $mids[$i]);
	}
	//
	$max_number = 0;
	
	$max_inner_nums = array();
	for($i = 0; $i < sizeof($views_data); $i++)
	{
		$max_inner_nums[$i] = max(array($views_data[$i][0], $views_data[$i][1]));
	}
	
	$max_number = max($max_inner_nums) + 1;
	//
	$stats_data = array();
	
	for($a = count($views_data)-1; $a >= 0; $a--)
	{
		$stats_data[$a] = array(
			"views" => intval($views_data[$a][0]),
			"unique" => intval($views_data[$a][1]),
			"startup_time" => intval($views_data[$a][2]),
			"date" => GetShortTime($mids[$a]),
			"max" => $max_number,
		);
	}
	
	return $stats_data;
}

/** Возвращает вёрстку статистики */
function GraphMarkup($id, $section = "all", $days = 14, $graph_id = 0)
{
	$m = "";
	
	$height = 150;
	
	$sig = Sig($section, $days);
	
	$table_id = "graph_table_".$graph_id;
	$m .= "
	<table class='stattable' style='height: ".$height."px;'>
		<tr id='".$table_id."'><td class='text'>Загрузка...</td></tr>
	</table>
	
	<script>
	
	ApiMethod('engine.sections.stats', { section: '".$section."', days: '".$days."', sig: '".$sig."' }, function(data){ 
		CreateGraph_".$graph_id."(data);
	});
	
	var CreateGraph_".$graph_id." = function(data)
	{
		if(data.response != 0)
		{
			var stats_data = data.response.items;
			var len = Object.keys(stats_data).length;
			
			var markup = '';
			if(len > 0)
			{
				height_ratio = ".$height."/stats_data[0].max;
				
				for(var i = 0; i < len; i++)
				{
					var h0 = (height_ratio * stats_data[i]['views']) + 2;
					var h1 = (height_ratio * stats_data[i]['unique']) + 1;
					
					var info_text = '' + 
					'Просмотры за ' + stats_data[i]['date'] + ': <span class=fore4>' + stats_data[i]['views'] + '</span><br/>' + 
					'Уникальные за ' + stats_data[i]['date'] + ': <span class=fore2>' + stats_data[i]['unique'] + '</span><br/>' + 
					'Среднее время за ' + stats_data[i]['date'] + ': <span class=fore5>' + stats_data[i]['startup_time'] + ' ms</span><br/>';
					
					markup = '' +
					'<td class=\'back3\' style=\'vertical-align: bottom; height: ".$height."px;\' data-caption=\'' + info_text + '\' onmouseover=\'console.log(this);Write(\"views_info_".$id."_".$graph_id."\", this.getAttribute(\"data-caption\"));\' onmouseout=\'Write(\"views_info_".$id."_".$graph_id."\", \"\");\'>' +
					'<div class=\'statcol n_back4\' style=\'height: ' + h0 + 'px; position: relative;\'>' +
					'<div class=\'statcol n_back2\' style=\'height: ' + h1 + 'px; position: absolute; bottom: 0px;\'></div>' +
					'</div>' +
					'</td>' +
					'' + markup;
				}
				
				markup += '' +
				'<td style=\'width: 10px;\'></td>' +
				'<td>' +
				'<div class=\'small_text\' style=\'text-align: left;\'>' +
				'Просмотры сегодня: <span class=\'fore4\'>' + stats_data[0]['views'] + '</span><br/>' +
				'Уникальные сегодня: <span class=\'fore2\'>' + stats_data[0]['unique'] + '</span><br/>' +
				'<div class=\'space\'></div>' +
				'<div id=\'views_info_".$id."_".$graph_id."\'></div>' +
				'</div>' +
				'</td>' +
				'';
				
				Clear('".$table_id."');
				Write('".$table_id."', markup);
			}
		}
	}
	</script>
	";
	
	return $m;
}


//Элементарные функции (Native)
/** Возвращает конфиг сайта */
function GetConfig()
{
	global $config;
	return $config;
}
/** Возвращает конфиг движка */
function GetEngineConfig()
{
	global $engine_config;
	return $engine_config;
}
/** Возвращает настройки сайта */
function GetSettings()
{
	$engine_config = GetEngineConfig();
	return $engine_config['settings'];
}

//
/** Выводи текст в поток */
function Draw($markup)
{
	echo $markup;
}
/** Обрабатывает равенство */
function isEqually($first, $second, $equals, $not_equals)
{
	if($first == $second)return $equals;
	else return $not_equals;
}

/** Обрабатывает логическое выражение */
function isTrue($exp, $satisfied, $unsatisfied)
{
	if($exp)return $satisfied;
	else return $unsatisfied;
}

/** Принадлежность символа к цифре */
function isDigit($char) //Ну, типа полезная функция
{
	$digits = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
	
	for($i = 0; $i < sizeof($digits); $i++)
	{
		if($char == $digits[$i])return true;
	}
	
	return false;
}

/** Сливает ассоциативные массивы */
function MergeArrays($data, $pattern) //Если в исходном массиве не хватает ключей, то они добавляются из паттерна
{
	foreach($pattern as $key => $value)
	{
		if(!isset($data[$key]))
		{
			$data[$key] = $value;
		}
	}
	
	return $data;
}

/** Сливает строки через запятую */
function MergeRanges($base, $ext) //Сливает последовательности через запятую, если новый кусок не пустой (так генерируется keywords)
{
	if($ext != "")$base .= ", ".$ext;
	return $base;
}

/** Парсит URL в массив параметров */
function RequestFromUrl($url) //Парсит url в массив параметров после знака "?". Request Array строится на основе текущего понимания сабжа
{
	$url_arr = explode("?", $url);
	
	if(sizeof($url_arr) > 1)
	{
		$params = explode("&", $url_arr[1]);
		
		$request = array();
		
		for($i = 0; $i < sizeof($params); $i++)
		{
			$param = explode("=", $params[$i]);
			if(!isset($param[1]))$param[1] = null;//Если [1] не существует, то нулим (?aaa&bbb=N)
			//
			$request[$param[0]] = $param[1];
		}
		
		return $request;
	}
	
	return array();
}

/** Кодирует JSON */
function ToJSON($arr)
{
	return json_encode($arr, true);
}

/** Раскодирует JSON */
function FromJSON($str)
{
	return json_decode($str, true);
}

/** Перезагружает страницу */
function Reload()
{
	Header("Location: ".$_SERVER[REQUEST_URI]);
}

/** Перенаправляет запрос */
function Navigate($url)
{
	Header("Location: ".$url);
}

/** Определяет ОС по User Agent */
/*function GetOS($ua = "")
{
	if($ua == "")$ua = $_SERVER['HTTP_USER_AGENT'];
	
	$assocs = array(
		"Windows 95", "Windows 95",
		"Windows 98", "Windows 98",
		"Windows ME", "Windows ME",
		"Windows NT 5.0", "Windows 2000",
		"Windows NT 5.1", "Windows XP",
		"Windows NT 5.2", "Windows 2003",
		"Windows NT 6.0", "Windows Vista",
		"Windows NT 6.1", "Windows 7",
		"Windows NT 6.2", "Windows 8",
		"Windows NT 6.3", "Windows 8.1",
		"Windows NT 10.0", "Windows 10",
		"Windows Phone 8.1", "Windows Phone 8.1",
		"Windows Phone 10", "Windows Phone 10",
		"Windows Phone", "Windows Phone",
		"Macintosh", "OS X",
		"Android 2", "Android 2",
		"Android 3", "Android 3",
		"Android 4", "Android 4",
		"Android 4.4", "Android 4.4",
		"Android 5", "Android 5",
		"Android 6", "Android 6",
		"Android 7", "Android 7",
		"Android 8", "Android 8",
		"Android 9", "Android 9",
		"Android", "Android",
		"Ubuntu", "Ubuntu",
		"Linux", "Linux",
		"iPhone OS 10", "iOS 10",
		"iPhone OS 9", "iOS 9",
		"iPhone OS 8", "iOS 8",
		"iPhone OS 7", "iOS 7",
		"iPhone OS 6", "iOS 6",
		"iPhone OS 5", "iOS 5",
		"iPhone OS 4", "iOS 4",
		"iPhone", "iOS",
		"Series 60", "Symbian",
	);
	
	for($i = 0; $i < sizeof($assocs); $i += 2)
	{
		if(stristr($ua, $assocs[$i + 0]))return $assocs[$i + 1];
	}
	
	return 'Unknown';
}*/

/** Определяет браузер по User Agent */
/*function GetBrowser($ua = "")
{
	if($ua == "")$ua = $_SERVER['HTTP_USER_AGENT'];
	
	$assocs = array(
		"Edge", "Edge",
		"IEMobile", "IE Mobile",
		"YaBrowser", "Yandex.Browser",
		"OPR", "Opera",
		"Amigo", "Amigo",
		"Vivaldi", "Vivaldi",
		"Chrome", "Chrome", //Все хром-подобные выше этой позиции
		"AppleWebKit", "WebKit",
		"Firefox", "Firefox",
		"Safari", "Safari",
		"Opera", "Opera",
		"Opera Mini", "Opera Mini",
		"MSIE 6.0", "IE 6",
		"MSIE 7.0", "IE 7",
		"MSIE 8.0", "IE 8",
		"MSIE 9.0", "IE 9",
		"MSIE 10.0", "IE 10",
		"MSIE 11.0", "IE 11",
		"MSIE 12.0", "IE 12",
	);
	
	for($i = 0; $i < sizeof($assocs); $i += 2)
	{
		if(stristr($ua, $assocs[$i + 0]))return $assocs[$i + 1];
	}
	
	return 'Unknown';
}*/

/** Определяет ОС по User Agent */
function GetOS($ua = "")
{
	if($ua == "")$ua = Security::String($_SERVER['HTTP_USER_AGENT']);
	
	$assocs = array(
		"Windows 95", "Windows 95",
		"Windows 98", "Windows 98",
		"Windows ME", "Windows ME",
		"Windows NT 5.0", "Windows 2000",
		"Windows NT 5.1", "Windows XP",
		"Windows NT 5.2", "Windows 2003",
		"Windows NT 6.0", "Windows Vista",
		"Windows NT 6.1", "Windows 7",
		"Windows NT 6.2", "Windows 8",
		"Windows NT 6.3", "Windows 8.1",
		"Windows NT 10.0", "Windows 10",
		"Windows Phone 8.1", "Windows Phone 8.1",
		"Windows Phone 10", "Windows Phone 10",
		"Windows Phone", "Windows Phone",
		"Macintosh", "macOS",
		"Android 2", "Android 2",
		"Android 3", "Android 3",
		"Android 4", "Android 4",
		"Android 4.4", "Android 4.4",
		"Android 5", "Android 5",
		"Android 6", "Android 6",
		"Android 7", "Android 7",
		"Android 8", "Android 8",
		"Android 9", "Android 9",
		"Android", "Android",
		"Ubuntu", "Ubuntu",
		"Linux", "Linux",
		"iPhone OS 10", "iOS 10",
		"iPhone OS 9", "iOS 9",
		"iPhone OS 8", "iOS 8",
		"iPhone OS 7", "iOS 7",
		"iPhone OS 6", "iOS 6",
		"iPhone OS 5", "iOS 5",
		"iPhone OS 4", "iOS 4",
		"iPhone", "iOS",
		"Series 60", "Symbian",
	);
	
	$arch_patterns = array(
		"Win64",
		"WOW64",
		"x86_64",
		"x64",
	);
	
	$name = "";
	for($i = 0; $i < sizeof($assocs); $i += 2)
	{
		if(stristr($ua, $assocs[$i + 0]) !== false)
		{
			$name = $assocs[$i + 1];
			break;
		}
	}
	
	$arch = "";
	for($i = 0; $i < sizeof($arch_patterns); $i++)
	{
		if(stristr($ua, $arch_patterns[$i]) !== false)
		{
			$arch = " (x64)";
			break;
		}
	}
	
	if($name != "")
	{
		return $name.$arch;
	}
	
	return 'Unknown';
}

/** Определяет браузер по User Agent */
function GetBrowser($ua = "")
{
	if($ua == "")$ua = Security::String($_SERVER['HTTP_USER_AGENT']);
	
	$assocs = array(
		"YaBrowser", "Yandex.Browser",
		"OPR", "Opera",
		"Amigo", "Amigo",
		"Vivaldi", "Vivaldi",
		"UCBrowser", "UC Browser",
		"Chrome", "Chrome", //Все хром-подобные выше этой позиции
		"Firefox", "Firefox",
		"Opera", "Opera",
		"Opera Mini", "Opera Mini",
		"Edge", "Edge",
		"IEMobile", "IE Mobile",
		"Trident/6", "IE 10",
		"Trident/7", "IE 11",
		"MSIE 6", "IE 6",
		"MSIE 7", "IE 7",
		"MSIE 8", "IE 8",
		"MSIE 9", "IE 9",
		"MSIE 10", "IE 10",
		"MSIE 11", "IE 11",
		"MSIE 12", "IE 12",
		"Trident", "IE",
		"AppleWebKit", "Safari",
		"Safari", "Safari",
	);
	
	for($i = 0; $i < sizeof($assocs); $i += 2)
	{
		if(stristr($ua, $assocs[$i + 0])) 
		{
			return $assocs[$i + 1];
		}
	}
	
	return 'Unknown';
}

/** Объявляет время запуска движка */
function StartTimer()
{
	global $engine_config;
	$engine_config['engine_startup_time'] = microtime(true);
}

/** Возвращает время работы движка */
function GetTimerDelta()
{
	global $engine_config;
	$load_start = $engine_config['engine_startup_time'];
	$load_end = microtime(true);
	
	$load_time = round(($load_end - $load_start) * 1000);//Миллисекунды
	return $load_time;
}

/*echo("<pre>");
print_r(GetEngineStructure());
echo("</pre>");
exit();*/


/** Возвращает функциональную структуру движка */
function GetEngineStructure()
{
	/*
	ini_set("opcache.enable", 1);
	ini_set("opcache.save_comments", 1);
	ini_set("opcache.load_comments", 1);
	*/
	$all_functions = get_defined_functions();
	$functions = $all_functions['user'];
	
	$result = array();
	
	for($f = 0; $f < sizeof($functions); $f++)
	{
		$reflection = new ReflectionFunction($functions[$f]);
		//
		$params = $reflection->getParameters();
		$params_names = array();
		//
		$file_path = $reflection->getFileName();
		$file_path_array = explode($_SERVER["HTTP_HOST"], $file_path);
		$file_path = $file_path_array[sizeof($file_path_array) - 1];
		//
		for($p = 0; $p < sizeof($params); $p++)
		{
			$params_names[] = StringFormat("\${0}", $params[$p]->GetName());
		}
		$function_name = $reflection->GetName();
		$function_comment = $reflection->getDocComment();
		
		$function_comment = str_replace("/**", "", $function_comment);
		$function_comment = str_replace("*/", "", $function_comment);
		$function_comment = trim($function_comment);
		
		$function_sign = StringFormat("{0}({1})", $function_name, implode(",", $params_names));
		//
		$result[] = array(
			"signature" => $function_sign,
			"name" => $function_name,
			"params" => $params_names,
			"comment" => $function_comment,
			"file_path" => $file_path,
			"reflection" => $reflection,
		);
	}
	
	return $result;
}
?>
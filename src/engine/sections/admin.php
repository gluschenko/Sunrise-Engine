<?php
function InitAdminSections()
{
	$cfg = GetConfig();
	$sections_dir = $cfg['root']."/engine/sections/";
	//
	AddAdminSection("settings", "Настройки", $sections_dir."admin_markup_settings.php");
	AddAdminSection("pages", "Страницы", $sections_dir."admin_markup_pages.php");
	AddAdminSection("edit_page", "Редактирование страницы", $sections_dir."admin_markup_edit_page.php");
	AddAdminSection("nav", "Навигация", $sections_dir."admin_markup_nav.php");
	AddAdminSection("edit_bar", "Редактирование бара", $sections_dir."admin_markup_edit_bar.php");
	AddAdminSection("upload", "Файлы", $sections_dir."admin_markup_upload.php");
	AddAdminSection("modules", "Модули", $sections_dir."admin_markup_modules.php");
	AddAdminSection("stats", "Статистика", $sections_dir."admin_markup_stats.php");
	AddAdminSection("templates", "Шаблонные вставки", $sections_dir."admin_markup_templates.php");
	AddAdminSection("system", "Система", $sections_dir."admin_markup_system.php");
}
//
$request = $section['request'];

if(!isset($request['act']))$request['act'] = "";

if(!isAdminLogged())
{
	$section['header_wrap_type'] = 0;
	$section['layout'] = 2;
	
	$section['logged'] = false;
	
	$section['header'] = "Вход в панель управления";
}
else
{
	$section['logged'] = true;
	
	InitAdminSections();
	
	$engine_cfg = GetEngineConfig();
	
	if(isset($engine_cfg['admin_sections'][$request['act']]))
	{
		$section['header'] = $engine_cfg['admin_sections'][$request['act']]['title'];
	}
	
	
	/*if($request['act'] == "")
	{
		$section['header'] = "Панель управления";
	}
	
	if($request['act'] == "settings")
	{
		$section['header'] = "Настройки";
	}
	
	if($request['act'] == "pages")
	{
		$section['header'] = "Страницы";
	}
	
	if($request['act'] == "edit_page")
	{
		$section['header'] = "Редактирование страницы";
	}
	
	if($request['act'] == "nav")
	{
		$section['header'] = "Навигация";
	}
	
	if($request['act'] == "edit_bar")
	{
		$section['header'] = "Редактирование бара";
	}
	
	if($request['act'] == "upload")
	{
		$section['header'] = "Файлы";
	}
	
	if($request['act'] == "modules")
	{
		$section['header'] = "Модули";
	}
	
	if($request['act'] == "stats")
	{
		$section['header'] = "Статистика";
	}
	
	if($request['act'] == "news")
	{
		$section['header'] = "Новости";
	}
	
	if($request['act'] == "edit_news")
	{
		$section['header'] = "Редактирование новости";
	}*/
}

?>
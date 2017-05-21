<?php
//Alexander Gluschenko (15-07-2015)

//Навигация

AddMethod("engine.sections.get", function($params){
	$URL = $params['url'];
	
	$settings = GetSiteSettings();
	$section = GetSection($URL, $settings);
	
	$section["markup"] = GetMarkup($section, $settings);
	
	return $section;
});

//Сатистика

AddMethod("engine.sections.stats", function($params){
	$section = $params['section'];
	$days = $params['days'];
	$sig = $params['sig'];
	
	if(TrueSig($sig, $section, $days))
	{
		return array("items" => GetStats($section, $days));
	}
		
	return array("items" => array());
});

//Авторизация администратора

AddMethod("admin.auth", function($params){
	$password = $params['password'];
	
	if(AdminAuth($password))
	{
		LogAction("Авторизация в панели управления", 0);
		return 1;
	}
	
	LogAction("Неудачная авторизация в панели управления", 1);
	return 0;
});

AddMethod("admin.logout", function($params){
	if(isAdminLogged())
	{
		AdminLogout();
		
		LogAction("Выход из панели управления", 0);
		return 1;
	}
	return 0;
});

//Настройки

AddMethod("admin.settings.save", function($params){
	if(isAdminLogged())
	{
		SaveSiteSettings($params);
		
		LogAction("Изменены настройки сайта", 0);
		return 1;
	}
	
	LogAction("Неудачная попытка измения настроек сайта", 1);
	return 0;
});

AddMethod("admin.modules.disable", function($params){
	$module_name = $params['module'];
	
	if(isAdminLogged())
	{
		$settings = GetSiteSettings();
		$modules = $settings['disabled_modules'];
		
		$modules[sizeof($modules)] = $module_name;
		
		SaveSiteSettings(array("disabled_modules" => $modules));
		
		LogAction("Отключен модуль ".$module_name, 0);
		return 1;
	}
	
	LogAction("Неудачная попытка отключить модуль ".$module_name, 1);
	return 0;
});

AddMethod("admin.modules.enable", function($params){
	$module_name = $params['module'];
	
	if(isAdminLogged())
	{
		$settings = GetSiteSettings();
		$modules = $settings['disabled_modules'];
		$new_modules = array();
		for($i = 0; $i < sizeof($modules); $i++)
		{
			if($modules[$i] != $module_name)$new_modules[sizeof($new_modules)] = $modules[$i];
		}
		
		SaveSiteSettings(array("disabled_modules" => $new_modules));
		
		LogAction("Включен модуль ".$module_name, 0);
		return 1;
	}
	
	LogAction("Неудачная попытка включить модуль ".$module_name, 1);
	return 0;
});

//Логи

AddMethod("admin.log.send", function($params){
	if(isAdminLogged())
	{
		$text = $params['text'];
		$need_markup = $params['need_markup'];
		
		$id = LogAction($text, 2);
		//
		$query = SQL("SELECT * FROM `log` WHERE `id`='$id' LIMIT 1");
		$row = SQLFetchAssoc($query);
		
		if($need_markup != 0)return array($id, ActionMarkup($row));
		else return 1;
	}
	
	return 0;
});

//Страницы

AddMethod("admin.pages.save", function($params){
	if(isAdminLogged())
	{
		CreateOrEditPage($params['data'], $params['markup']);
		
		LogAction("Сохранена страница с идентификатором → ".$params['data']['name'], 0);
		return 1;
	}
	
	LogAction("Неудачная попытка затронуть страницу → ".$params['data']['name'], 1);
	return 0;
});

AddMethod("admin.pages.delete", function($params){
	if(isAdminLogged())
	{
		DeletePage($params['page']);
		
		LogAction("Удалена страница с идентификатором → ".$params['page'], 0);
		return 1;
	}
	
	LogAction("Неудачная попытка удалить страницу с идентификатором → ".$params['page'], 1);
	return 0;
});

//Бары

AddMethod("admin.bars.save", function($params){
	if(isAdminLogged())
	{
		CreateOrEditBar($params);
		
		LogAction("Сохранен бар с идентификатором → ".$params['id'], 0);
		return 1;
	}
	
	LogAction("Неудачная попытка затронуть бар с идентификатором → ".$params['id'], 1);
	return 0;
});

//Шаблонные вставки

AddMethod("admin.templates.save", function($params){
	if(isAdminLogged())
	{
		$id = $params['template'];
		$data = $params['data'];
		//
		$id = str_replace("$", "", $id);
		//
		CreateOrEditTemplate($id, $data);
		
		LogAction("Сохранен шаблон с идентификатором → $".$params['template']."$", 0);
		return 1;
	}
	
	LogAction("Неудачная попытка затронуть шаблон с идентификатором → $".$params['template']."$", 1);
	return 0;
});

AddMethod("admin.bars.delete", function($params){
	if(isAdminLogged())
	{
		$id = $params['id'];
		DeleteBar($id);
		
		LogAction("Удален бар с идентификатором → ".$params['id'], 0);
		return 1;
	}
	
	LogAction("Неудачная попытка удалить бар с идентификатором → ".$params['id'], 1);
	return 0;
});

AddMethod("admin.templates.get", function($params){
	if(isAdminLogged())
	{
		$templates = GetTemplates();
		if(isset($templates[$params['template']]))
		{
			return array("name" => $params['template'], "data" => $templates[$params['template']]);
		}
	}
	
	return 0;
});

AddMethod("admin.templates.delete", function($params){
	if(isAdminLogged())
	{
		$template = $params['template'];
		DeleteTemplate($template);
		
		LogAction("Удален шаблон с идентификатором → $".$params['template']."$", 0);
		return 1;
	}
	
	LogAction("Неудачная попытка удалить шаблон с идентификатором → $".$params['template']."$", 1);
	return 0;
});

//

AddMethod("admin.files.delete", function($params){
	if(isAdminLogged())
	{
		$config = GetConfig();
		DeleteFile($config['root']."/files/".$params['file']);
		
		LogAction("Удален файл → ".$params['file'], 0);
		return 1;
	}
	
	LogAction("Неудачная попытка удалить файл → ".$params['file'], 1);
	return 0;
});

AddMethod("admin.files.rename", function($params){
	if(isAdminLogged())
	{
		$config = GetConfig();
		//
		$file_name = $params['file'];
		$file_path = $config['root']."/files/".$file_name;
		//
		$new_file_name = $params['new_file'];
		$new_file_name = NormalizeFileName($new_file_name);
		$new_file_path = $config['root']."/files/".$new_file_name;
		
		if($file_name == "" || $new_file_name == "")return 0;
		
		while(file_exists($new_file_path))
		{
			$new_file_name = StringFormat("_{0}", $new_file_name);
			$new_file_path = $config['root']."/files/".$new_file_name;
		}
		
		//
		
		if(isValidType($new_file_name, ""))
		{
			RenameFile($file_path, $new_file_path);
		}
		else
		{
			return 0;
		}
		
		LogAction("Переименован файл ".$file_name." → ".$new_file_name, 0);
		return 1;
	}
	
	LogAction("Неудачная попытка переименовать файл → ".$params['file'], 1);
	return 0;
});

//
AddMethod("admin.files.resize", function($params){
	if(isAdminLogged())
	{
		$config = GetConfig();
		
		$file = $params['file']; //Имя картиночного файла
		$size_rate = $params['size_rate']; //Проценты
		
		if(isImageType($file))
		{
			ResizeImage($config['root']."/files/".$file, $size_rate/100);
			
			return 1;
		}
	}
	
	return 0;
});

//

AddMethod("feedback.email.send", function($params){
	$receivers = $params['receivers'];//Строка с перечислением через запятую
	$author = $params['author'];
	$subject = $params['subject'];
	$text = $params['text'];
	
	$receivers_arr = explode(",", $receivers);
	
	for($i = 0; $i < sizeof($receivers_arr); $i++)
	{
		SendMail($receivers_arr[$i], $author, $subject, $text);
		if($i != sizeof($receivers_arr) - 1)sleep(3);//Если не последний элемент массива, то засыпаем на 3 сек.
	}
	
	return 1;
});

//

AddMethod("engine.backupAll", function($params){
	if(isAdminLogged())
	{
		IO::BackupAll();
		return 1;
	}
	
	return 0;
});

?>
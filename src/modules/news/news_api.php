<?php

AddMethod("engine.news.get", function($params){
	$number = Security::String($params['number']);
	$offset = Security::String($params['offset']);
	$need_markup = Security::String(isset($params['need_markup']) ? $params['need_markup'] : 0);
	
	$posts = GetNews($number, $offset);
	
	$result = array("posts" => $posts);
	
	if($need_markup == 1)
	{
		$markup = "";
		
		for($i = 0; $i < sizeof($posts); $i++)
		{
			$markup .= GetNewsMarkup($posts[$i], true);
		}
		
		$result['markup'] = $markup;
	}
	
	return $result;
	
	return 0;
});

AddMethod("admin.news.create", function($params){
	$header = Security::String($params['header']);
	$text = $params['text'];
	$preview_image = Security::String($params['preview_image']);
	$category = Security::String($params['category']);
	$author = Security::String($params['author']);
	
	if(isAdminLogged())
	{
		if(CreateNews($header, $text, $preview_image, $category, $author))
		{
			LogAction("Создана новость → ".$header, 0);
			return 1;
		}
	}
	
	LogAction("Неудачная попытка создать новость → ".$header, 1);
	return 0;
});

AddMethod("admin.news.edit", function($params){
	$id = Security::String($params['id']);
	$header = Security::String($params['header']);
	$text = $params['text'];
	$preview_image = Security::String($params['preview_image']);
	$category = Security::String($params['category']);
	$author = Security::String($params['author']);
	
	if(isAdminLogged())
	{
		if(EditNews($id, $header, $text, $preview_image, $category, $author))
		{
			LogAction("Изменена новость → ID".$id, 0);
			return 1;
		}
	}
	
	LogAction("Неудачная попытка изменить новость → ID".$id, 1);
	return 0;
});

AddMethod("admin.news.delete", function($params){
	$id = Security::String($params['id']);
	
	if(isAdminLogged())
	{
		if(DeleteNews($id))
		{
			LogAction("Удалена новость → ID".$id, 0);
			return 1;
		}
	}
	
	LogAction("Неудачная попытка удалить новость → ID".$id, 1);
	return 0;
});

AddMethod("admin.news.restore", function($params){
	$id = Security::String($params['id']);
	
	if(isAdminLogged())
	{
		if(RestoreNews($id))
		{
			LogAction("Восстановлена новость → ID".$id, 0);
			return 1;
		}
	}
	
	LogAction("Неудачная попытка восстановить новость → ID".$id, 1);
	return 0;
});

AddMethod("admin.news.pin", function($params){
	$id = Security::String($params['id']);
	
	if(isAdminLogged())
	{
		if(SetNewsPin($id, 1))
		{
			return 1;
		}
	}
	
	return 0;
});

AddMethod("admin.news.unpin", function($params){
	$id = Security::String($params['id']);
	
	if(isAdminLogged())
	{
		if(SetNewsPin($id, 0))
		{
			return 1;
		}
	}
	
	return 0;
});

?>
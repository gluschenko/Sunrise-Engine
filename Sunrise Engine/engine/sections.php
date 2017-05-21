<?php
//Alexander Gluschenko (15-07-2015)

AddSection(array(
	"type" => 0,
	"name" => "main",
	"header" => "Главная",
	"title" => "{site_name}",
	"layout" => 0,
	"permission" => 0,
	"keywords" => "",
	"description" => "",
));

AddSection(array(
	"type" => 0,
	"name" => "error",
	"header" => "",
	"title" => "Ошибка - {site_name}",
	"layout" => 2,
	"permission" => 0,
	"keywords" => "",
	"description" => "",
	"is_public" => 0,
));

AddSection(array(
	"type" => 0,
	"name" => "admin",
	"header" => "Панель управления",
	"title" => "Панель управления - {site_name}",
	"layout" => 3,
	"permission" => 0,
	"keywords" => "",
	"description" => "",
	"is_public" => 0,
));

AddSection(array(
	"type" => 0,
	"name" => "dev",
	"header" => "...",
	"title" => "Sunrise Engine",
	"layout" => 4,
	"permission" => 0,
	"keywords" => "",
	"description" => "",
	"is_public" => 0,
));

AddSection(array(
	"type" => 0,
	"name" => "test",
	"header" => "Тест",
	"title" => "Тест - {site_name}",
	"layout" => 1,
	"permission" => 0,
	"keywords" => "",
	"description" => "",
	"is_public" => 0,
));

AddSection(array(
	"type" => 0,
	"name" => "matrix",
	"header" => "...",
	"title" => "Matrix",
	"layout" => 4,
	"permission" => 0,
	"keywords" => "",
	"description" => "",
	"is_public" => 0,
));

//

LoadPages();//Загрузка страниц в виде секций


?>
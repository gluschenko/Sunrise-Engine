<?php

InitNews();
function InitNews()
{
	$cfg = GetConfig();
	$sections_dir = dirname(__FILE__)."/sections";
	
	AddAdminSection("news", "Новости", $sections_dir."/admin_markup_news.php");
	AddAdminSection("edit_news", "Редактирование новости", $sections_dir."/admin_markup_edit_news.php");
	
	AddSection(array(
		"type" => 0,
		"name" => "news",
		"header" => "Новости",
		"title" => "Новости - {site_name}",
		"layout" => 0,
		"permission" => 0,
		"keywords" => "",
		"description" => "",
		"section_dir" => "/modules/news/sections",
	));
	
	
}

AddTable("news", array(
	TableField("id", "int(11)"),
	TableField("ip", "text"),
	TableField("status", "int(11)"),
	TableField("created", "int(11)"),
	TableField("edited", "int(11)"),
	TableField("deleted", "int(11)"),
	TableField("restored", "int(11)"),
	TableField("header", "text"),
	TableField("text", "text"),
	TableField("preview_image", "text"), // new
	TableField("category", "text"), // new
	TableField("author", "text"), // new
	//TableField("address", "text"), // new
	TableField("views", "int(11)"), // new
	TableField("pin", "int(11)"),
));


?>
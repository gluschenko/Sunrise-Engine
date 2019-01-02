<?php
//Alexander Gluschenko (20-09-2015)
//Структура стандартной базы данных движка

AddTable("log", array(
	TableField("id", "int(11)"),
	TableField("ip", "text"),
	TableField("date", "int(11)"),
	TableField("text", "text"),
	TableField("type", "int(11)"),
	TableField("user_agent", "text"),
));

AddTable("views", array(
	TableField("id", "int(11)"),
	TableField("ip", "text"),
	TableField("date", "int(11)"),
	TableField("section", "text"),
	TableField("url", "text"),
	TableField("referer", "text"),
	TableField("user_agent", "text"),
	TableField("startup_time", "int(11)"),
));

AddTable("views_counters", array(
	TableField("id", "int(11)"),
	TableField("section", "text"),
	TableField("time", "int(11)"),
	TableField("day", "int(11)"),
	TableField("month", "int(11)"),
	TableField("year", "int(11)"),
	TableField("views", "int(11)"),
	TableField("unique_users", "int(11)"),
	TableField("startup_time", "int(11)"),
	TableField("all_unique_users", "int(11)"),
));

AddTable("search_index", array(
	TableField("id", "int(11)"),
	TableField("url", "text"),
	TableField("title", "text"),
	TableField("content", "text"),
	TableField("time", "int(11)"),
	TableField("updates", "int(11)"),
));

?>
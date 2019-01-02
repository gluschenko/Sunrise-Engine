<?
//Таблицы модуля расписаний в основной базе данных

AddTable("schedule_groups", array(
	TableField("id", "int(11)"),
	TableField("name", "text"),
	TableField("thumbnail", "text"),
	TableField("created", "int(11)"),
	TableField("year", "int(11)"),
	TableField("standard_schedule", "text"),
	TableField("status", "int(11)"),
));

AddTable("schedule_changes", array(
	TableField("id", "int(11)"),
	TableField("created", "int(11)"),
	TableField("year", "int(11)"),
	TableField("month", "int(11)"),
	TableField("day", "int(11)"),
	TableField("day_time", "int(11)"),
	TableField("schedule", "text"),
	TableField("status", "int(11)"),
));

AddTable("schedule_hints", array(
	TableField("id", "int(11)"),
	TableField("group_id", "int(11)"),
	TableField("last_usage", "int(11)"),
	TableField("subject", "text"),
));

?>
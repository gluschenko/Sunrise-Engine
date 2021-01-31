<?php
//Alexander Gluschenko (22-09-2015)

AddTable("profiles", array(
	TableField("id", "int(11)"),
	TableField("link", "text"),
	TableField("email", "text"),
	TableField("password", "text"),
	TableField("status", "int(11)"),
	TableField("visits", "int(11)"),
	TableField("reg_date", "int(11)"),
	TableField("last_seen", "int(11)"),
	TableField("first_name", "text"),
	TableField("last_name", "text"),
	TableField("avatar", "int(11)"),
	TableField("about", "text"),
	TableField("vk_id", "int(11)"),
	TableField("user_agent", "text"),
));

AddTable("anonimous_users", array(
	TableField("id", "int(11)"),
	TableField("ip", "text"),
	TableField("name", "text"),
	TableField("user_agent", "text"),
	TableField("created", "int(11)"),
	TableField("last_seen", "int(11)"),
	TableField("status", "int(11)"),
));

AddTable("posts", array(
	TableField("id", "int(11)"),
	TableField("owner", "int(11)"),
	TableField("recipient", "int(11)"),
	TableField("recipient_type", "text"),
	TableField("status", "int(11)"),
	TableField("created", "int(11)"),
	TableField("edited", "int(11)"),
	TableField("deleted", "int(11)"),
	TableField("restored", "int(11)"),
	TableField("text", "text"),
	TableField("attachments", "text"),
));

AddTable("content", array(
	TableField("id", "int(11)"),
	TableField("owner", "int(11)"),
	TableField("status", "int(11)"),
	TableField("created", "int(11)"),
	TableField("deleted", "int(11)"),
	TableField("restored", "int(11)"),
	TableField("type", "text"),
	TableField("data", "text"),
));

AddTable("notifications", array(
	TableField("id", "int(11)"),
	TableField("owner", "int(11)"),
	TableField("recipient", "int(11)"),
	TableField("created", "int(11)"),
	TableField("checked", "int(11)"),
	TableField("message", "text"),
	TableField("link", "text"),
	TableField("status", "int(11)"),
));

AddTable("topics", array(
	TableField("id", "int(11)"),
	TableField("owner", "int(11)"),
	TableField("status", "int(11)"),
	TableField("created", "int(11)"),
	TableField("edited", "int(11)"),
	TableField("deleted", "int(11)"),
	TableField("restored", "int(11)"),
	TableField("title", "text"),
	TableField("pinned", "int(11)"),
	TableField("closed", "int(11)"),
	TableField("rating", "int(11)"),
	TableField("last_writer", "int(11)"),
));

?>
<?php
SocialPreStart();

//Точка внедрения модуля
require("social_config.php");
require("social_api.php");
require("social_sections.php");
require("social_layout.php");
require("social_database.php");
require("social_vk.php");
//

OnLoad(function() {
	AddAdminLink("/board", "Обсуждения", 1);
	AddAdminLink("/login", "Вход в обсуждения", 1);
	AddAdminLink("javascript:ShowOnlineWindow();", "Пользователи онлайн (".sizeof(GetOnlineUsers()).")", 1);
});

function SocialPreStart()
{
	global $engine_config;
	$engine_config['logged'] = isLogged();
	//
	AddJS("/modules/social/social_io.js");
	AddCSS("/modules/social/assets/social_styles.css");
	//
	AddNodeVar("isSocialLogged", $engine_config['logged']);
	//
	InitAnonimousUser();
}

function InitAnonimousUser() // Работа с анонимусами
{
	if(!isLogged())
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	
		if(isAnonimousExists($ip))
		{
			UpdateAnonimousUser($ip);
		}
		else
		{
			//if(!isUserBot($_SERVER['HTTP_USER_AGENT']))
			CreateAnonimousUser();
		}
	}
	/*else
	{
		UnsetData("anonimous_user_id");
		UnsetData("anonimous_user_session");
	}*/
}

//Анонимусы

function CreateAnonimousUser()
{
	$newid = GetNewId("anonimous_users", "id");
	$time = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	$name = "Аноним #".$newid;
	//
	$query = SQL("INSERT INTO `anonimous_users`(`id`, `ip`, `name`, `created`, `last_seen`, `status`) 
		VALUES ('$newid', '$ip', '$name', '$time', '0', '0')");
	//
	return $newid;
}

function UpdateAnonimousUser($ip)
{
	$time = time();
	$ua = $_SERVER['HTTP_USER_AGENT'];
	$query = SQL("UPDATE `anonimous_users` SET `last_seen`='$time', `user_agent`='$ua' WHERE ip='$ip'");
}

function isAnonimousExists($ip)
{
	$query = SQL("SELECT * FROM `anonimous_users` WHERE `ip`='$ip' LIMIT 1");
	$res = SQLFetchAssoc($query);
	return $res['id'] != "";
}

function GetAnonimousUserID($ip = "")
{
	if($ip == "")$ip = $_SERVER['REMOTE_ADDR'];
	$query = SQL("SELECT * FROM `anonimous_users` WHERE `ip`='$ip' LIMIT 1");
	$res = SQLFetchAssoc($query);
	//
	$id = $res['id'];
	if($id == "")$id = CreateAnonimousUser();
	//
	return $id;
}

function isAnonimousLogged()
{
	$ip = $_SERVER['REMOTE_ADDR'];
	
	if(isAnonimousExists($ip))
	{
		return true;
	}
	
	return false;
}

//Юзеры

function GetProfile($id)
{
	$engine_config = GetEngineConfig();
	//
	$data = array();
	
	if($id > 0)
	{
		$query = SQL("SELECT * FROM `profiles` WHERE `id` = '$id'");
		$row = SQLFetchAssoc($query);
		//
		
		if($row['id'] != "")
		{
			$avatar_obj = GetContent($row['avatar'], $engine_config['social']['default_image']);
			//
			$data['id'] = $id;
			$data['link'] = ($row['link'] != "")? $row['link'] : "id".$id;
			$data['email'] = $row['email'];
			$data['status'] = $row['status'];
			$data['reg_date'] = $row['reg_date'];
			$data['last_seen'] = $row['last_seen'];
			$data['visits'] = $row['visits'];
			$data['first_name'] = $row['first_name'];
			$data['last_name'] = $row['last_name'];
			$data['avatar'] = $avatar_obj['data'];
			$data['avatar_id'] = $row['avatar'];
			$data['about'] = $row['about'];
			$data['vk_id'] = $row['vk_id'];
			$data['user_agent'] = $row['user_agent'];
			$data['name'] = $row['first_name']." ".$row['last_name'];
			$data['ip'] = "";
		}
		else
		{
			$data['id'] = $id;
			$data['status'] = -2; //Не существует
		}
	}
	else
	{
		$id = -$id;
		$query = SQL("SELECT * FROM `anonimous_users` WHERE `id` = '$id'");
		$row = SQLFetchAssoc($query);
		//
		
		if($row['id'] != "")
		{
			$data['id'] = $id;
			$data['link'] = "";
			$data['email'] = "";
			$data['status'] = $row['status'];
			$data['reg_date'] = $row['created'];
			$data['last_seen'] = $row['last_seen'];
			$data['visits'] = 0;
			$data['first_name'] = $row['name'];
			$data['last_name'] = "";
			$data['avatar'] = "/modules/social/user_image.php?ip=".$row['ip'];
			$data['avatar_id'] = 0;
			$data['about'] = "";
			$data['vk_id'] = 0;
			$data['user_agent'] = $row['user_agent'];
			$data['name'] = $row['name'];
			$data['ip'] = $row['ip'];
		}
		else
		{
			$data['id'] = $id;
			$data['status'] = -2;
		}
	}
	
	return $data;
}

function Register($login, $password, $first_name, $last_name)
{
	$newid = GetNewId("profiles", "id");
	//
	$time = time();
	
	$is_valid = $login != "" && $password != "" && $first_name != "" && $last_name != ""; 
	
	if($is_valid)
	{
		$get_email = SQL("SELECT * FROM `profiles` WHERE `email` = '$login'");
		$row = SQLFetchAssoc($get_email);
		$existing_email = $row['email'];
		//
		$password_hash = _SHA256($password);
		//
		if($login != $existing_email)
		{
			$query = SQL("INSERT INTO `profiles`(`id`, `link`, `email`, `password`, `status`, `visits`, `reg_date`, `last_seen`, `first_name`, `last_name`, `avatar`, `about`, `vk_id`) 
				VALUES ('$newid', '', '$login', '$password_hash', '0', '0', '$time', '$time', '$first_name', '$last_name', '', '', '0')");
			//
			Login($login, $password);
			
			return $newid;
		}
		//
	}
	
	return 0;
}

function UpdateProfile($id, $login, $password, $first_name, $last_name, $about, $avatar)
{
	$is_valid = $login != "" && $first_name != "" && $last_name != "";
	$is_set_password = $password != "";
	
	//
	$first_name = FilterText($first_name, "name");
	$last_name = FilterText($last_name, "name");
	$about = FilterText($about, "user_text");
	//
	$get_email = SQL("SELECT * FROM `profiles` WHERE `id` != '$id' AND `email` = '$login'");
	$row = SQLFetchAssoc($get_email);
	$existing_email = $row['email'];
	//
	
	if($login != $existing_email)
	{
		if($is_valid)
		{
			$query = SQL("UPDATE `profiles` SET email='$login', first_name='$first_name', last_name='$last_name', `about`='$about', `avatar`='$avatar' WHERE id='$id'");
			
			if($is_set_password)
			{
				$password_hash = _SHA256($password);
				$query = SQL("UPDATE `profiles` SET password='$password_hash' WHERE id='$id'");
				//
				DirectAuthorize($id);
			}
			
			return 1;
		}
	}
	
	return 0;
}

//

function Login($login, $password)
{
	$is_valid = $login != "" && $password != "";
	
	$password_hash = _SHA256($password);
	
	if($is_valid)
	{
		return UserAuthorize($login, $password_hash);
	}
	
	return 0;
}

function UserAuthorize($login, $password_hash)
{
	$query = SQL("SELECT * FROM `profiles` WHERE `email` = '$login'");
	$row = SQLFetchAssoc($query);
	$id = $row['id'];
	$user_password = $row['password'];
	
	if($id != "") //Если пользователь существует
	{
		$user_session = UserSessionHash($id, $password_hash);
		//
		if($user_password == $password_hash) //Сравниваем введенный хеш и хеш в базе
		{
			SetData("user_id", $id);
			SetData("user_session", $user_session);
			
			SetVisists($id);
			return 1;
		}
	}
	
	return 0;
}

function DirectAuthorize($id) //Намеренная дыра в протокле безопасности
{
	$query = SQL("SELECT * FROM `profiles` WHERE `id` = '$id'");
	$row = SQLFetchAssoc($query);
	
	$login = $row['email'];
	$user_password = $row['password'];
	
	return UserAuthorize($login, $user_password);
}

function isLogged()
{
	global $engine_config;
	//
	$id = GetData("user_id", 0);
	$user_session = GetData("user_session", "");
	//
	$query = SQL("SELECT * FROM `profiles` WHERE `id` = '$id'");
	$row = SQLFetchAssoc($query);
	//
	$password_hash = $row['password'];
	//
	$true_user_session = UserSessionHash($id, $password_hash);
	$is_logged = ($true_user_session == $user_session);
	$engine_config['logged'] = $is_logged;
	//
	return $is_logged;
}

function Logout()
{
	UnsetData("user_id");
	UnsetData("user_session");
	
	return 1;
}

//

function SetVisists($id)
{
	$query = SQL("UPDATE `profiles` SET visits=(visits+1) WHERE id='$id'");
	
	return true;
}

function SetLastSeen($id)
{
	$time = time();
	$ua = $_SERVER['HTTP_USER_AGENT'];
	$query = SQL("UPDATE `profiles` SET `last_seen`='$time', `user_agent`='$ua' WHERE id='$id'");
	
	return true;
}

function GetOnline($last_seen, $text = "●")
{
	$border = time() - (5 * 60);
	if($last_seen > $border)return $text;
	return "";
}

//Посты

function CreatePost($user_id, $recipient_type, $recipient, $text, $attachments)
{
	$newid = GetNewId("posts", "id");
	//
	$time = time();
	
	$is_valid = ($text != "" || $attachments != "");
	
	if($is_valid)
	{
		$query = SQL("INSERT INTO `posts`(`id`, `owner`, `recipient`, `recipient_type`, `status`, `created`, `edited`, `deleted`, `restored`, `text`, `attachments`) 
		VALUES ('$newid', '$user_id', '$recipient', '$recipient_type', '0', '$time', '0', '0', '0', '$text', '$attachments')");
		
		return $newid;
	}
	
	return false;
}

function EditPost($id, $text, $attachments)
{
	$is_valid = ($text != "");
	
	if($is_valid)
	{
		$time = time();
		$query = SQL("UPDATE `posts` SET `text`='$text', `attachments`='$attachments', `edited`='$time' WHERE id='$id'");
		
		return true;
	}
	
	return false;
}

function DeletePost($id)
{
	$time = time();
	$query = SQL("UPDATE `posts` SET `status`='-1', `deleted`='$time' WHERE id='$id'");
	
	return true;
}

function RestorePost($id)
{
	$time = time();
	$query = SQL("UPDATE `posts` SET `status`='0', `restored`='$time' WHERE id='$id'");
	
	return true;
}

function GetPost($id, $row = 0)
{
	if($row == 0)
	{
		$query = SQL("SELECT * FROM `posts` WHERE `id` = '$id'");
		$row = SQLFetchAssoc($query);
	}
	//
	$data = array();
	
	if($row['id'] != "")
	{
		$data['id'] = $id;
		$data['owner'] = $row['owner'];
		$data['recipient'] = $row['recipient'];
		$data['recipient_type'] = $row['recipient_type'];
		$data['status'] = $row['status'];
		$data['created'] = $row['created'];
		$data['edited'] = $row['edited'];
		$data['deleted'] = $row['deleted'];
		$data['restored'] = $row['restored'];
		$data['text'] = $row['text'];
		$data['attachments'] = $row['attachments'];
		$data['link'] = "post".$id;
	}
	else
	{
		$data['id'] = $id;
		$data['status'] = -2; //Не существует
	}
	
	return $data;
}

//Топики

function CreateTopic($owner, $title, $text)
{
	$user_id = $owner;
	//
	$time = time();
	
	$is_valid = $title != "";
	
	if($is_valid)
	{
		$newid = GetNewId("topics", "id");
		
		$query = SQL("INSERT INTO `topics`(`id`, `owner`, `status`, `created`, `edited`, `deleted`, `restored`, `title`, `pinned`, `last_writer`) 
			VALUES ('$newid', '$user_id', '0', '$time', '$time', '0', '0', '$title', '0', '$user_id')");
		
		if($text != "")
		{
			CreatePost($owner, "board", -$newid, $text, "");
		}
		
		return $newid;
	}
	
	return false;
}

function EditTopic($id, $title)
{
	$is_valid = ($title != "");
	
	if($is_valid)
	{
		$time = time();
		$query = SQL("UPDATE `topics` SET `title`='$title', `edited`='$time' WHERE id='$id'");
		
		return true;
	}
	
	return false;
}

function UpdateTopic($id, $last_writer)
{
	$last_write_time = time();
	$query = SQL("UPDATE `topics` SET `last_writer`='$last_writer', `edited`='$last_write_time' WHERE id='$id'");
}

function DeleteTopic($id)
{
	$time = time();
	$query = SQL("UPDATE `topics` SET `status`='-1', `deleted`='$time' WHERE id='$id'");
	
	return true;
}

function RestoreTopic($id) // Не используется
{
	$time = time();
	$query = SQL("UPDATE `topics` SET `status`='0', `restored`='$time' WHERE id='$id'");
	
	return true;
}

function PinTopic($id)
{
	$time = time();
	$query = SQL("UPDATE `topics` SET `pinned`='1' WHERE id='$id'");
	
	return true;
}

function UnpinTopic($id)
{
	$time = time();
	$query = SQL("UPDATE `topics` SET `pinned`='0' WHERE id='$id'");
	
	return true;
}

function CloseTopic($id)
{
	$time = time();
	$query = SQL("UPDATE `topics` SET `closed`='1' WHERE id='$id'");
	
	return true;
}

function OpenTopic($id)
{
	$time = time();
	$query = SQL("UPDATE `topics` SET `closed`='0' WHERE id='$id'");
	
	return true;
}

function AddTopicRating($id, $num = 1)
{
	$time = time();
	$query = SQL("UPDATE `topics` SET `rating`=(rating + $num) WHERE id='$id'");
	
	return true;
}

function GetTopic($id, $row = 0)
{
	if($row == 0)
	{
		$query = SQL("SELECT * FROM `topics` WHERE `id` = '$id' AND `status` != '-1'");
		$row = SQLFetchAssoc($query);
	}
	//
	$data = array();
	
	if($row['id'] != "")
	{
		$data['id'] = $id;
		$data['owner'] = $row['owner'];
		$data['status'] = $row['status'];
		$data['created'] = $row['created'];
		$data['edited'] = $row['edited'];
		$data['deleted'] = $row['deleted'];
		$data['restored'] = $row['restored'];
		$data['title'] = $row['title'];
		$data['pinned'] = $row['pinned'];
		$data['closed'] = $row['closed'];
		$data['rating'] = $row['rating'];
		$data['last_writer'] = $row['last_writer'];
	}
	else
	{
		$data = array();
	}
	
	return $data;
}

//Контент

function CreateContent($owner, $data, $type = "image")
{
	$time = time();
	
	$newid = GetNewId("content", "id");
	
	$query = SQL("INSERT INTO `content`(`id`, `owner`, `status`, `created`, `deleted`, `restored`, `type`, `data`) 
	VALUES ('$newid', '$owner', '0', '$time', '0', '0', '$type', '$data')");
	
	return $newid;
}

function DeleteContent($id)
{
	$time = time();
	$query = SQL("UPDATE `content` SET `status`='-1', `deleted`='$time' WHERE id='$id'");
	
	return true;
}

function RestoreContent($id)
{
	$time = time();
	$query = SQL("UPDATE `content` SET `status`='0', `restored`='$time' WHERE id='$id'");
	
	return true;
}

function GetContent($id, $alt = "", $row = 0)
{
	$engine_config = GetEngineConfig();
	
	if($row == 0)
	{
		$query = SQL("SELECT * FROM `content` WHERE `id` = '$id'");
		$row = SQLFetchAssoc($query);
	}
	
	//
	/*if($row['type'] == "image")
	{
		$url_status = UrlOK($row['data']);
		
		if(!$url_status || !file_exists($row['data']))
		{
			$data['data'] = $engine_config['social']['default_image'];
		}
	}*/
	//
	$data = array();
	
	if($row['id'] != "")
	{
		$data['id'] = $row['id'];
		$data['owner'] = $row['owner'];
		$data['status'] = $row['status'];
		$data['created'] = $row['created'];
		$data['deleted'] = $row['deleted'];
		$data['restored'] = $row['restored'];
		$data['type'] = $row['type'];
		$data['data'] = ($data['status'] != -1)? $row['data'] : $alt;
	}
	else
	{
		$data['id'] = $id;
		$data['status'] = -2;
		$data['data'] = $alt;
	}
	
	return $data;
}

function GetContentList($owner = 0, $type = "all")
{
	$by_type = "";
	if($type != "all")$by_type = "AND `type`='$type'";
	//
	$objs = array();
	
	$query = SQL("SELECT `id` FROM `content` WHERE `owner`='$owner' ".$by_type." ORDER BY `created` DESC LIMIT 0, 1000");
	$inc = 0;

	while($row = SQLFetchAssoc($query))
	{
		$objs[$inc] = GetContent($row['id']);
		$inc++;
	}
	
	return $objs;
}

//Нотификации

function PushNotification($user_id, $recipient, $message, $link, $time = 0){
	if($time <= time())$time = time(); //Чтобы делать отложенные уведомления, но не слать их в прошлое в случае серверной ошибки
	//
	
	$newid = GetNewId("notifications", "id");
	
	$query = SQL("INSERT INTO `notifications`(`id`, `owner`, `recipient`, `created`, `checked`, `message`, `link`, `status`) 
	VALUES ('$newid', '$user_id', '$recipient', '$time', '0', '$message', '$link', '0')");
	
	return $newid;
}

//PushNotification(1, "Ля-ля-ля", "/id1");

function GetNotification($id, $row = 0){
	if($row == 0)
	{
		$query = SQL("SELECT * FROM `notifications` WHERE `id` = '$id'");
		$row = SQLFetchAssoc($query);
	}
	//
	
	//
	$data = array();
	
	if($row['id'] != "")
	{
		$data['id'] = $row['id'];
		$data['owner'] = $row['owner'];
		$data['recipient'] = $row['recipient'];
		$data['created'] = $row['created'];
		$data['checked'] = $row['checked'];
		$data['message'] = FilterText($row['message'], "user_text");//$row['message'];
		$data['link'] = $row['link'];
		$data['status'] = $row['status'];
		$data['owner_profile'] = GetProfile($row['owner']);
	}
	else
	{
		$data['id'] = $id;
		$data['status'] = -2;
	}
	
	return $data;
}

function GetNotifications($user_id, $no_checked = true, $count = 10000){
	$checked = "";
	if($no_checked)$checked = "AND `checked`='0'";
	
	$query = SQL("SELECT * FROM `notifications` WHERE `recipient` = '$user_id' AND `status` = '0' ".$checked." ORDER BY `notifications`.`created` DESC LIMIT 0, ".$count);
	
	$notifications = array();
	$inc = 0;
	while($row = SQLFetchAssoc($query))
	{
		$notifications[$inc] = GetNotification($row['id'], $row);
		$inc++;
	}
	
	return $notifications;
}

function CheckNotification($id)
{
	$query = SQL("UPDATE `notifications` SET `checked`='1' WHERE id='$id'");
	return true;
}

function GetOnlineUsers()
{
	$time_border = time() - (5 * 60);
	
	$result = array();
	
	$query = SQL("SELECT * FROM `profiles` WHERE `last_seen` > $time_border");
	
	while($row = SQLFetchAssoc($query))
	{
		$result[sizeof($result)] = GetProfile($row['id']);
	}
	
	$query = SQL("SELECT * FROM `anonimous_users` WHERE `last_seen` > $time_border");
	
	while($row = SQLFetchAssoc($query))
	{
		$row['id'] *= -1;
		$result[sizeof($result)] = GetProfile($row['id']);
	}
	
	return $result;
}

?>
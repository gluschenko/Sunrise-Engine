<?php
//Alexander Gluschenko (26-09-2015)

AddMethod("social.account.register", function($params){ //Регистрация
	$login = Security::String($params['login']);
	$password = Security::String($params['password']);
	$first_name = Security::String($params['first_name']);
	$last_name = Security::String($params['last_name']);
	//
	return Register($login, $password, $first_name, $last_name);
});

AddMethod("social.account.update", function($params){ //Настройки
	$engine_cfg = GetEngineConfig();
	if($engine_cfg['logged'])
	{
		$uid = GetData("user_id", 0);
		
		$login = Security::String($params['login']);
		$password = Security::String($params['password']);
		$first_name = Security::String($params['first_name']);
		$last_name = Security::String($params['last_name']);
		$about = Security::String($params['about']);
		$avatar = Security::String($params['avatar']);
		
		return UpdateProfile($uid, $login, $password, $first_name, $last_name, $about, $avatar);
	}
	//
	return 0;
});

AddMethod("social.account.login", function($params){ //Вход
	$login = Security::String($params['login']);
	$password = Security::String($params['password']);
	//
	return Login($login, $password);
});

AddMethod("social.account.logout", function($params){ //Выход
	return Logout();
});

AddMethod("social.account.poll", function($params){ //Реал-тайм метод
	$engine_cfg = GetEngineConfig();
	
	$response = array(
		"online" => 0,
		"notifications" => array(),
	);
	
	if($engine_cfg['logged'])
	{
		$id = GetData("user_id", 0);
		
		$response["online"] = SetLastSeen($id) ? 1 : 0;
		$response["notifications"] = GetNotifications($id, true, 200);
	}
	
	return $response;
});

AddMethod("social.notifications.get", function($params){
	$engine_cfg = GetEngineConfig();
	
	$count = Security::String($params['count']);
	
	$response = array(
		"notifications_list" => "",
	);
	
	if($engine_cfg['logged'])
	{
		$id = GetData("user_id", 0);
		
		$all_nots = GetNotifications($id, false, $count);
		
		if(sizeof($all_nots) > 0)
		{
			for($i = 0; $i < sizeof($all_nots); $i++)
			{
				$response["notifications_list"] .= GetNotificationMarkup($all_nots[$i]);
				if($i < sizeof($all_nots) - 1)$response["notifications_list"] .= "<div class='divider'></div>";
			}
		}
		else
		{
			$response["notifications_list"] = "
			<div style='height: 50px;'></div>
			<div class='text inner_center'>Нет новых уведомлений</div>
			<div style='height: 50px;'></div>
			";
		}
	}
	
	return $response;
});

AddMethod("social.notifications.check", function($params){
	$engine_cfg = GetEngineConfig();
	
	$id = Security::String($params['id']);
	$sig = Security::String($params['sig']);
	
	if($engine_cfg['logged'])
	{
		$uid = GetData("user_id", 0);
		
		if(TrueSig($sig, $id, $uid))
		{
			CheckNotification($id);
			return 1;
		}
	}
	
	return 0;
});

//

AddMethod("social.users.online.get", function($params){
	$engine_cfg = GetEngineConfig();
	
	$result = array("users" => GetOnlineUsers(), "markup" => array());
	
	for($i = 0; $i < sizeof($result['users']); $i++)
	{
		$profile_data = $result['users'][$i];
		
		$is_bot = isUserBot($profile_data['user_agent']);
		
		$result["markup"][$i] = "
		<a href='/".$profile_data['link']."' target='_blank' title='".$profile_data['ip']." / ".$profile_data['user_agent']."' class='back3' style='text-align: left; display: inline-block; margin: 5px; padding: 5px; width: 250px;'>
			<div class='profile_avatar small_profile_avatar' style='vertical-align: middle; display: inline-block; background-image: url(".$profile_data['avatar'].");'></div>
			<div class='text bold' style='vertical-align: middle; display: inline-block;'>
				".$profile_data['name']." <span class='fore2'>".(!$is_bot ? GetOnline($profile_data['last_seen'], "●") : "бот")."</span>
				<div class='mini_text'>".GetOS($profile_data['user_agent'])." ● ".GetBrowser($profile_data['user_agent'])."</div>
			</div>
		</a>
		";
	}
	
	return $result;
});

/*AddMethod("social.account.online", function($params){
	$engine_cfg = GetEngineConfig();
	
	if($engine_cfg['logged'])
	{
		$id = GetData("user_id", 0);
		if(SetLastSeen($id))
		{
			return 1;
		}
	}
	
	return 0;
});*/


//

AddMethod("social.post", function($params){
	
	$logged = isLogged() || isAnonimousLogged();
	
	$uid = 0;
	if(isLogged())$uid = GetData("user_id", 0);
	else $uid = -GetAnonimousUserID();
	
	if($logged && $uid != 0)
	{
		$recipient_type = Security::String($params['recipient_type']);
		$recipient = Security::String($params['recipient']);
		$text = Security::String(FilterText($params['text'], "database_text"));
		$attachments = $params['attachments'];
		$sig = Security::String($params['sig']);
		
		$markup = Security::String(isset($params['markup'])? $params['markup'] : false);
		
		if(TrueSig($sig, $uid, $recipient_type, $recipient))
		{
			$post_id = CreatePost($uid, $recipient_type, $recipient, $text, $attachments);
			
			if($post_id != 0)
			{
				if(true /*$recipient_type != "board"*/)
				{
					$not_text = $text; //CropText($text, 35);
					$not_link = "";
					$not_recipient = 0;
					$can_send = true;
					
					if($recipient_type == "profile")
					{
						$not_link = "/post".$post_id;
						$not_recipient = $recipient;
						$can_send = ($uid != $recipient);
					}
					
					if($recipient_type == "post")
					{
						$not_post = GetPost($recipient);
						
						if(isset($not_post['owner']))
						{
							$not_link = "/post".$recipient;
							$not_recipient = $not_post['owner'];
							$can_send = ($uid != $not_post['owner']);
						}
						else
						{
							$can_send = false;
						}
					}
					
					if($recipient_type == "board")
					{
						$not_topic = GetTopic(-$recipient);
						
						if(isset($not_topic['owner']))
						{
							$not_link = "/topic".(-$recipient);
							$not_recipient = $not_topic['owner'];
							$can_send = ($uid != $not_topic['owner']);
						}
						else
						{
							$can_send = false;
						}
						
						UpdateTopic(-$recipient, $uid);
					}
					
					if($can_send)PushNotification($uid, $not_recipient, $not_text, $not_link);
				}
			}
			
			if(!$markup)
			{
				return array("post_id" => $post_id);
			}
			else
			{
				if($post_id != 0)
				{
					$post = GetPost($post_id);
					return array("post_id" => $post_id, "markup" => GetPostMarkup($post));
				}
				
				return 0;
			}
		}
	}
	
	return 0;
});

AddMethod("social.posts.edit", function($params){
	$admin_logged = isAdminLogged();
	if(isLogged() || $admin_logged)
	{
		$user_id = GetData("user_id", 0);
		
		$id = Security::String($params['id']);
		$text = Security::String(FilterText($params['text'], "database_text"));
		$attachments = Security::String($params['attachments']);
		$sig = Security::String($params['sig']);
		
		$markup = isset($params['markup'])? $params['markup'] : false;
		
		if(TrueSig($sig, $user_id, $id) || $admin_logged)
		{
			$state = EditPost($id, $text, $attachments);
			
			if($state)
			{
				$post = GetPost($id);
				return array("state" => $state, "markup" => GetPostText($post));
			}
		}
	}
	
	return 0;
});

AddMethod("social.posts.delete", function($params){
	$admin_logged = isAdminLogged();
	if(isLogged() || $admin_logged)
	{
		$user_id = GetData("user_id", 0);
		
		$id = Security::String($params['id']);
		$sig = Security::String($params['sig']);
		
		if(TrueSig($sig, $user_id, $id) || $admin_logged)
		{
			return DeletePost($id);
		}
	}
	
	return 0;
});

AddMethod("social.posts.restore", function($params){
	$admin_logged = isAdminLogged();
	if(isLogged() || $admin_logged)
	{
		$user_id = GetData("user_id", 0);
		
		$id = Security::String($params['id']);
		$sig = Security::String($params['sig']);
		
		if(TrueSig($sig, $user_id, $id) || $admin_logged)
		{
			return RestorePost($id);
		}
	}
	
	return 0;
});

//

AddMethod("social.photos.get", function($params){
	if(isLogged())
	{
		$user_id = GetData("user_id", 0);
		
		return GetContentList($user_id, "image");
	}
	
	return 0;
});

AddMethod("social.posts.get", function($params){
	$topic_id = Security::Int(-$params['topic_id']);
	$offset = Security::Int($params['offset']);
	
	$query = SQL("SELECT * FROM `posts` WHERE `recipient`='$topic_id' AND `recipient_type`='board' AND `status`='0' AND `id` > '$offset' ORDER BY `id` ASC");
	
	$posts = array();
	while($row = SQLFetchAssoc($query))
	{
		$posts[] = GetPost($row['id'], $row);
	}
	
	for($i = 0; $i < sizeof($posts); $i++)
	{
		$posts[$i]['markup'] = GetPostMarkup($posts[$i]);
	}
	
	return array("posts" => $posts);
});

AddMethod("social.posts.chat.get", function($params){
	$topic_id = Security::Int(-$params['topic_id']);
	$wrapper = Security::String($params['wrapper']);
	
	$markup = "
	<div id='".$wrapper."_scroll' style='height: 400px; overflow-y: auto; resize: vertical;'>
		<div id='".$wrapper."_messages'></div>
		<div id='".$wrapper."_sent_messages'></div>
	</div>
	".PostForm("board", $topic_id, $wrapper."_sent_messages", "WriteEnd")."
	";
	
	return array("markup" => $markup);
});

//

AddMethod("social.topics.create", function($params){
	$title = Security::String(FilterText($params['title'], "database_text"));
	$text = Security::String(FilterText($params['text'], "database_text"));
	
	$is_valid_title = $title != "";
	
	if($is_valid_title)
	{
		if(isLogged())
		{
			$user_id = GetData("user_id", 0);
			return CreateTopic($user_id, $title, $text);
		}
		elseif(isAnonimousLogged())
		{
			$user_id = GetAnonimousUserID(); //GetData("anonimous_user_id", 0);
			return CreateTopic(-$user_id, $title, $text);
		}
	}
	
	return false;
});

AddMethod("social.topics.delete", function($params){
	
	$id = Security::String($params['id']);
	
	if(isLogged())
	{
		$user_id = GetData("user_id", 0);
		
		$topic = GetTopic($id);
		
		if($topic['owner'] == $user_id)
		{
			return DeleteTopic($id);
		}
	}
	elseif(isAdminLogged())
	{
		return DeleteTopic($id);
	}
	
	return false;
});

AddMethod("social.topics.edit", function($params){
	
	$is_admin_logged = isAdminLogged();
	if(isLogged() || $is_admin_logged)
	{
		$user_id = GetData("user_id", 0);
		$id = Security::String($params['id']);
		$title = Security::String(FilterText($params['title'], "database_text"));
		
		$topic = GetTopic($id);
		
		if($topic['owner'] == $user_id || $is_admin_logged)
		{
			return EditTopic($id, $title);
		}
	}
	
	return false;
});

AddMethod("social.topics.pin", function($params){
	if(isAdminLogged())
	{
		$id = Security::String($params['id']);
		return PinTopic($id);
	}
	
	return false;
});

AddMethod("social.topics.unpin", function($params){
	if(isAdminLogged())
	{
		$id = Security::String($params['id']);
		return UnpinTopic($id);
	}
	
	return false;
});

AddMethod("social.topics.close", function($params){
	$is_admin_logged = isAdminLogged();
	if(isLogged() || $is_admin_logged)
	{
		$user_id = GetData("user_id", 0);
		$id = Security::String($params['id']);
		$topic = GetTopic($id);
		
		if($topic['owner'] == $user_id || $is_admin_logged)
		{
			return CloseTopic($id);
		}
	}
	
	return false;
});

AddMethod("social.topics.open", function($params){
	$is_admin_logged = isAdminLogged();
	if(isLogged() || $is_admin_logged)
	{
		$user_id = GetData("user_id", 0);
		$id = Security::String($params['id']);
		$topic = GetTopic($id);
		
		if($topic['owner'] == $user_id || $is_admin_logged)
		{
			return OpenTopic($id);
		}
	}
	
	return false;
});

?>
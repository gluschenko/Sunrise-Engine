<?php

$sidebar_links = "
<a class='menu_button' href='/' onclick='return NavAsync(this.href, true);'>Главная сайта</a>
<a class='menu_button' href='/board' onclick='return NavAsync(this.href, true);'>Обсуждения</a>
<a class='menu_button' href='/posts' onclick='return NavAsync(this.href, true);'>Недавние записи</a>
<div class='space'></div>
<div class='divider'></div>
<div class='space'></div>
<a class='menu_button' href='/login' onclick='return NavAsync(this.href, true);'>Войти</a>
<a class='menu_button' href='/register' onclick='return NavAsync(this.href, true);'>Регистрация</a>
";

global $engine_config;
if($engine_config['logged'])
{
	$user_id = GetData("user_id", 0);
	
	$sidebar_links = "
	<a class='menu_button' href='/id".$user_id."' onclick='return NavAsync(this.href, true);'>Моя страница</a>
	<a class='menu_button' href='/people' onclick='return NavAsync(this.href, true);'>Пользователи</a>
	<a class='menu_button' href='/board' onclick='return NavAsync(this.href, true);'>Обсуждения</a>
	<a class='menu_button' href='/posts' onclick='return NavAsync(this.href, true);'>Недавние записи</a>
	<a class='menu_button' href='/photos' onclick='return NavAsync(this.href, true);'>Фотографии</a>
	<a class='menu_button' href='/settings' onclick='return NavAsync(this.href, true);'>Настройки</a>
	
	<div class='menu_button' style='position: relative;' onclick='ShowNotifications();'>
		Уведомления
		<div id='notifications_counter' class='notify_counter n_back4'></div>
		<div id='notifications_box' class='box notify_box' style='display: none;'>
			<div class='padding'>
				<div id='notifications_list' style='max-height: 450px; overflow-y: auto;'>...</div>
			</div>
		</div>
	</div>
	
	<div class='space'></div>
	<div class='divider'></div>
	<div class='space'></div>
	<a class='menu_button' href='/' onclick='return NavAsync(this.href, true);'>Главная сайта</a>
	<a class='menu_button' href='#' onclick='SocialLogout();'>Выйти</a>
	
	<script>
	function SocialLogout()
	{
		ShowLoader();
		
		ApiMethod('social.account.logout', {}, function(data){ 
			ShowLoader();
		
			if(data.response != null)
			{
				if(data.response != 0)
				{
					AJAXLayout.Reload();
				}
			}
			
			HideLoader();
		});
	}
	</script>
	";
}

AddMarkup("social_sidebar", "
<div class='box social_sidebar' style='width: 250px;'>
	<div class='space_8'></div>
	<div id='social_sidebar_links'>
		".$sidebar_links."
	</div>
	<div class='space_8'></div>
</div>
<div class='pic_button back4 social_sidebar_button' onclick='ShowSocialSidebarWindow();'></div>
");

AddMarkup("upload_window", "
<noscript id='upload_form'>
	<div id='upload_form_main'>
		<form id='form'>
			<input class='text_input' type='file' name='file' id='file'/>
		</form>
		
		<div class='big_space'></div>
		
		<div class='button back4' style='margin: auto;' onclick='Upload();'>Загрузить</div>
		
		<div class='big_space'></div>
	</div>
	
	<div id='upload_form_loading' style='display: none;'>
		<div style='height: 90px;'></div>
		<div class='loader'></div>
		<div style='height: 40px;'></div>
		<div class='logo_text' style='text-align: center;'>Загрузка</div>
		<div class='big_space'></div>
	</div>
	
	<div id='upload_form_file' style='display: none;'>
		<div class='big_space'></div>
		<div id='uploaded_file' class='title_text' style='text-align: center;'>...</div>
		<div class='big_space'></div>
	</div>
	
	<div id='upload_form_error' style='display: none;'>
		<div class='big_space'></div>
		<div id='upload_error' class='title_text' style='text-align: center;'>Ошибка загрузки</div>
		<div class='big_space'></div>
	</div>
</noscript>
");

//

function PostForm($recipient_type, $recipient_id, $posts_block, $write_function = "WriteForward")
{
	//$uid = GetData("user_id", 0);
	$uid = 0;
	if(isLogged())$uid = GetData("user_id", 0);
	else $uid = -GetAnonimousUserID();
	
	$sig = Sig($uid, $recipient_type, $recipient_id);
	
	$m = "";
	
	$text_block = "form_text_".$sig;
	$atts_block = "form_attachments_".$sig;
	
	//Мда
	$oncreate_script = "function(markup){
		Find(\"".$text_block."\").value = \"\";
		Find(\"".$atts_block."\").setAttribute(\"data-attachments\", \"\");
		Clear(\"preview_".$atts_block."\");
		".$write_function."(\"".$posts_block."\", markup);
	}";
	
	$attachment_node = "
	".Markup("upload_window")."
	<script>
	function InitCallBacks_".$sig."(){
		_ChooseWindowCallback = _UploadWindowCallback = function(content_obj){
			var Attachments = Find(\"".$atts_block."\").getAttribute(\"data-attachments\");
			Find(\"".$atts_block."\").setAttribute(\"data-attachments\", AddAttachment(Attachments, new Attachment(\"image\", content_obj.id)));
			Find(\"preview_".$atts_block."\").innerHTML += \"<img class='content_image' alt='' src='\" + content_obj.data + \"'/>\";
		};
	}
	</script>
	";
	
	$m .= "
	<div class='padding' style='position: relative;'>
		".$attachment_node."
		<textarea id='".$text_block."' class='text_input n_back3' placeholder='Текст сообщения' maxlength='20480' style='display: block; width: 98%; height: 60px;'></textarea>
		<div id='preview_".$atts_block."'></div>
		<div id='".$atts_block."' data-attachments=''></div>
		<div class='space'></div>
		<div onclick='CreatePost(\"".$recipient_type."\", \"".$recipient_id."\", Find(\"".$text_block."\").value, Find(\"".$atts_block."\").getAttribute(\"data-attachments\"), \"".$sig."\", ".$oncreate_script.");' class='button back4' style='width: 150px; margin: auto;'>Отправить</div>
		<div class='button radial_button attach_btn back3' title='Прикрепить фото' style='bottom: 10px; right: 10px;' onclick='InitCallBacks_".$sig."(); SlideToggle(\"atts_panel_".$sig."\");'>
			<div style='position: relative;'>
				<div id='atts_panel_".$sig."' class='box border4' style='display: none; position: absolute; top: 50px; left: -53px; z-index: 100;'>
					<div class='padding'>
						<div class='button back4' style='margin: auto; width: 120px;' onclick='ShowUploadDialog();'>Загрузить</div>
						<div class='space'></div>
						<div class='button back4' style='margin: auto; width: 120px;' onclick='ShowChoosePhotoDialog();'>Выбрать</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	";
	
	return $m;
}

function GetPostMarkup($row)
{
	$engine_cfg = GetEngineConfig();
	//
	$profile_data = GetProfile($row['owner']);
	
	$post_block = "post".$row['id']."_wrap";
	$type = $row['recipient_type'];
	//
	$uid = GetData("user_id", 0);
	
	$delete_sig = "";
	$edit_sig = "";
	//
	$buttons_markup = "";
	if(true /*$engine_cfg['logged']*/)
	{
		$delete_sig = Sig($uid, $row['id']);
		$edit_sig = Sig($uid, $row['id']);
		
		$can_delete = false;
		$can_edit = false;
		
		if($row['recipient_type'] == "profile")
		{
			//$can_do = $action_sig == Sig($row['owner'], $row['id']) || $action_sig == Sig($row['recipient'], $row['id']);
			
			$can_delete = $delete_sig == Sig($row['owner'], $row['id']) || $delete_sig == Sig($row['recipient'], $row['id']);
			$can_edit = $edit_sig == Sig($row['owner'], $row['id']);
		}
		
		if($row['recipient_type'] == "post")
		{
			$rec_post = GetPost($row['recipient']);
			
			//$can_do = $action_sig == Sig($row['owner'], $row['id']) || $action_sig == Sig($rec_post['recipient'], $row['id']);
			
			$can_delete = $delete_sig == Sig($row['owner'], $row['id']) || $delete_sig == Sig($rec_post['recipient'], $row['id']);
			$can_edit = $edit_sig == Sig($row['owner'], $row['id']);
		}
		
		if($row['recipient_type'] == "board")
		{
			//$can_do = $action_sig == Sig($row['owner'], $row['id']);
			
			$can_delete = $delete_sig == Sig($row['owner'], $row['id']);
			$can_edit = $edit_sig == Sig($row['owner'], $row['id']);
		}
		
		//
		
		$is_admin_logged = isAdminLogged();
		if($can_edit || $is_admin_logged)$buttons_markup .= "<div class='button quad_button edit_btn back3' style='top: 5px; right: 35px;' onclick='EditBox(".$row['id'].", \"".$edit_sig."\");'></div>";
		if($can_delete || $is_admin_logged)$buttons_markup .= "<div class='button quad_button delete_btn back3' style='top: 5px; right: 5px;' onclick='DeletePost(".$row['id'].", \"".$delete_sig."\", \"".$post_block."\");'></div>";
	}
	//
	$bottom_markup = "";
	if($type == "profile" || $type == "board")
	{
		$posts = array();
		
		$query = SQL("SELECT * FROM `posts` WHERE `recipient`='".$row['id']."' AND `recipient_type`='post' AND `status`='0' ORDER BY `created` ASC LIMIT 0, 1000");
		
		while($crow = SQLFetchAssoc($query))
		{
			$posts[sizeof($posts)] = GetPost($crow['id'], $crow);
		}

		$comm_form_block = "commnets_form".$row['id'];
		
		$comment_form_markup = "
		<div style='padding: 30px;'>
			<div class='title_text'>Авторизуйтесь, чтобы оставлять комментарии</div>
		</div>
		";
		
		if(true /*$engine_cfg['logged']*/)
		{
			$comment_form_markup = PostForm("post", $row['id'], "async_comments_".$row['id'], "WriteEnd");
		}
		
		$bottom_markup = "
		<div class='small_text'>
			<a class='small_text pointer' href='/post".$row['id']."' onclick='return NavAsync(this.href, true);'>".GetTime($row['created'])."</a> | <a class='link' onclick='SlideToggle(\"".$comm_form_block."\"); ScrollToId(\"".$comm_form_block."\", 70);'>комментировать</a>
		</div>
		<div class='space'></div>
		".GetPostsMarkup($posts)."
		<div id='async_comments_".$row['id']."'></div>
		
		<div id='commnets_form".$row['id']."' style='display: none;'>
			<div class='space'></div>
			<div class='divider'></div>
			<div class='space'></div>
			".$comment_form_markup."
		</div>
		";
	}
	
	if($type == "post" || $type == "comment")
	{
		$bottom_markup = "
		<div class='small_text'>".GetTime($row['created'])."</div>
		<div class='space'></div>
		";
	}
	//
	$m = "";
	
	$m .= "
	<div id='".$post_block."'>
		<div class='divider'></div>
		<div>
			<table style='width: 100%;'>
				<tr>
					<td class='padding'>
						<a href='/".$profile_data['link']."' onclick='return NavAsync(this.href, true);'>
							<div class='profile_avatar small_profile_avatar' style='background-image: url(".$profile_data['avatar'].");'></div>
						</a>
					</td>
					<td style='width: 100%; position: relative;'>
						<div class='space'></div>
						<a href='/".$profile_data['link']."' onclick='return NavAsync(this.href, true);'>
							<div class='text bold' style='cursor: pointer; display: inline-block; line-wrap: nowrap;'>".$profile_data['name']." <span class='fore2'>".GetOnline($profile_data['last_seen'], "●")."</span></div>
						</a>
						".$buttons_markup."
						
						<div class='space'></div>
						<div id='text_".$row['id']."' class='text' style='width: 100%; word-break: break-word;'>".GetPostText($row)."</div>
						<div class='space'></div>
						<div id='".$post_block."_bottom'>
							".$bottom_markup."
						</div>
					</td>
				</tr>
			</table>
		</div>
		
		<div id='edit_text_".$row['id']."' style='display: none;'>".$row['text']."</div>
		<div id='edit_attachments_".$row['id']."' style='display: none;'>".ToJSON(FromJSON($row['attachments']))."</div>
	</div>
	<div id='deleted_".$post_block."' style='display: none;'>
		<div class='divider'></div>
		<div style='padding: 30px;'>
			<div class='title_text'>Удалено. <span class='link' onclick='RestorePost(".$row['id'].", \"".$delete_sig."\", \"".$post_block."\");'>Отмена</span></div>
		</div>
	</div>
	";
	
	return $m;
}

function GetPostsMarkup($posts)
{
	$m = "";
	
	for($i = 0; $i < sizeof($posts); $i++)
	{
		$m .= GetPostMarkup($posts[$i]);
	}
	
	return $m;
}

function GetPostText($row)
{
	$post_text = FilterText($row['text'], "user_text");
	$post_text .= GetAttachmentsMarkup($row['attachments']);
	
	return $post_text;
}

//


function GetTopicMarkup($row)
{
	$engine_cfg = GetEngineConfig();
	//
	$profile_data = GetProfile($row['last_writer'] != 0 ? $row['last_writer'] : $row['owner']);
	//
	$pin_flag = "";
	$closed_flag = "";
	if($row['pinned'] == 1)$pin_flag = "<div class='board_flag pinned_flag' title='Тема закреплена'></div>";
	if($row['closed'] == 1)$closed_flag = "<div class='board_flag locked_flag' title='Тема закрыта'></div>";
	//
	$m = "";
	
	$m .= "
	<div class='divider'></div>
	<div class='padding back3 pointer' style='position: relative;'>
		<a id='topic_".$row['id']."' href='/topic".$row['id']."' onclick='return NavAsync(this.href, true);' class='text bold pointer' style='display: block; padding: 8px; padding-top: 18px; padding-bottom: 18px;'>
			".CropText($row['title'], 50)."
		</a>
		
		<div style='position: absolute; right: 10px; bottom: 10px;'>
			<a href='/".$profile_data['link']."' class='small_text' style='cursor: pointer; line-wrap: nowrap;' onclick='return NavAsync(this.href, true);'>
				".$profile_data['name']." <span class='fore2'>".GetOnline($profile_data['last_seen'], "●")."</span>
				<span>| ".GetTime($row['edited'] != 0 ? $row['edited'] : $row['created'])."</span>
			</a>
		</div>
		
		<div style='position: absolute; right: 5px; top: 5px;'>
			".$pin_flag."
			".$closed_flag."
		</div>
	</div>
	";
	
	return $m;
}

function GetTopicsMarkup($topics)
{
	$m = "";
	
	for($i = 0; $i < sizeof($topics); $i++)
	{
		$m .= GetTopicMarkup($topics[$i]);
	}
	
	return $m;
}

//

function GetAttachmentsMarkup($atts)
{
	$atts_arr = array();
	
	try{
		$atts_arr = FromJSON(substr($atts, 1, -1));
	}
	catch(Exception $e){
		
	}
	
	$m = "";
	$m .= "<div>";
	
	if(is_array($atts_arr))
	{
		for($i = 0; $i < sizeof($atts_arr); $i++)
		{
			if(!isset($atts_arr[$i]['type']) && !isset($atts_arr[$i]['data']))continue;
			$type = $atts_arr[$i]['type'];
			$data = $atts_arr[$i]['data'];
			//
			if($type == "image")
			{
				$content_obj = GetContent($data);
				$m .= "<img class='content_image large_content_image' src='".$content_obj['data']."' alt='' onclick='ImageBox(this.src);'/>";
			}
		}
	}
	
	$m .= "</div>";
	
	return $m;
}

function GetPhotosMarkup()
{
	
}

//

function GetNotificationMarkup($row)
{
	$check_sig = Sig($row['id'], $row['recipient']);
	$unchecked_m = ($row['checked'] == 0)? "class='unchecked' onclick='CheckNotification(\"".$row['id']."\", \"".$check_sig."\", \"notification_".$row['id']."\");'" : "";
	//
	$m = "";
	
	$m .= "
	<a href='".$row['link']."' onclick='return NavAsync(this.href, true);'>
		<div id='notification_".$row['id']."' ".$unchecked_m.">
			<div class='padding'>
				<table style='width: 100%;'>
					<tr>
						<td>
							<a href='/".$row['owner_profile']['link']."' onclick='return NavAsync(this.href, true);'><div class='profile_avatar small_profile_avatar' style='background-image: url(".$row['owner_profile']['avatar'].");'></div></a>
						</td>
						<td style='width: 4px;'></td>
						<td style='width: 100%; position: relative;'>
							<a href='/".$row['owner_profile']['link']."' onclick='return NavAsync(this.href, true);'>
								<div class='small_text' style='cursor: pointer; font-weight: 600;'>".$row['owner_profile']['name']." <span class='fore2'>".GetOnline($row['owner_profile']['last_seen'], "●")."</span></div>
							</a>
							<div class='space'></div>
							<div class='small_text' style='width: 100%;'>".$row['message']."</div>
							<div class='space'></div>
							<div class='mini_text' style='width: 100%;'>".GetTime($row['created'])."</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</a>
	";
	
	return $m;
	
	
	return $row['id']."<br/>";
}

?>
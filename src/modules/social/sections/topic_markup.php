<?php

$engine_cfg = GetEngineConfig();

$topic_data = $section['topic_data'];

?>

<script>

function PinTopic(id)
{
	ApiMethod("social.topics.pin", { id: id }, function(data){ 
		if(data.response != null)
		{
			if(data.response === true)
			{
				ShowPanel("Закреплено", 0);
				Hide("pin_button");
				Show("unpin_button", "");
			}
			else
			{
				ShowPanel("Ошибка", 1);
			}
		}
	});
}

function UnpinTopic(id)
{
	ApiMethod("social.topics.unpin", { id: id }, function(data){ 
		if(data.response != null)
		{
			if(data.response === true)
			{
				ShowPanel("Откреплено", 0);
				Show("pin_button", "");
				Hide("unpin_button");
			}
			else
			{
				ShowPanel("Ошибка", 1);
			}
		}
	});
}

function DeleteTopic(id)
{
	ApiMethod("social.topics.delete", { id: id }, function(data){ 
		if(data.response != null)
		{
			if(data.response === true)
			{
				ShowPanel("Удалено", 0);
				NavigateAsync("/board", false);
			}
			else
			{
				ShowPanel("Ошибка", 1);
			}
		}
	});
	
	CloseWindow();
}

function ShowDeleteTopicDialog(id)
{
	ShowDialog("Удаление", "Тема будет безвозвратно удалена", "Удалить", "DeleteTopic(" + id + ");");
}

function EditTopic(id, title)
{
	ApiMethod("social.topics.edit", { id: id, title: title }, function(data){ 
		if(data.response != null)
		{
			if(data.response === true)
			{
				ShowPanel("Переименовано", 0);
				ReloadAsync();
				CloseWindow();
			}
			else
			{
				ShowPanel("Ошибка", 1);
			}
		}
	});
}

function CloseTopic(id)
{
	ApiMethod("social.topics.close", { id: id }, function(data){ 
		if(data.response != null)
		{
			if(data.response === true)
			{
				ShowPanel("Тема закрыта", 0);
				Hide("close_topic_button");
				Show("open_topic_button", "");
			}
			else
			{
				ShowPanel("Ошибка", 1);
			}
		}
	});
}

function OpenTopic(id)
{
	ApiMethod("social.topics.open", { id: id }, function(data){ 
		if(data.response != null)
		{
			if(data.response === true)
			{
				ShowPanel("Тема вновь открыта", 0);
				Show("close_topic_button", "");
				Hide("open_topic_button");
			}
			else
			{
				ShowPanel("Ошибка", 1);
			}
		}
	});
}

//

function ShowEditTopicWindow(file_name)
{
	ShowWindow("Изменение темы", Find("edit_topic_form").innerText);
	
	Find("new_topic_name").value = file_name;
}

</script>

<noscript id='edit_topic_form'>
	<input id='new_topic_name' placeholder='Название темы' maxlength='50' value='' class='text_input big_input inner_center' style='width: 98%;'/>
	<div class='big_space'></div>
	<div class='button' style='margin: auto;' onclick='EditTopic(<? echo($topic_data['id']);?>, Find("new_topic_name").value);'>Переименовать</div>
	<div class='big_space'></div>
</noscript>

<div id='social_layout'>
	<div class='content_box_parent'>
			<div class='content_substarate'></div>
				<div class='content_box'>
					<? Draw(Markup("social_sidebar")); ?>
					<div class='social_content_wrap'>
						<div style='min-height: 500px;'>
							<div class='box social_content_wrap responsive_content_box' style='width: 750px;'>
							<?php
							if(sizeof($topic_data) > 0)
							{
								AddTopicRating($topic_data['id']);
								?>

								<div class='profile_header'>
									<div class='padding'>
										<div class='text fore3' style='line-height: 45px;'><?php Draw(CropText($topic_data['title'], 50));?></div>
										
										<?php 
										Draw("<div class='button radial_button write_btn back3' style='bottom: 10px; right: 10px;' onclick='ScrollToId(\"post_form\");'></div>"); 
										?>
									</div>
								</div>
								<div class='divider'></div>
								
								<?php
								
								//
								$is_admin_logged = isAdminLogged();
								if($engine_cfg['logged'] || $is_admin_logged)
								{
									$uid = GetData("user_id", 0);
									//
									if($topic_data['owner'] == $uid || $is_admin_logged)
									{
										$pin_button = "";
										if($is_admin_logged)
										{
											$is_pinned = $topic_data['pinned'] == 1;
											$pin_display = ($is_pinned) ? "display: none;" : "";
											$unpin_display = (!$is_pinned) ? "display: none;" : "";
											
											$pin_button = "
											<div id='pin_button' style='".$pin_display."' class='topic_control_button small_button back3 fore0 border0' onclick='PinTopic(".$topic_data['id'].");'>Закрепить</div>
											<div id='unpin_button' style='".$unpin_display."' class='topic_control_button small_button back3 fore0 border0' onclick='UnpinTopic(".$topic_data['id'].");'>Открепить</div>
											";
										}
										//
										
										$is_closed = $topic_data['closed'] == 1;
										$close_display = ($is_closed) ? "display: none;" : "";
										$open_display = (!$is_closed) ? "display: none;" : "";
										
										$close_topic_button = "
										<div id='close_topic_button' class='topic_control_button small_button back3 fore0 border0' onclick='CloseTopic(".$topic_data['id'].");' style='".$close_display."'>Закрыть тему</div>
										<div id='open_topic_button' class='topic_control_button small_button back3 fore0 border0' onclick='OpenTopic(".$topic_data['id'].");' style='".$open_display."'>Открыть тему</div>
										";
										
										//
										$delete_topic_button = "";
										if($is_admin_logged)
										{
											$delete_topic_button = "
											<div class='topic_control_button small_button back3 fore0 border0' onclick='ShowDeleteTopicDialog(".$topic_data['id'].");'>Удалить</div>
											";
										}
										
										$m = "
										<style>
										.topic_control_button
										{
											display: inline-block;
											margin: 8px;
											margin-left: 4px;
											margin-right: 4px;
											width: auto;
											padding-left: 8px;
											padding-right: 8px;
										}
										</style>
										<div style='text-align: right; padding-left: 4px; padding-right: 4px;'>
											".$pin_button."
											".$close_topic_button."
											<div class='topic_control_button small_button back3 fore0 border0' onclick='ShowEditTopicWindow(".ToJSON($topic_data['title']).");'>Изменить</div>
											".$delete_topic_button."
										</div>
										<div class='divider'></div>
										";
										
										Draw($m);
									}
								}
								
								//
								
								$posts = array();
								
								$topic_id = -$topic_data['id'];
								$query = SQL("SELECT * FROM `posts` WHERE `recipient`='$topic_id' AND `recipient_type`='board' AND `status`='0' ORDER BY `created` ASC");
								$inc = 0;
								
								while($row = SQLFetchAssoc($query))
								{
									$posts[$inc] = GetPost($row['id'], $row);
									$inc++;
								}
								
								//
								
								if(sizeof($posts) > 0)
								{
									Draw("<div class='padding text'>Записи <span class='fore4'>".sizeof($posts)."</span></div>");
									
									Draw(GetPostsMarkup($posts));
									
									Draw("<div id='async_posts'></div>");
								}
								else
								{
									Draw("
									<div style='height: 100px;'></div>
									<div class='title_text' style='text-align: center;'>Пока нет записей</div>
									<div style='height: 100px;'></div>
									<div id='async_posts'></div>
									");
								}
								
								?>
								
								<div class='divider'></div>
								<div id='post_form'>
									<?php
									if($topic_data['closed'] == "0")
									{
										Draw(PostForm("board", $topic_id, "async_posts", "WriteEnd"));
									}
									else
									{
										Draw("
										<div style='height: 50px;'></div>
										<div class='title_text' style='text-align: center;'>Тема закрыта</div>
										<div style='height: 50px;'></div>
										");
									}
									?>
								</div>
							
							<?
							}
							else
							{
								?>
								<div style='height: 200px;'></div>
								<div class='title_text' style='text-align: center;'>Темы не существует</div>
								<div style='height: 200px;'></div>
								<?
							}
							?>
							<div class='divider'></div>
							<div class='space'></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php Draw(Markup("big_footer_markup"));?>
</div>
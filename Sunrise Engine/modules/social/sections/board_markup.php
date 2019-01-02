<?php
$engine_cfg = GetEngineConfig();

//CreateTopic("Тестовый топик");
?>

<script>
function CreateTopic(title, text)
{
	ApiMethod("social.topics.create", { title: title, text: text }, function(data){ 
		if(data.response != null)
		{
			if(data.response !== false)
			{
				var id = data.response;
				ShowPanel("Тема создана", 0);
				NavigateAsync("/topic" + id, false);
				CloseWindow();
			}
			else
			{
				ShowPanel("Ошибка", 1);
			}
		}
	});
}
</script>

<div id='social_layout'>
	<div class='content_box_parent'>
			<div class='content_substarate'></div>
				<div class='content_box'>
	
					<? Draw(Markup("social_sidebar")); ?>
					
					<div class='social_content_wrap'>
						<div style='min-height: 500px;'>
							<div class='box social_content_wrap responsive_content_box' style='width: 750px;'>
								<div class='profile_header'>
									<div class='padding'>
										<div class='text fore3' style='line-height: 45px;'>Обсуждения</div>
										
										<?php 
										if(true /*$engine_cfg['logged']*/)
										{
											Draw("<div class='button radial_button write_btn back3' style='bottom: 10px; right: 10px;' onclick='SlideToggle(\"post_form\");'></div>");
										}
										?>
									</div>
								</div>
								<div id='post_form' style='display: none;'>
									<div class='space'></div>
									<div class='title_text inner_center'>Создание темы</div>
									<div class='space'></div>
									<div class='divider'></div>
									<div class='padding'>
										<input id='new_topic_name' placeholder='Название темы (обязательно)' maxlength='50' value='' class='text_input big_input inner_center n_back3' style='width: 98%;'/>
										<div class='space'></div>
										<textarea id='new_topic_text' class='text_input n_back3' placeholder='Текст сообщения' maxlength='20480' style='display: block; width: 98%; height: 100px;'></textarea>
										<div class='space'></div>
										<div class='button' style='margin: auto;' onclick='CreateTopic(Find("new_topic_name").value, Find("new_topic_text").value);'>Создать</div>
										<div class='space'></div>
									</div>
								</div>
								<div class='divider'></div>
								
								<?
								
								$topics = array();
								
								$query = SQL("SELECT * FROM `topics` WHERE `status`='0' AND `pinned` = '1' ORDER BY `rating` DESC");
								
								while($row = SQLFetchAssoc($query))
								{
									$topics[sizeof($topics)] = GetTopic($row['id'], $row);
								}
								
								$query = SQL("SELECT * FROM `topics` WHERE `status`='0' AND `pinned` = '0' ORDER BY `edited` DESC");
								
								while($row = SQLFetchAssoc($query))
								{
									$topics[sizeof($topics)] = GetTopic($row['id'], $row);
								}
								
								//
								
								if(sizeof($topics) > 0)
								{
									Draw("<div class='padding text'>Темы <span class='fore4'>".sizeof($topics)."</span></div>");
									
									Draw(GetTopicsMarkup($topics));
								}
								else
								{
									Draw("
									<div style='height: 100px;'></div>
									<div class='title_text' style='text-align: center;'>Пока нет тем</div>
									<div style='height: 100px;'></div>
									");
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

<?php
$engine_cfg = GetEngineConfig();
?>

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
										<div class='title_text fore3' style='line-height: 45px;'>Все записи</div>
										
										<? if($engine_cfg['logged'])Draw("<div class='button radial_button write_btn back3' style='bottom: 10px; right: 10px;' onclick='SlideToggle(\"post_form\");'></div>"); ?>
									</div>
								</div>
								<div id='post_form' style='display: none;'>
									<?
									if($engine_cfg['logged'])
									{
										$user_id = GetData("user_id", 0);
										Draw(PostForm("profile", $user_id, "async_posts"));
									}
									?>
								</div>
								<div class='divider'></div>
								
								<?
								
								$posts = array();
								
								$query = SQL("SELECT `id` FROM `posts` WHERE (`recipient_type`='profile' OR `recipient_type`='board') AND `status`='0' ORDER BY `created` DESC LIMIT 0, 1000");
								$inc = 0;
								
								while($row = SQLFetchAssoc($query))
								{
									$posts[$inc] = GetPost($row['id']);
									$inc++;
								}
								
								//
								
								if(sizeof($posts) > 0)
								{
									Draw("<div class='padding title_text'>Записи <span class='fore4'>".sizeof($posts)."</span></div>");
									
									Draw("<div id='async_posts'></div>");
									
									Draw(GetPostsMarkup($posts));
								}
								else
								{
									Draw("
									<div style='height: 100px;'></div>
									<div class='title_text' style='text-align: center;'>Пока нет записей</div>
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
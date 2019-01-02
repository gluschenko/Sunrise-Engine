<?php

$engine_cfg = GetEngineConfig();

$profile_data = $section['profile_data'];

?>

<div id='social_layout'>
	<div class='content_box_parent'>
			<div class='content_substarate'></div>
				<div class='content_box'>
					<? Draw(Markup("social_sidebar")); ?>
					
					<div class='social_content_wrap'>
						<div style='min-height: 500px;'>
							<div class='box social_content_wrap responsive_content_box' style='width: 750px;'>
								<?
								if($profile_data['status'] != -2)
								{
								?>
								
								<div class='profile_header'>
									<div class='big_space'></div>
									
									<div id='profile_wrap'>
										<div class='profile_avatar pointer' style='background-image: url(<? echo($profile_data['avatar']);?>);' onclick='ImageBox("<? echo($profile_data['avatar']);?>");'></div>
										<div class='space'></div>
										<div class='title_text fore3'><? echo($profile_data['name']);?> <? echo(GetOnline($profile_data['last_seen'], "●"));?></div>
										<div <? echo(($profile_data['about'] == "")? "style='display: none;'" : "");?>>
											<div class='space'></div>
											<div class='text fore3' style='width: 400px; margin: auto;'><? echo($profile_data['about']);?></div>
										</div>
										<div class='space'></div>
										<a class='link text fore3' href='/id<?php echo($profile_data['id']); ?>' onclick='return NavAsync(this.href, true);'>@<?php echo($profile_data['link']);?></a>
									</div>
									
									<div id='profile_info_wrap' style='display: none;'>
										<div class='big_space'></div>
										<div class='profile_avatar small_profile_avatar' style='background-image: url(<? echo($profile_data['avatar']);?>);'></div>
										<div class='big_space'></div>
										
										<div style='margin: auto; max-width: 500px;'>
											<?
											
											$profile_vals = array(
												"Последние действия:", GetTime($profile_data['last_seen']),
												"Регистрация:", GetTime($profile_data['reg_date']),
												"Визиты:", $profile_data['visits'],
											);
											
											if($profile_data['vk_id'] != 0)
											{
												$profile_vals[sizeof($profile_vals)] = "Профиль ВКонтакте:";
												$profile_vals[sizeof($profile_vals)] = "<a class='link link_fore3' target='_blank' href='http://vk.com/id".$profile_data['vk_id']."'>id".$profile_data['vk_id']."</a>";
											}
											
											for($i = 0; $i < sizeof($profile_vals); $i += 2)
											{
												Draw("
												<div style='height: 35px;'>
													<div><div class='text fore3' style='float: left; width: 245px; text-align: right;'>".$profile_vals[$i + 0]."</div></div>
													<div><div class='text fore3' style='float: right; width: 245px; text-align: left;'><b>".$profile_vals[$i + 1]."</b></div></div>
												</div>
												");
											}
											
											?>
										</div>
										
										<div class='big_space'></div>
									</div>
									
									<script>
									function ShowHideInfo(btn){
										if(!Hidden("profile_wrap"))
										{
											SlideHide("profile_wrap");
											SlideShow("profile_info_wrap");
											btn.className = "button radial_button profile_hide_btn back3";
										}
										else
										{
											SlideShow("profile_wrap");
											SlideHide("profile_info_wrap");
											btn.className = "button radial_button profile_btn back3";
										}
									}
									</script>
									
									<div class='button radial_button write_btn back3' style='bottom: 10px; right: 10px;' onclick='SlideToggle("post_form");'></div>
									<div class='button radial_button profile_btn back3' style='bottom: 10px; right: 60px;' onclick='ShowHideInfo(this);'></div>
									
									<div class='big_space'></div>
								</div>
								
								<div id='post_form' style='display: none;'>
									<? 
									Draw(PostForm("profile", $profile_data['id'], "async_posts"));
									
									/*if($engine_cfg['logged'])
									{
										Draw(PostForm("profile", $profile_data['id'], "async_posts"));
									}
									else
									{
										Draw("
										<div class='big_space'></div>
										<div class='title_text' style='text-align: center;'>Авторизуйтесь, чтобы оставлять записи</div>
										<div class='big_space'></div>
										");
									}*/
									?>
									<div class='divider'></div>
								</div>
								
								<?
								
								$posts = array();
								
								$query = SQL("SELECT * FROM `posts` WHERE `recipient`='".$profile_data['id']."' AND `recipient_type`='profile' AND `status`='0' ORDER BY `created` DESC LIMIT 0, 1000");
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
									
									Draw("<div id='async_posts'></div>");
									
									Draw(GetPostsMarkup($posts));
									
									/*for($i = 0; $i < sizeof($posts); $i++)
									{
										Draw(GetPostMarkup($posts[$i]));
									}*/
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
								
								<?
								}
								else
								{
								?>
								<div style='height: 200px;'></div>
								<div class='title_text' style='text-align: center;'>Пользователя не существует</div>
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
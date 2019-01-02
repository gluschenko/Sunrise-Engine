<?php

if($section['logged'])
{
	$uid = GetData("user_id", 0);
	$profile = GetProfile($uid);
	
	?>
	
	<script>
	function UpdateProfile()
	{
		var login = Find("login").value;
		var first_name = Find("first_name").value;
		var last_name = Find("last_name").value;
		
		var about = Find("about").value;
		var avatar = Find("avatar").getAttribute("data-content");
		
		var password = Find("password").value;
		var password_confirm = Find("password_confirm").value;
		
		if(password == password_confirm){
			ShowLoader();
			
			ApiMethod("social.account.update", { login: login, password: password, first_name: first_name, last_name: last_name, about: about, avatar: avatar }, function(data){ 
				if(data.response != null)
				{
					//Hide("settings_info");
					
					if(data.response != 0)
					{
						/*ShowLoader();
						SlideShow("settings_info");*/
						
						ShowPanel("Настройки успешно сохранены", 0);
					}
					else
					{
						ShowModal("Ошибка", "Неверно введены данные", 2);
					}
				}
				
				HideLoader();
			});
		}
		else
		{
			ShowModal("Ошибка", "Пароли не совпадают", 2);
		}
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
								<div class='padding'>
									<div class='title_text'>Настройки</div>
								</div>
								<div class='divider'></div>
								<div class='big_space'></div>
								<div class='padding'>
									
									<div style='max-width: 400px; margin: auto;'>
										<div id='settings_info' style='display: none;'>
											<div class='panel panel_good'>
												<div class='title_text fore3'>Настройки успешно сохранены</div>
											</div>
											<div class='big_space'></div>
										</div>
										
										<div class='title_text inner_center'>Имя</div>
										<div class='space'></div>
										<input id='first_name' class='text_input big_input inner_center' value='<? echo $profile['first_name']; ?>' style='width: 98%;'/>
										<div class='space'></div>
										<div class='title_text inner_center'>Фамилия</div>
										<div class='space'></div>
										<input id='last_name' class='text_input big_input inner_center' value='<? echo $profile['last_name']; ?>' style='width: 98%;'/>
										<div class='space'></div>
										<div class='title_text inner_center'>E-mail</div>
										<div class='space'></div>
										<input id='login' class='text_input big_input inner_center' value='<? echo $profile['email']; ?>' style='width: 98%;'/>
										<div class='space'></div>
										<div class='title_text inner_center'>О себе</div>
										<div class='space'></div>
										<input id='about' class='text_input big_input inner_center' maxlength='140' value='<? echo $profile['about']; ?>' style='width: 98%;'/>
										<div class='space'></div>
										<div class='title_text inner_center'>Аватар</div>
										<div class='space'></div>
										<div style='position: relative;'>
											
											<?php
											Draw(Markup("upload_window"));
											//Draw(Markup("choose_photo_window"));
											?>
											
											<script>
											_ChooseWindowCallback = _UploadWindowCallback = function(content_obj){
												Find("avatar").setAttribute("data-content", content_obj.id);
												Find("avatar-preview").style.backgroundImage = "url(" + content_obj.data + ")";
											};
											</script>
											<div id='avatar' data-content='<? echo $profile['avatar_id']; ?>'></div>
											<div id='avatar-preview' class='profile_avatar' style='height: 150px; width: 150px; margin-left: 30px; background-image: url(<? echo $profile['avatar']; ?>);'></div>
											<div style='position: absolute; right: 30px; top: 30px;'>
												<div class="button back4" style="margin: auto; width: 120px;" onclick="ShowUploadDialog();">Загрузить</div>
												<div class='space'></div>
												<div class="button back4" style="margin: auto; width: 120px;" onclick="ShowChoosePhotoDialog();">Выбрать</div>
											</div>
										</div>
										<div class='big_space'></div>
										<div class='divider'></div>
										<div class='space'></div>
										<div class='title_text inner_center'>Безопасность</div>
										<div class='space'></div>
										<input placeholder='Новый пароль' id='password' type='password' class='text_input big_input inner_center' style='width: 98%;'/>
										<div class='space'></div>
										<input placeholder='Пароль ещё раз' id='password_confirm' type='password' class='text_input big_input inner_center' style='width: 98%;'/>
										
										<div class='big_space'></div>
										<div id='auth_info'></div>
										<div id='auth_button' class='button back4' style='margin: auto;' onclick='UpdateProfile();'>Сохранить</div>
									</div>
								</div>
								<div class='big_space'></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php Draw(Markup("big_footer_markup"));?>
	</div>
	
	<?
}
else
{
	?>
	<script>
		ShowLoader();
		setTimeout(function(){ 
			Navigate("/board"); 
		}, 100);
	</script>
	<?
}

?>
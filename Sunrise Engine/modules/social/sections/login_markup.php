<?php

if(!$section['logged'] && !$section['reload'])
{
	?>
	
	<script>
	function Login()
	{
		var login = Find("login").value;
		var password = Find("password").value;
		
		ShowLoader();
		
		ApiMethod("social.account.login", { login: login, password: password }, function(data){ 
			if(data.response != null)
			{
				if(data.response != 0)
				{
					ShowLoader();
					Navigate("/board");
				}
				else
				{
					var markup = "<div class='space'></div><div class='text fore4 inner_center'>Неверная пара логин-пароль</div><div class='space'></div>";
					Write("auth_info", markup);
				}
			}
			
			HideLoader();
		});
	}
	
	function VKLogin()
	{
		ShowLoader();
		Navigate("<? echo(VKAuthURL()); ?>");
	}
	</script>
	
	<style>
	.vk_login{
		background-repeat: no-repeat;
		background-size: 30px;
		background-image: url(/modules/social/assets/vk_logo.png);
		background-color: #5b7fa6;
		background-position: 13px, center;
	}
	
	.vk_login:hover{
		background-color: #2b587a;
	}
	</style>
	
	<div class='big_space'></div>
	<div class='title_text' style='text-align: center;'>Вход через ВКонтакте</div>
	<div class='big_space'></div>
	<div class='button vk_login' style='margin: auto;' onclick='VKLogin();'>Войти</div>
	<div class='big_space'></div>
	<div class='divider'></div>
	<div class='big_space'></div>
	
	<div style='margin: auto; width: 60%;'>
		<input placeholder='E-mail' id='login' class='text_input big_input inner_center' style='width: 98%;' onkeypress='if(event.keyCode == 13)Find("password").select();'/>
		<div class='space'></div>
		<input placeholder='Пароль' id='password' type='password' class='text_input big_input inner_center' style='width: 98%;' onkeypress='if(event.keyCode == 13)Find("auth_button").click();'/>
	</div>
	
	<div class='big_space'></div>
	<div id='auth_info'></div>
	<div id='auth_button' class='button back4' style='margin: auto;' onclick='Login();'>Войти</div>
	<div class='space'></div>
	<div class='space'></div>
	<div style='text-align: center;'>
		<a class='link link_fore4' href='/register' onclick='return NavAsync(this.href, true);'>Регистрация</a>
	</div>
	<div class='space'></div>
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
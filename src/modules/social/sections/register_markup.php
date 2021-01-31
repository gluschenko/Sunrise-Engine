<link rel="stylesheet" href="/modules/social/assets/social_styles.css" type="text/css"/>

<?php

if(!$section['logged'])
{
	?>
	
	<script>
	function Register()
	{
		var login = Find("login").value;
		var password = Find("password").value;
		var password_confirm = Find("password_confirm").value;
		var first_name = Find("first_name").value;
		var last_name = Find("last_name").value;
		
		ShowLoader();
		
		if(password == password_confirm){
			ApiMethod("social.account.register", { login: login, password: password, first_name: first_name, last_name: last_name }, function(data){ 
				if(data.response != null)
				{
					if(data.response != 0)
					{
						Navigate("/board");
					}
					else
					{
						var markup = "<div class='space'></div><div class='text fore4 inner_center'>Некорректно заполнены поля</div><div class='space'></div>";
						Write("auth_info", markup);
					}
				}
				
				HideLoader();
			});
		}
		else{
			var markup = "<div class='space'></div><div class='text fore4 inner_center'>Пароли не совпадают</div><div class='space'></div>";
			Write("auth_info", markup);
		}
	}
	</script>
	
	<div class='big_space'></div>
	<div style='margin: auto; width: 60%;'>
		<input placeholder='Имя' id='first_name' class='text_input big_input inner_center' style='width: 98%;'/>
		<div class='space'></div>
		<input placeholder='Фамилия' id='last_name' class='text_input big_input inner_center' style='width: 98%;'/>
		<div class='space'></div>
		<input placeholder='E-mail' id='login' class='text_input big_input inner_center' style='width: 98%;'/>
		<div class='space'></div>
		<input placeholder='Пароль' id='password' type='password' class='text_input big_input inner_center' style='width: 98%;'/>
		<div class='space'></div>
		<input placeholder='Пароль ещё раз' id='password_confirm' type='password' class='text_input big_input inner_center' style='width: 98%;'/>
		
		<div class='big_space'></div>
		<div id='auth_info'></div>
		<div id='auth_button' class='button back4' style='margin: auto;' onclick='Register();'>Регистрация</div>
		<div class='space'></div>
		<div class='space'></div>
		<div style='text-align: center;'>
			<a class='link link_fore4' href='/login' onclick='return NavAsync(this.href, true);'>Вход</a>
		</div>
		<div class='space'></div>
	</div>
	<?
}
else
{
	?>
	<div class='title_text'>Переадресация...</div>
	
	<script>
		setTimeout(function(){ 
			Navigate("/board"); 
		}, 100);
	</script>
	<?
}

?>
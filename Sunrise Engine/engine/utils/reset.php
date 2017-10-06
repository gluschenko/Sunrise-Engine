<?php
//Точка экстренного сброса пароля и токена
//Alexander Gluschenko (10-04-2016)

require($_SERVER['DOCUMENT_ROOT']."/engine/engine.php");

$state = 0;

if(isset($_REQUEST['reset_password']))
{
	ResetAdminPassword();
	
	$state = 1;
}

if(isset($_REQUEST['reset_token']))
{
	ResetAdminToken();
	
	$state = 1;
}

//

if($state == 0)
{
	?>
	<h1>Экстренный сброс:</h1><br/>
	<a href='?reset_password'>Сбросить пароль (будет выслан по email)</a><br/>
	<a href='?reset_token'>Сбросить токен (убъет все сессии)</a><br/>
	<a href='?reset_password&reset_token'>Сбросить пароль и токен!</a><br/>
	<?php
}
else
{
	?>
	<h1>Successfully done!</h1><br/>
	<a href='?'>Back</a><br/>
	<?php
}

?>
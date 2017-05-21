<?
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
	<h1>Emergency reset:</h1><br/>
	<p>Tap any link to exterminate access to Sunrise Engine</p><br/>
	<a href='?reset_password'>Reset admin password (will be sent to email)</a><br/>
	<a href='?reset_token'>Reset admin token (kills all sessions)</a><br/>
	<a href='?reset_password&reset_token'>Reset password & token!</a><br/>
	<?
}
else
{
	?>
	<h1>Successfully done!</h1><br/>
	<a href='?'>Back</a><br/>
	<?
}

?>
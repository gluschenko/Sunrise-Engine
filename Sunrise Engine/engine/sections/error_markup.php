<?php
//
$error = (isset($section['url_data'][1]))? $section['url_data'][1] : "";

$error_title = "Ошибка";
$error_description = "Мы сами этого не ожидали";

if($error == "403")
{
	$error_title = "Ошибка 403";
	$error_description = "Доступ к директории запрещён";
}

if($error == "404")
{
	$error_title = "Ошибка 404";
	$error_description = "Страница не найдена";
}

?>

<div style='height: 50px;'></div>
<div class='logo_text' style='text-align: center; font-size: 56px;'><?php echo($error_title);?></div>
<div style='height: 40px;'></div>
<div class='logo_text' style='text-align: center; font-size: 24px;'><?php echo($error_description);?></div>
<div style='height: 40px;'></div>
<a href='/' onclick='return NavAsync(this.href, true);'><div class='button back4' style='margin: auto;'>На главную</div></a>
<div style='height: 50px;'></div>

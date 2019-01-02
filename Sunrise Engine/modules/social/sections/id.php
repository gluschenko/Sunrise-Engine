<?php

$id = (isset($section['url_data'][1]))? $section['url_data'][1] : 0;

$profile_data = GetProfile($id);
$section['profile_data'] = $profile_data;

if($profile_data['status'] != -2)
{
	$section['title'] = $profile_data['name'];
}
else
{
	$section['title'] = "Пользователя не существует";
}

?>
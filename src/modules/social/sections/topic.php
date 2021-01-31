<?php

$id = (isset($section['url_data'][1]))? $section['url_data'][1] : 0;

$topic_data = GetTopic($id);
$section['topic_data'] = $topic_data;

if(sizeof($topic_data) > 0)
{
	$section['title'] = $topic_data['title']." - {site_name}";
}
else
{
	$section['title'] = "Темы не существует";
}

?>
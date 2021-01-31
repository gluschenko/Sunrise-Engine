<?php
$request = $section['request'];

$post = 0;
if(isset($request['post']))$post = $request['post'];

//

$section['post_id'] = $post;//0 - отображение новостей из массива

if($post == 0)
{
	$section['posts'] = GetNews();
}
else
{
	$news_post = GetNewsByID($post);
	
	if(sizeof($news_post) != 0)
	{
		$section['post'] = $news_post[0];
		$section['title'] = $news_post[0]['header']." - {site_name}";
	}
}

?>
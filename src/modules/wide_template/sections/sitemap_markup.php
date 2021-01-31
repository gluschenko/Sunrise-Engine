<?php

echo("
<style>
.sitemap_link{
	border-radius: 3px;
	display: block;
	position: relative;
}

.sitemap_link:hover {
	border-color: #777 !important;
}

.sitemap_preview{
	border-top-left-radius: 3px;
	border-bottom-left-radius: 3px;
	top: 0px;
    bottom: 0px;
	position: absolute;
	width: 54px;
	background-position: center, center;
	background-size: cover, cover;
}
</style>
");

$sitemap_link = "
<a class='border6 sitemap_link' href='/{2}' async>
	{3}
	<div class='padding' style='display: inline-block; vertical-align: middle;'>
		<span class='text' title='{0}'>{1}</span>
		<br/>
		<span class='text fore6'>/{2}</span>
	</div>
</a>
<div class='space'></div>
";

$cfg = GetEngineConfig();

$count = 0;

foreach($cfg['sections'] as $key => $value)
{
	$page = $cfg['sections'][$key];
	
	if($page['enabled'] == 1 && $page['is_public'] == 1)
	{
		$preview_markup = "";
		
		if($page['preview_image'] != "")
		{
			$preview_markup = "
			<div class='sitemap_preview' style='display: inline-block; background-image: url(".$page['preview_image'].");'></div>
			<div style='display: inline-block; width: 54px;'></div>
			";
		}
		
		echo(StringFormat($sitemap_link, $page['header'], CropText($page['header'], 40), $page['name'], $preview_markup));
		
		$count++;
	}
}

if($count == 0)
{
	echo("
	<div style='height: 120px;'></div>
	<div class='title_text inner_center'>Страниц нет</div>
	<div style='height: 120px;'></div>
	");
}


?>
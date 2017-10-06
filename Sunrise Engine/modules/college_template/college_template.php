<?php

$cfg = GetConfig();
$settings = GetSettings();

//

$view_stats = GetSectionViews("all");
$admin_logged = isAdminLogged();

//

AddSection(array(
	"type" => 0,
	"name" => "sitemap",
	"header" => "Карта сайта",
	"title" => "Карта сайта - {site_name}",
	"layout" => 0,
	"permission" => 0,
	"keywords" => "карта сайта, sitemap",
	"description" => "Карта сайта.",
	"section_dir" => "/modules/college_template/sections",
));

//

AddMarkup("global_markup", "\$global\$");
AddTemplate("global", "<!-- GLOBAL -->");

AddMarkup("left_menu_link", "<a class='menu_button' href='{href}' {external}>{title}</a>");
AddMarkup("header_menu_link", "<a class='header_link' href='{href}' {external}>{title}</a>");
AddMarkup("footer_menu_link", "<div><a class='small_text link fore6' style='line-height: 30px;' href='{href}' {external}>{title}</a></div>");

AddMarkup("footer_markup", "
<div style='text-align: center; position: relative;'>
	<div class='big_space'></div>
	<div class='small_text' style='text-align: center;'>".$settings['site_name']." © 2014-".GetYear(time())."</div>
	<div class='space'></div>
	<a href='/dev' onclick='return NavAsync(this.href, true);'><span class='mini_text pointer'>Разработка: Глущенко Александр</span></a>
	<div class='big_space'></div>
	<div class='zest'></div>
</div>
");

$metrika_couner = "";
if(isAdminLogged())
{
	$metrika_couner = "
	<div class='space'></div>
	<div class='divider'></div>
	<div class='space'></div>
	
	<div style='text-align: center;'>
		<!--Yandex-->
		<a href='https://metrika.yandex.ru/stat/?id=29931789&amp;from=informer'
			target='_blank' rel='nofollow'><img src='//bs.yandex.ru/informer/29931789/3_1_FFFFFFFF_FFFFFFFF_0_pageviews'
			style='width:88px; height:31px; border:0;' title='Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)' 
			onclick='try{Ya.Metrika.informer({i:this,id:29931789,lang:\"ru\"}); return false; }catch(e){console.log(e);}'/></a>
	</div>
	";
}

AddMarkup("big_footer_markup", "
<div class='footer_box'>
	<div class='content_wrap'>
		<div class='footer_box_wrap'>
			<div class='footer_block'>
				<div class='text fore3'>Ссылки</div>
				<div class='space'></div>
				".BlocksBar("footer_menu", "{content}", "{content}", Markup("footer_menu_link"))."
			</div>
			<div class='footer_block'>
				<div class='text fore3'>Контакты</div>
				<div class='space'></div>
				<div class='contacts_geo small_text fore6'>\$contacts_geo\$</div>
				<div class='contacts_phone small_text fore6'>\$contacts_phone\$</div>
				<div class='contacts_mail small_text fore6'>\$contacts_mail\$</div>
				<div class='contacts_time small_text fore6'>\$contacts_time\$</div>
			</div>
			<div class='footer_block'>
				".Markup("footer_extension_block")."
			</div>
		</div>
		<a href='/dev' async class='footer_author_text text link'>Разработка: Глущенко Александр</a>
	</div>
	<div class='footer_box_bottom'>
		<div class='content_wrap'>
			<div class='footer_box_bottom_wrap'>
				<a class='small_text link fore6 footer_bottom_text' href='/sitemap' async>Карта сайта</a>
				<div onclick='ShowStatsWindow();' class='pointer'>
					<div class='stats_views small_text fore6 footer_bottom_text' title='Просмотры сайта за сегодня'>".$view_stats[0]."</div>
					<div class='stats_users small_text fore6 footer_bottom_text' title='Уникальные посетители сегодня'>".$view_stats[1]."</div>
				</div>
				<div class='small_text fore6 footer_bottom_text'>".$settings['site_name']." © 2014-".GetYear(time())."</div>
			</div>
		</div>
		
		<script>
		function ShowStatsWindow()
		{
			ShowBox('Статистика сайта', Find('StatsWindow').innerText, 600);
		}
		</script>
		<noscript id='StatsWindow'>
		".GraphMarkup(0, "all", 30, rand(0, 100000))."
		".$metrika_couner."
		</noscript>
	</div>
</div>
");

AddMarkup("body_footer_markup", "
<div class='padding'>
	
	<div style='text-align: center;'>
		<div style='display: inline;' title='Посещения всего сайта за минувший месяц' onclick='if(Hidden(\"stats_{random}\")){ SlideShow(\"stats_{random}\"); ScrollToId(\"stats_{random}\"); } else { SlideHide(\"stats_{random}\"); }'>
			<div class='views sign_text pointer'>".$view_stats[0]."</div>
			<div class='inline_space'></div>
			<div class='unique sign_text pointer'>".$view_stats[1]."</div>
		</div>
	</div>
	
	<div id='stats_{random}' style='display: none;'>
		<div class='space'></div>
		<div class='line'></div>
		<div class='space'></div>
		
		<div class='title_text'>Статистика сайта</div>
		
		<div class='space'></div>
		
		".GraphMarkup(0, "all", 30, "{random}")."
		
		".
		($admin_logged? 
		"<div class='space'></div>
		<div class='line'></div>
		<div class='space'></div>
		
		<div style='text-align: center;'>
			<!--Yandex-->
			<a href='https://metrika.yandex.ru/stat/?id=29931789&amp;from=informer'
				target='_blank' rel='nofollow'><img src='//bs.yandex.ru/informer/29931789/3_1_FFFFFFFF_FFFFFFFF_0_pageviews'
				style='width:88px; height:31px; border:0;' title='Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)' 
				onclick='try{Ya.Metrika.informer({i:this,id:29931789,lang:\"ru\"}); return false; }catch(e){console.log(e);}'/></a>
		</div>" : "").
		"
	</div>
</div>
");

AddMarkup("left_markup", "
<div class='box sidebar_box' style='width: 250px;'>
	<div class='padding'>
		<div class='title_text'>{title}</div>
	</div>
	<div class='divider'></div>
	<div class='padding_8'>
		<div>{content}</div>
	</div>
</div>
");

AddMarkup("left_links", "
<div class='box sidebar_box' style='width: 250px;'>
	<div class='padding'>
		<div class='title_text'>{title}</div>
	</div>
	<div class='divider'></div>
	<div class='space_8'></div>
	<div>{content}</div>
	<div class='space_8'></div>
</div>
");

AddMarkup("pin_markup", "
<div id='pin_wrap' style='position: relative; z-index: 1001;'>
	<div class='n_back4 padding' style='text-align: center; width: 100%; position: fixed;'>
		<div class='small_text bold fore3' style='line-height: 30px; display: inline-block;'>{header}</div>
		<a href='{link}' onclick='SlideHide(\"pin_wrap\"); return NavAsync(this.href, true);' class='small_button back4 border3' style='font-size: 13px; margin-left: 10px; padding-left: 6px; padding-right: 6px; width: auto; display: inline-block;'>Подробнее</a>
		<div onclick='SlideHide(\"pin_wrap\");' style='position: absolute; right: 26px; top: 10px;' class='window_button close_white'></div>
	</div>
	<div style='height: 48px;'></div>
</div>
");


?>
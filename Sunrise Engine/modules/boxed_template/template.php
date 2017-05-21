<!-- Author: Alexander Gluschenko -->
<!-- 15-07-2015, 22-06-2016 -->
<?php
$settings = GetSettings();
?>
<!DOCTYPE HTML>
<!--

     #####
    #### _\_  ________
    ##=-[.].]| \      \
    #(    _\ |  |------|
     #   __| |  ||||||||
      \  _/  |  ||||||||    -  ЗЛОЙ АДМИН
   .--'--'-. |  | ____ |
  / __      `|__|[o__o]|
_(____nm_______ /____\____ 

-->
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta id="viewport" name="viewport" content="width=device-width, initial-scale=1"/>
	
	<meta name="description" content="<?php echo($section['description']);?>"/>
	<meta name="keywords" content="<?php echo($section['keywords']);?>"/>
	
	<meta name="author" content="<?php echo($settings['header']);?>"/>
	<meta name="coder" content="Alexander Gluschenko"/>
	<meta name="designer" content="Alexander Gluschenko"/>
	<meta name="robots" content="all"/>
	
	<meta name="theme-color" content="#e84c3d"/>
	<meta name="msapplication-TileColor" content="#e84c3d"/>
    <meta name="msapplication-TileImage" content="<?php echo($section['preview_image'] != "" ? $section['preview_image'] : "/modules/boxed_template/logo/og_logo.png");?>"/>
	
	<meta property="og:title" content="<?php echo($section['title']);?>"/>
	<meta property="og:site_name" content="<?php echo($settings['header']);?>"/>
	<meta property="og:description" content="<?php echo($section['description']);?>"/>
	<meta property="og:image" content="<?php echo($section['preview_image'] != "" ? $section['preview_image'] : "/modules/boxed_template/logo/og_logo.png");?>"/>
	<meta property="og:type" content="website"/>
	
	<title><?php echo($section['title']);?></title>
	
	<link rel="stylesheet" href="/engine/assets/common.css" type="text/css" />
	<link rel="stylesheet" href="/modules/boxed_template/assets/site.css" type="text/css" />
	
	<link rel="shortcut icon" href="<?php echo($section['icon'] != "" ? $section['icon'] : "/modules/boxed_template/favicon.ico");?>" type="image/x-icon"/>
	
	<script type="text/javascript" src="/engine/libs/jquery.js"></script>
	
	<script type="text/javascript" src="/engine/js/common.js"></script>
	<script type="text/javascript" src="/modules/boxed_template/js/site.js"></script>
	<!--Упразднено: <script type="text/javascript" async src="/engine/node.php"></script>-->
	
	<!--Автоматический вывод-->
	<? 
	DrawStyleSheets();

	DrawNodeVars();	
	DrawJavaScripts();
	?>
	
	<script>
	if(Sunrise.isMobile())
	{
		Find("viewport").setAttribute("content", "initial-scale=1, maximum-scale=1, user-scalable=0");
	}
	</script>
</head>

<body id='body'>
	
	<div id='loader' class='loader loader_body' style='display: none;'></div>
	
	<!--Windows-->
	<div id='window_layer' class='layer' style='display: none;'>
		<div id='window_layout' class='window'>
			<div id='window_box' class='box responsive_content_box'>
				<div id='window_header'>
					<div class='padding_16'>
						<div id='window_title' class='title_text' style='text-align: center;'>Window title</div>
					</div>
				</div>
				<div id='window_close_button' onclick='CloseWindow();' class='window_button close_mask back0'></div>
				<div class='divider'></div>
				<div id='window_content' class='padding'>
					[content]
				</div>
			</div>
			<div class='big_space'></div>
		</div>
	</div>
	
	<div id='alert_panel' class='alert_panel panel_good' style='display: none;'>
		<div id='alert_panel_text' class='title_text fore3'>State panel</div>
	</div>
	
	<!--Body-->
	
	<div id='body_wrap'>
		<div id='scroll_top' class='totop' style='display: <?php Draw(isTrue($section['layout'] != 3, "block", "none"));?>;' onclick='ScrollTo("scroll_top");'></div>
		
		<div id='header_wrap' style='display: <?php Draw(isTrue($section['header_wrap_type'] == 1, "block", "none"));?>;'>
			<? Draw(GetPinnedNews(Markup("pin_markup"))); ?>
			
			<div>
				<div style="height: 1px;"></div>
				<div class='head_box inner_center'>
					<div class='space'></div>
					<a href='/' onclick='return NavAsync(this.href, true);' style='margin: auto; display: inline-block;'>
						<div class='logo'></div>
						<div class='header_text fore3 logo_text pointer' title='<? echo $settings['slogan']; ?>'><?php echo($settings['header']);?></div>
					</a>
					<div class='space'></div>
					
					<div style='position: absolute; right: 10px; bottom: 40px;'>
						<a href='//vk.com/rzcoll' target="_blank" class='button header_button vk_button' style='display: inline-block;'></a>
						<a href='/board' onclick='return NavAsync(this.href, true);' class='button header_button board_button back4' style='display: inline-block;'>Обсуждения</a>
					</div>
					
					<div class='main_menu'>
						<div class='tool_menu_button' style='top: 0px; left: 0px;' onclick='ShowSideMenu("left_sidebar_wrap");'></div>
						<div class='header_tool_menu_button' style='top: 0px; right: 0px;' onclick='ShowSideMenu("header_menu_wrap");'></div>
						<div style='margin: auto; text-align: center;'>
							<div id='header_menu_wrap_back' class='outer_menu_wrap_container' onclick='HideSideMenu("header_menu_wrap");'></div>
							<div id='header_menu_wrap' class='header_menu_wrap' onclick='HideSideMenu("header_menu_wrap");'>
								<? Draw(BlocksBar("header_menu", "{content}", "{content}", Markup("header_menu_link"))); ?>
							</div>
						</div>
						
						<script>
						function ShowSideMenu(id)
						{
							Show(id, "inline-block");
							Show(id + "_back", "inline-block");
							if(id == "left_sidebar_wrap")
							{
								if(Hidden("default_layout"))NavAsync("/", false);
							}
						}
						
						function HideSideMenu(id)
						{
							Hide(id, "");
							Hide(id + "_back", "");
						}
						</script>
					</div>
				</div>
			</div>
		</div>
		
		<div id='admin_header_wrap' style='display: <?php Draw(isTrue($section['header_wrap_type'] == 2, "block", "none"));?>;'>
			<div class='admin_header'>
				<?php Draw(Markup("admin_menu_markup"));?>
			</div>
			<div style='height: 60px;'></div>
		</div>
		
		<div id='no_header_wrap' style='display: <?php Draw(isTrue($section['header_wrap_type'] == 0, "block", "none"));?>;'></div>
		
		<div id='empty_header_wrap' style='display: <?php Draw(isTrue($section['header_wrap_type'] == -1, "block", "none"));?>;'></div>
		
		<!---->
		
		<div id='default_layout' style='text-align: center; display: <?php Draw(isEqually($section['layout'], 0, "block", "none"));?>;'>
			<div id='left_sidebar_wrap_back' class='outer_menu_wrap_container' onclick='HideSideMenu("left_sidebar_wrap");'></div>
			<div id='left_sidebar_wrap' class='left_sidebar_wrap layout_box' onclick='HideSideMenu("left_sidebar_wrap");'>
				<?php Draw(BlocksBar("left_menu", Markup("left_markup"), Markup("left_links"), Markup("left_menu_link"))); ?>
			</div>
			<div class='layout_box' style='min-height: 500px;'>
				<div class='box responsive_content_box' style='width: 755px; margin-left: 15px;'>
					<div id='default_title_wrap' style='display: <?php Draw(isEqually($section['title_wrap_enabled'], true, "block", "none"));?>;'>
						<div class='padding'>
							<div id='default_title' class='title_text'><?php echo($section['header']);?></div>
						</div>
						<div class='divider'></div>
					</div>
					
					<div id='default_box' class='text padding'>
						<!--Content here!-->
						<?php Draw(isEqually($section['layout'], 0, "{markup}", ""));?>
					</div>
					<div class='divider'></div>
					<?php Draw(Markup("body_footer_markup"));?>
				</div>
				<?php Draw(Markup("footer_markup"));?>
			</div>
		</div>
		
		<div id='wide_layout' style='display: <?php Draw(isEqually($section['layout'], 1, "block", "none"));?>;'>
			<div class='box responsive_content_box' style='width: 1024px; margin: auto;'>
				<div id='wide_title_wrap' style='display: <?php Draw(isEqually($section['title_wrap_enabled'], true, "block", "none"));?>;'>
					<div class='padding'>
						<div id='wide_title' class='title_text'><?php echo($section['header']);?></div>
					</div>
					<div class='divider'></div>
				</div>
				
				<div id='wide_box' class='text padding'>
					<!--Content here!-->
					<?php Draw(isEqually($section['layout'], 1, "{markup}", ""));?>
				</div>
				<div class='divider'></div>
				<?php Draw(Markup("body_footer_markup"));?>
			</div>
			<?php Draw(Markup("footer_markup"));?>
		</div>
		
		<div id='narrow_layout' style='display: <?php Draw(isEqually($section['layout'], 2, "block", "none"));?>;'>
			<div class='box responsive_content_box' style='width: 512px; margin: auto; margin-top: 120px;'>
				<div id='narrow_title_wrap' style='display: <?php Draw(isEqually($section['title_wrap_enabled'], true, "block", "none"));?>;'>
					<div class='padding'>
						<div id='narrow_title' class='title_text'><?php echo($section['header']);?></div>
					</div>
					<div class='divider'></div>
				</div>
				
				<div id='narrow_box' class='text padding'>
					<!--Content here!-->
					<?php Draw(isEqually($section['layout'], 2, "{markup}", ""));?>
				</div>
			</div>
			<?php Draw(Markup("footer_markup"));?>
		</div>
		
		<div id='huge_layout' style='width: 98%; margin: auto; display: <?php Draw(isEqually($section['layout'], 3, "block", "none"));?>;'>
			<div class='box'>
				<div id='huge_title_wrap' style='display: <?php Draw(isEqually($section['title_wrap_enabled'], true, "block", "none"));?>;'>
					<div class='padding'>
						<div id='huge_title' class='title_text'><?php echo($section['header']);?></div>
					</div>
					<div class='divider'></div>
				</div>
				
				<div id='huge_box' class='text padding' style='min-height: 500px;'>
					<!--Content here!-->
					<?php Draw(isEqually($section['layout'], 3, "{markup}", ""));?>
				</div>
			</div>
			<?php Draw(Markup("footer_markup"));?>
		</div>
		
		<div id='full_layout' style='width: 100%; margin: auto; display: <?php Draw(isEqually($section['layout'], 4, "block", "none"));?>;'>
			<div id='full_box' class='text'>
				<!--Content here!-->
				<?php Draw(isEqually($section['layout'], 4, "{markup}", ""));?>
			</div>
		</div>
		
		<div id='semi_full_layout' style='text-align: center; display: <?php Draw(isEqually($section['layout'], 5, "block", "none"));?>;'>
			<div id='semi_full_box'>
				<!--Content here!-->
				<?php Draw(isEqually($section['layout'], 5, "{markup}", ""));?>
			</div>
		</div>
	</div>

	<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
	(function (d, w, c) {
		(w[c] = w[c] || []).push(function() {
			try {
				w.yaCounter29931789 = new Ya.Metrika({id:29931789,
					webvisor:true,
					clickmap:true,
					trackLinks:true,
					accurateTrackBounce:true});
			} catch(e) { }
		});
		
		var n = d.getElementsByTagName("script")[0],
			s = d.createElement("script"),
			f = function () { n.parentNode.insertBefore(s, n); };
		s.type = "text/javascript";
		s.async = true;
		s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";
		
		if (w.opera == "[object Opera]") {
			d.addEventListener("DOMContentLoaded", f, false);
		} else { f(); }
	})(document, window, "yandex_metrika_callbacks");
	</script>
	<noscript>
		<div><img src="//mc.yandex.ru/watch/29931789" style="position:absolute; left:-9999px;" alt="" /></div>
	</noscript>
	<!-- /Yandex.Metrika counter -->
</body>
</html>
<?

$config = GetConfig();
$settings = GetSiteSettings();

//print_r($settings);
		
?>
<script>

function SaveSiteSettings()
{
	var params = {
		admin_password: Find("settings_admin_password").value,
		admin_token: Find("admin_token").value,
		template: Find("template").value,
		site_name: Find("site_name").value,
		header: Find("header").value,
		slogan: Find("slogan").value,
		keywords: Find("keywords").value,
		description: Find("description").value,
		preview_image: Find("preview_image").value,
		icon: Find("icon").value,
		timezone: Find("timezone").value,
	};
	
	ShowLoader();
	
	ApiMethod("admin.settings.save", params, function(data){
		if(data.response != null)
		{
			if(data.response == 1)
			{
				/*Hide("settings_info");
				SlideShow("settings_info");
				ScrollToId("body");*/
				
				ShowPanel("Настройки успешно сохранены", 0);
			}
		}
		
		HideLoader();
	});
}

</script>

<div style='width: 600px; margin: auto;'>
	
	<!--<div id='settings_info' style='display: none;'>
		<div class='panel panel_good'>
			<div class='title_text fore3'>Настройки успешно сохранены</div>
		</div>
	</div>-->
	
	<div class='big_space'></div>
	
	<div class='title_text'>Название сайта (title)</div>
	<div class='space'></div>
	<input id='site_name' value='<? echo($settings['site_name']); ?>' placeholder='текст названия' class='text_input' style='width: 98%;'/>
	<div class='space'></div>
	
	<div class='title_text'>Заголовок</div>
	<div class='space'></div>
	<input id='header' value='<? echo($settings['header']); ?>' placeholder='текст заголовка' class='text_input' style='width: 98%;'/>
	<div class='space'></div>
	
	<div class='title_text'>Слоган</div>
	<div class='space'></div>
	<input id='slogan' value='<? echo($settings['slogan']); ?>' placeholder='Vi veri veniversum vivus vici' class='text_input' style='width: 98%;'/>
	<div class='space'></div>
	
	<div class='title_text'>Ключевые слова</div>
	<div class='space'></div>
	<textarea id='keywords' value='' placeholder='кошечки, рыбки, пирожки' class='text_input' style='width: 98%; height: 100px;'><? echo($settings['keywords']); ?></textarea>
	<div class='space'></div>
	
	<div class='title_text'>Описание</div>
	<div class='space'></div>
	<textarea id='description' value='' placeholder='текст описания' class='text_input' style='width: 98%; height: 100px;'><? echo($settings['description']); ?></textarea>
	<div class='space'></div>
	
	<div class='title_text'>Модуль шаблонов</div>
	<div class='space'></div>
	
	<select id='template' class='text_input' style='height: 30px; width: 100%;'>
		<?
		$modules = GetFilesList($config['root']."/modules", "");
		
		for($i = 0; $i < sizeof($modules); $i++)
		{
			$temp_path = $config['root']."/modules/".$modules[$i]['name']."/template.php";
			if(file_exists($temp_path))
			{
				Draw("<option value='".$modules[$i]['name']."'>".$modules[$i]['name']."</option>");
			}
		}
		?>
	</select>
	
	<script>
		Find("template").value = "<? echo $settings['template']; ?>";
	</script>
	<div class='space'></div>
	
	<div class='title_text'>Картинка предпросмотра (Open Graph)</div>
	<div class='space'></div>
	<input id='preview_image' value='<? echo($settings['preview_image']); ?>' placeholder='/files/image.png' class='text_input' style='width: 98%;'/>
	<div class='space'></div>
	
	<div class='title_text'>Иконка (.ico)</div>
	<div class='space'></div>
	<input id='icon' value='<? echo($settings['icon']); ?>' placeholder='/favicon.ico' class='text_input' style='width: 98%;'/>
	<div class='space'></div>
	
	<div class='title_text'>Часовой пояс (UTC)</div>
	<div class='space'></div>
	
	<select id='timezone' class='text_input' style='height: 30px; width: 100%;'>
		<option value='-12'>UTC -12</option>
		<option value='-11'>UTC -11</option>
		<option value='-10'>UTC -10</option>
		<option value='-9'>UTC -9</option>
		<option value='-8'>UTC -8</option>
		<option value='-7'>UTC -7</option>
		<option value='-6'>UTC -6</option>
		<option value='-5'>UTC -5</option>
		<option value='-4'>UTC -4</option>
		<option value='-3'>UTC -3</option>
		<option value='-2'>UTC -2</option>
		<option value='-1'>UTC -1</option>
		<option value='0'>UTC (0)</option>
		<option value='1'>UTC +1</option>
		<option value='2'>UTC +2</option>
		<option value='3'>UTC +3</option>
		<option value='4'>UTC +4</option>
		<option value='5'>UTC +5</option>
		<option value='6'>UTC +6</option>
		<option value='7'>UTC +7</option>
		<option value='8'>UTC +8</option>
		<option value='9'>UTC +9</option>
		<option value='10'>UTC +10</option>
		<option value='11'>UTC +11</option>
		<option value='12'>UTC +12</option>
	</select>
	
	<script>
		Find("timezone").value = "<? echo $settings['timezone']; ?>";
	</script>
	
	<div class='space'></div>
	
	<div class='title_text'>Ключ доступа <span title='Отвечает за актуальность текущих сессий'>[?]</span></div>
	<div class='space'></div>
	<input id='admin_token' value='<? echo($settings['admin_token']); ?>' class='text_input' style='width: 76.5%; display: inline-block; pointer-events: none;'/>
	<div class='small_button border0 fore0 back3' style='width: 20%; display: inline-block;' onclick='Find("admin_token").value = "";'>Сброс</div>
	<div class='space'></div>
	
	<div class='title_text'>Пароль администратора <span title='Остявьте поле пустым, если не хотите менять пароль'>[?]</span></div>
	<div class='space'></div>
	<input id='settings_admin_password' type='password' class='text_input' style='width: 98%;'/>
	<div class='space'></div>
	<div class='mini_text fore6'>E-mail для сброса пароля: <?php echo($config['admin_email']); ?> (admin_email в config.php)</div>
	<div class='space'></div>
	
	<div class='big_space'></div>
	
	<div class='button' style='margin: auto;' onclick='SaveSiteSettings();'>Сохранить</div>
	
	<div class='big_space'></div>

</div>
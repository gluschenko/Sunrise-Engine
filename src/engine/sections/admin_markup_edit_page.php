<?

if(true)
{
	if(true)
	{
		$page = "";
		if(isset($request['page']))$page = $request['page'];
		
		$page_obj = GetPage($page);
		?>
		
		<script>
		
		function SaveOrCreatePage()
		{
			ShowLoader();
			
			var params = {
				data: {
					header: Find("header").value,
					title: Find("title").value,
					layout: Find("layout_id").value,
					keywords: Find("keywords").value,
					description: Find("description").value,
					name: Find("name").value,
					enabled: parseInt(Find("enabled").value),
					icon: Find("icon").value,
					preview_image: Find("preview_image").value,
				},
				markup: Find("markup_editor").field.value,
			};
			
			ApiMethod("admin.pages.save", params, function(data){
				if(data.response != null)
				{
					if(data.response == 1)
					{
						ShowPanel("Страница успешно сохранена", 0);
					}
				}
				
				HideLoader();
			});
		}
		
		</script>
		
		
		<div class='title_text'>Содержимое</div>
		<div class='space'></div>
		<div id='markup_editor'></div>
		<!--<textarea id='markup_field' value='' class='text_input html_input'></textarea>-->
		<div class='space'></div>
		
		<script>
		var Markup = <? echo(ToJSON($page_obj['markup'])); ?>;
		//Find("markup_field").value = Markup;
		
		CodeEditor.Init("markup_editor", 
			function(editor){
				editor.field.value = Markup;
			},
			function(){
				SaveOrCreatePage();
			});
		</script>
		
		<div style='width: 600px; margin: auto;'>
			
			<div class='big_space'></div>
			
			<div class='title_text'>Название</div>
			<div class='space'></div>
			<input id='header' value='<? echo($page_obj['data']['header']); ?>' placeholder='Заголовок' class='text_input' style='width: 98%;'/>
			<div class='space'></div>
			
			<div class='title_text'>Заголовок (title)</div>
			<div class='space'></div>
			<input id='title' value='<? echo($page_obj['data']['title']); ?>' placeholder='Заголовок - {site_name}' class='text_input' style='width: 98%;'/>
			<div class='space'></div>
			
			<div class='title_text'>Идентификатор (URL) <span title='Изменение приведет к созданию копии данной страницы с новым идентификатором'>[?]</span></div>
			<div class='space'></div>
			<input id='name' value='<? echo($page_obj['data']['name']); ?>' placeholder='sample_page' class='text_input' style='width: 98%;'/>
			<div class='space'></div>
			
			<div class='title_text'>Каркас</div>
			<div class='space'></div>
			
			<select id='layout_id' class='text_input' style='height: 30px; width: 100%;'>
				<?
				$layout_assocs = array(
					"0", "Default (тело + сайдбар)",
					"1", "Wide (тело)",
					"2", "Narrow (узкое тело)",
					"3", "Huge (широкое тело)",
					"4", "Full (без тела)",
				);
				
				for($i = 0; $i < sizeof($layout_assocs); $i += 2)
				{
					Draw("<option value='".$layout_assocs[$i + 0]."'>".$layout_assocs[$i + 1]."</option>");
				}
				?>
			</select>
			
			<script>
				Find("layout_id").value = "<? echo $page_obj['data']['layout']; ?>";
			</script>
			<div class='space'></div>
			
			<div class='title_text'>Статус</div>
			<div class='space'></div>
			
			<select id='enabled' class='text_input' style='height: 30px; width: 100%;'>
				<option value='1'>Страница включена</option>
				<option value='0'>Страница выключена</option>
			</select>
			
			<script>
				Find("enabled").value = "<? echo $page_obj['data']['enabled']; ?>";
			</script>
			<div class='space'></div>
			
			<div class='title_text'>Ключевые слова</div>
			<div class='space'></div>
			<input id='keywords' value='<? echo($page_obj['data']['keywords']); ?>' placeholder='кошечки, рыбки, пирожки' class='text_input' style='width: 98%;'/>
			<div class='space'></div>
			
			<div class='title_text'>Описание</div>
			<div class='space'></div>
			<input id='description' value='<? echo($page_obj['data']['description']); ?>' placeholder='текст описания' class='text_input' style='width: 98%;'/>
			<div class='space'></div>
			
			<div class='title_text'>Картинка предпросмотра (Open Graph)</div>
			<div class='space'></div>
			<input id='preview_image' value='<? echo($page_obj['data']['preview_image']); ?>' placeholder='/files/image.png' class='text_input' style='width: 98%;'/>
			<div class='space'></div>
			
			<div class='title_text'>Иконка (.ico)</div>
			<div class='space'></div>
			<input id='icon' value='<? echo($page_obj['data']['icon']); ?>' placeholder='/favicon.ico' class='text_input' style='width: 98%;'/>
			<div class='space'></div>
			
			<div class='big_space'></div>
			
			<div style='height: 40px; width: 410px; margin: auto;'>
				<div class='button back4' style='float: left;' onclick='SaveOrCreatePage();'>Сохранить</div>
				<div class='button back2' style='float: right;' onclick='window.open("/" + Find("name").value);'>Просмотр</div>
			</div>
			
			<div class='big_space'></div>
		
		</div>
		
		<?
	}
}

?>
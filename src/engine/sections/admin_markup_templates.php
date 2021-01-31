<noscript id='new_template_window'>
	
	<input id='temp_name' value='' class='text_input big_input' style='width: 96%;'>
	<div class='space'></div>
	<div class='button back4 at_center' onclick='Find("body").TempName = Find("temp_name").value; Find("body").TempValue = ""; OpenTemplate("");'>Создать</div>
	
</noscript>

<noscript id='edit_template_window'>
	
	<!--<textarea id='markup_field' value='' class='text_input html_input'></textarea>-->
	<div id='template_markup_editor'></div>
	<div class='space'></div>
	<div class='button back4 at_center' onclick='SaveOrEditTemplate(Find("body").TempName, Find("template_markup_editor").field.value);'>Сохранить</div>
	
</noscript>

<script>

if(!Find("body").TempName)Find("body").TempName = "";
if(!Find("body").TempValue)Find("body").TempValue = "";

function NewTemplate()
{
	ShowWindow("Введите имя шаблона", Find("new_template_window").innerText, 0, 1, 400);
}

function OpenTemplate(template_name)
{
	var ShowWin = function()
	{
		ShowWindow("Шаблон - $" + Find("body").TempName + "$", Find("edit_template_window").innerText, 0, 0, 1000);
		
		CodeEditor.Init("template_markup_editor", function(editor){ //Закодить редактор? Изи!
			editor.field.value = Find("body").TempValue;
		});
		
		//Find("markup_field").value = TempValue;
	};
	
	if(template_name != "")
	{
		Find("body").TempName = "";
		Find("body").TempValue = "";
		
		ShowLoader();
		
		ApiMethod("admin.templates.get", { template: template_name }, function(data){
			console.log(data.response);
			
			if(data.response != null)
			{
				if(data.response != 0)
				{
					Find("body").TempName = data.response.name;
					Find("body").TempValue = data.response.data;
					
					ShowWin();
				}
			}
			
			HideLoader();
		});
	}
	else
	{
		ShowWin();
	}
}

function SaveOrEditTemplate(template_name, value)
{
	ShowLoader();
	
	ApiMethod("admin.templates.save", { template: template_name, data: value }, function(data){
		//console.log(data.response);
		
		if(data.response != null)
		{
			if(data.response != 0)
			{
				ReloadAsync();
				ShowPanel("Шаблон успешно сохранён", 0);
			}
			else
			{
				ShowPanel("Шаблон не сохранён", 1);
			}
		}
		
		HideLoader();
	});
}

var DeleteCallback = null;

function ShowDeleteDialog(name, template_block)
{
	DeleteCallback = function(){
		DeleteTemplate(name, template_block);
		CloseWindow();
	};
	
	ShowDialog("Удаление", "Шаблон будет безвозвратно удален", "Удалить", "DeleteCallback();");
}

function DeleteTemplate(template_name, template_block)
{
	ShowLoader();
	
	ApiMethod("admin.templates.delete", { template: template_name }, function(data){
		if(data.response != null)
		{
			if(data.response == 1)
			{
				Hide(template_block);
			}
		}
		
		HideLoader();
	});
}

</script>

<div class='button back4' onclick='NewTemplate();'>Добавить</div>

<div class='space'></div>

<div class='display: inline-block; margin: auto;'>

<style>
.item_border{
	border: solid 1px #ddd;
}

.item_border:hover{
	border: solid 1px #777;
}
</style>
<?

$templates = GetTemplates();

foreach($templates as $key => $value)
{
	?>
	<div id='<? echo($key);?>_block' class='n_back3 item_border' style='position: relative; border-radius: 3px; height: 40px; margin: 3px;'>
		
		<div class='text bold fore0' style='position: absolute; left: 10px; line-height: 40px;'>$<? echo($key);?>$</div>
		<div class='button back3 fore0' style='position: absolute; right: 90px; width: 90px;' onclick='OpenTemplate("<? echo($key);?>");'>Изменить</div>
		<div class='button back3 fore0' style='position: absolute; right: 0px; width: 90px;' onclick='ShowDeleteDialog("<? echo($key);?>", "<? echo($key);?>_block");'>Удалить</div>
		
	</div>
	<?
	
	?>
	<!--<div id='<? echo($key);?>_block' style='display: inline-block; width: 30%; margin: 10px;'>
		<div class='n_back4' style='border-top-left-radius: 15px; border-top-right-radius: 15px;'>
			<div class='text fore3 inner_center' style='line-height: 35px;'>$<? echo($key);?>$</div>
		</div>
		<div style='position: relative; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; border: solid #e84c3d 1px;'>
			<div style='height: 100px; text-align: left; overflow: hidden;'><? echo(FilterText($value, "open_html"));?></div>
			<div style='background-color: rgba(255,255,255,0.8); position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;'>
				<div class='big_space'></div>
				<div class='inner_center'>
					<div class='button back4' style='display: inline-block; width: 100px;' onclick='OpenTemplate("<? echo($key);?>");'>Изменить</div>
					<div class='button back4' style='display: inline-block; width: 100px;' onclick='ShowDeleteDialog("<? echo($key);?>", "<? echo($key);?>_block");'>Удалить</div>
				</div>
				<div class='big_space'></div>
			</div>
		</div>
	</div>-->
	<?
}

if(sizeof($templates) == 0)Draw("<div class='text'>Нет шаблонов...</div>");

?>
</div>
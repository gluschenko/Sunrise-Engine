<script>
function DisableModule(module)
{
	API.CallMethod("admin.modules.disable", { module: module }, function(data){
		if(data.response == 1)
		{
			Find("disable_" + module).style.display = "none";
			Find("enable_" + module).style.display = "inline-block";
		}
	});
}

function EnableModule(module)
{
	API.CallMethod("admin.modules.enable", { module: module }, function(data){
		if(data.response == 1)
		{
			Find("disable_" + module).style.display = "inline-block";
			Find("enable_" + module).style.display = "none";
		}
	});
}
</script>
<style>
.module_wrap{
	height: 120px;
	position: relative;
}

.module_icon{
	display: inline-block;
	width: 110px;
	height: 110px;
	margin: 5px;
	border-radius: 5px;
	background-size: cover, cover;
	background-image: url(/engine/assets/module_icon.png);
}
</style>

<?php

$config = GetConfig();
$files = GetFilesList($_SERVER['DOCUMENT_ROOT']."/modules", "");

for($i = 0; $i < sizeof($files); $i++)
{
	$file_name = $files[$i]['name'];
	$file_path = "/modules/".$files[$i]['name'];
	$file_size = round($files[$i]['size']/1024);
	$file_modify_date = $files[$i]['modified'];
	
	//
	$disable_hide = "";
	$enable_hide = "display: none;";
	//
	$settings = GetSettings();
	$disabled_modules = $settings['disabled_modules'];
	
	if(in_array($file_name, $disabled_modules))
	{
		$disable_hide = "display: none;";
		$enable_hide = "";
	}
	
	$module_title = $file_name;
	
	$manifest_path = $_SERVER['DOCUMENT_ROOT'].$file_path.StringFormat("/{0}.json", $file_name);
	if(file_exists($manifest_path))
	{
		$data = LoadJSON($manifest_path, array("title" => "", "version" => ""));
		$module_title = StringFormat("{0} (v {1})", $data['title'], $data['version']);
	}
	
	$module_icon = "";
	$icon_path = $file_path.StringFormat("/{0}.png", $file_name);
	if(file_exists($_SERVER['DOCUMENT_ROOT'].$icon_path))
	{
		$module_icon = StringFormat("background-image: url({0});", $icon_path);
	}
	//
	
	$markup = "
	<div class='module_wrap back3' id='module_".$i."'>
		<div style='position: absolute; left: 0px;'>
			<div class='module_icon' style='vertical-align: middle; ".$module_icon."'></div>
			<div style='display: inline-block; vertical-align: middle; margin-left: 5px;'>
				<div class='title_text'>".$module_title."</div>
				<div class='small_text fore6'>".$file_path."</div>
				<div class='small_text fore6'>Изменен ".GetTime($file_modify_date)."</div>
			</div>
		</div>
		<div style='position: absolute; right: 0px;'>
			<div style='line-height: 120px;'>
				<div class='inline_buttom small_button back6 fore0 disabled'>Параметры</div>
				<div id='disable_".$file_name."' class='inline_buttom small_button back2 fore3' style='".$disable_hide."' onclick='DisableModule(\"".$file_name."\");'>Включено</div>
				<div id='enable_".$file_name."' class='inline_buttom small_button back4 fore3' style='".$enable_hide."' onclick='EnableModule(\"".$file_name."\");'>Отключено</div>
			</div>
		</div>
	</div>
	";
	
	Draw($markup);
}

if(sizeof($files) == 0)Draw("<div class='text padding'>Нет модулей...</div><div class='space'></div>");

/*

if(true)
{
	if(true)
	{
		
		
		?>
		
		<table style='width: 100%;'>
			<tr class='table_header'>
				<td class='table_column'><div class='text fore3'>#</div></td>
				<td class='table_column'><div class='text fore3'>Имя</div></td>
				<td class='table_column'><div class='text fore3'>Путь</div></td>
				<td class='table_column'><div class='text fore3'>Дата изменения</div></td>
				<td class='table_column'></td>
				<td class='table_column'></td>
			</tr>
			
			<?
			
			for($i = 0; $i < sizeof($files); $i++)
			{
				$file_name = $files[$i]['name'];
				$file_path = "/modules/".$files[$i]['name'];
				$file_size = round($files[$i]['size']/1024);
				$file_modify_date = $files[$i]['modified'];
				
				//
				$disable_hide = "";
				$enable_hide = "display: none;";
				//
				$settings = GetSettings();
				$disabled_modules = $settings['disabled_modules'];
				
				if(in_array($file_name, $disabled_modules))
				{
					$disable_hide = "display: none;";
					$enable_hide = "";
				}
				
				//
				
				$markup = "
				<tr class='back3' id='module_".$i."'>
					<td class='table_column' style='width: 40px;'><div class='text'>".($i + 1).".</div></td>
					<td class='table_column'><div class='text'>".$file_name."</div></td>
					<td class='table_column'><div class='text'>".$file_path."</div></td>
					<td class='table_column'><div class='text'>".GetTime($file_modify_date)."</div></td>
					<td class='table_column'>
						<a class='link' href='?act=modules&settings=".$file_name."' async style='text-align: center; ".$disable_hide."' onclick=''>Параметры</a>
					</td>
					<td class='table_column'>
						<div id='disable_".$file_name."' class='link' style='text-align: center; ".$disable_hide."' onclick='DisableModule(\"".$file_name."\");'>Отключить</div>
						<div id='enable_".$file_name."' class='link' style='text-align: center; ".$enable_hide."' onclick='EnableModule(\"".$file_name."\");'>Подключить</div>
					</td>
				</tr>
				";
				
				Draw($markup);
			}
			
			?>
		
		</table>
		
		<?
		
		if(sizeof($files) == 0)Draw("<div class='text padding'>Нет модулей...</div><div class='space'></div>");
	}
}

*/

?>
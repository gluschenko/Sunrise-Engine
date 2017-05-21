<?php
$config = GetConfig();
$engine_config = GetEngineConfig();
?>

<?php
$conn = $engine_config['mysqli_connection'];
$sql_server = mysqli_get_server_info($conn);
?>

<table>
<td style='min-width: 300px; border-right: solid 1px #bbb; padding-right: 8px;'>
	<div class='title_text'>Сводная</div>
	<div class='space'></div>
	<div class='text'>Движок: <? echo $engine_config['engine_name']; ?></div>
	<div class='text'>Версия движка: <? echo $engine_config['version']; ?></div>
	<div class='text'>Сервер: <? echo $_SERVER['SERVER_SOFTWARE']; ?></div>
	<div class='text'>Интерфейс: <? echo $_SERVER['GATEWAY_INTERFACE']; ?></div>
	<div class='text'>IP сервера: <? echo $_SERVER['SERVER_ADDR']; ?></div>
	<div class='text'>Хост: <? echo $_SERVER['SERVER_NAME']; ?></div>
	<div class='text'>Протокол: <? echo $_SERVER['SERVER_PROTOCOL']; ?></div>
	<div class='text'>PHP: <? echo phpversion(); ?> (<a class='link' href='/engine/utils/platform.php' target='_blank'>подробнее</a>)</div>
	<div class='text'>MySQL: <? echo $sql_server; ?></div>
	<div class='text'>БД: <? echo $config['mysql_name']; ?></div>
	<div class='text'>Время: <? echo date("d-m-Y H:i:s", time()); ?></div>
	<?php $load_avg = sys_getloadavg(); ?>
	<div class='text' title='1, 5 и 15 минут'>Нагрузка: <? echo implode(" ", $load_avg); ?></div>
	<?php $inodes = GetInodes(); ?>
	<div class='text' title='Объекты ФС'>Inodes: <? echo(StringFormat("{0}/{1} ({2}%)", $inodes['used'], $inodes['all'], round($inodes['used_rate'] * 100, 4))); ?></div>
	
	<div style='height: 50px;'></div>
	<script>
	function MakeBackup()
	{
		ShowLoader();
		API.CallMethod("engine.backupAll", {}, function(data){
			if(data.response)
			{
				if(data.response == 1)
				{
					AJAXLayout.Navigate(null, "/admin?act=upload");
					ShowPanel("Бекап создан и помещен в /files", 0);
				}
				else
				{
					ShowPanel("Ошибка резервного копирования", 1);
				}
				HideLoader();
			}
		});
	}
	</script>
	<div class='big_button' style='width: auto;' onclick='MakeBackup();'>Сделать бекап</div>
</td>
<td style='min-width: 10px;'></td>
<td style='width: 100%;'>
	<div class='title_text'>Дисковое пространство</div>
	<div class='space'></div>
	<?php
	$free_space = disk_free_space($config['root']);
	$total_space = disk_total_space($config['root']);
	
	$bar_width = 400;
	$engaged_disk_ratio = (1 - $free_space/$total_space);
	$bar_body_width = $bar_width * $engaged_disk_ratio;
	?>
	<div title='<? echo(round($engaged_disk_ratio * 100, 2));?>%' class='n_back4' style='margin-top: 10px; margin-bottom: 10px; padding: 3px; border-radius: 15px; height: 15px; width: <? echo($bar_width);?>px;'>
		<div class='n_back3 fore0' style='border-radius: 15px; height: 15px; width: <? echo($bar_body_width);?>px;'></div>
	</div>
	<div class='text'><? echo GetDataUnits($free_space);?> свободно из <? echo GetDataUnits($total_space);?></div>
	
	<div class='space'></div>
	<div class='divider'></div>
	<div class='space'></div>
	<div class='title_text'>База данных</div>
	<div class='space'></div>
	
	<table style='width: 100%;'>
		<tr class='table_header'>
			<td class='table_column'><div class='text fore3'>Таблица</div></td>
			<td class='table_column'><div class='text fore3'>Поля</div></td>
			<td class='table_column'><div class='text fore3'>Строки</div></td>
			<td class='table_column'><div class='text fore3'>Размер</div></td>
			<td class='table_column'><div class='text fore3'>Макс. размер</div></td>
			<td class='table_column'><div class='text fore3'>Сравнение</div></td>
		</tr>
		
		<?php
		
		$database = GetDataBaseStructure();
		
		for($i = 0; $i < sizeof($database); $i++)
		{
			$markup = "
			<tr class='back3'>
				<td class='table_column'><div class='text'>".$database[$i]['table_name']."</div></td>
				<td class='table_column'><div class='text'><div class='link' onclick='ShowWindow(\"Таблица ".$database[$i]['table_name']."\", Find(\"table_".$i."\").innerText);'>".sizeof($database[$i]['fields'])."</div></div></td>
				<td class='table_column'><div class='text'>".$database[$i]['statuses']['rows']."</div></td>
				<td class='table_column'><div class='text'>".GetDataUnits($database[$i]['statuses']['length'])."</div></td>
				<td class='table_column'><div class='text'>".GetDataUnits($database[$i]['statuses']['max_length'])."</div></td>
				<td class='table_column'><div class='text'>".$database[$i]['statuses']['collation']."</div></td>
			</tr>
			";
			
			$markup .= "
			<noscript id='table_".$i."'>
				<table style='width: 100%;'>
					<tr class='table_header'>
						<td class='table_column'><div class='text fore3'>Поле</div></td>
						<td class='table_column'><div class='text fore3'>Тип данных</div></td>
						<td class='table_column'><div class='text fore3'>NULL</div></td>
					</tr>";
			
			for($f = 0; $f < sizeof($database[$i]['fields']); $f++)
			{
				$markup .= "
				<tr class='back3'>
					<td class='table_column'><div class='text'>".$database[$i]['fields'][$f]['name']."</div></td>
					<td class='table_column'><div class='text'>".$database[$i]['fields'][$f]['type']."</div></td>
					<td class='table_column'><div class='text'>".$database[$i]['fields'][$f]['null']."</div></td>
				</tr>
				";
			}
			
			$markup .= "
			</table>
			</noscript>";
			
			Draw($markup);
		}
		
		?>
	
	</table>
	
	<div class='space'></div>
	<div class='title_text'>Функции движка и модулей</div>
	<div class='space'></div>
	
	<div style='overflow-y: auto; max-height: 500px;'>
	
		<table style='width: 100%;'>
			<tr class='table_header'>
				<td class='table_column' style='width: 40px;'><div class='text fore3'>#</div></td>
				<td class='table_column' style='min-width: 200px;'><div class='text fore3'>Функция</div></td>
				<td class='table_column'><div class='text fore3'>Комментарий</div></td>
			</tr>
			<?php
			
			$functions = GetEngineStructure();
			
			$markup = "";
			$last_file_path = "";
			
			for($f = 0; $f < sizeof($functions); $f++)
			{
				$func = $functions[$f];
				$file_path = $func['file_path'];
				//
				if($file_path != $last_file_path)
				{
					$markup .= "
					<tr class='back3'>
						<td class='table_column n_back2'></td>
						<td class='table_column n_back2'><div class='text fore3'>".$file_path."</div></td>
						<td class='table_column n_back2'></td>
					</tr>
					";
				}
				
				$markup .= "
				<tr class='back3'>
					<td class='table_column'><div class='text'>".($f + 1).".</div></td>
					<td class='table_column'><div class='text' title='".$func['signature']."'>".CropText($func['signature'], 40)."</div></td>
					<td class='table_column'><div class='text'>".($func['comment'] ? $func['comment'] : "<span class='disabled'>нет комментария</span>")."</div></td>
				</tr>
				";
				//
				$last_file_path = $file_path;
			}
			
			Draw($markup);
			?>
		</table>
	
	</div>
	
	<div class='space'></div>
	<div class='title_text'>Методы API</div>
	<div class='space'></div>
	
	<div style='overflow-y: auto; max-height: 500px;'>
	
		<table style='width: 100%;'>
			<tr class='table_header'>
				<td class='table_column' style='width: 40px;'><div class='text fore3'>#</div></td>
				<td class='table_column' style='width: 200px;'><div class='text fore3'>Метод</div></td>
				<td class='table_column'><div class='text fore3'>Аргументы</div></td>
				<td class='table_column'><div class='text fore3'>Комментарий</div></td>
			</tr>
			<?
			$markup = "";
			
			$count = 0;
			foreach($engine_config['methods'] as $key => $value)
			{
				$markup .= "
				<tr class='back3'>
					<td class='table_column'><div class='text'>".($count + 1).".</div></td>
					<td class='table_column'><div class='text'>".$key."</div></td>
					<td class='table_column'><div class='text'>Coming soon</div></td>
					<td class='table_column'><div class='text'>Coming soon</div></td>
				</tr>
				";
				
				$count++;
			}
			
			Draw($markup);
			?>
		</table>
	
	</div>
</td>
</table>
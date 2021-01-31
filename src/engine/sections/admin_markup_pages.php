<?
if(true)
{
	if(true)
	{
		$config = GetConfig();
		$config_files = GetFilesList($config['root']."/data/pages", "_config.json", true, true);
		
		//Сортировака по дате изменения
		function sortByOrder($a, $b) {
			return $b['modified'] - $a['modified'];
		}
		
		usort($config_files, 'sortByOrder');
		//
		
		?>
		
		<script>
		
		var DeleteCallback = null;
		
		function ShowDeleteDialog(page_name, page_block)
		{
			DeleteCallback = function(){
				DeletePage(page_name, page_block);
				CloseWindow();
			};
			
			ShowDialog("Удаление", "Страница будет безвозвратно удалена", "Удалить", "DeleteCallback();");
		}
		
		function DeletePage(page_name, page_block)
		{
			ShowLoader();
			
			ApiMethod("admin.pages.delete", { page: page_name }, function(data){
				if(data.response != null)
				{
					if(data.response == 1)
					{
						Hide(page_block);
					}
				}
				
				HideLoader();
			});
		}
		
		</script>
		
		<a href='/admin?act=edit_page' onclick='return NavAsync(this.href, true);' style='display: inline-block;'><div class='button back4'>Создать новую</div></a>
		<a href='/admin?act=edit_page&page=main' onclick='return NavAsync(this.href, true);' style='display: inline-block;'><div class='button back4'>Главная (ред.)</div></a>
		
		<div class='space'></div>
		<div class='divider'></div>
		<div class='space'></div>
		
		<table style='width: 100%;'>
			<tr class='table_header'>
				<td class='table_column'><div class='text fore3'>#</div></td>
				<td class='table_column'><div class='text fore3'>Название</div></td>
				<td class='table_column'><div class='text fore3'>URL</div></td>
				<td class='table_column'><div class='text fore3'>Статус</div></td>
				<td class='table_column'><div class='text fore3'>Размер</div></td>
				<td class='table_column'><div class='text fore3'>Дата изменения</div></td>
				<td class='table_column'><div class='text fore3'>Статистика</div></td>
				<td class='table_column'></td>
			</tr>
			
			<?
			
			for($i = 0; $i < sizeof($config_files); $i++)
			{
				$page_data = MergeArrays($config_files[$i]['data'], DefaultSection());
				
				$page_name = $page_data['name'];
				$page_header = CropText($page_data['header'], 30);
				$page_header_full = $page_data['header'];
				$page_size = $config_files[$i]['size'] + GetFileSize($config['root'].$page_data['page_markup'], 0);
				$page_status = $page_data['enabled'];
				
				$view_stats = GetSectionViews($page_name);
				
				//
				
				$status_markup = "<div class='text bold fore2'>Включена</div>";
				
				if($page_status != 1)
				{
					$status_markup = "<div class='text bold fore4'>Выключена</div>";
				}
				
				$markup = "
				<tr class='back3' id='page_".$i."'>
					<td class='table_column'><div class='text'>".($i + 1).".</div></td>
					<td class='table_column'><a class='text pointer' title='".$page_header_full."' href='/admin?act=edit_page&page=".$page_name."' onclick='return NavAsync(this.href, true);'>".$page_header."</a></td>
					<td class='table_column'><a class='text pointer' href='/".$page_name."' target='_blank'>/".$page_name."</a></td>
					<td class='table_column'>".$status_markup."</td>
					<td class='table_column'><div class='text'>".GetDataUnits($page_size)."</div></td>
					<td class='table_column'><div class='text'>".GetTime($config_files[$i]['modified'])."</div></td>
					<td class='table_column'>
						<a href='/admin?act=stats&section=".$page_name."' onclick='return NavAsync(this.href, true);'>
						<div style='display: inline;' title='Просмотры и уникальные посетители за сегодня.'>
							<div class='views sign_text pointer'>".$view_stats[0]."</div>
							<div class='inline_space'></div>
							<div class='unique sign_text pointer'>".$view_stats[1]."</div>
						</div>
						</a>
					</td>
					<td class='table_column'><div class='text link' style='text-align: center;' onclick='ShowDeleteDialog(\"".$page_name."\", \"page_".$i."\");'>Удалить</div></td>
				</tr>
				";
				
				Draw($markup);
			}
			
			?>
		
		</table>
		
		<?
		
		if(sizeof($config_files) == 0)Draw("<div class='text padding'>Нет страниц...</div>");
	}
}

?>
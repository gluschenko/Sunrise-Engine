<?

if(true)
{
	if(true)
	{
		?>
		
		<script>
		
		var DeleteCallback = null;
		
		function ShowDeleteDialog(id, news_block)
		{
			DeleteCallback = function(){
				ShowLoader();
				
				ApiMethod("admin.news.delete", { id: id }, function(data){
					if(data.response != null)
					{
						if(data.response == 1)
						{
							Find(news_block).className = "back3 disabled";
							
							Hide("delete_" + id);
							Show("restore_" + id);
						}
					}
					
					HideLoader();
				});
				
				CloseWindow();
			};
			
			ShowDialog("Удаление", "Новость будет удалена", "Удалить", "DeleteCallback();");
		}
		
		function RestoreNews(id, news_block)
		{
			ShowLoader();
			
			ApiMethod("admin.news.restore", { id: id }, function(data){
				if(data.response != null)
				{
					if(data.response == 1)
					{
						Find(news_block).className = "back3";
						
						Show("delete_" + id);
						Hide("restore_" + id);
					}
				}
				
				HideLoader();
			});
		}
		
		function Pin(id)
		{
			ShowLoader();
			
			ApiMethod("admin.news.pin", { id: id }, function(data){
				if(data.response != null)
				{
					if(data.response == 1)
					{
						Hide("pin_" + id);
						Show("unpin_" + id);
					}
				}
				
				HideLoader();
			});
		}
		
		function Unpin(id)
		{
			ShowLoader();
			
			ApiMethod("admin.news.unpin", { id: id }, function(data){
				if(data.response != null)
				{
					if(data.response == 1)
					{
						Show("pin_" + id);
						Hide("unpin_" + id);
					}
				}
				
				HideLoader();
			});
		}
		
		</script>
		
		<a href='/admin?act=edit_news' onclick='return NavAsync(this.href, true);' style='display: inline-block;'><div class='button back4'>Добавить новость</div></a>
		
		<div class='space'></div>
		<div class='divider'></div>
		<div class='space'></div>
		
		<table style='width: 100%;'>
			<tr class='table_header'>
				<td class='table_column'><div class='text fore3'>#</div></td>
				<td class='table_column'><div class='text fore3'>Заголовок</div></td>
				<td class='table_column'><div class='text fore3'>Адрес</div></td>
				<td class='table_column'><div class='text fore3'>Дата публикации</div></td>
				<td class='table_column'></td>
				<td class='table_column'></td>
			</tr>
			
			<?
			$news = array();
			
			$query = SQL("SELECT * FROM `news` WHERE 1 ORDER BY `news`.`id` DESC");
			
			$iterations = 0;
			while($row = SQLFetchAssoc($query))
			{
				$news[$iterations] = $row;
				$iterations++;
			}
			
			for($i = 0; $i < sizeof($news); $i++)
			{
				$id = $news[$i]['id'];
				$ip = $news[$i]['ip'];
				$status = $news[$i]['status'];
				$created = GetTime($news[$i]['created']);
				$edited = GetTime($news[$i]['edited']);
				$deleted = GetTime($news[$i]['deleted']);
				$restored = GetTime($news[$i]['restored']);
				$header = $news[$i]['header'];
				$text = $news[$i]['text'];
				$pin = $news[$i]['pin'];
				
				$header_short = CropText($header, 50);
				
				//
				
				$disabled = "";
				$delete_hide = "";
				$restore_hide = "display: none;";
				$pin_hide = "";
				$unpin_hide = "display: none;";
				
				if($status != 0)
				{
					$disabled = "disabled";
					$delete_hide = "display: none;";
					$restore_hide = "";
				}
				
				if($pin != 0)
				{
					$pin_hide = "display: none;";
					$unpin_hide = "";
				}
				
				//
				
				$markup = "
				<tr class='back3 ".$disabled."' id='news_".$id."'>
					<td class='table_column'><div class='text'>".($i + 1).".</div></td>
					<td class='table_column'><a class='link' href='/admin?act=edit_news&post=".$id."' onclick='return NavAsync(this.href, true);' title='".$header."'>".$header_short."</a></td>
					<td class='table_column'><a class='link' href='/news?post=".$id."' onclick='return NavAsync(this.href, true);'>/news?post=".$id."</a></td>
					<td class='table_column'><div class='text'>".$created."</div></td>
					<td class='table_column'>
						<div id='pin_".$id."' class='link' style='text-align: center; ".$pin_hide."' onclick='Pin(".$id.");'>Закрепить</div>
						<div id='unpin_".$id."' class='link' style='text-align: center; ".$unpin_hide."' onclick='Unpin(".$id.");'>Открепить</div>
					</td>
					<td class='table_column'>
						<div id='delete_".$id."' class='link' style='text-align: center; ".$delete_hide."' onclick='ShowDeleteDialog(".$id.", \"news_".$id."\");'>Удалить</div>
						<div id='restore_".$id."' class='link' style='text-align: center; ".$restore_hide."' onclick='RestoreNews(".$id.", \"news_".$id."\");'>Восстановить</div>
					</td>
				</tr>
				";
				
				Draw($markup);
			}
			
			?>
		
		</table>
		
		<?
		
		if(sizeof($news) == 0)Draw("<div class='text padding'>Новостей нет...</div>");
	}
}

?>
<?

if(true)
{
	if(true)
	{
		$config = GetConfig();
		$config_files = GetFilesList($_SERVER['DOCUMENT_ROOT']."/data/blocks", "_config.json", true, true);
		
		?>
		
		<script>
		
		var DeleteCallback = null;
		
		function ShowDeleteDialog(id, bar_block)
		{
			DeleteCallback = function(){
				DeleteBar(id, bar_block);
				CloseWindow();
			};
			
			ShowDialog("Удаление", "Бар будет безвозвратно удален", "Удалить", "DeleteCallback();");
		}
		
		function DeleteBar(id, bar_block)
		{
			ShowLoader();
			
			ApiMethod("admin.bars.delete", { id: id }, function(data){
				if(data.response != null)
				{
					if(data.response == 1)
					{
						Hide(bar_block);
					}
				}
				
				HideLoader();
			});
		}
		
		function SaveBarBlocks(num_id)
		{
			ShowLoader();
			
			var params = {
				id: ConfigFiles[num_id]['data']['id'],
				blocks: ConfigFiles[num_id]['data']['blocks'],
			};
			
			ApiMethod("admin.bars.save", params, function(data){
				if(data.response != null)
				{
					if(data.response == 1)
					{
						ShowPanel("Сохранение бара прошло успешно", 0);
					}
					else
					{
						ShowPanel("Произошла ошибка при сохранении бара", 1);
					}
				}
				
				HideLoader();
			});
		}
		
		//
		
		var ConfigFiles = <? echo ToJSON($config_files);/*str_replace("'", "\\'", ToJSON($config_files));*/ ?>; //Блин, странно, но работает без FromJSON
		
		var SelectedBar = 0;
		var SelectedBlock = 0;
		var SelectedLink = 0;
		
		//
		
		function ShowAddBlockDialog(bar_id, block_id)
		{
			SelectedBar = bar_id;
			SelectedBlock = block_id;
			ShowWindow("Редактирование блока", Find("add_block_form").innerText);
			//
			if(block_id >= 0)
			{
				var Block = ConfigFiles[bar_id]['data']['blocks'][block_id];
				Find("title").value = Block['title'];
				Find("type").value = Block['type'];
				//Find("markup").value = Block['markup'];
			}
		}
		
		function ApplyBlock()
		{
			SetBlock(SelectedBar, SelectedBlock);
			Write("blocks_" + SelectedBar, GetBlocksMarkup(SelectedBar));
			CloseWindow();
			//
			SaveBarBlocks(SelectedBar);
		}
		
		function SetBlock(bar_id, block_id)
		{
			var Block = FromJSON('<? echo ToJSON(GetDefaultBlock()); ?>');
			if(block_id >= 0)Block = ConfigFiles[bar_id]['data']['blocks'][block_id];
			
			var TargetId = ConfigFiles[bar_id]['data']['blocks'].length;
			if(block_id >= 0)TargetId = block_id;
			
			Block['title'] = Find("title").value;
			Block['type'] = Find("type").value;
			//Block['markup'] = Find("markup").value;
			
			ConfigFiles[bar_id]['data']['blocks'][TargetId] = Block;
		}
		
		function DeleteBlock(bar_id, block_id)
		{
			var Blocks = ConfigFiles[bar_id]['data']['blocks'];
			var NewBlocks = [];
			//
			for(var i = 0; i < Blocks.length - 1; i++)
			{
				if(i < block_id)NewBlocks[i] = Blocks[i];
				else NewBlocks[i] = Blocks[i + 1];
			}
			//
			ConfigFiles[bar_id]['data']['blocks'] = NewBlocks;
			//
			Write("blocks_" + bar_id, GetBlocksMarkup(bar_id));
		}
		
		function MoveUp(bar_id, block_id)
		{
			var Blocks = ConfigFiles[bar_id]['data']['blocks'];
			
			if(block_id > 0)
			{
				var Movable = Blocks[block_id];
				Blocks[block_id] = Blocks[block_id - 1];
				Blocks[block_id - 1] = Movable;
			}
			
			ConfigFiles[bar_id]['data']['blocks'] = Blocks;
			//
			Write("blocks_" + bar_id, GetBlocksMarkup(bar_id));
		}
		
		function MoveDown(bar_id, block_id)
		{
			var Blocks = ConfigFiles[bar_id]['data']['blocks'];
			
			if(block_id < Blocks.length - 1)
			{
				var Movable = Blocks[block_id];
				Blocks[block_id] = Blocks[block_id + 1];
				Blocks[block_id + 1] = Movable;
			}
			
			ConfigFiles[bar_id]['data']['blocks'] = Blocks;
			//
			Write("blocks_" + bar_id, GetBlocksMarkup(bar_id));
		}
		
		//
		
		function ShowAddLinkDialog(bar_id, block_id, link_id)
		{
			SelectedBar = bar_id;
			SelectedBlock = block_id;
			SelectedLink = link_id;
			ShowWindow("Редактирование ссылки", Find("add_link_form").innerText);
			//
			if(link_id >= 0)
			{
				var Link = ConfigFiles[bar_id]['data']['blocks'][block_id]['links'][link_id];
				Find("title").value = Link['title'];
				Find("external").value = Link['external'];
				Find("href").value = Link['href'];
			}
		}
		
		function SetLink(bar_id, block_id, link_id)
		{
			var Link = FromJSON('<? echo ToJSON(GetDefaultLink()); ?>');
			if(link_id >= 0)Link = ConfigFiles[bar_id]['data']['blocks'][block_id]['links'][link_id];
			
			var TargetId = ConfigFiles[bar_id]['data']['blocks'][block_id]['links'].length;
			if(link_id >= 0)TargetId = link_id;
			
			Link['title'] = Find("title").value;
			Link['external'] = Find("external").value;
			Link['href'] = Find("href").value;
			
			ConfigFiles[bar_id]['data']['blocks'][block_id]['links'][TargetId] = Link;
		}
		
		function ApplyLink()
		{
			SetLink(SelectedBar, SelectedBlock, SelectedLink);
			Write("blocks_" + SelectedBar, GetBlocksMarkup(SelectedBar));
			CloseWindow();
		}
		
		function DeleteLink(bar_id, block_id, link_id)
		{
			var Links = ConfigFiles[bar_id]['data']['blocks'][block_id]['links'];
			var NewLinks = [];
			//
			for(var i = 0; i < Links.length - 1; i++)
			{
				if(i < link_id)NewLinks[i] = Links[i];
				else NewLinks[i] = Links[i + 1];
			}
			//
			ConfigFiles[bar_id]['data']['blocks'][block_id]['links'] = NewLinks;
			//
			Write("blocks_" + bar_id, GetBlocksMarkup(bar_id));
		}
		
		function MoveLinkUp(bar_id, block_id, link_id)
		{
			var Links = ConfigFiles[bar_id]['data']['blocks'][block_id]['links'];
			
			if(link_id > 0)
			{
				var Movable = Links[link_id];
				Links[link_id] = Links[link_id - 1];
				Links[link_id - 1] = Movable;
			}
			
			ConfigFiles[bar_id]['data']['blocks'][block_id]['links'] = Links;
			//
			Write("blocks_" + bar_id, GetBlocksMarkup(bar_id));
		}
		
		function MoveLinkDown(bar_id, block_id, link_id)
		{
			var Links = ConfigFiles[bar_id]['data']['blocks'][block_id]['links'];
			
			if(link_id < Links.length - 1)
			{
				var Movable = Links[link_id];
				Links[link_id] = Links[link_id + 1];
				Links[link_id + 1] = Movable;
			}
			
			ConfigFiles[bar_id]['data']['blocks'][block_id]['links'] = Links;
			//
			Write("blocks_" + bar_id, GetBlocksMarkup(bar_id));
		}
		
		//
		
		function ApplyMarkup(bar_id, block_id, markup)
		{
			ConfigFiles[bar_id]['data']['blocks'][block_id]['markup'] = markup;
		}
		
		//
		
		function GetBlocksMarkup(bar_id)
		{
			var m = "";
			
			var BlocksNum = ConfigFiles[bar_id]['data']['blocks'].length;
			for(var i = 0; i < BlocksNum; i++)
			{
				var Block = ConfigFiles[bar_id]['data']['blocks'][i];
				
				//
				m += "<div class='padding n_back2' style='position: relative; border-top-left-radius: 8px; border-top-right-radius: 8px;'>";
				
				m += "<div class='title_text fore3' style='height: 23px;'>" + Block['title'] + "</div>";
				if(Block['type'] == 1)m += "<div class='white_link' style='position: absolute; top: 10px; right: 390px; white-space: nowrap;' onclick='ShowAddLinkDialog(" + bar_id + ", " + i + ", -1);'>Добавить ссылку</div>";
				m += "<div class='white_link' style='position: absolute; top: 10px; right: 310px;' onclick='MoveUp(" + bar_id + ", " + i + ");'>Вверх</div>";
				m += "<div class='white_link' style='position: absolute; top: 10px; right: 230px;' onclick='MoveDown(" + bar_id + ", " + i + ");'>Вниз</div>";
				m += "<div class='white_link' style='position: absolute; top: 10px; right: 120px;' onclick='ShowAddBlockDialog(" + bar_id + ", " + i + ");'>Изменить</div>";
				m += "<div class='white_link' style='position: absolute; top: 10px; right: 15px;' onclick='DeleteBlock(" + bar_id + ", " + i + ");'>Удалить</div>";
				
				m += "</div>";
				
				m += "<div class='padding border2' style='margin-bottom: 10px; padding-left: 30px; padding-right: 30px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;'>";
				
				if(Block['type'] == 0)//Верстка
				{
					m += "<textarea id='markup_" + i + "' value='' onkeyup='ApplyMarkup(" + bar_id + ", " + i + ", this.value);' class='text_input html_input' style='width: 98%; height: 150px;'>" + Block['markup'] + "</textarea>";
				}
				
				if(Block['type'] == 1)//Ссылки
				{
					for(var j = 0; j < Block['links'].length; j++)
					{
						m += "<div class='padding back3' style='position: relative;'>";
						
						m += "<a href='" + Block['links'][j]['href'] + "' target='_blank'><div class='title_text'>" + Block['links'][j]['title'] + "</div></a>";
						m += "<div class='link' style='position: absolute; top: 10px; right: 310px;' onclick='MoveLinkUp(" + bar_id + ", " + i + ", " + j + ");'>Вверх</div>";
						m += "<div class='link' style='position: absolute; top: 10px; right: 230px;' onclick='MoveLinkDown(" + bar_id + ", " + i + ", " + j + ");'>Вниз</div>";
						m += "<div class='link' style='position: absolute; top: 10px; right: 120px;' onclick='ShowAddLinkDialog(" + bar_id + ", " + i + ", " + j + ");'>Изменить</div>";
						m += "<div class='link' style='position: absolute; top: 10px; right: 15px;' onclick='DeleteLink(" + bar_id + ", " + i + ", " + j + ");'>Удалить</div>";
						
						m += "</div>";
					}
					
					if(Block['links'].length == 0)m += "<div class='text'>Ссылок нет...</div>";
				}
				
				m += "</div>";
			}
			
			//
			
			if(BlocksNum == 0)m = "<div class='text padding'>Блоков нет...</div>";
			
			return m;
		}
		
		</script>
		
		<noscript id='add_block_form'>
			
			<div class='title_text'>Заголовок</div>
			<div class='space'></div>
			<input id='title' value='' class='text_input' style='width: 98%;'/>
			<div class='space'></div>
			
			<div class='title_text'>Тип</div>
			<div class='space'></div>
			<select id='type' class='text_input' style='height: 30px; width: 100%;'>
				<option value='0'>Вёрстка</option>
				<option value='1'>Список ссылок</option>
			</select>
			
			<div class='big_space'></div>
			
			<div class='button back4' style='margin: auto;' onclick='ApplyBlock();'>Применить</div>
			
			<div class='big_space'></div>
			
		</noscript>
		
		<noscript id='add_link_form'>
			
			<div class='title_text'>Заголовок ссылки</div>
			<div class='space'></div>
			<input id='title' value='' class='text_input' style='width: 98%;'/>
			<div class='space'></div>
			
			<div class='title_text'>Тип</div>
			<div class='space'></div>
			<select id='external' class='text_input' style='height: 30px; width: 100%;'>
				<option value='0'>Внутренняя</option>
				<option value='1'>Внешняя</option>
			</select>
			<div class='space'></div>
			
			<div class='title_text'>Адрес (href)</div>
			<div class='space'></div>
			<input id='href' value='' class='text_input' style='width: 98%;'/>
			<div class='space'></div>
			
			<div class='big_space'></div>
			
			<div class='button back4' style='margin: auto;' onclick='ApplyLink();'>Применить</div>
			
			<div class='big_space'></div>
			
		</noscript>
		
		<a href='/admin?act=edit_bar' onclick='return NavAsync(this.href, true);' style='display: inline-block;'><div class='button back4'>Создать бар</div></a>
		
		<div class='space'></div>
		
		<div>
		<?
		for($i = 0; $i < sizeof($config_files); $i++)
		{
			$data = $config_files[$i]['data'];
			
			$markup = "
			<div id='bar_".$i."'>
				<div class='padding n_back4' style='position: relative; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
					<a href='/admin?act=edit_bar&bar=".$data['id']."' onclick='return NavAsync(this.href, true);' class='title_text fore3'>".$data['title']." (".$data['id'].")</a>
					<div class='white_link' style='position: absolute; top: 10px; right: 120px;' onclick='ShowAddBlockDialog(".$i.", \"-1\");'>Добавить блок</div>
					<div class='white_link' style='position: absolute; top: 10px; right: 15px;' onclick='ShowDeleteDialog(\"".$data['id']."\", \"bar_".$i."\");'>Удалить</div>
				</div>
				<div class='border4' style='margin-bottom: 10px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;'>
					<div id='blocks_".$i."' class='padding' style='padding-left: 30px; padding-right: 30px;'>
						...
					</div>
					
					<div class='button back4' style='margin: auto;' onclick='SaveBarBlocks(".$i.");'>Сохранить</div>
					<div class='space'></div>
				</div>
			</div>
			
			<script>
			Write('blocks_".$i."', GetBlocksMarkup(".$i."));
			</script>
			";
			
			Draw($markup);
		}
		?>
		</div>
		
		<?
		if(sizeof($config_files) == 0)Draw("<div class='text padding'>Нет баров...</div>");
	}
}

?>
<?

if(true)
{
	if(true)
	{
		$bar = "";
		if(isset($request['bar']))$bar = $request['bar'];
		
		$bar_obj = GetBar($bar);
		
		?>
		
		<script>
		
		function CreateOrEditBar()
		{
			ShowLoader();
			
			var params = {
				id: Find("id").value,
				title: Find("title").value,
			};
			
			ApiMethod("admin.bars.save", params, function(data){
				if(data.response != null)
				{
					if(data.response == 1)
					{
						ShowPanel("Бар сохранен", 0);
						AJAXLayout.Nav(null, "/admin?act=nav");
					}
					else
					{
						ShowPanel("Бар не сохранен", 1);
					}
				}
				
				HideLoader();
			});
		}
		
		</script>
		
		<div style='width: 600px; margin: auto;'>
			
			<div class='big_space'></div>
			
			<div style='display: <? echo isTrue($bar == "", "block", "none"); ?>;'>
				<div class='title_text'>Идентификатор</div>
				<div class='space'></div>
				<input id='id' value='<? echo($bar_obj['id']); ?>' class='text_input' style='width: 98%;'/>
				<div class='space'></div>
			</div>
			
			<div class='title_text'>Название</div>
			<div class='space'></div>
			<input id='title' value='<? echo($bar_obj['title']); ?>' class='text_input' style='width: 98%;'/>
			<div class='space'></div>
			
			<div class='big_space'></div>
			
			<div class='button back4' style='margin: auto;' onclick='CreateOrEditBar();'>Сохранить</div>
			
			<div class='big_space'></div>
		
		</div>
		
		<?
	}
}

?>
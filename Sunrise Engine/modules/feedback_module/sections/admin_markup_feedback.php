<div>
	<script>
	function DeleteMessage(id, block_id)
	{
		ShowLoader();
		
		ApiMethod("feedback.delete", { id: id }, function(data){
			if(data.response != null)
			{
				if(data.response == 1)
				{
					SlideHide(block_id);
					ShowPanel("Удалено", 0);
				}
			}
			
			HideLoader();
		});
	}
	</script>
	<?
	$messages = array();
	
	$query = SQL("SELECT * FROM `feedback_messages` WHERE `status`!='-1' ORDER BY `feedback_messages`.`id` DESC LIMIT 0, 1000000");
	
	$iterations = 0;
	while($row = SQLFetchAssoc($query))
	{
		$messages[$iterations] = $row;
		$iterations++;
	}
	
	for($i = 0; $i < sizeof($messages); $i++)
	{
		$id = $messages[$i]['id'];
		$ip = $messages[$i]['ip'];
		$user_agent = $messages[$i]['user_agent'];
		$date = GetTime($messages[$i]['date']);
		$subject = FilterText($messages[$i]['subject'], "user_text");
		$text = FilterText($messages[$i]['text'], "user_text");
		$status = $messages[$i]['status'];
		
		$ip_array = explode(".", $ip);
		
		$markup = "
		<div id='message_".$id."'>
			<div style='padding: 10px; position: relative; border-left: solid 5px rgb(".$ip_array[0].", ".$ip_array[1].", ".$ip_array[2].");'>
				<div class='title_text'>".$subject."</div>
				<div class='text'>".$text."</div>
				<div class='space'></div>
				<div class='text' title='UA: ".$user_agent."'>".$date." | ".$ip.", ".GetOS($user_agent).", ".GetBrowser($user_agent)."</div>
				<span class='link' style='position: absolute; top: 10px; right: 10px;' onclick='DeleteMessage(".$id.", \"message_".$id."\");'>Удалить</span>
			</div>
			<div class='divider'></div>
		</div>
		";
		
		Draw($markup);
	}
	
	if(sizeof($messages) == 0)Draw("<div class='text'>Отзывов нет...</div>");
	?>
</div>
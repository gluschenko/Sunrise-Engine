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
					ShowPanel("Архивировано", 0);
				}
			}
			
			HideLoader();
		});
	}
	
	function RestoreMessage(id, block_id)
	{
		ShowLoader();
		
		ApiMethod("feedback.restore", { id: id }, function(data){
			if(data.response != null)
			{
				if(data.response == 1)
				{
					SlideHide(block_id);
					ShowPanel("Восстановлено", 0);
				}
			}
			
			HideLoader();
		});
	}
	</script>
	<?
	$messages = array();
	$archived_messages = array();
	
	$query = SQL("SELECT * FROM `feedback_messages` WHERE 1 ORDER BY `feedback_messages`.`id` DESC");
	
	while($row = SQLFetchAssoc($query))
	{
		if($row['status'] == 0) $messages[] = $row;
		else $archived_messages[] = $row;
	}
	
	for($i = 0; $i < sizeof($messages); $i++)
	{
		DrawFeedbackMessage($messages[$i]);
	}
	
	if(sizeof($messages) == 0) Draw("
		<div style='height: 100px;'></div>
		<div class='title_text inner_center'>Новых отзывов нет</div>
		<div style='height: 100px;'></div>
	");
	
	if(sizeof($archived_messages) > 0)
	{
		Draw("
		<div class='big_space'></div>
		<div class='header_underline title_text'>Архив обращений</div>
		<div class='divider'></div>
		<div class='space'></div>
		");
		
		for($i = 0; $i < sizeof($archived_messages); $i++)
		{
			DrawFeedbackMessage($archived_messages[$i]);
		}
	}
	?>
</div>

<?php

function DrawFeedbackMessage($message)
{
	$id = $message['id'];
	$ip = $message['ip'];
	$date = GetTime($message['date']);
	$user_agent = FilterText($message['user_agent'], "user_text");
	$name = FilterText($message['name'], "user_text");
	$email = StripHTML($message['email']);
	$subject = FilterText($message['subject'], "user_text");
	$text = FilterText($message['text'], "user_text");
	$status = $message['status'];
	
	$ip_array = explode(".", $ip);
	
	$action_text = "Архивировать";
	$action_event = "DeleteMessage(".$id.", \"message_".$id."\");";
	
	if($status != 0)
	{
		$action_text = "Восстановить";
		$action_event = "RestoreMessage(".$id.", \"message_".$id."\");";
	}
	
	$markup = "
	<div id='message_".$id."'>
		<div style='padding: 10px; position: relative; border-left: solid 5px rgb(".$ip_array[0].", ".$ip_array[1].", ".$ip_array[2].");'>
			<div class='title_text' style='margin-bottom: 10px;'>".$subject."</div>
			<div class='text'>".$text."</div>
			<div class='space'></div>
			<div class='small_text'>".$name." | <a class='link' target='_blank' href='mailto://".$email."'>".$email."</a> | ".$date."</div>
			<span class='small_text fore6' style='position: absolute; bottom: 10px; right: 10px;' title='User agent: ".$user_agent."'>ID: ".$id.", ".$ip.", ".GetOS($user_agent).", ".GetBrowser($user_agent)."</span>
			
			<span class='link' style='position: absolute; top: 10px; right: 10px;' onclick='".$action_event."'>".$action_text."</span>
		</div>
		<div class='divider'></div>
	</div>
	";
	
	Draw($markup);
}

?>
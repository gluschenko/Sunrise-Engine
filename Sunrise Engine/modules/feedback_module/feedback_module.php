<?
// 

AddTable("feedback_messages", array(
	TableField("id", "int(11)"),
	TableField("date", "int(11)"),
	TableField("ip", "text"),
	TableField("user_agent", "text"),
	TableField("subject", "text"),
	TableField("text", "text"),
	TableField("status", "int(11)"),
));

//

$cfg = GetConfig();

AddSection(array(
	"type" => 0,
	"name" => "feedback",
	"header" => "Обратная связь",
	"title" => "Обратная связь - {site_name}",
	"layout" => 0,
	"permission" => 0,
	"keywords" => "отзывы, обратная связь, вопросы, поддержка, feedback, guest book",
	"description" => "Страница обратной связи.",
	"section_dir" => "/modules/feedback_module/sections",
));
//
$sections_dir = $cfg['root']."/modules/feedback_module/sections/";

AddAdminSection("feedback", "Обратная связь", $sections_dir."admin_markup_feedback.php");
//
AddAdminLink("/admin?act=feedback", "Обратная связь (".GetFeedbackMessagesCount().")", 0);
//

AddMethod("feedback.send", function($params){
	$subject = Security::String(FilterText($params['subject'], "database_text"));
	$text = Security::String(FilterText($params['text'], "database_text"));
	
	if(CreateFeedbackMessage($subject, $text))
	{
		LogAction("Получен отзыв", 0);
		return 1;
	}
	
	return 0;
});

AddMethod("feedback.delete", function($params){
	if(isAdminLogged())
	{
		$id = $params['id'];
		
		if(DeleteFeedbackMessage($id))
		{
			LogAction("Удалён отзыв (".$id.")", 0);
			return 1;
		}
		
		return 0;
	}
	
	return 0;
});

//

function CreateFeedbackMessage($subject, $text)
{
	$newid = GetNewId("feedback_messages", "id");
	//
	$time = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	
	$query = SQL("INSERT INTO `feedback_messages`(`id`, `date`, `ip`, `user_agent`, `subject`, `text`, `status`) 
		VALUES ('$newid','$time','$ip','$user_agent','$subject', '$text', '0')");
	//
	return true;
}

function DeleteFeedbackMessage($id)
{
	$query = SQL("UPDATE `feedback_messages` SET status = '-1' WHERE id = '$id'");
	
	return true;
}

function GetFeedbackMessagesCount()
{
	$query = SQL("SELECT COUNT(*) FROM `feedback_messages` WHERE `status`='0'");
	$row = SQLFetchArray($query);
	return $row[0];
}

//

OnLoad(function(){
	InitFeedbackWidget();
});

function InitFeedbackWidget()
{
	AddTemplate("feedback_widget", "
	<!--Feedback widget-->
	<div class='padding'>
		".GetFeedbackScript()."
		
		<noscript id='feedback_markup'>
		".GetFeedbackMarkup()."
		</noscript>
		
		<div id='feedback_button' class='small_button back3 fore0 border0 at_center' onclick='ShowWindow(\"Обратная связь\", Find(\"feedback_markup\").innerText, 2, 0, 700);'>Нашли ошибку?</div>
	</div>
	");
}

//

function GetFeedbackScript()
{
	return "
	<script>

	function SendFeedback()
	{
		var params = {
			subject: Find('subject').value,
			text: Find('text').value,
		};
		
		if(params.text != '')
		{
			ShowLoader();
			
			ApiMethod('feedback.send', params, function(data){
				if(data.response != null)
				{
					if(data.response == 1)
					{
						SlideHide('feedback_form');
						SlideShow('feedback_result');
						ShowPanel('Отправлено', 0);
					}
				}
				
				HideLoader();
			});
		}
		else
		{
			ShowPanel('Заполните поле сообщения', 1);
		}
	}

	</script>
	";
}

function GetFeedbackMarkup()
{
	return "
	<div id='feedback_form' style='padding-left: 40px; padding-right: 40px;'>
		<div class='big_space'></div>
		<input id='subject' placeholder='Тема обращения' value='' class='text_input big_input inner_center' style='width: 98%;'/>
		<div class='big_space'></div>
		<textarea id='text' placeholder='Ваш отзыв, пожелание или жалоба' value='' class='text_input' style='width: 98%; height: 160px;'></textarea>
		<div class='big_space'></div>
		<div class='button back4' style='margin: auto;' onclick='SendFeedback();'>Отправить</div>
		<div class='big_space'></div>
	</div>
	<div id='feedback_result' style='display: none;'>
		<div class='title_text inner_center' style='padding: 150px;'>Ваш отзыв принят</div>
	</div>
	";
}

?>
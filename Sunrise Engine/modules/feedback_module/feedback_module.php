<?
// 

AddTable("feedback_messages", array(
	TableField("id", "int(11)"),
	TableField("date", "int(11)"),
	TableField("ip", "text"),
	TableField("user_agent", "text"),
	TableField("name", "text"),
	TableField("email", "text"),
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
$sections_dir = dirname(__FILE__)."/sections";

AddAdminSection("feedback", "Обратная связь", $sections_dir."/admin_markup_feedback.php");
//
AddAdminLink("/admin?act=feedback", "Обратная связь (".GetFeedbackMessagesCount().")", 0);
//

AddMethod("feedback.send", function($params){
	$name = Security::String($params['name']);
	$email = Security::String($params['email']);
	$subject = Security::String($params['subject']);
	$text = Security::String($params['text']);
	
	if(CreateFeedbackMessage($name, $email, $subject, $text))
	{
		LogAction("Получено обращение от ".$email." (".$name.")", 0);
		return 1;
	}
	
	return 0;
});

AddMethod("feedback.delete", function($params){
	if(isAdminLogged())
	{
		$id = Security::Int($params['id']);
		if(DeleteFeedbackMessage($id))
		{
			LogAction("Архивировано обращение (ID ".$id.")", 0);
			return 1;
		}
	}
	return 0;
});

AddMethod("feedback.restore", function($params){
	if(isAdminLogged())
	{
		$id = Security::Int($params['id']);
		if(RestoreFeedbackMessage($id))
		{
			LogAction("Восстановлено обращение (ID ".$id.")", 0);
			return 1;
		}
	}
	return 0;
});

//

function CreateFeedbackMessage($name, $email, $subject, $text)
{
	$newid = GetNewId("feedback_messages", "id");
	//
	$time = time();
	$ip = Security::String($_SERVER['REMOTE_ADDR']);
	$user_agent = Security::String($_SERVER['HTTP_USER_AGENT']);
	
	$is_valid = $name != "" && $email != "" && $text != "";
	
	if($is_valid)
	{
		$query = SQL("INSERT INTO `feedback_messages`(`id`, `date`, `ip`, `user_agent`, `name`, `email`, `subject`, `text`, `status`) 
			VALUES ('$newid', '$time', '$ip', '$user_agent', '$name', '$email', '$subject', '$text', '0')");
		//
		return true;
	}
	return false;
}

function DeleteFeedbackMessage($id)
{
	$query = SQL("UPDATE `feedback_messages` SET status = '-1' WHERE id = '$id'");
	return true;
}

function RestoreFeedbackMessage($id)
{
	$query = SQL("UPDATE `feedback_messages` SET status = '0' WHERE id = '$id'");
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
		
		<noscript id='feedback_markup_{random}'>
		".GetFeedbackMarkup()."
		</noscript>
		
		<div id='feedback_button' class='small_button back3 fore0 border0 at_center' onclick='ShowWindow(\"Обратная связь\", Find(\"feedback_markup_{random}\").innerText, 2, 0, 700);'>Нашли ошибку?</div>
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
			name: Find('user_name').value,
			email: Find('email').value,
			subject: Find('subject').value,
			text: Find('text').value,
		};
		
		var Confirmed = Find('152-FL').checked;
		
		if(params.text != '' && params.name != '' && params.email != '')
		{
			if(Confirmed)
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
						else
						{
							ShowPanel('Некорректное заполнение полей', 1);
						}
					}
					
					HideLoader();
				});
			}
			else
			{
				ShowPanel('Вы не дали согласие на обработку', 1);
			}
		}
		else
		{
			ShowPanel('Заполните обязательные поля *', 1);
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
		<input id='user_name' placeholder='Ваше имя (ФИО) *' value='' class='text_input big_input inner_center' style='width: 98%;'/>
		<div class='space'></div>
		<input id='email' placeholder='Контактный e-mail *' value='' class='text_input big_input inner_center' style='width: 98%;'/>
		<div class='space'></div>
		<input id='subject' placeholder='Тема обращения' value='' class='text_input big_input inner_center' style='width: 98%;'/>
		<div class='space'></div>
		<textarea id='text' placeholder='Текст обращения *' value='' class='text_input' style='width: 98%; height: 160px;'></textarea>
		<div class='space'></div>
		<div>
			<input type='checkbox' id='152-FL' value='checkbox_1'>
			<label for='152-FL' class='small_text'>
				Нажимая кнопку «Отправить», я принимаю условия Пользовательского соглашения и даю своё согласие на обработку моих персональных данных, в соответствии с Федеральным законом от 27.07.2006 года №152-ФЗ «О персональных данных», на условиях и для целей, определенных Политикой конфиденциальности
			</label>
		</div>
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
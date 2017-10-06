<?php
// Модуль для работы с событиями (13-12-2016)

// БД
AddTable("module_events", array(
	TableField("id", "int(11)"),
	TableField("date", "int(11)"),
	TableField("day", "int(11)"),
	TableField("month", "int(11)"),
	TableField("year", "int(11)"),
	TableField("text", "text"),
	TableField("link", "text"),
	TableField("status", "int(11)"),
));

// Страницы
AddSection(array(
	"type" => 0,
	"name" => "events",
	"header" => "События",
	"title" => "События - {site_name}",
	"layout" => 0,
	"permission" => 0,
	"keywords" => "события, праздники, мероприятия",
	"description" => "Календарь событий.",
	"section_dir" => "/modules/news/sections",
));

$cfg = GetConfig();
$sections_dir = dirname(__FILE__)."/sections";

AddAdminSection("events", "События", $sections_dir."/admin_markup_events.php");
//
AddAdminLink("/admin?act=events", "События", 0);
//
OnLoad(function(){
	InitEventsWidget();
});

function InitEventsWidget()
{
	$events = GetLastSocialEvents(4);

	$m = "";
	
	$m .= "<div class='inner_center'>";
	
	for($i = 0; $i < sizeof($events); $i++)
	{
		$m .= GetEventThumdMarkup($events[$i]);
	}
	
	if(sizeof($events) == 0)$m .= "<div class='text'>Событий нет...</div>";
	
	$m .= "</div>";
	
	$m .= GetEventThumdStyle();
	
	AddTemplate("events_widget", "
	<!--Events widget-->
	<div>
		".$m."
	</div>
	");
}

function GetEventThumdMarkup($event)
{
	$id = $event['id'];
	$text = $event['text'];
	$link = $event['link'];
	$day = $event['day'];
	$month = $event['month'];
	$year = $event['year'];
	
	$month_name = MonthByNumber($month);
	$month_name = ToUpperCase($month_name[1]);
	
	$link_nav = "";
	if($link != "")$link_nav = "href='".$link."' target='_blank'";
	
	return "
	<a ".$link_nav." class='events_preview back3 padding'>
		<div class='events_preview_left_column'>
			<div class='events_thumb_date n_back4 fore3'>
				<div class='text bold fore3' style='padding-top: 7px;'>".$day."</div>
				<div class='mini_text fore3'>".$month_name."</div>
			</div>
			<div style='height: 5px;'></div>
			<div class='inner_center mini_text'>".$year."</div>
		</div>
		
		<div class='events_preview_right_column'>
			<div style='margin-top: 4px;'>
				<div class='small_text bold'>".CropText($text, 70)."</div>
				<div class='small_text bold' style='color: #888; margin-top: 5px;'>".CropText($link, 50)."</div>
			</div>
		</div>
	</a>
	";
}

function GetEventThumdStyle()
{
	return "
	<style>
	.events_thumb_date{
		width: 50px;
		height: 50px;
		border-radius: 8px;
		text-align: center;
	}
	.events_preview{
		width: 330px;
		display: inline-block;
		border-radius: 5px;
	}
	
	.events_preview_left_column{
		width: 50px;
		float: left;
	}
	
	.events_preview_right_column{
		width: 270px;
		float: right;
		text-align: left;
	}
	</style>
	";
}
//
AddMethod("events.create", function($params){
	if(isAdminLogged())
	{
		$day = FilterText($params['day'], "database_text");
		$month = FilterText($params['month'], "database_text");
		$year = FilterText($params['year'], "database_text");
		$text = FilterText($params['text'], "database_text");
		$link = FilterText($params['link'], "database_text");
		
		if(CreateSocialEvent($text, $link, $day, $month, $year))
		{
			LogAction("Добавлено событие: ".$text, 0);
			return 1;
		}
	}
	return 0;
});

AddMethod("events.edit", function($params){
	if(isAdminLogged())
	{
		$id = FilterText($params['id'], "database_text");
		$day = FilterText($params['day'], "database_text");
		$month = FilterText($params['month'], "database_text");
		$year = FilterText($params['year'], "database_text");
		$text = FilterText($params['text'], "database_text");
		$link = FilterText($params['link'], "database_text");
		
		if(EditSocialEvent($id, $text, $link, $day, $month, $year))
		{
			LogAction("Изменено событие: ".$text, 0);
			return 1;
		}
	}
	return 0;
});

AddMethod("events.delete", function($params){
	if(isAdminLogged())
	{
		$id = FilterText($params['id'], "database_text");
		
		if(DeleteSocialEvent($id))
		{
			return 1;
		}
	}
	return 0;
});

//

function CreateSocialEvent($text, $link, $day, $month, $year)
{
	$newid = GetNewId("module_events", "id");
	//
	$time = time();
	
	$query = SQL("INSERT INTO `module_events`(`id`, `date`, `day`, `month`, `year`, `text`, `link`, `status`) 
		VALUES ('$newid','$time','$day','$month','$year', '$text', '$link', '0')");
	//
	return true;
}

function EditSocialEvent($id, $text, $link, $day, $month, $year)
{
	$time = time();
	$query = SQL("UPDATE `module_events` SET text = '$text', link = '$link', day = '$day', month = '$month', year = '$year', date = '$time' WHERE id = '$id'");
	//
	return true;
}

function DeleteSocialEvent($id)
{
	$query = SQL("UPDATE `module_events` SET status = '-1' WHERE id = '$id'");
	//
	return true;
}

function GetLastSocialEvents($number = 4)
{
	$current_day = GetDay(time());
	$current_month = GetMonth(time());
	$current_year = GetYear(time());
	
	$result = array();
	$query = SQL("SELECT * FROM `module_events` WHERE `day` >= $current_day AND `month` >= $current_month AND `year` >= $current_year AND `status` = '0' ORDER BY day, month, year ASC LIMIT ".$number.";");
	while($row = SQLFetchAssoc($query))
	{
		$result[] = $row;
	}
	
	if(sizeof($result) < $number)
	{
		$result = array();
		
		$query = SQL("SELECT * FROM `module_events` WHERE `status` = '0' ORDER BY id DESC LIMIT ".$number.";");
		while($row = SQLFetchAssoc($query))
		{
			$result[] = $row;
		}
	}
	
	return $result;
}

function GetSocialEvents()
{
	$result = array();
	$query = SQL("SELECT * FROM `module_events` WHERE `status` = '0' ORDER BY id DESC;");
	while($row = SQLFetchAssoc($query))
	{
		$result[] = $row;
	}
	
	return $result;
}

function GetSocialEventsByDate($dates)
{
	$result = array();
	$where = array();
	
	for($i = 0; $i < sizeof($dates); $i++)
	{
		$where[] = "(day=".$dates[$i]['day']." AND month=".$dates[$i]['month']." AND year=".$dates[$i]['year']." AND `status` = '0')";
	}
	
	$query = SQL("SELECT * FROM `module_events` WHERE ".implode(" OR ", $where)." ORDER BY id DESC;");
	while($row = SQLFetchAssoc($query))
	{
		$result[] = $row;
	}
	
	return $result;
}

?>
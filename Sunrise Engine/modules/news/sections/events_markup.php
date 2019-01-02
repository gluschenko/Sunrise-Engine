<?php

$current_day = GetDay(time());
$current_month = GetMonth(time());
$current_year = GetYear(time());
$current_day_of_week = GetDayOfWeek(time());

$months = GetMonths(time(), -1);
$days_index = array();

$start_time = 0;

for($i = 0; $i < sizeof($months); $i++)
{
	$month_unix = $months[$i]['unix'];
	if($current_month == $months[$i]['number'])
	{
		$start_time = $month_unix + 3600;
		$offset = GetDayOfWeek($start_time);
		$start_time -= $offset * (24 * 3600);
		
		break;
	}
}

//

$week_days = array("ПН", "ВТ", "СР", "ЧТ", "ПТ", "СБ", "ВС");
$lines = 6;


for($j = 0; $j < $lines; $j++)
{
	for($d = 0; $d < sizeof($week_days); $d++)
	{
		$n = $j * sizeof($week_days) + $d;
		$day_time = $start_time + ($n * 24 * 3600);
		
		$days_index[] = array(
			"day" => GetDay($day_time),
			"month" => GetMonth($day_time),
			"year" => GetYear($day_time),
			"day_of_week" => GetDayOfWeek($day_time),
		);
	}
}

$events_index = array();
$events = GetSocialEventsByDate($days_index);
for($i = 0; $i < sizeof($events); $i++)
{
	$element_name = $events[$i]['day']."_".$events[$i]['month']."_".$events[$i]['year'];
	
	if(!isset($element_name))$events_index[$element_name] = array();
	
	$events_index[$element_name][] = $events[$i];
}

//

$markup = "";
$markup .= "<table class='events_table' style='width: 100%;'>";

$markup .= "<tr>";
for($d = 0; $d < sizeof($week_days); $d++)
{
	$markup .= "
	<td class='n_back4 text fore3 inner_center padding'>
		".$week_days[$d]."
	</td>
	";
}
$markup .= "</tr>";

for($j = 0; $j < $lines; $j++)
{
	$markup .= "<tr>";
	
	for($d = 0; $d < sizeof($week_days); $d++)
	{
		$n = $j * sizeof($week_days) + $d;
		
		$day_obj = $days_index[$n];
		
		$day = $day_obj['day'];
		$month = $day_obj['month'];
		$year = $day_obj['year'];
		
		$element_name = $day."_".$month."_".$year;
		$has_events = isset($events_index[$element_name]);
		
		$month_name = MonthByNumber($month);
		$month_name = $month_name[1];
		
		$is_current_day = $day == $current_day && $month == $current_month && $year == $current_year;
		
		$events_thumbs_markup = "";
		$events_click_event = "";
		$events_counter = "";
		if($has_events)
		{
			$events_counter = sizeof($events_index[$element_name]);
			
			$events_thumbs_markup .= "<div class='inner_center'>";
			for($i = 0; $i < sizeof($events_index[$element_name]); $i++)
			{
				$events_thumbs_markup .= GetEventThumdMarkup($events_index[$element_name][$i]);
			}
			$events_thumbs_markup .= "</div>";
			
			$events_click_event = "onclick='ShowWindow(\"События\", Find(\"events_".$element_name."\").innerText, 0, 0, 400);'";
		}
		
		
		
		$text_style = "fore0";
		$back_style = "back3";
		
		if($d == 5 || $d == 6) // Выходные
		{
			$text_style = "fore0";
			$back_style = "weekend_back";
		}
		
		if($is_current_day) // Текущий день
		{
			$text_style = "fore3";
			$back_style = "back2";
		}
		
		if($has_events) // Событие
		{
			$text_style = "fore3";
			$back_style = "back4";
		}
		
		$day_markup = "
		<noscript id='events_".$element_name."'>
			".$events_thumbs_markup."
		</noscript>
		<div class='month_day' ".$events_click_event.">
			<div class='logo_text inner_center ".$text_style."'>".$day."</div>
			<div class='space'></div>
			<div class='small_text inner_center ".$text_style."'>".$month_name."</div>
			<div class='mini_text month_day_year'>".$year."</div>
			<div class='events_counter small_text n_back3 fore4'>".$events_counter."</div>
		</div>
		";
		
		$markup .= "
		<td class='month_day_cell ".$back_style."'>
			".$day_markup."
		</td>
		";
	}
	
	$markup .= "</tr>";
}

$markup .= "</table>";

Draw($markup);

//

Draw(GetEventThumdStyle());

?>

<div>
	<div class='space'></div>
	<table class='events_table'>
		<tr>
			<td class='padding'><div class='hint_thumb n_back2'></div><td>
			<td class='padding small_text' style='vertical-align: middle;'>Текущий день<td>
			<td class='padding'><div class='hint_thumb n_back4'></div><td>
			<td class='padding small_text' style='vertical-align: middle;'>Мероприятия<td>
			<td class='padding'><div class='hint_thumb weekend_back'></div><td>
			<td class='padding small_text' style='vertical-align: middle;'>Выходные<td>
		</tr>
	</table>
</div>

<style>
@media screen and (max-width: 600px) {
    .events_table{
        zoom: 0.5;
    }
}

.month_day
{
	padding-top: 20px;
	padding-bottom: 20px;
	position: relative;
}

.month_day_year
{
	right: 3px;
	bottom: 3px;
	position: absolute;
	color: #ccc;
}

.month_day_cell
{
	border: solid #eee 1px;
}

.hint_thumb
{
	height: 30px;
	width: 30px;
	border-radius: 4px;
}

.weekend_back
{
	background-color: #eaeaea;
}

.events_counter
{
	padding-right: 6px;
	padding-left: 6px;
	border-radius: 20px;
	line-height: 20px;
	display: inline-block;
	left: 5px;
	bottom: 5px;
	position: absolute;
}
</style>
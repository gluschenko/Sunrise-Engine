<?php

$current_day = GetDay(time());
$current_month = GetMonth(time());
$current_year = GetYear(time());

?>

<script>
var EVENT_ID = 0;
function ShowEventWindow(id)
{
	if(id == null)EVENT_ID = 0;
	else EVENT_ID = id;
	
	ShowWindow("Событие", Find("add_event_window").innerText, 0, 0, 600);
}

function FillEventWindow(text, link, day, month, year)
{
	Find('event_text').value = text;
	Find('event_link').value = link;
	Find('event_day').value = day;
	Find('event_month').value = month;
	Find('event_year').value = year;
}

function CreateEvent()
{
	var params = {
		text: Find('event_text').value,
		link: Find('event_link').value,
		day: Find('event_day').value,
		month: Find('event_month').value,
		year: Find('event_year').value,
	};
	
	var method = "events.create";
	if(EVENT_ID != 0)
	{
		params.id = EVENT_ID;
		method = "events.edit";
	}
	
	if(params.text != '')
	{
		ApiMethod(method, params, function(data){
			if(data.response != null)
			{
				if(data.response == 1)
				{
					CloseWindow();
					ReloadAsync();
					ShowPanel('Отправлено', 0);
				}
			}
		});
	}
	else
	{
		ShowPanel('Заполните поле сообщения', 1);
	}
}

function DeleteEvent(id)
{
	ApiMethod('events.delete', { id: id }, function(data){
		if(data.response != null)
		{
			if(data.response == 1)
			{
				Hide("event_" + id);
				ShowPanel('Удалено', 0);
			}
		}
	});
}

function DeleteEventDialog(id)
{
	DeleteCallback = function(){
		DeleteEvent(id);
		CloseWindow();
	};
	
	ShowDialog("Удаление события", "Событие будет удалено", "Удалить", "DeleteCallback();");
}
</script>

<noscript id='add_event_window'>
	<div class='inner_center'>
		<div class='text'>Дата</div>
		<div class='space'></div>
		<select id='event_day' class='text_input big_input' style='height: 40px; width: 30%; display: inline-block;'>
			<?php
			for($d = 1; $d <= 31; $d++)
			{
				echo("<option value='".$d."' ".($d == $current_day ? "selected" : "").">".$d."</option>");
			}
			?>
		</select>
		<select id='event_month' class='text_input big_input' style='height: 40px; width: 30%; display: inline-block;'>
			<?php
			for($m = 1; $m <= 12; $m++)
			{
				$month_name = MonthByNumber($m);
				$month_name = $month_name[0];
				
				echo("<option value='".$m."' ".($m == $current_month ? "selected" : "").">".$month_name."</option>");
			}
			?>
		</select>
		<select id='event_year' class='text_input big_input' style='height: 40px; width: 30%; display: inline-block;'>
			<?php
			for($y = $current_year; $y <= $current_year + 2; $y++)
			{
				echo("<option value='".$y."' ".($y == $current_year ? "selected" : "").">".$y."</option>");
			}
			?>
		</select>
	</div>
	
	<div class='space'></div>
	<div class='text'>Название</div>
	<div class='space'></div>
	
	<input id='event_text' value='' class='text_input big_input' style='width: 97%;'>
	
	<div class='space'></div>
	<div class='text'>Ссылка (внутренняя)</div>
	<div class='space'></div>
	
	<input id='event_link' value='' class='text_input' style='width: 97%;'>
	
	<div class='space'></div>
	<div class='button at_center' onclick='CreateEvent();'>Добавить</div>
</noscript>

<div class='button back4' style='display: inline-block;' onclick='ShowEventWindow();'>Добавить событие</div>

<div class='space'></div>
<div class='divider'></div>
<div class='space'></div>

<table style='width: 100%;'>
	<tr class='table_header'>
		<td class='table_column'><div class='text fore3'>#</div></td>
		<td class='table_column'><div class='text fore3'>Дата</div></td>
		<td class='table_column'><div class='text fore3'>Название</div></td>
		<td class='table_column'><div class='text fore3'>Ссылка</div></td>
		<td class='table_column'><div class='text fore3'>Дата изменения</div></td>
		<td class='table_column'></td>
		<td class='table_column'></td>
	</tr>
	
	<?php
	$events = GetSocialEvents();
	
	for($i = 0; $i < sizeof($events); $i++)
	{
		$month_name = MonthByNumber($events[$i]['month']);
		$month_name = ToLowerCase($month_name[0]);
		
		$m = "
		<tr id='event_".$events[$i]['id']."' class='back3'>
			<td class='table_column text'>".($i + 1).".</td>
			<td class='table_column text'>".$events[$i]['day']." ".$month_name." ".$events[$i]['year']."</td>
			<td class='table_column text'>".CropText($events[$i]['text'], 40)."</td>
			<td class='table_column text'><a class='link pointer' href='".$events[$i]['link']."' target='_blank'>".$events[$i]['link']."</a></td>
			<td class='table_column text'>".GetTime($events[$i]['date'])."</td>
			<td class='table_column text'><div class='link' style='text-align: center;' onclick='ShowEventWindow(".$events[$i]['id']."); FillEventWindow(\"".$events[$i]['text']."\", \"".$events[$i]['link']."\", ".$events[$i]['day'].", ".$events[$i]['month'].", ".$events[$i]['year'].");'>Изменить</div></td>
			<td class='table_column text'><div class='link' style='text-align: center;' onclick='DeleteEventDialog(".$events[$i]['id'].");'>Удалить</div></td>
		</tr>
		";
		
		Draw($m);
	}
	?>
</table>

<?php

if(sizeof($events) == 0)echo("Нет событий...");

?>
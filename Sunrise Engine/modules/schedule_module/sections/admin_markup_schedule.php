<div>
	<div class='button back4' style='display: inline-block;' onclick='ShowNewChangeWindow();'>Добавить расписание</div>
	<div class='button back4' style='display: inline-block;' onclick='ShowNewGroupWindow();'>Добавить группу</div>
</div>
<div class='space'></div>
<div class='divider'></div>

<div class='space'></div>
<div class='title_text'>Текущее расписание</div>
<div class='space'></div>

<table style='width: 100%;'>
	<tr class='table_header'>
		<td class='table_column' style='width: 40px;'><div class='text fore3'>#</div></td>
		<td class='table_column'><div class='text fore3'>Учебный день</div></td>
		<td class='table_column'><div class='text fore3'>День недели</div></td>
		<td class='table_column'><div class='text fore3'>Публикация</div></td>
		<td class='table_column'></td>
		<td class='table_column'></td>
	</tr>
	
	<?
	$changes = array();
	
	$query = SQL("SELECT * FROM `schedule_changes` WHERE 1 ORDER BY `id` DESC LIMIT 10");
	
	while($row = SQLFetchAssoc($query))
	{
		$changes[] = $row;
	}
	
	for($i = 0; $i < sizeof($changes); $i++)
	{
		$id = $changes[$i]['id'];
		$created = GetTime($changes[$i]['created']);
		$year = $changes[$i]['year'];
		$month = $changes[$i]['month'];
		$day = $changes[$i]['day'];
		$schedule = $changes[$i]['schedule'];
		$status = $changes[$i]['status'];
		
		//
		
		$delete_hide = "";
		$restore_hide = "display: none;";
		
		if($status != 0)
		{
			$delete_hide = "display: none;";
			$restore_hide = "";
		}
		
		//
		$wnames = WeekDayByNumber(GetDayOfWeek(strtotime($day.".".$month.".".$year)));
		$mnames = MonthByNumber($month);
		//
		$markup = "
		<tr class='back3' id='group_".$id."'>
			<td class='table_column'><div class='text'>".($i + 1).".</div></td>
			<td class='table_column'><div class='text'>".$day." ".$mnames[1]." ".$year."</div></td>
			<td class='table_column'><div class='text'>".$wnames[0]."</div></td>
			<td class='table_column'><div class='text'>".$created."</div></td>
			<td class='table_column'>
				<div class='link' style='text-align: center;' onclick='ShowNewChangeWindow(".$day.", ".$month.", ".$year.", ".$schedule.");'>Изменить</div>
			</td>
			<td class='table_column'>
				<div id='delete_change_".$id."' class='link' style='text-align: center; ".$delete_hide."' onclick='DeleteChange(".$id.");'>Удалить</div>
				<div id='restore_change_".$id."' class='link' style='text-align: center; ".$restore_hide."' onclick='RestoreChange(".$id.");'>Восстановить</div>
			</td>
		</tr>
		";
		
		Draw($markup);
	}
	
	?>

</table>

<?

if(sizeof($changes) == 0)Draw("<div class='text padding'>Расписания нет...</div>");

?>

<!---->

<div class='big_space'></div>
<div class='title_text'>Основное расписание</div>
<div class='space'></div>

<table style='width: 100%;'>
	<tr class='table_header'>
		<td class='table_column' style='width: 40px;'><div class='text fore3'>#</div></td>
		<td class='table_column'><div class='text fore3'>Группа</div></td>
		<td class='table_column'><div class='text fore3'>Учебный год</div></td>
		<td class='table_column'><div class='text fore3'>Дата создания</div></td>
		<td class='table_column' style='width: 200px;'></td>
		<td class='table_column' style='width: 200px;'></td>
		<td class='table_column' style='width: 200px;'></td>
	</tr>
	
	<?
	$groups = array();
	
	$query = SQL("SELECT * FROM `schedule_groups` WHERE `status`='0' ORDER BY `status` DESC LIMIT 0, 1000000");
	
	while($row = SQLFetchAssoc($query))
	{
		$groups[] = $row;
	}
	
	for($i = 0; $i < sizeof($groups); $i++)
	{
		$id = $groups[$i]['id'];
		$name = $groups[$i]['name'];
		$created = GetTime($groups[$i]['created']);
		$year = $groups[$i]['year'];
		$schedule = $groups[$i]['standard_schedule'];
		$thumbnail = $groups[$i]['thumbnail'];
		$status = $groups[$i]['status'];
		
		//
		
		$delete_hide = "";
		$restore_hide = "display: none;";
		
		if($status != 0)
		{
			$delete_hide = "display: none;";
			$restore_hide = "";
		}
		
		//
		
		$markup = "
		<tr class='back3' id='group_".$id."'>
			<td class='table_column'><div class='text'>".($i + 1).".</div></td>
			<td class='table_column'><div class='text'>".$name."</div></td>
			<td class='table_column'><div class='text'>".$year."-".($year + 1)."</div></td>
			<td class='table_column'><div class='text'>".$created."</div></td>
			<td class='table_column'><div class='link' style='text-align: center;' onclick='ShowSchedule(\"".$name."\", ".$schedule.");'>Просмотр</div></td>
			<td class='table_column'>
				<div class='link' style='text-align: center;' onclick='CurrentGroupID = ".$id."; ShowNewGroupWindow(\"".$name."\", \"".$year."\", \"".$thumbnail."\", ".$schedule.");'>Изменить</div>
			</td>
			<td class='table_column'>
				<div id='delete_group_".$id."' class='link' style='text-align: center; ".$delete_hide."' onclick='DeleteGroup(".$id.");'>Удалить</div>
				<div id='restore_group_".$id."' class='link' style='text-align: center; ".$restore_hide."' onclick='RestoreGroup(".$id.");'>Восстановить</div>
			</td>
		</tr>
		";
		
		Draw($markup);
	}
	
	?>

</table>

<?

if(sizeof($groups) == 0)Draw("<div class='text padding'>Групп нет...</div>");

?>

<!---->

<script>
var CurrentGroupID = 0;
</script>

<noscript id='new_group_window'>

	<div id='schedule_hint_list'>
		<div class='hint_list_body'>
			<div class='hint_list_header fore3 text' onclick='SlideToggle("schedule_hint_list_wrap");'>Подсказки <span id='hint_list_counter' class='fore2'></span></div>
			<div id='schedule_hint_list_wrap' class='hint_list' style='display: none;'></div>
		</div>
	</div>

	<table style='width: 100%;'>
		<tr>
			<td style='width: 50%;'>
				<input id='group_name' value='' placeholder='31 М' class='text_input big_input' style='width: 100%; display: inline-block;'>
			</td>
			<td style='width: 2%;'></td>
			<td>
				<select id='group_year' class='text_input big_input' style='height: 40px; width: 99%; display: inline-block;'>
				<?
				$year = GetYear(time()) - 1;
				
				for($i = 0; $i < 5; $i++)
				{
					Draw("<option value='".($year + $i)."'>".($year + $i)." - ".($year + $i + 1)."</option>");
				}
				?>
				</select>
			</td>
		</tr>
	</table>
	
	<div class='space'></div>
	<input id='group_thumbnail' value='' placeholder='Картинка (ссылка)' class='text_input big_input' style='width: 98%;'>
	<div class='space'></div>
	<div class='divider'></div>
	<div class='space'></div>
	<div class='title_text'>Расписание</div>
	<div class='space'></div>
	<div style='text-align: center;'>
	<?php
	$days = array("Понедельник", "Вторник", "Среда", "Четверг", "Пятница");
	$pairs = 7;
	
	for($d = 0; $d < sizeof($days); $d++)
	{
		Draw("<div style='display: inline-block; width: 240px; margin: 10px;'>");
		Draw("<div class='text'>".$days[$d]."</div><div class='space'></div>");
		
		for($p = 0; $p < $pairs; $p++)
		{
			Draw("
			<input id='pair_".$d."_".$p."' value='' placeholder='".($p + 1)." пара' class='text_input' onclick='ScheduleHintList.getList(this, CurrentGroupID);' style='width: 97.5%;'>
			<div class='space'></div>
			");
		}
		
		Draw("</div>");
	}
	?>
	</div>
	<div class='big_space'></div>
	<div class='button back4 at_center' onclick='CreateScheduleGroup(<? echo(sizeof($days)); ?>, <? echo($pairs); ?>);'>Сохранить</div>
	<div class='big_space'></div>
</noscript>

<noscript id='new_change_window'>

	<?
	$groups = array();
	
	$query = SQL("SELECT * FROM `schedule_groups` WHERE `status`!='-1' ORDER BY `schedule_groups`.`id` ASC LIMIT 0, 1000000");
	
	while($row = SQLFetchAssoc($query))
	{
		$groups[] = $row;
	}
	?>
	
	<div id='schedule_hint_list'>
		<div class='hint_list_body'>
			<div class='hint_list_header fore3 text' onclick='SlideToggle("schedule_hint_list_wrap");'>Подсказки <span id='hint_list_counter' class='fore2'></span></div>
			<div id='schedule_hint_list_wrap' class='hint_list' style='display: none;'></div>
		</div>
	</div>
	
	<div class='title_text inner_center'>Учебный день</div>
	<div class='space'></div>
	
	<div class='inner_center'>
		<select id='change_day' class='text_input big_input' style='height: 40px; width: 20%; display: inline-block; margin: 5px;'>
		<?
		$days_up_number = 1;
		$dow = GetDayOfWeek(time());
		if($dow == 4)$days_up_number = 3;
		if($dow == 5)$days_up_number = 2;
		if($dow == 7)$days_up_number = 1;
		
		$target_time = time() + ($days_up_number * 24 * 60 * 60);
		
		$day = GetDay($target_time);
		$month = GetMonth($target_time);
		$year = GetYear($target_time);
		$day_of_week = GetDayOfWeek($target_time);
		
		for($d = 1; $d <= 31; $d++)
		{
			$select = "";
			if($d == $day)$select = "selected";
			Draw("<option value='".$d."' ".$select.">".$d."</option>");
		}
		?>
		</select>
		
		<select id='change_month' class='text_input big_input' style='height: 40px; width: 20%; display: inline-block; margin: 5px;'>
		<?
		for($m = 1; $m <= 12; $m++)
		{
			$select = "";
			if($m == $month)$select = "selected";
			$names = MonthByNumber($m);
			
			Draw("<option value='".$m."' ".$select.">".$names[0]."</option>");
		}
		?>
		</select>
		
		<select id='change_year' class='text_input big_input' style='height: 40px; width: 20%; display: inline-block; margin: 5px;'>
		<?
		for($y = $year; $y < $year + 3; $y++)
		{
			$select = "";
			if($y == $year)$select = "selected";
			
			Draw("<option value='".$y."' ".$select.">".$y."</option>");
		}
		?>
		</select>
		
		<select id='change_week_day' onchange='' class='text_input big_input' style='height: 40px; width: 20%; display: inline-block; margin: 5px;'>
		<?
		$days = array("Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье");
		
		for($d = 0; $d < sizeof($days); $d++)
		{
			$select = "";
			if($d == $day_of_week)$select = "selected";
			
			Draw("<option value='".$d."' ".$select.">".$days[$d]."</option>");
		}
		?>
		</select>
		
		<div id='standard_schedule_data' style='display: none;'><? echo ToJSON($groups);?></div>
	</div>
	
	<div class='big_space'></div>
	<div class='title_text inner_center'>Изменения расписания</div>
	<div class='space'></div>
	
	<div class='inner_center'>
	<?
	for($i = 0; $i < sizeof($groups); $i++)
	{
		$id = $groups[$i]['id'];
		$name = $groups[$i]['name'];
		$created = GetTime($groups[$i]['created']);
		$year = $groups[$i]['year'];
		$schedule = $groups[$i]['standard_schedule'];
		$status = $groups[$i]['status'];
		
		//
		
		$group_schedule = FromJSON($schedule);
		//$day_of_week = ($day_of_week < sizeof($group_schedule) - 1) ? $day_of_week : 0;
		$day_pairs = $group_schedule[0];
		
		$markup = "";
		
		//
		
		$markup .= "
		<div style='display: inline-block; width: 200px; margin: 10px;'>
		<div class='text'>".$name."</div>
		<div class='space'></div>
		";
		
		for($p = 0; $p < sizeof($day_pairs); $p++)
		{
			$markup .= "
			<input id='pairfield_".$id."_".$p."' onclick='ScheduleHintList.getList(this, ".$id.");' value='' placeholder='' class='text_input' style='width: 97.5%;'>
			<div class='space'></div>
			";
		}
		
		$markup .= "
		<input id='pair_note_".$id."' onclick='ScheduleHintList.getList(this, ".$id.");' value='' placeholder='Примечание' class='text_input' style='width: 97.5%;'>
		<div class='space'></div>
		";
		
		$markup .= "</div>";
		
		//
		
		Draw($markup);
	}
	
	?>
	</div>
	
	<div class='big_space'></div>
	<div class='button back4 at_center' onclick='CreateScheduleChange();'>Опубликовать</div>
	<div class='big_space'></div>
	
</noscript>

<script>

function ApplySchedulePlaceholders(day)
{
	var schedule_data = FromJSON(Find("standard_schedule_data").innerText);
	
	for(var g = 0; g < schedule_data.length; g++)
	{
		var id = schedule_data[g].id;
		var schedule = FromJSON(schedule_data[g].standard_schedule);
		
		for(var d = 0; d < schedule.length; d++)
		{
			if(d == day && day < schedule.length)
			{
				for(var p = 0; p < schedule[d].length; p++)
				{
					var block = "pairfield_" + id + "_" + p;
					
					Find(block).placeholder = schedule[d][p];
				}
			}
		}
	}
}

function GetScheduleChanges()
{
	var schedule_data = FromJSON(Find("standard_schedule_data").innerText);
	var schedule_change = [];
	
	for(var g = 0; g < schedule_data.length; g++)
	{
		var id = schedule_data[g].id;
		var name = schedule_data[g].name;
		var schedule = FromJSON(schedule_data[g].standard_schedule);
		
		var group_pairs = {
			"id": id,
			"name": name,
			"changes": [],
			"note": "",
		};
		
		for(var p = 0; p < schedule[0].length; p++)
		{
			var block = "pairfield_" + id + "_" + p + "";
			
			var pair = Find(block).value;
			if(pair == "")pair = Find(block).placeholder;
			pair = CutJSONChars(pair);
			
			group_pairs['changes'][p] = pair;
		}
		
		group_pairs['note'] = CutJSONChars(Find("pair_note_" + id).value);
		
		schedule_change[g] = group_pairs;
	}
	
	return schedule_change;
}

function ShowNewChangeWindow(day, month, year, schedule)
{
	ShowWindow("Изменение расписания", Find("new_change_window").innerText, 0, 0, 1000);
	
	var change_week_day = function(){
		ApplySchedulePlaceholders(Find("change_week_day").value);
	};
	
	Find("change_week_day").onchange = function(){change_week_day();}
	
	if(day == null && month == null && year == null && schedule == null)
	{
		change_week_day(); // Плейсхолдеры применяются только при добавлении
	}
	else
	{
		Find("change_day").value = day;
		Find("change_month").value = month;
		Find("change_year").value = year;
		
		console.log(schedule);
		
		for(var g = 0; g < schedule.length; g++)
		{
			for(var p = 0; p < schedule[g].changes.length; p++)
			{
				var PairName = schedule[g].changes[p];
				var PairField = "pairfield_" + schedule[g].id + "_" + p;
				
				if(Exists(PairField))Find(PairField).value = PairName;
			}
			
			var NoteField = "pair_note_" + schedule[g].id;
			if(Exists(NoteField))Find(NoteField).value = schedule[g].note;
		}
	}
}

function ShowNewGroupWindow(name, year, thumbnail, schedule)
{
	ShowWindow("Редактирование группы", Find("new_group_window").innerText, 0, 0, 800);
	
	if(name != null && year != null && thumbnail != null && schedule != null)
	{
		Find("group_name").value = name;
		Find("group_year").value = year;
		Find("group_thumbnail").value = thumbnail;
		//
		for(var d = 0; d < schedule.length; d++)
		{
			for(var p = 0; p < schedule[d].length; p++)
			{
				var PairName = schedule[d][p];
				var PairField = "pair_" + d + "_" + p;
				
				if(Exists(PairField))
				{
					Find(PairField).value = PairName;
				}
			}
		}
	}
}

function CreateScheduleGroup(days_number, pairs_number)
{
	var group_name = CutJSONChars(Find("group_name").value);
	var group_year = Find("group_year").value;
	var group_thumbnail = Find("group_thumbnail").value;
	
	var schedule = [];
	
	for(var d = 0; d < days_number; d++)
	{
		var day_pairs = [];
		
		for(var p = 0; p < pairs_number; p++)
		{
			var field_id = "pair_" + d + "_" + p;
			
			if(Exists(field_id))
			{
				var pair_name = Find(field_id).value;
				pair_name = CutJSONChars(pair_name);
				day_pairs[p] = pair_name;
			}
		}
		
		schedule[d] = day_pairs;
	}
	
	var schedule_json = ToJSON(schedule);
	//console.log(schedule_json);
	
	if(group_name != "")
	{
		ApiMethod("schedule.groups.create", { "name": group_name, "year": group_year, "schedule": schedule_json, "thumbnail": group_thumbnail }, function(data){
			//console.log(data);
			
			if(data.response == 1)
			{
				ShowPanel("Группа создана", 0);
				ReloadAsync();
				CloseWindow();
			}
		});
	}
	else
	{
		ShowPanel("Нет названия группы", 1);
	}
}

function CreateScheduleChange()
{
	var year = Find("change_year").value;
	var month = Find("change_month").value;
	var day = Find("change_day").value;
	var schedule = GetScheduleChanges();
	
	//console.log(schedule);
	
	ApiMethod("schedule.changes.create", { "year": year, "month": month, "day": day, "schedule": ToJSON(schedule) }, function(data){
		console.log(data);
		
		if(data.response == 1)
		{
			ShowPanel("Расписание опубликовано", 0);
			ReloadAsync();
			CloseWindow();
		}
	});
}

function ShowSchedule(group_name, schedule)
{
	var m = "";
	
	var days = ["Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье"];
	
	for(var d = 0; d < schedule.length; d++)
	{
		m += "<div class='title_text'>" + days[d] + "</div>";
		
		for(var p = 0; p < schedule[d].length; p++)
		{
			var PairName = schedule[d][p];
			
			if(PairName != "")m += "<div class='text'>" + (p + 1) + ". " + PairName + "</div>";
		}
		
		m += "<div class='space'></div>";
	}
	
	//console.log(m);
	
	ShowWindow(group_name, m, 0, 0, 400);
}

function DeleteGroup(id, block)
{
	ApiMethod("schedule.groups.delete", { "id": id }, function(data){
		if(data.response == 1)
		{
			Hide("delete_group_" + id);
			Show("restore_group_" + id);
		}
	});
}

function RestoreGroup(id, block)
{
	ApiMethod("schedule.groups.restore", { "id": id }, function(data){
		if(data.response == 1)
		{
			Show("delete_group_" + id);
			Hide("restore_group_" + id);
		}
	});
}

function DeleteChange(id, block)
{
	ApiMethod("schedule.changes.delete", { "id": id }, function(data){
		if(data.response == 1)
		{
			Hide("delete_change_" + id);
			Show("restore_change_" + id);
		}
	});
}

function RestoreChange(id, block)
{
	ApiMethod("schedule.changes.restore", { "id": id }, function(data){
		if(data.response == 1)
		{
			Show("delete_change_" + id);
			Hide("restore_change_" + id);
		}
	});
}

function CutJSONChars(str)
{
	str = str.replace(/"/g, "");
	str = str.replace(new RegExp(/'/g, "g"), "");
	str = str.replace(new RegExp(/\\/g, "g"), "/");
	
	return str;
}

var ScheduleHintList = {
	
	currentField: null,
	currentGroupID: -1,
	getList: function(field, group_id){
		
		if(ScheduleHintList.currentGroupID != group_id)
		{
			API.CallMethod("schedule.hints.get", { group_id: group_id }, function(data){
				if(data.response != null)
				{
					if(data.response !== 0)
					{
						ScheduleHintList.setHintListMarkup(data.response);
					}
				}
			});
		}
		
		ScheduleHintList.currentField = field;
		ScheduleHintList.currentGroupID = group_id;
		
	},
	setFieldValue: function(hint_id, value){
		if(ScheduleHintList.currentField != null)
		{
			ScheduleHintList.currentField.value = value;
			ScheduleHintList.setLastUsage(hint_id);
		}
	},
	setLastUsage: function(hint_id){
		API.CallMethod("schedule.hints.setLastUsage", { hint_id: hint_id }, function(data){});
	},
	setHintListMarkup: function(items){
		
		var markup = "";
		for(var i = 0; i < items.length; i++)
		{
			markup += "<div class='hint_list_item fore3 small_text' onclick='ScheduleHintList.setFieldValue(" + items[i].id + ", this.innerText);'>" + items[i].subject + "</div>\n";
		}
		
		if(items.length == 0)markup = "<div class='text fore3 padding'>Ничего нет...</div>";
		
		if(Exists("schedule_hint_list_wrap"))
		{
			Find("schedule_hint_list_wrap").innerHTML = markup;
		}
		
		if(Exists("hint_list_counter"))
		{
			if(items.length > 0)Find("hint_list_counter").innerHTML = items.length;
			else Find("hint_list_counter").innerHTML = "";
		}
	},
	
};

</script>
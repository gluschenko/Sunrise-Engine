<?php
$selected_group_id = $section['group_id'];
?>

<script>
function ToggleFullscreen()
{
	var schedule_table = Find("schedule_table");
	
	if(schedule_table.className == "content fullscreen_content"){
		schedule_table.className = "content";
	}
	else{
		schedule_table.className = "content fullscreen_content";
	}
}

//

var ScrollDelta = 30;
var ScrollingElement = null;

function ScrollElement(e) {
	if(ScrollingElement != null)
	{
		if (!e) e = event;
		if (e.preventDefault) { e.preventDefault(); } else { e.returnValue = false; }
		var delta = e.wheelDelta || -e.detail;
		delta /= Math.abs(delta);
		ScrollingElement.scrollLeft -= delta * ScrollDelta; // FF, Opera, IE
		if (this.attachEvent) return false;
		ScrollingElement.scrollLeft -= delta * ScrollDelta; // Chrome
	}
}

function InitScrolling(element)
{
	ScrollingElement = element;
	
	var html = ScrollingElement;//document.documentElement;
	if (html.attachEvent) {
		html.attachEvent("onmousewheel", ScrollElement); // IE and Opera
	} 
	else 
	{
		html.addEventListener("DOMMouseScroll", ScrollElement, false); // FF
		html.addEventListener("mousewheel", ScrollElement, false); // Chrome
	}
}

setTimeout(function() {
	InitScrolling(Find("schedule_table"));
}, 100);

//

function SwitchTab(tab_id)
{
	var tab_ids_assoc = [
		"first_tab", "actually_schedule",
		"second_tab", "default_schedule",
		"third_tab", "schedule_groups",
	];
	
	for(var i = 0; i < tab_ids_assoc.length; i += 2)
	{
		if(tab_id == tab_ids_assoc[i])
		{
			Find(tab_ids_assoc[i]).className = "button back4 schedule_tab";
			Show(tab_ids_assoc[i + 1]);
		}
		else 
		{
			Find(tab_ids_assoc[i]).className = "button back0 schedule_tab";
			Hide(tab_ids_assoc[i + 1]);
		}
		//
		
	}
}
</script>

<div <?php if($selected_group_id != 0)echo("style='display: none;'");?>>
	<div>
		<div id='first_tab' class='button back4 schedule_tab' onclick='SwitchTab(this.id);'>Текущее</div>
		<div id='second_tab' class='button back0 schedule_tab' onclick='SwitchTab(this.id);'>Основное</div>
		<div id='third_tab' class='button back0 schedule_tab' onclick='SwitchTab(this.id);'>Группы</div>
	</div>

	<div id='schedule_table' class='content'>
		<div id='actually_schedule'>
			<?

			$changes = array();

			$changes_index = array();
			$changes_dates = array();

			$query = SQL("SELECT * FROM `schedule_changes` WHERE `status`!='-1' ORDER BY `day_time` DESC LIMIT 8");

			while($row = SQLFetchAssoc($query))
			{
				$changes[] = $row;
			}

			//

			if(sizeof($changes) > 0)
			{
				for($i = 0; $i < sizeof($changes); $i++)
				{
					$year = $changes[$i]['year'];
					$month = $changes[$i]['month'];
					$day = $changes[$i]['day'];
					$schedule = FromJSON($changes[$i]['schedule']);
					//
					$changes_index[$i] = $schedule;
					$changes_dates[$i] = array($day, $month, $year);
				}

				//

				$groups_index = array();
				for($d = 0; $d < sizeof($changes_index); $d++)
				{
					for($g = 0; $g < sizeof($changes_index[$d]); $g++)
					{
						$id = $changes_index[$d][$g]['id'];
						$name = $changes_index[$d][$g]['name'];
						
						$has_group = false;
						
						for($h = 0; $h < sizeof($groups_index); $h++)
						{
							if($groups_index[$h]['id'] == $id)$has_group = true;
						}
						//
						if(!$has_group)
						{
							$groups_index[sizeof($groups_index)] = array(
								"id" => $id,
								"name" => $name,
							);
						}
					}
				}

				//print_r($groups_index);


				//

				$markup = "";

				$markup .= "<table>";

				$markup .= "<tr>";
				$markup .= "<td class='colored_ceil n_back4'></td>";

				for($g = 0; $g < sizeof($groups_index); $g++)
				{
					$markup .= "
					<td class='colored_ceil padding back4 inner_center' style='height: auto;'>
						<div style='position: relative;'>
							<div class='group_name text bold padding fore4 n_back3'>".$groups_index[$g]['name']."</div>
							<a href='?group=".$groups_index[$g]['id']."' onclick='return NavAsync(this.href, true);' style='position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;'></a>
						</div>
					</td>
					";
				}

				$markup .= "</tr>";

				for($d = 0; $d < sizeof($changes_index); $d++) //Дни
				{
					$change = $changes_index[$d];
					
					$markup .= "<tr>";
					
					$month_day = $changes_dates[$d][0];
					$month_names = MonthByNumber($changes_dates[$d][1]);
					
					$day_time = TimeByDate($changes_dates[$d][0], $changes_dates[$d][1], $changes_dates[$d][2]);
					$week_day = GetDayOfWeek($day_time);
					$day_names = WeekDayByNumber($week_day);
					
					$day = GetDay($day_time);
					$month = GetMonth($day_time);
					$year = GetYear($day_time);
					
					$day_now = GetDay(time());
					$month_now = GetMonth(time());
					$year_now = GetYear(time());
					
					$is_today = $day == $day_now && $month == $month_now && $year == $year_now;
					$is_yesterday = $day == $day_now - 1 && $month == $month_now && $year == $year_now;
					$is_tomorrow = $day == $day_now + 1 && $month == $month_now && $year == $year_now;
					
					$day_title = $day_names[0];
					
					if($is_today)$day_title = "Сегодня";
					if($is_yesterday)$day_title = "Вчера";
					if($is_tomorrow)$day_title = "Завтра";
					
					$markup .= "
					<td class='colored_ceil n_back2'>
						<div class='date_label n_back3'>
							<div style='height: 12px;'></div>
							<div class='title_text fore2'>".$month_day."</div>
							<div class='small_text fore2'>".$month_names[1]."</div>
						</div>
						<div class='mini_text fore3 inner_center'>".$day_title."</div>
					</td>
					";
					
					for($g = 0; $g < sizeof($groups_index); $g++) //Группы
					{
						$group_id = $groups_index[$g]['id'];
						//
						$group_object = array();
						$has = false;
						
						for($cg = 0; $cg < sizeof($change); $cg++) //Проверка наличия группы
						{
							$id = $change[$cg]['id'];
							if($id == $group_id)
							{
								$group_object = $change[$cg];
								$has = true;
							}
						}
						//
						
						$markup .= "<td class='pairs_list'>"; //Список пар
						
						if($has)
						{
							if($group_object['note'] == "")
							{
								$markup .= GetPairsMerkup($group_object['changes']);
							}
							else
							{
								$markup .= "
								<div class='small_button border0 fore0 back3' style='width: 120px; margin: 40% auto;' onclick='ShowModal(\"".$groups_index[$g]['name']."\", \"".$group_object['note']."\");'>Примечание</div>
								";
							}
						}
						
						$markup .= "</td>";
					}
					
					$markup .= "</tr>";
				}

				$markup .= "</table>";

				Draw($markup);
			}
			else
			{
				Draw("<div class='text'>Расписания нет...</div>");
			}

			?>
			<div class='pic_button back4 schedule_fullscreen_button' onclick='ToggleFullscreen();' title='Полный экран'></div>
		</div>
		
		<div id='default_schedule' style='display: none;'>
			<?
			$groups = array();

			$query = SQL("SELECT * FROM `schedule_groups` WHERE `status`!='-1' ORDER BY `id` ASC LIMIT 10000");

			while($row = SQLFetchAssoc($query))
			{
				$groups[sizeof($groups)] = $row;
			}

			for($i = 0; $i < sizeof($groups); $i++)
			{
				$groups[$i]['standard_schedule'] = FromJSON($groups[$i]['standard_schedule']);
			}
			
			$days_count = 0;
			
			for($g = 0; $g < sizeof($groups); $g++)
			{
				if(sizeof($groups[$g]['standard_schedule']) > $days_count)$days_count = sizeof($groups[$g]['standard_schedule']);
			}

			//

			if(sizeof($groups) > 0)
			{
				$markup = "";

				$markup .= "<table>";
				$markup .= "<tr>";
				$markup .= "<td class='colored_ceil n_back4'></td>";

				for($g = 0; $g < sizeof($groups); $g++)
				{
					$markup .= "
					<td class='colored_ceil padding back4 inner_center' style='height: auto;'>
						<div style='position: relative;'>
							<div class='group_name text bold padding fore4 n_back3'>".$groups[$g]['name']."</div>
							<a href='?group=".$groups[$g]['id']."' onclick='return NavAsync(this.href, true);' style='position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;'></a>
						</div>
					</td>
					";
				}

				$markup .= "</tr>";

				for($d = 0; $d < $days_count; $d++) //Дни
				{
					$markup .= "<tr>";
					
					$day_names = WeekDayByNumber($d);
					
					$markup .= "
					<td class='colored_ceil n_back2'>
						<div class='date_label n_back3'>
							<div style='height: 20px;'></div>
							<div class='title_text fore2'>".ToUpperCase($day_names[1])."</div>
						</div>
					</td>
					";
					
					for($g = 0; $g < sizeof($groups); $g++) //Группы
					{
						$markup .= "<td class='pairs_list'>"; //Список пар
						
						$schedule = $groups[$g]['standard_schedule'];
						if(isset($schedule[$d]))
						{
							$markup .= GetPairsMerkup($schedule[$d]);
						}
						
						$markup .= "</td>";
					}
					
					$markup .= "</tr>";
				}

				$markup .= "</table>";
				
				$markup .= "
				
				";

				Draw($markup);
			}
			else
			{
				Draw("<div class='text'>Расписания нет...</div>");
			}

			function GetPairsMerkup($pairs)
			{
				$m = "";
				
				// Волшебный алгоритм, формирующий список пар, который нужно отображать.
				$displayed_items_list = array();
				$can_display = false;
				for($p = sizeof($pairs) - 1; $p >= 0; $p--)
				{
					if($pairs[$p] != "")$can_display = true;
					$displayed_items_list[$p] = $can_display;
				}
				//
				for($p = 0; $p < sizeof($pairs); $p++)
				{
					if($displayed_items_list[$p])
					{
						$pair_name = $pairs[$p];
						$pair_name_array = explode("№", $pair_name);
						
						$pair_name = $pair_name_array[0];
						$room_number = "";
						if(sizeof($pair_name_array) > 1)$room_number = $pair_name_array[1];
						
						if(isset($pair_name[0]))
						{
							if($pair_name[0] == "-")$pair_name = "";
						}
						
						$m .= "
						<div class='pair_label'>
							<div class='pair_number mini_text fore3 n_back2'>".($p + 1)."</div>
							<div class='pair_name small_text' title='".$pair_name."'>".CropText($pair_name, 19)."</div>
							<div class='room_number mini_text fore3 n_back4'>".$room_number."</div>
						</div>
						";
					}
				}
				
				return $m;
			}


			?>
			<div class='pic_button back4 schedule_fullscreen_button' onclick='ToggleFullscreen();' title='Полный экран'></div>
		</div>

	</div>

	<div id='schedule_groups' style='display: none;'>
		<div class='divider'></div>
		<div class='space'></div>
		<div class='inner_center'>
			<?php
			for($i = 0; $i < sizeof($groups); $i++)
			{
				$thumbnail = $groups[$i]['thumbnail'];
				$thumb_style = "";
				
				if($thumbnail != "")$thumb_style = "background-image: url(".$thumbnail.");";
				
				$markup = "
				<a class='group_link n_back0' href='?group=".$groups[$i]["id"]."' onclick='return NavAsync(this.href, true);'>
					<div class='group_link_back' style='".$thumb_style."'></div>
					<div class='group_link_title title_text fore3'>
						".$groups[$i]["name"]."
						<div class='group_link_title_middleback'></div>
						<div class='group_link_title_back n_back0' style='".$thumb_style."'></div>
					</div>
				</a>
				";
				
				Draw($markup);
			}
			?>
		</div>
	</div>

</div>

<?php
if($selected_group_id != 0)
{
	Draw("
	<div class='big_space'></div>
	<div class='title_text inner_center'>".GetGroupName($selected_group_id)."</div>
	<div class='big_space'></div>
	<div class='inner_center'>
	");
	
	$markup = "";
	
	for($d = 0; $d < sizeof($changes_index); $d++) //Дни
	{
		$change = $changes_index[$d];
	
		$markup .= "<tr>";
		
		$month_day = $changes_dates[$d][0];
		$month_names = MonthByNumber($changes_dates[$d][1]);
		
		$day_time = TimeByDate($changes_dates[$d][0], $changes_dates[$d][1], $changes_dates[$d][2]);
		$week_day = GetDayOfWeek($day_time);
		$day_names = WeekDayByNumber($week_day);
		
		$markup .= "
		<div class='group_pairs_list'>
			<div class='group_pairs_list_title text inner_center'>".$month_day." ".ToLowerCase($month_names[1])."</div>
		";
		
		for($g = 0; $g < sizeof($groups_index); $g++) //Группы
		{
			$group_id = $groups_index[$g]['id'];
			
			if($group_id == $selected_group_id)
			{
				$group_object = array();
				$has = false;
				
				for($cg = 0; $cg < sizeof($change); $cg++) //Проверка наличия группы
				{
					$id = $change[$cg]['id'];
					if($id == $group_id)
					{
						$group_object = $change[$cg];
						$has = true;
					}
				}
				//
				if($has)
				{
					if($group_object['note'] == "")
					{
						$markup .= GetPairsMerkup($group_object['changes']);
					}
					else
					{
						$markup .= "
						<div class='small_button border0 fore0 back3' style='width: 120px; margin: 40% auto;' onclick='ShowModal(\"".$groups_index[$g]['name']."\", \"".$group_object['note']."\");'>Примечание</div>
						";
					}
				}
			}
		}
		
		$markup .= "</div>";
	}
	
	Draw($markup);
	
	Draw("
	<div class='big_space'></div>
	<a class='at_center button' href='/schedule' onclick='return NavAsync(this.href, true);'>Расписание</a>
	<div class='big_space'></div>
	");
	
	Draw("</div>");
}

?>

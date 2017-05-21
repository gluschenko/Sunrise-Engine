<?
//Модуль расписания пар (03-03-2016)
require("schedule_db.php");
require("schedule_init.php");
//

function CreateScheduleGroup($name, $year, $schedule, $thumbnail)
{
	if(!isGroupExists($name, $year))
	{
		$id = GetNewId("schedule_groups");
		$time = time();
		
		$query = SQL("INSERT INTO `schedule_groups`(`id`, `name`, `thumbnail`, `created`, `year`, `standard_schedule`, `status`) VALUES ('$id','$name','$thumbnail','$time','$year','$schedule','0')");
		
		return true;
	}
	else
	{
		$query = SQL("UPDATE `schedule_groups` SET `standard_schedule`='$schedule', `thumbnail`='$thumbnail' WHERE `name`='$name' AND `year`='$year'");
		
		return true;
	}
	
	return false;
}

function DeleteScheduleGroup($id)
{
	$query = SQL("UPDATE `schedule_groups` SET `status`='-1' WHERE `id`='$id'");
}

function RestoreScheduleGroup($id)
{
	$query = SQL("UPDATE `schedule_groups` SET `status`='0' WHERE `id`='$id'");
}

function isGroupExists($name, $year)
{
	$query = SQL("SELECT * FROM `schedule_groups` WHERE `name`='$name' AND `year`='$year' LIMIT 1");
	$row = SQLFetchAssoc($query);
	
	return $row['id'] != "";
}

function isGroupIDExists($id)
{
	$query = SQL("SELECT * FROM `schedule_groups` WHERE `id`='$id' AND `status` != '-1' LIMIT 1");
	$row = SQLFetchAssoc($query);
	
	return $row['id'] != "";
}

function GetGroupName($id)
{
	$query = SQL("SELECT * FROM `schedule_groups` WHERE `id`='$id' LIMIT 1");
	$row = SQLFetchAssoc($query);
	
	return $row['name'];
}

function CreateScheduleChange($year, $month, $day, $schedule)
{
	$decoded_schedule = FromJSON($schedule);
	
	for($i = 0; $i < sizeof($decoded_schedule); $i++)
	{
		$group_id = $decoded_schedule[$i]['id'];
		$subjects = $decoded_schedule[$i]['changes'];
		$subjects[] = $decoded_schedule[$i]['note'];
		
		AddScheduleHints($group_id, $subjects);
	}
	//
	
	$school_day_time = TimeByDate($day, $month, $year);
	
	if(!isChangeExists($day, $month, $year))
	{
		$id = GetNewId("schedule_changes");
		$time = time();
		
		$query = SQL("INSERT INTO `schedule_changes`(`id`, `created`, `year`, `month`, `day`, `day_time`, `schedule`, `status`) 
			VALUES ('$id','$time','$year','$month','$day','$school_day_time','$schedule','0')");
			
		//
		
		return true;
	}
	else
	{
		$query = SQL("UPDATE `schedule_changes` SET `schedule`='$schedule', `day_time`='$school_day_time' WHERE `day`='$day' AND `month`='$month' AND `year`='$year'");
		
		return true;
	}
	
	return false;
}

function DeleteScheduleChange($id)
{
	$query = SQL("UPDATE `schedule_changes` SET `status`='-1' WHERE `id`='$id'");
}

function RestoreScheduleChange($id)
{
	$query = SQL("UPDATE `schedule_changes` SET `status`='0' WHERE `id`='$id'");
}

function isChangeExists($day, $month, $year)
{
	$query = SQL("SELECT * FROM `schedule_changes` WHERE `day`='$day' AND `month`='$month' AND `year`='$year' LIMIT 1");
	$row = SQLFetchAssoc($query);
	
	return $row['id'] != "";
}

//

function AddScheduleHints($group_id, $subjects)
{
	for($t = 0; $t < sizeof($subjects); $t++)
	{
		$subjects[$t] = trim($subjects[$t]);
		if(strpos($subjects[$t], "---") !== false)$subjects[$t] = "---";
	}
	//
	$existing_subjects = array();
	$query = SQL("SELECT * FROM `schedule_hints` WHERE group_id='$group_id';");
	while($row = SQLFetchAssoc($query))
	{
		$existing_subjects[] = $row['subject'];
	}
	//
	$subjects_to_add = array();
	for($i = 0; $i < sizeof($subjects); $i++)
	{
		if($subjects[$i] != "")
		{
			if(!in_array($subjects[$i], $existing_subjects) && !in_array($subjects[$i], $subjects_to_add))
			{
				$subjects_to_add[] = $subjects[$i];
			}
		}
	}
	//
	if(sizeof($subjects_to_add) > 0)
	{
		$id = GetNewId("schedule_hints");
		$values = array();
		$time = time();
		for($i = 0; $i < sizeof($subjects_to_add); $i++)
		{
			$values[] = StringFormat("('{0}','{1}','{2}','{3}')", $id, $group_id, $time, $subjects_to_add[$i]);
			
			$id++;
		}
		
		$query = SQL("INSERT INTO `schedule_hints`(`id`, `group_id`, `last_usage`, `subject`) 
			VALUES ".implode(",", $values));
	}
	
}

function GetScheduleHints($group_id)
{
	$items = array();
	
	$query = SQL("SELECT * FROM `schedule_hints` WHERE group_id='$group_id' ORDER BY last_usage DESC;");
	
	while($row = SQLFetchAssoc($query))
	{
		$items[] = $row;
	}
	
	return $items;
}

function SetScheduleHintLastUsage($hint_id)
{
	$time = time();
	$query = SQL("UPDATE `schedule_hints` SET last_usage = '$time' WHERE id = '$hint_id';");
}

?>
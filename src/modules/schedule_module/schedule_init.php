<?

$cfg = GetConfig();

AddSection(array(
	"type" => 0,
	"name" => "schedule",
	"header" => "Расписание",
	"title" => "Расписание - {site_name}",
	"layout" => 1,
	"permission" => 0,
	"keywords" => "расписание, занятия, пары",
	"description" => "Расписание занятий и звонков.",
	"section_dir" => "/modules/schedule_module/sections",
));
//
$sections_dir = dirname(__FILE__)."/sections";

AddAdminSection("schedule", "Управление расписанием", $sections_dir."/admin_markup_schedule.php");
//
AddAdminLink("/admin?act=schedule", "Управление расписанием", 0);
AddAdminLink("/schedule", "Расписание", 0);
//
AddCSS("/modules/schedule_module/assets/schedule_styles.css");
//

AddMethod("schedule.groups.create", function($params){
	if(isAdminLogged())
	{
		$name = $params['name'];
		$year = $params['year'];
		$schedule = $params['schedule'];
		$thumbnail = $params['thumbnail'];
		
		return CreateScheduleGroup($name, $year, $schedule, $thumbnail) ? 1 : 0;
	}
	
	return 0;
});

AddMethod("schedule.changes.create", function($params){
	if(isAdminLogged())
	{
		$year = $params['year'];
		$month = $params['month'];
		$day = $params['day'];
		$schedule = $params['schedule'];
		
		return CreateScheduleChange($year, $month, $day, $schedule) ? 1 : 0;
	}
	
	return 0;
});

AddMethod("schedule.groups.delete", function($params){
	if(isAdminLogged())
	{
		$id = $params['id'];
		DeleteScheduleGroup($id);
		return 1;
	}
	return 0;
});

AddMethod("schedule.groups.restore", function($params){
	if(isAdminLogged())
	{
		$id = $params['id'];
		RestoreScheduleGroup($id);
		return 1;
	}
	return 0;
});

AddMethod("schedule.changes.delete", function($params){
	if(isAdminLogged())
	{
		$id = $params['id'];
		DeleteScheduleChange($id);
		return 1;
	}
	return 0;
});

AddMethod("schedule.changes.restore", function($params){
	if(isAdminLogged())
	{
		$id = $params['id'];
		RestoreScheduleChange($id);
		return 1;
	}
	return 0;
});

AddMethod("schedule.hints.get", function($params){
	if(isAdminLogged())
	{
		$group_id = $params['group_id'];
		return GetScheduleHints($group_id);
	}
	return 0;
});

AddMethod("schedule.hints.setLastUsage", function($params){
	if(isAdminLogged())
	{
		$hint_id = $params['hint_id'];
		SetScheduleHintLastUsage($hint_id);
		return 1;
	}
	return 0;
});

?>
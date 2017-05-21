<?php

$group_id = 0;

if(isset($section['request']['group']))$group_id = $section['request']['group'];

if($group_id != 0)
{
	if(isGroupIDExists($group_id))$section['title'] = GetGroupName($group_id)." - {site_name}";
	else $group_id = 0;
}

$section['group_id'] = $group_id;

?>
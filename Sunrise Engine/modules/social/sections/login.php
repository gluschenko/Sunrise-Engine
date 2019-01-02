<?php

$section['logged'] = isLogged();
$section['reload'] = false;

//

$code = (isset($section['request']['code']))? $section['request']['code'] : "";
if($code != "")
{
	$section['code'] = $code;
	$section['vk_profile'] = GetVKProfile($code);
	//print_r($section['vk_profile']);
	
	//
	if(isset($section['vk_profile']['uid']))
	{
		$vk_id = $section['vk_profile']['uid'];
		$first_name = $section['vk_profile']['first_name'];
		$last_name = $section['vk_profile']['last_name'];
		$photo_max_orig = $section['vk_profile']['photo_max_orig'];
		$email = (isset($section['vk_profile']['email']))? $section['vk_profile']['email'] : "";
		//
		if($email != "")
		{
			$query = SQL("SELECT `id`, `vk_id` FROM `profiles` WHERE `vk_id`='$vk_id' OR `email`='$email' LIMIT 1");
			$row = SQLFetchAssoc($query);
			
			if($row['id'] == "")
			{
				$newid = Register($email, md5("pass_".$vk_id), $first_name, $last_name);
				$avatar = CreateContent($newid, $photo_max_orig);
				
				$query = SQL("UPDATE `profiles` SET vk_id='$vk_id', avatar='$avatar' WHERE `id`='$newid'");
			}
			else
			{
				$id = $row['id'];
				DirectAuthorize($id);
			}
		}
	}
	
	$section['reload'] = true;
}


?>
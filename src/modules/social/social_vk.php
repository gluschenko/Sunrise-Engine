<?php

function VKAuthURL()
{
	$cfg = GetConfig();
	$engine_config = GetEngineConfig();
	
	$redirect = "http://".$cfg['domain'].$engine_config['social']['vk_redirect'];
	
	$url = "http://oauth.vk.com/authorize";
	
	$params = array(
		"client_id" => $engine_config['social']['vk_app'],
		"redirect_uri" => $redirect,
		"scope" => "email",
		"response_type" => "code",
		"v" => "5.41",
	);
	
	$full_url = str_replace("&amp;", "&", $url."?".urldecode(http_build_query($params)));
	
	return $full_url;
}

function GetVKProfile($code)
{
	$cfg = GetConfig();
	$engine_config = GetEngineConfig();
	
	//
	
	$profile = array(
		"uid" => "",
		"first_name" => "",
		"last_name" => "",
		"photo_max_orig" => "",
		"email" => "",
	);
	$result = false;
	
	$params = array(
		"client_id" => $engine_config['social']['vk_app'],
		"client_secret" => $engine_config['social']['vk_secret'],
		"code" => $code,
		"redirect_uri" => "http://".$cfg['domain'].$engine_config['social']['vk_redirect'],
	);
	
	try{
		$query_url = "https://oauth.vk.com/access_token?".str_replace("&amp;", "&", urldecode(http_build_query($params)));
		
		$content = @file_get_contents($query_url);
		$token_data = json_decode($content, true);
		
		if(isset($token_data['access_token']))
		{
			$params = array(
				"user_ids" => $token_data['user_id'],
				"fields" => "uid,first_name,last_name,photo_max_orig",
				"access_token" => $token_data['access_token'],
				"lang" => "ru",
			);
			
			
			$resp = json_decode(file_get_contents("https://api.vk.com/method/users.get?".str_replace("&amp;", "&", urldecode(http_build_query($params)))), true);
			
			if(isset($resp['response'][0]['uid']))
			{
				$profile = MergeArrays($resp['response'][0], $profile);
				$profile['email'] = isset($token_data['email'])? $token_data['email'] : "change_".$profile['uid'];
				$result = true;
			}
		}
	}
	catch(Exception $e){
		//echo($e);
	}
	
	return $profile;
}

?>
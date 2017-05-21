<?php
//Alexander Gluschenko (6-12-2015)
//Ресивер загрузки для модуля social

include($_SERVER['DOCUMENT_ROOT'].'/engine/engine.php');

if(isLogged())
{
	if(isset($_FILES))
	{
		foreach($_FILES as $file)
		{
			$extentions = array(".jpg", ".jpeg", ".gif", ".png", ".tiff");
			
			$is_valid = false;
			
			$file_name = strtolower($file['name']);
			
			for($i = 0; $i < sizeof($extentions); $i++)
			{
				if(strpos($file_name, $extentions[$i]) !== false)$is_valid = true;
			}
			//
			if($is_valid)
			{
				$user_id = GetData("user_id", 0);
				$upload_path = "/files"; //"/content"; //"/modules/social/files";
				//
				$file_name_arr = explode(".", $file_name);
				$ext = $file_name_arr[sizeof($file_name_arr) - 1];
				$file['name'] = $user_id."_".time()."_".rand(0, 10000).".".$ext;
				//
				$upload_state = UploadFile($file, $upload_path);
				
				if($upload_state)
				{
					$content_id = CreateContent($user_id, $upload_path."/".$file['name'], "image");
					echo ToJSON(GetContent($content_id));
					error_log("FILE UPLOAD -> ".ToJSON($file));
				}
			}
		}
	}
}

?>
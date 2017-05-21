<?php
//Alexander Gluschenko (15-07-2015)
//Сюда приходит запрос от JS (multipart/form-data)

include($_SERVER['DOCUMENT_ROOT'].'/engine/engine.php');

if(isAdminLogged())
{
	if(isset($_FILES))
	{
		foreach($_FILES as $file)
		{
			echo UploadFile($file);
			error_log("File uploaded:".ToJSON($file));
		}
	}
}

?>
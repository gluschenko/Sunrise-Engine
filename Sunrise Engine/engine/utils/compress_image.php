<?
//Cжимает картинки на выводе

$target_width = 500;

if(isset($_REQUEST['i']))
{
	$path = $_REQUEST['i'];
	$is_external = preg_match('/https:/', $path) || preg_match('/http:/', $path);
	
	if(!$is_external)
	{
		try
		{
			$img = $_SERVER['DOCUMENT_ROOT'].$_REQUEST['i'];
		if(isset($_REQUEST['width']))$target_width = $_REQUEST['width'];
		//
		header('Content-Type: image/jpeg');
		
		list($width, $height) = getimagesize($img);
		
		$ratio = $target_width/$width;
		
		$new_width = $target_width;
		$new_height = $height * $ratio;
		
		
		$thumb = imagecreatetruecolor($new_width, $new_height);
		imageantialias($thumb, true);
		
		$source;
		if(strpos(strtolower($img), ".jpg") !== false || strpos(strtolower($img), ".jpeg") !== false)$source = imagecreatefromjpeg($img);
		if(strpos(strtolower($img), ".png") !== false)$source = imagecreatefrompng($img);
		if(strpos(strtolower($img), ".gif") !== false)$source = imagecreatefromgif($img);
		
		imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		
		imagejpeg($thumb);
		}
		catch(Exception $e){}
	}
	else
	{
		header('Content-Type: image/jpeg');
		$i = file_get_contents($path);
		echo $i;
	}
}


?>
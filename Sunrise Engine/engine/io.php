<?php
//Alexander Gluschenko (15-07-2015)

OnLoad(function(){
	ApplyDirectories();
});

function ApplyDirectories()
{
	global $config;
	global $engine_config;
	$folders = $engine_config['folders'];
	
	foreach($folders as $folder)
	{
		$path = $config['root'].$folder;
		$is_engaged = is_file($path) || is_dir($path) || is_link($path);
		
		if(!$is_engaged)
		{
			CreateFolder($path);
		}
	}
}

/** Загружает настройки сайта */
function GetSiteSettings()
{
	$config = GetConfig();
	
	$default = array(
		"admin_password" => _SHA256("123"), //Протокол
		"admin_token" => GenerateToken("RESET_IT_IMMEDIATE"), //Протокол // 
		"template" => "boxed_template",
		"site_name" => "Site",
		"header" => "Default Site",
		"slogan" => "Motto",
		"keywords" => "Sunrise Engine, JavaScript, AJAX, PHP, MySQL, CentOS",
		"description" => "Some description",
		"preview_image" => "",
		"icon" => "",
		"timezone" => "3",
		"disabled_modules" => array(),
	);
	
	$data = LoadJSON($config['root']."/data/settings.json", $default);
	
	return MergeArrays($data, $default);
}

/** Сохраняет настройки сайта */
function SaveSiteSettings($params)
{
	$config = GetConfig();
	
	//
	if(isset($params['admin_password']))
	{
		if($params['admin_password'] != "") //Если поле с паролем не пустое, то хешируем пароль
		{
			$params['admin_password'] = _SHA256($params['admin_password']);
		}
		else
		{
			unset($params['admin_password']); //Перезапить хеша пароля в настройках не произойдет
		}
	}
	
	if(isset($params['admin_token']))
	{
		if($params['admin_token'] == "")
		{
			$params['admin_token'] = GenerateToken();
		}
	}
	//
	
	$settings = GetSiteSettings();
	$settings = MergeArrays($params, $settings);
	
	SaveJSON($config['root']."/data/settings.json", $settings);
}

//

/** Структура страницы */
function GetDefaultPage()
{
	$page = DefaultSection();
	$page['type'] = 1;
	$page['page_markup'] = "";
	
	return $page;
}

/** Создает или изменяет страницу */
function CreateOrEditPage($data, $markup)
{
	$config = GetConfig();
	//
	$page = GetDefaultPage();
	$page['page_markup'] = "/data/pages/".$data['name'].".php";
	//
	$page = MergeArrays($data, $page);
	//
	SaveJSON($config['root']."/data/pages/".$page['name']."_config.json", $page);
	SaveFile($config['root'].$page['page_markup'], $markup);
}

/** Загружает объекты страницы */
function GetPage($name)
{
	$config = GetConfig();
	//
	$default_data = GetDefaultPage();
	//
	$data = LoadJSON($config['root']."/data/pages/".$name."_config.json", $default_data);
	$data = MergeArrays($data, $default_data);
	
	$markup = "";
	if($data['page_markup'] != "")$markup = LoadFile($config['root'].$data['page_markup'], "");
	
	$page = array("data" => $data, "markup" => $markup);
	//
	return $page;
}

/** Удаляет страницу */
function DeletePage($name)
{
	$config = GetConfig();
	//
	DeleteFile($config['root']."/data/pages/".$name.".php");
	DeleteFile($config['root']."/data/pages/".$name."_config.json");
}

//Native

/** Загружает .json файлы */
function LoadJSON($path, $alt_arr)
{
	$file_data = LoadFile($path, ToJSON($alt_arr));
	
	if($file_data != "")
	{
		return FromJSON($file_data);
	}
	
	return $alt_arr;
}

/** Сохраняет .json файлы */
function SaveJSON($path, $data_arr)
{
	$config = GetConfig();
	$json_data = ToJSON($data_arr);
	
	SaveFile($path, $json_data);
}

/** Загружает текстовый файл */
function FileToStr($file_array)
{
	/*$output = "";
	
	for($i = 0; $i < sizeof($file_array); $i++)
	{
		$output .= $file_array[$i];//."\n";
	}
	*/
	return implode("", $file_array); //$output;
}

/** Возвращает содержимое файла или создает файл */
function GetFileOrCreate($path, $alt = "")
{
	if(file_exists($path))
	{
		return file($path);
	}
	else
	{
		file_put_contents($path, $alt);
		return array($alt);
	}
}

/** Возвращает содержимое файла */
function GetFileContent($path, $alt = "")
{
	return LoadFile($path, $alt);
}

/** Создает папку и выдает права */
function CreateFolder($path, $rigths = 0777)
{
	if(!is_dir($path))
	{
		$dir = @mkdir($path, $rigths) or ThrowError("Невозможно создать директорию ".$path);
		
		if($dir)
		{
			SetFolderRights($path, $rigths);
			return true;
		}
	}
	return false;
}

/** Выдает права произвольной папке */
function SetFolderRights($path, $rigths = 0777)
{
	return @chmod($path, $rigths) or ThrowError("Невозможно назвачить права ".decoct($rigths)." для ".$path);
}

/** Загружает файл на сервер */
function UploadFile($file, $storage_path = "/files", $reupload = false)
{
	$config = GetConfig();
	
	$name = NormalizeFileName($file['name']);
	$type = $file['type'];
	$tmp_name = $file['tmp_name'];
	$size = $file['size'];
	$error = $file['error'];
	
	$path = $config['root'].$storage_path;
	$file_path = $path."/".$name;
	
	if(!$reupload) //Если перезаливка не разрешена
	{
		while(file_exists($file_path))
		{
			$name = StringFormat("_{0}", $name);
			$file_path = $path."/".$name;
		}
	}
	
	$size_limit = 1024 * 1024 * 100; //100 мб
	
	if(isValidType($name, $type))
	{
		if($size <= $size_limit)
		{
			if(is_uploaded_file($tmp_name))
			{
				if(move_uploaded_file($tmp_name, $file_path))
				{
					LogAction("Загружен файл → ".$name, 0);
					return $name;
				}
			}
		}
	}
	
	LogAction("Неудачная процедура загрузки файла → ".$name, 1);
	return false;
}

/** Проверяет тип файла */
function isValidType($name, $type)
{
	$black_mime = array("php/text");
	$black_ext = array(".php", ".phtml", ".php3", ".php4" /*, ".html", ".htm", ".js"*/);
	
	//
	
	$is_valid_mime = true;
	$is_valid_ext = true;
	
	for($i = 0; $i < sizeof($black_mime); $i++)
	{
		if($type == $black_mime[$i])$is_valid_mime = false;
	}
	
	for($i = 0; $i < sizeof($black_ext); $i++)
	{
		if(strpos($name, $black_ext[$i]))$is_valid_ext = false;
	}
	
	return $is_valid_mime && $is_valid_ext;
}

/** Приверяет принадлежность к изображениям */
function isImageType($name)
{
	$image_extensions = array("jpg", "jpeg", "png", "gif"/*, "bmp"*/);
	
	$name_arr = explode(".", ToLowerCase($name));
	
	$is_image = false;
	
	for($i = 0; $i < sizeof($image_extensions); $i++)
	{
		if(sizeof($name_arr) > 0)
		{
			if($image_extensions[$i] == $name_arr[sizeof($name_arr) - 1])
			{
				$is_image = true;
			}
		}
	}
	
	return $is_image;
}

/** Загружает файл */
function LoadFile($path, $alt = "")
{
	if(file_exists($path))
	{
		//return FileToStr(file($path));\
		return file_get_contents($path);
	}
	else
	{
		return $alt;
	}
}

/** Сохраняет файл */
function SaveFile($path, $data)
{
	file_put_contents($path, $data);
}

/** Удаляет файл */
function DeleteFile($path)
{
	if(file_exists($path))
	{
		unlink($path);
	}
}

/** Изменяет имя файла */
function RenameFile($path, $new_path)
{
	if(file_exists($path))
	{
		rename($path, $new_path);
	}
}

/** Получает размер файла в байтах */
function GetFileSize($path, $alt = 0)
{
	if(file_exists($path))
	{
		$file_data = stat($path);
		return $file_data['size'];
	}
	else
	{
		return $alt;
	}
}

/** Возвращает массив файлов по критериям */
function GetFilesList($dir, $key_name = ".", $with_data = false, $is_json = false) //Получает список файлов и/или папок без "." и ".."
{
	$files = scandir($dir);
	$files_data = array();
	
	$add_count = 0;
	for($i = 0; $i < sizeof($files); $i++)
	{
		$needed_file = ($key_name != "")? strpos($files[$i], $key_name) : true;
		
		if($needed_file && $files[$i] != "." && $files[$i] != "..")
		{
			$file_data = stat($dir."/".$files[$i]);
			
			$file = array(
				"name" => $files[$i],
				"dir" => $dir,
				"path" => $dir."/".$files[$i],
				"size" => $file_data['size'],
				"modified" => $file_data['mtime'],
			);
			
			if($with_data)
			{
				if($is_json)$file['data'] = LoadJSON($dir."/".$files[$i], array());
				else $file['data'] = GetFileContent($dir."/".$files[$i], "");
			}
			
			$files_data[$add_count] = $file;
			$add_count++;
		}
	}
	
	return $files_data;
}

/** Включает код */
function RequireIfExists($path, $alt = "")
{
	if(file_exists($path))
	{
		return require($path);
	}
	else
	{
		echo($alt);
	}
}

/** Включает код секции */
function RequireSectionIfExists($path, &$section, $alt = "") //Протягиевает переменную в верстку секции
{
	if(file_exists($path))
	{
		require($path);
		return $section;
	}
	/*else
	{
		echo($alt);
	}*/
}

//

/** Проверяет доступность файла */
function UrlOK($url)
{
	$headers = @get_headers($url, 1);
	
	//if(preg_match("|200|", $headers[0])) 
	if(strpos('200', $headers[0]))
	{
		return true;
	}
	
	/*if(@fopen($url, "r")){
		return true;
	}*/
	
	return false;
}

/** Изменяет размер изображения */
function ResizeImage($path, $rate)
{
	list($width, $height) = getimagesize($path);
	
	$new_width = $width * $rate;
	$new_height = $height * $rate;
	
	$new_image = imagecreatetruecolor($new_width, $new_height);
	
	$source_image;
	if(strpos($path, ".jpg") !== false || strpos($path, ".jpeg") !== false)$source_image = imagecreatefromjpeg($path);
	if(strpos($path, ".png") !== false)$source_image = imagecreatefrompng($path);
	if(strpos($path, ".gif") !== false)$source_image = imagecreatefromgif($path);
	
	imagecopyresized($new_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	
	imagejpeg($new_image, $path);
	
	// ДНО, НЕ РАБОТАЕТ
	/*$imagick = new Imagick($path);
	
	$width = $imagick->getImageWidth();
    $height = $imagick->getImageHeight();
	
	$width *= $rate;
	$height *= $rate;
	
	$imagick->resizeImage($width, $height, FILTER_GAUSSIAN, 1);
	*/
	
}

/** Возвращает адрес уменьшенной картинки */
function GetThumbImageURL($path, $width = 500)
{
	$is_external = preg_match('/https:/', $path) || preg_match('/http:/', $path);
	
	if(!$is_external)
	{
		return StringFormat("/engine/utils/compress_image.php?i={0}&width={1}", $path, $width);
	}
	
	return $path;
}

/** Получает информацаю об inodes */
function GetInodes()
{
	$shell = shell_exec("df -i");
	$arr = explode(" ", $shell);
	$second_arr = array();
	for($i = 0; $i < sizeof($arr); $i++)
	{
		if($arr[$i] != "")$second_arr[] = $arr[$i];
	}
	//
	$all = isset($second_arr[7]) ? $second_arr[7] : 0;
	$used = isset($second_arr[8]) ? $second_arr[8] : 0;
	$used_rate = 0;
	if($all > 0)
	{
		$used_rate = $used/$all;
	}
	
	return array("all" => $all, "used" => $used, "free" => $all - $used, "used_rate" => $used_rate);
}

//

class IO
{
	/*public static function Zip($source, $destination)
	{
		if (!extension_loaded('zip') || !file_exists($source)) {
			return false;
		}
		
		$zip = new ZipArchive();
		if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			return false;
		}
		
		$source = str_replace('\\', '/', realpath($source));
		
		if (is_dir($source) === true)
		{
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), 
				RecursiveIteratorIterator::SELF_FIRST,
				RecursiveIteratorIterator::CATCH_GET_CHILD);
			
			foreach ($files as $file)
			{
				$file = str_replace('\\', '/', $file);
				
				// Ignore "." and ".." folders
				if(in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
					continue;
				
				$file = realpath($file);
				
				//echo($file."\n");
				
				if (is_dir($file) === true)
				{
					$zip->addEmptyDir(str_replace($source.'/', '', $file.'/'));
				}
				else if (is_file($file) === true)
				{
					$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
					echo($file."\n");
				}
			}
		}
		else if (is_file($source) === true)
		{
			$zip->addFromString(basename($source), file_get_contents($source));
		}
		
		return $zip->close();
	}*/
	
	public static function BackupAll()
	{
		$cfg = GetConfig();
		
		$t = time();
		$filename = StringFormat("backup_{0}-{1}-{2}_{3}:{4}_{5}", GetDay($t), GetMonth($t), GetYear($t), GetHour($t), GetMinute($t), substr(md5(rand(0, 100000000)), 0, 16));
		$output_path = StringFormat("{0}/files/{1}.zip", $cfg['root'], $filename);
		
		IO::Zip($output_path, $cfg['root']);
		IO::ExportDatabase();
		//
		LogAction("Создана резервная копия файлов", 0);
	}
	
	public static function Zip($to, $path)
	{
		$output = shell_exec(StringFormat("zip -r {0} {1}", $to, $path));
		
		return $output;
	}
	
	public static function ExportDatabase()
	{
		$cfg = GetConfig();
		
		$user = $cfg['mysql_user'];
		$password = $cfg['mysql_password'];
		$name = $cfg['mysql_name'];
		
		$t = time();
		$filename = StringFormat("backup_{0}_{1}-{2}-{3}_{4}:{5}_{6}", $name, GetDay($t), GetMonth($t), GetYear($t), GetHour($t), GetMinute($t), substr(md5($password), 0, 16));
		$path = $cfg['root']."/files/".$filename.".sql.gz";
		
		exec(StringFormat("mysqldump --user={0} --password={1} --databases {2} | gzip > {3}", $user, $password, $name, $path));
	}
}

?>
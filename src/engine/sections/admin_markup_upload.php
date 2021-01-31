<style>
.table_button
{
    border-radius: 1px;
    background-size: 100%;
    height: 20px;
    width: 20px;
	border-radius: 3px;
    position: absolute;
}

.file_rename
{
	background-image: url(/engine/assets/pencil.png);
}

.file_resize
{
	background-image: url(/engine/assets/resize_image.png);
}
</style>

<?

$config = GetConfig();
$files = GetFilesList($_SERVER['DOCUMENT_ROOT']."/files");

//Сортировака по дате изменения
function sortByOrder($a, $b) {
	return $b['modified'] - $a['modified'];
}

usort($files, 'sortByOrder');
//

?>
<script>

var DeleteCallback = null;

function ShowDeleteDialog(file_name, file_block)
{
	DeleteCallback = function(){
		DeleteFile(file_name, file_block);
		CloseWindow();
	};
	
	ShowDialog("Удаление", "Файл будет безвозвратно удален", "Удалить", "DeleteCallback();");
}

function DeleteFile(file_name, file_block)
{
	ShowLoader();
	
	ApiMethod("admin.files.delete", { file: file_name }, function(data){
		if(data.response != null)
		{
			if(data.response == 1)
			{
				Hide(file_block);
			}
		}
		
		HideLoader();
	});
}

_CurrentFileName = "";

function RenameFile(file_name, new_file_name)
{
	ShowLoader();
	
	ApiMethod("admin.files.rename", { file: file_name, new_file: new_file_name }, function(data){
		if(data.response != null)
		{
			if(data.response == 1)
			{
				ReloadAsync();
				CloseWindow();
			}
			else
			{
				ShowPanel("Ошибка", 1);
			}
		}
		
		HideLoader();
	});
}

function ResizeImage(file_name, size_rate)
{
	ShowLoader();
	
	ApiMethod("admin.files.resize", { file: file_name, size_rate: size_rate }, function(data){
		if(data.response != null)
		{
			if(data.response == 1)
			{
				ReloadAsync();
				CloseWindow();
			}
			else
			{
				ShowPanel("Ошибка", 1);
			}
		}
		
		HideLoader();
	});
}

function ShowRenameWindow(file_name)
{
	_CurrentFileName = file_name;
	
	ShowWindow("Переименование", Find("rename_form").innerText);
	
	Find("new_file_name").value = _CurrentFileName;
}

function ShowResizeWindow(file_name)
{
	_CurrentFileName = file_name;
	
	ShowWindow("Изменить размер", Find("resize_form").innerText);
	
	Find("resizable_image").src = "/files/" + _CurrentFileName;
}

function ShowUploadDialog()
{
	ShowWindow("Загрузка файла", Find("admin_upload_form").innerText);
}

var UploadQueue = [];

function PushUploadQueue()
{
	Write("uploaded_file", ""); //Трем список загруженных файлов
	//
	var files = Find("upload_form_files").files;
	
	for(var i = 0; i < files.length; i++)
	{
		UploadQueue[UploadQueue.length] = files[i];
	}
}

function UploadFilesQueue()
{
	if(UploadQueue.length == 0)return;
	
	//var files = Find("file").files; //$("#file")[0].files;
	
	var file = UploadQueue[0]; //$("#file")[0].files[0];
	UploadQueue.splice(0, 1); // Удаляем из очереди
	
	SlideHide("upload_form_main");
	SlideShow("upload_form_loading");
	SlideHide("upload_form_file");
	SlideHide("upload_form_error");
	
	AJAX.Progress.onChanged = function(e){
		var ratio = AJAX.Progress.current/AJAX.Progress.total;
		if(Exists("upload_progress_bar"))
		{
			Find("upload_progress_bar").style.width = (ratio * 100) + "%";
		}
	};
	
	AJAX.UploadFile(file, function(response){
		
		var HasNextFile = UploadQueue.length > 0;
		
		console.log(response.srcElement.responseText);
		
		if(response.srcElement.responseText != "")
		{
			var FileName = response.srcElement.responseText;
			var FilePath = "/files/" + FileName;
			
			WriteForward("uploaded_file", "Ссылка: <b>" + FilePath + "</b><div class='space'></div>");
			
			if(!HasNextFile)
			{
				ReloadAsync();
				//
				setTimeout(function(){
					SlideHide("upload_form_main");
					SlideHide("upload_form_loading");
					SlideShow("upload_form_file");
					SlideHide("upload_form_error");
				}, 100);
			}
		}
		else
		{
			setTimeout(function(){
				SlideHide("upload_form_main");
				SlideHide("upload_form_loading");
				SlideHide("upload_form_file");
				SlideShow("upload_form_error");
			}, 100);
		}
		
		if(HasNextFile)
		{
			UploadFilesQueue();
		}
	});
}

//

function ShowUploadStats()
{
	ShowWindow("Статистика файлов", Find("upload_stats").innerText);
}

function ShowImagesBox()
{
	ShowWindow("Изображения", Find("view_images").innerText, 0, 0, 900);
	//ShowWindow("Изображения", Find("view_images").innerText);
}

</script>

<noscript id='admin_upload_form'>
	
	<div id='upload_form_main'>
		<form id='form'>
			<input id="upload_form_files" class='text_input' type="file" name="file" multiple/>
		</form>
		<div class='big_space'></div>
		<div class='button back4' style='margin: auto;' onclick='PushUploadQueue(); UploadFilesQueue();'>Загрузить</div>
		<div class='big_space'></div>
	</div>
	
	<div id='upload_form_loading' style='display: none;'>
		<div class='n_back4' style='margin: 50px; padding: 3px; border-radius: 15px;'>
			<div id='upload_progress_bar' class='n_back3' style='border-radius: 15px; height: 15px; width: 100%;'></div>
		</div>
	</div>
	
	<div id='upload_form_file' style='display: none;'>
		<div class='big_space'></div>
		<div id='uploaded_file' class='text' style='text-align: center;'>...</div>
		<div class='big_space'></div>
	</div>
	
	<div id='upload_form_error' style='display: none;'>
		<div class='big_space'></div>
		<div id='upload_error' class='title_text' style='text-align: center;'>Ошибка загрузки</div>
		<div class='big_space'></div>
	</div>
</noscript>

<noscript id='upload_stats'>
	<table style='width: 100%;'>
		<style>
		.ext_box{
			width: 40px;
			height: 40px;
			line-height: 40px;
			border-radius: 40px;
			text-align: center;
		}
		</style>
		
		<tr class='table_header'>
			<td class='table_column' style='width: 40px;'></td>
			<td class='table_column'><div class='text fore3 inner_center'>Количество</div></td>
			<td class='table_column'><div class='text fore3 inner_center'>Общий размер</div></td>
		</tr>
		<?
		$files_index = array();
		$bytes_count = 0;
		
		for($i = 0; $i < sizeof($files); $i++) //Индексируем файлы
		{
			$name_array = explode(".", $files[$i]['name']);
			$extension = ToUpperCase($name_array[sizeof($name_array) - 1]);
			
			if(!isset($files_index[$extension]))$files_index[$extension] = array(0, 0);
			
			$files_index[$extension][0] += 1;
			$files_index[$extension][1] += $files[$i]['size'];
			$bytes_count += $files[$i]['size'];
		}
		
		foreach($files_index as $key => $value)
		{
			$markup = "
			<tr class='back3'>
				<td class='table_column'><div class='ext_box mini_text fore3 n_back4'>".CropText($key, 4)."</div></td>
				<td class='table_column' style='vertical-align: middle;'><div class='text inner_center'>".$value[0]." ".WordByNumber("файл", $value[0])."</div></td>
				<td class='table_column' style='vertical-align: middle;'><div class='text inner_center'>".GetDataUnits($value[1])."</div></td>
			</tr>
			";
			
			Draw($markup);
		}
		?>
	</table>
	<?
	Draw("<div class='text' style='text-align: right; line-height: 40px;'>Всего занято: ".GetDataUnits($bytes_count)."</div>");
	?>
</noscript>

<noscript id='view_images'>
	<div class='inner_center'>
		<?
		//Создаем кучу преввью картинок
		
		$images = array();
		
		for($i = 0; $i < sizeof($files); $i++)
		{
			$name_array = explode(".", $files[$i]['name']);
			$extension = ToUpperCase($name_array[sizeof($name_array) - 1]);
			
			if($extension == "PNG" || $extension == "JPG" || $extension == "JPEG" || $extension == "GIF" || $extension == "BMP")
			{
				$images[sizeof($images)] = array($files[$i]['name'], $files[$i]['size']);
			}
		}
		
		foreach($images as $image)
		{
			$markup = "
			<div class='back3' style='display: inline-block; padding: 5px;'>
				<div><a href='/files/".$image[0]."' target='_blank'><img src='".GetThumbImageURL("/files/".$image[0], 200)."' alt='' class='n_back4' style='height: 100px;'/></a></div>
				<div class='mini_text'><span class='bold'>".CropText($image[0], 50)."</span> ".GetDataUnits($image[1])."</div>
			</div>
			";
			
			Draw($markup);
		}
		?>
	</div>
</noscript>

<noscript id='rename_form'>
	<input id='new_file_name' placeholder='Имя файла' value='' class='text_input big_input inner_center' style='width: 98%;'/>
	<div class='big_space'></div>
	<div class='button' style='margin: auto;' onclick='RenameFile(_CurrentFileName, Find("new_file_name").value);'>Переименовать</div>
	<div class='big_space'></div>
</noscript>

<noscript id='resize_form'>
	<div class='big_space'></div>
	<div style='text-align: center;'>
		<img id='resizable_image' src='' alt='' style='width: 60%;'></img>
	</div>
	<div class='big_space'></div>
	<select id='size_rate' class='text_input big_input inner_center' style='width: 100%; height: 40px;'>
		<?
		for($i = 1; $i <= 20; $i++)
		{
			$sel = "";
			if($i == 10)$sel = "selected";
			Draw("<option ".$sel." value='".($i * 10)."'>".($i * 10)."%</option>");
		}
		?>
	</select>
	<div class='big_space'></div>
	<div class='button' style='margin: auto;' onclick='ResizeImage(_CurrentFileName, Find("size_rate").value);'>Применить</div>
	<div class='big_space'></div>
</noscript>

<!---->

<div style='height: 40px;'>
	<div style='display: inline-block; float: left;'>
		<div onclick='ShowUploadDialog();' class='button back4' style='display: inline-block;'>Загрузить</div>
	</div>
	<div style='display: inline-block; float: right;'>
		<div onclick='ShowImagesBox();' class='headmenu_button' style='display: inline-block;'>Изображения</div>
		<div onclick='ShowUploadStats();' class='headmenu_button' style='display: inline-block;'>Статистика</div>
	</div>
</div>

<div class='space'></div>
<div class='divider'></div>
<div class='space'></div>

<table style='width: 100%;'>
	<tr class='table_header'>
		<td class='table_column' style='width: 40px;'><div class='text fore3'>#</div></td>
		<td class='table_column'><div class='text fore3'>Имя</div></td>
		<td class='table_column'></td>
		<td class='table_column'><div class='text fore3'>Путь</div></td>
		<td class='table_column'><div class='text fore3'>Размер</div></td>
		<td class='table_column'><div class='text fore3'>Дата загрузки</div></td>
		<td class='table_column'></td>
	</tr>
	
	<?
	
	for($i = 0; $i < sizeof($files); $i++)
	{
		$file_name = $files[$i]['name'];
		$file_path = "/files/".$files[$i]['name'];
		$file_size = $files[$i]['size'];
		$file_modify_date = $files[$i]['modified'];
		
		//
		$display_resize_image = "display: none;";
		if(isImageType($file_name))
		{
			$display_resize_image = "";
		}
		//
		
		$markup = "
		<tr class='back3' id='file_".$i."'>
			<td class='table_column'><div class='text'>".($i + 1).".</div></td>
			<td class='table_column'><a class='text pointer' href='".$file_path."' target='_blank'>".$file_name."</a></td>
			<td class='table_column'>
				<div style='position: relative; width: 50px;'>
					<div class='button table_button file_rename back3 border0' style='position: absolute; left: 0px;' title='Переименовать' onclick='ShowRenameWindow(\"".$file_name."\");'></div>
					<div class='button table_button file_resize back3 border0' style='position: absolute; left: 25px; ".$display_resize_image."' title='Изменить размер' onclick='ShowResizeWindow(\"".$file_name."\");'></div>
				</div>
			</td>
			<td class='table_column'><a class='text pointer' href='".$file_path."' target='_blank'>".$file_path."</a></td>
			<td class='table_column'><div class='text'>".GetDataUnits($file_size)."</div></td>
			<td class='table_column'><div class='text'>".GetTime($file_modify_date)."</div></td>
			<td class='table_column'><div class='link' style='text-align: center;' onclick='ShowDeleteDialog(\"".$file_name."\", \"file_".$i."\");'>Удалить</div></td>
		</tr>
		";
		
		Draw($markup);
	}
	
	?>

</table>

<?

if(sizeof($files) == 0)Draw("<div class='space'></div><div class='text'>Нет файлов...</div><div class='space'></div>");
?>
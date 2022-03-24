<?

$photos = GetGalleryPhotos(1000000, false);

?>

<script>

var DeleteCallback = null;

function ShowDeleteDialog(photo_id, photo_block)
{
	DeleteCallback = function(){
		DeletePhoto(photo_id, photo_block);
		CloseWindow();
	};
	
	ShowDialog("Удаление", "Сущность фото будет удалена", "Удалить", "DeleteCallback();");
}

function DeletePhoto(photo_id, photo_block)
{
	ShowLoader();
	
	ApiMethod("gallery.photo.delete", { id: photo_id }, function(data){
		if(data.response != null)
		{
			if(data.response == 1)
			{
				Hide(photo_block);
			}
		}
		
		HideLoader();
	});
}

function AddPhoto(photo, description)
{
	ShowLoader();
	
	ApiMethod("gallery.photo.add", { photo: photo, description: description }, function(data){
		if(data.response != null)
		{
			if(data.response == 1)
			{
				ReloadAsync();
				CloseWindow();
			}
		}
		
		HideLoader();
	});
}

function EditPhoto(id, photo, description)
{
	ShowLoader();
	
	ApiMethod("gallery.photo.edit", { id: id, photo: photo, description: description }, function(data){
		if(data.response != null)
		{
			if(data.response == 1)
			{
				ReloadAsync();
				CloseWindow();
			}
		}
		
		HideLoader();
	});
}

function ShowAddPhotoWindow()
{
	ShowWindow("Добавление фото", Find("add_photo_window").innerText);
}

_CurrentPhotoID = 0;
function ShowEditPhotoWindow(id, photo, descrition)
{
	ShowWindow("Изменение фото", Find("edit_photo_window").innerText);
	
	_CurrentPhotoID = id;
	Find("photo_path").value = photo;
	Find("photo_description").value = descrition;
}

</script>

<noscript id='add_photo_window'>
	<input id='photo_path' placeholder='Ссылка на фотографию' value='' class='text_input inner_center' style='width: 98%;'/>
	<div class='space'></div>
	<input id='photo_description' placeholder='Описание (необязательно)' value='' class='text_input inner_center' style='width: 98%;'/>
	<div class='space'></div>
	<div class='button' style='margin: auto;' onclick='AddPhoto(Find("photo_path").value, Find("photo_description").value);'>Добавить</div>
</noscript>

<noscript id='edit_photo_window'>
	<input id='photo_path' placeholder='Путь к фото' value='' class='text_input big_input inner_center' style='width: 98%;'/>
	<div class='big_space'></div>
	<input id='photo_description' placeholder='Описание' value='' class='text_input inner_center' style='width: 98%;'/>
	<div class='big_space'></div>
	<div class='button' style='margin: auto;' onclick='EditPhoto(_CurrentPhotoID, Find("photo_path").value, Find("photo_description").value);'>Добавить</div>
	<div class='big_space'></div>
</noscript>

<div class='button back4' onclick='ShowAddPhotoWindow();'>Добавить фото</div>
	
<div class='space'></div>
<div class='divider'></div>
<div class='space'></div>

<table style='width: 100%;'>
	<tr class='table_header'>
		<td class='table_column' style='width: 40px;'><div class='text fore3'>#</div></td>
		<td class='table_column'><div class='text fore3'>Фото</div></td>
		<td class='table_column'><div class='text fore3'>Путь</div></td>
		<td class='table_column'><div class='text fore3'>Описание</div></td>
		<td class='table_column'><div class='text fore3'>Просмотры</div></td>
		<td class='table_column'><div class='text fore3'>Дата</div></td>
		<td class='table_column'></td>
		<td class='table_column'></td>
	</tr>
	
	<?
	
	for($i = 0; $i < sizeof($photos); $i++)
	{
		$markup = "
		<tr class='back3' id='photo_".$i."'>
			<td class='table_column'><div class='text'>".($i + 1).".</div></td>
			<td class='table_column'><img style='max-width: 200px;' src='".$photos[$i]['photo']."' onclick='ImageBox(\"".$photos[$i]['photo']."\");' alt=''/></td>
			<td class='table_column'><a class='link' href='".$photos[$i]['photo']."' target='_blank'>".$photos[$i]['photo']."</a></td>
			<td class='table_column'>".CropText(FilterText($photos[$i]['description'], "user_text"), 30)."</td>
			<td class='table_column'><div class='text'>".$photos[$i]['views']."</div></td>
			<td class='table_column'><div class='text'>".GetTime($photos[$i]['date'])."</div></td>
			<td class='table_column'><div class='link' style='text-align: center;' onclick='ShowEditPhotoWindow(".$photos[$i]['id'].", \"".$photos[$i]['photo']."\", \"".$photos[$i]['description']."\");'>Изменить</div></td>
			<td class='table_column'><div class='link' style='text-align: center;' onclick='ShowDeleteDialog(".$photos[$i]['id'].", \"photo_".$i."\");'>Удалить</div></td>
		</tr>
		";
		
		Draw($markup);
	}
	
	?>
	
</table>

<?

if(sizeof($photos) == 0)Draw("<div class='text padding'>Нет фотографий...</div>");

?>
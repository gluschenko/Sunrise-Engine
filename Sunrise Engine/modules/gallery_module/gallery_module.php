<?
//Модуль галереи

AddTable("gallery_photos", array(
	TableField("id", "int(11)"),
	TableField("date", "int(11)"),
	TableField("photo", "text"),
	TableField("description", "text"),
	TableField("views", "int(11)"),
	TableField("last_view_time", "int(11)"),
	TableField("view_time_delta", "int(11)"),
	TableField("status", "int(11)"),
));

//

$cfg = GetConfig();

AddSection(array(
	"type" => 0,
	"name" => "gallery",
	"header" => "Галерея",
	"title" => "Галерея - {site_name}",
	"layout" => 0,
	"permission" => 0,
	"keywords" => "фотографии, галерея",
	"description" => "Фотографии.",
	"section_dir" => "/modules/gallery_module/sections",
));
//
$sections_dir = dirname(__FILE__)."/sections";

AddAdminSection("gallery", "Управление галереей", $sections_dir."/admin_markup_gallery.php");
//
AddAdminLink("/admin?act=gallery", "Управление галереей", 0);
//

AddMethod("gallery.photo.add", function($params){
	$photo = Security::String($params['photo']);
	$description = Security::String(FilterText($params['description'], "database_text"));
	
	if(isAdminLogged())
	{
		if(AddGalleryPhoto($photo, $description))
		{
			LogAction("Добавлено фото в галерею", 0);
			return 1;
		}
	}
	
	return 0;
});

AddMethod("gallery.photo.edit", function($params){
	$id = Security::String($params['id']);
	$photo = Security::String($params['photo']);
	$description = Security::String(FilterText($params['description'], "database_text"));
	
	if(isAdminLogged())
	{
		if(EditGalleryPhoto($id, $photo, $description))
		{
			LogAction("Изменено фото в галерее", 0);
			return 1;
		}
	}
	
	return 0;
});

AddMethod("gallery.photo.delete", function($params){
	$id = Security::String($params['id']);
	
	if(isAdminLogged())
	{
		if(DeleteGalleryPhoto($id))
		{
			LogAction("Удалено фото из галереи", 0);
			return 1;
		}
	}
	
	return 0;
});

AddMethod("gallery.photo.view", function($params){
	$id = Security::String($params['id']);
	
	AddViewsToGalleryPhoto($id);
	
	return 1;
});

//

function GetGalleryPhotos($number = 5, $sort = false)
{
	$photos = array();
	
	$current_number = 0;
	
	$order = "date DESC";
	if($sort)$order = "view_time_delta ASC";
	
	$query = SQL("SELECT * FROM `gallery_photos` WHERE `status`='0' ORDER BY ".$order." LIMIT ".$number);
	while($row = SQLFetchAssoc($query))
	{
		$photos[sizeof($photos)] = $row;
	}
	
	return $photos;
}

function AddGalleryPhoto($photo_path, $description)
{
	$newid = GetNewId("gallery_photos", "id");
	$time = time();
	
	$query = SQL("INSERT INTO `gallery_photos`(`id`, `date`, `photo`, `description`, `status`) 
	VALUES ('$newid','$time','$photo_path','$description', '0')");
	
	return true;
}

function EditGalleryPhoto($id, $photo_path, $description)
{
	$query = SQL("UPDATE `gallery_photos` SET photo = '$photo_path', description = '$description' WHERE id = '$id'");
	
	return true;
}

function DeleteGalleryPhoto($id)
{
	$query = SQL("UPDATE `gallery_photos` SET status = '-1' WHERE id = '$id'");
	
	return true;
}

function AddViewsToGalleryPhoto($id)
{
	$query = SQL("SELECT * FROM `gallery_photos` WHERE id = '$id' LIMIT 1");
	$row = SQLFetchAssoc($query);
	
	$last_view_time = $row['last_view_time'] != "" ? $row['last_view_time'] : 0;
	if($last_view_time == 0)$last_view_time = time();
	$time = time();
	
	$view_time_delta = $time - $last_view_time;
	
	$query = SQL("UPDATE `gallery_photos` SET views = (views + 1), last_view_time = '$time', view_time_delta = '$view_time_delta' WHERE id = '$id'");
	
	return true;
}

//

function GetGalleryStyle()
{
	return "
	<style>
	.gallery_photo
	{
		width: 170px;
		height: 170px;
		margin: 3px;
		border-radius: 4px;
		cursor: pointer;
		background-size: cover, cover;
		background-position: center center;
		display: inline-block;
		position: relative;
	}
	
	.gallery_photo_new_mark
	{
		bottom: 3px;
		right: 3px;
		border-radius: 2px;
		display: inline-block;
		padding: 2px;
		padding-left: 4px;
		padding-right: 4px;
		position: absolute;
	}
	</style>
	";
}

//

//Виджет
OnLoad(function(){
	InitGalleryWidget();
});

function InitGalleryWidget()
{
	$photos = GetGalleryPhotos(6, true);
	
	$markup = "";
	
	for($i = 0; $i < sizeof($photos); $i++)
	{
		$id = $photos[$i]['id'];
		$photo = $photos[$i]['photo'];
		$description = FilterText($photos[$i]['description'], "user_text");
		$views = $photos[$i]['views'];
		$date = $photos[$i]['date'];
		
		$new_markup = "<div class='gallery_photo_new_mark n_back4 fore3 small_text'>Новое</div>";
		if(time() - $date > (30 * 24 * 3600))$new_markup = "";
		
		$markup .= "
		<div class='gallery_photo' title='Просмотры: ".$views."' style='width: 105px; height: 105px; background-image: url(".$photo.");' onclick='PushGalleryView(".$id."); ImageBox(\"".$photo."\", \"".$description."\");'>
			".$new_markup."
		</div>
		";
	}
	
	if(sizeof($photos) == 0)$markup = "<div class='text'>Пока нет фотографий</div>";
	
	AddTemplate("gallery_widget", "
	<!--Gallery widget-->
	<script>
	function PushGalleryView(id)
	{
		API.CallMethod('gallery.photo.view', { id: id }, function(data){});
	}
	</script>
	<div>
		".GetGalleryStyle()."
		<div class='inner_center'>
		".$markup."
		</div>
	</div>
	");
}

//

?>
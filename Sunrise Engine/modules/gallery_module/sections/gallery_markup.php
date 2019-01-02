<?

$photos = GetGalleryPhotos(1000000, true);

?>

<script>
function PushGalleryView(id)
{
	API.CallMethod('gallery.photo.view', { id: id }, function(data){});
}
</script>

<div class='inner_center'>
<?

Draw(GetGalleryStyle());

for($i = 0; $i < sizeof($photos); $i++)
{
	$id = $photos[$i]['id'];
	$photo = $photos[$i]['photo'];
	$description = FilterText($photos[$i]['description'], "user_text");
	$views = $photos[$i]['views'];
	$date = $photos[$i]['date'];
	
	$new_markup = "<div class='gallery_photo_new_mark n_back4 fore3 small_text'>Новое</div>";
	if(time() - $date > (30 * 24 * 3600))$new_markup = "";
	
	$markup = "
	<div class='gallery_photo' title='Просмотры: ".$views."' style='background-image: url(".$photo.");' onclick='PushGalleryView(".$id."); ImageBox(\"".$photo."\", \"".$description."\");'>
		".$new_markup."
	</div>
	";
	
	Draw($markup);
}

if(sizeof($photos) == 0)Draw("<div class='text'>Пока нет фотографий</div>");

?>
</div>
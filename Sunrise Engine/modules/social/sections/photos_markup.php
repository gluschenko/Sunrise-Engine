<?php

if($section['logged'])
{
	$user_id = GetData("user_id", 0);
	$photos = GetContentList($user_id);
	
	?>
	
	<div id='social_layout'>
		<div class='content_box_parent'>
			<div class='content_substarate'></div>
				<div class='content_box'>
					<? Draw(Markup("social_sidebar")); ?>
					
					<div class='social_content_wrap'>
						<div style='min-height: 500px;'>
							<div class='box social_content_wrap responsive_content_box' style='width: 750px;'>
								<div>
									<? Draw(Markup("upload_window")); ?>
									<?
									if(sizeof($photos) > 0)
									{
										Draw("
										<div class='padding'>
											<div class='title_text'>Фотографии <b><span class='fore4'>".sizeof($photos)."</span></b></div>
										</div>
										<div class='divider'></div>
										<div class='padding'>
											<div class='button back4' style='margin: auto; width: 120px;' onclick='ShowUploadDialog();'>Загрузить</div>
										</div>
										<div class='divider'></div>
										<div class='padding inner_center'>
										");
										
										for($i = 0; $i < sizeof($photos); $i++)
										{
											Draw("
											<img alt='' class='content_image large_content_image' src='".$photos[$i]['data']."' onclick='ImageBox(\"".$photos[$i]['data']."\", \"Загружено ".GetTime($photos[$i]['created'])."\");'/>
											");
										}
										
										Draw("</div>");
									}
									else
									{
										Draw("
										<div class='padding'>
											<div style='height: 200px;'></div>
											<div class='title_text' style='text-align: center;'>Фотографий нет</div>
											<div style='height: 40px;'></div>
											<div class='button back4' style='margin: auto; width: 120px;' onclick='ShowUploadDialog();'>Загрузить</div>
											<div style='height: 200px;'></div>
										</div>
										");
									}
									?>
									<div class='divider'></div>
									<div class='space'></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php Draw(Markup("big_footer_markup"));?>
	</div>
	
	<?
}
else
{
	?>
	<script>
		ShowLoader();
		setTimeout(function(){ 
			Navigate("/board"); 
		}, 100);
	</script>
	<?
}

?>
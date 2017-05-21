<?php
$engine_cfg = GetEngineConfig();
?>

<div id='social_layout'>
	<div class='content_box_parent'>
			<div class='content_substarate'></div>
				<div class='content_box'>
					<? Draw(Markup("social_sidebar")); ?>
					
					<div class='social_content_wrap'>
						<div style='min-height: 500px;'>
							<div class='box social_content_wrap responsive_content_box' style='width: 750px;'>
								<?php
								
								$id = (isset($section['url_data'][1]))? $section['url_data'][1] : 0;
								$post = GetPost($id);
								
								//
								
								if($post['status'] != -1 && $post['status'] != -2)
								{
									Draw("
									<div class='padding'>
										<div class='title_text'>Запись</div>
									</div>
									<!--<div class='divider'></div>-->
									");
									
									Draw(GetPostMarkup($post));
								}
								else
								{
									Draw("
									<div style='height: 200px;'></div>
									<div class='title_text' style='text-align: center;'>Записи не существует</div>
									<div style='height: 200px;'></div>
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
	<?php Draw(Markup("big_footer_markup"));?>
</div>

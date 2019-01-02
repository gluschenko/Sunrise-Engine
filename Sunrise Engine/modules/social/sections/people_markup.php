<?php

$users = array();

$query = SQL("SELECT `id` FROM `profiles` WHERE 1 ORDER BY `last_seen` DESC LIMIT 0, 1000");
$inc = 0;

while($row = SQLFetchAssoc($query))
{
	$users[$inc] = GetProfile($row['id']);
	$inc++;
}

?>

<div id='social_layout'>
	<div class='content_box_parent'>
			<div class='content_substarate'></div>
				<div class='content_box'>
					<? Draw(Markup("social_sidebar")); ?>
					
					<div class='social_content_wrap'>
						<div style='min-height: 500px;'>
							<div class='box social_content_wrap responsive_content_box' style='width: 750px;'>
								<div class='padding'>
									<div class='title_text'>Пользователи <b><span class='fore4'><? echo(sizeof($users)); ?></span></b></div>
								</div>
								
								<div class='divider'></div>
								
								<div>
									<?php
									
									if(sizeof($users) > 0)
									{
										for($i = 0; $i < sizeof($users); $i++)
										{
											Draw("
											<a href='/".$users[$i]['link']."' onclick='return NavAsync(this.href, true);'>
												<div id='user_".$users[$i]['id']."' class='padding back3 pointer'>
													<table>
														<tr>
															<td><div class='profile_avatar' style='width: 60px; height: 60px; background-image: url(".$users[$i]['avatar'].");'></div></td>
															<td style='width: 10px;'></td>
															<td><div class='title_text' style='line-height: 60px;'>".$users[$i]['name']." <span class='fore2'>".GetOnline($users[$i]['last_seen'], "●")."</span></div></td>
														</tr>
													</table>
												</div>
											</a>
											");
											
											if($i != sizeof($users) - 1)
											{
												Draw("<div class='divider'></div>");
											}
										}
									}
									else
									{
										Draw("
										<div style='height: 200px;'></div>
										<div class='title_text' style='text-align: center;'>Пользователей нет</div>
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
	</div>
	<?php Draw(Markup("big_footer_markup"));?>
</div>

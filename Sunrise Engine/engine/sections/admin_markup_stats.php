<?

if(true)
{
	if(true)
	{
		$selected_section = "";
		if(isset($request['section']))$selected_section = $request['section'];
		
		$selected_ip = "";
		if(isset($section['request']['ip']))$selected_ip = $section['request']['ip'];
		
		?>
		
		<table>
			<tr>
				<td style='min-width: 350px;'>
					<div class='scroll_block' style='height: 795px;'>
					<?
					$engine_config = GetEngineConfig();
					
					$sections = $engine_config['sections'];
					
					$sections_data = array();
					
					foreach($sections as $key => $value)
					{
						//$section_obj = $value; //$sections[$key];
						//$view_stats = GetSectionViews($key);
						
						$sections_data[sizeof($sections_data)] = array(
							"name" => $key,
							"section" => $value,
							"views" => GetSectionViews($key),
						);
					}
					// Сортировка по уникам
					function stats_cmp($a, $b)
					{
						if ($a['views'][1] == $b['views'][1]) {
							return 0;
						}
						return ($a['views'][1] > $b['views'][1]) ? -1 : 1;
					}

					usort($sections_data, "stats_cmp");
					//
					for($i = 0; $i < sizeof($sections_data); $i++)
					{
						$sect = $sections_data[$i];
						
						$m = "
						<a href='/admin?act=stats&section=".$sect["name"]."' onclick='return NavAsync(this.href, true);'>
							<div class='padding back3' style='height: 17px;'>
								<div class='text' style='float: left; display: inline-block' title='".$sect["section"]["header"]."'><b>/".$sect["name"]."</b></div>
								<div style='float: right;' title='Просмотры и уникальные посетители за сегодня.'>
									<div class='sign_text pointer' style='padding-left: 5px;'>".$sect["views"][2]." ms</div>
									<div class='inline_space'></div>
									<div class='views sign_text pointer'>".$sect["views"][0]."</div>
									<div class='inline_space'></div>
									<div class='unique sign_text pointer'>".$sect["views"][1]."</div>
								</div>
							</div>
						</a>
						";
						
						Draw($m);
					}
					?>
					</div>
				</td>
				<td style='min-width: 10px;'></td>
				<td style='width: 100%;'>
					<?
					
					$label_caption = "Весь сайт";
					if($selected_section != "")$label_caption = "/".$selected_section;
					
					?>
					<a class='link title_text' href='/<? echo($selected_section); ?>' onclick='return NavAsync(this.href, true);'><? echo($label_caption); ?></a>
					<div class='space'></div>
					<div class='divider'></div>
					<div class='space'></div>
					<?php
					$target_section = $selected_section;
					if($selected_section == "")$target_section = "all";
					
					$daily_views = GetSectionViews($target_section);
					$monthly_views = GetSectionViews($target_section, (time() - (24 * 60 * 60 * 30)), false);
					$overall_views = GetSectionViews($target_section, 1, false);
					
					Draw("
					<table class='inner_center' style='width: 100%;'>
						<tr>
							<td style='width: 30%;'>
								<div class='text'>Сегодня</div>
								<div class='space'></div>
								<div class='views sign_text pointer'>".$daily_views[0]."</div>
								<div class='inline_space'></div>
								<div class='unique sign_text pointer'>".$daily_views[1]."</div>
								<div class='inline_space'></div>
								<div class='sign_text pointer' style='padding-left: 5px;'>".$daily_views[2]." ms</div>
							</td>
							<td style='width: 30%;'>
								<div class='text'>Последние 30 дней</div>
								<div class='space'></div>
								<div class='views sign_text pointer'>".$monthly_views[0]."</div>
								<div class='inline_space'></div>
								<div class='unique sign_text pointer'>".$monthly_views[1]."</div>
								<div class='inline_space'></div>
								<div class='sign_text pointer' style='padding-left: 5px;'>".$monthly_views[2]." ms</div>
							</td>
							<td style='width: 30%;'>
								<div class='text'>Всё время</div>
								<div class='space'></div>
								<div class='views sign_text pointer'>".$overall_views[0]."</div>
								<div class='inline_space'></div>
								<div class='unique sign_text pointer'>".$overall_views[1]."</div>
								<div class='inline_space'></div>
								<div class='sign_text pointer' style='padding-left: 5px;'>".$overall_views[2]." ms</div>
							</td>
						</tr>
					</table>
					");
					?>
					<div class='space'></div>
					<div class='divider'></div>
					<div class='space'></div>
					<?
					
					$stats_target = "all";
					if($selected_section != "")$stats_target = $selected_section;
					
					Draw(GraphMarkup(1, $stats_target, 60));
					?>
					<div class='space'></div>
					<div class='divider'></div>
					<div class='space'></div>
					<div class='scroll_block' style='height: 500px;'>
						<table style='width: 100%;'>
							<tr class='table_header'>
								<td class='table_column'>
									<div class='small_text fore3'>IP</div>
								</td>
								<td class='table_column'>
									<div class='small_text fore3'>Дата</div>
								</td>
								<td class='table_column'>
									<div class='small_text fore3'>Браузер</div>
								</td>
								<td class='table_column'>
									<div class='small_text fore3'>ОС</div>
								</td>
								<td class='table_column'>
									<div class='small_text fore3'>Время</div>
								</td>
								<td class='table_column'>
									<div class='small_text fore3'>URL</div>
								</td>
								<td class='table_column'>
									<div class='small_text fore3'>Источник</div>
								</td>
							</tr>
							
							<?
							
							$where = "";
							if($selected_section != "")
							{
								$where = "`section`='$selected_section'";
							}
							
							if($selected_ip != "")
							{
								if($where != "")$where .= " AND ";
								$where .= "`ip`='$selected_ip'";
							}
							
							if($where == "")$where = "1";
							
							///
							
							$query = SQL("SELECT * FROM `views` WHERE $where ORDER BY `views`.`date` DESC LIMIT 0, 500");
							
							$iterations = 0;
							while($row = SQLFetchAssoc($query))
							{
								$id = $row['id'];
								$ip = $row['ip'];
								$date = GetTime($row['date']);
								$section_ = $row['section'];
								$url = $row['url'];
								$referer = $row['referer'];
								$user_agent = $row['user_agent'];
								$startup_time = $row['startup_time'];
								
								//
								$ip_array = explode(".", $ip);
								$ip_link = "?act=stats";
								if($selected_section != "")$ip_link .= "&section=".$selected_section;
								if($selected_ip == "")$ip_link .= "&ip=".$ip;
								
								$ip_red = isset($ip_array[0])? $ip_array[0] : 100;
								$ip_green = isset($ip_array[1])? $ip_array[1] : 100;
								$ip_blue = isset($ip_array[2])? $ip_array[2] : 100;
								
								$is_bot = isUserBot($user_agent);
								$disabled = "";
								if($is_bot)$disabled = "disabled";
								
								$referer = str_replace("https://".$_SERVER['HTTP_HOST'], "", $referer);
								$referer = str_replace("http://".$_SERVER['HTTP_HOST'], "", $referer);
								
								$m = "
								<tr class='back3 ".$disabled."'>
									<td class='table_column' style='border-left: solid 5px rgb(".$ip_red.", ".$ip_green.", ".$ip_blue.");'>
										<a class='link small_text' href='".$ip_link."' onclick='return NavAsync(this.href, true);' title='".$ip."'>".CropText($ip, 15)."</a>
									</td>
									<td class='table_column'>
										<div class='small_text'>".$date."</div>
									</td>
									<td class='table_column'>
										<div class='small_text' title='User Agent: ".$user_agent."'>".GetBrowser($user_agent)."</div>
									</td>
									<td class='table_column'>
										<div class='small_text' title='User Agent: ".$user_agent."'>".GetOS($user_agent)."</div>
									</td>
									<td class='table_column'>
										<div class='small_text'>".$startup_time." ms</div>
									</td>
									<td class='table_column'>
										<a class='small_text link' href='".$url."' title='".$url."' onclick='return NavAsync(this.href, true);'>".CropText($url, 25)."</a>
									</td>
									<td class='table_column'>
										<a class='small_text link' href='".$referer."' title='".$referer."' onclick='return NavAsync(this.href, true);'>".CropText($referer, 25)."</a>
									</td>
								</tr>
								";
								
								Draw($m);
								
								///
								
								$iterations++;
							}
							
							?>
							
						</table>
						<?
						
						if($iterations == 0)Draw("<div class='text padding'>Нет данных...</div>");
						
						?>
					</div>
				</td>
			</tr>
		</table>
		<?
	}
}

?>
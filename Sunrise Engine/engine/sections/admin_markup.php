<style>
body{
    min-width: 1140px;
	background-image: none;
	background-color: #eee;
}
</style>

<script>

function AdminLogout()
{
	ShowLoader();
	
	ApiMethod("admin.logout", { }, function(data){ 
		if(data.response)
		{
			AJAXLayout.Reload();
		}
		
		HideLoader();
	});
}

</script>

<?php

if(!$section['logged'])
{
	?>
	
	<script>
	function AdminAuth(password)
	{
		ShowLoader();
		
		ApiMethod("admin.auth", { password: password }, function(data){ 
			if(data.response != null)
			{
				if(data.response != 0)
				{
					ReloadAsync();
				}
				else
				{
					var markup = "<div class='text fore4'>Неверный пароль</div><div class='space'></div>";
					Write("auth_info", markup);
				}
			}
			
			HideLoader();
		});
	}
	</script>
	
	<input type='password' class='text_input big_input' style='width: 98%;' id='password' onkeypress='if(event.keyCode == 13)Find("auth_button").click();'/>
	<div class='space'></div>
	<div id='auth_info'></div>
	<div id='auth_button' class='button back4' style='margin: auto;' onclick='AdminAuth(Find("password").value);'>Войти</div>
	
	<?
}
else
{
	$request = $section['request'];
	if(!isset($request['act']))$request['act'] = "";
	
	if($request['act'] == "")
	{
		?>
		
		<div style='height: 40px;'>
			<a href='/admin?act=news' onclick='return NavAsync(this.href, true);' style='display: inline-block;'><div class='headmenu_button'>Новости</div></a>
			<a href='/admin?act=templates' onclick='return NavAsync(this.href, true);' style='display: inline-block;'><div class='headmenu_button'>Шаблоны</div></a>
			<a href='/admin?act=stats' onclick='return NavAsync(this.href, true);' style='display: inline-block;'><div class='headmenu_button'>Статистика</div></a>
			<a href='/admin?act=system' onclick='return NavAsync(this.href, true);' style='display: inline-block;'><div class='headmenu_button'>Система</div></a>
		</div>
		
		<div class='space'></div>
		<div class='divider'></div>
		
		<table style='width: 100%;'>
			<tr>
				<?php
				$engine_config = GetEngineConfig();
				$link_pattern = "<a href='{href}' {external} class='menu_button' style='height: 40px; line-height: 40px;'>{title}</a>";
				
				if(sizeof($engine_config['admin_links']) > 0){
					$admin_links_markup = "
					<td rowspan='2' style='border-right: solid 1px #bbb;'>
						<div style='width: 250px; overflow-y: auto;'>
							".GetLinksList($engine_config['admin_links'], $link_pattern)."
						</div>
					</td>
					";
					
					Draw($admin_links_markup);
				}
				?>
				
				<th colspan="2">
					<style>
					.activity_thumb{
						display: inline-block;
						width: 10px;
						height: 10px;
						border-radius: 1px;
						margin: 1px;
						border: 0;
					}
					
					.activity_thumb:hover{
						margin: 0px;
						border: solid #999 1px;
					}
					
					.activity_frame
					{
						margin-bottom: 16px;
						margin-top: 16px;
					}
					
					.activity_header
					{
						margin-top: 16px;
						text-align: center;
					}
					</style>
					
					<div style='text-align: center;'>
						<div class='title_text activity_header'>Активность</div>
						<div class='activity_frame' style='text-align: left; display: inline-block;'>
						<?php
						
						$markup = "";
						
						$activity_colors = array("#eeeeee", "#d6e685", "#8cc665", "#44a340", "#1e6823");
						
						$activity_cols = 64;
						$activity_rows = 7;
						$days_number = $activity_cols * $activity_rows;
						$days_index = array();
						
						$time = time();
						for($d = 0; $d < $days_number; $d++)
						{
							$day = GetDay($time);
							$month = GetMonth($time);
							$year = GetYear($time);
							$date_name = StringFormat("{0}.{1}.{2}", $day, $month, $year);
							$days_index[$date_name] = 0;
							
							$time -= (24 * 60 * 60);
						}
						
						$max_activity = 0;
						$activity_time_border = time() - ($activity_cols * $activity_rows * 24 * 60 * 60);
						$query = SQL("SELECT `id`, `date` FROM `log` WHERE `date` > '$activity_time_border' ORDER BY `date` DESC");
						while($row = SQLFetchAssoc($query))
						{
							$id = $row['id'];
							$date = $row['date'];
							
							$day = GetDay($date);
							$month = GetMonth($date);
							$year = GetYear($date);
							$date_name = StringFormat("{0}.{1}.{2}", $day, $month, $year);
							
							if(isset($days_index[$date_name]))
							{
								$days_index[$date_name]++;
								if($days_index[$date_name] > $max_activity)$max_activity = $days_index[$date_name];
							}
						}
						
						$days_index = array_reverse($days_index);
						
						for($y = 0; $y < $activity_rows; $y++)
						{
							$markup .= "<div style='height: 12px;'>";
							
							for($x = 0; $x < $activity_cols; $x++)
							{
								$i = $y + ($activity_rows * $x);
								
								$keys = array_keys($days_index);
								$activity_value = $days_index[$keys[$i]];
								$ratio = sqrt($activity_value/$max_activity);
								$thumb_color = $activity_colors[ceil($ratio * (sizeof($activity_colors) - 1))];
								
								$markup .= "<div class='activity_thumb tooltip' data-tooltip='Активность: ".$activity_value." (".$keys[$i].")' style='background-color: ".$thumb_color.";'></div>";
							}
							
							$markup .= "</div>";
						}
						
						$markup .= "<div style='height: 12px; margin-top: 8px; text-align: right;'>";
						
						for($i = 0; $i < sizeof($activity_colors); $i++)
						{
							$markup .= "<div class='activity_thumb' style='background-color: ".$activity_colors[$i].";'></div>";
						}
						
						$markup .= "</div>";
						
						Draw($markup);
						
						?>
						</div>
					</div>
				</th>
			</tr>
			<tr>
				<?
				$query = SQL("SELECT * FROM `log` WHERE 1 ORDER BY `log`.`id` DESC LIMIT 0, 1000");
				
				$logs = array();
				while($row = SQLFetchAssoc($query))
				{
					$logs[] = $row;
				}
				
				$logs = array_reverse($logs);
				?>
				
				<td style='width: 50%; border-right: solid 1px #bbb; border-bottom: solid 1px #bbb;'>
					<div class='n_back4 fore3 padding text'>Записи</div>
					<div class='divider'></div>
					<div id='notes_log' class='scroll_block'>
						<div>
						<?
						foreach($logs as $row)
						{
							if($row['type'] == 2)
							{
								$m = ActionMarkup($row);
								Draw($m);
							}
						}
						
						Draw("<div id='async_actions'></div>");
						
						?>
						</div>
					</div>
					<div class='inner_center'>
						<div class='space'></div>
						<textarea id='action_text' placeholder='Заметка' value='' class='text_input at_center' style='width: 96%; height: 50px;'></textarea>
						<div class='space'></div>
						<div class='small_button back3 fore0 border0 at_center' style='width: 20%;' onclick='SendLogAction();'>Отправить</div>
						<div class='space'></div>
						
						<script>
						function SendLogAction()
						{
							var text = Find("action_text").value;
							
							if(text != "")
							{
								ShowLoader();
								
								ApiMethod("admin.log.send", { text: text, need_markup: 1 }, function(data){
									//console.log(data);
									
									if(data.response != null)
									{
										WriteEnd("async_actions", data.response[1]);
										Find("action_text").value = "";
										ScrollDownLogs();
										HideLoader();
									}
								});
							}
						}
						</script>
					</div>
				</td>
				<td style='width: 50%;'>
					<div class='n_back4 fore3 padding text'>Действия</div>
					<div class='divider'></div>
					<div id='system_log' class='scroll_block'>
						<div>
						<?
						$logs_by_month = array();
						$last_month = -1;
						$last_year = -1;
						
						foreach($logs as $row)
						{
							if($row['type'] != 2)
							{
								$current_month = GetMonth(time());
								$action_month = GetMonth($row['date']);
								$action_year = GetYear($row['date']);
								
								//
								
								if($action_month != $last_month && $last_month != -1)
								{
									Draw("
									</div>
									<div class='divider'></div>
									");
								}
								if($action_month != $last_month && $current_month != $action_month)
								{
									$mname = MonthByNumber($action_month);
									
									Draw("
									<div class='text fore4 back3' style='padding: 10px;' onclick='SlideToggle(\"stack_".$action_month."\");'>
										".$mname[0]." ".$action_year."
									</div>
									<div id='stack_".$action_month."' style='display: none;'>
									");
								}
								
								//
								
								$m = ActionMarkup($row);
								Draw($m);
								
								$last_month = $action_month;
								$last_year = $action_year;
							}
						}
						
						if(sizeof($logs) == 0)Draw("<div class='text padding'>Пока нет записей</div>");
						?>
						</div>
					</div>
				</td>
			</tr>
		</table>
		
		<script>
			setTimeout(function(){ 
				ScrollDownLogs();
			}, 100);
			
			function ScrollDownLogs()
			{
				Find("notes_log").scrollTop = Find("notes_log").scrollHeight; 
				Find("system_log").scrollTop = Find("system_log").scrollHeight; 
			}
		</script>
		<?
	}
	else
	{
		//Секции админки
		$engine_cfg = GetEngineConfig();
		
		if(isset($engine_cfg['admin_sections'][$request['act']]))
		{
			$admin_section = $engine_cfg['admin_sections'][$request['act']];
			
			//echo($admin_section['path']);
			if(file_exists($admin_section['path']))
			{
				require($admin_section['path']);
			}
			else 
			{
				Draw("<div class='text'>Нет исполняемого файла: ".$admin_section['path']."</div>");
			}
		}
		else
		{
			Draw("<div class='title_text'>Секция не определена</div>");
		}
	}
	
}
?>
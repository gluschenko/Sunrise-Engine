<?
//Компонент новостей
//Alexander Gluschenko (2-04-2016)

require("news_init.php");
require("news_api.php");
require("events_module.php"); // Запускаем помёрженный с модулем news модуль events

OnLoad(function(){
	InitNewsWidget();
});

AddCSS("/modules/news/assets/news.css");

/** Инициализация новостного виджета*/
function InitNewsWidget() 
{
	$news_posts = GetNews(3, 0, false);

	$posts_thumbs = "";
	
	/*$posts_number = sizeof($news_posts);
	if($posts_number > 4)$posts_number = 4; //Ограничиваем итерации до требуемого числа постов*/
	
	$posts_thumbs .= "<div class='inner_center'>";
	
	for($i = 0; $i < sizeof($news_posts); $i++)
	{
		$id = $news_posts[$i]['id'];
		$header = $news_posts[$i]['header'];
		$date = $news_posts[$i]['created'];
		
		$preview_image = $news_posts[$i]['preview_image'];
		$author = $news_posts[$i]['author'];
		$category = $news_posts[$i]['category'];
		
		$day = GetDay($date);
		$month = GetMonth($date);
		$year = GetYear($date);
		
		$month_name = MonthByNumberGenitive($month);
		//
		$category_markup = "";
		if($category != "")
		{
			$cat_style = WordByWord($category, array("n_back0", "n_back1", "n_back2", "n_back4", "n_back5"));
			$category_markup = "<div class='label small_text slim_shadow news_snippet_canegory ".$cat_style."'>".CropText($category, 20)."</div>";
		}
		
		$preview_markup = "";
		if($preview_image != "")
		{
			$preview_markup = "style='background-image: url(".$preview_image.");'";
		}
		//
		$posts_thumbs .= "
		<a href='/news?post=".$id."' onclick='AJAXLayout.Navigate(event, this.href);' class='news_snippet_wrap back3'>
			<div class='news_preview_image' ".$preview_markup.">
				".$category_markup."
			</div>
			<div style='padding-left: 4px; padding-right: 4px;'>
				<div class='small_text bold' style='margin-top: 4px; text-align: left;'>".CropText($header, 60)."</div>
				<div class='small_text fore6' style='margin-top: 4px; text-align: left;'>".$day." ".$month_name." ".$year."</div>
			</div>
		</a>
		";
	}
	
	$posts_thumbs .= "</div>";

	AddTemplate("news_widget", "
	<!--News widget-->
	<div>
		".$posts_thumbs."
	</div>
	");
}


//API
/** Возвращает массив новостей */
function GetNews($number = 5, $offset = 0, $count_views = true)
{
	$posts = array();
	
	$query = SQL("SELECT * FROM `news` WHERE `status`='0' ORDER BY `news`.`created` DESC LIMIT $offset, $number");
	
	$ids = array();
	while($row = SQLFetchAssoc($query))
	{
		$ids[] = $row['id'];
		$row['text'] = InterpretMarkup($row['text']);
		
		$posts[] = $row;
	}
	
	if($count_views)SetNewsViews($ids);
	
	return $posts;
}

/** Возвращает новость по ID */
function GetNewsByID($id)
{
	$posts = array();
	
	$query = SQL("SELECT * FROM `news` WHERE `id`='$id' AND `status`='0' LIMIT 1");
	
	$ids = array();
	while($row = SQLFetchAssoc($query))
	{
		$ids[] = $row['id'];
		$row['text'] = InterpretMarkup($row['text']);
		
		$posts[] = $row;
		
		Search::Push("/news?post=".$id, $row['header'], StripHTML($row['text']));
	}
	
	SetNewsViews($ids);
	
	return $posts;
}

/** Обновляет счетчик просмотров в новости */
function SetNewsViews($ids = array())
{
	if(sizeof($ids) > 0)
	{
		$where_arr = array();
		
		for($i = 0; $i < sizeof($ids); $i++)
		{
			$where_arr[] = StringFormat(" `id`='{0}' ", $ids[$i]);
		}
		
		$query = SQL(StringFormat("UPDATE `news` SET `views`=(views + 1) WHERE {0}", implode("OR", $where_arr)));
		//
		return true;
	}
	
	return false;
}

/** Создает новостной пост */
function CreateNews($header, $text, $preview_image, $category, $author)
{
	if($header == "")return false;
	if($text == "")return false;
	
	$header = SQLFilterText($header); //str_replace("'", "\'", $header);
	$text = str_replace("'", "\'", $text);
	$preview_image = SQLFilterText($preview_image);
	$category = SQLFilterText($category);
	$author = SQLFilterText($author);
	//
	$newid = GetNewId("news", "id");
	//
	$time = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	
	$query = SQL("INSERT INTO `news`(`id`, `ip`, `status`, `created`, `edited`, `deleted`, `restored`, `header`, `text`, `preview_image`, `category`, `author`, `views`, `pin`) 
	VALUES ('$newid','$ip','0','$time', '0', '0', '0', '$header', '$text', '$preview_image', '$category', '$author', '0', '0')");
	
	return true;
}

/** Вносит изменение в новостной пост*/
function EditNews($id, $header, $text, $preview_image, $category, $author)
{
	if($header == "")return false;
	if($text == "")return false;
	
	$header = str_replace("'", "\'", $header);
	$text = str_replace("'", "\'", $text);
	//
	$time = time();
	$query = SQL("UPDATE `news` SET header = '$header', text = '$text', edited = '$time', preview_image = '$preview_image', category = '$category', author = '$author' WHERE id = '$id'");
	
	return true;
}

/** Удаляет новость*/
function DeleteNews($id)
{
	$time = time();
	$query = SQL("UPDATE `news` SET status = '1', deleted = '$time' WHERE id = '$id'");
	
	return true;
}

/** Восстанавливает новость*/
function RestoreNews($id)
{
	$time = time();
	$query = SQL("UPDATE `news` SET status = '0', restored = '$time' WHERE id = '$id'");
	
	return true;
}

/** Закрепляет и открепляет новость (pinned - 0, 1)*/
function SetNewsPin($id, $pinned)
{
	$query = SQL("UPDATE `news` SET pin = '$pinned' WHERE id = '$id'");
	
	return true;
}

/** Получает последнюю закрепленную новость*/
function GetPinnedNews($markup)
{
	$query = SQL("SELECT * FROM `news` WHERE `pin`='1' AND `status`='0' ORDER BY `news`.`id` DESC LIMIT 0, 1");
	$row = SQLFetchAssoc($query);
	
	if($row['id'] != "")
	{
		$id = $row['id'];
		$header = $row['header'];
		$link = "/news?post=".$id."&ref=pin";
		
		$markup = str_replace("{header}", $header, $markup);
		$markup = str_replace("{link}", $link, $markup);
		return $markup;
	}
	
	return "";
}

/** Возвращает время чтения человеком в минутах */
function GetNewsReadDuration($content)
{
	$dur = round(sizeof(explode(" ", $content))/80);
	if($dur < 1)$dur = 1;
	return $dur;
}

/** Возвращает вёрстку новости по массиву параметров*/
function GetNewsMarkup($row, $compact)
{
	$id = $row['id'];
	$header = ConvertText($row['header'], "html");
	$text = ConvertText($row['text'], "html");
	$date = GetTime($row['created']);
	$short_date = GetShortTime($row['created']);
	
	$preview_image = $row['preview_image'];
	$views = $row['views'];
	$author = $row['author'];
	$category = $row['category'];
	
	$text_length = sizeof(explode(" ", $text));
	//
	$content_markup = "";
	$expand_button_display = "";
	
	if($text_length > 80)
	{
		$content_markup = "
		<div id='content_".$id."' class='text_collapse'>
			<div class='text'>".$text."</div>
			<div id='fadeout_".$id."' class='fadeout title_text' onclick='Expand".$id."();'>Читать далее</div>
		</div>
		
		<script>
		function Expand".$id."()
		{
			Hide('fadeout_".$id."');
			Find('content_".$id."').style.height = Find('content_".$id."').scrollHeight + 'px'; //'inherit';
		}
		</script>
		";
	}
	else
	{
		$content_markup = "
		<div id='content_".$id."'>
			<div class='text'>".$text."</div>
		</div>
		";
		
		$expand_button_display = "display: none;";
	}
	
	$m = "";
	
	$header_markup = "";
	$footer_markup = "";
	if($preview_image != "")
	{
		$author_markup = "";
		if($author != "")$author_markup = "<a class='link fore3'>".$author."</a>, ";
		
		$category_markup = "";
		if($category != "")
		{
			$cat_style = WordByWord($category, array("n_back0", "n_back1", "n_back2", "n_back4", "n_back5"));
			$category_markup = "<div class='label hard_shadow ".$cat_style."' style='position: absolute; right: 8px; top: 8px;'>".$category."</div>";
		}
		
		$header_markup = "
		<div class='banner anti_padding_wide n_back4' style='background-image: url(".$preview_image."); height: 240px;'>
			".$category_markup ."
			<div class='news_banner_content_wrap'>
				<div class='news_banner_content'>
					<div class='title_text hard_text_shadow' style='margin: 8px;'><a class='link fore3' href='/news?post=".$id."' async>".$header."</a></div>
					<div>
						<div style='display: inline-block; margin-right: 20px;'>
							<div class='small_text fore3 hard_text_shadow'>".$author_markup."<span class='tooltip' data-tooltip='".$date."'>".$short_date."</span></div>
						</div>
						<div style='display: inline-block;'>
							<div class='small_text fore3 hard_text_shadow'>
								<div class='news_banner_thumb news_banner_time tooltip' data-tooltip='Время на чтение'></div> ".GetNewsReadDuration($content_markup)." мин 
								<div class='news_banner_thumb news_banner_views tooltip' data-tooltip='Просмотры: ".$views."'></div> ".GetNumericSuffix($views)."
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='space'></div>
		";
	}
	else
	{
		if($compact)
		{
			$header_markup = "
			<a class='link bold text' href='/news?post=".$id."' async>".$header."</a>
			<div class='space'></div>
			";
		}
		else
		{
			$header_markup = "
			<div style='text-align: center;'>
				<div class='text bold fore4' style='padding-top: 15px; padding-bottom: 15px;'>".$header."</div>
			</div>
			";
		}
		
		$footer_markup = "
		<div class='space'></div>
		<div>
			<div class='small_text'>".$date." | Просмотры: ".$views."</div>
		</div>
		";
	}
	
	if($compact)
	{
		$m = "
		<div id='post_".$id."'>
			".$header_markup."
			".$content_markup."
			".$footer_markup."
			<div class='space'></div>
			<div class='divider'></div>
			<div class='space'></div>
		</div>
		";
	}
	else
	{
		$m = "
		<div>
			".$header_markup."
			<div class='text'>".$text."</div>
			".$footer_markup."
			
			<!--<div style='float: right;'><a href='/news' onclick='return NavAsync(this.href, true);'><div class='small_button back3 fore0 border0'>Назад</div></a></div>-->
		</div>
		";
	}
	
	return $m;
}

?>
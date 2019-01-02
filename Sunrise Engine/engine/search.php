<?php 
// Gluschenko (review 19-05-2017)
// Класс для индексирования и поиска

OnLoad(function(){
	AddMethod("search.pages", function($params){
		$match = $params['match'];
		
		$results = Search::Pages($match);
		$markup = "";
		
		foreach($results as $result)
		{
			$markup .= Search::GetSearchItemMarkup($result, $match);
		}
		
		return array("results" => $results, "markup" => $markup);
	});
});

class Search{
	
	public static function Pages($match)
	{
		$match = SQLFilterText($match);
		$words = explode(" ", $match);
		$words[] = $match;
		
		$where = array();
		for($i = 0; $i < sizeof($words); $i++)
		{
			if($words[$i] != "")
			{
				$where[] = "`url` LIKE '% ".$words[$i]."%'";
				$where[] = "`title` LIKE '% ".$words[$i]."%'";
				$where[] = "`content` LIKE '% ".$words[$i]."%'";
			}
		}
		
		$results = array();
		
		if($match != "")
		{
			$query = SQL("SELECT * FROM `search_index` WHERE ".implode(" OR ", $where)." ORDER BY time DESC LIMIT 100");
			while($row = SQLFetchAssoc($query))
			{
				$results[] = $row;
			}
		}
		
		return $results;
	}
	
	public static function Push($url, $title, $content)
	{
		$url = SQLFilterText($url);
		$title = SQLFilterText($title);
		$content = SQLFilterText($content);
		
		if(Search::NeedUpdate($url))
		{
			$query = SQL("SELECT * FROM `search_index` WHERE `url`='$url' LIMIT 1");
			$row = SQLFetchAssoc($query);
			
			$time = time();
			if($row['id'] == "") // Добавляем
			{
				$newid = GetNewId("search_index", "id");
				
				$query = SQL("INSERT DELAYED INTO `search_index`(`id`, `url`, `title`, `content`, `time`, `updates`) 
					VALUES ('$newid','$url','$title','$content','$time','0')", false);
			}
			else // Обновляем
			{
				$query = SQL("UPDATE `search_index` SET `title`='$title', `content`='$content', `time`='$time', `updates`=(updates + 1) WHERE `url`='$url' LIMIT 1");
			}
		}
	}
	
	static function NeedUpdate($url)
	{
		$query = SQL("SELECT * FROM `search_index` WHERE `url`='$url' LIMIT 1");
		$row = SQLFetchAssoc($query);
		
		if($row['id'] != "")
		{
			$time_border = time() - (24 * 3600);
			if($row['time'] < $time_border)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		return true;
	}
	
	public static function GetSearchItemMarkup($result, $match)
	{
		$m = "
		<a href='".$result['url']."' class='padding back3' style='display: block;' onclick='return NavAsync(this.href, true);'>
			<div class='text'>".CropText($result['title'], 60)."</div>
			<div class='small_text fore6'>".$result['url']."</div>
		</a>
		<div class='divider'></div>
		";
		
		return $m;
	}
	
}

?>
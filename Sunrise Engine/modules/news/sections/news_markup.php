<?php

if($section['post_id'] == 0)
{
	$posts = $section['posts'];
	$posts_offset = sizeof($posts);
	
	for($i = 0; $i < sizeof($posts); $i++)
	{
		DrawPost($posts[$i], true);
	}
	
	if(sizeof($posts) == 0)Draw("<div class='title_text padding'>Новостей пока нет</div>");
	
	if(sizeof($posts) != 0)
	{
		Draw("
		<div id='news_async_posts'></div>
		<div class='padding'>
			<div class='button at_center fore0 border0 back3' onclick='LoadMorePosts();'>Больше новостей</div>
		</div>
		
		<script>
		var posts_offset = ".$posts_offset.";
		var last_posts_number = 1;
		
		function LoadMorePosts()
		{
			var posts_number = 3;
			
			if(last_posts_number > 0)
			{
				ApiMethod('engine.news.get', { number: posts_number, offset: posts_offset, need_markup: 1 }, function(data){
				//console.log(data);
				
				if(data.response != null)
				{
					if(Exists('news_async_posts'))
					{
						WriteEnd('news_async_posts', data.response.markup);
						ExecuteJS(data.response.markup);
					}
					
					last_posts_number = data.response.posts.length;
				}
			});
			}
			
			posts_offset += posts_number;
		}
		
		setInterval(function()
		{
			if(document.location.search == '')
			{
				var deltaScroll = body.scrollHeight - window.scrollY - window.innerHeight;
				
				if(deltaScroll < 400)
				{
					LoadMorePosts();
				}
			}
		}, 1000);
		</script>
		");
	}
}
else
{
	if(isset($section['post']))
	{
		DrawPost($section['post'], false);
	}
	else
	{
		Draw("<div class='title_text padding'>Материала не существует</div>");
	}
}

function DrawPost($row, $compact)
{
	$m = GetNewsMarkup($row, $compact);
	
	Draw($m);
}

?>
<?

if(true)
{
	if(true)
	{
		$post = 0;
		if(isset($request['post']))$post = $request['post'];
		
		$header = "";
		$text = "";
		$preview_image= "";
		$author= "";
		$category= "";
			
		
		if($post != 0)
		{
			$query = SQL("SELECT * FROM `news` WHERE `id`='$post'");
			$row = SQLFetchAssoc($query);
			
			$post = $row['id'];
			$header = $row['header'];
			$text = $row['text'];
			
			$preview_image = $row['preview_image'];
			$author = $row['author'];
			$category = $row['category'];
			
		}
		
		?>
		
		<script>
		
		function SaveOrCreateNews()
		{
			ShowLoader();
			//
			var post = <? echo $post; ?>;
			
			var response_callback = function(data){
				if(data.response != null)
				{
					/*Hide("news_info");
					SlideShow("news_info");
					ScrollToId("body");*/
					
					if(data.response == 1)
					{
						if(post == 0)setTimeout(function(){ document.location = "/admin?act=news"; }, 100);
						
						//if(post == 0)setTimeout(function(){ NavigateAsync("/admin?act=news", false); }, 1000); //Перезагружаем страницу, если новость только что создана
						
						ShowPanel("Новость успешно сохранена", 0);
					}
					else
					{
						/*Hide("saved");
						Show("not_saved");*/
						
						ShowPanel("Новость не сохранена", 1);
					}
				}
				
				//concole.log(data);
				HideLoader();
			};
			
			var params = {
				id: post,
				header: Find("header").value,
				text: Find("text_editor").field.value, //Find("text").value,
				
				preview_image: Find("preview_image").value,
				category: Find("category").value,
				author: Find("author").value,
			};
			
			if(post == 0)
			{
				ApiMethod("admin.news.create", params, function(data){
					response_callback(data);
				});
			}
			else
			{
				ApiMethod("admin.news.edit", params, function(data){
					response_callback(data);
				});
			}
		}
		
		</script>
		
		<!--<div id='news_info' style='display: none;'>
			<div id='saved' class='panel panel_good'>
				<div class='title_text fore3'>Новость успешно сохранена</div>
			</div>
			<div id='not_saved' class='panel panel_bad'>
				<div class='title_text fore3'>Новость не сохранена</div>
			</div>
			<div class='space'></div>
		</div>-->
		
		<div id='page_info' style='display: none;'>
			<div class='panel panel_good'>
				<div class='title_text fore3'>Страница успешно сохранена</div>
			</div>
			<div class='space'></div>
		</div>
		
		<div class='title_text'>Заголовок</div>
		<div class='space'></div>
		<input id='header' value='<? echo($header); ?>' class='text_input' style='width: 99%;'/>
		<div class='space'></div>
		
		<div class='title_text'>Содержимое</div>
		<div class='space'></div>
		<div id='text_editor'></div>
		<!--<textarea id='text' value='' class='text_input html_input'></textarea>-->
		<div class='space'></div>
		
		<div style='margin: auto; width: 600px;'>
			<div class='title_text'>Обложка *</div>
			<div class='space'></div>
			<input id='preview_image' value='<? echo($preview_image); ?>' class='text_input' style='width: 99%;' pleceholder='/files/image.jpg'/>
			<div class='space'></div>
			
			<div class='title_text'>Категория *</div>
			<div class='space'></div>
			<input id='category' value='<? echo($category); ?>' class='text_input' style='width: 99%;'/>
			<div class='space'></div>
			
			<div class='title_text'>Автор *</div>
			<div class='space'></div>
			<input id='author' value='<? echo($author); ?>' class='text_input' style='width: 99%;'/>
			<div class='space'></div>
		</div>
		
		<script>
		var Text = <? echo(ToJSON($text)); ?>;
		CodeEditor.Init("text_editor", 
		function(editor){
			editor.field.value = Text;
		},
		function(){
			SaveOrCreateNews();
		});
		//Find("text").value = Text;
		</script>
		
		<div class='big_space'></div>
		
		<!--<div class='button back4' style='margin: auto;' onclick='SaveOrCreateNews();'>Сохранить</div>-->
		
		<div style='height: 40px; width: 410px; margin: auto;'>
			<div class='button back4' style='float: left;' onclick='SaveOrCreateNews();'>Сохранить</div>
			<div class='button back2' style='float: right;' onclick='window.open("/news?post=<? echo($post);?>");'>Просмотр</div>
		</div>
		
		<div class='big_space'></div>
		
		<?
	}
}

?>
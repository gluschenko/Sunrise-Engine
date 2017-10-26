<?php
$engine_cfg = GetEngineConfig();
$version = $engine_cfg['version'];
?>

<style>
body
{
	min-width: 1024px;
	background-color: #fff;
	background-image: none;
}

.dev_header
{
	background-color: #e84c3d;
	position: relative;
	background-image: url(/engine/assets/dev_background.png);
	background-position: center center;
	background-size: cover, cover;
	background-repeat: no-repeat;
}

.dev_nav_bar
{
	top: 0px;
	width: 100%;
	position: fixed;
	background-image: url(/engine/assets/dev_background.png);
	background-position: center center;
	background-size: cover, cover;
}

.dev_logo{
	background-image: url(/engine/assets/sunrise_logo.png);
	background-size: 80%;
	width: 400px;
	height: 400px;
	background-repeat: no-repeat;
	background-position: center center;
	margin: auto;
}

.feature_block
{
	display: inline-block;
	width: 150px;
	padding-top: 30px;
	padding-bottom: 10px;
	padding-left: 20px;
	padding-right: 20px;
	border-bottom: solid transparent 5px;
}

.feature_block_active{
	border-bottom: solid #888 5px;
	transition: .5s;
}

.ui_coin{
    width: 40px;
    height: 40px;
    background-size: 50%;
    background-position: center, center;
    border-radius: 100%;
    background-repeat: no-repeat;
    box-shadow: 0px 1px 2px rgba(6, 33, 63, .3);
}

.ui_font{
    line-height: 40px;
    font-weight: bold;
}

.feature_shortcut{
	min-width: 200px;
	color: #fff;
	padding-left: 50px;
	padding-right: 10px;
	line-height: 50px;
	text-align: center;
	background-size: 30px;
	background-repeat: no-repeat;
	background-position: 10px, center;
	display: inline-block;
	box-sizing: border-box;
	border-bottom: solid 2px rgba(255, 255, 255, 0);
	cursor: pointer;
}

.feature_shortcut:hover{
	border-bottom: solid 2px #fff;
}

.feature_shortcut_hover{
	border-bottom: solid 2px #fff;
	background-color: rgba(255, 255, 255, .1);
}

.dev_button{
	display: inline-block;
	width: auto;
	line-height: 52px;
	background-color: transparent;
	border-bottom: solid 2px rgba(255, 255, 255, 0);
	cursor: pointer;
}

.dev_button:hover{
	background-color: rgba(255, 255, 255, .1);
	border-bottom: solid 2px #fff;
}

</style>

<script>

addEventListener("scroll", function(e){ 
	var scroll = window.scrollY;
	
	if(Exists("dev_header") && Exists("dev_nav_bar") && Exists("dev_nav_bar_subwrap"))
	{
		var h = Find("dev_header").clientHeight - Find("dev_nav_bar").clientHeight;
		
		if(scroll < h)
		{
			Find("dev_nav_bar_subwrap").style.height = "0px";
			Find("dev_nav_bar").className = "";
		}
		else
		{
			Find("dev_nav_bar_subwrap").style.height = Find("dev_nav_bar").clientHeight + "px";
			Find("dev_nav_bar").className = "dev_nav_bar";
		}
	}
});

function ScrollToFeature(obj, id)
{
	var f_shortcuts = ["feature_1", "feature_2", "feature_3", "feature_4"];
	
	for(var i in f_shortcuts)
	{
		Find(f_shortcuts[i]).className = "feature_shortcut";
	}
	
	obj.className = "feature_shortcut feature_shortcut_hover";
	
	ScrollToId(id, 50);
}

</script>

<div>
	<div id='dev_header' class='dev_header'>
		<div class='dev_logo' onclick='ShowFeature(0);'></div>
		<div class='text fore3 text_shadow'style='position: absolute; bottom: 140px; left: 150px; margin-left:50%;'>v <? echo($version);?></div>
		<div class='text fore3 text_shadow inner_center'style='position: absolute; bottom: 85px; margin-left: 50%; left: -150px; width: 300px;'>Простота. Быстрота. Надёжность.</div>
		
		<div id='dev_nav_bar_subwrap'></div>
		<div id='dev_nav_bar' style='text-align: center;'>
			<div style='background-color: rgba(0, 0, 0, .3); border-bottom: solid 1px #fff;'>
				<div id='feature_1' class='feature_shortcut' style='background-image: url(/engine/assets/features/feature_1.png);' onclick='ScrollToFeature(this, "text_feature_1");'>Асинхронность</div>
				<div id='feature_2' class='feature_shortcut' style='background-image: url(/engine/assets/features/feature_2.png);' onclick='ScrollToFeature(this, "text_feature_2");'>Данные</div>
				<div id='feature_3' class='feature_shortcut' style='background-image: url(/engine/assets/features/feature_3.png);' onclick='ScrollToFeature(this, "text_feature_3");'>Система шаблонов</div>
				<div id='feature_4' class='feature_shortcut' style='background-image: url(/engine/assets/features/feature_4.png);' onclick='ScrollToFeature(this, "text_feature_4");'>Модульный код</div>
				
				<div class='dev_button text fore3' style='position: absolute; right: 20px; bottom: 0px;' onclick='ShowWindow("История версий", Find("change_log").innerHTML, 2, 0, 900);'>Что нового?</div>
			</div>
		</div>
	</div>
	<div id='change_log' style='display: none;'>
		<div style='overflow-y: auto; height: 600px; padding: 10px;'>
			<?
			$began = true;
			for($i = 0; $i < sizeof($engine_cfg['change_log']); $i++)
			{
				$entry = $engine_cfg['change_log'][$i];
				
				if($entry != "")
				{
					if($began)
					{
						echo("<div class='text bold'>Версия ".$entry."</div> <ul>\n");
					}
					else 
					{
						echo("<li class='text'>".$entry."</li>\n");
					}
				}
				else
				{
					echo("</ul> <div class='space'></div>\n");
				}
				
				$began = false;
				if($entry == "")$began = true;
			}
			?>
		</div>
	</div>
	
	<div id='text_feature_0' style='border-bottom: solid #888 1px;'>
		<div style='margin: auto; width: 800px; padding: 20px; padding-bottom: 50px;'>
			<div id='feature_title' class='title_text inner_center' style='padding-bottom: 20px;'>О движке</div>
			<div id='feature_text' class='text'>
			Sunrise Engine — универсальная система для создания контента и полнофункционального управления им. Проект является продуктом ряда неочевидных путей решения большинства проблем, связанных с управлением информацией в сети Интернет.<br/><br/>
			Простота в обращении, широкая адаптивность, мобильность, эффективное взаимодействие с поисковыми системами — задачи, ради которых избегались лёгкие пути в пользу более технологически продвинутых решений.<br/><br/>
			Движок наглядно демонстрирует превосходство рациональности и элегантности над классическими подходами к структурированию и управлению.
			</div>
		</div>
	</div>
	
	<div id='text_feature_1' style='border-bottom: solid #888 1px;'>
		<div style='margin: auto; width: 800px; padding: 20px; padding-bottom: 50px;'>
			<div id='feature_title' class='title_text inner_center' style='padding-bottom: 20px;'>Асинхронность</div>
			<div id='feature_text' class='text'>
			Порционная загрузга страниц вместо традиционной - синхронной. <br/><br/>
			Движок берет на себя комбинирование вёрстки и встраивание в неё информации, освобождая сайт от лишнего трафика и увеличивая скорость доставки контента до пользователя.  <br/><br/>
			Также сохраняется возможность загрузки страниц традиционным способом, что позволяет охарактеризовать систему как гибридную.
			</div>
		</div>
	</div>
	
	<div id='text_feature_2' style='border-bottom: solid #888 1px;'>
		<div style='margin: auto; width: 800px; padding: 20px; padding-bottom: 50px;'>
			<div id='feature_title' class='title_text inner_center' style='padding-bottom: 20px;'>Данные</div>
			<div id='feature_text' class='text'>
			Данные представлены в виде структурно-независимой сущности, что позволяет придать им любую форму при выводе из хранилища. <br/><br/>
			К примеру, страницы разделов лежат в хранилище, а доступ к ним осуществляется через слой подсистем движка, 
			которые эмулируют некоторые серверные процессы над страницами. 
			Получается, что страница существует, генерируясь практически на лету, но фактического адреса не имеет. «Ложки не существует».
			</div>
		</div>
	</div>
	
	<div id='text_feature_3' style='border-bottom: solid #888 1px;'>
		<div style='margin: auto; width: 800px; padding: 20px; padding-bottom: 50px;'>
			<div id='feature_title' class='title_text inner_center' style='padding-bottom: 20px;'>Система шаблонов</div>
			<div id='feature_text' class='text'>
			Наиболее рациональным путем построения интерфейсов является прототипирование элементов. 
			Для этого движок оснащён системой шаблонов, которая поддерживает использование ключевых слов и вставок.
			</div>
		</div>
	</div>
	
	<div id='text_feature_4' style='border-bottom: solid #888 1px;'>
		<div style='margin: auto; width: 800px; padding: 20px; padding-bottom: 50px;'>
			<div id='feature_title' class='title_text inner_center' style='padding-bottom: 20px;'>Модульный код</div>
			<div id='feature_text' class='text'>
			Присутствует возможность наращивания функционала без значительных изменений базового программного кода.<br/><br/>
			Ядро движка предоставляет весь необходимый функционал для создания дополнительных модулей и надстроек. 
			В теории подход модульной структуры освобождает процесс разработки от возможных конфликтов внутри существующей системы.
			</div>
		</div>
	</div>
	
	<div style='border-bottom: solid #888 1px; padding-bottom: 40px;'>
		<div id='feature_title' class='title_text inner_center' style='padding-top: 20px; padding-bottom: 20px;'>Что под капотом?</div>
		<div>
			<canvas id='EngineGraph' width='1024' height='600' style='display: block;'></canvas>
		</div>
			
		<script>
		var EngineStructire = <?php echo(ToJSON(GetEngineStructure())); ?>;

		AJAX.LoadScript("/engine/js/engine_graph.js", function(){
			EngineGraph.Init("EngineGraph", EngineStructire);
			
			window.addEventListener("resize", function(e){
				GraphResize("EngineGraph");
			});
			GraphResize("EngineGraph");
		});

		function GraphResize(obj)
		{
			if(Find(obj) != null)
			{
				Find(obj).width = document.body.clientWidth;
			}
		}

		</script>
		
	</div>
	
	<div style='border-bottom: solid #888 1px; padding: 20px;'>
		<div class='title_text inner_center' style='padding-bottom: 20px;'>Разработчик</div>
		<div style='width: 300px; margin: auto;'>
			<a href='mailto://alexander@gluschenko.net' target='_blank'>
				<div style='height: 50px;'>
					<div class='n_back4 ui_coin' style='float: left; background-image: url(/engine/assets/Email.png);'></div>
					<div class='text fore4 ui_font' style='float: right; width: 250px;'>alexander@gluschenko.net</div>
				</div>
			</a>
			<a href='https://github.com/gluschenko' target='_blank'>
				<div style='height: 50px;'>
					<div class='n_back4 ui_coin' style='float: left; background-image: url(/engine/assets/img/social/github.png);'></div>
					<div class='text fore4 ui_font' style='float: right; width: 250px;'>Gluschenko</div>
				</div>
			</a>
			<a href='http://dev.gluschenko.net' target='_blank'>
				<div style='height: 50px;'>
					<div class='n_back4 ui_coin' style='float: left; background-image: url(/engine/assets/Web.png);'></div>
					<div class='text fore4 ui_font' style='float: right; width: 250px;'>Just Dev</div>
				</div>
			</a>
		</div>
	</div>
	<div style='height: 50px;'></div>
	<div class='text inner_center'><s>Далеко не</s> все права защищены © <?php echo(GetYear(time())); ?></div>
	<div style='height: 50px;'></div>
</div>
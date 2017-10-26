<?php

$interpreter = array(
"<header>Заголовок</header>",

"<text>Lorem ipsum</text>
<huge_text>Lorem ipsum</huge_text>
<big_text>Lorem ipsum</big_text>
<small_text>Lorem ipsum</small_text>
<tiny_text>Lorem ipsum</tiny_text>",

"Ссылка с заголовком:<br/>
<link>https://vk.com|Ссылка внешняя</link><br/>
<inner_link>/admin|Ссылка внутренняя (асинхронная)</inner_link><br/>
Ссылка без заголовка:<br/>
<link>https://vk.com</link><br/>",

"Text
<center>Text</center>
Text",

"Пустое пространство
<space/>
Большое пустое пространство
<big_space/>
123",

"Обычная картинка:<br/>
<center>
	<image>/modules/guidelines/assets/img.jpg</image>
	<image>/modules/guidelines/assets/img.jpg</image>
</center>",
"Маленькая картинка:<br/>
<center>
	<small_image>/modules/guidelines/assets/img.jpg</small_image>
	<small_image>/modules/guidelines/assets/img.jpg</small_image>
</center>",
"Большая картинка:<br/>
<big_image>/modules/guidelines/assets/img.jpg</big_image>",
"Большая картинка без ограничения высоты:<br/>
<large_image>/modules/guidelines/assets/img.jpg</large_image>",
"Картинка на максимальную доступную ширину:<br/>
<wide_image>/modules/guidelines/assets/img.jpg</wide_image>",

"Приставка '_c' загрузит сжатую версию картинки, чтобы не мучить трафик:<br/>
<center>
	<image_c>/modules/guidelines/assets/img.jpg</image_c>
	<image_c>/modules/guidelines/assets/img.jpg</image_c>
</center>",

"Баннерная картинка (800 x 250):<br/>
<banner>/modules/guidelines/assets/img.jpg</banner>
Маленький баннер (800 x 100):<br/>
<small_banner>/modules/guidelines/assets/img.jpg</small_banner>
Средний (800 x 200):<br/>
<middle_banner>/modules/guidelines/assets/img.jpg</middle_banner>
Большой (800 x 360):<br/>
<big_banner>/modules/guidelines/assets/img.jpg</big_banner>",

"Вставка видео с YouTube: https://www.youtube.com/watch?v=<b>bLHW78X1XeE</b><br/>
<br/>
<youtube>bLHW78X1XeE</youtube>",

"Встраивание аудио:<br/>
<br/>
<embed_audio>/modules/guidelines/assets/sound.mp3</embed_audio>",

"Встраивание iframe:<br/>
<br/>
<embed_iframe>/404</embed_iframe>",

);

$interpreter_markup = "";

for($i = 0; $i < sizeof($interpreter); $i++)
{
	$interpreter_markup .= "
		<tr>
			<td class='table_column'>".($i + 1).".</td>
			<td class='table_column' style='font-family: monospace;'>
				".FilterText($interpreter[$i], "open_html")."
			</td>
			<td class='table_column'>
				".$interpreter[$i]."
			</td>
		</tr>
	";
}

//

$patterns = array(
"<div class='button'>Кнопка</div>
<space/>
<div class='small_button'>Маленькая кнопка</div>
<space/>
<div class='small_button at_center'>Кнопка по центру</div>
<space/>
<div class='small_button at_center back0'>Серая кнопка</div>
<space/>
<div class='small_button at_center back1'>Оранжевая кнопка</div>
<space/>
<div class='small_button at_center back2'>Зелёная кнопка</div>
<space/>
<div class='small_button at_center back3 fore0 border0'>Чёрно-белая</div>
<space/>
<div class='small_button at_center back5'>У меня тут синий</div>
<space/>
<div class='small_button at_center back6 fore0'>Бледная</div>",

"<div class='text'>Обычный текст</div>
<div class='title_text'>Заголовочный текст</div>
<div class='logo_text'>Большой текст</div>
<div class='small_text'>Маленький текст</div>
<div class='mini_text'>Мелкий текст</div>
<space/>
<div class='text fore1'>Оранженый текст</div>
<div class='text fore2'>Зелёный текст</div>
<div class='text fore3 n_back0'>Белый текст</div>
<div class='text fore4'>Красный текст</div>
<div class='text fore5'>Синий текст</div>
<div class='text fore6'>Бледный текст</div>
<div class='text'>
	<span class='text fore0'>Ц</span>
	<span class='text fore1'>В</span>
	<span class='text fore2'>Е</span>
	<span class='text fore3 n_back1'>Т</span>
	<span class='text fore4'>Н</span>
	<span class='text fore5'>О</span>
	<span class='text fore6'>Й</span>
</div>",

"<a class='link' href='/'>Ссылка</a><br/>
<a class='link fore1' href='/'>Ссылка</a><br/>
<a class='link fore2' href='/'>Ссылка</a><br/>
<a class='link fore5' href='/'>Ссылка</a><br/>",

"<div class='button' onclick='DocumentBox(\"/files/pdf\");'>Документ в окне</div>
<space/>
<a class='link' onclick='DocumentBox(\"/files/pdf\");'>Документ в окне по ссылке</a>",

"<div class='button' onclick='ImageBox(\"/modules/guidelines/assets/img.jpg\");'>Картинка в окне</div>
<space/>
<a class='link' onclick='ImageBox(\"/modules/guidelines/assets/img.jpg\");'>Картинка в окне по ссылке</a>
<space/>
Нативную картинку по надобности можно открыть так: <br/>
<img style='width: 150px;' src=\"/modules/guidelines/assets/img.jpg\" onclick=\"ImageBox(this.src);\">",

"<div id='win_1' style='display: none;'>
	<div class='text fore1'>Контент для</div>
	<div class='text fore2'>первого окна</div>
</div>
<div id='win_2' style='display: none;'>
	<div class='text fore4'>Контент для</div>
	<div class='text fore5'>второго окна</div>
</div>
<div class='small_button fore0 back6' onclick='ShowWindow(\"Wow!\", document.getElementById(\"win_1\").innerHTML, 0, 0, 400);'>Открыть окно #1</div>
<space/>
<div class='small_button back2' onclick='ShowWindow(\"Зеленая шапка\", document.getElementById(\"win_2\").innerHTML, 1, 0, 600);'>Открыть окно #2</div>
<space/>
<div class='small_button back4' onclick='ShowWindow(\"Красная шапка\", \"Третье окно\", 2, 0, 800);'>Окно с контентом #3</div>",

"<b>Типичный список:</b><br/>
<ul>
	<li>Node.js</li>
	<li>PHP</li>
	<li>JavaScript</li>
	<li>PostgreSQL</li>
</ul>",

"<b>Типичный список с нумерацией:</b><br/>
<ol>
	<li>DirectX</li>
	<li>OpenGL</li>
	<li>Vulkan</li>
	<li>Metal</li>
</ol>",

"<table class='data_table'>
	<tr class='table_header'>
		<td style='width: 40px;'>#</td>
		<td>Категория A</td>
		<td>Категория B</td>
	</tr>
	<tr>
		<td>1.</td>
		<td>FGHF</td>
		<td>VNVB</td>
	</tr>
	<tr>
		<td>2.</td>
		<td>PGHJ</td>
		<td>EFGF</td>
	</tr>
</table>",

"<table style='width: 100%;'>
	<tr>
		<td>Таблица</td>
		<td>по умолчанию - </td>
		<td>пустая,</td>
	</tr>
	<tr>
		<td>без</td>
		<td>отступов</td>
		<td>и границ</td>
	</tr>
</table>",

);

$patterns_markup = "";

for($i = 0; $i < sizeof($patterns); $i++)
{
	$patterns_markup .= "
		<tr>
			<td class='table_column'>".($i + 1).".</td>
			<td class='table_column' style='font-family: monospace;'>
				".FilterText($patterns[$i], "open_html")."
			</td>
			<td class='table_column'>
				".$patterns[$i]."
			</td>
		</tr>
	";
}

$text = "
	
	<header>Оформление</header>
	
	<b>Заголовки:</b>
	<ul>
		<li>Заголовки страниц должны быть короткими и ёмкими (макс. 3 слова)</li>
		<li>Заголовки новостей должны нести в себе только название мероприятия и никаких дат и прочих данных (макс. 10 слов)</li>
		<li>Заголовки должны начинаться с большой буквы и не иметь точки в конце</li>
	</ul>
	
	<b>Текст:</b>
	<ul>
		<li>Текст не должен быть большого размера (только 14px, только хардкор)</li>
		<li>Абзац разделяется двумя тегами переноса &lt;br/&gt;&lt;br/&gt;</li>
		<li>Абзац не должен начинаться с табуляции (шлем ГОСТы подальше)</li>
		<li>Ссылки, ведущие на страницы сайта (внутренние), не должны иметь в своём начале домен сайта (http://domain.com/admin => /admin)</li>
	</ul>
	
	<b>Картинки:</b>
	<ul>
		<li>Перед загрузкой картинки предварительно сжимаются до 1440 точек в ширину</li>
		<li>Сжание средствами сайта работает крайне паршиво</li>
	</ul>
	
	<b>Видео:</b>
	<ul>
		<li>Видео желательно загружать на стронние сайты типа ВКонтакте и YouTube (диска жалко)</li>
		<li>ВКонтакте и YouTube предоставляют встраивание через iframe (см. \"копировать код для вставки\")</li>
	</ul>
	
	<header>Безопасность</header>
	
	<ul>
		<li>По завершении работы с сайтом лучше всего выйти из админки</li>
		<li>Слив пароля сайта ставит под угрозу ВСЁ</li>
		<li>Смена пароля сайта аннулирует все активные сессии админки</li>
		<li>Сброс токена в настройках сайта аннулирует сессии без смены пароля</li>
		<li>В случае потери контроля над сайтом, необходимо пройти процедуру экстренного сброса пароля: <link>/engine/utils/reset.php|здесь</link></li>
	</ul>
	
	<header>Резервное копирование</header>
	
	<ul>
		<li>Последним смеется тот, у кого есть резервная копия</li>
		<li>Бекап нужно делать примерно раз в месяц (в случае потери сервера будет из чего восстановить)</li>
		<li>Переходим в раздел <link>/admin?act=system|Система</link></li>
		<li>Нажимаем кнопку \"Сделать резервную копию\"</li>
		<li>Ждем 2 минуты</li>
		<li>Переходим в раздел <link>/admin?act=upload|Файлы</link></li>
		<li>Скачиваем два последних файла (.gz и .zip)</li>
		<li>Первый файл хранит в себе дамп базы данных. Второй файл хранит файлы домена.</li>
		<li>После скачивания файлы с сервера удаляюм, так как занимаю место и скачать их может любой, если знает ссылку.</li>
	</ul>
	
	<header>Страницы</header>
	
	<table style='width: 100%;'>
		<tr>
			<td style='width: 70%;'>
				<link>/admin?act=pages|Страницы</link> являются основным носителем контента на сайте. 
				<space/>
				<b>Создание страницы:</b>
				<ul>
					<li>Придумать идентификатор страницы</li>
					<li>Идентификатор должен быть вида simple_page_id</li>
					<li>Убедиться, что страниц с таким идентификатором нет <link>/simple_page_id</link></li>
					<li class='fore4 bold'>В случае совпадения адресов не исключена возможность перезаписи существующей страницы!!!</li>
				</ul>
				Страницы можно вставлять друг в друга, прописав в желаемом месте вставки: <b>%имя_страницы%</b>.
			</td>
			<td>
				<wide_image>/modules/guidelines/assets/img/pages.png</wide_image>
			</td>
		</tr>
	</table>
	
	<header>Шаблоны</header>
	
	<table style='width: 100%;'>
		<tr>
			<td style='width: 70%;'>
				<link>/admin?act=templates|Шаблоны</link> нужны для прототипирования информации. 
				Написав конструкцию вида <b>\$шаблон\$</b> в тексте страницы, новости или другого шаблона, можно увидеть, 
				что при загрузке сохраненной страницы, место данной конструкции займет содержимое шаблона с соответствующим именем. 
				Таким образом, сможно размещать одинаковые куски текста в разных местах сайта. Это полезно, если эту информацию нужно будет изменить. 
				Например, можно сделать шаблоном список контактов, и вставить его в нескольких страницах, а в случае изменения 
				контактов, нужно будет изменить только шаблон.
				<space/>
				<b>Создание шаблона:</b>
				<ul>
					<li>Придумать название шаблона вида simple_template</li>
					<li>Поискать в <link>/admin?act=templates|списке шаблонов</link>, нет ли там уже существующего \$simple_template\$</li>
					<li class='fore4 bold'>В случае совпадения названий не исключена возможность перезаписи существующих шаблонов!!!</li>
				</ul>
				<b>Специальные шаблоны:</b>
				<ul>
					<li>\$global\$ - шаблон, который будет встроен в каждую страницу (полезно для стилей, скриптов)</li>
					<li>\$страница:before\$ - всталяется автоматически в начало страницы (полезно для разделов типа расписания)</li>
					<li>\$страница:after\$ - вставляется автоматически в конец страницы</li>
				</ul>
			</td>
			<td>
				<wide_image>/modules/guidelines/assets/img/templates.png</wide_image>
			</td>
		</tr>
	</table>
	
	<header>Навигация</header>
	
	<table style='width: 100%;'>
		<tr>
			<td style='width: 70%;'>
				<link>/admin?act=nav|Навигация</link> необходима для желаемого распределния распределения трафика по сайту. 
				Информативные и полезные разделы размещаются на видное место, чтобы пользователь с наибольшей вероятностью попал <s>в АД</s> туда, куда хотел. 
				<br/><br/>
				Основным объектом навигации является <b>бар</b>. Имя бара определено шаблоном сайта: если создан бар с нужным именем, то он будет вставлен в шаблон сайта при прсомотре. 
				Бар содержит <b>блоки</b>, которые выводятся последовательно в месте втавки бара. Обертка блоков задается шаблоном сайта, поэтому из админки не редактируется.
				<br/><br/>
				Блок может содержать <b>список ссылок</b>, либо <b>гипертекст</b>. 
				<space/>
				<li class='fore4 bold'>В случае совпадения названий баров не исключена возможность перезаписи!!!</li>
			</td>
			<td>
				<wide_image>/modules/guidelines/assets/img/navigation.png</wide_image>
				<center>
					<small_image>/modules/guidelines/assets/img/navigation_block.png</small_image>
					<small_image>/modules/guidelines/assets/img/navigation_link.png</small_image>
				</center>
			</td>
		</tr>
	</table>
	
	<header>Новости</header>
	
	<table style='width: 100%;'>
		<tr>
			<td style='width: 70%;'>
				<link>/admin?act=news|Новости</link> являются отражением текущих, прошедших и будущих событий. 
				Дата публикации новости важна для восприятия пользователя и для пространственно-временного континуума: 
				новость не может быть иметь дату публикации раньше, чем дата самого события. Посему, новая новость публикуется 
				только когда есть информация о событии, а не информация о новом событии вносится в существующую новость.
				<br/><br/>
				В новостях поддерживается интерпретатор и работает вставка <b>\$шаблонов\$</b> и <b>%страниц%</b>.
				<br/><br/>
				Новость можно закрепить в самом верху страниц сайта, нажав на соответствующую ссылку напротив новости (картика 2).
			</td>
			<td>
				<wide_image>/modules/guidelines/assets/img/news.png</wide_image>
				<wide_image>/modules/guidelines/assets/img/news_pin.png</wide_image>
			</td>
		</tr>
	</table>
	
	<header>Интерпретатор</header>
	
	Ниже прдеставлены примеры использования конструкций, работающих в рамках интерпретатора, встроенного в движок повсеместно: новости, страницы, шаблоны, сайдбары. 
	Несмотря на то что по синтаксису они схожи с HTML, таковым они не являются и актуальны только в рамках сайта.<br/><br/>
	Данные условные костркуции Интерпретатор трансформирует в нативные HTML элементы.
	<space/>
	<table style='width: 100%;'>
		<tr class='table_header fore3'>
			<td class='table_column' style='width: 40px;'>#</td>
			<td class='table_column'>Код</td>
			<td class='table_column'>Результат</td>
		</tr>
		".$interpreter_markup."
	</table>
	
	<header>Паттерны</header>
	
	Далее показаны приёмы создания коплексных элементов на нативном HTML, но с применением готовых классов из файла common.css (вшит к каждую страницу и можно применять абсолютно по всему сайту).<br/><br/>
	Стили уложены так, что сочетание нескольких классов позволяет сделать кастомизацию элемента (цвет фона - back[n]/n_back[n], цвет текста - fore[n], цвет границ - border[n]).
	<space/>
	<table style='width: 100%;'>
		<tr class='table_header fore3'>
			<td class='table_column' style='width: 40px;'>#</td>
			<td class='table_column'>Код</td>
			<td class='table_column'>Результат</td>
		</tr>
		".$patterns_markup."
	</table>
	
	<header>Код этой страницы</header>
";

echo(FilterText($text, "html"));

echo("
<div id='source_wrap' style='display: none;'>
	<div style='font-family: monospace;'>
		".FilterText($text, "open_html")."
	</div>
</div>
<div>
	<div class='small_button' onclick='SlideToggle(\"source_wrap\");'>Показать/скрыть</div>
</div>
");

?>
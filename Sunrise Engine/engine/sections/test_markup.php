<div id='test'>

<style>
.inline
{
	display: inline-block;
}
</style>

<div class='title_text'>AJAX Layout 2.0</div>
<div class='space'></div>
<div class='divider'></div>
<div class='space'></div>

<a href='youtube.com' async>Async attribute (youtube.com)</a><br/>
<a href='https://youtube.com' async>Async attribute (https://youtube.com)</a><br/>
<a href='/admin' async>Async attribute (/admin)</a><br/>
<br/>
<a href='youtube.com' onclick='AJAXLayout.Navigate(event, this.href);'>Classic AL (youtube.com)</a><br/>
<a href='https://youtube.com' onclick='AJAXLayout.Navigate(event, this.href);'>Classic AL (https://youtube.com)</a><br/>
<a href='/admin' onclick='AJAXLayout.Navigate(event, this.href);'>Classic AL (/admin)</a><br/>
<a href='http://rz.likescope.ru/admin' onclick='AJAXLayout.Navigate(event, this.href);'>Classic AL (/admin)</a><br/>
<br/>
<a href='/dd' onclick='event.preventDefault();'>ffff</a><br/>
<a href='/dd'>ffff</a><br/>

<div class='title_text'>Buttons</div>
<div class='space'></div>
<div class='divider'></div>
<div class='space'></div>

<table>
	<tr>
		<td><div class='big_button'>Big button</div></td>
		<td><div class='button'>Default button</div></td>
		<td><div class='small_button'>Small button</div></td>
		<td><div class='little_button'>Little button</div></td>
	</tr>
	<tr>
		<td><div class='big_button back0'>Big button</div></td>
		<td><div class='button back0'>Default button</div></td>
		<td><div class='small_button back0'>Small button</div></td>
		<td><div class='little_button back0'>Little button</div></td>
	</tr>
	<tr>
		<td><div class='big_button back1'>Big button</div></td>
		<td><div class='button back1'>Default button</div></td>
		<td><div class='small_button back1'>Small button</div></td>
		<td><div class='little_button back1'>Little button</div></td>
	</tr>
	<tr>
		<td><div class='big_button back2'>Big button</div></td>
		<td><div class='button back2'>Default button</div></td>
		<td><div class='small_button back2'>Small button</div></td>
		<td><div class='little_button back2'>Little button</div></td>
	</tr>
	<tr>
		<td><div class='big_button back3 fore0 border0'>Big button</div></td>
		<td><div class='button back3 fore0 border0'>Default button</div></td>
		<td><div class='small_button back3 fore0 border0'>Small button</div></td>
		<td><div class='little_button back3 fore0 border0'>Little button</div></td>
	</tr>
	<tr>
		<td><div class='big_button back4'>Big button</div></td>
		<td><div class='button back4'>Default button</div></td>
		<td><div class='small_button back4'>Small button</div></td>
		<td><div class='little_button back4'>Little button</div></td>
	</tr>
	<tr>
		<td><div class='big_button back5'>Big button</div></td>
		<td><div class='button back5'>Default button</div></td>
		<td><div class='small_button back5'>Small button</div></td>
		<td><div class='little_button back5'>Little button</div></td>
	</tr>
</table>

<div class='listitem text'>Example of list items</div>
<div class='listitem text'>Example of list items</div>
<div class='listitem text'>Example of list items</div>
<div class='listitem title_text'>Example of list <br/>items</div>

<br/><br/>

<a href='http://getbootstrap.com/examples/theme/' target='_blank'>Bootstrap</a>


<div class='text'>Text</div>
<div class='title_text'>Title_text</div>
<div class='logo_text'>Logo_text</div>
<div class='text fore0'>Fore0</div>
<div class='text fore1'>Fore1</div>
<div class='text fore2'>Fore2</div>
<div class='text fore3'>Fore3</div>
<div class='text fore4'>Fore4</div>
<div class='text fore5'>Fore5</div>
<br/>
<div class='button'>Button</div>
<br/>
<div class='small_button'>Small button</div>
<br/>
<div class='big_button'>Big button</div>
<br/>
<div class='button' onclick='ShowWindow("Win", "CCCC", 1, 1, 1000);'>Window</div>
<br/>
<div class='button' onclick='ShowBox("Окно", Find("test").innerHTML, 600);'>Box</div>
<br/>
<div class='button' onclick='ShowModal("Модальное окно", "Применяется для информирования хомяка", 0);'>Modal 0</div>
<br/>
<div class='button' onclick='ShowModal("Модальное окно", "Показывается после успешных операций", 1);'>Modal 1</div>
<br/>
<div class='button' onclick='ShowModal("Модальное окно", "Показывается после не очень успешных операций", 2);'>Modal 2</div>
<br/>

</div>
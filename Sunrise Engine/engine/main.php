<?php
//Основная точка входа в Sunrise Engine
//Developed by Alexander Gluschenko (2015-2017)

//ob_start();
//
require($_SERVER['DOCUMENT_ROOT']."/engine/engine.php");
//
//Из секций следующие переменные не звать!
//
$URL = $_SERVER['REQUEST_URI'];
$settings = GetSiteSettings();
//
$section = GetSection($URL, $settings);
$markup = GetMarkup($section, $settings);
$layout_markup = CreateLayout($section, $settings);

$layout_markup = str_replace("{markup}", $markup, $layout_markup);
//
Draw($layout_markup); //Вывод страницы
//
//ob_end_flush();
?>
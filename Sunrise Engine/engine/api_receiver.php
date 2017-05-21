<?php
//Alexander Gluschenko (15-07-2015)
//Сюда приходит запрос из JS

include($_SERVER['DOCUMENT_ROOT'].'/engine/engine.php');

$request = $_POST;
if(sizeof($_POST) == 0)$request = $_REQUEST;

$method = (isset($request['method']))? $request['method'] : "";
$params = FromJSON((isset($request['params']))? $request['params'] : "");

$resp = ApiMethod($method, $params);

echo(ToJSON($resp));
?>
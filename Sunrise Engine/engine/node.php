<?php
//Больше не используется, из-за плохой производительности движка

//Alexander Gluschenko (15-07-2015)
//Узел, образуемый кодом скриптов JavaScript, прописываемых в модулях и прочем трэше.
// AddJS(path)

header('Content-Type: text/javascript');
include($_SERVER['DOCUMENT_ROOT'].'/engine/engine.php');

$cfg = GetConfig();
$engine_cfg = GetEngineConfig();

Draw("var ServerData = NodeVars = ".ToJSON($engine_config['node_vars']).";");

for($i = 0; $i < sizeof($engine_cfg['javascripts']); $i++)
{
	$client_path = $engine_cfg['javascripts'][$i];
	
	Draw("\n\n/*Original: ".$client_path." */\n\n"); //рут не палим в целях безопасности
	
	$path = $cfg['root'].$engine_cfg['javascripts'][$i];
	RequireIfExists($path); //Выкатываем
}

?>
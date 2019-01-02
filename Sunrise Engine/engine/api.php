<?php
//Alexander Gluschenko (15-07-2015)

/** Исполняет метод API */
function ApiMethod($method, $params)
{
	$output;
	///
	$load_start = microtime(true);
	///
	
	$execution = Execute($method, $params);
	if(!is_string($execution))
	{
		$output['response'] = $execution;
	}
	else
	{
		$output['error'] = array(
			'text' => $execution,
			'method' => $method,
			'params' => $params,
		);
	}
	
	///
	$load_end = microtime(true);
	$load_time = round(($load_end - $load_start) * 1000);//Миллисекунды
	///
	
	$output['server'] = array(
		'time' => time(),
		'exec_time' => $load_time,
	);
	
	return $output;
}

/** Непосредственный исполнитель API */
function Execute($method, $params)
{
	global $engine_config;
	$methods = $engine_config['methods'];
	
	if(isset($methods[$method]))
	{
		return $methods[$method]($params);
	}
	
	return "Unknown method";
}

/** Добавляет метод API в конфиг */
function AddMethod($method, $func)
{
	global $engine_config;
	
	$engine_config['methods'][$method] = $func;
}



?>
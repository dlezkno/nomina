<?php
	$controllerFile = 'controllers/' . $controller . '.php';

	if (file_exists($controllerFile)) 
	{
		require_once($controllerFile);
		$controller = new $controller();
		if (method_exists($controller, $method))
		{
			if (isset($param2))
				$controller->{$method}($param1, $param2);
			elseif (isset($param1))
				$controller->{$method}($param1);
			else
				$controller->{$method}();
		}
		else
			require_once('controllers/error404.php');
	}
	else
		require_once('controllers/error404.php');
?>
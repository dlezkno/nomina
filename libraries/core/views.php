<?php
	class Views
	{
		function getView($controller, $view, $data = '', $estado = '')
		{
			$controller = get_class($controller);

			if ($controller == 'home')
				$view = 'views/' . $view . '.php';
			else
				$view = 'views/' . $controller . '/' . $view . '.php';

			require_once($view);
		}
	}

<?php
	spl_autoload_register(function($class)
	{
		if (file_exists('libraries/core/' . $class . '.php')) 
			require_once('libraries/core/' . $class . '.php');
	});
?>

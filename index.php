<?php
	require_once('config/config.php');
	require_once('helpers/helpers.php');
	require_once('libraries/core/autoload.php');

	define('EMPLEADO', 1);
	define('SELECCION', 2);
	define('CONTRATACION', 3);
	define('CONTABILIDAD', 4);
	define('AUDITORIA', 5);
	define('RRHH_AUX', 97);
	define('RRHH', 98);
	define('ADMINISTRADOR', 99);

	session_name('COMWARE');
	session_start();

	// echo "esto esta melo";

	if	( ! isset($_SESSION['Login']['Usuario']) AND ! isset($_SESSION['Login']['Perfil']) )
		$_GET['url'] = '';

	$url = ! empty($_GET['url']) ? $_GET['url'] : 'usuarios/login';

	$aUrl = explode('/', $url);
	$controller = $aUrl[0];
	$method = $aUrl[0];
	// $params = '';

	if (! empty($aUrl[1]))
		$method = $aUrl[1];

	if (! empty($aUrl[2]))
	{
		for ($i = 2; $i < count($aUrl); $i++) 
		{ 
			$variable = 'param' . ($i - 1);

			$$variable = $aUrl[$i];
		}
	}

	// VALIDAR SI LA URL ESTA INCLUIDA EN LA LISTA BLANCA
	if (isset($_SESSION['ListaBlanca'])) 
	{
		$AccesoDenegado = TRUE;

		for ($i = 0; $i < count($_SESSION['ListaBlanca']) ; $i++) 
		{ 
			if (strtolower($_SESSION['ListaBlanca'][$i]) == strtolower($controller)) 
			{
				$AccesoDenegado = FALSE;
				break;
			}
		}

		if ($AccesoDenegado)
		{
			$controller = 'error401';
			$method = 'error401';
			$params = '';
		}
	}

	require_once('libraries/core/load.php');
?>

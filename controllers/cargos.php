<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Cargos extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/cargos/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/cargos/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/cargos/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['CARGOS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['CARGOS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['CARGOS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['CARGOS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['CARGOS']['Filtro']))
			{
				$_SESSION['CARGOS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['CARGOS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['CARGOS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['CARGOS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['CARGOS']['Orden'])) 
					$_SESSION['CARGOS']['Orden'] = 'CARGOS.Nombre';

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= 'WHERE ';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(CARGOS.Cargo, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['CARGOS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarCargos($query);
			$this->views->getView($this, 'cargos', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/cargos/actualizarCategoria';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/cargos/lista/' . $_SESSION['CARGOS']['Pagina'];

			$data = array(
				'reg' => array(
					'Nombre' 				=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
					'SueldoMinimo' 			=> isset($_REQUEST['SueldoMinimo']) ? $_REQUEST['SueldoMinimo'] : 0,
					'SueldoMaximo' 			=> isset($_REQUEST['SueldoMaximo']) ? $_REQUEST['SueldoMaximo'] : 0,
					'IdCargoSuperior' 		=> isset($_REQUEST['IdCargoSuperior']) ? $_REQUEST['IdCargoSuperior'] : 0, 
					'IdCargoBase' 			=> isset($_REQUEST['IdCargoBase']) ? $_REQUEST['IdCargoBase'] : 0, 
					'PorcentajeARL' 		=> isset($_REQUEST['PorcentajeARL']) ? $_REQUEST['PorcentajeARL'] : 0
				),
				'regPerfil' => array(
					'IdDependencia'			=> isset($_REQUEST['IdDependencia']) ? $_REQUEST['IdDependencia'] : 0, 
					'NivelAcademico'		=> isset($_REQUEST['NivelAcademico']) ? $_REQUEST['NivelAcademico'] : 0, 
					'Estudios'				=> isset($_REQUEST['Estudios']) ? $_REQUEST['Estudios'] : '', 
					'ExperienciaLaboral'	=> isset($_REQUEST['ExperienciaLaboral']) ? $_REQUEST['ExperienciaLaboral'] : '', 
					'FormacionAdicional'	=> isset($_REQUEST['FormacionAdicional']) ? $_REQUEST['FormacionAdicional'] : '', 
					'CondicionesTrabajo'	=> isset($_REQUEST['CondicionesTrabajo']) ? $_REQUEST['CondicionesTrabajo'] : '', 
					'MisionCargo'			=> isset($_REQUEST['MisionCargo']) ? $_REQUEST['MisionCargo'] : '', 
					'Funciones'				=> isset($_REQUEST['Funciones']) ? $_REQUEST['Funciones'] : '', 
					'Responsable'			=> isset($_REQUEST['Responsable']) ? $_REQUEST['Responsable'] : '', 
					'Elabora'				=> isset($_REQUEST['Elabora']) ? $_REQUEST['Elabora'] : '', 
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Nombre'])) 
			{
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
				else
				{
					$query = 'SELECT * FROM CARGOS ' .
							"WHERE CARGOS.Nombre = '" . $data['reg']['Nombre'] . "'";

					$reg = $this->model->buscarCargo($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Cargo') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( $data['reg']['SueldoMinimo'] < 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo mínimo') . '</strong><br>';
			
				if	( $data['reg']['SueldoMaximo'] < 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo máximo') . '</strong><br>';

				if	( $data['reg']['SueldoMinimo'] > $data['reg']['SueldoMaximo'])
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo mínimo') . '</strong> ' . label('inferior o igual al') . ' <strong>' . label('Sueldo máximo') . '</strong><br>';
			
				if	( $data['reg']['PorcentajeARL'] < 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Porcentaje de riesgo') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$Id = $this->model->guardarCargo($data['reg']);

					if ($Id) 
					{
						if ($data['regPerfil']['NivelAcademico'] > 0)
							$resp = $this->model->actualizarPerfil($data['regPerfil'], $Id);
						else
							$resp = true;
						
						if ($resp) 
						{
							header('Location: ' . $_SESSION['Lista']);
							exit();
						}
					}
				}
			}
			else
				$this->views->getView($this, 'adicionar', $data);
		}

		public function editar($Id)
		{
			if (isset($_REQUEST['Nombre']))
			{
				$data = array(
					'reg' => array(
						'Nombre' 				=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
						'SueldoMinimo' 			=> isset($_REQUEST['SueldoMinimo']) ? $_REQUEST['SueldoMinimo'] : 0,
						'SueldoMaximo' 			=> isset($_REQUEST['SueldoMaximo']) ? $_REQUEST['SueldoMaximo'] : 0,
						'IdCargoSuperior' 		=> isset($_REQUEST['IdCargoSuperior']) ? $_REQUEST['IdCargoSuperior'] : 0, 
						'IdCargoBase' 			=> isset($_REQUEST['IdCargoBase']) ? $_REQUEST['IdCargoBase'] : 0, 
						'PorcentajeARL' 		=> isset($_REQUEST['PorcentajeARL']) ? $_REQUEST['PorcentajeARL'] : 0
					),
					'regPerfil' => array(
						'IdDependencia'			=> isset($_REQUEST['IdDependencia']) ? $_REQUEST['IdDependencia'] : 0, 
						'NivelAcademico'		=> isset($_REQUEST['NivelAcademico']) ? $_REQUEST['NivelAcademico'] : 0, 
						'Estudios'				=> isset($_REQUEST['Estudios']) ? $_REQUEST['Estudios'] : '', 
						'ExperienciaLaboral'	=> isset($_REQUEST['ExperienciaLaboral']) ? $_REQUEST['ExperienciaLaboral'] : '', 
						'FormacionAdicional'	=> isset($_REQUEST['FormacionAdicional']) ? $_REQUEST['FormacionAdicional'] : '', 
						'CondicionesTrabajo'	=> isset($_REQUEST['CondicionesTrabajo']) ? $_REQUEST['CondicionesTrabajo'] : '', 
						'MisionCargo'			=> isset($_REQUEST['MisionCargo']) ? $_REQUEST['MisionCargo'] : '', 
						'Funciones'				=> isset($_REQUEST['Funciones']) ? $_REQUEST['Funciones'] : '', 
						'Responsable'			=> isset($_REQUEST['Responsable']) ? $_REQUEST['Responsable'] : '', 
						'Elabora'				=> isset($_REQUEST['Elabora']) ? $_REQUEST['Elabora'] : '', 
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
				else
				{
					$query = "SELECT * FROM CARGOS WHERE CARGOS.Nombre = '" . $data['reg']['Nombre'] . "' AND CARGOS.Id <> " . $Id;

					$reg = $this->model->buscarCargo($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Cargo') . '</strong> ' . label('ya existe') . '<br>';
				}
			
				if	( $data['reg']['SueldoMinimo'] < 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo mínimo') . '</strong><br>';
			
				if	( $data['reg']['SueldoMaximo'] < 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo máximo') . '</strong><br>';

				if	( $data['reg']['SueldoMinimo'] > $data['reg']['SueldoMaximo'])
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo mínimo') . '</strong> ' . label('inferior o igual al') . ' <strong>' . label('Sueldo máximo') . '</strong><br>';

				if	( $data['reg']['PorcentajeARL'] <= 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Porcentaje de riesgo') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarCargo($data['reg'], $Id);

					if ($resp)
					{
						if ($data['regPerfil']['NivelAcademico'] > 0)
							$resp = $this->model->actualizarPerfil($data['regPerfil'], $Id);
						else
							$resp = true;
						
						if ($resp) 
						{
							header('Location: ' . $_SESSION['Lista']);
							exit();
						}
					}
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/cargos/actualizar';
				$_SESSION['Retroceder'] = '';
				$_SESSION['Avanzar'] = '';
				$_SESSION['Novedades'] = '';
				$_SESSION['Importar'] = '';
				$_SESSION['ImportarArchivo'] = '';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL . '/cargos/lista/' . $_SESSION['CARGOS']['Pagina'];

				$regCargo = getRegistro('CARGOS', $Id);

				if ($regCargo['idcargobase'] > 0)
					$regPerfil = getRegistro('PERFILES', 0, 'PERFILES.IdCargoBase = ' . $regCargo['idcargobase']);
				else
					$regPerfil = getRegistro('PERFILES', 0, 'PERFILES.IdCargoBase = ' . $Id);

				$data = array(
					'reg' => array(
						'Id' 					=> $regCargo['id'],
						'Nombre' 				=> $regCargo['nombre'],
						'SueldoMinimo' 			=> $regCargo['sueldominimo'],
						'SueldoMaximo' 			=> $regCargo['sueldomaximo'],
						'IdCargoSuperior' 		=> $regCargo['idcargosuperior'], 
						'IdCargoBase' 			=> $regCargo['idcargobase'], 
						'PorcentajeARL' 		=> $regCargo['porcentajearl']
					),
					'regPerfil' => array(
						'IdDependencia'			=> $regPerfil ? $regPerfil['iddependencia'] : 0, 
						'NivelAcademico'		=> $regPerfil ? $regPerfil['nivelacademico'] : 0, 
						'Estudios'				=> $regPerfil ? $regPerfil['estudios'] : '', 
						'ExperienciaLaboral'	=> $regPerfil ? $regPerfil['experiencialaboral'] : '', 
						'FormacionAdicional'	=> $regPerfil ? $regPerfil['formacionadicional'] : '', 
						'CondicionesTrabajo'	=> $regPerfil ? $regPerfil['condicionestrabajo'] : '', 
						'MisionCargo'			=> $regPerfil ? $regPerfil['misioncargo'] : '', 
						'Funciones'				=> $regPerfil ? $regPerfil['funciones'] : '', 
						'Responsable'			=> $regPerfil ? $regPerfil['responsable'] : '', 
						'Elabora'				=> $regPerfil ? $regPerfil['elabora'] : '', 
					),
				'mensajeError' => ''
				);
				
				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($Id)
		{
			if (isset($_REQUEST['Nombre']))
			{
				$data = array(
					'reg' => array(
						'Nombre' 				=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
						'SueldoMinimo' 			=> isset($_REQUEST['SueldoMinimo']) ? $_REQUEST['SueldoMinimo'] : 0,
						'SueldoMaximo' 			=> isset($_REQUEST['SueldoMaximo']) ? $_REQUEST['SueldoMaximo'] : 0,
						'IdCargoSuperior' 		=> isset($_REQUEST['IdCargoSuperior']) ? $_REQUEST['IdCargoSuperior'] : 0, 
						'IdCargoBase' 			=> isset($_REQUEST['IdCargoBase']) ? $_REQUEST['IdCargoBase'] : 0, 
						'PorcentajeARL' 		=> isset($_REQUEST['PorcentajeARL']) ? $_REQUEST['PorcentajeARL'] : 0
					),
					'regPerfil' => array(
						'IdDependencia'			=> isset($_REQUEST['IdDependencia']) ? $_REQUEST['IdDependencia'] : 0, 
						'NivelAcademico'		=> isset($_REQUEST['NivelAcademico']) ? $_REQUEST['NivelAcademico'] : 0, 
						'Estudios'				=> isset($_REQUEST['Estudios']) ? $_REQUEST['Estudios'] : '', 
						'ExperienciaLaboral'	=> isset($_REQUEST['ExperienciaLaboral']) ? $_REQUEST['ExperienciaLaboral'] : '', 
						'FormacionAdicional'	=> isset($_REQUEST['FormacionAdicional']) ? $_REQUEST['FormacionAdicional'] : '', 
						'CondicionesTrabajo'	=> isset($_REQUEST['CondicionesTrabajo']) ? $_REQUEST['CondicionesTrabajo'] : '', 
						'MisionCargo'			=> isset($_REQUEST['MisionCargo']) ? $_REQUEST['MisionCargo'] : '', 
						'Funciones'				=> isset($_REQUEST['Funciones']) ? $_REQUEST['Funciones'] : '', 
						'Responsable'			=> isset($_REQUEST['Responsable']) ? $_REQUEST['Responsable'] : '', 
						'Elabora'				=> isset($_REQUEST['Elabora']) ? $_REQUEST['Elabora'] : '', 
					),
					'mensajeError' => ''
				);

				$query = <<<EOD
					SELECT COUNT(*) AS Registros 
						FROM EMPLEADOS 
						WHERE EMPLEADOS.IdCargo = $Id;
				EOD;

				$reg = $this->model->leer($query);
	
				if ($reg['Registros'] > 0) 
					$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Cargo') . '</strong><br>';
					
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarCargo($Id);

					if ($resp) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = SERVERURL . '/cargos/borrar';
				$_SESSION['ActualizarRegistro'] = '';
				$_SESSION['Retroceder'] = '';
				$_SESSION['Avanzar'] = '';
				$_SESSION['Novedades'] = '';
				$_SESSION['Importar'] = '';
				$_SESSION['ImportarArchivo'] = '';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL . '/cargos/lista/' . $_SESSION['CARGOS']['Pagina'];

				$regCargo = getRegistro('CARGOS', $Id);

				if ($regCargo['idcargobase'] > 0)
					$regPerfil = getRegistro('PERFILES', 0, 'PERFILES.IdCargoBase = ' . $regCargo['idcargobase']);
				else
					$regPerfil = getRegistro('PERFILES', 0, 'PERFILES.IdCargoBase = ' . $Id);

				$data = array(
					'reg' => array(
						'Id' 					=> $regCargo['id'],
						'Nombre' 				=> $regCargo['nombre'],
						'SueldoMinimo' 			=> $regCargo['sueldominimo'],
						'SueldoMaximo' 			=> $regCargo['sueldomaximo'],
						'IdCargoSuperior' 		=> $regCargo['idcargosuperior'], 
						'IdCargoBase' 			=> $regCargo['idcargobase'], 
						'PorcentajeARL' 		=> $regCargo['porcentajearl']
					),
					'regPerfil' => array(
						'IdDependencia'			=> $regPerfil ? $regPerfil['iddependencia'] : 0, 
						'NivelAcademico'		=> $regPerfil ? $regPerfil['nivelacademico'] : 0, 
						'Estudios'				=> $regPerfil ? $regPerfil['estudios'] : '', 
						'ExperienciaLaboral'	=> $regPerfil ? $regPerfil['experiencialaboral'] : '', 
						'FormacionAdicional'	=> $regPerfil ? $regPerfil['formacionadicional'] : '', 
						'CondicionesTrabajo'	=> $regPerfil ? $regPerfil['condicionestrabajo'] : '', 
						'MisionCargo'			=> $regPerfil ? $regPerfil['misioncargo'] : '', 
						'Funciones'				=> $regPerfil ? $regPerfil['funciones'] : '', 
						'Responsable'			=> $regPerfil ? $regPerfil['responsable'] : '', 
						'Elabora'				=> $regPerfil ? $regPerfil['elabora'] : '', 
					),
				'mensajeError' => ''
				);
				
				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar2($id)
		{
			$query = 'SELECT * FROM CARGOS WHERE CARGOS.Id = ' . $id;
				
			$reg = $this->model->leer($query);
			$data = array(
				'reg' => array(
					'Id' => $reg['id'],
					'Cargo' => $reg['cargo'],
					'Nombre' => $reg['nombre'],
					'SueldoMinimo' => $reg['sueldominimo'],
					'SueldoMaximo' => $reg['sueldomaximo'],
					'IdCargoSuperior' => $reg['idcargosuperior'], 
					'PorcentajeARL' => $reg['porcentajearl']
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Id']))
			{
				// $query = 'SELECT COUNT(*) AS Registros ' .
				// 		'FROM NITS ' .
				// 		'WHERE NITS.IdCategoria = ' . $id;

				// $reg = $this->model->buscarCategoria($query);

				// if ($reg['registros'] > 0) 
				// {
				// 	$data['mensajeError'] .= label('Existe información en otras tablas relacionada con esta') . ' <strong>' . label('Categoría') . '</strong><br>';
				// }

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarCargo($id);

					if ($resp) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = SERVERURL . '/cargos/borrar';
				$_SESSION['ActualizarRegistro'] = '';
				$_SESSION['Retroceder'] = '';
				$_SESSION['Avanzar'] = '';
				$_SESSION['Novedades'] = '';
				$_SESSION['Importar'] = '';
				$_SESSION['ImportarArchivo'] = '';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL . '/cargos/lista/' . $_SESSION['CARGOS']['Pagina'];

				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function informe()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = 00;
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/cargos/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['CARGOS']['Filtro'];

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= 'WHERE ';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(CARGOS.Cargo, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['CARGOS']['Orden']; 
			$data['rows'] = $this->model->listarCargos($query);
			$this->views->getView($this, 'informe', $data);
		}

		public function importar()
		{
			$data = array();

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
				{
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				}
				else
				{
					ini_set('max_execution_time', 600);
					
					$archivo = $_FILES['archivo']['name'];
		
					if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
					{
						if ( file_exists ($archivo) )
						{
							// $oExcel = IOFactory::load($archivo);
							$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $Excel->getSheet(0);
		
							$row = 0;
							$Excel = array();
		
							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
								{
									$Excel[$row][0] = $oHoja->getCell('A' . $i)->getCalculatedValue();
									$Excel[$row][1] = $oHoja->getCell('B' . $i)->getCalculatedValue();
									$Excel[$row][2] = $oHoja->getCell('C' . $i)->getCalculatedValue();
									$Excel[$row][3] = $oHoja->getCell('D' . $i)->getCalculatedValue();
									$Excel[$row][4] = $oHoja->getCell('E' . $i)->getCalculatedValue();
									$Excel[$row][5] = $oHoja->getCell('F' . $i)->getCalculatedValue();
									$row++;
								}
							}

							for ( $i = 0; $i < count($Excel); $i++ )
							{
								// BUSCAMOS EL CARGO SUPERIOR
								if (! empty($Excel[$i][4])) 
								{
									$query = 'SELECT * ' .
											'FROM CARGOS ' .
											"WHERE CARGOS.Cargo = '" . $Excel[$i][4] . "'";

									$reg = $this->model->buscarCargo($query);

									if ($reg)
										$Excel[$i][4] = $reg['id'];
									else
										$Excel[$i][4] = 0;
								}
								else
									$Excel[$i][4] = 0;

								// BUSCAMOS EL CARGO PARA ADICIONAR O ACTUALIZAR
								$query = 'SELECT * ' .
										'FROM CARGOS ' .
										"WHERE CARGOS.Cargo = '" . $Excel[$i][0] . "'";

								$reg = $this->model->buscarCargo($query);

								if ($reg) 
									$this->model->actualizarCargo($Excel[$i], $reg['id']);
								else
									$this->model->guardarCargo($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/cargos/lista/' . $_SESSION['CARGOS']['Pagina']);
							exit;
						}
					}
				}
			}

			if (isset($_FILES) AND count($_FILES) == 0 OR ! empty($data['mensajeError']))
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = '';
				$_SESSION['Retroceder'] = '';
				$_SESSION['Avanzar'] = '';
				$_SESSION['Novedades'] = '';
				$_SESSION['Importar'] =  '';
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/cargos/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/cargos/lista/1';

				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>
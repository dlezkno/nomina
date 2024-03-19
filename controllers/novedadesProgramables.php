<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class NovedadesProgramables extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/novedadesProgramables/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR OR $_SESSION['Login']['Perfil'] == RRHH OR $_SESSION['Login']['Perfil'] == RRHH_AUX)
				$_SESSION['Importar'] = SERVERURL . '/novedadesProgramables/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = SERVERURL . '/novedadesProgramables/exportar';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/novedadesProgramables/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['NOVEDADESPROGRAMABLES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['NOVEDADESPROGRAMABLES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['NOVEDADESPROGRAMABLES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['NOVEDADESPROGRAMABLES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['NOVEDADESPROGRAMABLES']['Filtro']))
			{
				$_SESSION['NOVEDADESPROGRAMABLES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['NOVEDADESPROGRAMABLES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['NOVEDADESPROGRAMABLES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['NOVEDADESPROGRAMABLES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['NOVEDADESPROGRAMABLES']['Orden'])) 
					$_SESSION['NOVEDADESPROGRAMABLES']['Orden'] = 'AUXILIARES.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

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

					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
			{
				$query .= 'ORDER BY ' . $_SESSION['NOVEDADESPROGRAMABLES']['Orden']; 
				$data['rows'] = $this->model->listarNovedadesProgramables($query);

				$Archivo = 'descargas/' . $_SESSION['Login']['Usuario'] . '_NovedadesProgramables_' . date('YmdGis') . '.csv';

				$output = fopen($Archivo, 'w');

				fputcsv($output, array('FECHA', 'TIPO EMP.', 'DOCUMENTO', 'NOMBRE EMPLEADO', 'CENTRO', 'CARGO', 'CONCEPTO', 'DESCRIPCION', 'HORAS', 'VALOR', 'SALARIO LIM.', 'FECHA LIM.', 'TERCERO', 'NOMBRE TERCERO', 'APLICA', 'MODO LIQ.', 'ESTADO'), ';');

				for ($i = 0; $i < count($data['rows']); $i++) 
				{ 
					$reg = $data['rows'][$i];

					foreach ($reg as $key => $value) 
					{
						if ($key == 'fecha' OR 
							$key == 'horas' OR
							$key == 'valor' OR 
							$key == 'salariolimite' OR 
							$key == 'fechalimite')
							continue;

						$reg[$key] = utf8_decode($value);
					}

					$regDatos = array($reg['fecha'], $reg['NombreTipoEmpleado'], $reg['Documento'], $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'], $reg['NombreCentro'], $reg['NombreCargo'], $reg['Mayor'] . $reg['Auxiliar'], $reg['NombreConcepto'], number_format($reg['horas'], 2, '.', ''), number_format($reg['valor'], 2, '.', ''), number_format($reg['salariolimite'], 2, '.', ''), $reg['fechalimite'], $reg['DocumentoTercero'], $reg['NombreTercero'], $reg['NombreAplica'], $reg['NombreModoLiquidacion'], $reg['NombreEstado']);

					fputcsv($output, $regDatos, ';');
				}
				
				fclose($output);

				header('Content-Description: File Transfer');
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename=' . basename($Archivo));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($Archivo));
				ob_clean();
				flush();
				readfile($Archivo);
				exit();
			}
			else
			{
				$data['registros'] = $this->model->contarRegistros($query);
				$lineas = LINES;
				$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
				$query .= 'ORDER BY ' . $_SESSION['NOVEDADESPROGRAMABLES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
				$data['rows'] = $this->model->listarNovedadesProgramables($query);
				$this->views->getView($this, 'novedadesProgramables', $data);
			}
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/novedadesProgramables/actualizarAuxiliar';
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
			$_SESSION['Lista'] = SERVERURL . '/novedadesProgramables/lista/' . $_SESSION['NOVEDADESPROGRAMABLES']['Pagina'];

			$data = array(
				'reg' => array(
					'Fecha' 			=> isset($_REQUEST['Fecha']) ? $_REQUEST['Fecha'] : NULL,
					'IdConcepto' 		=> isset($_REQUEST['IdConcepto']) ? $_REQUEST['IdConcepto'] : 0,
					'TipoEmpleado' 		=> isset($_REQUEST['TipoEmpleado']) ? $_REQUEST['TipoEmpleado'] : 0,
					'IdEmpleado' 		=> isset($_REQUEST['IdEmpleado']) ? $_REQUEST['IdEmpleado'] : 0,
					'IdCentro' 			=> isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0,
					'IdCargo' 			=> isset($_REQUEST['IdCargo']) ? $_REQUEST['IdCargo'] : 0,
					'Horas' 			=> isset($_REQUEST['Horas']) ? $_REQUEST['Horas'] : 0,
					'Valor' 			=> isset($_REQUEST['Valor']) ? $_REQUEST['Valor'] : 0,
					'SalarioLimite' 	=> isset($_REQUEST['SalarioLimite']) ? $_REQUEST['SalarioLimite'] : 0,
					'FechaLimite' 		=> isset($_REQUEST['FechaLimite']) ? $_REQUEST['FechaLimite'] : NULL,
					'IdTercero' 		=> isset($_REQUEST['IdTercero']) ? $_REQUEST['IdTercero'] : 0,
					'Aplica' 			=> isset($_REQUEST['Aplica']) ? $_REQUEST['Aplica'] : 0,
					'ModoLiquidacion' 	=> isset($_REQUEST['ModoLiquidacion']) ? $_REQUEST['ModoLiquidacion'] : 0,
					'Estado' 			=> isset($_REQUEST['Estado']) ? $_REQUEST['Estado'] : 0
				),
				'Concepto' => '',
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Fecha'])) 
			{
				if	( empty($data['reg']['Fecha']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de inicio') . '</strong><br>';

				if	( empty($data['reg']['IdConcepto']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Concepto') . '</strong><br>';

				// if	( empty($data['reg']['TipoEmpleado']) )
				// 			$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de empleado') . '</strong><br>';

				// if	( empty($data['reg']['IdEmpleado']) )	
				// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Empleado') . '</strong><br>';
	
				// if	( empty($data['reg']['IdCentro']) )	
				// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Centro de costos') . '</strong><br>';
	
				// if	( empty($data['reg']['IdCargo']) )
				// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Cargo de empleado') . '</strong><br>';

				$Fecha 			= $data['reg']['Fecha'];
				$IdConcepto 	= $data['reg']['IdConcepto'];
				$TipoEmpleado 	= $data['reg']['TipoEmpleado'];
				$IdEmpleado 	= $data['reg']['IdEmpleado'];
				$IdCentro 		= $data['reg']['IdCentro'];
				$IdCargo 		= $data['reg']['IdCargo'];

				$query = <<<EOD
					SELECT NOVEDADESPROGRAMABLES.*
						FROM NOVEDADESPROGRAMABLES 
						WHERE NOVEDADESPROGRAMABLES.Fecha = '$Fecha' AND 
							NOVEDADESPROGRAMABLES.IdConcepto = $IdConcepto AND 
							NOVEDADESPROGRAMABLES.TipoEmpleado = $TipoEmpleado AND 
							NOVEDADESPROGRAMABLES.IdEmpleado = $IdEmpleado AND 
							NOVEDADESPROGRAMABLES.IdCentro = $IdCentro AND 
							NOVEDADESPROGRAMABLES.IdCargo = $IdCargo;
				EOD;

				$reg = $this->model->buscarNovedadProgramable($query);

				if ($reg) 
					$data['mensajeError'] .= '<strong>' . label('Novedad programable') . '</strong> ' . label('ya existe') . '<br>';

				// if	( $data['reg']['Horas'] <= 0 )
				// 	$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Horas') . '</strong><br>';

				// if	( $data['reg']['Valor'] <= 0 )
				// 	$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Valor') . '</strong><br>';

				// if	( $data['reg']['SalarioLimite'] <= 0 )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Salario límite') . '</strong><br>';

				if	( empty($data['reg']['Aplica']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Modo de aplicación') . '</strong><br>';

				if	( empty($data['reg']['ModoLiquidacion']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Modo de liquidación') . '</strong><br>';
	
				if	( $data['reg']['Estado'] == 0 )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Estado de la novedad') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					if(empty($data['reg']['IdTercero'])){
						$tercero = getRegistro('AUXILIARES', $data['reg']['IdConcepto'])['idtercero'];
						$data['reg']['IdTercero'] = $tercero == null ? 0 : $tercero;
					}

					$id = $this->model->guardarNovedadProgramable($data['reg']);

					if ($id) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
				$this->views->getView($this, 'adicionar', $data);
		}

		public function editar($id)
		{
			if (isset($_REQUEST['IdConcepto']))
			{
				$data = array(
					'reg' => array(
						'Fecha' 			=> isset($_REQUEST['Fecha']) ? $_REQUEST['Fecha'] : NULL,
						'IdConcepto' 		=> isset($_REQUEST['IdConcepto']) ? $_REQUEST['IdConcepto'] : 0,
						'TipoEmpleado' 		=> isset($_REQUEST['TipoEmpleado']) ? $_REQUEST['TipoEmpleado'] : 0,
						'IdEmpleado' 		=> isset($_REQUEST['IdEmpleado']) ? $_REQUEST['IdEmpleado'] : 0,
						'IdCentro' 			=> isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0,
						'IdCargo' 			=> isset($_REQUEST['IdCargo']) ? $_REQUEST['IdCargo'] : 0,
						'Horas' 			=> isset($_REQUEST['Horas']) ? $_REQUEST['Horas'] : 0,
						'Valor' 			=> isset($_REQUEST['Valor']) ? $_REQUEST['Valor'] : 0,
						'SalarioLimite' 	=> isset($_REQUEST['SalarioLimite']) ? $_REQUEST['SalarioLimite'] : 0,
						'FechaLimite' 		=> isset($_REQUEST['FechaLimite']) ? $_REQUEST['FechaLimite'] : NULL,
						'IdTercero' 		=> isset($_REQUEST['IdTercero']) ? $_REQUEST['IdTercero'] : 0,
						'Aplica' 			=> isset($_REQUEST['Aplica']) ? $_REQUEST['Aplica'] : 0,
						'ModoLiquidacion' 	=> isset($_REQUEST['ModoLiquidacion']) ? $_REQUEST['ModoLiquidacion'] : 0,
						'Estado' 			=> isset($_REQUEST['Estado']) ? $_REQUEST['Estado'] : 0
						),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['Fecha']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de inicio') . '</strong><br>';

				if	( empty($data['reg']['IdConcepto']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Concepto') . '</strong><br>';

				// if	( empty($data['reg']['TipoEmpleado']) )
				// 			$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de empleado') . '</strong><br>';

				// if	( empty($data['reg']['IdEmpleado']) )	
				// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Empleado') . '</strong><br>';
	
				// if	( empty($data['reg']['IdCentro']) )	
				// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Centro de costos') . '</strong><br>';
	
				// if	( empty($data['reg']['IdCargo']) )
				// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Cargo de empleado') . '</strong><br>';
	
				$Fecha 			= $data['reg']['Fecha'];
				$IdConcepto 	= $data['reg']['IdConcepto'];
				$TipoEmpleado 	= $data['reg']['TipoEmpleado'];
				$IdEmpleado 	= $data['reg']['IdEmpleado'];
				$IdCentro 		= $data['reg']['IdCentro'];
				$IdCargo 		= $data['reg']['IdCargo'];

				$query = <<<EOD
					SELECT NOVEDADESPROGRAMABLES.*
						FROM NOVEDADESPROGRAMABLES 
						WHERE NOVEDADESPROGRAMABLES.Fecha = '$Fecha' AND 
							NOVEDADESPROGRAMABLES.IdConcepto = $IdConcepto AND 
							NOVEDADESPROGRAMABLES.TipoEmpleado = $TipoEmpleado AND 
							NOVEDADESPROGRAMABLES.IdEmpleado = $IdEmpleado AND 
							NOVEDADESPROGRAMABLES.IdCentro = $IdCentro AND 
							NOVEDADESPROGRAMABLES.IdCargo = $IdCargo AND 
							NOVEDADESPROGRAMABLES.Id <> $id;
				EOD;

				$reg = $this->model->buscarNovedadProgramable($query);

				if ($reg) 
					$data['mensajeError'] .= '<strong>' . label('Novedad programable') . '</strong> ' . label('ya existe') . '<br>';

				// if	( $data['reg']['Horas'] <= 0 )
				// 	$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Horas') . '</strong><br>';

				// if	( $data['reg']['Valor'] <= 0 )
				// 	$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Valor') . '</strong><br>';

				// if	( $data['reg']['SalarioLimite'] <= 0 )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Salario límite') . '</strong><br>';

				if	( empty($data['reg']['Aplica']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Modo de aplicación') . '</strong><br>';

				if	( empty($data['reg']['ModoLiquidacion']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Modo de liquidación') . '</strong><br>';
	
				if	( $data['reg']['Estado'] == 0 )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Estado de la novedad') . '</strong><br>';
				
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarNovedadProgramable($data['reg'], $id);

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
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/novedadesProgramables/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/novedadesProgramables/lista/' . $_SESSION['NOVEDADESPROGRAMABLES']['Pagina'];

				$query = 'SELECT * FROM NOVEDADESPROGRAMABLES WHERE NOVEDADESPROGRAMABLES.Id = ' . $id;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'Id' 				=> $reg['id'],
						'Fecha' 			=> $reg['fecha'],
						'IdConcepto' 		=> $reg['idconcepto'],
						'TipoEmpleado' 		=> $reg['tipoempleado'],
						'IdEmpleado' 		=> $reg['idempleado'],
						'IdCentro' 			=> $reg['idcentro'],
						'IdCargo' 			=> $reg['idcargo'],
						'Horas' 			=> $reg['horas'],
						'Valor' 			=> $reg['valor'],
						'SalarioLimite' 	=> $reg['salariolimite'],
						'FechaLimite' 		=> $reg['fechalimite'],
						'IdTercero' 		=> $reg['idtercero'],
						'Aplica' 			=> $reg['aplica'],
						'ModoLiquidacion' 	=> $reg['modoliquidacion'], 
						'Estado' 			=> $reg['estado']
					),
					'mensajeError' => ''
				);

				if ($data) 
					$this->views->getView($this, 'actualizar', $data, $id);
			}
		}

		public function borrar($id)
		{
			$query = <<<EOD
				SELECT *
					FROM NOVEDADESPROGRAMABLES
					WHERE NOVEDADESPROGRAMABLES.Id = $id;
			EOD;
				
			$reg = $this->model->leer($query);

			$data = array(
				'reg' => array(
					'Id' 				=> $reg['id'],
					'Fecha' 			=> $reg['fecha'],
					'IdConcepto' 		=> $reg['idconcepto'],
					'TipoEmpleado' 		=> $reg['tipoempleado'],
					'IdEmpleado' 		=> $reg['idempleado'],
					'IdCentro' 			=> $reg['idcentro'],
					'IdCargo' 			=> $reg['idcargo'],
					'Horas' 			=> $reg['horas'],
					'Valor' 			=> $reg['valor'],
					'SalarioLimite' 	=> $reg['salariolimite'],
					'FechaLimite' 		=> $reg['fechalimite'],
					'IdTercero' 		=> $reg['idtercero'],
					'Aplica' 			=> $reg['aplica'],
					'ModoLiquidacion' 	=> $reg['modoliquidacion'], 
					'Estado' 			=> $reg['estado']
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['IdConcepto']))
			{
				if	( ! empty($data['mensajeError']) )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarNovedadProgramable($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/novedadesProgramables/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/novedadesProgramables/lista/' . $_SESSION['NOVEDADESPROGRAMABLES']['Pagina'];

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
			$_SESSION['Lista'] = SERVERURL . '/novedadesProgramables/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['NOVEDADESPROGRAMABLES']['Filtro'];

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

					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['NOVEDADESPROGRAMABLES']['Orden']; 
			$data['rows'] = $this->model->listarNovedadesProgramables($query);
			$this->views->getView($this, 'informe', $data);
		}

		public function importar()
		{
			$data = array();
			$data['mensajeError'] = '';


			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
				{
					$data['mensajeError'] .= "Seleccione un <strong>Archivo en Excel</strong><br>";
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

							$ConceptoAnt = '';
							$TipoEmpAnt = '';
							$TipoEmpleado = 0;
							$EmpleadoAnt = '';
							$CentroAnt = '';
							$IdCentro = 0;
							$CargoAnt = '';
							$IdCargo = 0;
							$AplicaAnt = '';
							$ModoLiqAnt = '';
							$EstadoAnt = '';
		
							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
								{
									$Fecha 			= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('A' . $i)->getCalculatedValue())->format('Y-m-d');
									$Concepto 		= trim($oHoja->getCell('B' . $i)->getCalculatedValue());
									$Concepto		= str_pad($Concepto, 5, '0', STR_PAD_LEFT);
									$TipoEmp 		= trim($oHoja->getCell('C' . $i)->getCalculatedValue());
									$Empleado 		= trim($oHoja->getCell('D' . $i)->getCalculatedValue());
									$Centro 		= trim($oHoja->getCell('E' . $i)->getCalculatedValue());
									$Cargo 			= trim($oHoja->getCell('F' . $i)->getCalculatedValue());
									$Horas 			= $oHoja->getCell('G' . $i)->getCalculatedValue();
									$Horas 			= is_null($Horas) ? 0 : $Horas;
									$Valor 			= $oHoja->getCell('H' . $i)->getCalculatedValue();
									$Valor 			= is_null($Valor) ? 0 : $Valor;
									$SalarioLimite 	= $oHoja->getCell('I' . $i)->getCalculatedValue();
									$SalarioLimite 	= is_null($SalarioLimite) ? 0 : $SalarioLimite;
									$FechaLimite = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('J' . $i)->getCalculatedValue())->format('Y-m-d');
									$FechaLimite 	= ($FechaLimite == '1970-01-01' ? NULL : $FechaLimite);
									$Tercero 		= trim($oHoja->getCell('K' . $i)->getCalculatedValue());
									$NombreTercero 	= trim($oHoja->getCell('L' . $i)->getCalculatedValue());
									$DetAplica 		= trim($oHoja->getCell('M' . $i)->getCalculatedValue());
									$ModoLiq 		= trim($oHoja->getCell('N' . $i)->getCalculatedValue());
									$DetEstado 		= trim($oHoja->getCell('O' . $i)->getCalculatedValue());

									$Mayor 			= substr($Concepto, 0, 2);
									$Auxiliar 		= substr($Concepto, 2, 3);

									if ($Concepto <> $ConceptoAnt) 
									{
										$ConceptoAnt = $Concepto;

										$query = <<<EOD
											SELECT AUXILIARES.*
												FROM AUXILIARES
													INNER JOIN MAYORES 
													ON AUXILIARES.IdMayor = MAYORES.Id 
												WHERE MAYORES.Mayor = '$Mayor' AND
													AUXILIARES.Auxiliar = '$Auxiliar' AND 
													AUXILIARES.Borrado = 0;
										EOD;

										$reg = $this->model->buscarNovedadProgramable($query);

										if ($reg)
											$IdConcepto = $reg['id'];
										else
											$data['mensajeError'] .= "Concepto no existe <strong>$Concepto</strong><br>";
									}

									if ($TipoEmp <> $TipoEmpAnt) 
									{
										$TipoEmpAnt = $TipoEmp;

										if (! empty($TipoEmp)) 
										{
											$query = <<<EOD
												SELECT *
												FROM PARAMETROS
												WHERE PARAMETROS.Parametro = 'TipoEmpleado' AND
													PARAMETROS.Detalle = '$TipoEmp';
											EOD;

											$reg = $this->model->buscarNovedadProgramable($query);

											if ($reg)
												$TipoEmpleado = $reg['id'];
											else
												$data['mensajeError'] .= "Tipo de empleado no existe <strong>$TipoEmp</strong><br>";
										}
										else
											$TipoEmpleado = 0;
									}

									if ($Empleado <> $EmpleadoAnt) 
									{
										$EmpleadoAnt = $Empleado;

										$query = <<<EOD
											SELECT *
											FROM EMPLEADOS
											WHERE EMPLEADOS.Documento = '$Empleado'
											ORDER by id DESC;
										EOD;

										$reg = $this->model->buscarNovedadProgramable($query);

										if ($reg)
											$IdEmpleado = $reg['id'];
										else
											$data['mensajeError'] .= "Empleado no existe <strong>$Empleado</strong><br>";
									}

									if ($Centro <> $CentroAnt) 
									{
										$CentroAnt = $Centro;

										$query = <<<EOD
											SELECT *
											FROM CENTROS
											WHERE CENTROS.Centro = '$Centro' AND 
												CENTROS.Borrado = 0;
										EOD;

										$reg = $this->model->buscarNovedadProgramable($query);

										if ($reg)
											$IdCentro = $reg['id'];
										else
											$data['mensajeError'] .= "Centro de costo no existe <strong>$Centro</strong><br>";
									}
	
									if ($Cargo <> $CargoAnt) 
									{
										$CargoAnt = $Cargo;

										$query = <<<EOD
											SELECT *
											FROM CARGOS
											WHERE CARGOS.Cargo = '$Cargo';
										EOD;

										$reg = $this->model->buscarNovedadProgramable($query);

										if ($reg)
											$IdCargo = $reg['id'];
										else
											$data['mensajeError'] .= "Cargo de empleados no existe <strong>$Cargo</strong><br>";
									}

									if (! empty($Tercero))
										$IdTercero = getId('TERCEROS', "TERCEROS.Documento = '$Tercero'");
									else
										$IdTercero = 0;

									if ($DetAplica <> $AplicaAnt) 
									{
										$AplicaAnt = $DetAplica;

										$query = <<<EOD
											SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'AplicaNovedad' AND
												PARAMETROS.Detalle = '$DetAplica';
										EOD;

										$reg = $this->model->buscarNovedadProgramable($query);

										if ($reg)
											$Aplica = $reg['id'];
										else
											$data['mensajeError'] .= "Aplica no existe <strong>$DetAplica</strong><br>";
									}

									if ($ModoLiq <> $ModoLiqAnt) 
									{
										$ModoLiqAnt = $ModoLiq;

										$query = <<<EOD
											SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'ModoLiquidacionNP' AND
												PARAMETROS.Detalle = '$ModoLiq';
										EOD;

										$reg = $this->model->buscarNovedadProgramable($query);

										if ($reg)
											$ModoLiquidacion = $reg['id'];
										else
											$data['mensajeError'] .= "Modo de liquidación no existe <strong>$ModoLiq</strong><br>";
									}
	
									if ($DetEstado <> $EstadoAnt) 
									{
										$EstadoAnt = $DetEstado;

										$query = <<<EOD
											SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'EstadoNovedad' AND
												PARAMETROS.Detalle = '$DetEstado';
										EOD;

										$reg = $this->model->buscarNovedadProgramable($query);

										if ($reg)
											$Estado = $reg['id'];
										else
											$data['mensajeError'] .= "Estado no existe <strong>$DetEstado</strong><br>";
									}

									if (empty($data['mensajeError'])) 
									{
										if ($IdTercero == 0){
											$tercero = getRegistro('AUXILIARES',$IdConcepto)['idtercero'];
											$IdTercero = $tercero == null ? 0 : $tercero;
										}

										$datos = array($Fecha, $IdConcepto, $TipoEmpleado, $IdEmpleado, $IdCentro, $IdCargo, $Horas, $Valor, $SalarioLimite, $FechaLimite, $IdTercero, $Aplica, $ModoLiquidacion, $Estado);

										$query = <<<EOD
											SELECT *
												FROM NOVEDADESPROGRAMABLES
												WHERE 
													NOVEDADESPROGRAMABLES.Fecha = '$Fecha' AND 
													NOVEDADESPROGRAMABLES.IdConcepto = $IdConcepto AND 
													NOVEDADESPROGRAMABLES.TipoEmpleado = $TipoEmpleado AND 
													NOVEDADESPROGRAMABLES.IdEmpleado = $IdEmpleado AND 
													NOVEDADESPROGRAMABLES.IdCentro = $IdCentro AND 
													NOVEDADESPROGRAMABLES.IdCargo = $IdCargo;
										EOD;

										$reg = $this->model->buscarNovedadProgramable($query);

										if ($reg){ 
											$this->model->actualizarNovedadProgramable($datos, $reg['id']);
										}else{
											$this->model->guardarNovedadProgramable($datos);
										}
									}
								}
							}

							if (! empty($data['mensajeError'])) 
								$this->views->getView($this, 'importar', $data);
							else
								header('Location: ' . SERVERURL . '/novedadesProgramables/lista/1');
						
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/novedadesProgramables/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/novedadesProgramables/lista/' . $_SESSION['NOVEDADESPROGRAMABLES']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>

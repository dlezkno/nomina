<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Incapacidades extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/incapacidades/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR OR $_SESSION['Login']['Perfil'] == RRHH OR $_SESSION['Login']['Perfil'] == RRHH_AUX)
				$_SESSION['Importar'] = SERVERURL . '/incapacidades/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = SERVERURL . '/incapacidades/exportar';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/incapacidades/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['INCAPACIDADES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['INCAPACIDADES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['INCAPACIDADES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['INCAPACIDADES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['INCAPACIDADES']['Filtro']))
			{
				$_SESSION['INCAPACIDADES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['INCAPACIDADES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['INCAPACIDADES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['INCAPACIDADES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['INCAPACIDADES']['Orden'])) 
					$_SESSION['INCAPACIDADES']['Orden'] = 'INCAPACIDADES.FechaInicio,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			if (! isset($_REQUEST['Action']))
			{
				$query = <<<EOD
					WHERE INCAPACIDADES.DiasIncapacidad > INCAPACIDADES.DiasCausados 
				EOD;

				if	( ! empty($lcFiltro) )
				{
					$aFiltro = explode(' ', $lcFiltro);

					for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
					{
						$query .= ' AND (';
						$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= ') ';
					}
				}
			}

			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar')
			{
				$datos = $this->model->exportarIncapacidades();

				$Archivo = 'descargas/' . $_SESSION['Login']['Usuario'] . '_Incapacidades_' . date('YmdGis') . '.csv';

				$output = fopen($Archivo, 'w');

				fputcsv($output, array('DOCUMENTO', 'NOMBRE EMPLEADO', 'CLASE AUSENTISMO', 'FECHA INCAP.', 'FECHA INICIO', 'DIAS INCAP.', 'DIAS CAUSADOS', '% AUXILIO', 'DIAGNOSTICO', 'DESCRIPCION', 'FECHA REGISTRO'), ';');

				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$reg = $datos[$i];

					foreach ($reg as $key => $value) 
					{
						if ($key == 'FechaIncapacidad' OR 
							$key == 'FechaInicio' OR 
							$key == 'DiasIncapacidad' OR 
							$key == 'DiasCausados' OR
							$key == 'PorcentajeAuxilio' OR 
							$key == 'FechaRegistro')
							continue;

						$reg[$key] = utf8_decode($value);
					}

					$regDatos = array($reg['Documento'], $reg['NombreEmpleado'], $reg['ClaseAusentismo'], $reg['FechaIncapacidad'], $reg['FechaInicio'], number_format($reg['DiasIncapacidad'], 0), number_format($reg['DiasCausados'], 0), number_format($reg['PorcentajeAuxilio'], 2), $reg['Diagnostico'], $reg['DescripcionDiagnostico'], $reg['FechaRegistro']);

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
				$query .= 'ORDER BY ' . $_SESSION['INCAPACIDADES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
				$data['rows'] = $this->model->listarIncapacidades($query);
				$this->views->getView($this, 'incapacidades', $data);
			}
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/incapacidades/actualizarNovedad';
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
			$_SESSION['Lista'] = SERVERURL . '/incapacidades/lista/' . $_SESSION['INCAPACIDADES']['Pagina'];

			$data = array(
				'reg' => array(
					'IdEmpleado' 		=> 0,
					'IdConcepto' 		=> 0, 
					'FechaIncapacidad' 	=> NULL, 
					'FechaInicio' 		=> NULL,
					'DiasIncapacidad' 	=> 0, 
					'PorcentajeAuxilio' => 0, 
					'BaseLiquidacion' 	=> 0,
					'IdDiagnostico' 	=> 0, 
					'EsProrroga'		=> FALSE
				),	
				'mensajeError' => '',
				'Nov' => array()
			);	

			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'");
			$FechaLimiteNovedades = $reg['fecha'];

			if (date('Y-m-d') > $FechaLimiteNovedades) 
				$data['mensajeError'] = label('Fecha límite para reportar novedades ha expirado') . '<br>';

			$IdEmpleado = 0;

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$Documento = $_REQUEST['Documento'];
					$reg = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento'");

					if ($reg) 
					{
						$data['reg']['IdEmpleado'] = $reg['id'];
						$IdEmpleado = $reg['id'];
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
				}
				
				if	( empty($_REQUEST['Concepto']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Concepto') . '</strong><br>';
				else
				{
					$Mayor = substr($_REQUEST['Concepto'], 0, 2);
					$Auxiliar = substr($_REQUEST['Concepto'], 2, 3);

					$reg = getRegistro('MAYORES', 0, "MAYORES.Mayor = '$Mayor'");

					if ($reg) 
					{
						$IdMayor = $reg['id'];

						$query = <<<EOD
							SELECT AUXILIARES.* 
								FROM AUXILIARES 
								WHERE AUXILIARES.IdMayor = $IdMayor AND 
									AUXILIARES.Auxiliar = '$Auxiliar' AND 
									AUXILIARES.Borrado = 0;
						EOD;

						$reg = $this->model->leer($query);

						if ($reg) 
						{
							$data['reg']['IdConcepto'] = $reg['id'];
							$IdConcepto = $reg['id'];
							$ModoLiquidacion = $reg['modoliquidacion'];

							$reg = getRegistro('PARAMETROS', $ModoLiquidacion);

							if ($reg['detalle'] == 'AUTOMÁTICO')
								$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('liquida automáticamente, no se puede reportar como novedad') . '<br>';
						}
						else
							$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
				}

				if (empty($_REQUEST['FechaIncapacidad'])) 
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de incapacidad') . '</strong><br>';
				else
				{
					$FechaIncapacidad = $_REQUEST['FechaIncapacidad'];
					$data['reg']['FechaIncapacidad'] = $_REQUEST['FechaIncapacidad'];
				}

				if (empty($_REQUEST['FechaInicio'])) 
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de inicio') . '</strong><br>';
				else
				{
					$FechaInicio = $_REQUEST['FechaInicio'];
					$data['reg']['FechaInicio'] = $_REQUEST['FechaInicio'];
				}

				if ($_REQUEST['DiasIncapacidad'] <= 0) 
					$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Días de incapacidad') . '</strong><br>';
				else
					$data['reg']['DiasIncapacidad'] = $_REQUEST['DiasIncapacidad'];

				if ($_REQUEST['PorcentajeAuxilio'] < 0 OR $_REQUEST['PorcentajeAuxilio'] > 33.33)
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Porcentaje de auxilio') . '</strong> ' . label('entre 0 y 33.33%') . '<br>';
				else
					$data['reg']['PorcentajeAuxilio'] = $_REQUEST['PorcentajeAuxilio'];

				if (empty($_REQUEST['BaseLiquidacion']))
					$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Base de liquidación') . '</strong><br>';
				else
				{
					$BaseLiquidacion = $_REQUEST['BaseLiquidacion'];
					$data['reg']['BaseLiquidacion'] = $_REQUEST['BaseLiquidacion'];
				}
	
				if (empty($_REQUEST['Diagnostico'])) 
				{
					// $data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Código de diagnóstico') . '</strong><br>';
				}
				else
				{
					$Diagnostico = $_REQUEST['Diagnostico'];
					$reg = getRegistro('DIAGNOSTICOS', 0, "DIAGNOSTICOS.Diagnostico = '$Diagnostico'");

					if ($reg) 
					{
						$data['reg']['IdDiagnostico'] = $reg['id'];
						$IdDiagnostico = $reg['id'];
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Diagnóstico') . '</strong> ' . label('no existe') . '<br>';
				}

				if (isset($_REQUEST['EsProrroga']))
					$data['reg']['EsProrroga'] = TRUE;

				if	( empty($data['mensajeError']) )
				{
					$reg = getRegistro('INCAPACIDADES', 0, "INCAPACIDADES.IdEmpleado = $IdEmpleado AND INCAPACIDADES.IdConcepto = $IdConcepto AND INCAPACIDADES.FechaInicio = '$FechaInicio'");

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Incapacidad') . '</strong> ' . label('ya existe') . '<br>';
				}

				// LISTA DE INCAPACIDADES DEL EMPLEADO
				if (! empty($IdEmpleado)) 
				{
					$query = <<<EOD
						WHERE INCAPACIDADES.IdEmpleado = $IdEmpleado 
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, AUXILIARES.Nombre;
					EOD;
			
					$data['Nov'] = $this->model->listarIncapacidades($query);
				}
				else
					$data['Nov'] = array();
				
				
				if	( ! empty($data['mensajeError']) )
				{
					$this->views->getView($this, 'adicionar', $data);
					exit;
				}
				else
				{
					$ok = $this->model->guardarIncapacidad($data['reg']);

					if ($ok) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
			{
				// LISTA DE INCAPACIDADES DEL EMPLEADO
				if (! empty($IdEmpleado)) 
				{
					$query = <<<EOD
						WHERE INCAPACIDADES.IdEmpleado = $IdEmpleado 
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, AUXILIARES.Nombre;
					EOD;
			
					$data['Nov'] = $this->model->listarIncapacidades($query);
				}
				else
					$data['Nov'] = array();

				$this->views->getView($this, 'adicionar', $data);
				exit;
			}
		}

		public function editar($id)
		{
			$data = array(
				'reg' => array(
					'IdEmpleado' 		=> 0,
					'IdConcepto' 		=> 0,
					'FechaIncapacidad' 	=> null,
					'FechaInicio' 		=> null,
					'DiasIncapacidad' 	=> 0, 
					'PorcentajeAuxilio' => 0, 
					'BaseLiquidacion' 	=> 0, 
					'IdDiagnostico' 	=> 0, 
					'EsProrroga'		=> FALSE
				),	
				'mensajeError' => '',
				'Nov' => array()
			);	

			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'");
			$FechaLimiteNovedades = $reg['fecha'];

			if (date('Y-m-d') > $FechaLimiteNovedades) 
				$data['mensajeError'] = label('Fecha límite para reportar novedades ha expirado') . '<br>';

			$reg 		= getRegistro('INCAPACIDADES', $id);
			$IdEmpleado = $reg['idempleado'];

			// LISTA DE INCAPACIDADES DEL EMPLEADO
			if (! empty($IdEmpleado)) 
			{
				$query = <<<EOD
					WHERE INCAPACIDADES.IdEmpleado = $IdEmpleado 
					ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, AUXILIARES.Nombre;
				EOD;
		
				$data['Nov'] = $this->model->listarIncapacidades($query);
			}
			else
				$data['Nov'] = array();

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$Documento = $_REQUEST['Documento'];
					$reg = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento'");

					if ($reg) 
					{
						$data['reg']['IdEmpleado'] = $reg['id'];
						$IdEmpleado = $reg['id'];
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
				}
				
				if	( empty($_REQUEST['Concepto']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Concepto') . '</strong><br>';
				else
				{
					$Mayor = substr($_REQUEST['Concepto'], 0, 2);
					$reg = getRegistro('MAYORES', 0, "MAYORES.Mayor = '$Mayor'");

					if ($reg) 
					{
						$IdMayor = $reg['id'];
						$Auxiliar = substr($_REQUEST['Concepto'], 2, 3);

						$query = <<<EOD
							SELECT AUXILIARES.* 
								FROM AUXILIARES 
								WHERE AUXILIARES.IdMayor = $IdMayor AND 
									AUXILIARES.Auxiliar = '$Auxiliar' AND 
									AUXILIARES.Borrado = 0;
						EOD;

						$reg = $this->model->leer($query);

						if ($reg) 
						{
							$data['reg']['IdConcepto'] = $reg['id'];
							$IdConcepto = $reg['id'];

							$reg = getRegistro('PARAMETROS', $reg['modoliquidacion']);

							if ($reg['detalle'] == 'AUTOMÁTICO')
								$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('liquida automáticamente, no se puede reportar como novedad') . '<br>';
						}
						else
							$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
				}

				if (empty($_REQUEST['FechaIncapacidad']))
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de incapacidad') . '</strong><br>';
				else
				{
					$FechaIncapacidad = $_REQUEST['FechaIncapacidad'];
					$data['reg']['FechaIncapacidad'] = $FechaIncapacidad;
				}

				if (empty($_REQUEST['FechaInicio']))
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de inicio') . '</strong><br>';
				else
				{
					$FechaInicio = $_REQUEST['FechaInicio'];
					$data['reg']['FechaInicio'] = $FechaInicio;
				}

				if ($_REQUEST['DiasIncapacidad'] <= 0)
					$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Días de incapacidad') . '</strong><br>';
				else
				{
					$DiasIncapacidad = $_REQUEST['DiasIncapacidad'];
					$data['reg']['DiasIncapacidad'] = $DiasIncapacidad;
				}

				if ($_REQUEST['PorcentajeAuxilio'] < 0 AND $_REQUEST['PorcentajeAuxilio'] > 33.33)
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Porcentaje de auxilio') . '</strong> ' . label('entre 0 y 33.33%') . '<br>';
				else
				{
					$PorcentajeAuxilio = $_REQUEST['PorcentajeAuxilio'];
					$data['reg']['PorcentajeAuxilio'] = $PorcentajeAuxilio;
				}

				if (empty($_REQUEST['BaseLiquidacion']))
					$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Base de liquidación') . '</strong><br>';
				else
				{
					$BaseLiquidacion = $_REQUEST['BaseLiquidacion'];
					$data['reg']['BaseLiquidacion'] = $_REQUEST['BaseLiquidacion'];
				}
	
				if (empty($_REQUEST['Diagnostico'])) 
				{
					// $data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Código de diagnóstico') . '</strong><br>';
				}
				else
				{
					$Diagnostico = $_REQUEST['Diagnostico'];
					$reg = getRegistro('DIAGNOSTICOS', 0, "DIAGNOSTICOS.Diagnostico = '$Diagnostico'");

					if ($reg) 
					{
						$data['reg']['IdDiagnostico'] = $reg['id'];
						$IdDiagnostico = $reg['id'];
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Diagnóstico') . '</strong> ' . label('no existe') . '<br>';
				}
	
				if (isset($_REQUEST['EsProrroga']))
					$data['reg']['EsProrroga'] = TRUE;

				if	( ! $data['mensajeError'] )
				{
					$query = <<<EOD
							INCAPACIDADES.IdEmpleado = $IdEmpleado AND 
							INCAPACIDADES.IdConcepto = $IdConcepto AND 
							INCAPACIDADES.FechaInicio = '$FechaInicio' AND 
							INCAPACIDADES.Id <> $id;
					EOD;

					$reg = getRegistro('INCAPACIDADES', 0, $query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Incapacidad') . '</strong> ' . label('ya existe') . '<br>';
				}

				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'actualizar', $data);
					exit;
				}
				else
				{
					$resp = $this->model->actualizarIncapacidad($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/incapacidades/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/incapacidades/lista/' . $_SESSION['INCAPACIDADES']['Pagina'];

				$regEmp 						= getRegistro('EMPLEADOS', $reg['idempleado']);
				$_REQUEST['Documento'] 			= $regEmp['documento'];
				$_REQUEST['NombreEmpleado'] 	= $regEmp['apellido1'] . ' ' . $regEmp['apellido2'] . ' ' . $regEmp['nombre1'] . ' ' . $regEmp['nombre2'];
				$regCargo 						= getRegistro('CARGOS', $regEmp['idcargo']);
				$_REQUEST['Cargo'] 				= $regCargo['nombre'];
				$regCentro 						= getRegistro('CENTROS', $regEmp['idcentro']);
				$_REQUEST['Centro'] 			= $regCentro['nombre'];

				$regAux 						= getRegistro('AUXILIARES', $reg['idconcepto']);
				$regMay 						= getRegistro('MAYORES', $regAux['idmayor']);
				$_REQUEST['Concepto'] 			= $regMay['mayor'] . $regAux['auxiliar'];
				$_REQUEST['NombreConcepto'] 	= $regAux['nombre'];
				
				$_REQUEST['FechaIncapacidad'] 	= $reg['fechaincapacidad'];
				$_REQUEST['FechaInicio'] 		= $reg['fechainicio'];
				$_REQUEST['DiasIncapacidad'] 	= $reg['diasincapacidad'];
				$_REQUEST['PorcentajeAuxilio'] 	= $reg['porcentajeauxilio'];
				$_REQUEST['BaseLiquidacion'] 	= $reg['baseliquidacion'];
				$_REQUEST['IdDiagnostico'] 		= $reg['iddiagnostico'];
				$_REQUEST['EsProrroga'] 		= $reg['esprorroga'];
				
				if ($reg['iddiagnostico'] > 0) 
				{
					$regDiagnostico 				= getRegistro('DIAGNOSTICOS', $reg['iddiagnostico']);
					$_REQUEST['Diagnostico'] 		= $regDiagnostico['diagnostico'];
					$_REQUEST['NombreDiagnostico'] 	= $regDiagnostico['nombre'];
				}
				else
				{
					$_REQUEST['Diagnostico'] 		= '';
					$_REQUEST['NombreDiagnostico'] 	= '';
				}

				$this->views->getView($this, 'actualizar', $data);
				exit;
			}
		}

		public function borrar($id)
		{
			$data = array(
				'reg' => array(
					'IdEmpleado' 		=> 0,
					'IdConcepto' 		=> 0,
					'FechaIncapacidad' 	=> null, 
					'FechaInicio' 		=> null, 
					'DiasIncapacidad' 	=> 0,
					'PorcentajeAuxilio' => 0, 
					'BaseLiquidacion' 	=> 0, 
					'IdDiagnostico' 	=> 0, 
					'EsProrroga'		=> FALSE
				),	
				'mensajeError' => '',
				'Nov' => array()
			);	

			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'");
			$FechaLimiteNovedades = $reg['fecha'];

			if (date('Y-m-d') > $FechaLimiteNovedades) 
				$data['mensajeError'] = label('Fecha límite para reportar novedades ha expirado') . '<br>';
			
			$reg = getRegistro('INCAPACIDADES', $id);
			$IdEmpleado = $reg['idempleado'];

			// LISTA DE INCAPACIDADES DEL EMPLEADO
			if (! empty($IdEmpleado)) 
			{
				$query = <<<EOD
					WHERE INCAPACIDADES.IdEmpleado = $IdEmpleado 
					ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, AUXILIARES.Nombre;
				EOD;
		
				$data['Nov'] = $this->model->listarIncapacidades($query);
			}
			else
				$data['Nov'] = array();
			
			if (isset($_REQUEST['Documento']))
			{
				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'actualizar', $data);
					exit;
				}
				else
				{
					$resp = $this->model->borrarIncapacidad($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/incapacidades/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/incapacidades/lista/' . $_SESSION['INCAPACIDADES']['Pagina'];

				$regEmp 						= getRegistro('EMPLEADOS', $reg['idempleado']);
				$_REQUEST['Documento'] 			= $regEmp['documento'];
				$_REQUEST['NombreEmpleado'] 	= $regEmp['apellido1'] . ' ' . $regEmp['apellido2'] . ' ' . $regEmp['nombre1'] . ' ' . $regEmp['nombre2'];
				$regCargo 						= getRegistro('CARGOS', $regEmp['idcargo']);
				$_REQUEST['Cargo'] 				= $regCargo['nombre'];
				$regCentro 						= getRegistro('CENTROS', $regEmp['idcentro']);
				$_REQUEST['Centro'] 			= $regCentro['nombre'];

				$regAux 						= getRegistro('AUXILIARES', $reg['idconcepto']);
				$regMay 						= getRegistro('MAYORES', $regAux['idmayor']);
				$_REQUEST['Concepto'] 			= $regMay['mayor'] . $regAux['auxiliar'];
				$_REQUEST['NombreConcepto'] 	= $regAux['nombre'];
				
				$_REQUEST['FechaIncapacidad'] 	= $reg['fechaincapacidad'];
				$_REQUEST['FechaInicio'] 		= $reg['fechainicio'];
				$_REQUEST['DiasIncapacidad'] 	= $reg['diasincapacidad'];
				$_REQUEST['PorcentajeAuxilio'] 	= $reg['porcentajeauxilio'];
				$_REQUEST['BaseLiquidacion'] 	= $reg['baseliquidacion'];
				$_REQUEST['IdDiagnostico'] 		= $reg['iddiagnostico'];
				$_REQUEST['EsProrroga'] 		= $reg['esprorroga'];
				
				if ($reg['iddiagnostico'] > 0) 
				{
					$regDiagnostico 				= getRegistro('DIAGNOSTICOS', $reg['iddiagnostico']);
					$_REQUEST['Diagnostico'] 		= $regDiagnostico['diagnostico'];
					$_REQUEST['NombreDiagnostico'] 	= $regDiagnostico['nombre'];
				}
				else
				{
					$_REQUEST['Diagnostico'] 		= '';
					$_REQUEST['NombreDiagnostico'] 	= '';
				}

				$this->views->getView($this, 'actualizar', $data);
				exit;
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
			$_SESSION['Lista'] = SERVERURL . '/incapacidades/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['INCAPACIDADES']['Filtro'];

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

					$query .= "UPPER(REPLACE(MAYORES.Mayor, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(MAYORES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['INCAPACIDADES']['Orden']; 
			$data['rows'] = $this->model->listarIncapacidades($query);
			$this->views->getView($this, 'informe', $data);
		}

		public function importar()
		{
			$data = array();
			$data['mensajeError'] = '';

			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'");
			$FechaLimiteNovedades = $reg['fecha'];

			if (date('Y-m-d') > $FechaLimiteNovedades) 
				$data['mensajeError'] = label('Fecha límite para reportar novedades ha expirado') . '<br>';

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

							$EmpleadoAnt = '';

							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								if (empty($oHoja->getCell('A' . $i)->getCalculatedValue()))
									break;

								$Empleado 				= trim($oHoja->getCell('A' . $i)->getCalculatedValue());
								$Concepto 				= trim($oHoja->getCell('B' . $i)->getCalculatedValue());
								$FechaIncapacidad 		= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('D' . $i)->getCalculatedValue())->format('Y-m-d');
								$FechaInicio 			= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('E' . $i)->getCalculatedValue())->format('Y-m-d');
								$DiasIncapacidad 		= $oHoja->getCell('F' . $i)->getCalculatedValue();
								$PorcentajeAuxilio 		= $oHoja->getCell('G' . $i)->getCalculatedValue();
								$BaseLiquidacion 		= trim($oHoja->getCell('H' . $i)->getCalculatedValue());
								$EsProrroga 			= trim($oHoja->getCell('I' . $i)->getCalculatedValue());
								$Diagnostico 			= trim($oHoja->getCell('J' . $i)->getCalculatedValue());

								$query = <<<EOD
									SELECT EMPLEADOS.* 
										FROM EMPLEADOS 
											INNER JOIN PARAMETROS 
												ON EMPLEADOS.Estado = PARAMETROS.Id 
										WHERE EMPLEADOS.Documento = '$Empleado' AND 
											EMPLEADOS.fechaliquidacion IS NULL;
								EOD;

								$regEmpleado = $this->model->leer($query);

								if ($regEmpleado) 
									$IdEmpleado = $regEmpleado['id'];
								else
								{
									$data['mensajeError'] .= 'Empleado no existe o ya fue liquidado (' . $Empleado . ') <br>';
									continue;
								}

								$Concepto = str_pad($Concepto, 5, '0', STR_PAD_LEFT);

								$Mayor 					= substr($Concepto, 0, 2);
								$Auxiliar 				= substr($Concepto, 2, 3);

								$regMayor = getRegistro('MAYORES', 0, "MAYORES.Mayor = '$Mayor'");

								if ($regMayor) 
									$IdMayor = $regMayor['id'];
								else
									$IdMayor = 0;

								if ($IdMayor > 0) 
								{
									$query = <<<EOD
										SELECT AUXILIARES.* 
											FROM AUXILIARES 
											WHERE AUXILIARES.IdMayor = $IdMayor AND 
												AUXILIARES.Auxiliar = '$Auxiliar' AND 
												AUXILIARES.Borrado = 0;
									EOD;

									$regAuxiliar = $this->model->leer($query);

									if ($regAuxiliar) 
									{
										$IdConcepto = $regAuxiliar['id'];

										$reg = getRegistro('PARAMETROS', $regAuxiliar['modoliquidacion']);

										if ($reg['detalle'] == 'AUTOMÁTICO') 
										{
											$data['mensajeError'] .= 'Concepto liquida automáticamente, no se pude reportar como novedad (' . $Empleado . ' - ' . $Concetpto . ') <br>';
											continue;
										}
									}
									else
									{
										$data['mensajeError'] .= 'Concepto no existe (' . $Concepto . ') <br>';
										continue;
									}
								}
								else
								{
									$data['mensajeError'] .= 'Concepto no existe (' . $Concepto . ') <br>';
									continue;
								}
								
								// $reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");

								if ($FechaIncapacidad > $FechaInicio)
								{
									$data['mensajeError'] .= "Fecha de incapacidad debe ser menor o igual a la de inicio ($Empleado - $Concepto) <br>";
									continue;
								}

								// if ($FechaInicio < $reg['fechainicialperiodo'] OR $FechaInicio > $reg['fechafinalperiodo']) 
								// {
								// 	$data['mensajeError'] .= "Fecha de inicio de incapacidad fuera del período actual ($Empleado - $Concepto) <br>";
								// 	continue;
								// }

								if (is_null($PorcentajeAuxilio))
									$PorcentajeAuxilio = 0;
								elseif ($PorcentajeAuxilio > 33.33)
								{
									$data['mensajeError'] .= "Porcentaje de auxilio debe ser menor o igual a 33.33% ($Empleado - $Concepto) <br>";
									continue;
								}

								// BUSCAR PARAMETRO BASE DE LIQUIDACION
								if (strpos($BaseLiquidacion, 'BASICO') > 0)
									$BaseLiquidacion = getId('PARAMETROS', "PARAMETROS.Parametro = 'BaseLiquidacionIncapacidad' AND PARAMETROS.Detalle = 'SUELDO BASICO'");
								else
									$BaseLiquidacion = getId('PARAMETROS', "PARAMETROS.Parametro = 'BaseLiquidacionIncapacidad' AND PARAMETROS.Detalle = 'IBC MES ANTERIOR'");

								if (is_null($EsProrroga) OR strtoupper($EsProrroga) == 'NO')
									$EsProrroga = 0;
								else
									$EsProrroga = 1;

								// BUSCAR EL DIAGNOSTICO Y SI NO EXISTE CREARLO
								if (! empty($Diagnostico))
								{
									$IdDiagnostico = getId('DIAGNOSTICOS', "DIAGNOSTICOS.Diagnostico = '$Diagnostico'");

									if ($IdDiagnostico == 0)
									{
										$NombreDiagnostico = $oHoja->getCell('J' . $i)->getCalculatedValue();

										$query = <<<EOD
											INSERT INTO DIAGNOSTICOS 
												(Diagnostico, Nombre)
												VALUES (
													'$Diagnostico', 
													'$NombreDiagnostico');
										EOD;

										$ok = $this->model->query($query);

										$IdDiagnostico = getId('DIAGNOSTICOS', "DIAGNOSTICOS.Diagnostico = '$Diagnostico'");
									}
								}
								else
									$IdDiagnostico = 0;

								$datos = array($IdEmpleado, $IdConcepto, $FechaIncapacidad, $FechaInicio, $DiasIncapacidad, $PorcentajeAuxilio, $BaseLiquidacion, $EsProrroga, $IdDiagnostico); 

								$reg = getRegistro('INCAPACIDADES', 0, "INCAPACIDADES.IdEmpleado = $IdEmpleado AND INCAPACIDADES.IdConcepto = $IdConcepto AND INCAPACIDADES.FechaIncapacidad = '$FechaIncapacidad'");

								if ($reg) 
								{
									$data['mensajeError'] .= "Incapacidad ya existe ($Empleado - $Concepto - $FechaIncapacidad) <br>";

									// $this->model->actualizarIncapacidad($datos, $reg['id']);
								}
								else
									$this->model->guardarIncapacidad($datos);
							}

							if (! empty($data['mensajeError'])) 
								$this->views->getView($this, 'importar', $data);
							else
								header('Location: ' . SERVERURL . '/incapacidades/lista/1');
							
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/incapacidades/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/incapacidades/lista/' . $_SESSION['INCAPACIDADES']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>
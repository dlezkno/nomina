<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class RetirosEmpleados extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/retirosEmpleados/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR OR $_SESSION['Login']['Perfil'] == RRHH OR $_SESSION['Login']['Perfil'] == RRHH_AUX)
				$_SESSION['Importar'] = SERVERURL . '/retirosEmpleados/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/retirosEmpleados/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['RETIROS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['RETIROS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['RETIROS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['RETIROS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['RETIROS']['Filtro']))
			{
				$_SESSION['RETIROS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['RETIROS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['RETIROS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['RETIROS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['RETIROS']['Orden'])) 
					$_SESSION['RETIROS']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$Referencia = $reg['valor'];
			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$Periodicidad = $reg['valor'];
			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			$IdPeriodo = $reg['valor'];
			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");
			$Ciclo = $reg['valor'];
			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'");
			$FechaLimiteNovedades = $reg['fecha'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];
			$FechaInicial = $regPeriodo['fechainicial'];
			$FechaFinal = $regPeriodo['fechafinal'];

			$regPeriodicidad = getRegistro('PARAMETROS', $Periodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);
		
			if ($FechaLimiteNovedades < date('Y-m-d')) 
			{
				// $_SESSION['NuevoRegistro'] = '';
				// $_SESSION['Importar'] = '';
				$data['RO'] = TRUE;
			}
			else
				$data['RO'] = FALSE;

			// $query = "WHERE EMPLEADOS.FechaRetiro >= '$FechaInicial' AND EMPLEADOS.FechaRetiro <= '$FechaFinal' AND PARAMETROS1.Detalle = 'RETIRADO' ";

			$query = "WHERE EMPLEADOS.FechaLiquidacion IS NULL AND EMPLEADOS.FechaRetiro IS NOT NULL AND PARAMETROS1.Detalle = 'RETIRADO' ";

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

					$query .= ') ';
				}

			}

			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['RETIROS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarRetirosEmpleados($query);
			$this->views->getView($this, 'retirosEmpleados', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/retirosEmpleados/actualizarNovedad';
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
			$_SESSION['Lista'] = SERVERURL . '/retirosEmpleados/lista/' . $_SESSION['RETIROS']['Pagina'];

			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'");
			$FechaLimiteNovedades = $reg['fecha'];

			$data = array(
				'reg' => array(
					'IdEmpleado' => 0, 
					'Estado' => 0, 
					'FechaRetiro' => NULL,
					'MotivoRetiro' => 0
				),	
				'mensajeError' => ''
			);	

			if (date('Y-m-d') > $FechaLimiteNovedades) 
			{
				$data['mensajeError'] = 'Fecha límite para reportar novedades ha expirado.';
				$this->views->getView($this, 'adicionar', $data);
				exit;
			}

			$IdEmpleado = 0;

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$Documento = $_REQUEST['Documento'];

					$query = <<<EOD
						SELECT EMPLEADOS.* 
							FROM EMPLEADOS 
								INNER JOIN PARAMETROS 
									ON EMPLEADOS.Estado = PARAMETROS.Id 
							WHERE EMPLEADOS.Documento = '$Documento' AND 
								PARAMETROS.Detalle = 'ACTIVO'; 
					EOD;

					$regEmpleado = $this->model->leer($query);

					if ($regEmpleado)
						$IdEmpleado = $regEmpleado['id'];
					else
						$IdEmpleado = 0;

					if ($IdEmpleado == 0) 
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
					else
						$data['reg']['IdEmpleado'] = $IdEmpleado;
				}
				
				if ($IdEmpleado > 0)
				{
					$data['reg']['Estado'] = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'RETIRADO'");

					if	( empty($_REQUEST['FechaRetiro']) )
						$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de retiro') . '</strong><br>';
					elseif ($_REQUEST['FechaRetiro'] < $regEmpleado['fechaingreso'])
						$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de retiro') . '</strong> ' . label('posterior a la fecha de ingreso') . '<br>';
					else
						$data['reg']['FechaRetiro'] = $_REQUEST['FechaRetiro'];
				}

				if (empty($_REQUEST['MotivoRetiro']))
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Motivo de retiro') . '</strong><br>';
				else
					$data['reg']['MotivoRetiro'] = $_REQUEST['MotivoRetiro'];

				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'adicionar', $data);
					exit;
				}
				else
				{
					$ok = $this->model->retirarEmpleado($data['reg']);

					if ($ok) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
			{
				$this->views->getView($this, 'adicionar', $data);
				exit;
			}
		}

		public function editar($id)
		{
			$data = array(
				'reg' => array(
					'IdEmpleado' => 0,
					'Estado' => 0, 
					'FechaRetiro' => null,
					'MotivoRetiro' => 0
				),	
				'mensajeError' => ''
			);	

			$reg = getRegistro('EMPLEADOS', $id);

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$Documento = $_REQUEST['Documento'];
					$regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento'");

					if ($regEmpleado)
						$IdEmpleado = $regEmpleado['id'];
					else
						$IdEmpleado = 0;

					if ($IdEmpleado == 0) 
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
					else
						$data['reg']['IdEmpleado'] = $IdEmpleado;
				}
				
				if ($IdEmpleado > 0)
				{
					$data['reg']['Estado'] = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'RETIRADO'");

					if	( empty($_REQUEST['FechaRetiro']) )
						$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de retiro') . '</strong><br>';
					elseif ($_REQUEST['FechaRetiro'] < $regEmpleado['fechaingreso'])
						$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de retiro') . '</strong> ' . label('posterior a la fecha de ingreso') . '<br>';
					else
						$data['reg']['FechaRetiro'] = $_REQUEST['FechaRetiro'];
				}

				if (empty($_REQUEST['MotivoRetiro']))
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Motivo de retiro') . '</strong><br>';
				else
					$data['reg']['MotivoRetiro'] = $_REQUEST['MotivoRetiro'];

				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'actualizar', $data);
					exit;
				}
				else
				{
					$ok = $this->model->retirarEmpleado($data['reg']);

					if ($ok) 
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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/retirosEmpleados/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/retirosEmpleados/lista/' . $_SESSION['RETIROS']['Pagina'];

				$regEmp = getRegistro('EMPLEADOS', $id);
				$_REQUEST['Documento'] = $regEmp['documento'];
				$_REQUEST['NombreEmpleado'] = $regEmp['apellido1'] . ' ' . $regEmp['apellido2'] . ' ' . $regEmp['nombre1'] . ' ' . $regEmp['nombre2'];
				$regCargo = getRegistro('CARGOS', $regEmp['idcargo']);
				$_REQUEST['Cargo'] = $regCargo['nombre'];
				$regCentro = getRegistro('CENTROS', $regEmp['idcentro']);
				$_REQUEST['Centro'] = $regCentro['nombre'];

				$_REQUEST['FechaRetiro'] = $reg['fecharetiro'];
				$_REQUEST['MotivoRetiro'] = $reg['motivoretiro'];
	
				$this->views->getView($this, 'actualizar', $data);
				exit;
			}
		}

		public function borrar($id)
		{
			$data = array(
				'reg' => array(
					'IdEmpleado' => 0, 
					'Estado' => 0, 
					'FechaRetiro' => null, 
					'MotivoRetiro' => 0
				),	
				'mensajeError' => ''
			);	
			
			$reg = getRegistro('EMPLEADOS', $id);

			$Estado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ACTIVO'");

			if (isset($_REQUEST['Documento']))
			{
				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'actualizar', $data);
					exit;
				}
				else
				{
					$resp = $this->model->borrarRetiroEmpleado($id, $Estado);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/retirosEmpleados/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/retirosEmpleados/lista/' . $_SESSION['RETIROS']['Pagina'];

				if ($data) 
				{
					$regEmp = getRegistro('EMPLEADOS', $id);
					$_REQUEST['Documento'] = $regEmp['documento'];
					$_REQUEST['NombreEmpleado'] = $regEmp['apellido1'] . ' ' . $regEmp['apellido2'] . ' ' . $regEmp['nombre1'] . ' ' . $regEmp['nombre2'];
					$regCargo = getRegistro('CARGOS', $regEmp['idcargo']);
					$_REQUEST['Cargo'] = $regCargo['nombre'];
					$regCentro = getRegistro('CENTROS', $regEmp['idcentro']);
					$_REQUEST['Centro'] = $regCentro['nombre'];

					$_REQUEST['FechaRetiro'] = $reg['fecharetiro'];
					$_REQUEST['MotivoRetiro'] = $reg['motivoretiro'];

					$this->views->getView($this, 'actualizar', $data);
					exit;
				}
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
			$_SESSION['Lista'] = SERVERURL . '/retirosEmpleados/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$Referencia = $reg['valor'];
			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$Periodicidad = $reg['valor'];
			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			$IdPeriodo = $reg['valor'];
			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");
			$Ciclo = $reg['valor'];
			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'");
			$FechaLimiteNovedades = $reg['fecha'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];
			$FechaInicial = $regPeriodo['fechainicial'];
			$FechaFinal = $regPeriodo['fechafinal'];

			$regPeriodicidad = getRegistro('PARAMETROS', $Periodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$lcFiltro = $_SESSION['RETIROS']['Filtro'];

			$query = "WHERE EMPLEADOS.FechaRetiro >= '$FechaInicial' AND EMPLEADOS.FechaRetiro <= '$FechaFinal' AND PARAMETROS1.Detalle = 'RETIRADO' ";

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

					$query .= ') ';
				}

			}
			
			$query .= 'ORDER BY ' . $_SESSION['RETIROS']['Orden']; 
			$data['rows'] = $this->model->listarRetirosEmpleados($query);
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
					ini_set('max_execution_time', 0);

					// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
					$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
					$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
					$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
					$reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");
					
					$Referencia = $reg1['valor'];
					$IdPeriodicidad = $reg2['valor'];

					$Periodicidad = getRegistro('PARAMETROS', $IdPeriodicidad)['detalle'];
					$cPeriodicidad = substr($Periodicidad, 0, 1);

					$IdPeriodo = $reg3['valor'];
					$Ciclo = $reg4['valor'];

					$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
					$Periodo = $regPeriodo['periodo'];

					$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;
					
					$archivo = $_FILES['archivo']['name'];
		
					if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
					{
						if ( file_exists ($archivo) )
						{
							$oExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $oExcel->getSheet(0);

							$P_SabadoFestivo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'SabadoFestivo'")['valor'];

							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								$Documento 			= $oHoja->getCell('A' . $i)->getCalculatedValue();
								$NombreEmpleado 	= $oHoja->getCell('B' . $i)->getCalculatedValue();
								$FechaRetiro 		= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('C' . $i)->getCalculatedValue())->format('Y-m-d');
								$FechaLiquidacion 	= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('D' . $i)->getCalculatedValue())->format('Y-m-d');
								$MotivoRetiro 		= $oHoja->getCell('E' . $i)->getCalculatedValue();

								$query = <<<EOD
									SELECT EMPLEADOS.* 
										FROM EMPLEADOS 
											INNER JOIN PARAMETROS 
												ON EMPLEADOS.Estado = PARAMETROS.Id 
										WHERE EMPLEADOS.Documento = '$Documento' AND 
											PARAMETROS.Detalle = 'ACTIVO';
								EOD;

								$regEmpleado = $this->model->leer($query);

								if ($regEmpleado) 
									$IdEmpleado = $regEmpleado['id'];
								else
								{
									$data['mensajeError'] .= 'Empleado no existe (' . $Documento . ' - ' . $NombreEmpleado . ') <br>';
									continue;
								}

								if ($FechaRetiro < $regEmpleado['fechaingreso'])
								{
									$data['mensajeError'] .= 'Empleado (' . $Documento . ' - ' . $NombreEmpleado . ') con fecha de retiro anterior a la de ingreso<br>';
									continue;
								}

								// if  (date('w', strtotime($FechaRetiro)) == 5)  // VIERNES
								// {
								// 	if ($P_SabadoFestivo)
								// 		$FechaRetiro = date('Y-m-d', strtotime($FechaRetiro . ' + 2 day'));
								// 	else
								// 		$FechaRetiro = date('Y-m-d', strtotime($FechaRetiro . ' + 1 day'));
								// }
								// elseif  (date('w', strtotime($FechaRetiro)) == 6)  // SABADO
								// 	$FechaRetiro = date('Y-m-d', strtotime($FechaRetiro . ' + 1 day'));

								$Estado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'RETIRADO'");
								$MotivoRetiro = getId('PARAMETROS', "PARAMETROS.Parametro = 'MotivoRetiro' AND PARAMETROS.Detalle = '$MotivoRetiro'");

								if ($MotivoRetiro == 0) 
								{
									$data['mensajeError'] .= 'Motivo de retiro no válido. Empleado (' . $Documento . ' - ' . $NombreEmpleado . ') <br>';
									continue;
								}

								$datos = array(
									'IdEmpleado' => $IdEmpleado, 
									'Estado' => $Estado, 
									'FechaRetiro' => $FechaRetiro, 
									'MotivoRetiro' => $MotivoRetiro); 

								$this->model->retirarEmpleado($datos);
							}

							if (! empty($data['mensajeError'])) 
								$this->views->getView($this, 'importar', $data);
							else
								header('Location: ' . SERVERURL . '/retirosEmpleados/lista/1');
							
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/retirosEmpleados/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/retirosEmpleados/lista/' . $_SESSION['RETIROS']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>
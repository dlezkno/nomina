<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class AumentosSalariales extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/aumentosSalariales/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR OR $_SESSION['Login']['Perfil'] == RRHH OR $_SESSION['Login']['Perfil'] == RRHH_AUX)
				$_SESSION['Importar'] = SERVERURL . '/aumentosSalariales/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/aumentosSalariales/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['AUMENTOSSALARIALES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['AUMENTOSSALARIALES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['AUMENTOSSALARIALES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['AUMENTOSSALARIALES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['AUMENTOSSALARIALES']['Filtro']))
			{
				$_SESSION['AUMENTOSSALARIALES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['AUMENTOSSALARIALES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['AUMENTOSSALARIALES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['AUMENTOSSALARIALES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['AUMENTOSSALARIALES']['Orden'])) 
					$_SESSION['AUMENTOSSALARIALES']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUMENTOSSALARIALES.FechaAumento';

			$IdPeriodo 				= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$regPeriodo 			= getRegistro('PERIODOS', $IdPeriodo);
			$FechaInicialPeriodo 	= $regPeriodo['fechainicial'];
			$FechaFinalPeriodo 		= $regPeriodo['fechafinal'];

			$reg 					= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'");
			$FechaLimiteNovedades 	= $reg['fecha'];

			if ($FechaLimiteNovedades < date('Y-m-d')) 
			{
				// $_SESSION['NuevoRegistro'] = '';
				// $_SESSION['Importar'] = '';
				$data['RO'] = TRUE;
			}
			else
				$data['RO'] = FALSE;

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= '(';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.CodigoSAP, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Cargo, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}

				$query .= ') ';
			}

			if (! empty($query))
				$query = "WHERE " . $query;
			else
				$query = "WHERE AUMENTOSSALARIALES.Procesado = 0";

			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['AUMENTOSSALARIALES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarAumentosSalariales($query);
			$this->views->getView($this, 'aumentosSalariales', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/aumentosSalariales/actualizarNovedad';
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
			$_SESSION['Lista'] = SERVERURL . '/aumentosSalariales/lista/' . $_SESSION['AUMENTOSSALARIALES']['Pagina'];

			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'");
			$FechaLimiteNovedades = $reg['fecha'];

			if (date('Y-m-d') > $FechaLimiteNovedades) 
			{
				$data['mensajeError'] = 'Fecha límite para reportar novedades ha expirado.';
				$this->views->getView($this, 'adicionar', $data);
				exit;
			}

			$data = array(
				'reg' => array(
					'IdEmpleado' => 0,
					'FechaAumento' => NULL,
					'SueldoBasicoAnterior' => 0, 
					'SubsidioTransporteAnterior' => 0, 
					'SueldoBasico' => 0, 
					'SubsidioTransporte' => 0, 
					'Procesado' => 0
				),	
				'mensajeError' => ''
			);	

			$IdEmpleado = 0;

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$Documento = $_REQUEST['Documento'];
					$idEstadoActivo = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ACTIVO'");
					$regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento' AND EMPLEADOS.Estado=$idEstadoActivo");

					if ($regEmpleado) 
					{
						$regTipoEmp = getRegistro('PARAMETROS', $regEmpleado['tipocontrato']);

						if ($regTipoEmp['detalle'] == 'APRENDIZ DEL SENA') 
							$data['mensajeError'] .= label('Debe seleccionar un empleado que no sea') . ' <strong>' . label('Aprendiz del SENA') . '</strong><br>';
						else
						{
							$data['reg']['IdEmpleado'] = $regEmpleado['id'];
							$data['reg']['SueldoBasicoAnterior'] = $regEmpleado['sueldobasico'];
							$data['reg']['SubsidioTransporteAnterior'] = $regEmpleado['subsidiotransporte'];
						}
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe o no esta activo') . '<br>';
				}
				
				if	( empty($_REQUEST['FechaAumento']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de aumento') . '</strong><br>';
				else
				{
					$data['reg']['FechaAumento'] = $_REQUEST['FechaAumento'];
				}

				if ($_REQUEST['SueldoBasico'] <= $regEmpleado['sueldobasico']) 
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo básico') . '</strong> ' . label('mayor al Sueldo anterior') . '<br>';
				else
					$data['reg']['SueldoBasico'] = $_REQUEST['SueldoBasico'];

				// if	( ! $data['mensajeError'] )
				// {
				// 	$reg = getRegistro('AUMENTOSSALARIALES', 0, "AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND AUMENTOSSALARIALES.Procesado = 0");

				// 	if ($reg) 
				// 		$data['mensajeError'] .= label('Existe un') . ' <strong>' . label('Aumento salarial') . '</strong> ' . label('sin procesar') . '<br>';
				// }

				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'adicionar', $data);
					exit;
				}
				else
				{
					$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];
					$SubsidioTransporteCompleto = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'SUBSIDIO COMPLETO'");
					$NoSubsidioTransporte = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'NO RECIBE SUBSIDIO'");

					if ($_REQUEST['SueldoBasico'] <= $SueldoMinimo * 2) 
						$data['reg']['SubsidioTransporte'] = $SubsidioTransporteCompleto;
					else
						$data['reg']['SubsidioTransporte'] = $NoSubsidioTransporte;

					$ok = $this->model->guardarAumentoSalarial($data['reg']);

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
					'FechaAumento' => NULL,
					'SueldoBasicoAnterior' => 0, 
					'SubsidioTransporteAnterior' => 0, 
					'SueldoBasico' => 0, 
					'SubsidioTransporte' => 0, 
					'Procesado' => 0
				),	
				'mensajeError' => ''
			);	

			$reg = getRegistro('AUMENTOSSALARIALES', $id);
			$IdEmpleado = $reg['idempleado'];

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$Documento = $_REQUEST['Documento'];
					$regEmpleado = getRegistro('EMPLEADOS', $IdEmpleado);

					if ($regEmpleado) 
					{
						$regTipoEmp = getRegistro('PARAMETROS', $regEmpleado['tipocontrato']);

						if ($regTipoEmp['detalle'] == 'APRENDIZ DEL SENA') 
							$data['mensajeError'] .= label('Debe seleccionar un empleado que no sea') . ' <strong>' . label('Aprendiz del SENA') . '</strong><br>';
						else
						{
							$data['reg']['IdEmpleado'] = $regEmpleado['id'];
							$data['reg']['SueldoBasicoAnterior'] = $regEmpleado['sueldobasico'];
							$data['reg']['SubsidioTransporteAnterior'] = $regEmpleado['subsidiotransporte'];
						}
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
				}
				
				if	( empty($_REQUEST['FechaAumento']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de aumento') . '</strong><br>';
				else
				{
					$data['reg']['FechaAumento'] = $_REQUEST['FechaAumento'];
				}

				$data['reg']['SueldoBasico'] = $_REQUEST['SueldoBasico'];


				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'actualizar', $data);
					exit;
				}
				else
				{
					$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];
					$SubsidioTransporteCompleto = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'SUBSIDIO COMPLETO'");
					$NoSubsidioTransporte = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'NO RECIBE SUBSIDIO'");

					if ($_REQUEST['SueldoBasico'] <= $SueldoMinimo * 2) 
						$data['reg']['SubsidioTransporte'] = $SubsidioTransporteCompleto;
					else
						$data['reg']['SubsidioTransporte'] = $NoSubsidioTransporte;

					$ok = $this->model->actualizarAumentoSalarial($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/aumentosSalariales/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/aumentosSalariales/lista/' . $_SESSION['AUMENTOSSALARIALES']['Pagina'];

				$regEmp = getRegistro('EMPLEADOS', $reg['idempleado']);
				$_REQUEST['Documento'] = $regEmp['documento'];
				$_REQUEST['NombreEmpleado'] = $regEmp['apellido1'] . ' ' . $regEmp['apellido2'] . ' ' . $regEmp['nombre1'] . ' ' . $regEmp['nombre2'];
				$regCargo = getRegistro('CARGOS', $regEmp['idcargo']);
				$_REQUEST['Cargo'] = $regCargo['nombre'];
				$regCentro = getRegistro('CENTROS', $regEmp['idcentro']);
				$_REQUEST['Centro'] = $regCentro['nombre'];

				$_REQUEST['FechaAumento'] = $reg['fechaaumento'];
				$_REQUEST['SueldoBasico'] = $reg['sueldobasico'];
				$_REQUEST['SueldoAnterior'] = $reg['sueldobasicoanterior'];
				$_REQUEST['Procesado'] = $reg['procesado'];
	
				$this->views->getView($this, 'actualizar', $data);
				exit;
			}
		}

		public function borrar($id)
		{
			$data = array(
				'reg' => array(
					'IdEmpleado' => 0,
					'FechaAumento' => NULL,
					'SueldoBasicoAnterior' => 0, 
					'SubsidioTransporteAnterior' => 0, 
					'SueldoBasico' => 0, 
					'SubsidioTransporte' => 0, 
					'Procesado' => 0
				),	
				'mensajeError' => ''
			);	
			
			$reg = getRegistro('AUMENTOSSALARIALES', $id);
			$IdEmpleado = $reg['idempleado'];

			if (isset($_REQUEST['Documento']))
			{
				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'actualizar', $data);
					exit;
				}
				else
				{
					$resp = $this->model->borrarAumentoSalarial($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/aumentosSalariales/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/aumentosSalariales/lista/' . $_SESSION['AUMENTOSSALARIALES']['Pagina'];

				if ($data) 
				{
					$regEmp = getRegistro('EMPLEADOS', $reg['idempleado']);
					$_REQUEST['Documento'] = $regEmp['documento'];
					$_REQUEST['NombreEmpleado'] = $regEmp['apellido1'] . ' ' . $regEmp['apellido2'] . ' ' . $regEmp['nombre1'] . ' ' . $regEmp['nombre2'];
					$regCargo = getRegistro('CARGOS', $regEmp['idcargo']);
					$_REQUEST['Cargo'] = $regCargo['nombre'];
					$regCentro = getRegistro('CENTROS', $regEmp['idcentro']);
					$_REQUEST['Centro'] = $regCentro['nombre'];

					$_REQUEST['FechaAumento'] = $reg['fechaaumento'];
					$_REQUEST['SueldoBasico'] = $reg['sueldobasico'];
					$_REQUEST['SueldoAnterior'] = $reg['sueldobasicoanterior'];
					$_REQUEST['Procesado'] = $reg['procesado'];

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
			$_SESSION['Lista'] = SERVERURL . '/aumentosSalariales/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['AUMENTOSSALARIALES']['Filtro'];

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
			
			$query .= 'ORDER BY ' . $_SESSION['AUMENTOSSALARIALES']['Orden']; 
			$data['rows'] = $this->model->listarAumentosSalariales($query);
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
							$oExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $oExcel->getSheet(0);

							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								$Documento 		= trim($oHoja->getCell('A' . $i)->getCalculatedValue());
								$NombreEmpleado = trim($oHoja->getCell('B' . $i)->getCalculatedValue());
								$FechaAumento 	= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('C' . $i)->getCalculatedValue())->format('Y-m-d');
								$SueldoBasico 	= $oHoja->getCell('D' . $i)->getCalculatedValue();

								$idEstadoActivo = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ACTIVO'");
								$reg = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento' AND EMPLEADOS.Estado=$idEstadoActivo");

								if ($reg) 
								{
									$Estado = getRegistro('PARAMETROS', $reg['estado'])['detalle'];

									// if ($Estado <> 'ACTIVO') 
									// {
									// 	$data['mensajeError'] .= 'Empleado no está activo (' . $Documento . ' - ' . $NombreEmpleado . ') <br>';
									// 	continue;
									// }

									$IdEmpleado = $reg['id'];
									$SueldoBasicoAnterior = $reg['sueldobasico'];
									$SubsidioTransporteAnterior = $reg['subsidiotransporte'];
								}
								else
								{
									$data['mensajeError'] .= 'Empleado no existe o no esta activo (' . $Documento . ' - ' . $NombreEmpleado . ') <br>';
									continue;
								}

								$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];
								$SubsidioTransporteCompleto = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'SUBSIDIO COMPLETO'");
								$NoSubsidioTransporte = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'NO RECIBE SUBSIDIO'");
			
								if ($SueldoBasico <= $SueldoMinimo * 2) 
									$SubsidioTransporte = $SubsidioTransporteCompleto;
								else
									$SubsidioTransporte = $NoSubsidioTransporte;

								$datos = array
										(
											'IdEmpleado' => $IdEmpleado,
											'FechaAumento' => $FechaAumento,
											'SueldoBasicoAnterior' => $SueldoBasicoAnterior, 
											'SubsidioTransporteAnterior' => $SubsidioTransporteAnterior, 
											'SueldoBasico' => $SueldoBasico, 
											'SubsidioTransporte' => $SubsidioTransporte, 
											'Procesado' => 0
										);
					
								$this->model->guardarAumentoSalarial($datos);
							}

							if (! empty($data['mensajeError'])) 
								$this->views->getView($this, 'importar', $data);
							else
								header('Location: ' . SERVERURL . '/aumentosSalariales/lista/1');
							
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/aumentosSalariales/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/aumentosSalariales/lista/' . $_SESSION['AUMENTOSSALARIALES']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>
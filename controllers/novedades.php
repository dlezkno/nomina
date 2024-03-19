<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Novedades extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/novedades/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR OR $_SESSION['Login']['Perfil'] == RRHH OR $_SESSION['Login']['Perfil'] == RRHH_AUX)
				$_SESSION['Importar'] = SERVERURL . '/novedades/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = SERVERURL . '/novedades/exportar';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/novedades/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['NOVEDADES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['NOVEDADES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['NOVEDADES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['NOVEDADES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['NOVEDADES']['Filtro']))
			{
				$_SESSION['NOVEDADES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['NOVEDADES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['NOVEDADES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['NOVEDADES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['NOVEDADES']['Orden'])) 
					$_SESSION['NOVEDADES']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUXILIARES.Nombre';

			$Referencia 			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodicidad 		= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo 				= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo 					= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];

			$FechaLimiteNovedades 	= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];

			$regPeriodo 			= getRegistro('PERIODOS', $IdPeriodo);
			$Periodo 				= $regPeriodo['periodo'];
			$FechaInicialPeriodo 	= $regPeriodo['fechainicial'];
			$FechaFinalPeriodo 		= $regPeriodo['fechafinal'];

			$regPeriodicidad 		= getRegistro('PARAMETROS', $IdPeriodicidad);
			$cPeriodicidad 			= substr($regPeriodicidad['detalle'], 0, 1);

			$CicloAcumulado			= getRegistro('PERIODOSACUMULADOS', 0, "PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo AND PERIODOSACUMULADOS.Ciclo = $Ciclo")['acumulado'];

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			if ($FechaLimiteNovedades < date('Y-m-d') OR $CicloAcumulado == 1) 
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['Importar'] = '';
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
						$query .= ' AND (';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
					$query .= "OR UPPER(REPLACE(MAYORES.Mayor + AUXILIARES.Auxiliar, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}

				$query .= ') ';
			}

			$query = "WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND $ArchivoNomina.Ciclo = $Ciclo AND $ArchivoNomina.Liquida IN ('N', 'T') $query";

			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
			{
				$query .= 'ORDER BY ' . $_SESSION['NOVEDADES']['Orden']; 
				$data['rows'] = $this->model->listarNovedades($ArchivoNomina, $query);

				$Archivo = 'descargas/' . $_SESSION['Login']['Usuario'] . '_Novedades_' . $regPeriodo['periodo'] . '_' . $Ciclo . '_' . date('YmdGis') . '.csv';

				$output = fopen($Archivo, 'w');

				fputcsv($output, array('DOCUMENTO', 'NOMBRE EMPLEADO', 'CONCEPTO', 'DESCRIPCION', 'HORAS', 'VALOR DB.', 'VALOR CR.', 'TERCERO'), ';');

				for ($i = 0; $i < count($data['rows']); $i++) 
				{ 
					$reg = $data['rows'][$i];

					foreach ($reg as $key => $value) 
					{
						if ($key == 'Imputacion' OR 
							$key == 'horas' OR
							$key == 'valor')
							continue;

						$reg[$key] = utf8_decode($value);
					}

					$regDatos = array($reg['Documento'], $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'], $reg['Mayor'] . $reg['Auxiliar'], $reg['NombreConcepto'], number_format($reg['horas'], 2, '.', ''), ($reg['Imputacion'] == 'PAGO' ? number_format($reg['valor'], 2, '.', '') : 0), ($reg['Imputacion'] == 'DEDUCCIÓN' ? number_format($reg['valor'], 2, '.', '') : 0), $reg['DocumentoTercero'], $reg['NombreTercero'], );

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
				$data['registros'] = $this->model->contarRegistros($ArchivoNomina, $query);
				$lineas = LINES;
				$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
				$query .= 'ORDER BY ' . $_SESSION['NOVEDADES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
				$data['rows'] = $this->model->listarNovedades($ArchivoNomina, $query);
				$data['Periodo'] = $regPeriodo['periodo'];
				$data['Ciclo'] = $Ciclo;
				$data['FechaInicial'] = $regPeriodo['fechainicial'];
				$data['FechaFinal'] = $regPeriodo['fechafinal'];
				$this->views->getView($this, 'novedades', $data);
			}
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/novedades/actualizarNovedad';
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
			$_SESSION['Lista'] = SERVERURL . '/novedades/lista/' . $_SESSION['NOVEDADES']['Pagina'];

			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];
			$FechaLimiteNovedades = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			if (date('Y-m-d') > $FechaLimiteNovedades) 
			{
				$data['mensajeError'] = 'Fecha límite para reportar novedades ha expirado.';
				$this->views->getView($this, 'adicionar', $data);
				exit;
			}

			$data = array(
				'reg' => array(
					'IdPeriodo' 	=> $regPeriodo['id'],
					'Ciclo' 		=> $Ciclo,
					'IdEmpleado' 	=> 0,
					'IdCentro' 		=> 0,
					'TipoEmpleado' 	=> 0,
					'IdConcepto' 	=> 0,
					'Base' 			=> 0, 
					'Porcentaje' 	=> 0, 
					'Horas' 		=> isset($_REQUEST['Horas']) ? $_REQUEST['Horas'] : 0,
					'Valor' 		=> isset($_REQUEST['Valor']) ? $_REQUEST['Valor'] : 0, 
					'IdTercero' 	=> 0, 
					'FechaInicial' 	=> isset($_REQUEST['FechaInicial']) ? $_REQUEST['FechaInicial'] : NULL,
					'FechaFinal' 	=> isset($_REQUEST['FechaFinal']) ? $_REQUEST['FechaFinal'] : NULL
				),	
				'mensajeError' => ''
			);	

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$regEmp = buscarRegistro('EMPLEADOS', "EMPLEADOS.Documento = '" . $_REQUEST['Documento'] . "'");
					$regCentro = getRegistro('CENTROS', $regEmp['idcentro']);

					if ($regEmp) 
					{
						$data['reg']['IdEmpleado'] = $regEmp['id'];
						$data['reg']['IdCentro'] = $regEmp['idcentro'];
						$data['reg']['TipoEmpleado'] = $regCentro['tipoempleado'];
						$IdEmpleado = $regEmp['id'];

						// SE REVISAN SI HAY AUMENTOS SALARIALES Y SI LA INCAPACIDAD ESTA COBIJADA POR EL AUMENTO
						$query = <<<EOD
							SELECT AUMENTOSSALARIALES.FechaAumento, 
									AUMENTOSSALARIALES.SueldoBasico, 
									AUMENTOSSALARIALES.SueldoBasicoAnterior 
								FROM AUMENTOSSALARIALES 
								WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
									AUMENTOSSALARIALES.Procesado = 0;
						EOD;
	
						$regAumento = $this->model->leerRegistro($query);
	
						if ($regAumento) $regEmp['sueldobasico'] = $regAumento['SueldoBasico'];
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
				}
				
				if	( empty($_REQUEST['Concepto']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Concepto') . '</strong><br>';
				else
				{
					$regMay = buscarRegistro('MAYORES', "MAYORES.Mayor = '" . substr($_REQUEST['Concepto'], 0, 2) . "'");

					if ($regMay) 
					{
						$IdMayor = $regMay['id'];
						$Auxiliar = substr($_REQUEST['Concepto'], 2, 3);

						$query = <<<EOD
							SELECT AUXILIARES.* 
								FROM AUXILIARES 
								WHERE AUXILIARES.IdMayor = $IdMayor AND 
									AUXILIARES.Auxiliar = '$Auxiliar' AND 
									AUXILIARES.Borrado = 0;
						EOD;

						$regAux = $this->model->leer($query);

						if ($regAux) 
						{
							// $regPar = getRegistro('PARAMETROS', $regAux['modoliquidacion']);

							// if ($regPar['detalle'] == 'AUTOMÁTICO')
							// 	$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('liquida automáticamente, no se puede reportar como novedad') . '<br>';
							// else
							// {
								$data['reg']['IdConcepto'] = $regAux['id'];
								$IdConcepto = $regAux['id'];
							// }
						}
						else
							$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
				}

				if	( ! empty($_REQUEST['Tercero']) )
				{
					$regTercero = buscarRegistro('TERCEROS', "TERCEROS.Documento = '" . $_REQUEST['Tercero'] . "'");

					if ($regTercero) 
					{
						$data['reg']['IdTercero'] = $regTercero['id'];
						$IdTercero = $regTercero['id'];
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Tercero') . '</strong> ' . label('no existe') . '<br>';
				}

				if	( ! $data['mensajeError'] )
				{
					$query = <<<EOD
							$ArchivoNomina.IdPeriodo = $IdPeriodo AND
							$ArchivoNomina.Ciclo = $Ciclo AND
							$ArchivoNomina.IdEmpleado = $IdEmpleado AND
							$ArchivoNomina.IdConcepto = $IdConcepto;
					EOD;

					$regNom = buscarRegistro($ArchivoNomina, $query);

					if ($regNom) 
						$data['mensajeError'] .= '<strong>' . label('Novedad') . '</strong> ' . label('ya existe') . '<br>';
				
					if (empty($data['reg']['FechaInicial']) AND empty($data['reg']['FechaFinal'])) 
					{
						$data['reg']['FechaInicial'] = '';
						$data['reg']['FechaFinal'] = '';
						if	( $data['reg']['Horas'] <= 0 AND $data['reg']['Valor'] <= 0 )
							$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Horas o Valor') . '</strong><br>';
						else
						{
							$regPar = getRegistro('PARAMETROS', $regMay['tipoliquidacion']);

							if ($regPar['detalle'] == 'DÍAS')
								$Horas = $data['reg']['Horas'] * 8;
							else
								$Horas = $data['reg']['Horas'];

							if ($data['reg']['Horas'] > 0) 
							{
								$data['reg']['Base'] 		= $regEmp['sueldobasico'];
								$data['reg']['Porcentaje'] 	= $regAux['factorconversion'];
								$data['reg']['Horas'] 		= $Horas;
								$data['reg']['Valor'] 		= round($regEmp['sueldobasico'] / $regEmp['horasmes'] * $Horas * $regAux['factorconversion'], 0);
							}
						}
					}
					else
					{
						if (empty($data['reg']['FechaInicial'])) 
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha inicial') . '</strong><br>';

						if (empty($data['reg']['FechaFinal'])) 
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha final') . '</strong><br>';

						if ($data['reg']['FechaInicial'] > $data['reg']['FechaFinal']) 
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha final') . '</strong> ' . label('mayor o igual a la') . ' <strong>' . label('Fecha inicial') . '</strong><br>';
						else
						{
							$FechaFinal 	= new DateTime($data['reg']['FechaFinal']);
							$FechaInicial 	= new DateTime($data['reg']['FechaInicial']);

							$dias = $FechaFinal->diff($FechaInicial)->days + 1;
							$Horas = $dias * 8;

							if ($Horas > 0) 
							{
								$data['reg']['Base'] 		= $regEmp['sueldobasico'];
								$data['reg']['Porcentaje']	= $regAux['factorconversion'];
								$data['reg']['Horas'] 		= $Horas;
								$data['reg']['Valor'] 		= round($regEmp['sueldobasico'] / $regEmp['horasmes'] * $Horas * $regAux['factorconversion'], 0);
							}
						}
					}

					$Ciclo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];
					if ($Ciclo == 98 OR $Ciclo == 99) $data['reg']['Liquida'] = 'T';
					else $data['reg']['Liquida'] = 'N';
				}

				if	( $data['mensajeError'] )
				{
					// LISTA DE NOVEDADES DEL EMPLEADO
					if (! empty($IdPeriodo) AND ! empty($Ciclo) AND ! empty($IdEmpleado)) 
					{
						$query = <<<EOD
							WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
								$ArchivoNomina.Ciclo = $Ciclo AND 
								$ArchivoNomina.IdEmpleado = $IdEmpleado 
							ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, AUXILIARES.Nombre;
						EOD;
				
						$data['Nov'] = $this->model->listarNovedades($ArchivoNomina, $query);
					}
					else
						$data['Nov'] = array();

					$this->views->getView($this, 'adicionar', $data);
					exit;
				}
				else
				{

					if(empty($data['reg']['IdTercero'])){
						$tercero = getRegistro('AUXILIARES', $data['reg']['IdConcepto'])['idtercero'];
						$data['reg']['IdTercero'] = $tercero == null ? 0 : $tercero;
					}

					$id = $this->model->guardarNovedad($ArchivoNomina, $data['reg']);

					if ($id) 
					{
						// LISTA DE NOVEDADES DEL EMPLEADO
						if (! empty($IdPeriodo) AND ! empty($Ciclo) AND ! empty($IdEmpleado)) 
						{
							$query = <<<EOD
								WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
									$ArchivoNomina.Ciclo = $Ciclo AND 
									$ArchivoNomina.IdEmpleado = $IdEmpleado 
								ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, AUXILIARES.Nombre;
							EOD;
					
							$data['Nov'] = $this->model->listarNovedades($ArchivoNomina, $query);
						}
						else
							$data['Nov'] = array();

						$_REQUEST['NombreEmpleado'] = '';
						$_REQUEST['Cargo'] 			= '';
						$_REQUEST['Centro'] 		= '';
						$_REQUEST['Concepto'] 		= '';
						$_REQUEST['NombreConcepto'] = '';
						$data['reg']['Base'] 		= 0;
						$data['reg']['Porcentaje'] 	= 0;
						$data['reg']['Horas'] 		= 0;
						$data['reg']['Valor'] 		= 0;
						$data['reg']['Tercero'] 	= 0;
	
						$this->views->getView($this, 'adicionar', $data);
						exit;
					}
				}
			}
			else
			{
				// LISTA DE NOVEDADES DEL EMPLEADO
				if (! empty($IdPeriodo) AND ! empty($Ciclo) AND ! empty($IdEmpleado)) 
				{
					$query = <<<EOD
						WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
							$ArchivoNomina.Ciclo = $Ciclo AND 
							$ArchivoNomina.IdEmpleado = $IdEmpleado 
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, AUXILIARES.Nombre;
					EOD;
			
					$data['Nov'] = $this->model->listarNovedades($ArchivoNomina, $query);
				}
				else
					$data['Nov'] = array();

				$this->views->getView($this, 'adicionar', $data);
				exit;
			}
		}

		public function editar($id)
		{
			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];
			$FechaLimiteNovedades = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			if (date('Y-m-d') > $FechaLimiteNovedades) 
			{
				$data['mensajeError'] = 'Fecha límite para reportar novedades ha expirado.';
				$this->views->getView($this, 'adicionar', $data);
				exit;
			}

			$query = <<<EOD
				SELECT *
				FROM $ArchivoNomina
				WHERE $ArchivoNomina.Id = $id;
			EOD;
			
			$regNovedad = $this->model->leer($query);

			$data = array(
				'reg' => array(
					'IdPeriodo' 		=> $IdPeriodo,
					'Ciclo' 			=> $Ciclo,
					'IdEmpleado' 		=> $regNovedad['idempleado'],
					'IdCentro' 			=> $regNovedad['idcentro'],
					'TipoEmpleado' 		=> $regNovedad['tipoempleado'],
					'IdConcepto' 		=> $regNovedad['idconcepto'],
					'Base' 				=> $regNovedad['base'],
					'Porcentaje'		=> $regNovedad['porcentaje'], 
					'Horas' 			=> isset($_REQUEST['Horas']) ? $_REQUEST['Horas'] : $regNovedad['horas'],
					'Valor' 			=> isset($_REQUEST['Valor']) ? $_REQUEST['Valor'] : $regNovedad['valor'], 
					'IdTercero' 		=> $regNovedad['idtercero'], 
					'FechaInicial' 		=> isset($_REQUEST['FechaInicial']) ? $_REQUEST['FechaInicial'] : $regNovedad['fechainicial'],
					'FechaFinal' 		=> isset($_REQUEST['FechaFinal']) ? $_REQUEST['FechaFinal'] : $regNovedad['fechafinal'], 
					'Liquida'			=> $regNovedad['liquida']
				),	
				'mensajeError' => ''
			);	

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$regEmp = buscarRegistro('EMPLEADOS', "EMPLEADOS.Documento = '" . $_REQUEST['Documento'] . "'");
					$regCentro = getRegistro('CENTROS', $regEmp['idcentro']);

					if ($regEmp) 
					{
						$data['reg']['IdEmpleado'] = $regEmp['id'];
						$data['reg']['IdCentro'] = $regEmp['idcentro'];
						$data['reg']['TipoEmpleado'] = $regCentro['tipoempleado'];
						$IdEmpleado = $regEmp['id'];

						// SE REVISAN SI HAY AUMENTOS SALARIALES Y SI LA INCAPACIDAD ESTA COBIJADA POR EL AUMENTO
						$query = <<<EOD
							SELECT AUMENTOSSALARIALES.FechaAumento, 
									AUMENTOSSALARIALES.SueldoBasico, 
									AUMENTOSSALARIALES.SueldoBasicoAnterior 
								FROM AUMENTOSSALARIALES 
								WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
									AUMENTOSSALARIALES.Procesado = 0;
						EOD;
	
						$regAumento = $this->model->leerRegistro($query);
	
						if ($regAumento) $regEmp['sueldobasico'] = $regAumento['SueldoBasico'];
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
				}
				
				if	( empty($_REQUEST['Concepto']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Concepto') . '</strong><br>';
				else
				{
					$regMay = buscarRegistro('MAYORES', "MAYORES.Mayor = '" . substr($_REQUEST['Concepto'], 0, 2) . "'");

					if ($regMay) 
					{
						$IdMayor = $regMay['id'];
						$Auxiliar = substr($_REQUEST['Concepto'], 2, 3);

						$query = <<<EOD
							SELECT AUXILIARES.* 
								FROM AUXILIARES 
								WHERE AUXILIARES.IdMayor = $IdMayor AND 
									AUXILIARES.Auxiliar = '$Auxiliar' AND 
									AUXILIARES.Borrado = 0;
						EOD;

						$regAux = $this->model->leer($query);

						if ($regAux) 
						{
							$regPar = getRegistro('PARAMETROS', $regAux['modoliquidacion']);

							if ($regPar['detalle'] == 'AUTOMÁTICO')
								$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('liquida automáticamente, no se puede reportar como novedad') . '<br>';
							else
							{
								$data['reg']['IdConcepto'] = $regAux['id'];
								$IdConcepto = $regAux['id'];
							}
						}
						else
							$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
				}

				if	( ! empty($_REQUEST['Tercero']) )
				{
					$regTercero = buscarRegistro('TERCEROS', "TERCEROS.Documento = '" . $_REQUEST['Tercero'] . "'");

					if ($regTercero) 
					{
						$data['reg']['IdTercero'] = $regTercero['id'];
						$IdTercero = $regTercero['id'];
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Tercero') . '</strong> ' . label('no existe') . '<br>';
				}

				if	( ! $data['mensajeError'] )
				{
					$query = <<<EOD
							$ArchivoNomina.IdPeriodo = $IdPeriodo AND
							$ArchivoNomina.Ciclo = $Ciclo AND
							$ArchivoNomina.IdEmpleado = $IdEmpleado AND
							$ArchivoNomina.IdConcepto = $IdConcepto AND 
							$ArchivoNomina.Id <> $id;
					EOD;

					$regNom = buscarRegistro($ArchivoNomina, $query);

					if ($regNom) 
						$data['mensajeError'] .= '<strong>' . label('Novedad') . '</strong> ' . label('ya existe') . '<br>';
				
					if (empty($data['reg']['FechaInicial']) AND empty($data['reg']['FechaFinal'])) 
					{
						if	( $data['reg']['Horas'] <= 0 AND $data['reg']['Valor'] <= 0 )
							$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Horas o Valor') . '</strong><br>';
						else
						{
							if ($data['reg']['Horas'] > 0 AND $data['reg']['Valor'] == 0) 
								$data['reg']['Valor'] = round($regEmp['sueldobasico'] / $regEmp['horasmes'] * $data['reg']['Horas'] * $regAux['factorconversion'], 0);
						}
					}
					else
					{
						if (empty($data['reg']['FechaInicial'])) 
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha inicial') . '</strong><br>';

						if (empty($data['reg']['FechaFinal'])) 
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha final') . '</strong><br>';

						if ($data['reg']['FechaInicial'] > $data['reg']['FechaFinal']) 
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha final') . '</strong> ' . label('mayor o igual a la') . ' <strong>' . label('Fecha inicial') . '</strong><br>';
					}
				}

				if	( $data['mensajeError'] )
				{
					// LISTA DE NOVEDADES DEL EMPLEADO
					if (! empty($IdPeriodo) AND ! empty($Ciclo) AND ! empty($IdEmpleado)) 
					{
						$query = <<<EOD
							WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
								$ArchivoNomina.Ciclo = $Ciclo AND 
								$ArchivoNomina.IdEmpleado = $IdEmpleado 
							ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, AUXILIARES.Nombre;
						EOD;
				
						$data['Nov'] = $this->model->listarNovedades($ArchivoNomina, $query);
					}
					else
						$data['Nov'] = array();

					$this->views->getView($this, 'adicionar', $data);
					exit;
				}
				else
				{
					if (empty($data['reg']['FechaInicial']))
						$data['reg']['FechaInicial'] = NULL;

					if (empty($data['reg']['FechaFinal']))
						$data['reg']['FechaFinal'] = NULL;

					$resp = $this->model->actualizarNovedad($ArchivoNomina, $data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/novedades/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/novedades/lista/' . $_SESSION['NOVEDADES']['Pagina'];

				$regEmp = getRegistro('EMPLEADOS', $regNovedad['idempleado']);
				$_REQUEST['Documento'] = $regEmp['documento'];
				$_REQUEST['NombreEmpleado'] = $regEmp['apellido1'] . ' ' . $regEmp['apellido2'] . ' ' . $regEmp['nombre1'] . ' ' . $regEmp['nombre2'];
				$regCargo = getRegistro('CARGOS', $regEmp['idcargo']);
				$_REQUEST['Cargo'] = $regCargo['nombre'];
				$regCentro = getRegistro('CENTROS', $regEmp['idcentro']);
				$_REQUEST['Centro'] = $regCentro['nombre'];

				$regAux = getRegistro('AUXILIARES', $regNovedad['idconcepto']);
				$regMay = getRegistro('MAYORES', $regAux['idmayor']);
				$_REQUEST['Concepto'] = $regMay['mayor'] . $regAux['auxiliar'];
				$_REQUEST['NombreConcepto'] = $regAux['nombre'];
				
				if ($regNovedad['idtercero'] > 0)
				{
					$regTercero = getRegistro('TERCEROS', $regNovedad['idtercero']);
					$_REQUEST['Tercero'] = $regTercero['documento'];
					$_REQUEST['NombreTercero'] = $regTercero['nombre'];
				}
				else
				{
					$_REQUEST['Tercero'] = '';
					$_REQUEST['NombreTercero'] = '';
				}
	
				// LISTA DE NOVEDADES DEL EMPLEADO
				if (! empty($IdPeriodo) AND ! empty($Ciclo) AND ! empty($IdEmpleado)) 
				{
					$query = <<<EOD
						WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
							$ArchivoNomina.Ciclo = $Ciclo AND 
							$ArchivoNomina.IdEmpleado = $IdEmpleado 
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, AUXILIARES.Nombre;
					EOD;
			
					$data['Nov'] = $this->model->listarNovedades($ArchivoNomina, $query);
				}
				else
					$data['Nov'] = array();

				$this->views->getView($this, 'actualizar', $data);
				exit;
			}
		}

		public function borrar($id)
		{
			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];
			$FechaLimiteNovedades = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			if (date('Y-m-d') > $FechaLimiteNovedades) 
			{
				$data['mensajeError'] = 'Fecha límite para reportar novedades ha expirado.';
				$this->views->getView($this, 'adicionar', $data);
				exit;
			}

			$query = <<<EOD
				SELECT *
				FROM $ArchivoNomina
				WHERE $ArchivoNomina.Id = $id;
			EOD;
			
			$regNovedad = $this->model->leer($query);

			$data = array(
				'reg' => array(
					'IdPeriodo' 	=> $IdPeriodo,
					'Ciclo' 		=> $Ciclo,
					'IdEmpleado' 	=> $regNovedad['idempleado'],
					'IdCentro' 		=> $regNovedad['idcentro'],
					'TipoEmpleado' 	=> $regNovedad['tipoempleado'],
					'IdConcepto' 	=> $regNovedad['idconcepto'],
					'Base' 			=> $regNovedad['base'],
					'Horas' 		=> isset($_REQUEST['Horas']) ? $_REQUEST['Horas'] : $regNovedad['horas'],
					'Valor' 		=> isset($_REQUEST['Valor']) ? $_REQUEST['Valor'] : $regNovedad['valor'], 
					'IdTercero' 	=> $regNovedad['idtercero'], 
					'FechaInicial' 	=> isset($_REQUEST['FechaInicial']) ? $_REQUEST['FechaInicial'] : $regNovedad['fechainicial'],
					'FechaFinal' 	=> isset($_REQUEST['FechaFinal']) ? $_REQUEST['FechaFinal'] : $regNovedad['fechafinal']
				),	
				'mensajeError' => ''
			);	

			if (isset($_REQUEST['Documento']))
			{
				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'actualizar', $data);
					exit;
				}
				else
				{
					$resp = $this->model->borrarNovedad($ArchivoNomina, $id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/novedades/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/novedades/lista/' . $_SESSION['NOVEDADES']['Pagina'];

				$regEmp = getRegistro('EMPLEADOS', $regNovedad['idempleado']);
				$_REQUEST['Documento'] = $regEmp['documento'];
				$_REQUEST['NombreEmpleado'] = $regEmp['apellido1'] . ' ' . $regEmp['apellido2'] . ' ' . $regEmp['nombre1'] . ' ' . $regEmp['nombre2'];
				$regCargo = getRegistro('CARGOS', $regEmp['idcargo']);
				$_REQUEST['Cargo'] = $regCargo['nombre'];
				$regCentro = getRegistro('CENTROS', $regEmp['idcentro']);
				$_REQUEST['Centro'] = $regCentro['nombre'];

				$regAux = getRegistro('AUXILIARES', $regNovedad['idconcepto']);
				$regMay = getRegistro('MAYORES', $regAux['idmayor']);
				$_REQUEST['Concepto'] = $regMay['mayor'] . $regAux['auxiliar'];
				$_REQUEST['NombreConcepto'] = $regAux['nombre'];
				
				if ($regNovedad['idtercero'] > 0)
				{
					$regTercero = getRegistro('TERCEROS', $regNovedad['idtercero']);
					$_REQUEST['Tercero'] = $regTercero['documento'];
					$_REQUEST['NombreTercero'] = $regTercero['nombre'];
				}
				else
				{
					$_REQUEST['Tercero'] = '';
					$_REQUEST['NombreTercero'] = '';
				}
	
				// LISTA DE NOVEDADES DEL EMPLEADO
				if (! empty($IdPeriodo) AND ! empty($Ciclo) AND ! empty($IdEmpleado)) 
				{
					$query = <<<EOD
						WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
							$ArchivoNomina.Ciclo = $Ciclo AND 
							$ArchivoNomina.IdEmpleado = $IdEmpleado 
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, AUXILIARES.Nombre;
					EOD;
			
					$data['Nov'] = $this->model->listarNovedades($ArchivoNomina, $query);
				}
				else
					$data['Nov'] = array();

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
			$_SESSION['Lista'] = SERVERURL . '/mayores/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['NOVEDADES']['Filtro'];

			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];
			$FechaLimiteNovedades = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			if (date('Y-m-d') > $FechaLimiteNovedades) 
			{
				$data['mensajeError'] = 'Fecha límite para reportar novedades ha expirado.';
				$this->views->getView($this, 'adicionar', $data);
				exit;
			}

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= ' AND (';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}

				$query .= ') ';
			}
			
			$query .= 'ORDER BY ' . $_SESSION['NOVEDADES']['Orden']; 
			$data['rows'] = $this->model->listarNovedades($ArchivoNomina, $query);
			$this->views->getView($this, 'informe', $data);
		}

		public function importar()
		{
			$data = array();
			$data['mensajeError'] = '';

			$Referencia 			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodicidad 		= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo 				= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo 					= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];
			$FechaLimiteNovedades 	= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			if (date('Y-m-d') > $FechaLimiteNovedades) 
				$data['mensajeError'] = 'Fecha límite para reportar novedades ha expirado.';

			if (empty($data['mensajeError']))
			{
				$regPeriodoAcumulado = getRegistro('PERIODOSACUMULADOS', 0, "PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo AND PERIODOSACUMULADOS.Ciclo = $Ciclo");

				if ($regPeriodoAcumulado)
				{
					if ($regPeriodoAcumulado['acumulado'] == 1)
						$data['mensajeError'] .= "Período - Ciclo ya está acumulado ($Periodo - $Ciclo) <br>";
				}
				else
					$data['mensajeError'] .= "Período - Ciclo no se ha hecho apertura ($Periodo - $Ciclo) <br>";
			}

			if (empty($data['mensajeError']))
			{
				if (isset($_FILES) AND count($_FILES) > 0) 
				{
					if	( empty($_FILES['archivo']['name']) )
						$data['mensajeError'] .= "Seleccione un <strong>Archivo en Excel</strong><br>";
					else
					{
						ini_set('max_execution_time', 6000);

						$archivo = $_FILES['archivo']['name'];
			
						if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
						{
							if ( file_exists ($archivo) )
							{
								// $oExcel = IOFactory::load($archivo);
								$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
								$oHoja = $Excel->getSheet(0);

								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if (! empty($oHoja->getCell('A' . $i)->getCalculatedValue()))
									{
										$Ciclo = trim($oHoja->getCell('A' . $i)->getCalculatedValue());

										switch ($Ciclo)
										{
											case 'NOMINA':
												$Ciclo = 1;
												$Liquida = 'N';
												break;
											case 'AJUSTE':
												$Ciclo = 2;
												$Liquida = 'N';
												break;
											case 'LIQUIDACION':
												$Ciclo = 98;
												$Liquida = 'T';
												break;
											case 'RELIQUIDACION':
												$Ciclo = 99;
												$Liquida = 'T';
												break;
											case '96':
											case '97':
												$Liquida = 'T';
												break;
											default:
												$Liquida = 'N';
												break;
										}

										$Documento 			= trim($oHoja->getCell('B' . $i)->getCalculatedValue());
										$Concepto 			= trim($oHoja->getCell('C' . $i)->getCalculatedValue());
										$Detalle 			= trim($oHoja->getCell('D' . $i)->getCalculatedValue());
										$Horas 				= $oHoja->getCell('E' . $i)->getCalculatedValue();
										$Minutos 			= $oHoja->getCell('F' . $i)->getCalculatedValue();
										$Valor 				= $oHoja->getCell('G' . $i)->getCalculatedValue();
										$Tercero 			= trim($oHoja->getCell('H' . $i)->getCalculatedValue());
										$NombreTercero 		= trim($oHoja->getCell('I' . $i)->getCalculatedValue());
										$FechaInicial 		= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('J' . $i)->getCalculatedValue())->format('Y-m-d');
										$FechaInicial 		= ($FechaInicial == '1970-01-01' ? NULL : $FechaInicial);
										$FechaFinal 		= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('K' . $i)->getCalculatedValue())->format('Y-m-d');
										$FechaFinal 		= ($FechaFinal == '1970-01-01' ? NULL : $FechaFinal);

										if ($Ciclo == 98 OR $Ciclo == 99)
											$Estado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'RETIRADO'");
										else
											$Estado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ACTIVO'");

										//$regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento' AND EMPLEADOS.Estado = $Estado");

										$regEmpleado = $this->model->listarEmpleados("SELECT * FROM EMPLEADOS WHERE EMPLEADOS.Documento = '$Documento' AND EMPLEADOS.Estado = $Estado ORDER BY fecharetiro;");

										if (count($regEmpleado) > 0)
										{

											$regEmpleado = $regEmpleado[count($regEmpleado)-1];

											$regCentro = getRegistro('CENTROS', $regEmpleado['idcentro']);

											if ($regEmpleado['fechaingreso'] > $FechaFinalPeriodo) 
											{
												$data['mensajeError'] .= "Empleado con fecha de ingreso posterior al período a liquidar ($Documento) <br>";
												continue;
											}

											$IdEmpleado 	= $regEmpleado['id'];
											$IdCentro 		= $regEmpleado['idcentro'];
											$TipoEmpleado 	= $regCentro['tipoempleado'];
											$SueldoBasico 	= $regEmpleado['sueldobasico'];
											$HorasMes 		= getHoursMonth();

											// SE REVISAN SI HAY AUMENTOS SALARIALES Y SI LA INCAPACIDAD ESTA COBIJADA POR EL AUMENTO
											$query = <<<EOD
												SELECT AUMENTOSSALARIALES.FechaAumento, 
														AUMENTOSSALARIALES.SueldoBasico, 
														AUMENTOSSALARIALES.SueldoBasicoAnterior 
													FROM AUMENTOSSALARIALES 
													WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
														AUMENTOSSALARIALES.Procesado = 0;
											EOD;

											$regAumento = $this->model->leerRegistro($query);

											if ($regAumento) $SueldoBasico = $regAumento['SueldoBasico'];
										}
										else
										{
											$data['mensajeError'] .= "Empleado no existe ($Documento) <br>";
											continue;
										}

										$Concepto	= str_pad($Concepto, 5, '0', STR_PAD_LEFT);
										$Mayor = substr($Concepto, 0, 2);
										$Auxiliar = substr($Concepto, 2, 3);

										$query = <<<EOD
											SELECT AUXILIARES.*, 
												MAYORES.TipoLiquidacion, 
												PARAMETROS1.Detalle AS NombreTipoLiquidacion, 
												PARAMETROS2.Detalle AS NombreModoLiquidacion
											FROM AUXILIARES
												INNER JOIN MAYORES 
													ON AUXILIARES.IdMayor = MAYORES.Id 
												INNER JOIN PARAMETROS AS PARAMETROS1 
													ON MAYORES.TipoLiquidacion = PARAMETROS1.Id 
												INNER JOIN PARAMETROS AS PARAMETROS2 
													ON AUXILIARES.ModoLiquidacion = PARAMETROS2.Id 
											WHERE AUXILIARES.Auxiliar = '$Auxiliar' AND 
												MAYORES.Mayor = '$Mayor' AND 
												AUXILIARES.Borrado = 0; 
										EOD;

										$regConcepto = $this->model->leer($query);

										if ($regConcepto)
										{
											// if ($regConcepto['NombreModoLiquidacion'] == 'AUTOMÁTICO') 
											// {
											// 	$data['mensajeError'] .= "Concepto liquida automáticamente, no se pude reportar como novedad ( $Documento - $Concepto) <br>";
											// 	continue;
											// }
											// else
											// {
												$IdConcepto 			= $regConcepto['id'];
												$FactorConversion 		= $regConcepto['factorconversion'];
												$NombreTipoLiquidacion 	= $regConcepto['NombreTipoLiquidacion'];
											// }
										}
										else
										{
											$data['mensajeError'] .= "Concepto no existe ($Concepto) <br>";
											continue;
										}

										if (is_null($Horas))
											$Horas = 0;

										if (is_null($Minutos))
											$Minutos = 0;
										elseif ($Minutos < 0 OR $Minutos > 59)
										{
											$data['mensajeError'] .= "Minutos deben ser entre 0 y 59 ($Documento - $Concepto') <br>";
											continue;
										}

										if ($NombreTipoLiquidacion == 'HORAS' AND $Valor == 0) 
										
											$Valor = round($SueldoBasico / $HorasMes * ($Horas + round($Minutos / 60, 2)) * $FactorConversion, 0);
										elseif ($NombreTipoLiquidacion == 'DÍAS') 
										{
											$Horas = ($Horas * 8) + round($Minutos / 60, 2);

											if ($Valor == 0) 
												$Valor = round($SueldoBasico / $HorasMes * $Horas * $FactorConversion, 0);
										}
										elseif (is_null($Valor) OR $Valor <= 0)
										{
											$data['mensajeError'] .= "Valor debe ser mayor que cero ($Documento - $Concepto)<br>";
											continue;
										}

										if ($Tercero <> 0){
											$IdTercero = getId('TERCEROS', "TERCEROS.Documento = '$Tercero'");
										}
										else{											
											$terce = getRegistro('AUXILIARES',$IdConcepto)['idtercero'];
											$IdTercero = $terce == null ? 0 : $terce;
										}
											
										$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdCentro, $TipoEmpleado, $IdConcepto, 0, 0, $Horas, $Valor, $IdTercero, $FechaInicial, $FechaFinal, $Liquida); 

										$query = <<<EOD
											SELECT $ArchivoNomina.*
												FROM $ArchivoNomina 
												WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
													$ArchivoNomina.Ciclo = $Ciclo AND  
													$ArchivoNomina.IdEmpleado = $IdEmpleado AND 
													$ArchivoNomina.IdConcepto = $IdConcepto AND 
													$ArchivoNomina.IdTercero = $IdTercero 
										EOD;

										$reg = $this->model->buscarNovedad($query);

										if ($reg)
											$this->model->actualizarNovedad($ArchivoNomina, $datos, $reg['id']);
										else{
											$this->model->guardarNovedad($ArchivoNomina, $datos);
										}
									}
								}

								if (! empty($data['mensajeError'])) 
									$this->views->getView($this, 'importar', $data);
								else
									header('Location: ' . SERVERURL . '/novedades/lista/1');
								
								exit;
							}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/novedades/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/novedades/lista/' . $_SESSION['NOVEDADES']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}

		public function importarAdicionar()
		{
			$data = array();
			$data['mensajeError'] = '';

			$Referencia 			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodicidad 		= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo 				= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo 					= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];
			$FechaLimiteNovedades 	= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			if (date('Y-m-d') > $FechaLimiteNovedades) 
				$data['mensajeError'] = 'Fecha límite para reportar novedades ha expirado.';

			if (! empty($data['mensajeError']))
			{
				if (isset($_FILES) AND count($_FILES) > 0) 
				{
					if	( empty($_FILES['archivo']['name']) )
						$data['mensajeError'] .= "Seleccione un <strong>Archivo en Excel</strong><br>";
					else
					{
						ini_set('max_execution_time', 6000);

						$archivo = $_FILES['archivo']['name'];
			
						if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
						{
							if ( file_exists ($archivo) )
							{
								// $oExcel = IOFactory::load($archivo);
								$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
								$oHoja = $Excel->getSheet(0);

								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									$Documento 			= $oHoja->getCell('A' . $i)->getCalculatedValue();
									$Concepto 			= $oHoja->getCell('B' . $i)->getCalculatedValue();
									$Detalle 			= $oHoja->getCell('C' . $i)->getCalculatedValue();
									$Horas 				= $oHoja->getCell('D' . $i)->getCalculatedValue();
									$Minutos 			= $oHoja->getCell('E' . $i)->getCalculatedValue();
									$Valor 				= $oHoja->getCell('F' . $i)->getCalculatedValue();
									$Tercero 			= $oHoja->getCell('G' . $i)->getCalculatedValue();
									$NombreTercero 		= $oHoja->getCell('H' . $i)->getCalculatedValue();
									$FechaInicial 		= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('I' . $i)->getCalculatedValue())->format('Y-m-d');
									$FechaInicial 		= ($FechaInicial == '1970-01-01' ? NULL : $FechaInicial);
									$FechaFinal 		= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('J' . $i)->getCalculatedValue())->format('Y-m-d');
									$FechaFinal 		= ($FechaFinal == '1970-01-01' ? NULL : $FechaFinal);

									$regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento'");

									if ($regEmpleado)
									{
										$regCentro = getRegistro('CENTROS', $regEmpleado['idcentro']);

										if ($regEmpleado['fechaingreso'] > $FechaFinalPeriodo) 
										{
											$data['mensajeError'] .= "Empleado con fecha de ingreso posterior al período a liquidar ($Documento) <br>";
											continue;
										}

										$IdEmpleado 	= $regEmpleado['id'];
										$IdCentro 		= $regEmpleado['idcentro'];
										$TipoEmpleado 	= $regCentro['tipoempleado'];
										$SueldoBasico 	= $regEmpleado['sueldobasico'];
										$HorasMes 		= $regEmpleado['horasmes'];

										// SE REVISAN SI HAY AUMENTOS SALARIALES Y SI LA INCAPACIDAD ESTA COBIJADA POR EL AUMENTO
										$query = <<<EOD
											SELECT AUMENTOSSALARIALES.FechaAumento, 
													AUMENTOSSALARIALES.SueldoBasico, 
													AUMENTOSSALARIALES.SueldoBasicoAnterior 
												FROM AUMENTOSSALARIALES 
												WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
													AUMENTOSSALARIALES.Procesado = 0;
										EOD;

										$regAumento = $this->model->leerRegistro($query);

										if ($regAumento) $SueldoBasico = $regAumento['SueldoBasico'];
									}
									else
									{
										$data['mensajeError'] .= "Empleado no existe ($Documento) <br>";
										continue;
									}

									$Mayor = substr($Concepto, 0, 2);
									$Auxiliar = substr($Concepto, 2, 3);

									$query = <<<EOD
										SELECT AUXILIARES.*, 
											MAYORES.TipoLiquidacion, 
											PARAMETROS1.Detalle AS NombreTipoLiquidacion, 
											PARAMETROS2.Detalle AS NombreModoLiquidacion
										FROM AUXILIARES
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS AS PARAMETROS1 
												ON MAYORES.TipoLiquidacion = PARAMETROS1.Id 
											INNER JOIN PARAMETROS AS PARAMETROS2 
												ON AUXILIARES.ModoLiquidacion = PARAMETROS2.Id 
										WHERE AUXILIARES.Auxiliar = '$Auxiliar' AND 
											MAYORES.Mayor = '$Mayor'AND 
											AUXILIARES.Borrado = 0; 
									EOD;

									$regConcepto = $this->model->leer($query);

									if ($regConcepto)
									{
										if ($regConcepto['NombreModoLiquidacion'] == 'AUTOMÁTICO') 
										{
											$data['mensajeError'] .= "Concepto liquida automáticamente, no se pude reportar como novedad ( $Documento - $Concepto) <br>";
											continue;
										}
										else
										{
											$IdConcepto 			= $regConcepto['id'];
											$FactorConversion 		= $regConcepto['factorconversion'];
											$NombreTipoLiquidacion 	= $regConcepto['NombreTipoLiquidacion'];
										}
									}
									else
									{
										$data['mensajeError'] .= "Concepto no existe ($Concepto) <br>";
										continue;
									}

									if ($Minutos < 0 OR $Minutos > 59)
									{
										$data['mensajeError'] .= "Minutos deben ser entre 0 y 59 ($Documento - $Concepto') <br>";
										continue;
									}

									if ($NombreTipoLiquidacion == 'HORAS' AND $Valor == 0) 
										$Valor = round($SueldoBasico / $HorasMes * ($Horas + round($Minutos / 60, 2)) * $FactorConversion, 0);
									elseif ($NombreTipoLiquidacion == 'DÍAS') 
									{
										$Horas = ($Horas * 8) + round($Minutos / 60, 2);

										if ($Valor == 0) 
											$Valor = round($SueldoBasico / $HorasMes * $Horas * $FactorConversion, 0);
									}
									// else
									// 	$Horas = 0;

									if ($Tercero <> 0){
										$IdTercero = getId('TERCEROS', "TERCEROS.Documento = '$Tercero'");
									}else{
										$terce = getRegistro('AUXILIARES',$IdConcepto)['idtercero'];
										$IdTercero = $terce == null ? 0 : $terce;
									}
									$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdCentro, $TipoEmpleado, $IdConcepto, 0, $Horas, $Valor, $IdTercero, $FechaInicial, $FechaFinal); 

									$query = <<<EOD
										SELECT $ArchivoNomina.*
											FROM $ArchivoNomina 
											WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
												$ArchivoNomina.Ciclo = $Ciclo AND  
												$ArchivoNomina.IdEmpleado = $IdEmpleado AND 
												$ArchivoNomina.IdConcepto = $IdConcepto
									EOD;

									$reg = $this->model->buscarNovedad($query);

									if ($reg) 
										$this->model->actualizarNovedadAdicionar($ArchivoNomina, $datos, $reg['Id']);
									else
										$this->model->guardarNovedad($ArchivoNomina, $datos);
								}

								if (! empty($data['mensajeError'])) 
									$this->views->getView($this, 'importarAdicionar', $data);
								else
									header('Location: ' . SERVERURL . '/novedades/lista/1');
								
								exit;
							}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/novedades/importarAdicionar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/novedades/lista/' . $_SESSION['NOVEDADES']['Pagina'];
			
				$this->views->getView($this, 'importarAdicionar', $data);
			}
		}
	}
?>

<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Comprobantes extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/comprobantes/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/comprobantes/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/comprobantes/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['COMPROBANTES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['COMPROBANTES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['COMPROBANTES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['COMPROBANTES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['COMPROBANTES']['Filtro']))
			{
				$_SESSION['COMPROBANTES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['COMPROBANTES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['COMPROBANTES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['COMPROBANTES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['COMPROBANTES']['Orden'])) 
					$_SESSION['COMPROBANTES']['Orden'] = 'TIPODOC.TipoDocumento,MAYORES.Mayor,AUXILIARES.Auxiliar,PARAMETROS1.Detalle';

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= 'WHERE ';
					else
						$query .= 'AND ';

					$query .= "(UPPER(REPLACE(TIPODOC.TipoDocumento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(MAYORES.Mayor + AUXILIARES.Auxiliar, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(COMPROBANTES.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(MAYORES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PARAMETROS1.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PARAMETROS2.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PARAMETROS3.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%') ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['COMPROBANTES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarComprobantes($query);
			$this->views->getView($this, 'comprobantes', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/comprobantes/actualizarComprobante';
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
			$_SESSION['Lista'] = SERVERURL . '/comprobantes/lista/' . $_SESSION['COMPROBANTES']['Pagina'];

			$data = array(
				'reg' => array(
					'IdTipoDoc' 		=> isset($_REQUEST['IdTipoDoc']) ? $_REQUEST['IdTipoDoc'] : 0,
					'IdConcepto' 		=> isset($_REQUEST['IdConcepto']) ? $_REQUEST['IdConcepto'] : 0,
					'TipoEmpleado' 		=> isset($_REQUEST['TipoEmpleado']) ? $_REQUEST['TipoEmpleado'] : 0,
					'Porcentaje' 		=> isset($_REQUEST['Porcentaje']) ? $_REQUEST['Porcentaje'] : 0,
					'CuentaDb' 			=> isset($_REQUEST['CuentaDb']) ? $_REQUEST['CuentaDb'] : '',
					'DetallaCentroDb' 	=> isset($_REQUEST['DetallaCentroDb']) ? 'true' : 'false',
					'CuentaCr' 			=> isset($_REQUEST['CuentaCr']) ? $_REQUEST['CuentaCr'] : '',
					'DetallaCentroCr' 	=> isset($_REQUEST['DetallaCentroCr']) ? 'true' : 'false',
					'TipoTercero' 		=> isset($_REQUEST['TipoTercero']) ? $_REQUEST['TipoTercero'] : 0
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['IdTipoDoc'])) 
			{
				if	( empty($data['reg']['IdTipoDoc']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de documento') . '</strong><br>';

				if	( empty($_REQUEST['Concepto']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Concepto') . '</strong><br>';
				else
				{
					$regMay = buscarRegistro('MAYORES', "MAYORES.Mayor = '" . substr($_REQUEST['Concepto'], 0, 2) . "'");

					if ($regMay) 
					{
						$regAux = buscarRegistro('AUXILIARES', "AUXILIARES.Auxiliar = '" . substr($_REQUEST['Concepto'], 2, 3) . "' AND AUXILIARES.IdMayor = " . $regMay['id']);

						if ($regAux) 
						{
							$data['reg']['IdConcepto'] = $regAux['id'];
							$IdConcepto = $regAux['id'];
						}
						else
							$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
				}

				// if	( empty($data['reg']['TipoEmpleado']) )
				// 	$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Tipo de empleado') . '</strong><br>';
			
				if	( $data['reg']['Porcentaje'] <= 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Porcentaje') . '</strong> ' . label('mayor que cero') . '<br>';

				if	( empty($data['reg']['CuentaDb']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuenta Db.') . '</strong><br>';
			
				if	( empty($data['reg']['CuentaCr']) )	
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuenta Cr.') . '</strong><br>';
				
				if	( empty($data['reg']['TipoTercero']))
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de tercero') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarComprobante($data['reg']);

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
			if (isset($_REQUEST['IdTipoDoc']))
			{
				$data = array(
					'reg' => array(
						'IdTipoDoc' 		=> isset($_REQUEST['IdTipoDoc']) ? $_REQUEST['IdTipoDoc'] : 0,
						'IdConcepto' 		=> isset($_REQUEST['IdConcepto']) ? $_REQUEST['IdConcepto'] : 0,
						'TipoEmpleado' 		=> isset($_REQUEST['TipoEmpleado']) ? $_REQUEST['TipoEmpleado'] : 0,
						'Porcentaje' 		=> isset($_REQUEST['Porcentaje']) ? $_REQUEST['Porcentaje'] : 0,
						'CuentaDb' 			=> isset($_REQUEST['CuentaDb']) ? $_REQUEST['CuentaDb'] : '',
						'DetallaCentroDb' 	=> isset($_REQUEST['DetallaCentroDb']) ? 'true' : 'false',
						'CuentaCr' 			=> isset($_REQUEST['CuentaCr']) ? $_REQUEST['CuentaCr'] : '',
						'DetallaCentroCr' 	=> isset($_REQUEST['DetallaCentroCr']) ? 'true' : 'false',
						'TipoTercero' 		=> isset($_REQUEST['TipoTercero']) ? $_REQUEST['TipoTercero'] : 0
					),
					'mensajeError' => ''
				);
	
				if	( empty($data['reg']['IdTipoDoc']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de documento') . '</strong><br>';

				if	( empty($_REQUEST['Concepto']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Concepto') . '</strong><br>';
				else
				{
					$regMay = buscarRegistro('MAYORES', "MAYORES.Mayor = '" . substr($_REQUEST['Concepto'], 0, 2) . "'");

					if ($regMay) 
					{
						$regAux = buscarRegistro('AUXILIARES', "AUXILIARES.Auxiliar = '" . substr($_REQUEST['Concepto'], 2, 3) . "' AND AUXILIARES.IdMayor = " . $regMay['id']);

						if ($regAux) 
						{
							$data['reg']['IdConcepto'] = $regAux['id'];
							$IdConcepto = $regAux['id'];
						}
						else
							$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
				}

				// if	( empty($data['reg']['TipoEmpleado']) )
				// 	$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Tipo de empleado') . '</strong><br>';
			
				if	( $data['reg']['Porcentaje'] <= 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Porcentaje') . '</strong> ' . label('mayor que cero') . '<br>';

				if	( empty($data['reg']['CuentaDb']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuenta Db.') . '</strong><br>';
				
				if	( empty($data['reg']['CuentaCr']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuenta Cr.') . '</strong><br>';
				
				if	( empty($data['reg']['TipoTercero']))
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de tercero') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarComprobante($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/comprobantes/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/comprobantes/lista/' . $_SESSION['COMPROBANTES']['Pagina'];

				$query = 'SELECT * FROM COMPROBANTES WHERE COMPROBANTES.Id = ' . $id;
				
				$reg = $this->model->leer($query);

				$regAux = getRegistro('AUXILIARES', $reg['idconcepto']);
				$regMay = getRegistro('MAYORES', $regAux['idmayor']);
				$_REQUEST['Concepto'] = $regMay['mayor'] . $regAux['auxiliar'];
				$_REQUEST['NombreConcepto'] = $regAux['nombre'];

				$data = array(
					'reg' => array(
						'Id' => $reg['id'],
						'IdTipoDoc' => $reg['idtipodoc'],
						'IdConcepto' => $reg['idconcepto'],
						'TipoEmpleado' => $reg['tipoempleado'],
						'Porcentaje' => $reg['porcentaje'],
						'CuentaDb' => $reg['cuentadb'],
						'CuentaCr' => $reg['cuentacr'],
						'DetallaCentroDb' => ($reg['detallacentrodb'] == 1 ? true : false),
						'DetallaCentroCr' => ($reg['detallacentrocr'] == 1 ? true : false),
						'TipoTercero' => $reg['tipotercero']),
					'mensajeError' => ''
				);

				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			$query = 'SELECT * FROM COMPROBANTES WHERE COMPROBANTES.Id = ' . $id;
				
			$reg = $this->model->leer($query);

			$regAux = getRegistro('AUXILIARES', $reg['idconcepto']);
			$regMay = getRegistro('MAYORES', $regAux['idmayor']);
			$_REQUEST['Concepto'] = $regMay['mayor'] . $regAux['auxiliar'];
			$_REQUEST['NombreConcepto'] = $regAux['nombre'];

			$data = array(
				'reg' => array(
					'Id' => $reg['id'],
					'IdTipoDoc' => $reg['idtipodoc'],
					'IdConcepto' => $reg['idconcepto'],
					'TipoEmpleado' => $reg['tipoempleado'],
					'Porcentaje' => $reg['porcentaje'],
					'CuentaDb' => $reg['cuentadb'],
					'CuentaCr' => $reg['cuentacr'],
					'DetallaCentro' => ($reg['detallacentro'] ? true : false),
					'TipoTercero' => $reg['tipotercero']), 
				'mensajeError' => ''
			);

			if (isset($_REQUEST['IdTipoDoc']))
			{
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarComprobante($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/comprobantes/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/comprobantes/lista/' . $_SESSION['COMPROBANTES']['Pagina'];

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
			$_SESSION['Lista'] = SERVERURL . '/comprobantes/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['COMPROBANTES']['Filtro'];

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= 'WHERE ';
					else
						$query .= 'AND ';

					$query .= "(UPPER(REPLACE(TIPODOC.TipoDocumento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(MAYORES.Mayor + AUXILIARES.Auxiliar, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(MAYORES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PARAMETROS1.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PARAMETROS2.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PARAMETROS3.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%') ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['COMPROBANTES']['Orden']; 
			$data['rows'] = $this->model->listarComprobantes($query);
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
					ini_set('max_execution_time', 6000);
		
					$archivo = $_FILES['archivo']['name'];
		
					if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
					{
						if ( file_exists ($archivo) )
						{
							// $oExcel = IOFactory::load($archivo);
							$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $Excel->getSheet(0);
		
							$query = <<<EOD
								DELETE FROM COMPROBANTES;
							EOD;

							$ok = $this->model->query($query);

							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
								{
									$MensajeError = '';
        
		                            $Comprobante 		= trim($oHoja->getCell('A' . $i)->getCalculatedValue());
                                    $Mayor 				= str_pad($oHoja->getCell('B' . $i)->getCalculatedValue(), 2, '0', STR_PAD_LEFT);
                                    $Auxiliar 			= str_pad($oHoja->getCell('C' . $i)->getCalculatedValue(), 3, '0', STR_PAD_LEFT);
                                    $Detalle 			= trim($oHoja->getCell('D' . $i)->getCalculatedValue());
                                    $TipoEmpleado 		= trim($oHoja->getCell('E' . $i)->getCalculatedValue());
                                    $Porcentaje 		= $oHoja->getCell('F' . $i)->getCalculatedValue();
                                    $CuentaDb 			= trim($oHoja->getCell('G' . $i)->getCalculatedValue());
                                    $DetallaCentroDb 	= trim($oHoja->getCell('H' . $i)->getCalculatedValue());
                                    $CuentaCr 			= trim($oHoja->getCell('I' . $i)->getCalculatedValue());
                                    $DetallaCentroCr 	= trim($oHoja->getCell('J' . $i)->getCalculatedValue());
                                    $TipoTercero 		= trim($oHoja->getCell('K' . $i)->getCalculatedValue());
                                    $Exonerable 		= trim($oHoja->getCell('L' . $i)->getCalculatedValue());

									$IdTipoDoc = getId('TIPODOC', "TIPODOC.TipoDocumento = '$Comprobante'");

									if ($IdTipoDoc == 0) 
										$MensajeError .= 'Tipo documento no existe <strong>' . $Comprobante . '</strong><br>';

									if (empty($CuentaDb) OR empty($CuentaCr))
										continue;

									$IdMayor = getId('MAYORES', "MAYORES.Mayor = '$Mayor'");

									if ($IdMayor == 0) 
										$MensajeError .= 'Concepto mayor no existe <strong>' . $Mayor . '</strong><br>';

									$IdConcepto = getId('AUXILIARES', "AUXILIARES.IdMayor = $IdMayor AND AUXILIARES.Auxiliar = '$Auxiliar'");

									if ($IdConcepto == 0) 
										$MensajeError .= 'Concepto auxiliar no existe <strong>' . $Mayor . $Auxiliar . '</strong><br>';

									if (! empty($TipoEmpleado)) 
									{
										$IdTipoEmpleado = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoEmpleado' AND PARAMETROS.Detalle = '$TipoEmpleado'");

										if ($IdTipoEmpleado == 0) 
											$MensajeError .= 'Tipo empleado no existe <strong>' . $TipoEmpleado . '</strong><br>';
									}
									else
										$IdTipoEmpleado = 0;

									// if (! empty($RegimenCesantias)) 
									// {
									// 	$IdRegimenCesantias = getId('PARAMETROS', "PARAMETROS.Parametro = 'RegimenCesantias' AND PARAMETROS.Detalle = '$RegimenCesantias'");

									// 	if ($IdRegimenCesantias == 0) 
									// 	{
									// 		// REGIMEN CESANTIAS NO EXISTE
									// 		$MensajeError .= 'Regimen cesantías no existe <strong>' . $RegimenCesantias . '</strong><br>';
									// 	}
									// }
									// else
									// 	$IdRegimenCesantias = 0;

									// $IdImputacion = getId('PARAMETROS', "PARAMETROS.Parametro = 'Imputacion' AND PARAMETROS.Detalle = '$Imputacion'");

									// if ($IdImputacion == 0) 
									// {
									// 	// IMPUTACION NO EXISTE
									// 	$MensajeError .= 'Imputación no existe <strong>' . $Imputacion . '</strong><br>';
									// }

									if (strtoupper($DetallaCentroDb) == 'SI') 
										$DetallaCentroDb = 1;
									else
										$DetallaCentroDb = 0;

									if (strtoupper($DetallaCentroCr) == 'SI') 
										$DetallaCentroCr = 1;
									else
										$DetallaCentroCr = 0;

									$IdTipoTercero = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoTercero' AND PARAMETROS.Detalle = '$TipoTercero'");

									if ($IdTipoTercero == 0) 
										$MensajeError .= 'Tipo tercero no existe <strong>' . $TipoTercero . '</strong><br>';

									if (empty($MensajeError))
									{
										$query = <<<EOD
											INSERT INTO COMPROBANTES 
												(IdTipoDoc, IdConcepto, Detalle, TipoEmpleado, Porcentaje, CuentaDb, DetallaCentroDb, CuentaCr, DetallaCentroCr, TipoTercero, Exonerable)
												VALUES (
													$IdTipoDoc,  
													$IdConcepto, 
													'$Detalle', 
													$IdTipoEmpleado, 
													$Porcentaje, 
													'$CuentaDb', 
													$DetallaCentroDb,
													'$CuentaCr', 
													$DetallaCentroCr,
													$IdTipoTercero, 
													$Exonerable);
										EOD;

										$ok = $this->model->query($query);
                                    }
									else
										$data['mensajeError'] .= $MensajeError;
								}
							}

							if (empty($data['mensajeError']))
							{
								header('Location: ' . SERVERURL . '/comprobantes/lista/1');
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/comprobantes/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/comprobantes/lista/' . $_SESSION['COMPROBANTES']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>

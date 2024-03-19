<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Mayores extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/mayores/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/mayores/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/mayores/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['MAYORES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['MAYORES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['MAYORES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['MAYORES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['MAYORES']['Filtro']))
			{
				$_SESSION['MAYORES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['MAYORES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['MAYORES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['MAYORES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['MAYORES']['Orden'])) 
					$_SESSION['MAYORES']['Orden'] = 'MAYORES.Mayor';

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
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['MAYORES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarMayores($query);
			$this->views->getView($this, 'mayores', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/mayores/actualizarMayor';
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
			$_SESSION['Lista'] = SERVERURL . '/mayores/lista/' . $_SESSION['MAYORES']['Pagina'];

			$data = array(
				'reg' => array(
					'Mayor' 			=> isset($_REQUEST['Mayor']) ? $_REQUEST['Mayor'] : '',
					'Nombre' 			=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
					'TipoLiquidacion' 	=> isset($_REQUEST['TipoLiquidacion']) ? $_REQUEST['TipoLiquidacion'] : '',
					'ClaseConcepto' 	=> isset($_REQUEST['ClaseConcepto']) ? $_REQUEST['ClaseConcepto'] : '',
					'TipoRetencion' 	=> isset($_REQUEST['TipoRetencion']) ? $_REQUEST['TipoRetencion'] : '',
					'BasePrimas' 		=> isset($_REQUEST['BasePrimas']) ? 'true' : 'false',
					'BaseVacaciones' 	=> isset($_REQUEST['BaseVacaciones']) ? 'true' : 'false',
					'BaseCesantias' 	=> isset($_REQUEST['BaseCesantias']) ? 'true' : 'false',
					'AcumulaSanciones' 	=> isset($_REQUEST['AcumulaSanciones']) ? 'true' : 'false',
					'AcumulaLicencias' 	=> isset($_REQUEST['AcumulaLicencias']) ? 'true' : 'false',
					'ControlaSaldos' 	=> isset($_REQUEST['ControlaSaldos']) ? 'true' : 'false',
					'RenglonCertificado' => isset($_REQUEST['RengloCertificado']) ? $_REQUEST['RenglonCertificado'] : '', 
					'ExcluidoNE' 		=> isset($_REQUEST['ExcluidoNE']) ? 'true' : 'false'
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Mayor'])) 
			{
				if	( empty($data['reg']['Mayor']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Concepto mayor') . '</strong><br>';
				else
				{
					$query = 'SELECT * FROM MAYORES ' .
							"WHERE MAYORES.Mayor = '" . $data['reg']['Mayor'] . "'";

					$reg = $this->model->buscarMayor($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Concepto mayor') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				if	( empty($data['reg']['TipoLiquidacion']) )
					$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Tipo de liquidación') . '</strong><br>';
			
				if	( empty($data['reg']['ClaseConcepto']) )
					$data['mensajeError'] .= label('Debe selecccionar una') . ' <strong>' . label('Clase de concepto') . '</strong><br>';
			
				if	( empty($data['reg']['TipoRetencion']) )
					$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Tipo de retención') . '</strong><br>';
			
				// if	( empty($data['reg']['RenglonCertificado']) )
				// 	$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Renglón en certificado') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarMayor($data['reg']);

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
			if (isset($_REQUEST['Mayor']))
			{
				$data = array(
					'reg' => array(
						'Mayor' 			=> isset($_REQUEST['Mayor']) ? $_REQUEST['Mayor'] : '',
						'Nombre' 			=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
						'TipoLiquidacion' 	=> isset($_REQUEST['TipoLiquidacion']) ? $_REQUEST['TipoLiquidacion'] : '',
						'ClaseConcepto' 	=> isset($_REQUEST['ClaseConcepto']) ? $_REQUEST['ClaseConcepto'] : '',
						'TipoRetencion' 	=> isset($_REQUEST['TipoRetencion']) ? $_REQUEST['TipoRetencion'] : '',
						'BasePrimas' 		=> isset($_REQUEST['BasePrimas']) ?'true' : 'false',
						'BaseVacaciones' 	=> isset($_REQUEST['BaseVacaciones']) ? 'true' : 'false',
						'BaseCesantias' 	=> isset($_REQUEST['BaseCesantias']) ? 'true' : 'false',
						'AcumulaSanciones' 	=> isset($_REQUEST['AcumulaSanciones']) ? 'true' : 'false',
						'AcumulaLicencias' 	=> isset($_REQUEST['AcumulaLicencias']) ? 'true' : 'false',
						'ControlaSaldos' 	=> isset($_REQUEST['ControlaSaldos']) ? 'true' : 'false',
						'RenglonCertificado' => isset($_REQUEST['RenglonCertificado']) ? $_REQUEST['RenglonCertificado'] : '', 
						'ExcluidoNE' 		=> isset($_REQUEST['ExcluidoNE']) ? 'true' : 'false'
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['Mayor']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Concepto mayor') . '</strong><br>';
				else
				{
					$Mayor = $data['reg']['Mayor'];

					$query = <<<EOD
						SELECT * FROM MAYORES
							WHERE MAYORES.Mayor = '$Mayor' AND 
								MAYORES.Id <> $id
					EOD;

					$reg = $this->model->buscarMayor($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Concepto mayor') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				if	( empty($data['reg']['TipoLiquidacion']) )
					$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Tipo de liquidación') . '</strong><br>';
			
				if	( empty($data['reg']['ClaseConcepto']) )
					$data['mensajeError'] .= label('Debe selecccionar una') . ' <strong>' . label('Clase de concepto') . '</strong><br>';
			
				if	( empty($data['reg']['TipoRetencion']) )
					$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Tipo de retención') . '</strong><br>';
			
				// if	( empty($data['reg']['RenglonCertificado']) )
				// 	$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Renglón en certificado') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarMayor($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/mayores/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/mayores/lista/' . $_SESSION['MAYORES']['Pagina'];

				$query = 'SELECT * FROM MAYORES WHERE MAYORES.Id = ' . $id;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'Id' 				=> $reg['id'], 
						'Mayor' 			=> $reg['mayor'],
						'Nombre' 			=> $reg['nombre'],
						'TipoLiquidacion' 	=> $reg['tipoliquidacion'],
						'ClaseConcepto' 	=> $reg['claseconcepto'],
						'TipoRetencion' 	=> $reg['tiporetencion'],
						'BasePrimas' 		=> ($reg['baseprimas'] ? true : false),
						'BaseVacaciones' 	=> ($reg['basevacaciones'] ? true : false),
						'BaseCesantias' 	=> ($reg['basecesantias'] ? true : false),
						'AcumulaSanciones' 	=> ($reg['acumulasanciones'] ? true : false),
						'AcumulaLicencias' 	=> ($reg['acumulalicencias'] ? true : false),
						'ControlaSaldos' 	=> ($reg['controlasaldos'] ? true : false),
						'RenglonCertificado' => $reg['rengloncertificado'],
						'ExcluidoNE' 		=> ($reg['excluidone'] ? true : false) 
					),
					'mensajeError' => ''
				);

				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			$query = <<<EOD
				SELECT *
				FROM MAYORES
				WHERE MAYORES.Id = $id
			EOD;
				
			$reg = $this->model->leer($query);

			$data = array(
				'reg' => array(
					'Id' 				=> $reg['id'], 
					'Mayor' 			=> $reg['mayor'],
					'Nombre' 			=> $reg['nombre'],
					'TipoLiquidacion' 	=> $reg['tipoliquidacion'],
					'ClaseConcepto' 	=> $reg['claseconcepto'],
					'TipoRetencion' 	=> $reg['tiporetencion'],
					'BasePrimas' 		=> ($reg['baseprimas'] ? true : false),
					'BaseVacaciones' 	=> ($reg['basevacaciones'] ? true : false),
					'BaseCesantias' 	=> ($reg['basecesantias'] ? true : false),
					'AcumulaSanciones' 	=> ($reg['acumulasanciones'] ? true : false),
					'AcumulaLicencias' 	=> ($reg['acumulalicencias'] ? true : false),
					'ControlaSaldos' 	=> ($reg['controlasaldos'] ? true : false),
					'RenglonCertificado' => $reg['rengloncertificado'], 
					'ExcluidoNE' 		=> ($reg['excluidone'] ? true : false) 
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Id']))
			{
				$query = <<<EOD
					SELECT COUNT(*) AS Registros 
						FROM AUXILIARES 
						WHERE AUXILIARES.IdMayor = $id;
				EOD;

				$reg = $this->model->buscarMayor($query);

				if ($reg['registros'] > 0) 
					$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Concepto mayor') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarMayor($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/mayores/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/mayores/lista/' . $_SESSION['MAYORES']['Pagina'];

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
			$_SESSION['Lista'] = SERVERURL . '/mayores/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['MAYORES']['Filtro'];

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
			
			$query .= 'ORDER BY ' . $_SESSION['MAYORES']['Orden']; 
			$data['rows'] = $this->model->listarMayores($query);
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
									$Excel[$row]['Mayor'] 	= trim($oHoja->getCell('A' . $i)->getCalculatedValue());
									$Excel[$row]['Nombre'] 	= trim($oHoja->getCell('B' . $i)->getCalculatedValue());
									
									$TipoLiquidacion = $oHoja->getCell('C' . $i)->getCalculatedValue();

									$query = <<<EOD
										SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'TipoLiquidacion' AND
												PARAMETROS.Detalle = '$TipoLiquidacion'
									EOD;

									$reg = $this->model->buscarMayor($query);

									if ($reg)
										$Excel[$row]['TipoLiquidacion'] = $reg['id'];
									else
										$Excel[$row]['TipoLiquidacion'] = 0;

									$ClaseConcepto = $oHoja->getCell('D' . $i)->getCalculatedValue();
									$query = <<<EOD
										SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'ClaseConcepto' AND
												PARAMETROS.Detalle = '$ClaseConcepto'
									EOD;

									$reg = $this->model->buscarMayor($query);

									if ($reg)
										$Excel[$row]['ClaseConcepto'] = $reg['id'];
									else
										$Excel[$row]['ClaseConcepto'] = 0;

									$Excel[$row]['BasePrimas'] = empty($oHoja->getCell('E' . $i)->getCalculatedValue()) ? 0 : 1;
									$Excel[$row]['BaseVacaciones'] = empty($oHoja->getCell('F' . $i)->getCalculatedValue()) ? 0 : 1;
									$Excel[$row]['BaseCesantias'] = empty($oHoja->getCell('G' . $i)->getCalculatedValue()) ? 0 : 1;
									$Excel[$row]['AcumulaSanciones'] = empty($oHoja->getCell('H' . $i)->getCalculatedValue()) ? 0 : 1;
									$Excel[$row]['AcumulaLicencias'] = empty($oHoja->getCell('I' . $i)->getCalculatedValue()) ? 0 : 1;
									$Excel[$row]['ControlaSaldos'] = empty($oHoja->getCell('J' . $i)->getCalculatedValue()) ? 0 : 1;

									$TipoRetencion = $oHoja->getCell('K' . $i)->getCalculatedValue();

									$query = <<<EOD
										SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'TipoRetencion' AND
												PARAMETROS.Detalle = '$TipoRetencion'
									EOD;

									$reg = $this->model->buscarMayor($query);

									if ($reg)
										$Excel[$row]['TipoRetencion'] = $reg['id'];
									else
										$Excel[$row]['TipoRetencion'] = 0;

									$Excel[$row]['RenglonCertificado'] = $oHoja->getCell('L' . $i)->getCalculatedValue();
									$row++;
								}
							}

							for ( $i = 0; $i < count($Excel); $i++ )
							{
								// BUSCAMOS EL CONCEPTOS MAYOR PARA ADICIONAR O ACTUALIZAR
								$Mayor = $Excel[$i]['Mayor'];

								$query = <<<EOD
									SELECT *
										FROM MAYORES
										WHERE MAYORES.Mayor = '$Mayor'
								EOD;

								$reg = $this->model->buscarMayor($query);

								if ($reg) 
									$this->model->actualizarMayor($Excel[$i], $reg['id']);
								else
									$this->model->guardarMayor($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/mayores/lista/1');
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/mayores/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/mayores/lista/' . $_SESSION['MAYORES']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>
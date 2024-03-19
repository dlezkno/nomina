<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Auxiliares extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/auxiliares/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/auxiliares/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/auxiliares/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['AUXILIARES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['AUXILIARES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['AUXILIARES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['AUXILIARES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['AUXILIARES']['Filtro']))
			{
				$_SESSION['AUXILIARES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['AUXILIARES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['AUXILIARES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['AUXILIARES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['AUXILIARES']['Orden'])) 
					$_SESSION['AUXILIARES']['Orden'] = 'AUXILIARES.Borrado,MAYORES.Mayor,AUXILIARES.Auxiliar';

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (! empty($query))
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(MAYORES.Mayor + AUXILIARES.Auxiliar, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(MAYORES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}

			if (! empty($query))
				$query = 'WHERE ' . $query;
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['AUXILIARES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarAuxiliares($query);
			$this->views->getView($this, 'auxiliares', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/auxiliares/actualizarAuxiliar';
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
			$_SESSION['Lista'] = SERVERURL . '/auxiliares/lista/' . $_SESSION['AUXILIARES']['Pagina'];

			$data = array(
				'reg' => array(
					'IdMayor' 				=> isset($_REQUEST['IdMayor']) ? $_REQUEST['IdMayor'] : '',
					'Auxiliar' 				=> isset($_REQUEST['Auxiliar']) ? $_REQUEST['Auxiliar'] : '',
					'Nombre' 				=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
					'TipoEmpleado' 			=> isset($_REQUEST['TipoEmpleado']) ? $_REQUEST['TipoEmpleado'] : 0,
					'Imputacion' 			=> isset($_REQUEST['Imputacion']) ? $_REQUEST['Imputacion'] : 0,
					'ModoLiquidacion' 		=> isset($_REQUEST['ModoLiquidacion']) ? $_REQUEST['ModoLiquidacion'] : 0,
					'FactorConversion' 		=> isset($_REQUEST['FactorConversion']) ? $_REQUEST['FactorConversion'] : 1,
					'HoraFija' 				=> isset($_REQUEST['HoraFija']) ? $_REQUEST['HoraFija'] : 0,
					'ValorFijo' 			=> isset($_REQUEST['ValorFijo']) ? $_REQUEST['ValorFijo'] : 0,
					'TipoAuxiliar' 			=> isset($_REQUEST['TipoAuxiliar']) ? $_REQUEST['TipoAuxiliar'] : 0,
					'TipoRegistroAuxiliar' 	=> isset($_REQUEST['TipoRegistroAuxiliar']) ? $_REQUEST['TipoRegistroAuxiliar'] : 0,
					'EsDispersable' 		=> 1, 
					'CodigoNE'				=> isset($_REQUEST['CodigoNE']) ? $_REQUEST['CodigoNE'] : '', 
					'ExcluidoNE' 			=> isset($_REQUEST['ExcluidoNE']) ? 'true' : 'false'
				),
				'NombreTipoLiquidacion' => '',
				'mensajeError' => ''
			);

			if (isset($_REQUEST['IdMayor'])) 
			{
				if	( empty($data['reg']['IdMayor']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Concepto mayor') . '</strong><br>';
				else
				{
					$IdMayor = $data['reg']['IdMayor'];

					$query = <<<EOD
						SELECT MAYORES.*,
								PARAMETROS.Detalle AS NombreTipoLiquidacion
							FROM MAYORES 
								INNER JOIN PARAMETROS 
									ON MAYORES.TipoLiquidacion = PARAMETROS.Id 
							WHERE MAYORES.Id = $IdMayor AND 
								PARAMETROS.Parametro = 'TipoLiquidacion';
					EOD;

					$regMayor = $this->model->buscarAuxiliar($query);

					$data['NombreTipoLiquidacion'] .= $regMayor['NombreTipoLiquidacion'];
				}

				if	( empty($data['reg']['Auxiliar']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Concepto auxiliar') . '</strong><br>';
				else
				{
					$IdMayor = $data['reg']['IdMayor'];
					$Auxiliar = $data['reg']['Auxiliar'];

					$query = <<<EOD
						SELECT * FROM AUXILIARES 
							INNER JOIN MAYORES
								ON AUXILIARES.IdMayor = MAYORES.Id 
							WHERE AUXILIARES.IdMayor = $IdMayor AND 
								AUXILIARES.Auxiliar = '$Auxiliar';
					EOD;

					$reg = $this->model->buscarAuxiliar($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Concepto auxiliar') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				// if	( empty($data['reg']['TipoEmpleado']) )
				// 	$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Tipo de empleado') . '</strong><br>';
			
				if	( empty($data['reg']['Imputacion']) )
					$data['mensajeError'] .= label('Debe selecccionar una') . ' <strong>' . label('Imputación') . '</strong><br>';
			
				if	( empty($data['reg']['ModoLiquidacion']) )
					$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Modo de liquidación') . '</strong><br>';
			
				if	( $data['reg']['FactorConversion'] <= 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Factor de conversión') . '</strong><br>';
			
				if ($data['NombreTipoLiquidacion'] == 'HORAS' OR $data['NombreTipoLiquidacion'] == 'PRODUCCIÓN')
				{
					if	( $data['reg']['HoraFija'] < 0 )
						$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Hora fija') . '</strong><br>';
				}
			
				if ($data['NombreTipoLiquidacion'] == 'VALOR')
				{
					if	( $data['reg']['ValorFijo'] < 0 )
						$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Valor fijo') . '</strong><br>';
				}

				if	( empty($data['reg']['CodigoNE']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Código de nómina electrónica') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarAuxiliar($data['reg']);

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
			if (isset($_REQUEST['IdMayor']))
			{
				$data = array(
					'reg' => array(
						'IdMayor' 				=> isset($_REQUEST['IdMayor']) ? $_REQUEST['IdMayor'] : '',
						'Auxiliar' 				=> isset($_REQUEST['Auxiliar']) ? $_REQUEST['Auxiliar'] : '',
						'Nombre' 				=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
						'TipoEmpleado' 			=> isset($_REQUEST['TipoEmpleado']) ? $_REQUEST['TipoEmpleado'] : 0,
						'Imputacion' 			=> isset($_REQUEST['Imputacion']) ? $_REQUEST['Imputacion'] : 0,
						'ModoLiquidacion' 		=> isset($_REQUEST['ModoLiquidacion']) ? $_REQUEST['ModoLiquidacion'] : 0,
						'FactorConversion' 		=> isset($_REQUEST['FactorConversion']) ? $_REQUEST['FactorConversion'] : 1,
						'HoraFija' 				=> isset($_REQUEST['HoraFija']) ? $_REQUEST['HoraFija'] : 0,
						'ValorFijo' 			=> isset($_REQUEST['ValorFijo']) ? $_REQUEST['ValorFijo'] : 0,
						'TipoAuxiliar' 			=> isset($_REQUEST['TipoAuxiliar']) ? $_REQUEST['TipoAuxiliar'] : 0,
						'TipoRegistroAuxiliar' 	=> isset($_REQUEST['TipoRegistroAuxiliar']) ? $_REQUEST['TipoRegistroAuxiliar'] : 0,
						'EsDispersable' 		=> isset($_REQUEST['EsDispersable']) ? 1 : 0,
						'CodigoNE' 				=> isset($_REQUEST['CodigoNE']) ? $_REQUEST['CodigoNE'] : ''
					),
					'NombreTipoLiquidacion' => '',
					'mensajeError' => ''
				);

				if	( empty($data['reg']['IdMayor']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Concepto mayor') . '</strong><br>';
				else
				{
					$IdMayor = $data['reg']['IdMayor'];

					$query = <<<EOD
						SELECT MAYORES.*,
								PARAMETROS.Detalle AS NombreTipoLiquidacion
							FROM MAYORES 
								INNER JOIN PARAMETROS 
									ON MAYORES.TipoLiquidacion = PARAMETROS.Id 
							WHERE MAYORES.Id = $IdMayor AND 
								PARAMETROS.Parametro = 'TipoLiquidacion';
					EOD;

					$regMayor = $this->model->buscarAuxiliar($query);

					$data['NombreTipoLiquidacion'] .= $regMayor['NombreTipoLiquidacion'];
				}

				if	( empty($data['reg']['Auxiliar']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Concepto auxiliar') . '</strong><br>';
				else
				{
					$IdMayor = $data['reg']['IdMayor'];
					$Auxiliar = $data['reg']['Auxiliar'];

					$query = <<<EOD
						SELECT * FROM AUXILIARES 
							INNER JOIN MAYORES
								ON AUXILIARES.IdMayor = MAYORES.Id 
							WHERE AUXILIARES.IdMayor = $IdMayor AND 
								AUXILIARES.Auxiliar = '$Auxiliar' AND 
								AUXILIARES.Id <> $id;
					EOD;

					$reg = $this->model->buscarAuxiliar($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Concepto auxiliar') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				// if	( empty($data['reg']['TipoEmpleado']) )
				// 	$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Tipo de empleado') . '</strong><br>';
			
				if	( empty($data['reg']['Imputacion']) )
					$data['mensajeError'] .= label('Debe selecccionar una') . ' <strong>' . label('Imputación') . '</strong><br>';
			
				if	( empty($data['reg']['ModoLiquidacion']) )
					$data['mensajeError'] .= label('Debe selecccionar un') . ' <strong>' . label('Modo de liquidación') . '</strong><br>';
			
				if	( $data['reg']['FactorConversion'] <= 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Factor de conversión') . '</strong><br>';
			
				if ($data['NombreTipoLiquidacion'] == 'HORAS' OR $data['NombreTipoLiquidacion'] == 'PRODUCCIÓN')
				{
					if	( $data['reg']['HoraFija'] < 0 )
						$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Hora fija') . '</strong><br>';
				}
			
				if ($data['NombreTipoLiquidacion'] == 'VALOR')
				{
					if	( $data['reg']['ValorFijo'] < 0 )
						$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Valor fijo') . '</strong><br>';
				}
				
				if	( empty($data['reg']['CodigoNE']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Código de nómina electrónica') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarAuxiliar($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/auxiliares/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/auxiliares/lista/' . $_SESSION['AUXILIARES']['Pagina'];

				$query = 'SELECT * FROM AUXILIARES WHERE AUXILIARES.Id = ' . $id;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'Id' 					=> $reg['id'],
						'IdMayor' 				=> $reg['idmayor'],
						'Auxiliar' 				=> $reg['auxiliar'],
						'Nombre' 				=> $reg['nombre'],
						'TipoEmpleado' 			=> $reg['tipoempleado'],
						'Imputacion' 			=> $reg['imputacion'],
						'ModoLiquidacion' 		=> $reg['modoliquidacion'],
						'FactorConversion' 		=> $reg['factorconversion'],
						'HoraFija' 				=> $reg['horafija'],
						'ValorFijo' 			=> $reg['valorfijo'],
						'TipoAuxiliar' 			=> $reg['tipoauxiliar'],
						'TipoRegistroAuxiliar' 	=> $reg['tiporegistroauxiliar'],
						'EsDispersable' 		=> $reg['esdispersable'],
						'CodigoNE' 				=> $reg['codigone']
					),
					'NombreTipoLiquidacion' => '',
					'mensajeError' => ''
				);

				if ($data) 
					$this->views->getView($this, 'actualizar', $data, $id);
			}
		}

		public function borrar($id)
		{
			if (isset($_REQUEST['IdMayor']))
			{
				$this->model->borrarAuxiliar($id);

				header('Location: ' . $_SESSION['Lista']);
				exit();
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = SERVERURL . '/auxiliares/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/auxiliares/lista/' . $_SESSION['AUXILIARES']['Pagina'];

				$query = 'SELECT * FROM AUXILIARES WHERE AUXILIARES.Id = ' . $id;
					
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'Id' 					=> $reg['id'],
						'IdMayor' 				=> $reg['idmayor'],
						'Auxiliar' 				=> $reg['auxiliar'],
						'Nombre' 				=> $reg['nombre'],
						'TipoEmpleado' 			=> $reg['tipoempleado'],
						'Imputacion' 			=> $reg['imputacion'],
						'ModoLiquidacion' 		=> $reg['modoliquidacion'],
						'FactorConversion' 		=> $reg['factorconversion'],
						'HoraFija' 				=> $reg['horafija'],
						'ValorFijo' 			=> $reg['valorfijo'],
						'TipoAuxiliar' 			=> $reg['tipoauxiliar'],
						'TipoRegistroAuxiliar' 	=> $reg['tiporegistroauxiliar'],
						'EsDispersable' 		=> $reg['esdispersable'],
						'CodigoNE' 				=> $reg['codigone']
					),
					'NombreTipoLiquidacion' => '',
					'mensajeError' => ''
				);

				if ($data) 
					$this->views->getView($this, 'actualizar', $data, $id);
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
			$_SESSION['Lista'] = SERVERURL . '/auxiliares/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['AUXILIARES']['Filtro'];

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

					$query .= "UPPER(REPLACE(MAYORES.Mayor + AUXILIARES.Auxiliar, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(MAYORES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['AUXILIARES']['Orden']; 
			$data['rows'] = $this->model->listarAuxiliares($query);
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
									$Mayor = trim($oHoja->getCell('A' . $i)->getCalculatedValue());

									$query = <<<EOD
										SELECT *
											FROM MAYORES
											WHERE MAYORES.Mayor = '$Mayor';
									EOD;

									$reg = $this->model->buscarAuxiliar($query);

									if ($reg)
										$Excel[$row]['IdMayor'] = $reg['id'];
									else
										$Excel[$row]['IdMayor'] = 0;

									$Excel[$row]['Auxiliar'] 	= trim($oHoja->getCell('B' . $i)->getCalculatedValue());
									$Excel[$row]['Nombre'] 		= trim($oHoja->getCell('C' . $i)->getCalculatedValue());
									
									$TipoEmpleado = trim($oHoja->getCell('D' . $i)->getCalculatedValue());
									$query = <<<EOD
										SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'TipoEmpleado' AND
												PARAMETROS.Detalle = '$TipoEmpleado';
									EOD;

									$reg = $this->model->buscarAuxiliar($query);

									if ($reg)
										$Excel[$row]['TipoEmpleado'] = $reg['id'];
									else
										$Excel[$row]['TipoEmpleado'] = 0;

									$Imputacion = trim($oHoja->getCell('E' . $i)->getCalculatedValue());

									$query = <<<EOD
										SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'Imputacion' AND
												PARAMETROS.Detalle = '$Imputacion';
									EOD;

									$reg = $this->model->buscarAuxiliar($query);

									if ($reg)
										$Excel[$row]['Imputacion'] = $reg['id'];
									else
										$Excel[$row]['Imputacion'] = 0;

									$ModoLiquidacion = trim($oHoja->getCell('F' . $i)->getCalculatedValue());

									$query = <<<EOD
										SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'ModoLiquidacion' AND
												PARAMETROS.Detalle = '$ModoLiquidacion';
									EOD;

									$reg = $this->model->buscarAuxiliar($query);

									if ($reg)
										$Excel[$row]['ModoLiquidacion'] = $reg['id'];
									else
										$Excel[$row]['ModoLiquidacion'] = 0;

									$Excel[$row]['FactorConversion'] = empty($oHoja->getCell('G' . $i)->getCalculatedValue()) ? 0 : 1;
									$Excel[$row]['HoraFija'] = empty($oHoja->getCell('H' . $i)->getCalculatedValue()) ? 0 : 1;
									$Excel[$row]['ValorFijo'] = empty($oHoja->getCell('I' . $i)->getCalculatedValue()) ? 0 : 1;

									$TipoAuxiliar = trim($oHoja->getCell('J' . $i)->getCalculatedValue());

									$query = <<<EOD
										SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'TipoAuxiliar' AND
												PARAMETROS.Detalle = '$TipoAuxiliar';
									EOD;

									$reg = $this->model->buscarAuxiliar($query);

									if ($reg)
										$Excel[$row]['TipoAuxiliar'] = $reg['id'];
									else
										$Excel[$row]['TipoAuxiliar'] = 0;

									$TipoRegistroAuxiliar = trim($oHoja->getCell('K' . $i)->getCalculatedValue());

									$query = <<<EOD
										SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND
												PARAMETROS.Detalle = '$TipoRegistroAuxiliar';
									EOD;

									$reg = $this->model->buscarAuxiliar($query);

									if ($reg)
										$Excel[$row]['TipoRegistroAuxiliar'] = $reg['id'];
									else
										$Excel[$row]['TipoRegistroAuxiliar'] = 0;
	
									$row++;
								}
							}

							for ( $i = 0; $i < count($Excel); $i++ )
							{
								// BUSCAMOS EL CONCEPTOS AUXILIAR PARA ADICIONAR O ACTUALIZAR
								$IdMayor = $Excel[$i]['IdMayor'];
								$Auxiliar = $Excel[$i]['Auxiliar'];

								$query = <<<EOD
									SELECT *
										FROM AUXILIARES
										WHERE AUXILIARES.IdMayor = $IdMayor AND 
											AUXILIARES.Auxiliar = '$Auxiliar';
								EOD;

								$reg = $this->model->buscarAuxiliar($query);

								if ($reg) 
									$this->model->actualizarAuxiliar($Excel[$i], $reg['id']);
								else
									$this->model->guardarAuxiliar($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/auxiliares/lista/1');
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/auxiliares/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/auxiliares/lista/' . $_SESSION['AUXILIARES']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>
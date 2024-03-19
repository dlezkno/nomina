<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Terceros extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/terceros/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/terceros/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/terceros/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['TERCEROS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['TERCEROS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['TERCEROS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['TERCEROS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['TERCEROS']['Filtro']))
			{
				$_SESSION['TERCEROS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['TERCEROS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['TERCEROS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['TERCEROS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['TERCEROS']['Orden'])) 
					$_SESSION['TERCEROS']['Orden'] = 'TERCEROS.Nombre';

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

					$query .= "UPPER(REPLACE(TERCEROS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(TERCEROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['TERCEROS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarTerceros($query);
			$this->views->getView($this, 'terceros', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/terceros/actualizarTercero';
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
			$_SESSION['Lista'] = SERVERURL . '/terceros/lista/' . $_SESSION['TERCEROS']['Pagina'];

			$data = array(
				'reg' => array(
					'TipoIdentificacion' 	=> isset($_REQUEST['TipoIdentificacion']) ? $_REQUEST['TipoIdentificacion'] : '',
					'Documento' 			=> isset($_REQUEST['Documento']) ? $_REQUEST['Documento'] : '',
					'Nombre' 				=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
					'Nombre2' 				=> isset($_REQUEST['Nombre2']) ? $_REQUEST['Nombre2'] : '',
					'EsDeudor' 				=> isset($_REQUEST['EsDeudor']) ? 'true' : 'false',
					'EsAcreedor' 			=> isset($_REQUEST['EsAcreedor']) ? 'true' : 'false',
					'Direccion' 			=> isset($_REQUEST['Direccion']) ? $_REQUEST['Direccion'] : '',
					'IdCiudad' 				=> isset($_REQUEST['IdCiudad']) ? $_REQUEST['IdCiudad'] : 0,
					'Telefono' 				=> isset($_REQUEST['Telefono']) ? $_REQUEST['Telefono'] : '',
					'Celular' 				=> isset($_REQUEST['Celular']) ? $_REQUEST['Celular'] : '',
					'Email' 				=> isset($_REQUEST['Email']) ? $_REQUEST['Email'] : '',
					'FormaDePago' 			=> isset($_REQUEST['FormaDePago']) ? $_REQUEST['FormaDePago'] : '',
					'IdBanco' 				=> isset($_REQUEST['IdBanco']) ? $_REQUEST['IdBanco'] : 0,
					'CuentaBancaria' 		=> isset($_REQUEST['CuentaBancaria']) ? $_REQUEST['CuentaBancaria'] : '',
					'TipoCuentaBancaria' 	=> isset($_REQUEST['TipoCuentaBancaria']) ? $_REQUEST['TipoCuentaBancaria'] : 0,
					'EsSindicato' 			=> isset($_REQUEST['EsSindicato']) ? 'true' : 'false',
					'EsEPS' 				=> isset($_REQUEST['EsEPS']) ? 'true' : 'false',
					'EsARL' 				=> isset($_REQUEST['EsARL']) ? 'true' : 'false',
					'EsFondoCesantias' 		=> isset($_REQUEST['EsFondoCesantias']) ? 'true' : 'false',
					'EsFondoPensiones' 		=> isset($_REQUEST['EsFondoPensiones']) ? 'true' : 'false',
					'EsCCF' 				=> isset($_REQUEST['EsCCF']) ? 'true' : 'false',
					'Codigo' 				=> isset($_REQUEST['Codigo']) ? $_REQUEST['Codigo'] : '',
					'CodigoSAP' 			=> isset($_REQUEST['CodigoSAP']) ? $_REQUEST['CodigoSAP'] : ''),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($data['reg']['TipoIdentificacion']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de identificación.') . '</strong><br>';

				if	( empty($data['reg']['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento') . '</strong><br>';
				else
				{
					$Documento = $data['reg']['Documento'];

					$query = <<<EOD
						SELECT * FROM TERCEROS
							WHERE TERCEROS.Documento = '$Documento';
					EOD;

					$reg = $this->model->buscarTercero($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Documento') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				// if	( empty($data['reg']['Nombre2']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre 2') . '</strong><br>';
			
				// if	( empty($data['reg']['Direccion']) )
				// 	$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Dirección') . '</strong><br>';
			
				// if	( empty($data['reg']['IdCiudad']) )
				// 	$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad') . '</strong><br>';
			
				// if	( empty($data['reg']['Telefono']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Teléfono') . '</strong><br>';
			
				// if	( empty($data['reg']['Celular']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Celular') . '</strong><br>';
			
				// if	( empty($data['reg']['Email']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('E-mail') . '</strong><br>';
			
				// if	( empty($data['reg']['CodigoSAP']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Código SAP') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarTercero($data['reg']);

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
			if (isset($_REQUEST['Documento']))
			{
				$data = array(
					'reg' => array(
						'TipoIdentificacion' => isset($_REQUEST['TipoIdentificacion']) ? $_REQUEST['TipoIdentificacion'] : '',
						'Documento' 		=> isset($_REQUEST['Documento']) ? $_REQUEST['Documento'] : '',
						'Nombre' 			=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
						'Nombre2' 			=> isset($_REQUEST['Nombre2']) ? $_REQUEST['Nombre2'] : '',
						'EsDeudor' 			=> isset($_REQUEST['EsDeudor']) ? 'true' : 'false',
						'EsAcreedor' 		=> isset($_REQUEST['EsAcreedor']) ? 'true' : 'false',
						'Direccion' 		=> isset($_REQUEST['Direccion']) ? $_REQUEST['Direccion'] : '',
						'IdCiudad' 			=> isset($_REQUEST['IdCiudad']) ? $_REQUEST['IdCiudad'] : 0,
						'Telefono' 			=> isset($_REQUEST['Telefono']) ? $_REQUEST['Telefono'] : '',
						'Celular' 			=> isset($_REQUEST['Celular']) ? $_REQUEST['Celular'] : '',
						'Email' 			=> isset($_REQUEST['Email']) ? $_REQUEST['Email'] : '',
						'FormaDePago' 		=> isset($_REQUEST['FormaDePago']) ? $_REQUEST['FormaDePago'] : '',
						'IdBanco' 			=> isset($_REQUEST['IdBanco']) ? $_REQUEST['IdBanco'] : 0,
						'CuentaBancaria' 	=> isset($_REQUEST['CuentaBancaria']) ? $_REQUEST['CuentaBancaria'] : '',
						'TipoCuentaBancaria' => isset($_REQUEST['TipoCuentaBancaria']) ? $_REQUEST['TipoCuentaBancaria'] : 0,
						'EsSindicato' 		=> isset($_REQUEST['EsSindicato']) ? 'true' : 'false',
						'EsEPS' 			=> isset($_REQUEST['EsEPS']) ? 'true' : 'false',
						'EsARL' 			=> isset($_REQUEST['EsARL']) ? 'true' : 'false',
						'EsFondoCesantias' 	=> isset($_REQUEST['EsFondoCesantias']) ? 'true' : 'false',
						'EsFondoPensiones' 	=> isset($_REQUEST['EsFondoPensiones']) ? 'true' : 'false',
						'EsCCF' 			=> isset($_REQUEST['EsCCF']) ? 'true' : 'false',
						'Codigo' 			=> isset($_REQUEST['Codigo']) ? $_REQUEST['Codigo'] : '',
						'CodigoSAP' 		=> isset($_REQUEST['CodigoSAP']) ? $_REQUEST['CodigoSAP'] : ''),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['TipoIdentificacion']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de identificación.') . '</strong><br>';

				if	( empty($data['reg']['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento') . '</strong><br>';
				else
				{
					$Documento = $data['reg']['Documento'];

					$query = <<<EOD
						SELECT * FROM TERCEROS
							WHERE TERCEROS.Documento = '$Documento' AND 
								TERCEROS.Id <> $id;
					EOD;

					$reg = $this->model->buscarTercero($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Documento') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				// if	( empty($data['reg']['Nombre2']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre 2') . '</strong><br>';
			
				// if	( empty($data['reg']['Direccion']) )
				// 	$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Dirección') . '</strong><br>';
			
				// if	( empty($data['reg']['IdCiudad']) )
				// 	$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad') . '</strong><br>';
			
				// if	( empty($data['reg']['Telefono']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Teléfono') . '</strong><br>';
			
				// if	( empty($data['reg']['Celular']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Celular') . '</strong><br>';
			
				// if	( empty($data['reg']['Email']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('E-mail') . '</strong><br>';
			
				// if	( empty($data['reg']['CodigoSAP']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Código SAP') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarTercero($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/terceros/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/terceros/lista/' . $_SESSION['TERCEROS']['Pagina'];

				$query = 'SELECT * FROM TERCEROS WHERE TERCEROS.Id = ' . $id;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'TipoIdentificacion' 	=> $reg['tipoidentificacion'],
						'Documento' 			=> $reg['documento'],
						'Nombre' 				=> $reg['nombre'],
						'Nombre2' 				=> $reg['nombre2'],
						'EsDeudor' 				=> $reg['esdeudor'],
						'EsAcreedor' 			=> $reg['esacreedor'],
						'Direccion' 			=> $reg['direccion'],
						'IdCiudad' 				=> $reg['idciudad'],
						'Telefono' 				=> $reg['telefono'], 
						'Celular' 				=> $reg['celular'], 
						'Email' 				=> $reg['email'], 
						'FormaDePago' 			=> $reg['formadepago'],
						'IdBanco' 				=> $reg['idbanco'],
						'CuentaBancaria' 		=> $reg['cuentabancaria'],
						'TipoCuentaBancaria' 	=> $reg['tipocuentabancaria'],
						'EsSindicato' 			=> $reg['essindicato'], 
						'EsEPS' 				=> $reg['eseps'], 
						'EsARL' 				=> $reg['esarl'], 
						'EsFondoCesantias' 		=> $reg['esfondocesantias'], 
						'EsFondoPensiones' 		=> $reg['esfondopensiones'], 
						'EsCCF' 				=> $reg['esccf'],
						'Codigo' 				=> $reg['codigo'],
						'CodigoSAP' 			=> $reg['codigosap']
						),
					'mensajeError' => ''
				);

				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			if (isset($_REQUEST['Documento']))
			{
				$query = <<<EOD
					SELECT COUNT(*) AS Registros 
						FROM EMPLEADOS 
						WHERE EMPLEADOS.IdSindicato = $id OR 
							EMPLEADOS.IdEPS = $id OR 
							EMPLEADOS.IdFondoCesantias = $id OR 
							EMPLEADOS.IdFondoPensiones = $id OR 
							EMPLEADOS.IdCajaCompensacion = $id;
				EOD;

				$reg = $this->model->buscarTercero($query);

				if ($reg['Registros'] > 0) 
					$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Tercero') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarTercero($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/terceros/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/terceros/lista/' . $_SESSION['TERCEROS']['Pagina'];

				$query = 'SELECT * FROM TERCEROS WHERE TERCEROS.Id = ' . $id;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'TipoIdentificacion' 	=> $reg['tipoidentificacion'],
						'Documento' 			=> $reg['documento'],
						'Nombre' 				=> $reg['nombre'],
						'Nombre2' 				=> $reg['nombre2'],
						'EsDeudor' 				=> $reg['esdeudor'],
						'EsAcreedor' 			=> $reg['esacreedor'],
						'Direccion' 			=> $reg['direccion'],
						'IdCiudad' 				=> $reg['idciudad'],
						'Telefono' 				=> $reg['telefono'], 
						'Celular' 				=> $reg['celular'], 
						'Email' 				=> $reg['email'], 
						'FormaDePago' 			=> $reg['formadepago'],
						'IdBanco' 				=> $reg['idbanco'],
						'CuentaBancaria' 		=> $reg['cuentabancaria'],
						'TipoCuentaBancaria' 	=> $reg['tipocuentabancaria'],
						'EsSindicato' 			=> $reg['essindicato'], 
						'EsEPS' 				=> $reg['eseps'], 
						'EsARL' 				=> $reg['esarl'], 
						'EsFondoCesantias' 		=> $reg['esfondocesantias'], 
						'EsFondoPensiones' 		=> $reg['esfondopensiones'], 
						'EsCCF' 				=> $reg['esccf'],
						'Codigo' 				=> $reg['codigo'],
						'CodigoSAP' 			=> $reg['codigosap']
						),
					'mensajeError' => ''
				);

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
			$_SESSION['Lista'] = SERVERURL . '/terceros/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['TERCEROS']['Filtro'];

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

					$query .= "UPPER(REPLACE(TERCEROS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(TERCEROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['TERCEROS']['Orden']; 
			$data['rows'] = $this->model->listarTerceros($query);
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
									$Excel[$row]['TipoIdentificacion'] = trim($oHoja->getCell('A' . $i)->getCalculatedValue());
									$Excel[$row]['Documento'] = trim($oHoja->getCell('B' . $i)->getCalculatedValue());
									$Excel[$row]['Nombre'] = trim($oHoja->getCell('C' . $i)->getCalculatedValue());
									$Excel[$row]['EsDeudor'] = empty($oHoja->getCell('D' . $i)->getCalculatedValue()) ? 'false' : 'true';
									$Excel[$row]['EsAcreedor'] = empty($oHoja->getCell('E' . $i)->getCalculatedValue()) ? 'false' : 'true';
									$Excel[$row]['Direccion'] 	= trim($oHoja->getCell('F' . $i)->getCalculatedValue());
									$Excel[$row]['IdCiudad'] 	= $oHoja->getCell('G' . $i)->getCalculatedValue();
									$Excel[$row]['Telefono'] 	= trim($oHoja->getCell('I' . $i)->getCalculatedValue());
									$Excel[$row]['Celular'] 	= trim($oHoja->getCell('J' . $i)->getCalculatedValue());
									$Excel[$row]['Email'] 		= trim($oHoja->getCell('K' . $i)->getCalculatedValue());
									$Excel[$row]['FormaDePago'] = trim($oHoja->getCell('L' . $i)->getCalculatedValue());
									$Excel[$row]['IdBanco'] 	= $oHoja->getCell('M' . $i)->getCalculatedValue();
									$Excel[$row]['CuentaBancaria'] = trim($oHoja->getCell('N' . $i)->getCalculatedValue());
									$Excel[$row]['TipoCuentaBancaria'] = trim($oHoja->getCell('O' . $i)->getCalculatedValue());
									$Excel[$row]['EsSindicato'] = empty($oHoja->getCell('P' . $i)->getCalculatedValue()) ? 'false' : 'true';
									$Excel[$row]['EsEPS'] = empty($oHoja->getCell('Q' . $i)->getCalculatedValue()) ? 'false' : 'true';
									$Excel[$row]['CuentaEPS'] = trim($oHoja->getCell('R' . $i)->getCalculatedValue());
									$Excel[$row]['EsARL'] = empty($oHoja->getCell('S' . $i)->getCalculatedValue()) ? 'false' : 'true';
									$Excel[$row]['CuentaARL'] = trim($oHoja->getCell('T' . $i)->getCalculatedValue());
									$Excel[$row]['EsFondoCesantias'] = empty($oHoja->getCell('U' . $i)->getCalculatedValue()) ? 'false' : 'true';
									$Excel[$row]['CuentaFondoCesantias'] = trim($oHoja->getCell('V' . $i)->getCalculatedValue());
									$Excel[$row]['EsFondoPensiones'] = empty($oHoja->getCell('W' . $i)->getCalculatedValue()) ? 'false' : 'true';
									$Excel[$row]['CuentaFondoPensiones'] = trim($oHoja->getCell('X' . $i)->getCalculatedValue());
									$Excel[$row]['EsCCF'] = empty($oHoja->getCell('Y' . $i)->getCalculatedValue()) ? 'false' : 'true';
									$Excel[$row]['CuentaCCF'] = trim($oHoja->getCell('Z' . $i)->getCalculatedValue());
									$Excel[$row]['CodigoSAP'] = trim($oHoja->getCell('AA' . $i)->getCalculatedValue());

									$TipoIdentificacion = $oHoja->getCell('A' . $i)->getCalculatedValue();
									
									$query = <<<EOD
										SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'TipoIdentificacion' AND
												PARAMETROS.Detalle = '$TipoIdentificacion';
									EOD;

									$reg = $this->model->buscarTercero($query);

									if ($reg)
										$Excel[$row]['TipoIdentificacion'] = $reg['id'];
									else
										$Excel[$row]['TipoIdentificacion'] = 0;
									
									$Ciudad = $oHoja->getCell('G' . $i)->getCalculatedValue();
									$query = <<<EOD
										SELECT *
											FROM CIUDADES
											WHERE CIUDADES.Nombre = '$Ciudad';
									EOD;

									$reg = $this->model->buscarTercero($query);

									if ($reg)
										$Excel[$row]['IdCiudad'] = $reg['id'];
									else
										$Excel[$row]['IdCiudad'] = 0;

									$FormaDePago = $oHoja->getCell('L' . $i)->getCalculatedValue();
									$query = <<<EOD
										SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'FormaDePago' AND
												PARAMETROS.Detalle = '$FormaDePago';
									EOD;

									$reg = $this->model->buscarTercero($query);

									if ($reg)
										$Excel[$row]['FormaDePago'] = $reg['id'];
									else
										$Excel[$row]['FormaDePago'] = 0;

									$Banco = $oHoja->getCell('M' . $i)->getCalculatedValue();
									$query = <<<EOD
										SELECT *
											FROM BANCOS
											WHERE BANCOS.Nombre = '$Banco';
									EOD;

									$reg = $this->model->buscarTercero($query);

									if ($reg)
										$Excel[$row]['IdBanco'] = $reg['id'];
									else
										$Excel[$row]['IdBanco'] = 0;
	
									$TipoCuentaBancaria = $oHoja->getCell('O' . $i)->getCalculatedValue();
									$query = <<<EOD
										SELECT *
											FROM PARAMETROS
											WHERE PARAMETROS.Parametro = 'TipoCuentaBancaria' AND
												PARAMETROS.Detalle = '$TipoCuentaBancaria';
									EOD;

									$reg = $this->model->buscarTercero($query);

									if ($reg)
										$Excel[$row]['TipoCuentaBancaria'] = $reg['id'];
									else
										$Excel[$row]['TipoCuentaBancaria'] = 0;
	
									$row++;
								}
							}

							for ( $i = 0; $i < count($Excel); $i++ )
							{
								// BUSCAMOS EL TERCERO PARA ADICIONAR O ACTUALIZAR
								$Documento = $Excel[$i]['Documento'];

								$query = <<<EOD
									SELECT *
										FROM TERCEROS
										WHERE TERCEROS.Documento = '$Documento';
								EOD;

								$reg = $this->model->buscarTercero($query);

								if ($reg) 
									$this->model->actualizarTercero($Excel[$i], $reg['id']);
								else
									$this->model->guardarTercero($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/terceros/lista/1');
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/terceros/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/terceros/lista/' . $_SESSION['TERCEROS']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}

		public function importarSAP()
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
							$oExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $oExcel->getSheet(0);
		
							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
								{
									$CodigoSAP = $oHoja->getCell('A' . $i)->getCalculatedValue();
									$Nombre = $oHoja->getCell('B' . $i)->getCalculatedValue();
									$Documento = $oHoja->getCell('D' . $i)->getCalculatedValue();
									$Ciudad = $oHoja->getCell('F' . $i)->getCalculatedValue();
									$Telefono = $oHoja->getCell('G' . $i)->getCalculatedValue();
									$Telefono = substr(str_replace('-', '', str_replace(' ', '', $Telefono)), 0, 15);
									$Email = $oHoja->getCell('H' . $i)->getCalculatedValue();
									$Direccion = $oHoja->getCell('J' . $i)->getCalculatedValue();

									$TipoIdentificacion = 0;
									
									$IdCiudad = getId('CIUDADES', "CIUDADES.Nombre = '$Ciudad'");

									if (substr($CodigoSAP, 0, 1) == 'E')
									{
										$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");

										if ($IdEmpleado > 0)
										{
											$query = <<<EOD
												UPDATE EMPLEADOS 
													SET CodigoSAP = '$CodigoSAP' 
													WHERE EMPLEADOS.Id = $IdEmpleado;
											EOD;

											$ok = $this->model->query($query);
										}
									}
									else
									{
										$datos = array(
											'TipoIdentificacion' => 0, 
											'Documento' => $Documento, 
											'Nombre' => $Nombre, 
											'EsDeudor' => 0, 
											'EsAcreedor' => 1, 
											'Direccion' => $Direccion, 
											'IdCiudad' => $IdCiudad, 
											'Telefono' => $Telefono, 
											'Celular' => '', 
											'Email' => $Email, 
											'FormaDePago' => 0, 
											'IdBanco' => 0, 
											'CuentaBancaria' => '', 
											'TipoCuentaBancaria' => 0, 
											'EsSindicato' => 0, 
											'EsEPS' => 0, 
											'CuentaEPS' => '', 
											'EsARL' => 0, 
											'CuentaARL' => '', 
											'EsFondoCesantias' => 0, 
											'CuentaFondoCesantias' => '', 
											'EsFondoPensiones' => 0, 
											'CuentaFondoPensiones' => '', 
											'EsCCF' => 0, 
											'CuentaCCF' => '',  
											'CodigoSAP' => $CodigoSAP
										);

										$IdTercero = getId('TERCEROS', "TERCEROS.Documento = '$Documento'");

										if ($IdTercero > 0) 
											$this->model->actualizarTercero($datos, $IdTercero);
										else
											$this->model->guardarTercero($datos);
									}
								}
							}

							header('Location: ' . SERVERURL . '/terceros/lista/1');
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/terceros/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/terceros/lista/' . $_SESSION['TERCEROS']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}

		public function importar2()
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
							$query = <<<EOD
								UPDATE TERCEROS
									SET EsEPS = 0, 
										EsFondoPensiones = 0,
										EsFondoCesantias = 0,
										EsARL = 0,
										EsCCF = 0,
										EsParafiscales = 0;
							EOD;

							$this->model->query($query);

							$oExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $oExcel->getSheet(0);
		
							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								if	( ! empty($oHoja->getCell('C' . $i)->getCalculatedValue()) )
								{
									$TipoIdentificacion = $oHoja->getCell('A' . $i)->getCalculatedValue();
									$Documento 			= $oHoja->getCell('B' . $i)->getCalculatedValue();
									$Documento 			= is_null($Documento) ? '' : $Documento;
									$Nombre 			= strtoupper($oHoja->getCell('C' . $i)->getCalculatedValue());
									$Codigo 			= $oHoja->getCell('D' . $i)->getCalculatedValue();
									$TipoAdmin 			= $oHoja->getCell('E' . $i)->getCalculatedValue();

									$regTercero = getRegistro('TERCEROS', 0, "TERCEROS.Documento = '$Documento'");

									if ($regTercero)
									{
										$IdTercero = $regTercero['id'];

										switch ($TipoAdmin)
										{
											case 'EPS':
												$query = <<<EOD
													UPDATE TERCEROS 
														SET EsEPS = 1 
														WHERE TERCEROS.Id = $IdTercero;
												EOD;

												break;

											case 'AFP':
												$query = <<<EOD
													UPDATE TERCEROS 
														SET EsFondoPensiones = 1, 
															EsFondoCesantias = 1 
														WHERE TERCEROS.Id = $IdTercero;
												EOD;

												break;

											case 'ARL':
												$query = <<<EOD
													UPDATE TERCEROS 
														SET EsARL = 1 
														WHERE TERCEROS.Id = $IdTercero;
												EOD;

												break;

											case 'CCF':
												$query = <<<EOD
													UPDATE TERCEROS 
														SET EsCCF = 1 
														WHERE TERCEROS.Id = $IdTercero;
												EOD;

												break;

											case 'PARAFISCALES':
												$query = <<<EOD
													UPDATE TERCEROS 
														SET EsParafiscales = 1 
														WHERE TERCEROS.Id = $IdTercero;
												EOD;

												break;
										}

										$ok = $this->model->query($query);
									}
									else
									{
										$TipoIdentificacion = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'TipoIdentificacion' AND PARAMETROS.Detalle = '$TipoIdentificacion'")['valor'];
										$EsEPS = 0;
										$EsFondoPensiones = 0;
										$EsFondoCesantias = 0;
										$EsARL = 0;
										$EsCCF = 0;
										$EsParafiscales = 0;

										switch ($TipoAdmin)
										{
											case 'EPS':
												$EsEPS = 1;
												break;

											case 'AFP':
												$EsFondoPensiones = 1;
												$EsFondoCesantias = 1;
												break;

											case 'ARL':
												$EsARL = 1;
												break;

											case 'CCF':
												$EsCCF = 1;
												break;

											case 'PARAFISCALES':
												$EsParafiscales = 1;
												break;
										}

										$query = <<<EOD
											INSERT INTO TERCEROS 
												(TipoIdentificacion, Documento, Nombre, EsEPS, EsFondoPensiones, EsFondoCesantias, EsARL, EsCCF, EsParafiscales) 
												VALUES (
													$TipoIdentificacion, 
													'$Documento', 
													'$Nombre', 
													$EsEPS, 
													$EsFondoPensiones, 
													$EsFondoCesantias, 
													$EsARL, 
													$EsCCF, 
													$EsParafiscales);
										EOD;

										$ok = $this->model->query($query);
									}
								}
							}

							header('Location: ' . SERVERURL . '/terceros/lista/1');
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/terceros/importar2';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/terceros/lista/' . $_SESSION['TERCEROS']['Pagina'];
			
				$this->views->getView($this, 'importar2', $data);
			}
		}
	}
?>
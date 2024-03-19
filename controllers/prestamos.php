<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Prestamos extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/prestamos/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR OR $_SESSION['Login']['Perfil'] == RRHH OR $_SESSION['Login']['Perfil'] == RRHH_AUX)
				$_SESSION['Importar'] = SERVERURL . '/prestamos/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/prestamos/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['PRESTAMOS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['PRESTAMOS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['PRESTAMOS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['PRESTAMOS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['PRESTAMOS']['Filtro']))
			{
				$_SESSION['PRESTAMOS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['PRESTAMOS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['PRESTAMOS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['PRESTAMOS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['PRESTAMOS']['Orden'])) 
					$_SESSION['PRESTAMOS']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUXILIARES.Nombre';

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
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['PRESTAMOS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarPrestamos($query);
			$this->views->getView($this, 'prestamos', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/prestamos/actualizarAuxiliar';
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
			$_SESSION['Lista'] = SERVERURL . '/prestamos/lista/' . $_SESSION['PRESTAMOS']['Pagina'];

			$data = array(
				'reg' => array(
					'IdEmpleado' 	=> isset($_REQUEST['IdEmpleado']) ? $_REQUEST['IdEmpleado'] : 0,
					'IdConcepto' 	=> isset($_REQUEST['IdConcepto']) ? $_REQUEST['IdConcepto'] : 0,
					'TipoPrestamo' 	=> isset($_REQUEST['TipoPrestamo']) ? $_REQUEST['TipoPrestamo'] : 0,
					'Fecha' 		=> isset($_REQUEST['Fecha']) ? $_REQUEST['Fecha'] : '',
					'ValorPrestamo' => isset($_REQUEST['ValorPrestamo']) ? $_REQUEST['ValorPrestamo'] : 0,
					'ValorCuota' 	=> isset($_REQUEST['ValorCuota']) ? $_REQUEST['ValorCuota'] : 0,
					'Cuotas' 		=> isset($_REQUEST['Cuotas']) ? $_REQUEST['Cuotas'] : 0,
					'SaldoPrestamo' => isset($_REQUEST['SaldoPrestamo']) ? $_REQUEST['SaldoPrestamo'] : 0,
					'SaldoCuotas' 	=> isset($_REQUEST['SaldoCuotas']) ? $_REQUEST['SaldoCuotas'] : 0,
					'IdTercero' 	=> isset($_REQUEST['IdTercero']) ? $_REQUEST['IdTercero'] : 0,
					'Estado' 		=> isset($_REQUEST['Estado']) ? $_REQUEST['Estado'] : 0
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['IdEmpleado'])) 
			{
				if	( empty($data['reg']['IdEmpleado']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Empleado') . '</strong><br>';

				if	( empty($data['reg']['IdConcepto']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Concepto') . '</strong><br>';

				// if	( ! empty($data['reg']['IdEmpleado']) AND ! empty($data['reg']['IdConcepto']) )
				// {
				// 	$IdEmpleado = $data['reg']['IdEmpleado'];
				// 	$IdConcepto = $data['reg']['IdConcepto'];

				// 	$query = <<<EOD
				// 		SELECT PRESTAMOS.*
				// 			FROM PRESTAMOS 
				// 			WHERE PRESTAMOS.IdEmpleado = $IdEmpleado AND 
				// 				PRESTAMOS.IdConcepto = $IdConcepto;
				// 	EOD;

				// 	$reg = $this->model->buscarPrestamo($query);

				// 	if ($reg) 
				// 		$data['mensajeError'] .= '<strong>' . label('Préstamo') . '</strong> ' . label('ya existe') . '<br>';
				// }
	
				if	( empty($data['reg']['TipoPrestamo']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de préstamo') . '</strong><br>';
			
				if	( empty($data['reg']['Fecha']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha del préstamo') . '</strong><br>';
			
				if	( empty($data['reg']['ValorPrestamo']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Valor del préstamo') . '</strong><br>';
			
				// if	( empty($data['reg']['ValorCuota']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Valor de cuota') . '</strong><br>';
			
				// if	( empty($data['reg']['Cuotas']) )
				// 	$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Número de cuotas') . '</strong><br>';

				if (empty($data['reg']['ValorCuota']) AND empty($data['reg']['Cuotas']))
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Valor de cuota') . '</strong> ' . label('ó un') . ' <strong>' . label('Número de cuotas') . '</strong><br>';
				elseif (! empty($data['reg']['ValorCuota']))
				{
					$data['reg']['Cuotas'] = ceil($data['reg']['ValorPrestamo'] / $data['reg']['ValorCuota']);
					$data['reg']['SaldoCuotas'] = ceil($data['reg']['ValorPrestamo'] / $data['reg']['ValorCuota']);
				}
				elseif (! empty($data['reg']['Cuotas']))
				{
					$data['reg']['ValorCuota'] = round($data['reg']['ValorPrestamo'] / $data['reg']['Cuotas'], 0);
					$data['reg']['SaldoCuotas'] = $data['reg']['Cuotas'];
				}
			
				if	( $data['reg']['SaldoPrestamo'] <= 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Saldo del préstamo') . '</strong><br>';
				else
					$data['reg']['SaldoCuotas'] = ceil($data['reg']['SaldoPrestamo'] / $data['reg']['ValorCuota']);
				
				if	( $data['reg']['SaldoCuotas'] <= 0 )
					$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Cuotas pendientes') . '</strong><br>';
			
				if	( $data['reg']['Estado'] == 0 )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Estado del préstamo') . '</strong><br>';

				// if (empty($data['reg']['IdTercero']))
				// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tercero') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarPrestamo($data['reg']);

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
			if (isset($_REQUEST['IdEmpleado']))
			{
				$data = array(
					'reg' => array(
						'IdEmpleado' 	=> isset($_REQUEST['IdEmpleado']) ? $_REQUEST['IdEmpleado'] : 0,
						'IdConcepto' 	=> isset($_REQUEST['IdConcepto']) ? $_REQUEST['IdConcepto'] : 0,
						'TipoPrestamo' 	=> isset($_REQUEST['TipoPrestamo']) ? $_REQUEST['TipoPrestamo'] : 0,
						'Fecha' 		=> isset($_REQUEST['Fecha']) ? $_REQUEST['Fecha'] : '',
						'ValorPrestamo' => isset($_REQUEST['ValorPrestamo']) ? $_REQUEST['ValorPrestamo'] : 0,
						'ValorCuota' 	=> isset($_REQUEST['ValorCuota']) ? $_REQUEST['ValorCuota'] : 0,
						'Cuotas' 		=> isset($_REQUEST['Cuotas']) ? $_REQUEST['Cuotas'] : 0,
						'SaldoPrestamo' => isset($_REQUEST['SaldoPrestamo']) ? $_REQUEST['SaldoPrestamo'] : 0,
						'SaldoCuotas' 	=> isset($_REQUEST['SaldoCuotas']) ? $_REQUEST['SaldoCuotas'] : 0,
						'IdTercero' 	=> isset($_REQUEST['IdTercero']) ? $_REQUEST['IdTercero'] : 0,
						'Estado' 		=> isset($_REQUEST['Estado']) ? $_REQUEST['Estado'] : 0
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['IdEmpleado']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Empleado') . '</strong><br>';

				if	( empty($data['reg']['IdConcepto']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Concepto') . '</strong><br>';

				// if	( ! empty($data['reg']['IdEmpleado']) AND ! empty($data['reg']['IdConcepto']) )
				// {
				// 	$IdEmpleado = $data['reg']['IdEmpleado'];
				// 	$IdConcepto = $data['reg']['IdConcepto'];

				// 	$query = <<<EOD
				// 		SELECT PRESTAMOS.*
				// 			FROM PRESTAMOS 
				// 			WHERE PRESTAMOS.IdEmpleado = $IdEmpleado AND 
				// 				PRESTAMOS.IdConcepto = $IdConcepto AND 
				// 				PRESTAMOS.Id <> $id;
				// 	EOD;

				// 	$reg = $this->model->buscarPrestamo($query);

				// 	if ($reg) 
				// 		$data['mensajeError'] .= '<strong>' . label('Préstamo') . '</strong> ' . label('ya existe') . '<br>';
				// }
	
				if	( empty($data['reg']['TipoPrestamo']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de préstamo') . '</strong><br>';
			
				if	( empty($data['reg']['Fecha']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha del préstamo') . '</strong><br>';
			
				if	( empty($data['reg']['ValorPrestamo']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Valor del préstamo') . '</strong><br>';
			
				// if	( empty($data['reg']['ValorCuota']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Valor de cuota') . '</strong><br>';
			
				// if	( empty($data['reg']['Cuotas']) )
				// 	$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Número de cuotas') . '</strong><br>';

				if (empty($data['reg']['ValorCuota']) AND empty($data['reg']['Cuotas']))
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Valor de cuota') . '</strong> ' . label('ó un') . ' <strong>' . label('Número de cuotas') . '</strong><br>';
				elseif (! empty($data['reg']['ValorCuota']))
				{
					$data['reg']['Cuotas'] = ceil($data['reg']['ValorPrestamo'] / $data['reg']['ValorCuota']);
					$data['reg']['SaldoCuotas'] = ceil($data['reg']['ValorPrestamo'] / $data['reg']['ValorCuota']);
				}
				elseif (! empty($data['reg']['Cuotas']))
				{
					$data['reg']['ValorCuota'] = round($data['reg']['ValorPrestamo'] / $data['reg']['Cuotas'], 0);
					$data['reg']['SaldoCuotas'] = $data['reg']['Cuotas'];
				}
			
				if	( $data['reg']['SaldoPrestamo'] <= 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Saldo del préstamo') . '</strong><br>';
				else
					$data['reg']['SaldoCuotas'] = ceil($data['reg']['SaldoPrestamo'] / $data['reg']['ValorCuota']);
			
				if	( $data['reg']['SaldoCuotas'] <= 0 )
					$data['mensajeError'] .= label('Debe digitar') . ' <strong>' . label('Cuotas pendientes') . '</strong><br>';
			
				// if	( empty($data['reg']['IdTercero']) )
				// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tercero') . '</strong><br>';

				if	( $data['reg']['Estado'] = 0 )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Estado del préstamo') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarPrestamo($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/prestamos/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/prestamos/lista/' . $_SESSION['PRESTAMOS']['Pagina'];

				$query = 'SELECT * FROM PRESTAMOS WHERE PRESTAMOS.Id = ' . $id;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'Id' 			=> $reg['id'],
						'IdEmpleado' 	=> $reg['idempleado'],
						'IdConcepto' 	=> $reg['idconcepto'],
						'TipoPrestamo' 	=> $reg['tipoprestamo'],
						'Fecha' 		=> $reg['fecha'],
						'ValorPrestamo' => $reg['valorprestamo'],
						'ValorCuota' 	=> $reg['valorcuota'],
						'Cuotas' 		=> $reg['cuotas'],
						'SaldoPrestamo' => $reg['saldoprestamo'],
						'SaldoCuotas' 	=> $reg['saldocuotas'],
						'IdTercero' 	=> $reg['idtercero'],
						'Estado' 		=> $reg['estado']
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
				FROM PRESTAMOS
				WHERE PRESTAMOS.Id = $id
			EOD;
				
			$reg = $this->model->leer($query);

			$data = array(
				'reg' => array(
					'Id' 			=> $reg['id'],
					'IdEmpleado' 	=> $reg['idempleado'],
					'IdConcepto' 	=> $reg['idconcepto'],
					'TipoPrestamo' 	=> $reg['tipoprestamo'],
					'Fecha' 		=> $reg['fecha'],
					'ValorPrestamo' => $reg['valorprestamo'],
					'ValorCuota' 	=> $reg['valorcuota'],
					'Cuotas' 		=> $reg['cuotas'],
					'SaldoPrestamo' => $reg['saldoprestamo'],
					'SaldoCuotas' 	=> $reg['saldocuotas'],
					'IdTercero' 	=> $reg['idtercero'],
					'Estado' 		=> $reg['estado']
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['IdEmpleado']))
			{
				// $query = 'SELECT COUNT(*) AS Registros ' .
				// 		'FROM AUXILIARES ' .
				// 		'WHERE AUXILIARES.IdMayor = ' . $id;

				// $reg = $this->model->buscarMayor($query);

				// if ($reg['registros'] > 0) 
				// {
				// 	$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Concepto mayor') . '</strong><br>';
				// }

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarPrestamo($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/prestamos/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/prestamos/lista/' . $_SESSION['PRESTAMOS']['Pagina'];

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
			$_SESSION['Lista'] = SERVERURL . '/prestamos/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['PRESTAMOS']['Filtro'];

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

					$query .= "UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['PRESTAMOS']['Orden']; 
			$data['rows'] = $this->model->listarPrestamos($query);
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

							$EmpleadoAnt = '';
							$ConceptoAnt = '';
							$TipoPtmoAnt = '';
							$EstadoPtmoAnt = '';
		
							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
								{
									$Empleado 		= trim($oHoja->getCell('A' . $i)->getCalculatedValue());
									$NombreEmpleado = trim($oHoja->getCell('B' . $i)->getCalculatedValue());

									if ($Empleado <> $EmpleadoAnt) 
									{
										$EmpleadoAnt = $Empleado;

										$query = <<<EOD
											SELECT *
												FROM EMPLEADOS 
													INNER JOIN PARAMETROS 
														ON EMPLEADOS.Estado = PARAMETROS.Id 
												WHERE EMPLEADOS.Documento = '$Empleado' AND 
													PARAMETROS.Detalle = 'ACTIVO';
										EOD;

										$reg = $this->model->buscarPrestamo($query);

										if ($reg)
											$IdEmpleado = $reg['id'];
										else
											$data['mensajeError'] .= "Empleado no existe <strong>$Empleado ($NombreEmpleado)</strong><br>";
									}

									$Concepto 	= trim($oHoja->getCell('C' . $i)->getCalculatedValue());
									$Concepto	= str_pad($Concepto, 5, '0', STR_PAD_LEFT);
									$Mayor 		= substr($Concepto, 0, 2);
									$Auxiliar 	= substr($Concepto, 2, 3);

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

										$reg = $this->model->buscarPrestamo($query);

										if ($reg)
											$IdConcepto = $reg['id'];
										else
											$data['mensajeError'] .= "Concepto no existe <strong>$Concepto ($NombreEmpleado)</strong><br>";
									}

									$TipoPtmo = trim($oHoja->getCell('D' . $i)->getCalculatedValue());

									if ($TipoPtmo <> $TipoPtmoAnt) 
									{
										$TipoPrestamo = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPrestamo' AND PARAMETROS.Detalle = '$TipoPtmo'");

										if ($TipoPrestamo == 0)
											$data['mensajeError'] .= "Tipo de préstamo no existe <strong>$TipoPtmo ($NombreEmpleado)</strong><br>";
										else
											$TipoPtmoAnt = $TipoPtmo;
									}

									$Fecha 			= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('E' . $i)->getCalculatedValue())->format('Y-m-d');
									$ValorPrestamo 	= $oHoja->getCell('F' . $i)->getCalculatedValue();
									$ValorCuota 	= $oHoja->getCell('G' . $i)->getCalculatedValue();
									$Cuotas 		= $oHoja->getCell('H' . $i)->getCalculatedValue();

									if ($ValorCuota > 0 AND $ValorPrestamo / $ValorCuota) 
										$Cuotas = CEIL($ValorPrestamo / $ValorCuota);
									else
										$ValorCuota = ROUND($ValorPrestamo / $Cuotas, 0);

									$SaldoPrestamo 	= $oHoja->getCell('I' . $i)->getCalculatedValue();
									$SaldoCuotas 	= CEIL($SaldoPrestamo / $ValorCuota);
									
									$Tercero 		= trim($oHoja->getCell('J' . $i)->getCalculatedValue());
									$NombreTercero 	= trim($oHoja->getCell('K' . $i)->getCalculatedValue());

									// BUSCAR EL TERCERO Y SI NO EXISTE CREARLO
									if (! empty($Tercero))
									{
										$IdTercero = getId('TERCEROS', "TERCEROS.CodigoSAP = '$Tercero'");

										if ($IdTercero == 0)
										{
											$query = <<<EOD
												INSERT INTO TERCEROS 
													(COdigoSAP, Nombre, EsAcredor, AceptaPoliticaTD)
													VALUES (
														'$Tercero', 
														'$NombreTercero',
														1,
														1);
											EOD;

											$ok = $this->model->query($query);

											$IdTercero = getId('TERCEROS', "TERCEROS.CodigoSAP = '$Tercero'");
										}
									}

									if ($IdTercero == 0)
										$data['mensajeError'] .= "Tercero no existe <strong>$Tercero ($NombreTercero)</strong><br>";

									$EstadoPtmo = trim($oHoja->getCell('L' . $i)->getCalculatedValue());

									if ($EstadoPtmo <> $EstadoPtmoAnt) 
									{
										$EstadoPrestamo = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoPrestamo' AND PARAMETROS.Detalle = '$EstadoPtmo'");

										if ($EstadoPrestamo == 0)
											$data['mensajeError'] .= "Estado de préstamo no existe <strong>$EstadoPtmo ($NombreEmpleado)</strong><br>";
										else
											$EstadoPtmoAnt = $EstadoPtmo;
									}

									if (empty($data['mensajeError'])) 
									{
										$datos = array($IdEmpleado, $IdConcepto, $TipoPrestamo, $Fecha, $ValorPrestamo, $ValorCuota, $Cuotas, $SaldoPrestamo, $SaldoCuotas, $IdTercero, $EstadoPrestamo);

										$query = <<<EOD
											SELECT *
												FROM PRESTAMOS
												WHERE PRESTAMOS.IdEmpleado = $IdEmpleado AND 
													PRESTAMOS.IdConcepto = $IdConcepto AND 
													PRESTAMOS.IdTercero = $IdTercero;
										EOD;

										$reg = $this->model->buscarPrestamo($query);

										if ($reg) 
											$this->model->actualizarPrestamo($datos, $reg['id']);
										else
											$this->model->guardarPrestamo($datos);
									}
								}
							}

							if (! empty($data['mensajeError'])) 
								$this->views->getView($this, 'importar', $data);
							else
								header('Location: ' . SERVERURL . '/prestamos/lista/1');
					
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/prestamos/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/prestamos/lista/' . $_SESSION['PRESTAMOS']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>
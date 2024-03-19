<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class deduccionesRetFte extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/deduccionesRetFte/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR OR $_SESSION['Login']['Perfil'] == RRHH)
				$_SESSION['Importar'] = SERVERURL . '/deduccionesRetFte/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/deduccionesRetFte/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['DEDUCCIONESRETFTE']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['DEDUCCIONESRETFTE']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['DEDUCCIONESRETFTE']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['DEDUCCIONESRETFTE']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['DEDUCCIONESRETFTE']['Filtro']))
			{
				$_SESSION['DEDUCCIONESRETFTE']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['DEDUCCIONESRETFTE']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['DEDUCCIONESRETFTE']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['DEDUCCIONESRETFTE']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['DEDUCCIONESRETFTE']['Orden'])) 
					$_SESSION['DEDUCCIONESRETFTE']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

			$query = <<<EOD
                WHERE PARAMETROS1.Detalle = 'ACTIVO' AND 
                    (EMPLEADOS.CuotaVivienda > 0 OR 
                    EMPLEADOS.SaludYEducacion > 0 OR 
                    EMPLEADOS.DeduccionDependientes = 1) 
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

					$query .= ') ';
				}
			}

			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['DEDUCCIONESRETFTE']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarDeduccionesRetFte($query);
			$this->views->getView($this, 'deduccionesRetFte', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/deduccionesRetFte/actualizarNovedad';
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
			$_SESSION['Lista'] = SERVERURL . '/deduccionesRetFte/lista/' . $_SESSION['DEDUCCIONESRETFTE']['Pagina'];

			$data = array(
				'reg' => array(
					'IdEmpleado' 				=> 0, 
					'CuotaVivienda' 			=> 0, 
					'SaludYEducacion' 			=> 0, 
					'Alimentacion'				=> 0, 
					'DeduccionDependientes' 	=> 0, 
					'FechaInicialDeducciones' 	=> NULL, 
					'FechaFinalDeducciones' 	=> NULL
				),	
				'mensajeError' => ''
			);	

			if (isset($_REQUEST['Documento'])) 
			{
				$ValorUVTVivienda = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT' AND PARAMETROS.Detalle = 'VALOR UVT'")['valor'] * 120;
				$ValorUVTSalud = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT' AND PARAMETROS.Detalle = 'VALOR UVT'")['valor'] * 16;
				$ValorUVTAlimentacion = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT' AND PARAMETROS.Detalle = 'VALOR UVT'")['valor'] * 41;

				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$Documento = $_REQUEST['Documento'];
					$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");

					if ($IdEmpleado == 0) 
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
					else
						$data['reg']['IdEmpleado'] = $IdEmpleado;
				}

				if ($_REQUEST['CuotaVivienda'] < 0)
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuota de vivienda') . '</strong> ' . label('mayor o igual a cero') . '<br>';
				elseif ($_REQUEST['CuotaVivienda'] > $ValorUVTVivienda)
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuota de vivienda') . '</strong> ' . label('menor o igual a') . ' ' . $ValorUVTVivienda . '<br>';
				else
					$data['reg']['CuotaVivienda'] = $_REQUEST['CuotaVivienda'];
				
				if ($_REQUEST['SaludYEducacion'] < 0)
					$data['mensajeError'] .= label('Debe digitar un valor de') . ' <strong>' . label('Salud') . '</strong> ' . label('mayor o igual a cero') . '<br>';
				elseif ($_REQUEST['SaludYEducacion'] > $ValorUVTSalud)
					$data['mensajeError'] .= label('Debe digitar un valor de') . ' <strong>' . label('Salud') . '</strong> ' . label('menor o igual a') . ' ' . $ValorUVTSalud . '<br>';
				else
					$data['reg']['SaludYEducacion'] = $_REQUEST['SaludYEducacion'];

				if ($_REQUEST['Alimentacion'] < 0)
					$data['mensajeError'] .= label('Debe digitar un valor de') . ' <strong>' . label('Alimentación') . '</strong> ' . label('mayor o igual a cero') . '<br>';
				elseif ($_REQUEST['Alimentacion'] > $ValorUVTAlimentacion)
					$data['mensajeError'] .= label('Debe digitar un valor de') . ' <strong>' . label('Alimentación') . '</strong> ' . label('menor o igual a') . ' ' . $ValorUVTAlimentacion . '<br>';
				else
					$data['reg']['Alimentacion'] = $_REQUEST['Alimentacion'];

				if (isset($_REQUEST['DeduccionDependientes']))
					$data['reg']['DeduccionDependientes'] = 1;
				else
					$data['reg']['DeduccionDependientes'] = 0;

				if (is_null($data['reg']['FechaInicialDeducciones']))
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha inicial') . '</strong> <br>';

				if (is_null($data['reg']['FechaFinallDeducciones']))
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha final') . '</strong> <br>';

				if ($data['reg']['FechaInicialDeducciones'] >= $data['reg']['FechaFinalDeducciones'])
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha inicial') . '</strong> ' . label('menor que la') . ' <strong>' . label('Fecha final') . '</strong><br>';

				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'adicionar', $data);
					exit;
				}
				else
				{
					$ok = $this->model->adicionarDeduccionesRetFte($data['reg']);

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

		public function editar($Id)
		{
			$data = array(
				'reg' => array(
					'IdEmpleado' 				=> 0, 
					'CuotaVivienda' 			=> 0, 
					'SaludYEducacion' 			=> 0, 
					'Alimentacion'				=> 0, 
					'DeduccionDependientes' 	=> 0, 
					'FechaInicialDeducciones' 	=> NULL, 
					'FechaFinalDeducciones' 	=> NULL
				),	
				'mensajeError' => ''
			);	

			$reg = getRegistro('EMPLEADOS', $Id);

			if (isset($_REQUEST['Documento'])) 
			{
				$ValorUVTVivienda = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT' AND PARAMETROS.Detalle = 'VALOR UVT'")['valor'] * 120;
				$ValorUVTSalud = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT' AND PARAMETROS.Detalle = 'VALOR UVT'")['valor'] * 16;
				$ValorUVTAlimentacion = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT' AND PARAMETROS.Detalle = 'VALOR UVT'")['valor'] * 41;

				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$Documento = $_REQUEST['Documento'];
					$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");

					if ($IdEmpleado == 0) 
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
					else
						$data['reg']['IdEmpleado'] = $IdEmpleado;
				}

				if ($_REQUEST['CuotaVivienda'] < 0)
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuota de vivienda') . '</strong> ' . label('mayor o igual a cero') . '<br>';
				elseif ($_REQUEST['CuotaVivienda'] > $ValorUVTVivienda)
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuota de vivienda') . '</strong> ' . label('menor o igual a') . ' ' . $ValorUVTVivienda . '<br>';
				else
					$data['reg']['CuotaVivienda'] = $_REQUEST['CuotaVivienda'];
				
				if ($_REQUEST['SaludYEducacion'] < 0)
					$data['mensajeError'] .= label('Debe digitar un valor de') . ' <strong>' . label('Salud') . '</strong> ' . label('mayor o igual a cero') . '<br>';
				elseif ($_REQUEST['SaludYEducacion'] > $ValorUVTSalud)
					$data['mensajeError'] .= label('Debe digitar un valor de') . ' <strong>' . label('Salud') . '</strong> ' . label('menor o igual a') . ' ' . $ValorUVTSalud . '<br>';
				else
					$data['reg']['SaludYEducacion'] = $_REQUEST['SaludYEducacion'];

				if ($_REQUEST['Alimentacion'] < 0)
					$data['mensajeError'] .= label('Debe digitar un valor de') . ' <strong>' . label('Alimentación') . '</strong> ' . label('mayor o igual a cero') . '<br>';
				elseif ($_REQUEST['Alimentacion'] > $ValorUVTAlimentacion)
					$data['mensajeError'] .= label('Debe digitar un valor de') . ' <strong>' . label('Alimentación') . '</strong> ' . label('menor o igual a') . ' ' . $ValorUVTAlimentacion . '<br>';
				else
					$data['reg']['Alimentacion'] = $_REQUEST['Alimentacion'];

				if (isset($_REQUEST['DeduccionDependientes']))
					$data['reg']['DeduccionDependientes'] = 1;
				else
					$data['reg']['DeduccionDependientes'] = 0;
				
				if (is_null($data['reg']['FechaInicialDeducciones']))
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha inicial') . '</strong> <br>';

				if (is_null($data['reg']['FechaFinallDeducciones']))
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha final') . '</strong> <br>';

				if ($data['reg']['FechaInicialDeducciones'] >= $data['reg']['FechaFinalDeducciones'])
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha inicial') . '</strong> ' . label('menor que la') . ' <strong>' . label('Fecha final') . '</strong><br>';

				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'actualizar', $data);
					exit;
				}
				else
				{
					$ok = $this->model->actualizarDeduccionesRetFte($data['reg']);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/deducionesRetFte/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/deduccionesRetFte/lista/' . $_SESSION['DEDUCCIONESRETFTE']['Pagina'];

				$regEmp = getRegistro('EMPLEADOS', $Id);
				$regCargo = getRegistro('CARGOS', $regEmp['idcargo']);
				$regCentro = getRegistro('CENTROS', $regEmp['idcentro']);

				$_REQUEST['IdEmpleado'] 				= $Id;
				$_REQUEST['Documento']  				= $regEmp['documento'];
				$_REQUEST['NombreEmpleado'] 			= $regEmp['apellido1'] . ' ' . $regEmp['apellido2'] . ' ' . $regEmp['nombre1'] . ' ' . $regEmp['nombre2'];
				$_REQUEST['Cargo'] 						= $regCargo['nombre'];
				$_REQUEST['Centro'] 					= $regCentro['nombre'];
				$_REQUEST['CuotaVivienda'] 				= $regEmp['cuotavivienda'];
				$_REQUEST['SaludYEducacion']			= $regEmp['saludyeducacion'];
				$_REQUEST['Alimentacion'] 				= $regEmp['alimentacion'];
				$_REQUEST['DeduccionDependientes'] 		= $regEmp['deducciondependientes'];
				$_REQUEST['FechaInicialDeducciones'] 	= $regEmp['fechainicialdeducciones'];
				$_REQUEST['FechaFinalDeducciones'] 		= $regEmp['fechafinaldeducciones'];
	
				$this->views->getView($this, 'actualizar', $data);
				exit;
			}
		}

		public function borrar($Id)
		{
			$data = array(
				'reg' => array(
					'IdEmpleado' 				=> 0, 
					'CuotaVivienda' 			=> 0, 
					'SaludYEducacion' 			=> 0, 
					'Alimentacion'				=> 0, 
					'DeduccionDependientes' 	=> 0, 
					'FechaInicialDeducciones' 	=> NULL, 
					'FechaFinalDeducciones' 	=> NULL
				),	
				'mensajeError' => ''
			);	

			
			$regEmp = getRegistro('EMPLEADOS', $Id);

			if (isset($_REQUEST['Documento']))
			{
				if	( $data['mensajeError'] )
				{
					$this->views->getView($this, 'actualizar', $data);
					exit;
				}
				else
				{
					$resp = $this->model->borrarDeduccionesRetFte($Id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/deduccionesRetFte/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/deduccionesRetFte/lista/' . $_SESSION['DEDUCCIONESRETFTE']['Pagina'];

				$regEmp = getRegistro('EMPLEADOS', $Id);
				$regCargo = getRegistro('CARGOS', $regEmp['idcargo']);
				$regCentro = getRegistro('CENTROS', $regEmp['idcentro']);

				$_REQUEST['IdEmpleado'] 				= $Id;
				$_REQUEST['Documento']  				= $regEmp['documento'];
				$_REQUEST['NombreEmpleado'] 			= $regEmp['apellido1'] . ' ' . $regEmp['apellido2'] . ' ' . $regEmp['nombre1'] . ' ' . $regEmp['nombre2'];
				$_REQUEST['Cargo'] 						= $regCargo['nombre'];
				$_REQUEST['Centro'] 					= $regCentro['nombre'];
				$_REQUEST['CuotaVivienda'] 				= $regEmp['cuotavivienda'];
				$_REQUEST['SaludYEducacion']			= $regEmp['saludyeducacion'];
				$_REQUEST['Alimentacion'] 				= $regEmp['alimentacion'];
				$_REQUEST['DeduccionDependientes'] 		= $regEmp['deducciondependientes'];
				$_REQUEST['FechaInicialDeducciones'] 	= $regEmp['fechainicialdeducciones'];
				$_REQUEST['FechaFinalDeducciones'] 		= $regEmp['fechafinaldeducciones'];

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
			$_SESSION['Lista'] = SERVERURL . '/deduccionesRetFte/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['DEDUCCIONESRETFTE']['Filtro'];

			$query = <<<EOD
                WHERE PARAMETROS1.Detalle = 'ACTIVO' AND 
                    (EMPLEADOS.CuotaVivienda > 0 OR 
                    EMPLEADOS.SaludYEducacion > 0 OR 
                    EMPLEADOS.DeduccionDependientes = 1) 
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

					$query .= ') ';
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['DEDUCCIONESRETFTE']['Orden']; 
			$data['rows'] = $this->model->listarDeduccionesRetFte($query);
			$this->views->getView($this, 'informe', $data);
		}

		public function importar()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['Archivo']['name']) )
				{
					$data['mensajeError'] .= "Seleccione un <strong>Archivo en Excel</strong><br>";
				}
				else
				{
					ini_set('max_execution_time', 0);

					$query = <<<EOD
						UPDATE EMPLEADOS 
							SET 
								DeduccionDependientes = 0, 
								SaludYEducacion = 0, 
								CuotaVivienda = 0, 
								FechaInicialDeducciones = NULL, 
								FechaFinalDeducciones = NULL 
							FROM PARAMETROS 
							WHERE EMPLEADOS.Estado = PARAMETROS.Id AND 
								PARAMETROS.Detalle = 'ACTIVO';
					EOD;

					$ok = $this->model->query($query);
					
					$archivo = $_FILES['Archivo']['name'];
		
					if ( copy($_FILES['Archivo']['tmp_name'], $archivo)) 
					{
						if ( file_exists ($archivo) )
						{
							$oExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $oExcel->getSheet(0);

							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								$Documento 			= trim($oHoja->getCell('A' . $i)->getCalculatedValue());
								$NombreEmpleado 	= trim($oHoja->getCell('B' . $i)->getCalculatedValue());
								$Novedad 			= trim($oHoja->getCell('C' . $i)->getCalculatedValue());
								$FechaInicial		= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('D' . $i)->getCalculatedValue())->format('Y-m-d');
								$FechaFinal			= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('E' . $i)->getCalculatedValue())->format('Y-m-d');
								$Valor 				= $oHoja->getCell('F' . $i)->getCalculatedValue();

								$regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento'");

								if ($regEmpleado) 
									$IdEmpleado = $regEmpleado['id'];
								else
								{
									$data['mensajeError'] .= 'Empleado no existe (' . $Documento . ' - ' . $NombreEmpleado . ') <br>';
									continue;
								}

								switch ($Novedad)
								{
									case 'DEPENDIENTES':
										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET DeduccionDependientes = $Valor, 
													FechaInicialDeducciones = '$FechaInicial', 
													FechaFinalDeducciones = '$FechaFinal' 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;

										break;

									case 'SALUD':
										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET SaludYEducacion = $Valor, 
													FechaInicialDeducciones = '$FechaInicial', 
													FechaFinalDeducciones = '$FechaFinal' 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;

										break;

									case 'VIVIENDA':
										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET CuotaVivienda = $Valor,  
													FechaInicialDeducciones = '$FechaInicial', 
													FechaFinalDeducciones = '$FechaFinal' 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;

										break;
									
									default:
										$query = '';
										$data['mensajeError'] .= "Novedad no válida ($Documento - $NombreEmpleado) - $Novedad<br>";
										break;
								}

								if (! empty($query))
									$ok = $this->model->query($query);
							}

							if (! empty($data['mensajeError'])) 
								$this->views->getView($this, 'importar', $data);
							else
								header('Location: ' . SERVERURL . '/deduccionesRetFte/lista/1');
							
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/deduccionesRetFte/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/deduccionesRetFte/lista/' . $_SESSION['DEDUCCIONESRETFTE']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>
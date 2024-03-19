<?php
	require_once('./templates/vendor/autoload.php');
	require './templates/PHPMailer-master/src/PHPMailer.php';
	require './templates/PHPMailer-master/src/SMTP.php';
	require './templates/PHPMailer-master/src/Exception.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Entrevista1 extends Controllers
	{
		function __destruct()
		{
			// unset($_SESSION['Paso1']);
		}
		
		public function lista($pagina)
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
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['CANDIDATOS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['CANDIDATOS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['CANDIDATOS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['CANDIDATOS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['CANDIDATOS']['Filtro']))
			{
				$_SESSION['CANDIDATOS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['CANDIDATOS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['CANDIDATOS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['CANDIDATOS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['CANDIDATOS']['Orden'])) 
					$_SESSION['CANDIDATOS']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

			$documentopsicologo = $_SESSION['Login']['Documento']; 
			$IdSicologo = getId('empleados', "empleados.documento =  '$documentopsicologo'");

			unset($_SESSION['Paso1']);

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$query .= "WHERE (UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(EMPLEADOS.CodigoSAP, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CARGOS.Cargo, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CENTROS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(PROYECTOS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(PROYECTOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%') ";
				$query .= "AND PARAMETROS.Detalle NOT IN ('RETIRADO', 'CANDIDATO DESISTE') ";
			}
			else
				$query .= "WHERE PARAMETROS.Detalle NOT IN ('RETIRADO', 'CANDIDATO DESISTE') ";
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['CANDIDATOS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarCandidatos($query);
			$this->views->getView($this, 'candidatos', $data);
		}	
		
		public function editar($Id)
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
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/entrevista1/lista/' . $_SESSION['CANDIDATOS']['Pagina'];

			if (! isset($_SESSION['Paso1']))
				$_SESSION['Paso1'] = 1;

			$paso = $_SESSION['Paso1'];

			$data['mensajeError'] = '';

			$FechaEntrevista = date('Y-m-d');

			if (isset($_REQUEST['Action']))
			{
				if (substr($_REQUEST['Action'], 0, 5) == 'PASO_')
				{
					$_SESSION['Paso1'] = str_replace('_', '', substr($_REQUEST['Action'], -2)); 
					$paso = $_SESSION['Paso1'];
				}

				if (substr($_REQUEST['Action'], 0, 7) == 'BORRAR_')
				{
					$IdReferencia = str_replace('_', '', substr($_REQUEST['Action'], -2)); 

					$query= <<<EOD
						DELETE FROM REFERENCIASLABORALES 
							WHERE REFERENCIASLABORALES.Id = $IdReferencia;
					EOD;

					$this->model->query($query);
				}

				if ($_REQUEST['Action'] == 'FINALIZAR')
				{
					// SE GENERA EL DOCUMENTO DE ENTREVISTA SICOLOGICA Y SE ARCHIVA
					global $lcOrientacion;
					global $lcTitulo;
					global $lcSubTitulo;
					global $lcEncabezado;
					global $lcEncabezado2;
				
					$PDF = new PDF(); 
					$PDF->AliasNbPages();
		
					$lcTitulo = utf8_decode('ENTREVISTA PSICOLÓGICA');
					$lcOrientacion = 'P';
					$lcEncabezado = '';

					$PDF->AddFont('Tahoma','','tahoma.php');
					$PDF->AddPage($lcOrientacion);
					$PDF->SetFont('Tahoma', '', 8);

					$regEmpleado 		= getRegistro('EMPLEADOS', $Id);
					$regEntrevista 		= getRegistro('ENTREVISTASSICOLOGIA', 0, "ENTREVISTASSICOLOGIA.IdEmpleado = $Id");
					$regEntrevistador 	= getRegistro('EMPLEADOS', $regEntrevista['idsicologo']);
					$referencias 		= getTabla('REFERENCIASLABORALES', "REFERENCIASLABORALES.IdEmpleado = $Id", "REFERENCIASLABORALES.FechaIngreso DESC");

					$Documento = $regEmpleado['documento'];
					$NombreEmpleado = $regEmpleado['apellido1'] . ' ' . $regEmpleado['apellido2'] . ' ' . $regEmpleado['nombre1'] . ' ' . $regEmpleado['nombre2'];

					if ($regEmpleado['idcargo'] > 0)
						$NombreCargo = getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];
					else
						$NombreCargo = '';

					$NombreEntrevistador = $regEntrevistador['apellido1'] . ' ' . $regEntrevistador['apellido2'] . ' ' . $regEntrevistador['nombre1'] . ' ' . $regEntrevistador['nombre2'];

					$PDF->Cell(50, 5, utf8_decode('FECHA DE ENTREVISTA:'), 0, 0, 'L'); 
					$PDF->Cell(25, 5, $regEntrevista['fecha'], 0, 0, 'L'); 
					$PDF->Ln(); 

					$PDF->Cell(50, 5, utf8_decode('DOCUMENTO IDENTIFICACIÓN:'), 0, 0, 'L'); 
					$PDF->Cell(25, 5, $regEmpleado['documento'], 0, 0, 'L'); 
					$PDF->Ln(); 

					$PDF->Cell(50, 5, utf8_decode('NOMBRE DEL CANDIDATO:'), 0, 0, 'L'); 
					$PDF->Cell(60, 5, utf8_decode($NombreEmpleado), 0, 0, 'L'); 
					$PDF->Ln(); 

					$PDF->Cell(50, 5, utf8_decode('CARGO POSTULACIÓN:'), 0, 0, 'L'); 
					$PDF->Cell(60, 5, utf8_decode($NombreCargo), 0, 0, 'L'); 
					$PDF->Ln(); 

					$PDF->Cell(50, 5, utf8_decode('NOMBRE DEL ENTREVISTADOR:'), 0, 0, 'L'); 
					$PDF->Cell(60, 5, utf8_decode($NombreEntrevistador), 0, 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 

					$PDF->SetTextColor(0,0,255);
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(0, 5, utf8_decode('INFORMACIÓN OBTENIDA EN LA ENTREVISTA PSICOLÓGICA'), 0, 0, 'C'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->SetTextColor(0,0,0);
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('FORTALEZAS / ASPECTOS A MEJORAR'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->MultiCell(0, 5, utf8_decode($regEntrevista['fortalezas']), 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('PROYECCIÓN / METAS'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->MultiCell(0, 5, utf8_decode($regEntrevista['proyeccion']), 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('DINÁMICA FAMILIAR'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->MultiCell(0, 5, utf8_decode($regEntrevista['dinamicafamiliar']), 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('VALORES INCULCADOS'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->MultiCell(0, 5, utf8_decode($regEntrevista['valoresinculcados']), 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('PRINCIPALES LOGROS'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->MultiCell(0, 5, utf8_decode($regEntrevista['logrosacademicos']), 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('MOTIVACIONES Y EXPECTATIVAS HACIA LA EMPRESA Y EL CARGO'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->MultiCell(0, 5, utf8_decode($regEntrevista['motivacionlaboral']), 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('DISPONIBILIDAD LABORAL'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					if ($regEntrevista['disponibilidadTC'] == 1)
						$PDF->Cell(150, 5, utf8_decode('DISPONIBLE para trabajar tiempo completo'), 0, 0, 'L'); 
					else
						$PDF->Cell(150, 5, utf8_decode('NO DISPONIBLE para trabajar tiempo completo'), 0, 0, 'L'); 
					$PDF->Ln(); 

					if ($regEntrevista['disponibilidadFS'] == 1)
						$PDF->Cell(150, 5, utf8_decode('DISPONIBLE para trabajar en fines de semana'), 0, 0, 'L'); 
					else
						$PDF->Cell(150, 5, utf8_decode('NO DISPONIBLE para trabajar en fines de semana'), 0, 0, 'L'); 
					$PDF->Ln(); 

					if ($regEntrevista['disponibilidadTR'] == 1)
						$PDF->Cell(150, 5, utf8_decode('DISPONIBLE para trabajar en turnos rotativos'), 0, 0, 'L'); 
					else
						$PDF->Cell(150, 5, utf8_decode('NO DISPONIBLE para trabajar en turnos rotativos'), 0, 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('REFERENCIAS LABORALES'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->Ln(); 
					$PDF->Cell(60, 5, utf8_decode('EMPRESA / REFERENTE'), 0, 0, 'L'); 
					$PDF->Cell(20, 5, utf8_decode('TELÉFONO'), 0, 0, 'L'); 
					$PDF->Cell(20, 5, utf8_decode('FECHA ING.'), 0, 0, 'L'); 
					$PDF->Cell(20, 5, utf8_decode('FECHA RET.'), 0, 0, 'L'); 
					$PDF->Cell(20, 5, utf8_decode('CARGO EMP. / REF.'), 0, 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 

					for ($i = 0; $i < count($referencias); $i++)
					{
						$regReferencia = $referencias[$i];

						$PDF->Cell(60, 5, utf8_decode($regReferencia['empresa']), 0, 0, 'L'); 
						$PDF->Cell(20, 5, utf8_decode(''), 0, 0, 'L'); 
						$PDF->Cell(20, 5, utf8_decode($regReferencia['fechaingreso']), 0, 0, 'L'); 
						$PDF->Cell(20, 5, utf8_decode($regReferencia['fecharetiro']), 0, 0, 'L'); 
						$PDF->Cell(60, 5, utf8_decode($regReferencia['cargoempleado']), 0, 0, 'L'); 
						$PDF->Ln(); 
						$PDF->Cell(60, 5, utf8_decode($regReferencia['nombrereferente']), 0, 0, 'L'); 
						$PDF->Cell(20, 5, utf8_decode($regReferencia['telefono']), 0, 0, 'L'); 
						$PDF->Cell(40, 5, utf8_decode(''), 0, 0, 'L'); 
						$PDF->Cell(60, 5, utf8_decode($regReferencia['cargoreferente']), 0, 0, 'L'); 
						$PDF->Ln(); 
						$PDF->MultiCell(0, 5, 'MOTIVO RETIRO: ' . utf8_decode($regReferencia['motivoretiro']), 0, 'L'); 
						$PDF->MultiCell(0, 5, 'OBSERVACIONES: ' . utf8_decode($regReferencia['observaciones']), 0, 'L'); 
						$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					}

					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->Ln(1); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->Ln(); 
					$PDF->Ln(); 

					$PDF->SetFont('Tahoma', '', 7);
					$PDF->MultiCell(180, 5, utf8_decode("Este documento es propiedad de COMWARE S.A., es para consulta y uso de todos sus procesos y proyectos. No se permite su reproducción o modificación sin la debida autorización, conforme a lo establecido en el instructivo para la gestión de la información documentada. Si este documento está impreso, NO se considera vigente. Los documentos vigentes están en la herramienta DocManager"), 0, 'C'); 

					$cDirectorio = str_replace(" ","",'documents/' . $regEmpleado['documento'] . '_' . strtoupper($regEmpleado['apellido1']) . '_' . strtoupper($regEmpleado['apellido2']) . '_' . strtoupper($regEmpleado['nombre1']) . '_' . strtoupper($regEmpleado['nombre2']));

					if	( ! is_dir($cDirectorio) )
						mkdir($cDirectorio);

					$cDirectorio .= '/PRUEBAS_SICOTECNICAS';

					if	( ! is_dir($cDirectorio) )
						mkdir($cDirectorio);
			
					$PDF->Output('F', $cDirectorio . '/' . $regEmpleado['documento'] . '_' . $regEntrevista['fecha'] . '_ENTREVISTA_PSICOLOGICA.PDF', TRUE); 

					$Estado = getRegistro('PARAMETROS', 0, "PARAMETROS.parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ENTREVISTA PSICOLOGÍA REALIZADA'")['id'];

					$query = <<<EOD
						UPDATE EMPLEADOS 
							SET SEL_EntrevistaSicologica = 1 
							WHERE EMPLEADOS.id = $Id;
					EOD;

					$this->model->query($query);

					unset($_SESSION['Paso1']);

					header('Location: ' . SERVERURL . '/candidatos/lista/1');
					exit();
				}

				if ($_REQUEST['Action'] == 'GUARDAR'){	
					if (empty($_REQUEST['Empresa']))
									$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Empresa') . '</strong><br>';

								if (empty($_REQUEST['NombreReferente']))
									$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Referente') . '</strong><br>';

								if (empty($_REQUEST['CargoReferente']))
									$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Cargo de referente') . '</strong><br>';

								if (empty($_REQUEST['Telefono']))
									$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Teléfono') . '</strong><br>';

								if (empty($_REQUEST['FechaIngreso']))
									$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de ingreso') . '</strong><br>';

								if (empty($_REQUEST['FechaRetiro']))
									$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de retiro') . '</strong><br>';

								if (empty($_REQUEST['CargoEmpleado']))
									$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Cargo del empleado') . '</strong><br>';

								if (empty($_REQUEST['MotivoRetiro']))
									$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Motivo de retiro') . '</strong><br>';

								if (empty($_REQUEST['Observaciones']))
									$data['mensajeError'] .= label('Debe digitar unas') . ' <strong>' . label('Observaciones') . '</strong><br>';

								if	( empty($data['mensajeError']) )
								{
									$Empresa 			= $_REQUEST['Empresa'];
									$NombreReferente 	= $_REQUEST['NombreReferente'];
									$CargoReferente		= $_REQUEST['CargoReferente'];
									$Telefono			= $_REQUEST['Telefono'];
									$FechaIngreso		= $_REQUEST['FechaIngreso'];
									$FechaRetiro		= $_REQUEST['FechaRetiro'];
									$CargoEmpleado		= $_REQUEST['CargoEmpleado'];
									$MotivoRetiro		= $_REQUEST['MotivoRetiro'];
									$Observaciones		= $_REQUEST['Observaciones'];
									$documentopsicologo = $_SESSION['Login']['Documento']; 
									$IdSicologo = getId('empleados', "empleados.documento =  '$documentopsicologo'");

									$query = <<<EOD
										INSERT INTO REFERENCIASLABORALES 
											(IdEmpleado, Fecha, Empresa, NombreReferente, CargoReferente, Telefono, FechaIngreso, FechaRetiro, CargoEmpleado, MotivoRetiro, Observaciones, IdSicologo) 
											VALUES (
												$Id, 
												'$FechaEntrevista', 
												'$Empresa', 
												'$NombreReferente', 
												'$CargoReferente', 
												'$Telefono', 
												'$FechaIngreso', 
												'$FechaRetiro', 
												'$CargoEmpleado', 
												'$MotivoRetiro', 
												'$Observaciones', 
												$IdSicologo);
									EOD;

									$this->model->query($query);
								}
				}

				

				if ($_REQUEST['Action'] == 'AVANZAR')
				{
					switch ($paso)
					{
						case 1:
							if	( empty($_REQUEST['Fortalezas']) )
								$data['mensajeError'] .= label('Debe digitar unas') . ' <strong>' . label('Fortalezas / Aspectos a mejorar') . '</strong><br>';

							if	( empty($_REQUEST['Proyeccion']) )
								$data['mensajeError'] .= label('Debe digitar unas') . ' <strong>' . label('Proyecciones / Metas') . '</strong><br>';

							if (empty($data['mensajeError']))
							{
								$Fortalezas = $_REQUEST['Fortalezas'];
								$Proyeccion = $_REQUEST['Proyeccion'];

								$IdEntrevista = getId('ENTREVISTASSICOLOGIA', "ENTREVISTASSICOLOGIA.IdEmpleado = $Id AND ENTREVISTASSICOLOGIA.Fecha = '$FechaEntrevista'");

								if ($IdEntrevista)
								{
									$query = <<<EOD
										UPDATE ENTREVISTASSICOLOGIA 
											SET 
												Fortalezas = '$Fortalezas', 
												Proyeccion = '$Proyeccion'  
											WHERE ENTREVISTASSICOLOGIA.Id = $IdEntrevista;
									EOD;
								}
								else
								{
									$documentopsicologo = $_SESSION['Login']['Documento']; 
									$IdSicologo = getId('empleados', "empleados.documento =  '$documentopsicologo'");
									// $IdSicologo = $_SESSION['Login']['Id'];

									$query = <<<EOD
										INSERT INTO ENTREVISTASSICOLOGIA 
											(IdEmpleado, Fecha, Fortalezas, Proyeccion, IdSicologo) 
											VALUES 
											($Id, '$FechaEntrevista', '$Fortalezas', '$Proyeccion', $IdSicologo);
									EOD;
								}

								$this->model->query($query);
							}

							break;

						case 2:
							if	( empty($_REQUEST['DinamicaFamiliar']) )
								$data['mensajeError'] .= label('Debe digitar unas') . ' <strong>' . label('Dinámicas familiares') . '</strong><br>';

							if	( empty($_REQUEST['ValoresInculcados']) )
								$data['mensajeError'] .= label('Debe digitar unos') . ' <strong>' . label('Valores inculcados') . '</strong><br>';

							if (empty($data['mensajeError']))
							{
								$DinamicaFamiliar = $_REQUEST['DinamicaFamiliar'];
								$ValoresInculcados = $_REQUEST['ValoresInculcados'];

								$IdEntrevista = getId('ENTREVISTASSICOLOGIA', "ENTREVISTASSICOLOGIA.IdEmpleado = $Id AND ENTREVISTASSICOLOGIA.Fecha = '$FechaEntrevista'");

								$query = <<<EOD
									UPDATE ENTREVISTASSICOLOGIA 
										SET 
											DinamicaFamiliar = '$DinamicaFamiliar', 
											ValoresInculcados = '$ValoresInculcados'  
										WHERE ENTREVISTASSICOLOGIA.Id = $IdEntrevista;
								EOD;

								$this->model->query($query);
							}

							break;

						case 3:
							if	( empty($_REQUEST['LogrosAcademicos']) )
								$data['mensajeError'] .= label('Debe digitar unos') . ' <strong>' . label('Logros académicos') . '</strong><br>';

							if	( empty($_REQUEST['MotivacionLaboral']) )
								$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Motivación laboral') . '</strong><br>';

							if (empty($data['mensajeError']))
							{
								$LogrosAcademicos = $_REQUEST['LogrosAcademicos'];
								$MotivacionLaboral = $_REQUEST['MotivacionLaboral'];
								$DisponibilidadTC = isset($_REQUEST['DisponibilidadTC']) ? 1 : 0;
								$DisponibilidadFS = isset($_REQUEST['DisponibilidadFS']) ? 1 : 0;
								$DisponibilidadTR = isset($_REQUEST['DisponibilidadTR']) ? 1 : 0;

								$IdEntrevista = getId('ENTREVISTASSICOLOGIA', "ENTREVISTASSICOLOGIA.IdEmpleado = $Id AND ENTREVISTASSICOLOGIA.Fecha = '$FechaEntrevista'");

								$query = <<<EOD
									UPDATE ENTREVISTASSICOLOGIA 
										SET 
											LogrosAcademicos = '$LogrosAcademicos', 
											MotivacionLaboral = '$MotivacionLaboral', 
											DisponibilidadTC = $DisponibilidadTC, 
											DisponibilidadFS = $DisponibilidadFS, 
											DisponibilidadTR = $DisponibilidadTR 
										WHERE ENTREVISTASSICOLOGIA.Id = $IdEntrevista;
								EOD;

								$this->model->query($query);
							}

							break;

						case 4:

							

							break;

					}

					if (empty($data['mensajeError']))
					{
						$paso++;
						$_SESSION['Paso1'] = $paso;
						$data['mensajeError'] = '';
						$_REQUEST['Action'] = '';
					}
				}

				if ($_REQUEST['Action'] == 'RETROCEDER')
				{
					$paso--;
					$_SESSION['Paso1'] = $paso;
					$_REQUEST['Action'] = '';
				}
			}

			$regEntrevista = getRegistro('ENTREVISTASSICOLOGIA', 0, "ENTREVISTASSICOLOGIA.IdEmpleado = $Id AND ENTREVISTASSICOLOGIA.Fecha <= '$FechaEntrevista'");

			if ($regEntrevista)
			{
				switch ($paso)
				{
					case 1:
						$data['reg'] = array(
							'Id'				=> $regEntrevista['id'], 
							'IdEmpleado'		=> $Id, 
							'Fortalezas' 		=> $regEntrevista['fortalezas'],
							'Proyeccion' 		=> $regEntrevista['proyeccion']
						);

						break;

					case 2;
						$data['reg'] = array(
							'Id'				=> $regEntrevista['id'],
							'IdEmpleado'		=> $Id, 
							'DinamicaFamiliar' 	=> $regEntrevista['dinamicafamiliar'],
							'ValoresInculcados' => $regEntrevista['valoresinculcados']
						);

						break;

					case 3:
						$data['reg'] = array(
							'Id'				=> $regEntrevista['id'],
							'IdEmpleado'		=> $Id, 
							'LogrosAcademicos' 	=> $regEntrevista['logrosacademicos'],
							'MotivacionLaboral' => $regEntrevista['motivacionlaboral'],
							'DisponibilidadTC' 	=> $regEntrevista['disponibilidadTC'],
							'DisponibilidadFS' 	=> $regEntrevista['disponibilidadFS'],
							'DisponibilidadTR' 	=> $regEntrevista['disponibilidadTR']
						);

						break;

					case 4:
						$data['reg'] = array(
							'Id'				=> $regEntrevista['id'],
							'IdEmpleado'		=> $Id, 
							'Empresa' 			=> '',
							'NombreReferente' 	=> '',
							'CargoReferente'	=> '',
							'Telefono' 			=> '',
							'FechaIngreso' 		=> NULL,
							'FechaRetiro' 		=> NULL,
							'CargoEmpleado'		=> '',
							'MotivoRetiro'		=> '',
							'Observaciones' 	=> ''
						);
						
						$query = <<<EOD
							SELECT REFERENCIASLABORALES.*, 
									USUARIOS.Nombre 
								FROM REFERENCIASLABORALES 
									INNER JOIN USUARIOS 
										ON REFERENCIASLABORALES.IdSicologo = USUARIOS.Id 
								WHERE REFERENCIASLABORALES.IdEmpleado = $Id;
						EOD;

						$referencias = $this->model->listar($query);

						if ($referencias)
						{
							for ($i = 0; $i < count($referencias); $i++)
							{
								$regReferencia = $referencias[$i];

								$data['referencias'][$i] = array(
									'Id'				=> $regReferencia['id'],
									'IdEmpleado'		=> $Id, 
									'Fecha'				=> $regReferencia['fecha'], 
									'Empresa'			=> $regReferencia['empresa'], 
									'NombreReferente'	=> $regReferencia['nombrereferente'], 
									'CargoReferente'	=> $regReferencia['cargoreferente'], 
									'Telefono'			=> $regReferencia['telefono'], 
									'FechaIngreso'		=> $regReferencia['fechaingreso'],
									'FechaRetiro'		=> $regReferencia['fecharetiro'],
									'CargoEmpleado'		=> $regReferencia['cargoempleado'], 
									'MotivoRetiro'		=> $regReferencia['motivoretiro'], 
									'Observaciones'		=> $regReferencia['observaciones'], 
									'Sicologo'			=> $regReferencia['Nombre']
								);
							}
						}
						else
							$data['referencias'] = false;

						break;

					case 5:
						$data['reg'] = array(
							'Id'				=> $regEntrevista['id'],
							'IdEmpleado'		=> $Id
						);

						break;
				}

				// $data['mensajeError'] = '';
			}
			else
			{
				switch ($paso)
				{
					case 1:
						$data['reg'] = array(
							'Id'				=> 0,
							'IdEmpleado'		=> $Id, 
							'Fortalezas' 		=> '',
							'Proyeccion' 		=> ''
						);

						break;

					case 2:
						$data['reg'] = array(
							'Id'				=> 0,
							'IdEmpleado'		=> $Id, 
							'DinamicaFamiliar' 	=> '',
							'ValoresInculcados' => ''
						);

						break;

					case 3:
						$data['reg'] = array(
							'Id'				=> 0,
							'IdEmpleado'		=> $Id, 
							'LogrosAcademicos' 	=> '',
							'MotivacionLaboral' => '',
							'DisponibilidadTC' 	=> '',
							'DisponibilidadFS' 	=> '',
							'DisponibilidadTR' 	=> ''
						);

						break;

					case 4:
						$data['reg'] = array(
							'Id'				=> 0,
							'IdEmpleado'		=> $Id, 
							'Empresa'			=> '', 
							'NombreReferente'	=> '', 
							'CargoReferente'	=> '', 
							'Telefono'			=> '', 
							'FechaIngreso'		=> NULL, 
							'FechaRetiro'		=> NULL, 
							'CargoEmpleado'		=> '',
							'MotivoRetiro'		=> '',
							'Observaciones'		=> ''
						);

						$data['referencias'] = false;

						break;

					case 5:
						$data['reg'] = array(
							'Id'				=> 0,
							'IdEmpleado'		=> $Id
						);

						break;
				}

				$data['mensajeError'] = '';
			}

			$this->views->getView($this, 'actualizar', $data);
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
			$_SESSION['Lista'] = SERVERURL . '/candidatos/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['CANDIDATOS']['Filtro'];

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
					$query .= "UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['CANDIDATOS']['Orden']; 
			$data['rows'] = $this->model->listarCandidatos($query);
			$this->views->getView($this, 'informe', $data);
		}
	}
?>
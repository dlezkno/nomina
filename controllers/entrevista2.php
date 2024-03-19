<?php
	require_once('./templates/vendor/autoload.php');
	require './templates/PHPMailer-master/src/PHPMailer.php';
	require './templates/PHPMailer-master/src/SMTP.php';
	require './templates/PHPMailer-master/src/Exception.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Entrevista2 extends Controllers
	{
		function __destruct()
		{
			// unset($_SESSION['Paso2']);
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


		
			$documentoEntrevistador = $_SESSION['Login']['Documento']; 
			$IdEntrevistador = getId('empleados', "empleados.documento =  '$documentoEntrevistador'");
			

			unset($_SESSION['Paso2']);

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
			$_SESSION['Lista'] = SERVERURL . '/entrevista2/lista/' . $_SESSION['CANDIDATOS']['Pagina'];

			if (! isset($_SESSION['Paso2']))
				$_SESSION['Paso2'] = 1;

			$paso = $_SESSION['Paso2'];

			$data['mensajeError'] = '';

			$FechaEntrevista = date('Y-m-d');

			if (isset($_REQUEST['Action']))
			{
				if (substr($_REQUEST['Action'], 0, 5) == 'PASO_')
				{
					$_SESSION['Paso2'] = str_replace('_', '', substr($_REQUEST['Action'], -2)); 
					$paso = $_SESSION['Paso2'];
				}

				if ($_REQUEST['Action'] == 'FINALIZAR')
				{
					// SE GENERA EL DOCUMENTO DE ENTREVISTA TECNICA Y SE ARCHIVA
					global $lcOrientacion;
					global $lcTitulo;
					global $lcSubTitulo;
					global $lcEncabezado;
					global $lcEncabezado2;
				
					$PDF = new PDF(); 
					$PDF->AliasNbPages();
		
					$lcTitulo = utf8_decode('ENTREVISTA TÉCNICA');
					$lcOrientacion = 'P';
					$lcEncabezado = '';

					$PDF->AddFont('Tahoma','','tahoma.php');
					$PDF->AddPage($lcOrientacion);
					$PDF->SetFont('Tahoma', '', 8);

					$regEmpleado 		= getRegistro('EMPLEADOS', $Id);
					$regEntrevista 		= getRegistro('ENTREVISTASTECNICAS', 0, "ENTREVISTASTECNICAS.IdEmpleado = $Id");
					$regEntrevistador 	= getRegistro('EMPLEADOS', $regEntrevista['identrevistador']);

					$Documento = $regEmpleado['documento'];
					$NombreEmpleado = $regEmpleado['apellido1'] . ' ' . $regEmpleado['apellido2'] . ' ' . $regEmpleado['nombre1'] . ' ' . $regEmpleado['nombre2'];

					if ($regEmpleado['idcargo'] > 0)
						$NombreCargo = getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];
					else
						$NombreCargo = '';

					$NombreEntrevistador = $regEntrevistador['apellido1'] . ' ' . $regEntrevistador['apellido2'] . ' ' . $regEntrevistador['nombre1'] . ' ' . $regEntrevistador['nombre2'];

					$PuntajeTotal = $regEntrevista['puntajeveracidad'] + $regEntrevista['puntajeexperiencia'] + $regEntrevista['puntajeconocimiento'] + $regEntrevista['puntajecompetencias'];

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
					$PDF->Cell(0, 5, utf8_decode('INFORMACIÓN OBTENIDA EN LA ENTREVISTA TÉCNICA'), 0, 0, 'C'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->SetTextColor(0,0,0);
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('COHERENCIA DE LA HOJA DE VIDA CON LA ENTREVISTA'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Cell(30, 5, utf8_decode('Máximo 20 pts.'), 0, 0, 'R'); 
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(170, 5, utf8_decode($regEntrevista['puntajeveracidad']), 0, 0, 'R'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->MultiCell(150, 5, utf8_decode($regEntrevista['veracidadHV']), 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('EXPERIENCIA LABORAL'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Cell(30, 5, utf8_decode('Máximo 30 pts.'), 0, 0, 'R'); 
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(170, 5, utf8_decode($regEntrevista['puntajeexperiencia']), 0, 0, 'R'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->MultiCell(150, 5, utf8_decode($regEntrevista['experiencialaboral']), 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('CONOCIMIENTOS TÉCNICOS'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Cell(30, 5, utf8_decode('Máximo 30 pts.'), 0, 0, 'R'); 
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(170, 5, utf8_decode($regEntrevista['puntajeconocimiento']), 0, 0, 'R'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->MultiCell(150, 5, utf8_decode($regEntrevista['conocimientotecnico']), 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('COMPETENCIAS'), 0, 0, 'L'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Cell(30, 5, utf8_decode('Máximo 20 pts.'), 0, 0, 'R'); 
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(170, 5, utf8_decode($regEntrevista['puntajecompetencias']), 0, 0, 'R'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->MultiCell(150, 5, utf8_decode($regEntrevista['competencias']), 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(150, 5, utf8_decode('PUNTAJE TOTAL'), 0, 0, 'L'); 
					$PDF->Cell(20, 5, utf8_decode($PuntajeTotal), 0, 0, 'R'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->Ln(1); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->Ln(); 
					$PDF->Ln(); 

					$PDF->SetTextColor(0,0,255);
					$PDF->SetFont('Tahoma', '', 12);
					$PDF->Cell(0, 5, utf8_decode('CONCEPTO'), 0, 0, 'C'); 
					$PDF->SetFont('Tahoma', '', 8);
					$PDF->SetTextColor(0,0,0);
					$PDF->Ln(); 
					if ($regEntrevista['recomendado'] == 1)
						$PDF->Cell(50, 5, utf8_decode('EL CANDIDATO ES RECOMENDADO PARA EL CARGO: SI'), 0, 0, 'L'); 
					else
						$PDF->Cell(50, 5, utf8_decode('EL CANDIDATO ES RECOMENDADO PARA EL CARGO: NO'), 0, 0, 'L'); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->MultiCell(180, 5, 'ARGUMENTOS: ' . utf8_decode($regEntrevista['argumentos']), 0, 'L'); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->Ln(); 
					$PDF->Ln(); 
					$PDF->SetFont('Tahoma', '', 7);
					$PDF->MultiCell(180, 5, utf8_decode("Este documento es propiedad de COMWARE S.A., es para consulta y uso de todos sus procesos y proyectos. No se permite su reproducción o modificación sin la debida autorización, conforme a lo establecido en el instructivo para la gestión de la información documentada. Si este documento está impreso, NO se considera vigente. Los documentos vigentes están en la herramienta DocManager"), 0, 'C'); 

					$cDirectorio = str_replace(" ","",'documents/' . trim($regEmpleado['documento']) . '_' . strtoupper(trim($regEmpleado['apellido1']) . '_' . trim($regEmpleado['apellido2']) . '_' . trim($regEmpleado['nombre1']) . '_' . trim($regEmpleado['nombre2'])));

					if	( ! is_dir($cDirectorio) )
						mkdir($cDirectorio);

					$cDirectorio .= '/PRUEBAS_SICOTECNICAS';

					if	( ! is_dir($cDirectorio) )
						mkdir($cDirectorio);
			
					$PDF->Output('F', $cDirectorio . '/' . $regEmpleado['documento'] . '_' . $regEntrevista['fecha'] . '_ENTREVISTA_TECNICA.PDF', TRUE); 

					$Estado = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ENTREVISTA TÉCNICA REALIZADA'")['id'];

					$query = <<<EOD
						UPDATE EMPLEADOS 
							SET 
								SEL_EntrevistaTecnica = 1  
							WHERE EMPLEADOS.id = $Id;
					EOD;

					$this->model->query($query);

					unset($_SESSION['Paso2']);

					header('Location: ' . SERVERURL . '/candidatos/lista/1');
					exit();
				}

				if ($_REQUEST['Action'] == 'AVANZAR')
				{
					switch ($paso)
					{
						case 1:
							if	( empty($_REQUEST['VeracidadHV']) )
								$data['mensajeError'] .= label('Debe digitar unas observaciones de') . ' <strong>' . label('Veracidad hoja de vida') . '</strong><br>';

							if	( $_REQUEST['PuntajeVeracidad'] < 1 OR $_REQUEST['PuntajeVeracidad'] > 20)
								$data['mensajeError'] .= label('Debe digitar un puntaje entre 1 y 20') . '<br>';

							if (empty($data['mensajeError']))
							{
								$VeracidadHV = $_REQUEST['VeracidadHV'];
								$PuntajeVeracidad = $_REQUEST['PuntajeVeracidad'];

								$IdEntrevista = getId('ENTREVISTASTECNICAS', "ENTREVISTASTECNICAS.IdEmpleado = $Id AND ENTREVISTASTECNICAS.Fecha = '$FechaEntrevista'");

								if ($IdEntrevista)
								{
									$query = <<<EOD
										UPDATE ENTREVISTASTECNICAS 
											SET 
												VeracidadHV = '$VeracidadHV', 
												PuntajeVeracidad = $PuntajeVeracidad 
											WHERE ENTREVISTASTECNICAS.Id = $IdEntrevista;
									EOD;
								}
								else
								{
									$documentoEntrevistador = $_SESSION['Login']['Documento']; 
									$IdEntrevistador = getId('empleados', "empleados.documento =  '$documentoEntrevistador'");

									$query = <<<EOD
										INSERT INTO ENTREVISTASTECNICAS 
											(IdEmpleado, Fecha, VeracidadHV, PuntajeVeracidad, IdEntrevistador) 
											VALUES 
											($Id, '$FechaEntrevista', '$VeracidadHV', $PuntajeVeracidad, $IdEntrevistador);
									EOD;
								}

								$this->model->query($query);
							}

							break;

						case 2:
							if	( empty($_REQUEST['ExperienciaLaboral']) )
								$data['mensajeError'] .= label('Debe digitar unas observaciones de') . ' <strong>' . label('Experiencia laboral') . '</strong><br>';

							if	( $_REQUEST['PuntajeExperiencia'] < 1 OR $_REQUEST['PuntajeExperiencia'] > 30)
								$data['mensajeError'] .= label('Debe digitar un puntaje entre 1 y 30') . '<br>';

							if (empty($data['mensajeError']))
							{
								$ExperienciaLaboral = $_REQUEST['ExperienciaLaboral'];
								$PuntajeExperiencia = $_REQUEST['PuntajeExperiencia'];

								$IdEntrevista = getId('ENTREVISTASTECNICAS', "ENTREVISTASTECNICAS.IdEmpleado = $Id AND ENTREVISTASTECNICAS.Fecha = '$FechaEntrevista'");

								$query = <<<EOD
									UPDATE ENTREVISTASTECNICAS 
										SET 
											ExperienciaLaboral = '$ExperienciaLaboral', 
											PuntajeExperiencia = $PuntajeExperiencia  
										WHERE ENTREVISTASTECNICAS.Id = $IdEntrevista;
								EOD;

								$this->model->query($query);
							}

							break;

						case 3:
							if	( empty($_REQUEST['ConocimientoTecnico']) )
								$data['mensajeError'] .= label('Debe digitar unas observaciones de') . ' <strong>' . label('Conocimiento técnico') . '</strong><br>';

							if	( $_REQUEST['PuntajeConocimiento'] < 1 OR $_REQUEST['PuntajeConocimiento'] > 30)
								$data['mensajeError'] .= label('Debe digitar un puntaje entre 1 y 30') . '<br>';

							if (empty($data['mensajeError']))
							{
								$ConocimientoTecnico = $_REQUEST['ConocimientoTecnico'];
								$PuntajeConocimiento = $_REQUEST['PuntajeConocimiento'];

								$IdEntrevista = getId('ENTREVISTASTECNICAS', "ENTREVISTASTECNICAS.IdEmpleado = $Id AND ENTREVISTASTECNICAS.Fecha = '$FechaEntrevista'");

								$query = <<<EOD
									UPDATE ENTREVISTASTECNICAS 
										SET 
											ConocimientoTecnico = '$ConocimientoTecnico', 
											PuntajeConocimiento = $PuntajeConocimiento  
										WHERE ENTREVISTASTECNICAS.Id = $IdEntrevista;
								EOD;

								$this->model->query($query);
							}

							break;

						case 4:
							if	( empty($_REQUEST['Competencias']) )
								$data['mensajeError'] .= label('Debe digitar unas observaciones de') . ' <strong>' . label('Competencias') . '</strong><br>';

							if	( $_REQUEST['PuntajeCompetencias'] < 1 OR $_REQUEST['PuntajeCompetencias'] > 20)
								$data['mensajeError'] .= label('Debe digitar un puntaje entre 1 y 20') . '<br>';

							if (empty($data['mensajeError']))
							{
								$Competencias = $_REQUEST['Competencias'];
								$PuntajeCompetencias = $_REQUEST['PuntajeCompetencias'];

								$IdEntrevista = getId('ENTREVISTASTECNICAS', "ENTREVISTASTECNICAS.IdEmpleado = $Id AND ENTREVISTASTECNICAS.Fecha = '$FechaEntrevista'");

								$query = <<<EOD
									UPDATE ENTREVISTASTECNICAS 
										SET 
											Competencias = '$Competencias', 
											PuntajeCompetencias = $PuntajeCompetencias  
										WHERE ENTREVISTASTECNICAS.Id = $IdEntrevista;
								EOD;

								$this->model->query($query);
							}

							break;

						case 5:
							if	( empty($_REQUEST['Argumentos']) )
								$data['mensajeError'] .= label('Debe digitar unas observaciones de') . ' <strong>' . label('Argumentos') . '</strong><br>';

							if (empty($data['mensajeError']))
							{
								$Recomendado = isset($_REQUEST['Recomendado']) ? 1 : 0;
								$Argumentos = $_REQUEST['Argumentos'];

								$IdEntrevista = getId('ENTREVISTASTECNICAS', "ENTREVISTASTECNICAS.IdEmpleado = $Id AND ENTREVISTASTECNICAS.Fecha = '$FechaEntrevista'");

								$query = <<<EOD
									UPDATE ENTREVISTASTECNICAS 
										SET 
											Recomendado = $Recomendado, 
											Argumentos = '$Argumentos'   
										WHERE ENTREVISTASTECNICAS.Id = $IdEntrevista;
								EOD;

								$this->model->query($query);
							}

							break;

					}

					if (empty($data['mensajeError']))
					{
						$paso++;
						$_SESSION['Paso2'] = $paso;
						$data['mensajeError'] = '';
						$_REQUEST['Action'] = '';
					}
				}

				if ($_REQUEST['Action'] == 'RETROCEDER')
				{
					$paso--;
					$_SESSION['Paso2'] = $paso;
					$_REQUEST['Action'] = '';
				}
			}

			$regEntrevista = getRegistro('ENTREVISTASTECNICAS', 0, "ENTREVISTASTECNICAS.IdEmpleado = $Id AND ENTREVISTASTECNICAS.Fecha <= '$FechaEntrevista'");

			if (! isset($_SESSION['Paso2']))
				$_SESSION['Paso2'] = 1;

			$paso = $_SESSION['Paso2'];

			if ($regEntrevista)
			{
				switch ($paso)
				{
					case 1:
						$data['reg'] = array(
							'Id'					=> $regEntrevista['id'], 
							'IdEmpleado'			=> $Id, 
							'VeracidadHV' 			=> $regEntrevista['veracidadHV'],
							'PuntajeVeracidad' 		=> $regEntrevista['puntajeveracidad']
						);

						break;

					case 2:
						$data['reg'] = array(
							'Id'					=> $regEntrevista['id'],
							'IdEmpleado'			=> $Id, 
							'ExperienciaLaboral'	=> $regEntrevista['experiencialaboral'],
							'PuntajeExperiencia' 	=> $regEntrevista['puntajeexperiencia']
						);

						break;

					case 3:
						$data['reg'] = array(
							'Id'					=> $regEntrevista['id'],
							'IdEmpleado'			=> $Id, 
							'ConocimientoTecnico' 	=> $regEntrevista['conocimientotecnico'],
							'PuntajeConocimiento' 	=> $regEntrevista['puntajeconocimiento']
						);

						break;

					case 4:
						$data['reg'] = array(
							'Id'					=> $regEntrevista['id'],
							'IdEmpleado'			=> $Id, 
							'Competencias' 			=> $regEntrevista['competencias'],
							'PuntajeCompetencias' 	=> $regEntrevista['puntajecompetencias']
						);

						break;

					case 5:
						$data['reg'] = array(
							'Id'					=> $regEntrevista['id'],
							'IdEmpleado'			=> $Id, 
							'Recomendado' 			=> $regEntrevista['recomendado'],
							'Argumentos' 			=> $regEntrevista['argumentos']
						);

						break;

					case 6:
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
							'Id'					=> 0,
							'IdEmpleado'			=> $Id, 
							'VeracidadHV' 			=> '',
							'PuntajeVeracidad' 		=> 0
						);

						break;

					case 2:
						$data['reg'] = array(
							'Id'					=> 0,
							'IdEmpleado'			=> $Id, 
							'ExperienciaLaboral'	=> '',
							'PuntajeExperiencia' 	=> 0
						);

						break;

					case 3:
						$data['reg'] = array(
							'Id'					=> 0,
							'IdEmpleado'			=> $Id, 
							'ConocimientoTecnico' 	=> '',
							'PuntajeConocimiento' 	=> 0
						);

						break;

					case 4:
						$data['reg'] = array(
							'Id'					=> 0,
							'IdEmpleado'			=> $Id, 
							'Competencias' 			=> '',
							'PuntajeCompetencias' 	=> 0
						);

						break;

					case 5:
						$data['reg'] = array(
							'Id'					=> 0,
							'IdEmpleado'			=> $Id, 
							'Recomendado' 			=> 0,
							'Argumentos' 			=> ''
						);

						break;

					case 6:
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
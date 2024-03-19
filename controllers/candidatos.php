<?php
	require_once('./templates/vendor/autoload.php');
	require './templates/PHPMailer-master/src/PHPMailer.php';
	require './templates/PHPMailer-master/src/SMTP.php';
	require './templates/PHPMailer-master/src/Exception.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Candidatos extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/candidatos/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = SERVERURL . '/candidatos/importar';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = FALSE;

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

			$IdSicologo = $_SESSION['Login']['Id'];

			$query = "WHERE PARAMETROS.Detalle = 'EN PROCESO DE SELECCION' ";

			if	( ! empty($lcFiltro) )
			{
				$query .= "AND (UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(SICOLOGOS.Apellido1 + ' ' + SICOLOGOS.Apellido2 + ' ' + SICOLOGOS.Nombre1 + ' ' + SICOLOGOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($lcFiltro) . "%') ";
			}
			
			// $data['registros'] = $this->model->contarRegistros($query);
			// $lineas = LINES;
			// $offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			// $query .= 'ORDER BY ' . $_SESSION['CANDIDATOS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$query .= 'ORDER BY ' . $_SESSION['CANDIDATOS']['Orden']; 
			$data['rows'] = $this->model->listarCandidatos($query);
			if(isset($_REQUEST['Action'])){
				if(strrpos($_REQUEST['Action'], 'ELIFIRM_') !== FALSE){
					$id = explode('_',$_REQUEST['Action'])[1];
					$rps = cancelardocumentos($id);
				}
			}
			$this->views->getView($this, 'candidatos', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/candidatos/actualizar';
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
			$_SESSION['Lista'] = SERVERURL . '/candidatos/lista/' . $_SESSION['CANDIDATOS']['Pagina'];

			$data = array(
				'reg' => array(
					'Documento'	=> trim(isset($_REQUEST['Documento']) ? $_REQUEST['Documento'] : ''),
					'Apellido1'	=> trim(strtoupper(isset($_REQUEST['Apellido1']) ? $_REQUEST['Apellido1'] : '')),
					'Apellido2'	=> trim(strtoupper(isset($_REQUEST['Apellido2']) ? $_REQUEST['Apellido2'] : '')),
					'Nombre1'	=> trim(strtoupper(isset($_REQUEST['Nombre1']) ? $_REQUEST['Nombre1'] : '')),
					'Nombre2'	=> trim(strtoupper(isset($_REQUEST['Nombre2']) ? $_REQUEST['Nombre2'] : '')),
					'Email' 	=> trim(strtoupper(isset($_REQUEST['Email']) ? $_REQUEST['Email'] : '')),
					'Celular' 	=> trim(isset($_REQUEST['Celular']) ? $_REQUEST['Celular'] : ''),
					'Estado' 	=> 0
				),
				'mensajeError' 		=> ''
			);

			if (isset($_REQUEST['Documento'])) 
			{
				$Documento 	= $data['reg']['Documento'];
				$Nombre 	= strtoupper($data['reg']['Nombre1'] . ' ' . $data['reg']['Nombre2'] . ' ' . $data['reg']['Apellido1'] . ' ' . $data['reg']['Apellido2']);
				$Email 		= $data['reg']['Email'];
				$Celular 	= $data['reg']['Celular'];

				if	( empty($data['reg']['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de identidad') . '</strong><br>';
				else
				{
					$query = <<<EOD
						SELECT EMPLEADOS.Id 
							FROM EMPLEADOS 
								INNER JOIN PARAMETROS 
									ON EMPLEADOS.Estado = PARAMETROS.Id 
							WHERE EMPLEADOS.Documento = '$Documento' AND 
								PARAMETROS.Detalle <> 'RETIRADO'  AND 
								PARAMETROS.Detalle <> 'CANDIDATO DESISTE'   AND 
								PARAMETROS.Detalle <> 'CANDIDATO NO CALIFICADO';
					EOD;

					$reg = $this->model->leer($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Documento') . '</strong> ' . label('ya existe') . '<br>';
				}

				if	( empty($data['reg']['Apellido1']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Primer apellido') . '</strong><br>';

				if	( empty($data['reg']['Nombre1']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Primer nombre') . '</strong><br>';

				if	( empty($data['reg']['Email']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Email') . '</strong><br>';
				elseif	( !filter_var($data['reg']['Email'], FILTER_VALIDATE_EMAIL) )
					$data['mensajeError'] .= label('Formato invalido de') . ' <strong>' . label('Email') . '</strong><br>';
				else
				{
					$query = <<<EOD
						SELECT EMPLEADOS.Id  
							FROM EMPLEADOS 
							INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
							WHERE EMPLEADOS.Documento <> '$Documento' AND 
							PARAMETROS.Detalle <> 'RETIRADO'  AND 
								PARAMETROS.Detalle <> 'CANDIDATO DESISTE'   AND 
								PARAMETROS.Detalle <> 'CANDIDATO NO CALIFICADO' AND
								EMPLEADOS.Email = '$Email';
					EOD;

					$reg = $this->model->leer($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Email') . '</strong> ' . label('ya está registrado con otra persona') . '<br>';
				}
			
				if	( empty($data['reg']['Celular']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Celular') . '</strong><br>';
				elseif (!is_numeric($data['reg']['Celular']))
					$data['mensajeError'] .= label('Formato invalido de') . ' <strong>' . label('Celular') . '</strong><br>';
				elseif (strlen($data['reg']['Celular']) < 10)
					$data['mensajeError'] .= '<strong>' . label('Celular') . ' </strong>'.label('debe tener al menos 10 digitos<br>');

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					// BUSCAMOS EL ESTADO DEL EMPLEADO
					$EstadoEmpleado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'EN PROCESO DE SELECCION'");

					$data['reg']['Estado'] = $EstadoEmpleado;

					$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'DOCUMENTO'");

					$id = $this->model->guardarCandidato($data['reg']);

					if ($id) 
					{
						$regDocumento = getRegistro('PLANTILLAS', 0, "PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND PLANTILLAS.TipoPlantilla = $TipoPlantilla AND PLANTILLAS.Asunto = 'AUTORIZACION PARA EL TRATAMIENTO DE DATOS PERSONALES'");
						
						if ($regDocumento)
						{
							// ENVIO DE POLITICA DE TRATAMIENTO DE DATOS PARA FIRMA ELECTRONICA
							$Usuario = USER_FIRMA;
							$Clave 	 = CLAVE_FIRMA;

							$data_Firma = <<<EOD
								{
									"Usuario": "$Usuario",
									"Clave": "$Clave",
									"Firmantes": [
										{
											"Identificacion": "$Documento",
											"TipoIdentificacion": "CC",
											"Nombre": "$Nombre",
											"Correo": "$Email",
											"NroCelular": "$Celular",
											"Foto": "1",
											"FotoObligatoria": "1",
											"SolicitarAdjunto": "0"
										}
									],
							EOD;

							$archivos_base64 = '';

							// GENERACION DOCUMENTO EN PDF
							global $Asunto;
							global $CodigoDocumento;

							$Asunto 			= utf8_decode($regDocumento['asunto']);
							$Plantilla 			= $regDocumento['plantilla'];
							$CodigoDocumento 	= utf8_decode($regDocumento['codigodocumento']);

							// $Plantilla = str_replace('<<Logotipo>>', LOGO, $Plantilla);
							$Plantilla = str_replace('<<NombreEmpleado>>', $Nombre, $Plantilla);
							$Plantilla = str_replace('<<DocumentoIdentidad>>', $Documento, $Plantilla);
							$Plantilla = str_replace('<<Dia>>', date('d'), $Plantilla);
							$NombreMes = NombreMes(date('m'));
							$Plantilla = str_replace('<<NombreMes>>', $NombreMes, $Plantilla);
							$Plantilla = str_replace('<<Año>>', date('Y'), $Plantilla);
							$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);

							$Plantilla = utf8_decode($Plantilla);

							$pdf = new PDF2();
							
							$pdf->AliasNbPages();
							$pdf->SetAutoPageBreak(true, 25);
							$pdf->SetLeftMargin(25);
								
							$pdf->AddPage();
							$pdf->SetFont('Arial','',10);

							// $pdf->WriteHTML($Plantilla);
							$pdf->MultiCell(170, 5, $Plantilla, 0, 'J', FALSE);
							
							$cDirectorio = str_replace(" ","",'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1'] . '_' . $data['reg']['Apellido2'] . '_' . $data['reg']['Nombre1'] . '_' . $data['reg']['Nombre2']));

							if	( ! is_dir($cDirectorio) )
								mkdir($cDirectorio);

							$cDirectorio .= '/CONTRATOS';

							if	( ! is_dir($cDirectorio) )
								mkdir($cDirectorio);
		
							$pdf->Output('F', $cDirectorio . '/' . $data['reg']['Documento'] . '_' . strtoupper($Asunto) . '.PDF', TRUE); 

							// SE PREPARA EL ARCHIVO PARA FIRMA DIGITAL
							$Archivo  = utf8_decode($Asunto);
							$archivo1 = base64_encode(file_get_contents($cDirectorio . '/' . $data['reg']['Documento'] . '_' . strtoupper($Asunto) . '.PDF'));

							$archivos_base64 = <<<EOD
								"ArchivosPDF": [
									{
										"Nombre": "$Archivo", 
										"Documento_base64": "$archivo1"
									}
							EOD;

							$archivos_base64 .= <<<EOD
								]}
							EOD;

							$datos_firma = $data_Firma . $archivos_base64;

							// SE HACE EL ENVIO DE LOS DOCUMENTOS PARA FIRMA DIGITAL
							$curl = curl_init();

							curl_setopt_array($curl, 
								array(
									CURLOPT_URL => URL_FIRMA . 'signer',
									CURLOPT_RETURNTRANSFER => true,
									CURLOPT_ENCODING => '',
									CURLOPT_MAXREDIRS => 10,
									CURLOPT_TIMEOUT => 0,
									CURLOPT_FOLLOWLOCATION => true,
									CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
									CURLOPT_CUSTOMREQUEST => 'POST',
									CURLOPT_POSTFIELDS => $datos_firma,
									CURLOPT_HTTPHEADER => array(
										'Token: ' . TOKEN_FIRMA,
										'Content-Type: application/json'
									),
								)
							);

							$response = json_decode(curl_exec($curl), true);
 							logRequests("CANDIDATOS","",json_encode(curl_getinfo($curl)), json_encode($response), "FIRMA PLUS");
							curl_close($curl);

							if ($response['Code'] == 1)
							{
								$SolicitudFirma = $response['Data']['NroSolicitud'];
								$FechaSolicitud = $response['Data']['Fecha'];
								
								$query = <<<EOD
									UPDATE EMPLEADOS 
										SET
											SolicitudFirma = $SolicitudFirma, 
											FechaSolicitud = '$FechaSolicitud' 
										WHERE EMPLEADOS.Id = $id;
								EOD;

								$this->model->query($query);

								// ENVIO DE CORREO ELECTRONICO
								$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'CORREO ELECTRÓNICO'");
				
								$query = <<<EOD
									SELECT PLANTILLAS.* 
										FROM PLANTILLAS 
										WHERE PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND 
											PLANTILLAS.TipoPlantilla = $TipoPlantilla AND 
											PLANTILLAS.Asunto = 'PROCESO DE SELECCIÓN';
								EOD;

								$reg = $this->model->leer($query);

								if ($reg) 
								{
									$Asunto = utf8_decode($reg['asunto']);
									$Plantilla = $reg['plantilla'];

									$Plantilla = str_replace('<<Logotipo>>', LOGOTIPO, $Plantilla);
									$Plantilla = str_replace('<<EnlacePortal>>', SERVERURL, $Plantilla);
									$Plantilla = str_replace('<<Email>>', $data['reg']['Email'], $Plantilla);
									$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);

									$Plantilla = utf8_decode($Plantilla);

									$mail = new PHPMailer\PHPMailer\PHPMailer(true);

									$mail->SMTPOptions = array(
										'ssl' => array(
											'verify_peer' => false,
											'verify_peer_name' => false,
											'allow_self_signed' => true
										)
									);

									$mail->SMTPDebug 		= 0;
									$mail->isSMTP();
									$mail->Host       		= HOST;
									$mail->Port       		= PORT;
									$mail->SMTPKeepAlive 	= true;          
									$mail->SMTPAuth   		= false;
									$mail->SMTPSecure 		= 'tls';  
									$mail->isHTML(true);

									$from = 'no-reply@comware.com.co';
									$fromName = 'GESTION HUMANA - COMWARE';

									$mail->Subject = $Asunto;
									$mail->addEmbeddedImage(LOGOTIPO, 'comware');
									$mail->Body = $Plantilla;
									$mail->AddAddress($data['reg']['Email']);
									$response = new stdClass();
									try 
									{
										$mail->setFrom($from, $fromName);	
										$obj = (object) array(
											'CharSet' => $mail->CharSet,
											'ContentType' => $mail->ContentType,
											'Encoding' => $mail->Encoding,
											'From' => $mail->From,
											'FromName' => $mail->FromName,
											'Sender' => $mail->Sender,
											'Subject' => $mail->Subject,
											'Mailer' => $mail->Mailer,
											'Sendmail' => $mail->Sendmail,
											'Host' => $mail->Host,
											'SMTPOptions' => $mail->SMTPOptions,
											'smtp' => $mail->smtp,
											'to' => $mail->to);									
											$response = $mail->send();
										logRequests("CANDIDATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", $mail->to);
									} 
									catch (Exception $e) 
									{
										logRequests("CANDIDATOS",$Plantilla,json_encode($obj), "ERROR", "ENVIO DE EMAIL", $data['reg']['Email']);
										$data['mensajeError'] .= "Error al enviar correo a $Email <br>";
										$mail->getSMTPInstance()->reset();
									}

									$mail->clearAddresses();
									$mail->clearAttachments();
								}
							}
							else
							{
								$CodigoError = $response['Code'];
								$MensajeError = $response['Message'];

								$data['mensajeError'] .= <<<EOD
									Error al tratar de enviar la AUTORIZACION PARA EL TRATAMIENTO DE DATOS PERSONALES para firma electrónica <br>
									Código de error: $CodigoError<br>
									Mensaje: $MensajeError<br>
								EOD;
							}
						}
						else
						{
							$data['mensajeError'] .= "No hay una plantilla de AUTORIZACION PARA EL TRATAMIENTO DE DATOS PERSONALES <br>";
						}
					}

					if (empty($data['mensajeError']))
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}

			$this->views->getView($this, 'adicionar', $data);
		}

		public function editar($Id)
		{
			$regEmpleado = getRegistro('EMPLEADOS', $Id);

			if (isset($_REQUEST['Documento']))
			{
				$data = array(
					'reg' => array(
						'Documento'	=> trim(isset($_REQUEST['Documento']) ? $_REQUEST['Documento'] : ''),
						'Apellido1'	=> trim(strtoupper(isset($_REQUEST['Apellido1']) ? $_REQUEST['Apellido1'] : '')),
						'Apellido2'	=> trim(strtoupper(isset($_REQUEST['Apellido2']) ? $_REQUEST['Apellido2'] : '')),
						'Nombre1'	=> trim(strtoupper(isset($_REQUEST['Nombre1']) ? $_REQUEST['Nombre1'] : '')),
						'Nombre2'	=> trim(strtoupper(isset($_REQUEST['Nombre2']) ? $_REQUEST['Nombre2'] : '')),
						'Email' 	=> trim(strtoupper(isset($_REQUEST['Email']) ? $_REQUEST['Email'] : '')),
						'Celular' 	=> trim(isset($_REQUEST['Celular']) ? $_REQUEST['Celular'] : '')
					),
					'mensajeError' 		=> ''
				);

				$Documento 	= $data['reg']['Documento'];
				$Nombre 	= strtoupper($data['reg']['Nombre1'] . ' ' . $data['reg']['Nombre2'] . ' ' . $data['reg']['Apellido1'] . ' ' . $data['reg']['Apellido2']);
				$Email 		= $data['reg']['Email'];
				$Celular 	= $data['reg']['Celular'];

				if (isset($_REQUEST['CandidatoDesiste'])) 
				{
					$query = <<<EOD
						SELECT PARAMETROS.Id 
							FROM PARAMETROS 
							WHERE PARAMETROS.Parametro = 'EstadoEmpleado' AND 
								PARAMETROS.Detalle = 'CANDIDATO DESISTE';
					EOD;
	
					$reg = $this->model->buscarCandidato($query);
	
					if ($reg) 
						$data['reg']['Estado'] = $reg['Id'];
					else
						$data['mensajeError'] .= label('Se requiere parametrizar un estado de empleado') . ': <strong>' . label('CANDIDATO DESISTE') . '</strong><br>';

					$resp = $this->model->actualizarCandidato($data['reg'], $Id);
				}
				else
				{
					$Documento 	= $data['reg']['Documento'];
					$Email 		= $data['reg']['Email'];

					if	( empty($data['reg']['Documento']) )
						$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de identidad') . '</strong><br>';
					else
					{
						$query = <<<EOD
							SELECT EMPLEADOS.Id 
								FROM EMPLEADOS 
									INNER JOIN PARAMETROS 
										ON EMPLEADOS.Estado = PARAMETROS.Id 
								WHERE EMPLEADOS.Documento = '$Documento' AND 
									PARAMETROS.Detalle <> 'ACTIVO' AND 
									EMPLEADOS.Id <> $Id;
						EOD;

						$reg = $this->model->leer($query);

						if ($reg) 
							$data['mensajeError'] .= '<strong>' . label('Documento') . '</strong> ' . label('ya existe') . '<br>';
					}

					if	( empty($data['reg']['Apellido1']) )
						$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Primer apellido') . '</strong><br>';

					if	( empty($data['reg']['Nombre1']) )
						$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Primer nombre') . '</strong><br>';

					if	( empty($data['reg']['Email']) )
						$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Email') . '</strong><br>';
					elseif	( !filter_var($data['reg']['Email'], FILTER_VALIDATE_EMAIL) )
						$data['mensajeError'] .= label('Formato invalido de') . ' <strong>' . label('Email') . '</strong><br>';
					else
					{
						$query = <<<EOD
							SELECT EMPLEADOS.Id  
								FROM EMPLEADOS 
								WHERE EMPLEADOS.Documento <> '' AND 
									EMPLEADOS.Documento <> '$Documento' AND 
									EMPLEADOS.Email = '$Email';
						EOD;

						$reg = $this->model->leer($query);

						if ($reg) 
							$data['mensajeError'] .= '<strong>' . label('Email') . '</strong> ' . label('ya está registrado con otra persona') . '<br>';
					}
			
					if	( empty($data['reg']['Celular']) )
						$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Celular') . '</strong><br>';
					elseif (!is_numeric($data['reg']['Celular']))
						$data['mensajeError'] .= label('Formato invalido de') . ' <strong>' . label('Celular') . '</strong><br>';
					elseif (strlen($data['reg']['Celular']) < 10)
						$data['mensajeError'] .= '<strong>' . label('Celular') . ' </strong>'.label('debe tener al menos 10 digitos<br>');

					if	( $data['mensajeError'] )
						$this->views->getView($this, 'actualizar', $data);
					else
					{
						$data['reg']['Estado'] = $regEmpleado['estado'];

						$resp = $this->model->actualizarCandidato($data['reg'], $Id);

						if ($resp) 
						{
							if (isset($_REQUEST['ReenviarCorreo'])) 
							{
								$EstadoEmpleado = $regEmpleado['estado'];

								$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'DOCUMENTO'");

								$regDocumento = getRegistro('PLANTILLAS', 0, "PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND PLANTILLAS.TipoPlantilla = $TipoPlantilla AND PLANTILLAS.Asunto = 'AUTORIZACION PARA EL TRATAMIENTO DE DATOS PERSONALES'");
								
								if($regEmpleado['solicitudfirma'] == "0" || $regEmpleado['solicitudfirma'] == "" || $regEmpleado['solicitudfirma'] == NULL){

									if ($regDocumento)
									{
										// ENVIO DE POLITICA DE TRATAMIENTO DE DATOS PARA FIRMA ELECTRONICA
										$Usuario = USER_FIRMA;
										$Clave 	 = CLAVE_FIRMA;

										$data_Firma = <<<EOD
											{
												"Usuario": "$Usuario",
												"Clave": "$Clave",
												"Firmantes": [
													{
														"Identificacion": "$Documento",
														"TipoIdentificacion": "CC",
														"Nombre": "$Nombre",
														"Correo": "$Email",
														"NroCelular": "$Celular",
														"Foto": "1",
														"FotoObligatoria": "1",
														"SolicitarAdjunto": "0"
													}
												],
										EOD;

										$archivos_base64 = '';

										// GENERACION DOCUMENTO EN PDF
										global $Asunto;
										global $CodigoDocumento;

										$Asunto 			= utf8_decode($regDocumento['asunto']);
										$Plantilla 			= $regDocumento['plantilla'];
										$CodigoDocumento 	= utf8_decode($regDocumento['codigodocumento']);

										// $Plantilla = str_replace('<<Logotipo>>', LOGO, $Plantilla);
										$Plantilla = str_replace('<<NombreEmpleado>>', $Nombre, $Plantilla);
										$Plantilla = str_replace('<<DocumentoIdentidad>>', $Documento, $Plantilla);
										$Plantilla = str_replace('<<Dia>>', date('d'), $Plantilla);
										$NombreMes = NombreMes(date('m'));
										$Plantilla = str_replace('<<NombreMes>>', $NombreMes, $Plantilla);
										$Plantilla = str_replace('<<Año>>', date('Y'), $Plantilla);
										$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);

										$Plantilla = utf8_decode($Plantilla);

										$pdf = new PDF2();
										
										$pdf->AliasNbPages();
										$pdf->SetAutoPageBreak(true, 25);
										$pdf->SetLeftMargin(25);
											
										$pdf->AddPage();
										$pdf->SetFont('Arial','',10);

										// $pdf->WriteHTML($Plantilla);
										$pdf->MultiCell(170, 5, $Plantilla, 0, 'J', FALSE);
										
										$cDirectorio = str_replace(" ","",'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1'] . '_' . $data['reg']['Apellido2'] . '_' . $data['reg']['Nombre1'] . '_' . $data['reg']['Nombre2']));

										if	( ! is_dir($cDirectorio) )
											mkdir($cDirectorio);

										$cDirectorio .= '/CONTRATOS';

										if	( ! is_dir($cDirectorio) )
											mkdir($cDirectorio);
					
										$pdf->Output('F', $cDirectorio . '/' . $data['reg']['Documento'] . '_' . strtoupper($Asunto) . '.PDF', TRUE); 

										// SE PREPARA EL ARCHIVO PARA FIRMA DIGITAL
										$Archivo  = utf8_decode($Asunto);
										$archivo1 = base64_encode(file_get_contents($cDirectorio . '/' . $data['reg']['Documento'] . '_' . strtoupper($Asunto) . '.PDF'));

										$archivos_base64 = <<<EOD
											"ArchivosPDF": [
												{
													"Nombre": "$Archivo", 
													"Documento_base64": "$archivo1"
												}
										EOD;

										$archivos_base64 .= <<<EOD
											]}
										EOD;

										$datos_firma = $data_Firma . $archivos_base64;

										// SE HACE EL ENVIO DE LOS DOCUMENTOS PARA FIRMA DIGITAL
										$curl = curl_init();

										curl_setopt_array($curl, 
											array(
												CURLOPT_URL => URL_FIRMA . "signer",
												CURLOPT_RETURNTRANSFER => true,
												CURLOPT_ENCODING => '',
												CURLOPT_MAXREDIRS => 10,
												CURLOPT_TIMEOUT => 0,
												CURLOPT_FOLLOWLOCATION => true,
												CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
												CURLOPT_CUSTOMREQUEST => 'POST',
												CURLOPT_POSTFIELDS => $datos_firma,
												CURLOPT_HTTPHEADER => array(
													'Token: ' . TOKEN_FIRMA,
													'Content-Type: application/json'
												),
											)
										);

										$response = json_decode(curl_exec($curl), true);

										curl_close($curl);

										if ($response['Code'] == 1)
										{

											$SolicitudFirma = $response['Data']['NroSolicitud'];
											$FechaSolicitud = $response['Data']['Fecha'];


											$query = <<<EOD
												UPDATE EMPLEADOS 
													SET
														SolicitudFirma = $SolicitudFirma, 
														FechaSolicitud = '$FechaSolicitud' 
													WHERE EMPLEADOS.Id = $Id;
											EOD;

											$this->model->query($query);

											
											// ENVIO DE CORREO ELECTRONICO
											$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'CORREO ELECTRÓNICO'");
							
											$query = <<<EOD
												SELECT PLANTILLAS.* 
													FROM PLANTILLAS 
													WHERE PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND 
														PLANTILLAS.TipoPlantilla = $TipoPlantilla AND 
														PLANTILLAS.Asunto = 'PROCESO DE SELECCIÓN';
											EOD;

											$reg = $this->model->leer($query);

											if ($reg) 
											{
												$Asunto = utf8_decode($reg['asunto']);
												$Plantilla = $reg['plantilla'];

												$Plantilla = str_replace('<<Logotipo>>', LOGOTIPO, $Plantilla);
												$Plantilla = str_replace('<<EnlacePortal>>', SERVERURL, $Plantilla);
												$Plantilla = str_replace('<<Email>>', $data['reg']['Email'], $Plantilla);
												$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);

												$Plantilla = utf8_decode($Plantilla);

												$mail = new PHPMailer\PHPMailer\PHPMailer(true);

												$mail->SMTPOptions = array(
													'ssl' => array(
														'verify_peer' => false,
														'verify_peer_name' => false,
														'allow_self_signed' => true
													)
												);

												$mail->SMTPDebug 		= 0;
												$mail->isSMTP();
												$mail->Host       		= HOST;
												$mail->Port       		= PORT;
												$mail->SMTPKeepAlive 	= true;          
												$mail->SMTPAuth   		= false;
												$mail->SMTPSecure 		= 'tls';  
												$mail->isHTML(true);

												$from = 'no-reply@comware.com.co';
												$fromName = 'GESTION HUMANA - COMWARE';

												$mail->Subject = $Asunto;
												$mail->addEmbeddedImage(LOGOTIPO, 'comware');
												$mail->Body = $Plantilla;
												$mail->AddAddress($data['reg']['Email']);
												$response = new stdClass();
												try 
												{
													$mail->setFrom($from, $fromName);
													$obj = (object) array(
														'CharSet' => $mail->CharSet,
														'ContentType' => $mail->ContentType,
														'Encoding' => $mail->Encoding,
														'From' => $mail->From,
														'FromName' => $mail->FromName,
														'Sender' => $mail->Sender,
														'Subject' => $mail->Subject,
														'Mailer' => $mail->Mailer,
														'Sendmail' => $mail->Sendmail,
														'Host' => $mail->Host,
														'SMTPOptions' => $mail->SMTPOptions,
														'smtp' => $mail->smtp,
														'to' => $mail->to);
														$response = $mail->send();
													logRequests("CANDIDATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $mail->to);
												} 
												catch (Exception $e) 
												{
													logRequests("CANDIDATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $data['reg']['Email']);
													$data['mensajeError'] .= "Error al enviar correo a $Email <br>";
													$mail->getSMTPInstance()->reset();
												}

												$mail->clearAddresses();
												$mail->clearAttachments();
											}
										}
										else
										{
											$CodigoError = $response['Code'];
											$MensajeError = $response['Message'];

											$data['mensajeError'] .= <<<EOD
												Error al tratar de enviar la AUTORIZACION PARA EL TRATAMIENTO DE DATOS PERSONALES para firma electrónica <br>
												Código de error: $CodigoError<br>
												Mensaje: $MensajeError<br>
											EOD;
										}
									}
									else
									{
										$data['mensajeError'] .= "No hay una plantilla de AUTORIZACION PARA EL TRATAMIENTO DE DATOS PERSONALES <br>";
									}

								}else{


									// ENVIO DE CORREO ELECTRONICO
									$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'CORREO ELECTRÓNICO'");
							
									$query = <<<EOD
										SELECT PLANTILLAS.* 
											FROM PLANTILLAS 
											WHERE PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND 
												PLANTILLAS.TipoPlantilla = $TipoPlantilla AND 
												PLANTILLAS.Asunto = 'PROCESO DE SELECCIÓN';
									EOD;

									$reg = $this->model->leer($query);

									if ($reg) 
									{
										$Asunto = utf8_decode($reg['asunto']);
										$Plantilla = $reg['plantilla'];

										$Plantilla = str_replace('<<Logotipo>>', LOGOTIPO, $Plantilla);
										$Plantilla = str_replace('<<EnlacePortal>>', SERVERURL, $Plantilla);
										$Plantilla = str_replace('<<Email>>', $data['reg']['Email'], $Plantilla);
										$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);

										$Plantilla = utf8_decode($Plantilla);

										$mail = new PHPMailer\PHPMailer\PHPMailer(true);

										$mail->SMTPOptions = array(
											'ssl' => array(
												'verify_peer' => false,
												'verify_peer_name' => false,
												'allow_self_signed' => true
											)
										);

										$mail->SMTPDebug 		= 0;
										$mail->isSMTP();
										$mail->Host       		= HOST;
										$mail->Port       		= PORT;
										$mail->SMTPKeepAlive 	= true;          
										$mail->SMTPAuth   		= false;
										$mail->SMTPSecure 		= 'tls';  
										$mail->isHTML(true);

										$from = 'no-reply@comware.com.co';
										$fromName = 'GESTION HUMANA - COMWARE';

										$mail->Subject = $Asunto;
										$mail->addEmbeddedImage(LOGOTIPO, 'comware');
										$mail->Body = $Plantilla;
										$mail->AddAddress($data['reg']['Email']);
										$response = new stdClass();
										try 
										{
											$mail->setFrom($from, $fromName);
											$obj = (object) array(
												'CharSet' => $mail->CharSet,
												'ContentType' => $mail->ContentType,
												'Encoding' => $mail->Encoding,
												'From' => $mail->From,
												'FromName' => $mail->FromName,
												'Sender' => $mail->Sender,
												'Subject' => $mail->Subject,
												'Mailer' => $mail->Mailer,
												'Sendmail' => $mail->Sendmail,
												'Host' => $mail->Host,
												'SMTPOptions' => $mail->SMTPOptions,
												'smtp' => $mail->smtp,
												'to' => $mail->to);
												$response = $mail->send();
											logRequests("CANDIDATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $mail->to);
										} 
										catch (Exception $e) 
										{
											logRequests("CANDIDATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $data['reg']['Email']);
											$data['mensajeError'] .= "Error al enviar correo a $Email <br>";
											$mail->getSMTPInstance()->reset();
										}

										$mail->clearAddresses();
										$mail->clearAttachments();
									}

									$data['mensajeError'] .= "El candidato tiene DOCUMENTOS pendientes por firmar <br>";
								}

							}

							if (empty($data['mensajeError']))
							{
								header('Location: ' . $_SESSION['Lista']);
								exit();
							}
						}
					}
				}
			}

			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/candidatos/actualizar';
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
			$_SESSION['Lista'] = SERVERURL . '/candidatos/lista/' . $_SESSION['CANDIDATOS']['Pagina'];

			$regEmpleado = getRegistro('EMPLEADOS', $Id);

			$data = array(
				'reg' => array(
					'TipoIdentificacion' 	=> $regEmpleado['tipoidentificacion'],
					'Documento' 			=> $regEmpleado['documento'],
					'IdCiudadExpedicion' 	=> $regEmpleado['idciudadexpedicion'],
					'Apellido1' 			=> $regEmpleado['apellido1'],
					'Apellido2' 			=> $regEmpleado['apellido2'],
					'Nombre1' 				=> $regEmpleado['nombre1'],
					'Nombre2' 				=> $regEmpleado['nombre2'],
					'IdCargo' 				=> $regEmpleado['idcargo'],
					'Email' 				=> $regEmpleado['email'],
					'Celular' 				=> $regEmpleado['celular'],
					'SueldoBasico' 			=> $regEmpleado['sueldobasico'], 
					'SubsidioTransporte' 	=> 0,
					'IdCiudadTrabajo' 		=> $regEmpleado['idciudadtrabajo'],
					'TipoContrato' 			=> $regEmpleado['tipocontrato'], 
					'InstitutoFormacion'	=> $regEmpleado['institutoformacion'], 
					'EspecialidadAprendiz'	=> $regEmpleado['especialidadaprendiz'], 
					'FechaIngreso' 			=> $regEmpleado['fechaingreso'], 
					'FechaPeriodoPrueba'	=> $regEmpleado['fechaperiodoprueba'], 
					'DuracionContrato' 		=> $regEmpleado['duracioncontrato'], 
					'FechaVencimiento' 		=> $regEmpleado['fechavencimiento'],  
					'ModalidadTrabajo' 		=> $regEmpleado['modalidadtrabajo'], 
					'PeriodicidadPago' 		=> $regEmpleado['periodicidadpago'], 
					'MetodoRetencion' 		=> $regEmpleado['metodoretencion'], 
					'Estado' 				=> $regEmpleado['estado']
					),
				'mensajeError' 		=> isset($data['mensajeError']) ? $data['mensajeError'] : ''
			);

			$this->views->getView($this, 'actualizar', $data);
		}

		public function cargarDatos($Id)
		{
			// INFORMACION EMPLEADO
			$reg = getRegistro('EMPLEADOS', $Id);

			if ($reg)
			{
				$IdEmpleado = $reg['id'];

				$data['reg'] = array(
					'Id' 					=> $reg['id'], 
					'Estado'				=> $reg['estado'],

					'TipoIdentificacion' 	=> $reg['tipoidentificacion'], 
					'Documento' 			=> $reg['documento'], 
					'FechaExpedicion' 		=> $reg['fechaexpedicion'], 
					'IdCiudadExpedicion' 	=> $reg['idciudadexpedicion'], 
					'Apellido1' 			=> str_replace(" ","",$reg['apellido1']), 
					'Apellido2' 			=> str_replace(" ","",$reg['apellido2']), 
					'Nombre1' 				=> str_replace(" ","",$reg['nombre1']), 
					'Nombre2' 				=> str_replace(" ","",$reg['nombre2']), 
					'PoliticamenteExpuesta' => ($reg['politicamenteexpuesta'] ? true : false), 

					'FechaNacimiento' 		=> $reg['fechanacimiento'], 
					'IdCiudadNacimiento' 	=> $reg['idciudadnacimiento'], 
					'Genero' 				=> $reg['genero'], 
					'EstadoCivil' 			=> $reg['estadocivil'], 
					'FactorRH' 				=> $reg['factorrh'], 
					'LibretaMilitar' 		=> $reg['libretamilitar'],
					'DistritoMilitar' 		=> $reg['distritomilitar'],
					'LicenciaConduccion' 	=> $reg['licenciaconduccion'],
					'TarjetaProfesional' 	=> $reg['tarjetaprofesional'],
					'Direccion' 			=> $reg['direccion'], 
					'Barrio' 				=> $reg['barrio'], 
					'Localidad' 			=> $reg['localidad'], 
					'IdCiudad' 				=> $reg['idciudad'], 
					'Email' 				=> $reg['email'], 
					'Telefono' 				=> $reg['telefono'], 
					'Celular' 				=> $reg['celular'], 
					'IdEPS' 				=> $reg['ideps'],
					'IdFondoCesantias' 		=> $reg['idfondocesantias'],
					'IdFondoPensiones' 		=> $reg['idfondopensiones'],
					'IdBanco' 				=> $reg['idbanco'],
					'TipoCuentaBancaria' 	=> $reg['tipocuentabancaria'],
					'CuentaBancaria' 		=> $reg['cuentabancaria'],
					'CuentaBancaria2' 		=> $reg['cuentabancaria2'],
					'IdCargo' 				=> $reg['idcargo'], 
					'PerfilProfesional' 	=> $reg['perfilprofesional'],

					'Empresa' 				=> '',
					'IdCiudadEmpresa'		=> 0,
					'CargoEmpresa'			=> '',
					'JefeInmediato' 		=> '',
					'TelefonoEmpresa'		=> '',
					'FechaIngresoEmpresa'	=> NULL,
					'FechaRetiroEmpresa' 	=> NULL,
					'Responsabilidades' 	=> '',

					'CentroEducativoF' 		=> '',
					'NivelAcademicoF' 		=> 0,
					'EstudioF' 				=> '',
					'EstadoF' 				=> 0,
					'AnoInicioF' 			=> 0,
					'MesInicioF' 			=> 0,
					'AnoFinalizacionF' 		=> 0,
					'MesFinalizacionF' 		=> 0,
					'NombreNivelAcademicoF'	=> '', 

					'CentroEducativoNF' 		=> '',
					'NivelAcademicoNF' 			=> 0,
					'EstudioNF' 				=> '',
					'EstadoNF' 					=> 0,
					'AnoInicioNF' 				=> 0,
					'MesInicioNF' 				=> 0,
					'AnoFinalizacionNF' 		=> 0,
					'MesFinalizacionNF' 		=> 0,
					'NombreNivelAcademicoNF'	=> '', 

					'IdIdioma' 				=> 0,
					'NivelIdioma' 			=> 0,

					'Conocimiento' 			=> '',
					'NivelConocimiento'		=> 0,

					'NombreContacto'		=> '', 
					'TelefonoContacto'		=> '', 
					'ParentescoContacto'	=> 0, 

					'Fotografia'			=> FALSE,
					'DocumentoIdentidad'	=> FALSE, 
					'HojaVida'				=> FALSE, 
					'TarjetaProfesional'	=> FALSE, 
					'CertificacionBancaria'	=> FALSE, 
					'CertificadoFP'			=> FALSE, 
					'CertificadoRegimenEps'	=> FALSE, 
					'CertificadoEPS'		=> FALSE, 
					'CertificadoFC'			=> FALSE, 
					'CertificadosAcademicos'=> FALSE,
					'CertificadosLaborales'	=> FALSE, 

					'EnPobrezaExtrema'		=> FALSE, 
					'Desplazado'			=> FALSE, 
					'EnReincorporacion'		=> FALSE, 
					'AdultoMayor'			=> FALSE, 
					'CabezaHogar'			=> FALSE, 
					'Discapacitado'			=> FALSE, 
					'ComunidadLGBTI'		=> FALSE, 
					'Negritudes'			=> FALSE,
					'Indigenas'				=> FALSE, 

					'certificadoAprendiz'       => FALSE,
					'AntecedentesProcuraduria'	=> FALSE, 
					'casoArandaServiceDesk'		=> FALSE,
					'AntecedentesContraloria'	=> FALSE, 
					'AntecedentesPolicia'		=> FALSE, 
					'InhabilidadesSexuales'		=> FALSE, 
					'ConsultaInfolaft'			=> FALSE, 
					'InformeSeleccion'			=> FALSE, 
					'EntrevistaTecnica'			=> FALSE, 
					'EstudioSeguridad'			=> FALSE, 
					'Prueba360'					=> FALSE, 
					'PruebaTecnica'				=> FALSE, 
					'PruebaOptimo'				=> FALSE, 
					'PruebaOrtografia'			=> FALSE, 
					'RUAF'						=> FALSE, 
					'ExamenMedico'				=> FALSE, 
					'RecomendacionesMedicas'	=> FALSE, 

					'SEL_DocumentosActualizados'=> $reg['sel_documentosactualizados'],

					'IdCargo'				=> $reg['idcargo'],
					'IdCentro'				=> $reg['idcentro'],
					'IdProyecto'			=> $reg['idproyecto'],
					'IdSede'				=> $reg['idsede'],
					'Vicepresidencia'		=> $reg['vicepresidencia'], 
					'TipoContrato'			=> $reg['tipocontrato'], 
					'FechaIngreso'			=> $reg['fechaingreso'],
					'FechaPeriodoPrueba'	=> $reg['fechaperiodoprueba'],
					'FechaVencimiento'		=> $reg['fechavencimiento'],
					'SueldoBasico'			=> $reg['sueldobasico'],
					'Observaciones'			=> $reg['observaciones'],

					'ModalidadTrabajo'		=> $reg['modalidadtrabajo']
				);
				$data['mensajeError'] = '';
			}
			else
				$data['mensajeError'] = 'Empleado no existe en la base de datos';

			// EXPERIENCIA LABORAL
			$query = <<<EOD
				SELECT EXPERIENCIALABORAL.* 
					FROM EXPERIENCIALABORAL 
						INNER JOIN EMPLEADOS 
							ON EXPERIENCIALABORAL.IdEmpleado = EMPLEADOS.Id 
					WHERE EMPLEADOS.Id = $IdEmpleado;
			EOD;

			$dataExp = $this->model->listar($query);

			if ($dataExp)
			{
				for ($i = 0; $i < count($dataExp); $i++) 
				{ 
					$data['regEmp'][$i] = array(
						'Id' 				=> $dataExp[$i]['id'],
						'Empresa' 			=> $dataExp[$i]['empresa'],
						'IdCiudad'			=> $dataExp[$i]['idciudad'],
						'Cargo' 			=> $dataExp[$i]['cargo'],
						'JefeInmediato' 	=> $dataExp[$i]['jefeinmediato'],
						'Telefono' 			=> $dataExp[$i]['telefono'],
						'FechaIngreso' 		=> $dataExp[$i]['fechaingreso'],
						'FechaRetiro' 		=> $dataExp[$i]['fecharetiro'],
						'Responsabilidades' => $dataExp[$i]['responsabilidades']
					);
				}
			}
			else
				$data['regEmp'] = false;

			// EDUCACION FORMAL
			$query = <<<EOD
				SELECT EDUCACIONEMPLEADO.Id, 
						EDUCACIONEMPLEADO.CentroEducativo, 
						PARAMETROS.Detalle AS NivelAcademico, 
						PARAMETROS2.Detalle AS Estado, 
						EDUCACIONEMPLEADO.Estudio, 
						EDUCACIONEMPLEADO.AnoInicio, 
						EDUCACIONEMPLEADO.MesInicio, 
						EDUCACIONEMPLEADO.AnoFinalizacion, 
						EDUCACIONEMPLEADO.MesFinalizacion 
					FROM EDUCACIONEMPLEADO 
						INNER JOIN EMPLEADOS 
							ON EDUCACIONEMPLEADO.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN PARAMETROS 
							ON EDUCACIONEMPLEADO.NivelAcademico = PARAMETROS.Id
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EDUCACIONEMPLEADO.Estado = PARAMETROS2.Id
						WHERE EMPLEADOS.Id = $IdEmpleado AND 
							EDUCACIONEMPLEADO.TipoEducacion = 1; 
			EOD;

			$dataEduF = $this->model->listar($query);

			if ($dataEduF)
			{
				for ($i = 0; $i < count($dataEduF); $i++) 
				{ 
					$data['regEduF'][$i] = array(
						'Id' 					=> $dataEduF[$i]['Id'],
						'CentroEducativo' 		=> $dataEduF[$i]['CentroEducativo'],
						'NivelAcademico' 		=> $dataEduF[$i]['NivelAcademico'],
						'Estado' 				=> $dataEduF[$i]['Estado'],
						'Estudio' 				=> $dataEduF[$i]['Estudio'],
						'AnoInicio' 			=> $dataEduF[$i]['AnoInicio'],
						'MesInicio' 			=> $dataEduF[$i]['MesInicio'],
						'AnoFinalizacion' 		=> $dataEduF[$i]['AnoFinalizacion'],
						'MesFinalizacion' 		=> $dataEduF[$i]['MesFinalizacion']
					);
				}
			}
			else
				$data['regEduF'] = false;

			// EDUCACION NO FORMAL
			$query = <<<EOD
				SELECT EDUCACIONEMPLEADO.Id, 
						EDUCACIONEMPLEADO.CentroEducativo, 
						PARAMETROS.Detalle AS NivelAcademico, 
						PARAMETROS2.Detalle AS Estado, 
						EDUCACIONEMPLEADO.Estudio, 
						EDUCACIONEMPLEADO.AnoInicio, 
						EDUCACIONEMPLEADO.MesInicio, 
						EDUCACIONEMPLEADO.AnoFinalizacion, 
						EDUCACIONEMPLEADO.MesFinalizacion 
					FROM EDUCACIONEMPLEADO 
						INNER JOIN EMPLEADOS 
							ON EDUCACIONEMPLEADO.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN PARAMETROS 
							ON EDUCACIONEMPLEADO.NivelAcademico = PARAMETROS.Id
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EDUCACIONEMPLEADO.Estado = PARAMETROS2.Id
						WHERE EMPLEADOS.Id = $IdEmpleado AND 
							EDUCACIONEMPLEADO.TipoEducacion = 2; 
			EOD;

			$dataEduNF = $this->model->listar($query);

			if ($dataEduNF)
			{
				for ($i = 0; $i < count($dataEduNF); $i++) 
				{ 
					$data['regEduNF'][$i] = array(
						'Id' 					=> $dataEduNF[$i]['Id'],
						'CentroEducativo' 		=> $dataEduNF[$i]['CentroEducativo'],
						'NivelAcademico' 		=> $dataEduNF[$i]['NivelAcademico'],
						'Estado' 				=> $dataEduNF[$i]['Estado'],
						'Estudio' 				=> $dataEduNF[$i]['Estudio'],
						'AnoInicio' 			=> $dataEduNF[$i]['AnoInicio'],
						'MesInicio' 			=> $dataEduNF[$i]['MesInicio'],
						'AnoFinalizacion' 		=> $dataEduNF[$i]['AnoFinalizacion'],
						'MesFinalizacion' 		=> $dataEduNF[$i]['MesFinalizacion']
					);
				}
			}
			else
				$data['regEduNF'] = false;

			// IDIOMAS
			$query = <<<EOD
				SELECT IDIOMASEMPLEADO.Id, 
						IDIOMAS.Nombre AS Idioma,
						PARAMETROS.Detalle AS NivelIdioma 
					FROM IDIOMASEMPLEADO 
						INNER JOIN IDIOMAS
							ON IDIOMASEMPLEADO.IdIdioma = IDIOMAS.Id 
						INNER JOIN PARAMETROS
							ON IDIOMASEMPLEADO.Nivel = PARAMETROS.Id 
					WHERE IDIOMASEMPLEADO.IdEmpleado = $IdEmpleado;
			EOD;

			$dataIdiomas = $this->model->listar($query);

			if ($dataIdiomas)
			{
				for ($i = 0; $i < count($dataIdiomas); $i++) 
				{ 
					$data['regIdiomas'][$i] = array(
						'Id' 				=> $dataIdiomas[$i]['Id'],
						'Idioma' 			=> $dataIdiomas[$i]['Idioma'],
						'NivelIdioma' => $dataIdiomas[$i]['NivelIdioma']
					);
				}
			}
			else
				$data['regIdiomas'] = false;

			// OTROS CONOCIMIENTOS
			$query = <<<EOD
				SELECT OTROSCONOCIMIENTOSEMPLEADO.*, 
						PARAMETROS.Detalle AS NombreNivelConocimiento 
					FROM OTROSCONOCIMIENTOSEMPLEADO 
						INNER JOIN EMPLEADOS 
							ON OTROSCONOCIMIENTOSEMPLEADO.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN PARAMETROS
							ON OTROSCONOCIMIENTOSEMPLEADO.Nivel = PARAMETROS.Id 
					WHERE EMPLEADOS.Id = $IdEmpleado;
			EOD;

			$dataOCE = $this->model->listar($query);

			if ($dataOCE)
			{
				for ($i = 0; $i < count($dataOCE); $i++) 
				{ 
					$data['regOCE'][$i] = array(
						'Id' 						=> $dataOCE[$i]['id'],
						'Conocimiento' 				=> $dataOCE[$i]['conocimiento'],
						'Nivel' 					=> $dataOCE[$i]['nivel'],
						'NombreNivelConocimiento' 	=>  $dataOCE[$i]['NombreNivelConocimiento']
					);
				}
			}
			else
				$data['regOCE'] = false;

			// CONTACTOS EMPLEADO
			$query = <<<EOD
				SELECT CONTACTOSEMPLEADO.*, 
						PARAMETROS.Detalle AS NombreParentesco 
					FROM CONTACTOSEMPLEADO 
						INNER JOIN EMPLEADOS 
							ON CONTACTOSEMPLEADO.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN PARAMETROS
							ON CONTACTOSEMPLEADO.Parentesco = PARAMETROS.Id 
					WHERE EMPLEADOS.Id = $IdEmpleado;
			EOD;

			$dataContactos = $this->model->listar($query);

			if ($dataContactos)
			{
				for ($i = 0; $i < count($dataContactos); $i++) 
				{ 
					$data['regContacto'][$i] = array(
						'Id' 					=> $dataContactos[$i]['id'],
						'NombreContacto'		=> $dataContactos[$i]['nombre'],
						'Telefono' 				=> $dataContactos[$i]['telefono'],
						'ParentescoContacto'	=> $dataContactos[$i]['parentesco'],
						'NombreParentesco' 		=> $dataContactos[$i]['NombreParentesco']
					);
				}
			}
			else
				$data['regContacto'] = false;

			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/HV';

			if (is_dir($cDirectorio))
			{
				$dir = opendir($cDirectorio);

				while (($archivo = readdir($dir)) !== false)
				{
					if ($archivo == '.' OR $archivo == '..')
						continue;

					if (strpos($archivo, $data['reg']['Documento'] . '_FOTOGRAFIA') !== FALSE)
						$data['reg']['Fotografia'] = TRUE;

					if (strpos($archivo, 'DOCUMENTO_IDENTIDAD') !== FALSE)
						$data['reg']['DocumentoIdentidad'] = TRUE;

					if (strpos($archivo, 'HOJA_DE_VIDA') !== FALSE)
						$data['reg']['HojaVida'] = TRUE;

					if (strpos($archivo, 'TARJETA_PROFESIONAL') !== FALSE)
						$data['reg']['TarjetaProfesional'] = TRUE;

					if (strpos($archivo, 'CERTIFICACION_BANCARIA') !== FALSE)
						$data['reg']['CertificacionBancaria'] = TRUE;
				}
			}

			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/SEGURIDAD_SOCIAL';

			if (is_dir($cDirectorio))
			{
				$dir = opendir($cDirectorio);

				while (($archivo = readdir($dir)) !== false)
				{
					if ($archivo == '.' OR $archivo == '..')
						continue;

					if (strpos($archivo, 'CERTIFICADO_FONDO_PENSIONES') !== FALSE)
						$data['reg']['CertificadoFP'] = TRUE;

					if (strpos($archivo, 'CERTIFICADO_EPS') !== FALSE)
						$data['reg']['CertificadoEPS'] = TRUE;

						if (strpos($archivo, 'CERTIFICADO_REGIMENES_EPS') !== FALSE)
						$data['reg']['CertificadoRegimenEps'] = TRUE;

					if (strpos($archivo, 'CERTIFICADO_FONDO_CESANTIAS') !== FALSE)
						$data['reg']['CertificadoFC'] = TRUE;
				}
			}

			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/SOPORTES_ACADEMICOS';

			if (is_dir($cDirectorio))
			{
				$dir = opendir($cDirectorio);

				while (($archivo = readdir($dir)) !== false)
				{
					if ($archivo == '.' OR $archivo == '..')
						continue;

					if (strpos($archivo, 'CERTIFICADO_ACADEMICO') !== FALSE)
						$data['reg']['CertificadosAcademicos'] = TRUE;
				}
			}

			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/SOPORTES_LABORALES';

			if (is_dir($cDirectorio))
			{
				$dir = opendir($cDirectorio);

				while (($archivo = readdir($dir)) !== false)
				{
					if ($archivo == '.' OR $archivo == '..')
						continue;

					if (strpos($archivo, 'CERTIFICADO_LABORAL') !== FALSE)
						$data['reg']['CertificadosLaborales'] = TRUE;
				}
			}

			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/GRUPOS_POBLACIONALES';

			if (is_dir($cDirectorio))
			{
				$dir = opendir($cDirectorio);

				while (($archivo = readdir($dir)) !== false)
				{
					if ($archivo == '.' OR $archivo == '..')
						continue;

					if (strpos($archivo, 'EN_POBREZA_EXTREMA') !== FALSE)
						$data['reg']['EnPobrezaExtrema'] = TRUE;

					if (strpos($archivo, 'DESPLAZADO') !== FALSE)
						$data['reg']['Desplazado'] = TRUE;

					if (strpos($archivo, 'EN_REINCORPORACION') !== FALSE)
						$data['reg']['EnReincorporacion'] = TRUE;

					if (strpos($archivo, 'ADULTO_MAYOR') !== FALSE)
						$data['reg']['AdultoMayor'] = TRUE;

					if (strpos($archivo, 'CABEZA_HOGAR') !== FALSE)
						$data['reg']['CabezaHogar'] = TRUE;

					if (strpos($archivo, 'DISCAPACITADO') !== FALSE)
						$data['reg']['Discapacitado'] = TRUE;

					if (strpos($archivo, 'COMUNIDAD_LGBTI') !== FALSE)
						$data['reg']['ComunidadLGBTI'] = TRUE;

					if (strpos($archivo, 'NEGRITUDES') !== FALSE)
						$data['reg']['Negritudes'] = TRUE;

					if (strpos($archivo, 'INDIGENAS') !== FALSE)
						$data['reg']['Indigenas'] = TRUE;
				}
			}

			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/ANTECEDENTES';

			if (is_dir($cDirectorio))
			{
				$dir = opendir($cDirectorio);

				while (($archivo = readdir($dir)) !== false)
				{
					if ($archivo == '.' OR $archivo == '..')
						continue;

					if (strpos($archivo, 'ANTECEDENTES_PROCURADURIA') !== FALSE)
						$data['reg']['AntecedentesProcuraduria'] = TRUE;
					
					if (strpos($archivo, 'CERTIFICADO_APRENDIZ') !== FALSE)
						$data['reg']['certificadoAprendiz'] = TRUE;

					if (strpos($archivo, 'ANTECEDENTES_CONTRALORIA') !== FALSE)
						$data['reg']['AntecedentesContraloria'] = TRUE;

					if (strpos($archivo, 'ANTECEDENTES_POLICIA') !== FALSE)
						$data['reg']['AntecedentesPolicia'] = TRUE;

					if (strpos($archivo, 'INHABILIDDES_SEXUALES') !== FALSE)
						$data['reg']['InhabilidadesSexuales'] = TRUE;

					if (strpos($archivo, 'CONSULTA_INFOLAFT') !== FALSE)
						$data['reg']['ConsultaInfolaft'] = TRUE;

					if (strpos($archivo, 'CONSULTA_INFOLAFT') !== FALSE)
						$data['reg']['ConsultaInfolaft'] = TRUE;
				}
			}

			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/PRUEBAS_SICOTECNICAS';

			if (is_dir($cDirectorio))
			{
				$dir = opendir($cDirectorio);

				while (($archivo = readdir($dir)) !== false)
				{
					if ($archivo == '.' OR $archivo == '..')
						continue;

					if (strpos($archivo, 'INFORME_SELECCION') !== FALSE)
						$data['reg']['InformeSeleccion'] = TRUE;

					if (strpos($archivo, 'CASO_SERVICE_DESK') !== FALSE)
						$data['reg']['casoArandaServiceDesk'] = TRUE;	

					if (strpos($archivo, 'ENTREVISTA_TECNICA') !== FALSE)
						$data['reg']['EntrevistaTecnica'] = TRUE;

					if (strpos($archivo, 'ESTUDIO_SEGURIDAD') !== FALSE)
						$data['reg']['EstudioSeguridad'] = TRUE;

					if (strpos($archivo, 'PRUEBA_360') !== FALSE)
						$data['reg']['Prueba360'] = TRUE;

					if (strpos($archivo, 'PRUEBA_TECNICA') !== FALSE)
						$data['reg']['PruebaTecnica'] = TRUE;

					if (strpos($archivo, 'PRUEBA_OPTIMO') !== FALSE)
						$data['reg']['PruebaOptimo'] = TRUE;

					if (strpos($archivo, 'PRUEBA_ORTOGRAFIA') !== FALSE)
						$data['reg']['PruebaOrtografia'] = TRUE;

					if (strpos($archivo, 'RUAF') !== FALSE)
						$data['reg']['RUAF'] = TRUE;

					if (strpos($archivo, 'EXAMEN_MEDICO') !== FALSE)
						$data['reg']['ExamenMedico'] = TRUE;

					if (strpos($archivo, 'RECOMENDACIONES_MEDICAS') !== FALSE)
						$data['reg']['RecomendacionesMedicas'] = TRUE;
				}
			}

			return($data);
		}

		public function validarDatos($Id, $paso)
		{
			// SE CARGAN DATOS
			$data = $this->cargarDatos($Id);
			$data['mensajeError'] = '';

			if ($paso < 16)
			{
				$PasoInicial = $paso;
				$PasoFinal = $paso;
			}
			else
			{
				$PasoInicial = 1;
				$PasoFinal = $paso;
			}

			for ($i = $PasoInicial; $i <= $PasoFinal; $i++ )
			{
				switch ($i)
				{
					case 1:
						$data['reg']['TipoIdentificacion'] 	= isset($_REQUEST['TipoIdentificacion']) ? $_REQUEST['TipoIdentificacion'] : '';
						$data['reg']['Documento']			= isset($_REQUEST['Documento']) ? $_REQUEST['Documento'] : '';
						$data['reg']['FechaExpedicion']		= isset($_REQUEST['FechaExpedicion']) ? $_REQUEST['FechaExpedicion'] : '';
						$data['reg']['IdCiudadExpedicion']	= isset($_REQUEST['IdCiudadExpedicion']) ? $_REQUEST['IdCiudadExpedicion'] : '';
						$data['reg']['Apellido1']			= isset($_REQUEST['Apellido1']) ? $_REQUEST['Apellido1'] : '';
						$data['reg']['Apellido2']			= isset($_REQUEST['Apellido2']) ? $_REQUEST['Apellido2'] : '';
						$data['reg']['Nombre1']				= isset($_REQUEST['Nombre1']) ? $_REQUEST['Nombre1'] : '';
						$data['reg']['Nombre2']				= isset($_REQUEST['Nombre2']) ? $_REQUEST['Nombre2'] : '';
						$data['reg']['PoliticamenteExpuesta'] = isset($_REQUEST['PoliticamenteExpuesta']) ? 'true' : 'false';

						if	( empty($data['reg']['TipoIdentificacion']) )
							$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de identificación.') . '</strong><br>';

						if	( empty($data['reg']['Documento']) )
							$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento') . '</strong><br>';
						else
						{
							$Documento = $data['reg']['Documento'];
							$IdEmpleado = $data['reg']['Id'];

							$query = <<<EOD
								SELECT * FROM EMPLEADOS 
									WHERE EMPLEADOS.Documento = '$Documento' AND 
									EMPLEADOS.Id <> $IdEmpleado;
							EOD;

							$reg = $this->model->buscarCandidato($query);

							if ($reg) 
								$data['mensajeError'] .= '<strong>' . label('Documento') . '</strong> ' . label('ya existe') . '<br>';
						}

						if	( empty($data['reg']['FechaExpedicion']) )
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de expedición') . '</strong><br>';
						elseif ($data['reg']['FechaExpedicion'] >= date('Y-m-d'))
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de expedición correcta') . '</strong><br>';

						if	( empty($data['reg']['IdCiudadExpedicion']) )
							$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad de expedición') . '</strong><br>';
					
						if	( empty($data['reg']['Apellido1']) )
							$data['mensajeError'] .= label('Debe digitar el primer') . ' <strong>' . label('Apellido') . '</strong><br>';
					
						// if	( empty($data['reg']['Apellido2']) )
						// 	$data['mensajeError'] .= label('Debe digitar el segundo') . ' <strong>' . label('Apellido') . '</strong><br>';
					
						if	( empty($data['reg']['Nombre1']) )
							$data['mensajeError'] .= label('Debe digitar el primer') . ' <strong>' . label('Nombre') . '</strong><br>';
					
						// if	( empty($data['reg']['Nombre2']) )
						// 	$data['mensajeError'] .= label('Debe digitar el segundo') . ' <strong>' . label('Nombre') . '</strong><br>';
					
						break;

					case 2:
						$data['reg']['FechaNacimiento'] 	= isset($_REQUEST['FechaNacimiento']) ? $_REQUEST['FechaNacimiento'] : '';
						$data['reg']['IdCiudadNacimiento']	= isset($_REQUEST['IdCiudadNacimiento']) ? $_REQUEST['IdCiudadNacimiento'] : '';
						$data['reg']['Genero']				= isset($_REQUEST['Genero']) ? $_REQUEST['Genero'] : '';
						$data['reg']['EstadoCivil']			= isset($_REQUEST['EstadoCivil']) ? $_REQUEST['EstadoCivil'] : '';
						$data['reg']['FactorRH']			= isset($_REQUEST['FactorRH']) ? $_REQUEST['FactorRH'] : '';
						$data['reg']['LibretaMilitar']		= isset($_REQUEST['LibretaMilitar']) ? $_REQUEST['LibretaMilitar'] : '';
						$data['reg']['DistritoMilitar']		= isset($_REQUEST['DistritoMilitar']) ? $_REQUEST['DistritoMilitar'] : '';
						$data['reg']['LicenciaConduccion']	= isset($_REQUEST['LicenciaConduccion']) ? $_REQUEST['LicenciaConduccion'] : '';
						$data['reg']['TarjetaProfesional']	= isset($_REQUEST['TarjetaProfesional']) ? $_REQUEST['TarjetaProfesional'] : '';

						if	( empty($data['reg']['FechaNacimiento']) )
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de nacimiento') . '</strong><br>';
						elseif ($data['reg']['FechaNacimiento'] >= $data['reg']['FechaExpedicion'])
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de nacimiento correcta') . '</strong><br>';
					
						if	( empty($data['reg']['IdCiudadNacimiento']) )
							$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad de nacimiento') . '</strong><br>';
					
						if	( empty($data['reg']['Genero']) )
							$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Género') . '</strong><br>';
					
						if	( empty($data['reg']['EstadoCivil']) )
							$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Estado civil') . '</strong><br>';
					
						if	( empty($data['reg']['FactorRH']) )
							$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Factor RH') . '</strong><br>';
			
						break;

					case 3:
						$data['reg']['Direccion']		= isset($_REQUEST['Direccion']) ? $_REQUEST['Direccion'] : '';
						$data['reg']['Barrio']			= isset($_REQUEST['Barrio']) ? $_REQUEST['Barrio'] : '';
						$data['reg']['Localidad']		= isset($_REQUEST['Localidad']) ? $_REQUEST['Localidad'] : '';
						$data['reg']['IdCiudad']		= isset($_REQUEST['IdCiudad']) ? $_REQUEST['IdCiudad'] : '';
						$data['reg']['Email']			= isset($_REQUEST['Email']) ? $_REQUEST['Email'] : '';
						$data['reg']['Telefono']		= isset($_REQUEST['Telefono']) ? $_REQUEST['Telefono'] : '';
						$data['reg']['Celular']			= isset($_REQUEST['Celular']) ? $_REQUEST['Celular'] : '';

						if	( empty($data['reg']['Direccion']) )
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Dirección') . '</strong><br>';
					
						if	( empty($data['reg']['Barrio']) )
							$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Barrio') . '</strong><br>';
					
						if	( empty($data['reg']['IdCiudad']) )
							$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad') . '</strong><br>';
					
						if	( empty($data['reg']['Email']) )
							$data['mensajeError'] .= label('Debe digitar el') . ' <strong>' . label('E-mail') . '</strong><br>';
						elseif	( !filter_var($data['reg']['Email'], FILTER_VALIDATE_EMAIL) )
							$data['mensajeError'] .= label('Formato invalido de') . ' <strong>' . label('Email') . '</strong><br>';
					
						if	( empty($data['reg']['Celular']) )
							$data['mensajeError'] .= label('Debe digitar el número de') . ' <strong>' . label('Celular') . '</strong><br>';
						elseif (!is_numeric($data['reg']['Celular']))
							$data['mensajeError'] .= label('Formato invalido de') . ' <strong>' . label('Celular') . '</strong><br>';
						elseif (strlen($data['reg']['Celular']) < 10)
							$data['mensajeError'] .= '<strong>' . label('Celular') . ' </strong>'.label('debe tener al menos 10 digitos<br>');

						break;

					case 4:
						if	( empty($_REQUEST['IdEPS']) )
							$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('E.P.S.') . '</strong><br>';
						if	( empty($_REQUEST['IdFondoCesantias']) )
							$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de cesantías') . '</strong><br>';
						if	( empty($_REQUEST['IdFondoPensiones']) )
							$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de pensiones') . '</strong><br>';
						if	( empty($_REQUEST['IdBanco']) )
							$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Entidad bancaria') . '</strong><br>';
						if	( empty($_REQUEST['TipoCuentaBancaria']) )
							$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Tipo cuenta bancaria') . '</strong><br>';
						if	( empty($_REQUEST['CuentaBancaria']) )
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuenta bancaria') . '</strong><br>';

						if (empty($data['mensajeError'])) {
							$data['reg']['IdEPS']				= isset($_REQUEST['IdEPS']) ? $_REQUEST['IdEPS'] : 0;
							$data['reg']['IdFondoCesantias'] 	= isset($_REQUEST['IdFondoCesantias']) ? $_REQUEST['IdFondoCesantias'] : 0;
							$data['reg']['IdFondoPensiones'] 	= isset($_REQUEST['IdFondoPensiones']) ? $_REQUEST['IdFondoPensiones'] : 0;
							$data['reg']['IdBanco']				= isset($_REQUEST['IdBanco']) ? $_REQUEST['IdBanco'] : 0;
							$data['reg']['TipoCuentaBancaria'] 	= isset($_REQUEST['TipoCuentaBancaria']) ? $_REQUEST['TipoCuentaBancaria'] : 0;
							$data['reg']['CuentaBancaria']		= isset($_REQUEST['CuentaBancaria']) ? $_REQUEST['CuentaBancaria'] : 0;
							$data['reg']['CuentaBancaria2']		= isset($_REQUEST['CuentaBancaria2']) ? $_REQUEST['CuentaBancaria2'] : 0;
						}

						break;

					case 5:
						$data['reg']['IdCargo']				= isset($_REQUEST['IdCargo']) ? $_REQUEST['IdCargo'] : 0;
						$data['reg']['PerfilProfesional']	= isset($_REQUEST['PerfilProfesional']) ? $_REQUEST['PerfilProfesional'] : '';

						if	( $_SESSION['Login']['Perfil'] <> EMPLEADO AND empty($data['reg']['IdCargo']) )
							$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Cargo') . '</strong><br>';

						if	( empty($data['reg']['PerfilProfesional']) )
							$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Perfíl profesional') . '</strong><br>';

						break;

					case 6:
						$data['reg']['Empresa']			= isset($_REQUEST['Empresa']) ? $_REQUEST['Empresa'] : '';
						$data['reg']['IdCiudadEmpresa']	= isset($_REQUEST['IdCiudadEmpresa']) ? $_REQUEST['IdCiudadEmpresa'] : '';
						$data['reg']['CargoEmpresa']	= isset($_REQUEST['CargoEmpresa']) ? $_REQUEST['CargoEmpresa'] : '';
						$data['reg']['JefeInmediato']	= isset($_REQUEST['JefeInmediato']) ? $_REQUEST['JefeInmediato'] : '';
						$data['reg']['TelefonoEmpresa']	= isset($_REQUEST['TelefonoEmpresa']) ? $_REQUEST['TelefonoEmpresa'] : '';
						$data['reg']['FechaIngresoEmpresa']	= isset($_REQUEST['FechaIngresoEmpresa']) ? $_REQUEST['FechaIngresoEmpresa'] : '';
						$data['reg']['FechaRetiroEmpresa'] = isset($_REQUEST['FechaRetiroEmpresa']) ? $_REQUEST['FechaRetiroEmpresa'] : '';
						$data['reg']['Responsabilidades'] = isset($_REQUEST['Responsabilidades']) ? $_REQUEST['Responsabilidades'] : '';

						if	( ! empty($data['reg']['Empresa']) )
						{
							if	( empty($data['reg']['IdCiudadEmpresa']) )
								$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad de trabajo') . '</strong><br>';
					
							if	( empty($data['reg']['CargoEmpresa']) )
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Cargo en la empresa') . '</strong><br>';

							if	( empty($data['reg']['JefeInmediato']) )
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Jefe inmediato') . '</strong><br>';
					
							if	( empty($data['reg']['TelefonoEmpresa']) )
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Teléfono de la empresa') . '</strong><br>';
				
							if	( empty($data['reg']['FechaIngresoEmpresa']) )
								$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de ingreso') . '</strong><br>';
							else
								if	( ! empty($data['reg']['FechaRetiroEmpresa']) AND $data['reg']['FechaRetiroEmpresa'] < $data['reg']['FechaIngresoEmpresa'])
									$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de retiro') . '</strong> ' . label('posterior a la') . ' <strong>' . label('Fecha de ingreso') . '</strong><br>';
							
							if	( empty($data['reg']['Responsabilidades']) )
								$data['mensajeError'] .= label('Debe digitar unas') . ' <strong>' . label('Funciones del cargo') . '</strong><br>';
						}
						// else
						// {
						// 	$IdEmpleado = $data['reg']['Id'];

						// 	$query = <<<EOD
						// 		SELECT COUNT(*) AS Registros 
						// 			FROM EXPERIENCIALABORAL 
						// 			WHRE EXPERIENCIALABORAL.IdEmpleado = $IdEmpleado;
						// 	EOD;

						// 	$reg = $this->model->leer($query);

						// 	if ($reg['Registros'] == 0)
						// 		$data['mensajeError'] .= label('Debe digitar al menos una') . ' <strong>' . label('Experiencia laboral') . '</strong><br>';
						// }

						break;

					case 7:
						$data['reg']['CentroEducativoF'] 	= isset($_REQUEST['CentroEducativoF']) ? $_REQUEST['CentroEducativoF'] : '';
						$data['reg']['NivelAcademicoF']		= isset($_REQUEST['NivelAcademicoF']) ? $_REQUEST['NivelAcademicoF'] : '';
						$data['reg']['EstudioF']			= isset($_REQUEST['EstudioF']) ? $_REQUEST['EstudioF'] : '';
						$data['reg']['EstadoF'] 			= isset($_REQUEST['EstadoF']) ? $_REQUEST['EstadoF'] : '';
						$data['reg']['AnoInicioF']			= isset($_REQUEST['AnoInicioF']) ? $_REQUEST['AnoInicioF'] : '';
						$data['reg']['MesInicioF']			= isset($_REQUEST['MesInicioF']) ? $_REQUEST['MesInicioF'] : '';
						$data['reg']['AnoFinalizacionF']	= isset($_REQUEST['AnoFinalizacionF']) ? $_REQUEST['AnoFinalizacionF'] : '';
						$data['reg']['MesFinalizacionF']	= isset($_REQUEST['MesFinalizacionF']) ? $_REQUEST['MesFinalizacionF'] : '';

						if (! empty($data['reg']['CentroEducativoF'])) 
						{
							if	( empty($data['reg']['NivelAcademicoF']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Nivel académico') . '</strong><br>';
					
							if	( empty($data['reg']['EstadoF']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Estado de nivel académico') . '</strong><br>';

							if	( empty($data['reg']['EstudioF']) )
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Título o estudios realizados') . '</strong><br>';

							if	( empty($data['reg']['AnoInicioF']) )
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Año de inicio') . '</strong><br>';
					
							if	( empty($data['reg']['MesInicioF']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Mes de inicio') . '</strong><br>';
					
							if	( ! empty($data['reg']['AnoFinalizacionF']) )
							{
								if	( empty($data['reg']['MesFinalizacionF']) )
									$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Mes de finalización') . '</strong><br>';

								if ($data['reg']['AnoFinalizacionF'] < $data['reg']['AnoInicioF'])
									$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Año de finalización') . '</strong> ' . label('mayor o igual al') . ' <strong>' . label('Año de inicio') . '</strong><br>';

								if ($data['reg']['AnoInicioF'] == $data['reg']['AnoFinalizacionF'])
									if ($data['reg']['MesFinalizacionF'] < $data['reg']['MesInicioF'])
										$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Mes de finalización') . '</strong> ' . label('mayor al') . ' <strong>' . label('Mes de inicio') . '</strong><br>';
							}
						}
						else
						{
							$IdEmpleado = $data['reg']['Id'];

							$query = <<<EOD
								SELECT COUNT(*) AS Registros 
									FROM EDUCACIONEMPLEADO  
									WHERE EDUCACIONEMPLEADO.IdEmpleado = $IdEmpleado AND 
										EDUCACIONEMPLEADO.TipoEducacion = 1;
							EOD;

							$reg = $this->model->leer($query);

							if ($reg['Registros'] == 0)
								$data['mensajeError'] .= label('Debe digitar al menos una') . ' <strong>' . label('Educación formal') . '</strong><br>';
						}


						break;

					case 8:
						$data['reg']['CentroEducativoNF'] 	= isset($_REQUEST['CentroEducativoNF']) ? $_REQUEST['CentroEducativoNF'] : '';
						$data['reg']['NivelAcademicoNF']	= isset($_REQUEST['NivelAcademicoNF']) ? $_REQUEST['NivelAcademicoNF'] : '';
						$data['reg']['EstudioNF']			= isset($_REQUEST['EstudioNF']) ? $_REQUEST['EstudioNF'] : '';
						$data['reg']['EstadoNF'] 			= isset($_REQUEST['EstadoNF']) ? $_REQUEST['EstadoNF'] : '';
						$data['reg']['AnoInicioNF']			= isset($_REQUEST['AnoInicioNF']) ? $_REQUEST['AnoInicioNF'] : '';
						$data['reg']['MesInicioNF']			= isset($_REQUEST['MesInicioNF']) ? $_REQUEST['MesInicioNF'] : '';
						$data['reg']['AnoFinalizacionNF']	= isset($_REQUEST['AnoFinalizacionNF']) ? $_REQUEST['AnoFinalizacionNF'] : '';
						$data['reg']['MesFinalizacionNF']	= isset($_REQUEST['MesFinalizacionNF']) ? $_REQUEST['MesFinalizacionNF'] : '';

						if (! empty($data['reg']['CentroEducativoNF'])) 
						{
							if	( empty($data['reg']['NivelAcademicoNF']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Nivel académico') . '</strong><br>';
					
							if	( empty($data['reg']['EstadoNF']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Estado de nivel académico') . '</strong><br>';

							if	( empty($data['reg']['EstudioNF']) )
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Título o estudios realizados') . '</strong><br>';

							if	( empty($data['reg']['AnoInicioNF']) )
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Año de inicio') . '</strong><br>';
					
							if	( empty($data['reg']['MesInicioNF']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Mes de inicio') . '</strong><br>';
					
							if	( ! empty($data['reg']['AnoFinalizacionNF']) )
							{
								if	( empty($data['reg']['MesFinalizacionNF']) )
									$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Mes de finalización') . '</strong><br>';

								if ($data['reg']['AnoFinalizacionNF'] < $data['reg']['AnoInicioNF'])
									$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Año de finalización') . '</strong> ' . label('mayor o igual al') . ' <strong>' . label('Año de inicio') . '</strong><br>';

								if ($data['reg']['AnoInicioNF'] == $data['reg']['AnoFinalizacionNF'])
									if ($data['reg']['MesFinalizacionNF'] < $data['reg']['MesInicioNF'])
										$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Mes de finalización') . '</strong> ' . label('mayor al') . ' <strong>' . label('Mes de inicio') . '</strong><br>';
							}
						}

						break;

					case 9:
						$data['reg']['IdIdioma'] 	= isset($_REQUEST['IdIdioma']) ? $_REQUEST['IdIdioma'] : 0;
						$data['reg']['NivelIdioma']	= isset($_REQUEST['NivelIdioma']) ? $_REQUEST['NivelIdioma'] : 0;

						if (! empty($data['reg']['IdIdioma'])) 
						{
							if	( empty($data['reg']['NivelIdioma']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Nivel de idioma') . '</strong><br>';
						}
						else
						{
							$IdEmpleado = $data['reg']['Id'];

							$query = <<<EOD
								SELECT COUNT(*) AS Registros 
									FROM IDIOMASEMPLEADO  
									WHERE IDIOMASEMPLEADO.IdEmpleado = $IdEmpleado;
							EOD;

							$reg = $this->model->leer($query);

							if ($reg['Registros'] == 0)
								$data['mensajeError'] .= label('Debe digitar al menos un conocimiento de') . ' <strong>' . label('Idioma') . '</strong><br>';
						}

						break;

					case 10:
						$data['reg']['Conocimiento']	= isset($_REQUEST['Conocimiento']) ? $_REQUEST['Conocimiento'] : 0;
						$data['reg']['NivelConocimiento']	= isset($_REQUEST['NivelConocimiento']) ? $_REQUEST['NivelConocimiento'] : 0;

						if (! empty($data['reg']['Conocimiento'])) 
						{
							if	( empty($data['reg']['NivelConocimiento']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Nivel de conocimiento') . '</strong><br>';
						}

						break;

					case 11:
						$data['reg']['NombreContacto']		= isset($_REQUEST['NombreContacto']) ? $_REQUEST['NombreContacto'] : '';
						$data['reg']['TelefonoContacto']	= isset($_REQUEST['TelefonoContacto']) ? $_REQUEST['TelefonoContacto'] : '';
						$data['reg']['ParentescoContacto']	= isset($_REQUEST['ParentescoContacto']) ? $_REQUEST['ParentescoContacto'] : '';

						if (! empty($data['reg']['NombreContacto'])) 
						{
							if	( empty($data['reg']['TelefonoContacto']) )
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Teléfono de contacto') . '</strong><br>';

							if	( empty($data['reg']['ParentescoContacto']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Parentesco de contacto') . '</strong><br>';
						}
						else
						{
							$IdEmpleado = $data['reg']['Id'];

							$query = <<<EOD
								SELECT COUNT(*) AS Registros 
									FROM CONTACTOSEMPLEADO  
									WHERE CONTACTOSEMPLEADO.IdEmpleado = $IdEmpleado;
							EOD;

							$reg = $this->model->leer($query);

							if ($reg['Registros'] == 0)
								$data['mensajeError'] .= label('Debe digitar al menos un') . ' <strong>' . label('Contacto') . '</strong><br>';
						}

						break;

					case 12:
						if (empty($data['reg']['Fotografia']) AND empty($_FILES['Fotografia']['name']))
							$data['mensajeError'] .= label('Debe cargar una') . ' <strong>' . label('Fotografía') . '</strong><br>';

						if (empty($data['reg']['DocumentoIdentidad']) AND empty($_FILES['DocumentoIdentidad']['name']))
							$data['mensajeError'] .= label('Debe cargar un') . ' <strong>' . label('Documento de identidad') . '</strong><br>';

						if (empty($data['reg']['HojaVida']) AND empty($_FILES['HojaVida']['name']))
							$data['mensajeError'] .= label('Debe cargar una') . ' <strong>' . label('Hoja de vida') . '</strong><br>';

						// if (empty($data['reg']['TarjetaProfesional']) AND empty($_FILES['TarjetaProfesional']['name']))
						// 	$data['mensajeError'] .= label('Debe cargar una') . ' <strong>' . label('Tarjeta profesional') . '</strong><br>';

						if (empty($data['reg']['CertificacionBancaria']) AND empty($_FILES['CertificacionBancaria']['name']))
							$data['mensajeError'] .= label('Debe cargar una certificación de') . ' <strong>' . label('Cuenta bancaria') . '</strong><br>';

						// if (empty($data['reg']['CertificadoFP']) AND empty($_FILES['CertificadoFP']['name']))
						// 	$data['mensajeError'] .= label('Debe cargar un certificado de afiliación a') . ' <strong>' . label('Fondo de pensión') . '</strong><br>';

						// if (empty($data['reg']['CertificadoEPS']) AND empty($_FILES['CertificadoEPS']['name']))
						// 	$data['mensajeError'] .= label('Debe cargar un certificado de afiliación a') . ' <strong>' . label('EPS') . '</strong><br>';

						// if (empty($data['reg']['CertificadoFC']) AND empty($_FILES['CertificadoFC']['name']))
						// 	$data['mensajeError'] .= label('Debe cargar un certificado de afiliación a') . ' <strong>' . label('Fondo de cesantías') . '</strong><br>';

						if (empty($data['reg']['CertificadosAcademicos']) AND empty($_FILES['CertificadosAcademicos']['name']))
							$data['mensajeError'] .= label('Debe cargar una o más') . ' <strong>' . label('Certificaciones académicas') . '</strong><br>';

						// if (empty($data['reg']['CertificadosLaborales']) AND empty($_FILES['CertificadosLaborales']['name']))
						// 	$data['mensajeError'] .= label('Debe cargar una o más') . ' <strong>' . label('Certificaciones laborales') . '</strong><br>';

						break;

					case 13:
						break;

					case 14:
						break;

					case 15:
						if ($_SESSION['Login']['Perfil'] <> EMPLEADO)
						{
							$EstadoEmpleado = getRegistro('PARAMETROS', $data['reg']['Estado'])['detalle'];

							$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];

							$data['reg']['IdCargo']			= isset($_REQUEST['IdCargo']) ? $_REQUEST['IdCargo'] : 0;
							$data['reg']['IdCentro']		= isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0;
							$data['reg']['IdProyecto']		= isset($_REQUEST['IdProyecto']) ? $_REQUEST['IdProyecto'] : 0;
							$data['reg']['Vicepresidencia']	= isset($_REQUEST['Vicepresidencia']) ? $_REQUEST['Vicepresidencia'] : 0;
							$data['reg']['IdSede']			= isset($_REQUEST['IdSede']) ? $_REQUEST['IdSede'] : 0;
							$data['reg']['TipoContrato']	= isset($_REQUEST['TipoContrato']) ? $_REQUEST['TipoContrato'] : 0;
							if (strpos($_SERVER['REDIRECT_URL'], 'editar2') === FALSE)
							{
								$data['reg']['FechaIngreso']	= isset($_REQUEST['FechaIngreso']) ? $_REQUEST['FechaIngreso'] : NULL;
								$data['reg']['FechaPeriodoPrueba']	= isset($_REQUEST['FechaPeriodoPrueba']) ? $_REQUEST['FechaPeriodoPrueba'] : NULL;
								$data['reg']['FechaVencimiento']	= isset($_REQUEST['FechaVencimiento']) ? $_REQUEST['FechaVencimiento'] : NULL;
							}
							$data['reg']['ModalidadTrabajo']	= isset($_REQUEST['ModalidadTrabajo']) ? $_REQUEST['ModalidadTrabajo'] : 0;
							$data['reg']['SueldoBasico']	= isset($_REQUEST['SueldoBasico']) ? $_REQUEST['SueldoBasico'] : 0;
							$data['reg']['Observaciones']	= isset($_REQUEST['Observaciones']) ? $_REQUEST['Observaciones'] : '';
							$data['reg']['SubsidioTransporte']	= 0;
							$data['reg']['PeriodicidadPago']	= 10;
							$data['reg']['HorasMes']			= getHoursMonth();
							$data['reg']['DiasAno']				= 360;

							if	(empty($data['reg']['IdCargo']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Cargo') . '</strong><br>';

							if	(empty($data['reg']['IdCentro']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Centro de costos') . '</strong><br>';
							else
								$Centro = getRegistro('CENTROS', $data['reg']['IdCentro'])['centro'];

							if	(! empty($data['reg']['IdProyecto']) AND $Centro <> '04099')
								$data['mensajeError'] .= label('Centro de costo debe ser 04099 si define un') . ' <strong>' . label('Proyecto') . '</strong><br>';

							if	(empty($data['reg']['Vicepresidencia']) )
								$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Vicepresidencia') . '</strong><br>';

							if	(empty($data['reg']['IdSede']) )
								$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Sede') . '</strong><br>';

							if	(empty($data['reg']['TipoContrato']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de contrato') . '</strong><br>';

							if (strpos($_SERVER['REDIRECT_URL'], 'editar2') === FALSE)
							{
								if	(empty($data['reg']['FechaIngreso']) )
									$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de ingreso') . '</strong><br>';
								elseif ($data['reg']['FechaIngreso'] < date('Y-m-d'))
									$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de ingreso') . '</strong> posterior a hoy<br>';

								if	(empty($data['reg']['FechaPeriodoPrueba']) )
									$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de período de prueba') . '</strong><br>';
								elseif ($data['reg']['FechaPeriodoPrueba'] <= $data['reg']['FechaIngreso'])
									$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de período de prueba') . '</strong> posterior a la fecha de ingreso<br>';
						
								if (! empty($data['reg']['TipoContrato']))
								{
									$TipoContrato = getRegistro('PARAMETROS', $data['reg']['TipoContrato'])['detalle'];

									switch ($TipoContrato)
									{
										case 'INDEFINIDO':
											if (! empty($data['reg']['FechaVencimiento']))
												$data['mensajeError'] .= '<strong>' . label('Fecha de vencimiento') . '</strong> ' . label('debe estar en blanco') . '<br>';

											$FechaPeriodoPrueba =  date('Y-m-d', strtotime($data['reg']['FechaIngreso'] . ' + 60 days'));

											if ($data['reg']['FechaPeriodoPrueba'] > $FechaPeriodoPrueba)
												$data['mensajeError'] .= '<strong>' . label('Fecha en período de prueba') . '</strong> ' . label('no puede ser posterior a') . ' ' .$FechaPeriodoPrueba . '<br>';
											
											break;

										case 'TERMINO FIJO':
										case 'DE LABOR U OBRA CONTRATADA':
											$FechaVencimiento = new DateTime($data['reg']['FechaVencimiento']);
											$FechaIngreso = new DateTime($data['reg']['FechaIngreso']);
											
											$dias = $FechaVencimiento->diff($FechaIngreso)->days;
											$dias = min($dias / 5, 60);
											
											$FechaPeriodoPrueba = date('Y-m-d', strtotime($data['reg']['FechaIngreso'] . ' + ' . intdiv($dias, 1) . ' days'));

											if ($data['reg']['FechaPeriodoPrueba'] > $FechaPeriodoPrueba)
												$data['mensajeError'] .= '<strong>' . label('Fecha en período de prueba') . '</strong> ' . label('no puede ser posterior a') . ' ' . $FechaPeriodoPrueba . '<br>';

											break;

										default:  // APRENDIZ SENA
											$FechaVencimiento = new DateTime($data['reg']['FechaVencimiento']);
											$FechaIngreso = new DateTime($data['reg']['FechaIngreso']);
											
											$dias = $FechaVencimiento->diff($FechaIngreso)->days;
											$dias = min(intdiv($dias, 5), 90);

											$FechaPeriodoPrueba = date('Y-m-d', strtotime($data['reg']['FechaIngreso'] . '+ ' . intdiv($dias, 1) . ' days'));

											if ($data['reg']['FechaPeriodoPrueba'] > $FechaPeriodoPrueba)
												$data['mensajeError'] .= '<strong>' . label('Fecha en período de prueba') . '</strong> ' . label('no puede ser posterior a') . ' ' . $FechaPeriodoPrueba . '<br>';

											break;
									}

									if	($TipoContrato <> 'INDEFINIDO' AND empty($data['reg']['FechaVencimiento']) )
									{
										$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de vencimiento') . '</strong><br>';
									
										if($data['reg']['FechaVencimiento'] <= $data['reg']['FechaPeriodoPrueba'])
											$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de vencimiento') . '</strong> posterior a la fecha del período de prueba.<br>';
									}
								}
							}

							if	(empty($data['reg']['ModalidadTrabajo']) )
							{
								$ModalidadTrabajo = getId('PARAMETROS', "PARAMETROS.Parametro = 'ModalidadTrabajo' AND PARAMETROS.Detalle = 'SUELDO BÁSICO'");
								$data['reg']['ModalidadTrabajo'] = $ModalidadTrabajo;
							}

							if ($data['reg']['SueldoBasico'] <= 0)
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo básico') . '</strong><br>';
							else
							{
								$IdCargo = $data['reg']['IdCargo'];

								$query = <<<EOD
									SELECT CARGOS.SueldoMinimo, 
											CARGOS.SueldoMaximo 
										FROM CARGOS 
										WHERE CARGOS.Id = $IdCargo;
								EOD;

								$reg = $this->model->leer($query);

								if ($reg)
								{
									if ($reg['SueldoMinimo'] <> 0 AND $reg['SueldoMaximo'] <> 0) 
									{
										if ($data['reg']['SueldoBasico'] < $reg['SueldoMinimo'] OR 
											$data['reg']['SueldoBasico'] > $reg['SueldoMaximo']) 
											$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo básico') . '</strong> ' . label('válido para este cargo') . '<br>';
									}
								}
					
								if ($data['reg']['TipoContrato'] == 3 OR $data['reg']['TipoContrato'] == 5 OR $data['reg']['TipoContrato'] == 6 OR $data['reg']['TipoContrato'] == 7)  // APRENDIZ SENA
									if ($data['reg']['SueldoBasico'] < $SueldoMinimo) 
										$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo básico') . '</strong> ' . label('mayor o igual a medio sueldo mínimo legal') . '<br>';
							}

							if ($data['reg']['SueldoBasico'] <= $SueldoMinimo * 2)
								$data['reg']['SubsidioTransporte'] = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'SUBSIDIO COMPLETO'");
							else
								$data['reg']['SubsidioTransporte'] = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'NO RECIBE SUBSIDIO'");
						}

						break;

					case 16:
						break;
				}
			}

			return($data);
		}

		public function editar2($Id)
		{
			if ($_SESSION['Login']['Perfil'] <> EMPLEADO)
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
				$_SESSION['Lista'] = SERVERURL . '/candidatos/lista/1';
			}
			else
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
			}

			if (! isset($_SESSION['Paso']))
				$_SESSION['Paso'] = 1;

			$paso = $_SESSION['Paso'];

			if (isset($_REQUEST['Action']))
			{
				$_SESSION['Paso'] = str_replace('_', '', substr($_REQUEST['Action'], -2)); 
				$paso = $_SESSION['Paso'];

				switch ($_REQUEST['Action'])
				{
					case 'DOCUMENTACION_16':
						$this->documentacion($Id);
						break;

					case 'NOCALIFICADO_16':
						$this->nocalificado($Id);
						break;
				}

				$data = $this->cargarDatos($Id);

				if (substr($_REQUEST['Action'], 0, 5) == 'PASO_')
				{
					$_SESSION['Paso'] = str_replace('_', '', substr($_REQUEST['Action'], -2)); 
					$paso = $_SESSION['Paso'];

					if ($paso == 14 AND $_SESSION['Login']['Perfil'] == EMPLEADO)
					{
						$_SESSION['Paso'] = 15; 
						$paso = $_SESSION['Paso'];
					}

					$data = $this->cargarDatos($Id);
					$data['mensajeError'] = '';
					$data['paso'] = $paso;
					$this->views->getView($this, 'actualizar2', $data);
					exit();
				}

				if ($_REQUEST['Action'] == 'FINALIZAR')
				{
					$data = $this->validarDatos($Id, 16);

					if (empty($data['mensajeError']))
						$this->finalizar($Id);
				}

				if (substr($_REQUEST['Action'], 0, 7) == 'BORRAR_')
				{
					$i = str_replace('_', '', substr(str_replace(substr($_REQUEST['Action'], -3), '', $_REQUEST['Action']), -2));

					$this->borrarDatos($data, $paso, $i);

					$data = $this->cargarDatos($Id);
				}
				elseif (empty($data['mensajeError']) OR (! empty($data['mensajeError']) AND ($paso == 12 OR $paso == 13)))
				{
					$data = $this->validarDatos($Id, $paso);

					if (empty($data['mensajeError']))
					{
						if ($paso == 15)
							$resp = $this->model->actualizarCondiciones($data['reg'], $Id);
						else
							$resp = $this->model->actualizarCandidato($data['reg'], $Id);

						$data = $this->cargarDatos($Id);
					}
				}

				if ($_REQUEST['Action'] == 'GUARDAR')
				{
					if	( empty($data['mensajeError']) )
					{
						$data = $this->cargarDatos($Id);
						$data['mensajeError'] = '';
						$_SESSION['Paso'] = $paso;
					}

					$_REQUEST['Action'] = '';
				}

				if ($_REQUEST['Action'] == 'AVANZAR')
				{
					if	( empty($data['mensajeError']) )
					{
						$paso++;

						if ($paso == 14 AND $_SESSION['Login']['Perfil'] == EMPLEADO)
							$paso++;

						$data = $this->cargarDatos($ID);
						$data['mensajeError'] = '';
						$_SESSION['Paso'] = $paso;
					}

					$_REQUEST['Action'] = '';
				}

				if ($_REQUEST['Action'] == 'RETROCEDER')
				{
					$paso--;

					if ($paso == 14 AND $_SESSION['Login']['Perfil'] == EMPLEADO)
						$paso--;

					$data = $this->cargarDatos($Id);
					$data['mensajeError'] = '';
					$_SESSION['Paso'] = $paso;
					$_REQUEST['Action'] = '';
				}

				if ($_REQUEST['Action'] == 'DESISTIR')
				{
					$this->desistir($Id);
				}

				$this->views->getView($this, 'actualizar2', $data);
				exit;
			}
			else 
			{
				$data = $this->cargarDatos($Id);
				$data['mensajeError'] = '';
				$data['paso'] = $paso;
				$this->views->getView($this, 'actualizar2', $data);
			}
		}

		public function documentacion($Id)
		{
			$query = <<<EOD
				UPDATE EMPLEADOS
					SET
						SEL_DocumentosActualizados = 1 
					WHERE EMPLEADOS.Id = $Id;
			EOD;

			$this->model->query($query);

			header('Location: ' . SERVERURL . '/candidatos/lista/1');
			exit();
		}

		public function nocalificado($Id)
		{
			// ENVIO DE CORREO

			// ACTUALIZAR LOG
			$regEmpleado = getRegistro('EMPLEADOS', $Id);

			// BUSCAMOS EL ESTADO ACTUAL DEL EMPLEADO 
			$EstadoActual = getRegistro('PARAMETROS', $regEmpleado['estado'])['detalle'];
			$EstadoNuevo = 'CANDIDATO NO CALIFICADO';

			$data = array($Id, 'ESTADO', $EstadoActual, $EstadoNuevo, $_SESSION['Login']['Id']);

			$resp = $this->model->guardarLogEmpleado($data);

			//ACTUALIZACIÓN ESTADO EMPLEADO
			$EstadoNuevo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = '$EstadoNuevo'")['id'];

			$query = <<<EOD
				UPDATE EMPLEADOS
					SET
						Estado = $EstadoNuevo 
					WHERE EMPLEADOS.Id = $Id;
			EOD;

			$this->model->query($query);

			header('Location: ' . SERVERURL . '/candidatos/lista/1');
			exit();
		}

		public function borrar($id)
		{
			$query = 'SELECT * FROM EMPLEADOS WHERE EMPLEADOS.Id = ' . $id;
				
			$data['reg'] = $this->model->leer($query);
			$data['mensajeError'] = '';

			if (isset($_REQUEST['id']))
			{
				// $query = 'SELECT COUNT(*) AS Registros ' .
				// 		'FROM NITS ' .
				// 		'WHERE NITS.IdBanco = ' . $id;

				// $reg = $this->model->buscarBanco($query);

				// if ($reg['registros'] > 0) 
				// {
				// 	$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Banco') . '</strong><br>';
				// }

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarCandidato($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/candidatos/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/candidatos/lista/' . $_SESSION['CANDIDATOS']['Pagina'];

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

		public function importar()
		{
			$data = array();
			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['Archivo_candidatos']['name']) )
				{
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				}
				else
				{
					ini_set('max_execution_time', 600);
					
					$archivo = $_FILES['Archivo_candidatos']['name'];
		
					if ( copy($_FILES['Archivo_candidatos']['tmp_name'], $archivo)) 
					{
						if ( file_exists ($archivo) )
						{
							// $oExcel = IOFactory::load($archivo);
							$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $Excel->getSheet(0);

							$mensajeError = '';
							$data['reg'] =  array();
							for ( $i = 2; $i <= $oHoja->getHighestRow() && empty($mensajeError); $i++ )
							{
								if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
								{
									$data['reg']['Documento'] 	= trim($oHoja->getCell('A' . $i)->getCalculatedValue());
									$data['reg']['Apellido1'] 	= trim($oHoja->getCell('B' . $i)->getCalculatedValue());
									$data['reg']['Apellido2'] 	= trim($oHoja->getCell('C' . $i)->getCalculatedValue());
									$data['reg']['Nombre1'] 	= trim($oHoja->getCell('D' . $i)->getCalculatedValue());
									$data['reg']['Nombre2'] 	= trim($oHoja->getCell('E' . $i)->getCalculatedValue());
									$data['reg']['Email'] 		= trim($oHoja->getCell('F' . $i)->getCalculatedValue());
									$data['reg']['Celular'] 	= trim($oHoja->getCell('G' . $i)->getCalculatedValue());



									$Documento 	= $data['reg']['Documento'];
									$Nombre 	= strtoupper($data['reg']['Nombre1'] . ' ' . $data['reg']['Nombre2'] . ' ' . $data['reg']['Apellido1'] . ' ' . $data['reg']['Apellido2']);
									$Email 		= $data['reg']['Email'];
									$Celular 	= $data['reg']['Celular'];

									if	( empty($data['reg']['Documento']) ){
										$mensajeError .= label('Debe digitar un') . ' <strong>' . label('Documento de identidad') . '</strong><br>';
									}else{
										$query = <<<EOD
											SELECT EMPLEADOS.Id 
												FROM EMPLEADOS 
													INNER JOIN PARAMETROS 
														ON EMPLEADOS.Estado = PARAMETROS.Id 
												WHERE EMPLEADOS.Documento = '$Documento' AND 
												PARAMETROS.Detalle <> 'RETIRADO'  AND 
												PARAMETROS.Detalle <> 'CANDIDATO DESISTE'   AND 
												PARAMETROS.Detalle <> 'CANDIDATO NO CALIFICADO';
										EOD;

										$reg = $this->model->leer($query);

										if ($reg) {
											$mensajeError .= '<strong>' . label('Documento '.$Documento) . '</strong> ' . label('ya existe') . '<br>';
										}
									}

									if	( empty($data['reg']['Apellido1']) ){
										$mensajeError.= label('Debe digitar un') . ' <strong>' . label('Primer apellido') . '</strong><br>';

									}if	( empty($data['reg']['Nombre1']) ){
										$mensajeError .= label('Debe digitar un') . ' <strong>' . label('Primer nombre') . '</strong><br>';

									}if	( empty($data['reg']['Email']) ){
										$mensajeError .= label('Debe digitar un') . ' <strong>' . label('Email') . '</strong><br>';
									}elseif	( !filter_var($data['reg']['Email'], FILTER_VALIDATE_EMAIL) ){
										$mensajeError .= label('Formato invalido de') . ' <strong>' . label('Email') . '</strong><br>';
									}else
									{
										$query = <<<EOD
											SELECT EMPLEADOS.Id  
												FROM EMPLEADOS 
												WHERE EMPLEADOS.Documento <> '$Documento' AND 
													EMPLEADOS.Email = '$Email';
										EOD;

										$reg = $this->model->leer($query);

										if ($reg){ 
											$mensajeError .= '<strong>' . label('Email'.$Email) . '</strong> ' . label('ya está registrado con otra persona') . '<br>';
										}
									}
								
									if	( empty($data['reg']['Celular']) ){
										$mensajeError .= label('Debe digitar un') . ' <strong>' . label('Celular') . '</strong><br>';
									}elseif (!is_numeric($data['reg']['Celular'])){
										$mensajeError .= label('Formato invalido de') . ' <strong>' . label('Celular') . '</strong><br>';
									}elseif (strlen($data['reg']['Celular']) < 10){
										$mensajeError .= '<strong>' . label('Celular') . ' </strong>'.label('debe tener al menos 10 digitos<br>');
									}if	(empty($mensajeError)){
										// BUSCAMOS EL ESTADO DEL EMPLEADO
										$EstadoEmpleado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'EN PROCESO DE SELECCION'");

										$data['reg']['Estado'] = $EstadoEmpleado;

										$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'DOCUMENTO'");

										$id = $this->model->guardarCandidato($data['reg']);

										if ($id) 
										{
											$regDocumento = getRegistro('PLANTILLAS', 0, "PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND PLANTILLAS.TipoPlantilla = $TipoPlantilla AND PLANTILLAS.Asunto = 'AUTORIZACION PARA EL TRATAMIENTO DE DATOS PERSONALES'");

											if ($regDocumento)
											{
												// ENVIO DE POLITICA DE TRATAMIENTO DE DATOS PARA FIRMA ELECTRONICA
												$Usuario = USER_FIRMA;
												$Clave 	 = CLAVE_FIRMA;

												$data_Firma = <<<EOD
													{
														"Usuario": "$Usuario",
														"Clave": "$Clave",
														"Firmantes": [
															{
																"Identificacion": "$Documento",
																"TipoIdentificacion": "CC",
																"Nombre": "$Nombre",
																"Correo": "$Email",
																"NroCelular": "$Celular",
																"Foto": "1",
																"FotoObligatoria": "1",
																"SolicitarAdjunto": "0"
															}
														],
												EOD;

												$archivos_base64 = '';

												// GENERACION DOCUMENTO EN PDF
												global $Asunto;
												global $CodigoDocumento;

												$Asunto 			= utf8_decode($regDocumento['asunto']);
												$Plantilla 			= $regDocumento['plantilla'];
												$CodigoDocumento 	= utf8_decode($regDocumento['codigodocumento']);

												// $Plantilla = str_replace('<<Logotipo>>', LOGO, $Plantilla);
												$Plantilla = str_replace('<<NombreEmpleado>>', $Nombre, $Plantilla);
												$Plantilla = str_replace('<<DocumentoIdentidad>>', $Documento, $Plantilla);
												$Plantilla = str_replace('<<Dia>>', date('d'), $Plantilla);
												$NombreMes = NombreMes(date('m'));
												$Plantilla = str_replace('<<NombreMes>>', $NombreMes, $Plantilla);
												$Plantilla = str_replace('<<Año>>', date('Y'), $Plantilla);
												$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);

												$Plantilla = utf8_decode($Plantilla);

												$pdf = new PDF2();
												
												$pdf->AliasNbPages();
												$pdf->SetAutoPageBreak(true, 25);
												$pdf->SetLeftMargin(25);
													
												$pdf->AddPage();
												$pdf->SetFont('Arial','',10);

												// $pdf->WriteHTML($Plantilla);
												$pdf->MultiCell(170, 5, $Plantilla, 0, 'J', FALSE);
												
												$cDirectorio = str_replace(" ","",'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1'] . '_' . $data['reg']['Apellido2'] . '_' . $data['reg']['Nombre1'] . '_' . $data['reg']['Nombre2']));

												if	( ! is_dir($cDirectorio) )
													mkdir($cDirectorio);

												$cDirectorio .= '/CONTRATOS';

												if	( ! is_dir($cDirectorio) )
													mkdir($cDirectorio);
							
												$pdf->Output('F', $cDirectorio . '/' . $data['reg']['Documento'] . '_' . strtoupper($Asunto) . '.PDF', TRUE); 

												// SE PREPARA EL ARCHIVO PARA FIRMA DIGITAL
												$Archivo  = utf8_decode($Asunto);
												$archivo1 = base64_encode(file_get_contents($cDirectorio . '/' . $data['reg']['Documento'] . '_' . strtoupper($Asunto) . '.PDF'));

												$archivos_base64 = <<<EOD
													"ArchivosPDF": [
														{
															"Nombre": "$Archivo", 
															"Documento_base64": "$archivo1"
														}
												EOD;

												$archivos_base64 .= <<<EOD
													]}
												EOD;

												$datos_firma = $data_Firma . $archivos_base64;

												// SE HACE EL ENVIO DE LOS DOCUMENTOS PARA FIRMA DIGITAL
												$curl = curl_init();

												curl_setopt_array($curl, 
													array(
														CURLOPT_URL => URL_FIRMA . 'signer',
														CURLOPT_RETURNTRANSFER => true,
														CURLOPT_ENCODING => '',
														CURLOPT_MAXREDIRS => 10,
														CURLOPT_TIMEOUT => 0,
														CURLOPT_FOLLOWLOCATION => true,
														CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
														CURLOPT_CUSTOMREQUEST => 'POST',
														CURLOPT_POSTFIELDS => $datos_firma,
														CURLOPT_HTTPHEADER => array(
															'Token: ' . TOKEN_FIRMA,
															'Content-Type: application/json'
														),
													)
												);

												$response = json_decode(curl_exec($curl), true);

												curl_close($curl);

												if ($response['Code'] == 1)
												{
													$SolicitudFirma = $response['Data']['NroSolicitud'];
													$FechaSolicitud = $response['Data']['Fecha'];
													
													$query = <<<EOD
														UPDATE EMPLEADOS 
															SET
																SolicitudFirma = $SolicitudFirma, 
																FechaSolicitud = '$FechaSolicitud' 
															WHERE EMPLEADOS.Id = $id;
													EOD;

													$this->model->query($query);

													// ENVIO DE CORREO ELECTRONICO
													$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'CORREO ELECTRÓNICO'");
									
													$query = <<<EOD
														SELECT PLANTILLAS.* 
															FROM PLANTILLAS 
															WHERE PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND 
																PLANTILLAS.TipoPlantilla = $TipoPlantilla AND 
																PLANTILLAS.Asunto = 'PROCESO DE SELECCIÓN';
													EOD;

													$reg = $this->model->leer($query);

													if ($reg) 
													{
														$Asunto = utf8_decode($reg['asunto']);
														$Plantilla = $reg['plantilla'];

														$Plantilla = str_replace('<<Logotipo>>', LOGOTIPO, $Plantilla);
														$Plantilla = str_replace('<<EnlacePortal>>', SERVERURL, $Plantilla);
														$Plantilla = str_replace('<<Email>>', $data['reg']['Email'], $Plantilla);
														$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);

														$Plantilla = utf8_decode($Plantilla);

														$mail = new PHPMailer\PHPMailer\PHPMailer(true);

														$mail->SMTPOptions = array(
															'ssl' => array(
																'verify_peer' => false,
																'verify_peer_name' => false,
																'allow_self_signed' => true
															)
														);

														$mail->SMTPDebug 		= 0;
														$mail->isSMTP();
														$mail->Host       		= HOST;
														$mail->Port       		= PORT;
														$mail->SMTPKeepAlive 	= true;          
														$mail->SMTPAuth   		= false;
														$mail->SMTPSecure 		= 'tls';  
														$mail->isHTML(true);

														$from = 'no-reply@comware.com.co';
														$fromName = 'GESTION HUMANA - COMWARE';

														$mail->Subject = $Asunto;
														$mail->addEmbeddedImage(LOGOTIPO, 'comware');
														$mail->Body = $Plantilla;
														$mail->AddAddress($data['reg']['Email']);
														$response = new stdClass();
														try 
														{
															$mail->setFrom($from, $fromName);
															$obj = (object) array(
																'CharSet' => $mail->CharSet,
																'ContentType' => $mail->ContentType,
																'Encoding' => $mail->Encoding,
																'From' => $mail->From,
																'FromName' => $mail->FromName,
																'Sender' => $mail->Sender,
																'Subject' => $mail->Subject,
																'Mailer' => $mail->Mailer,
																'Sendmail' => $mail->Sendmail,
																'Host' => $mail->Host,
																'SMTPOptions' => $mail->SMTPOptions,
																'smtp' => $mail->smtp,
																'to' => $mail->to);
																$response = $mail->send();
															logRequests("CANDIDATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $mail->to);
														} 
														catch (Exception $e) 
														{
															logRequests("CANDIDATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $data['reg']['Email']);
															$mensajeError .= "Error al enviar correo a $Email <br>";
															$mail->getSMTPInstance()->reset();
														}

														$mail->clearAddresses();
														$mail->clearAttachments();
													}
												}
												else
												{
													$CodigoError = $response['Code'];
													$MensajeError = $response['Message'];

													$mensajeError .= <<<EOD
														Error al tratar de enviar la AUTORIZACION PARA EL TRATAMIENTO DE DATOS PERSONALES para firma electrónica <br>
														Código de error: $CodigoError<br>
														Mensaje: $MensajeError<br>
													EOD;
												}
											}
											else
											{
												$mensajeError .= "No hay una plantilla de AUTORIZACION PARA EL TRATAMIENTO DE DATOS PERSONALES <br>";
											}
										}
									}

									$data['mensajeError'] = $mensajeError ." fila : ".$i;

								}


							}

							if (empty($mensajeError)){
								header('Location: ' . SERVERURL . '/candidatos/lista/1');
								exit;
							}else{
								$this->views->getView($this, 'importar', $data);
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/candidatos/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/candidatos/lista/' . $_SESSION['CANDIDATOS']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>
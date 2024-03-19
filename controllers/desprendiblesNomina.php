<?php
	require_once('./templates/vendor/autoload.php');

    require './templates/PHPMailer-master/src/PHPMailer.php';
    require './templates/PHPMailer-master/src/SMTP.php';
    require './templates/PHPMailer-master/src/Exception.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class desprendiblesNomina extends Controllers
	{
		public function parametros()
		{
			ini_set('max_execution_time', 0);

			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			// $reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");

			$Referencia 	= isset($_REQUEST['Referencia']) ? $_REQUEST['Referencia'] : $reg1['valor'];
			$IdPeriodicidad = isset($_REQUEST['Periodicidad']) ? $_REQUEST['Periodicidad'] : $reg2['valor'];
			$Periodicidad 	= getRegistro('PARAMETROS', $IdPeriodicidad)['detalle'];
			$cPeriodicidad 	= substr($Periodicidad, 0, 1);

			if (isset($_REQUEST['Periodo'])) 
			{
				$Periodo = $_REQUEST['Periodo'];

				$query = <<<EOD
					PERIODOS.Referencia = $Referencia AND 
					PERIODOS.Periodicidad = $IdPeriodicidad AND 
					PERIODOS.Periodo = $Periodo
				EOD;

				$regPeriodo = getRegistro('PERIODOS', 0, $query);
				$IdPeriodo	= $regPeriodo['id'];
			}
			else
			{
				$regPeriodo = getRegistro('PERIODOS', $reg3['valor']);
				$Periodo 	= $regPeriodo['periodo'];
				$IdPeriodo 	= $regPeriodo['id'];
			}

			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'Referencia' 	=> $Referencia, 
					'Periodicidad' 	=> $IdPeriodicidad, 
					'Periodo' 		=> $Periodo, 
					'IdCentro' 		=> isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0,
					'TipoEmpleados' => isset($_REQUEST['TipoEmpleados']) ? $_REQUEST['TipoEmpleados'] : 0, 
					'Empleado' 		=> isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : ''
					),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Referencia']))
			{
				$P_IdCentro 		= $_REQUEST['IdCentro'];
				$P_TipoEmpleados 	= $_REQUEST['TipoEmpleados'];
				$P_Empleado 		= $_REQUEST['Empleado'];
				$P_Ciclo 			= $_REQUEST['Ciclo'];
	
				$FechaInicial 	= $regPeriodo['fechainicial'];
				$FechaFinal		= $regPeriodo['fechafinal'];

				$query = <<<EOD
					WHERE EMPLEADOS.Email <> '' AND 
						ACUMULADOS.FechaInicialPeriodo >= '$FechaInicial' AND 
						ACUMULADOS.FechaFinalPeriodo <= '$FechaFinal' 
				EOD;

				if (! empty($P_IdCentro)) 
				{
					$query .= <<<EOD
						AND ACUMULADOS.IdCentro = $P_IdCentro 
					EOD;
				}

				if (! empty($P_Empleado)) 
				{
					$query .= <<<EOD
						AND EMPLEADOS.Documento = '$P_Empleado' 
					EOD;
				}

				if (! empty($P_TipEmpleados)) 
				{
					$query .= <<<EOD
						AND ACUMULADOS.TipoEmpleado = $P_TipoEmpleados 
					EOD;
				}

				if (! empty($P_Ciclo)) 
				{
					$query .= <<<EOD
						AND ACUMULADOS.Ciclo = $P_Ciclo 
					EOD;
				}

				$datos = $this->model->comprobantePago($query);

				if (count($datos) > 0) 
				{
					global $lcOrientacion;
					global $lcTitulo;
					global $lcSubTitulo;
					global $lcEncabezado;
					global $lcEncabezado2;
				
					$PDF = new PDF(); 
					$PDF->AliasNbPages();
				
					$lcTitulo = utf8_decode('COMPROBANTE DE PAGO DE NÓMINA');
					$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
					$lcEncabezado = '';
			
					$PDF->AddFont('Tahoma','','tahoma.php');
					$PDF->AddPage();
					$PDF->SetFont('Tahoma', '', 8);

					$EmpleadoAnt 			= 0;
					$NombreAnt 				= '';
					$EmailAnt 				= '';
					$NombreFormaDePagoAnt 	= '';
					$NombreBancoAnt			= '';
					$NombreTipoCuentaAnt 	= '';
					$CuentaBancariaAnt		= '';
					$TotalPagos 			= 0;
					$TotalDeducciones 		= 0;

					// SE CONFIGURA EL MAIL
					$mail = new PHPMailer\PHPMailer\PHPMailer(true);

					$mail->SMTPOptions = array(
						'ssl' => array(
							'verify_peer' => false,
							'verify_peer_name' => false,
							'allow_self_signed' => true
						)
					);

					$mail->SMTPDebug = 0;
					$mail->isSMTP();
					$mail->Host       = HOST;
					$mail->Port       = PORT;
					$mail->SMTPKeepAlive = true;          
					$mail->SMTPAuth   = false;
					$mail->SMTPSecure = 'tls';  
					$mail->isHTML(true);

					$from = 'recursos.humanos@comware.com.co';
					$fromName = 'GESTION HUMANA - COMWARE';
					$subject = utf8_decode('DESPRENDIBLE DE NÓMINA - ') . strtoupper(NombreMes(date('m', strtotime($FechaInicial))));
					$body = utf8_decode(<<<EOD
						Cordial saludo.<br><br>
						Adjunto encontrara su comprobante de pago.<br>
						En caso de tener dudas, por favor remitirse con el área de Compensación y beneficios a los correos;<br>
						Geraldinne.aguilar@comware.com.co <br>
						Analistagh@comware.com.co<br>
						Analistanomina@comware.com.co<br>
						Auxiliargh@comware.com.co<br><br>
						Este es un correo automático.<br><br>
						Feliz Día!<br>
					EOD);
			
					for ($i = 0; $i < count($datos); $i++)
					{
						$reg = $datos[$i];

						if ($reg['Documento'] <> $EmpleadoAnt) 
						{
							if (! empty($EmpleadoAnt)) 
							{
								$Archivo = './descargas/' . $EmpleadoAnt . '_ComprobantesDePago_' . strtoupper(NombreMes(date('m', strtotime($FechaInicial)))) . '_' . date('Y', strtotime($FechaInicial)) . '_' . date('YmdGis') . '.PDF';

								$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
								$PDF->Cell(105, 5, utf8_decode('TOTALES  '), 0, 0, 'R'); 
								$PDF->SetFont('Arial', 'B', 8); 
								$PDF->Cell(25, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
								$PDF->Cell(35, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
								$PDF->SetFont('Arial', '', 8); 
								$PDF->Ln(); 
								if ($NombreFormaDePagoAnt == 'TRANSFERENCIA BANCARIA') 
									if ($NombreTipoCuentaAnt == 'CUENTA DE AHORROS')
										$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreBancoAnt . ' ( AH ' . $CuentaBancariaAnt . ')'), 0, 0, 'R'); 
									else
										$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreBancoAnt . ' ( CC ' . $CuentaBancariaAnt . ')'), 0, 0, 'R'); 
								else
									$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreFormaDePagoAnt), 0, 0, 'R'); 
								$PDF->SetFont('Arial', 'B', 8); 
								$PDF->Cell(25, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
								$PDF->SetFont('Arial', '', 8); 
								$PDF->Ln(); 
								$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 

								$PDF->Output($Archivo, 'F'); 
								$response = new stdClass();
								// SE HACE EL ENVIO DEL CORREO
								try 
								{
									$mail->setFrom($from, $fromName);
									$mail->addAddress($EmailAnt, utf8_decode($NombreAnt));
									// $mail->addReplyTo('info@example.com', 'Information');
									// $mail->addCC('cc@example.com');
									// $mail->addBCC('bcc@example.com');

									$mail->Subject = $subject;
									$mail->Body    = $body;
									$mail->AltBody = $body;
									$mail->addAttachment($Archivo);
									// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

									
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
									logRequests("DESPRENDIBLE DE NOMINA",$body,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $mail->to);
								} 
								catch (Exception $e) 
								{
									logRequests("DESPRENDIBLE DE NOMINA",$body,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $EmailAnt);
									$data['mensajeError'] .= 'Error al enviar desprendible a ' . $NombreAnt . '<br>';
									$mail->getSMTPInstance()->reset();
								}

								$mail->clearAddresses();
    							$mail->clearAttachments();

								unset($PDF);

								$PDF = new PDF(); 
								$PDF->AliasNbPages();
							
								$lcTitulo = utf8_decode('COMPROBANTE DE PAGO DE NÓMINA');
								$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
								$lcEncabezado = '';
						
								$PDF->AddFont('Tahoma','','tahoma.php');
								$PDF->AddPage();
								$PDF->SetFont('Tahoma', '', 8);

								$TotalPagos = 0;
								$TotalDeducciones = 0;
							}

							$PDF->Cell(25, 7, utf8_decode('DOCUMENTO: '), 0, 0, 'L'); 
							$PDF->SetFont('Arial', 'B', 8); 
							$PDF->Cell(75, 7, number_format($reg['Documento'], 0), 0, 0, 'L'); 
							$PDF->SetFont('Arial', '', 8); 
							$PDF->Cell(35, 7, utf8_decode('SUELDO BÁSICO: '), 0, 0, 'L'); 
							$PDF->SetFont('Arial', 'B', 8); 
							$PDF->Cell(60, 7, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'L'); 
							$PDF->SetFont('Arial', '', 8); 
							$PDF->Ln(); 
							$PDF->Cell(25, 7, utf8_decode('NOMBRE: '), 0, 0, 'L'); 
							$PDF->SetFont('Arial', 'B', 8); 
							$PDF->Cell(75, 7, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 35), 0, 0, 'L'); 
							$PDF->SetFont('Arial', '', 8); 
							$PDF->Cell(35, 7, utf8_decode('FECHA DE INGRESO: '), 0, 0, 'L'); 
							$PDF->SetFont('Arial', 'B', 8); 
							$PDF->Cell(60, 7, $reg['FechaIngreso'], 0, 0, 'L'); 
							$PDF->SetFont('Arial', '', 8); 
							$PDF->Ln(); 
							$PDF->Cell(25, 7, utf8_decode('CARGO: '), 0, 0, 'L'); 
							$PDF->SetFont('Arial', 'B', 8); 
							$PDF->Cell(75, 7, substr(utf8_decode($reg['NombreCargo']), 0, 35), 0, 0, 'L'); 
							$PDF->SetFont('Arial', '', 8); 
							$PDF->Cell(35, 7, utf8_decode('E.P.S.: '), 0, 0, 'L'); 
							$PDF->SetFont('Arial', 'B', 8); 
							$PDF->Cell(60, 7, substr(utf8_decode($reg['NombreEPS']), 0, 30), 0, 0, 'L'); 
							$PDF->SetFont('Arial', '', 8); 
							$PDF->Ln(); 
							$PDF->Cell(25, 7, utf8_decode('CENTRO: '), 0, 0, 'L'); 
							$PDF->SetFont('Arial', 'B', 8); 
							$PDF->Cell(75, 7, substr(utf8_decode($reg['Centro'] . ' - ' . $reg['NombreCentro']), 0, 35), 0, 0, 'L'); 
							$PDF->SetFont('Arial', '', 8); 
							$PDF->Cell(35, 7, utf8_decode('FONDO DE PENSIÓN: '), 0, 0, 'L'); 
							$PDF->SetFont('Arial', 'B', 8); 
							$PDF->Cell(60, 7, substr(utf8_decode($reg['NombreFondoPension']), 0, 30), 0, 0, 'L'); 
							$PDF->SetFont('Arial', '', 8); 
							$PDF->Ln(); 
							$PDF->Ln(); 
							
							$PDF->SetTextColor(255, 255, 255);
							$PDF->Cell(80, 5, utf8_decode('CONCEPTO'), 0, 0, 'L', TRUE); 
							$PDF->Cell(25, 5, utf8_decode('Ho/Di'), 0, 0, 'R', TRUE); 
							$PDF->Cell(25, 5, utf8_decode('PAGOS'), 0, 0, 'R', TRUE); 
							$PDF->Cell(35, 5, utf8_decode('DEDUCCIONES'), 0, 0, 'R', TRUE); 
							$PDF->Cell(25, 5, utf8_decode('SALDO'), 0, 0, 'R', TRUE); 
							$PDF->SetTextColor(0, 0, 0);
							$PDF->Ln(); 

							$EmpleadoAnt 	= $reg['Documento'];
							$EmailAnt 		= $reg['Email'];
							$NombreAnt		= $reg['Apellido1'] .  ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'];

							$NombreFormaDePagoAnt 	= $reg['NombreFormaDePago'];
							$NombreBancoAnt			= $reg['NombreBanco'];
							$NombreTipoCuentaAnt 	= $reg['NombreTipoCuentaBancaria'];
							$CuentaBancariaAnt		= $reg['CuentaBancaria'];
						}

						$PDF->Cell(80, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 60), 0, 0, 'L'); 

						if ($reg['Horas'] > 0)
							if ($reg['NombreTipoLiquidacion'] == 'HORAS')
								$PDF->Cell(25, 5, number_format($reg['Horas'], 0) . 'H', 0, 0, 'R'); 
							else
								$PDF->Cell(25, 5, number_format($reg['Horas'] / 8, 0) . 'D', 0, 0, 'R'); 
						else
							$PDF->Cell(25, 5, '', 0, 0, 'R'); 
						
						if ($reg['Imputacion'] == 'PAGO') 
						{
							$PDF->Cell(25, 5, number_format($reg['Valor'], 0), 0, 0, 'R'); 
							$TotalPagos += $reg['Valor'];
						}
						else
							$PDF->Cell(25, 5, '', 0, 0, 'R'); 
						
						if ($reg['Imputacion'] == 'DEDUCCIÓN') 
						{
							$PDF->Cell(35, 5, number_format($reg['Valor'], 0), 0, 0, 'R'); 
							$TotalDeducciones += $reg['Valor'];
						}
						else
							$PDF->Cell(35, 5, '', 0, 0, 'R'); 

						if ($reg['Saldo'] <> 0) 
							$PDF->Cell(25, 5, number_format($reg['Saldo'], 0), 0, 0, 'R'); 

						$PDF->Ln(); 
					} 

					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
					$PDF->Cell(105, 5, utf8_decode('TOTALES  '), 0, 0, 'R'); 
					$PDF->SetFont('Arial', 'B', 8); 
					$PDF->Cell(25, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
					$PDF->Cell(35, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
					$PDF->SetFont('Arial', '', 8); 
					$PDF->Ln(); 
					if ($NombreFormaDePagoAnt == 'TRANSFERENCIA BANCARIA') 
						if ($NombreTipoCuentaAnt == 'CUENTA DE AHORROS')
							$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreBancoAnt . ' ( AH ' . $CuentaBancariaAnt . ')'), 0, 0, 'R'); 
						else
							$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreBancoAnt . ' ( CC ' . $CuentaBancariaAnt . ')'), 0, 0, 'R'); 
					else
						$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreFormaDePagoAnt), 0, 0, 'R'); 
					$PDF->SetFont('Arial', 'B', 8); 
					$PDF->Cell(25, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
					$PDF->SetFont('Arial', '', 8); 
					$PDF->Ln(); 
					$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 

					$Archivo = './descargas/' . $reg['Documento'] . '_ComprobantesDePago_' . strtoupper(NombreMes(date('m', strtotime($FechaInicial)))) . '_' . date('Y', strtotime($FechaInicial)) . '_' . date('YmdGis') . '.PDF';

					$PDF->Output($Archivo, 'F'); 
					$response = new stdClass();
					try 
					{
						$mail->setFrom($from, $fromName);
						$mail->addAddress($reg['Email'], utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']));
						// $mail->addReplyTo('info@example.com', 'Information');
						// $mail->addCC('cc@example.com');
						// $mail->addBCC('bcc@example.com');

						$mail->Subject = $subject;
						$mail->Body    = $body;
						$mail->AltBody = $body;
						$mail->addAttachment($Archivo);
						// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

						
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
						logRequests("DESPRENDIBLE DE NOMINA",$body,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $mail->to);
					} 
					catch (Exception $e) 
					{
						logRequests("DESPRENDIBLE DE NOMINA",$body,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $reg['Email']);
						$data['mensajeError'] .= 'Error al enviar desprendible a ' . utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']) . '<br>';
						$mail->getSMTPInstance()->reset();
					}

					unlink($Archivo);

					$mail->clearAddresses();
					$mail->clearAttachments();

					if (empty($data['mensajeError']))
						$data['mensajeError'] = "DESPRENDIBLES SE HAN ENVIADO CORRECTAMENTE<br>";
				}
				else
				{
					$data['mensajeError'] = 'No hay datos disponibles.';
				}
			}

			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/desprendiblesNomina/parametros';
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
			$_SESSION['Lista'] = SERVERURL . '/desprendiblesNomina/lista/1';

			if ($data) 
				$this->views->getView($this, 'actualizar', $data);
		}
	}
?>

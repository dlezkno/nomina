<?php
	require_once('./templates/vendor/autoload.php');
	require './templates/PHPMailer-master/src/PHPMailer.php';
	require './templates/PHPMailer-master/src/SMTP.php';
	require './templates/PHPMailer-master/src/Exception.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Usuarios extends Controllers
	{
		public function login()
		{
			if (isset($_SESSION['Login']['Id']))
			{
				$IdUsuario = $_SESSION['Login']['Id'];

				$query = <<<EOD
					UPDATE USUARIOS 
						SET 
						LogOut = getdate() 
						WHERE USUARIOS.Id = $IdUsuario;
				EOD;

				$this->model->query($query);
							
				session_unset();
				session_destroy();
			}

			if (session_name() <> 'COMWARE')
			{
				session_name('COMWARE');
				session_start();
			}


			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			if (! isset($_SESSION['Login'])) 
				$_SESSION['ActualizarRegistro'] = '';
			else
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/usuarios/actualizarRegistro';
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

			$data = array(
				'reg' => array(
					'Usuario' => isset($_REQUEST['Usuario']) ? $_REQUEST['Usuario'] : '',
					'Nombre' => isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
					'Email' => isset($_REQUEST['Email']) ? $_REQUEST['Email'] : '',
					'Contrasena' => isset($_REQUEST['Contrasena']) ? $_REQUEST['Contrasena'] : '',
					'Vigencia' => 0
				),
				'mensajeError' => ''
			);

			// INGRESA A SECCION DE RECUPERAR CONTRASEÑA
			if(isset($_REQUEST["url"]) && $_REQUEST["url"] == "login/forgot"){
					
				$this->change($data);
	
			}else{
				if (isset($_REQUEST['Usuario'])) {
					if (isset($_REQUEST['Nombre']))
					{
						if (empty($data['reg']['Usuario']))
							$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Usuario') . '</strong><br>';

						if (empty($data['reg']['Nombre']))
							$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';

						if (empty($data['reg']['Email']))
							$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('E-mail') . '</strong><br>';

						if (empty($data['reg']['Contrasena']))
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Contraseña') . '</strong><br>';
						elseif (strlen($data['reg']['Contrasena']) < 6) 
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Contraseña') . '</strong> ' . label('de al menos 6 caracteres de longitud') . '<br>';

						if ($data['reg']['Contrasena'] <> $_REQUEST['Contrasena2'])
							$data['mensajeError'] .= label('Contraseñas no coinciden') . '<br>';

						if	( $data['mensajeError'] )
							$this->views->getView($this, 'register', $data);
						else
						{
							$id = $this->model->guardarRegistro($data['reg']);

							if ($id) 
							{
								header('Location: ' . SERVERURL . '/login/ingreso');
								exit();
							}
						}
					}
					else
					{
						if (empty($data['reg']['Usuario']))
							$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Usuario') . '</strong><br>';

						if (empty($data['reg']['Contrasena']))
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Contraseña') . '</strong><br>';
						elseif (strlen($data['reg']['Contrasena']) < 6) 
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Contraseña') . '</strong> ' . label('de al menos 6 caracteres de longitud') . '<br>';


							$Usuario = $data['reg']['Usuario'];

							$query = <<<EOD
								SELECT USUARIOS.*, 
										PARAMETROS.Valor AS ValorPerfil  
									FROM USUARIOS 
										INNER JOIN PARAMETROS
											ON USUARIOS.Perfil = PARAMETROS.Id 
									WHERE (USUARIOS.Usuario = '$Usuario'  OR 
										USUARIOS.EMail = '$Usuario') AND 
										USUARIOS.Bloqueado = 0;
							EOD;

							$reg = $this->model->buscarUsuario($query);
							

							if ($reg)
							{
								// BUSCAMOS EL EMPLEADO PARA VALIDAR QUE ESTE ACTIVO
								if ($reg['ValorPerfil'] <> ADMINISTRADOR AND $reg['ValorPerfil'] <> AUDITORIA)
								{
									$Documento = $reg['documento'];

									$queryEmpleado = <<<EOD
									SELECT id
										FROM EMPLEADOS 
										WHERE EMPLEADOS.Documento = '$Documento' AND ESTADO = '141';
									EOD;

									$empleadoSelected = $this->model->leer($queryEmpleado);
									$id = $empleadoSelected['id'];
									$query = <<<EOD
										SELECT PARAMETROS.Detalle AS Estado 
											FROM EMPLEADOS 
												INNER JOIN PARAMETROS 
													ON EMPLEADOS.Estado = PARAMETROS.Id 
											WHERE EMPLEADOS.Documento = '$Documento' AND EMPLEADOS.id = '$id';
									EOD;

									$regEmpleado = $this->model->leer($query);

									if ($regEmpleado['Estado'] <> 'ACTIVO')
									{
										$data['mensajeError'] .= '<strong>' . label('Usuario') . '</strong> ' . label('no existe') . '<br>';
										$this->views->getView($this, 'login', $data);
										exit();
									}
									else
									{
										$pasmd5 = md5($data['reg']['Contrasena']);

										$centinelaHash = password_verify($data['reg']['Contrasena'], $reg['registro']);
										$centinelaMd5 = (strtoupper($pasmd5) == strtoupper($reg['registro']));

										if ( $centinelaHash == false)
										{
											if($centinelaMd5){										
												header('Location: ' . SERVERURL . '/login/forgot');
											}else{
												// VALIDAR SI EL USUARIO TIENE CONTRASEÑA, EN CASO CONTRARIO IR A ASIGNARLA 
												$data['mensajeError'] .= '<strong>' . label('Contraseña') . '</strong> ' . label('incorrecta') . '<br>';
												$this->views->getView($this, 'login', $data);
												exit();
											}
										}
										else
										{
											$this->model->actualizarLogin($Usuario);
											
											$_SESSION['Login']['Id'] = $reg['id'];
											$_SESSION['Login']['Usuario'] = trim($reg['usuario']);
											$_SESSION['Login']['Nombre'] = trim($reg['nombre']);
											$_SESSION['Login']['Documento'] = trim($reg['documento']);
											$_SESSION['Login']['EMail'] = $reg['email'];
											$_SESSION['Login']['Perfil'] = $reg['ValorPerfil'];
											$_SESSION['Login']['IdIdioma'] = $reg['ididioma'];
							
											$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'NitEmpresa' ");
											if ($reg) 
												$_SESSION['Empresa']['Nit'] = $reg['detalle'];
					
											$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'DireccionEmpresa' ");
											if ($reg) 
												$_SESSION['Empresa']['Direccion'] = $reg['detalle'];
						
											$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'TelefonoEmpresa' ");
											if ($reg) 
												$_SESSION['Empresa']['Telefono'] = $reg['detalle'];
						
											$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'EmailEmpresa' ");
											if ($reg) 
												$_SESSION['Empresa']['Email'] = $reg['detalle'];

											// ARMAR UNA LISTA BLANCA PARA CONTROLAR LOS ACCESOS
											switch ($_SESSION['Login']['Perfil'])
											{
												case EMPLEADO:
													$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'misdatos');
													break;
												case SELECCION:
													$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'candidatos', 'entrevista1', 'entrevista2', 'documentos', 'estadoSolicitud', 'misdatos');
													break;
												case CONTRATACION:
													$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'contratos', 'documentos', 'estadoSolicitud', 'misdatos', 'empleados', 'plantillas', 'renovaciones','centros');
													break;
												case AUDITORIA:
													$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'documentos', 'estadoSolicitud');
													break;
												case RRHH_AUX:
													$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'misdatos', 'centros', 'periodos', 'mayores', 'auxiliares', 'categorias', 'cargos', 'diasfestivos', 'bancos', 'cuentasbancarias', 'terceros', 'tipodoc', 'empleados', 'retirados', 'novedadesEmpleados', 'prestamos', 'informesEmpleados', 'novedadesProgramables', 'novedades', 'incapacidades', 'aumentosSalariales', 'retirosEmpleados', 'deduccionesRetFte', 'liquidacionPrima', 'liquidacionCesantias', 'liquidacionContrato', 'Vacaciones', 'acumulados', 'informesNomina', 'desprendiblesNomina', 'usuarios', 'plantillas', 'diagnosticos');
													break;
												case RRHH:
													$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'misdatos', 'centros', 'periodos', 'mayores', 'auxiliares', 'categorias', 'cargos', 'diasfestivos', 'bancos', 'cuentasbancarias', 'terceros', 'tipodoc', 'empleados', 'retirados', 'novedadesEmpleados', 'prestamos', 'informesEmpleados', 'aperturaNovedades', 'novedadesProgramables', 'novedades', 'dispersionPorCentro', 'incapacidades', 'aumentosSalariales', 'retirosEmpleados', 'deduccionesRetFte', 'liquidacionPrenomina', 'liquidacionPrima', 'liquidacionCesantias', 'simulacionLiquidacionContrato', 'liquidacionContrato', 'Vacaciones', 'acumulados', 'informesNomina', 'desprendiblesNomina', 'comprobantes', 'contabilizacionSAP', 'dispersionNomina', 'trasladosCentros', 'informeContabilizacionAcumulados', 'inicioContadores', 'nominaElectronica', 'informeNominaElectronica', 'usuarios', 'plantillas', 'diagnosticos');
													break;
												case ADMINISTRADOR:
													$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'candidatos', 'documentos', 'estadoSolicitud', 'entrevista1', 'entrevista2', 'misdatos', 'contratos', 'renovaciones', 'centros', 'periodos', 'mayores', 'auxiliares', 'categorias', 'cargos', 'diasfestivos', 'bancos', 'cuentasbancarias', 'terceros', 'tipodoc', 'empleados', 'retirados', 'novedadesEmpleados', 'prestamos', 'informesEmpleados', 'aperturaNovedades', 'novedadesProgramables', 'novedades', 'dispersionPorCentro',  'incapacidades', 'aumentosSalariales', 'retirosEmpleados', 'deduccionesRetFte', 'liquidacionPrenomina', 'informePrenomina', 'liquidacionPrima', 'liquidacionCesantias', 'simulacionLiquidacionContrato', 'liquidacionContrato', 'Vacaciones', 'acumulados', 'informesNomina', 'desprendiblesNomina', 'comprobantes', 'contabilizacionSAP', 'dispersionNomina', 'trasladosCentros', 'informeContabilizacionAcumulados', 'inicioContadores', 'nominaElectronica', 'informeNominaElectronica', 'usuarios', 'plantillas', 'parametros', 'ciudades', 'paises', 'diagnosticos', 'idiomas', 'traducciones', 'prueba', 'error401', 'error404');
													break;
											}
											
											if ($_SESSION['Login']['Perfil'] == EMPLEADO)
												header('Location: ' . SERVERURL . '/misdatos/editar/' . strtoupper($reg['registro']));
											else
												header('Location: ' . SERVERURL . '/dashboard/dashboard');

											exit();
										}						
									}
								}
								else
								{
									$pasmd5 = md5($data['reg']['Contrasena']);

									$centinelaHash = password_verify($data['reg']['Contrasena'], $reg['registro']);
									$centinelaMd5 = (strtoupper($pasmd5) == strtoupper($reg['registro']));


									if ( $centinelaHash == false)
									{
										if($centinelaMd5){										
											header('Location: ' . SERVERURL . '/login/forgot');
										}else{
											// VALIDAR SI EL USUARIO TIENE CONTRASEÑA, EN CASO CONTRARIO IR A ASIGNARLA 
											$data['mensajeError'] .= '<strong>' . label('Contraseña') . '</strong> ' . label('incorrecta') . '<br>';
											$this->views->getView($this, 'login', $data);
											exit();
										}
										
									}
									else
									{
										$this->model->actualizarLogin($Usuario);
										
										$_SESSION['Login']['Id'] = $reg['id'];
										$_SESSION['Login']['Usuario'] = trim($reg['usuario']);
										$_SESSION['Login']['Nombre'] = trim($reg['nombre']);
										$_SESSION['Login']['Documento'] = trim($reg['documento']);
										$_SESSION['Login']['EMail'] = $reg['email'];
										$_SESSION['Login']['Perfil'] = $reg['ValorPerfil'];
										$_SESSION['Login']['IdIdioma'] = $reg['ididioma'];
						
										$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'NitEmpresa' ");
										if ($reg) 
											$_SESSION['Empresa']['Nit'] = $reg['detalle'];
				
										$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'DireccionEmpresa' ");
										if ($reg) 
											$_SESSION['Empresa']['Direccion'] = $reg['detalle'];
					
										$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'TelefonoEmpresa' ");
										if ($reg) 
											$_SESSION['Empresa']['Telefono'] = $reg['detalle'];
					
										$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'EmailEmpresa' ");
										if ($reg) 
											$_SESSION['Empresa']['Email'] = $reg['detalle'];

										// ARMAR UNA LISTA BLANCA PARA CONTROLAR LOS ACCESOS
										switch ($_SESSION['Login']['Perfil'])
										{
											case EMPLEADO:
												$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'misdatos');
												break;
											case SELECCION:
												$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'candidatos', 'entrevista1', 'entrevista2', 'documentos', 'estadoSolicitud', 'misdatos');
												break;
											case CONTRATACION:
												$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'contratos', 'documentos', 'estadoSolicitud', 'misdatos', 'empleados', 'plantillas', 'renovaciones','centros');
												break;
											case AUDITORIA:
												$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'documentos', 'estadoSolicitud');
												break;
											case RRHH_AUX:
												$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'misdatos', 'centros', 'periodos', 'mayores', 'auxiliares', 'categorias', 'cargos', 'diasfestivos', 'bancos', 'cuentasbancarias', 'terceros', 'tipodoc', 'empleados', 'retirados', 'novedadesEmpleados', 'prestamos', 'informesEmpleados', 'novedadesProgramables', 'novedades', 'incapacidades', 'aumentosSalariales', 'retirosEmpleados', 'deduccionesRetFte', 'liquidacionPrima', 'liquidacionCesantias', 'liquidacionContrato', 'Vacaciones', 'acumulados', 'informesNomina', 'desprendiblesNomina', 'usuarios', 'plantillas', 'diagnosticos');
												break;
											case RRHH:
												$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'misdatos', 'centros', 'periodos', 'mayores', 'auxiliares', 'categorias', 'cargos', 'diasfestivos', 'bancos', 'cuentasbancarias', 'terceros', 'tipodoc', 'empleados', 'retirados', 'novedadesEmpleados', 'prestamos', 'informesEmpleados', 'aperturaNovedades', 'novedadesProgramables', 'novedades', 'dispersionPorCentro', 'incapacidades', 'aumentosSalariales', 'retirosEmpleados', 'deduccionesRetFte', 'liquidacionPrenomina', 'liquidacionPrima', 'liquidacionCesantias', 'simulacionLiquidacionContrato', 'liquidacionContrato', 'Vacaciones', 'acumulados', 'informesNomina', 'desprendiblesNomina', 'comprobantes', 'contabilizacionSAP', 'dispersionNomina', 'trasladosCentros', 'informeContabilizacionAcumulados', 'inicioContadores', 'nominaElectronica', 'informeNominaElectronica', 'usuarios', 'plantillas', 'diagnosticos');
												break;
											case ADMINISTRADOR:
												$_SESSION['ListaBlanca'] = array('personal','usuarios', 'home', 'dashboard', 'pronosticos', 'candidatos', 'entrevista1', 'entrevista2', 'documentos', 'estadoSolicitud', 'misdatos', 'contratos', 'renovaciones', 'centros', 'periodos', 'mayores', 'auxiliares', 'categorias', 'cargos', 'diasfestivos', 'bancos', 'cuentasbancarias', 'terceros', 'tipodoc', 'empleados', 'retirados', 'novedadesEmpleados', 'prestamos', 'informesEmpleados', 'aperturaNovedades', 'novedadesProgramables', 'novedades', 'dispersionPorCentro', 'incapacidades', 'aumentosSalariales', 'retirosEmpleados', 'deduccionesRetFte', 'liquidacionPrenomina', 'informePrenomina', 'liquidacionPrima', 'liquidacionCesantias', 'simulacionLiquidacionContrato', 'liquidacionContrato', 'Vacaciones', 'acumulados', 'informesNomina', 'desprendiblesNomina', 'comprobantes', 'contabilizacionSAP', 'dispersionNomina', 'trasladosCentros', 'informeContabilizacionAcumulados', 'inicioContadores', 'nominaElectronica', 'informeNominaElectronica', 'usuarios', 'plantillas', 'parametros', 'ciudades', 'paises', 'diagnosticos', 'idiomas', 'traducciones', 'prueba');
												break;
										}
										
										if ($_SESSION['Login']['Perfil'] == EMPLEADO)
											header('Location: ' . SERVERURL . '/misdatos/editar/' . strtoupper($reg['registro']));
										else
											header('Location: ' . SERVERURL . '/dashboard/dashboard');

										exit();
									}
								}
							}


							
							$query = <<<EOD
								SELECT EMPLEADOS.* 
									FROM EMPLEADOS 
									INNER JOIN PARAMETROS 
									ON EMPLEADOS.Estado = PARAMETROS.Id 
									WHERE EMPLEADOS.EMail = '$Usuario' AND 
										PARAMETROS.Detalle <> 'RETIRADO'  AND 
										PARAMETROS.Detalle <> 'CANDIDATO DESISTE'   AND 
										PARAMETROS.Detalle <> 'CANDIDATO NO CALIFICADO' AND
										EMPLEADOS.FechaRetiro IS NULL;
							EOD;

							$regEmpleado = $this->model->buscarUsuario($query);

							if ($regEmpleado)
							{
								// VALIDAMOS SI INTENTA INGRESAR DENTRO DEL TIEMPO ESTIMADO
								$FechaActualizacion = substr($regEmpleado['fechacreacion'], 0, 10);

								if (! empty($regEmpleado['fechaactualizacion'])){
									$FechaActualizacion = substr($regEmpleado['fechaactualizacion'], 0, 10);
								}
								$d = date('D',strtotime($FechaActualizacion));
								
								if($d == "Mon"){
									$newd = date('Y-m-d', strtotime($FechaActualizacion . '+ 10 days'));
								}else if($d == "Tue"){
									$newd = date('Y-m-d', strtotime($FechaActualizacion . '+ 10 days'));
								}else{
									$newd = date('Y-m-d', strtotime($FechaActualizacion . '+ 12 days'));
								}
								
								if (date('Y-m-d') > date('Y-m-d', strtotime($newd)))
								{
									$data['mensajeError'] .= 'Tiempo para ingresar a actualizar datos ha caducado. Pongase en contacto con COMWARE' . '<br>';
									$this->views->getView($this, 'login', $data);
								}
								elseif ( $data['reg']['Contrasena'] <> $regEmpleado['celular'] AND 
									$data['reg']['Contrasena'] <> $regEmpleado['documento'] )
								{
									$data['mensajeError'] .= '<strong>' . label('Contraseña') . '</strong> ' . label('incorrecta') . '<br>';
									$this->views->getView($this, 'login', $data);
								}
								else
								{
									// $this->model->actualizarLogin($Usuario);
									
									$_SESSION['Login']['Id'] 		= $regEmpleado['id'];
									$_SESSION['Login']['Usuario'] 	= trim($regEmpleado['documento']);
									$_SESSION['Login']['Nombre'] 	= trim($regEmpleado['apellido1']) . ' ' . trim($regEmpleado['apellido2']) . ' ' . trim($regEmpleado['nombre1']) . ' ' . trim($regEmpleado['nombre2']);
									$_SESSION['Login']['Documento'] = trim($regEmpleado['documento']);
									$_SESSION['Login']['EMail'] 	= $regEmpleado['email'];
									$_SESSION['Login']['Perfil'] 	= EMPLEADO;
									$_SESSION['Login']['IdIdioma'] 	= 0;
					
									$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'NitEmpresa' ");
									$_SESSION['Empresa']['Nit'] = $reg['detalle'];
									$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'DireccionEmpresa' ");
									$_SESSION['Empresa']['Direccion'] = $reg['detalle'];
									$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'TelefonoEmpresa' ");
									$_SESSION['Empresa']['Telefono'] = $reg['detalle'];
									$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'EmailEmpresa' ");
									$_SESSION['Empresa']['Email'] = $reg['detalle'];

									// LISTA BLANCA DE EMPLEADOS
									$_SESSION['ListaBlanca'] = array('usuarios', 'home', 'misdatos');

									// VALIDAMOS SI EL CANDIDATO YA FIRMO EL ACUERDO DE TRATAMIENTO DE DATOS
									if ($regEmpleado['aceptapoliticatd'] == 0)
									{
										// DESCARGAMOS LA FIRMA ELECTRONICA DEL DOCUMENTO
										$IdEmpleado 	= $regEmpleado['id'];
										$Documento 		= $regEmpleado['documento'];
										$Apellido1 		= $regEmpleado['apellido1'];
										$Apellido2		= $regEmpleado['apellido2'];
										$Nombre1		= $regEmpleado['nombre1'];
										$Nombre2		= $regEmpleado['nombre2'];
										$SolicitudFirma = $regEmpleado['solicitudfirma'];

										// SE HACE LA CONSULTA DE LA SOLICITUD
										$curl = curl_init();

										curl_setopt_array(
											$curl, array(
												CURLOPT_URL => URL_FIRMA .'resumenfirma/' . $SolicitudFirma,
												CURLOPT_RETURNTRANSFER => true,
												CURLOPT_ENCODING => '',
												CURLOPT_MAXREDIRS => 10,
												CURLOPT_TIMEOUT => 0,
												CURLOPT_FOLLOWLOCATION => true,
												CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
												CURLOPT_CUSTOMREQUEST => 'GET',
												CURLOPT_HTTPHEADER => array(
													'Token: ' . TOKEN_FIRMA,
													'Content-Type: application/json'
												)
											)
										);

										$response = json_decode(curl_exec($curl), true);

										curl_close($curl);

										// SE TRASLADAN LOS DOCUMENTOS AL REPOSITORIO
										if ($response['Code'] == '100')
										{
											$Fecha = $response['Data']['Firmante'][0]['Fecha'];
											$Fecha = substr($Fecha, 0, 10);
											$Fecha = substr($Fecha, -4) . '-' . substr($Fecha, 3, 2) . '-' . substr($Fecha, 0, 2);

											for ($j = 0; $j < count($response['Data']['Firmante']); $j++)
											{
												$Fotografia = base64_decode($response['Data']['Firmante'][$j]['FotoBase64']);

												// SE GUARDA LA FOTOGRAFIA EN CONTRATOS
												$cDirectorio = str_replace(" ","",'documents/' . $Documento . '_' . strtoupper($Apellido1) . '_' . strtoupper($Apellido2) . '_' . strtoupper($Nombre1) . '_' . strtoupper($Nombre2));

												if	( ! is_dir($cDirectorio) )
													mkdir($cDirectorio);

												if	( ! is_dir($cDirectorio . '/HV') )
													mkdir($cDirectorio . '/HV');

												$archivoDestino = $cDirectorio . '/HV/' . $Documento . '_' . $Fecha . '_FOTOGRAFIA.JPG';

												$ok = file_put_contents($archivoDestino, $Fotografia, LOCK_EX);
											}

											for ($j = 0; $j < count($response['Data']['DocumentosPDF']); $j++)
											{
												$TipoDocumento = $response['Data']['DocumentosPDF'][$j]['TipoDocumento'];
												$DocumentoFirmado = base64_decode($response['Data']['DocumentosPDF'][$j]['DocumentoFirmado']);

												$cDirectorio = str_replace(" ","",'documents/' . $Documento . '_' . strtoupper($Apellido1) . '_' . strtoupper($Apellido2) . '_' . strtoupper($Nombre1) . '_' . strtoupper($Nombre2));

												if	( ! is_dir($cDirectorio) )
													mkdir($cDirectorio);

												if	( ! is_dir($cDirectorio . '/HV') )
													mkdir($cDirectorio . '/HV');

												$archivoDestino = $cDirectorio . '/HV/' . $Documento . '_' . $Fecha . '_' . strtoupper($TipoDocumento) . '.PDF';

												$ok = file_put_contents($archivoDestino, $DocumentoFirmado, LOCK_EX);
											}

											// ACTUALIZAMOS LA POLITICA DE TRATAMIENTO DE DATOS EN EL EMPLEADO
											$query = <<<EOD
												UPDATE EMPLEADOS 
													SET AceptaPoliticaTD = 1, 
														SolicitudFirma = 0, 
														FechaSolicitud = ''
													WHERE EMPLEADOS.Id = $IdEmpleado;
											EOD;

											$this->model->query($query);
										}
										else
										{
											$data['mensajeError'] .= <<<EOD
												Usuario no ha firmado POLÍTICA DE TRATAMIENTO DE DATOS.<br>
												Revise su correo y firme el documento, luego vuelva a ingresar.<br>
											EOD;

											$this->views->getView($this, 'login', $data);
											exit();
										}
									}

									header('Location: ' . SERVERURL . '/misdatos/editar/' . $_SESSION['Login']['Id']);
									exit();
								}						
							}
							else
							{
								$data['mensajeError'] .= '<strong>' . label('Usuario') . '</strong> ' . label('no existe') . '<br>';
								$this->views->getView($this, 'login', $data);
								exit();
							}
						
					}
				}else{	
					$query = "WHERE USUARIOS.Bloqueado = 0";

					$registros = $this->model->contarRegistros($query);

					if ($registros == 0)
						$this->views->getView($this, 'registro', $data);
					else
						$this->views->getView($this, 'login', $data);
					
					exit();
				}
			}
		}


		public function change($data){
			if (isset($_REQUEST['Usuario']) ) {

				if(isset($_REQUEST["Codigo"])){
					$Usuario = $_REQUEST['Usuario'];
					$codigo = $_REQUEST["Codigo"];
					$query = <<<EOD
							SELECT USUARIOS.*
								FROM USUARIOS 
								WHERE (USUARIOS.Usuario = '$Usuario'  OR 
									USUARIOS.EMail = '$Usuario') AND 
									USUARIOS.coderegistro = '$codigo' AND
									USUARIOS.Bloqueado = 0;
						EOD;
	
						$reg = $this->model->buscarUsuario($query);
						if($reg){
							$fechadb =  date('m-d-Y h:i:s', strtotime($reg["fechacambioregistro"] . ' + 5 minute'));
							$fecha = date('m-d-Y h:i:s');
							if($fecha < $fechadb ){
								$data["nuewPass"] = true;
	
								$this->views->getView($this, 'forgot', $data);
							}
						}

				}else{

					if(isset($_REQUEST["nuevaContrasena"])){

						if($_REQUEST["nuevaContrasena"] != ""){

							$pass = str_split($_REQUEST["nuevaContrasena"]);
							$up = explode("," , "A,B,C,D,E,F,G,H,I,J,K,L,M,N,Ñ,O,P,Q,R,S,T,U,V,W,X,Y,Z");
							$low = explode("," , "a,b,c,d,e,f,g,h,i,j,k,l,m,n,ñ,o,p,q,r,s,t,u,v,w,x,y,z");
							$nums = explode("," , "1,2,3,4,5,6,7,8,9,0");
							$characts = explode("," , "!,',#,$,%,&,/,(,),=,?,¡,¨,*,],[,:,;,-,_,.");


							$valup = false;
							$vallow = false;
							$valnums = false;
							$valchar = false;
							for($i = 0; $i < count($pass); $i ++){
								
								if($valup === false){
									$valup = array_search($pass[$i], $up);
								}
								if($vallow == false){
									$vallow = array_search($pass[$i], $low);
								}
								if($valnums == false){
									$valnums = array_search($pass[$i], $nums);
								}
								if($valchar == false){
									$valchar = array_search($pass[$i], $characts);
								}
								
							}

							$cent = ($_REQUEST["nuevaContrasena"] == $_REQUEST["repiteNuevaContrasena"]);

							if($valup == false || $vallow == false || $valnums == false ||  $valchar == false || $cent == false || count($pass) < 8){
								$data['mensajeError'] .= "La contraseña debe tener minimo 8 caracteres, una letra en minuscula, una letra en mayuscula, un numero , un caracter especial y deben coincidir<br>";
								$data["nuewPass"] = true;
								$this->views->getView($this, 'forgot', $data);
							}else{
								$Usuario = $_REQUEST['Usuario'];
								$new = password_hash($_REQUEST["nuevaContrasena"], PASSWORD_BCRYPT);

								$queryUpdate = <<<EOD
								UPDATE USUARIOS 
									SET 
									registro = '$new'
									WHERE (USUARIOS.Usuario = '$Usuario'  OR 
									USUARIOS.EMail = '$Usuario')  ;
								EOD;

								$rps  = $this->model->query($queryUpdate);
								header('Location: ' . SERVERURL . '/login/ingreso');
								exit();
							}
								

							
						}else{

						}

					}else{

						$Usuario = $_REQUEST['Usuario'];
						$query = <<<EOD
							SELECT USUARIOS.*, 
									PARAMETROS.Valor AS ValorPerfil  
								FROM USUARIOS 
									INNER JOIN PARAMETROS
										ON USUARIOS.Perfil = PARAMETROS.Id 
								WHERE (USUARIOS.Usuario = '$Usuario'  OR 
									USUARIOS.EMail = '$Usuario') AND 
									USUARIOS.Bloqueado = 0;
						EOD;
	
						$reg = $this->model->buscarUsuario($query);
						if($reg){
							$Documento = $reg['documento'];
							
							$queryEmpleado = <<<EOD
							SELECT id
								FROM EMPLEADOS 
								WHERE EMPLEADOS.Documento = '$Documento' AND ESTADO = '141';
							EOD;

							$empleadoSelected = $this->model->leer($queryEmpleado);
							$id = $empleadoSelected['id'];

							
							$query = <<<EOD
								SELECT PARAMETROS.Detalle AS Estado 
									FROM EMPLEADOS 
										INNER JOIN PARAMETROS 
											ON EMPLEADOS.Estado = PARAMETROS.Id 
									WHERE EMPLEADOS.Documento = '$Documento' AND EMPLEADOS.id = '$id';
							EOD;
	
							$regEmpleado = $this->model->leer($query);
	
							if ($regEmpleado['Estado'] == 'ACTIVO')
							{
	
								$date = date('m-d-Y h:i:s');
								$IdUsuario = $reg["id"];
								$str = substr(sha1(mt_rand()),17,8);
								// $reg["fechacambioregistro"]
								$query = <<<EOD
									UPDATE USUARIOS 
										SET 
										fechacambioregistro = '$date',
										coderegistro = '$str'
										WHERE USUARIOS.Id = '$IdUsuario';
								EOD;
	
								$rps  = $this->model->query($query);
	
								$data["code"] = true;
	
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
								$fromName = 'COMWARE';
								$mail->Subject = "CODIGO DE VERIFICACION";
								$mail->Body = 'ESTAS INTENTANDO CAMBIAR TU CONTRASEÑA ? <br> este es tu codigo de verificacion : '.$str;
	
								$mail->AddAddress( $Usuario);
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
									logRequests("USUARIOS LOGIN",$str,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", $IdUsuario, $mail->to);
								} 
								catch (Exception $e) 
								{
									logRequests("USUARIOS LOGIN",$str,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", $IdUsuario,$Usuario);
									$data['mensajeError'] .= "Error al enviar correo <br>";
									$mail->getSMTPInstance()->reset();
								}
								$this->views->getView($this, 'forgot', $data);
							}else{
								$data['mensajeError'] .= "Error el usuario no se encuntra activo <br>";
								$this->views->getView($this, 'forgot', $data);
							}
						}else{
							$data['mensajeError'] .= "Error el usuario no se encuntra en la base de datos <br>";
							$this->views->getView($this, 'forgot', $data);
						}
					}
				}
			}else{
				$this->views->getView($this, 'forgot', $data);
			}	
		}
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/usuarios/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/usuarios/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['USUARIOS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['USUARIOS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['USUARIOS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['USUARIOS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['USUARIOS']['Filtro']))
			{
				$_SESSION['USUARIOS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['USUARIOS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['USUARIOS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['USUARIOS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['USUARIOS']['Orden'])) 
					$_SESSION['USUARIOS']['Orden'] = 'USUARIOS.Nombre';

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

					$query .= "UPPER(REPLACE(USUARIOS.Usuario, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(USUARIOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PARAMETROS.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}

			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['USUARIOS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarUsuarios($query);
			$this->views->getView($this, 'usuarios', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/usuarios/actualizarUsuario';
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
			$_SESSION['Lista'] = SERVERURL . '/usuarios/lista/' . $_SESSION['USUARIOS']['Pagina'];

			$data = array(
				'reg' => array(
					'Usuario' => isset($_REQUEST['Usuario']) ? $_REQUEST['Usuario'] : '',
					'Nombre' => isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
					'TipoId' => isset($_REQUEST['TipoId']) ? $_REQUEST['TipoId'] : '',
					'Documento' => isset($_REQUEST['Documento']) ? $_REQUEST['Documento'] : '',
					'Perfil' => isset($_REQUEST['Perfil']) ? $_REQUEST['Perfil'] : '',
					'Registro' => isset($_REQUEST['Documento']) ? $_REQUEST['Documento'] : '',
					'Vigencia' => isset($_REQUEST['Vigencia']) ? $_REQUEST['Vigencia'] : '',
					'Direccion' => isset($_REQUEST['Direccion']) ? $_REQUEST['Direccion'] : '',
					'IdCiudad' => isset($_REQUEST['IdCiudad']) ? $_REQUEST['IdCiudad'] : '',
					'Telefono' => isset($_REQUEST['Telefono']) ? $_REQUEST['Telefono'] : '',
					'Celular' => isset($_REQUEST['Celular']) ? $_REQUEST['Celular'] : '',
					'Email' => isset($_REQUEST['Email']) ? $_REQUEST['Email'] : ''
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Usuario'])) 
			{
				if	( empty($data['reg']['Usuario']) )
					$data['mensajeError'] .= label('Debe digitar un código de') . ' <strong>' . label('Usuario') . '</strong><br>';
				else
				{
					$Usuario = $data['reg']['Usuario'];

					$query = <<<EOD
						SELECT * FROM USUARIOS 
							WHERE USUARIOS.Usuario = '$Usuario' OR  
							USUARIOS.EMail = '$Usuario';
					EOD; 

					$reg = $this->model->buscarUsuario($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Usuario') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				if	( empty($data['reg']['TipoId']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de identificación') . '</strong><br>';
			
				if	( empty($data['reg']['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento') . '</strong><br>';
			
				if	( empty($data['reg']['Email']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('E-Mail') . '</strong><br>';
				elseif	( !filter_var($data['reg']['Email'], FILTER_VALIDATE_EMAIL) )
					$data['mensajeError'] .= label('Formato invalido de') . ' <strong>' . label('Email') . '</strong><br>';
				
				if	( empty($data['reg']['Perfil']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Perfil') . '</strong><br>';
			
				if	( $data['reg']['Vigencia'] < 0 )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Vigencia') . '</strong> ' . label('mayor o igual a cero') . '<br>';
			
				// if	( empty($data['reg']['Direccion']) )
				// 	$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Dirección') . '</strong><br>';
			
				// if	( empty($data['reg']['IdCiudad']) )
				// 	$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad') . '</strong><br>';
			
				// if	( empty($data['reg']['Telefono']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Teléfono') . '</strong><br>';
			
				// if	( empty($data['reg']['Celular']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Celular') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarUsuario($data['reg']);

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
			if (isset($_REQUEST['Usuario']))
			{
				$data = array(
					'reg' => array(
						'usuario' => isset($_REQUEST['Usuario']) ? $_REQUEST['Usuario'] : '',
						'nombre' => isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
						'tipoid' => isset($_REQUEST['TipoId']) ? $_REQUEST['TipoId'] : '',
						'documento' => isset($_REQUEST['Documento']) ? $_REQUEST['Documento'] : '',
						'perfil' => isset($_REQUEST['Perfil']) ? $_REQUEST['Perfil'] : '',
						'vigencia' => isset($_REQUEST['Vigencia']) ? $_REQUEST['Vigencia'] : '',
						'direccion' => isset($_REQUEST['Direccion']) ? $_REQUEST['Direccion'] : '',
						'idciudad' => isset($_REQUEST['IdCiudad']) ? $_REQUEST['IdCiudad'] : '',
						'telefono' => isset($_REQUEST['Telefono']) ? $_REQUEST['Telefono'] : '',
						'celular' => isset($_REQUEST['Celular']) ? $_REQUEST['Celular'] : '',
						'email' => isset($_REQUEST['Email']) ? $_REQUEST['Email'] : ''
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['usuario']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Usuario') . '</strong><br>';
				else
				{
					$Usuario = $data['reg']['usuario'];

					$query = <<<EOD
						SELECT * FROM USUARIOS
							WHERE USUARIOS.Usuario = '$Usuario' AND 
								USUARIOS.Id <> $id
					EOD;

					$reg = $this->model->buscarUsuario($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Usuario') . '</strong> ' . label('ya existe') . '<br>';
				}

				if	( empty($data['reg']['nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				if	( empty($data['reg']['tipoid']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de identificación') . '</strong><br>';
			
				if	( empty($data['reg']['documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento') . '</strong><br>';
			
				if	( empty($data['reg']['email']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('E-Mail') . '</strong><br>';
				
				if	( empty($data['reg']['perfil']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Perfil') . '</strong><br>';
			
				if	( $data['reg']['vigencia'] < 0 )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Vigencia') . '</strong> ' . label('mayor o igual a cero') . '<br>';
				
				// if	( empty($data['reg']['direccion']) )
				// 	$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Dirección') . '</strong><br>';
			
				// if	( empty($data['reg']['idciudad']) )
				// 	$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad') . '</strong><br>';
			
				// if	( empty($data['reg']['telefono']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Teléfono') . '</strong><br>';
			
				// if	( empty($data['reg']['celular']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Celular') . '</strong><br>';
				
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$id = $this->model->actualizarUsuario($data['reg'], $id);

					if ($id) 
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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/usuarios/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/usuarios/lista/' . $_SESSION['USUARIOS']['Pagina'];

				$query = 'SELECT * FROM USUARIOS WHERE USUARIOS.Id = ' . $id;
				
				$data['reg'] = $this->model->leer($query);
				$data['mensajeError'] = '';
				
				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			$query = 'SELECT * FROM USUARIOS WHERE USUARIOS.Id = ' . $id;
				
			$data['reg'] = $this->model->leer($query);
			$data['mensajeError'] = '';

			if (isset($_REQUEST['id']))
			{
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarUsuario($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/usuarios/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/usuarios/lista/' . $_SESSION['USUARIOS']['Pagina'];

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
			$_SESSION['Lista'] = SERVERURL . '/ciudades/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['CIUDADES']['Filtro'];

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

					$query .= "UPPER(REPLACE(CIUDADES.Ciudad, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CIUDADES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CIUDADES.Departamento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CIUDADES.Pais, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['CIUDADES']['Orden']; 
			$data['rows'] = $this->model->listarCiudades($query);
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
									$Excel[$row][0] = $oHoja->getCell('A' . $i)->getCalculatedValue();
									$Excel[$row][1] = $oHoja->getCell('B' . $i)->getCalculatedValue();
									$Excel[$row][2] = $oHoja->getCell('C' . $i)->getCalculatedValue();
									$Excel[$row][3] = $oHoja->getCell('D' . $i)->getCalculatedValue();
									$row++;
								}
							}

							for ( $i = 0; $i < count($Excel); $i++ )
							{
								// BUSCAMOS LA CIUDAD PARA ADICIONAR O ACTUALIZAR
								$query = 'SELECT * ' .
										'FROM CIUDADES ' .
										"WHERE CIUDADES.Ciudad = '" . $Excel[$i][0] . "'";

								$reg = $this->model->buscarCiudad($query);

								if ($reg) 
									$this->model->actualizarCiudad($Excel[$i], $reg['id']);
								else
									$this->model->guardarCiudad($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/ciudades/lista/' . $_SESSION['CIUDADES']['Pagina']);
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/ciudades/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/ciudades/lista/' . $_SESSION['CIUDADES']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>

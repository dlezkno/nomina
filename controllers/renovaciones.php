<?php
	require_once('./templates/vendor/autoload.php');
	require './templates/PHPMailer-master/src/PHPMailer.php';
	require './templates/PHPMailer-master/src/SMTP.php';
	require './templates/PHPMailer-master/src/Exception.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Renovaciones extends Controllers
	{

		

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
			$_SESSION['Correo'] = SERVERURL . '/renovaciones/enviarCorreo';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['RENOVACIONES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['RENOVACIONES']['Pagina'];

			$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'CORREO ELECTRÓNICO'");
			$TipoPlantillaAdjunto = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'DOCUMENTO'");
			$EstadoEmpleado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ACTIVO'");

			$conditional = "PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND PLANTILLAS.TipoPlantilla = $TipoPlantilla ";
			$conditionalAdjunto = "PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND PLANTILLAS.TipoPlantilla = $TipoPlantillaAdjunto ";
			$defaultTemplate = array("asunto" => "", "plantilla" => "");

			$data['mensajeError'] = '';

			$confirmation = getRegistro('PLANTILLAS', 0, "$conditional AND PLANTILLAS.ASunto = 'CONFIRMACION DE RENOVACION DE CONTRATO'");
			if (!isset($confirmation)) {
				$confirmation = $defaultTemplate;
				$data['mensajeError'] .= label('No hay una plantilla CONFIRMACION DE RENOVACION DE CONTRATO para envío de correo.') . '<br>';
			}

			$renovation = getRegistro('PLANTILLAS', 0, "$conditional AND PLANTILLAS.ASunto = 'CONFIRMACION DE RENOVACION DE CONTRATO'");
			if (!$renovation) {
				$renovation = $defaultTemplate;
				$data['mensajeError'] .= label('No hay una plantilla CONFIRMACION DE RENOVACION DE CONTRATO para envío de correo.') . '<br>';
			}

			$adjuntoRenovacion = getRegistro('PLANTILLAS', 0, "$conditionalAdjunto AND PLANTILLAS.ASunto = 'RENOVACION CONTRATO A TERMINO FIJO'");
			if (!$adjuntoRenovacion) {
				$adjuntoRenovacion = $defaultTemplate;
				$data['mensajeError'] .= label('No hay una plantilla DOCUMENTO DE RENOVACION CONTRATO A TERMINO FIJO para envío a firma electronica.') . '<br>';
			}

			$noRenovation = getRegistro('PLANTILLAS', 0, "$conditional AND PLANTILLAS.ASunto = 'ENVIO CORREO NO RENOVACION'");
			if (!$noRenovation) {
				$noRenovation = $defaultTemplate;
				$data['mensajeError'] .= label('No hay una plantilla NO RENOVACION DE CONTRATO para envío de correo.') . '<br>';
			}

			$adjuntoNORenovacion = getRegistro('PLANTILLAS', 0, "$conditionalAdjunto AND PLANTILLAS.ASunto = 'AVISO DE NO RENOVACION CONTRATO DE TRABAJO'");
			if (!$adjuntoNORenovacion) {
				$adjuntoNORenovacion = $defaultTemplate;
				$data['mensajeError'] .= label('No hay una plantilla DOCUMENTO DE AVISO DE NO RENOVACION CONTRATO DE TRABAJO para envío a firma electronica.') . '<br>';
			}

			$data['templates'] = array(
				"Confirmación" => $confirmation,
				"Renovación" => $renovation,
				"NoRenovación" => $noRenovation);
			$data["templatesAdjunto"] = array(				
				"AdjuntoRenovación" => $adjuntoRenovacion,
				"AdjuntoNoRenovación" => $adjuntoNORenovacion
			);

			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['RENOVACIONES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['RENOVACIONES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['RENOVACIONES']['Filtro']))
			{
				$_SESSION['RENOVACIONES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['RENOVACIONES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['RENOVACIONES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['RENOVACIONES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['RENOVACIONES']['Orden'])) 
					$_SESSION['RENOVACIONES']['Orden'] = 'YEAR(EMPLEADOS.FechaVencimiento), MONTH(EMPLEADOS.FechaVencimiento), DAY(EMPLEADOS.FechaVencimiento), EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2';

			$query = "WHERE PARAMETROS.Detalle = 'ACTIVO' 
				AND EMPLEADOS.FechaVencimiento <= EOMONTH(GETDATE() + 60)
				AND PARAMETROS2.Detalle = 'TERMINO FIJO' ";

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					$query .= 'AND (';
					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= ') ';
				}
			}

			$queryOrg = $query;

			// ENVIAR CORREOS A GERENTES DE PROYECTO
			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Correo') {

				
				$dataTemplates = new stdClass();
				foreach (array_keys($_REQUEST) as $key) {
					if(strrpos($key, "DataRenovations") !== false){		
						$id = explode('_', $key)[1];
						if(!isset($dataTemplates->{$id})){
							$dataTemplates->{$id} = new stdClass();
							$dataTemplates->{$id}->confirmacion = new stdClass();
							$dataTemplates->{$id}->renovacion = new stdClass();
							$dataTemplates->{$id}->norenovacion = new stdClass();
						}
					}
				}

				foreach($dataTemplates as $id => $obj) {
					foreach (array_keys($_REQUEST) as $key) {
						if(strrpos($key, $id) !== false){
							if(strrpos($key, "-Confirmación") !== false){
								$dataTemplates->{$id}->confirmacion->{$key} = $_REQUEST[$key];
							}
							if(strrpos($key, "-Renovación") !== false){
								$dataTemplates->{$id}->renovacion->{$key} =  $_REQUEST[$key];
							}
							if(strrpos($key, "-No_Renovación") !== false){
								$dataTemplates->{$id}->norenovacion->{$key} =  $_REQUEST[$key];
							}
						}
					}
				}
				$emails = array();
				foreach($dataTemplates as $id => $obj) {
					foreach($obj as $plant => $conreno) {
						foreach($conreno as $name => $tosubtem) {
							if(strpos($name,"-to") !== false && $tosubtem != ""){
								array_push($emails, $conreno);
							}
						}
					}
				}

				for($j = 0; $j < count($emails); $j++) {
					$to;
					$sub;
					$tem;
					$adj;
					$IdEmp;
					foreach($emails[$j] as $nameItem => $objEmail) {						
						if(strpos($nameItem,"-to") !== false){
							$IdEmp = explode('_', $nameItem)[1];
							$to = $objEmail;
						}
						if(strpos($nameItem,"subject") !== false){
							$sub = $objEmail;
						}
						if(strpos($nameItem,"template") !== false){
							$tem = $objEmail;
						}
						if(strpos($nameItem,"-adjunto") !== false){
							$adj = $objEmail;
						}
						
					}

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
					$fromName = 'CONTRATACION COMWARE';
					
					$mail->Subject = $sub;
					$mail->addEmbeddedImage(LOGOTIPO, 'comware');
					$mail->Body = $tem;

					
					$aMails = explode(',', $to);

					for ($i = 0; $i < count($aMails); $i++)
					{
						$mail->AddAddress($aMails[$i]);
					}
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
						logRequests("RENOVACIONES",$tem,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $mail->to);
					} 
					catch (Exception $e) 
					{
						logRequests("RENOVACIONES",$tem,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $to);
						$data['mensajeError'] .= "Error al enviar correo <br>";
						$mail->getSMTPInstance()->reset();
					}

					$mail->clearAddresses();
					$mail->clearAttachments();

					if($adj){

						$regEmpleado = getRegistro('EMPLEADOS', $IdEmp);

						$Documento 	= $regEmpleado['documento'];
						$NombreEmpleado	= strtoupper(trim($regEmpleado['apellido1']) . ' ' . trim($regEmpleado['apellido2']) . ' ' . trim($regEmpleado['nombre1']) . ' ' . trim($regEmpleado['nombre2']));
						$Nombre 	= strtoupper(trim($regEmpleado['nombre1']) . ' ' . trim($regEmpleado['nombre2']));
						$Email 		= $to;//$regEmpleado['email'];
						$Celular 	= $regEmpleado['celular'];


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

						
						$Plantilla = $adj;
						// $Plantilla = str_replace('<<FechaActual>>', getDate() , $Plantilla);
						// $Plantilla = str_replace('<<NombreEmpleado>>', $NombreEmpleado, $Plantilla);
						// $Plantilla = str_replace('<<TerminacionContrato>>', $regEmpleado['fechavencimiento'], $Plantilla);
						$Plantilla = utf8_decode($Plantilla);
						$Asunto 			= utf8_decode(cleanAccents($sub));
						$pdf = new PDF2();
						
						$pdf->AliasNbPages();
						$pdf->SetAutoPageBreak(true, 25);
						$pdf->SetLeftMargin(25);
							
						$pdf->AddPage();
						$pdf->SetFont('Arial','',10);

						// $pdf->WriteHTML($Plantilla);
						$pdf->MultiCell(170, 5, $Plantilla, 0, 'J', FALSE);
						
						$cDirectorio = 'documents/' . $regEmpleado['documento'] . '_' . strtoupper($regEmpleado['apellido1'] . '_' . $regEmpleado['apellido2'] . '_' . $regEmpleado['nombre1'] . '_' . $regEmpleado['nombre2']);

						if	( ! is_dir($cDirectorio) )
							mkdir($cDirectorio);

						$cDirectorio .= '/CONTRATOS';

						if	( ! is_dir($cDirectorio) )
							mkdir($cDirectorio);

						$pdf->Output('F', $cDirectorio . '/' . $regEmpleado['documento'] . '_' . strtoupper($Asunto) . '.PDF', TRUE); 


						$archivos_base64 = '';
						global $CodigoDocumento;
						$Plantilla = utf8_decode($adj);
						$Archivo  = utf8_decode($sub);

						$Archivo  = utf8_decode($Asunto);
						$archivo1 = base64_encode(file_get_contents($cDirectorio . '/' . $regEmpleado['documento'] . '_' . strtoupper($Asunto) . '.PDF'));

						unlink($cDirectorio . '/' . $regEmpleado['documento'] . '_' . strtoupper($Asunto) . '.PDF');

						$archivos_base64 = <<<EOD
							"ArchivosPDF": [
								{
									"Nombre": "$Archivo", 
									"Documento_base64": "$archivo1"
								}
							]
						EOD;

						$datos_firma = $data_Firma . $archivos_base64;

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
					logRequests("RENOVACIONES","",json_encode(curl_getinfo($curl)), json_encode($response), "FIRMA PLUS");
					curl_close($curl);

					if ($response['Code'] == 1){

					}

					}

				}
				
			}

			// RETOMAR EL QUERY INICIAL PARA QUE NO ARROJE ERRORES DESPUES DE ENVIAR CORREOS
			$query = $queryOrg;
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['RENOVACIONES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$empl = $this->model->listarRenovaciones($query);
			$data['rows'] =  $this->unique_multidim_array($empl,'IdEmpleado');
			$this->views->getView($this, 'renovaciones', $data);
		}


		public function sendMasiveEmails(){
			
			

			header('Location: ' . SERVERURL . '/renovaciones/lista/1');
			
			exit();

		}

		public function unique_multidim_array($array, $key) {
			$temp_array = array();		
			$key_array = array();

			foreach($array as $val) {
				if(count($temp_array) == 0){
					array_push($key_array, $val[$key]);
					array_push($temp_array , $val);
				}else{
					$id = $val[$key];
					$dkey = array_search($id , $key_array);
					if($dkey === false){
						array_push($key_array, $val[$key]);
						array_push($temp_array , $val);
					}
				}
			}
			return $temp_array;		
		}

		public function cargarDatos($id)
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
			$_SESSION['Lista'] = SERVERURL . '/retirados/lista/1';

			// INFORMACION EMPLEADO
			$query = <<<EOD
				SELECT EMPLEADOS.* 
					FROM EMPLEADOS 
					WHERE EMPLEADOS.Id = $id;
			EOD;

			$reg = $this->model->leer($query);

			if ($reg)
			{
				$IdEmpleado = $reg['id'];

				// LOS DATOS TIPO BOOLEAN SE CARGAN COMO TRUE o FALSE
				// AL MOMENTO DE ACTUALIZAR (VALIDAR DATOS) SE PASAN A 'TRUE' o 'FALSE' PARA EVITAR ERRORES CON PDO
				$data['reg'] = array(
					'Id' => $reg['id'], 
					'TipoIdentificacion' => $reg['tipoidentificacion'], 
					'Documento' => $reg['documento'], 
					'CodigoSAP' => $reg['codigosap'], 
					'FechaExpedicion' => $reg['fechaexpedicion'], 
					'IdCiudadExpedicion' => $reg['idciudadexpedicion'], 
					'Apellido1' => $reg['apellido1'], 
					'Apellido2' => $reg['apellido2'], 
					'Nombre1' => $reg['nombre1'], 
					'Nombre2' => $reg['nombre2'], 
					'FechaNacimiento' => $reg['fechanacimiento'], 
					'IdCiudadNacimiento' => $reg['idciudadnacimiento'], 
					'Genero' => $reg['genero'], 
					'EstadoCivil' => $reg['estadocivil'], 
					'FactorRH' => $reg['factorrh'], 
					'LibretaMilitar' => $reg['libretamilitar'], 
					'DistritoMilitar' => $reg['distritomilitar'], 
					'LicenciaConduccion' => $reg['licenciaconduccion'], 
					'TarjetaProfesional' => $reg['tarjetaprofesional'], 
					'Direccion' => $reg['direccion'], 
					'Barrio' => $reg['barrio'], 
					'Localidad' => $reg['localidad'], 
					'IdCiudad' => $reg['idciudad'], 
					'Email' => $reg['email'], 
					'Telefono' => $reg['telefono'], 
					'Celular' => $reg['celular'], 
					'PerfilProfesional' => $reg['perfilprofesional'],
					'IdCentro' => $reg['idcentro'],
					'TipoContrato' => $reg['tipocontrato'],
					'IdCategoria' => $reg['idcategoria'],
					'IdCiudadTrabajo' => $reg['idciudadtrabajo'],
					'FechaIngreso' => $reg['fechaingreso'],
					'FechaPeriodoPrueba' => $reg['fechaperiodoprueba'],
					'FechaVencimiento' => $reg['fechavencimiento'],
					'ModalidadTrabajo' => $reg['modalidadtrabajo'],
					'IdCargo' => $reg['idcargo'], 
					'SueldoBasico' => $reg['sueldobasico'],
					'SubsidioTransporte' => $reg['subsidiotransporte'],
					'PeriodicidadPago' => $reg['periodicidadpago'],
					'IdEPS' => $reg['ideps'],
					'RegimenCesantias' => $reg['regimencesantias'],
					'FactorPrestacional' => $reg['factorprestacional'],
					'IdFondoCesantias' => $reg['idfondocesantias'],
					'IdFondoPensiones' => $reg['idfondopensiones'],
					'FormaDePago' => $reg['formadepago'],
					'IdBanco' => $reg['idbanco'],
					'TipoCuentaBancaria' => $reg['tipocuentabancaria'],
					'CuentaBancaria' => $reg['cuentabancaria'],
					'MetodoRetencion' => $reg['metodoretencion'],
					'PorcentajeRetencion' => $reg['porcentajeretencion'],
					'MayorRetencionFuente' => $reg['mayorretencionfuente'],
					'DeduccionDependientes' => $reg['deducciondependientes'],
					'CuotaVivienda' => $reg['cuotavivienda'],
					'SaludYEducacion' => $reg['saludyeducacion'],
					'PoliticamenteExpuesta' => ($reg['politicamenteexpuesta'] ? true : false), 
					'AceptaPoliticaTD' => ($reg['aceptapoliticatd'] ? true : false), 
					'Estado' => $reg['estado'] 
				);
				$data['mensajeError'] = '';

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
							'Id' => $dataExp[$i]['id'],
							'Empresa' => $dataExp[$i]['empresa'],
							'IdCiudad' => $dataExp[$i]['idciudad'],
							'Cargo' => $dataExp[$i]['cargo'],
							'JefeInmediato' => $dataExp[$i]['jefeinmediato'],
							'Telefono' => $dataExp[$i]['telefono'],
							'FechaIngreso' => $dataExp[$i]['fechaingreso'],
							'FechaRetiro' => $dataExp[$i]['fecharetiro'],
							'Responsabilidades' => $dataExp[$i]['responsabilidades']
						);
					}
				}
				else
					$data['regEmp'] = false;
	
				// EDUCACION FORMAL
				$query = <<<EOD
					SELECT EDUCACIONEMPLEADO.*, 
							PARAMETROS.Detalle AS NombreNivelAcademico 
						FROM EDUCACIONEMPLEADO 
							INNER JOIN EMPLEADOS 
								ON EDUCACIONEMPLEADO.IdEmpleado = EMPLEADOS.Id 
							INNER JOIN PARAMETROS 
								ON EDUCACIONEMPLEADO.NivelAcademico = PARAMETROS.Id
						 WHERE EMPLEADOS.Id = $IdEmpleado AND 
							 EDUCACIONEMPLEADO.TipoEducacion = 1; 
				EOD;
	
				$dataEduF = $this->model->listar($query);
	
				if ($dataEduF)
				{
					for ($i = 0; $i < count($dataEduF); $i++) 
					{ 
						$data['regEduF'][$i] = array(
							'Id' => $dataEduF[$i]['id'],
							'CentroEducativo' => $dataEduF[$i]['centroeducativo'],
							'NivelAcademico' => $dataEduF[$i]['nivelacademico'],
							'Estudio' => $dataEduF[$i]['estudio'],
							'Estado' => $dataEduF[$i]['estado'],
							'AnoInicio' => $dataEduF[$i]['anoinicio'],
							'MesInicio' => $dataEduF[$i]['mesinicio'],
							'AnoFinalizacion' => $dataEduF[$i]['anofinalizacion'],
							'MesFinalizacion' => $dataEduF[$i]['mesfinalizacion'],
							'NombreNivelAcademico' => $dataEduF[$i]['NombreNivelAcademico']
						);
					}
				}
				else
					$data['regEduF'] = false;
	
				// EDUCACION NO FORMAL
				$query = <<<EOD
					SELECT EDUCACIONEMPLEADO.*
						FROM EDUCACIONEMPLEADO 
							INNER JOIN EMPLEADOS 
								ON EDUCACIONEMPLEADO.IdEmpleado = EMPLEADOS.Id 
						WHERE EMPLEADOS.Id = $IdEmpleado AND 
							EDUCACIONEMPLEADO.TipoEducacion = 2;
				EOD;
				
				$dataEduNF = $this->model->listar($query);
	
				if ($dataEduNF)
				{
					for ($i = 0; $i < count($dataEduNF); $i++) 
					{ 
						$data['regEduNF'][$i] = array(
							'Id' => $dataEduNF[$i]['id'],
							'CentroEducativo' => $dataEduNF[$i]['centroeducativo'],
							'NivelAcademico' => $dataEduNF[$i]['nivelacademico'],
							'Estudio' => $dataEduNF[$i]['estudio'],
							'Estado' => $dataEduNF[$i]['estado'],
							'AnoInicio' => $dataEduNF[$i]['anoinicio'],
							'MesInicio' => $dataEduNF[$i]['mesinicio'],
							'AnoFinalizacion' => $dataEduNF[$i]['anofinalizacion'],
							'MesFinalizacion' => $dataEduNF[$i]['mesfinalizacion']
						);
					}
				}
				else
					$data['regEduNF'] = false;
	
				// IDIOMAS
				$query = <<<EOD
					SELECT IDIOMASEMPLEADO.*, 
							IDIOMAS.Nombre,
							PARAMETROS.Detalle AS NombreNivelIdioma 
						FROM IDIOMASEMPLEADO 
							INNER JOIN EMPLEADOS 
								ON IDIOMASEMPLEADO.IdEmpleado = EMPLEADOS.Id 
							INNER JOIN IDIOMAS
								ON IDIOMASEMPLEADO.IdIdioma = IDIOMAS.Id 
							INNER JOIN PARAMETROS
								ON IDIOMASEMPLEADO.Nivel = PARAMETROS.Id 
						WHERE EMPLEADOS.Id = $IdEmpleado;
				EOD;
	
				$dataIdiomas = $this->model->listar($query);
	
				if ($dataIdiomas)
				{
					for ($i = 0; $i < count($dataIdiomas); $i++) 
					{ 
						$data['regIdiomas'][$i] = array(
							'Id' => $dataIdiomas[$i]['id'],
							'IdIdioma' => $dataIdiomas[$i]['ididioma'],
							'Nivel' => $dataIdiomas[$i]['nivel'],
							'Nombre' => $dataIdiomas[$i]['Nombre'],
							'NombreNivelIdioma' => $dataIdiomas[$i]['NombreNivelIdioma']
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
							'Id' => $dataOCE[$i]['id'],
							'Conocimiento' => $dataOCE[$i]['conocimiento'],
							'Nivel' => $dataOCE[$i]['nivel'],
							'NombreNivelConocimiento' =>  $dataOCE[$i]['NombreNivelConocimiento']
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
						$data['regContactos'][$i] = array(
							'Id' => $dataContactos[$i]['id'],
							'Nombre' => $dataContactos[$i]['nombre'],
							'Telefono' => $dataContactos[$i]['telefono'],
							'Parentesco' => $dataContactos[$i]['parentesco'],
							'NombreParentesco' => $dataContactos[$i]['NombreParentesco']
						);
					}
				}
				else
					$data['regContactos'] = false;
			}
			else
				$data['mensajeError'] = 'Empleado no existe en la base de datos';

			return($data);
		}

		public function consultar($id)
		{
			$data = $this->cargarDatos($id);
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
			$_SESSION['Lista'] = SERVERURL . '/retirados/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['RENOVACIONES']['Filtro'];

			$query = "WHERE ((PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.FechaVencimiento <= EOMONTH(GETDATE() + 60)) OR
						(PARAMETROS.Detalle = 'RETIRADO' AND 
						YEAR(EMPLEADOS.FechaVencimiento) = YEAR(GETDATE()) AND 
						MONTH(EMPLEADOS.FechaVencimiento) = MONTH(GETDATE()))) ";

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					$query .= 'AND (';
					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= ') ';
				}
			}

			$query .= 'ORDER BY EMPLEADOS3.Documento, EMPLEADOS2.Documento, YEAR(EMPLEADOS.FechaVencimiento), MONTH(EMPLEADOS.FechaVencimiento), DAY(EMPLEADOS.FechaVencimiento), EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2';

			$data['rows'] = $this->model->listarRenovaciones($query);
			$this->views->getView($this, 'informe', $data);
		}
	}
?>
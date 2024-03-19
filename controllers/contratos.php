<?php
	require_once('./templates/vendor/autoload.php');
	require './templates/PHPMailer-master/src/PHPMailer.php';
	require './templates/PHPMailer-master/src/SMTP.php';
	require './templates/PHPMailer-master/src/Exception.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	// use setasign\Fpdi\Fpdi;

	class Contratos extends Controllers
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
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = FALSE;

			

			$_SESSION['CONTRATOS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['CONTRATOS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['CONTRATOS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['CONTRATOS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['CONTRATOS']['Filtro']))
			{
				$_SESSION['CONTRATOS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['CONTRATOS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['CONTRATOS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['CONTRATOS']['Pagina'] = 1;
				$pagina = 1;
			}
			else{
				if (! isset($_SESSION['CONTRATOS']['Orden'])) {
					$_SESSION['CONTRATOS']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';
				}
			}
			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'DESCARGAS')
			{
				$query = <<<EOD
					SELECT EMPLEADOS.Id, 
							EMPLEADOS.Documento, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							EMPLEADOS.SolicitudFirma, 
							EMPLEADOS.FechaSolicitud, 
							EMPLEADOS.LinkFirma 
						FROM EMPLEADOS 
							INNER JOIN PARAMETROS 
								ON EMPLEADOS.Estado = PARAMETROS.Id 
						WHERE PARAMETROS.Detalle = 'EN PROCESO DE CONTRATACION' AND 
							EMPLEADOS.CNT_ContratosEnviados = 1 AND 
							(EMPLEADOS.CNT_ContratosFirmados = 0 OR 
							EMPLEADOS.CNT_ContratosFirmados IS NULL);
				EOD;

				$candidatos = $this->model->listar($query);

				if ($candidatos)
				{
					for ($i = 0; $i < count($candidatos); $i++)
					{
						$regCandidato = $candidatos[$i];

						$IdEmpleado = $regCandidato['Id'];
						$Documento 	= trim($regCandidato['Documento']);
						$Apellido1 	= strtoupper(trim($regCandidato['Apellido1']));
						$Apellido2	= strtoupper(trim($regCandidato['Apellido2']));
						$Nombre1	= strtoupper(trim($regCandidato['Nombre1']));
						$Nombre2	= strtoupper(trim($regCandidato['Nombre2']));
						$SolicitudFirma = $regCandidato['SolicitudFirma'];

						// SE HACE LA CONSULTA DE LA SOLICITUD
						$curl = curl_init();

						curl_setopt_array(
							$curl, array(
								CURLOPT_URL => URL_FIRMA . "resumenfirma/" . $SolicitudFirma,
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

						$response = curl_exec($curl);
						

						$response = json_decode($response, true);

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
								$cDirectorio = str_replace(" ","",'documents/' . $Documento . '_' . $Apellido1 . '_' . $Apellido2 . '_' . $Nombre1 . '_' . $Nombre2);

								if	( ! is_dir($cDirectorio) )
									mkdir($cDirectorio);

								if	( ! is_dir($cDirectorio . '/CONTRATOS') )
									mkdir($cDirectorio . '/CONTRATOS');

								$archivoDestino = $cDirectorio . '/CONTRATOS/' . $Documento . '_' . $Fecha . '_FOTOGRAFIA.JPG';

								$ok = file_put_contents($archivoDestino, $Fotografia, LOCK_EX);
							}

							for ($j = 0; $j < count($response['Data']['DocumentosPDF']); $j++)
							{
								$TipoDocumento = $response['Data']['DocumentosPDF'][$j]['TipoDocumento'];
								$DocumentoFirmado = base64_decode($response['Data']['DocumentosPDF'][$j]['DocumentoFirmado']);

								$cDirectorio = str_replace(" ","",'documents/' . $Documento . '_' . $Apellido1 . '_' . $Apellido2 . '_' . $Nombre1 . '_' . $Nombre2);

								if	( ! is_dir($cDirectorio) )
									mkdir($cDirectorio);

								if	( ! is_dir($cDirectorio . '/CONTRATOS') )
									mkdir($cDirectorio . '/CONTRATOS');

								$archivoDestino = $cDirectorio . '/CONTRATOS/' . $Documento . '_' . $Fecha . '_' . strtoupper($TipoDocumento) . '.PDF';

								$ok = file_put_contents($archivoDestino, $DocumentoFirmado, LOCK_EX);
							}

							// SE ACTUALIZA EL ESTADO DEL CANDIDATO
							$query = <<<EOD
								UPDATE EMPLEADOS 
									SET CNT_ContratosFirmados = 1  
									WHERE EMPLEADOS.Id = $IdEmpleado;
							EOD;

							$this->model->query($query);
						}

						curl_close($curl);
					}
				}
			}

			
			$IdSicologo = $_SESSION['Login']['Id'];

			$query = "WHERE PARAMETROS.Detalle = 'EN PROCESO DE CONTRATACION' ";

			if	( ! empty($lcFiltro) )
			{
				$query .= "AND (UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($lcFiltro) . "%') ";
			}
			
			// $data['registros'] = $this->model->contarRegistros($query);
			// $lineas = LINES;
			// $offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			// $query .= 'ORDER BY ' . $_SESSION['CANDIDATOS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			
			if (isset($_REQUEST['Action']) AND strrpos($_REQUEST['Action'], "VALIDATE_FIRM_") !== FALSE){
				$id = explode('_', $_REQUEST['Action'])[2];
				$reg = getRegistro('EMPLEADOS', $id);

				$curl = curl_init();

				curl_setopt_array($curl, 
					array(
						CURLOPT_URL => URL_FIRMA . '/consultarsolicitud/'.$reg["solicitudfirma"],
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
						),
					)
				);

				$response = json_decode(curl_exec($curl), true);

				curl_close($curl);

				if ($response['Code'] == 1){
					if($response["Data"]["Estado"] == "FIRMADO"){
						$querychange = <<<EOD
								UPDATE EMPLEADOS 
									SET CNT_ContratosFirmados = 1  
									WHERE EMPLEADOS.Id = $id;
							EOD;

							$this->model->query($querychange);
					}
				}

			}

			$query .= 'ORDER BY ' . $_SESSION['CONTRATOS']['Orden']; 
			$data['rows'] = $this->model->listarCandidatos($query);
			$this->views->getView($this, 'contratos', $data);
		}	
		
		public function cargarDatos($Id)
		{
			// INFORMACION EMPLEADO
			$reg = getRegistro('EMPLEADOS', $Id);

			if ($reg)
			{

				$SolicitudFirma = $reg['solicitudfirma'];

				//VALIDA DOCUMENTO DE FIRMA PLUS

				if(isset($SolicitudFirma)) {

					$Documento 	= trim($reg['documento']);
					$Apellido1 	= strtoupper(trim($reg['apellido1']));
					$Apellido2	= strtoupper(trim($reg['apellido2']));
					$Nombre1	= strtoupper(trim($reg['nombre1']));
					$Nombre2	= strtoupper(trim($reg['nombre2']));

					// SE HACE LA CONSULTA DE LA SOLICITUD
					$curl = curl_init();

					curl_setopt_array(
						$curl, array(
							CURLOPT_URL => URL_FIRMA . "resumenfirma/" . $SolicitudFirma,
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

					$response = curl_exec($curl);
					$response = json_decode($response, true);

					// SE TRASLADAN LOS DOCUMENTOS AL REPOSITORIO
					if ($response['Code'] == '100')
					{
						$Fecha = $response['Data']['Firmante'][0]['Fecha'];
						$Fecha = substr($Fecha, 0, 10);
						$Fecha = substr($Fecha, -4) . '-' . substr($Fecha, 3, 2) . '-' . substr($Fecha, 0, 2);

						for ($j = 0; $j < count($response['Data']['DocumentosPDF']); $j++)
						{
							$TipoDocumento = $response['Data']['DocumentosPDF'][$j]['TipoDocumento'];
							$DocumentoFirmado = base64_decode($response['Data']['DocumentosPDF'][$j]['DocumentoFirmado']);

							$destinoDirectorio = str_replace(" ","",'documents/' . $Documento . '_' . $Apellido1 . '_' . $Apellido2 . '_' . $Nombre1 . '_' . $Nombre2);

							if	( ! is_dir($destinoDirectorio) )
								mkdir($destinoDirectorio);

							if	( ! is_dir($destinoDirectorio . '/CONTRATOS') )
								mkdir($destinoDirectorio . '/CONTRATOS');

							$archivoDestino = $destinoDirectorio . '/CONTRATOS/' . $Documento . '_' . $Fecha . '_' . strtoupper($TipoDocumento) . '.PDF';

							$ok = file_put_contents($archivoDestino, $DocumentoFirmado, LOCK_EX);
						}
					}

					curl_close($curl);
				}

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
					'PoliticamenteExpuesta' => (isset($_REQUEST['PoliticamenteExpuesta']) AND $_REQUEST['PoliticamenteExpuesta']=='si') ? 'true' : 'false',
					'DeclaracionOrigenRecursos' => ($reg['declaracionorigenrecursos'] ? true : false), 
					'UsoLicitoRecursos' 	=> ($reg['usolicitorecursos'] ? true : false), 
					
					'FechaNacimiento' 		=> $reg['fechanacimiento'], 
					'IdCiudadNacimiento' 	=> $reg['idciudadnacimiento'], 
					'Genero' 				=> $reg['genero'], 
					'EstadoCivil' 			=> $reg['estadocivil'], 
					'FactorRH' 				=> $reg['factorrh'], 
					'talla' 				=> $reg['talla'], 
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
					'IdCajaCompensacion' 	=> $reg['idcajacompensacion'],
					'IdARL' 				=> $reg['idarl'],
					'IdBanco' 				=> $reg['idbanco'],
					'TipoCuentaBancaria' 	=> $reg['tipocuentabancaria'],
					'CuentaBancaria' 		=> $reg['cuentabancaria'],
					'CuentaBancaria2' 		=> $reg['cuentabancaria2'],
					'IdCargo' 				=> $reg['idcargo'], 
					'IdJefe' 				=> $reg['idjefe'], 
					'PerfilProfesional' 	=> $reg['perfilprofesional'], 
					
					'EspecialidadAprendiz' 	=> $reg['especialidadaprendiz'],
					'salarioPractica'       => $reg['salariopractica'],
					'InstitucionDeFormacion' 	=> $reg['instituciondeformacion'],
					'FechaIngreso'			=> $reg['fechaingreso'], 					
					'FechaFinEtapaLectiva'		=> $reg['fechafinetapalectiva'],
					'FechaInicioEtapaProductiva'		=> $reg['fechainicioetapaproductiva'],
					'FechaPeriodoPrueba'	=> $reg['fechaperiodoprueba'], 
					'FechaVencimiento'		=> $reg['fechavencimiento'], 

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

					'CertificadoEPS'		=> FALSE, 
					'CertificadoFC'			=> FALSE, 
					'CertificadoFP'			=> FALSE, 
					'CertificadoCCF'		=> FALSE, 
					'CertificadoARL'		=> FALSE, 
					'CertificadoCuenta'		=> FALSE, 

					'EnPobrezaExtrema'		=> FALSE, 
					'Desplazado'			=> FALSE, 
					'EnReincorporacion'		=> FALSE, 
					'AdultoMayor'			=> FALSE, 
					'CabezaHogar'			=> FALSE, 
					'Discapacitado'			=> FALSE, 
					'ComunidadLGBTI'		=> FALSE, 
					'Negritudes'			=> FALSE,
					'Indigenas'				=> FALSE, 
					'GrupoPoblacional'		=> 0,

					'certificadoAprendiz'       => FALSE,
					'AntecedentesProcuraduria'	=> FALSE, 
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
					'SEL_RevisionCliente'		=> $reg['sel_revisioncliente'],
					'SEL_CartaOferta'			=> $reg['sel_cartaoferta'],
					'CNT_DocumentosActualizados'=> $reg['cnt_documentosactualizados'],
					'CNT_CondicionesLaborales'	=> $reg['cnt_condicioneslaborales'],
					'CNT_CartaOferta'			=> $reg['cnt_cartaoferta'],
					'CNT_ContratosEnviados'		=> $reg['cnt_contratosenviados'],


					'IdCargo'				=> $reg['idcargo'],
					'IdJefe'				=> $reg['idjefe'],
					'IdCentro'				=> $reg['idcentro'],
					'IdProyecto'			=> $reg['idproyecto'],
					'IdSede'				=> $reg['idsede'],
					'IdCiudadTrabajo'		=> $reg['idciudadtrabajo'],
					'Vicepresidencia'		=> $reg['vicepresidencia'], 
					'TipoContrato'			=> $reg['tipocontrato'], 
					'SueldoBasico'			=> $reg['sueldobasico'],
					'duracionContrato'      => $reg['duracioncontrato'],
					'Observaciones'			=> $reg['observaciones'],
					'SubsidioTransporte'	=> $reg['subsidiotransporte'],
					'PeriodicidadPago'		=> $reg['periodicidadpago'],
					'RegimenCesantias'		=> $reg['regimencesantias'],
					'NivelRiesgo'			=> $reg['nivelriesgo'],
					'FormaDePago'				=> $reg['formadepago'],
					'MetodoRetencion'		=> $reg['metodoretencion'],

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

					$archivo = strtoupper($archivo);

					if (strpos($archivo, $data['reg']['Documento'] . '_FOTOGRAFIA') !== FALSE)
						$data['reg']['Fotografia'] = TRUE;

					if (strpos($archivo, 'DOCUMENTO_IDENTIDAD') !== FALSE)
						$data['reg']['DocumentoIdentidad'] = TRUE;

					if (strpos($archivo, 'HOJA_DE_VIDA') !== FALSE)
						$data['reg']['HojaVida'] = TRUE;

					if (strpos($archivo, 'TARJETA_PROFESIONAL') !== FALSE)
						$data['reg']['TarjetaProfesional'] = TRUE;

					if (strpos($archivo, 'CERTIFICACION_BANCARIA') !== FALSE)
						$data['reg']['CertificadoCuenta'] = TRUE;
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

					if (strpos($archivo, 'CERTIFICADO_EPS') !== FALSE)
						$data['reg']['CertificadoEPS'] = TRUE;

					if (strpos($archivo, 'CERTIFICADO_FONDO_CESANTIAS') !== FALSE)
						$data['reg']['CertificadoFC'] = TRUE;

					if (strpos($archivo, 'CERTIFICADO_FONDO_PENSIONES') !== FALSE)
						$data['reg']['CertificadoFP'] = TRUE;

					if (strpos($archivo, 'CERTIFICADO_CAJA_COMPENSACION') !== FALSE)
						$data['reg']['CertificadoCCF'] = TRUE;

					if (strpos($archivo, 'CERTIFICADO_ARL') !== FALSE)
						$data['reg']['CertificadoARL'] = TRUE;
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

			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/HV';

			if (is_dir($cDirectorio))
			{
				$dir = opendir($cDirectorio);

				while (($archivo = readdir($dir)) !== false)
				{
					if ($archivo == '.' OR $archivo == '..')
						continue;

					if (strpos($archivo, 'CERTIFICADO_APRENDIZ') !== FALSE)
						$data['reg']['certificadoAprendiz'] = TRUE;
				}
			}

			return($data);
		}

		public function validarDatos($Id, $paso)
		{
			// SE CARGAN DATOS
			$data = $this->cargarDatos($Id);
			$data['mensajeError'] = '';

			$mensajeError = '';

			switch ($paso)
			{
				case 1:

					$centAprendiz = false;

					if($_REQUEST['TipoContrato'] != '144' &&
					$_REQUEST['TipoContrato'] != '428' &&
					$_REQUEST['TipoContrato'] != '429'){
						$centAprendiz = true;
					}


					if	( empty($_REQUEST['IdEPS']) )
						$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('E.P.S.') . '</strong><br>';
					if	( empty($_REQUEST['IdFondoCesantias']) && !$centAprendiz )
						$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de cesantías') . '</strong><br>';
					if	( empty($_REQUEST['IdFondoPensiones']) && !$centAprendiz )
						$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de pensiones') . '</strong><br>';
					if	( empty($_REQUEST['NivelRiesgo']) )
						$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Nivel de riesgo') . '</strong><br>';
					if	( empty($_REQUEST['RegimenCesantias']) && !$centAprendiz )
						$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Régimen de cesantías') . '</strong><br>';
					if	( empty($_REQUEST['FormaDePago']) )
						$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('forma de pago') . '</strong><br>';
					if	( empty($_REQUEST['IdBanco']) )
						$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Entidad bancaria') . '</strong><br>';
					if	( empty($_REQUEST['TipoCuentaBancaria']) )
						$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Tipo cuenta bancaria') . '</strong><br>';
					if	( empty($_REQUEST['CuentaBancaria']) )
						$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuenta bancaria') . '</strong><br>';

					$data['datos'] = array();

					$data['datos']['IdEPS']				= isset($_REQUEST['IdEPS']) ? $_REQUEST['IdEPS'] : 0;
					$data['datos']['IdFondoCesantias'] 	= isset($_REQUEST['IdFondoCesantias']) ? $_REQUEST['IdFondoCesantias'] : 0;
					$data['datos']['IdFondoPensiones'] 	= isset($_REQUEST['IdFondoPensiones']) ? $_REQUEST['IdFondoPensiones'] : 0;
					$data['datos']['IdCajaCompensacion'] 	= isset($_REQUEST['IdCajaCompensacion']) ? $_REQUEST['IdCajaCompensacion'] : 0;
					$data['datos']['IdARL'] 				= isset($_REQUEST['IdARL']) ? $_REQUEST['IdARL'] : 0;
					$data['datos']['NivelRiesgo'] 		= isset($_REQUEST['NivelRiesgo']) ? $_REQUEST['NivelRiesgo'] : 0;
					$data['datos']['RegimenCesantias'] 	= isset($_REQUEST['RegimenCesantias']) ? $_REQUEST['RegimenCesantias'] : 0;
					$data['datos']['FormaDePago'] 		= isset($_REQUEST['FormaDePago']) ? $_REQUEST['FormaDePago'] : 0;
					$data['datos']['IdBanco']				= isset($_REQUEST['IdBanco']) ? $_REQUEST['IdBanco'] : 0;
					$data['datos']['TipoCuentaBancaria'] 	= isset($_REQUEST['TipoCuentaBancaria']) ? $_REQUEST['TipoCuentaBancaria'] : 0;
					$data['datos']['CuentaBancaria']		= isset($_REQUEST['CuentaBancaria']) ? $_REQUEST['CuentaBancaria'] : 0;
					$data['datos']['CuentaBancaria2']		= isset($_REQUEST['CuentaBancaria2']) ? $_REQUEST['CuentaBancaria2'] : "0";

					break;

				case 2:
					$EstadoEmpleado = getRegistro('PARAMETROS', $data['reg']['Estado'])['detalle'];

					$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];

					$MetodoRetencion = getId('PARAMETROS', "PARAMETROS.Parametro = 'MetodoRetencion' AND PARAMETROS.Detalle = 'BUSQUEDA EN TABLA'");

					
					if (! empty($_REQUEST['TipoContrato']))
					{
						$TipoContrato = getRegistro('PARAMETROS', $_REQUEST['TipoContrato'])['detalle'];


						if	($TipoContrato <> 'INDEFINIDO' AND empty($_REQUEST['FechaVencimiento']) )
						{
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de vencimiento') . '</strong><br>';
						
							if ($_REQUEST['FechaVencimiento'] <= $_REQUEST['FechaPeriodoPrueba'])
								$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de vencimiento') . '</strong> posterior a la fecha del período de prueba.<br>';
						}
					}

					if ($_REQUEST['SueldoBasico'] <= 0)
						$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo básico') . '</strong><br>';
					else
					{
						$IdCargo = $_REQUEST['IdCargo'];

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
								if ($_REQUEST['SueldoBasico'] < $reg['SueldoMinimo'] OR 
									$_REQUEST['SueldoBasico'] > $reg['SueldoMaximo']) 
									$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo básico') . '</strong> ' . label('válido para este cargo') . '<br>';
							}
						}
			
						if ($_REQUEST['TipoContrato'] == 3 OR $_REQUEST['TipoContrato'] == 5 OR $_REQUEST['TipoContrato'] == 6 OR $_REQUEST['TipoContrato'] == 7)  // APRENDIZ SENA
							if ($_REQUEST['SueldoBasico'] < $SueldoMinimo) 
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo básico') . '</strong> ' . label('mayor o igual a medio sueldo mínimo legal') . '<br>';
					}

					if ($_REQUEST['SueldoBasico'] <= $SueldoMinimo * 2)
						$_REQUEST['SubsidioTransporte'] = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'SUBSIDIO COMPLETO'");
					else
						$_REQUEST['SubsidioTransporte'] = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'NO RECIBE SUBSIDIO'");

					$data['datos'] = array();

					// SE ACTUALIZAN TODOS LOS DATOS DE (IDENTIFICACION) DEL CANDIDATO

					
					$data['datos']['TipoIdentificacion'] 	= isset($_REQUEST['TipoIdentificacion']) ? $_REQUEST['TipoIdentificacion'] : '';
					$data['datos']['Documento']				= isset($_REQUEST['Documento']) ? $_REQUEST['Documento'] : '';
					$data['datos']['FechaExpedicion']		= isset($_REQUEST['FechaExpedicion']) ? $_REQUEST['FechaExpedicion'] : '';
					$data['datos']['IdCiudadExpedicion']	= isset($_REQUEST['IdCiudadExpedicion']) ? $_REQUEST['IdCiudadExpedicion'] : '';
					$data['datos']['Apellido1']				= isset($_REQUEST['Apellido1']) ? $_REQUEST['Apellido1'] : '';
					$data['datos']['Apellido2']				= isset($_REQUEST['Apellido2']) ? $_REQUEST['Apellido2'] : '';
					$data['datos']['Nombre1']				= isset($_REQUEST['Nombre1']) ? $_REQUEST['Nombre1'] : '';
					$data['datos']['Nombre2']				= isset($_REQUEST['Nombre2']) ? $_REQUEST['Nombre2'] : '';


					// SE ACTUALIZAN TODOS LOS DATOS DE (INFORMACIÓN PERSONAL) DEL CANDIDATO

					$data['datos']['FechaNacimiento'] 		= isset($_REQUEST['FechaNacimiento']) ? $_REQUEST['FechaNacimiento'] : '';
					$data['datos']['IdCiudadNacimiento']	= isset($_REQUEST['IdCiudadNacimiento']) ? $_REQUEST['IdCiudadNacimiento'] : '';
					$data['datos']['Genero']				= isset($_REQUEST['Genero']) ? $_REQUEST['Genero'] : '';
					$data['datos']['EstadoCivil']			= isset($_REQUEST['EstadoCivil']) ? $_REQUEST['EstadoCivil'] : '';
					$data['datos']['FactorRH']				= isset($_REQUEST['FactorRH']) ? $_REQUEST['FactorRH'] : '';
					$data['datos']['talla']					= isset($_REQUEST['talla']) ? $_REQUEST['talla'] : '';
					$data['datos']['LibretaMilitar']		= isset($_REQUEST['LibretaMilitar']) ? $_REQUEST['LibretaMilitar'] : '';
					$data['datos']['DistritoMilitar']		= isset($_REQUEST['DistritoMilitar']) ? $_REQUEST['DistritoMilitar'] : '';
					$data['datos']['LicenciaConduccion']	= isset($_REQUEST['LicenciaConduccion']) ? $_REQUEST['LicenciaConduccion'] : '';
					$data['datos']['TarjetaProfesional']	= isset($_REQUEST['TarjetaProfesional']) ? $_REQUEST['TarjetaProfesional'] : '';


					// SE ACTUALIZAN TODOS LOS DATOS DE (INFORMACIÓN DE CONTACTO) DEL CANDIDATO

					$data['datos']['Direccion']		= isset($_REQUEST['Direccion']) ? $_REQUEST['Direccion'] : '';
					$data['datos']['Barrio']		= isset($_REQUEST['Barrio']) ? $_REQUEST['Barrio'] : '';
					$data['datos']['Localidad']		= isset($_REQUEST['Localidad']) ? $_REQUEST['Localidad'] : '';
					$data['datos']['IdCiudad']		= isset($_REQUEST['IdCiudad']) ? $_REQUEST['IdCiudad'] : '';
					$data['datos']['Email']			= isset($_REQUEST['Email']) ? $_REQUEST['Email'] : '';
					$data['datos']['Telefono']		= isset($_REQUEST['Telefono']) ? $_REQUEST['Telefono'] : '';
					$data['datos']['Celular']		= isset($_REQUEST['Celular']) ? $_REQUEST['Celular'] : '';



					// SE ACTUALIZAN TODOS LOS DATOS DE (AFILIACIONES) DEL CANDIDATO

					$data['datos']['IdEPS']				= isset($_REQUEST['IdEPS']) ? $_REQUEST['IdEPS'] : 0;
					$data['datos']['IdFondoCesantias'] 	= isset($_REQUEST['IdFondoCesantias']) ? $_REQUEST['IdFondoCesantias'] : 0;
					$data['datos']['IdFondoPensiones'] 	= isset($_REQUEST['IdFondoPensiones']) ? $_REQUEST['IdFondoPensiones'] : 0;
					$data['datos']['IdBanco']				= isset($_REQUEST['IdBanco']) ? $_REQUEST['IdBanco'] : 0;
					$data['datos']['TipoCuentaBancaria'] 	= isset($_REQUEST['TipoCuentaBancaria']) ? $_REQUEST['TipoCuentaBancaria'] : 0;
					$data['datos']['CuentaBancaria']		= isset($_REQUEST['CuentaBancaria']) ? $_REQUEST['CuentaBancaria'] : 0;



					// SE ACTUALIZAN TODOS LOS DATOS DE ( CONDICIONES LABORALES ) DEL CANDIDATO


					$data['datos']['IdCargo']			= isset($_REQUEST['IdCargo']) ? $_REQUEST['IdCargo'] : 0;
					$data['datos']['IdJefe']			= isset($_REQUEST['IdJefe']) ? $_REQUEST['IdJefe'] : 0;
					$data['datos']['IdCiudadTrabajo']	= isset($_REQUEST['IdCiudadTrabajo']) ? $_REQUEST['IdCiudadTrabajo'] : 0;
					$data['datos']['IdCentro']			= isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0;
					$data['datos']['IdProyecto']		= isset($_REQUEST['IdProyecto']) ? $_REQUEST['IdProyecto'] : 0;
					$data['datos']['Vicepresidencia']	= isset($_REQUEST['Vicepresidencia']) ? $_REQUEST['Vicepresidencia'] : 0;
					$data['datos']['IdSede']			= isset($_REQUEST['IdSede']) ? $_REQUEST['IdSede'] : 0;
					$data['datos']['TipoContrato']		= isset($_REQUEST['TipoContrato']) ? $_REQUEST['TipoContrato'] : 0;
					$data['datos']['ModalidadTrabajo']	= isset($_REQUEST['ModalidadTrabajo']) ? $_REQUEST['ModalidadTrabajo'] : 0;
					$data['datos']['SueldoBasico']		= isset($_REQUEST['SueldoBasico']) ? $_REQUEST['SueldoBasico'] : 0;
					$data['datos']['duracionContrato']	= isset($_REQUEST['duracionContrato']) ? $_REQUEST['duracionContrato'] : 0;
					$data['datos']['Observaciones']		= isset($_REQUEST['Observaciones']) ? $_REQUEST['Observaciones'] : '';
					$data['datos']['FechaPeriodoPrueba']= isset($_REQUEST['FechaPeriodoPrueba']) ? $_REQUEST['FechaPeriodoPrueba'] : NULL; 
					$data['datos']['FechaIngreso']		= isset($_REQUEST['FechaIngreso']) ? $_REQUEST['FechaIngreso'] : NULL; 
					
					$data['datos']['salarioPractica']	= (!isset($_REQUEST['salarioPractica']) || $_REQUEST['salarioPractica'] == '') ? 0 : $_REQUEST['salarioPractica'];
					$data['datos']['InstitucionDeFormacion']	= isset($_REQUEST['InstitucionDeFormacion']) ? $_REQUEST['InstitucionDeFormacion'] : '';
					$data['datos']['EspecialidadAprendiz']	= isset($_REQUEST['EspecialidadAprendiz']) ? $_REQUEST['EspecialidadAprendiz'] : '';
					$data['datos']['FechaFinEtapaLectiva'] =  isset($_REQUEST['FechaFinEtapaLectiva']) ? $_REQUEST['FechaFinEtapaLectiva'] : null;
					$data['datos']['FechaInicioEtapaProductiva'] =  isset($_REQUEST['FechaInicioEtapaProductiva']) ? $_REQUEST['FechaInicioEtapaProductiva'] : NULL;


					if (empty($data['datos']['FechaIngreso']) OR $data['datos']['FechaIngreso'] == '1900-01-01'){
						$data['datos']['FechaIngreso'] = NULL;
					}

					$data['datos']['FechaVencimiento']	= isset($_REQUEST['FechaVencimiento']) ? $_REQUEST['FechaVencimiento'] : NULL;
					
					if (empty($data['datos']['FechaVencimiento']) OR $data['datos']['FechaVencimiento'] == '1900-01-01'){
						$data['datos']['FechaVencimiento'] = NULL;
					}

					$data['datos']['SubsidioTransporte']= isset($_REQUEST['SubsidioTransporte']) ? $_REQUEST['SubsidioTransporte'] : 0;
					$data['datos']['PeriodicidadPago']	= 10;
					$data['datos']['HorasMes']			= getHoursMonth();
					$data['datos']['DiasAno']			= 360;
					$data['datos']['MetodoRetencion']	= $MetodoRetencion;

					$rps = validateFields($data['datos']);
					$data['mensajeError'] .= $rps;
					break;
			}

			return($data);
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
			$_SESSION['Lista'] = SERVERURL . '/contratos/lista/1';

			if (isset($_REQUEST['Action']))
			{
				switch ($_REQUEST['Action'])
				{
					case 'CONDICIONES':
						$data = $this->validarDatos($Id, 2);

						if (empty($data['mensajeError'])) {
							$this->actualizarCondiciones($data, $Id);
							$data = $this->cargarDatos($Id);
						}

						break;

					case 'DOCUMENTOS':
						$data = $this->validarDatos($Id, 1);

						$this->actualizarDocumentos($data, $Id);
						$data = $this->cargarDatos($Id);

						break;

					case 'CONTRATOS':
						$data = $this->cargarDatos($Id);

						$this->enviarContratos($data, $Id);

						break;
					case 'CANCELAR':
						$data = $this->cargarDatos($Id);
						$this->cancelarDocumentos($data, $Id);
						break;

					case 'FINALIZAR':
						$this->finalizar($Id);
						break;

					case 'SELECCION':
						$this->aSeleccion($Id);
						break;
					case 'PREVIEW':
						$data = $this->cargarDatos($Id);
						$this->previewDocument($Id);
						break;
					default:
						if(strrpos($_REQUEST['Action'], "FIRMAR;") !== FALSE){
							$this->firmaDigital($Id,explode(';', $_REQUEST['Action'])[1]);
						}else
						{
							$data = $this->cargarDatos($Id);
						}
						

				}

				$this->views->getView($this, 'actualizar', $data);
				exit;
			}
			else 
			{
				$data = $this->cargarDatos($Id);
				$data['mensajeError'] = '';
				$this->views->getView($this, 'actualizar', $data);
			}
		}


		public function cancelarDocumentos($data,$id){

			$rps = cancelardocumentos($id);

			$data['mensajeError'] .= $rps;
			$this->views->getView($this, 'actualizar', $data);
			exit();	

		}

		public function firmaDigital($id,$pathDoc){
			$data = $this->cargarDatos($id);
			$urldoc = $pathDoc;
			$arrdest = explode('/', $pathDoc);
			$pathinfo = pathinfo($pathDoc);
			$namepdf = $pathinfo['filename'];
			$centinela = false;
			$path = 'documents/'.$arrdest[1] . '/DOCUMENTOS_FIRMADOS';

			if(!is_dir($path)){
				mkdir($path);
			}else{
				$dir = $_SERVER['DOCUMENT_ROOT'].'/Nomina/'. $path;
				$files = scandir($dir);
				for($i = 0; $i < count($files) && $centinela == false; $i ++){
					if(strrpos($files[$i], $namepdf) !== FALSE){
						$centinela = true;
					}
				}
			}

			if($centinela){
				echo '<script>window.open("'.SERVERURL.'/'.$path.'/'.$namepdf.'_FIRMADO.pdf", "_blank");</script>';
			}else{
				$rps = exec('.\main.exe "'.$pathDoc.'" "'.$path.'/'.$namepdf.'_FIRMADO.pdf" "documents/COMWARE_CERITICADO.pfx" "'.PASSWORD_SIGNATURE.'"');
				if(strpos($rps, 'Documento firmado y generado') !== FALSE){
					echo '<script>window.open("'.SERVERURL.'/'.$path.'/'.$namepdf.'_FIRMADO.pdf", "_blank");</script>';
				}else{
					$data['mensajeError'] = 'Error al firmar el documento por favor comunicate con el area encargada';
				}

				logRequests("CONTRATOS","",'.\main.exe "'.$pathDoc.'" "'.$path.'/'.$namepdf.'_FIRMADO.pdf" "documents/COMWARE_CERITICADO.pfx" "'.PASSWORD_SIGNATURE.'"', 
					iconv('','UTF-8',$rps), 
					"FIRMA REPRESENTANTE LEGAL");

			}

			$this->views->getView($this, 'actualizar', $data);
			exit();


		}

		public function previewDocument($Id){
			if(isset($_REQUEST['Ok'])){
				$req = $_REQUEST['Ok'][0];

				if(strrpos($req, "RECOMENDACIONES_MEDICAS.") !== FALSE){
					$data = $this->cargarDatos($Id);
					$data['preview_document'] = SERVERURL . '/' . $req;
					$this->views->getView($this, 'actualizar', $data);
					exit();
				}else{
					$idPlantilla = $req;
				
					$regDocumento = getRegistro('PLANTILLAS', $idPlantilla);
					$regEmpleado = getRegistro('EMPLEADOS', $Id);
					$cDirectorio = 'documents/' . $regEmpleado['documento'] . '_' . strtoupper($regEmpleado['apellido1'] . '_' . $regEmpleado['apellido2'] . '_' . $regEmpleado['nombre1'] . '_' . $regEmpleado['nombre2']);
					$nameFile = utf8_decode(cleanAccents($regDocumento['asunto']));
					
					

						$data = $this->cargarDatos($Id);
						$EstadoEmpleado = $data['reg']['Estado'];
						$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'DOCUMENTO'");
						$TipoContrato = $data['reg']['TipoContrato'];

						$query = <<<EOD
							PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND 
							PLANTILLAS.TipoPlantilla = $TipoPlantilla AND 
							PLANTILLAS.TipoContrato = $TipoContrato 
						EOD;

						$data['contratos'] = array();

						if ($regDocumento)
						{


							

							$Documento 	= $regEmpleado['documento'];
							$NombreEmpleado	= strtoupper(trim($regEmpleado['apellido1']) . ' ' . trim($regEmpleado['apellido2']) . ' ' . trim($regEmpleado['nombre1']) . ' ' . trim($regEmpleado['nombre2']));
							$Nombre 	= strtoupper(trim($regEmpleado['nombre1']) . ' ' . trim($regEmpleado['nombre2']));
							$Email 		= $regEmpleado['email'];
							$Celular 	= $regEmpleado['celular'];
							// GENERACION DOCUMENTO EN PDF
							global $Asunto;
							global $CodigoDocumento;
							
							$Asunto 			= utf8_decode(cleanAccents($regDocumento['asunto']));
							$Plantilla 			= $regDocumento['plantilla'];
							$CodigoDocumento 	= utf8_decode($regDocumento['codigodocumento']);

							// $Plantilla = str_replace('<<Logotipo>>', LOGO, $Plantilla);
							$Plantilla = str_replace('<<NombreEmpleado>>', $NombreEmpleado, $Plantilla);
							$Plantilla = str_replace('<<DireccionEmpleado>>', $regEmpleado['direccion'], $Plantilla);
							$Plantilla = str_replace('<<TelefonoEmpleado>>', $regEmpleado['telefono'], $Plantilla);
							$Plantilla = str_replace('<<CelularEmpleado>>', $regEmpleado['celular'], $Plantilla);
							$Plantilla = str_replace('<<CorreoElectronico>>', $Email, $Plantilla);


							$regEmpleado['idciudadnacimiento'] != "" && $regEmpleado['idciudadnacimiento'] != "0" ? $ciudadnacimiento = getRegistro('CIUDADES', $regEmpleado['idciudadnacimiento']) : $ciudadnacimiento = FALSE;
							$ciudadnacimiento == FALSE ? $Plantilla = str_replace('<<CiudadNacimiento>>', 'SIN CIUDAD DE NACIMIENTO', $Plantilla) : $Plantilla = str_replace('<<CiudadNacimiento>>', $ciudadnacimiento['nombre'], $Plantilla);


							$Plantilla = str_replace('<<SalarioEtapaLectiva>>', $regEmpleado['sueldobasico'], $Plantilla);
							$Plantilla = str_replace('<<SalarioEtapaProductiva>>', $regEmpleado['salariopractica'], $Plantilla);
							$Plantilla = str_replace('<<FechaNacimiento>>', $regEmpleado['fechanacimiento'], $Plantilla);


							$Plantilla = str_replace('<<TerminacionContrato>>', $regEmpleado['fechavencimiento'], $Plantilla);
							$Plantilla = str_replace('<<EmailEmpleado>>', $regEmpleado['email'], $Plantilla);
							$Plantilla = str_replace('<<FechaIngreso>>', $regEmpleado['fechaingreso'], $Plantilla);
							$Plantilla = str_replace('<<InicioEtapaProductiva>>', $regEmpleado['fechainicioetapaproductiva'], $Plantilla);
							if(isset($regEmpleado['fechafinetapalectiva'])){
								$Plantilla = str_replace('<<FinEtapaLectiva>>', $regEmpleado['fechafinetapalectiva'], $Plantilla);
							}
							
							

							if(isset($regEmpleado['observaciones'])){
								$Plantilla = str_replace('<<ValorBonificacion>>', $regEmpleado['observaciones'], $Plantilla);
							}
							
							$Plantilla = str_replace('<<FechaFinalizacion>>', $regEmpleado['fechavencimiento'], $Plantilla);
							$Plantilla = str_replace('<<InstitucionDeFormacion>>', $regEmpleado['instituciondeformacion'], $Plantilla);
							
							
							$Plantilla = str_replace('<<EspecialidadAprendiz>>', $regEmpleado['especialidadaprendiz'], $Plantilla);

							$regEmpleado['ideps'] != "" && $regEmpleado['ideps'] != "0" ? $eps = getRegistro('TERCEROS', $regEmpleado['ideps']) : $eps = FALSE;
							$eps == FALSE ? $Plantilla = str_replace('<<NombreEps>>', 'SIN EPS', $Plantilla) : $Plantilla = str_replace('<<NombreEps>>', $eps['nombre'], $Plantilla);


							$regEmpleado['idarl'] != "" && $regEmpleado['idarl'] != "0" ? $arl = getRegistro('TERCEROS', $regEmpleado['idarl']) : $arl = FALSE;
							$arl == FALSE ? $Plantilla = str_replace('<<NombreArl>>', 'SIN ARL', $Plantilla) : $Plantilla = str_replace('<<NombreArl>>', $arl['nombre'], $Plantilla);
							
							$regEmpleado['idcargo'] != "" && $regEmpleado['idcargo'] != "0" ? $Cargo = getRegistro('CARGOS', $regEmpleado['idcargo']) : $Cargo = FALSE;
							$Cargo == FALSE ? $Plantilla = str_replace('<<CargoEmpleado>>', "SIN CARGO", $Plantilla) : $Plantilla = str_replace('<<CargoEmpleado>>', $Cargo["nombre"], $Plantilla);
							
							$regEmpleado['idciudad'] != "" && $regEmpleado['idciudad'] != "0" ? $ciudad = getRegistro('CIUDADES', $regEmpleado['idciudad']) : $ciudad = FALSE;
							$ciudad == FALSE ? $Plantilla = str_replace('<<CiudadResidencia>>', "SIN CIUDAD", $Plantilla) : $Plantilla = str_replace('<<CiudadResidencia>>', $ciudad['nombre'], $Plantilla);
							
							$regEmpleado['idciudadexpedicion'] != "" && $regEmpleado['idciudadexpedicion'] != "0" ? $ciudadexpedicion = getRegistro('CIUDADES', $regEmpleado['idciudadexpedicion']) : $ciudadexpedicion = FALSE;
							$ciudadexpedicion == FALSE ? $Plantilla = str_replace('<<CiudadExpedicion>>', "SIN CIUDAD DE EXPEDICION", $Plantilla) :$Plantilla = str_replace('<<CiudadExpedicion>>', $ciudadexpedicion['nombre'], $Plantilla);
							
							$regEmpleado['idciudadtrabajo'] != "" && $regEmpleado['idciudadtrabajo'] != "0" ? $ciudadtrabajo = getRegistro('CIUDADES', $regEmpleado['idciudadtrabajo']) : $ciudadtrabajo = FALSE;
							$ciudadtrabajo == FALSE ? str_replace('<<CiudadTrabajo>>', "SIN CIUDAD DE TRABAJO", $Plantilla) : str_replace('<<CiudadTrabajo>>', $ciudadtrabajo['nombre'], $Plantilla);
							
							$Plantilla = str_replace('<<DocumentoIdentidad>>', $Documento, $Plantilla);
							$SueldoBasicoLetras = montoEscrito( $regEmpleado['sueldobasico']);
							$Plantilla = str_replace('<<SueldoBasicoLetras>>', $SueldoBasicoLetras, $Plantilla);
							$Plantilla = str_replace('<<SueldoBasico>>', number_format($regEmpleado['sueldobasico'], 0), $Plantilla);
							$Plantilla = str_replace('<<Dia>>', date('d'), $Plantilla);
							$NombreMes = NombreMes(date('m'));
							$Plantilla = str_replace('<<NombreMes>>', $NombreMes, $Plantilla);
							$Plantilla = str_replace('<<Año>>', date('Y'), $Plantilla);
							$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);

							

							if(isset($regEmpleado['duracioncontrato'])){
								$Plantilla = str_replace('<<DuracionContrato>>', $regEmpleado['duracioncontrato'], $Plantilla);
							}

							$Plantilla = utf8_decode($Plantilla);

							$pdf = new PDF2();
							
							$pdf->AliasNbPages();
							$pdf->SetAutoPageBreak(true, 25);
							$pdf->SetLeftMargin(25);
								
							$pdf->AddPage();
							$pdf->SetFont('Arial','',10);

							// $pdf->WriteHTML($Plantilla);
							$pdf->MultiCell(170, 5, strtoupper($Plantilla), 0, 'J', FALSE);
							
							if	( ! is_dir($cDirectorio) )
								mkdir($cDirectorio);

							$cDirectorio .= '/CONTRATOS';

							if	( ! is_dir($cDirectorio) )
								mkdir($cDirectorio);

							$pdf->Output('F', $cDirectorio . '/' . $regEmpleado['documento'] . '_' . strtoupper($Asunto) . '.PDF', TRUE); 
							


							$data = $this->cargarDatos($Id);
							$data['preview_document'] = SERVERURL . '/' .$cDirectorio . '/' . $regEmpleado['documento'] . '_' . strtoupper($Asunto) . '.PDF';
							$this->views->getView($this, 'actualizar', $data);
							exit();
						}
					
				}
				
				
			}
		}

		public function actualizarCondiciones($data, $Id)
		{
			$set = "";
			$index = 0;
			foreach($data['datos'] as $key => $val) {
							
				if($key == "FechaActualizacion"){
					$set .= ",FechaActualizacion = ".getDate();
				}else{
					$set .= $index == 0 ? $key." = '".$val."'" : ",".$key." = '".$val."'";		
				}
				$index++;
			}

			$set .= ", cnt_condicioneslaborales = '1'";

			$query = <<<EOD
				UPDATE EMPLEADOS 
					SET $set WHERE EMPLEADOS.Id = $Id;
			EOD;

			$this->model->query($query);

			// $resp = $this->model->actualizar($query, $data['datos']);
		}

		public function actualizarDocumentos($data, $Id)
		{
			$query = <<<EOD
				UPDATE EMPLEADOS 
					SET 
						IdEPS 				= :IdEPS, 
						IdFondoCesantias 	= :IdFondoCesantias, 
						IdFondoPensiones 	= :IdFondoPensiones, 
						IdCajaCompensacion 	= :IdCajaCompensacion, 
						IdARL 				= :IdARL, 
						NivelRiesgo			= :NivelRiesgo, 
						RegimenCesantias	= :RegimenCesantias, 
						FormaDePago			= :FormaDePago, 
						IdBanco				= :IdBanco, 
						TipoCuentaBancaria	= :TipoCuentaBancaria, 
						CuentaBancaria		= :CuentaBancaria,
						CuentaBancaria2		= :CuentaBancaria2,
						cnt_documentosactualizados = 1
					WHERE EMPLEADOS.Id = $Id;
			EOD;

			$this->model->actualizar($query, $data['datos']);

			// CARGA DE ARCHIVOS DE CONTRATACION
			$cDirectorio = 'documents/' . trim($data['reg']['Documento']) . '_' . strtoupper(trim($data['reg']['Apellido1']) . '_' . trim($data['reg']['Apellido2']) . '_' . trim($data['reg']['Nombre1']) . '_' . trim($data['reg']['Nombre2']));

			if	( ! is_dir($cDirectorio) )
				mkdir($cDirectorio);

			if	( ! is_dir($cDirectorio . '/SEGURIDAD_SOCIAL') )
				mkdir($cDirectorio . '/SEGURIDAD_SOCIAL');

			if	( ! empty($_FILES['AfiliacionEPS']['name']) )
			{
				$cArchivoDestino = $cDirectorio . '/SEGURIDAD_SOCIAL/' . trim($data['reg']['Documento']) . '_CERTIFICADO_EPS.' . pathinfo($_FILES['AfiliacionEPS']['name'], PATHINFO_EXTENSION);
				move_uploaded_file($_FILES['AfiliacionEPS']['tmp_name'], $cArchivoDestino);
			}

			if	( ! empty($_FILES['AfiliacionFC']['name']) )
			{
				$cArchivoDestino = $cDirectorio . '/SEGURIDAD_SOCIAL/' . trim($data['reg']['Documento']) . '_CERTIFICADO_FONDO_CESANTIAS.' . pathinfo($_FILES['AfiliacionFC']['name'], PATHINFO_EXTENSION);
				move_uploaded_file($_FILES['AfiliacionFC']['tmp_name'], $cArchivoDestino);
			}

			if	( ! empty($_FILES['AfiliacionFP']['name']) )
			{
				$cArchivoDestino = $cDirectorio . '/SEGURIDAD_SOCIAL/' . trim($data['reg']['Documento']) . '_CERTIFICADO_FONDO_PENSIONES.' . pathinfo($_FILES['AfiliacionFP']['name'], PATHINFO_EXTENSION);
				move_uploaded_file($_FILES['AfiliacionFP']['tmp_name'], $cArchivoDestino);
			}

			if	( ! empty($_FILES['AfiliacionCCF']['name']) )
			{
				$cArchivoDestino = $cDirectorio . '/SEGURIDAD_SOCIAL/' . trim($data['reg']['Documento']) . '_CERTIFICADO_CAJA_COMPENSACION.' . pathinfo($_FILES['AfiliacionCCF']['name'], PATHINFO_EXTENSION);
				move_uploaded_file($_FILES['AfiliacionCCF']['tmp_name'], $cArchivoDestino);
			}

			if	( ! empty($_FILES['AfiliacionARL']['name']) )
			{
				$cArchivoDestino = $cDirectorio . '/SEGURIDAD_SOCIAL/' . trim($data['reg']['Documento']) . '_CERTIFICADO_ARL.' . pathinfo($_FILES['AfiliacionARL']['name'], PATHINFO_EXTENSION);
				move_uploaded_file($_FILES['AfiliacionARL']['tmp_name'], $cArchivoDestino);
			}

			if	( ! is_dir($cDirectorio . '/HV') )
				mkdir($cDirectorio . '/HV');

			if	( ! empty($_FILES['CertificacionBancaria']['name']) )
			{
				$cArchivoDestino = $cDirectorio . '/HV/' . trim($data['reg']['Documento']) . '_CERTIFICACION_BANCARIA.' . pathinfo($_FILES['CertificacionBancaria']['name'], PATHINFO_EXTENSION);
				move_uploaded_file($_FILES['CertificacionBancaria']['tmp_name'], $cArchivoDestino);
			}

			if (empty($data['mensajeError']))
			{
				$query = <<<EOD
					UPDATE EMPLEADOS
						SET
							CNT_DocumentosActualizados = 1 
						WHERE EMPLEADOS.Id = $Id;
				EOD;

				$this->model->query($query);
			}
		}

		public function enviarContratos($data, $Id)
		{
			if (isset($_REQUEST['Ok']))
			{

				$regEmpleado = getRegistro('EMPLEADOS', $Id);

				$Documento 	= $regEmpleado['documento'];
				$NombreEmpleado	= strtoupper(trim($regEmpleado['apellido1']) . ' ' . trim($regEmpleado['apellido2']) . ' ' . trim($regEmpleado['nombre1']) . ' ' . trim($regEmpleado['nombre2']));
				$Nombre 	= strtoupper(trim($regEmpleado['nombre1']) . ' ' . trim($regEmpleado['nombre2']));
				$Email 		= $regEmpleado['email'];
				$Celular 	= $regEmpleado['celular'];
				$centAprendiz = false;

				if($regEmpleado['tipocontrato'] != '144' AND
				$regEmpleado['tipocontrato'] != '428' AND
				$regEmpleado['tipocontrato'] != '429'){
					$centAprendiz = true;
				}


				$valid = validateDocumentFirmPLus($regEmpleado['solicitudfirma'], $Id);

				if($valid){				

					if (($regEmpleado['cnt_documentosactualizados'] OR $centAprendiz == false) AND  $regEmpleado['cnt_condicioneslaborales'])
					{
						// ENVIO DE CONTRATOS PARA FIRMA ELECTRONICA
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

						for ($i = 0; $i < count($_REQUEST['Ok']); $i++)
						{
							$req = $_REQUEST['Ok'][$i];

							if(strrpos($req, "RECOMENDACIONES_MEDICAS.") !== FALSE){
								
								$Archivo  = utf8_decode("RECOMENDACIONES MEDICAS");
								$archivo1 = base64_encode(file_get_contents($req));

								if (empty($archivos_base64))
								{
									$archivos_base64 = <<<EOD
										"ArchivosPDF": [
											{
												"Nombre": "$Archivo", 
												"Documento_base64": "$archivo1"
											}
									EOD;
								}
								else
								{
									$archivos_base64 .= <<<EOD
											,
											{
												"Nombre": "$Archivo", 
												"Documento_base64": "$archivo1"
											}
									EOD;
								}

							}else{
								$regDocumento = getRegistro('PLANTILLAS', $_REQUEST['Ok'][$i]);

								if ($regDocumento)
								{
									// GENERACION DOCUMENTO EN PDF
									global $Asunto;
									global $CodigoDocumento;
		
									$Asunto 			= utf8_decode(cleanAccents($regDocumento['asunto']));
									$Plantilla 			= $regDocumento['plantilla'];
									$CodigoDocumento 	= utf8_decode($regDocumento['codigodocumento']);
		
									// $Plantilla = str_replace('<<Logotipo>>', LOGO, $Plantilla);
									$Plantilla = str_replace('<<NombreEmpleado>>', $NombreEmpleado, $Plantilla);
									$Plantilla = str_replace('<<DireccionEmpleado>>', $regEmpleado['direccion'], $Plantilla);
									$Plantilla = str_replace('<<TelefonoEmpleado>>', $regEmpleado['telefono'], $Plantilla);
									$Plantilla = str_replace('<<CelularEmpleado>>', $regEmpleado['celular'], $Plantilla);
									$Plantilla = str_replace('<<CorreoElectronico>>', $Email, $Plantilla);
									$reg = getRegistro('CIUDADES', $regEmpleado['idciudadnacimiento']);
									$Plantilla = str_replace('<<CiudadNacimiento>>', $reg['nombre'], $Plantilla);
									$Plantilla = str_replace('<<FechaNacimiento>>', $regEmpleado['fechanacimiento'], $Plantilla);
		
		
									$Plantilla = str_replace('<<TerminacionContrato>>', $regEmpleado['fechavencimiento'], $Plantilla);
									$Plantilla = str_replace('<<EmailEmpleado>>', $regEmpleado['email'], $Plantilla);
									$Plantilla = str_replace('<<FechaIngreso>>', $regEmpleado['fechaingreso'], $Plantilla);
									$Plantilla = str_replace('<<InicioEtapaProductiva>>', $regEmpleado['fechainicioetapaproductiva'], $Plantilla);
									
									if(isset($regEmpleado['fechafinetapalectiva'])){
										$Plantilla = str_replace('<<FinEtapaLectiva>>', $regEmpleado['fechafinetapalectiva'], $Plantilla);
									}
									

								$Plantilla = str_replace('<<FechaFinalizacion>>', $regEmpleado['fechavencimiento'], $Plantilla);
								$Plantilla = str_replace('<<InstitucionDeFormacion>>', $regEmpleado['instituciondeformacion'], $Plantilla);
								$Plantilla = str_replace('<<ValorBonificacion>>', $regEmpleado['observaciones'], $Plantilla);
	
								$Plantilla = str_replace('<<EspecialidadAprendiz>>', $regEmpleado['especialidadaprendiz'], $Plantilla);
								$eps = getRegistro('TERCEROS', $regEmpleado['ideps']);
								$Plantilla = str_replace('<<NombreEps>>', $eps['nombre'], $Plantilla);
	
								if(empty($regEmpleado['idarl'])){
									$Plantilla = str_replace('<<NombreArl>>', "SEGUROS BOLIBAR", $Plantilla);
								}else{
									$arl = getRegistro('TERCEROS', $regEmpleado['idarl']);
									$Plantilla = str_replace('<<NombreArl>>', $arl['nombre'], $Plantilla);
								}
	
								
								$Plantilla = str_replace('<<SalarioEtapaLectiva>>', $regEmpleado['sueldobasico'], $Plantilla);
								$Plantilla = str_replace('<<SalarioEtapaProductiva>>', $regEmpleado['salariopractica'], $Plantilla);
							
	
	
	
								$reg = getRegistro('CIUDADES', $regEmpleado['idciudad']);
								$Plantilla = str_replace('<<CiudadResidencia>>', $reg['nombre'], $Plantilla);
								$Plantilla = str_replace('<<DocumentoIdentidad>>', $Documento, $Plantilla);
								$reg = getRegistro('CIUDADES', $regEmpleado['idciudadexpedicion']);
								$Plantilla = str_replace('<<CiudadExpedicion>>', $reg['nombre'], $Plantilla);
								$reg = getRegistro('CARGOS', $regEmpleado['idcargo']);
								$Plantilla = str_replace('<<CargoEmpleado>>', $reg['nombre'], $Plantilla);
								$SueldoBasicoLetras = montoEscrito( $regEmpleado['sueldobasico']);
								$Plantilla = str_replace('<<SueldoBasicoLetras>>', $SueldoBasicoLetras, $Plantilla);
								$Plantilla = str_replace('<<SueldoBasico>>', number_format($regEmpleado['sueldobasico'], 0), $Plantilla);
								$Plantilla = str_replace('<<Dia>>', date('d'), $Plantilla);
								$NombreMes = NombreMes(date('m'));
								$Plantilla = str_replace('<<NombreMes>>', $NombreMes, $Plantilla);
								$Plantilla = str_replace('<<Año>>', date('Y'), $Plantilla);
								$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);
	
								$reg = getRegistro('CIUDADES', $regEmpleado['idciudadtrabajo']);
								$Plantilla = str_replace('<<CiudadTrabajo>>', $reg['nombre'], $Plantilla);
	
								if(isset($regEmpleado['duracioncontrato'])){
									$Plantilla = str_replace('<<DuracionContrato>>', $regEmpleado['duracioncontrato'], $Plantilla);
								}
								
	
								$Plantilla = utf8_decode($Plantilla);
	
								$pdf = new PDF2();
								
								$pdf->AliasNbPages();
								$pdf->SetAutoPageBreak(true, 25);
								$pdf->SetLeftMargin(25);
									
								$pdf->AddPage();
								$pdf->SetFont('Arial','',10);
	
								// $pdf->WriteHTML($Plantilla);
								$pdf->MultiCell(170, 5, strtoupper($Plantilla), 0, 'J', FALSE);
								
								$cDirectorio = 'documents/' . $regEmpleado['documento'] . '_' . strtoupper($regEmpleado['apellido1'] . '_' . $regEmpleado['apellido2'] . '_' . $regEmpleado['nombre1'] . '_' . $regEmpleado['nombre2']);
	
								if	( ! is_dir($cDirectorio) )
									mkdir($cDirectorio);
	
								$cDirectorio .= '/CONTRATOS';
	
								if	( ! is_dir($cDirectorio) )
									mkdir($cDirectorio);
	
								$pdf->Output('F', $cDirectorio . '/' . $regEmpleado['documento'] . '_' . strtoupper($Asunto) . '.PDF', TRUE); 
	
								// SE PREPARA EL ARCHIVO PARA FIRMA DIGITAL
								$Archivo  = utf8_decode($Asunto);
								$archivo1 = base64_encode(file_get_contents($cDirectorio . '/' . $regEmpleado['documento'] . '_' . strtoupper($Asunto) . '.PDF'));
	
								unlink($cDirectorio . '/' . $regEmpleado['documento'] . '_' . strtoupper($Asunto) . '.PDF');
	
								if (empty($archivos_base64))
								{
									$archivos_base64 = <<<EOD
										"ArchivosPDF": [
											{
												"Nombre": "$Archivo", 
												"Documento_base64": "$archivo1"
											}
									EOD;
								}
								else
								{
									$archivos_base64 .= <<<EOD
											,
											{
												"Nombre": "$Archivo", 
												"Documento_base64": "$archivo1"
											}
									EOD;
								}
							}
						}
						
					}

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
						logRequests("CONTRATOS","",json_encode(curl_getinfo($curl)), json_encode($response), "FIRMA PLUS");
						curl_close($curl);

						if ($response['Code'] == 1)
						{
							$SolicitudFirma = $response['Data']['NroSolicitud'];
							$FechaSolicitud = $response['Data']['Fecha'];
							
							$query = <<<EOD
								UPDATE EMPLEADOS 
									SET CNT_ContratosEnviados = 1, 
										SolicitudFirma = $SolicitudFirma, 
										FechaSolicitud = '$FechaSolicitud' 
									WHERE EMPLEADOS.Id = $Id;
							EOD;

							$this->model->query($query);
						}
						else
						{
							$CodigoError = $response['Code'];
							$MensajeError = $response['Message'];

							$mensajeError = <<<EOD
								Error al tratar de enviar CONTRATOS para firma electrónica <br>
								Código de error: $CodigoError<br>
								Mensaje: $MensajeError<br>
							EOD;
						}
					}else{
						$mensajeError = "Candidato debe tener DOCUMENTOS ACTUALIZADOS y CONDICIONES LABORALES completas para poder enviar CONTRATOS<br>";
					}

				}else{
					$mensajeError = "El candidato tiene DOCUMENTOS por firmar <br>";
				}

				if (empty($mensajeError))
				{
					header('Location: ' . SERVERURL . '/contratos/lista/1');
					exit();
				}
				else
				{
					$data['mensajeError'] = $mensajeError;
					$this->views->getView($this, 'actualizar', $data);
				}
			}
		}

		public function finalizar($Id)
		{
			$regEmpleado = getRegistro('EMPLEADOS', $Id);
			$Nombre = strtoupper(trim($regEmpleado['apellido1']) . ' ' . trim($regEmpleado['apellido2']) . ' ' . trim($regEmpleado['nombre1']) . ' ' . trim($regEmpleado['nombre2']));

			$Cargo = getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];

			if ($regEmpleado['idproyecto'] > 0)
			{
				$regCentro = getRegistro('CENTROS', $regEmpleado['idproyecto']);
				$Centro = $regCentro['nombre'];
			}
			else
			{
				$regCentro = getRegistro('CENTROS', $regEmpleado['idcentro']);
				$Centro = $regCentro['nombre'];
			}

			// ENVIO DE CORREO
			$EstadoEmpleado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'EN PROCESO DE CONTRATACION' ");
			$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'CORREO ELECTRÓNICO'");
				
			$query = <<<EOD
				SELECT PLANTILLAS.* 
					FROM PLANTILLAS 
					WHERE PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND 
						PLANTILLAS.TipoPlantilla = $TipoPlantilla AND 
						PLANTILLAS.Asunto = 'INGRESO DE CANDIDATO';
			EOD;

			$reg = $this->model->leer($query);

			if ($reg) 
			{
				$Asunto = utf8_decode($reg['asunto']);
				$Plantilla = $reg['plantilla'];

				$Plantilla = str_replace('<<Logotipo>>', LOGOTIPO, $Plantilla);
				$Plantilla = str_replace('<<EnlacePortal>>', SERVERURL, $Plantilla);
				$Plantilla = str_replace('<<Email>>', $regEmpleado['email'], $Plantilla);
				$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);

				$Plantilla = str_replace('<<NombreEmpleado>>', $Nombre, $Plantilla);
				$Plantilla = str_replace('<<DocumentoIdentidad>>', $regEmpleado['documento'], $Plantilla);
				$Plantilla = str_replace('<<CelularEmpleado>>', $regEmpleado['celular'], $Plantilla);
				$Plantilla = str_replace('<<CorreoElectronico>>', $regEmpleado['email'], $Plantilla);
				$Plantilla = str_replace('<<CargoEmpleado>>', $Cargo, $Plantilla);
				$Plantilla = str_replace('<<Centro>>', $Centro, $Plantilla);
				$Plantilla = str_replace('<<FechaIngreso>>', $regEmpleado['fechaingreso'], $Plantilla);

				if (empty($regEmpleado['fechavencimiento']) || $regEmpleado['fechavencimiento'] == '1900-01-01')
					$Plantilla = str_replace('<<FechaVencimiento>>', 'INDEFINIDO', $Plantilla);
				else
					$Plantilla = str_replace('<<FechaVencimiento>>', $regEmpleado['fechavencimiento'], $Plantilla);

				
				$day = explode("-",$regEmpleado['fechaingreso'])[2];

				$Plantilla = str_replace('<<Dia>>', $day, $Plantilla);
				$NombreMes = NombreMes(substr($regEmpleado['fechaingreso'], 5, 2));
				$Plantilla = str_replace('<<NombreMes>>', $NombreMes, $Plantilla);
				$Plantilla = str_replace('<<Año>>', substr($regEmpleado['fechaingreso'], 0, 4), $Plantilla);
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
				$fromName = 'CONTRATACION COMWARE';

				$mail->Subject = $Asunto;
				$mail->addEmbeddedImage(LOGOTIPO, 'comware');
				$mail->Body = $Plantilla;
				
				$aMails = explode(';', $_REQUEST['EmailAdicional']);

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
					logRequests("CONTRATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $mail->to);
				} 
				catch (Exception $e) 
				{
					logRequests("CONTRATOS",$Plantilla,json_encode($obj), $mail->ErrorInfo, "ENVIO DE EMAIL", "", $_REQUEST['EmailAdicional']);
					$data['mensajeError'] .= "Error al enviar correo a ". $_REQUEST['EmailAdicional'] ." <br>";
					$mail->getSMTPInstance()->reset();
				}

				$mail->clearAddresses();
				$mail->clearAttachments();
			}

			// BUSCAMOS EL ESTADO DEL EMPLEADO
			$Estado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ACTIVO'");

			//ACTUALIZACIÓN ESTADO EMPLEADO
			$query = <<<EOD
				UPDATE EMPLEADOS
					SET
						Estado = $Estado 
					WHERE EMPLEADOS.Id = $Id;
			EOD;

			$this->model->query($query);

			header('Location: ' . SERVERURL . '/contratos/lista/1');
			
			exit();
		}

		public function aSeleccion($Id)
		{
			$mensajeError = '';
			
			$regEmpleado = getRegistro('EMPLEADOS', $Id);

			$IdEmpleado 	= $regEmpleado['id'];
			$Documento 		= $regEmpleado['documento'];
			$Apellido1 		= $regEmpleado['apellido1'];
			$Apellido2		= $regEmpleado['apellido2'];
			$Nombre1		= $regEmpleado['nombre1'];
			$Nombre2		= $regEmpleado['nombre2'];

			$Nombre			= strtoupper(trim($Apellido1) . ' ' . trim($Apellido2) . ' ' . trim($Nombre1) . ' ' . trim($Nombre2));

			if(!isset($regEmpleado['idcargo']) && $regEmpleado['idcargo'] == '0'){
				$mensajeError .= 'no tiene un cargo definido ';
			}else{
				$Cargo= getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];

				if ($regEmpleado['idproyecto'] == 0)
					$Centro = getRegistro('CENTROS', $regEmpleado['idcentro'])['nombre'];
				else
					$Centro = getRegistro('CENTROS', $regEmpleado['idproyecto'])['nombre'];
	
				// ENVIO DE CORREO
				if (isset($_REQUEST['Justificacion']))
					$Justificacion = $_REQUEST['Justificacion'];
				else
					$Justificacion = '';
	
				// ENVIAMOS CORREO A SELECCION
				$EstadoEmpleado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'EN PROCESO DE CONTRATACION' ");
				$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'CORREO ELECTRÓNICO'");
					
				$query = <<<EOD
					SELECT PLANTILLAS.* 
						FROM PLANTILLAS 
						WHERE PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND 
							PLANTILLAS.TipoPlantilla = $TipoPlantilla AND 
							PLANTILLAS.Asunto = 'DEVOLUCION A SELECCION';
				EOD;
	
				$reg = $this->model->leer($query);			

				if ($reg) 
				{
					$Asunto = utf8_decode($reg['asunto']);
					$Plantilla = $reg['plantilla'];

					$Plantilla = str_replace('<<Logotipo>>', LOGOTIPO, $Plantilla);
					$Plantilla = str_replace('<<EnlacePortal>>', SERVERURL, $Plantilla);
					$Plantilla = str_replace('<<Email>>', $regEmpleado['email'], $Plantilla);
					$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);
					$Plantilla = str_replace('<<Justificacion>>', $Justificacion, $Plantilla);

					$Plantilla = str_replace('<<NombreEmpleado>>', $Nombre, $Plantilla);
					$Plantilla = str_replace('<<DocumentoIdentidad>>', $regEmpleado['documento'], $Plantilla);
					$Plantilla = str_replace('<<CelularEmpleado>>', $regEmpleado['celular'], $Plantilla);
					$Plantilla = str_replace('<<CorreoElectronico>>', $regEmpleado['email'], $Plantilla);
					$Plantilla = str_replace('<<CargoEmpleado>>', $Cargo, $Plantilla);
					$Plantilla = str_replace('<<Centro>>', $Centro, $Plantilla);
					$Plantilla = str_replace('<<FechaIngreso>>', $regEmpleado['fechaingreso'], $Plantilla);

					if (empty($regEmpleado['fechavencimiento']))
						$Plantilla = str_replace('<<FechaVencimiento>>', 'INDEFINIDO', $Plantilla);
					else
						$Plantilla = str_replace('<<FechaVencimiento>>', $regEmpleado['fechavencimiento'], $Plantilla);

					$Plantilla = str_replace('<<Dia>>', substr($regEmpleado['fechaingreso'], 9, 2), $Plantilla);
					$NombreMes = NombreMes(substr($regEmpleado['fechaingreso'], 5, 2));
					$Plantilla = str_replace('<<NombreMes>>', $NombreMes, $Plantilla);
					$Plantilla = str_replace('<<Año>>', substr($regEmpleado['fechaingreso'], 0, 4), $Plantilla);
					$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);

					$regContratante = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '" . $_SESSION['Login']['Documento'] . "'");

					if ($regContratante)
						$NombreContratante = strtoupper(trim($regContratante['nombre1']) . ' ' . trim($regContratante['nombre2']) . ' ' . trim($regContratante['apellido1']));
					else
						$NombreContratante = '';

					$Plantilla = str_replace('<<NombreContratante>>', $NombreContratante, $Plantilla);

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
					$fromName = 'CONTRATACIÓN DE PERSONAL - COMWARE';

					$mail->Subject = $Asunto;
					$mail->addEmbeddedImage(LOGOTIPO, 'comware');
					$mail->Body = $Plantilla;

					$EmailSeleccion = getRegistro('EMPLEADOS', $regEmpleado['idsicologo'])['email'];

					if (! empty($EmailSeleccion))
						$mail->AddAddress($EmailSeleccion);
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
						logRequests("CONTRATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $EmailSeleccion);
					} 
					catch (Exception $e) 
					{
						logRequests("CONTRATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $EmailSeleccion);
						$mensajeError .= "Error al enviar correo a $EmailSeleccion <br>";
						$mail->getSMTPInstance()->reset();
					}

					$mail->clearAddresses();
					$mail->clearAttachments();
				}
				else
				{
					$mensajeError .= "No existe una plantilla de correo para DEVOLUCION A SELECCION<br>";
				}	

			}

			// ACTUALIZAR LOG
			// BUSCAMOS EL ESTADO ACTUAL DEL EMPLEADO 
			$EstadoActual = getRegistro('PARAMETROS', $regEmpleado['estado'])['detalle'];
			$EstadoNuevo = 'EN PROCESO DE SELECCION';

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

			if (empty($mensajeError))
			{
				header('Location: ' . SERVERURL . '/contratos/lista/1');
				exit();
			}
			else
			{
				$data = $this->cargarDatos($Id);
				$data['mensajeError'] = $mensajeError;
				$this->views->getView($this, 'actualizar', $data);
			}

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
	}
?>

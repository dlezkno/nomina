<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Documentos extends Controllers
	{
		public function lista($pagina)
		{
			$data['mensajeError'] = '';
			
			if (isset($_REQUEST['Action']))
			{
				$regEmpleado = getRegistro('EMPLEADOS', $_REQUEST['Action']);

				$Documento 	= trim($regEmpleado['documento']);
				$Apellido1 	= strtoupper(trim($regEmpleado['apellido1']));
				$Apellido2	= strtoupper(trim($regEmpleado['apellido2']));
				$Nombre1	= strtoupper(trim($regEmpleado['nombre1']));
				$Nombre2	= strtoupper(trim($regEmpleado['nombre2']));
				$SolicitudFirma = $regEmpleado['solicitudfirma'];

				if(isset($SolicitudFirma)) {
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

				$cDirectorioO = trim($regEmpleado['documento']) . '_' . strtoupper(trim($regEmpleado['apellido1']) . '_' . trim($regEmpleado['apellido2']) . '_' . trim($regEmpleado['nombre1']) . '_' . trim($regEmpleado['nombre2']));

				$archivoZip ="documentos.rar";
				unlink($archivoZip);	
				$cDirectorioO = 'documents/' . $cDirectorioO;
				
				if (is_dir($cDirectorioO))
				{
					$zip = new ZipArchive();
					
					if ($zip->open($archivoZip, ZipArchive::CREATE) === TRUE) 
					{
						$archivos = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cDirectorioO));
					
						foreach ($archivos as $archivo) 
						{
							if (!$archivo->isDir()) 
							{
								$archivoEnZip = substr($archivo->getPathname(), strlen($cDirectorioO) + 1);
								$archivoEnZip = str_replace('CONTRATOS', 'DOC_LEGALES', $archivoEnZip);
								$archivoEnZip = str_replace('SICOTECNICAS', 'PSICOTECNICAS', $archivoEnZip);
								$arrayFileName = explode('\\', $archivoEnZip);
								if(isset($arrayFileName[1])){
									if (preg_match("/(EXAMEN_MEDICO|RECOMENDACIONES_MEDICAS)/", $arrayFileName[1])) {
										$archivoEnZip = str_replace($arrayFileName[0], 'SEGURIDAD_SOCIAL', $archivoEnZip);
									} else if (preg_match("/(AUTORIZACION PARA EL TRATAMIENTO DE DATOS PERSONALES)/", $arrayFileName[1])) {
										$archivoEnZip = str_replace($arrayFileName[0], 'DOC_LEGALES', $archivoEnZip);
									}
									$zip->addFile($archivo->getPathname(), $archivoEnZip);
								}
								
							}
						}

						$zip->close();
						$data["url_redirect"] = SERVERURL . "/" . $archivoZip;
						// header("Content-Type: application/zip");
						// header("Content-Disposition: attachment; filename=\"$archivoZip\"");
						// header("Content-Length: " . filesize($archivoZip));

						// readfile($archivoZip);
						
					}
					else 
						$data['mensajeError'] = "Error al crear el archivo ZIP.";
				}
				else
					$data['mensajeError'] = "Empleado no tiene documentos.";
			}

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

			$_SESSION['DOCUMENTOS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['DOCUMENTOS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['DOCUMENTOS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['DOCUMENTOS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['DOCUMENTOS']['Filtro']))
			{
				$_SESSION['DOCUMENTOS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['DOCUMENTOS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['DOCUMENTOS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['DOCUMENTOS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['DOCUMENTOS']['Orden'])) 
					$_SESSION['DOCUMENTOS']['Orden'] = 'PARAMETROS1.Detalle DESC,EMPLEADOS.FechaIngreso,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

			$query = "WHERE (PARAMETROS1.Detalle = 'ACTIVO' OR PARAMETROS1.Detalle = 'EN PROCESO DE SELECCION' OR PARAMETROS1.Detalle = 'EN PROCESO DE CONTRATACION') ";

			if	( ! empty($lcFiltro) )
			{
				$query .= "AND (UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(EMPLEADOS.CodigoSAP, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CARGOS.Cargo, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CENTROS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(PROYECTOS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(PROYECTOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%') ";
			}

			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['DOCUMENTOS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarEmpleados($query);
			$this->views->getView($this, 'documentos', $data);
		}	
	}
?>
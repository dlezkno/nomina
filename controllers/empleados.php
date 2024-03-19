<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Empleados extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = SERVERURL . '/empleados/importar';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/empleados/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['EMPLEADOS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['EMPLEADOS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['EMPLEADOS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['EMPLEADOS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['EMPLEADOS']['Filtro']))
			{
				$_SESSION['EMPLEADOS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['EMPLEADOS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['EMPLEADOS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['EMPLEADOS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['EMPLEADOS']['Orden'])) 
					$_SESSION['EMPLEADOS']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

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
			}

			if (empty($query))
				$query .= "WHERE PARAMETROS1.Detalle = 'ACTIVO' ";
			else
				$query .= ") AND PARAMETROS1.Detalle = 'ACTIVO' ";

			// 	switch ($estado)
			// 	{
			// 		case 'C':
			// 			$query = "WHERE PARAMETROS1.Detalle = 'EN PROCESO DE CONTRATACION' ";
			// 			break;
			// 		case 'R':
			// 			$query = "WHERE PARAMETROS1.Detalle = 'RETIRADO' ";
			// 			break;
			// 	}
			// }
			// else
			// {
			// 	switch ($estado)
			// 	{
			// 		case 'C':
			// 			$query = ") AND PARAMETROS1.Detalle = 'EN PROCESO DE CONTRATACION' ";
			// 			break;
			// 		case 'R':
			// 			$query = ") AND PARAMETROS1.Detalle = 'RETIRADO' ";
			// 			break;
			// 	}
			// }
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['EMPLEADOS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarEmpleados($query);
			$this->views->getView($this, 'empleados', $data);
		}	

		public function cargarDatos($id)
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/empleados/actualizar';
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
			$_SESSION['Lista'] = SERVERURL . '/empleados/lista/1';

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
					'Id' 					=> $reg['id'], 
					'TipoIdentificacion' 	=> $reg['tipoidentificacion'], 
					'Documento' 			=> $reg['documento'], 
					'Documento2' 			=> $reg['documento2'], 
					'CodigoSAP' 			=> $reg['codigosap'], 
					'FechaExpedicion' 		=> $reg['fechaexpedicion'], 
					'IdCiudadExpedicion' 	=> $reg['idciudadexpedicion'], 
					'Apellido1' 			=> str_replace(" ","",$reg['apellido1']), 
					'Apellido2' 			=> str_replace(" ","",$reg['apellido2']), 
					'Nombre1' 				=> str_replace(" ","",$reg['nombre1']), 
					'Nombre2' 				=> str_replace(" ","",$reg['nombre2']), 
					'Estado' 				=> $reg['estado'], 
					'FechaNacimiento' 		=> $reg['fechanacimiento'], 
					'IdCiudadNacimiento' 	=> $reg['idciudadnacimiento'], 
					'Genero' 				=> $reg['genero'], 
					'EstadoCivil' 			=> $reg['estadocivil'], 
					'FactorRH' 				=> $reg['factorrh'], 
					'Profesion' 			=> $reg['profesion'], 
					'Educacion' 			=> $reg['educacion'], 
					'LibretaMilitar' 		=> $reg['libretamilitar'], 
					'DistritoMilitar' 		=> $reg['distritomilitar'], 
					'LicenciaConduccion' 	=> $reg['licenciaconduccion'], 
					'TarjetaProfesional' 	=> $reg['tarjetaprofesional'], 
					'Direccion' 			=> $reg['direccion'], 
					'Barrio' 				=> $reg['barrio'], 
					'Localidad' 			=> $reg['localidad'], 
					'IdCiudad' 				=> $reg['idciudad'], 
					'Email' 				=> $reg['email'], 
					'EmailCorporativo' 		=> $reg['emailcorporativo'], 
					'EmailProyecto' 		=> $reg['emailproyecto'], 
					'Telefono' 				=> $reg['telefono'], 
					'Celular' 				=> $reg['celular'], 
					'IdCargo' 				=> $reg['idcargo'], 
					'PerfilProfesional' 	=> $reg['perfilprofesional'],
					'IdCentro' 				=> $reg['idcentro'],
					'IdProyecto' 			=> $reg['idproyecto'],
					'Vicepresidencia' 		=> $reg['vicepresidencia'],
					'IdSede' 				=> $reg['idsede'],
					'TipoContrato' 			=> $reg['tipocontrato'],
					'IdCategoria' 			=> $reg['idcategoria'],
					'IdCiudadTrabajo' 		=> $reg['idciudadtrabajo'],
					'FechaIngreso' 			=> $reg['fechaingreso'],
					'FechaPeriodoPrueba' 	=> $reg['fechaperiodoprueba'],
					'FechaVencimiento' 		=> $reg['fechavencimiento'],
					'Prorrogas' 			=> $reg['prorrogas'],
					'ModalidadTrabajo' 		=> $reg['modalidadtrabajo'],
					'SueldoBasico' 			=> $reg['sueldobasico'],
					'SubsidioTransporte' 	=> $reg['subsidiotransporte'],
					'PeriodicidadPago' 		=> $reg['periodicidadpago'],
					'HorasMes' 				=> $reg['horasmes'], 
					'DiasAno' 				=> $reg['diasano'], 
					'IdEPS' 				=> $reg['ideps'],
					'RegimenCesantias' 		=> $reg['regimencesantias'],
					'FactorPrestacional' 	=> $reg['factorprestacional'],
					'IdFondoCesantias' 		=> $reg['idfondocesantias'],
					'IdFondoPensiones' 		=> $reg['idfondopensiones'],
					'IdCajaCompensacion' 	=> $reg['idcajacompensacion'],
					'IdARL' 				=> $reg['idarl'], 
					'NivelRiesgo' 			=> $reg['nivelriesgo'], 
					'FormaDePago' 			=> $reg['formadepago'],
					'IdBanco' 				=> $reg['idbanco'],
					'TipoCuentaBancaria' 	=> $reg['tipocuentabancaria'],
					'CuentaBancaria' 		=> $reg['cuentabancaria'],
					'CuentaBancaria2' 		=> $reg['cuentabancaria2'],
					'MetodoRetencion' 		=> $reg['metodoretencion'],
					'PorcentajeRetencion' 	=> $reg['porcentajeretencion'],
					'MayorRetencionFuente' 	=> $reg['mayorretencionfuente'],
					'DeduccionDependientes' => $reg['deducciondependientes'],
					'CuotaVivienda' 		=> $reg['cuotavivienda'],
					'SaludYEducacion' 		=> $reg['saludyeducacion'],
					'Observaciones' 		=> $reg['observaciones'], 
					'GrupoPoblacional'		=> $reg['grupopoblacional'], 
					'PoliticamenteExpuesta' => ($reg['politicamenteexpuesta'] ? true : false)
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

				// AUDITORIA
				$query = <<<EOD
					SELECT USUARIOS.Nombre AS NombreUsuario, 
							LOGEMPLEADOS.FechaCreacion AS Fecha, 
							LOGEMPLEADOS.Campo, 
							LOGEMPLEADOS.ValorAnterior, 
							LOGEMPLEADOS.ValorActual  
						FROM LOGEMPLEADOS  
							INNER JOIN USUARIOS 
								ON LOGEMPLEADOS.IdUsuario = USUARIOS.Id 
						WHERE LOGEMPLEADOS.IdEmpleado = $IdEmpleado 
						ORDER BY LOGEMPLEADOS.FechaCreacion;
				EOD;
	
				$dataAud = $this->model->listar($query);
	
				if ($dataAud)
				{
					for ($i = 0; $i < count($dataAud); $i++) 
					{ 
						$data['regAud'][$i] = array(
							'NombreUsuario' => $dataAud[$i]['NombreUsuario'],
							'Fecha' => $dataAud[$i]['Fecha'],
							'Campo' => $dataAud[$i]['Campo'],
							'ValorAnterior' => $dataAud[$i]['ValorAnterior'],
							'ValorActual' => $dataAud[$i]['ValorActual']
						);
					}
				}
				else
					$data['regAud'] = false;
			}
			else
				$data['mensajeError'] = 'Empleado no existe en la base de datos';

			return($data);
		}

		public function validarDatos($id)
		{
			// SE CARGAN DATOS
			$data = $this->cargarDatos($id);

			$data['IdEmpleado'] = $data['reg']['Id'];

			// REGISTRO PARA EL LOG DE EMPLEADOS
			if (TRUE) 
			{
				$logEmpleado = array();

				// 	if ($_REQUEST['TipoIdentificacion'] <> $data['reg']['TipoIdentificacion']) 
				// 	{
				// 		$Campo = 'TipoIdentificacion';

				// 	if ($data['reg']['TipoIdentificacion'] > 0) 
				// 	{
				// 		$reg = getRegistro('PARAMETROS', $data['reg']['TipoIdentificacion']);
				// 		if ($reg)
				// 			$ValorAnterior = $reg['detalle'];
				// 		else
				// 			$ValorAnterior = '';
				// 	}
				// 	else
				// 		$ValorAnterior = '';

				// 	$reg = getRegistro('PARAMETROS', $_REQUEST['TipoIdentificacion']);
				// 	$ValorActual = $reg['detalle'];
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				if ($_REQUEST['Documento'] <> $data['reg']['Documento']) 
				{
					$Campo = 'Documento';
					$ValorAnterior = $data['reg']['Documento'];
					$ValorActual = $_REQUEST['Documento'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['CodigoSAP'] <> $data['reg']['CodigoSAP']) 
				{
					$Campo = 'CodigoSAP';
					$ValorAnterior = $data['reg']['CodigoSAP'];
					$ValorActual = $_REQUEST['CodigoSAP'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				// if ($_REQUEST['FechaExpedicion'] <> $data['reg']['FechaExpedicion']) 
				// {
				// 	$Campo = 'FechaExpedicion';
				// 	$ValorAnterior = $data['reg']['FechaExpedicion'];
				// 	$ValorActual = $_REQUEST['FechaExpedicion'];
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				// if ($_REQUEST['IdCiudadExpedicion'] <> $data['reg']['IdCiudadExpedicion']) 
				// {
				// 	$Campo = 'CiudadExpedicion';

				// 	if ($data['reg']['IdCiudadExpedicion'] > 0) 
				// 	{
				// 		$reg = getRegistro('CIUDADES', $data['reg']['IdCiudadExpedicion']);
				// 		if ($reg)
				// 			$ValorAnterior = $reg['nombre'] . ' (' . $reg['departamento'] . ')';
				// 		else
				// 			$ValorAnterior = '';
				// 	}
				// 	else
				// 		$ValorAnterior = '';

				// 	if ($_REQUEST['IdCiudadExpedicion'] > 0)
				// 	{
				// 		$reg = getRegistro('CIUDADES', $_REQUEST['IdCiudadExpedicion']);
				// 		$ValorActual = $reg['nombre'] . ' (' . $reg['departamento'] . ')';
				// 	}
				// 	else
				// 		$ValorActual = '';
	
				// 		$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				if ($_REQUEST['Apellido1'] <> $data['reg']['Apellido1']) 
				{
					$Campo = 'Apellido1';
					$ValorAnterior = $data['reg']['Apellido1'];
					$ValorActual = $_REQUEST['Apellido1'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['Apellido2'] <> $data['reg']['Apellido2']) 
				{
					$Campo = 'Apellido2';
					$ValorAnterior = $data['reg']['Apellido2'];
					$ValorActual = $_REQUEST['Apellido2'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['Nombre1'] <> $data['reg']['Nombre1']) 
				{
					$Campo = 'Nombre1';
					$ValorAnterior = $data['reg']['Nombre1'];
					$ValorActual = $_REQUEST['Nombre1'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['Nombre2'] <> $data['reg']['Nombre2']) 
				{
					$Campo = 'Nombre2';
					$ValorAnterior = $data['reg']['Nombre2'];
					$ValorActual = $_REQUEST['Nombre2'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				// if ($_REQUEST['FechaNacimiento'] <> $data['reg']['FechaNacimiento']) 
				// {
				// 	$Campo = 'FechaNacimiento';
				// 	$ValorAnterior = $data['reg']['FechaNacimiento'];
				// 	$ValorActual = $_REQUEST['FechaNacimiento'];
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				// if ($_REQUEST['IdCiudadNacimiento'] <> $data['reg']['IdCiudadNacimiento']) 
				// {
				// 	$Campo = 'CiudadNacimiento';

				// 	if ($data['reg']['IdCiudadNacimiento'] > 0) 
				// 	{
				// 		$reg = getRegistro('CIUDADES', $data['reg']['IdCiudadNacimiento']);
				// 		if ($reg)
				// 			$ValorAnterior = $reg['nombre'] . ' (' . $reg['departamento'] . ')';
				// 		else
				// 			$ValorAnterior = '';
				// 	}
				// 	else
				// 		$ValorAnterior = '';

				// 	if ($_REQUEST['IdCiudadNacimiento'] > 0)
				// 	{
				// 		$reg = getRegistro('CIUDADES', $_REQUEST['IdCiudadNacimiento']);
				// 		$ValorActual = $reg['nombre'] . ' (' . $reg['departamento'] . ')';
				// 	}
				// 	else
				// 		$ValorActual = '';

				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				if ($_REQUEST['Genero'] <> $data['reg']['Genero']) 
				{
					$Campo = 'Genero';

					if ($data['reg']['Genero'] > 0) 
					{
						$reg = getRegistro('PARAMETROS', $data['reg']['Genero']);
						if ($reg)
							$ValorAnterior = $reg['detalle'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['Genero'] > 0)
					{
						$reg = getRegistro('PARAMETROS', $_REQUEST['Genero']);
						$ValorActual = $reg['detalle'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['EstadoCivil'] <> $data['reg']['EstadoCivil']) 
				{
					$Campo = 'EstadoCivil';

					if ($data['reg']['EstadoCivil'] > 0) 
					{
						$reg = getRegistro('PARAMETROS', $data['reg']['EstadoCivil']);
						if ($reg)
							$ValorAnterior = $reg['detalle'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';
						
					if ($_REQUEST['EstadoCivil'] > 0)
					{
						$reg = getRegistro('PARAMETROS', $_REQUEST['EstadoCivil']);
						$ValorActual = $reg['detalle'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				// if ($_REQUEST['FactorRH'] <> $data['reg']['FactorRH']) 
				// {
				// 	$Campo = 'FactorRH';
				
				// 	if ($data['reg']['FactorRH'] > 0) 
				// 	{
				// 		$reg = getRegistro('PARAMETROS', $data['reg']['FactorRH']);
				// 		if ($reg)
				// 			$ValorAnterior = $reg['detalle'];
				// 		else
				// 			$ValorAnterior = '';
				// 	}
				// 	else
				// 		$ValorAnterior = '';

				// 	if ($_REQUEST['FactorRH'] > 0)
				// 	{
				// 		$reg = getRegistro('PARAMETROS', $_REQUEST['FactorRH']);
				// 		$ValorActual = $reg['detalle'];
				// 	}
				// 	else
				// 		$ValorActual = '';
	
				// 		$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				// if ($_REQUEST['LibretaMilitar'] <> $data['reg']['LibretaMilitar']) 
				// {
				// 	$Campo = 'LibretaMilitar';
				// 	$ValorAnterior = $data['reg']['LibretaMilitar'];
				// 	$ValorActual = $_REQUEST['LibretaMilitar'];
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				// if ($_REQUEST['DistritoMilitar'] <> $data['reg']['DistritoMilitar']) 
				// {
				// 	$Campo = 'DistritoMilitar';
				// 	$ValorAnterior = $data['reg']['DistritoMilitar'];
				// 	$ValorActual = $_REQUEST['DistritoMilitar'];
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				// if ($_REQUEST['LicenciaConduccion'] <> $data['reg']['LicenciaConduccion']) 
				// {
				// 	$Campo = 'LicenciaConduccion';
				// 	$ValorAnterior = $data['reg']['LicenciaConduccion'];
				// 	$ValorActual = $_REQUEST['LicenciaConduccion'];
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				// if ($_REQUEST['TarjetaProfesional'] <> $data['reg']['TarjetaProfesional']) 
				// {
				// 	$Campo = 'TarjetaProfesional';
				// 	$ValorAnterior = $data['reg']['TarjetaProfesional'];
				// 	$ValorActual = $_REQUEST['TarjetaProfesional'];
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				if ($_REQUEST['Direccion'] <> $data['reg']['Direccion']) 
				{
					$Campo = 'Direccion';
					$ValorAnterior = $data['reg']['Direccion'];
					$ValorActual = $_REQUEST['Direccion'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				// if ($_REQUEST['Barrio'] <> $data['reg']['Barrio']) 
				// {
				// 	$Campo = 'Barrio';
				// 	$ValorAnterior = $data['reg']['Barrio'];
				// 	$ValorActual = $_REQUEST['Barrio'];
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				// if ($_REQUEST['Localidad'] <> $data['reg']['Localidad']) 
				// {
				// 	$Campo = 'Localidad';
				// 	$ValorAnterior = $data['reg']['Localidad'];
				// 	$ValorActual = $_REQUEST['Localidad'];
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				if ($_REQUEST['IdCiudad'] <> $data['reg']['IdCiudad']) 
				{
					$Campo = 'Ciudad';

					if ($data['reg']['IdCiudad'] > 0) 
					{
						$reg = getRegistro('CIUDADES', $data['reg']['IdCiudad']);
						if ($reg)
							$ValorAnterior = $reg['nombre'] . ' (' . $reg['departamento'] . ')';
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['IdCiudad'] > 0)
					{
						$reg = getRegistro('CIUDADES', $_REQUEST['IdCiudad']);
						$ValorActual = $reg['nombre'] . ' (' . $reg['departamento'] . ')';
					}
					else
						$ValorActual = '';
	
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['Email'] <> $data['reg']['Email']) 
				{
					$Campo = 'Email';
					$ValorAnterior = $data['reg']['Email'];
					$ValorActual = $_REQUEST['Email'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['EmailCorporativo'] <> $data['reg']['EmailCorporativo']) 
				{
					$Campo = 'EmailCorporativo';
					$ValorAnterior = $data['reg']['EmailCorporativo'];
					$ValorActual = $_REQUEST['EmailCorporativo'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['EmailProyecto'] <> $data['reg']['EmailProyecto']) 
				{
					$Campo = 'EmailProyecto';
					$ValorAnterior = $data['reg']['EmailProyecto'];
					$ValorActual = $_REQUEST['EmailProyecto'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['Telefono'] <> $data['reg']['Telefono']) 
				{
					$Campo = 'Telefono';
					$ValorAnterior = $data['reg']['Telefono'];
					$ValorActual = $_REQUEST['Telefono'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['Celular'] <> $data['reg']['Celular']) 
				{
					$Campo = 'Celular';
					$ValorAnterior = $data['reg']['Celular'];
					$ValorActual = $_REQUEST['Celular'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				// if ($_REQUEST['PerfilProfesional'] <> $data['reg']['PerfilProfesional']) 
				// {
				// 	$Campo = 'PerfilProfesional';
				// 	$ValorAnterior = $data['reg']['PerfilProfesional'];
				// 	$ValorActual = $_REQUEST['PerfilProfesional'];
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				// if ($_REQUEST['TipoEmpleado'] <> $data['reg']['TipoEmpleado']) 
				// {
				// 	$Campo = 'TipoEmpleado';

				// 	if ($data['reg']['TipoEmpleado'] > 0) 
				// 	{
				// 		$reg = getRegistro('PARAMETROS', $data['reg']['TipoEmpleado']);
				// 		if ($reg)
				// 			$ValorAnterior = $reg['detalle'];
				// 		else
				// 			$ValorAnterior = '';
				// 	}
				// 	else
				// 		$ValorAnterior = '';

				// 	if ($_REQUEST['TipoEmpleado'] > 0)
				// 	{
				// 		$reg = getRegistro('PARAMETROS', $_REQUEST['TipoEmpleado']);
				// 		$ValorActual = $reg['detalle'];
				// 	}
				// 	else
				// 		$ValorActual = '';
	
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				if ($_REQUEST['IdCentro'] <> $data['reg']['IdCentro']) 
				{
					$Campo = 'Centro';

					if ($data['reg']['IdCentro'] > 0) 
					{
						$reg = getRegistro('CENTROS', $data['reg']['IdCentro']);
						if ($reg)
							$ValorAnterior = $reg['nombre'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['IdCentro'] > 0)
					{
						$reg = getRegistro('CENTROS', $_REQUEST['IdCentro']);
						$ValorActual = $reg['nombre'];
					}
					else
						$ValorActual = '';
	
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['IdProyecto'] <> $data['reg']['IdProyecto']) 
				{
					$Campo = 'Proyecto';

					if ($data['reg']['IdProyecto'] > 0) 
					{
						$reg = getRegistro('CENTROS', $data['reg']['IdProyecto']);
						if ($reg)
							$ValorAnterior = $reg['nombre'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['IdProyecto'] > 0) 
					{
						$reg = getRegistro('CENTROS', $_REQUEST['IdProyecto']);
						$ValorActual = $reg['nombre'];
					}
					else
						$ValorActual = '';
						
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['Vicepresidencia'] <> $data['reg']['Vicepresidencia']) 
				{
					$Campo = 'Vicepresidencia';

					if ($data['reg']['Vicepresidencia'] > 0) 
					{
						$reg = getRegistro('PARAMETROS', $data['reg']['Vicepresidencia']);
						if ($reg)
							$ValorAnterior = $reg['detalle'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['Vicepresidencia'] > 0)
					{
						$reg = getRegistro('PARAMETROS', $_REQUEST['Vicepresidencia']);
						$ValorActual = $reg['detalle'];
					}
					else
						$ValorActual = '';
	
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['IdSede'] <> $data['reg']['IdSede']) 
				{
					$Campo = 'Sede';

					if ($data['reg']['IdSede'] > 0) 
					{
						$reg = getRegistro('SEDES', $data['reg']['IdSede']);
						if ($reg)
							$ValorAnterior = $reg['nombre'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['IdSede'] > 0) 
					{
						$reg = getRegistro('SEDES', $_REQUEST['IdSede']);
						$ValorActual = $reg['nombre'];
					}
					else
						$ValorActual = '';
						
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['TipoContrato'] <> $data['reg']['TipoContrato']) 
				{
					$Campo = 'TipoContrato';

					if ($data['reg']['TipoContrato'] > 0) 
					{
						$reg = getRegistro('PARAMETROS', $data['reg']['TipoContrato']);
						if ($reg)
							$ValorAnterior = $reg['detalle'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['TipoContrato'] > 0)
					{
						$reg = getRegistro('PARAMETROS', $_REQUEST['TipoContrato']);
						$ValorActual = $reg['detalle'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				// if ($_REQUEST['IdCategoria'] <> $data['reg']['IdCategoria']) 
				// {
				// 	$Campo = 'Categoria';

				// 	if ($data['reg']['IdCategoria'] > 0) 
				// 	{
				// 		$reg = getRegistro('CATEGORIAS', $data['reg']['IdCategoria']);
				// 		if ($reg)
				// 			$ValorAnterior = $reg['nombre'];
				// 		else
				// 			$ValorAnterior = '';
				// 	}
				// 	else
				// 		$ValorAnterior = '';

				// 	if ($_REQUEST['IdCategoria'] > 0)
				// 	{
				// 		$reg = getRegistro('CATEGORIAS', $_REQUEST['IdCategoria']);
				// 		$ValorActual = $reg['nombre'];
				// 	}
				// 	else
				// 		$ValorActual = '';
					
				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				// if ($_REQUEST['IdCiudadTrabajo'] <> $data['reg']['IdCiudadTrabajo']) 
				// {
				// 	$Campo = 'CiudadTrabajo';

				// 	if ($data['reg']['IdCiudadTrabajo'] > 0) 
				// 	{
				// 		$reg = getRegistro('CIUDADES', $data['reg']['IdCiudadTrabajo']);
				// 		if ($reg)
				// 			$ValorAnterior = $reg['nombre'] . ' (' . $reg['departamento'] . ')';
				// 		else
				// 			$ValorAnterior = '';
				// 	}
				// 	else
				// 		$ValorAnterior = '';

				// 	if ($_REQUEST['IdCiudadTrabajo'] > 0)
				// 	{
				// 		$reg = getRegistro('CIUDADES', $_REQUEST['IdCiudadTrabajo']);
				// 		$ValorActual = $reg['nombre'] . ' (' . $reg['departamento'] . ')';
				// 	}
				// 	else
				// 		$ValorActual = '';

				// 	$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				// }

				if ($_REQUEST['FechaIngreso'] <> $data['reg']['FechaIngreso']) 
				{
					$Campo = 'FechaIngreso';
					$ValorAnterior = $data['reg']['FechaIngreso'];
					$ValorActual = $_REQUEST['FechaIngreso'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['FechaPeriodoPrueba'] <> $data['reg']['FechaPeriodoPrueba']) 
				{
					$Campo = 'FechaPeriodoPrueba';
					$ValorAnterior = $data['reg']['FechaPeriodoPrueba'];
					$ValorActual = $_REQUEST['FechaPeriodoPrueba'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['FechaVencimiento'] <> $data['reg']['FechaVencimiento']) 
				{
					$Campo = 'FechaVencimiento';
					$ValorAnterior = $data['reg']['FechaVencimiento'];
					$ValorActual = $_REQUEST['FechaVencimiento'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['ModalidadTrabajo'] <> $data['reg']['ModalidadTrabajo']) 
				{
					$Campo = 'ModalidadTrabajo';

					if ($data['reg']['ModalidadTrabajo'] > 0) 
					{
						$reg = getRegistro('PARAMETROS', $data['reg']['ModalidadTrabajo']);
						if ($reg)
							$ValorAnterior = $reg['detalle'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['ModalidadTrabajo'] > 0)
					{
						$reg = getRegistro('PARAMETROS', $_REQUEST['ModalidadTrabajo']);
						$ValorActual = $reg['detalle'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['IdCargo'] <> $data['reg']['IdCargo']) 
				{
					$Campo = 'Cargo';

					if ($data['reg']['IdCargo'] > 0) 
					{
						$reg = getRegistro('CARGOS', $data['reg']['IdCargo']);
						if ($reg)
							$ValorAnterior = $reg['nombre'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['IdCargo'] > 0)
					{
						$reg = getRegistro('CARGOS', $_REQUEST['IdCargo']);
						$ValorActual = $reg['nombre'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['SueldoBasico'] <> $data['reg']['SueldoBasico']) 
				{
					$Campo = 'SueldoBasico';
					$ValorAnterior = $data['reg']['SueldoBasico'];
					$ValorActual = $_REQUEST['SueldoBasico'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['Observaciones'] <> $data['reg']['Observaciones']) 
				{
					$Campo = 'Observaciones';
					$ValorAnterior = $data['reg']['Observaciones'];
					$ValorActual = $_REQUEST['Observaciones'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['SubsidioTransporte'] <> $data['reg']['SubsidioTransporte']) 
				{
					$Campo = 'SubsidioTransporte';

					if ($data['reg']['SubsidioTransporte'] > 0) 
					{
						$reg = getRegistro('PARAMETROS', $data['reg']['SubsidioTransporte']);
						if ($reg)
							$ValorAnterior = $reg['detalle'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['SubsidioTransporte'] > 0)
					{
						$reg = getRegistro('PARAMETROS', $_REQUEST['SubsidioTransporte']);
						$ValorActual = $reg['detalle'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['PeriodicidadPago'] <> $data['reg']['PeriodicidadPago']) 
				{
					$Campo = 'PeriodicidadPago';

					if ($data['reg']['PeriodicidadPago'] > 0) 
					{
						$reg = getRegistro('PARAMETROS', $data['reg']['PeriodicidadPago']);
						if ($reg)
							$ValorAnterior = $reg['detalle'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['PeriodicidadPago'] > 0)
					{
						$reg = getRegistro('PARAMETROS', $_REQUEST['PeriodicidadPago']);
						$ValorActual = $reg['detalle'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['IdEPS'] <> $data['reg']['IdEPS']) 
				{
					$Campo = 'EPS';

					if ($data['reg']['IdEPS'] > 0) 
					{
						$reg = getRegistro('TERCEROS', $data['reg']['IdEPS']);
						if ($reg) 
							$ValorAnterior = $reg['nombre'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['IdEPS'] > 0)
					{
						$reg = getRegistro('TERCEROS', $_REQUEST['IdEPS']);
						$ValorActual = $reg['nombre'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['RegimenCesantias'] <> $data['reg']['RegimenCesantias']) 
				{
					$Campo = 'RegimenCesantias';

					if ($data['reg']['RegimenCesantias'] > 0) 
					{
						$reg = getRegistro('PARAMETROS', $data['reg']['RegimenCesantias']);
						if ($reg)
							$ValorAnterior = $reg['detalle'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['RegimenCesantias'] > 0)
					{
						$reg = getRegistro('PARAMETROS', $_REQUEST['RegimenCesantias']);
						$ValorActual = $reg['detalle'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['FactorPrestacional'] <> $data['reg']['FactorPrestacional']) 
				{
					$Campo = 'FactorPrestacional';
					$ValorAnterior = $data['reg']['FactorPrestacional'];
					$ValorActual = $_REQUEST['FactorPrestacional'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['IdFondoCesantias'] <> $data['reg']['IdFondoCesantias']) 
				{
					$Campo = 'FondoCesantias';

					if ($data['reg']['IdFondoCesantias'] > 0) 
					{
						$reg = getRegistro('TERCEROS', $data['reg']['IdFondoCesantias']);
						if ($reg)
							$ValorAnterior = $reg['nombre'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';
					
					if ($_REQUEST['IdFondoCesantias'] > 0) 
					{
						$reg = getRegistro('TERCEROS', $_REQUEST['IdFondoCesantias']);
						$ValorActual = $reg['nombre'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['IdFondoPensiones'] <> $data['reg']['IdFondoPensiones']) 
				{
					$Campo = 'FondoPensiones';

					if ($data['reg']['IdFondoPensiones'] > 0) 
					{
						$reg = getRegistro('TERCEROS', $data['reg']['IdFondoPensiones']);
						if ($reg)
							$ValorAnterior = $reg['nombre'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['IdFondoPensiones'] > 0) 
					{
						$reg = getRegistro('TERCEROS', $_REQUEST['IdFondoPensiones']);
						$ValorActual = $reg['nombre'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['IdCajaCompensacion'] <> $data['reg']['IdCajaCompensacion']) 
				{
					$Campo = 'CajaCompensacion';
					
					if ($data['reg']['IdCajaCompensacion'] > 0) 
					{
						$reg = getRegistro('TERCEROS', $data['reg']['IdCajaCompensacion']);
						if ($reg)
							$ValorAnterior = $reg['nombre'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';
					
					if ($_REQUEST['IdCajaCompensacion'] > 0) 
					{
						$reg = getRegistro('TERCEROS', $_REQUEST['IdCajaCompensacion']);

						if ($reg) 
							$ValorActual = $reg['nombre'];
						else
							$ValorActual = '';
					}
					else
						$ValorActual = '';
						
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['FormaDePago'] <> $data['reg']['FormaDePago']) 
				{
					$Campo = 'FormaDePago';

					if ($data['reg']['FormaDePago'] > 0) 
					{
						$reg = getRegistro('PARAMETROS', $data['reg']['FormaDePago']);
						if ($reg)
							$ValorAnterior = $reg['detalle'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['formaDePago'] > 0)
					{
						$reg = getRegistro('PARAMETROS', $_REQUEST['FormaDePago']);
						$ValorActual = $reg['detalle'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['IdBanco'] <> $data['reg']['IdBanco']) 
				{
					$Campo = 'Banco';

					if ($data['reg']['IdBanco'] > 0) 
					{
						$reg = getRegistro('BANCOS', $data['reg']['IdBanco']);
						if ($reg)
							$ValorAnterior = $reg['nombre'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';
					
					if ($_REQUEST['IdBanco'] > 0)
					{
						$reg = getRegistro('BANCOS', $_REQUEST['IdBanco']);
						$ValorActual = $reg['nombre'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['TipoCuentaBancaria'] <> $data['reg']['TipoCuentaBancaria']) 
				{
					$Campo = 'TipoCuentaBancaria';

					if ($data['reg']['TipoCuentaBancaria'] > 0) 
					{
						$reg = getRegistro('PARAMETROS', $data['reg']['TipoCuentaBancaria']);
						if ($reg)
							$ValorAnterior = $reg['detalle'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['TipoCuentaBancaria'] > 0)
					{
						$reg = getRegistro('PARAMETROS', $_REQUEST['TipoCuentaBancaria']);
						$ValorActual = $reg['detalle'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['CuentaBancaria'] <> $data['reg']['CuentaBancaria']) 
				{
					$Campo = 'CuentaBancaria';
					$ValorAnterior = $data['reg']['CuentaBancaria'];
					$ValorActual = $_REQUEST['CuentaBancaria'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['CuentaBancaria2'] <> $data['reg']['CuentaBancaria2']) 
				{
					$Campo = 'CuentaBancaria2';
					$ValorAnterior = $data['reg']['CuentaBancaria2'];
					$ValorActual = $_REQUEST['CuentaBancaria2'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['MetodoRetencion'] <> $data['reg']['MetodoRetencion']) 
				{
					$Campo = 'MetodoRetencion';

					if ($data['reg']['MetodoRetencion'] > 0) 
					{
						$reg = getRegistro('PARAMETROS', $data['reg']['MetodoRetencion']);
						if ($reg)
							$ValorAnterior = $reg['detalle'];
						else
							$ValorAnterior = '';
					}
					else
						$ValorAnterior = '';

					if ($_REQUEST['MetodoRetencion'] > 0)
					{
						$reg = getRegistro('PARAMETROS', $_REQUEST['MetodoRetencion']);
						$ValorActual = $reg['detalle'];
					}
					else
						$ValorActual = '';

					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['PorcentajeRetencion'] <> $data['reg']['PorcentajeRetencion']) 
				{
					$Campo = 'PorcentajeRetencion';
					$ValorAnterior = $data['reg']['PorcentajeRetencion'];
					$ValorActual = $_REQUEST['PorcentajeRetencion'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['MayorRetencionFuente'] <> $data['reg']['MayorRetencionFuente']) 
				{
					$Campo = 'MayorRetencionFuente';
					$ValorAnterior = $data['reg']['MayorRetencionFuente'];
					$ValorActual = $_REQUEST['MayorRetencionFuente'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if (isset($_REQUEST['DeduccionDependientes']) AND $_REQUEST['DeduccionDependientes'] <> $data['reg']['DeduccionDependientes']) 
				{
					$Campo = 'DeduccionDependientes';
					$ValorAnterior = 0;
					$ValorActual = 1;
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}
				elseif($data['reg']['DeduccionDependientes'])
				{
					$Campo = 'DeduccionDependientes';
					$ValorAnterior = 1;
					$ValorActual = 0;
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['CuotaVivienda'] <> $data['reg']['CuotaVivienda']) 
				{
					$Campo = 'CuotaVivienda';
					$ValorAnterior = $data['reg']['CuotaVivienda'];
					$ValorActual = $_REQUEST['CuotaVivienda'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['SaludYEducacion'] <> $data['reg']['SaludYEducacion']) 
				{
					$Campo = 'SaludYEducacion';
					$ValorAnterior = $data['reg']['SaludYEducacion'];
					$ValorActual = $_REQUEST['SaludYEducacion'];
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if (isset($_REQUEST['PoliticamenteExpuesta']) AND $_REQUEST['PoliticamenteExpuesta'] <> $data['reg']['PoliticamenteExpuesta']) 
				{
					$Campo = 'PoliticamenteExpuesta';
					$ValorAnterior = 0;
					$ValorActual = 1;
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}
				elseif($data['reg']['PoliticamenteExpuesta'])
				{
					$Campo = 'PoliticamenteExpuesta';
					$ValorAnterior = 1;
					$ValorActual = 0;
					$logEmpleado[] = array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}
			}

			$regEmpleado = getRegistro('EMPLEADOS', $id);
			// LOS DATOS TIPO BOOLEAN SE CARGAN COMO TRUE o FALSE EN CARGAR DATOS
			// AL MOMENTO DE ACTUALIZAR (VALIDAR DATOS) SE PASAN A 'TRUE' o 'FALSE' PARA EVITAR ERRORES CON PDO
			$data['reg'] = array(
					'TipoIdentificacion' 	=> isset($_REQUEST['TipoIdentificacion']) ? $_REQUEST['TipoIdentificacion'] : '',
					'Documento' 			=> isset($_REQUEST['Documento']) ? $_REQUEST['Documento'] : '',
					'Documento2' 			=> isset($_REQUEST['Documento2']) ? $_REQUEST['Documento2'] : '',
					'FechaExpedicion' 		=> (isset($_REQUEST['FechaExpedicion']) AND ! empty($_REQUEST['FechaExpedicion'])) ? $_REQUEST['FechaExpedicion'] : NULL,
					'IdCiudadExpedicion' 	=> isset($_REQUEST['IdCiudadExpedicion']) ? $_REQUEST['IdCiudadExpedicion'] : '',
					'Apellido1' 			=> isset($_REQUEST['Apellido1']) ? $_REQUEST['Apellido1'] : '',
					'Apellido2' 			=> isset($_REQUEST['Apellido2']) ? $_REQUEST['Apellido2'] : '',
					'Nombre1' 				=> isset($_REQUEST['Nombre1']) ? $_REQUEST['Nombre1'] : '',
					'Nombre2' 				=> isset($_REQUEST['Nombre2']) ? $_REQUEST['Nombre2'] : '',
					'Estado' 				=> $regEmpleado['estado'], 
					'FechaNacimiento' 		=> (isset($_REQUEST['FechaNacimiento']) AND ! empty($_REQUEST['FechaNacimiento'])) ? $_REQUEST['FechaNacimiento'] : NULL,
					'IdCiudadNacimiento' 	=> isset($_REQUEST['IdCiudadNacimiento']) ? $_REQUEST['IdCiudadNacimiento'] : '',
					'Genero' 				=> isset($_REQUEST['Genero']) ? $_REQUEST['Genero'] : '',
					'EstadoCivil' 			=> isset($_REQUEST['EstadoCivil']) ? $_REQUEST['EstadoCivil'] : '',
					'FactorRH' 				=> isset($_REQUEST['FactorRH']) ? $_REQUEST['FactorRH'] : '',
					'Profesion' 			=> $regEmpleado['profesion'], 
					'Educacion' 			=> $regEmpleado['educacion'], 
					'LibretaMilitar' 		=> isset($_REQUEST['LibretaMilitar']) ? $_REQUEST['LibretaMilitar'] : '',
					'DistritoMilitar' 		=> isset($_REQUEST['DistritoMilitar']) ? $_REQUEST['DistritoMilitar'] : '',
					'LicenciaConduccion' 	=> isset($_REQUEST['LicenciaConduccion']) ? $_REQUEST['LicenciaConduccion'] : '',
					'TarjetaProfesional' 	=> isset($_REQUEST['TarjetaProfesional']) ? $_REQUEST['TarjetaProfesional'] : '',
					'Direccion' 			=> isset($_REQUEST['Direccion']) ? $_REQUEST['Direccion'] : '',
					'Barrio' 				=> isset($_REQUEST['Barrio']) ? $_REQUEST['Barrio'] : '',
					'Localidad' 			=> isset($_REQUEST['Localidad']) ? $_REQUEST['Localidad'] : '',
					'IdCiudad' 				=> isset($_REQUEST['IdCiudad']) ? $_REQUEST['IdCiudad'] : '',
					'Email' 				=> isset($_REQUEST['Email']) ? $_REQUEST['Email'] : '',
					'EmailCorporativo' 		=> isset($_REQUEST['EmailCorporativo']) ? $_REQUEST['EmailCorporativo'] : '',
					'EmailProyecto' 		=> isset($_REQUEST['EmailProyecto']) ? $_REQUEST['EmailProyecto'] : '',
					'Telefono' 				=> isset($_REQUEST['Telefono']) ? $_REQUEST['Telefono'] : '',
					'Celular' 				=> isset($_REQUEST['Celular']) ? $_REQUEST['Celular'] : '',
					'IdCargo' 				=> isset($_REQUEST['IdCargo']) ? $_REQUEST['IdCargo'] : 0,
					'PerfilProfesional' 	=> isset($_REQUEST['PerfilProfesional']) ? $_REQUEST['PerfilProfesional'] : '',
					'CodigoSAP' 			=> isset($_REQUEST['CodigoSAP']) ? $_REQUEST['CodigoSAP'] : '',
					'IdCentro' 				=> isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0,
					'IdProyecto' 			=> isset($_REQUEST['IdProyecto']) ? $_REQUEST['IdProyecto'] : 0,
					'Vicepresidencia' 		=> isset($_REQUEST['Vicepresidencia']) ? $_REQUEST['Vicepresidencia'] : 0,
					'IdSede' 				=> isset($_REQUEST['IdSede']) ? $_REQUEST['IdSede'] : 0,
					'TipoContrato' 			=> isset($_REQUEST['TipoContrato']) ? $_REQUEST['TipoContrato'] : 0,
					'IdCategoria' 			=> isset($_REQUEST['IdCategoria']) ? $_REQUEST['IdCategoria'] : 0,
					'IdCiudadTrabajo' 		=> isset($_REQUEST['IdCiudadTrabajo']) ? $_REQUEST['IdCiudadTrabajo'] : 0,
					'FechaIngreso' 			=> (isset($_REQUEST['FechaIngreso']) AND ! empty($_REQUEST['FechaIngreso'])) ? $_REQUEST['FechaIngreso'] : NULL,
					'FechaPeriodoPrueba' 	=> (isset($_REQUEST['FechaPeriodoPrueba']) AND ! empty($_REQUEST['FechaPeriodoPrueba'])) ? $_REQUEST['FechaPeriodoPrueba'] : NULL,
					'FechaVencimiento' 		=> (isset($_REQUEST['FechaVencimiento']) AND ! empty($_REQUEST['FechaVencimiento'])) ? $_REQUEST['FechaVencimiento'] : NULL,
					'Prorrogas' 			=> isset($_REQUEST['Prorrogas']) ? $_REQUEST['Prorrogas'] : 0,
					'ModalidadTrabajo' 		=> isset($_REQUEST['ModalidadTrabajo']) ? $_REQUEST['ModalidadTrabajo'] : 0,
					'SueldoBasico' 			=> isset($_REQUEST['SueldoBasico']) ? $_REQUEST['SueldoBasico'] : 0,
					'SubsidioTransporte' 	=> isset($_REQUEST['SubsidioTransporte']) ? $_REQUEST['SubsidioTransporte'] : 0,
					'PeriodicidadPago' 		=> isset($_REQUEST['PeriodicidadPago']) ? $_REQUEST['PeriodicidadPago'] : 0,
					'HorasMes' 				=> $regEmpleado['horasmes'], 
					'DiasAno' 				=> $regEmpleado['diasano'], 
					'IdEPS' 				=> isset($_REQUEST['IdEPS']) ? $_REQUEST['IdEPS'] : 0,
					'RegimenCesantias' 		=> isset($_REQUEST['RegimenCesantias']) ? $_REQUEST['RegimenCesantias'] : 0,
					'FactorPrestacional' 	=> isset($_REQUEST['FactorPrestacional']) ? $_REQUEST['FactorPrestacional'] : 0,
					'IdFondoCesantias' 		=> isset($_REQUEST['IdFondoCesantias']) ? $_REQUEST['IdFondoCesantias'] : 0,
					'IdFondoPensiones' 		=> isset($_REQUEST['IdFondoPensiones']) ? $_REQUEST['IdFondoPensiones'] : 0,
					'IdCajaCompensacion' 	=> isset($_REQUEST['IdCajaCompensacion']) ? $_REQUEST['IdCajaCompensacion'] : 0,
					'IdARL' 				=> $regEmpleado['idarl'], 
					'NivelRiesgo' 			=> $regEmpleado['nivelriesgo'], 
					'FormaDePago' 			=> isset($_REQUEST['FormaDePago']) ? $_REQUEST['FormaDePago'] : 0,
					'IdBanco' 				=> isset($_REQUEST['IdBanco']) ? $_REQUEST['IdBanco'] : 0,
					'TipoCuentaBancaria' 	=> isset($_REQUEST['TipoCuentaBancaria']) ? $_REQUEST['TipoCuentaBancaria'] : 0,
					'CuentaBancaria' 		=> isset($_REQUEST['CuentaBancaria']) ? $_REQUEST['CuentaBancaria'] : '',
					'CuentaBancaria2' 		=> isset($_REQUEST['CuentaBancaria2']) ? $_REQUEST['CuentaBancaria2'] : '',
					'MetodoRetencion' 		=> isset($_REQUEST['MetodoRetencion']) ? $_REQUEST['MetodoRetencion'] : 0,
					'PorcentajeRetencion' 	=> isset($_REQUEST['PorcentajeRetencion']) ? $_REQUEST['PorcentajeRetencion'] : 0,
					'MayorRetencionFuente' 	=> isset($_REQUEST['MayorRetencionFuente']) ? $_REQUEST['MayorRetencionFuente'] : 0,
					'DeduccionDependientes' => isset($_REQUEST['DeduccionDependientes']) ? 'true' : 'false',
					'CuotaVivienda' 		=> isset($_REQUEST['CuotaVivienda']) ? $_REQUEST['CuotaVivienda'] : 0,
					'SaludYEducacion' 		=> isset($_REQUEST['SaludYEducacion']) ? $_REQUEST['SaludYEducacion'] : 0,
					'Observaciones' 		=> isset($_REQUEST['Observaciones']) ? $_REQUEST['Observaciones'] : '',
					'GrupoPoblacional' 		=> $regEmpleado['grupopoblacional'],	
					'PoliticamenteExpuesta' => isset($_REQUEST['PoliticamenteExpuesta']) ? 'true' : 'false'
			);

			$data['mensajeError'] = '';

			// VALIDACION DATOS EMPLEADO
			// if	( empty($data['reg']['TipoIdentificacion']) )
			// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de identificación.') . '</strong><br>';

			if	( empty($data['reg']['Documento']) )
				$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento') . '</strong><br>';
			else
			{
				$Documento = $data['reg']['Documento'];
				$IdEmpleado = $data['IdEmpleado'];

				$query = <<<EOD
					SELECT * FROM EMPLEADOS 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
						WHERE EMPLEADOS.Documento = '$Documento' AND 
							PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.Id <> $IdEmpleado;
				EOD;

				$reg = $this->model->buscarEmpleado($query);

				if ($reg) 
					$data['mensajeError'] .= '<strong>' . label('Documento') . '</strong> ' . label('ya existe') . '<br>';
			}

			if	( ! empty($data['reg']['CodigoSAP']) )
				// $data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Código SAP') . '</strong><br>';
			// else
			{
				$Documento = $data['reg']['Documento'];
				$CodigoSAP = $data['reg']['CodigoSAP'];
				$IdEmpleado = $data['IdEmpleado'];

				$query = <<<EOD
					SELECT * FROM EMPLEADOS 
						WHERE EMPLEADOS.CodigoSAP = '$CodigoSAP' AND 
							EMPLEADOS.Documento <> '$Documento' AND 
							EMPLEADOS.Id <> $IdEmpleado;
				EOD;

				$reg = $this->model->buscarEmpleado($query);

				if ($reg) 
					$data['mensajeError'] .= '<strong>' . label('Código SAP') . '</strong> ' . label('ya existe') . '<br>';
			}

			if	( ! empty($data['reg']['Documento2']) )
			{
				$Documento2 = $data['reg']['Documento2'];
				$IdEmpleado = $data['IdEmpleado'];

				$query = <<<EOD
					SELECT * FROM EMPLEADOS 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
						WHERE EMPLEADOS.Documento = '$Documento2' AND 
							PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.Id <> $IdEmpleado;
				EOD;

				$reg = $this->model->buscarEmpleado($query);

				if ($reg) 
					$data['mensajeError'] .= '<strong>' . label('Cedula extranjeria') . '</strong> ' . label('ya existe') . '<br>';
			}

			// if	( empty($data['reg']['FechaExpedicion']) )
			// 	$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de expedición') . '</strong><br>';
		
			// if	( empty($data['reg']['IdCiudadExpedicion']) )
			// 	$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad de expedición') . '</strong><br>';
		
			if	( empty($data['reg']['Apellido1']) )
				$data['mensajeError'] .= label('Debe digitar el primer') . ' <strong>' . label('Apellido') . '</strong><br>';
		
			// if	( empty($data['reg']['Apellido2']) )
			// 	$data['mensajeError'] .= label('Debe digitar el segundo') . ' <strong>' . label('Apellido') . '</strong><br>';
		
			if	( empty($data['reg']['Nombre1']) )
				$data['mensajeError'] .= label('Debe digitar el primer') . ' <strong>' . label('Nombre') . '</strong><br>';
		
			// if	( empty($data['reg']['Nombre2']) )
			// 	$data['mensajeError'] .= label('Debe digitar el segundo') . ' <strong>' . label('Nombre') . '</strong><br>';
		
			if	( empty($data['reg']['FechaNacimiento']) )
				$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de nacimiento') . '</strong><br>';
		
			if	( empty($data['reg']['IdCiudadNacimiento']) )
				$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad de nacimiento') . '</strong><br>';
		
			if	( empty($data['reg']['Genero']) )
				$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Género') . '</strong><br>';
		
			// if	( empty($data['reg']['EstadoCivil']) )
			// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Estado civil') . '</strong><br>';
		
			// if	( empty($data['reg']['FactorRH']) )
			// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Factor RH') . '</strong><br>';
		
			if	( empty($data['reg']['Direccion']) )
				$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Dirección') . '</strong><br>';
		
			// if	( empty($data['reg']['Barrio']) )
			// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Barrio') . '</strong><br>';
		
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

			// if	( empty($data['reg']['TipoEmpleado']) )
			// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de empleado') . '</strong><br>';

			if	( empty($data['reg']['IdCentro']) )
				$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Centro de costos') . '</strong><br>';

			// if	( empty($data['reg']['IdProyecto']) )
			// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Proyecto') . '</strong><br>';

			if	( empty($data['reg']['TipoContrato']) )
				$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de contrato') . '</strong><br>';

			if	( empty($data['reg']['FechaIngreso']) )
				$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de ingreso') . '</strong><br>';

			// if	( empty($data['reg']['FechaPeriodoPrueba']) )
			// 	$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha en período de prueba') . '</strong><br>';

			$reg = getRegistro('PARAMETROS', $data['reg']['TipoContrato']);
			$TipoContrato = $reg['valor'];

			switch ($TipoContrato)
			{
				case 1:  // INDEFINIDO
					$FechaPeriodoPrueba =  date('Y-m-d', strtotime($data['reg']['FechaIngreso'] . ' + 60 days'));

					if ($data['reg']['FechaPeriodoPrueba'] > $FechaPeriodoPrueba)
						$data['mensajeError'] .= '<strong>' . label('Fecha en período de prueba') . '</strong> ' . label('no puede ser posterior a') . ' ' .$FechaPeriodoPrueba . '<br>';
					
					break;

				case 2:  // TERMINO FIJO
					$FechaVencimiento = new DateTime($data['reg']['FechaVencimiento']);
					$FechaIngreso = new DateTime($data['reg']['FechaIngreso']);
					
					$dias = $FechaVencimiento->diff($FechaIngreso)->days;
					$dias = min($dias / 5, 60);
					
					$FechaPeriodoPrueba = date('Y-m-d', strtotime($data['reg']['FechaIngreso'] . ' + ' . intdiv($dias, 1) . ' days'));

					if ($data['reg']['FechaPeriodoPrueba'] > $FechaPeriodoPrueba)
						$data['mensajeError'] .= '<strong>' . label('Fecha en período de prueba') . '</strong> ' . label('no puede ser posterior a') . ' ' . $FechaPeriodoPrueba . '<br>';

					break;

				case 3:  // APRENDIZ SENA
					$FechaVencimiento = new DateTime($data['reg']['FechaVencimiento']);
					$FechaIngreso = new DateTime($data['reg']['FechaIngreso']);
					
					$dias = $FechaVencimiento->diff($FechaIngreso)->days;
					$dias = min(intdiv($dias, 5), 90);

					$FechaPeriodoPrueba = date('Y-m-d', strtotime($data['reg']['FechaIngreso'] . '+ ' . intdiv($dias, 1) . ' days'));

					if ($data['reg']['FechaPeriodoPrueba'] > $FechaPeriodoPrueba)
						$data['mensajeError'] .= '<strong>' . label('Fecha en período de prueba') . '</strong> ' . label('no puede ser posterior a') . ' ' . $FechaPeriodoPrueba . '<br>';

					break;
			}

			if ($TipoContrato <> 1 AND $TipoContrato <> 4)  // INDEFINIDO 
			{
				if	( empty($data['reg']['FechaVencimiento']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de vencimiento') . '</strong><br>';
			}

			if	( empty($data['reg']['ModalidadTrabajo']) )
				$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Modalida de trabajo') . '</strong><br>';

			if	( empty($data['reg']['IdCargo']) )
				$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Cargo') . '</strong><br>';

			if	( $data['reg']['SueldoBasico'] <= 0 )
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
	
				$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'ValorSueldoMinimo'");
				$SueldoMinimo = $reg['valor'];

				if ($data['reg']['TipoContrato'] == 2)  // APRENDIZ SENA
					if ($data['reg']['SueldoBasico'] < $SueldoMinimo) 
						$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Sueldo básico') . '</strong> ' . label('mayor o igual a medio sueldo mínimo legal') . '<br>';
			}

			if	( empty($data['reg']['SubsidioTransporte']) )
				$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Subsidio de transporte') . '</strong><br>';
			
			$reg = getRegistro('PARAMETROS', $data['reg']['SubsidioTransporte']);
			$TipoSubsidioTransporte = $reg['valor'];
			$reg = buscarRegistro('PARAMETROS', "PARAMETROS.Parametro = 'ValorSubsidioTransporte'");
			$SubsidioTransporte = $reg['valor'];

			if ($TipoSubsidioTransporte <> 3)  // NO RECIBE
				if ($data['reg']['SueldoBasico'] > $SueldoMinimo * 2) 
					$data['mensajeError'] .= label('Empleado no tiene derecho a recibir') . ' <strong>' . label('Subsidio de transporte') . '</strong><br>';

			if	( empty($data['reg']['PeriodicidadPago']) )
				$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Periodicidad de pago') . '</strong><br>';
		
			if	( empty($data['reg']['IdEPS']) )
				$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('E.P.S.') . '</strong><br>';
		
			if	( empty($data['reg']['RegimenCesantias']) AND $TipoContrato <> 3 )  // NO APRENDIZ SENA
				$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Régimen cesantías') . '</strong><br>';
			else
			{
				if ($data['reg']['RegimenCesantias'] > 0) 
				{
					$reg = getRegistro('PARAMETROS', $data['reg']['RegimenCesantias']);
					$RegimenCesantias = $reg['valor'];
				}
				else
					$RegimenCesantias = 0;
	
				switch ($RegimenCesantias)
				{
					case 0:  // SIN REGIMEN - APRENDIZ SENA
						if	( ! empty($data['reg']['IdFondoCesantias']) )
							$data['mensajeError'] .= label('No debe seleccionar un') . ' <strong>' . label('Fondo de cesantías') . '</strong><br>';

						if	( ! empty($data['reg']['IdFondoPensiones']) )
							$data['mensajeError'] .= label('No debe seleccionar un') . ' <strong>' . label('Fondo de pensiones') . '</strong><br>';

						if	( ! empty($data['reg']['IdCajaCompensacion']) )
							$data['mensajeError'] .= label('No debe seleccionar una') . ' <strong>' . label('Caja de compensación') . '</strong><br>';

						break;

					case 1:  // FONDO DE CESANTIAS
						if ($data['reg']['FactorPrestacional'] > 0) 
							$data['mensajeError'] .= '<strong>' . label('Factor prestacional') . '</strong> ' . label('debe ser cero') . '<br>';

						if	( empty($data['reg']['IdFondoCesantias']) AND ($TipoContrato == 1 OR $TipoContrato == 2 OR $TipoContrato == 4))
							$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de cesantías') . '</strong><br>';

						// if	( empty($data['reg']['IdFondoPensiones']) )
						// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de pensiones') . '</strong><br>';

						if	( empty($data['reg']['IdCajaCompensacion']) AND ($TipoContrato == 1 OR $TipoContrato == 2 OR $TipoContrato == 4))
							$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Caja de compensación') . '</strong><br>';

						break;

					case 2:  // SALARIO INTEGRAL
						if ($TipoContrato <> 3)  // APRENDIZ SENA
						{
							if ($data['reg']['FactorPrestacional'] < 30 OR $data['reg']['FactorPrestacional'] > 100) 
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Factor prestacional') . '</strong><br>';
						}
						else
						{
							$data['mensajeError'] .= '<strong>' . label('Salario integral') . '</strong> ' . label('no aplica para aprendiz del sena') . '<br>';

							if ($data['reg']['FactorPrestacional'] > 0) 
								$data['mensajeError'] .= '<strong>' . label('Factor prestacional') . '</strong> ' . label('debe ser cero') . '<br>';
						}

						if	( ! empty($data['reg']['IdFondoCesantias']) )
							$data['mensajeError'] .= label('No debe seleccionar un') . ' <strong>' . label('Fondo de cesantías') . '</strong><br>';

						// if	( empty($data['reg']['IdFondoPensiones']) )
						// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de pensiones') . '</strong><br>';

						if	( empty($data['reg']['IdCajaCompensacion']) )
							$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Caja de compensación') . '</strong><br>';

						break;

					case 3:  // REGIMEN TRADICIONAL
						if ($data['reg']['FactorPrestacional'] > 0) 
							$data['mensajeError'] .= '<strong>' . label('Factor prestacional') . '</strong> ' . label('debe ser cero') . '<br>';
						
						if	( ! empty($data['reg']['IdFondoCesantias']) )
							$data['mensajeError'] .= label('No debe seleccionar un') . ' <strong>' . label('Fondo de cesantías') . '</strong><br>';

						// if	( empty($data['reg']['IdFondoPensiones']) )
						// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de pensiones') . '</strong><br>';

						if	( empty($data['reg']['IdCajaCompensacion']) )
							$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Caja de compensación') . '</strong><br>';

						break;
				}
			}

			if	( empty($data['reg']['FormaDePago']) )
				$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Forma de pago') . '</strong><br>';
			else
			{
				$reg = getRegistro('PARAMETROS', $data['reg']['FormaDePago']);
				$FormaDePago = $reg['valor'];
	
				switch ($FormaDePago)
				{
					case 3:  // TRANSFERENCIA BANCARIA
						// if	( empty($data['reg']['IdBanco']) )
						// 	$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Entidad bancaria') . '</strong><br>';

						// if	( empty($data['reg']['TipoCuentaBancaria']) )
						// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de cuenta bancaria') . '</strong><br>';

						// if	( empty($data['reg']['CuentaBancaria']) )
						// 	$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuenta bancaria') . '</strong><br>';

						break;

					default:
						if	( ! empty($data['reg']['IdBanco']) )
							$data['mensajeError'] .= label('No debe seleccionar una') . ' <strong>' . label('Entidad bancaria') . '</strong><br>';

						if	( ! empty($data['reg']['TipoCuentaBancaria']) )
							$data['mensajeError'] .= label('No debe seleccionar un') . ' <strong>' . label('Tipo de cuenta bancaria') . '</strong><br>';

						if	( ! empty($data['reg']['CuentaBancaria']) )
							$data['mensajeError'] .= label('No debe digitar una') . ' <strong>' . label('Cuenta bancaria') . '</strong><br>';

						if	( ! empty($data['reg']['CuentaBancaria2']) )
							$data['mensajeError'] .= label('No debe digitar una') . ' <strong>' . label('Cuenta BBVA-NEQUI') . '</strong><br>';

						break;
				}
			}

			if	( empty($data['reg']['MetodoRetencion']) )
				$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Método de retención') . '</strong><br>';

			if	( $data['reg']['PorcentajeRetencion'] < 0 )
				$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Porcentaje de retención') . '</strong> ' . label('mayor o igual a cero') . '<br>';

			if	( $data['reg']['MayorRetencionFuente'] < 0 )
				$data['mensajeError'] .= label('Debe digitar un valor de ') . ' <strong>' . label('Mayor retención fuente') . '</strong> ' . label('mayor o igual a cero') . '<br>';

			if	( $data['reg']['CuotaVivienda'] < 0 )
				$data['mensajeError'] .= label('Debe digitar un valor de ') . ' <strong>' . label('Cuota de vivienda') . '</strong> ' . label('mayor o igual a cero') . '<br>';

			if	( $data['reg']['SaludYEducacion'] < 0 )
				$data['mensajeError'] .= label('Debe digitar un valor de ') . ' <strong>' . label('Salud y educación') . '</strong> ' . label('mayor o igual a cero') . '<br>';
				
			// if	( empty($data['reg']['PerfilProfesional']) )
			// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Perfíl profesional') . '</strong><br>';

			if (empty($data['mensajeError'])) 
			{
				for ($i = 0; $i < count($logEmpleado); $i++) 
				{ 
					$resp = $this->model->guardarLogEmpleado($logEmpleado[$i]);
				}
			}
		
			return($data);
		}

		public function editar($id)
		{
			if (isset($_REQUEST['Documento']))
			{
				$data = $this->validarDatos($id);

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarEmpleado($data['reg'], $id);

					if ($resp) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
			{
				$data = $this->cargarDatos($id);
				$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function informe()
		{
			$query = <<<EOD
				SELECT PARAMETROS.Id 
					FROM PARAMETROS 
					WHERE PARAMETROS.Parametro = 'EstadoEmpleado' AND 
						PARAMETROS.Detalle = 'ACTIVO';
			EOD;

			$reg = $this->model->buscarEmpleado($query);

			if ($reg) 
				$EmpleadoActivo = $reg['Id'];
			else
				$EmpleadoActivo = 0;

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
			$_SESSION['Lista'] = SERVERURL . '/empleados/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['EMPLEADOS']['Filtro'];

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= 'WHERE (';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Cargo, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PROYECTOS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PROYECTOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			if (empty($query))
				$query = "WHERE EMPLEADOS.Estado = $EmpleadoActivo";
			else
				$query .= ") AND EMPLEADOS.Estado = $EmpleadoActivo";

			$query .= 'ORDER BY ' . $_SESSION['EMPLEADOS']['Orden']; 
			$data['rows'] = $this->model->listarEmpleados($query);
			$this->views->getView($this, 'informe', $data);
		}

		public function importar()
		{
			$data = array();

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['Archivo']['name']) )
				{
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				}
				else
				{
					ini_set('max_execution_time', 0);
					
					$data['mensajeError'] = '';

					$archivo = $_FILES['Archivo']['name'];
		
					if ( copy($_FILES['Archivo']['tmp_name'], $archivo)) 
					{
						if ( file_exists ($archivo) )
						{
							$EstadoEmpleado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ACTIVO'");

							$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];

							$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $Excel->getSheet(0);

							if (isset($_REQUEST['SonNovedades']))
							{
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										// INTERFACE NOVEDADES DE EMPLEADOS
										$Documento = 					$oHoja->getCell('A' . $i)->getCalculatedValue();
										$NombreEmpleado = 				$oHoja->getCell('B' . $i)->getCalculatedValue();
										$FechaNovedad = 				\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('C' . $i)->getCalculatedValue())->format('Y-m-d');
										if ($FechaNovedad = '1970-01-01')
											$FechaNovedad = date('Y-m-d');
										$TipoContrato = 				$oHoja->getCell('D' . $i)->getCalculatedValue();
										$NombreCargo = 					$oHoja->getCell('E' . $i)->getCalculatedValue();
										$Centro = 						$oHoja->getCell('F' . $i)->getCalculatedValue();
										if (is_numeric($centro))
											$Centro = str_pad($Centro, 5, '0', STR_PAD_LEFT);
										$NombreCentro = 				$oHoja->getCell('G' . $i)->getCalculatedValue();
										$Proyecto = 					$oHoja->getCell('H' . $i)->getCalculatedValue();
										$NombreProyecto = 				$oHoja->getCell('I' . $i)->getCalculatedValue();
										$FP = 							$oHoja->getCell('J' . $i)->getCalculatedValue();
										$NombreFP = 					$oHoja->getCell('K' . $i)->getCalculatedValue();
										$EPS = 							$oHoja->getCell('L' . $i)->getCalculatedValue();
										$NombreEPS = 					$oHoja->getCell('M' . $i)->getCalculatedValue();
										$FC = 							$oHoja->getCell('N' . $i)->getCalculatedValue();
										$NombreFC = 					$oHoja->getCell('O' . $i)->getCalculatedValue();
										$CCF = 							$oHoja->getCell('P' . $i)->getCalculatedValue();
										$NombreCCF = 					$oHoja->getCell('Q' . $i)->getCalculatedValue();
										$PorcentajeARL = 				$oHoja->getCell('R' . $i)->getCalculatedValue();
										$TipoCuenta = 					$oHoja->getCell('S' . $i)->getCalculatedValue();
										$CuentaBancaria = 				$oHoja->getCell('T' . $i)->getCalculatedValue();
										$Banco = 						$oHoja->getCell('U' . $i)->getCalculatedValue();
										$NombreBanco = 					$oHoja->getCell('V' . $i)->getCalculatedValue();

										$regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento' AND EMPLEADOS.Estado = $EstadoEmpleado");
										
										if ($regEmpleado)
										{
											$logEmpleado = array();

											$IdEmpleado = $regEmpleado['id'];

											$data['reg'] = array(
												'TipoContrato' => 			$regEmpleado['tipocontrato'],
												'IdCargo' => 				$regEmpleado['idcargo'], 
												'IdCentro' => 				$regEmpleado['idcentro'],
												'IdProyecto' => 			$regEmpleado['idproyecto'],
												'IdFondoPensiones' => 		$regEmpleado['idfondopensiones'],
												'IdEPS' => 					$regEmpleado['ideps'],
												'IdFondoCesantias' => 		$regEmpleado['idfondocesantias'],
												'IdCajaCompensacion' => 	$regEmpleado['idcajacompensacion'],
												'NivelRiesgo' => 			$regEmpleado['nivelriesgo'], 
												'TipoCuentaBancaria' => 	$regEmpleado['tipocuentabancaria'],
												'CuentaBancaria' => 		$regEmpleado['cuentabancaria'],
												'IdBanco' => 				$regEmpleado['idbanco']
											);
	
											if (! empty($TipoContrato))
											{
												$IdTipoContrato = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoContrato' AND PARAMETROS.Detalle = '" . $TipoContrato . "'");

												if ($IdTipoContrato)
												{
													if ($IdTipoContrato <> $regEmpleado['tipocontrato'])
													{
														$Campo = 'TipoContrato [' . $FechaNovedad . ']';
														if ($regEmpleado['tipocontrato'] > 0)
															$ValorAnterior = getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['detalle'];
														else
															$ValorAnterior = '';
														$ValorActual = $TipoContrato;
														$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

														$data['reg']['TipoContrato'] = $IdTipoContrato;
													}
												}
												else
													$data['mensajeError'] = "Empleado <strong>$Documento - $NombreEmpleado</strong> TIPO CONTRATO $TipoContrato no existe.<br>";
											}

											if (! empty($NombreCargo))
											{
												$IdCargo = getId('CARGOS', "CARGOS.Nombre = '" . $NombreCargo . "'");

												if ($IdCargo > 0)
												{
													if ($IdCargo <> $regEmpleado['idcargo'])
													{
														$Campo = 'Cargo [' . $FechaNovedad . ']';
														if ($regEmpleado['idcargo'] > 0)
															$ValorAnterior = getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];
														else
															$ValorAnterior = '';
														$ValorActual = $NombreCargo;
														$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
														
														$data['reg']['IdCargo'] = $IdCargo;
													}
												}
												else
												{
													$query = <<<EOD
														SELECT MAX(CARGOS.Cargo) AS Cargo
															FROM CARGOS;
													EOD;

													$reg = $this->model->leer($query);

													$Cargo = $reg['Cargo'] + 1;

													$query = <<<EOD
														INSERT INTO CARGOS
															(Cargo, Nombre) 
															VALUES ('$Cargo', '$NombreCargo');
													EOD;

													$ok = $this->model->query($query);

													$Campo = 'Cargo [' . $FechaNovedad . ']';
													if ($regEmpleado['idcargo'] > 0)
														$ValorAnterior = getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];
													else
														$ValorAnterior = '';
													$ValorActual = $NombreCargo;
													$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
																											
													$IdCargo = getId('CARGOS', "CARGOS.Cargo = '" . $Cargo . "'");
													$data['reg']['IdCargo'] = $IdCargo;
												}
											}

											if (! empty($Centro))
											{
												$IdCentro = getId('CENTROS', "CENTROS.Centro = '" . $Centro . "'");

												if ($IdCentro > 0)
												{
													if ($IdCentro <> $regEmpleado['idcentro'])
													{
														$Campo = 'Centro [' . $FechaNovedad . ']';
														if ($regEmpleado['idcentro'] > 0)
															$ValorAnterior = getRegistro('CENTROS', $regEmpleado['idcentro'])['nombre'];
														else
															$ValorAnterior = '';
														$ValorActual = $NombreCentro;
														$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

														$data['reg']['IdCentro'] = $IdCentro;
													}
												}
												else
												{
													$query = <<<EOD
														INSERT INTO CENTROS
															(Centro, Nombre) 
															VALUES ('$Centro', '$NombreCentro');
													EOD;

													$ok = $this->model->query($query);

													$Campo = 'Centro [' . $FechaNovedad . ']';
													if ($regEmpleado['idcentro'] > 0)
														$ValorAnterior = getRegistro('CENTROS', $regEmpleado['idcentro'])['nombre'];
													else
														$ValorAnterior = '';
													$ValorActual = $NombreCentro;
													$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
																											
													$IdCentro = getId('CENTROS', "CENTROS.Centro = '" . $Centro . "'");
													$data['reg']['IdCentro'] = $IdCentro;
												}
											}

											if (! empty($Proyecto))
											{
												$IdProyecto = getId('CENTROS', "CENTROS.Centro = '" . $Proyecto . "'");

												if ($IdProyecto > 0)
												{
													if ($IdProyecto <> $regEmpleado['idproyecto'])
													{
														$Campo = 'Proyecto [' . $FechaNovedad . ']';
														if ($regEmpleado['idproyecto'] > 0)
															$ValorAnterior = getRegistro('CENTROS', $regEmpleado['idproyecto'])['nombre'];
														else
															$ValorAnterior = '';
														$ValorActual = $NombreProyecto;
														$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

														$data['reg']['IdProyecto'] = $IdProyecto;
													}
												}
												else
												{
													$query = <<<EOD
														INSERT INTO CENTROS
															(Centro, Nombre) 
															VALUES ('$Proyecto', '$NombreProyecto');
													EOD;

													$ok = $this->model->query($query);

													$Campo = 'Proyecto [' . $FechaNovedad . ']';
													if ($regEmpleado['idproyecto'] > 0)
														$ValorAnterior = getRegistro('CENTROS', $regEmpleado['idproyecto'])['nombre'];
													else
														$ValorAnterior = '';
													$ValorActual = $NombreProyecto;
													$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
																											
													$IdProyecto = getId('CENTROS', "CENTROS.Centro = '" . $Proyecto . "'");
													$data['reg']['IdProyecto'] = $IdProyecto;
												}
											}

											if (! empty($FP))
											{
												$IdFP = getId('TERCEROS', "TERCEROS.Documento = '" . $FP . "'");

												if ($IdFP > 0)
												{
													if ($IdFP <> $regEmpleado['idfondopensiones'])
													{
														$Campo = 'FondoPensiones [' . $FechaNovedad . ']';
														if ($regEmpleado['idfondopensiones'] > 0)
															$ValorAnterior = getRegistro('TERCEROS', $regEmpleado['idfondopensiones'])['nombre'];
														else
															$ValorAnterior = '';
														$ValorActual = $NombreFP;
														$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

														$data['reg']['IdFondoPensiones'] = $IdFP;
													}
												}
												else
												{
													$query = <<<EOD
														INSERT INTO TERCEROS
															(Documento, Nombre, EsAcreedor, EsFondoPensiones) 
															VALUES ('$FP', '$NombreFP', 1, 1);
													EOD;

													$ok = $this->model->query($query);

													$Campo = 'FondoPensiones [' . $FechaNovedad . ']';
													if ($regEmpleado['idfondopensiones'] > 0)
														$ValorAnterior = getRegistro('TERCEROS', $regEmpleado['idfondopensiones'])['nombre'];
													else
														$ValorAnterior = '';
													$ValorActual = $NombreFP;
													$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
																											
													$IdFP = getId('TERCEROS', "TERCEROS.Documento = '" . $FP . "'");
													$data['reg']['IdFondoPensiones'] = $IdFP;
												}
											}

											if (! empty($EPS))
											{
												$IdEPS = getId('TERCEROS', "TERCEROS.Documento = '" . $EPS . "'");

												if ($IdEPS > 0)
												{
													if ($IdEPS <> $regEmpleado['ideps'])
													{
														$Campo = 'EPS [' . $FechaNovedad . ']';
														if ($regEmpleado['ideps'] > 0)
															$ValorAnterior = getRegistro('TERCEROS', $regEmpleado['ideps'])['nombre'];
														else
															$ValorAnterior = '';
														$ValorActual = $NombreEPS;
														$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

														$data['reg']['IdEPS'] = $IdEPS;
													}
												}
												else
												{
													$query = <<<EOD
														INSERT INTO TERCEROS
															(Documento, Nombre, EsAcreedor, EsEPS) 
															VALUES ('$EPS', '$NombreEPS', 1, 1);
													EOD;

													$ok = $this->model->query($query);

													$Campo = 'EPS [' . $FechaNovedad . ']';
													if ($regEmpleado['ideps'] > 0)
														$ValorAnterior = getRegistro('TERCEROS', $regEmpleado['ideps'])['nombre'];
													else
														$ValorAnterior = '';
													$ValorActual = $NombreEPS;
													$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
																											
													$IdEPS = getId('TERCEROS', "TERCEROS.Documento = '" . $EPS . "'");
													$data['reg']['IdEPS'] = $IdEPS;
												}
											}

											if (! empty($FC))
											{
												$IdFC = getId('TERCEROS', "TERCEROS.Documento = '" . $FC . "'");

												if ($IdFC > 0)
												{
													if ($IdFC <> $regEmpleado['idfondocesantias'])
													{
														$Campo = 'FondoCesantias [' . $FechaNovedad . ']';
														if ($regEmpleado['idfondocesantias'] > 0)
															$ValorAnterior = getRegistro('TERCEROS', $regEmpleado['idfondocesantias'])['nombre'];
														else
															$ValorAnterior = '';
														$ValorActual = $NombreFC;
														$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

														$data['reg']['IdFondoCesantias'] = $IdFC;
													}
												}
												else
												{
													$query = <<<EOD
														INSERT INTO TERCEROS
															(Documento, Nombre, EsAcreedeor, EsFondoCesantias) 
															VALUES ('$FC', '$NombreFC', 1, 1);
													EOD;

													$ok = $this->model->query($query);

													$Campo = 'FondoCesantias [' . $FechaNovedad . ']';
													if ($regEmpleado['idfondocesantias'] > 0)
														$ValorAnterior = getRegistro('TERCEROS', $regEmpleado['idfondocesantias'])['nombre'];
													else
														$ValorAnterior = '';
													$ValorActual = $NombreFC;
													$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
																											
													$IdFC = getId('TERCEROS', "TERCEROS.Documento = '" . $FC . "'");
													$data['reg']['IdFondoCesantias'] = $IdFC;
												}
											}

											if (! empty($CCF))
											{
												$IdCCF = getId('TERCEROS', "TERCEROS.Documento = '" . $CCF . "'");

												if ($IdCCF > 0)
												{
													if ($IdCCF <> $regEmpleado['idcajacompensacion'])
													{
														$Campo = 'CCF [' . $FechaNovedad . ']';
														if ($regEmpleado['idcajacompensacion'] > 0)
															$ValorAnterior = getRegistro('TERCEROS', $regEmpleado['idcajacompensacion'])['nombre'];
														else
															$ValorAnterior = '';
														$ValorActual = $NombreCCF;
														$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

														$data['reg']['IdCajaCompensacion'] = $IdCCF;
													}
												}
												else
												{
													$query = <<<EOD
														INSERT INTO TERCEROS
															(Documento, Nombre, EsAcreedor, EsCCF) 
															VALUES ('$CCF', '$NombreCCF', 1, 1);
													EOD;

													$ok = $this->model->query($query);

													$Campo = 'CCF [' . $FechaNovedad . ']';
													if ($regEmpleado['idcajacompensacion'] > 0)
														$ValorAnterior = getRegistro('TERCEROS', $regEmpleado['idcajacompensacion'])['nombre'];
													else
														$ValorAnterior = '';
													$ValorActual = $NombreCCF;
													$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
																											
													$IdCCF = getId('TERCEROS', "TERCEROS.Documento = '" . $CCF . "'");
													$data['reg']['IdCajaCompensacion'] = $IdCCF;
												}
											}

											if (! empty($PorcentajeARL))
											{
												if ($regEmpleado['nivelriesgo'] > 0)
												{
													$reg = getRegistro('PARAMETROS', $regEmpleado['nivelriesgo']);
													$PorcentajeActual = $reg['valor2'];
												}
												else
													$PorcentajeActual = 0;

												if ($PorcentajeARL <> $PorcentajeActual)
												{
													$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'NivelRiesgo' AND PARAMETROS.Valor2 = " . $PorcentajeARL);

													if ($reg)
													{
														$Campo = 'NivelRiesgo [' . $FechaNovedad . ']';
														$ValorAnterior = $PorcentajeActual;
														$ValorActual = $PorcentajeARL;
														$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

														$data['reg']['NivelRiesgo'] = $reg['id'];
													}
													else
														$data['mensajeError'] = "Empleado <strong>$Documento - $NombreEmpleado</strong> tiene un porcentaje de riesgo incorrecto $PorcentajeARL.<br>";
												}
											}

											if (! empty($TipoCuenta))
											{
												$IdTipoCuenta = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoCuentaBancaria' AND PARAMETROS.Detalle = '" . $TipoCuenta . "'");

												if ($IdTipoCuenta > 0)
												{
													if ($IdTipoCuenta <> $regEmpleado['tipocuentabancaria'])
													{
														$Campo = 'TipoCuentabancaria [' . $FechaNovedad . ']';
														if ($regEmpleado['tipocuentabancaria'] > 0)
															$ValorAnterior = getRegistro('PARAMETROS', $regEmpleado['tipocuentabancaria'])['detalle'];
														else
															$ValorAnterior = '';
														$ValorActual = $TipoCuenta;
														$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

														$data['reg']['TipoCuentaBancaria'] = $IdTipoCuenta;
													}
												}
												else
												{
													$data['mensajeError'] = "Empleado <strong>$Documento - $NombreEmpleado</strong> TIPO CUENTA  $TipoCuenta no existe.<br>";
												}
											}

											if (! empty($CuentaBancaria))
											{
												if ($CuentaBancaria <> $regEmpleado['cuentabancaria'])
												{
													$Campo = 'CuentaBancaria [' . $FechaNovedad . ']';
													$ValorAnterior = $regEmpleado['cuentabancaria'];
													$ValorActual = $CuentaBancaria;
													$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

													$data['reg']['CuentaBancaria'] = $CuentaBancaria;
												}
											}

											if (! empty($Banco))
											{
												$IdBanco = getId('BANCOS', "BANCOS.Banco = '" . $Banco . "'");

												if ($IdBanco > 0)
												{
													if ($IdBanco <> $regEmpleado['idbanco'])
													{
														$Campo = 'Banco [' . $FechaNovedad . ']';
														if ($regEmpleado['idbanco'] > 0)
															$ValorAnterior = getRegistro('BANCOS', $regEmpleado['idbanco'])['nombre'];
														else
															$ValorAnterior = '';
														$ValorActual = $NombreBanco;
														$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

														$data['reg']['IdBanco'] = $IdBanco;
													}
												}
												else
												{
													$query = <<<EOD
														INSERT INTO BANCOS
															(banco, Nombre) 
															VALUES ('$Banco', '$NombreBanco');
													EOD;

													$ok = $this->model->query($query);

													$Campo = 'Banco [' . $FechaNovedad . ']';
													if ($regEmpleado['idbanco'] > 0)
														$ValorAnterior = getRegistro('BANCOS', $regEmpleado['idbanco'])['nombre'];
													else
														$ValorAnterior = '';
													$ValorActual = $NombreBanco;
													$logEmpleado[] = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

													$Idbanco = getId('BANCOS', "BANCOS.banco = '" . $Banco . "'");
													$data['reg']['IdBanco'] = $IdBanco;
												}
											}

											if (! empty($logEmpleado))
											{
												for ($j = 0; $j < count($logEmpleado); $j++) 
												{ 
													$resp = $this->model->guardarLogEmpleado($logEmpleado[$j]);
												}

												$resp = $this->model->actualizarNovedadEmpleado($data['reg'], $IdEmpleado);

												unset($logEmpleado);
											}
											else
												$data['mensajeError'] = "Novedades de empleado <strong>$Documento - $NombreEmpleado</strong> ya estaban procesadas.<br>";
										}
										else
										{
											$data['mensajeError'] = "Empleado <strong>$Documento - $NombreEmpleado</strong> no existe.<br>";
										}
									}
								}

								if (empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
							}
							if (isset($_REQUEST['SonRenovaciones']))
							{
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										$Documento 		= $oHoja->getCell('A' . $i)->getCalculatedValue();
										$NombreEmpleado = $oHoja->getCell('B' . $i)->getCalculatedValue();
										$Cargo			= $oHoja->getCell('C' . $i)->getCalculatedValue();
										$Centro			= $oHoja->getCell('D' . $i)->getCalculatedValue();
										$NombreCentro	= $oHoja->getCell('E' . $i)->getCalculatedValue();
										$TipoContrato	= $oHoja->getCell('F' . $i)->getCalculatedValue();
										$SueldoBasico	= $oHoja->getCell('G' . $i)->getCalculatedValue();
										$FechaIngreso 	= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('H' . $i)->getCalculatedValue())->format('Y-m-d');
										
										if ($FechaIngreso == '1970-01-01')
											$FechaIngreso = NULL;
										
										$FechaVencimiento = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('I' . $i)->getCalculatedValue())->format('Y-m-d');

										if ($FechaVencimiento == '1970-01-01')
											$FechaVencimiento = NULL;

										$Prorrogas		= $oHoja->getCell('J' . $i)->getCalculatedValue();
										$Renueva		= $oHoja->getCell('K' . $i)->getCalculatedValue();

										// BUSCAMOS EL EMPLEADO
										$query = <<<EOD
											SELECT EMPLEADOS.Id, 
													EMPLEADOS.SueldoBasico, 
													EMPLEADOS.FechaIngreso, 
													EMPLEADOS.FechaVencimiento 
												FROM EMPLEADOS 
													INNER JOIN PARAMETROS 
														ON EMPLEADOS.Estado = PARAMETROS.Id 
												WHERE EMPLEADOS.Documento = '$Documento' AND 
													PARAMETROS.Detalle = 'ACTIVO';
										EOD;

										$regEmpleado = $this->model->leer($query); 

										if ($regEmpleado)
										{
											$IdEmpleado 			= $regEmpleado['Id'];
											$SueldoBasicoActual 	= $regEmpleado['SueldoBasico'];
											$FechaIngresoActual 	= $regEmpleado['FechaIngreso'];
											$FechaVencimientoActual = $regEmpleado['FechaVencimiento'];
										}
										else
										{
											$data['mensajeError'] .= "Empleado $Documento - $NombreEmpleado no existe<br>";
											continue;
										}

										if (strtoupper(substr($Renueva, 0, 1)) == 'S')
										{
											// HAY RENOVACION SE ACTUALIZAN LOS DATOS
											$IdCargo = getId('CARGOS', "CARGOS.Nombre = '$Cargo'");

											if ($IdCargo == 0)
											{
												$data['mensajeError'] = "Cargo no existe - $Cargo <br>";
												continue;
											}

											if (substr($Centro, 0, 1) == 'S')
											{
												$IdCentro = getId('CENTROS', "CENTROS.Centro = '04099'");

												if ($IdCentro == 0)
												{
													$data['mensajeError'] = "Centro no existe - $Centro <br>";
													continue;
												}

												$IdProyecto = getId('CENTROS', "CENTROS.Centro = '$Centro'");

												if ($IdProyecto == 0)
												{
													$data['mensajeError'] = "Proyecto no existe - $Centro <br>";
													continue;
												}
											}
											else
											{
												$IdCentro = getId('CENTROS', "CENTROS.Centro = '$Centro'");
												$IdProyecto = 0;

												if ($IdCentro == 0)
												{
													$data['mensajeError'] = "Centro no existe - $Centro <br>";
													continue;
												}
											}

											$TipoContrato 	= getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoContrato' AND PARAMETROS.Detalle = '$TipoContrato'");

											if ($TipoContrato == 0)
											{
												$data['mensajeError'] = "Tipo contrato no válido - $TipoContrato <br>";
												continue;
											}

											if ($TipoContrato <> 'INDEFINIDO')
											{
												if (empty($FechaVencimiento))
												{
													$data['mensajeError'] = "Falta definir fecha de vencimiento - $Documento - $NombreEmpleado <br>";
													continue;
												}
											}

											if ($SueldoBasicoActual < $SueldoBasico)
											{
												$data['mensajeError'] = "Sueldo básico nuevo inferior al actual - $Documento - $NombreEmpleado <br>";
												continue;
											}

											if (! empty($FechaVencimiento) AND ! empty($FechaVencimientoActual))
											{
												if ($FechaVencimiento < $FechaVencimientoActual)
												{
													$data['mensajeError'] = "Fecha de vencimiento inferior al actual - $Documento - $NombreEmpleado <br>";
													continue;
												}
											}
											
											$datos = array(
												'IdCargo'			=> $IdCargo, 
												'IdCentro'			=> $IdCentro, 
												'IdProyecto'		=> $IdProyecto, 
												'TipoContrato'		=> $TipoContrato, 
												'SueldoBasico'		=> $SueldoBasico, 
												'FechaVencimiento'	=> $FechaVencimiento, 
												'IdEmpleado'		=> $IdEmpleado
											);

											$query = <<<EOD
												UPDATE EMPLEADOS 
													SET 
														IdCargo				= :IdCargo, 
														IdCentro			= :IdCentro, 
														IdProyecto			= :IdProyecto, 
														TipoContrato 		= :TipoContrato, 
														SueldoBasico 		= :SueldoBasico, 
														FechaVencimiento 	= :FechaVencimiento, 
														Prorrogas 			= EMPLEADOS.Prorrogas + 1
													WHERE EMPLEADOS.Id 		= :IdEmpleado;
											EOD;

											$ok = $this->model->actualizar($query, $datos);
										}
										else
										{
											// NO HAY RENOVACION - SE GRABA LA NOVEDAD DE FINALIZACION DE CONTRATO
											// REVISAR BIEN ESTE PROCESO PUEDE ALTERAR LA LIQUIDACION DE PRENOMINA
											// $Estado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'RETIRADO'");

											// $MotivoRetiro = getId('PARAMETROS', "PARAMETROS.Parametro = 'MotivoRetiro' AND PARAMETROS.Detalle = 'FINALIZACIÓN DE CONTRATO'");

											// $query = <<<EOD
											// 	UPDATE EMPLEADOS 
											// 		SET 
											// 			Estado 				= $Estado, 
											// 			FechaRetiro 		= '$FechaVencimiento', 
											// 			MotivoRetiro 		= $MotivoRetiro, 
											// 			FechaLiquidacion 	= NULL 
											// 		WHERE EMPLEADOS.Id 		= $IdEmpleado;
											// EOD;

											// $ok = $this->model->query($query, $data);
										}
									}
								}
		
								if (empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
							}
							elseif (isset($_REQUEST['SonCentros']))
							{
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										$Documento 		= $oHoja->getCell('A' . $i)->getCalculatedValue();
										$NombreEmpleado = $oHoja->getCell('B' . $i)->getCalculatedValue();
										$Centro 		= $oHoja->getCell('C' . $i)->getCalculatedValue();
										$Proyecto 		= $oHoja->getCell('D' . $i)->getCalculatedValue();
										
										// BUSCAMOS EL EMPLEADO
										$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");
										
										if ($IdEmpleado > 0)
										{
											$Centro = str_pad($Centro, 5, '0', STR_PAD_LEFT);

											$IdCentro = getId('CENTROS', "CENTROS.Centro = '$Centro'");

											if ($IdCentro > 0)
											{
												if (! empty($Proyecto))
												{
													$IdProyecto = getId('CENTROS', "CENTROS.Centro = '$Proyecto'");

													if ($IdProyecto == 0)
													{
														$data['mensajeError'] .= "Proyecto $Proyecto no existe<br>";
														continue;
													}
												}
												else
													$IdProyecto = 0;

												$query = <<<EOD
													UPDATE EMPLEADOS 
														SET IdCentro = $IdCentro, 
															IdProyecto = $IdProyecto  
														WHERE EMPLEADOS.Id = $IdEmpleado;
												EOD;

												$this->model->query($query);
											}
											else
											{
												$data['mensajeError'] .= "Centro $Centro no existe<br>";
												continue;
											}
										}
										else
											$data['mensajeError'] .= "Empleado $Documento - $NombreEmpleado no existe<br>";
									}
									else
										break;
								}
								
								header('Location: ' . SERVERURL . '/empleados/lista/1');
								exit;
							}
							else
							{
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										// INTERFACE MAESTRO DE EMPLEADOS ACTIVOS
										$TipoDocumento 			= trim($oHoja->getCell('A' . $i)->getCalculatedValue());
										$NombreTipoDocumento 	= trim($oHoja->getCell('B' . $i)->getCalculatedValue());
										$Documento 				= trim($oHoja->getCell('C' . $i)->getCalculatedValue());
										$Apellido1 				= trim($oHoja->getCell('D' . $i)->getCalculatedValue());
										$Apellido2 				= trim($oHoja->getCell('E' . $i)->getCalculatedValue());
										if (is_null($Apellido2)) 
											$Apellido2 = '';
										$Nombre1 				= trim($oHoja->getCell('F' . $i)->getCalculatedValue());
										$Nombre2 				= trim($oHoja->getCell('G' . $i)->getCalculatedValue());
										if (is_null($Nombre2)) 
											$Nombre2 = '';
										$CodigoSAP 				= trim($oHoja->getCell('H' . $i)->getCalculatedValue());
										$FechaNacimiento 		= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('I' . $i)->getCalculatedValue())->format('Y-m-d');
										$CiudadNacimiento 		= trim($oHoja->getCell('J' . $i)->getCalculatedValue());
										if ($CiudadNacimiento <= 99999) 
											$CiudadNacimiento = substr('0000' . $CiudadNacimiento, -5);
										$NombreCiudadNacimiento = trim($oHoja->getCell('K' . $i)->getCalculatedValue());
										$Genero 				= trim($oHoja->getCell('L' . $i)->getCalculatedValue());
										$EstadoCivil 			= trim($oHoja->getCell('M' . $i)->getCalculatedValue());
										$FactorRH 				= trim($oHoja->getCell('N' . $i)->getCalculatedValue());
										$Direccion 				= trim($oHoja->getCell('O' . $i)->getCalculatedValue());
										$CiudadResidencia 		= trim($oHoja->getCell('P' . $i)->getCalculatedValue());
										if ($CiudadResidencia <= 99999) 
											$CiudadResidencia = substr('0000' . $CiudadResidencia, -5);
										$NombreCiudadResidencia = trim($oHoja->getCell('Q' . $i)->getCalculatedValue());
										$Email 					= trim($oHoja->getCell('R' . $i)->getCalculatedValue());
										$Telefono 				= trim($oHoja->getCell('S' . $i)->getCalculatedValue());
										if (is_null($Telefono)) 
											$Telefono = '';
										$Celular 				= trim($oHoja->getCell('T' . $i)->getCalculatedValue());
										if (is_null($Celular)) 
											$Celular = '';
										$TipoEmpleado 			= trim($oHoja->getCell('U' . $i)->getCalculatedValue());
										$Centro 				= trim($oHoja->getCell('V' . $i)->getCalculatedValue());
										if (is_numeric($Centro))
											$Centro = str_pad($Centro, 5, '0', STR_PAD_LEFT);
										$NombreCentro 			= trim($oHoja->getCell('W' . $i)->getCalculatedValue());
										$Proyecto 				= trim($oHoja->getCell('X' . $i)->getCalculatedValue());
										$NombreProyecto 		= trim($oHoja->getCell('Y' . $i)->getCalculatedValue());
										$Vicepresidencia 		= trim($oHoja->getCell('Z' . $i)->getCalculatedValue());
										$CiudadTrabajo 			= trim($oHoja->getCell('AA' . $i)->getCalculatedValue());
										$NombreCiudadTrabajo 	= trim($oHoja->getCell('AB' . $i)->getCalculatedValue());
										$Cargo 					= trim($oHoja->getCell('AC' . $i)->getCalculatedValue());
										$NombreCargo 			= trim($oHoja->getCell('AD' . $i)->getCalculatedValue());
										$TipoContrato 			= trim($oHoja->getCell('AE' . $i)->getCalculatedValue());
										$FechaIngreso = 				\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('AF' . $i)->getCalculatedValue())->format('Y-m-d');
										$FechaVencimiento = 			\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('AG' . $i)->getCalculatedValue())->format('Y-m-d');
										$SueldoBasico 			= $oHoja->getCell('AH' . $i)->getCalculatedValue();
										$NitEPS 				= trim($oHoja->getCell('AI' . $i)->getCalculatedValue());
										$NombreEPS 				= trim($oHoja->getCell('AJ' . $i)->getCalculatedValue());
										$NitCCF 				= trim($oHoja->getCell('AK' . $i)->getCalculatedValue());
										$NombreCCF 				= trim($oHoja->getCell('AL' . $i)->getCalculatedValue());
										$NitFC 					= trim($oHoja->getCell('AM' . $i)->getCalculatedValue());
										$NombreFC 				= trim($oHoja->getCell('AN' . $i)->getCalculatedValue());
										$NitFP 					= trim($oHoja->getCell('AO' . $i)->getCalculatedValue());
										$NombreFP 				= trim($oHoja->getCell('AP' . $i)->getCalculatedValue());
										$NitARL 				= trim($oHoja->getCell('AQ' . $i)->getCalculatedValue());
										$NombreARL 				= trim($oHoja->getCell('AR' . $i)->getCalculatedValue());
										$PorcentajeRiesgo 		= $oHoja->getCell('AS' . $i)->getCalculatedValue();
										$RegimenCesantias 		= trim($oHoja->getCell('AT' . $i)->getCalculatedValue());
										$TipoCuentaBancaria 	= trim($oHoja->getCell('AU' . $i)->getCalculatedValue());
										$Banco 					= trim($oHoja->getCell('AV' . $i)->getCalculatedValue());
										$NombreBanco 			= trim($oHoja->getCell('AW' . $i)->getCalculatedValue());
										$CuentaBancaria 		= trim($oHoja->getCell('AX' . $i)->getCalculatedValue());
										$CuentaBancaria2 		= trim($oHoja->getCell('AY' . $i)->getCalculatedValue());
										$MetodoRetencion 		= $oHoja->getCell('AZ' . $i)->getCalculatedValue();
										$CuotaVivienda 			= $oHoja->getCell('BA' . $i)->getCalculatedValue();
										if (is_null($CuotaVivienda)) 
											$CuotaVivienda = 0;
										$SaludYEducacion 		= $oHoja->getCell('BB' . $i)->getCalculatedValue();
										if (is_null($SaludYEducacion)) 
											$SaludYEducacion = 0;
										$DeduccionDependientes 	= $oHoja->getCell('BC' . $i)->getCalculatedValue();
										if (is_null($DeduccionDependientes)) 
											$DeduccionDependientes = 0;

										// BUSCAMOS EL EMPLEADO PARA ADICIONAR
										$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento' AND EMPLEADOS.Estado != '145'");
										
										if ($IdEmpleado > 0)
										{
											$emple = getRegistro('EMPLEADOS',$IdEmpleado);
											$id_estado = $emple['estado'];
											$est_emple = getRegistro('PARAMETROS',$id_estado);
											$est_emple = $est_emple['detalle'];
											$data['mensajeError'] .= "Empleado $Documento - $Apellido1 $Apellido2 $Nombre1 $Nombre2 se encuentra en estado $est_emple, no se procesa el ingreso.<br>";

											continue;
										}

										$errorEmpleado = '';

										switch ($TipoDocumento) 
										{
											case 'CC':
												$TipoIdentificacion = 2;
												break;
											
											case 'NE':
												$TipoIdentificacion = 4;
												break;
												
											case 'NIT':
												$TipoIdentificacion = 3;
												break;
													
											case 'PA':
												$TipoIdentificacion = 8;
												break;
													
											case 'RC':
												$TipoIdentificacion = 6;
												break;

											case 'RIF':
											case 'PPT':
												$TipoIdentificacion = 363;
												break;
													
											case 'TI':
												$TipoIdentificacion = 7;
												break;

											case 'PT':
												$TipoIdentificacion = 407;
												break;
													
											default:
												$TipoIdentificacion = 0;

												$errorEmpleado .= "Empleado $Documento - Tipo de documento No Válido.<br>";
												break;
										}

										$IdCiudadNacimiento = getId('CIUDADES', "CIUDADES.Ciudad = '$CiudadNacimiento'");

										if ($IdCiudadNacimiento == 0)
											$errorEmpleado .= "Empleado $Documento - Ciudad de nacimiento no existe.<br>";

										switch (substr(strtoupper($Genero), 0, 1)) 
										{
											case 'F':
												$Genero = 32;
												break;
											
											case 'M':
												$Genero = 33;
												break;
												
											default:
												$Genero = 0;
												$errorEmpleado .= "Empleado $Documento - Genero No Válido.<br>";
												break;
										}

										switch (substr(strtoupper($EstadoCivil), 0, 1)) 
										{
											case 'S':  // SOLTERO
												$EstadoCivil = 34;
												break;
											
											case 'C':  // CASADO
												$EstadoCivil = 35;
												break;
												
											case 'U':  // UNION LIBRE
												$EstadoCivil = 36;
												break;
													
											case 'V':  // VIUDO
												$EstadoCivil = 37;
												break;
														
											default:
												$EstadoCivil = 0;
												$errorEmpleado .= "Empleado $Documento - Estado civil No Válido.<br>";
												break;
										}

										switch (strtoupper($FactorRH)) 
										{
											case 'A+':
												$FactorRH = 38;
												break;
											
											case 'A-':
												$FactorRH = 39;
												break;
												
											case 'B+':
												$FactorRH = 40;
												break;
											
											case 'B-':
												$FactorRH = 41;
												break;
													
											case 'AB+':
												$FactorRH = 42;
												break;
											
											case 'AB-':
												$FactorRH = 43;
												break;
													
											case 'O+':
											case '0+':
												$FactorRH = 44;
												break;
											
											case 'O-':
											case '0-':
												$FactorRH = 45;
												break;
													
											default:
												$FactorRH = 0;
												$errorEmpleado .= "Empleado $Documento - Factor RH No Válido.<br>";
												break;
										}

										$IdCiudad = getId('CIUDADES', "CIUDADES.Ciudad = '$CiudadResidencia'");

										if ($IdCiudad == 0)
											$errorEmpleado .= "Empleado $Documento - Ciudad de residencia no existe.<br>";

										switch (substr(strtoupper($TipoEmpleado), 0, 1)) 
										{
											case 'A':  // ADMINISTRACION
												$TipoEmpleado = 87;
												break;
											
											case 'C':  // COSTOS
												$TipoEmpleado = 90;
												break;
												
											case 'S':  // SERVICIOS
												$TipoEmpleado = 89;
												break;
												
											case 'V':  // VENTAS
												$TipoEmpleado = 88;
												break;
													
											default:
												$TipoEmpleado = 0;
												$errorEmpleado .= "Empleado $Documento - Tipo empleado No Válido.<br>";
												break;
										}

										if (! is_null($Centro)) 
										{
											$IdCentro = getId('CENTROS', "CENTROS.Centro = '$Centro'");

											// if ($IdCentro == 0) 
											// {
											// 	$query = <<<EOD
											// 		INSERT INTO CENTROS 
											// 			(Centro, Nombre, TipoEmpleado)
											// 			VALUES ('$Centro', '$NombreCentro', $TipoEmpleado); 
											// 	EOD;

											// 	$ok = $this->model->query($query);

											// 	$IdCentro = getId('CENTROS', "CENTROS.Centro = '$Centro'");
											// }
										}
										else
											$IdCentro = 0;

										if ($IdCentro == 0)
											$errorEmpleado .= "Empleado $Documento - Centro de costo No Válido.<br>";

										if (! is_null($Proyecto)) 
										{
											$IdProyecto = getId('CENTROS', "CENTROS.Centro = '$Proyecto'");

											// if ($IdProyecto == 0) 
											// {
											// 	$query = <<<EOD
											// 		INSERT INTO CENTROS 
											// 			(Centro, Nombre, TipoEmpleado)
											// 			VALUES ('$Proyecto', '$NombreProyecto', $TipoEmpleado); 
											// 	EOD;

											// 	$ok = $this->model->query($query);

											// 	$IdProyecto = getId('CENTROS', "CENTROS.Centro = '$Proyecto'");
											// }
										}
										else
											$IdProyecto = 0;

										if ($Vicepresidencia == 'ADMINISTRACION') 
											$Vicepresidencia = 344;
										else
											$Vicepresidencia = 345;

										$IdCiudadTrabajo = getId('CIUDADES', "CIUDADES.Ciudad = '$CiudadTrabajo'");

										if ($IdCiudadTrabajo == 0)
											$errorEmpleado .= "Empleado $Documento - Ciudad de trabajo no existe.<br>";

										if (! is_null($Cargo)) 
										{
											$IdCargo = getId('CARGOS', "CARGOS.Cargo = '$Cargo'");

											// if ($IdCentro == 0) 
											// {
											// 	$query = <<<EOD
											// 		INSERT INTO CENTROS 
											// 			(Centro, Nombre, TipoEmpleado)
											// 			VALUES ('$Centro', '$NombreCentro', $TipoEmpleado); 
											// 	EOD;

											// 	$ok = $this->model->query($query);

											// 	$IdCentro = getId('CENTROS', "CENTROS.Centro = '$Centro'");
											// }
										}
										else
											$IdCargo = 0;

										if ("$IdCargo" == "0")
											$errorEmpleado .= "Empleado $Documento - Cargo No Válido.<br>";

										// $NombreCargo2 = str_replace(' DEL ', '', $NombreCargo);
										// $NombreCargo2 = str_replace(' DE ', '', $NombreCargo2);
										// $NombreCargo2 = str_replace(' EN ', '', $NombreCargo2);

										// $query = <<<EOD
										// 	SELECT CARGOS.Id 
										// 		FROM CARGOS 
										// 		WHERE REPLACE(REPLACE(REPLACE(CARGOS.Nombre, ' EN ', ''), ' DEL ', ''), ' DE ', '') = '$NombreCargo2';
										// EOD;

										// $regCargo = $this->model->query($query);

										// if ($regCargo)
										// 	$IdCargo = $regCargo['Id']; 
										// else
										// {
										// 	$IdCargo = 0;

											// $query = <<<EOD
											// 	INSERT INTO CARGOS
											// 		(Nombre, SueldoMinimo, SueldoMaximo, IdCargoSuperior, PorcentajeARL, IdPerfil, IdCargoBase) 
											// 		VALUES (
											// 			:NombreCargo, 
											// 			0, 0, 0, 0, 0, 0);
											// EOD;

											// $IdCargo = $this->model->adicionar($query, array($NombreCargo));
										// }

										// if ($IdCargo == 0)
										// 	$errorEmpleado .= "Empleado $Documento - Cargo no existe.<br>";

										switch (substr(strtoupper($TipoContrato), 0, 1)) 
										{
											case 'I':  // INDEFINIDO
												$TipoContrato = 142;
												$FechaPeriodoPrueba = date('Y-m-d', strtotime($FechaIngreso . ' + 60 days'));
												break;
											
											case 'T':  // TERMINO FIJO
											case 'F':
												$TipoContrato = 143;
												if ($FechaVencimiento <> '1970-01-01') 
												{
													$Fecha1 = date_create($FechaVencimiento);
													$Fecha2 = date_create($FechaIngreso);
													$dias = date_diff($Fecha1, $Fecha2)->days;
													$dias = min($dias / 5, 60);
													$FechaPeriodoPrueba = date('Y-m-d', strtotime($FechaIngreso . ' + ' . intdiv($dias, 1) . ' days'));
												}
												else
													$FechaPeriodoPrueba = NULL;
												break;
												
											case 'A':  // APRENDIZAJE - ETAPA PRACTICA
												$TipoContrato = 144;
												$FechaPeriodoPrueba = NULL;
												break;
														
											case 'D':  // DE LABOR U OBRA CONTRATADA
											case 'L':
												$TipoContrato = 165;
												$FechaPeriodoPrueba =  date('Y-m-d', strtotime($FechaIngreso . ' + 60 days'));
												break;
														
											default:
												$TipoContrato = 0;
												$FechaPeriodoPrueba = NULL;
												$errorEmpleado .= "Empleado $Documento - Tipo de contrato No Válido.<br>";

												break;
										}

										if ($NitEPS == '0')
											$IdEPS = getId('TERCEROS', "TERCEROS.Nombre = '$NombreEPS' AND TERCEROS.EsEPS = 1");
										else
											$IdEPS = getId('TERCEROS', "TERCEROS.Documento = '$NitEPS'");

										if ($IdEPS == 0)
											$errorEmpleado .= "Empleado $Documento - EPS No Válida.<br>";

										// if ($IdEPS == 0) 
										// {
										// 	$query = <<<EOD
										// 		INSERT INTO TERCEROS 
										// 			(TipoIdentificacion, Documento, Nombre, EsAcreedor, EsEPS, AceptaPoliticaTD)
										// 			VALUES (3, '$NitEPS', '$NombreEPS', 1, 1, 1); 
										// 	EOD;

										// 	$ok = $this->model->query($query);

										// 	$IdEPS = getId('TERCEROS', "TERCEROS.Documento = '$NitEPS'");
										// }

										if ($NitCCF == '0')
											$IdCajaCompensacion = getId('TERCEROS', "TERCEROS.Nombre = '$NombreCCF' AND TERCEROS.EsCCF = 1");
										else
											$IdCajaCompensacion = getId('TERCEROS', "TERCEROS.Documento = '$NitCCF'");

										// if ($IdCajaCompensacion == 0) 
										// {
										// 	$query = <<<EOD
										// 		INSERT INTO TERCEROS 
										// 			(TipoIdentificacion, Documento, Nombre, EsAcreedor, EsCCF, AceptaPoliticaTD)
										// 			VALUES (3, '$NitCCF', '$NombreCCF', 1, 1, 1); 
										// 	EOD;

										// 	$ok = $this->model->query($query);

										// 	$IdCajaCompensacion = getId('TERCEROS', "TERCEROS.Documento = '$NitCCF'");
										// }

										if ($NitFC == '0')
											$IdFondoCesantias = getId('TERCEROS', "TERCEROS.Nombre = '$NombreFC' AND TERCEROS.EsFondoCesantias = 1");
										else
											$IdFondoCesantias = getId('TERCEROS', "TERCEROS.Documento = '$NitFC'");

										// if ($IdFondoCesantias == 0) 
										// {
										// 	$query = <<<EOD
										// 		INSERT INTO TERCEROS 
										// 			(TipoIdentificacion, Documento, Nombre, EsAcreedor, EsFondoCesantias, AceptaPoliticaTD)
										// 			VALUES (3, '$NitFC', '$NombreFC', 1, 1, 1); 
										// 	EOD;

										// 	$ok = $this->model->query($query);

										// 	$IdFondoCesantias = getId('TERCEROS', "TERCEROS.Documento = '$NitFC'");
										// }

										if ($NitFP == '0')
											$IdFondoPensiones = getId('TERCEROS', "TERCEROS.Nombre = '$NombreFP'");
										else
											$IdFondoPensiones = getId('TERCEROS', "TERCEROS.Documento = '$NitFP'");

										// if ($IdFondoPensiones == 0) 
										// {
										// 	$query = <<<EOD
										// 		INSERT INTO TERCEROS 
										// 			(TipoIdentificacion, Documento, Nombre, EsAcreedor, EsFondoPensiones, AceptaPoliticaTD)
										// 			VALUES (3, '$NitFP', '$NombreFP', 1, 1, 1); 
										// 	EOD;

										// 	$ok = $this->model->query($query);

										// 	$IdFondoPensiones = getId('TERCEROS', "TERCEROS.Documento = '$NitFP'");
										// }

										if ($NitARL == '0')
											$IdARL = getId('TERCEROS', "TERCEROS.Nombre = '$NombreARL'");
										else
											$IdARL = getId('TERCEROS', "TERCEROS.Documento = '$NitARL'");

										// if ($IdARL == 0) 
										// {
										// 	$query = <<<EOD
										// 		INSERT INTO TERCEROS 
										// 			(TipoIdentificacion, Documento, Nombre, EsAcreedor, EsARL, AceptaPoliticaTD)
										// 			VALUES (3, '$NitARL', '$NombreARL', 1, 1, 1); 
										// 	EOD;

										// 	$ok = $this->model->query($query);

										// 	$IdARL = getId('TERCEROS', "TERCEROS.Documento = '$NitARL'");
										// }

										if ($PorcentajeRiesgo > 0)
											$NivelRiesgo = getiD('PARAMETROS', "PARAMETROS.Parametro = 'NivelRiesgo' AND PARAMETROS.Valor2 = $PorcentajeRiesgo");
										else
											$NivelRiesgo = 0;

										switch (substr(strtoupper($RegimenCesantias), 0, 1)) 
										{
											case 'F':  // FONDO CESANTIAS
												$RegimenCesantias = 154;
												$FactorPrestacional = 0;
												break;
											
											case 'S':  // SALARIO INTEGRAL
											case 'I':
												$RegimenCesantias = 155;
												$FactorPrestacional = 30;
												break;
												
											default:
												$RegimenCesantias = 0;
												$FactorPrestacional = 0;
												break;
										}

										switch (substr(strtoupper(trim($TipoCuentaBancaria)), 0, 1)) 
										{
											case 'A':  // AHORROS
												$TipoCuentaBancaria = 160;
												break;
											
											case 'C':  // CORRIENTE
												$TipoCuentaBancaria = 161;
												break;
												
											default:
												$TipoCuentaBancaria = 0;
												break;
										}

										if (! empty($Banco))
										{
											if (strlen($Banco) < 2)
												$Banco = substr('0' . $Banco, 0, 2);
												
											$IdBanco = getId('BANCOS', "BANCOS.Banco = '$Banco'");
										}
										else
											$IdBanco = 0;

										// if ($MetodoRetencion == 1) 
											$MetodoRetencion = 221;
										// else
										// 	$MetodoRetencion = 222

										if (strtoupper($DeduccionDependientes) == 'SI') 
											$DeduccionDependientes = 1;
										else
											$DeduccionDependientes = 0;

										$IdCiudadExpedicion = 0;
										$FechaExpedicion = NULL;
										$Estado = 141;
										$Barrio = '';
										$Localidad = '';
										$EmailCorporativo = '';
										$EmailProyecto = '';
										$IdCategoria = 0;
										$IdSede = 0; 
										$PerfilProfesional = '';
										$Prorrogas = 0;
										$ModalidadTrabajo = 148;

										if ($SueldoBasico <= $SueldoMinimo * 2) 
											$SubsidioTransporte = 151;
										else
											$SubsidioTransporte = 153;

										$PeriodicidadPago = 10;
										$HorasMes = getHoursMonth();
										$DiasAno = 360; 
										$LibretaMilitar = '';
										$DistritoMilitar = '';
										$LicenciaConduccion = '';
										$TarjetaProfesional = '';
										$Profesion = '';
										$Educacion = 0;
										$CuentaBancaria2 = '';
										$PorcentajeRetencion = 0;
										$MayorRetencionFuente = 0;
										$PoliticamenteExpuesta = 0;
										$Observaciones = '';
										$FormaDePago = 159;

										if (empty($errorEmpleado))
										{
											$aEmpleado = array(
												'TipoIdentificacion' 	=> $TipoIdentificacion, 
												'Documento' 			=> $Documento, 
												'IdCiudadExpedicion' 	=> $IdCiudadExpedicion, 
												'FechaExpedicion' 		=> $FechaExpedicion, 
												'Apellido1' 			=> str_replace(" ","",$Apellido1), 
												'Apellido2' 			=> str_replace(" ","",$Apellido2), 
												'Nombre1' 				=> str_replace(" ","",$Nombre1), 
												'Nombre2' 				=> str_replace(" ","",$Nombre2), 
												'Estado' 				=> $Estado, 
												'Direccion' 			=> $Direccion, 
												'Barrio' 				=> $Barrio, 
												'Localidad' 			=> $Localidad, 
												'IdCiudad' 				=> $IdCiudad, 
												'Telefono' 				=> $Telefono, 
												'Celular' 				=> $Celular, 
												'Email' 				=> $Email, 
												'EmailCorporativo' 		=> $EmailCorporativo, 
												'EmailProyecto' 		=> $EmailProyecto, 
												'FechaNacimiento' 		=> $FechaNacimiento, 
												'IdCiudadNacimiento' 	=> $IdCiudadNacimiento, 
												'IdCargo' 				=> $IdCargo, 
												'IdCentro' 				=> $IdCentro, 
												'IdSede' 				=> $IdSede, 
												'IdCategoria' 			=> $IdCategoria, 
												'PerfilProfesional' 	=> $PerfilProfesional, 
												'IdCiudadTrabajo' 		=> $IdCiudadTrabajo, 
												'FechaIngreso' 			=> $FechaIngreso, 
												'TipoContrato' 			=> $TipoContrato, 
												'FechaPeriodoPrueba' 	=> $FechaPeriodoPrueba, 
												'FechaVencimiento' 		=> $FechaVencimiento, 
												'Prorrogas' 			=> $Prorrogas, 
												'ModalidadTrabajo' 		=> $ModalidadTrabajo, 
												'SueldoBasico' 			=> $SueldoBasico, 
												'SubsidioTransporte' 	=> $SubsidioTransporte, 
												'PeriodicidadPago' 		=> $PeriodicidadPago, 
												'HorasMes' 				=> $HorasMes, 
												'DiasAno' 				=> $DiasAno, 
												'IdEPS' 				=> $IdEPS, 
												'RegimenCesantias' 		=> $RegimenCesantias, 
												'IdFondoCesantias' 		=> $IdFondoCesantias, 
												'FactorPrestacional' 	=> $FactorPrestacional, 
												'IdFondoPensiones' 		=> $IdFondoPensiones, 
												'IdCajaCompensacion' 	=> $IdCajaCompensacion, 
												'IdARL' 				=> $IdARL, 
												'NivelRiesgo' 			=> $NivelRiesgo, 
												'Genero' 				=> $Genero, 
												'EstadoCivil' 			=> $EstadoCivil, 
												'LibretaMilitar' 		=> $LibretaMilitar, 
												'DistritoMilitar' 		=> $DistritoMilitar, 
												'LicenciaConduccion' 	=> $LicenciaConduccion, 
												'TarjetaProfesional' 	=> $TarjetaProfesional, 
												'FactorRH' 				=> $FactorRH, 
												'Profesion' 			=> $Profesion, 
												'Educacion' 			=> $Educacion, 
												'FormaDePago' 			=> $FormaDePago, 
												'IdBanco' 				=> $IdBanco, 
												'CuentaBancaria' 		=> $CuentaBancaria, 
												'CuentaBancaria2' 		=> $CuentaBancaria2, 
												'TipoCuentaBancaria' 	=> $TipoCuentaBancaria, 
												'MetodoRetencion' 		=> $MetodoRetencion, 
												'PorcentajeRetencion' 	=> $PorcentajeRetencion, 
												'MayorRetencionFuente' 	=> $MayorRetencionFuente, 
												'CuotaVivienda' 		=> $CuotaVivienda, 
												'SaludYEducacion' 		=> $SaludYEducacion, 
												'DeduccionDependientes' => $DeduccionDependientes, 
												'PoliticamenteExpuesta' => $PoliticamenteExpuesta, 
												'Observaciones' 		=> $Observaciones, 
												'GrupoPoblacional' 		=> 0, 
												'Vicepresidencia' 		=> $Vicepresidencia, 
												'CodigoSAP' 			=> $CodigoSAP, 
												'IdProyecto' 			=> $IdProyecto);

											$this->model->guardarEmpleado($aEmpleado);
										}
										else
											$data['mensajeError'] .= $errorEmpleado;
									}
								}
								
								if (empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/empleados/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/empleados/lista/' . $_SESSION['EMPLEADOS']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}

		public function transferir($TipoTransferencia)
		{
			ini_set('max_execution_time', 0);

			switch ($TipoTransferencia)
			{
				case 1:

					// DESCOMENTAR SI SE QUIERE UNA TRANSFERENCIA LIMIPA DESDE CERO
					// $query = <<<EOD
					// 	TRUNCATE TABLE Nomina.CENTROS;
					// 	TRUNCATE TABLE Nomina.CARGOS;
					// EOD;

					// $ok = $this->model->query($query);

					$query = <<<EOD
						SELECT RECURSOHUMANO.NumIdentificacion AS Documento, VICEPRESIDENCIA.Nombre AS Vicepresidencia, RECURSOHUMANO.CodigoCentroCosto AS Centro, CENTROCOSTO.Nombre AS NombreCentro, RECURSOHUMANO.CodigoCiudadLabor AS CiudadTrabajo, RECURSOHUMANO.CodigoCiudadResidencia AS CiudadResidencia, RECURSOHUMANO.CodigoCiudadNacimiento AS CiudadNacimiento, RECURSOHUMANO.CodigoCiudadExpedicion AS CiudadExpedicion, RECURSOHUMANO.IdCargo, CARGO.Nombre AS Cargo, ESTADOCIVIL.Nombre AS EstadoCivil, RECURSOHUMANO.CodigoFondoSalud AS EPS, FONDOSALUD.Nombre AS NombreEPS, FONDOPENSIONES.Nombre AS FondoPensiones, CAJACOMPENSACION.Nit AS CajaCompensacion, CAJACOMPENSACION.Nombre AS NombreCajaCompensacion, CAJACOMPENSACION.Municipio AS CiudadCCF, FONDOCESANTIAS.Nombre AS FondoCesantias, TIPOCONTRATO.Nombre AS TipoContrato, RECURSOHUMANO.IdClaseSalarial AS RegimenCesantias, GRUPOSANGUINEO.Tipo AS FactorRH, GENERO.Sexo AS Genero, NIVELEDUCATIVO.Nombre AS NivelAcademico, PROFESION.Nombre AS Profesion, RECURSOHUMANO.IdSede, SEDE.Nombre AS NombreSede, RECURSOHUMANO.Nombres, RECURSOHUMANO.Apellidos, RECURSOHUMANO.Observaciones, RECURSOHUMANO.FechaIngreso, RECURSOHUMANO.FechaFinalizacionContrato AS FechaVencimiento, RECURSOHUMANO.Prorrogas, RECURSOHUMANO.CorreoCorporativo, RECURSOHUMANO.CorreoPersonal, RECURSOHUMANO.EstadoLaboral, RECURSOHUMANO.FechaNacimiento, RECURSOHUMANO.FechaExpedicionCC, RECURSOHUMANO.TelefonoFijo, RECURSOHUMANO.TelefonoCelular, RECURSOHUMANO.Direccion, RECURSOHUMANO.Salario, RECURSOHUMANO.AnticipoComision, RECURSOHUMANO.Carnet, RECURSOHUMANO.Localidad, RECURSOHUMANO.Barrio, NIVEL_RIESGO.Riesgo, RECURSOHUMANO.Talla, RECURSOHUMANO.CorreoProyecto, RECURSOHUMANO.MotivoRetiro
						FROM RRHH.dbo.RECURSOHUMANO AS RECURSOHUMANO   
							LEFT JOIN RRHH.dbo.VICEPRESIDENCIA AS VICEPRESIDENCIA  ON RECURSOHUMANO.IdVicepresidencia = VICEPRESIDENCIA.Codigo 
							LEFT JOIN  RRHH.dbo.CENTROCOSTO AS CENTROCOSTO ON RECURSOHUMANO.CodigoCentroCosto = CENTROCOSTO.Codigo 
							LEFT JOIN  RRHH.dbo.CARGO AS CARGO ON RECURSOHUMANO.IdCargo = CARGO.Id 
							LEFT JOIN  RRHH.dbo.ESTADOCIVIL AS ESTADOCIVIL ON RECURSOHUMANO.IdEstadoCivil = ESTADOCIVIL.Id 
							LEFT JOIN  RRHH.dbo.FONDOSALUD AS FONDOSALUD ON RECURSOHUMANO.CodigoFondoSalud = FONDOSALUD.Codigo 
							LEFT JOIN  RRHH.dbo.FONDOPENSIONES AS FONDOPENSIONES ON RECURSOHUMANO.IdFondoPensiones = FONDOPENSIONES.Id 
							LEFT JOIN  RRHH.dbo.CAJACOMPENSACION AS CAJACOMPENSACION ON RECURSOHUMANO.IdCajaCompensacion = CAJACOMPENSACION.Id
							LEFT JOIN  RRHH.dbo.FONDOCESANTIAS AS FONDOCESANTIAS ON RECURSOHUMANO.IdFondoCesantias = FONDOCESANTIAS.Id 
							LEFT JOIN  RRHH.dbo.TIPOCONTRATO AS TIPOCONTRATO ON RECURSOHUMANO.IdTipoContrato = TIPOCONTRATO.Id 
							LEFT JOIN  RRHH.dbo.GRUPOSANGUINEO AS GRUPOSANGUINEO ON RECURSOHUMANO.IdGrupoSanguineo = GRUPOSANGUINEO.Id 
							LEFT JOIN  RRHH.dbo.GENERO AS GENERO ON RECURSOHUMANO.IdGenero = GENERO.Id 
							LEFT JOIN  RRHH.dbo.NIVELEDUCATIVO AS NIVELEDUCATIVO ON RECURSOHUMANO.IdNivelEducativo = NIVELEDUCATIVO.Id 
							LEFT JOIN  RRHH.dbo.PROFESION AS PROFESION ON RECURSOHUMANO.IdProfesion = PROFESION.Id 
							LEFT JOIN  RRHH.dbo.SEDE AS SEDE ON RECURSOHUMANO.IdSede = SEDE.Id 
							LEFT JOIN  RRHH.dbo.NIVEL_RIESGO AS NIVEL_RIESGO ON RECURSOHUMANO.IdNivelRiesgo = NIVEL_RIESGO.Id 
						ORDER BY RECURSOHUMANO.NumIdentificacion;
					EOD;

					$empleados = $this->model->listar($query);

					for ($i = 0; $i < count($empleados); $i++) 
					{ 
						$regEmpleado = $empleados[$i];

						$Documento = $regEmpleado['Documento'];
						$Vicepresidencia = getId('PARAMETROS', "PARAMETROS.Parametro = 'Vicepresidencia' AND PARAMETROS.Detalle = '" . $regEmpleado['Vicepresidencia'] . "'");
						
						$IdCentro = getId('CENTROS', "CENTROS.Centro = '" . $regEmpleado['Centro'] . "'");

						if ($IdCentro == 0)
						{
							$Centro = $regEmpleado['Centro'];
							$NombreCentro = $regEmpleado['NombreCentro'];

							$query = <<<EOD
								INSERT INTO CENTROS 
									(Centro, Nombre) 
									VALUES('$Centro', '$NombreCentro');
							EOD;

							$ok = $this->model->query($query);

							$IdCentro = getId('CENTROS', "CENTROS.Centro = '" . $regEmpleado['Centro'] . "'");
						}

						if ($regEmpleado['CiudadTrabajo'] == '76994') 
							$IdCiudadTrabajo = 396;
						else
							$IdCiudadTrabajo = getId('CIUDADES', "CIUDADES.Ciudad = '" . $regEmpleado['CiudadTrabajo'] . "'");

						if ($regEmpleado['CiudadResidencia'] == '76994') 
							$IdCiudad = 396;
						else
							$IdCiudad = getId('CIUDADES', "CIUDADES.Ciudad = '" . $regEmpleado['CiudadResidencia'] . "'");

						if ($regEmpleado['CiudadNacimiento'] == '76994') 
							$IdCiudadNacimiento = 396;
						else
							$IdCiudadNacimiento = getId('CIUDADES', "CIUDADES.Ciudad = '" . $regEmpleado['CiudadNacimiento'] . "'");

						if ($regEmpleado['CiudadExpedicion'] == '76994') 
							$IdCiudadExpedicion = 396;
						else
							$IdCiudadExpedicion = getId('CIUDADES', "CIUDADES.Ciudad = '" . $regEmpleado['CiudadExpedicion'] . "'");
						
						$IdCargo = getId('CARGOS', "CARGOS.Nombre = '" . $regEmpleado['Cargo'] . "'");

						if ($IdCargo == 0)
						{
							$Cargo = $regEmpleado['IdCargo'];
							$NombreCargo = $regEmpleado['Cargo'];

							$query = <<<EOD
								INSERT INTO CARGOS 
									(Cargo, Nombre) 
									VALUES('$IdCargo', '$NombreCargo');
							EOD;

							$ok = $this->model->query($query);

							$IdCargo = getId('CARGOS', "CARGOS.Nombre = '" . $regEmpleado['Cargo'] . "'");
						}

						switch ($regEmpleado['EstadoCivil'])
						{
							case 'SOLTERO (A)':
								$EstadoCivil = 'SOLTERO';
								break;
							case 'CASADO (A)':
								$EstadoCivil = 'CASADO';
								break;
							case 'UNIÓN LIBRE':
								$EstadoCivil = 'UNIÓN LIBRE';
								break;
							case 'VIUDO (A)':
								$EstadoCivil = 'VIUDO';
								break;
							default:
								$EstadoCivil = 'SOLTERO';
						}

						$EstadoCivil = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoCivil' AND PARAMETROS.Detalle = '" . $EstadoCivil . "'");

						$IdEPS = getId('TERCEROS', "TERCEROS.Documento = '" . $regEmpleado['EPS'] . "'");

						if ($IdEPS == 0)
						{
							$EPS = $regEmpleado['EPS'];
							$NombreEPS = $regEmpleado['NombreEPS'];

							$query = <<<EOD
								INSERT INTO TERCEROS  
									(Documento, Codigo, Nombre, EsAcreedor, EsEPS, AceptaPoliticaTD) 
									VALUES('$EPS', '$EPS', '$NombreEPS', 1, 1, 1);
							EOD;

							$ok = $this->model->query($query);

							$IdEPS = getId('TERCEROS', "TERCEROS.Nombre = '" . $regEmpleado['NombreEPS'] . "'");
						}

						switch ($regEmpleado['FondoPensiones'])
						{
							case 'COLPENSIONES':
								$IdFondoPensiones = 1;
								break;
							case 'F DE PENSIONES OBL COLFONDOS':
								$IdFondoPensiones = 2;
								break;
							case 'OLD MUTUAL':
								$IdFondoPensiones = 3;
								break;
							case 'PENSION OBL PORVENIR S.A.':
								$IdFondoPensiones = 4;
								break;
							case 'PROTECCION F PENSIONES OBL':
								$IdFondoPensiones = 5;
								break;
							case 'NO APLICA':
								$IdFondoPensiones = 6;
								break;
							default:
								$IdFondoPensiones = 0;
						}

						$IdCajaCompensacion = getId('TERCEROS', "TERCEROS.Documento = '" . str_replace('.', '', $regEmpleado['CajaCompensacion']) . "'");

						if ($IdCajaCompensacion == 0) 
						{
							$CajaCompensacion = str_replace('.', '', $regEmpleado['CajaCompensacion']);
							$NombreCajaCompensacion = $regEmpleado['NombreCajaCompensacion'];
							$CiudadCCF = $regEmpleado['CiudadCCF'];

							$query = <<<EOD
								INSERT INTO TERCEROS  
									(Documento, Codigo, Nombre, Direccion, EsAcreedor, EsCCF, AceptaPoliticaTD) 
									VALUES('$CajaCompensacion', '$CajaCompensacion', '$NombreCajaCompensacion', '$CiudadCCF', 1, 1, 1);
							EOD;

							$ok = $this->model->query($query);

							$IdCajaCompensacion = getId('TERCEROS', "TERCEROS.Documento = '" . str_replace('.', '', $regEmpleado['CajaCompensacion']) . "'");
						}

						switch ($regEmpleado['FondoCesantias'])
						{
							case 'COLFONDOS (CESAN)':
								$IdFondoCesantias = 2;
								break;
							case 'FONDO NAL DEL AHORRO':
								$IdFondoCesantias = 7;
								break;
							case 'OLD MUTUAL (CESAN)':
								$IdFondoCesantias = 3;
								break;
							case 'PORVENIR (CESAN)':
								$IdFondoCesantias = 4;
								break;
							case 'PROTECCION (CESAN)':
								$IdFondoCesantias = 5;
								break;
							case 'NO APLICA':
								$IdFondoCesantias = 6;
								break;
							default:
								$IdFondoCesantias = 0;
						}

						$TipoContrato = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoContrato' AND PARAMETROS.Detalle = '" . $regEmpleado['TipoContrato'] . "'");

						if ($regEmpleado['RegimenCesantias'] == 2)
							$RegimenCesantias = 154;
						else
							$RegimenCesantias = 155; 

						$FactorRH = getId('PARAMETROS', "PARAMETROS.Parametro = 'FactorRH' AND PARAMETROS.Detalle = '" . $regEmpleado['FactorRH'] . "'");

						$Genero = getId('PARAMETROS', "PARAMETROS.Parametro = 'Genero' AND PARAMETROS.Detalle = '" . $regEmpleado['Genero'] . "'");

						switch ($regEmpleado['NivelAcademico'])
						{
							case 'TECNICO':
								$NivelAcademico = 51;
								break;
							case 'TECNOLOGO':
								$NivelAcademico = 52;
								break;
							case 'ESPECIALISTA':
								$NivelAcademico = 54;
								break;
							case 'MASTER':
								$NivelAcademico = 55;
								break;
							case 'PROFESIONAL':
								$NivelAcademico = 53;
								break;
							case 'BACHILLER':
								$NivelAcademico = 49;
								break;
							case 'PRIMARIA':
								$NivelAcademico = 48;
								break;
							case 'ESTUDIANTE':
								$NivelAcademico = 48;
								break;
						}

						$Profesion = $regEmpleado['Profesion'];

						$IdSede = getId('SEDES', "SEDES.Sede = '" . $regEmpleado['IdSede'] . "'");

						if ($IdSede == 0) 
						{
							$Sede = str_replace('.', '', $regEmpleado['IdSede']);
							$NombreSede = $regEmpleado['NombreSede'];

							$query = <<<EOD
								INSERT INTO SEDES   
									(Sede, Nombre) 
									VALUES('$Sede', '$NombreSede');
							EOD;

							$ok = $this->model->query($query);

							$IdSede = getId('SEDES', "SEDES.Sede = '" . $regEmpleado['IdSede'] . "'");
						}

						$aNombres = explode(' ', $regEmpleado['Nombres']);
						$Nombre1 = str_replace(" ","",$aNombres[0]);
						$Nombre2 = (count($aNombres) > 1 ? str_replace(" ","",$aNombres[1]) : '');
						$aApellidos = explode(' ', $regEmpleado['Apellidos']);
						$Apellido1 = str_replace(" ","",$aApellidos[0]);
						$Apellido2 = (count($aApellidos) > 1 ? str_replace(" ","",$aApellidos[1]) : '');
						$Observaciones = $regEmpleado['Observaciones'];
						$FechaIngreso = $regEmpleado['FechaIngreso'];
						$FechaVencimiento = is_null($regEmpleado['FechaVencimiento']) ? NULL : $regEmpleado['FechaVencimiento'];
						$Prorrogas = is_null($regEmpleado['Prorrogas']) ? 0 : $regEmpleado['Prorrogas'];
						$EmailCorporativo = strtolower($regEmpleado['CorreoCorporativo']);
						$Email = strtolower($regEmpleado['CorreoPersonal']);
						$Estado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = '" . ($regEmpleado['EstadoLaboral'] == 0 ? 'RETIRADO' : 'ACTIVO') . "'");
						$FechaRetiro = ($regEmpleado['EstadoLaboral'] == 1 ? NULL : $FechaVencimiento);
						$FechaNacimiento = $regEmpleado['FechaNacimiento'];
						$FechaExpedicion = $regEmpleado['FechaExpedicionCC'];
						$Telefono = $regEmpleado['TelefonoFijo'];
						$Celular = $regEmpleado['TelefonoCelular'];
						$Direccion = $regEmpleado['Direccion'];
						$SueldoBasico = $regEmpleado['Salario'];
						$SubsidioTransporte = ($SueldoBasico > 2000000 ? 153 : 151);
						$Localidad = $regEmpleado['Localidad'];
						$Barrio = $regEmpleado['Barrio'];
						$EmailProyecto = strtolower($regEmpleado['CorreoProyecto']);

						switch ($regEmpleado['MotivoRetiro'])
						{
							case 'ABANDONO DE CARGO':
							case 'ABANDONO DE CARGO ';
								$MotivoRetiro = 255;
								break;
							case 'CON JUSTA CAUSA':
							case 'CON JUSTA CAUSA ':
								$MotivoRetiro = 256;
								break;
							case 'CON JUSTA CAUSA-PENSION DE VEJEZ':
								$MotivoRetiro = 259;
								break;
							case 'FALLECIMIENTO DEL FUNCIONARIO':
							case 'FALLECIMIENTO DEL FUNCIONARIO ':
								$MotivoRetiro = 260;
								break;
							case 'MUTUO ACUERDO':
								$MotivoRetiro = 258;
								break;
							case 'PERIODO DE PRUEBA':
							case 'PERIODO DE PRUEBA ':
								$MotivoRetiro = 261;
								break;
							case 'RENUNCIA':
							case 'RENUNCIA ':
							case 'RENUNCIA  ':
							case 'RENUNCIA-NO CONTRATAR':
							case 'RENUNCIA-NO VOLVER A CONTRATAR':
								$MotivoRetiro = 254;
								break;
							case 'SIN JUSTA CAUSA':
							case 'SIN JUSTA CAUSA ':
							case 'SIN MOTIVO':
								$MotivoRetiro = 257;
								break;
							case 'TERMINACION DE CONTRATO':
							case 'TERMINACION DE CONTRATO ':
							case 'TERMINACION DE CONTRATO  ':
								$MotivoRetiro = 262;
								break;
							default:
								$MotivoRetiro = 0;
						}

						// BUSCAMOS EL EMPLEADO PARA ADICIONAR O ACTUALIZAR
						$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");
						$horasMes = getHoursMonth();
						if ($IdEmpleado > 0) 
						{
							$query = <<<EOD
								UPDATE EMPLEADOS
									SET 
										IdCiudadExpedicion = $IdCiudadExpedicion, 
										Apellido1 = '$Apellido1', 
										Apellido2 = '$Apellido2', 
										Nombre1 = '$Nombre1', 
										Nombre2 = '$Nombre2', 
										Estado = $Estado, 
										Direccion = '$Direccion', 
										Barrio = '$Barrio', 
										Localidad = '$Localidad', 
										IdCiudad = $IdCiudad, 
										Telefono = '$Telefono', 
										Celular = '$Celular', 
										Email = '$Email', 
										EmailCorporativo = '$EmailCorporativo', 
										EmailProyecto = '$EmailProyecto', 
										FechaNacimiento = '$FechaNacimiento', 
										IdCiudadNacimiento = $IdCiudadNacimiento, 
										IdCargo = $IdCargo, 
										IdCentro = $IdCentro, 
										IdSede = $IdSede, 
										IdCiudadTrabajo = $IdCiudadTrabajo, 
										FechaIngreso = '$FechaIngreso', 
										TipoContrato = $TipoContrato, 
										FechaVencimiento = '$FechaVencimiento', 
										Prorrogas = $Prorrogas, 
										ModalidadTrabajo = 148, 
										SueldoBasico = $SueldoBasico, 
										SubsidioTransporte = $SubsidioTransporte, 
										PeriodicidadPago = 10, 
										HorasMes = $horasMes, 
										DiasAno = 360, 
										IdEPS = $IdEPS, 
										RegimenCesantias = $RegimenCesantias, 
										IdFondoCesantias = $IdFondoCesantias, 
										IdFondoPensiones = $IdFondoPensiones, 
										IdCajaCompensacion = $IdCajaCompensacion, 
										Genero = $Genero, 
										EstadoCivil = $EstadoCivil, 
										FactorRH = $FactorRH, 
										Profesion = '$Profesion', 
										Educacion = $NivelAcademico, 
										FormaDePago = 159, 
										FechaRetiro = '$FechaRetiro', 
										MotivoRetiro = $MotivoRetiro, 
										Observaciones = '$Observaciones' 
									WHERE EMPLEADOS.Id = $IdEmpleado;
							EOD;
						}
						else
						{
							$query = <<<EOD
								INSERT INTO EMPLEADOS
									(Documento, IdCiudadExpedicion, FechaExpedicion, Apellido1, Apellido2, Nombre1, Nombre2, Estado, Direccion, Barrio, Localidad, IdCiudad, Telefono, Celular, Email, EmailCorporativo, EmailProyecto, FechaNacimiento, IdCiudadNacimiento, IdCargo, Vicepresidencia, IdSede, IdCentro, IdCiudadTrabajo, FechaIngreso, TipoContrato, FechaVencimiento, Prorrogas, ModalidadTrabajo, SueldoBasico, SubsidioTransporte, PeriodicidadPago, HorasMes, DiasAno, IdEPS, RegimenCesantias, IdFondoCesantias, IdFondoPensiones, IdCajaCompensacion, Genero, EstadoCivil, FactorRH, Profesion, FormaDePago, MetodoRetencion, FechaRetiro, MotivoRetiro, AceptaPoliticaTD, Observaciones)
									VALUES (
										'$Documento', 
										$IdCiudadExpedicion, 
										'$FechaExpedicion', 
										'$Apellido1', 
										'$Apellido2', 
										'$Nombre1', 
										'$Nombre2', 
										$Estado, 
										'$Direccion', 
										'$Barrio', 
										'$Localidad', 
										$IdCiudad, 
										'$Telefono', 
										'$Celular', 
										'$Email', 
										'$EmailCorporativo', 
										'$EmailProyecto', 
										'$FechaNacimiento', 
										$IdCiudadNacimiento, 
										$IdCargo, 
										$Vicepresidencia, 
										$IdSede, 
										$IdCentro, 
										$IdCiudadTrabajo, 
										'$FechaIngreso', 
										$TipoContrato, 
										'$FechaVencimiento', 
										$Prorrogas, 
										148, 
										$SueldoBasico, 
										$SubsidioTransporte,
										10, 
										$horasMes, 
										360, 
										$IdEPS, 
										$RegimenCesantias, 
										$IdFondoCesantias, 
										$IdFondoPensiones, 
										$IdCajaCompensacion, 
										$Genero, 
										$EstadoCivil, 
										$FactorRH, 
										'$Profesion', 
										159, 
										221, 
										'$FechaRetiro', 
										$MotivoRetiro, 
										1, 
										'$Observaciones');
							EOD;
						}

						$this->model->query($query);
					}

					break;

				case 2:
					$query = <<<EOD
						SELECT CUENTABANCARIA.IdRecursoHumano AS Documento, 
								BANCO.Nombre AS Banco, 
								CUENTABANCARIA.IdTipoPago AS TipoCuenta, 
								CUENTABANCARIA.NumCuenta AS Cuenta
							FROM RRHH.dbo.CUENTABANCARIA AS CUENTABANCARIA 
							 	LEFT JOIN RRHH.dbo.BANCO AS BANCO 
									ON CUENTABANCARIA.IdBanco = BANCO.Id 
							WHERE CUENTABANCARIA.NumCuenta IS NOT NULL;
					EOD;

					$empleados = $this->model->listar($query);

					for ($i = 0; $i < count($empleados); $i++) 
					{
						$regEmpleado = $empleados[$i];

						$Documento = $regEmpleado['Documento'];
						$Banco = $regEmpleado['Banco'];
						$TipoCuenta = ($regEmpleado['TipoCuenta'] == 0 ? 160 : 161);
						$Cuenta = trim($regEmpleado['Cuenta']);

						switch ($Banco)
						{
							case 'BANCO BBVA':
								$IdBanco = 9;
								break;
							case 'BANCOLOMBIA':
								$IdBanco = 5;
								break;
							case 'BANCO ITAU':
								$IdBanco = 4;
								break;
							case 'BANCO CAJA SOCIAL':
								$IdBanco = 14;
								break;
							case 'BANCO BOGOTA':
								$IdBanco = 2;
								break;
							case 'BANCO DAVIVIENDA':
								$IdBanco = 16;
								break;
							case 'COLPATRIA':
								$IdBanco = 11;
								break;
							case 'NEQUI-BANCOLOMBIA':
								$IdBanco = 28;
								break;
							case 'BANCO-FALABELLA':
								$IdBanco = 23;
								break;
							case 'HSBC':
								$IdBanco = 8;
								break;
							default:
								$IdBanco = getId('BANCOS', "BANCOS.Nombre = '" . $regEmpleado['Banco'] . "'");
						}

						if ($IdBanco == 0) 
						{
							echo 'Banco no existe ' . $regEmpleado['Banco'] . '<br>';
						}
						else
						{
							$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");

							if ($IdEmpleado > 0) 
							{
								$query = <<<EOD
									UPDATE EMPLEADOS
										SET
											FormaDePago = 159,
											IdBanco = $IdBanco, 
											CuentaBancaria = '$Cuenta',
											TipoCuentaBancaria = $TipoCuenta 
										WHERE EMPLEADOS.Documento = '$Documento';
								EOD;

								$this->model->query($query);
							}
							else
							{
								echo 'Empleado no existe ' . $Docuemnto . '<br>';
							}
						}
					}
			}

			header('Location: ' . SERVERURL . '/empleados/lista/1');
			exit;
		}

		// FILTRAR EL EMPLEADO QUE ESTADO SEA ACTIVO DE AQUI EN ADELANTE
		public function importarCodigosSAP()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				else
				{
					ini_set('max_execution_time', 6000);
					
					$archivo = $_FILES['archivo']['name'];

					if	(empty($_FILES['archivo']['tmp_name']))
						$data['mensajeError'] = "Archivo no pudo ser cargado<br>";
					else
					{
						if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
						{
							if ( file_exists ($archivo) )
							{
								$EstadoEmpleado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ACTIVO'");
	
								$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
								$oHoja = $Excel->getSheet(0);
			
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										$Documento 		= $oHoja->getCell('A' . $i)->getCalculatedValue();
										$NombreEmpleado = $oHoja->getCell('B' . $i)->getCalculatedValue();
										$CodigoSAP 		= $oHoja->getCell('C' . $i)->getCalculatedValue();

										// BUSCAMOS EL EMPLEADO
										$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento' AND EMPLEADOS.Estado = $EstadoEmpleado");

										if ($IdEmpleado > 0)
										{
											$query = <<<EOD
												UPDATE EMPLEADOS 
													SET
														CodigoSAP = '$CodigoSAP'  
													WHERE EMPLEADOS.Id = $IdEmpleado;
											EOD;
					
											$ok = $this->model->query($query);
										}
										else
											$data['mensajeError'] .= "Empleado [$Documento] - $NombreEmpleado no existe.<br>";
									}
								}
		
								if (empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/empleados/importarCodigosSAP';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/empleados/lista/' . $_SESSION['EMPLEADOS']['Pagina'];
			
				$this->views->getView($this, 'importarCodigosSAP', $data);
			}
		}

		public function importarCuentasBancarias()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				else
				{
					ini_set('max_execution_time', 6000);
					
					$archivo = $_FILES['archivo']['name'];

					if	(empty($_FILES['archivo']['tmp_name']))
						$data['mensajeError'] = "Archivo no pudo ser cargado<br>";
					else
					{
						if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
						{
							if ( file_exists ($archivo) )
							{
								$EstadoEmpleado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ACTIVO'");
	
								$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
								$oHoja = $Excel->getSheet(0);
			
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										$Documento 	= $oHoja->getCell('A' . $i)->getCalculatedValue();
										$Nombre 	= $oHoja->getCell('B' . $i)->getCalculatedValue();
										$Banco 		= $oHoja->getCell('C' . $i)->getCalculatedValue();
										$Cuenta1 	= $oHoja->getCell('D' . $i)->getCalculatedValue();
										$TipoCuenta = $oHoja->getCell('E' . $i)->getCalculatedValue();

										// BUSCAMOS EL EMPLEADO
										$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento' AND EMPLEADOS.Estado = $EstadoEmpleado");

										$IdBanco = getID('BANCOS', "BANCOS.Nombre = '$Banco'");
										$TipoCuenta = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoCuentaBancaria' AND PARAMETROS.Detalle = '$TipoCuenta'");

										if ($IdEmpleado > 0 AND $IdBanco > 0)
										{
											$query = <<<EOD
												UPDATE EMPLEADOS 
													SET
														IdBanco = $IdBanco, 
														CuentaBancaria = '$Cuenta1', 
														CuentaBancaria2 = '', 
														TipoCuentaBancaria = $TipoCuenta  
													WHERE EMPLEADOS.Id = $IdEmpleado;
											EOD;
					
											$ok = $this->model->query($query);
										}
										else
											$data['mensajeError'] .= 'Empleado ' . $Documento . ' no existe<br>';
									}
								}
		
								if (! empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/empleados/importarCuentasBancarias';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/empleados/lista/' . $_SESSION['EMPLEADOS']['Pagina'];
			
				$this->views->getView($this, 'importarCuentasBancarias', $data);
			}
		}

		public function importarTiposContratos()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				else
				{
					ini_set('max_execution_time', 6000);
					
					$archivo = $_FILES['archivo']['name'];

					if	(empty($_FILES['archivo']['tmp_name']))
						$data['mensajeError'] = "Archivo no pudo ser cargado<br>";
					else
					{
						if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
						{
							if ( file_exists ($archivo) )
							{
								// ACTUALIZO LA FECHA DE VENCIMIENTO DE TODOS LOS EMPLEADOS ACTIVOS
								$query = <<<EOD
									UPDATE EMPLEADOS 
										SET FechaVencimiento = NULL, 
											DuracionContrato = 0   
										WHERE EMPLEADOS.Estado = 141;
								EOD;

								$ok = $this->model->query($query);

								$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
								$oHoja = $Excel->getSheet(0);
			
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										$TipoIdentificacion = $oHoja->getCell('A' . $i)->getCalculatedValue();
										$Documento 			= $oHoja->getCell('B' . $i)->getCalculatedValue();
										$NombreEmpleado		= $oHoja->getCell('C' . $i)->getCalculatedValue();
										$Cargo				= strtoupper($oHoja->getCell('D' . $i)->getCalculatedValue());
										$Centro				= strtoupper($oHoja->getCell('E' . $i)->getCalculatedValue());
										$NombreCentro		= strtoupper($oHoja->getCell('F' . $i)->getCalculatedValue());
										$Proyecto			= strtoupper($oHoja->getCell('G' . $i)->getCalculatedValue());
										$NombreProyecto		= strtoupper($oHoja->getCell('H' . $i)->getCalculatedValue());
										$FechaIngreso 		= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('I' . $i)->getCalculatedValue())->format('Y-m-d');
										$TipoContrato 		= $oHoja->getCell('J' . $i)->getCalculatedValue();
										$FechaVencimiento 	= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('K' . $i)->getCalculatedValue())->format('Y-m-d');
										$Prorrogas 			= $oHoja->getCell('L' . $i)->getCalculatedValue();
										$Duracion 			= $oHoja->getCell('M' . $i)->getCalculatedValue();
										$Tiempo 			= $oHoja->getCell('N' . $i)->getCalculatedValue();

										if ($Tiempo <> 'MESES')
											$Duracion = 0;

										if ($FechaIngreso == '1970-01-01')
											$FechaIngreso = NULL;

										if ($FechaVencimiento == '1970-01-01')
											$FechaVencimiento = NULL;

										// BUSCAMOS EL EMPLEADO
										$regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento'");

										if ($regEmpleado)
										{
											$IdEmpleado = $regEmpleado['id'];
											$Estado = $regEmpleado['estado'];

											$Estado = getRegistro('PARAMETROS', $Estado)['detalle'];

											if ($Estado <> 'ACTIVO')
												$data['mensajeError'] .= "Empleado $Documento [$NombreEmpleado] no está activo<br>";
										}
										else
											$IdEmpleado = 0;

										$TipoIdentificacion = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoIdentificacion' AND PARAMETROS.Detalle = '$TipoIdentificacion'");
										$TipoContrato = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoContrato' AND PARAMETROS.Detalle = '$TipoContrato'");

										$Cargo2 = str_replace(' DE ', ' ', strtoupper($Cargo));

										$query = <<<EOD
											SELECT MIN(CARGOS.Id) AS Id 
												FROM CARGOS 
												WHERE REPLACE(CARGOS.Nombre, ' DE ', ' ') = '$Cargo2';
										EOD;

										$regCargo = $this->model->leer($query);

										if ($regCargo)
											$IdCargo = $regCargo['Id'];
										else
											$IdCargo = 0;

										if ($IdCargo == 0)
										{
											$query = <<<EOD
												INSERT INTO CARGOS 
													(Nombre) 
													VALUES (:Nombre);
											EOD;

											$IdCargo = $this->model->adicionar($query, array($Cargo)); 
										}

										$IdCentro = getId('CENTROS', "CENTROS.Centro = '$Centro'");

										if ($IdCentro == 0)
										{
											$query = <<<EOD
												INSERT INTO CENTROS  
													(Centro, Nombre) 
													VALUES (:Centro, :Nombre);
											EOD;

											$IdCentro = $this->model->adicionar($query, array($Centro, $NombreCentro)); 
										}

										if (! empty($Proyecto))
										{
											$IdProyecto = getId('CENTROS', "CENTROS.Centro = '$Proyecto'");

											if ($IdProyecto == 0)
											{
												$query = <<<EOD
													INSERT INTO CENTROS  
														(Centro, Nombre) 
														VALUES (:Centro, :Nombre);
												EOD;

												$IdProyecto = $this->model->adicionar($query, array($Proyecto, $NombreProyecto)); 
											}
										}
										else
											$IdProyecto = 0;

										if ($IdEmpleado > 0)
										{
											if (! is_null($FechaVencimiento))
											{
												$query = <<<EOD
													UPDATE EMPLEADOS 
														SET TipoIdentificacion = $TipoIdentificacion, 
															IdCargo = $IdCargo, 
															IdCentro = $IdCentro, 
															IdProyecto = $IdProyecto, 
															FechaIngreso = '$FechaIngreso', 
															TipoContrato = $TipoContrato, 
															FechaVencimiento = '$FechaVencimiento', 
															Prorrogas = $Prorrogas, 
															DuracionContrato = $Duracion 
														WHERE EMPLEADOS.Id = $IdEmpleado;
												EOD;
											}
											else
											{
												$query = <<<EOD
													UPDATE EMPLEADOS 
														SET TipoIdentificacion = $TipoIdentificacion, 
															IdCargo = $IdCargo, 
															IdCentro = $IdCentro, 
															IdProyecto = $IdProyecto, 
															FechaIngreso = '$FechaIngreso', 
															TipoContrato = $TipoContrato, 
															Prorrogas = $Prorrogas 
														WHERE EMPLEADOS.Id = $IdEmpleado;
												EOD;
											}

											$ok = $this->model->query($query);
										}
										else
											$data['mensajeError'] .= "Empleado $Documento [$NombreEmpleado] no existe<br>";
									}
								}
		
								if (! empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/empleados/importarTiposContratos';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/empleados/lista/' . $_SESSION['EMPLEADOS']['Pagina'];
			
				$this->views->getView($this, 'importarTiposContratos', $data);
			}
		}

		public function importarExenciones()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				else
				{
					ini_set('max_execution_time', 6000);
					
					$archivo = $_FILES['archivo']['name'];

					if	(empty($_FILES['archivo']['tmp_name']))
						$data['mensajeError'] = "Archivo no pudo ser cargado<br>";
					else
					{
						if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
						{
							if ( file_exists ($archivo) )
							{
								$query = <<<EOD
									UPDATE EMPLEADOS
										SET ExencionAnual25 = 0, 
											ExencionAnual = 0, 
											ExencionMes25 = 0, 
											ExencionMes = 0, 
											ExencionAfcFvpAnual = 0,
											ExencionAfcFvpMes = 0;
								EOD; 

								$ok = $this->model->query($query);

								$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
								$oHoja = $Excel->getSheet(0);
			
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										$Documento 			= $oHoja->getCell('A' . $i)->getCalculatedValue();
										$NombreEmpleado 	= $oHoja->getCell('B' . $i)->getCalculatedValue();
										$ExencionAnual25	= $oHoja->getCell('C' . $i)->getCalculatedValue();
										$ExencionAnual 	= $oHoja->getCell('D' . $i)->getCalculatedValue();
										$ExencionMes25		= $oHoja->getCell('E' . $i)->getCalculatedValue();
										$ExencionMes 		= $oHoja->getCell('F' . $i)->getCalculatedValue();
										$ExencionAfcFvpAnual = $oHoja->getCell('G' . $i)->getCalculatedValue();
										$ExencionAfcFvpMes = $oHoja->getCell('H' . $i)->getCalculatedValue();
										
										// BUSCAMOS EL EMPLEADO
										$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");

										if ($IdEmpleado > 0)
										{
											$query = <<<EOD
												UPDATE EMPLEADOS 
													SET ExencionAnual25 = $ExencionAnual25, 
														ExencionAnual = $ExencionAnual, 
														ExencionMes25 = $ExencionMes25, 
														ExencionMes = $ExencionMes, 
														ExencionAfcFvpAnual = $ExencionAfcFvpAnual, 
														ExencionAfcFvpMes = $ExencionAfcFvpMes 
													WHERE EMPLEADOS.Id = $IdEmpleado;
											EOD;
					
											$ok = $this->model->query($query);
										}
										else
											$data['mensajeError'] .= "Empleado $Documento [$NombreEmpleado] no existe<br>";
									}
								}
		
								if (! empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/empleados/importarExenciones';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/empleados/lista/' . $_SESSION['EMPLEADOS']['Pagina'];
			
				$this->views->getView($this, 'importarExenciones', $data);
			}
		}

		public function importarExenciones2()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				else
				{
					ini_set('max_execution_time', 6000);
					
					$archivo = $_FILES['archivo']['name'];

					if	(empty($_FILES['archivo']['tmp_name']))
						$data['mensajeError'] = "Archivo no pudo ser cargado<br>";
					else
					{
						if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
						{
							if ( file_exists ($archivo) )
							{
								$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
								$oHoja = $Excel->getSheet(0);
			
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										$IdEmpleado			= $oHoja->getCell('A' . $i)->getCalculatedValue();
										$Valor 				= $oHoja->getCell('B' . $i)->getCalculatedValue();
										
										if ($IdEmpleado > 0)
										{
											$query = <<<EOD
												UPDATE EMPLEADOS 
													SET ExencionAnual25 = ExencionAnual25 + $Valor, 
														ExencionAnual = ExencionAnual + $Valor 
													WHERE EMPLEADOS.Id = $IdEmpleado;
											EOD;
					
											$ok = $this->model->query($query);
										}
										else
											$data['mensajeError'] .= "Empleado $Documento [$NombreEmpleado] no existe<br>";
									}
								}
		
								if (! empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/empleados/importarExenciones';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/empleados/lista/' . $_SESSION['EMPLEADOS']['Pagina'];
			
				$this->views->getView($this, 'importarExenciones', $data);
			}
		}

		public function actualizarTerceros()
		{
			$query = <<<EOD
				SELECT EMPLEADOS.* 
					FROM EMPLEADOS 
					ORDER BY EMPLEADOS.Documento;
			EOD;

			$empleados = $this->model->listar($query);

			if ($empleados) 
			{
				for ($i = 0; $i < count($empleados); $i++) 
				{ 
					$regEmpleado = $empleados[$i];
					$IdEmpleado = $regEmpleado['id'];
					$Documento = $regEmpleado['documento'];

					$query = <<<EOD
						SELECT RRHH.dbo.recursoHumano.numIdentificacion, 
								RRHH.dbo.fondoPensiones.id as IdFP, 
								RRHH.dbo.fondoPensiones.nombre as NombreFP, 
								RRHH.dbo.fondoCesantias.id as IdFC, 
								RRHH.dbo.fondoCesantias.nombre as NombreFC, 
								RRHH.dbo.cajaCompensacion.id as IdCCF, 
								RRHH.dbo.cajaCompensacion.nombre as NombreCCF, 
								RRHH.dbo.fondoSalud.codigo as CodigoEPS,
								RRHH.dbo.fondoSalud.nombre as NombreEPS 
							FROM RRHH.dbo.recursoHumano 
								LEFT JOIN RRHH.dbo.fondoPensiones 
									ON RRHH.dbo.recursoHumano.idFondoPensiones = RRHH.dbo.fondoPensiones.id 
								LEFT JOIN RRHH.dbo.fondoCesantias 
									ON RRHH.dbo.recursoHumano.idFondoCesantias = RRHH.dbo.fondoCesantias.id 
								LEFT JOIN RRHH.dbo.cajaCompensacion 
									ON RRHH.dbo.recursoHumano.idCajaCompensacion = RRHH.dbo.cajaCompensacion.id 
								LEFT JOIN RRHH.dbo.fondoSalud 
									ON RRHH.dbo.recursoHumano.codigoFondoSalud = RRHH.dbo.fondoSalud.codigo 
							WHERE RRHH.dbo.recursoHumano.numIdentificacion = '$Documento';
					EOD;

					$regEmpleado = $this->model->leer($query);

					if ($regEmpleado) 
					{
						// SE ACTUALIZA EL FONDO DE PENSIONES
						$CodigoFP = $regEmpleado['IdFP'] + 1000;
						$NombreFP = strtoupper($regEmpleado['NombreFP']);

						$query = <<<EOD
							SELECT TERCEROS.* 
								FROM TERCEROS 
								WHERE TERCEROS.Nombre = '$NombreFP';
						EOD;

						$regTercero = $this->model->leer($query);

						if ($regTercero) 
							$IdFP = $regTercero['id'];
						else
						{
							$query = <<<EOD
								INSERT INTO TERCEROS 
									(TipoIdentificacion, Documento, Codigo, Nombre, EsAcreedor, EsFondoPensiones, AceptaPoliticaTD)
									VALUES (3, '', '$CodigoFP', '$NombreFP', 1, 1, 1);
							EOD;

							$ok = $this->model->query($query);

							$IdFP = getId('TERCEROS', "TERCEROS.Codigo = '$CodigoFP' AND TERCEROS.EsFondoPensiones = 1");
						}

						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET IdFondoPensiones = $IdFP 
								WHERE EMPLEADOS.Id = $IdEmpleado;
						EOD;

						$ok = $this->model->query($query);

						// SE ACTUALIZA EL FONDO DE CESANTIAS
						$CodigoFC = $regEmpleado['IdFC'] + 2000;
						$NombreFC = strtoupper($regEmpleado['NombreFC']);

						$query = <<<EOD
							SELECT TERCEROS.* 
								FROM TERCEROS 
								WHERE TERCEROS.Nombre = '$NombreFC';
						EOD;

						$regTercero = $this->model->leer($query);

						if ($regTercero) 
							$IdFC = $regTercero['id'];
						else
						{
							$query = <<<EOD
								INSERT INTO TERCEROS 
									(TipoIdentificacion, Documento, Codigo, Nombre, EsAcreedor, EsFondoCesantias, AceptaPoliticaTD)
									VALUES (3, '', '$CodigoFC', '$NombreFC', 1, 1, 1);
							EOD;

							$ok = $this->model->query($query);

							$IdFC = getId('TERCEROS', "TERCEROS.Codigo = '$CodigoFC' AND TERCEROS.EsFondoCesantias = 1");
						}

						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET IdFondoCesantias = $IdFC 
								WHERE EMPLEADOS.Id = $IdEmpleado;
						EOD;

						$ok = $this->model->query($query);

						// SE ACTUALIZA LA CAJA DE COMPENSACION FAMILIAR
						$CodigoCCF = $regEmpleado['IdCCF'] + 3000;
						$NombreCCF = strtoupper($regEmpleado['NombreCCF']);

						$query = <<<EOD
							SELECT TERCEROS.* 
								FROM TERCEROS 
								WHERE TERCEROS.Nombre = '$NombreCCF';
						EOD;

						$regTercero = $this->model->leer($query);

						if ($regTercero) 
							$IdCCF = $regTercero['id'];
						else
						{
							$query = <<<EOD
								INSERT INTO TERCEROS 
									(TipoIdentificacion, Documento, Codigo, Nombre, EsAcreedor, EsCCF, AceptaPoliticaTD)
									VALUES (3, '', '$CodigoCCF', '$NombreCCF', 1, 1, 1);
							EOD;

							$ok = $this->model->query($query);

							$IdFC = getId('TERCEROS', "TERCEROS.Codigo = '$CodigoCCF' AND TERCEROS.EsCCF = 1");
						}

						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET IdCajaCompensacion = $IdCCF 
								WHERE EMPLEADOS.Id = $IdEmpleado;
						EOD;

						$ok = $this->model->query($query);

						// SE ACTUALIZA LA EPS
						$CodigoEPS = strtoupper($regEmpleado['CodigoEPS']);
						$NombreEPS = strtoupper($regEmpleado['NombreEPS']);

						$query = <<<EOD
							SELECT TERCEROS.* 
								FROM TERCEROS 
								WHERE TERCEROS.Codigo = '$CodigoEPS';
						EOD;

						$regTercero = $this->model->leer($query);

						if ($regTercero) 
							$IdEPS = $regTercero['id'];
						else
						{
							$query = <<<EOD
								INSERT INTO TERCEROS 
									(TipoIdentificacion, Documento, Codigo, Nombre, EsAcreedor, EsEPS, AceptaPoliticaTD)
									VALUES (3, '', '$CodigoEPS', '$NombreEPS', 1, 1, 1);
							EOD;

							$ok = $this->model->query($query);

							$IdEPS = getId('TERCEROS', "TERCEROS.Codigo = '$CodigoEPS' AND TERCEROS.EsEPS = 1");
						}

						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET IdEPS = $IdEPS 
								WHERE EMPLEADOS.Id = $IdEmpleado;
						EOD;

						$ok = $this->model->query($query);
					}
					else
					{
						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET 
									IdFondoPensiones = 0, 
									IdFondoCesantias = 0, 
									IdCajaCompensacion = 0,
									IdEPS = 0 
								WHERE EMPLEADOS.Id = $IdEmpleado;
						EOD; 

						$ok = $this->model->query($query);
					}
				}
			}

			header('Location: ' . SERVERURL . '/empleados/lista/1');
			exit;
		}

		public function importarSueldos()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				else
				{
					ini_set('max_execution_time', 6000);
					
					$archivo = $_FILES['archivo']['name'];

					if	(empty($_FILES['archivo']['tmp_name']))
						$data['mensajeError'] = "Archivo no pudo ser cargado<br>";
					else
					{
						if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
						{
							if ( file_exists ($archivo) )
							{
								$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
								$oHoja = $Excel->getSheet(0);
			
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										$Documento 			= $oHoja->getCell('A' . $i)->getCalculatedValue();
										$NombreEmpleado 	= $oHoja->getCell('B' . $i)->getCalculatedValue();
										$FechaAumento 		= $oHoja->getCell('C' . $i)->getCalculatedValue();
										$SueldoBasico 		= $oHoja->getCell('D' . $i)->getCalculatedValue();

										$FechaAumento = date('Y-m-d', strtotime('20' . substr($FechaAumento, 6, 2) . '-' . substr($FechaAumento, 3, 2) . '-' . substr($FechaAumento, 0, 2)));

										// BUSCAMOS EL EMPLEADO
										$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");

										if ($IdEmpleado > 0)
										{
											$query = <<<EOD
												UPDATE EMPLEADOS 
													SET 
														SueldoBasico = $SueldoBasico, 
														FechaAumento = '$FechaAumento' 
													WHERE EMPLEADOS.Documento = '$Documento';
											EOD;

											$ok = $this->model->query($query);
										}
										else
											$data['mensajeError'] .= 'Empleado ' . $Documento . ' no existe<br>';
									}
								}
		
								if (! empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/empleados/importarSueldos';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/empleados/lista/' . $_SESSION['EMPLEADOS']['Pagina'];
			
				$this->views->getView($this, 'importarSueldos', $data);
			}
		}

		public function importarCorreos()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				else
				{
					ini_set('max_execution_time', 6000);
					
					$archivo = $_FILES['archivo']['name'];

					if	(empty($_FILES['archivo']['tmp_name']))
						$data['mensajeError'] = "Archivo no pudo ser cargado<br>";
					else
					{
						if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
						{
							if ( file_exists ($archivo) )
							{
								$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
								$oHoja = $Excel->getSheet(0);
			
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										$Documento 			= $oHoja->getCell('A' . $i)->getCalculatedValue();
										$NombreEmpleado 	= $oHoja->getCell('B' . $i)->getCalculatedValue();
										$Email 				= $oHoja->getCell('C' . $i)->getCalculatedValue();

										// BUSCAMOS EL EMPLEADO
										$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");

										if ($IdEmpleado > 0)
										{
											$query = <<<EOD
												UPDATE EMPLEADOS 
													SET 
														Email = '$Email'  
													WHERE EMPLEADOS.Documento = '$Documento';
											EOD;

											$ok = $this->model->query($query);
										}
										else
											$data['mensajeError'] .= 'Empleado ' . $Documento . ' no existe<br>';
									}
								}
		
								if (! empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/empleados/importarCorreos';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/empleados/lista/' . $_SESSION['EMPLEADOS']['Pagina'];
			
				$this->views->getView($this, 'importarCorreos', $data);
			}
		}

		public function importarDatos()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				else
				{
					ini_set('max_execution_time', 6000);
					
					$archivo = $_FILES['archivo']['name'];

					if	(empty($_FILES['archivo']['tmp_name']))
						$data['mensajeError'] = "Archivo no pudo ser cargado<br>";
					else
					{
						if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
						{
							if ( file_exists ($archivo) )
							{
								$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
								$oHoja = $Excel->getSheet(0);
			
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										$Documento 		= $oHoja->getCell('A' . $i)->getCalculatedValue();
										$NombreEmpleado = $oHoja->getCell('B' . $i)->getCalculatedValue();
										$Centro 		= $oHoja->getCell('C' . $i)->getCalculatedValue();
										$Proyecto 		= $oHoja->getCell('D' . $i)->getCalculatedValue();
										
										// BUSCAMOS EL EMPLEADO
										$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");
										
										if ($IdEmpleado > 0)
										{
											$Centro = str_pad($Centro, 5, '0', STR_PAD_LEFT);

											$IdCentro = getId('CENTROS', "CENTROS.Centro = '$Centro'");

											if ($IdCentro > 0)
											{
												if (! empty($Proyecto))
												{
													$IdProyecto = getId('CENTROS', "CENTROS.Centro = '$Proyecto'");

													if ($IdProyecto == 0)
													{
														$data['mensajeError'] .= "Proyecto $Proyecto no existe<br>";
														continue;
													}
												}
												else
													$IdProyecto = 0;

												$query = <<<EOD
													UPDATE EMPLEADOS 
														SET IdCentro = $IdCentro, 
															IdProyecto = $IdProyecto  
														WHERE EMPLEADOS.Id = $IdEmpleado;
												EOD;

												$this->model->query($query);
											}
											else
											{
												$data['mensajeError'] .= "Centro $Centro no existe<br>";
												continue;
											}
										}
										else
											$data['mensajeError'] .= "Empleado $Documento - $NombreEmpleado no existe<br>";
									}
									else
										break;
								}
		
								if (empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/empleados/importarDatos';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/empleados/lista/' . $_SESSION['EMPLEADOS']['Pagina'];
			
				$this->views->getView($this, 'importarDatos', $data);
			}
		}

		public function ActualizarNombres()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				else
				{
					ini_set('max_execution_time', 6000);
					
					$archivo = $_FILES['archivo']['name'];

					if	(empty($_FILES['archivo']['tmp_name']))
						$data['mensajeError'] = "Archivo no pudo ser cargado<br>";
					else
					{
						if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
						{
							if ( file_exists ($archivo) )
							{
								$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
								$oHoja = $Excel->getSheet(0);
			
								for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
								{
									if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
									{
										$Documento 	= $oHoja->getCell('A' . $i)->getCalculatedValue();
										$Apellido1 	= $oHoja->getCell('B' . $i)->getCalculatedValue();
										$Apellido2 	= $oHoja->getCell('C' . $i)->getCalculatedValue();
										$Nombre1 	= $oHoja->getCell('D' . $i)->getCalculatedValue();
										$Nombre2 	= $oHoja->getCell('E' . $i)->getCalculatedValue();
										$Genero 	= $oHoja->getCell('F' . $i)->getCalculatedValue();

										$Genero = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'Genero' AND PARAMETROS.Detalle = '$Genero'")['id'];

										// BUSCAMOS EL EMPLEADO
										$IdEmpleado = getID('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");

										if ($IdEmpleado > 0)
										{
											$query = <<<EOD
												UPDATE EMPLEADOS 
													SET
														Apellido1 = '$Apellido1', 
														Apellido2 = '$Apellido2', 
														Nombre1 = '$Nombre1', 
														Nombre2 = '$Nombre2', 
														Genero = $Genero  
													WHERE EMPLEADOS.Id = $IdEmpleado;
											EOD;
					
											$ok = $this->model->query($query);
										}
										else
											$data['mensajeError'] .= "Empleado $Documento [$Apellido1 $Apellido2 $Nombre1 no existe<br>";
									}
								}
		
								if (! empty($data['mensajeError']))
								{
									header('Location: ' . SERVERURL . '/empleados/lista/1');
									exit;
								}
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/empleados/actualizarNombres';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/empleados/lista/' . $_SESSION['EMPLEADOS']['Pagina'];
			
				$this->views->getView($this, 'actualizarNombres', $data);
			}
		}
	}
?>
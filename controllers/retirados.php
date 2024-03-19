<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Retirados extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] 		= '';
			$_SESSION['BorrarRegistro'] 	= '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] 		= '';
			$_SESSION['Avanzar'] 			= '';
			$_SESSION['Novedades'] 			= '';
			$_SESSION['Importar'] 			= '';
			$_SESSION['ImportarArchivo'] 	= '';
			$_SESSION['Exportar'] 			= '';
			$_SESSION['ExportarArchivo'] 	= '';
			$_SESSION['Informe'] 			= SERVERURL . '/retirados/informe';
			$_SESSION['GenerarInforme'] 	= '';
			$_SESSION['Correo'] 			= '';
			$_SESSION['Lista'] 				= '';
		
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
				$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CARGOS.Cargo, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CENTROS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
			}

			if (empty($query))
				$query .= "WHERE PARAMETROS1.Detalle = 'RETIRADO' ";
			else
				$query .= ") AND PARAMETROS1.Detalle = 'RETIRADO' ";

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
			$this->views->getView($this, 'retirados', $data);
		}	

		public function cargarDatos($id)
		{
			$_SESSION['NuevoRegistro'] 		= '';
			$_SESSION['BorrarRegistro'] 	= '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] 		= '';
			$_SESSION['Avanzar'] 			= '';
			$_SESSION['Novedades'] 			= '';
			$_SESSION['Importar'] 			= '';
			$_SESSION['ImportarArchivo'] 	= '';
			$_SESSION['Exportar'] 			= '';
			$_SESSION['ExportarArchivo'] 	= '';
			$_SESSION['Informe'] 			= '';
			$_SESSION['GenerarInforme'] 	= '';
			$_SESSION['Correo'] 			= '';
			$_SESSION['Lista'] 				= SERVERURL . '/retirados/lista/1';

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
							'Id' 				=> $dataExp[$i]['id'],
							'Empresa' 			=> $dataExp[$i]['empresa'],
							'IdCiudad' 			=> $dataExp[$i]['idciudad'],
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
							'Id' 					=> $dataEduF[$i]['id'],
							'CentroEducativo' 		=> $dataEduF[$i]['centroeducativo'],
							'NivelAcademico' 		=> $dataEduF[$i]['nivelacademico'],
							'Estudio' 				=> $dataEduF[$i]['estudio'],
							'Estado' 				=> $dataEduF[$i]['estado'],
							'AnoInicio' 			=> $dataEduF[$i]['anoinicio'],
							'MesInicio' 			=> $dataEduF[$i]['mesinicio'],
							'AnoFinalizacion' 		=> $dataEduF[$i]['anofinalizacion'],
							'MesFinalizacion' 		=> $dataEduF[$i]['mesfinalizacion'],
							'NombreNivelAcademico' 	=> $dataEduF[$i]['NombreNivelAcademico']
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
							'Id' 				=> $dataEduNF[$i]['id'],
							'CentroEducativo' 	=> $dataEduNF[$i]['centroeducativo'],
							'NivelAcademico' 	=> $dataEduNF[$i]['nivelacademico'],
							'Estudio' 			=> $dataEduNF[$i]['estudio'],
							'Estado' 			=> $dataEduNF[$i]['estado'],
							'AnoInicio' 		=> $dataEduNF[$i]['anoinicio'],
							'MesInicio' 		=> $dataEduNF[$i]['mesinicio'],
							'AnoFinalizacion' 	=> $dataEduNF[$i]['anofinalizacion'],
							'MesFinalizacion' 	=> $dataEduNF[$i]['mesfinalizacion']
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
							'Id' 				=> $dataIdiomas[$i]['id'],
							'IdIdioma' 			=> $dataIdiomas[$i]['ididioma'],
							'Nivel' 			=> $dataIdiomas[$i]['nivel'],
							'Nombre' 			=> $dataIdiomas[$i]['Nombre'],
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
						$data['regContactos'][$i] = array(
							'Id' 				=> $dataContactos[$i]['id'],
							'Nombre' 			=> $dataContactos[$i]['nombre'],
							'Telefono' 			=> $dataContactos[$i]['telefono'],
							'Parentesco' 		=> $dataContactos[$i]['parentesco'],
							'NombreParentesco' 	=> $dataContactos[$i]['NombreParentesco']
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
							'NombreUsuario' 	=> $dataAud[$i]['NombreUsuario'],
							'Fecha' 			=> $dataAud[$i]['Fecha'],
							'Campo' 			=> $dataAud[$i]['Campo'],
							'ValorAnterior' 	=> $dataAud[$i]['ValorAnterior'],
							'ValorActual' 		=> $dataAud[$i]['ValorActual']
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
					$Campo 			= 'Documento';
					$ValorAnterior 	= $data['reg']['Documento'];
					$ValorActual 	= $_REQUEST['Documento'];
					$logEmpleado[] 	= array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['CodigoSAP'] <> $data['reg']['CodigoSAP']) 
				{
					$Campo 			= 'CodigoSAP';
					$ValorAnterior 	= $data['reg']['CodigoSAP'];
					$ValorActual 	= $_REQUEST['CodigoSAP'];
					$logEmpleado[] 	= array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
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
					$Campo 			= 'Apellido1';
					$ValorAnterior 	= $data['reg']['Apellido1'];
					$ValorActual 	= $_REQUEST['Apellido1'];
					$logEmpleado[] 	= array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['Apellido2'] <> $data['reg']['Apellido2']) 
				{
					$Campo 			= 'Apellido2';
					$ValorAnterior 	= $data['reg']['Apellido2'];
					$ValorActual 	= $_REQUEST['Apellido2'];
					$logEmpleado[] 	= array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['Nombre1'] <> $data['reg']['Nombre1']) 
				{
					$Campo 			= 'Nombre1';
					$ValorAnterior 	= $data['reg']['Nombre1'];
					$ValorActual 	= $_REQUEST['Nombre1'];
					$logEmpleado[] 	= array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
				}

				if ($_REQUEST['Nombre2'] <> $data['reg']['Nombre2']) 
				{
					$Campo 			= 'Nombre2';
					$ValorAnterior 	= $data['reg']['Nombre2'];
					$ValorActual 	= $_REQUEST['Nombre2'];
					$logEmpleado[] 	= array($id, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
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
					'GrupoPoblacional' 		=> $regEmpleado['GrupoPoblacional'],
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
						WHERE EMPLEADOS.Documento = '$Documento' AND 
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
				$CodigoSAP = $data['reg']['CodigoSAP'];
				$IdEmpleado = $data['IdEmpleado'];

				$query = <<<EOD
					SELECT * FROM EMPLEADOS 
						WHERE EMPLEADOS.CodigoSAP = '$CodigoSAP' AND 
						EMPLEADOS.Id <> $IdEmpleado;
				EOD;

				$reg = $this->model->buscarEmpleado($query);

				if ($reg) 
					$data['mensajeError'] .= '<strong>' . label('Código SAP') . '</strong> ' . label('ya existe') . '<br>';
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

						if	( empty($data['reg']['IdFondoCesantias']) AND $TipoContrato <> 3)
							$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de cesantías') . '</strong><br>';

						// if	( empty($data['reg']['IdFondoPensiones']) )
						// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de pensiones') . '</strong><br>';

						if	( empty($data['reg']['IdCajaCompensacion']) AND $TipoContrato <> 3)
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
	}
?>
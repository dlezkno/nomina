<?php
	require_once('./templates/vendor/autoload.php');
	require './templates/PHPMailer-master/src/PHPMailer.php';
	require './templates/PHPMailer-master/src/SMTP.php';
	require './templates/PHPMailer-master/src/Exception.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class MisDatos extends Controllers
	{
		function __destruct()
		{
			// unset($_SESSION['Paso']);
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
					'IdBanco' 				=> $reg['idbanco'],
					'TipoCuentaBancaria' 	=> $reg['tipocuentabancaria'],
					'CuentaBancaria' 		=> $reg['cuentabancaria'],
					'IdCargo' 				=> $reg['idcargo'], 
					'IdJefe' 				=> $reg['idjefe'], 
					'PerfilProfesional' 	=> $reg['perfilprofesional'],
					'EspecialidadAprendiz' 	=> $reg['especialidadaprendiz'],
					'salarioPractica'       => $reg['salariopractica'],
					'InstitucionDeFormacion' 	=> $reg['instituciondeformacion'],

					
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
					'CertificadoEPS'		=> FALSE, 
					'CertificadoRegimenEps'	=> FALSE, 
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
					'GrupoPoblacional'		=> 0,

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
					'SEL_RevisionCliente'		=> $reg['sel_revisioncliente'],
					'SEL_CartaOferta'			=> $reg['sel_cartaoferta'],

					'IdCargo'				=> $reg['idcargo'],
					'IdJefe'				=> $reg['idjefe'],
					'IdCiudadTrabajo'		=> $reg['idciudadtrabajo'],
					'IdCentro'				=> $reg['idcentro'],
					'IdProyecto'			=> $reg['idproyecto'],
					'IdSede'				=> $reg['idsede'],
					'Vicepresidencia'		=> $reg['vicepresidencia'], 
					'TipoContrato'			=> $reg['tipocontrato'], 
					'SueldoBasico'			=> $reg['sueldobasico'],
					'duracionContrato'      => $reg['duracioncontrato'],
					'Observaciones'			=> $reg['observaciones'],
					'FechaIngreso'			=> $reg['fechaingreso'],
					'FechaFinEtapaLectiva'		=> $reg['fechafinetapalectiva'],
					'FechaInicioEtapaProductiva'		=> $reg['fechainicioetapaproductiva'],
					'FechaVencimiento'		=> $reg['fechavencimiento'],

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
						'NombreNivelConocimiento' 	=> $dataOCE[$i]['NombreNivelConocimiento']
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

					if (strpos($archivo, 'CERTIFICADO_APRENDIZ') !== FALSE)
						$data['reg']['certificadoAprendiz'] = TRUE;

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
						$data['reg']['PoliticamenteExpuesta'] = (isset($_REQUEST['PoliticamenteExpuesta']) AND $_REQUEST['PoliticamenteExpuesta']=='si') ? 'true' : 'false';
						$data['reg']['DeclaracionOrigenRecursos'] = isset($_REQUEST['DeclaracionOrigenRecursos']) ? 'true' : 'false';
						$data['reg']['UsoLicitoRecursos'] 	= isset($_REQUEST['UsoLicitoRecursos']) ? 'true' : 'false';

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
									INNER JOIN PARAMETROS 
									ON EMPLEADOS.Estado = PARAMETROS.Id 
									WHERE EMPLEADOS.Documento = '$Documento' AND 
									PARAMETROS.Detalle <> 'RETIRADO'  AND 
									PARAMETROS.Detalle <> 'CANDIDATO DESISTE'   AND 
									PARAMETROS.Detalle <> 'CANDIDATO NO CALIFICADO' AND
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

						if (! isset($_REQUEST['DeclaracionOrigenRecursos']))
							$data['mensajeError'] .= label('Debe aceptar') . ' <strong>' . label('Declaración de origen y destino de recursos') . '</strong><br>';
					
						if (! isset($_REQUEST['UsoLicitoRecursos']))
							$data['mensajeError'] .= label('Debe aceptar') . ' <strong>' . label('Uso lícito de recursos') . '</strong><br>';
					
						break;

					case 2:
						$data['reg']['FechaNacimiento'] 	= isset($_REQUEST['FechaNacimiento']) ? $_REQUEST['FechaNacimiento'] : '';
						$data['reg']['IdCiudadNacimiento']	= isset($_REQUEST['IdCiudadNacimiento']) ? $_REQUEST['IdCiudadNacimiento'] : '';
						$data['reg']['Genero']				= isset($_REQUEST['Genero']) ? $_REQUEST['Genero'] : '';
						$data['reg']['EstadoCivil']			= isset($_REQUEST['EstadoCivil']) ? $_REQUEST['EstadoCivil'] : '';
						$data['reg']['FactorRH']			= isset($_REQUEST['FactorRH']) ? $_REQUEST['FactorRH'] : '';
						$data['reg']['talla']				= isset($_REQUEST['talla']) ? $_REQUEST['talla'] : '';
						$data['reg']['LibretaMilitar']		= isset($_REQUEST['LibretaMilitar']) ? $_REQUEST['LibretaMilitar'] : '';
						$data['reg']['DistritoMilitar']		= isset($_REQUEST['DistritoMilitar']) ? $_REQUEST['DistritoMilitar'] : '';
						$data['reg']['LicenciaConduccion']	= isset($_REQUEST['LicenciaConduccion']) ? $_REQUEST['LicenciaConduccion'] : '';
						$data['reg']['TarjetaProfesional']	= isset($_REQUEST['TarjetaProfesional']) ? $_REQUEST['TarjetaProfesional'] : '';

						$validFechaNacimiento =  date('Y-m-d', strtotime(date('Y-m-d') . ' - 15 year'));
						if	( empty($data['reg']['FechaNacimiento']) )
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de nacimiento') . '</strong><br>';
						elseif ($data['reg']['FechaNacimiento'] >= $validFechaNacimiento)
							$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de nacimiento correcta (posterior a la Fecha de expedición)') . '</strong><br>';
					

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
						// if	( empty($_REQUEST['IdEPS']) )
						// 	$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('E.P.S.') . '</strong><br>';
						// if	( empty($_REQUEST['IdFondoCesantias']) )
						// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de cesantías') . '</strong><br>';
						// if	( empty($_REQUEST['IdFondoPensiones']) )
						// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Fondo de pensiones') . '</strong><br>';
						// if	( empty($_REQUEST['IdBanco']) )
						// 	$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Entidad bancaria') . '</strong><br>';
						// if	( empty($_REQUEST['TipoCuentaBancaria']) )
						// 	$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Tipo cuenta bancaria') . '</strong><br>';
						// if	( empty($_REQUEST['CuentaBancaria']) )
						// 	$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Cuenta bancaria') . '</strong><br>';

						if (empty($data['mensajeError'])) {
							$data['reg']['IdEPS']				= isset($_REQUEST['IdEPS']) ? $_REQUEST['IdEPS'] : 0;
							$data['reg']['IdFondoCesantias'] 	= isset($_REQUEST['IdFondoCesantias']) ? $_REQUEST['IdFondoCesantias'] : 0;
							$data['reg']['IdFondoPensiones'] 	= isset($_REQUEST['IdFondoPensiones']) ? $_REQUEST['IdFondoPensiones'] : 0;
							$data['reg']['IdBanco']				= isset($_REQUEST['IdBanco']) ? $_REQUEST['IdBanco'] : 0;
							$data['reg']['TipoCuentaBancaria'] 	= isset($_REQUEST['TipoCuentaBancaria']) ? $_REQUEST['TipoCuentaBancaria'] : 0;
							$data['reg']['CuentaBancaria']		= isset($_REQUEST['CuentaBancaria']) ? $_REQUEST['CuentaBancaria'] : 0;
						}

						break;

					case 5:
						$data['reg']['PerfilProfesional']	= isset($_REQUEST['PerfilProfesional']) ? $_REQUEST['PerfilProfesional'] : '';

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
							elseif($data['reg']['AnoInicioF'] < 1950)
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Año de inicio') . '</strong> ' . label('posterior a 1950') . '<br>';
					
							if	( empty($data['reg']['MesInicioF']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Mes de inicio') . '</strong><br>';
					
							if	( ! empty($data['reg']['AnoFinalizacionF']) )
							{
								if ($data['reg']['AnoFinalizacionF'] < $data['reg']['AnoInicioF'])
									$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Año de finalización') . '</strong> ' . label('mayor o igual al') . ' <strong>' . label('Año de inicio') . '</strong><br>';

								if	( empty($data['reg']['MesFinalizacionF']) )
									$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Mes de finalización') . '</strong><br>';

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
							elseif($data['reg']['AnoInicioNF'] < 1950)
								$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Año de inicio') . '</strong> ' . label('posterior a 1950') . '<br>';
					
							if	( empty($data['reg']['MesInicioNF']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Mes de inicio') . '</strong><br>';
					
							if	( ! empty($data['reg']['AnoFinalizacionNF']) )
							{
								if ($data['reg']['AnoFinalizacionNF'] < $data['reg']['AnoInicioNF'])
									$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Año de finalización') . '</strong> ' . label('mayor o igual al') . ' <strong>' . label('Año de inicio') . '</strong><br>';

								if	( empty($data['reg']['MesFinalizacionNF']) )
									$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Mes de finalización') . '</strong><br>';

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

						// if (empty($data['reg']['CertificacionBancaria']) AND empty($_FILES['CertificacionBancaria']['name']))
						// 	$data['mensajeError'] .= label('Debe cargar una certificación de') . ' <strong>' . label('Cuenta bancaria') . '</strong><br>';

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
						$data['reg']['SEL_DocumentosActualizados'] 	= isset($_REQUEST['DocumentosActualizados']) ? 1 : 0;

						break;

					case 15:
						if ($_SESSION['Login']['Perfil'] <> EMPLEADO)
						{
							$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];

							$MetodoRetencion = getId('PARAMETROS', "PARAMETROS.Parametro = 'MetodoRetencion' AND PARAMETROS.Detalle = 'BUSQUEDA EN TABLA'");

							$data['reg']['IdCargo']			= isset($_REQUEST['IdCargo']) ? $_REQUEST['IdCargo'] : 0;
							$data['reg']['IdJefe']			= isset($_REQUEST['IdJefe']) ? $_REQUEST['IdJefe'] : 0;
							$data['reg']['IdCiudadTrabajo']	= isset($_REQUEST['IdCiudadTrabajo']) ? $_REQUEST['IdCiudadTrabajo'] : 0;
							$data['reg']['IdCentro']		= isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0;
							$data['reg']['IdProyecto']		= isset($_REQUEST['IdProyecto']) ? $_REQUEST['IdProyecto'] : 0;
							$data['reg']['Vicepresidencia']	= isset($_REQUEST['Vicepresidencia']) ? $_REQUEST['Vicepresidencia'] : 0;
							$data['reg']['IdSede']			= isset($_REQUEST['IdSede']) ? $_REQUEST['IdSede'] : 0;
							$data['reg']['TipoContrato']	= isset($_REQUEST['TipoContrato']) ? $_REQUEST['TipoContrato'] : 0;
							$data['reg']['SueldoBasico']	= isset($_REQUEST['SueldoBasico']) ? $_REQUEST['SueldoBasico'] : 0;
							$data['reg']['duracionContrato']	= isset($_REQUEST['duracionContrato']) ? $_REQUEST['duracionContrato'] : 0;
							$data['reg']['Observaciones']	= isset($_REQUEST['Observaciones']) ? $_REQUEST['Observaciones'] : '';
							$data['reg']['FechaIngreso']	= isset($_REQUEST['FechaIngreso']) ? $_REQUEST['FechaIngreso'] : '';
							$data['reg']['FechaVencimiento']	= isset($_REQUEST['FechaVencimiento']) ? $_REQUEST['FechaVencimiento'] : '';

							$data['reg']['InstitucionDeFormacion']	= isset($_REQUEST['InstitucionDeFormacion']) ? $_REQUEST['InstitucionDeFormacion'] : '';
							$data['reg']['EspecialidadAprendiz']	= isset($_REQUEST['EspecialidadAprendiz']) ? $_REQUEST['EspecialidadAprendiz'] : '';
							$data['reg']['salarioPractica']	= (!isset($_REQUEST['salarioPractica']) || $_REQUEST['salarioPractica'] == '') ? 0 : $_REQUEST['salarioPractica'];
							$data['reg']['FechaFinEtapaLectiva'] =  isset($_REQUEST['FechaFinEtapaLectiva']) ? $_REQUEST['FechaFinEtapaLectiva'] : '';
							$data['reg']['FechaInicioEtapaProductiva'] =  isset($_REQUEST['FechaInicioEtapaProductiva']) ? $_REQUEST['FechaInicioEtapaProductiva'] : '';

							$data['reg']['ModalidadTrabajo']	= 0;
							$data['reg']['SubsidioTransporte']	= 0;
							$data['reg']['PeriodicidadPago']	= 10;
							$data['reg']['HorasMes']			= getHoursMonth();
							$data['reg']['DiasAno']				= 360;
							$data['reg']['MetodoRetencion']		= $MetodoRetencion;

							if	(empty($data['reg']['IdCargo']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Cargo') . '</strong><br>';
							if	(empty($data['reg']['IdJefe']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Jefe inmediato') . '</strong><br>';
							if	( empty($data['reg']['IdCiudadTrabajo']) )
								$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad de trabajo') . '</strong><br>';

							if	(empty($data['reg']['IdCentro']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Centro de costos') . '</strong><br>';

							if	(empty($data['reg']['Vicepresidencia']) )
								$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Vicepresidencia') . '</strong><br>';

							if	(empty($data['reg']['IdSede']) )
								$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Sede') . '</strong><br>';

							if	(empty($data['reg']['TipoContrato']) )
								$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de contrato') . '</strong><br>';

							$ModalidadTrabajo = getId('PARAMETROS', "PARAMETROS.Parametro = 'ModalidadTrabajo' AND PARAMETROS.Detalle = 'SUELDO BÁSICO'");
							$data['reg']['ModalidadTrabajo'] = $ModalidadTrabajo;

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
							}

							if (! empty($data['reg']['TipoContrato']))
								$TipoContrato = getRegistro('PARAMETROS', $data['reg']['TipoContrato'])['detalle'];
							else
								$TipoContrato = 0;

							if ($data['reg']['SueldoBasico'] <= $SueldoMinimo * 2)
								$data['reg']['SubsidioTransporte'] = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'SUBSIDIO COMPLETO'");
							else
								$data['reg']['SubsidioTransporte'] = getId('PARAMETROS', "PARAMETROS.Parametro = 'SubsidioTransporte' AND PARAMETROS.Detalle = 'NO RECIBE SUBSIDIO'");

							if (empty($data['reg']['FechaIngreso']))
								$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de ingreso') . '</strong><br>';
							
							if ($TipoContrato <> 'INDEFINIDO' AND empty($data['reg']['FechaVencimiento']))
								$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de vencimiento') . '</strong><br>';
						}

						break;

					case 16:
						break;
				}
			}

			return($data);
		}

		public function editar($Id)
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
					case 'REVISION_CLIENTE_16':
						$this->revisionCliente($Id);
						break;

					case 'NOCALIFICADO_16':
						$this->noCalificado($Id);
						break;

					case 'CARTA_OFERTA_16':
						$this->cartaOferta($Id);
						break;

					case 'ACTUALIZAR_16':
						$this->candidatoNuevo($Id);
						break;

					case 'CONTRATAR_16':
						$this->contratacion($Id);
						break;

					case 'DOCUMENTACION_16':
						$this->documentacion($Id);
						break;

					case 'FINALIZAR':
						$data = $this->validarDatos($Id, 16);

						if (empty($data['mensajeError']))
							$this->finalizar($Id);

						break;

					case 'DESISTIR':
						$this->desistir($Id);
						break;

					default:
						$data = $this->cargarDatos($Id);
						break;
				}

				if (substr($_REQUEST['Action'], 0, 7) == 'BORRAR_')
				{
					$i = str_replace('_', '', substr(str_replace(substr($_REQUEST['Action'], -2), '', $_REQUEST['Action']), -2));

					$this->borrarDatos($data, $paso, $i);

					$data = $this->cargarDatos($Id);
				}
				elseif ((empty($data['mensajeError']) AND $paso <> 16) OR (! empty($data['mensajeError']) AND ($paso == 12 OR $paso == 13)))
				{
					$data = $this->validarDatos($Id, $paso);

					if (empty($data['mensajeError']))
					{
						$resp = $this->model->actualizarCandidato($data['reg'], $paso);
						$data = $this->cargarDatos($Id);

						if (substr($_REQUEST['Action'], 0, 10) <> 'ACTUALIZAR')
						{
							$paso++;

							if ($paso == 14 AND $_SESSION['Login']['Perfil'] == EMPLEADO)
								$paso += 2;

							$_SESSION['Paso'] = $paso;
						}
					}
				} else $data = $this->cargarDatos($Id);

				$this->views->getView($this, 'actualizar', $data);
				exit;
			}
			else 
			{
				$data = $this->cargarDatos($Id);
				$data['mensajeError'] = '';
				$data['paso'] = $paso;
				$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrarDatos($data, $paso, $i)
		{
			switch ($paso)
			{
				case 6:
					$IdRegistro = $data['regEmp'][$i]['Id'];

					if ($IdRegistro)
					{
						$query = <<<EOD
							DELETE FROM EXPERIENCIALABORAL 
								WHERE Id = $IdRegistro;
						EOD;

						$this->model->query($query);
					}

					break;

				case 7:
					$IdRegistro = $data['regEduF'][$i]['Id'];

					if ($IdRegistro)
					{
						$query = <<<EOD
							DELETE FROM EDUCACIONEMPLEADO 
								WHERE Id = $IdRegistro;
						EOD;

						$this->model->query($query);
					}

					break;

				case 8:
					$IdRegistro = $data['regEduNF'][$i]['Id'];

					if ($IdRegistro)
					{
						$query = <<<EOD
							DELETE FROM EDUCACIONEMPLEADO 
								WHERE Id = $IdRegistro;
						EOD;

						$this->model->query($query);
					}

					break;

				case 9:
					$IdRegistro = $data['regIdiomas'][$i]['Id'];

					if ($IdRegistro)
					{
						$query = <<<EOD
							DELETE FROM IDIOMASEMPLEADO 
								WHERE Id = $IdRegistro;
						EOD;

						$this->model->query($query);
					}

					break;

				case 10:
					$IdRegistro = $data['regOCE'][$i]['Id'];

					if ($IdRegistro)
					{
						$query = <<<EOD
							DELETE FROM OTROSCONOCIMIENTOSEMPLEADO 
								WHERE Id = $IdRegistro;
						EOD;

						$this->model->query($query);
					}

					break;

				case 11:
					$IdRegistro = $data['regContacto'][$i]['Id'];

					if ($IdRegistro)
					{
						$query = <<<EOD
							DELETE FROM CONTACTOSEMPLEADO 
								WHERE Id = $IdRegistro;
						EOD;

						$this->model->query($query);
					}

					break;

			}
		}

		public function revisionCliente($Id)
		{
			$query = <<<EOD
				UPDATE EMPLEADOS
					SET
						SEL_RevisionCliente = 1 
					WHERE EMPLEADOS.Id = $Id;
			EOD;

			$this->model->query($query);

			// header('Location: ' . SERVERURL . '/candidatos/lista/1');
			// exit();
		}

		public function noCalificado($Id)
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

		public function candidatonuevo($Id)
		{
			// ENVIO DE CORREO

			// ACTUALIZAR LOG
			$regEmpleado = getRegistro('EMPLEADOS', $Id);

			// BUSCAMOS EL ESTADO ACTUAL DEL EMPLEADO 
			$EstadoActual = getRegistro('PARAMETROS', $regEmpleado['estado'])['detalle'];
			$EstadoNuevo = 'EN PROCESO DE SELECCION';

			$data = array($Id, 'ESTADO', $EstadoActual, $EstadoNuevo, $_SESSION['Login']['Id']);

			$resp = $this->model->guardarLogEmpleado($data);

			//ACTUALIZACIÓN ESTADO EMPLEADO
			$EstadoNuevo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = '$EstadoNuevo'")['id'];
			$EstadoNuevo = isset($EstadoNuevo) ? $EstadoNuevo :'NULL';

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

		public function cartaOferta($Id)
		{
			$regEmpleado = getRegistro('EMPLEADOS', $Id);

			$Documento 	= $regEmpleado['documento'];
			$NombreEmpleado	= strtoupper(trim($regEmpleado['apellido1']) . ' ' . trim($regEmpleado['apellido2']) . ' ' . trim($regEmpleado['nombre1']) . ' ' . trim($regEmpleado['nombre2']));
			$Nombre 	= strtoupper(trim($regEmpleado['nombre1']) . ' ' . trim($regEmpleado['nombre2']));
			$Email 		= $regEmpleado['email'];
			$Celular 	= $regEmpleado['celular'];

			if (empty($regEmpleado['idcargo']) OR 
				empty($regEmpleado['idcentro']) OR 
				empty($regEmpleado['vicepresidencia']) OR 
				empty($regEmpleado['idsede']) OR 
				empty($regEmpleado['tipocontrato']) OR 
				empty($regEmpleado['sueldobasico']))
				$mensajeError = "Candidato no tiene definidas las CONDICIONES LABORALES<br>";
			else
			{
				// BUSCAMOS EL ESTADO DEL EMPLEADO
				$EstadoEmpleado = $regEmpleado['estado'];


				$valid = validateDocumentFirmPLus($regEmpleado['solicitudfirma'], $Id);

				if($valid){		

					if ($regEmpleado['sel_examenesmedicos'] OR $regEmpleado['sel_revisioncliente'])
					{
						if (!empty($_FILES['CartaOferta']['name']))
						{
							// ENVIO DE CARTA OFERTA LABORAL PARA FIRMA ELECTRONICA
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
								
							// CARGAMOS EL DOCUMENTO EN CONTRATOS
							$cDirectorio = str_replace(" ","",'documents/' . $regEmpleado['documento'] . '_' . strtoupper($regEmpleado['apellido1'] . '_' . $regEmpleado['apellido2'] . '_' . $regEmpleado['nombre1'] . '_' . $regEmpleado['nombre2']));

							if	( ! is_dir($cDirectorio) )
								mkdir($cDirectorio);

							$cDirectorio .= '/CONTRATOS';

							if	( ! is_dir($cDirectorio) )
								mkdir($cDirectorio);

							$cArchivoDestino = $cDirectorio . '/' . $regEmpleado['documento'] . '_CARTA_OFERTA_LABORAL.' . pathinfo($_FILES['CartaOferta']['name'], PATHINFO_EXTENSION);
							move_uploaded_file($_FILES['CartaOferta']['tmp_name'], $cArchivoDestino);

							// SE PREPARA EL ARCHIVO PARA FIRMA DIGITAL
							$Archivo  = utf8_decode('CARTA OFERTA LABORAL');
							$archivo1 = base64_encode(file_get_contents($cArchivoDestino));

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
							logRequests("SELECCION","",json_encode(curl_getinfo($curl)), json_encode($response), "FIRMA PLUS");

							curl_close($curl);

							if ($response['Code'] == 1)
							{
								$SolicitudFirma = $response['Data']['NroSolicitud'];
								$FechaSolicitud = $response['Data']['Fecha'];
								
								$query = <<<EOD
									UPDATE EMPLEADOS 
										SET SEL_CartaOferta = 1, 
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
									Error al tratar de enviar la CARTA OFERTA LABORAL para firma electrónica <br>
									Código de error: $CodigoError<br>
									Mensaje: $MensajeError<br>
								EOD;
							}
						}
						else
						{
							$mensajeError = "No se puede leer el documento por favor intenta de nuevo";
						}
					}else{
						$mensajeError = "Candidato debe tener EXÁMENES MÉDICOS REALIZADOS ó REVISIÓN POR PARTE DEL CLIENTE REALIZADA para poder enviar CARTA OFERTA LABORAL<br>";
					}

				}else{
					$mensajeError = "El candidato tiene DOCUMENTOS pendientes por firmar <br>";
				}

			}

			if (empty($mensajeError))
			{
				header('Location: ' . SERVERURL . '/candidatos/lista/1');
				exit();
			}
			else
			{
				$data = $this->cargarDatos($Id);
				$data['mensajeError'] = $mensajeError;
				$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function contratacion($Id)
		{
			$mensajeError = '';

			$regEmpleado = getRegistro('EMPLEADOS', $Id);

			if (empty($regEmpleado['idcargo']) OR 
				empty($regEmpleado['idcentro']) OR 
				empty($regEmpleado['vicepresidencia']) OR 
				empty($regEmpleado['idsede']) OR 
				empty($regEmpleado['tipocontrato']) OR 
				empty($regEmpleado['sueldobasico']))
				$mensajeError .= "Candidato no tiene definidas las CONDICIONES LABORALES<br>";
			else
			{
				// VALIDAMOS SI EL CANDIDATO YA FIRMO LA CARTA OFERTA LABORAL
				// DESCARGAMOS LA FIRMA ELECTRONICA DEL DOCUMENTO
				$IdEmpleado 	= $regEmpleado['id'];
				$Documento 		= $regEmpleado['documento'];
				$Apellido1 		= $regEmpleado['apellido1'];
				$Apellido2		= $regEmpleado['apellido2'];
				$Nombre1		= $regEmpleado['nombre1'];
				$Nombre2		= $regEmpleado['nombre2'];
				$SolicitudFirma = $regEmpleado['solicitudfirma'];

				$Nombre			= strtoupper(trim($Apellido1) . ' ' . trim($Apellido2) . ' ' . trim($Nombre1) . ' ' . trim($Nombre2));
				$Cargo			= getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];

				if ($regEmpleado['idproyecto'] == 0)
					$Centro = getRegistro('CENTROS', $regEmpleado['idcentro'])['nombre'];
				else
					$Centro = getRegistro('CENTROS', $regEmpleado['idproyecto'])['nombre'];

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
				if ($response['Code'] == '100' ||
					$regEmpleado['tipocontrato'] == '429' || 
					$regEmpleado['tipocontrato'] == '428' || 
					$regEmpleado['tipocontrato'] == '144' || 
					$regEmpleado['tipocontrato'] == '427')
				{

					if(
						$regEmpleado['tipocontrato'] != '429' && 
						$regEmpleado['tipocontrato'] != '428' && 
						$regEmpleado['tipocontrato'] != '144' && 
						$regEmpleado['tipocontrato'] != '427'
					){
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

							if	( ! is_dir($cDirectorio . '/CONTRATOS') )
								mkdir($cDirectorio . '/CONTRATOS');

							$archivoDestino = $cDirectorio . '/CONTRATOS/' . $Documento . '_' . $Fecha . '_FOTOGRAFIA.JPG';

							$ok = file_put_contents($archivoDestino, $Fotografia, LOCK_EX);
						}

						for ($j = 0; $j < count($response['Data']['DocumentosPDF']); $j++)
						{
							$TipoDocumento = $response['Data']['DocumentosPDF'][$j]['TipoDocumento'];
							$DocumentoFirmado = base64_decode($response['Data']['DocumentosPDF'][$j]['DocumentoFirmado']);

							$cDirectorio = str_replace(" ","",'documents/' . $Documento . '_' . strtoupper($Apellido1) . '_' . strtoupper($Apellido2) . '_' . strtoupper($Nombre1) . '_' . strtoupper($Nombre2));

							if	( ! is_dir($cDirectorio) )
								mkdir($cDirectorio);

							if	( ! is_dir($cDirectorio . '/CONTRATOS') )
								mkdir($cDirectorio . '/CONTRATOS');

							$archivoDestino = $cDirectorio . '/CONTRATOS/' . $Documento . '_' . $Fecha . '_' . strtoupper($TipoDocumento) . '.PDF';

							$ok = file_put_contents($archivoDestino, $DocumentoFirmado, LOCK_EX);
						}

					}

					// ENVIAMOS CORREO A CONTRATACION
					$EstadoEmpleado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'EN PROCESO DE SELECCION' ");
					$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'CORREO ELECTRÓNICO'");
				
					$query = <<<EOD
						SELECT PLANTILLAS.* 
							FROM PLANTILLAS 
							WHERE PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND 
								PLANTILLAS.TipoPlantilla = $TipoPlantilla AND 
								PLANTILLAS.Asunto = 'CONTRATAR CANDIDATO';
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

						if (empty($regEmpleado['fechavencimiento']))
							$Plantilla = str_replace('<<FechaVencimiento>>', 'INDEFINIDO', $Plantilla);
						else
							$Plantilla = str_replace('<<FechaVencimiento>>', $regEmpleado['fechavencimiento'], $Plantilla);

						$Plantilla = str_replace('<<Dia>>', substr($regEmpleado['fechaingreso'], 9, 2), $Plantilla);
						$NombreMes = NombreMes(substr($regEmpleado['fechaingreso'], 5, 2));
						$Plantilla = str_replace('<<NombreMes>>', $NombreMes, $Plantilla);
						$Plantilla = str_replace('<<Año>>', substr($regEmpleado['fechaingreso'], 0, 4), $Plantilla);
						$Plantilla = str_replace('<<Fecha>>', script_fecha(), $Plantilla);

						$regSicologo = getRegistro('EMPLEADOS', $regEmpleado['idsicologo']);

						if ($regSicologo)
							$NombreSicologo = strtoupper(trim($regSicologo['nombre1']) . ' ' . trim($regSicologo['nombre2']) . ' ' . trim($regSicologo['apellido1']));
						else
							$NombreSicologo = '';

						$Plantilla = str_replace('<<NombreSicologo>>', $NombreSicologo, $Plantilla);

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
						$fromName = 'SELECCION DE PERSONAL - COMWARE';

						$mail->Subject = $Asunto;
						$mail->addEmbeddedImage(LOGOTIPO, 'comware');
						$mail->Body = $Plantilla;

						$EmailContratacion = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CorreoContratacion'")['detalle'];

						if (! empty($EmailContratacion))
							$mail->AddAddress($EmailContratacion);
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
							logRequests("SELECCION MIS DATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $mail->to);
						} 
						catch (Exception $e) 
						{
							logRequests("SELECCION MIS DATOS",$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $EmailContratacion);
							$mensajeError .= "Error al enviar correo a $EmailContratacion <br>";
							$mail->getSMTPInstance()->reset();
						}

						$mail->clearAddresses();
						$mail->clearAttachments();
					}
					else
					{
						$mensajeError .= "No existe una plantilla de correo para CONTRATAR CANDIDATO<br>";
					}

					// ACTUALIZAR LOG
					$EstadoActual = getRegistro('PARAMETROS', $regEmpleado['estado'])['detalle'];
					$EstadoNuevo = 'EN PROCESO DE CONTRATACION';

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
				}
				else
				{
					$CodigoError = $response['Code'];
					$MensajeError = $response['Message'];

					$mensajeError .= <<<EOD
						Candidato no ha firmado la CARTA DE OFERTA LABORAL. No se puede avanzar a Contratación.<br>
						Código de error: $CodigoError<br>
						Mensaje: $MensajeError<br>
					EOD;
				}
			}

			if (empty($mensajeError))
			{
				header('Location: ' . SERVERURL . '/candidatos/lista/1');
				exit();
			}
			else
			{
				$data = $this->cargarDatos($Id);
				$data['mensajeError'] = $mensajeError;
				$this->views->getView($this, 'actualizar', $data);
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

		public function finalizar($Id)
		{
			// ENVIO DE CORREO A SELECCION INFORMANDO ACTUALIZACION DE DATOS

			$query = <<<EOD
				UPDATE EMPLEADOS
					SET
						SEL_DatosActualizados = 1 
					WHERE EMPLEADOS.Id = $Id;
			EOD;

			$this->model->query($query);

			unset($_SESSION['Paso']);

			header('Location: ' . SERVERURL);
			exit();
		}

		public function desistir($Id)
		{
			// ENVIO DE CORREO

			// ACTUALIZAR LOG
			$regEmpleado = getRegistro('EMPLEADOS', $Id);

			// BUSCAMOS EL ESTADO ACTUAL DEL EMPLEADO 
			$EstadoActual = getRegistro('PARAMETROS', $regEmpleado['estado'])['detalle'];
			$EstadoNuevo = 'CANDIDATO DESISTE';

			$data = array($IdEmpleado, 'ESTADO', $EstadoActual, $EstadoNuevo, $_SESSION['Login']['Id']);

			$resp = $this->model->guardarLogEmpleado($data);

			//ACTUALIZACIÓN ESTADO EMPLEADO
			$EstadoNuevo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = '$EstadoNuevo'")['id'];

			$query = <<<EOD
				UPDATE EMPLEADOS
					SET
						Estado = $EstadoNuevo 
					WHERE EMPLEADOS.Id = $IdEmpleado;
			EOD;

			$this->model->query($query);

			header('Location: ' . SERVERURL);
			exit();
		}
	}
?>
<?php
	class misdatosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	

		public function buscarCandidato($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarCandidato(array $data, $paso)
		{
			switch ($paso)
			{
				case 1:
					$datos = array();
					$datos['Id'] 					= $data['Id'];
					$datos['TipoIdentificacion'] 	= $data['TipoIdentificacion'];
					$datos['Documento'] 			= trim($data['Documento']);
					$datos['FechaExpedicion'] 		= $data['FechaExpedicion'];
					$datos['IdCiudadExpedicion'] 	= $data['IdCiudadExpedicion'];
					$datos['Apellido1'] 			= strtoupper(trim($data['Apellido1']));
					$datos['Apellido2'] 			= strtoupper(trim($data['Apellido2']));
					$datos['Nombre1'] 				= strtoupper(trim($data['Nombre1']));
					$datos['Nombre2'] 				= strtoupper(trim($data['Nombre2']));
					$datos['PoliticamenteExpuesta'] = $data['PoliticamenteExpuesta'];
					$datos['DeclaracionOrigenRecursos'] = $data['DeclaracionOrigenRecursos'];
					$datos['UsoLicitoRecursos'] 	= $data['UsoLicitoRecursos'];

					$query = <<<EOD
						UPDATE EMPLEADOS 
							SET 
								TipoIdentificacion 		= :TipoIdentificacion, 
								Documento 				= :Documento, 
								FechaExpedicion 		= :FechaExpedicion, 
								IdCiudadExpedicion 		= :IdCiudadExpedicion, 
								Apellido1 				= :Apellido1, 
								Apellido2 				= :Apellido2, 
								Nombre1 				= :Nombre1, 
								Nombre2 				= :Nombre2, 
								PoliticamenteExpuesta 	= :PoliticamenteExpuesta, 
								DeclaracionOrigenRecursos = :DeclaracionOrigenRecursos, 
								UsoLicitoRecursos		= :UsoLicitoRecursos, 
								FechaActualizacion 		= getDate() 
							WHERE EMPLEADOS.Id 			= :Id;
					EOD;

					$resp = $this->actualizar($query, $datos);

					break;

				case 2:
					if (! empty($data['FechaNacimiento']))
					{
						$datos = array();
						$datos['Id'] 					= $data['Id'];
						$datos['FechaNacimiento'] 		= $data['FechaNacimiento'];
						$datos['IdCiudadNacimiento'] 	= $data['IdCiudadNacimiento'];
						$datos['Genero'] 				= $data['Genero'];
						$datos['EstadoCivil'] 			= $data['EstadoCivil'];
						$datos['FactorRH'] 				= $data['FactorRH'];
						$datos['talla'] 				= $data['talla'];
						$datos['LibretaMilitar'] 		= $data['LibretaMilitar'];
						$datos['DistritoMilitar'] 		= $data['DistritoMilitar'];
						$datos['LicenciaConduccion'] 	= $data['LicenciaConduccion'];
						$datos['TarjetaProfesional'] 	= $data['TarjetaProfesional'];

						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET 
									FechaNacimiento 		= :FechaNacimiento, 
									IdCiudadNacimiento 		= :IdCiudadNacimiento, 
									Genero 					= :Genero, 
									EstadoCivil 			= :EstadoCivil, 
									FactorRH 				= :FactorRH, 
									talla 					= :talla, 
									LibretaMilitar 			= :LibretaMilitar, 
									DistritoMilitar 		= :DistritoMilitar, 
									LicenciaConduccion 		= :LicenciaConduccion, 
									TarjetaProfesional 		= :TarjetaProfesional, 
									FechaActualizacion 		= getDate() 
								WHERE EMPLEADOS.Id 			= :Id;
						EOD;

						$resp = $this->actualizar($query, $datos);
					}
					else
						$resp = false;

					break;

				case 3:
					$datos = array();
					$datos['Id'] 		= $data['Id'];
					$datos['Direccion'] = strtoupper(trim($data['Direccion']));
					$datos['Barrio'] 	= strtoupper(trim($data['Barrio']));
					$datos['Localidad'] = strtoupper(trim($data['Localidad']));
					$datos['IdCiudad'] 	= $data['IdCiudad'];
					$datos['Email'] 	= strtoupper(trim($data['Email']));
					$datos['Telefono'] 	= trim($data['Telefono']);
					$datos['Celular'] 	= trim($data['Celular']);

					$query = <<<EOD
						UPDATE EMPLEADOS 
							SET 
								Direccion 				= :Direccion, 
								Barrio 					= :Barrio, 
								Localidad 				= :Localidad, 
								IdCiudad 				= :IdCiudad, 
								Email 					= :Email, 
								Telefono 				= :Telefono, 
								Celular 				= :Celular, 
								FechaActualizacion 		= getDate() 
							WHERE EMPLEADOS.Id 			= :Id;
					EOD;

					$resp = $this->actualizar($query, $datos);

					break;

				case 4:
					$datos = array();
					$datos['Id'] 					= $data['Id'];
					$datos['IdEPS'] 				= $data['IdEPS'];
					$datos['IdFondoCesantias'] 		= $data['IdFondoCesantias'];
					$datos['IdFondoPensiones'] 		= $data['IdFondoPensiones'];
					$datos['IdBanco'] 				= $data['IdBanco'];
					$datos['TipoCuentaBancaria'] 	= $data['TipoCuentaBancaria'];
					$datos['CuentaBancaria'] 		= $data['CuentaBancaria'];

					$query = <<<EOD
						UPDATE EMPLEADOS 
							SET 
								IdEPS 					= :IdEPS, 
								IdFondoCesantias 		= :IdFondoCesantias, 
								IdFondoPensiones 		= :IdFondoPensiones, 
								IdBanco 				= :IdBanco, 
								TipoCuentaBancaria 		= :TipoCuentaBancaria, 
								CuentaBancaria 			= :CuentaBancaria, 
								FechaActualizacion 		= getDate() 
							WHERE EMPLEADOS.Id 			= :Id;
					EOD;

					$resp = $this->actualizar($query, $datos);

					break;

				case 5:
					$datos = array();
					$datos['Id'] 				= $data['Id'];
					$datos['IdCargo'] 			= $data['IdCargo'];
					$datos['PerfilProfesional'] = strtoupper($data['PerfilProfesional']);

					$query = <<<EOD
						UPDATE EMPLEADOS 
							SET 
								IdCargo 				= :IdCargo, 
								PerfilProfesional 		= :PerfilProfesional, 
								FechaActualizacion 		= getDate() 
							WHERE EMPLEADOS.Id 			= :Id;
					EOD;

					$resp = $this->actualizar($query, $datos);

					break;

				case 6:
					if (! empty($data['Empresa']))
					{
						$datos = array();
						$datos['Id'] 				= $data['Id'];
						$datos['Empresa'] 			= strtoupper($data['Empresa']);
						$datos['IdCiudad'] 			= $data['IdCiudadEmpresa'];
						$datos['Cargo'] 			= strtoupper($data['CargoEmpresa']);
						$datos['JefeInmediato'] 	= strtoupper($data['JefeInmediato']);
						$datos['Telefono'] 			= $data['TelefonoEmpresa'];
						$datos['FechaIngreso'] 		= $data['FechaIngresoEmpresa'];
						$datos['FechaRetiro'] 		= $data['FechaRetiroEmpresa'];
						$datos['Responsabilidades'] = strtoupper($data['Responsabilidades']);

						$query = <<<EOD
							INSERT INTO EXPERIENCIALABORAL 
								(IdEmpleado, Empresa, IdCiudad, Cargo, JefeInmediato, Telefono, FechaIngreso, FechaRetiro, Responsabilidades) 
							VALUES (
								:Id, 
								:Empresa, 
								:IdCiudad, 
								:Cargo, 
								:JefeInmediato, 
								:Telefono, 
								:FechaIngreso, 
								:FechaRetiro, 
								:Responsabilidades 
							);
						EOD;

						$resp = $this->actualizar($query, $datos);
					}
					else
						$resp = false;

					break;

				case 7:
					if (! empty($data['CentroEducativoF']))
					{
						$datos = array();
						$datos['Id'] 				= $data['Id'];
						$datos['CentroEducativo'] 	= strtoupper($data['CentroEducativoF']);
						$datos['NivelAcademico'] 	= $data['NivelAcademicoF'];
						$datos['Estudio'] 			= strtoupper($data['EstudioF']);
						$datos['Estado'] 			= $data['EstadoF'];
						$datos['AnoInicio'] 		= $data['AnoInicioF'];
						$datos['MesInicio'] 		= $data['MesInicioF'];
						$datos['AnoFinalizacion'] 	= $data['AnoFinalizacionF'];
						$datos['MesFinalizacion'] 	= $data['MesFinalizacionF'];

						$query = <<<EOD
							INSERT INTO EDUCACIONEMPLEADO 
							(IdEmpleado, TipoEducacion, CentroEducativo, NivelAcademico, Estudio, Estado, AnoInicio, MesInicio, AnoFinalizacion, MesFinalizacion) 
							VALUES (
								:Id, 
								1, 
								:CentroEducativo, 
								:NivelAcademico, 
								:Estudio, 
								:Estado, 
								:AnoInicio, 
								:MesInicio, 
								:AnoFinalizacion, 
								:MesFinalizacion 
							);
						EOD;

						$resp = $this->actualizar($query, $datos);
					}
					else
						$resp = false;

					break;

				case 8:
					if (! empty($data['CentroEducativoNF']))
					{
						$datos = array();
						$datos['Id'] 				= $data['Id'];
						$datos['CentroEducativo'] 	= strtoupper($data['CentroEducativoNF']);
						$datos['NivelAcademico'] 	= $data['NivelAcademicoNF'];
						$datos['Estudio'] 			= strtoupper($data['EstudioNF']);
						$datos['Estado'] 			= $data['EstadoNF'];
						$datos['AnoInicio'] 		= $data['AnoInicioNF'];
						$datos['MesInicio'] 		= $data['MesInicioNF'];
						$datos['AnoFinalizacion'] 	= $data['AnoFinalizacionNF'];
						$datos['MesFinalizacion'] 	= $data['MesFinalizacionNF'];

						$query = <<<EOD
							INSERT INTO EDUCACIONEMPLEADO 
							(IdEmpleado, TipoEducacion, CentroEducativo, NivelAcademico, Estudio, Estado, AnoInicio, MesInicio, AnoFinalizacion, MesFinalizacion) 
							VALUES (
								:Id, 
								2, 
								:CentroEducativo, 
								:NivelAcademico, 
								:Estudio, 
								:Estado, 
								:AnoInicio, 
								:MesInicio, 
								:AnoFinalizacion, 
								:MesFinalizacion 
							);
						EOD;

						$resp = $this->actualizar($query, $datos);
					}
					else
						$resp = false;

					break;

				case 9:
					if (! empty($data['IdIdioma']))
					{
						$datos = array();
						$datos['Id'] 		= $data['Id'];
						$datos['IdIdioma'] 	= $data['IdIdioma'];
						$datos['Nivel'] 	= $data['NivelIdioma'];

						$query = <<<EOD
							INSERT INTO IDIOMASEMPLEADO 
								(IdEmpleado, IdIdioma, Nivel) 
								VALUES (
									:Id, 
									:IdIdioma, 
									:Nivel 
								);
						EOD;

						$resp = $this->actualizar($query, $datos);
					}
					else
						$resp = false;

					break;

				case 10:
					if (! empty($data['Conocimiento']))
					{
						$datos = array();
						$datos['Id'] 			= $data['Id'];
						$datos['Conocimiento'] 	= strtoupper($data['Conocimiento']);
						$datos['Nivel'] 		= $data['NivelConocimiento'];

						$query = <<<EOD
							INSERT INTO OTROSCONOCIMIENTOSEMPLEADO 
							(IdEmpleado, Conocimiento, Nivel) 
							VALUES (
								:Id, 
								:Conocimiento, 
								:Nivel 
							);
						EOD;

						$resp = $this->actualizar($query, $datos);
					}
					else
						$resp = false;

					break;

				case 11:
					if (! empty($data['NombreContacto']))
					{
						$datos = array();
						$datos['Id'] 			= $data['Id'];
						$datos['Nombre'] 		= strtoupper($data['NombreContacto']);
						$datos['Telefono'] 		= $data['TelefonoContacto'];
						$datos['Parentesco'] 	= $data['ParentescoContacto'];

						$query = <<<EOD
							INSERT INTO CONTACTOSEMPLEADO 
							(IdEmpleado, Nombre, Telefono, Parentesco) 
							VALUES (
								:Id, 
								:Nombre, 
								:Telefono, 
								:Parentesco 
							);
						EOD;

						$resp = $this->actualizar($query, $datos);
					}
					else
						$resp = false;

					break;

				case 12:
					// CARGA DE ARCHIVOS CANDIDATO
					$cDirectorio = str_replace(" ","",'documents/' . $data['Documento'] . '_' . strtoupper($data['Apellido1']) . '_' . strtoupper($data['Apellido2']) . '_' . strtoupper($data['Nombre1']) . '_' . strtoupper($data['Nombre2']));

					if	( ! is_dir($cDirectorio) )
						mkdir($cDirectorio);

					if	( ! is_dir($cDirectorio . '/HV') )
						mkdir($cDirectorio . '/HV');

					if	( ! empty($_FILES['Fotografia']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/HV/' . $data['Documento'] . '_FOTOGRAFIA.' . pathinfo($_FILES['Fotografia']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['Fotografia']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['DocumentoIdentidad']['name']) )	
					{
						$cArchivoDestino = $cDirectorio . '/HV/' . $data['Documento'] . '_DOCUMENTO_IDENTIDAD.' . pathinfo($_FILES['DocumentoIdentidad']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['DocumentoIdentidad']['tmp_name'], $cArchivoDestino);
					}	

					if	( ! empty($_FILES['HojaVida']['name']) )	
					{
						$cArchivoDestino = $cDirectorio . '/HV/' . $data['Documento'] . '_HOJA_DE_VIDA.' . pathinfo($_FILES['HojaVida']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['HojaVida']['tmp_name'], $cArchivoDestino);
					}	

					if	( ! empty($_FILES['TarjetaProfesional']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/HV/' . $data['Documento'] . '_TARJETA_PROFESIONAL.' . pathinfo($_FILES['TarjetaProfesional']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['TarjetaProfesional']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['CertificacionBancaria']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/HV/' . $data['Documento'] . '_CERTIFICACION_BANCARIA.' . pathinfo($_FILES['CertificacionBancaria']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['CertificacionBancaria']['tmp_name'], $cArchivoDestino);
					}

					if	( ! is_dir($cDirectorio . '/SEGURIDAD_SOCIAL') )
						mkdir($cDirectorio . '/SEGURIDAD_SOCIAL');

					if	( ! empty($_FILES['CertificadoFondoPensiones']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/SEGURIDAD_SOCIAL/' . $data['Documento'] . '_CERTIFICADO_FONDO_PENSIONES.' . pathinfo($_FILES['CertificadoFondoPensiones']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['CertificadoFondoPensiones']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['CertificadoRegimenEps']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/SEGURIDAD_SOCIAL/' . $data['Documento'] . '_CERTIFICADO_REGIMENES_EPS.' . pathinfo($_FILES['CertificadoRegimenEps']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['CertificadoRegimenEps']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['CertificadoEPS']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/SEGURIDAD_SOCIAL/' . $data['Documento'] . '_CERTIFICADO_EPS.' . pathinfo($_FILES['CertificadoEPS']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['CertificadoEPS']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['CertificadoFondoCesantias']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/SEGURIDAD_SOCIAL/' . $data['Documento'] . '_CERTIFICADO_FONDO_CESANTIAS.' . pathinfo($_FILES['CertificadoFondoCesantias']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['CertificadoFondoCesantias']['tmp_name'], $cArchivoDestino);
					}

					if	( ! is_dir($cDirectorio . '/SOPORTES_ACADEMICOS') )
						mkdir($cDirectorio . '/SOPORTES_ACADEMICOS');

					for ($i = 0; $i < count($_FILES['CertificadosAcademicos']['name']); $i++) 
					{ 
						if	( ! empty($_FILES['CertificadosAcademicos']['name'][$i]) )
						{
							$cArchivoDestino = $cDirectorio . '/SOPORTES_ACADEMICOS/' . $data['Documento'] . '_CERTIFICADO_ACADEMICO_' . $i . '.' . pathinfo($_FILES['CertificadosAcademicos']['name'][$i], PATHINFO_EXTENSION);
							move_uploaded_file($_FILES['CertificadosAcademicos']['tmp_name'][$i], $cArchivoDestino);
						}
					}

					if	( ! is_dir($cDirectorio . '/SOPORTES_LABORALES') )
						mkdir($cDirectorio . '/SOPORTES_LABORALES');

					for ($i = 0; $i < count($_FILES['CertificadosLaborales']['name']); $i++) 
					{ 
						if	( ! empty($_FILES['CertificadosLaborales']['name'][$i]) )
						{
							$cArchivoDestino = $cDirectorio . '/SOPORTES_LABORALES/' . $data['Documento'] . '_CERTIFICADO_LABORAL_' . $i . '.' . pathinfo($_FILES['CertificadosLaborales']['name'][$i], PATHINFO_EXTENSION);
							move_uploaded_file($_FILES['CertificadosLaborales']['tmp_name'][$i], $cArchivoDestino);
						}
					}

					$resp = false;

					break;

				case 13:
					// CARGA DE ARCHIVOS GRUPOS POBLACIONALES
					$cDirectorio = str_replace(" ","",'documents/' . $data['Documento'] . '_' . strtoupper($data['Apellido1']) . '_' . strtoupper($data['Apellido2']) . '_' . strtoupper($data['Nombre1']) . '_' . strtoupper($data['Nombre2']));

					if	( ! is_dir($cDirectorio) )
						mkdir($cDirectorio);

					if	( ! is_dir($cDirectorio . '/GRUPOS_POBLACIONALES') )
						mkdir($cDirectorio . '/GRUPOS_POBLACIONALES');

					if	( ! empty($_FILES['EnPobrezaExtrema']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/GRUPOS_POBLACIONALES/' . $data['Documento'] . '_EN_POBREZA_EXTREMA.' . pathinfo($_FILES['EnPobrezaExtrema']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['EnPobrezaExtrema']['tmp_name'], $cArchivoDestino);
						$data['GrupoPoblacional'] += 1;
					}

					if	( ! empty($_FILES['Desplazado']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/GRUPOS_POBLACIONALES/' . $data['Documento'] . '_DESPLAZADO.' . pathinfo($_FILES['Desplazado']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['Desplazado']['tmp_name'], $cArchivoDestino);
						$data['GrupoPoblacional'] += 2;
					}

					if	( ! empty($_FILES['EnReincorporacion']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/GRUPOS_POBLACIONALES/' . $data['Documento'] . '_EN_REINCORPORACION.' . pathinfo($_FILES['EnReincorporacion']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['EnReincorporacion']['tmp_name'], $cArchivoDestino);
						$data['GrupoPoblacional'] += 4;
					}

					if	( ! empty($_FILES['AdultoMayor']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/GRUPOS_POBLACIONALES/' . $data['Documento'] . '_ADULTO_MAYOR.' . pathinfo($_FILES['AdultoMayor']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['AdultoMayor']['tmp_name'], $cArchivoDestino);
						$data['GrupoPoblacional'] += 8;
					}

					if	( ! empty($_FILES['CabezaHogar']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/GRUPOS_POBLACIONALES/' . $data['Documento'] . '_CABEZA_HOGAR.' . pathinfo($_FILES['CabezaHogar']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['CabezaHogar']['tmp_name'], $cArchivoDestino);
						$data['GrupoPoblacional'] += 16;
					}

					if	( ! empty($_FILES['Discapacitado']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/GRUPOS_POBLACIONALES/' . $data['Documento'] . '_DISCAPACITADO.' . pathinfo($_FILES['Discapacitado']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['Discapacitado']['tmp_name'], $cArchivoDestino);
						$data['GrupoPoblacional'] += 32;
					}

					if	( ! empty($_FILES['ComunidadLGBTI']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/GRUPOS_POBLACIONALES/' . $data['Documento'] . '_COMUNIDAD_LGBTI.' . pathinfo($_FILES['ComunidadLGBTI']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['ComunidadLGBTI']['tmp_name'], $cArchivoDestino);
						$data['GrupoPoblacional'] += 64;
					}

					if	( ! empty($_FILES['Negritudes']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/GRUPOS_POBLACIONALES/' . $data['Documento'] . '_NEGRITUDES.' . pathinfo($_FILES['Negritudes']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['Negritudes']['tmp_name'], $cArchivoDestino);
						$data['GrupoPoblacional'] += 128;
					}

					if	( ! empty($_FILES['Indigenas']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/GRUPOS_POBLACIONALES/' . $data['Documento'] . '_INDIGENAS.' . pathinfo($_FILES['Indigenas']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['Indigenas']['tmp_name'], $cArchivoDestino);
						$data['GrupoPoblacional'] += 256;
					}

					$GrupoPoblacional = $data['GrupoPoblacional'];
					$Id = $data['Id'];

					$query = <<<EOD
						UPDATE EMPLEADOS 
							SET 
							GrupoPoblacional 	= $GrupoPoblacional, 
							FechaActualizacion 	= getDate() 
							WHERE EMPLEADOS.Id 	= $Id;
					EOD;

					$resp = $this->query($query);

					break;

				case 14:
					// CARGA DE ARCHIVOS DE SELECCION
					$cDirectorio = str_replace(" ","",'documents/' . $data['Documento'] . '_' . strtoupper($data['Apellido1']) . '_' . strtoupper($data['Apellido2']) . '_' . strtoupper($data['Nombre1']) . '_' . strtoupper($data['Nombre2']));

					if	( ! is_dir($cDirectorio) )
						mkdir($cDirectorio);

					if	( ! is_dir($cDirectorio . '/ANTECEDENTES') )
						mkdir($cDirectorio . '/ANTECEDENTES');

					if( ! is_dir($cDirectorio . '/HV') ){
						mkdir($cDirectorio . '/HV');						
					}

					if	( ! empty($_FILES['certificadoAprendiz']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/HV/' . $data['Documento'] . '_CERTIFICADO_APRENDIZ.' . pathinfo($_FILES['certificadoAprendiz']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['certificadoAprendiz']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['casoArandaServiceDesk']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/PRUEBAS_SICOTECNICAS/' . $data['Documento'] . '_CASO_SERVICE_DESK.' . pathinfo($_FILES['casoArandaServiceDesk']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['casoArandaServiceDesk']['tmp_name'], $cArchivoDestino);
					}


					if	( ! empty($_FILES['AntecedentesContraloria']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/ANTECEDENTES/' . $data['Documento'] . '_ANTECEDENTES_CONTRALORIA.' . pathinfo($_FILES['AntecedentesContraloria']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['AntecedentesContraloria']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['AntecedentesProcuraduria']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/ANTECEDENTES/' . $data['Documento'] . '_ANTECEDENTES_PROCURADURIA.' . pathinfo($_FILES['AntecedentesProcuraduria']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['AntecedentesProcuraduria']['tmp_name'], $cArchivoDestino);
					}

					

					if	( ! empty($_FILES['AntecedentesPolicia']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/ANTECEDENTES/' . $data['Documento'] . '_ANTECEDENTES_POLICIA.' . pathinfo($_FILES['AntecedentesPolicia']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['AntecedentesPolicia']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['InhabilidadesSexuales']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/ANTECEDENTES/' . $data['Documento'] . '_INHABILIDDES_SEXUALES.' . pathinfo($_FILES['InhabilidadesSexuales']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['InhabilidadesSexuales']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['ConsultaInfolaft']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/ANTECEDENTES/' . $data['Documento'] . '_CONSULTA_INFOLAFT.' . pathinfo($_FILES['ConsultaInfolaft']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['ConsultaInfolaft']['tmp_name'], $cArchivoDestino);
					}

					if	( ! is_dir($cDirectorio . '/PRUEBAS_SICOTECNICAS') )
						mkdir($cDirectorio . '/PRUEBAS_SICOTECNICAS');

					if	( ! empty($_FILES['InformeSeleccion']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/PRUEBAS_SICOTECNICAS/' . $data['Documento'] . '_INFORME_SELECCION.' . pathinfo($_FILES['InformeSeleccion']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['InformeSeleccion']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['EstudioSeguridad']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/PRUEBAS_SICOTECNICAS/' . $data['Documento'] . '_ESTUDIO_SEGURIDAD.' . pathinfo($_FILES['EstudioSeguridad']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['EstudioSeguridad']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['Prueba360']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/PRUEBAS_SICOTECNICAS/' . $data['Documento'] . '_PRUEBA_360.' . pathinfo($_FILES['Prueba360']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['Prueba360']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['PruebaTecnica']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/PRUEBAS_SICOTECNICAS/' . $data['Documento'] . '_PRUEBA_TECNICA.' . pathinfo($_FILES['PruebaTecnica']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['PruebaTecnica']['tmp_name'], $cArchivoDestino);

						$IdEmpleado = $data['Id'];

						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET SEL_PruebaTecnica = 1 
								WHERE EMPLEADOS.Id = $IdEmpleado;
						EOD;

						$this->query($query);
					}

					if	( ! empty($_FILES['PruebaOptimo']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/PRUEBAS_SICOTECNICAS/' . $data['Documento'] . '_PRUEBA_OPTIMO.' . pathinfo($_FILES['PruebaOptimo']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['PruebaOptimo']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['PruebaOrtografia']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/PRUEBAS_SICOTECNICAS/' . $data['Documento'] . '_PRUEBA_ORTOGRAFIA.' . pathinfo($_FILES['PruebaOrtografia']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['PruebaOrtografia']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['RUAF']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/PRUEBAS_SICOTECNICAS/' . $data['Documento'] . '_RUAF.' . pathinfo($_FILES['RUAF']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['RUAF']['tmp_name'], $cArchivoDestino);
					}

					if	( ! empty($_FILES['ExamenMedico']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/PRUEBAS_SICOTECNICAS/' . $data['Documento'] . '_EXAMEN_MEDICO.' . pathinfo($_FILES['ExamenMedico']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['ExamenMedico']['tmp_name'], $cArchivoDestino);

						$IdEmpleado = $data['Id'];

						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET SEL_ExamenesMedicos = 1 
								WHERE EMPLEADOS.Id = $IdEmpleado;
						EOD;

						$this->query($query);
					}

					if	( ! empty($_FILES['RecomendacionesMedicas']['name']) )
					{
						$cArchivoDestino = $cDirectorio . '/PRUEBAS_SICOTECNICAS/' . $data['Documento'] . '_RECOMENDACIONES_MEDICAS.' . pathinfo($_FILES['RecomendacionesMedicas']['name'], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['RecomendacionesMedicas']['tmp_name'], $cArchivoDestino);
					}

					$IdEmpleado = $data['Id'];
					
					if ($data['SEL_DocumentosActualizados'])
					{
						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET SEL_DocumentosActualizados = 1 
								WHERE EMPLEADOS.Id = $IdEmpleado;
						EOD;

						$this->query($query);
					}
					else
					{
						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET SEL_DocumentosActualizados = 0 
								WHERE EMPLEADOS.Id = $IdEmpleado;
						EOD;

						$this->query($query);
					}

					$resp = false;

					break;

				case 15:
					$datos = array();
					$datos['Id'] 					= $data['Id'];
					$datos['IdCargo'] 				= $data['IdCargo'];
					$datos['IdJefe'] 				= $data['IdJefe'];
					$datos['IdCiudadTrabajo'] 		= $data['IdCiudadTrabajo'];
					$datos['IdCentro'] 				= $data['IdCentro'];
					$datos['IdProyecto'] 			= $data['IdProyecto'];
					$datos['Vicepresidencia'] 		= $data['Vicepresidencia'];
					$datos['IdSede'] 				= $data['IdSede'];
					$datos['TipoContrato'] 			= $data['TipoContrato'];
					$datos['ModalidadTrabajo'] 		= $data['ModalidadTrabajo'];
					$datos['SueldoBasico'] 			= $data['SueldoBasico'];
					$datos['Observaciones'] 		= strtoupper($data['Observaciones']);
					$datos['FechaIngreso'] 			= $data['FechaIngreso'];
					$datos['InstitucionDeFormacion'] 	= (empty($data['InstitucionDeFormacion']) ? NULL : $data['InstitucionDeFormacion']);
					$datos['EspecialidadAprendiz'] 	= (empty($data['EspecialidadAprendiz']) ? NULL : $data['EspecialidadAprendiz']);
					$datos['salarioPractica'] 		= (empty($data['salarioPractica']) ? 0 : $data['salarioPractica']);
					$datos['FechaFinEtapaLectiva'] 	= (empty($data['FechaFinEtapaLectiva']) ? NULL : $data['FechaFinEtapaLectiva']);
					$datos['duracionContrato'] 		= (empty($data['duracionContrato']) ? 0 : $data['duracionContrato']);
					$datos['FechaInicioEtapaProductiva'] 	= (empty($data['FechaInicioEtapaProductiva']) ? NULL : $data['FechaInicioEtapaProductiva']);
					$datos['FechaVencimiento'] 		= $data['FechaVencimiento'];
					$datos['SubsidioTransporte'] 	= $data['SubsidioTransporte'];
					$datos['PeriodicidadPago'] 		= $data['PeriodicidadPago'];
					$datos['HorasMes'] 				= $data['HorasMes'];
					$datos['DiasAno'] 				= $data['DiasAno'];
					$datos['MetodoRetencion'] 		= $data['MetodoRetencion'];

					$query = <<<EOD
						UPDATE EMPLEADOS 
							SET 
								IdCargo					 	= :IdCargo, 
								IdJefe					 	= :IdJefe, 
								IdCiudadTrabajo				= :IdCiudadTrabajo, 
								IdCentro					= :IdCentro, 
								IdProyecto					= :IdProyecto, 
								Vicepresidencia				= :Vicepresidencia, 
								IdSede						= :IdSede, 
								TipoContrato				= :TipoContrato, 
								ModalidadTrabajo			= :ModalidadTrabajo, 
								SueldoBasico				= :SueldoBasico, 
								duracionContrato		    = :duracionContrato,
								Observaciones				= :Observaciones, 
								FechaIngreso				= :FechaIngreso, 
								EspecialidadAprendiz    	= :EspecialidadAprendiz,
								salarioPractica    			= :salarioPractica,
								InstitucionDeFormacion    	= :InstitucionDeFormacion,
								FechaFinEtapaLectiva      	= :FechaFinEtapaLectiva,
								FechaInicioEtapaProductiva 	= :FechaInicioEtapaProductiva,
								FechaVencimiento			= :FechaVencimiento, 
								SubsidioTransporte			= :SubsidioTransporte, 
								PeriodicidadPago			= :PeriodicidadPago, 
								HorasMes					= :HorasMes, 
								DiasAno						= :DiasAno, 
								MetodoRetencion				= :MetodoRetencion, 
								SEL_CondicionesLaborales 	= 1, 
								FechaActualizacion 			= getDate() 
							WHERE EMPLEADOS.Id 				= :Id;
					EOD;

					$resp = $this->actualizar($query, $datos);

					break;
			}

			return $resp;
		}

		public function guardarLogEmpleado($data)
		{
			$query = <<<EOD
				INSERT INTO LOGEMPLEADOS 
					(IdEmpleado, Campo, ValorAnterior, ValorActual, IdUsuario)
					VALUES (
						:IdEmpleado, 
						:Campo, 
						:ValorAnterior, 
						:ValorActual,
						:IdUsuario);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
	}
?>
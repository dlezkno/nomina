<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	if ($data['reg'])
	{
		$SelectTipoIdentificacion = getSelect('TipoIdentificacion', $data['reg']['TipoIdentificacion'], '', 'PARAMETROS.Valor');
		$SelectGenero = getSelect('Genero', $data['reg']['Genero'], '', 'PARAMETROS.Valor');
		$SelectEstadoCivil = getSelect('EstadoCivil', $data['reg']['EstadoCivil'], '', 'PARAMETROS.Valor');
		$SelectFactorRH = getSelect('FactorRH', $data['reg']['FactorRH'], '', 'PARAMETROS.Valor');

		$SelectTipoContrato = getSelect('TipoContrato', $data['reg']['TipoContrato'], '', 'PARAMETROS.Valor');
		$SelectModalidadTrabajo = getSelect('ModalidadTrabajo', $data['reg']['ModalidadTrabajo'], '', 'PARAMETROS.Valor');
		$SelectSubsidioTransporte = getSelect('SubsidioTransporte', $data['reg']['SubsidioTransporte'], '', 'PARAMETROS.Valor');
		$SelectPeriodicidadPago = getSelect('Periodicidad', $data['reg']['PeriodicidadPago'], '', 'PARAMETROS.Valor');

		$SelectRegimenCesantias = getSelect('RegimenCesantias', $data['reg']['RegimenCesantias'], '', 'PARAMETROS.Valor');
		$SelectFormaDePago = getSelect('FormaDePago', $data['reg']['FormaDePago'], '', 'PARAMETROS.Valor');
		$SelectTipoCuentaBancaria = getSelect('TipoCuentaBancaria', $data['reg']['TipoCuentaBancaria'], '', 'PARAMETROS.Valor');

		$SelectMetodoRetencion = getSelect('MetodoRetencion', $data['reg']['MetodoRetencion'], '', 'PARAMETROS.Valor');

		$ciudades = getTabla('CIUDADES', '', 'CIUDADES.Orden,CIUDADES.Nombre');

		$SelectCiudadExpedicion = '';
		$SelectCiudadNacimiento = '';
		$SelectCiudad = '';
		$SelectCiudadEmpresa = '';
		$SelectCiudadTrabajo = '';
		
		for ($i = 0; $i < count($ciudades); $i++) 
		{ 
			if	($ciudades[$i]['id'] == $data['reg']['IdCiudadExpedicion'])
				$SelectCiudadExpedicion .= '<option selected value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
			else
				$SelectCiudadExpedicion .= '<option value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';

			if	($ciudades[$i]['id'] == $data['reg']['IdCiudadNacimiento'])
				$SelectCiudadNacimiento .= '<option selected value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
			else
				$SelectCiudadNacimiento .= '<option value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';

			if	($ciudades[$i]['id'] == $data['reg']['IdCiudad'])
				$SelectCiudad .= '<option selected value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
			else
				$SelectCiudad .= '<option value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';

			// if	($ciudades[$i]['id'] == $data['regEmpNuevo']['IdCiudad'])
			// 	$SelectCiudadEmpresa .= '<option selected value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
			// else
			// 	$SelectCiudadEmpresa .= '<option value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';

			if	($ciudades[$i]['id'] == $data['reg']['IdCiudadTrabajo'])
				$SelectCiudadTrabajo .= '<option selected value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
			else
				$SelectCiudadTrabajo .= '<option value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
		}

		// $paises = getTabla('PAISES', '', 'PAISES.Orden,PAISES.Nombre1');

		// $SelectPais = '';
		
		// for ($i = 0; $i < count($paises); $i++)
		// { 
		// 	if	($paises[$i]['id'] == $data['reg']['IdPais'])
		// 		$SelectPais .= '<option selected value=' . $paises[$i]['id'] . '>' . trim($paises[$i]['nombre1']) . '</option>';
		// 	else
		// 		$SelectPais .= '<option value=' . $paises[$i]['id'] . '>' . trim($paises[$i]['nombre1']) . '</option>';
		// }

		// EL CARGO SELECCIONADO ES EL ASIGNADO Y NO PERMITE LECTURAS ADICIONALES
		$cargos = getTabla('CARGOS', '', 'CARGOS.Nombre');

		$SelectCargo = '';
		
		for ($i = 0; $i < count($cargos); $i++) 
		{ 
			if	($cargos[$i]['id'] == $data['reg']['IdCargo'])
				$SelectCargo .= '<option selected value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';
			else
				$SelectCargo .= '<option value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';
		}

		// SE CONSULTA LA INFORMACION DEL PERFIL
		if ($cargos[0]['idcargobase'] > 0)
			$regPerfil = getRegistro('PERFILES', 0, 'PERFILES.IdCargoBase = ' . $cargos[0]['idcargobase']);
		else
			$regPerfil = getRegistro('PERFILES', 0, 'PERFILES.IdCargoBase = ' . $cargos[0]['id']);

		if ($regPerfil)
		{
			$SelectDependencia = '';

			$SelectNivelAcademico = getSelect('NivelAcademico', $regPerfil['nivelacademico'], '', 'PARAMETROS.Valor');

			$Estudios = $regPerfil['estudios'];
			$ExperienciaLaboral = $regPerfil['experiencialaboral'];
			$FormacionAdicional = $regPerfil['formacionadicional'];
			$CondicionesTrabajo = $regPerfil['condicionestrabajo'];
			$MisionCargo = $regPerfil['misioncargo'];
			$Funciones = $regPerfil['funciones'];

			if ($regPerfil['funcionesHSEQ'] > 0)
				$FuncionesHSEQ = getRegistro('PARAMETROS', $regPerfil['funcioneshseq'])['texto'];

			if ($regPerfil['gestionHS'] > 0)
				$GestionHS = getRegistro('PARAMETROS', $regPerfil['gestionHS'])['texto'];

			if ($regPerfil['gestionambiental'] > 0)
				$GestionAmbiental = getRegistro('PARAMETROS', $regPerfil['gestionambiental'])['texto'];

			if ($regPerfil['gestioncalidad'] > 0)
				$GestionCalidad = getRegistro('PARAMETROS', $regPerfil['gestioncalidad'])['texto'];

			if ($regPerfil['gestionSI'] > 0)
				$GestionSI = getRegistro('PARAMETROS', $regPerfil['gestionSI'])['texto'];

			$Responsable = $regPerfil['responsable'];
			$Elabora = $regPerfil['elabora'];
		}
		else
		{
			$SelectDependencia = '';
	
			$SelectNivelAcademico = 0;
	
			$Estudios = '';
			$ExperienciaLaboral = '';
			$FormacionAdicional = '';
			$CondicionesTrabajo = '';
			$MisionCargo = '';
			$Funciones = '';
	
			$FuncionesHSEQ = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FuncionesDelCargo' AND PARAMETROS.Detalle = 'FUNCIONES Y RESPONSABILIDADES DE LOS SISTEMAS DE GESTIÓN HSEQ-SI'")['texto'];
			$GestionHS = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FuncionesDelCargo' AND PARAMETROS.Detalle = 'GESTION DE SEGURIDAD Y SALUD EN EL TRABAJO (HS)'")['texto'];
			$GestionAmbiental = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FuncionesDelCargo' AND PARAMETROS.Detalle = 'GESTION AMBIENTAL (E)'")['texto'];
			$GestionCalidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FuncionesDelCargo' AND PARAMETROS.Detalle = 'GESTION DE CALIDAD (Q)'")['texto'];
			$GestionSI = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FuncionesDelCargo' AND PARAMETROS.Detalle = 'GESTION DE SEGURIDAD DE LA INFORMACION (SI)'")['texto'];
	
			$Responsable = '';
			$Elabora = '';
		}
	
		// $idiomas = getTabla('IDIOMAS', '', 'IDIOMAS.Orden,IDIOMAS.Nombre');

		// $SelectIdioma = '';
		
		// for ($i=0; $i < count($idiomas); $i++) 
		// { 
		// 	if	($idiomas[$i]['id'] == $data['regIdiomaNuevo']['IdIdioma'])
		// 		$SelectIdioma .= '<option selected value=' . $idiomas[$i]['id'] . '>' . trim($idiomas[$i]['nombre']) . '</option>';
		// 	else
		// 		$SelectIdioma .= '<option value=' . $idiomas[$i]['id'] . '>' . trim($idiomas[$i]['nombre']) . '</option>';
		// }

		$centros = getTabla('CENTROS', '', 'CENTROS.Nombre');

		$SelectCentro = '';
		$SelectProyecto = '';
		
		for ($i = 0; $i < count($centros); $i++) 
		{ 
			if	($centros[$i]['id'] == $data['reg']['IdCentro'])
				$SelectCentro .= '<option selected value=' . $centros[$i]['id'] . '>' . $centros[$i]['nombre'] . ' [' . $centros[$i]['centro'] . ']</option>';
			else
				$SelectCentro .= '<option value=' . $centros[$i]['id'] . '>' . $centros[$i]['nombre'] . ' [' . $centros[$i]['centro'] . ']</option>';

			if	($centros[$i]['id'] == $data['reg']['IdProyecto'])
				$SelectProyecto .= '<option selected value=' . $centros[$i]['id'] . '>' . $centros[$i]['nombre'] . ' [' . $centros[$i]['centro'] . ']</option>';
			else
				$SelectProyecto .= '<option value=' . $centros[$i]['id'] . '>' . $centros[$i]['nombre'] . ' [' . $centros[$i]['centro'] . ']</option>';
		}

		$SelectVicepresidencia = getSelect('Vicepresidencia', $data['reg']['Vicepresidencia'], '', 'PARAMETROS.Valor');

		$sedes = getTabla('SEDES', '', 'SEDES.Nombre');

		$SelectSede = '';
		
		for ($i = 0; $i < count($sedes); $i++) 
		{ 
			if	($sedes[$i]['id'] == $data['reg']['IdSede'])
				$SelectSede .= '<option selected value=' . $sedes[$i]['id'] . '>' . trim($sedes[$i]['nombre']) . '</option>';
			else
				$SelectSede .= '<option value=' . $sedes[$i]['id'] . '>' . trim($sedes[$i]['nombre']) . '</option>';
		}

		$categorias = getTabla('CATEGORIAS', '', 'CATEGORIAS.Nombre');

		$SelectCategoria = '';
		
		for ($i = 0; $i < count($categorias); $i++) 
		{ 
			if	($categorias[$i]['id'] == $data['reg']['IdCategoria'])
				$SelectCategoria .= '<option selected value=' . $categorias[$i]['id'] . '>' . trim($categorias[$i]['nombre']) . '</option>';
			else
				$SelectCategoria .= '<option value=' . $categorias[$i]['id'] . '>' . trim($categorias[$i]['nombre']) . '</option>';
		}

		$terceros = getTabla('TERCEROS', '', 'TERCEROS.Nombre');

		$SelectEPS = '';
		$SelectARL = '';
		$SelectFondoCesantias = '';
		$SelectFondoPensiones = '';
		$SelectCajaCompensacion = '';

		for ($i = 0; $i < count($terceros); $i++) 
		{ 
			if ($terceros[$i]['eseps'])
			{
				if	($terceros[$i]['id'] == $data['reg']['IdEPS'])
					$SelectEPS .= '<option selected value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
				else
					$SelectEPS .= '<option value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
			}

			if ($terceros[$i]['esarl'])
			{
				if	($terceros[$i]['id'] == $data['reg']['IdARL'])
					$SelectARL .= '<option selected value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
				else
					$SelectARL .= '<option value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
			}

			if ($terceros[$i]['esfondocesantias'])
			{
				if	($terceros[$i]['id'] == $data['reg']['IdFondoCesantias'])
					$SelectFondoCesantias .= '<option selected value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
				else
					$SelectFondoCesantias .= '<option value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
			}

			if ($terceros[$i]['esfondopensiones'])
			{
				if	($terceros[$i]['id'] == $data['reg']['IdFondoPensiones'])
					$SelectFondoPensiones .= '<option selected value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
				else
					$SelectFondoPensiones .= '<option value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
			}

			if ($terceros[$i]['esccf'])
			{
				if	($terceros[$i]['id'] == $data['reg']['IdCajaCompensacion'])
					$SelectCajaCompensacion .= '<option selected value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
				else
					$SelectCajaCompensacion .= '<option value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
			}
		}

		$bancos = getTabla('BANCOS', '', 'BANCOS.Nombre');

		$SelectBanco = '';
		
		for ($i = 0; $i < count($bancos); $i++) 
		{ 
			if	($bancos[$i]['id'] == $data['reg']['IdBanco'])
				$SelectBanco .= '<option selected value=' . $bancos[$i]['id'] . '>' . trim($bancos[$i]['nombre']) . '</option>';
			else
				$SelectBanco .= '<option value=' . $bancos[$i]['id'] . '>' . trim($bancos[$i]['nombre']) . '</option>';
		}

		if (isset($data['reg']['Estado']))
			$EstadoEmpleado = $data['reg']['Estado'];
		else
			$EstadoEmpleado = 0;

		$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'DOCUMENTO'");
		$TipoContrato = $data['reg']['TipoContrato'];

		$query = <<<EOD
			PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND 
			PLANTILLAS.TipoPlantilla = $TipoPlantilla AND 
			PLANTILLAS.TipoContrato = $TipoContrato
		EOD;

		$contratos = getTabla('PLANTILLAS', $query, 'PLANTILLAS.Asunto');
	}
?>
<div id="main">
	<div class="row">
		<div class="content-wrapper-before cyan darken-4"></div>
		<div class="col s12 m12 l12">
			<div class="container">
				<div class="section section-data-tables">
					<div class="card">
						<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
							<div class="row">
								<div class="col s12 m6 l6">
									<h3 class="white-text">Empleados (retirados)</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<?php if ($data['reg']): ?>
							<section class="tabs-vertical mt-1 section">
								<div class="row">
									<div class="col s12 l3">
										<div class="card-panel">
											<ul class="tabs">
												<li class="tab">
													<a href="#pagIdentificacion">
														<i class="material-icons">person_outline</i>
														<span>Identificación</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagPersonal">
														<i class="material-icons">person_outline</i>
														<span>Información personal</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagContactoPersonal">
														<i class="material-icons">location_on</i>
														<span>Información de contacto</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagCondicionesLaborales">
														<i class="material-icons">payment</i>
														<span>Condiciones laborales</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagAfiliaciones">
														<i class="material-icons">chrome_reader_mode</i>
														<span>Afiliaciones</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagTributaria">
														<i class="material-icons">chrome_reader_mode</i>
														<span>Información tributaria</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagPerfilProfesional">
														<i class="material-icons">accessibility</i>
														<span>Perfil profesional</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagPerfil">
														<i class="material-icons">accessibility</i>
														<span>Perfil del cargo</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagExperiencia">
														<i class="material-icons">airline_seat_recline_normal</i>
														<span>Experiencia laboral</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagEducacionFormal">
														<i class="material-icons">school</i>
														<span>Educación formal</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagEducacionNoFormal">
														<i class="material-icons">school</i>
														<span>Educación no formal</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagIdiomas">
														<i class="material-icons">textsms</i>
														<span>Idiomas</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagOtrosConocimientos">
														<i class="material-icons">book</i>
														<span>Otros conocimientos</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagContactos">
														<i class="material-icons">people_outline</i>
														<span>Contactos</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagDocumentos">
														<i class="material-icons">folder_open</i>
														<span>Documentos</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagContratos">
														<i class="material-icons">folder_open</i>
														<span>Contratos</span>
													</a>
												</li>
												<?php if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR OR $_SESSION['Login']['Perfil'] == RRHH): ?>
													<li class="tab">
														<a href="#pagAuditoria">
															<i class="material-icons">search</i>
															<span>Auditoría</span>
														</a>
													</li>
												<?php endif; ?>
											</ul>
										</div>
									</div>

									<div class="col s12 l9">
										<div id="pagIdentificacion" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>IDENTIFICACIÓN</p>
													</div>
												</div>
												<?php 
													$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/HV';

													if (is_dir($cDirectorio))
														$archivo = $cDirectorio . '/' . $data['reg']['Documento'] . '_FOTOGRAFIA.jpg';
													else
													{
														$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . ucwords(strtolower($data['reg']['Apellido1']) . ' ' . strtolower($data['reg']['Apellido2']) . ' ' . strtolower($data['reg']['Nombre1']) . ' ' . strtolower($data['reg']['Nombre2'])) . '/HV';

														if (is_dir($cDirectorio))
															$archivo = $cDirectorio . '/' . $data['reg']['Documento'] . '_Foto.jpg';
													}

													if (isset($archivo) AND ! file_exists($archivo))
														$archivo = str_replace('jpeg', 'jpg', $archivo);

													if (isset($archivo)):
												?>
												<div class="row center-align">
													<img src="<?= SERVERURL . '/' . $archivo ?>" alt="Fotografia"
														class="circle responsive-img" width="150px">
												</div>
												<?php endif; ?>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php
															get(label('Tipo de identificación*'), 'TipoIdentificacion', $SelectTipoIdentificacion, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Documento*'), 'Documento', $data['reg']['Documento'], 'text', 15, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Fecha de expedición*'), 'FechaExpedicion', $data['reg']['FechaExpedicion'], 'date', 10, FALSE, 'required', 'far fa-calendar'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php
															get(label('Lugar de expedición*'), 'IdCiudadExpedicion', $SelectCiudadExpedicion, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Código SAP*'), 'CodigoSAP', $data['reg']['CodigoSAP'], 'text', 15, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Primer apellido*'), 'Apellido1', $data['reg']['Apellido1'], 'text', 25, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Segundo apellido'), 'Apellido2', $data['reg']['Apellido2'], 'text', 25, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Primer nombre*'), 'Nombre1', $data['reg']['Nombre1'], 'text', 25, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Segundo nombre'), 'Nombre2', $data['reg']['Nombre2'], 'text', 25, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php
															get(label('Políticamente expuesta*'), 'PoliticamenteExpuesta', $data['reg']['PoliticamenteExpuesta'], 'checkbox', $data['reg']['PoliticamenteExpuesta'], FALSE, 'required', '');
														?>
													</div>
												</div>
											</div>
										</div>

										<div id="pagPersonal" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>INFORMACIÓN PERSONAL</p>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Fecha nacimiento*'), 'FechaNacimiento', $data['reg']['FechaNacimiento'], 'date', 10, FALSE, 'required', 'far fa-calendar'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php
															get(label('Ciudad de nacimiento*'), 'IdCiudadNacimiento', $SelectCiudadNacimiento, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php
															get(label('Genero*'), 'Genero', $SelectGenero, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php
															get(label('Estado civil*'), 'EstadoCivil', $SelectEstadoCivil, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php
															get(label('Factor RH*'), 'FactorRH', $SelectFactorRH, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php
															get(label('Libreta militar'), 'LibretaMilitar', $data['reg']['LibretaMilitar'], 'text', 20, FALSE, '', 'fas fa-pen');
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php
															get(label('Distrito militar'), 'DistritoMilitar', $data['reg']['DistritoMilitar'], 'text', 3, FALSE, '', 'fas fa-pen');
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php
															get(label('Licencia conducción'), 'LicenciaConduccion', $data['reg']['LicenciaConduccion'], 'text', 20, FALSE, '', 'fas fa-pen');
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php
															get(label('Tarjeta profesional'), 'TarjetaProfesional', $data['reg']['TarjetaProfesional'], 'text', 20, FALSE, '', 'fas fa-pen');
														?>
													</div>
												</div>
												<div class="row">
													<p>GRUPOS POBLACIONALES:</p>
													<br>
													<?php if ($data['reg']['GrupoPoblacional'] == 0): ?>
														<p>NO PERTENECE A NINGÚIN GRUPO POBLACIONAL</p>
													<?php else: ?>
														<?php 
															$GrupoPoblacional = $data['reg']['GrupoPoblacional'];

															if ($GrupoPoblacional >= 256)
															{
																echo "<p>COMUNIDADES INDÍGENAS</p>";
																$GrupoPoblacional -= 256;
															}

															if ($GrupoPoblacional >= 128)
															{
																echo "<p>NEGRITUDES</p>";
																$GrupoPoblacional -= 128;
															}

															if ($GrupoPoblacional >= 64)
															{
																echo "<p>COMUNIDAD LGBTIQ+</p>";
																$GrupoPoblacional -= 64;
															}

															if ($GrupoPoblacional >= 32)
															{
																echo "<p>PERSONA DISCAPACITADA</p>";
																$GrupoPoblacional -= 32;
															}

															if ($GrupoPoblacional >= 16)
															{
																echo "<p>MUJER/MADRE/HOMBRE CABEZA DE HOGAR</p>";
																$GrupoPoblacional -= 16;
															}

															if ($GrupoPoblacional >= 8)
															{
																echo "<p>ADULTO MAYOR</p>";
																$GrupoPoblacional -= 8;
															}

															if ($GrupoPoblacional >= 4)
															{
																echo "<p>EN REINCORPORACIÓN</p>";
																$GrupoPoblacional -= 4;
															}

															if ($GrupoPoblacional >= 2)
															{
																echo "<p>DESPLAZADO</p>";
																$GrupoPoblacional -= 2;
															}

															if ($GrupoPoblacional >= 1)
															{
																echo "<p>EN POBREZA EXTREMA</p>";
																$GrupoPoblacional -= 1;
															}
														?>
													<?php endif; ?>
												</div>
											</div>
										</div>

										<div id="pagContactoPersonal" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>CONTACTO PERSONAL</p>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Dirección*'), 'Direccion', $data['reg']['Direccion'], 'text', 60, FALSE, 'required', 'fas fa-map-marker-alt'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Barrio*'), 'Barrio', $data['reg']['Barrio'], 'text', 25, FALSE, 'required', 'fas fa-map-marker-alt'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Localidad'), 'Localidad', $data['reg']['Localidad'], 'text', 25, FALSE, '', 'fas fa-map-marker-alt'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php
															get(label('Ciudad*'), 'IdCiudad', $SelectCiudad, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('E-Mail*'), 'Email', $data['reg']['Email'], 'email', 100, FALSE, 'required', 'fas fa-paper-plane'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('E-Mail corporativo'), 'EmailCorporativo', $data['reg']['EmailCorporativo'], 'email', 100, FALSE, '', 'fas fa-paper-plane'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('E-Mail proyecto'), 'EmailProyecto', $data['reg']['EmailProyecto'], 'email', 100, FALSE, '', 'fas fa-paper-plane'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Teléfono'), 'Telefono', $data['reg']['Telefono'], 'tel', 15, FALSE, 'required', 'fas fa-phone'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Celular*'), 'Celular', $data['reg']['Celular'], 'tel', 15, FALSE, 'required', 'fas fa-phone'); 
														?>
													</div>
												</div>
											</div>
										</div>

										<div id="pagCondicionesLaborales" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>CONDICIONES LABORALES</p>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Centro de costos*'), 'IdCentro', $SelectCentro, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Proyecto'), 'IdProyecto', $SelectProyecto, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Vicepresidencia*'), 'Vicepresidencia', $SelectVicepresidencia, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Sede'), 'IdSede', $SelectSede, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<!-- <div class="input-field col s6 m6">
														<?php 
															get(label('Tipo de empleado*'), 'TipoEmpleado', $SelectTipoEmpleado, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div> -->
													<div class="input-field col s6 m6">
														<?php 
															get(label('Tipo de contrato*'), 'TipoContrato', $SelectTipoContrato, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Fecha de ingreso*'), 'FechaIngreso', $data['reg']['FechaIngreso'], 'date', 0, FALSE, '', 'fas fa-calendar'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('En perído de prueba hasta*'), 'FechaPeriodoPrueba', $data['reg']['FechaPeriodoPrueba'], 'date', 0, FALSE, '', 'fas fa-calendar'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Fecha vencimiento contrato*'), 'FechaVencimiento', $data['reg']['FechaVencimiento'], 'date', 0, FALSE, '', 'fas fa-calendar'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Prorrogas'), 'Prorrogas', $data['reg']['Prorrogas'], 'number', 2, FALSE, '', 'fas fa-edit'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Modalidad de trabajo*'), 'ModalidadTrabajo', $SelectModalidadTrabajo, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Sueldo básico*'), 'SueldoBasico', $data['reg']['SueldoBasico'], 'number', 12, TRUE, '', 'fas fa-edit'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Observaciones'), 'Observaciones', $data['reg']['Observaciones'], 'textarea', 2, FALSE, '', 'fas fa-edit'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Subsidio de transporte*'), 'SubsidioTransporte', $SelectSubsidioTransporte, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Periodicidad de pago*'), 'PeriodicidadPago', $SelectPeriodicidadPago, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
											</div>
										</div>

										<div id="pagAfiliaciones" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>AFILIACIONES</p>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('E.P.S.*'), 'IdEPS', $SelectEPS, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Caja de Compensación Familiar*'), 'IdCajaCompensacion', $SelectCajaCompensacion, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Régimen cesantías*'), 'RegimenCesantias', $SelectRegimenCesantias, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Factor prestacional'), 'FactorPrestacional', $data['reg']['FactorPrestacional'], 'number', 3, FALSE, '', 'fas fa-edit'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Fondo de cesantías*'), 'IdFondoCesantias', $SelectFondoCesantias, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Fondo de pensiones*'), 'IdFondoPensiones', $SelectFondoPensiones, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Forma de pago*'), 'FormaDePago', $SelectFormaDePago, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Entidad bancaria*'), 'IdBanco', $SelectBanco, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Tipo cuenta bancaria*'), 'TipoCuentaBancaria', $SelectTipoCuentaBancaria, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Cuenta bancaria*'), 'CuentaBancaria', $data['reg']['CuentaBancaria'], 'text', 20, FALSE, '', 'fas fa-edit'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Cuenta bancaria BBVA-Nequi*'), 'CuentaBancaria2', $data['reg']['CuentaBancaria2'], 'text', 20, FALSE, '', 'fas fa-edit'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Categoría*'), 'IdCategoria', $SelectCategoria, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Ciudad de trabajo*'), 'IdCiudadTrabajo', $SelectCiudadTrabajo, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
															?>
													</div>
												</div>
											</div>
										</div>

										<div id="pagTributaria" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>INFORMACIÓN TRIBUTARIA</p>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Método retención fuente*'), 'MetodoRetencion', $SelectMetodoRetencion, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('% Retención fuente'), 'PorcentajeRetencion', $data['reg']['PorcentajeRetencion'], 'number', 6, FALSE, '', 'fas fa-pen'); 
															?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Mayor retención'), 'MayorRetencionFuente', $data['reg']['MayorRetencionFuente'], 'number', 12, FALSE, '', 'fas fa-pen'); 
															?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Deducción por dependientes'), 'DeduccionDependientes', $data['reg']['DeduccionDependientes'], 'checkbox', $data['reg']['DeduccionDependientes'], FALSE, '', ''); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Cuota de vivienda'), 'CuotaVivienda', $data['reg']['CuotaVivienda'], 'number', 12, FALSE, '', 'fas fa-pen'); 
															?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Salud (prepagada y seguros)'), 'SaludYEducacion', $data['reg']['SaludYEducacion'], 'number', 12, FALSE, '', 'fas fa-pen'); 
															?>
													</div>
												</div>
											</div>
										</div>

										<div id="pagPerfilProfesional" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>PERFIL PROFESIONAL</p>
													</div>
												</div>
												<div class="row">
													<div class="col s12">
														<?php 
															get(label('Perfil profesional*'), 'PerfilProfesional', $data['reg']['PerfilProfesional'], 'textarea', 5, FALSE, 'required', 'far fa-edit'); 
														?>
													</div>
												</div>
											</div>
										</div>

										<div id="pagPerfil" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>PERFIL DEL CARGO</p>
													</div>
												</div>
												<div class="row">
													<div class="col s6">
														<?php 
															get(label('Cargo*'), 'IdCargo', $SelectCargo, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
                                                <div class="row">
                                                    <div class="input-field col s6 m6">
                                                        <?php 
															get(label('Dependencia*'), 'IdDependencia', $SelectDependencia, 'select', 0, TRUE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="input-field col s6 m6">
                                                        <?php 
															get(label('Nivel académico*'), 'NivelAcademico', $SelectNivelAcademico, 'select', 0, TRUE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                    <div class="input-field col s6 m6">
                                                        <?php 
															get(label('Estudios*'), 'Estudios', $Estudios, 'textarea', 3, TRUE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="input-field col s6 m6">
                                                        <?php 
															get(label('Experiencia laboral*'), 'ExperienciaLaboral', $ExperienciaLaboral, 'textarea', 3, TRUE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                    <div class="input-field col s6 m6">
                                                        <?php 
															get(label('Formación adicional'), 'FormacionAdicional', $FormacionAdicional, 'textarea', 3, TRUE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="input-field col s6 m6">
                                                        <?php 
															get(label('Condiciones de trabajo*'), 'CondicionesTrabajo', $CondicionesTrabajo, 'textarea', 3, TRUE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                    <div class="input-field col s6 m6">
                                                        <?php 
															get(label('Misión del cargo*'), 'MisionCargo', $MisionCargo, 'textarea', 3, TRUE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="input-field col s12 m12">
                                                        <?php 
															get(label('Funciones y responsabilidades*'), 'Funciones', $Funciones, 'textarea', 3, TRUE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="input-field col s6 m6">
                                                        <?php 
															get(label('Responsable*'), 'Responsable', $Responsable, 'text', 100, TRUE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                    <div class="input-field col s6 m6">
                                                        <?php 
															get(label('Elaboró*'), 'Elabora', $Elabora, 'text', 100, TRUE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                </div>

                                                <ul class="collapsible">
                                                    <li>
                                                        <div class="collapsible-header">
                                                            <i class="material-icons">folder_open</i>
                                                            COMPETENCIAS Y FUNCIONES Y RESPONSABILIDADES SISTEMA DE
                                                            GESTIÓN
                                                        </div>
                                                        <div class="collapsible-body">
                                                            <div class="row">
                                                                <?php 
																	$cDirectorio = 'documents/DocumentosGenericos/';
																	$archivo = 'Competencias'; 
																?>
                                                                <div class="col s4 m4">
                                                                    <?= $archivo ?>
                                                                </div>
                                                                <div class="col s1 m1">
                                                                    <a href="<?= SERVERURL . '/' . $cDirectorio . $archivo . '.pdf'; ?>"
                                                                        target="_blank"
                                                                        class="btn btn-sm teal lighten-3 tooltipped"
                                                                        data-position="bottom"
                                                                        data-tooltip="Ver documento">
                                                                        <i class="material-icons">visibility</i>
                                                                    </a>
                                                                </div>
                                                                <div class="col s1 m1">
                                                                    <a href="<?= SERVERURL . '/' . $cDirectorio . $archivo . '.xlsx'; ?>"
                                                                        download="<?php echo $archivo; ?>"
                                                                        class="btn btn-sm teal lighten-3 tooltipped"
                                                                        data-position="bottom"
                                                                        data-tooltip="Descargar documento">
                                                                        <i class="material-icons">cloud_download</i>
                                                                    </a>
                                                                </div>
                                                                <?php 
																	$cDirectorio = 'documents/DocumentosGenericos/';
																	$archivo = 'FuncionesYResponsabilidadesSistemaGestion'; 
																?>
                                                                <div class="col s4 m4">
                                                                    <?= $archivo ?>
                                                                </div>
                                                                <div class="col s1 m1">
                                                                    <a href="<?= SERVERURL . '/' . $cDirectorio . $archivo . '.pdf'; ?>"
                                                                        target="_blank"
                                                                        class="btn btn-sm teal lighten-3 tooltipped"
                                                                        data-position="bottom"
                                                                        data-tooltip="Ver documento">
                                                                        <i class="material-icons">visibility</i>
                                                                    </a>
                                                                </div>
                                                                <div class="col s1 m1">
                                                                    <a href="<?= SERVERURL . '/' . $cDirectorio . $archivo . '.xlsx'; ?>"
                                                                        download="<?php echo $archivo; ?>"
                                                                        class="btn btn-sm teal lighten-3 tooltipped"
                                                                        data-position="bottom"
                                                                        data-tooltip="Descargar documento">
                                                                        <i class="material-icons">cloud_download</i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="collapsible-header">
                                                            <i class="material-icons">folder_open</i>
                                                            FUNCIONES SISTEMA DE GESTIÓN HSEQ-SI*
                                                        </div>
                                                        <div class="collapsible-body">
                                                            <div class="row">
                                                                <div class="col s12 m12">
																	<?php 
																		get('', 'FuncionesHSEQ', $FuncionesHSEQ, 'textarea', 3, TRUE, '', 'fas fa-ellipsis-v'); 
																	?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="collapsible-header">
                                                            <i class="material-icons">folder_open</i>
                                                            GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
                                                        </div>
                                                        <div class="collapsible-body">
                                                            <div class="row">
                                                                <div class="col s12 m12">
																	<?php 
																		get('', 'GestionHS', $GestionHS, 'textarea', 3, TRUE, '', 'fas fa-ellipsis-v'); 
																	?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="collapsible-header">
                                                            <i class="material-icons">folder_open</i>
                                                            GESTIÓN AMBIENTAL (E)
                                                        </div>
                                                        <div class="collapsible-body">
                                                            <div class="row">
                                                                <div class="col s12 m12">
																	<?php 
																		get('', 'GestionAmbiental', $GestionAmbiental, 'textarea', 3, TRUE, '', 'fas fa-ellipsis-v'); 
																	?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="collapsible-header">
                                                            <i class="material-icons">folder_open</i>
                                                            GESTIÓN DE CALIDAD (Q)
                                                        </div>
                                                        <div class="collapsible-body">
                                                            <div class="row">
                                                                <div class="col s12 m12">
																	<?php 
																		get('', 'GestionCalidad', $GestionCalidad, 'textarea', 3, TRUE, '', 'fas fa-ellipsis-v'); 
																	?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="collapsible-header">
                                                            <i class="material-icons">folder_open</i>
                                                            GESTIÓN DE SEGURIDAD DE LA INFORMACIÓN (SI)
                                                        </div>
                                                        <div class="collapsible-body">
                                                            <div class="row">
                                                                <div class="col s12 m12">
																	<?php 
																		get('', 'GestionSI', $GestionSI, 'textarea', 3, TRUE, '', 'fas fa-ellipsis-v'); 
																	?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
											</div>
										</div>

										<div id="pagExperiencia" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>EXPERIENCIA LABORAL</p>
													</div>
												</div>

												<table>
													<thead>
														<tr>
															<th>EMPRESA</th>
															<th>FECHA INGRESO</th>
															<th>FECHA RETIRO</th>
														</tr>
													</thead>
													<tbody>
														<?php if ($data['regEmp']): ?>
														<?php for ($i = 0; $i < count($data['regEmp']); $i++ ): ?>
														<tr>
															<td><?= $data['regEmp'][$i]['Empresa'] ?></td>
															<td><?= $data['regEmp'][$i]['FechaIngreso'] ?></td>
															<td><?= $data['regEmp'][$i]['FechaRetiro'] ?></td>
														</tr>
														<?php endfor; ?>
														<?php endif; ?>
													</tbody>
												</table>
											</div>
										</div>

										<div id="pagEducacionFormal" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>EDUCACIÓN FORMAL</p>
													</div>
												</div>

												<table>
													<thead>
														<tr>
															<th>CENTRO EDUCATIVO</th>
															<th>NIVEL ACADÉMICO</th>
															<th>FECHA INICIO</th>
															<th>FECHA FINALIZACIÓN</th>
														</tr>
													</thead>
													<tbody>
														<?php if ($data['regEduF']): ?>
														<?php for ($i = 0; $i < count($data['regEduF']); $i++ ): ?>
														<tr>
															<td><?= $data['regEduF'][$i]['CentroEducativo'] ?></td>
															<td><?= $data['regEduF'][$i]['NombreNivelAcademico'] ?>
															</td>
															<td><?= $data['regEduF'][$i]['AnoInicio'] . '-' . $data['regEduF'][$i]['MesInicio'] ?>
															</td>
															<td><?= $data['regEduF'][$i]['AnoFinalizacion'] . '-' . $data['regEduF'][$i]['MesFinalizacion'] ?>
															</td>
														</tr>
														<?php endfor; ?>
														<?php endif; ?>
													</tbody>
												</table>
											</div>
										</div>

										<div id="pagEducacionNoFormal" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>EDUCACIÓN NO FORMAL</p>
													</div>
												</div>

												<table>
													<thead>
														<tr>
															<th>CENTRO EDUCATIVO</th>
															<th>ESTUDIO</th>
															<th>FECHA INICIO</th>
															<th>FECHA FINALIZACIÓN</th>
														</tr>
													</thead>
													<tbody>
														<?php if ($data['regEduNF']): ?>
														<?php for ($i = 0; $i < count($data['regEduNF']); $i++ ): ?>
														<tr>
															<td><?= $data['regEduNF'][$i]['CentroEducativo'] ?></td>
															<td><?= $data['regEduNF'][$i]['Estudio'] ?></td>
															<td><?= $data['regEduNF'][$i]['AnoInicio'] . '-' . $data['regEduNF'][$i]['MesInicio'] ?>
															</td>
															<td><?= $data['regEduNF'][$i]['AnoFinalizacion'] . '-' . $data['regEduNF'][$i]['MesFinalizacion'] ?>
															</td>
														</tr>
														<?php endfor; ?>
														<?php endif; ?>
													</tbody>
												</table>
											</div>
										</div>

										<div id="pagIdiomas" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>IDIOMAS</p>
													</div>
												</div>

												<table>
													<thead>
														<tr>
															<th>IDIOMA</th>
															<th>NIVEL</th>
														</tr>
													</thead>
													<tbody>
														<?php if ($data['regIdiomas']): ?>
														<?php for ($i = 0; $i < count($data['regIdiomas']); $i++ ): ?>
														<tr>
															<td><?= $data['regIdiomas'][$i]['Nombre'] ?></td>
															<td><?= $data['regIdiomas'][$i]['NombreNivelIdioma'] ?>
															</td>
														</tr>
														<?php endfor; ?>
														<?php endif; ?>
													</tbody>
												</table>
											</div>
										</div>

										<div id="pagOtrosConocimientos" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>OTROS CONOCIMIENTOS</p>
													</div>
												</div>

												<table>
													<thead>
														<tr>
															<th>CONOCIMIENTO</th>
															<th>NIVEL</th>
														</tr>
													</thead>
													<tbody>
														<?php if ($data['regOCE']): ?>
														<?php for ($i = 0; $i < count($data['regOCE']); $i++ ): ?>
														<tr>
															<td><?= $data['regOCE'][$i]['Conocimiento'] ?></td>
															<td><?= $data['regOCE'][$i]['NombreNivelConocimiento'] ?>
															</td>
														</tr>
														<?php endfor; ?>
														<?php endif; ?>
													</tbody>
												</table>
											</div>
										</div>

										<div id="pagContactos" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>CONTACTOS</p>
													</div>
												</div>

												<table>
													<thead>
														<tr>
															<th>CONTACTO</th>
															<th>TELÉFONO</th>
															<th>PARENTESCO</th>
														</tr>
													</thead>
													<tbody>
														<?php if ($data['regContactos']): ?>
														<?php for ($i = 0; $i < count($data['regContactos']); $i++ ): ?>
														<tr>
															<td><?= $data['regContactos'][$i]['Nombre'] ?></td>
															<td><?= $data['regContactos'][$i]['Telefono'] ?></td>
															<td><?= $data['regContactos'][$i]['NombreParentesco'] ?>
															</td>
														</tr>
														<?php endfor; ?>
														<?php endif; ?>
													</tbody>
												</table>
											</div>
										</div>

										<div id="pagDocumentos" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>DOCUMENTOS</p>
													</div>
												</div>

												<ul class="collapsible">
													<li>
														<div class="collapsible-header">
															<i class="material-icons">folder_open</i>
															H.V.
														</div>
														<div class="collapsible-body">
															<table>
																<tbody>
																	<?php
																		$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/HV';

																		if (! is_dir($cDirectorio))
																			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . ucwords(strtolower($data['reg']['Apellido1']) . ' ' . strtolower($data['reg']['Apellido2']) . ' ' . strtolower($data['reg']['Nombre1']) . ' ' . strtolower($data['reg']['Nombre2'])) . '/HV';

    																	if (is_dir($cDirectorio)):
        																	$dir = opendir($cDirectorio);

																			while (($archivo = readdir($dir)) !== false):
																				if ($archivo != '.' AND $archivo != '..'):
																	?>
																	<tr>
																		<td>
																			<?= $archivo ?>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				target="_blank"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Ver documento">
																				<i class="material-icons">visibility</i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class="material-icons">cloud_download</i>
																			</a>
																		</td>
																	</tr>
																	<?php
																				endif;
																			endwhile;
																			closedir($dir);
																		endif;
																	?>
																</tbody>
															</table>
														</div>
													</li>
													<li>
														<div class="collapsible-header">
															<i class="material-icons">folder_open</i>
															SEGURIDAD SOCIAL
														</div>
														<div class="collapsible-body">
															<table>
																<tbody>
																	<?php
																		$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/SEGURIDAD_SOCIAL';

																		if (! is_dir($cDirectorio))
																			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . ucwords(strtolower($data['reg']['Apellido1']) . ' ' . strtolower($data['reg']['Apellido2']) . ' ' . strtolower($data['reg']['Nombre1']) . ' ' . strtolower($data['reg']['Nombre2'])) . '/Seguridad Social';

																		if (is_dir($cDirectorio)):
        																	$dir = opendir($cDirectorio);

																			while (($archivo = readdir($dir)) !== false):
																				if ($archivo != '.' AND $archivo != '..'):
																	?>
																	<tr>
																		<td>
																			<?= $archivo ?>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				target="_blank"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Ver documento">
																				<i class="material-icons">visibility</i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class="material-icons">cloud_download</i>
																			</a>
																		</td>
																	</tr>
																	<?php
																				endif;
																			endwhile;
																			closedir($dir);
																		endif;
																	?>
																</tbody>
															</table>
														</div>
													</li>
													<li>
														<div class="collapsible-header">
															<i class="material-icons">folder_open</i>
															SOPORTES ACADÉMICOS
														</div>
														<div class="collapsible-body">
															<table>
																<tbody>
																	<?php
																		$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/SOPORTES_ACADEMICOS';

																		if (! is_dir($cDirectorio))
																			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . ucwords(strtolower($data['reg']['Apellido1']) . ' ' . strtolower($data['reg']['Apellido2']) . ' ' . strtolower($data['reg']['Nombre1']) . ' ' . strtolower($data['reg']['Nombre2'])) . '/Soportes Academicos';

    																	if (is_dir($cDirectorio)):
        																	$dir = opendir($cDirectorio);

																			while (($archivo = readdir($dir)) !== false):
																				if ($archivo != '.' AND $archivo != '..'):
																	?>
																	<tr>
																		<td>
																			<?= $archivo ?>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				target="_blank"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Ver documento">
																				<i class="material-icons">visibility</i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class="material-icons">cloud_download</i>
																			</a>
																		</td>
																	</tr>
																	<?php
																				endif;
																			endwhile;
																			closedir($dir);
																		endif;
																	?>
																</tbody>
															</table>
														</div>
													</li>
													<li>
														<div class="collapsible-header">
															<i class="material-icons">folder_open</i>
															SOPORTES LABORALES
														</div>
														<div class="collapsible-body">
															<table>
																<tbody>
																	<?php
																		$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/SOPORTES_LABORALES';

																		if (! is_dir($cDirectorio))
																			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . ucwords(strtolower($data['reg']['Apellido1']) . ' ' . strtolower($data['reg']['Apellido2']) . ' ' . strtolower($data['reg']['Nombre1']) . ' ' . strtolower($data['reg']['Nombre2'])) . '/Certificaciones Laborales';

    																	if (is_dir($cDirectorio)):
        																	$dir = opendir($cDirectorio);

																			while (($archivo = readdir($dir)) !== false):
																				if ($archivo != '.' AND $archivo != '..'):
																	?>
																	<tr>
																		<td>
																			<?= $archivo ?>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				target="_blank"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Ver documento">
																				<i class="material-icons">visibility</i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class="material-icons">cloud_download</i>
																			</a>
																		</td>
																	</tr>
																	<?php
																				endif;
																			endwhile;
																			closedir($dir);
																		endif;
																	?>
																</tbody>
															</table>
														</div>
													</li>
													<li>
														<div class="collapsible-header">
															<i class="material-icons">folder_open</i>
															PRUEBAS PSICOTÉCNICAS
														</div>
														<div class="collapsible-body">
															<table>
																<tbody>
																	<?php
																		$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/PRUEBAS_SICOTECNICAS';

																		if (! is_dir($cDirectorio))
																			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . ucwords(strtolower($data['reg']['Apellido1']) . ' ' . strtolower($data['reg']['Apellido2']) . ' ' . strtolower($data['reg']['Nombre1']) . ' ' . strtolower($data['reg']['Nombre2'])) . '/Pruebas Psicotécnicas';

    																	if (is_dir($cDirectorio)):
        																	$dir = opendir($cDirectorio);

																			while (($archivo = readdir($dir)) !== false):
																				if ($archivo != '.' AND $archivo != '..'):
																	?>
																	<tr>
																		<td>
																			<?= $archivo ?>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				target="_blank"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Ver documento">
																				<i class="material-icons">visibility</i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class="material-icons">cloud_download</i>
																			</a>
																		</td>
																	</tr>
																	<?php
																				endif;
																			endwhile;
																			closedir($dir);
																		endif;
																	?>
																</tbody>
															</table>
														</div>
													</li>
													<li>
														<div class="collapsible-header">
															<i class="material-icons">folder_open</i>
															DOCUMENTOS LEGALES
														</div>
														<div class="collapsible-body">
															<table>
																<tbody>
																	<?php
																		$cDirectorio = 'documents/' . trim($data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2'])) . '/DOCUMENTOS_LEGALES';

																		if (! is_dir($cDirectorio))
																			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . trim(ucwords(strtolower($data['reg']['Apellido1']) . ' ' . strtolower($data['reg']['Apellido2']) . ' ' . strtolower($data['reg']['Nombre1']) . ' ' . strtolower($data['reg']['Nombre2']))) . '/Documentos Legales';

																		$cDirectorio = str_replace('  ', ' ', trim($cDirectorio));

    																	if (is_dir($cDirectorio)):
        																	$dir = opendir($cDirectorio);

																			while (($archivo = readdir($dir)) !== false):
																				if ($archivo != '.' AND $archivo != '..'):
																	?>
																	<tr>
																		<td>
																			<?= $archivo ?>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				target="_blank"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Ver documento">
																				<i class="material-icons">visibility</i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class="material-icons">cloud_download</i>
																			</a>
																		</td>
																	</tr>
																	<?php
																				endif;
																			endwhile;
																			closedir($dir);
																		endif;
																	?>
																</tbody>
															</table>
														</div>
													</li>
													<li>
														<div class="collapsible-header">
															<i class="material-icons">folder_open</i>
															GRUPOS POBLACIONALES
														</div>
														<div class="collapsible-body">
															<table>
																<tbody>
																	<?php
																		$cDirectorio = 'documents/' . trim($data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2'])) . '/GRUPOS_POBLACIONALES';

    																	if (is_dir($cDirectorio)):
        																	$dir = opendir($cDirectorio);

																			while (($archivo = readdir($dir)) !== false):
																				if ($archivo != '.' AND $archivo != '..'):
																	?>
																	<tr>
																		<td>
																			<?= $archivo ?>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				target="_blank"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Ver documento">
																				<i class="material-icons">visibility</i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class="material-icons">cloud_download</i>
																			</a>
																		</td>
																	</tr>
																	<?php
																				endif;
																			endwhile;
																			closedir($dir);
																		endif;
																	?>
																</tbody>
															</table>
														</div>
													</li>
												</ul>
											</div>
										</div>

										<div id="pagContratos" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>CONTRATOS</p>
													</div>
												</div>

												<ul class="collapsible">
													<li>
														<div class="collapsible-header">
															<i class="material-icons">folder_open</i>
															CONTRATOS
														</div>
														<div class="collapsible-body">
															<table>
																<tbody>
																	<?php
																		$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/CONTRATOS';

    																	if (is_dir($cDirectorio)):
        																	$dir = opendir($cDirectorio);

																			while (($archivo = readdir($dir)) !== false):
																				if ($archivo != '.' AND $archivo != '..'):
																	?>
																	<tr>
																		<td>
																			<?= $archivo ?>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				target="_blank"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Ver documento">
																				<i class="material-icons">visibility</i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class="material-icons">cloud_download</i>
																			</a>
																		</td>
																	</tr>
																	<?php
																				endif;
																			endwhile;
																			closedir($dir);
																		endif;
																	?>
																</tbody>
															</table>
														</div>
													</li>
												</ul>
											</div>
										</div>

										<?php if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR OR $_SESSION['Login']['Perfil'] == RRHH): ?>
										<div id="pagAuditoria" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>AUDITORÍA</p>
													</div>
												</div>

												<table>
													<thead>
														<tr>
															<th>USUARIO</th>
															<th>FECHA</th>
															<th>CAMPO</th>
															<th>VALOR ANTERIOR</th>
															<th>VALOR ACTUAL</th>
														</tr>
													</thead>
													<tbody>
														<?php if ($data['regAud']): ?>
														<?php for ($i = 0; $i < count($data['regAud']); $i++ ): ?>
														<tr>
															<td><?= $data['regAud'][$i]['NombreUsuario'] ?></td>
															<td><?= $data['regAud'][$i]['Fecha'] ?></td>
															<td><?= $data['regAud'][$i]['Campo'] ?></td>
															<td><?= $data['regAud'][$i]['ValorAnterior'] ?></td>
															<td><?= $data['regAud'][$i]['ValorActual'] ?></td>
														</tr>
														<?php endfor; ?>
														<?php endif; ?>
													</tbody>
												</table>
											</div>
										</div>
										<?php endif; ?>
									</div>
								</div>
							</section>
							<?php endif; ?>
						</div>
					</div>
					<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
						<?php if ( $data['mensajeError'] ): ?>
						<div class="row">
							<div class="col s12">
								<h6 class="orange-text">
									<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por favor
									valídelas:
								</h6>
								<br>
								<?= $data['mensajeError'] ?>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
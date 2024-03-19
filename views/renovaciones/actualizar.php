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
		
		for ($i=0; $i < count($ciudades); $i++) 
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

		// EL CARGO SELECCIONADO ES EL ASIGNADO Y NO PERMITE LECTURAS ADICIONALES
		$cargos = getTabla('CARGOS', '', 'CARGOS.Nombre');

		$SelectCargo = '';
		
		for ($i=0; $i < count($cargos); $i++) 
		{ 
			if	($cargos[$i]['id'] == $data['reg']['IdCargo'])
				$SelectCargo .= '<option selected value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';
			else
				$SelectCargo .= '<option value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';
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
		
		for ($i=0; $i < count($centros); $i++) 
		{ 
			if	($centros[$i]['id'] == $data['reg']['IdCentro'])
				$SelectCentro .= '<option selected value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
			else
				$SelectCentro .= '<option value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
		}

		$categorias = getTabla('CATEGORIAS', '', 'CATEGORIAS.Nombre');

		$SelectCategoria = '';
		
		for ($i=0; $i < count($categorias); $i++) 
		{ 
			if	($categorias[$i]['id'] == $data['reg']['IdCategoria'])
				$SelectCategoria .= '<option selected value=' . $categorias[$i]['id'] . '>' . trim($categorias[$i]['nombre']) . '</option>';
			else
				$SelectCategoria .= '<option value=' . $categorias[$i]['id'] . '>' . trim($categorias[$i]['nombre']) . '</option>';
		}

		$terceros = getTabla('TERCEROS', '', 'TERCEROS.Nombre');

		$SelectEPS = '';
		$SelectFondoCesantias = '';
		$SelectFondoPensiones = '';

		for ($i=0; $i < count($terceros); $i++) 
		{ 
			if ($terceros[$i]['eseps'])
			{
				if	($terceros[$i]['id'] == $data['reg']['IdEPS'])
					$SelectEPS .= '<option selected value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
				else
					$SelectEPS .= '<option value=' . $terceros[$i]['id'] . '>' . trim($terceros[$i]['nombre']) . '</option>';
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
		}

		$bancos = getTabla('BANCOS', '', 'BANCOS.Nombre');

		$SelectBanco = '';
		
		for ($i=0; $i < count($bancos); $i++) 
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
									<h3 class="white-text">Empleados</h3>
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
													<a href="#pagPerfil">
														<i class="material-icons">accessibility</i>
														<span>Perfil profesional</span>
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

													if (isset($archivo) AND ! file_exists($archivo)):
														$archivo = str_replace('jpeg', 'jpg', $archivo);
												?>
												<div class="row center-align">
													<img src="<?= SERVERURL . '/' . $archivo ?>" alt="Fotografia"
														class="circle responsive-img" width="150px">
												</div>
												<?php
													endif;
												?>
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
													<div class="input-field col s6 m6">
														<?php
															get(label('Acepta política de tratamiento de datos*'), 'AceptaPoliticaTD', $data['reg']['AceptaPoliticaTD'], 'checkbox', $data['reg']['AceptaPoliticaTD'], FALSE, 'required', '');
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
															get(label('Pais*'), 'IdPais', $SelectPais, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('E-Mail*'), 'Email', $data['reg']['Email'], 'email', 100, FALSE, 'required', 'fas fa-paper-plane	'); 
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
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Tipo de contrato*'), 'TipoContrato', $SelectTipoContrato, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Fecha de ingreso*'), 'FechaIngreso', $data['reg']['FechaIngreso'], 'date', 0, FALSE, '', 'fas fa-calendar'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('En perído de prueba hasta*'), 'FechaPeriodoPrueba', $data['reg']['FechaPeriodoPrueba'], 'date', 0, FALSE, '', 'fas fa-calendar'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Fecha vencimiento contrato*'), 'FechaVencimiento', $data['reg']['FechaVencimiento'], 'date', 0, FALSE, '', 'fas fa-calendar'); 
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
															get(label('Sueldo básico*'), 'SueldoBasico', $data['reg']['SueldoBasico'], 'number', 12, FALSE, '', 'fas fa-edit'); 
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
															get(label('Salud y educación'), 'SaludYEducacion', $data['reg']['SaludYEducacion'], 'number', 12, FALSE, '', 'fas fa-pen'); 
															?>
													</div>
												</div>
											</div>
										</div>

										<div id="pagPerfil" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>PERFIL PROFESIONAL</p>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s6 m6">
														<?php 
															get(label('Cargo*'), 'IdCargo', $SelectCargo, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
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
																		$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/DOCUMENTOS_LEGALES';

																		if (! is_dir($cDirectorio))
																			$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . ucwords(strtolower($data['reg']['Apellido1']) . ' ' . strtolower($data['reg']['Apellido2']) . ' ' . strtolower($data['reg']['Nombre1']) . ' ' . strtolower($data['reg']['Nombre2'])) . '/Documentos Legales';

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

										<?php if ($_SESSION['Login']['Perfil'] <> EMPLEADO): ?>
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
<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	// IDENTIFICACION
	$SelectTipoIdentificacion = getSelect('TipoIdentificacion', $data['reg']['TipoIdentificacion'], '', 'PARAMETROS.Valor');

	$IdCiudadExpedicion = $data['reg']['IdCiudadExpedicion'];
	if ($IdCiudadExpedicion > 0)
	{
		$regCiudad = getRegistro('CIUDADES', $IdCiudadExpedicion);
		if ($regCiudad)
			$CiudadExpedicion = $regCiudad['nombre'] . ' (' . $regCiudad['departamento'] . ')';
		else
			$CiudadExpedicion = '';
	}
	else
		$CiudadExpedicion = '';

	$SelectGenero = getSelect('Genero', $data['reg']['Genero'], '', 'PARAMETROS.Valor');
	$SelectEstadoCivil = getSelect('EstadoCivil', $data['reg']['EstadoCivil'], '', 'PARAMETROS.Valor');
	$SelectFactorRH = getSelect('FactorRH', $data['reg']['FactorRH'], '', 'PARAMETROS.Valor');

	// INFORMACION PERSONAL
	$IdCiudadNacimiento = $data['reg']['IdCiudadNacimiento'];
	if ($IdCiudadNacimiento > 0)
	{
		$regCiudad = getRegistro('CIUDADES', $IdCiudadNacimiento);
		if ($regCiudad)
			$CiudadNacimiento = $regCiudad['nombre'] . ' (' . $regCiudad['departamento'] . ')';
		else
			$CiudadNacimiento = '';
	}
	else
		$CiudadNacimiento = '';

	// INFORMACION DE CONTACTO
	$IdCiudad = $data['reg']['IdCiudad'];
	if ($IdCiudad)
	{
		$regCiudad = getRegistro('CIUDADES', $IdCiudad);
		if ($regCiudad)
			$Ciudad = $regCiudad['nombre'] . ' (' . $regCiudad['departamento'] . ')';
		else
			$Ciudad = '';
	}
	else
		$Ciudad = '';

	// PERFIL PROFESIONAL

	// EXPERIENCIA LABORAL
	$CiudadEmpresa = '';
	$NombreCiudadEmpresa = '';

	// EDUCACION FORMAL

	// EDUCACION NO FORMAL

	// IDIOMAS

	// OTROS CONOCIMIENTOS

	// CONTACTOS

	// DOCUMENTOS
	$IdEPS = $data['reg']['IdEPS'];
	if ($IdEPS > 0)
	{
		$regTercero = getRegistro('TERCEROS', $IdEPS);
		if ($regTercero)
			$NombreEPS = $regTercero['nombre'];
		else
			$NombreEPS = '';
	}
	else
		$NombreEPS = '';

	$IdFondoCesantias = $data['reg']['IdFondoCesantias'];
	if ($IdFondoCesantias > 0)
	{
		$regTercero = getRegistro('TERCEROS', $IdFondoCesantias);
		if ($regTercero)
			$NombreFC = $regTercero['nombre'];
		else
			$NombreFC = '';
	}
	else
		$NombreFC = '';

	$IdFondoPensiones = $data['reg']['IdFondoPensiones'];
	if ($IdFondoPensiones > 0)
	{
		$regTercero = getRegistro('TERCEROS', $IdFondoPensiones);
		if ($regTercero)
			$NombreFP = $regTercero['nombre'];
		else
			$NombreFP = '';
	}
	else
		$NombreFP = '';

	$IdCajaCompensacion = $data['reg']['IdCajaCompensacion'];
	if ($IdCajaCompensacion > 0)
	{
		$regTercero = getRegistro('TERCEROS', $IdCajaCompensacion);
		if ($regTercero)
			$NombreCCF = $regTercero['nombre'];
		else
			$NombreCCF = '';
	}
	else
		$NombreCCF = '';

	$IdARL = $data['reg']['IdARL'];
	if ($IdARL > 0)
	{
		$regTercero = getRegistro('TERCEROS', $IdARL);
		if ($regTercero)
			$NombreARL = $regTercero['nombre'];
		else
			$NombreARL = '';
	}
	else
		$NombreARL = '';

	$SelectNivelRiesgo = getSelect('NivelRiesgo', $data['reg']['NivelRiesgo'], '', 'PARAMETROS.Valor');
	$SelectRegimenCesantias = getSelect('RegimenCesantias', $data['reg']['RegimenCesantias'], '', 'PARAMETROS.Valor');
	$SelectFormaDePago = getSelect('FormaDePago', $data['reg']['FormaDePago'], '', 'PARAMETROS.Valor');

	$IdBanco = $data['reg']['IdBanco'];
	if ($IdBanco > 0)
	{
		$regBanco = getRegistro('BANCOS', $IdBanco);
		if ($regBanco)
			$NombreBanco = $regBanco['nombre'];
		else
			$NombreBanco = '';
	}
	else
		$NombreBanco = '';

	$SelectTipoCuentaBancaria = getSelect('TipoCuentaBancaria', $data['reg']['TipoCuentaBancaria'], '', 'PARAMETROS.Valor');

	$IdCiudadTrabajo = $data['reg']['IdCiudadTrabajo'];
	if ($IdCiudadTrabajo > 0)
	{
		$regCiudad = getRegistro('CIUDADES', $IdCiudadTrabajo);
		if ($regCiudad)
			$CiudadTrabajo = $regCiudad['nombre'] . ' (' . $regCiudad['departamento'] . ')';
		else
			$CiudadTrabajo = '';
	}
	else
		$CiudadTrabajo = '';

	// GRUPOS POBLACIONALES

	// DOCUMENTOS SELECCION

	// CONDICIONES LABORALES
	$IdCargo = $data['reg']['IdCargo'];
	if ($IdCargo > 0)
	{
		$regCargo = getRegistro('CARGOS', $IdCargo);
		if ($regCargo)
			$NombreCargo = $regCargo['nombre'];
		else
			$NombreCargo = '';
	}
	else
		$NombreCargo = '';
	
	// CONDICIONES JEFE
	$IdJefe = $data['reg']['IdJefe'];
	if ($IdJefe > 0)
	{
		$regJefe = getRegistro('EMPLEADOS', $IdJefe);
		if ($regJefe)
			$NombreJefe = $regJefe['nombre1']." ".$regJefe['nombre2']." ".$regJefe['apellido1']." ".$regJefe['apellido2'];
		else
			$NombreJefe = '';
	}
	else{
		$NombreJefe = '';
	}


	$IdCentro = $data['reg']['IdCentro'];
	if ($IdCentro > 0)
	{
		$regCentro = getRegistro('CENTROS', $IdCentro);
		if ($regCentro)
			$NombreCentro = $regCentro['nombre'];
		else
			$NombreCentro = '';
	}
	else
		$NombreCentro = '';

	$IdProyecto = $data['reg']['IdProyecto'];
	if ($IdProyecto > 0)
	{
		$regProyecto = getRegistro('CENTROS', $IdProyecto);
		if ($regProyecto)
			$NombreProyecto = $regProyecto['nombre'];
		else
			$NombreProyecto = '';
	}
	else
		$NombreProyecto = '';

	$IdSede = $data['reg']['IdSede'];
	if ($IdSede > 0)
	{
		$regSede = getRegistro('SEDES', $IdSede);
		if ($regSede)
			$NombreSede = $regSede['nombre'];
		else
			$NombreSede = '';
	}
	else
		$NombreSede = '';

	$SelectVicepresidencia = getSelect('Vicepresidencia', $data['reg']['Vicepresidencia'], '', 'PARAMETROS.Valor');
	$SelectTipoContrato = getSelect('TipoContrato', $data['reg']['TipoContrato'], '', 'PARAMETROS.Valor');
	$SelectModalidadTrabajo = getSelect('ModalidadTrabajo', $data['reg']['ModalidadTrabajo'], '', 'PARAMETROS.Valor');

	// CONTRATOS
	$EstadoEmpleado = $data['reg']['Estado'];
	$TipoPlantilla = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoPlantilla' AND PARAMETROS.Detalle = 'DOCUMENTO'");
	$TipoContrato = $data['reg']['TipoContrato'];

	$query = <<<EOD
		PLANTILLAS.EstadoEmpleado = $EstadoEmpleado AND 
		PLANTILLAS.TipoPlantilla = $TipoPlantilla AND 
		PLANTILLAS.TipoContrato = $TipoContrato 
	EOD;

	$contratos = getTabla('PLANTILLAS', $query, 'PLANTILLAS.Asunto');

	$Documento = $data['reg']['Documento'];
	$regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento'");
	$EstadoEmpleado = getRegistro('PARAMETROS', $regEmpleado['estado'])['detalle'];

	$SelectSubsidioTransporte = getSelect('SubsidioTransporte', $data['reg']['SubsidioTransporte'], '', 'PARAMETROS.Valor');
	$SelectPeriodicidadPago = getSelect('Periodicidad', $data['reg']['PeriodicidadPago'], '', 'PARAMETROS.Valor');
	$SelectMetodoRetencion = getSelect('MetodoRetencion', $data['reg']['MetodoRetencion'], '', 'PARAMETROS.Valor');
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
									<h3 class="white-text">Contratos</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<?php if ( $data['mensajeError'] ): ?>
						<div class="card-content red white-text z-depth-2">
							<div class="row" id="mensajeError">
								<div class="col s12">
									<h5 class="white-text">
										<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por favor valídelas:
									</h5>
									<br>
									<h5 class="white-text">
									<?= $data['mensajeError'] ?>
									</h5>
								</div>
							</div>
						</div>
						<?php endif; ?>
						<div class="card-content">
							<?php if ($data['reg']): ?>
							<section class="tabs-vertical mt-1 section">
								<?php 
									$regEmpleado = getRegistro('EMPLEADOS', $data['reg']['Id']);
									$dir = '/documents/' . $regEmpleado['documento'] . '_' . strtoupper($regEmpleado['apellido1']) . '_' . strtoupper($regEmpleado['apellido2']) . '_' . strtoupper($regEmpleado['nombre1']) . '_' . strtoupper($regEmpleado['nombre2']);
									$cDirectorio = SERVERURL . $dir;
									$archivo = getImage($dir,$cDirectorio);
								?>
								<div class="row">
									<div class="col s12 m3">
										<div class="card-panel">
											<div class="media center">
												<img src="<?= $archivo ?>" alt="Fotografia" class="border-radius-4" height="250px" width="200px">
												<h4 class="text center">
													<?= $regEmpleado['nombre1'] . ' ' . $regEmpleado['nombre2'] . ' ' .$regEmpleado['apellido1'] . ' ' .$regEmpleado['apellido2'] ?>
												</h4>
											</div>
            							</div>	
										<div class="row">
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
														<a href="#pagPerfilProfesional">
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
														<a href="#pagDocumentos" <?= $data['reg']['CNT_DocumentosActualizados'] == 0 ? 'class="active"' : '' ?>>
															<i class="material-icons">folder_open</i>
															<span>Documentos</span>
														</a>
													</li>
													<li class="tab">
														<a href="#pagCondicionesLaborales" <?= $data['reg']['CNT_CondicionesLaborales'] == 0 ? 'class="active"' : '' ?>>
															<i class="material-icons">payment</i>
															<span>Condiciones laborales</span>
														</a>
													</li>
													<li class="tab">
														<a href="#pagContratos" <?= $data['reg']['CNT_ContratosEnviados'] == 0 ? 'class="active"' : '' ?>>
															<i class="material-icons">folder_open</i>
															<span>Doc. Legales</span>
														</a>
													</li>
													<?php if ($_SESSION['Login']['Perfil'] == CONTRATACION OR $_SESSION['Login']['Perfil'] == ADMINISTRADOR): ?>
													<li class="tab">
														<a href="#pagFinalizar">
															<i class="material-icons">send</i>
															<span>Finalizar</span>
														</a>
													</li>
													<?php endif; ?>
												</ul>
											</div>
										</div>
									</div>

									<div class="col s12 m9">
										<div id="pagIdentificacion" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>IDENTIFICACIÓN (PASO 1)</p>
													</div>
												</div>

												<div class="row">
													<div class="input-field col s12 m6">
														<?php
															get(label('Tipo de identificación*'), 'TipoIdentificacion', $SelectTipoIdentificacion, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Documento*'), 'Documento', $data['reg']['Documento'], 'text', 15, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Fecha de expedición*'), 'FechaExpedicion', $data['reg']['FechaExpedicion'], 'date', 10, FALSE, '', 'far fa-calendar'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdCiudadExpedicion" id="IdCiudadExpedicion" value="<?= $IdCiudadExpedicion ?>">
														<?php 
															get(label('Ciudad de expedición*'), 'CiudadExpedicion', $CiudadExpedicion, 'text', 25, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsCiudadExpedicion"></div>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Primer apellido*'), 'Apellido1', $data['reg']['Apellido1'], 'text', 25, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Segundo apellido'), 'Apellido2', $data['reg']['Apellido2'], 'text', 25, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Primer nombre*'), 'Nombre1', $data['reg']['Nombre1'], 'text', 25, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Segundo nombre'), 'Nombre2', $data['reg']['Nombre2'], 'text', 25, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12">
														Persona políticamente expuesta*
														<div class="col s12">
															<div class="col s6">
																<?php
																	$CheckedRadio = '';
																	if (isset($data['reg'])) $CheckedRadio = $data['reg']['PoliticamenteExpuesta'] ? 'checked' : '';
																	echo <<<EOD
																		<p>
																			<label>
																			<input disabled name="PoliticamenteExpuesta" type="radio" value="si" $CheckedRadio />
																			<span>Si</span>
																			</label>
																		</p>
																	EOD;
																?>
															</div>
															<div class="col s6">
																<?php
																	$CheckedRadio = 'checked';
																	if (isset($data['reg'])) $CheckedRadio = $data['reg']['PoliticamenteExpuesta'] ? '' : 'checked';
																	echo <<<EOD
																		<p>
																			<label>
																			<input disabled name="PoliticamenteExpuesta" type="radio" value="no" $CheckedRadio />
																			<span>No</span>
																			</label>
																		</p>
																	EOD;
																?>
															</div>
														</div>
													</div>
													<div class="input-field col s12">
														<ul class="collapsible">
															<li>
																<div class="collapsible-header">Ver política</div>
																<div class="collapsible-body">
																	<h4>PERSONA POLÍTICAMENTE EXPUESTA</h4>
																	<p>Decreto 830 de 2021:</p>
																	<p><strong>"ARTÍCULO 2.1.4.2.3 Personas expuestas políticamente</strong>.
																		Se cosideran como Personas Expuestas 	Políticamente (PEP) los servidores públicos de cualquier sistema de nomenclatura y clasificación de empleos de la administración pública nacional y territorial cuando tengan asignadas o delegadas funciones de expedición de normas o regulaciones, dirección general, formulación de políticas institucionales y adopción de planes, programas y proyectos, manejo directo de bienes, dineros o valores del Estado, administración de justicia o facultades administrativo sancionatorias y los particulares que tengan a su cargo la dirección o manejo de recursos en los movimientos o partidos políticos."</p>
																</div>
															</li>
														</ul>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<p>Declaro de manera expresa que los recursos que movilizo
															tienen origen lícito</p>
														<?php
															get(label('Declaración de origen y destino de recursos*'), 'DeclaracionOrigenRecursos', $data['reg']['DeclaracionOrigenRecursos'], 'checkbox', $data['reg']['DeclaracionOrigenRecursos'], FALSE, '', '');
														?>
													</div>
													<div class="input-field col s12 m6">
														<p>Declaro de manera expresa que los recursos que movilizo
															serán usados de forma lícita</p>
														<?php
															get(label('Uso lícito de recursos*'), 'UsoLicitoRecursos', $data['reg']['UsoLicitoRecursos'], 'checkbox', $data['reg']['UsoLicitoRecursos'], FALSE, '', '');
														?>
													</div>
												</div>
												<?php if ($regEmpleado['sel_datosactualizados'] == 0 AND $_SESSION['Login']['Perfil'] == EMPLEADO): ?>
												<div class="row">
													<div class="input-field col s12 m6">
														<button class="btn btn-sm cyan darken-4" type="submit"
															name="Action" value="GUARDAR_1">
															GUARDAR DATOS Y AVANZAR
															<i class="medium material-icons left">save</i>
														</button>
													</div>
													<div class="input-field col s12 m6">
														<button class="btn btn-sm red darken-4" type="submit"
															name="Action" value="DESISTIR">
															DESISTIR AL CARGO
														</button>
													</div>
												</div>
												<?php endif; ?>
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
													<div class="input-field col s12 m6">
														<?php 
															get(label('Fecha nacimiento*'), 'FechaNacimiento', $data['reg']['FechaNacimiento'], 'date', 10, FALSE, '', 'far fa-calendar'); 
														?>
													</div>
													<div class="input-field col s12 m6">
													<input type="hidden" name="IdCiudadNacimiento" id="IdCiudadNacimiento" value="<?= $IdCiudadNacimiento ?>">
														<?php 
															get(label('Ciudad de nacimiento*'), 'CiudadNacimiento', $CiudadNacimiento, 'text', 25, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsCiudadNacimiento"></div>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php
															get(label('Genero*'), 'Genero', $SelectGenero, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php
															get(label('Estado civil*'), 'EstadoCivil', $SelectEstadoCivil, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php
															get(label('Factor RH*'), 'FactorRH', $SelectFactorRH, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
														?>
													</div>
													<div class="input-field col s12 m6">
															<?php
																get(label('Talla'), 'talla', $data['reg']['talla'], 'text', 10, FALSE, '', 'fas fa-pen');
															?>
														</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php
															get(label('Libreta militar'), 'LibretaMilitar', $data['reg']['LibretaMilitar'], 'text', 20, FALSE, '', 'fas fa-pen');
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php
															get(label('Distrito militar'), 'DistritoMilitar', $data['reg']['DistritoMilitar'], 'text', 3, FALSE, '', 'fas fa-pen');
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php
															get(label('Licencia conducción'), 'LicenciaConduccion', $data['reg']['LicenciaConduccion'], 'text', 20, FALSE, '', 'fas fa-pen');
														?>
													</div>
													<div class="input-field col s12 m6">
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
													<div class="input-field col s12 m6">
														<?php 
															get(label('Dirección*'), 'Direccion', $data['reg']['Direccion'], 'text', 60, FALSE, '', 'fas fa-map-marker-alt'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Barrio*'), 'Barrio', $data['reg']['Barrio'], 'text', 25, FALSE, '', 'fas fa-map-marker-alt'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Localidad'), 'Localidad', $data['reg']['Localidad'], 'text', 25, FALSE, '', 'fas fa-map-marker-alt'); 
														?>
													</div>
													<div class="input-field col s12 m6">
													<input type="hidden" name="IdCiudad" id="IdCiudad" value="<?= $IdCiudad ?>">
														<?php 
															get(label('Ciudad*'), 'Ciudad', $Ciudad, 'text', 25, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsCiudad"></div>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('E-Mail*'), 'Email', $data['reg']['Email'], 'email', 100, FALSE, '', 'fas fa-paper-plane	'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Teléfono'), 'Telefono', $data['reg']['Telefono'], 'tel', 15, FALSE, '', 'fas fa-phone'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Celular*'), 'Celular', $data['reg']['Celular'], 'tel', 15, FALSE, '', 'fas fa-phone'); 
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
															get(label('Perfil profesional*'), 'PerfilProfesional', $data['reg']['PerfilProfesional'], 'textarea', 5, FALSE, '', 'far fa-edit'); 
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
															<th style="text-align:center;"></th>
															<th>EMPRESA</th>
															<th>FECHA INGRESO</th>
															<th>FECHA RETIRO</th>
															<th>TELÉFONO</th>
															<th>CARGO</th>
															<th>JEFE INMEDIATO</th>
														</tr>
													</thead>
													<tbody>
														<?php if ($data['regEmp']): ?>
														<?php for ($i = 0; $i < count($data['regEmp']); $i++ ): ?>
														<tr>
															<td class="center-align">
																<button class="btn btn-sm red darken-4" type="submit" name="Action" value="BORRAR_<?= $i ?>_6">
																	<i class="small material-icons">delete</i>
																</button>									
															</td>
															<td><?= $data['regEmp'][$i]['Empresa'] ?></td>
															<td><?= $data['regEmp'][$i]['FechaIngreso'] ?></td>
															<td><?= $data['regEmp'][$i]['FechaRetiro'] ?></td>
															<td><?= $data['regEmp'][$i]['Telefono'] ?></td>
															<td><?= $data['regEmp'][$i]['Cargo'] ?></td>
															<td><?= $data['regEmp'][$i]['JefeInmediato'] ?></td>
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
															<th>TÍTULO / ESTUDIOS REALIZADOS</th>
															<th>NIVEL ACADÉMICO</th>
															<th>FECHA INICIO</th>
															<th>FECHA FINALIZACIÓN</th>
															<th>ESTADO</th>
														</tr>
													</thead>
													<tbody>
														<?php if ($data['regEduF']): ?>
														<?php for ($i = 0; $i < count($data['regEduF']); $i++ ): ?>
														<tr>
															<td><?= $data['regEduF'][$i]['CentroEducativo'] ?></td>
															<td><?= $data['regEduF'][$i]['Estudio'] ?></td>
															<td><?= $data['regEduF'][$i]['NivelAcademico'] ?></td>
															<td><?= $data['regEduF'][$i]['AnoInicio'] . '-' . $data['regEduF'][$i]['MesInicio'] ?>
															</td>
															<td><?= $data['regEduF'][$i]['AnoFinalizacion'] . '-' . $data['regEduF'][$i]['MesFinalizacion'] ?>
															</td>
															<td><?= $data['regEduF'][$i]['Estado'] ?></td>
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
															<th>TÍTULO / ESTUDIOS REALIZADOS</th>
															<th>NIVEL ACADÉMICO</th>
															<th>FECHA INICIO</th>
															<th>FECHA FINALIZACIÓN</th>
															<th>ESTADO</th>
														</tr>
													</thead>
													<tbody>
														<?php if ($data['regEduNF']): ?>
														<?php for ($i = 0; $i < count($data['regEduNF']); $i++ ): ?>
														<tr>
															<td><?= $data['regEduNF'][$i]['CentroEducativo'] ?></td>
															<td><?= $data['regEduNF'][$i]['Estudio'] ?></td>
															<td><?= $data['regEduNF'][$i]['NivelAcademico'] ?></td>
															<td><?= $data['regEduNF'][$i]['AnoInicio'] . '-' . $data['regEduNF'][$i]['MesInicio'] ?>
															</td>
															<td><?= $data['regEduNF'][$i]['AnoFinalizacion'] . '-' . $data['regEduNF'][$i]['MesFinalizacion'] ?>
															</td>
															<td><?= $data['regEduNF'][$i]['Estado'] ?></td>
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
															<td><?= $data['regIdiomas'][$i]['Idioma'] ?></td>
															<td><?= $data['regIdiomas'][$i]['NivelIdioma'] ?></td>
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
														<?php if ($data['regContacto']): ?>
														<?php for ($i = 0; $i < count($data['regContacto']); $i++ ): ?>
														<tr>
															<td><?= $data['regContacto'][$i]['NombreContacto'] ?></td>
															<td><?= $data['regContacto'][$i]['Telefono'] ?></td>
															<td><?= $data['regContacto'][$i]['NombreParentesco'] ?>
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

												<div class="row">
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdEPS" id="IdEPS" value="<?= $IdEPS ?>">
														<?php 
															get(label('E.P.S.*'), 'NombreEPS', $NombreEPS, 'text', 60, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsEPS"></div>
													</div>
													<div class="col s12 m4">
														<?php 
															get(label('Afiliación EPS'), 'AfiliacionEPS', '.pdf', 'file', 0, FALSE, '', '');
														?>
													</div>
													<div class="col s12 m2">
														<?php if ($data['reg']['CertificadoEPS']): ?>
															<strong class="teal darken-3">
																<i class="medium material-icons left">check</i>
															</strong>
														<?php endif; ?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdFondoCesantias" id="IdFondoCesantias" value="<?= $IdFondoCesantias ?>">
														<?php 
															get(label('Fondo de cesantías'), 'NombreFC', $NombreFC, 'text', 60, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsFC"></div>
													</div>
													<div class="col s12 m4">
														<?php 
															get(label('Afiliación FC'), 'AfiliacionFC', '.pdf', 'file', 0, FALSE, '', '');
														?>
													</div>
													<div class="col s12 m2">
														<?php if ($data['reg']['CertificadoFC']): ?>
															<strong class="teal darken-3">
																<i class="medium material-icons left">check</i>
															</strong>
														<?php endif; ?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdFondoPensiones" id="IdFondoPensiones" value="<?= $IdFondoPensiones ?>">
														<?php 
															get(label('Fondo de pensiones'), 'NombreFP', $NombreFP, 'text', 60, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsFP"></div>
													</div>
													<div class="col s12 m4">
														<?php 
															get(label('Afiliación FP'), 'AfiliacionFP', '.pdf', 'file', 0, FALSE, '', '');
														?>
													</div>
													<div class="col s12 m2">
														<?php if ($data['reg']['CertificadoFP']): ?>
															<strong class="teal darken-3">
																<i class="medium material-icons left">check</i>
															</strong>
														<?php endif; ?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdCajaCompensacion" id="IdCCF" value="<?= $IdCajaCompensacion ?>">
														<?php 
															get(label('Caja de compensación familiar*'), 'NombreCCF', $NombreCCF, 'text', 60, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsCCF"></div>
													</div>
													<div class="col s12 m4">
														<?php 
															get(label('Afiliacion CCF'), 'AfiliacionCCF', '.pdf', 'file', 0, FALSE, '', '');
														?>
													</div>
													<div class="col s12 m2">
														<?php if ($data['reg']['CertificadoCCF']): ?>
															<strong class="teal darken-3">
																<i class="medium material-icons left">check</i>
															</strong>
														<?php endif; ?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdARL" id="IdARL" value="<?= $IdARL ?>">
														<?php 
															get(label('A.R.L.*'), 'NombreARL', $NombreARL, 'text', 60, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsARL"></div>
													</div>
													<div class="col s12 m4">
														<?php 
															get(label('Afiliacion ARL'), 'AfiliacionARL', '.pdf', 'file', 0, FALSE, '', '');
														?>
													</div>
													<div class="col s12 m2">
														<?php if ($data['reg']['CertificadoARL']): ?>
															<strong class="teal darken-3">
																<i class="medium material-icons left">check</i>
															</strong>
														<?php endif; ?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Nivel de riesgo*'), 'NivelRiesgo', $SelectNivelRiesgo, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
															?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Régimen cesantías*'), 'RegimenCesantias', $SelectRegimenCesantias, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Forma de pago*'), 'FormaDePago', $SelectFormaDePago, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdBanco" id="IdBanco" value="<?= $IdBanco ?>">
														<?php 
															get(label('Entidad bancaria'), 'NombreBanco', $NombreBanco, 'text', 60, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsBanco"></div>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Tipo cuenta bancaria*'), 'TipoCuentaBancaria', $SelectTipoCuentaBancaria, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s12 m6">
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
													<div class="input-field col s12 m6">
													</div>
													<div class="col s12 m4">
														<?php 
															get(label('Certificación cuenta bancaria'), 'CertificacionBancaria', '.pdf', 'file', 0, FALSE, '', '');
														?>
													</div>
													<div class="col s12 m2">
														<?php if ($data['reg']['CertificadoCuenta']): ?>
															<strong class="teal darken-3">
																<i class="medium material-icons left">check</i>
															</strong>
														<?php endif; ?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12">
														<button class="btn btn-sm cyan darken-4 col s12" type="submit" name="Action" value="DOCUMENTOS">
															GUARDAR AFILIACIONES Y DOCUMENTOS
															<i class="material-icons left">check</i>
														</button>									
													</div>
												</div>
											</div>
											<div class="card-panel">
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
																				<i class='fas fa-eye'></i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class='fas fa-download'></i>
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
																				<i class='fas fa-eye'></i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class='fas fa-download'></i>
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
																				<i class='fas fa-eye'></i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class='fas fa-download'></i>
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
																				<i class='fas fa-eye'></i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class='fas fa-download'></i>
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
																				<i class='fas fa-eye'></i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class='fas fa-download'></i>
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

										<div id="pagCondicionesLaborales" class="col s12">
											<div class="card-panel" >
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>CONDICIONES LABORALES (PASO 15)</p>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m12">
														<input type="hidden" name="IdJefe" id="IdJefe" value="<?= $IdJefe ?>">
														<?php 
															get(label('JEFE INMEDIATO *'), 'NombreJefe', $NombreJefe, 'text', 60, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsJefe"></div>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdCargo" id="IdCargo" value="<?= $IdCargo ?>">
														<?php 
															get(label('Cargo*'), 'NombreCargo', $NombreCargo, 'text', 60, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsCargo"></div>
													</div>
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdCiudadTrabajo" id="IdCiudadTrabajo" value="<?= $IdCiudadTrabajo ?>">
														<?php 
															get(label('Ciudad de trabajo*'), 'CiudadTrabajo', $CiudadTrabajo, 'text', 25, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsCiudadTrabajo"></div>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdCentro" id="IdCentro" value="<?= $IdCentro ?>">
														<?php 
															get(label('Centro de costos*'), 'NombreCentro', $NombreCentro, 'text', 60, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsCentro"></div>
													</div>
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdProyecto" id="IdProyecto" value="<?= $IdProyecto ?>">
														<?php 
															get(label('Proyecto*'), 'NombreProyecto', $NombreProyecto, 'text', 60, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsProyecto"></div>
													</div>
												</div>

												<div class="containerAprendiz"  style="display:none">

														<div class="row">
															<div class="input-field col s12 m6">
																<input type="hidden" name="InstitucionDeFormacion" id="IdInstitucionDeFormacion" value="<?= $data['reg']['InstitucionDeFormacion'] ?>">
																<?php 
																	get(label('Institucion de Formacion'), 'InstitucionDeFormacion', $data['reg']['InstitucionDeFormacion'], 'text', 60, FALSE, '', 'textsms'); 
																?>
															</div>
															<div class="input-field col s12 m6">
																<input type="hidden" name="IdEspecialidadAprendiz" id="IdEspecialidadAprendiz" value="<?= $data['reg']['EspecialidadAprendiz'] ?>">
																<?php 
																	get(label('Especialidad Aprendiz'), 'EspecialidadAprendiz', $data['reg']['EspecialidadAprendiz'], 'text', 60, FALSE, '', 'textsms'); 
																?>
															</div>
														</div>

												</div>

												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Vicepresidencia*'), 'Vicepresidencia', $SelectVicepresidencia, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<input type="hidden" name="IdSede" id="IdSede" value="<?= $IdSede ?>">
														<?php 
															get(label('Sede*'), 'NombreSede', $NombreSede, 'text', 60, FALSE, '', 'textsms'); 
														?>
														<div id="suggestionsSede"></div>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Tipo de contrato*'), 'TipoContrato', $SelectTipoContrato, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
														<script>
															const select = document.getElementById("TipoContrato");
															select.addEventListener("change", function() {
																const valor = select.value;
																const div = document.getElementById("fechaVencimientoContrato");
																div.style.display = (valor == 142) ? "none" : "block";
															});
														</script>
													</div>
													
													<div class="input-field col s12 m6">
														<?php 
															get(label('Duracion del contrato'), 'duracionContrato', $data['reg']['duracionContrato'], 'number', 12, FALSE, '', 'fas fa-edit'); 
														?>
													</div>


												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Sueldo básico*'), 'SueldoBasico', $data['reg']['SueldoBasico'], 'number', 12, FALSE, '', 'fas fa-edit'); 
														?>
													</div>
													
													<div class="input-field col s12 m6 containerAprendiz"  style="display:none">
														<input type="hidden" name="salarioPractica" id="IdsalarioPractica" value="<?= $data['reg']['salarioPractica'] ?>">
														<?php 
															get(label('Salario etapa practica'), 'salarioPractica', $data['reg']['salarioPractica'], 'number', 12, FALSE, '', 'fas fa-edit'); 
														?>
													</div>

												</div>
												<div class="row">
													<div class="input-field col s12 m12">
														<?php 
															get(label('Observaciones'), 'Observaciones', $data['reg']['Observaciones'], 'textarea', 2, FALSE, '', 'fas fa-edit'); 
														?>
													</div>
												</div>
												
											</div>
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>CONDICIONES DE INGRESO</p>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Fecha de ingreso*'), 'FechaIngreso', $data['reg']['FechaIngreso'], 'date', 0, FALSE, '', 'fas fa-calendar'); 
														?>
													</div>
													<div class="input-field col s12 m6 containerAprendiz" style="display:none">
														<?php 
															get(label('Fecha fin Etapa Electiva'), 'FechaFinEtapaLectiva', $data['reg']['FechaFinEtapaLectiva'] , 'date', 0, FALSE, '', 'fas fa-calendar'); 
														?>
													</div>
												</div>
												<div>

													<div class="row">
														<div class="input-field col s12 m6 containerAprendiz" style="display:none">
															<?php 
																get(label('Fecha Inicio Etapa Practica'), 'FechaInicioEtapaProductiva', $data['reg']['FechaInicioEtapaProductiva'] , 'date', 0, FALSE, '', 'fas fa-calendar'); 
															?>
														</div>

														<div class="input-field col s12 m6" id="fechaVencimientoContrato">
															<?php 
																get(label('Fecha vencimiento contrato*'), 'FechaVencimiento', $data['reg']['FechaVencimiento'], 'date', 0, FALSE, '', 'fas fa-calendar'); 
															?>
														</div>														
													</div>

													

													</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('En perído de prueba hasta*'), 'FechaPeriodoPrueba', $data['reg']['FechaPeriodoPrueba'], 'date', 0, FALSE, '', 'fas fa-calendar'); 
														?>
													</div>
														<?php if ($data['reg']['TipoContrato']==142): ?>
														<script>
															const div = document.getElementById("fechaVencimientoContrato");
															div.style.display = "none";
														</script>
														<?php endif; ?>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Modalidad Trabajo*'), 'ModalidadTrabajo', $SelectModalidadTrabajo, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="CONDICIONES">
															GUARDAR DATOS
														</button>									
													</div>
												</div>
											</div>
										</div>

										<div id="pagContratos" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>DOC. LEGALES</p>
													</div>
												</div>
												<?php for ($i = 0; $i < count($contratos); $i++): ?> 
													
													<div class="row">
														<div class="col s8">
														<?php
															get($contratos[$i]['asunto'], 'Ok[]', $contratos[$i]['id'], 'checkbox', FALSE, FALSE, '', '');
														?>
														</div>
														<div class="col s4">
															<button class="btn btn-sm cyan darken-4 col s12" type="submit" name="Action" value="PREVIEW">
																<i class='fas fa-eye'></i>
															</button>
														</div>
													</div>
												<?php endfor; ?>

												<?php
												
												$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/PRUEBAS_SICOTECNICAS';
												if (file_exists($cDirectorio)):
													$files = scandir($cDirectorio);
													for($i = 0; $i < count($files); $i ++):
														if(strrpos($files[$i], "RECOMENDACIONES_MEDICAS.") !== FALSE):
															$arrname = explode("_",$files[$i]);
															$name = $arrname[1]." ".$arrname[2];
												?>
												
												<div class="row">
														<div class="col s8">
														<?php
															get($name, 'Ok[]', $cDirectorio."/".$files[$i], 'checkbox', FALSE, FALSE, '', '');
														?>
														</div>
														<div class="col s4">
															<button class="btn btn-sm cyan darken-4 col s12" type="submit" name="Action" value="PREVIEW">
																<i class='fas fa-eye'></i>
															</button>
														</div>
													</div>
												<?php
														endif;
													endfor;
												endif;
												?>
											</div>
											<div class="card-panel">
												<div class="row">
													<div class="input-field col s12 m6">
														<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="CONTRATOS">
															ENVIAR DOCUMENTOS A FIRMA ELECTRÓNICA
														</button>									
													</div>
													<div class="input-field col s12 m6">
														<button class="btn btn-sm red darken-4" type="submit" name="Action" value="CANCELAR">
															CANCELAR FIRMAS EXISTENTES
														</button>									
													</div>
												</div>
											</div>
											<div class="card-panel">
												<ul class="collapsible">
													<li>
														<div class="collapsible-header">
															<i class="material-icons">folder_open</i>
															DOC. LEGALES
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
																				<i class='fas fa-eye'></i>
																			</a>
																		</td>
																		<td>
																			<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																				download="<?php echo $archivo; ?>"
																				class="btn btn-sm teal lighten-3 tooltipped"
																				data-position="bottom"
																				data-tooltip="Descargar documento">
																				<i class='fas fa-download'></i>
																			</a>
																		</td>
																		<td>
																					
																		
																			<div class="col s12">
																				<button class="btn btn-sm cyan darken-4 col s12" type="submit" name="Action" value="FIRMAR;<?= $cDirectorio . '/' . $archivo; ?>">
																					FIRMAR REPRESENTANTE
																				</button>
																			</div>
																	
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

										<div id="pagFinalizar" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>FINALIZAR</p>
													</div>
												</div>

												<div class="row">
													<div class="col s12">
														<h5>Por favor valide toda la información registrada antes de
															proceder con la actualización final.</h5>
														<br>
														<h6>Si desea incluir a otras personas para reportar el ingreso del Candidato, digite las cuentas de correos separadas por un ; en la casilla E-mails adicionales</h6>
														<br>
														<h6>Si los datos están correctos haga clic en el botón
															"Finalizar contratación".</h6>
														<br>
														<br>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12">
														<?php 
															$EmailAdicional = '';
															
															get(label('E-Mails adicionales'), 'EmailAdicional', $EmailAdicional, 'text', 200, FALSE, '', 'fas fa-paper-plane	'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12">
														<button class="btn btn-sm cyan darken-4 col s12" type="submit" name="Action" value="FINALIZAR">
															FINALIZAR CONTRATACIÓN
															<i class="material-icons left">check</i>
														</button>									
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12">
														<?php 
															$Justificacion = '';
															
															get(label('Justificación*'), 'Justificacion', $Justificacion, 'textarea', 3, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12">
														<button class="btn btn-sm red darken-4 col s12" type="submit" name="Action" value="SELECCION">
															DEVOLVER A SELECCIÓN DE PERSONAL
															<i class="material-icons left">replay</i>
														</button>									
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</section>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if ( isset($data['preview_document']) ): ?>
	<script>
		window.open("<?php echo $data['preview_document']?>","_blank")
	</script>
<?php endif; ?>

</div>

<?php require_once('views/templates/footer.php'); ?>

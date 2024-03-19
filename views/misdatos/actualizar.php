<?php 
	require_once('views/templates/header.php');

	if	($_SESSION['Login']['Perfil'] <> EMPLEADO)
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
	if ($IdCiudad > 0)
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
	$IdCiudadEmpresa = $data['reg']['IdCiudadEmpresa'];
	if ($IdCiudadEmpresa > 0)
	{
		$regCiudad = getRegistro('CIUDADES', $IdCiudadEmpresa);
		if ($regCiudad)
			$CiudadEmpresa = $regCiudad['nombre'] . ' (' . $regCiudad['departamento'] . ')';
		else
			$CiudadEmpresa = '';
	}
	else
		$CiudadEmpresa = '';

	// EDUCACION FORMAL
	$SelectNivelAcademicoF = getSelect('NivelAcademico', $data['reg']['NivelAcademicoF'], '', 'PARAMETROS.Valor');
	$SelectEstadoNivelAcademicoF = getSelect('EstadoNivelAcademico', $data['reg']['EstadoF'], '', 'PARAMETROS.Valor');
	$SelectMesInicioF = getSelectValor('Mes', $data['reg']['MesInicioF'], '', 'PARAMETROS.Valor');
	$SelectMesFinalizacionF = getSelectValor('Mes', $data['reg']['MesFinalizacionF'], '', 'PARAMETROS.Valor');

	// EDUCACION NO FORMAL
	$SelectNivelAcademicoNF = getSelect('NivelAcademico', $data['reg']['NivelAcademicoNF'], '', 'PARAMETROS.Valor');
	$SelectEstadoNivelAcademicoNF = getSelect('EstadoNivelAcademico', $data['reg']['EstadoNF'], '', 'PARAMETROS.Valor');
	$SelectMesInicioNF = getSelectValor('Mes', $data['reg']['MesInicioNF'], '', 'PARAMETROS.Valor');
	$SelectMesFinalizacionNF = getSelectValor('Mes', $data['reg']['MesFinalizacionNF'], '', 'PARAMETROS.Valor');

	// IDIOMAS
	$IdIdioma = 0;
	$Idioma = '';
	$SelectNivelIdioma = getSelect('NivelDominioIdioma', $data['reg']['NivelIdioma'], '', 'PARAMETROS.Valor');

	// OTROS CONOCIMIENTOS
	$SelectNivelConocimiento = getSelect('NivelDominioOC', $data['reg']['NivelConocimiento'], '', 'PARAMETROS.Valor');

	// CONTACTOS
	$SelectParentesco = getSelect('Parentesco', $data['reg']['ParentescoContacto'], '', 'PARAMETROS.Valor');

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

	// CONTRATOS


	$Documento = $data['reg']['Documento'];
	$regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento'");
	$EstadoEmpleado = getRegistro('PARAMETROS', $regEmpleado['estado'])['detalle'];
?>
<?php if ($_SESSION['Login']['Perfil'] <> EMPLEADO): ?>
<div id="main">
	<?php else: ?>
	<div>
		<?php endif; ?>
		<div class="row">
			<div class="content-wrapper-before cyan darken-4"></div>
			<div class="col s12 m12 l12">
				<div class="container">
					<div class="section section-data-tables">
						<div class="card">
							<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
								<div class="row">
									<div class="col s12 m6">
										<?php if ($EstadoEmpleado == 'ACTIVO'): ?>
										<h3 class="white-text">EMPLEADO</h3>
										<?php else: ?>
										<h3 class="white-text">CANDIDATO</h3>
										<?php endif; ?>
									</div>
									<div class="col s12 m6 right-align">
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
													<img src="<?= $archivo ?>" alt="Fotografia" class="border-radius-4"
														height="250px" width="200px">
													<h5 class="text center">
														<?= $regEmpleado['nombre1'] . ' ' . $regEmpleado['nombre2'] . ' ' .$regEmpleado['apellido1'] . ' ' .$regEmpleado['apellido2'] ?>
													</h5>
												</div>
											</div>
											<div class="row">
												<div class="card-panel">
													<ul class="tabs">
														<li class="tab">
															<a href="#pagIdentificacion"
																<?= $_SESSION['Paso'] == 1 ? 'class="active"' : '' ?>>
																<i class="material-icons">person_outline</i>
																<span>Identificación</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagPersonal"
																<?= $_SESSION['Paso'] == 2 ? 'class="active"' : '' ?>>
																<i class="material-icons">person_outline</i>
																<span>Información personal</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagContactoPersonal"
																<?= $_SESSION['Paso'] == 3 ? 'class="active"' : '' ?>>
																<i class="material-icons">location_on</i>
																<span>Información de contacto</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagAfiliaciones"
																<?= $_SESSION['Paso'] == 4 ? 'class="active"' : '' ?>>
																<i class="material-icons">chrome_reader_mode</i>
																<span>Afiliaciones</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagPerfilProfesional"
																<?= $_SESSION['Paso'] == 5 ? 'class="active"' : '' ?>>
																<i class="material-icons">accessibility</i>
																<span>Perfil profesional</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagExperiencia"
																<?= $_SESSION['Paso'] == 6 ? 'class="active"' : '' ?>>
																<i
																	class="material-icons">airline_seat_recline_normal</i>
																<span>Experiencia laboral</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagEducacionFormal"
																<?= $_SESSION['Paso'] == 7 ? 'class="active"' : '' ?>>
																<i class="material-icons">school</i>
																<span>Educación formal</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagEducacionNoFormal"
																<?= $_SESSION['Paso'] == 8 ? 'class="active"' : '' ?>>
																<i class="material-icons">school</i>
																<span>Educación no formal</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagIdiomas"
																<?= $_SESSION['Paso'] == 9 ? 'class="active"' : '' ?>>
																<i class="material-icons">textsms</i>
																<span>Idiomas</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagOtrosConocimientos"
																<?= $_SESSION['Paso'] == 10 ? 'class="active"' : '' ?>>
																<i class="material-icons">book</i>
																<span>Otros conocimientos</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagContactos"
																<?= $_SESSION['Paso'] == 11 ? 'class="active"' : '' ?>>
																<i class="material-icons">people_outline</i>
																<span>Contactos</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagDocumentos"
																<?= $_SESSION['Paso'] == 12 ? 'class="active"' : '' ?>>
																<i class="material-icons">folder_open</i>
																<span>Documentos</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagGruposPoblacionales"
																<?= $_SESSION['Paso'] == 13 ? 'class="active"' : '' ?>>
																<i class="material-icons">folder_open</i>
																<span>Grupos poblacionales</span>
															</a>
														</li>
														<?php if ($_SESSION['Login']['Perfil'] <> EMPLEADO): ?>
														<li class="tab">
															<a href="#pagDocumentosSeleccion"
																<?= $_SESSION['Paso'] == 14 ? 'class="active"' : '' ?>>
																<i class="material-icons">folder_open</i>
																<span>Documentos proceso selección</span>
															</a>
														</li>
														<li class="tab">
															<a href="#pagCondicionesLaborales"
																<?= $_SESSION['Paso'] == 15 ? 'class="active"' : '' ?>>
																<i class="material-icons">folder_open</i>
																<span>Condiciones laborales</span>
															</a>
														</li>
														<?php endif; ?>
														<li class="tab">
															<a href="#pagFinalizar"
																<?= $_SESSION['Paso'] == 16 ? 'class="active"' : '' ?>>
																<i class="material-icons">send</i>
																<span>Finalizar</span>
															</a>
														</li>
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
																				<input name="PoliticamenteExpuesta" type="radio" value="si" $CheckedRadio />
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
																				<input name="PoliticamenteExpuesta" type="radio" value="no" $CheckedRadio />
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
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
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
															<p>INFORMACIÓN PERSONAL (PASO 2)</p>
														</div>
													</div>
													<div class="row">
														<div class="input-field col s6 m6">
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
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4" type="submit"
																name="Action" value="GUARDAR_2">
																GUARDAR DATOS Y AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
											</div>
											<div id="pagContactoPersonal" class="col s12">
												<div class="card-panel">
													<div class="card-alert card cyan darken-4">
														<div class="card-content white-text">
															<p>CONTACTO PERSONAL (PASO 3)</p>
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
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4" type="submit"
																name="Action" value="GUARDAR_3">
																GUARDAR DATOS Y AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
											</div>
											<div id="pagAfiliaciones" class="col s12">
												<div class="card-panel">
													<div class="card-alert card cyan darken-4">
														<div class="card-content white-text">
															<p>AFILIACIONES (PASO 4)</p>
														</div>
													</div>
													<div class="row">
														<div class="input-field col s12 m6">
															<input type="hidden" name="IdEPS" id="IdEPS" value="<?= $IdEPS ?>">
															<?php 
																get(label('E.P.S.'), 'NombreEPS', $NombreEPS, 'text', 60, FALSE, '', 'textsms'); 
															?>
															<div id="suggestionsEPS"></div>
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
														<div class="input-field col s12 m6">
															<input type="hidden" name="IdFondoPensiones" id="IdFondoPensiones" value="<?= $IdFondoPensiones ?>">
															<?php 
																get(label('Fondo de pensiones'), 'NombreFP', $NombreFP, 'text', 60, FALSE, '', 'textsms'); 
															?>
															<div id="suggestionsFP"></div>
														</div>
													</div>
													<div class="row">
														<div class="input-field col s12 m6">
															<input type="hidden" name="IdBanco" id="IdBanco" value="<?= $IdBanco ?>">
															<?php 
																get(label('Entidad bancaria'), 'NombreBanco', $NombreBanco, 'text', 60, FALSE, '', 'textsms'); 
															?>
															<div id="suggestionsBanco"></div>
														</div>
														<div class="input-field col s12 m6">
															<?php 
																get(label('Tipo cuenta bancaria*'), 'TipoCuentaBancaria', $SelectTipoCuentaBancaria, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
															?>
														</div>
													</div>
													<div class="row">
														<div class="input-field col s12 m6">
															<?php 
																get(label('Cuenta bancaria*'), 'CuentaBancaria', $data['reg']['CuentaBancaria'], 'text', 20, FALSE, '', 'fas fa-edit'); 
															?>
														</div>
													</div>
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4" type="submit"
																name="Action" value="GUARDAR_4">
																GUARDAR DATOS Y AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
											</div>
											<div id="pagPerfilProfesional" class="col s12">
												<div class="card-panel">
													<div class="card-alert card cyan darken-4">
														<div class="card-content white-text">
															<p>PERFIL PROFESIONAL (PASO 5)</p>
														</div>
													</div>
													<div class="row">
														<div class="input-field col s12">
															<?php 
																get(label('Perfil profesional*'), 'PerfilProfesional', $data['reg']['PerfilProfesional'], 'textarea', 5, FALSE, '', 'far fa-edit'); 
															?>
														</div>
													</div>
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4" type="submit"
																name="Action" value="GUARDAR_5">
																GUARDAR DATOS Y AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
											</div>
											<div id="pagExperiencia" class="col s12">
												<div class="card-panel">
													<div class="card-alert card cyan darken-4">
														<div class="card-content white-text">
															<p>EXPERIENCIA LABORAL (PASO 6)</p>
														</div>
													</div>

													<div class="row">
														<div class="input-field col s12 m4">
															<?php 
																get(label('Empresa'), 'Empresa', $data['reg']['Empresa'], 'text', 100, FALSE, '', 'fas fa-pen'); 
															?>
														</div>
														<div class="input-field col s12 m4">
															<input type="hidden" name="IdCiudadEmpresa" id="IdCiudadEmpresa" value="<?= $IdCiudadEmpresa ?>">
															<?php 
																get(label('Ciudad de trabajo'), 'CiudadEmpresa', $CiudadEmpresa, 'text', 25, FALSE, '', 'textsms'); 
															?>
															<div id="suggestionsCiudadEmpresa"></div>
														</div>
														<div class="input-field col s12 m4">
															<?php 
																get(label('Teléfono de la empresa'), 'TelefonoEmpresa', $data['reg']['TelefonoEmpresa'], 'tel', 15, FALSE, '', 'fas fa-phone'); 
															?>
														</div>
													</div>
													<div class="row">
														<div class="col s12 m4">
															<?php 
																get(label('Cargo en la empresa'), 'CargoEmpresa', $data['reg']['CargoEmpresa'], 'text', 100, FALSE, '', 'fas fa-pen'); 
															?>
														</div>
														<div class="col s12 m4">
															<?php 
																get(label('Jefe inmediato'), 'JefeInmediato', $data['reg']['JefeInmediato'], 'text', 100, FALSE, '', 'fas fa-pen'); 
															?>
														</div>
													</div>
													<div class="row">
														<div class="col s12 m4">
															<?php 
																get(label('Fecha de ingreso'), 'FechaIngresoEmpresa', $data['reg']['FechaIngresoEmpresa'], 'date', 10, FALSE, '', 'far fa-calendar'); 
															?>
														</div>
														<div class="col s12 m4">
															<?php 
																get(label('Fecha de retiro'), 'FechaRetiroEmpresa', $data['reg']['FechaRetiroEmpresa'], 'date', 10, FALSE, '', 'far fa-calendar'); 
															?>
														</div>
													</div>
													<div class="row">
														<div class="col s12 m12">
															<?php 
																get(label('Funciones del cargo'), 'Responsabilidades', $data['reg']['Responsabilidades'], 'textarea', 5, FALSE, '', 'far fa-edit'); 
															?>
														</div>
													</div>
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm blue darken-3" type="submit"
																name="Action" value="ACTUALIZAR_6">
																ADICIONAR EXPERIENCIA LABORAL
																<i class="medium material-icons left">save</i>
															</button>
														</div>
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4" type="submit"
																name="Action" value="GUARDAR_6">
																GUARDAR DATOS Y AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
												<div class="card-panel">
													<table class="responsive-table">
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
																	<button class="btn btn-sm red darken-4"
																		type="submit" name="Action"
																		value="BORRAR_<?= $i ?>_6">
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
															<p>EDUCACIÓN FORMAL (PASO 7)</p>
														</div>
													</div>

													<div class="row">
														<div class="col s12 m4">
															<?php 
																get(label('Centro educativo'), 'CentroEducativoF', $data['reg']['CentroEducativoF'], 'text', 100, FALSE, '', 'fas fa-pen'); 
															?>
														</div>
														<div class="col s12 m4">
															<?php
																get(label('Nivel académico'), 'NivelAcademicoF', $SelectNivelAcademicoF, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
															?>
														</div>
														<div class="col s12 m4">
															<?php
																get(label('Estado'), 'EstadoF', $SelectEstadoNivelAcademicoF, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
															?>
														</div>
													</div>
													<div class="row">
														<div class="col s12">
															<?php 
																get(label('Estudios realizados'), 'EstudioF', $data['reg']['EstudioF'], 'text', 100, FALSE, '', 'fas fa-pen'); 
															?>
														</div>
													</div>
													<div class="row">
														<div class="col s12 m4">
															<?php
																get(label('Año inicio'), 'AnoInicioF', $data['reg']['AnoInicioF'], 'number', 4, FALSE, '', 'fas fa-pen');
															?>
														</div>
														<div class="col s12 m4">
															<?php
																get(label('Mes inicio'), 'MesInicioF', $SelectMesInicioF, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
															?>
														</div>
													</div>
													<div class="row">
														<div class="col s12 m4">
															<?php
																get(label('Año finalización'), 'AnoFinalizacionF', $data['reg']['AnoFinalizacionF'], 'number', 4, FALSE, '', 'fas fa-pen');
															?>
														</div>
														<div class="col s12 m4">
															<?php
																get(label('Mes finalización'), 'MesFinalizacionF', $SelectMesFinalizacionF, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
															?>
														</div>
													</div>
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm blue darken-4" type="submit"
																name="Action" value="ACTUALIZAR_7">
																ADICIONAR ESTUDIOS
																<i class="medium material-icons left">save</i>
															</button>
														</div>
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4" type="submit"
																name="Action" value="GUARDAR_7">
																GUARDAR DATOS Y AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
												<div class="card-panel">
													<table class="responsive-table">
														<thead>
															<tr>
																<th style="text-align:center;"></th>
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
																<td class="center-align">
																	<button class="btn btn-sm red darken-4"
																		type="submit" name="Action"
																		value="BORRAR_<?= $i ?>_7">
																		<i class="small material-icons">delete</i>
																	</button>
																</td>
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
															<p>EDUCACIÓN NO FORMAL (PASO 8)</p>
														</div>
													</div>

													<div class="row">
														<div class="col s12 m4">
															<?php 
																get(label('Centro educativo'), 'CentroEducativoNF', $data['reg']['CentroEducativoNF'], 'text', 100, FALSE, '', 'fas fa-pen'); 
															?>
														</div>
														<div class="col s12 m4">
															<?php
																get(label('Nivel académico'), 'NivelAcademicoNF', $SelectNivelAcademicoNF, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
															?>
														</div>
														<div class="col s12 m4">
															<?php
																get(label('Estado'), 'EstadoNF', $SelectEstadoNivelAcademicoNF, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
															?>
														</div>
													</div>
													<div class="row">
														<div class="col s12">
															<?php 
																get(label('Estudios realizados'), 'EstudioNF', $data['reg']['EstudioNF'], 'text', 100, FALSE, '', 'fas fa-pen'); 
															?>
														</div>
													</div>
													<div class="row">
														<div class="col s12 m4">
															<?php
																get(label('Año inicio'), 'AnoInicioNF', $data['reg']['AnoInicioNF'], 'number', 4, FALSE, '', 'fas fa-pen');
															?>
														</div>
														<div class="col s12 m4">
															<?php
																get(label('Mes inicio'), 'MesInicioNF', $SelectMesInicioNF, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
															?>
														</div>
													</div>
													<div class="row">
														<div class="col s12 m4">
															<?php
																get(label('Año finalización'), 'AnoFinalizacionNF', $data['reg']['AnoFinalizacionNF'], 'number', 4, FALSE, '', 'fas fa-pen');
															?>
														</div>
														<div class="col s12 m4">
															<?php
																get(label('Mes finalización'), 'MesFinalizacionNF', $SelectMesFinalizacionNF, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
															?>
														</div>
													</div>
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm blue darken-4" type="submit"
																name="Action" value="ACTUALIZAR_8">
																ADICIONAR ESTUDIOS
																<i class="medium material-icons left">save</i>
															</button>
														</div>
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4" type="submit"
																name="Action" value="GUARDAR_8">
																GUARDAR DATOS Y AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
												<div class="card-panel">
													<table class="responsive-table">
														<thead>
															<tr>
																<th style="text-align:center;"></th>
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
																<td class="center-align">
																	<button class="btn btn-sm red darken-4"
																		type="submit" name="Action"
																		value="BORRAR_<?= $i ?>_8">
																		<i class="small material-icons">delete</i>
																	</button>
																</td>
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
															<p>IDIOMAS (PASO 9)</p>
														</div>
													</div>

													<div class="row">
														<h5>Incluya los idiomas que conoce, como mínimo incluya el
															Español como idioma nativo</h5>
													</div>
													<div class="row">
														<div class="input-field col s12 m6">
															<input type="hidden" name="IdIdioma" id="IdIdioma" value="<?= $IdIdioma ?>">
															<?php 
																get(label('Idioma'), 'Idioma', $Idioma, 'text', 25, FALSE, '', 'textsms'); 
															?>
															<div id="suggestionsIdioma"></div>
														</div>
														<div class="input-field col s12 m6">
															<?php
																get(label('Nivel'), 'NivelIdioma', $SelectNivelIdioma, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
															?>
														</div>
													</div>
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm blue darken-4" type="submit"
																name="Action" value="ACTUALIZAR_9">
																ADICIONAR IDIOMA
																<i class="material-icons left">save</i>
															</button>
														</div>
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4" type="submit"
																name="Action" value="GUARDAR_9">
																GUARDAR DATOS Y AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
												<div class="card-panel">
													<table class="responsive-table">
														<thead>
															<tr>
																<th style="text-align:center;"></th>
																<th>IDIOMA</th>
																<th>NIVEL</th>
															</tr>
														</thead>
														<tbody>
															<?php if ($data['regIdiomas']): ?>
															<?php for ($i = 0; $i < count($data['regIdiomas']); $i++ ): ?>
															<tr>
																<td class="center-align">
																	<button class="btn btn-sm red darken-4"
																		type="submit" name="Action"
																		value="BORRAR_<?= $i ?>_9">
																		<i class="small material-icons">delete</i>
																	</button>
																</td>
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
															<p>OTROS CONOCIMIENTOS (PASO 10)</p>
														</div>
													</div>

													<div class="row">
														<h5>Incluya los conocimientos en herramientas de ofimática como Excel, Word, etc. o cualquier otro tipo de conocimiento que domine</h5>
													</div>
													<div class="row">
														<div class="col s12 m6">
															<?php
																get(label('Conocimiento'), 'Conocimiento', $data['reg']['Conocimiento'], 'text', 100, FALSE, '', 'fas fa-pen');
															?>
														</div>
														<div class="col s12 m6">
															<?php
																get(label('Nivel'), 'NivelConocimiento', $SelectNivelConocimiento, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
															?>
														</div>
													</div>
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm blue darken-4" type="submit"
																name="Action" value="ACTUALIZAR_10">
																ADICIONAR CONOCIMIENTO
																<i class="material-icons left">save</i>
															</button>
														</div>
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4" type="submit"
																name="Action" value="GUARDAR_10">
																GUARDAR DATOS Y AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
												<div class="card-panel">
													<table class="responsive-table">
														<thead>
															<tr>
																<th style="text-align:center;"></th>
																<th>CONOCIMIENTO</th>
																<th>NIVEL</th>
															</tr>
														</thead>
														<tbody>
															<?php if ($data['regOCE']): ?>
															<?php for ($i = 0; $i < count($data['regOCE']); $i++ ): ?>
															<tr>
																<td class="center-align">
																	<button class="btn btn-sm red darken-4"
																		type="submit" name="Action"
																		value="BORRAR_<?= $i ?>_10">
																		<i class="small material-icons">delete</i>
																	</button>
																</td>
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
															<p>CONTACTOS (PASO 11)</p>
														</div>
													</div>

													<div class="row">
														<div class="col s12 m6">
															<?php
																get(label('Nombre contacto'), 'NombreContacto', $data['reg']['NombreContacto'], 'text', 100, FALSE, '', 'fas fa-pen');
															?>
														</div>
														<div class="col s12 m6">
															<?php
																get(label('Teléfono contacto'), 'TelefonoContacto', $data['reg']['TelefonoContacto'], 'tel', 15, FALSE, '', 'fas fa-phone');
															?>
														</div>
													</div>
													<div class="row">
														<div class="col s12 m6">
															<?php
																get(label('Parentesco'), 'ParentescoContacto', $SelectParentesco, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
															?>
														</div>
													</div>
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm blue darken-4" type="submit"
																name="Action" value="ACTUALIZAR_11">
																ADICIONAR CONTACTO
																<i class="material-icons left">save</i>
															</button>
														</div>
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4" type="submit"
																name="Action" value="GUARDAR_11">
																GUARDAR DATOS Y AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
												<div class="card-panel">
													<table class="responsive-table">
														<thead>
															<tr>
																<th style="text-align:center;"></th>
																<th>CONTACTO</th>
																<th>TELÉFONO</th>
																<th>PARENTESCO</th>
															</tr>
														</thead>
														<tbody>
															<?php if ($data['regContacto']): ?>
															<?php for ($i = 0; $i < count($data['regContacto']); $i++ ): ?>
															<tr>
																<td class="center-align">
																	<button class="btn btn-sm red darken-4"
																		type="submit" name="Action"
																		value="BORRAR_<?= $i ?>_11">
																		<i class="small material-icons">delete</i>
																	</button>
																</td>
																<td><?= $data['regContacto'][$i]['NombreContacto'] ?>
																</td>
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
															<p>DOCUMENTOS (PASO 12)</p>
														</div>
													</div>
													<div class="row">
														<div class="col s12 m6">
															<div class="row">
																<div class="col s12">
																	<h5>POR FAVOR ADJUNTE LOS SIGUIENTES DOCUMENTOS</h5>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Fotografía de frente en fondo blanco'), 'Fotografia', '.png, .jpg, .jpeg', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['Fotografia']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
																<div class="col s12">
																	<div id="cond-conflicto">
																	Recuerda que debe ser una fotografía corporativa con fondo blanco para el carnet.
																	</div>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Documento de identidad'), 'DocumentoIdentidad', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['DocumentoIdentidad']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Hoja de vida'), 'HojaVida', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['HojaVida']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Certificados académicos (multiples archivos)'), 'CertificadosAcademicos', '.pdf', 'file', 0, FALSE, 'multiple', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['CertificadosAcademicos']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s12">
																	<h5>SI HA LABORADO PREVIAMENTE ADJUNTE LOS
																		SIGUIENTES DOCUMENTOS</h5>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Certificado fondo pensiones (SI ESTÁ O ESTUVO AFILIADO)'), 'CertificadoFondoPensiones', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['CertificadoFP']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Certificado regímenes de EPS'), 'CertificadoRegimenEps', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['CertificadoRegimenEps']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>															
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Certificado EPS (SI ESTÁ O ESTUVO AFILIADO)'), 'CertificadoEPS', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['CertificadoEPS']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Certificado fondo cesantías (SI ESTÁ O ESTUVO AFILIADO)'), 'CertificadoFondoCesantias', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['CertificadoFC']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Certificados laborales (multiples archivos)'), 'CertificadosLaborales', '.pdf', 'file', 0, FALSE, 'multiple', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['CertificadosLaborales']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Certificación cuenta bancaria'), 'CertificacionBancaria', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['CertificacionBancaria']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Tarjeta profesional (OPCIONAL)'), 'TarjetaProfesional', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['TarjetaProfesional']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
														</div>
														<div class="col s6">
															<div class="card-panel">
																<div class="card-alert card cyan darken-4">
																	<div class="card-content white-text">
																		<p>AQUI PUEDE VALIDAR LOS DOCUMENTOS CARGADOS
																		</p>
																	</div>
																</div>
																<div class="row">
																	<ul class="collapsible">
																		<li class="active">
																			<div class="collapsible-header">
																				<i
																					class="material-icons">folder_open</i>
																				H.V.
																			</div>
																			<div class="collapsible-body">
																				<table class="responsive-table">
																					<tbody>
																						<?php
																							$cDirectorio = 'documents/' . trim($data['reg']['Documento']) . '_' . strtoupper(trim($data['reg']['Apellido1']) . '_' . trim($data['reg']['Apellido2']) . '_' . trim($data['reg']['Nombre1']) . '_' . trim($data['reg']['Nombre2'])) . '/HV';

																							if (is_dir($cDirectorio)):
																								$dir = opendir($cDirectorio);

																								while (($archivo = readdir($dir)) !== false):
																									if ($archivo != '.' AND $archivo != '..'):
																						?>
																						<tr>
																							<td>
																								<?php 
																									$NombreArchivo = 
																								str_replace($data['reg']['Documento'] . '_', '', $archivo);
																									$NombreArchivo = 
																								str_replace('.pdf', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.png', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.jpg', '', $NombreArchivo);
																								
																									echo str_replace("_", " ", $NombreArchivo);
																								?>
																							</td>
																							<td>
																								<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																									target="_blank"
																									class="btn btn-sm teal lighten-3 tooltipped"
																									data-position="bottom"
																									data-tooltip="Ver documento">
																									<i
																										class='fas fa-eye'></i>
																								</a>
																							</td>
																							<!-- <td>
																							<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																								download="<?php echo $archivo; ?>"
																								class="btn btn-sm teal lighten-3 tooltipped"
																								data-position="bottom"
																								data-tooltip="Descargar documento">
																								<i class='fas fa-download'></i>
																							</a>
																						</td> -->
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
																				<i
																					class="material-icons">folder_open</i>
																				SEGURIDAD SOCIAL
																			</div>
																			<div class="collapsible-body">
																				<table class="responsive-table">
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
																								<?php 
																									$NombreArchivo = 
																								str_replace($data['reg']['Documento'] . '_', '', $archivo);
																									$NombreArchivo = 
																								str_replace('.pdf', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.png', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.jpg', '', $NombreArchivo);
																								
																									echo str_replace("_", " ", $NombreArchivo);
																								?>
																							</td>
																							<td>
																								<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																									target="_blank"
																									class="btn btn-sm teal lighten-3 tooltipped"
																									data-position="bottom"
																									data-tooltip="Ver documento">
																									<i
																										class='fas fa-eye'></i>
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
																				<i
																					class="material-icons">folder_open</i>
																				SOPORTES ACADÉMICOS
																			</div>
																			<div class="collapsible-body">
																				<table class="responsive-table">
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
																								<?php 
																									$NombreArchivo = 
																								str_replace($data['reg']['Documento'] . '_', '', $archivo);
																									$NombreArchivo = 
																								str_replace('.pdf', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.png', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.jpg', '', $NombreArchivo);
																								
																									echo str_replace("_", " ", $NombreArchivo);
																								?>
																							</td>
																							<td>
																								<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																									target="_blank"
																									class="btn btn-sm teal lighten-3 tooltipped"
																									data-position="bottom"
																									data-tooltip="Ver documento">
																									<i
																										class='fas fa-eye'></i>
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
																				<i
																					class="material-icons">folder_open</i>
																				SOPORTES LABORALES
																			</div>
																			<div class="collapsible-body">
																				<table class="responsive-table">
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
																								<?php 
																									$NombreArchivo = 
																								str_replace($data['reg']['Documento'] . '_', '', $archivo);
																									$NombreArchivo = 
																								str_replace('.pdf', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.png', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.jpg', '', $NombreArchivo);
																								
																									echo str_replace("_", " ", $NombreArchivo);
																								?>
																							</td>
																							<td>
																								<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																									target="_blank"
																									class="btn btn-sm teal lighten-3 tooltipped"
																									data-position="bottom"
																									data-tooltip="Ver documento">
																									<i
																										class='fas fa-eye'></i>
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
														</div>
													</div>
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO OR $_SESSION['Login']['Perfil'] == SELECCION): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm blue darken-4 col s12"
																style="margin-top: 10px;" type="submit" name="Action"
																value="GUARDAR_12">
																CARGAR DOCS.
																<i class="medium material-icons left">save</i>
															</button>
														</div>
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4 col s12"
																style="margin-top: 10px;" type="submit" name="Action"
																value="GUARDAR_12">
																AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
											</div>
											<div id="pagGruposPoblacionales" class="col s12">
												<div class="card-panel">
													<div class="card-alert card cyan darken-4">
														<div class="card-content white-text">
															<p>GRUPOS POBLACIONALES (PASO 13)</p>
														</div>
													</div>
													<div class="row">
														<div class="col s12 m6">
															<div class="row">
																<div class="col s10">
																	<!-- <a onclick="M.toast({html: 'I am a toast'})" class="btn btn-floating">?</a> -->
																	<?php 
																		get(label('Persona en pobreza extrema'), 'EnPobrezaExtrema', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['EnPobrezaExtrema']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
																<div class="col s10">
																	<?php 
																		get(label('Desplazado por el conflicto armado'), 'Desplazado', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['Desplazado']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
																<div class="col s12">
																	<a style="cursor:pointer" onclick="showcondicion('cond-conflicto')">condiciones</a>
																	<div id="cond-conflicto" style="display:none">
																	Estar registrado en el Registro Único de Victimas RUV, 
																	de la Unidad para la atención reparación integral a las víctimas de la violencia. 
																	Adicionalmente a la contratación del personal con personas de especial protección 
																	constitucional el PROVEEDOR deberá garantizar la contratación mínimo de un 5% del personal 
																	de la operación que sean beneficiarios activos (época de estudios o amortización) de algún 
																	beneficio de la entidad.
																	</div>
																</div>
																<div class="col s10">
																	<?php 
																	get(label('En proceso de reincorporación'), 'EnReincorporacion', '.pdf', 'file', 0, FALSE, '', '');
																?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['EnReincorporacion']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
																<div class="col s10">
																	<?php 
																		get(label('Adulto mayor'), 'AdultoMayor', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['AdultoMayor']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
																<div class="col s10">
																	<?php 
																		get(label('Mujer/Madre/Padre cabeza de hogar'), 'CabezaHogar', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['CabezaHogar']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
																<div class="col s12">
																	<a style="cursor:pointer" onclick="showcondicion('cond-madres')">condiciones</a>
																	<div id="cond-madres" style="display:none">
																	Formato de Declaración Juramentada como Madre Cabeza de Familia y debe cumplir de acuerdo con la Corte Constitucional las siguientes condiciones:
																	(i) que se tenga a cargo la responsabilidad de hijos menores o de otras personas incapacitadas para trabajar; 
																	(ii) que esa responsabilidad sea de carácter permanente; 
																	(iii) no sólo la ausencia permanente o abandono del hogar por parte de la pareja, sino que aquélla se sustraiga del cumplimiento de sus obligaciones como padre; 
																	(iv) o bien que la pareja no asuma la responsabilidad que le corresponde y ello obedezca a un motivo verdaderamente poderoso como la incapacidad física, sensorial, síquica o mental o, como es obvio, la muerte;
																	(v) por último, que haya una deficiencia sustancial de ayuda de los demás miembros de la familia, lo cual significa la responsabilidad solitaria de la madre para sostener el hogar”.
																	</div>
																</div>
																<div class="col s10">
																	<?php 
																		get(label('Persona en condición de discapacidad'), 'Discapacitado', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['Discapacitado']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
																<div class="col s12">
																	<a style="cursor:pointer" onclick="showcondicion('cond-discapacidad')">condiciones</a>
																	<div id="cond-discapacidad" style="display:none">
																	Certificado y registro de discapacidad (Resolución 0583 de 2018) 
																	del Ministerio de Salud y Protección Social.
																	</div>
																</div>
																<div class="col s10">
																	<?php 
																		get(label('Comunidad LGBTIQ+'), 'ComunidadLGBTI', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['ComunidadLGBTI']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
																<div class="col s12">
																	<a style="cursor:pointer" onclick="showcondicion('cond-LGBTIQ')">condiciones</a>
																	<div id="cond-LGBTIQ" style="display:none">
																	Certificado de puño y letra firmado por el candidato, manifestando su grupo poblacional
																	</div>
																</div>
																<div class="col s10">
																	<?php 
																		get(label('Negritudes'), 'Negritudes', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['Negritudes']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
																<div class="col s12">
																	<a style="cursor:pointer" onclick="showcondicion('cond-negritudes')">condiciones</a>
																	<div id="cond-negritudes" style="display:none">
																	Certificación de Autorreconocimiento como miembro 
																	de la Población Negra, Afrocolombiana, Raizal y Palenquera, 
																	emitido por el Ministerio del Interior
																	</div>
																</div>
																<div class="col s10">
																	<?php 
																		get(label('Indigenas'), 'Indigenas', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['Indigenas']): ?>
																	<strong class="teal darken-3">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
																<div class="col s12">
																	<a style="cursor:pointer" onclick="showcondicion('cond-indigenas')">condiciones</a>
																	<div id="cond-indigenas" style="display:none">
																	Certificado de pertenencia indígena del Ministerio del Interior, 
																	basado en los auto censos de las comunidades y reportados a la Dirección 
																	de asuntos Indígenas, Rom, y Minorías (DAIRM) antes del 30 de abril de 
																	cada año
																	</div>
																</div>
															</div>
														</div>
														<div class="col s6">
															<div class="card-panel">
																<div class="card-alert card cyan darken-4">
																	<div class="card-content white-text">
																		<p>AQUI PUEDE VALIDAR LOS DOCUMENTOS CARGADOS
																		</p>
																	</div>
																</div>
																<div class="row">
																	<ul class="collapsible">
																		<li class="active">
																			<div class="collapsible-header">
																				<i
																					class="material-icons">folder_open</i>
																				GRUPOS POBLACIONALES
																			</div>
																			<div class="collapsible-body">
																				<table class="responsive-table">
																					<tbody>
																						<?php
																							$cDirectorio = 'documents/' . trim($data['reg']['Documento']) . '_' . strtoupper(trim($data['reg']['Apellido1']) . '_' . trim($data['reg']['Apellido2']) . '_' . trim($data['reg']['Nombre1']) . '_' . trim($data['reg']['Nombre2'])) . '/GRUPOS_POBLACIONALES';

																							if (is_dir($cDirectorio)):
																								$dir = opendir($cDirectorio);

																								while (($archivo = readdir($dir)) !== false):
																									if ($archivo != '.' AND $archivo != '..'):
																						?>
																						<tr>
																							<td>
																								<?php 
																									$NombreArchivo = 
																								str_replace($data['reg']['Documento'] . '_', '', $archivo);
																									$NombreArchivo = 
																								str_replace('.pdf', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.png', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.jpg', '', $NombreArchivo);
																								
																									echo str_replace("_", " ", $NombreArchivo);
																								?>
																							</td>
																							<td>
																								<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																									target="_blank"
																									class="btn btn-sm teal lighten-3 tooltipped"
																									data-position="bottom"
																									data-tooltip="Ver documento">
																									<i
																										class='fas fa-eye'></i>
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
														</div>
													</div>
													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm blue darken-4 col s12"
																style="margin-top: 10px;" type="submit" name="Action"
																value="GUARDAR_13">
																CARGAR DOCS.
																<i class="medium material-icons left">save</i>
															</button>
														</div>
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4 col s12"
																style="margin-top: 10px;" type="submit" name="Action"
																value="GUARDAR_13">
																AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
													<?php endif; ?>
												</div>
											</div>
											<?php if ($_SESSION['Login']['Perfil'] <> EMPLEADO): ?>
											<div id="pagDocumentosSeleccion" class="col s12">
												<div class="card-panel">
													<div class="card-alert card cyan darken-4">
														<div class="card-content white-text">
															<p>DOCUMENTOS PROCESO SELECCIÓN (PASO 14)</p>
														</div>
													</div>
													<div class="row">
														<div class="col s12 m6">
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Caso Aranda Service Desk'), 'casoArandaServiceDesk', '.xlsx,.xls,.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['casoArandaServiceDesk']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Antecedentes procuraduría'), 'AntecedentesProcuraduria', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['AntecedentesProcuraduria']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Certificado Universidad (Aprendiz)'), 'certificadoAprendiz', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['certificadoAprendiz']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Antecedentes contraloría'), 'AntecedentesContraloria', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['AntecedentesContraloria']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Antecedentes policía'), 'AntecedentesPolicia', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['AntecedentesPolicia']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Certificado inhabilidades sexuales'), 'InhabilidadesSexuales', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['InhabilidadesSexuales']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Consulta Infolaft'), 'ConsultaInfolaft', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['ConsultaInfolaft']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Informe de selección'), 'InformeSeleccion', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['InformeSeleccion']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Estudio de seguridad'), 'EstudioSeguridad', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['EstudioSeguridad']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Prueba 360'), 'Prueba360', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['Prueba360']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Prueba técnica'), 'PruebaTecnica', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['PruebaTecnica']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Prueba Óptimo'), 'PruebaOptimo', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['PruebaOptimo']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Prueba de ortografía'), 'PruebaOrtografia', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['PruebaOrtografia']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('RUAF'), 'RUAF', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['RUAF']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Examen médico'), 'ExamenMedico', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['ExamenMedico']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
															<div class="row">
																<div class="col s10">
																	<?php 
																		get(label('Recomendaciones médicas'), 'RecomendacionesMedicas', '.pdf', 'file', 0, FALSE, '', '');
																	?>
																</div>
																<div class="col s2">
																	<?php if ($data['reg']['RecomendacionesMedicas']): ?>
																	<strong class="green darken-4">
																		<i class="medium material-icons left">check</i>
																	</strong>
																	<?php endif; ?>
																</div>
															</div>
														</div>
														<div class="col s12 m6">
															<div class="card-panel">
																<div class="card-alert card cyan darken-4">
																	<div class="card-content white-text">
																		<p>AQUI PUEDE VALIDAR LOS DOCUMENTOS CARGADOS
																		</p>
																	</div>
																</div>
																<div class="row">
																	<ul class="collapsible">
																		<li class="active">
																			<div class="collapsible-header">
																				<i
																					class="material-icons">folder_open</i>
																				ANTECEDENTES
																			</div>
																			<div class="collapsible-body">
																				<table class="responsive-table">
																					<tbody>
																						<?php
																							$cDirectorio = 'documents/' . trim($data['reg']['Documento']) . '_' . strtoupper(trim($data['reg']['Apellido1']) . '_' . trim($data['reg']['Apellido2']) . '_' . trim($data['reg']['Nombre1']) . '_' . trim($data['reg']['Nombre2'])) . '/ANTECEDENTES';

																							if (is_dir($cDirectorio)):
																								$dir = opendir($cDirectorio);

																								while (($archivo = readdir($dir)) !== false):
																									if ($archivo != '.' AND $archivo != '..'):
																						?>
																						<tr>
																							<td>
																								<?php 
																									$NombreArchivo = 
																								str_replace($data['reg']['Documento'] . '_', '', $archivo);
																									$NombreArchivo = 
																								str_replace('.pdf', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.png', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.jpg', '', $NombreArchivo);
																								
																									echo str_replace("_", " ", $NombreArchivo);
																								?>
																							</td>
																							<td>
																								<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																									target="_blank"
																									class="btn btn-sm teal lighten-3 tooltipped"
																									data-position="bottom"
																									data-tooltip="Ver documento">
																									<i
																										class='fas fa-eye'></i>
																								</a>
																							</td>
																							<!-- <td>
																							<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																								download="<?php echo $archivo; ?>"
																								class="btn btn-sm teal lighten-3 tooltipped"
																								data-position="bottom"
																								data-tooltip="Descargar documento">
																								<i class='fas fa-download'></i>
																							</a>
																						</td> -->
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
																		<li >
																			<div class="collapsible-header">
																				<i
																					class="material-icons">folder_open</i>
																				H.V.
																			</div>
																			<div class="collapsible-body">
																				<table class="responsive-table">
																					<tbody>
																						<?php
																							$cDirectorio = 'documents/' . trim($data['reg']['Documento']) . '_' . strtoupper(trim($data['reg']['Apellido1']) . '_' . trim($data['reg']['Apellido2']) . '_' . trim($data['reg']['Nombre1']) . '_' . trim($data['reg']['Nombre2'])) . '/HV';

																							if (is_dir($cDirectorio)):
																								$dir = opendir($cDirectorio);

																								while (($archivo = readdir($dir)) !== false):
																									if ($archivo != '.' AND $archivo != '..'):
																						?>
																						<tr>
																							<td>
																								<?php 
																									$NombreArchivo = 
																								str_replace($data['reg']['Documento'] . '_', '', $archivo);
																									$NombreArchivo = 
																								str_replace('.pdf', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.png', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.jpg', '', $NombreArchivo);
																								
																									echo str_replace("_", " ", $NombreArchivo);
																								?>
																							</td>
																							<td>
																								<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																									target="_blank"
																									class="btn btn-sm teal lighten-3 tooltipped"
																									data-position="bottom"
																									data-tooltip="Ver documento">
																									<i
																										class='fas fa-eye'></i>
																								</a>
																							</td>
																							<!-- <td>
																							<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																								download="<?php echo $archivo; ?>"
																								class="btn btn-sm teal lighten-3 tooltipped"
																								data-position="bottom"
																								data-tooltip="Descargar documento">
																								<i class='fas fa-download'></i>
																							</a>
																						</td> -->
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
																				<table class="responsive-table">
																					<tbody>
																						<?php
																							$cDirectorio = 'documents/' . trim($data['reg']['Documento']) . '_' . strtoupper(trim($data['reg']['Apellido1']) . '_' . trim($data['reg']['Apellido2']) . '_' . trim($data['reg']['Nombre1']) . '_' . trim($data['reg']['Nombre2'])) . '/PRUEBAS_SICOTECNICAS';

																							if (is_dir($cDirectorio)):
																								$dir = opendir($cDirectorio);

																								while (($archivo = readdir($dir)) !== false):
																									if ($archivo != '.' AND $archivo != '..'):
																						?>
																						<tr>
																							<td>
																								<?php 
																									$NombreArchivo = 
																								str_replace(trim($data['reg']['Documento']) . '_', '', $archivo);
																									$NombreArchivo = 
																								str_replace('.pdf', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.png', '', $NombreArchivo);
																									$NombreArchivo = 
																								str_replace('.jpg', '', $NombreArchivo);
																								
																									echo str_replace("_", " ", $NombreArchivo);
																								?>
																							</td>
																							<td>
																								<a href="<?= SERVERURL . '/' . $cDirectorio . '/' . $archivo; ?>"
																									target="_blank"
																									class="btn btn-sm teal lighten-3 tooltipped"
																									data-position="bottom"
																									data-tooltip="Ver documento">
																									<i
																										class='fas fa-eye'></i>
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
														</div>
													</div>
													<div class="row">
														<div class="col s12">
															<h5>Si los documentos requeridos han sido cargados, encienda
																el check de DOCUMENTOS ACTUALIZADOS</h5>
														</div>
													</div>
													<div class="row">
														<div class="col s6">
															<?php
																get('Documentos han sido actualizados', 'DocumentosActualizados', $data['reg']['SEL_DocumentosActualizados'], 'checkbox', $data['reg']['SEL_DocumentosActualizados'], FALSE, '', ''); 
															?>
														</div>
													</div>
													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm blue darken-4 col s12"
																style="margin-top: 10px;" type="submit" name="Action"
																value="GUARDAR_14">
																CARGAR DOCS.
																<i class="medium material-icons left">save</i>
															</button>
														</div>
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4 col s12"
																style="margin-top: 10px;" type="submit" name="Action"
																value="GUARDAR_14">
																AVANZAR
																<i class="medium material-icons left">save</i>
															</button>
														</div>
													</div>
												</div>
											</div>
											<div id="pagCondicionesLaborales" class="col s12">
												<div class="card-panel">
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
																get(label('Duracion del contrato*'), 'duracionContrato', $data['reg']['duracionContrato'], 'number', 12, FALSE, '', 'fas fa-edit'); 
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
														<div class="input-field col s12 m6" id="fechaVencimientoContrato">
															<?php 
																get(label('Fecha vencimiento contrato*'), 'FechaVencimiento', $data['reg']['FechaVencimiento'], 'date', 0, FALSE, '', 'fas fa-calendar'); 
															?>
														</div>														
														<script>
															const div = document.getElementById("fechaVencimientoContrato");
															div.style.display = "none";
														</script>
													</div>

													<div class="containerAprendiz" style="display:none">

														<div class="row">
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Fecha Inicio Etapa Practica'), 'FechaInicioEtapaProductiva', $data['reg']['FechaInicioEtapaProductiva'] , 'date', 0, FALSE, '', 'fas fa-calendar'); 
																?>
															</div>
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Fecha fin Etapa Electiva'), 'FechaFinEtapaLectiva', $data['reg']['FechaFinEtapaLectiva'] , 'date', 0, FALSE, '', 'fas fa-calendar'); 
																?>
															</div>
														</div>

													

													</div>

													<div class="row">
														<div class="input-field col s12 m6">
															<button class="btn btn-sm cyan darken-4" type="submit"
																name="Action" value="GUARDAR_15">
																GUARDAR DATOS
															</button>
														</div>
													</div>
												</div>
											</div>
											<?php endif; ?>
											<div id="pagFinalizar" class="col s12">
												<div class="card-panel">
													<div class="card-alert card cyan darken-4">
														<div class="card-content white-text">
															<p>FINALIZAR</p>
														</div>
													</div>

													<?php if ($_SESSION['Login']['Perfil'] == EMPLEADO): ?>
														<div class="row">
															<div class="col s12">
																<h4>Por favor valide toda la información registrada antes de
																	FINALIZAR.</h4>
																<br>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<button class="btn btn-sm cyan darken-4 col s12"
																	type="submit" name="Action" value="FINALIZAR">
																	FINALIZAR ACTUALIZACIÓN DE DATOS
																	<i class="material-icons left">check</i>
																</button>
															</div>
															<div class="input-field col s12 m6">
																<button class="btn btn-sm red darken-4 col s12"
																	type="submit" name="Action" value="DESISTIR">
																	DESISTIR AL CARGO
																</button>
															</div>
														</div>
													
													<?php else: ?>
														<div class="row">
															<div class="col s12">
																<h5>Por favor valide toda la información registrada antes de proceder con la actualización final.</h5>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<?php if ($data['reg']['SEL_RevisionCliente'] == 1): ?>
																<button class="btn btn-sm cyan darken-4 col s12"
																	type="submit" name="Action" value="REVISION_CLIENTE_16">
																	Revisión cliente realizada
																	<i class="material-icons left">check_box</i>
																</button>
																<?php else: ?>
																<button class="btn btn-sm red darken-4 col s12"
																	type="submit" name="Action" value="REVISION_CLIENTE_16">
																	Revisión por parte del cliente
																	<i
																		class="material-icons left">check_box_outline_blank</i>
																</button>
																<?php endif; ?>
															</div>
															<div class="input-field col s12 m6">
																<button class="btn btn-sm red darken-4 col s12"
																	type="submit" name="Action" value="NOCALIFICADO_16">
																	Candidato no calificado
																	<i class="material-icons left">send</i>
																</button>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<button class="btn btn-sm cyan darken-4 col s12"
																	type="submit" name="Action" value="ACTUALIZAR_16">
																	Actualizar datos nuevamente
																	<i class="material-icons left">replay</i>
																</button>
															</div>
														</div>
														<br>
														<div class="row">
															<div class="col s12 m6">
																<?php 
																	get(label('Carta oferta'), 'CartaOferta', '.pdf', 'file', 0, FALSE, '', '');
																?>
															</div>
															<?php if ($data['reg']['SEL_CartaOferta'] == 1): ?>
															<div class="input-field col s12 m6">
																<button class="btn btn-sm cyan darken-4 col s12"
																	type="submit" name="Action" value="CARTA_OFERTA_16">
																	Carta Oferta enviada / Reenviar
																	<i class="material-icons left">check_box</i>
																</button>
															</div>
															<?php else: ?>
															<div class="input-field col s12 m6">
																<button class="btn btn-sm red darken-4 col s12"
																	type="submit" name="Action" value="CARTA_OFERTA_16">
																	Enviar Carta Oferta
																	<i
																		class="material-icons left">check_box_outline_blank</i>
																</button>
															</div>
															<?php endif; ?>
														</div>
														<br>
														<div class="row">
															<div class="input-field col s12 m6">
																<button class="btn btn-sm cyan darken-4 col s12"
																	type="submit" name="Action" value="CONTRATAR_16">
																	Avanzar a contratación
																	<i class="material-icons left">send</i>
																</button>
															</div>
														</div>
													<?php endif; ?>
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
	<script>
		

			function showcondicion($id){
				$(`#${$id}`).toggle("slow");
			}
			
		
	</script>
	<?php require_once('views/templates/footer.php'); ?>

<?php 
	require_once('views/templates/header.php');
	if	($_SESSION['Login']['Perfil'] <> EMPLEADO)
		require_once('views/templates/sideBar.php');

	$Documento = $data['reg']['Documento'];

	$regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Documento'");

	$EstadoEmpleado = getRegistro('PARAMETROS', $regEmpleado['estado'])['detalle'];

	$SelectTipoIdentificacion = getSelect('TipoIdentificacion', $data['reg']['TipoIdentificacion'], '', 'PARAMETROS.Valor');

	$ciudades = getTabla('CIUDADES', '', 'CIUDADES.Orden,CIUDADES.Nombre');
	$SelectCiudadExpedicion = '';
	$SelectCiudadNacimiento = '';
	$SelectCiudad = '';
	$SelectCiudadEmpresa = '';

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

		if	(isset($data['reg']['IdCiudad']) AND $ciudades[$i]['id'] == $data['reg']['IdCiudadEmpresa'])
			$SelectCiudadEmpresa .= '<option selected value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
		else
			$SelectCiudadEmpresa .= '<option value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
	}

	$SelectGenero = getSelect('Genero', $data['reg']['Genero'], '', 'PARAMETROS.Valor');
	$SelectEstadoCivil = getSelect('EstadoCivil', $data['reg']['EstadoCivil'], '', 'PARAMETROS.Valor');
	$SelectFactorRH = getSelect('FactorRH', $data['reg']['FactorRH'], '', 'PARAMETROS.Valor');
		
	$terceros = getTabla('TERCEROS', 'TERCEROS.EsEPS = 1 OR TERCEROS.EsFondoCesantias = 1 OR TERCEROS.EsFondoPensiones = 1', 'TERCEROS.Nombre');
	$SelectEPS = '';
	$SelectFondoCesantias = '';
	$SelectFondoPensiones = '';

	for ($i = 0; $i < count($terceros); $i++) 
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
	
	for ($i = 0; $i < count($bancos); $i++) 
	{ 
		if	($bancos[$i]['id'] == $data['reg']['IdBanco'])
			$SelectBanco .= '<option selected value=' . $bancos[$i]['id'] . '>' . trim($bancos[$i]['nombre']) . '</option>';
		else
			$SelectBanco .= '<option value=' . $bancos[$i]['id'] . '>' . trim($bancos[$i]['nombre']) . '</option>';
	}

	$SelectTipoCuentaBancaria = getSelect('TipoCuentaBancaria', $data['reg']['TipoCuentaBancaria'], '', 'PARAMETROS.Valor');

	$cargos = getTabla('CARGOS', '', 'CARGOS.Nombre');

	$SelectCargo = '';

	for ($i=0; $i < count($cargos); $i++) 
	{
		if (strpos($SelectCargo, $cargos[$i]['nombre']) !== false) continue;

		if	($cargos[$i]['id'] == $data['reg']['IdCargo'])
			$SelectCargo .= '<option selected value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';
		else
			$SelectCargo .= '<option value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';
	}

	// SE CONSULTA LA INFORMACION DEL PERFIL
	if ($cargos)
		if ($cargos[0]['idcargobase'] > 0)
			$regPerfil = getRegistro('PERFILES', 0, 'PERFILES.IdCargoBase = ' . $cargos[0]['idcargobase']);
		else
			$regPerfil = getRegistro('PERFILES', 0, 'PERFILES.IdCargoBase = ' . $cargos[0]['id']);
	else
		$regPerfil = false;

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
		else
			$FuncionesHSEQ = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FuncionesDelCargo' AND PARAMETROS.Detalle = 'FUNCIONES Y RESPONSABILIDADES DE LOS SISTEMAS DE GESTIÓN HSEQ-SI'")['texto'];

		if ($regPerfil['gestionHS'] > 0)
			$GestionHS = getRegistro('PARAMETROS', $regPerfil['gestionHS'])['texto'];
		else
			$GestionHS = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FuncionesDelCargo' AND PARAMETROS.Detalle = 'GESTION DE SEGURIDAD Y SALUD EN EL TRABAJO (HS)'")['texto'];

		if ($regPerfil['gestionambiental'] > 0)
			$GestionAmbiental = getRegistro('PARAMETROS', $regPerfil['gestionambiental'])['texto'];
		else
			$GestionAmbiental = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FuncionesDelCargo' AND PARAMETROS.Detalle = 'GESTION AMBIENTAL (E)'")['texto'];

		if ($regPerfil['gestioncalidad'] > 0)
			$GestionCalidad = getRegistro('PARAMETROS', $regPerfil['gestioncalidad'])['texto'];
		else
			$GestionCalidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FuncionesDelCargo' AND PARAMETROS.Detalle = 'GESTION DE CALIDAD (Q)'")['texto'];

		if ($regPerfil['gestionSI'] > 0)
			$GestionSI = getRegistro('PARAMETROS', $regPerfil['gestionSI'])['texto'];
		else
			$GestionSI = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FuncionesDelCargo' AND PARAMETROS.Detalle = 'GESTION DE SEGURIDAD DE LA INFORMACION (SI)'")['texto'];

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

	$SelectNivelAcademicoF = getSelect('NivelAcademico', $data['reg']['NivelAcademicoF'], '', 'PARAMETROS.Valor');
	$SelectEstadoNivelAcademicoF = getSelect('EstadoNivelAcademico', $data['reg']['EstadoF'], '', 'PARAMETROS.Valor');
	$SelectMesInicioF = getSelectValor('Mes', $data['reg']['MesInicioF'], '', 'PARAMETROS.Valor');
	$SelectMesFinalizacionF = getSelectValor('Mes', $data['reg']['MesFinalizacionF'], '', 'PARAMETROS.Valor');

	$SelectNivelAcademicoNF = getSelect('NivelAcademico', $data['reg']['NivelAcademicoNF'], '', 'PARAMETROS.Valor');
	$SelectEstadoNivelAcademicoNF = getSelect('EstadoNivelAcademico', $data['reg']['EstadoNF'], '', 'PARAMETROS.Valor');
	$SelectMesInicioNF = getSelectValor('Mes', $data['reg']['MesInicioNF'], '', 'PARAMETROS.Valor');
	$SelectMesFinalizacionNF = getSelectValor('Mes', $data['reg']['MesFinalizacionNF'], '', 'PARAMETROS.Valor');

	$idiomas = getTabla('IDIOMAS', '', 'IDIOMAS.Orden,IDIOMAS.Nombre');
	$SelectIdioma = '';
	
	for ($i=0; $i < count($idiomas); $i++) 
	{ 
		if	($idiomas[$i]['id'] == $data['reg']['IdIdioma'])
			$SelectIdioma .= '<option selected value=' . $idiomas[$i]['id'] . '>' . trim($idiomas[$i]['nombre']) . '</option>';
		else
			$SelectIdioma .= '<option value=' . $idiomas[$i]['id'] . '>' . trim($idiomas[$i]['nombre']) . '</option>';
	}

	$SelectNivelIdioma = getSelect('NivelDominio', $data['reg']['NivelIdioma'], '', 'PARAMETROS.Valor');

	$SelectNivelConocimiento = getSelect('NivelDominio', $data['reg']['NivelConocimiento'], '', 'PARAMETROS.Valor');

	$SelectParentesco = getSelect('Parentesco', $data['reg']['ParentescoContacto'], '', 'PARAMETROS.Valor');

	$cargos = getTabla('CARGOS', '', 'CARGOS.Nombre');

	$SelectCargo = '';
	
	for ($i = 0; $i < count($cargos); $i++) 
	{ 
		if	($cargos[$i]['id'] == $data['reg']['IdCargo'])
			$SelectCargo .= '<option selected value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';
		else
			$SelectCargo .= '<option value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';
	}

	$centros = getTabla('CENTROS', '', 'CENTROS.Nombre');

	$SelectCentro = '';
	$SelectProyecto = '';

	for ($i = 0; $i < count($centros); $i++) 
	{ 
		if (left($centros[$i]['centro'], 1) <> 'S')
		{
			if	($centros[$i]['id'] == $data['reg']['IdCentro'])
				$SelectCentro .= '<option selected value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
			else
				$SelectCentro .= '<option value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
		}

		if (left($centros[$i]['centro'], 1) == 'S')
		{
			if	($centros[$i]['id'] == $data['reg']['IdProyecto'])
				$SelectProyecto .= '<option selected value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
			else
				$SelectProyecto .= '<option value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
		}
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

	$SelectTipoContrato = getSelect('TipoContrato', $data['reg']['TipoContrato'], '', 'PARAMETROS.Valor');
	$SelectModalidadTrabajo = getSelect('ModalidadTrabajo', $data['reg']['ModalidadTrabajo'], '', 'PARAMETROS.Valor');
?>
<?php if	($_SESSION['Login']['Perfil'] <> EMPLEADO): ?>
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
						<div class="card-content">
							<?php if ($data['reg']): ?>
							<section class="tabs-vertical mt-1 section">
								<?php 
									$regEmpleado = getRegistro('EMPLEADOS', $data['reg']['Id']);
									$dir = '/documents/' . trim($regEmpleado['documento']) . '_' . strtoupper(trim($regEmpleado['apellido1']) . '_' . trim($regEmpleado['apellido2']) . '_' . trim($regEmpleado['nombre1']) . '_' . trim($regEmpleado['nombre2']));
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
											<!-- <div class="media-body">			
												<div class="general-action-btn">
													<button id="select-files" class="btn indigo mr-2">
														<span>Cargar nueva fotografía</span>
													</button>
												</div>
												<p><small>Formatos válidos JPG o PNG.</small></p>
												<p><small>Tamaño max. 800kB</small></p>
												<div class="upfilewrapper">
													<input id="upfile" type="file" />
												</div>
											</div> -->
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
														<a href="#pagDocumentos">
															<i class="material-icons">folder_open</i>
															<span>Documentos</span>
														</a>
													</li>
													<li class="tab">
														<a href="#pagGruposPoblacionales">
															<i class="material-icons">folder_open</i>
															<span>Grupos poblacionales</span>
														</a>
													</li>
													<li class="tab">
														<a href="#pagDocumentosSeleccion" id="pagDocumentosSeleccionLink">
															<i class="material-icons">folder_open</i>
															<span>Documentos proceso selección</span>
														</a>
													</li>
													<li class="tab">
														<a href="#pagCondicionesLaborales" id="pagCondicionesLaboralesLink">
															<i class="material-icons">folder_open</i>
															<span>Condiciones laborales</span>
														</a>
													</li>
													<li class="tab">
														<a href="#pagFinalizar">
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
														<p>IDENTIFICACIÓN</p>
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
														<?php
															get(label('Lugar de expedición*'), 'IdCiudadExpedicion', $SelectCiudadExpedicion, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
														?>
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
													<div class="input-field col s12 m6">
														<?php
															get(label('Políticamente expuesta*'), 'PoliticamenteExpuesta', $data['reg']['PoliticamenteExpuesta'], 'checkbox', $data['reg']['PoliticamenteExpuesta'], FALSE, '', '');
														?>
													</div>
												</div>
												<!-- <div class="row">
													<div class="input-field col s12 m6">
														<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="GUARDAR_01">
														GUARDAR DATOS
														</button>									
													</div>
													<div class="input-field col s12 m6">
														<button class="btn btn-sm red darken-4" type="submit" name="Action" value="DESISTIR">
															DESISTIR AL CARGO
														</button>									
													</div>
												</div> -->
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
																<h5>SI HA LABORADO PREVIAMENTE ADJUNTE LOS SIGUIENTES DOCUMENTOS</h5>
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
														<div>
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
														<div class="row">
															<div class="input-field col s12">
																<button class="btn btn-sm blue darken-4 col s12" style="margin-top: 10px;" type="submit" name="Action" value="GUARDAR_12">
																	CARGAR DOCS.
																	<i class="medium material-icons left">save</i>
																</button>									
															</div>
															<div class="input-field col s12">
															</div>
														</div>
													</div>
													<div class="col s6">
														<div class="card-panel">
															<div class="card-alert card cyan darken-4">
																<div class="card-content white-text">
																	<p>AQUI PUEDE VALIDAR LOS DOCUMENTOS CARGADOS</p>
																</div>
															</div>
															<div class="row">
																<ul class="collapsible">
																	<li class="active">
																		<div class="collapsible-header">
																			<i class="material-icons">folder_open</i>
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
																								<i class='fas fa-eye'></i>
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
																								<i class='fas fa-eye'></i>
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
																								<i class='fas fa-eye'></i>
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
																								<i class='fas fa-eye'></i>
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
														</div>
														<div class="row">
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
														</div>
														<div class="row">
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
														</div>
														<div class="row">
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
														</div>
														<div class="row">
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
														</div>
														<div class="row">
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
														</div>
														<div class="row">
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
														</div>
														<div class="row">
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
														</div>
														<div class="row">
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
														</div>
														<div class="row">
															<div class="input-field col s12">
																<button class="btn btn-sm blue darken-4 col s12" style="margin-top: 10px;" type="submit" name="Action" value="GUARDAR_13">
																	CARGAR DOCS.
																	<i class="medium material-icons left">save</i>
																</button>									
															</div>
															<div class="input-field col s12">
															</div>
														</div>
													</div>
													<div class="col s6">
														<div class="card-panel">
															<div class="card-alert card cyan darken-4">
																<div class="card-content white-text">
																	<p>AQUI PUEDE VALIDAR LOS DOCUMENTOS CARGADOS</p>
																</div>
															</div>
															<div class="row">
																<ul class="collapsible">
																	<li class="active">
																		<div class="collapsible-header">
																			<i class="material-icons">folder_open</i>
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
																								<i class='fas fa-eye'></i>
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
											</div>
										</div>
										<div id="pagDocumentosSeleccion" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>DOCUMENTOS PROCESO SELECCIÓN</p>
													</div>
												</div>
												<div class="row">
													<div class="col s12 m6">
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
														<div class="row">
															<div class="input-field col s12">
																<button class="btn btn-sm blue darken-4 col s12" style="margin-top: 10px;" type="submit" name="Action" value="GUARDAR_14">
																	CARGAR DOCS.
																	<i class="medium material-icons left">save</i>
																</button>									
															</div>
															<div class="input-field col s12">
															</div>
														</div>
													</div>
													<div class="col s12 m6">
														<div class="card-panel">
															<div class="card-alert card cyan darken-4">
																<div class="card-content white-text">
																	<p>AQUI PUEDE VALIDAR LOS DOCUMENTOS CARGADOS</p>
																</div>
															</div>
															<div class="row">
																<ul class="collapsible">
																	<li class="active">
																		<div class="collapsible-header">
																			<i class="material-icons">folder_open</i>
																			ANTECEDENTES
																		</div>
																		<div class="collapsible-body">
																			<table class="responsive-table">
																				<tbody>
																					<?php
																						$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/ANTECEDENTES';

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
																								<i class='fas fa-eye'></i>
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
																						$cDirectorio = 'documents/' . $data['reg']['Documento'] . '_' . strtoupper($data['reg']['Apellido1']) . '_' . strtoupper($data['reg']['Apellido2']) . '_' . strtoupper($data['reg']['Nombre1']) . '_' . strtoupper($data['reg']['Nombre2']) . '/PRUEBAS_SICOTECNICAS';

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
																								<i class='fas fa-eye'></i>
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
													<div class="input-field col s12 m6">
														<?php 
															get(label('Cargo*'), 'IdCargo', $SelectCargo, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
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
															get(label('Sueldo básico*'), 'SueldoBasico', $data['reg']['SueldoBasico'], 'number', 12, FALSE, '', 'fas fa-edit'); 
														?>
													</div>
													<div class="input-field col s6 m6">
														<?php 
															get(label('Observaciones'), 'Observaciones', $data['reg']['Observaciones'], 'textarea', 2, FALSE, '', 'fas fa-edit'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="GUARDAR_15">
															GUARDAR DATOS
														</button>									
													</div>
												</div>
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
														<h4>Por favor valide toda la información registrada antes de
															proceder con la actualización final.</h4>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m4">
														<?php if ($data['reg']['SEL_DocumentosActualizados'] == 1): ?>
														<button class="btn btn-sm blue darken-4 col s12" type="submit" name="Action" value="DOCUMENTACION_16">
															Documentación actualizada
															<i class="material-icons left">check_box</i>
														</button>	
														<?php else: ?>
														<button class="btn btn-sm blue darken-4 col s12" type="submit" name="Action" value="DOCUMENTACION_16">
															Documentación actualizada
															<i class="material-icons left">check_box_outline_blank</i>
														</button>	
														<?php endif; ?>
													</div>
													<div class="input-field col s12 m4">
														<button class="btn btn-sm red darken-4 col s12" type="submit" name="Action" value="NOCALIFICADO_16">
															Candidato no calificado
															<i class="material-icons left">send</i>
														</button>									
													</div>
													<div class="input-field col s12 m4">
													</div>
												</div>
												<br>
											</div>
										</div>
									</div>
								</div>
							</section>
							<?php endif; ?>
						</div>
					</div>
					<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
						<?php if ( $data['mensajeError'] ): ?>
						<div class="row" id="mensajeError">
							<script>window.location.href = "#mensajeError";</script>
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

<?php require_once('views/templates/footer.php'); ?>

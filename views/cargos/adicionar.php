<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$cargos = getTabla('CARGOS', '', 'CARGOS.Nombre');

	$SelectCargoSuperior = '';
	$SelectCargoBase = '';
	
	for ($i = 0; $i < count($cargos); $i++) 
	{ 
		if	($data['reg']['IdCargoSuperior'] == $cargos[$i]['id'])
			$SelectCargoSuperior .= '<option selected value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';
		else
			$SelectCargoSuperior .= '<option value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';

		if	($data['reg']['IdCargoBase'] == $cargos[$i]['id'])
			$SelectCargoBase .= '<option selected value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';
		else
			$SelectCargoBase .= '<option value=' . $cargos[$i]['id'] . '>' . trim($cargos[$i]['nombre']) . '</option>';
	}

	if ($data['reg']['IdPerfil'] > 0)
	{
		$regPerfil = getRegistro('PERFILES', $data['reg']['IdPerfil']);

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

		$SelectNivelAcademico = getSelect('NivelAcademico', 0, '', 'PARAMETROS.Valor');

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
									<h3 class="white-text">Cargos de empleados</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<section class="tabs-vertical mt-1 section">
								<div class="row">
									<div class="col l3 s12">
										<div class="card-panel">
											<ul class="tabs">
												<li class="tab">
													<a href="#pagCargo">
														<i class="material-icons">error_outline</i>
														<span>Cargo</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagPerfil">
														<i class="material-icons">error_outline</i>
														<span>Perfil</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagEstructura">
														<i class="material-icons">list</i>
														<span>Estructura</span>
													</a>
												</li>
											</ul>
										</div>
									</div>

									<div class="col s12 l9">
										<div id="pagCargo" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>CARGO</p>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Nombre*'), 'Nombre', $data['reg']['Nombre'], 'text', 40, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Sueldo mínimo'), 'SueldoMinimo', $data['reg']['SueldoMinimo'], 'number', 12, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Sueldo máximo'), 'SueldoMaximo', $data['reg']['SueldoMaximo'], 'number', 12, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Cargo superior'), 'IdCargoSuperior', $SelectCargoSuperior, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Perfil cargo base'), 'IdCargoBase', $SelectCargoBase, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Porcentaje riesgo (ARL)*'), 'PorcentajeARL', $data['reg']['PorcentajeARL'], 'number', 10, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
												</div>
											</div>
										</div>

                                        <div id="pagPerfil" class="col s12">
                                            <div class="card-panel">
                                                <div class="card-alert card cyan darken-4">
                                                    <div class="card-content white-text">
                                                        <p>PERFIL</p>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="input-field col s12 m6">
                                                        <?php 
															get(label('Dependencia*'), 'IdDependencia', $SelectDependencia, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="input-field col s12 m6">
                                                        <?php 
															get(label('Nivel académico*'), 'NivelAcademico', $SelectNivelAcademico, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                    <div class="input-field col s12 m6">
                                                        <?php 
															get(label('Estudios*'), 'Estudios', $Estudios, 'textarea', 3, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="input-field col s12 m6">
                                                        <?php 
															get(label('Experiencia laboral*'), 'ExperienciaLaboral', $ExperienciaLaboral, 'textarea', 3, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                    <div class="input-field col s12 m6">
                                                        <?php 
															get(label('Formación adicional'), 'FormacionAdicional', $FormacionAdicional, 'textarea', 3, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="input-field col s12 m6">
                                                        <?php 
															get(label('Condiciones de trabajo*'), 'CondicionesTrabajo', $CondicionesTrabajo, 'textarea', 3, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                    <div class="input-field col s12 m6">
                                                        <?php 
															get(label('Misión del cargo*'), 'MisionCargo', $MisionCargo, 'textarea', 3, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="input-field col s12 m12">
                                                        <?php 
															get(label('Funciones y responsabilidades*'), 'Funciones', $Funciones, 'textarea', 3, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="input-field col s12 m6">
                                                        <?php 
															get(label('Responsable*'), 'Responsable', $Responsable, 'text', 100, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
                                                    </div>
                                                    <div class="input-field col s12 m6">
                                                        <?php 
															get(label('Elaboró*'), 'Elabora', $Elabora, 'text', 100, FALSE, '', 'fas fa-ellipsis-v'); 
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
                                                                <div class="col s12 m4">
                                                                    <?= $archivo ?>
                                                                </div>
                                                                <div class="col s12 m1">
                                                                    <a href="<?= SERVERURL . '/' . $cDirectorio . $archivo . '.pdf'; ?>"
                                                                        target="_blank"
                                                                        class="btn btn-sm teal lighten-3 tooltipped"
                                                                        data-position="bottom"
                                                                        data-tooltip="Ver documento">
                                                                        <i class="material-icons">visibility</i>
                                                                    </a>
                                                                </div>
                                                                <div class="col s12 m1">
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
                                                                <div class="col s12 m4">
                                                                    <?= $archivo ?>
                                                                </div>
                                                                <div class="col s12 m1">
                                                                    <a href="<?= SERVERURL . '/' . $cDirectorio . $archivo . '.pdf'; ?>"
                                                                        target="_blank"
                                                                        class="btn btn-sm teal lighten-3 tooltipped"
                                                                        data-position="bottom"
                                                                        data-tooltip="Ver documento">
                                                                        <i class="material-icons">visibility</i>
                                                                    </a>
                                                                </div>
                                                                <div class="col s12 m1">
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
																		get('', 'FuncionesHSEQ', $FuncionesHSEQ, 'textarea', 3, FALSE, '', 'fas fa-ellipsis-v'); 
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
																		get('', 'GestionHS', $GestionHS, 'textarea', 3, FALSE, '', 'fas fa-ellipsis-v'); 
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
																		get('', 'GestionAmbiental', $GestionAmbiental, 'textarea', 3, FALSE, '', 'fas fa-ellipsis-v'); 
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
																		get('', 'GestionCalidad', $GestionCalidad, 'textarea', 3, FALSE, '', 'fas fa-ellipsis-v'); 
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
																		get('', 'GestionSI', $GestionSI, 'textarea', 3, FALSE, '', 'fas fa-ellipsis-v'); 
																	?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>

                                            </div>
                                        </div>

										<div id="pagEstructura" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>ESTRUCTURA</p>
													</div>
												</div>

												<div class="row">
												</div>
											</div>
										</div>
									</div>
								</div>
							</section>

						</div>
						<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
							<?php if ( $data['mensajeError'] ): ?>
							<div class="row">
								<div class="col s12">
									<h6 class="orange-text">
										<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por
										favor valídelas:
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
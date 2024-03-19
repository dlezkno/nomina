<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$regEmpleado = getRegistro('EMPLEADOS', $data['reg']['IdEmpleado']);

	$EstadoEmpleado = getRegistro('PARAMETROS', $regEmpleado['estado'])['detalle'];
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
								<div class="col s12 m6">
									<h3 class="white-text">CANDIDATO (ENTREVISTA TÉCNICA)</h3>
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
							<?php endif; ?>
						</div>
						<div class="card-content">
							<?php if ($data['reg']): ?>
							<section class="tabs-vertical mt-1 section">
								<?php 
									$dir = '/documents/' . $regEmpleado['documento'] . '_' . strtoupper($regEmpleado['apellido1']) . '_' . strtoupper($regEmpleado['apellido2']) . '_' . strtoupper($regEmpleado['nombre1']) . '_' . strtoupper($regEmpleado['nombre2']);
									$cDirectorio = SERVERURL . $dir;
									$archivo = getImage($dir,$cDirectorio);
								?>
								<div class="row">
									<div class="col s12 m3">
										<div class="card-panel">
											<div class="media center">
												<img src="<?= $archivo ?>" alt="Fotografia" class="border-radius-4" height="250px" width="200px">												
												<h5 class="text center"><?= $regEmpleado['nombre1'] . ' ' . $regEmpleado['nombre2'] . ' ' .$regEmpleado['apellido1'] ?></h5>
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
									</div>
									<div class="col s12 m9">
										<div class="row">
											<div class="col">
												<div class="input-field col s12 m6">
													<button class="btn btn-floating btn-md cyan darken-4" type="submit" name="Action" value="PASO_1">
													1
													</button>									
												</div>
											</div>
											<div class="col">
												<div class="input-field col s12 m6">
													<button class="btn btn-floating btn-md cyan darken-4" type="submit" name="Action" value="PASO_2">
													2
													</button>									
												</div>
											</div>
											<div class="col">
												<div class="input-field col s12 m6">
													<button class="btn btn-floating btn-md cyan darken-4" type="submit" name="Action" value="PASO_3">
													3
													</button>									
												</div>
											</div>
											<div class="col">
												<div class="input-field col s12 m6">
													<button class="btn btn-floating btn-md cyan darken-4" type="submit" name="Action" value="PASO_4">
													4
													</button>									
												</div>
											</div>
											<div class="col">
												<div class="input-field col s12 m6">
													<button class="btn btn-floating btn-md cyan darken-4" type="submit" name="Action" value="PASO_5">
													5
													</button>									
												</div>
											</div>
											<div class="col">
												<div class="input-field col s12 m6">
													<button class="btn btn-floating btn-md cyan darken-4" type="submit" name="Action" value="PASO_6">
													6
													</button>									
												</div>
											</div>
										</div>

										<?php 
											switch ($_SESSION['Paso2']):
												case 1: 
										?>
												<div id="pagHojaVida" class="col s12">
													<div class="card-panel">
														<div class="card-alert card cyan darken-4">
															<div class="card-content white-text">
																<p>HOJA DE VIDA (PASO 1)</p>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<?php
																	get(label('Coherencia Hoja de Vida'), 'VeracidadHV', $data['reg']['VeracidadHV'], 'textarea', 5, FALSE, '', 'fas fa-ellipsis-v');
																?>
															</div>
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Puntaje (1-20)'), 'PuntajeVeracidad', $data['reg']['PuntajeVeracidad'], 'number', 2, FALSE, '', 'fas fa-pen');
																?>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="AVANZAR">
																GUARDAR DATOS Y CONTINUAR
																</button>									
															</div>
														</div>
													</div>
												</div>
											<?php
												break;
												case 2:
											?>
												<div id="pagExperiencia" class="col s12">
													<div class="card-panel">
														<div class="card-alert card cyan darken-4">
															<div class="card-content white-text">
																<p>EXPERIENCIA LABORAL (PASO 2)</p>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s6 m6">
																<?php 
																	get(label('Experiencia laboral'), 'ExperienciaLaboral', $data['reg']['ExperienciaLaboral'], 'textarea', 5, FALSE, '', 'fas fa-ellipsis-v');
																?>
															</div>
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Puntaje (1-30)'), 'PuntajeExperiencia', $data['reg']['PuntajeExperiencia'], 'number', 2, FALSE, '', 'fas fa-pen');
																?>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="AVANZAR">
																	GUARDAR DATOS Y AVANZAR
																</button>									
															</div>
															<div class="input-field col s12 m6">
																<button class="btn btn-sm red darken-4" type="submit" name="Action" value="RETROCEDER">
																	VOLVER ATRÁS
																</button>									
															</div>
														</div>
													</div>
												</div>
											<?php
												break;
												case 3:
											?>
												<div id="pagConocimientos" class="col s12">
													<div class="card-panel">
														<div class="card-alert card cyan darken-4">
															<div class="card-content white-text">
																<p>CONOCIMIENTOS (PASO 3)</p>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Conocimiento técnico'), 'ConocimientoTecnico', $data['reg']['ConocimientoTecnico'], 'textarea', 5, FALSE, '', 'fas fa-ellipsis-v');
																?>
															</div>
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Puntaje (1-30)'), 'PuntajeConocimiento', $data['reg']['PuntajeConocimiento'], 'number', 2, FALSE, '', 'fas fa-pen');
																?>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="AVANZAR">
																	GUARDAR DATOS Y AVANZAR
																</button>									
															</div>
															<div class="input-field col s12 m6">
																<button class="btn btn-sm red darken-4" type="submit" name="Action" value="RETROCEDER">
																	VOLVER ATRÁS
																</button>									
															</div>
														</div>
													</div>
												</div>
											<?php
												break;
												case 4:
											?>
												<div id="pagCompetencias" class="col s12">
													<div class="card-panel">
														<div class="card-alert card cyan darken-4">
															<div class="card-content white-text">
																<p>COMPETENCIAS (PASO 4)</p>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Competencias'), 'Competencias', $data['reg']['Competencias'], 'textarea', 5, FALSE, '', 'fas fa-ellipsis-v');
																?>
															</div>
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Puntaje (1-20)'), 'PuntajeCompetencias', $data['reg']['PuntajeCompetencias'], 'number', 2, FALSE, '', 'fas fa-pen');
																?>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="AVANZAR">
																	GUARDAR DATOS Y AVANZAR
																</button>									
															</div>
															<div class="input-field col s12 m6">
																<button class="btn btn-sm red darken-4" type="submit" name="Action" value="RETROCEDER">
																	VOLVER ATRÁS
																</button>									
															</div>
														</div>
													</div>
												</div>
											<?php
												break;
												case 5:
											?>
												<div id="pagRecomendacion" class="col s12">
													<div class="card-panel">
														<div class="card-alert card cyan darken-4">
															<div class="card-content white-text">
																<p>RECOMENDACIONES FINALES (PASO 5)</p>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Recomendado para el cargo'), 'Recomendado', $data['reg']['Recomendado'], 'checkbox', $data['reg']['Recomendado'], FALSE, '', '');
																?>
															</div>
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Argumentos'), 'Argumentos', $data['reg']['Argumentos'], 'textarea', 5, FALSE, '', 'fas fa-ellipsis-v');
																?>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="AVANZAR">
																	GUARDAR DATOS Y AVANZAR
																</button>									
															</div>
															<div class="input-field col s12 m6">
																<button class="btn btn-sm red darken-4" type="submit" name="Action" value="RETROCEDER">
																	VOLVER ATRÁS
																</button>									
															</div>
														</div>
													</div>
												</div>
											<?php
												break;
												case 6:
											?>
												<div id="pagFinalizar" class="col s12">
													<div class="card-panel">
														<div class="card-alert card cyan darken-4">
															<div class="card-content white-text">
																<p>FINALIZAR</p>
															</div>
														</div>

														<div class="row">
															<div class="col s12">
																<h4>Por favor valide toda la información registrada antes de proceder con la actualización final.</h4>
																<br>
															</div>
														</div>

														<div class="row">
															<div class="input-field col s12 m4">
																<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="FINALIZAR">
																	FINALIZAR ENTREVISTA
																</button>									
															</div>
														</div>
													</div>
												</div>
											<?php
												break;
											?>
										<?php
											endswitch;
										?>
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

<?php require_once('views/templates/footer.php'); ?>

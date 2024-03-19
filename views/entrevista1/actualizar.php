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
									<h3 class="white-text">CANDIDATO (ENTREVISTA PSICOLÓGICA)</h3>
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
										</div>

										<?php 
											switch ($_SESSION['Paso1']):
												case 1: 
										?>
												<div id="pagInfPersonal" class="col s12">
													<div class="card-panel">
														<div class="card-alert card cyan darken-4">
															<div class="card-content white-text">
																<p>INFORMACIÓN PERSONAL (PASO 1)</p>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<?php
																	get(label('Fortalezas / Aspectos a mejorar'), 'Fortalezas', $data['reg']['Fortalezas'], 'textarea', 5, FALSE, '', 'fas fa-ellipsis-v');
																?>
															</div>
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Proyección / Metas'), 'Proyeccion', $data['reg']['Proyeccion'], 'textarea', 5, FALSE, '', 'fas fa-ellipsis-v');
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
												<div id="pagInfFamiliar" class="col s12">
													<div class="card-panel">
														<div class="card-alert card cyan darken-4">
															<div class="card-content white-text">
																<p>INFORMACIÓN FAMILIAR (PASO 2)</p>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s6 m6">
																<?php 
																	get(label('Dinámica familiar'), 'DinamicaFamiliar', $data['reg']['DinamicaFamiliar'], 'textarea', 5, FALSE, '', 'fas fa-ellipsis-v');
																?>
															</div>
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Valores inculcados'), 'ValoresInculcados', $data['reg']['ValoresInculcados'], 'textarea', 5, FALSE, '', 'fas fa-ellipsis-v');
																?>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="AVANZAR">
																	AVANZAR
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
												<div id="pagInfAcademica" class="col s12">
													<div class="card-panel">
														<div class="card-alert card cyan darken-4">
															<div class="card-content white-text">
																<p>INFORMACIÓN ACADÉMICA Y LABORAL (PASO 3)</p>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Principales logros'), 'LogrosAcademicos', $data['reg']['LogrosAcademicos'], 'textarea', 5, FALSE, '', 'fas fa-ellipsis-v');
																?>
															</div>
															<div class="input-field col s12 m6">
																<?php 
																	get(label('Motivación y expectativas hacia la Empresa y el cargo'), 'MotivacionLaboral', $data['reg']['MotivacionLaboral'], 'textarea', 5, FALSE, '', 'fas fa-ellipsis-v');
																?>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m4">
																<?php
																	get(label('Disponibilidad Tiempo completo'), 'DisponibilidadTC', $data['reg']['DisponibilidadTC'], 'checkbox', $data['reg']['DisponibilidadTC'], FALSE, '', '');
																?>
															</div>
															<div class="input-field col s12 m4">
																<?php
																	get(label('Disponibilidad Fin de semana'), 'DisponibilidadFS', $data['reg']['DisponibilidadFS'], 'checkbox', $data['reg']['DisponibilidadFS'], FALSE, '', '');
																?>
															</div>
															<div class="input-field col s12 m4">
																<?php
																	get(label('Disponibilidad Turnos rotativos'), 'DisponibilidadTR', $data['reg']['DisponibilidadTR'], 'checkbox', $data['reg']['DisponibilidadTR'], FALSE, '', '');
																?>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m6">
																<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="AVANZAR">
																	AVANZAR
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
												<div id="pagReferencias" class="col s12">
													<div class="card-panel">
														<div class="card-alert card cyan darken-4">
															<div class="card-content white-text">
																<p>REFERENCIAS LABORALES (PASO 4)</p>
															</div>
														</div>

														<div class="row">
															<div class="col s12 m6">
																<?php 
																	get(label('Empresa'), 'Empresa', $data['reg']['Empresa'], 'text', 100, FALSE, '', 'fas fa-pen'); 
																?>
															</div>
														</div>
														<div class="row">
															<div class="col s12 m6">
																<?php
																	get(label('Nombre referente'), 'NombreReferente', $data['reg']['NombreReferente'], 'text', 100, FALSE, '', 'fas fa-pen');
																?>
															</div>
															<div class="col s12 m6">
																<?php
																	get(label('Cargo del referente'), 'CargoReferente', $data['reg']['CargoReferente'], 'text', 100, FALSE, '', 'fas fa-pen');
																?>
															</div>
														</div>
														<div class="row">
															<div class="col s12 m4">
																<?php
																	get(label('Teléfono'), 'Telefono', $data['reg']['Telefono'], 'tel', 15, FALSE, '', 'fas fa-pen');
																?>
															</div>
														</div>
														<div class="row">
															<div class="col s12 m4">
																<?php 
																	get(label('Fecha de ingreso'), 'FechaIngreso', $data['reg']['FechaIngreso'], 'date', 10, FALSE, '', 'far fa-calendar'); 
																?>
															</div>
															<div class="col s12 m4">
																<?php 
																	get(label('Fecha de retiro'), 'FechaRetiro', $data['reg']['FechaRetiro'], 'date', 10, FALSE, '', 'far fa-calendar'); 
																?>
															</div>
															<div class="col s12 m4">
																<?php
																	get(label('Cargo del empleado'), 'CargoEmpleado', $data['reg']['CargoEmpleado'], 'text', 100, FALSE, '', 'fas fa-pen');
																?>
															</div>
														</div>
														<div class="row">
															<div class="col s12 m6">
																<?php 
																	get(label('Motivo del retiro'), 'MotivoRetiro', $data['reg']['MotivoRetiro'], 'textarea', 5, FALSE, '', 'far fa-edit'); 
																?>
															</div>
															<div class="col s12 m6">
																<?php 
																	get(label('Observaciones'), 'Observaciones', $data['reg']['Observaciones'], 'textarea', 5, FALSE, '', 'far fa-edit'); 
																?>
															</div>
														</div>
														<div class="row">
															<div class="input-field col s12 m4">
																<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="AVANZAR">
																	AVANZAR
																</button>									
															</div>
															<div class="input-field col s12 m4">
																<button class="btn btn-sm blue darken-3" type="submit" name="Action" value="GUARDAR">
																	ACTUALIZAR REFERENCIA LABORAL
																</button>									
															</div>
															<div class="input-field col s12 m4">
																<button class="btn btn-sm red darken-4" type="submit" name="Action" value="RETROCEDER">
																	VOLVER ATRÁS
																</button>									
															</div>
														</div>
													</div>
													<div class="card-panel">
														<table>
															<thead>
																<tr>
																	<th style="text-align:center;"></th>
																	<th>EMPRESA / REFERENTE</th>
																	<th>TELÉFONO</th>
																	<th>FECHA INGRESO</th>
																	<th>FECHA RETIRO</th>
																	<th>OBSERVACIONES</th>
																</tr>
															</thead>
															<tbody>
																<?php if ($data['referencias']): ?>
																<?php for ($i = 0; $i < count($data['referencias']); $i++ ): ?>
																<tr>
																	<td class="center-align">
																		<button class="btn btn-sm red darken-4" type="submit" name="Action" value="BORRAR_<?= $data['referencias'][$i]['Id'] ?>">
																			<i class="small material-icons">delete</i>
																		</button>									
																	</td>
																	<td><?= $data['referencias'][$i]['Empresa'] . '<br>' . $data['referencias'][$i]['NombreReferente'] . '<br>' . $data['referencias'][$i]['CargoReferente'] ?></td>
																	<td><?= $data['referencias'][$i]['Telefono'] ?></td>
																	<td><?= $data['referencias'][$i]['FechaIngreso'] ?></td>
																	<td><?= $data['referencias'][$i]['FechaRetiro'] ?></td>
																	<td><?= 'CARGO: ' . $data['referencias'][$i]['CargoEmpleado'] . '<br>' . $data['referencias'][$i]['Observaciones'] . '<br>' . 'VALIDÓ: ' . $data['referencias'][$i]['Sicologo'] ?></strong></td>
																</tr>
																<?php endfor; ?>
																<?php endif; ?>
															</tbody>
														</table>
													</div>
												</div>
											<?php
												break;
												case 5:
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

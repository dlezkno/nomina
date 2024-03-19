<?php 
	$lcFiltro = $_SESSION['DOCUMENTOS']['Filtro'];
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');
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
									<h3 class="white-text">Documentos de empleados</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/documentos/lista', $data['registros'], $_SESSION['DOCUMENTOS']['Pagina'] );
									?>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12">
									<table>
										<thead>
											<tr>
												<th>
													<?php 
														if	( $_SESSION['DOCUMENTOS']['Orden'] == 'EMPLEADOS.Documento' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('DOCUMENTO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Documento">';
																echo label('Documento');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['DOCUMENTOS']['Orden'] == 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('NOMBRE EMPLEADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2">';
																echo label('Nombre Empleado');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['DOCUMENTOS']['Orden'] == 'CARGOS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CARGO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="CARGOS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Cargo');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['DOCUMENTOS']['Orden'] == 'CENTROS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CENTRO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="CENTROS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Centro');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['DOCUMENTOS']['Orden'] == 'PROYECTOS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('PROYECTO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="PROYECTOS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Proyecto');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['DOCUMENTOS']['Orden'] == 'EMPLEADOS.FechaIngreso,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('FECHA ING.') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.FechaIngreso,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Fecha Ing.');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['DOCUMENTOS']['Orden'] == 'PARAMETROS1.Detalle DESC,EMPLEADOS.FechaIngreso,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('ESTADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="PARAMETROS1.Detalle DESC,EMPLEADOS.FechaIngreso,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2">';
																echo label('Estado');
															echo '</button>';
														}
													?>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i=0; $i < count($data['rows']); $i++)
												{
													$reg = $data['rows'][$i];

													if ($reg['EstadoEmpleado'] == 'EN PROCESO DE SELECCION' AND $reg['sel_documentosactualizados'] == 0)
														continue;
											?>
											<tr>
												<td><?= $reg['documento'] ?></td>
												<td><?= $reg['apellido1'] . ' ' . $reg['apellido2'] . ' ' . $reg['nombre1'] . ' ' . $reg['nombre2'] ?></td>
												<td><?= $reg['NombreCargo'] ?></td>
												<td><?= $reg['NombreCentro'] ?></td>
												<td><?= $reg['NombreProyecto'] ?></td>
												<td><?= $reg['fechaingreso'] ?></td>
												<?php if ($reg['EstadoEmpleado'] == 'ACTIVO'): ?>
												<td>
													<button class="btn btn-sm green darken-4" type="submit" name="Action" value=<?= $reg['id'] ?>>
														ACTIVO
													</button>									
												</td>
												<?php elseif ($reg['EstadoEmpleado'] == 'EN PROCESO DE SELECCION'): ?>
												<td>
													<button class="btn btn-sm red darken-4" type="submit" name="Action" value=<?= $reg['id'] ?>>
														SELECC.
													</button>									
												</td>
												<?php elseif ($reg['EstadoEmpleado'] == 'EN PROCESO DE CONTRATACION'): ?>
												<td>
													<button class="btn btn-sm red darken-4" type="submit" name="Action" value=<?= $reg['id'] ?>>
														CONTR.
													</button>									
												</td>
												<?php endif; ?>
											</tr>
											<?php
												}
											?>
										</tbody>
									</table>
								</div>
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
			<div class="content-overlay"></div>
		</div>
	</div>
</div>

<?php if ( isset($data['url_redirect']) ): ?>
	<script>
		window.open("<?php echo $data['url_redirect']?>","_blank")
	</script>
<?php endif; ?>
<?php require_once('views/templates/footer.php'); ?>

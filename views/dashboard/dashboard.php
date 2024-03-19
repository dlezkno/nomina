<?php 
	$lcFiltro = '';
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
									<h3 class="white-text">Dashboard</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div id="card-stats" class="pt-0">
								<div class="row">
									<div class="col s12 m12 l12">
										<div class="card animate fadeLeft">
											<div class="card-content green lighten-1 white-text">
												<p class="card-stats-title"><i class="material-icons">cake</i>
													Cumpleaños de la semana</p>
											</div>
											<div class="card-action">
												<ul class="collapsible">
													<li>
														<div class="collapsible-header">
															<i class="material-icons">remove_red_eye</i>
														</div>
														<div class="collapsible-body">
															<table class="table responsive-table">
																<thead>
																	<tr>
																		<th>DÍA</th>
																		<th>EMPLEADO</th>
																		<th>CARGO</th>
																		<th>CENTRO DE COSTOS</th>
																	</tr>
																</thead>
																<tbody>
																	<?php for ($i = 0; $i < isset($data['CumpleanosEmpleados']) ? count($data['CumpleanosEmpleados']) : 0; $i++): ?>
																	<tr>
																		<td><?= date('d', strtotime($data['CumpleanosEmpleados'][$i]['FechaNacimiento'])) ?>
																		</td>
																		<td><?= $data['CumpleanosEmpleados'][$i]['Empleado'] ?>
																		</td>
																		<td><?= $data['CumpleanosEmpleados'][$i]['Cargo'] ?>
																		</td>
																		<td><?= $data['CumpleanosEmpleados'][$i]['Centro'] ?>
																		</td>
																	</tr>
																	<?php endfor; ?>
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
									<div class="col s12 m4 l3">
										<div class="card animate fadeLeft">
											<div class="card-content cyan white-text">
												<p class="card-stats-title"><i class="material-icons">person_outline</i>
													Ingresos en el mes</p>
												<h4 class="card-stats-number white-text"><?= $data['Ingresos'] ?></h4>
												<p class="card-stats-compare">
													<?php if ($data['VariacionIngresos'] > 0): ?>
													<i class="material-icons">trending_up</i>
													<?= $data['VariacionIngresos'] ?>%
													<?php else: ?>
													<i class="material-icons">trending_flat</i>
													<?= $data['VariacionIngresos'] ?>%
													<?php endif; ?>
													<span class="cyan text text-lighten-5">desde el último mes</span>
												</p>
											</div>
											<div class="card-action">
												<ul class="collapsible">
													<li>
														<div class="collapsible-header">
															<i class="material-icons">remove_red_eye</i>
														</div>
														<div class="collapsible-body">
															<table class="table responsive-table">
																<thead>
																	<tr>
																		<th>Ingreso</th>
																		<th>Empleado</th>
																	</tr>
																</thead>
																<tbody>
																	<?php for ($i = 0; $i < count($data['EmpleadosNuevos']); $i++): ?>
																	<tr>
																		<td><?= substr($data['EmpleadosNuevos'][$i]['FechaIngreso'], -5) ?>
																		</td>
																		<td>
																			<?= $data['EmpleadosNuevos'][$i]['Empleado'] ?>
																			<br>
																			<?= 'CENTRO: ' . $data['EmpleadosNuevos'][$i]['Centro'] . ' - ' . $data['EmpleadosNuevos'][$i]['NombreCentro'] ?>
																			<br>
																			<?= 'CARGO: ' . $data['EmpleadosNuevos'][$i]['NombreCargo'] ?>
																		</td>
																	</tr>
																	<?php endfor; ?>
																</tbody>
																<tfoot>
																	<tr>
																		<td></td>
																		<td>TOTAL INGRESOS <?= $data['Ingresos'] ?></td>
																	</tr>
																</tfoot>
															</table>
														</div>
													</li>
												</ul>
											</div>
											<!-- <div class="card-action cyan darken-1">
												<div id="clients-bar" class="center-align">
													<canvas width="227" height="25" style="display: inline-block; width: 227px; height: 25px; vertical-align: top;"></canvas>
												</div>
											</div> -->
										</div>
									</div>
									<div class="col s12 m4 l3">
										<div class="card animate fadeLeft">
											<div class="card-content purple white-text">
												<p class="card-stats-title"><i class="material-icons">person_outline</i>
													Retiros en el mes</p>
												<h4 class="card-stats-number white-text"><?= $data['Egresos'] ?></h4>
												<p class="card-stats-compare">
													<?php if ($data['VariacionEgresos'] > 0): ?>
													<i class="material-icons">trending_down</i>
													<?= $data['VariacionEgresos'] ?>%
													<?php else: ?>
													<i class="material-icons">trending_flat</i>
													<?= $data['VariacionEgresos'] ?>%
													<?php endif; ?>
													<span class="text-lighten-5">desde el último mes</span>
												</p>
											</div>
											<div class="card-action">
												<ul class="collapsible">
													<li>
														<div class="collapsible-header">
															<i class="material-icons">remove_red_eye</i>
														</div>
														<div class="collapsible-body">
															<table class="table responsive-table">
																<thead>
																	<tr>
																		<th>Retiro</th>
																		<th>Empleado</th>
																	</tr>
																</thead>
																<tbody>
																	<?php for ($i = 0; $i < count($data['EmpleadosRetirados']); $i++): ?>
																	<tr>
																		<td><?= substr($data['EmpleadosRetirados'][$i]['FechaRetiro'], -5) ?>
																		</td>
																		<td>
																			<?= $data['EmpleadosRetirados'][$i]['Empleado'] ?>
																			<br>
																			<?= 'CENTRO: ' . $data['EmpleadosRetirados'][$i]['Centro'] . ' - ' . $data['EmpleadosRetirados'][$i]['NombreCentro'] ?>
																			<br>
																			<?= 'CARGO: ' . $data['EmpleadosRetirados'][$i]['NombreCargo'] ?>
																		</td>
																	</tr>
																	<?php endfor; ?>
																</tbody>
																<tfoot>
																	<tr>
																		<td></td>
																		<td>TOTAL RETIROS <?= $data['Egresos'] ?></td>
																	</tr>
																</tfoot>
															</table>
														</div>
													</li>
												</ul>

											</div>
											<!-- <div class="card-action red">
												<div id="clients-bar" class="center-align">
													<canvas width="227" height="25" style="display: inline-block; width: 227px; height: 25px; vertical-align: top;"></canvas>
												</div>
											</div> -->
										</div>
									</div>
									<div class="col s12 m4 l3">
										<div class="card animate fadeLeft">
											<div class="card-content blue darken-1 white-text">
												<p class="card-stats-title"><i class="material-icons">person_outline</i>
													Total empleados</p>
												<h4 class="card-stats-number white-text"><?= $data['TotalEmpleados'] ?>
												</h4>
												<p class="card-stats-compare">
													<?php if ($data['VariacionEmpleados'] > 0): ?>
													<i class="material-icons">trending_up</i>
													<?= $data['VariacionEmpleados'] ?>%
													<?php elseif ($data['VariacionEmpleados'] < 0): ?>
													<i class="material-icons">trending_down</i>
													<?= $data['VariacionEmpleados'] ?>%
													<?php else: ?>
													<i class="material-icons">trending_flat</i>
													<?= $data['VariacionEmpleados'] ?>%
													<?php endif; ?>
													<span class="text-lighten-5">desde el último mes</span>
												</p>
											</div>
											<div class="card-action">
												<ul class="collapsible">
													<li>
														<div class="collapsible-header">
															<i class="material-icons">remove_red_eye</i>
														</div>
														<div class="collapsible-body">
															<table class="table responsive-table">
																<thead>
																	<tr>
																		<th>Centro de costo</th>
																		<th class="right align">Empl.</th>
																		<th></th>
																	</tr>
																</thead>
																<tbody>
																	<?php for ($i = 0; $i < isset($data['EmpleadosPorCentro']) ? count($data['EmpleadosPorCentro']) : 0; $i++): ?>
																	<tr>
																		<td><?= $data['EmpleadosPorCentro'][$i]['Centro'] . ' - ' . $data['EmpleadosPorCentro'][$i]['NombreCentro'] ?>
																		</td>
																		<td class="center align">
																			<?= $data['EmpleadosPorCentro'][$i]['Empleados'] ?>
																		</td>
																		<td class="centr align">
																			<?= round($data['EmpleadosPorCentro'][$i]['Empleados'] / $data['TotalEmpleados'] * 100, 1) ?>%
																		</td>
																	</tr>
																	<?php endfor; ?>
																</tbody>
																<tfoot>
																	<tr>
																		<td>TOTAL EMPLEADOS</td>
																		<td class="center align">
																			<?= $data['TotalEmpleados'] ?></td>
																		<td></td>
																	</tr>
																</tfoot>
															</table>
														</div>
													</li>
												</ul>
											</div>
											<!-- <div class="card-action green">
												<div id="clients-bar" class="center-align">
													<canvas width="227" height="25" style="display: inline-block; width: 227px; height: 25px; vertical-align: top;"></canvas>
												</div>
											</div> -->
										</div>
									</div>
									



									<div class="col s12 m4 l3">
										<div class="card animate fadeLeft">
											<div class="card-content blue darken-1 white-text">
												<p class="card-stats-title"><i class="material-icons">folder_open</i>
													documentos firmados</p>
												<h4 class="card-stats-number white-text">
													<?php echo $data["signscount"] ?>
												</h4>
												<p class="card-stats-compare">
													<i class="material-icons">trending_flat</i>
													<span class="text-lighten-5">desde el último mes</span>
												</p>
											</div>
											<div class="card-action">
												<ul class="collapsible">
													<li>
														<div class="collapsible-header">
																<i class="material-icons">remove_red_eye</i>
															</div>
															<div class="collapsible-body">
															<div class="row">
																<div class="col s12 m6">
																	<?php 
																		get(label('inicial'), 'fechaInit', '', 'date', 10, FALSE, '', 'far fa-calendar'); 
																	?>
																</div>
																<div class="col s12 m6">
																	<?php 
																		get(label('final'), 'fechaEnd', '', 'date', 10, FALSE, '', 'far fa-calendar'); 
																	?>
																</div>
															</div>	
															<div class="row">
																<div class="col s12 m12" style="text-align:center">
																	<button class="btn btn-sm cyan darken-4" type="submit" name="Action" value="VERIFICAR">
																		VERIFICAR
																	</button>
																</div>
															</div>
														</div>																											
													</li>
												</ul>
											</div>											
										</div>
									</div>

								</div>
								<div class="row">
									<div class="col s12 m12 l12">
										<div class="card animate fadeLeft">
											<div class="card-content green lighten-1 white-text">
												<p class="card-stats-title"><i class="material-icons">cake</i>
													Vencimiento de contratos</p>
											</div>
											<div class="card-action">
												<ul class="collapsible">
													<li>
														<div class="collapsible-header">
															<i class="material-icons">remove_red_eye</i>
														</div>
														<div class="collapsible-body">
															<table class="table responsive-table">
																<thead>
																	<tr>
																		<th>EMPLEADO</th>
																		<th>CARGO</th>
																		<th>CENTRO DE COSTOS / PROYECTO</th>
																		<th>FECHA</th>
																	</tr>
																</thead>
																<tbody>
																	<?php 
																		for ($i = 0; $i < count($data['VencimientoContratos']); $i++):
																	?>
																	<tr>
																		<td><?= $data['VencimientoContratos'][$i]['Empleado'] ?>
																		</td>
																		<td><?= $data['VencimientoContratos'][$i]['Cargo'] ?>
																		</td>
																		<td><?= $data['VencimientoContratos'][$i]['Centro'] . '<br><strong>' . $data['VencimientoContratos'][$i]['Proyecto'] . '</strong>' ?></td>
																		<td><?= $data['VencimientoContratos'][$i]['FechaVencimiento'] ?>
																		</td>
																		<td>
																			<?php
																				$FechaVcto = $data['VencimientoContratos'][$i]['FechaVencimiento'];
																				$datetime1 = date_create(date('Y-m-d'));
																				$datetime2 = date_create($FechaVcto);
																				$contador = date_diff($datetime1, $datetime2);
																				if ($FechaVcto < date('Y-m-d'))
																					echo '<span class="badge red">CRÍTICO</span>';
																				elseif ($contador->format('%a') <= 30)
																					echo '<span class="badge red">' . $contador->format('%a') . ' días</span>';
																				elseif ($contador->format('%a') <= 45)
																					echo '<span class="badge orange">' . $contador->format('%a') . ' días</span>';
																				else
																					echo '<span class="badge green">' . $contador->format('%a') . ' días</span>';
																			?>
																		</td>
																		<td>
																			<?php
																				if (! is_null($data['VencimientoContratos'][$i]['FechaRetiro'])) 
																					echo '<span class="badge green">EN PROCESO RETIRO</span>';
																			?>
																		</td>
																	</tr>
																	<?php endfor; ?>
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
									<div class="col s12 m12 l12">
										<div class="card animate fadeLeft">
											<div class="card-content red lighten-1 white-text">
												<p class="card-stats-title"><i class="material-icons">person_outline</i>
													Inconsistencias de empleados <?= count($data['InconsistenciasEmpleados']) ?></p>
											</div>
											<div class="card-action">
												<ul class="collapsible">
													<li>
														<div class="collapsible-header">
															<i class="material-icons">remove_red_eye</i>
														</div>
														<div class="collapsible-body">
															<table class="table responsive-table">
																<thead>
																	<tr>
																		<th>DOCUMENTO</th>
																		<th>NOMBRE EMPLEADO</th>
																		<th>INCONSISTENCIA</th>
																	</tr>
																</thead>
																<tbody>
																	<?php for ($i = 0; $i < count($data['InconsistenciasEmpleados']); $i++): ?>
																	<tr>
																		<td><?= $data['InconsistenciasEmpleados'][$i]['Documento'] ?></td>
																		<td><?= $data['InconsistenciasEmpleados'][$i]['NombreEmpleado'] ?></td>
																		<td><?= $data['InconsistenciasEmpleados'][$i]['Inconsistencia'] ?></td>
																	</tr>
																	<?php endfor; ?>
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
					</div>
				</div>
			</div>
			<div class="content-overlay"></div>
		</div>
	</div>
</div>
<?php require_once('views/templates/footer.php'); ?>

<?php 
	$lcFiltro = $_SESSION['PRESTAMOS']['Filtro'];
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
									<h3 class="white-text">Préstamos a empleados</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/prestamos/lista', $data['registros'], $_SESSION['PRESTAMOS']['Pagina'] );
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
												<th style="text-align:center;"></th>
												<th style="text-align:center;"></th>
												<th>
													<?php 
														if	( $_SESSION['PRESTAMOS']['Orden'] == 'EMPLEADOS.Documento,AUXILIARES.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('DOCUMENTO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Documento,AUXILIARES.Nombre">';
																echo label('Documento');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['PRESTAMOS']['Orden'] == 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUXILIARES.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('EMPLEADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUXILIARES.Nombre">';
																echo label('Empleado');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['PRESTAMOS']['Orden'] == 'AUXILIARES.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CONCEPTO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="AUXILIARES.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2">';
																echo label('Concepto');
															echo '</button>';
														}
													?>
												</th>
												<th>Tipo préstamo</th>
												<th class="right-align">Valor préstamo</th>
												<th class="right-align">Vr. Cuotas</th>
												<th class="right-align">Cuotas</th>
												<th class="right-align">Saldo préstamo</th>
												<th>Tercero</th>
												<th>Estado</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i=0; $i < count($data['rows']); $i++)
												{
													$reg = $data['rows'][$i];
											?>
											<?php if ($reg['EstadoEmpleado'] == 'ACTIVO'): ?>
											<tr>
											<?php else: ?>
											<tr class="red-text">
											<?php endif; ?>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/prestamos/editar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
												</td>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/prestamos/borrar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a>
												</td>
												<td><?= $reg['Documento'] ?></td>
												<td>
													<?= $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'] ?>
												</td>
												<td><?= $reg['NombreConcepto'] ?></td>
												<td><?= $reg['NombreTipoPrestamo'] ?></td>
												<td class="right-align"><?= number_format($reg['valorprestamo'], 0) ?></td>
												<td class="right-align"><?= number_format($reg['valorcuota'], 0) ?></td>
												<td class="right-align"><?= number_format($reg['cuotas'], 0) ?></td>
												<td class="right-align"><?= number_format($reg['saldoprestamo'], 0) ?></td>
												<td><?= is_null($reg['NombreBanco']) ? $reg['NombreTercero'] : $reg['NombreBanco'] ?></td>
												<td>
													<?php if ($reg['EstadoPrestamo'] == 'ACTIVO'): ?>
														<i class="material-icons green-text">check</i>
													<?php else: ?>
														<i class="material-icons red-text">block</i>
													<?php endif; ?>
												</td>
											</tr>
											<?php
												}
											?>
										</tbody>
									</table>
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
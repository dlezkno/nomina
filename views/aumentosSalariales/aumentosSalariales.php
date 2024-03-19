<?php 
	$lcFiltro = $_SESSION['AUMENTOSSALARIALES']['Filtro'];
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
									<h3 class="white-text">Aumentos salariales</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/aumentosSalariales/lista', $data['registros'], $_SESSION['AUMENTOSSALARIALES']['Pagina'] );
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
														if	( $_SESSION['AUMENTOSSALARIALES']['Orden'] == 'EMPLEADOS.Documento,AUMENTOSSALARIALES.FechaAumento' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('EMPLEADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Documento,AUMENTOSSALARIALES.FechaAumento">';
																echo label('Empleado');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['AUMENTOSSALARIALES']['Orden'] == 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUMENTOSSALARIALES.FechaAumento' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('NOMBRE EMPLEADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUMENTOSSALARIALES.FechaAumento">';
																echo label('Nombre Empleado');
															echo '</button>';
														}
													?>
												</th>
												<th class="right-align">Fecha aumento</th>
												<th class="right-align">Sueldo anterior</th>
												<th class="right-align">Sueldo básico</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i=0; $i < count($data['rows']); $i++)
												{
													$reg = $data['rows'][$i];
											?>
											<tr>
												<td class="center-align">
													<?php if (! $reg['procesado']): ?>
													<?php if (! $data['RO']): ?>
													<a class="tooltipped"
														href="<?= SERVERURL ?>/aumentosSalariales/editar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
													<?php endif; ?>
													<?php endif; ?>
												</td>
												<td class="center-align">
													<?php if (! $reg['procesado']): ?>
													<?php if (! $data['RO']): ?>
													<a class="tooltipped"
														href="<?= SERVERURL ?>/aumentosSalariales/borrar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a>
													<?php endif; ?>
													<?php endif; ?>
												</td>
												<td><?= $reg['Documento'] ?></td>
												<td><?= $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'] ?></td>
												<td class="right-align"><?= $reg['fechaaumento'] ?></td>
												<td class="right-align"><?= number_format($reg['sueldobasicoanterior'], 0) ?></td>
												<td class="right-align"><?= number_format($reg['sueldobasico'], 0) ?></td>
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
<?php 
	$lcFiltro = $_SESSION['DISPERSIONPORCENTRO']['Filtro'];
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
									<h3 class="white-text">Dispersión por centro</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php if (count($data['rows']) > 0): ?>
									<h6 class="white-text"><?= 'PERÍODO: ' . $data['rows'][0]['Periodo'] ?></h6>
									<h6 class="white-text"><?= 'DESDE: ' . $data['rows'][0]['FechaInicial'] . ' - ' . $data['rows'][0]['FechaFinal'] ?></h6>
									<?php endif; ?>
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/dispersionPorCentro/lista', $data['registros'], $_SESSION['DISPERSIONPORCENTRO']['Pagina'] );
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
														if	( $_SESSION['DISPERSIONPORCENTRO']['Orden'] == 'EMPLEADOS.Documento,CENTROS.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('EMPLEADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Documento,CENTROS.Nombre">';
																echo label('Empleado');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['DISPERSIONPORCENTRO']['Orden'] == 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,CENTROS.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('NOMBRE EMPLEADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,CENTROS.Nombre">';
																echo label('Nombre empleado');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['DISPERSIONPORCENTRO']['Orden'] == 'CENTROS.Centro,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CENTRO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="CENTROS.Centro,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2">';
																echo label('Centro');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['DISPERSIONPORCENTRO']['Orden'] == 'CENTROS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('NOMBRE CENTRO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="CENTROS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2">';
																echo label('Nombre Centro');
															echo '</button>';
														}
													?>
												</th>
												<th class="right-align">Porcentaje</th>
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
													<?php if (! $data['RO']): ?>
													<a class="tooltipped"
														href="<?= SERVERURL ?>/dispersionPorCentro/editar/<?= $reg['Id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
													<?php endif; ?>
												</td>
												<td class="center-align">
													<?php if (! $data['RO']): ?>
													<a class="tooltipped"
														href="<?= SERVERURL ?>/dispersionPorCentro/borrar/<?= $reg['Id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a>
													<?php endif; ?>
												</td>
												<td><?= $reg['Documento']?></td>
												<td><?= $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']?></td>
												<td><?= $reg['Centro']?></td>
												<td><?= $reg['NombreCentro']?></td>
												<td class="right-align"><?= number_format($reg['Porcentaje'], 0) ?></td>
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
<?php 
	$lcFiltro = $_SESSION['EMPLEADOS']['Filtro'];
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
									<h3 class="white-text">Empleados (retirados)</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/empleados/lista', $data['registros'], $_SESSION['EMPLEADOS']['Pagina'] );
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
												<th>
													<?php 
														if	( $_SESSION['EMPLEADOS']['Orden'] == 'EMPLEADOS.Documento' )
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
														if	( $_SESSION['EMPLEADOS']['Orden'] == 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
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
														if	( $_SESSION['EMPLEADOS']['Orden'] == 'EMPLEADOS.FechaIngreso,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
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
														if	( $_SESSION['EMPLEADOS']['Orden'] == 'EMPLEADOS.FechaRetiro,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('FECHA RET.') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.FechaRetiro,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Fecha Ret.');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['EMPLEADOS']['Orden'] == 'CARGOS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
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
														if	( $_SESSION['EMPLEADOS']['Orden'] == 'CENTROS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CENTRO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="CENTROS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Centro');
															echo '</button>';
														}
													?>
												</th>
												<?php if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR): ?>
												<th class="right-align">Sueldo Básico</th>
												<?php endif; ?>
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
													<a class="tooltipped"
														href="<?= SERVERURL ?>/retirados/editar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">remove_red_eye</i>
													</a>
												</td>
												<td><?= $reg['documento'] ?></td>
												<td><?= $reg['apellido1'] . ' ' . $reg['apellido2'] . ' ' . $reg['nombre1'] . ' ' . $reg['nombre2'] ?></td>
												<td><?= $reg['fechaingreso'] ?></td>
												<td><?= $reg['fecharetiro'] ?></td>
												<td><?= $reg['NombreCargo'] ?></td>
												<td><?= $reg['NombreCentro'] ?></td>
												<?php if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR): ?>
												<td class="right-align"><?= number_format($reg['sueldobasico'], 0) ?></td>
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
					</div>
				</div>
			</div>
			<div class="content-overlay"></div>
		</div>
	</div>
</div>
<?php require_once('views/templates/footer.php'); ?>

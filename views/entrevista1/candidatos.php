<?php 
	$lcFiltro = $_SESSION['CANDIDATOS']['Filtro'];
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
									<h3 class="white-text">Candidatos (Entrevista psicológica)</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/entrevista1/lista', $data['registros'], $_SESSION['CANDIDATOS']['Pagina'] );
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
														if	( $_SESSION['CANDIDATOS']['Orden'] == 'EMPLEADOS.Documento' )
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
														if	( $_SESSION['CANDIDATOS']['Orden'] == 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('NOMBRE EMPLEADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2">';
																echo label('Nombre empleado');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['CANDIDATOS']['Orden'] == 'CARGOS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CARGO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="CARGOS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Cargo');
															echo '</button>';
														}
													?>
												</th>
												<th><strong>Email / Celular</strong></th>
												<th>
													<?php 
														if	( $_SESSION['CANDIDATOS']['Orden'] == 'PARAMETROS.Detalle DESC,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('ESTADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="PARAMETROS.Detalle DESC,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Estado');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['CANDIDATOS']['Orden'] == 'SICOLOGOS.Apellido1,SICOLOGOS.Apellido2,SICOLOGOS.Nombre1,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('PSICÓLOGO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="SICOLOGOS.Apellido1,SICOLOGOS.Apellido2,SICOLOGOS.Nombre1,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Psicólogo');
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
											?>
											<tr>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/entrevista1/editar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
												</td>
												<td><?= $reg['documento'] ?></td>
												<td><?= $reg['apellido1'] . ' ' . $reg['apellido2'] . ' ' . $reg['nombre1'] . ' ' . $reg['nombre2'] ?></td>
												<td><?= $reg['NombreCargo'] ?></td>
												<td><?= $reg['email'] . '<br>' . $reg['celular'] ?></td>
												<td>
													<?php
														echo '<p class="green-text lighten-3">' . $reg['EstadoEmpleado'] . '</p>' ;
													?>
												</td>
												<td><?= $reg['Apellido1S'] . ' ' . $reg['Apellido2S'] . ' ' . $reg['Nombre1S'] . ' ' . $reg['Nombre2S'] ?></td>
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

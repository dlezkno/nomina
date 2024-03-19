<?php 
	$lcFiltro = $_SESSION['NOVEDADESPROGRAMABLES']['Filtro'];
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
									<h3 class="white-text">Novedades programables</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/novedadesProgramables/lista', $data['registros'], $_SESSION['NOVEDADESPROGRAMABLES']['Pagina'] );
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
														if	( $_SESSION['NOVEDADESPROGRAMABLES']['Orden'] == 'MAYORES.Mayor,AUXILIARES.Auxiliar,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CONCEPTO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="MAYORES.Mayor,AUXILIARES.Auxiliar,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2">';
																echo label('Concepto');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['NOVEDADESPROGRAMABLES']['Orden'] == 'AUXILIARES.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('DESCRIPCIÓN') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="AUXILIARES.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2">';
																echo label('Descripción');
															echo '</button>';
														}
													?>
												</th>
												<th>Tipo empleado</th>
												<th>
													<?php 
														if	( $_SESSION['NOVEDADESPROGRAMABLES']['Orden'] == 'EMPLEADOS.Documento,AUXILIARES.Nombre' )
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
														if	( $_SESSION['NOVEDADESPROGRAMABLES']['Orden'] == 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUXILIARES.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('NOMBRE EMPLEADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUXILIARES.Nombre">';
																echo label('Nombre empleado');
															echo '</button>';
														}
													?>
												</th>
												<th>Centro</th>
												<th>Cargo</th>
												<th class="right-align">Vr.Novedad</th>
												<th>Estado</th>
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
														href="<?= SERVERURL ?>/novedadesProgramables/editar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
												</td>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/novedadesProgramables/borrar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a>
												</td>
												<td><?= $reg['Mayor'] . $reg['Auxiliar'] ?></td>
												<td><?= $reg['NombreConcepto'] ?></td>
												<td><?= $reg['NombreTipoEmpleado'] ?></td>
												<td><?= $reg['Documento'] ?></td>
												<td>
													<?= $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'] ?>
												</td>
												<td><?= $reg['NombreCentro'] ?></td>
												<td><?= $reg['NombreCargo'] ?></td>
												<td class="right-align"><?= number_format($reg['valor'], 0) ?></td>
												<td><?= $reg['NombreEstado'] ?></td>
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
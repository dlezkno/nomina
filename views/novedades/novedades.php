<?php 
	$lcFiltro = $_SESSION['NOVEDADES']['Filtro'];
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
									<h3 class="white-text">Novedades</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<h6 class="white-text"><?= 'PERÍODO: ' . $data['Periodo'] . '-' . $data['Ciclo'] ?></h6>
									<h6 class="white-text"><?= 'DESDE: ' . $data['FechaInicial'] . ' - ' . $data['FechaFinal'] ?></h6>
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/novedades/lista', $data['registros'], $_SESSION['NOVEDADES']['Pagina'] );
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
												<th style="text-align:center;"></th>
												<th>
													<?php 
														if	( $_SESSION['NOVEDADES']['Orden'] == 'EMPLEADOS.Documento,AUXILIARES.Nombre' )
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
														if	( $_SESSION['NOVEDADES']['Orden'] == 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUXILIARES.Nombre' )
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
														if	( $_SESSION['NOVEDADES']['Orden'] == 'MAYORES.Mayor,AUXILIARES.Auxiliar,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
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
														if	( $_SESSION['NOVEDADES']['Orden'] == 'AUXILIARES.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('DESCRIPCIÓN') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="AUXILIARES.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2">';
																echo label('Descripción');
															echo '</button>';
														}
													?>
												</th>
												<th class="right-align">Horas</th>
												<th class="right-align">Valor</th>
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
														href="<?= SERVERURL ?>/novedades/editar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
													<?php endif; ?>
												</td>
												<td class="center-align">
													<?php if (! $data['RO']): ?>
													<a class="tooltipped"
														href="<?= SERVERURL ?>/novedades/borrar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a>
													<?php endif; ?>
												</td>
												<td><?= $reg['liquida'] ?></td>
												<td><?= $reg['Documento']?></td>
												<td><?= $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']?></td>
												<td><?= $reg['Mayor'] . $reg['Auxiliar'] ?></td>
												<td><?= $reg['NombreConcepto']?></td>
												<td class="right-align">
													<?php
														if (is_null($reg['fechainicial']))
															if ($reg['horas'] > 0) 
															{
																if ($reg['NombreTipoLiquidacion'] == 'HORAS')
																	echo number_format($reg['horas'], 0) . ' H';
																else
																	echo number_format($reg['horas'] / 8, 0) . ' D';
															}
														else
															echo $reg['fechainicial'];
													?>
												</td>
												<td class="right-align">
													<?php
														if (is_null($reg['fechafinal']))
															echo number_format($reg['valor'], 0);
														else
															echo $reg['fechafinal'];
													?>
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
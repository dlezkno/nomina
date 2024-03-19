<?php 
	$lcFiltro = $_SESSION['PERIODOS']['Filtro'];
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
									<h3 class="white-text">Períodos de pago</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/periodos/lista', $data['registros'], $_SESSION['PERIODOS']['Pagina'] );
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
														if	( $_SESSION['PERIODOS']['Orden'] == 'PERIODOS.Referencia,PERIODOS.Periodicidad,PERIODOS.Periodo' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('REFERENCIA') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="PERIODOS.Referencia,PERIODOS.Periodicidad,PERIODOS.Periodo">';
																echo label('Referencia');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['PERIODOS']['Orden'] == 'PERIODOS.Periodicidad,PERIODOS.Referencia,PERIODOS.Periodo' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('PERIODICIDAD') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="PERIODOS.Periodicidad,PERIODOS.Referencia,PERIODOS.Periodo">';
																echo label('Periodicidad');
															echo '</button>';
														}
													?>
												</th>
												<th class="right-align"><strong>Período</strong></th>
												<th><strong>Fecha de inicio</strong></th>
												<th><strong>Fecha final</strong></th>
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
														href="<?= SERVERURL ?>/periodos/editar/<?= $reg['Id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
												</td>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/periodos/borrar/<?= $reg['Id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a>
												</td>
												<td><?= $reg['Referencia'] ?></td>
												<td><?= $reg['Detalle'] ?></td>
												<td class="right-align"><?= $reg['Periodo'] ?></td>
												<td><?= $reg['FechaInicial'] ?></td>
												<td><?= $reg['FechaFinal'] ?></td>
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
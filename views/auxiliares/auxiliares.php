<?php 
	$lcFiltro = $_SESSION['AUXILIARES']['Filtro'];
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
									<h3 class="white-text">Conceptos auxiliares</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/auxiliares/lista', $data['registros'], $_SESSION['AUXILIARES']['Pagina'] );
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
														if	( $_SESSION['AUXILIARES']['Orden'] == 'AUXILIARES.Borrado,MAYORES.Mayor,AUXILIARES.Auxiliar' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CONCEPTO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="AUXILIARES.Borrado,MAYORES.Mayor,AUXILIARES.Auxiliar">';
																echo label('Concepto');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['AUXILIARES']['Orden'] == 'AUXILIARES.Borrado,MAYORES.Nombre,AUXILIARES.Auxiliar' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('NOMBRE MAYOR') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="AUXILIARES.Borrado,MAYORES.Nombre,AUXILIARES.Auxiliar">';
																echo label('Nombre mayor');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['AUXILIARES']['Orden'] == 'AUXILIARES.Borrado,AUXILIARES.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('NOMBRE AUXILIAR') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="AUXILIARES.Borrado,AUXILIARES.Nombre">';
																echo label('Nombre auxiliar');
															echo '</button>';
														}
													?>
												</th>
												<th>Imputación</th>
												<th>Factor</th>
												<th>Disp.</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i=0; $i < count($data['rows']); $i++)
												{
													$reg = $data['rows'][$i];
											?>
											<?php if ($reg['borrado'] == 0): ?>
											<tr>
											<?php else: ?>
											<tr class="red-text">
											<?php endif; ?>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/auxiliares/editar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
												</td>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/auxiliares/borrar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a>
												</td>
												<td><?= $reg['Mayor'] . $reg['auxiliar'] ?></td>
												<td class=""><?= $reg['NombreMayor'] ?></td>
												<td><?= $reg['nombre'] ?></td>
												<td><?= $reg['Imputacion'] ?></td>
												<td><?= number_format($reg['factorconversion'], 2) ?></td>
												<?php if ($reg['esdispersable'] == 1): ?>
												<td><i class="material-icons">check</i></td>
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
<?php 
	$lcFiltro = $_SESSION['PLANTILLAS']['Filtro'];
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
									<h3 class="white-text">Plantillas</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/plantillas/lista', $data['registros'], $_SESSION['PLANTILLAS']['Pagina'] );
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
														if	( $_SESSION['PLANTILLAS']['Orden'] == 'PARAMETROS1.Detalle' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('ESTADO EMPLEADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="PARAMETROS1.Detalle">';
																echo label('Estado empleado');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['PLANTILLAS']['Orden'] == 'PARAMETROS2.Detalle' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('TIPO PLANTILLA') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="PARAMETROS2.Detalle">';
																echo label('Tipo plantilla');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['PLANTILLAS']['Orden'] == 'PARAMETROS3.Detalle' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('TIPO CONTRATO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="PARAMETROS3.Detalle">';
																echo label('Tipo contrato');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['PLANTILLAS']['Orden'] == 'PLANTILLAS.Asunto' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('ASUNTO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="PLANTILLAS.Asunto">';
																echo label('Asunto');
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
														href="<?= SERVERURL ?>/plantillas/editar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
												</td>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/plantillas/borrar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a>
												</td>
												<td><?= $reg['NombreEstadoEmpleado'] ?></td>
												<td><?= $reg['NombreTipoPlantilla'] ?></td>
												<td><?= $reg['NombreTipoContrato'] ?></td>
												<td><?= $reg['asunto'] ?></td>
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
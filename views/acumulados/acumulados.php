<?php 
	$lcFiltro = $_SESSION['ACUMULADOS']['Filtro'];
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
									<h3 class="white-text">Acumulados</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/acumulados/list', $data['registros'], $_SESSION['ACUMULADOS']['Pagina'] );
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
												<th>ID</th>
												<th>EMPLEADO</th>
												<th>NOMBRE EMPLEADO</th>
												<th>CONCEPTO</th>
												<th>DESCRIPCIÓN</th>
												<th>FECHA INI.</th>
												<th>FECHA FIN.</th>
												<th class="right-align">HORAS/DÍAS</th>
												<th class="right-align">PAGOS</th>
												<th class="right-align">DEDUCCIONES</th>
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
													<!-- <a class="tooltipped"
														href="<?= SERVERURL ?>/acumulados/editar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a> -->
												</td>
												<td class="center-align">
													<!-- <a class="tooltipped"
														href="<?= SERVERURL ?>/acumulados/borrar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a> -->
												</td>
												<td><?= $reg['Id'] ?></td>
												<td><?= $reg['Documento'] ?></td>
												<td><?= $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']?></td>
												<td><?= $reg['Mayor'] . $reg['Auxiliar']?></td>
												<td><?= $reg['NombreConcepto'] ?></td>
												<td><?= $reg['FechaInicialPeriodo'] ?></td>
												<td><?= $reg['FechaFinalPeriodo'] ?></td>
												<td class="right-align">
													<?php
														if	($reg['NombreTipoLiquidacion'] == 'DÍAS')
															echo number_format($reg['Horas'] / 8, 0) . 'D';
														elseif	($reg['NombreTipoLiquidacion'] == 'HORAS')
															echo number_format($reg['Horas'], 0) . 'H';
													?>
												</td>
												<td class="right-align"><?= $reg['Imputacion'] == 'PAGO' ? number_format($reg['Valor'], 0) : '' ?></td>
												<td class="right-align"><?= $reg['Imputacion'] == 'PAGO' ? '' : number_format($reg['Valor'], 0) ?></td>
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
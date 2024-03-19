<?php 
	$lcFiltro = $_SESSION['LIQ_PRENOMINA']['Filtro'];
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
									<h3 class="white-text">Prenómina</h3>
									<?php if (count($data['rows']) > 0): ?>
									<h6 class="white-text">
										<?= 'PERÍODO: ' . $data['rows'][0]['Periodo'] . '-' . $data['rows'][0]['Ciclo'] . '  DESDE: ' . $data['rows'][0]['FechaInicial'] . ' - ' . $data['rows'][0]['FechaFinal'] ?>
									</h6>
									<?php endif; ?>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										// if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
										// 	echo paginar(SERVERURL . '/liquidacionPrenomina/informePrenomina', $data['registros'], $_SESSION['LIQ_PRENOMINA']['Pagina'] );
									?>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12">
									<div class="row">
										<div class="col s6">CONCEPTO / EMPLEADO</div>
										<div class="col s2 right-align">H/D</div>
										<div class="col s2 right-align">PAGOS</div>
										<div class="col s2 right-align">DEDUCCIONES</div>
									</div>
									<ul class="collapsible">
									<?php
										$ConceptoAnt = '';
											
										for ($i = 0; $i < count($data['rows']); $i++):
											$reg = $data['rows'][$i];
											$NombreEmpleado = $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'];
											$Concepto = $reg['Mayor'] . $reg['Auxiliar'];

											if ($reg['NombreConcepto'] <> $ConceptoAnt) 
											{
												if (! empty($ConceptoAnt))
												{
									?>
												<hr/>
												<div class="row">
													<div class="col s6 m6">
														TOTAL POR <strong><?= $Concepto ?></strong>
													</div>
													<div class="col s2 m2"></div>
													<div class="col s2 m2 right-align"><?= number_format($TotalDb, 0) ?></div>
													<div class="col s2 m2 right-align"><?= number_format($TotalCr, 0) ?></div>
												</div>
											</div>
										</li>
									<?php
												}

												$Concepto = $reg['NombreConcepto'];
												$TotalDb = $reg['TotalDb'];
												$TotalCr = $reg['TotalCr'];
									?>
										<li>
											<div class="collapsible-header">
												<div class="row">
													<div class="col">																
														<strong><?= $reg['NombreConcepto'] ?></strong>
													</div>
												</div>
											</div>
											<div class="collapsible-body">
									<?php
												$ConceptoAnt = $reg['NombreConcepto'];
											}
									?>
												<div class="row">
													<div class="col s6"><?= $NombreEmpleado ?></div>
													<div class="col s2 right-align">
														<?php
															if ($reg['Horas'] > 0) 
																if ($reg['NombreTipoLiquidacion'] == 'DÍAS' OR $reg['Liquida'] == 'P')
																	echo number_format($reg['Horas'] / 8, 0) . ' D';
																else
																	echo number_format($reg['Horas'], 0) . ' H';
														?>
													</div>
													<div class="col s2 right-align">
														<?php
															if ($reg['Imputacion'] == 'PAGO') 
																echo number_format($reg['Valor'], 0);
														?>
													</div>
													<div class="col s2 right-align">
														<?php
															if ($reg['Imputacion'] <> 'PAGO') 
																	echo number_format($reg['Valor'], 0);
														?>
													</div>
												</div>
									<?php endfor; ?>
												<hr/>
												<div class="row">
													<div class="col s6 m6">																
														TOTAL POR <strong><?= $Concepto ?></strong>
													</div>
													<div class="col s2 m2"></div>
													<div class="col s2 m2 right-align"><?= number_format($TotalDb, 0) ?></div>
													<div class="col s2 m2 right-align"><?= number_format($TotalCr, 0) ?></div>
												</div>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
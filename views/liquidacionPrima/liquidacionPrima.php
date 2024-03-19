<?php 
	$lcFiltro = $_SESSION['LIQ_PRIMA']['Filtro'];
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
									<h3 class="white-text">Pago de Prima</h3>
									<?php if (count($data['rows']) > 0): ?>
									<h6 class="white-text"><?= 'PERÍODO: ' . $data['rows'][0]['Periodo'] . '-' . $data['rows'][0]['Ciclo'] ?></h6>
									<?php endif; ?>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/liquidacionPrima/lista/'.$data['CicloPrima'], $data['registros'], $_SESSION['LIQ_PRIMA']['Pagina'] );
									?>
								</div>
								<div class="col s12">
									<div class="row">
										<div class="col s6 right-align">
											<a href="<?php echo SERVERURL; ?>/liquidacionPrima/lista/97/1">
												<label style="cursor: pointer;">
													<?php
														echo ($data['CicloPrima'] <> '96')
															? '<label style="display: inline-block; border-radius: 100%; background: #FF4081; width: 15px; height: 15px;" ></label>'
															: '<label style="display: inline-block; border-radius: 100%; border: 2px solid #5A5A5A; width: 15px; height: 15px;" ></label>';
													?>
													<span style="margin-left: 15px; font-size: 15px; margin-top: -10px;">Sin Icetex (97)</span>
												</label>
											</a>
										</div>
										<div class="col s6">
											<a href="<?php echo SERVERURL; ?>/liquidacionPrima/lista/96/1">
												<label style="cursor: pointer;">
													<?php
														echo ($data['CicloPrima'] <> '96')
															? '<label style="display: inline-block; border-radius: 100%; border: 2px solid #5A5A5A; width: 15px; height: 15px;" ></label>'
															: '<label style="display: inline-block; border-radius: 100%; background: #FF4081; width: 15px; height: 15px;" ></label>';
													?>
													<span style="margin-left: 15px; font-size: 15px; margin-top: -10px;">Solo Icetex (96)</span>
												</label>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12">
									<table>
										<thead>
											<tr>
												<th>CONCEPTO</th>
												<th class="right-align">BASE</th>
												<th class="right-align">H/D</th>
												<th class="right-align">PAGOS</th>
												<th class="right-align">DEDUCCIONES</th>
												<th class="right-align">SALDO</th>
												<th class="right-align">FECHA INI.</th>
												<th class="right-align">FECHA FIN.</th>
												<th>LIQ.</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$NombreAnt = '';
											
												for ($i = 0; $i < count($data['rows']); $i++):
													$reg = $data['rows'][$i];
													$NombreEmpleado = $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'];
											?>
											<?php if ($NombreEmpleado <> $NombreAnt): 
												if ($reg['EstadoEmpleado'] == 'RETIRADO'): ?>
												<tr class="red lighten-4">
												<?php else: ?>
												<tr class="teal lighten-5">
												<?php endif; ?>
													<td>
														<strong class="blue-text text-darken-4"><?= $reg['Documento'] ?></strong>
														<br>
														<strong class="blue-text text-darken-4"><?= $NombreEmpleado ?></strong>
														<br>
														<?= 'CC: ' . $reg['NombreCentro']; ?>
														<br>
														<?= 'CG: ' . $reg['NombreCargo']; ?>
													</td>
													<td class="right-align">
														<br>
														<?= 'S.B: ' . number_format($reg['SueldoBasico'], 0) ?>
														<br>
														<?= 'ING: ' . $reg['FechaIngreso'] ?>
														<br>
														<?= 'VCT: ' . $reg['FechaVencimiento'] ?>
													</td>
													<td></td>
													<td class="right-align">
														<br>
														<br>
														<br>
														<strong class="blue-text text-darken-4"><?= number_format($reg['TotalDb'], 0) ?></strong>
													</td>
													<td class="right-align">
														<br>
														<br>
														<br>
														<strong class="blue-text text-darken-4"><?= number_format($reg['TotalCr'], 0) ?></strong>
													</td>
													<td class="right-align">
														<br>
														<br>
														<br>
														<strong class="blue-text text-darken-4"><?= number_format($reg['TotalDb'] - $reg['TotalCr'], 0) ?></strong>
													</td>
													<td></td>
													<td></td>
													<td>
														<?php 
															$dir = '/documents/' . $reg['Documento'] . '_' . strtoupper($reg['Apellido1']) . '_' . strtoupper($reg['Apellido2']) . '_' . strtoupper($reg['Nombre1']) . '_' . strtoupper($reg['Nombre2']);
															$cDirectorio = SERVERURL . $dir;
															$archivo = getImage($dir,$cDirectorio);
														?>
														<img src="<?= $archivo ?>" alt="" class="circle responsive-img" width="100px">
													</td>
												</tr>
											<?php
													$NombreAnt = $NombreEmpleado;
												endif; 
											?>
											<tr>
												<td><?= $reg['NombreConcepto'] ?></td>
												<?php if ($reg['Base'] > 0): ?>
													<td class="right-align"><?= number_format($reg['Base'], 0) ?></td>
												<?php else: ?>
													<td></td>
												<?php endif; ?>
												<?php if ($reg['Horas'] > 0): ?>
													<?php if ($reg['NombreTipoLiquidacion'] == 'HORAS'): ?>
														<td class="right-align">
															<strong class="blue-text text-darken-4"><?= number_format($reg['Horas'], 0) . ' H' ?></strong>
														</td>
													<?php else: ?>
														<td class="right-align">
															<strong class="blue-text text-darken-4"><?= number_format($reg['Horas'] / 8, 0) . ' D' ?></strong>
														</td>
													<?php endif; ?>
													<!-- <?php if ($reg['NombreTipoLiquidacion'] == 'DÍAS' OR $reg['Liquida'] == 'P'): ?>
														<td class="right-align">
															<strong class="blue-text text-darken-4"><?= number_format($reg['Horas'] / 8, 0) . ' D' ?></strong>
														</td>
													<?php else: ?>
														<td class="right-align">
															<strong class="blue-text text-darken-4"><?= number_format($reg['Horas'], 0) . ' H' ?></strong>
														</td>
													<?php endif; ?> -->
												<?php else: ?>
													<td></td>
												<?php endif; ?>
												<?php if ($reg['Imputacion'] == 'PAGO'): ?>
													<td class="right-align">
														<strong class="blue-text text-darken-4"><?= number_format($reg['Valor'], 0) ?></strong>
													</td>
													<td></td>
												<?php else: ?>
													<td></td>
													<td class="right-align">
														<strong class="blue-text text-darken-4"><?= number_format($reg['Valor'], 0) ?></strong>
													</td>
												<?php endif; ?>
												<?php if ($reg['Saldo'] > 0): ?>
													<td class="right-align">
														<?= number_format($reg['Saldo'], 0) ?>
													</td>
												<?php else: ?>
													<td></td>
												<?php endif; ?>
												<?php if (! is_null($reg['FechaInicialVC'])): ?>
													<td class="right-align">
														<?= $reg['FechaInicialVC'] ?>
													</td>
													<td class="right-align">
														<?= $reg['FechaFinalVC'] ?>
													</td>
												<?php else: ?>
													<td></td>
													<td></td>
												<?php endif; ?>
												<td>
													<?php 
														switch ($reg['Liquida']) 
														{
															case 'I':
																echo '<span class="badge orange">INC</span>';
																break;
															case 'N':
																echo '<span class="badge red">NOV</span>';
																break;
															case 'P':
																echo '<span class="badge green">N.P</span>';
																break;
															case 'C':
																echo '<span class="badge green">CR.</span>';
																break;
															case 'A':
															case 'R':
																	echo '<span class="badge blue">AUT</span>';
																break;
														}
													?>
												</td>
											</tr>
											<?php endfor; ?>
										</tbody>
									</table>
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
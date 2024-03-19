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
									<h3 class="white-text">Retención Fuente</h3>
									<?php if (count($data['rows']) > 0): ?>
									<h6 class="white-text"><?= 'PERÍODO: ' . $data['rows'][0]['Periodo'] . '-' . $data['rows'][0]['Ciclo'] . '  DESDE: ' . $data['rows'][0]['FechaInicial'] . ' - ' . $data['rows'][0]['FechaFinal'] ?></h6>
									<?php endif; ?>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										// if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
										// 	echo paginar(SERVERURL . '/liquidacionPrenomina/listaRF', $data['registros'], $_SESSION['LIQ_PRENOMINA']['Pagina'] );
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
												<th></th>
												<th>NOMBRE EMPLEADO / CENTRO</th>
												<th class="right-align">INGRESOS</th>
												<th class="right-align">SALUD+PENSION</th>
												<th class="right-align">AFC+FVP</th>
												<th class="right-align">VIVIENDA</th>
												<th class="right-align">SALUD PREP.</th>
												<th class="right-align">DEPENDIENTES</th>
												<th class="right-align">TOTAL DEDUCC.</th>
												<th class="right-align">NETO</th>
												<th class="right-align">DEDUCIBLE 25%</th>
												<th class="right-align">NETO</th>
												<th class="right-align">%</th>
												<th class="right-align">RET.FTE.</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i = 0; $i < count($data['rows']); $i++):
													$reg = $data['rows'][$i];
													$NombreEmpleado = $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'];
											?>
												<tr>
													<td>
														<?php 
														
															$dir = '/documents/' . $reg['Documento'] . '_' . strtoupper($reg['Apellido1']) . '_' . strtoupper($reg['Apellido2']) . '_' . strtoupper($reg['Nombre1']) . '_' . strtoupper($reg['Nombre2']);
															$cDirectorio = SERVERURL . $dir;
															$archivo = getImage($dir,$cDirectorio);
														?>
														<img src="<?= $archivo ?>" alt="" class="circle responsive-img" width="100px">
													</td>
													<td>
														<?= $reg['Documento'] ?>
														<br>
														<strong><?= $NombreEmpleado ?></strong>
														<br>
														<?= 'CC: ' . $reg['NombreCentro']; ?>
														<br>
														<?= $reg['TipoEmpleado']; ?>
														<br>
														<?= $reg['MetodoRetencion']; ?>
													</td>
													<td class="right-align">
														<?= number_format($reg['IngresoBruto'], 0) ?>
													</td>
													<td class="right-align red-text">
														<?= number_format($reg['SaludPension'], 0) ?>
													</td>
													<td class="right-align red-text">
														<?= number_format($reg['AfcFvp'], 0) . $reg['AfcFvpAj'] ?>
														<br>
														Max: <?= number_format($reg['TopeAfcFvp'], 0) ?>
													</td>
													<td class="right-align red-text">
														<?= number_format($reg['CuotaVivienda'], 0) . $reg['CuotaViviendaAj'] ?>
														<br>
														Max: <?= number_format($reg['TopeCuotaVivienda'], 0) ?>
													</td>
													<td class="right-align red-text">
														<?= number_format($reg['SaludYEducacion'], 0) . $reg['SaludYEducacionAj'] ?>
														<br>
														Max: <?= number_format($reg['TopeSaludYEducacion'], 0) ?>
													</td>
													<td class="right-align red-text">
														<?= number_format($reg['DeduccionDependientes'], 0) ?>
														<br>
														Max: <?= number_format($reg['TopeDedDep'], 0) ?>
													</td>
													<td class="right-align red-text">
														<?= number_format($reg['ValorDeducciones'], 0) . $reg['ValorDeduccionesAj'] ?>
														<br>
														Ac.: <?= number_format($reg['ExencionAnual'], 0) ?>
														<br>
														Max: <?= number_format($reg['TopeAnual'], 0) ?>
													</td>
													<td class="right-align">
														<?= number_format($reg['IngresoNeto1'], 0) ?>
													</td>
													<td class="right-align red-text">
														<?= number_format($reg['ValorDeducible25'], 0) . $reg['ValorDeducible25Aj'] ?>
														<br>
														Ac: <?= number_format($reg['ExencionAnual25'], 0) ?>
														<br>
														Max: <?= number_format($reg['TopeAnual25'], 0) ?>
													</td>
													<td class="right-align">
														<?= number_format($reg['IngresoNeto'], 0) ?>
													</td>
													<td class="right-align">
														<?= number_format($reg['PorcentajeRetencion'], 2) ?>
													</td>
													<td class="right-align green-text">
														<?= number_format($reg['ValorRetFte'], 0) ?>
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
<?php 
	$lcFiltro = $_SESSION['LIBROVACACIONES']['Filtro'];
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
									<h3 class="white-text">Libro de Vacaciones</h3>
								</div><div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/Vacaciones/listaLibroVacaciones', $data['registros'], $_SESSION['LIBROVACACIONES']['Pagina'] );
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
												<!-- <th></th> -->
												<th></th>
												<th>EMPLEADO</th>
												<th class="right-align">SUELDO BÁSICO</th>
												<th class="right-align">SALARIO BASE</th>
												<th class="right-align">DÍAS SyL</th>
												<th class="right-align">DÍAS LIQ.</th>
												<th>FECHA CAUSACIÓN</th>
												<th class="right-align">VR. TIEMPO</th>
												<th class="right-align">VR. DINERO</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i = 0; $i < count($data['rows']); $i++):
													$reg = $data['rows'][$i];
													$NombreEmpleado = $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'];
											?>
											<tr>
												<!-- <td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/Vacaciones/libroVacaciones/<?= $reg['Id'] ?>"
														data-position="bottom" data-tooltip="Consultar" style="color:teal;">
														<i class="small material-icons">remove_red_eye</i>
													</a>
												</td> -->
												<td>
													<?php 
														$dir = '/documents/' . $reg['Documento'] . '_' . strtoupper($reg['Apellido1']) . '_' . strtoupper($reg['Apellido2']) . '_' . strtoupper($reg['Nombre1']) . '_' . strtoupper($reg['Nombre2']);
														$cDirectorio = SERVERURL . $dir;
														$archivo = getImage($dir,$cDirectorio);
													?>
													<img src="<?= $archivo ?>" alt="" class="circle responsive-img" width="75px">
												</td>
												<td>
													<strong><?= $NombreEmpleado ?></strong>
													<br>
													<?= 'CC: ' . $reg['NombreCentro']; ?>
													<br>
													<?= 'CG: ' . $reg['NombreCargo']; ?>
												</td>
												<td class="right-align">
													<?= number_format($reg['SueldoBasico'], 0) ?>
													<br>
													<?= 'ING: ' . $reg['FechaIngreso'] ?>
													<br>
													<?= 'VCT: ' . $reg['FechaVencimiento'] ?>
												</td>
												<td class="right-align">
													<strong><?= number_format($reg['SalarioBase'], 0) ?></strong>
												</td>
												<td class="right-align">
													<strong><?= number_format($reg['DiasSancionYLicencia'], 0) ?></strong>
												</td>
												<td class="right-align">
													<strong><?= number_format($reg['DiasALiquidar'], 0) ?></strong>
												</td>
												<td><?= $reg['FechaCausacion'] ?></td>
												<td class="right-align">
													<strong><?= number_format($reg['ValorEnTiempo'], 0) ?></strong>
												</td>
												<td class="right-align">
													<strong><?= number_format($reg['ValorEnDinero'], 0) ?></strong>
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
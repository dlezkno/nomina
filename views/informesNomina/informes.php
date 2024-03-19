<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$Referencia 		= $data['reg']['Referencia'];
	$SelectPeriodicidad = getSelect('Periodicidad', $data['reg']['Periodicidad'], '', 'PARAMETROS.Valor');
	$Periodo 			= $data['reg']['Periodo'];
	$Ciclo 				= $data['reg']['Ciclo'];

	$SelectInforme = getSelectValor('InformeNomina', 0, '', 'PARAMETROS.Valor');

	$centros = getTabla('CENTROS', '', 'CENTROS.Nombre');

	$SelectCentro = '';
	$SelectProyecto = '';
	
	for ($i=0; $i < count($centros); $i++) 
	{ 
		if (left($centros[$i]['centro'], 1) <> 'S')
		{
			if	($centros[$i]['id'] == $data['reg']['IdCentro'])
				$SelectCentro .= '<option selected value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
			else
				$SelectCentro .= '<option value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
		}

		if (left($centros[$i]['centro'], 1) == 'S')
		{
			if	($centros[$i]['id'] == $data['reg']['IdProyecto'])
				$SelectProyecto .= '<option selected value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
			else
				$SelectProyecto .= '<option value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
		}
	}

	$SelectTipoEmpleado = getSelect('TipoEmpleado', $data['reg']['TipoEmpleados'], '', 'PARAMETROS.Valor');
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
									<h3 class="white-text">Informes de nómina</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<section class="tabs-vertical mt-1 section">
								<div class="card-panel">
									<div class="card-alert card cyan darken-4">
										<div class="card-content white-text">
											<p>INFORMES DE NÓMINA</p>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m3">
											<?php 
												get(label('Referencia*'), 'Referencia', $Referencia, 'number', 5, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
										<div class="col s12 m3">
											<?php 
												get(label('Periodicidad*'), 'Periodicidad', $SelectPeriodicidad, 'select', 0, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
										<div class="col s12 m3">
											<?php 
												get(label('Período*'), 'Periodo',  $Periodo, 'number', 2, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
										<div class="col s12 m3">
											<?php 
												get(label('Ciclo*'), 'Ciclo',  $Ciclo, 'number', 2, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m6">
											<?php 
												get(label('Informe*'), 'Informe',  $SelectInforme, 'select', 0, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m6">
											<?php 
												get(label('Tipo empleados'), 'TipoEmpleados', $SelectTipoEmpleado, 'select', 0, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
										<div class="col s12 m6">
											<?php 
												get(label('Empleado'), 'Empleado', $data['reg']['Empleado'], 'text', 15, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m6">
											<?php 
												get(label('Centro de costos'), 'IdCentro',  $SelectCentro, 'select', 0, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
										<div class="col s12 m6">
											<?php 
												get(label('Proyecto'), 'IdProyecto',  $SelectProyecto, 'select', 0, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
									</div>
								</div>
							</section>
							<section class="tabs-vertical mt-1 section">
								<table>
									<tbody>
										<?php
											$cDirectorio = './descargas/';

											if (is_dir($cDirectorio)):
												$dir = opendir($cDirectorio);

												while (($archivo = readdir($dir)) !== false):
													if ($archivo != '.' AND $archivo != '..'):
														if (strpos($archivo, $_SESSION['Login']['Usuario']) !== FALSE):														
										?>
										<tr>
											<td>
												<?= $archivo ?>
											</td>
											<td>
												<a href="<?= $cDirectorio . $archivo; ?>"
													download="<?php echo $archivo; ?>"
													class="btn btn-sm teal lighten-3 tooltipped" data-position="bottom"
													data-tooltip="Descargar documento">
													<i class="material-icons">cloud_download</i>
												</a>
											</td>
											<td>
												<button type="submit" class="btn btn-sm teal lighten-3 tooltipped"
													formaction="?Action=ELIMINAR&Archivo=<?= $cDirectorio . $archivo ?>"
													data-position="bottom" data-tooltip="Eliminar documento">
													<i class="material-icons">delete</i>
												</button>
											</td>
										</tr>
										<?php
														endif;
													endif;
												endwhile;
												closedir($dir);
											endif;
										?>
									</tbody>
								</table>
							</section>
						</div>
					</div>
					<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
						<?php if ( $data['mensajeError'] ): ?>
						<div class="row">
							<div class="col s12">
								<h6 class="orange-text">
									<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por favor
									valídelas:
								</h6>
								<br>
								<?= $data['mensajeError'] ?>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$Select = '';
	$dataSel = $data['reg']['Periodos'];

	for ($i=0; $i < count($dataSel); $i++) {
		if	( $dataSel[$i]['id'] ==  $data['reg']['IdPeriodo'] )
			$Select .= '<option selected value=' . $dataSel[$i]['id'] . '>' . $dataSel[$i]['detalle'] . '</option>';
		else
			$Select .= '<option value=' . $dataSel[$i]['id'] . '>' . $dataSel[$i]['detalle'] . '</option>';
	}
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
									<h3 class="white-text">Nómina electrónica</h3>
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
											<p>NÓMINA ELECTRÓNICA</p>
										</div>
									</div>
									<?php if ($data['reg']['EstadoUltimaTransmision']=='EnProgreso') { ?>
									<div class="card-alert card red darken-4">
										<div class="card-content white-text">
											<p>EN ESTE MOMENTO NO SE PUEDE REALIZAR UNA TRANSMISIÓN YA QUE EXISTE UNA EN PROGRESO</p>
										</div>
									</div>
									<?php } ?>
									<?php ?>
									<div class="row">
										<div class="col s12">
											<?php 
												get(label('Periodo*'), 'IdPeriodo', $Select, 'select', 0, $data['reg']['EstadoUltimaTransmision']=='EnProgreso', 'required', 'fas fa-pen'); 
											?>
										</div>
										<div class="col s12">
											<?php 
												get(label('Empleado'), 'Empleado', $data['reg']['Empleado'], 'text', null, $data['reg']['EstadoUltimaTransmision']=='EnProgreso', '', 'fas fa-pen'); 
											?>
										</div>
									</div>
								</div>
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
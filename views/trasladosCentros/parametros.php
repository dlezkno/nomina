<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$IdCentroActual 	= $data['reg']['IdCentroActual'];
	$IdCentroNuevo 		= $data['reg']['IdCentroNuevo'];
	$CantidadTraslados 	= $data['reg']['CantidadTraslados'];

	$centros = getTabla('CENTROS', '', 'CENTROS.Nombre');

	$SelectCentroActual = '';
	$SelectCentroNuevo  = '';
	
	for ($i = 0; $i < count($centros); $i++) 
	{ 
		if	($centros[$i]['id'] == $data['reg']['IdCentroActual'])
			$SelectCentroActual .= '<option selected value=' . $centros[$i]['id'] . '>' . $centros[$i]['nombre'] . ' [' . $centros[$i]['centro'] . ']</option>';
		else
			$SelectCentroActual .= '<option value=' . $centros[$i]['id'] . '>' . $centros[$i]['nombre'] . ' [' . $centros[$i]['centro'] . ']</option>';

		if	($centros[$i]['id'] == $data['reg']['IdCentroNuevo'])
			$SelectCentroNuevo .= '<option selected value=' . $centros[$i]['id'] . '>' . $centros[$i]['nombre'] . ' [' . $centros[$i]['centro'] . ']</option>';
		else
			$SelectCentroNuevo .= '<option value=' . $centros[$i]['id'] . '>' . $centros[$i]['nombre'] . ' [' . $centros[$i]['centro'] . ']</option>';
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
									<h3 class="white-text">Traslados de Centros de Costos</h3>
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
											<p>TRASLADOS DE CENTROS DE COSTOS</p>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m6">
											<?php 
												get(label('Centro de costos actual*'), 'IdCentroActual', $SelectCentroActual, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
											?>
										</div>
										<div class="col s12 m6">
											<?php 
												get(label('Centro de costos nuevo*'), 'IdCentroNuevo', $SelectCentroNuevo, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
											?>
										</div>
									</div>
									<div class="row">
										<?php if ($CantidadTraslados > 0): ?>
										<h3>Se han trasladado <?= $CantidadTraslados ?> empleados</h3>
										<?php endif; ?>
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
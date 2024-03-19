<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');
	
	$SelectPeriodicidad = getSelect('Periodicidad', $data['reg']['Periodicidad'], '', 'PARAMETROS.Valor');
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
									<h3 class="white-text">Períodos de pago</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12 m6">
									<div class="input-field">
										<?php 
											get(label('Referencia (año)*'), 'Referencia', $data['reg']['Referencia'], 'number', 5, FALSE, 'required', 'far fa-calendar-alt'); 
										?>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col s12 m6">
									<div class="input-field">
										<?php
											get(label('Periodicidad*'), 'Periodicidad', $SelectPeriodicidad, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
										?>
									</div>
								</div>
								<div class="col s12 m6">
									<div class="input-field">
										<?php 
											get(label('Período*'), 'Periodo', $data['reg']['Periodo'], 'number', 2, FALSE, 'required', 'fas fa-list-ol'); 
										?>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col s12 m6">
									<div class="input-field">
										<?php 
											get(label('Fecha inicial*'), 'FechaInicial', $data['reg']['FechaInicial'], 'date', 10, FALSE, 'required', 'far fa-calendar'); 
										?>
									</div>
								</div>
								<div class="col s12 m6">
									<div class="input-field">
										<?php 
											get(label('Fecha final*'), 'FechaFinal', $data['reg']['FechaFinal'], 'date', 10, FALSE, 'required', 'far fa-calendar'); 
										?>
									</div>
								</div>
							</div>
						</div>
						<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
							<?php if ( $data['mensajeError'] ): ?>
							<div class="row">
								<div class="col s12">
									<h6 class="orange-text">
										<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por
										favor valídelas:
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
</div>

<?php require_once('views/templates/footer.php'); ?>
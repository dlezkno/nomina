<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	// $data = array();
	// $data['mensajeError'] = '';

	$FechaInicialPeriodo = ComienzoMes(date('Y-m-d'));
	$FechaFinalPeriodo = FinMes(date('Y-m-d'));
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
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Fecha inicial período*'), 'FechaInicialPeriodo', $FechaInicialPeriodo, 'date', 10, FALSE, 'required', ''); 
									?>
								</div>
								<div class="col s12 l6">
									<?php 
										get(label('Fecha final período*'), 'FechaFinalPeriodo', $FechaFinalPeriodo, 'date', 10, FALSE, 'required', ''); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="file-field input-field col s6 m6 l6">
									<div class="btn teal lighten-3">
										<span>Archivo</span>
										<input type="file" accept=".xlsx,.xls" id="Archivo" name="Archivo">
									</div>
									<div class="file-path-wrapper">
										<input class="file-path validate" type="text" placeholder="Seleccione archivo para cargar">
									</div>
									<!-- <p>Maximo tamaño de archivo 2MB.</p> -->
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
								<br>
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
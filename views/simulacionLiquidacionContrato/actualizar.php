<?php 
	$lcFiltro = $_SESSION['SIMULACION']['Filtro'];
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$centros = getTabla('CENTROS', '', 'CENTROS.Nombre');

	$SelectCentro = '';
	
	for ($i=0; $i < count($centros); $i++) 
	{ 
		if	($centros[$i]['id'] == $data['reg']['IdCentro'])
			$SelectCentro .= '<option selected value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
		else
			$SelectCentro .= '<option value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
	}

    $FechaLiquidacion = $data['reg']['FechaLiquidacion'];
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
									<h3 class="white-text">Simulación liquidacion contrato</h3>
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
											<p>ACUMULADOS</p>
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
												get(label('Empleado'), 'Empleado', $data['reg']['Empleado'], 'text', 15, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m6">
											<?php 
												get(label('Fecha liquidación*'), 'FechaLiquidacion', $FechaLiquidacion, 'date', 10, FALSE, '', 'fas fa-pen'); 
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
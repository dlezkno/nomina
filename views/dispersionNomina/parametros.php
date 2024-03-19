<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$Referencia = $data['reg']['Referencia'];
	$SelectPeriodicidad = getSelect('Periodicidad', $data['reg']['Periodicidad'], '', 'PARAMETROS.Valor');
	$Periodo = $data['reg']['Periodo'];
	$Ciclo = $data['reg']['Ciclo'];

	$bancos = getTabla('BANCOS', '', 'BANCOS.Banco');

	$SelectBanco = '';

	for ($i = 0; $i < count($bancos); $i++) 
	{ 
		if	($bancos[$i]['id'] == $data['reg']['IdBanco'])
			$SelectBanco .= '<option selected value=' . $bancos[$i]['id'] . '>' . trim($bancos[$i]['nombre']) . '</option>';
		else
			$SelectBanco .= '<option value=' . $bancos[$i]['id'] . '>' . trim($bancos[$i]['nombre']) . '</option>';
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
									<h3 class="white-text">Dispersión de Nómina</h3>
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
											<p>DISPERSIÓN DE NÓMINA</p>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m6">
											<?php 
												get(label('Referencia*'), 'Referencia', $Referencia, 'number', 5, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
										<div class="col s12 m6">
											<?php 
												get(label('Periodicidad*'), 'Periodicidad', $SelectPeriodicidad, 'select', 0, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m4">
											<?php 
												get(label('Período*'), 'Periodo',  $Periodo, 'number', 5, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
										<div class="col s12 m4">
											<?php 
												get(label('Ciclo*'), 'Ciclo',  $Ciclo, 'number', 5, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m4">
											<?php 
												get(label('Banco*'), 'IdBanco', $SelectBanco, 'select', 0, FALSE, '', 'fas fa-pen'); 
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
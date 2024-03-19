<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$SelectEstadoEmpleado = getSelect('EstadoEmpleado', $data['reg']['EstadoEmpleado'], '', 'PARAMETROS.Valor');
	$SelectTipoPlantilla = getSelect('TipoPlantilla', $data['reg']['TipoPlantilla'], '', 'PARAMETROS.Valor');
	$SelectTipoContrato = getSelect('TipoContrato', $data['reg']['TipoContrato'], '', 'PARAMETROS.Valor');
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
									<h3 class="white-text">Plantillas</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Estado empleado*'), 'EstadoEmpleado', $SelectEstadoEmpleado, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Tipo de plantilla*'), 'TipoPlantilla', $SelectTipoPlantilla, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Tipo contrato'), 'TipoContrato', $SelectTipoContrato, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m12">
									<?php 
										get(label('Asunto*'), 'Asunto', $data['reg']['Asunto'], 'text', 255, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m12">
									<?php 
										get(label('Plantilla*'), 'Plantilla', $data['reg']['Plantilla'], 'textarea', 5, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Código documento*'), 'CodigoDocumento', $data['reg']['CodigoDocumento'], 'text', 40, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
							</div>
						</div>
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

<?php require_once('views/templates/footer.php'); ?>
<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$SelectTipoNumeracion = getSelect('TipoNumeracion', $data['reg']['TipoNumeracion'], '', 'PARAMETROS.Valor');
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
									<h3 class="white-text">Tipos de documentos</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Tipo de documento*'), 'TipoDocumento', $data['reg']['TipoDocumento'], 'text', 5, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Nombre del comprobante*'), 'Nombre', $data['reg']['Nombre'], 'text', 40, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Tipo de numeración*'), 'TipoNumeracion', $SelectTipoNumeracion, 'select', 0, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Prefijo'), 'Prefijo', $data['reg']['Prefijo'], 'text', 5, FALSE, '', 'fas fa-pen'); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Secuencia'), 'Secuencia', $data['reg']['Secuencia'], 'number', 10, FALSE, '', 'fas fa-pen'); 
									?>
								</div>
							</div>
						</div>
						<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
							<?php if ( $data['mensajeError'] ): ?>
							<div class="row">
								<div class="col s12">
									<h6 class="orange-text">
										<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por favor valídelas:
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
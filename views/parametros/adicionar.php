<?php 
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
									<h3 class="white-text">Parámetros</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Parámetro*'), 'Parametro', $data['reg']['Parametro'], 'text', 40, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Detalle*'), 'Detalle', $data['reg']['Detalle'], 'text', 100, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Valor'), 'Valor', $data['reg']['Valor'], 'number', 2, FALSE, 'required', 'fas fa-hashtag'); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Valor 2'), 'Valor2', $data['reg']['Valor2'], 'number', 16, FALSE, 'required', 'fas fa-hashtag'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Texto'), 'Texto', $data['reg']['Texto'], 'textarea', 3, FALSE, 'required', 'fas fa-hashtag'); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Fecha'), 'Fecha', $data['reg']['Fecha'], 'date', 10, FALSE, 'required', 'far fa-calendar'); 
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
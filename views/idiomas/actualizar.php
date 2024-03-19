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
									<h3 class="white-text">Idiomas</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<input type="hidden" name="id" value="<?= $data['reg']['id'] ?>" />
							<div class="row">
								<div class="input-field col s12 m6">
									<input id="idioma" name="idioma" type="text" class="validate"
										value="<?= $data['reg']['idioma'] ?>">
									<label for="idioma">Idioma*</label>
								</div>
								<div class="input-field col s12 m6">
									<input id="nombre" name="nombre" type="text" class="validate"
										value="<?= $data['reg']['nombre'] ?>">
									<label for="nombre">Nombre*</label>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s6 m6">
									<input id="orden" name="orden" type="number" class="validate"
										value="<?= $data['reg']['orden'] ?>">
									<label for="orden">Orden</label>
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
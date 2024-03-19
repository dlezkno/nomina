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
									<h3 class="white-text">Países</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="input-field col s12 m6">
									<input id="Nombre1" name="Nombre1" type="text" class="validate"
										value="<?= $data['reg']['Nombre1'] ?>">
									<label for="Nombre1">Nombre en español*</label>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<input id="Nombre2" name="Nombre2" type="text" class="validate"
										value="<?= $data['reg']['Nombre2'] ?>">
									<label for="Nombre2">Nombre en inglés*</label>
								</div>
								<div class="input-field col s12 m6">
									<input id="Nombre3" name="Nombre3" type="text" class="validate"
										value="<?= $data['reg']['Nombre3'] ?>">
									<label for="Nombre3">Nombre en francés*</label>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<input id="Iso2" name="Iso2" type="text" class="validate"
										value="<?= $data['reg']['Iso2'] ?>">
									<label for="Iso2">Iso - 2*</label>
								</div>
								<div class="input-field col s12 m6">
									<input id="Iso3" name="Iso3" type="text" class="validate"
										value="<?= $data['reg']['Iso3'] ?>">
									<label for="Iso3">Iso - 3*</label>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<input id="PhoneCode" name="PhoneCode" type="text" class="validate"
										value="<?= $data['reg']['PhoneCode'] ?>">
									<label for="PhoneCode">Phone code*</label>
								</div>
								<div class="input-field col s12 m6">
									<input id="Orden" name="Orden" type="number" class="validate"
										value="<?= $data['reg']['Orden'] ?>">
									<label for="Orden">Orden</label>
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
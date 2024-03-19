<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$SelectTipoId = getSelect('TipoIdentificacion', $data['reg']['tipoid'], '', 'PARAMETROS.Valor');
	$SelectPerfil = getSelect('Perfil', $data['reg']['perfil'], '', 'PARAMETROS.Valor');

	$ciudades = getTabla('CIUDADES', '', 'CIUDADES.Orden,CIUDADES.Nombre');

	$SelectCiudad = '';
	
	for ($i = 0; $i < count($ciudades); $i++) 
	{ 
		if	($ciudades[$i]['id'] == $data['reg']['idciudad'])
			$SelectCiudad .= '<option selected value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
		else
			$SelectCiudad .= '<option value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
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
									<h3 class="white-text">Usuarios</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<section class="tabs-vertical mt-1 section">
								<div class="row">
									<div class="col l3 s12">
										<div class="card-panel">
											<ul class="tabs">
												<li class="tab">
													<a href="#pagUsuario">
														<i class="material-icons">error_outline</i>
														<span>Usuario</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagContacto">
														<i class="material-icons">brightness_low</i>
														<span>Contacto</span>
													</a>
												</li>
											</ul>
										</div>
									</div>

									<div class="col s12 l9">
										<div id="pagUsuario" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>USUARIO</p>
													</div>
												</div>
												<input type="hidden" name="id" value="<?= $data['reg']['id'] ?>" />
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Usuario*'), 'Usuario', $data['reg']['usuario'], 'text', 10, FALSE, 'required', 'far fa-user-circle'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Nombre*'), 'Nombre', $data['reg']['nombre'], 'text', 60, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php
															get(label('Tipo de identificación*'), 'TipoId', $SelectTipoId, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Documento*'), 'Documento', $data['reg']['documento'], 'number', 12, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m12">
														<?php 
															get(label('E-Mail*'), 'Email', $data['reg']['email'], 'email', 100, FALSE, 'required', 'fas fa-paper-plane	'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Perfil*'), 'Perfil', $SelectPerfil, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Vigencia contraseña*'), 'Vigencia', $data['reg']['vigencia'], 'number', 2, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
												</div>
											</div>
										</div>

										<div id="pagContacto" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>CONTACTO</p>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Dirección'), 'Direccion', $data['reg']['direccion'], 'text', 60, FALSE, '', 'fas fa-map-marker-alt'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php
															get(label('Ciudad'), 'IdCiudad', $SelectCiudad, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
														?>
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12 m6">
														<?php 
															get(label('Teléfono'), 'Telefono', $data['reg']['telefono'], 'tel', 15, FALSE, 'required', 'fas fa-phone'); 
														?>
													</div>
													<div class="input-field col s12 m6">
														<?php 
															get(label('Celular'), 'Celular', $data['reg']['celular'], 'tel', 15, FALSE, '', 'fas fa-phone'); 
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
			</div>
		</div>
	</div>
</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
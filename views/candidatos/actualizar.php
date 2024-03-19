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
									<h3 class="white-text">Candidatos</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Documento identidad*'), 'Documento', $data['reg']['Documento'], 'text', 20, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Primer apellido*'), 'Apellido1', $data['reg']['Apellido1'], 'text', 25, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Segundo apellido'), 'Apellido2', $data['reg']['Apellido2'], 'text', 25, FALSE, '', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Primer nombre*'), 'Nombre1', $data['reg']['Nombre1'], 'text', 25, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Segundo nombre'), 'Nombre2', $data['reg']['Nombre2'], 'text', 25, FALSE, '', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('E-mail*'), 'Email', $data['reg']['Email'], 'email', 100, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Celular*'), 'Celular', $data['reg']['Celular'], 'tel', 15, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Reenviar correo bienvenida'), 'ReenviarCorreo', FALSE, 'checkbox', 0, FALSE, '', ''); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Candidato desiste'), 'CandidatoDesiste', FALSE, 'checkbox', 0, FALSE, '', ''); 
									?>
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
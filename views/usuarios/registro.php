<?php 
	require_once('views/templates/header.php');
	// require_once('views/templates/sideBar.php');
?>
<div id="main">
	<div class="row">
		<div class="col s2 m3">
		</div>
		<div class="col s8 m6 l4 z-depth-4 card-panel border-radius-6 register-card bg-opacity-8">
			<div class="row">
				<div class="input-field col s12">
					<h5 class="ml-4">Registro de usuario Administrador</h5>
				</div>
				<div class="input-field col s12">
					<?php get(label('Usuario*'), 'Usuario', $data['reg']['Usuario'], 'text', 10, FALSE, 'required', 'far fa-user-circle'); ?>
				</div>
				<div class="input-field col s12">
					<?php get(label('Nombre*'), 'Nombre', $data['reg']['Nombre'], 'text', 60, FALSE, 'required', 'fas fa-pen'); ?>
				</div>
				<div class="input-field col s12">
					<?php get(label('E-mail*'), 'Email', $data['reg']['Email'], 'email', 100, FALSE, 'required', 'far fa-envelope'); ?>
				</div>
				<div class="input-field col s12">
					<?php get(label('Contraseña*'), 'Contrasena', $data['reg']['Contrasena'], 'password', 60, FALSE, 'required', 'fas fa-lock'); ?>
				</div>
				<div class="input-field col s12">
					<?php get(label('Valide contraseña*'), 'Contrasena2', '', 'password', 60, FALSE, 'required', 'fas fa-lock'); ?>
				</div>
			</div>

			<?php if ( $data['mensajeError'] ): ?>
			<div class="row">
				<div class="col s12">
					<h6 class="orange-text">
						<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por favor valídelas:
					</h6>
					<?= $data['mensajeError'] ?>
				</div>
			</div>
			<?php endif; ?>

			<div class="row">
				<div class="input-field col s12">
					<p class="margin medium-small">
						<a href="<?= SERVERURL ?>/login/login">Ya tienes una cuenta? Ingreso</a>
					</p>
				</div>
			</div>
		</div>
		<div class="col s2 m3">
		</div>
	</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
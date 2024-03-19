<?php 
	require_once('views/templates/header.php');
	// require_once('views/templates/sideBar.php');
?>
<div id="main">
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<div class="row">
		<div class="col s2 m3">
		</div>
		<div class="col s8 m6 l4 z-depth-4 card-panel border-radius-6 register-card">
			<div class="row">
				<div class="input-field col s12">
					<h5 class="ml-4">Portal Gestión Humana</h5>
				</div>
				<?php
					if(MANTEIN == true){
						echo '<strong > <h4 style="color:red;text-align: center;">PLATAFORMA EN MANTENIMIENTO </h4></strong>';
					}
				?>
				<div class="input-field col s12">
					<?php get(label('Usuario o correo electrónico*'), 'Usuario', $data['reg']['Usuario'], 'text', 100, FALSE, 'required', 'far fa-user-circle'); ?>
				</div>
				<div class="input-field col s12">
					<?php get(label('Contraseña*'), 'Contrasena', $data['reg']['Contrasena'], 'password', 60, FALSE, 'required', 'fas fa-lock'); ?>
				</div>
			</div>
			<div class="container-login100-form-btn center-align">
				<button submit class="btn cyan darken-4 white-text border-round mr-1 mb-1">
					Ingresar
				</button>
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

			<ul class="login-more p-t-50">
				<li class="m-b-8">
					<span class="txt1">
						Olvidó
					</span>
					<a href="<?= SERVERURL ?>/login/forgot" class="txt2">Usuario / Contraseña?</a>
				</li>
			</ul>
		</div>
		<div class="col s2 m3">
		</div>
	</div>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<?php require_once('views/templates/footer.php'); ?>
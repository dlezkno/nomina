<?php 
	require_once('views/templates/header.php');
	// require_once('views/templates/sideBar.php');
?>
<div id="main">
	<div id="login-page" class="row">
		<div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
			<div class="row">
				<div class="input-field col s12">
					<h5 class="ml-4">Portal Gestión Humana</h5>
				</div>
			</div>
			<div class="row margin">
				<?php 
					get(label('Usuario o correo electrónico*'), 'Usuario', $data['reg']['Usuario'], 'text', 100, FALSE, 'required', 'far fa-user-circle'); 
				?>
			</div>
			<div class="row margin">
				<?php 
					get(label('Contraseña*'), 'Contrasena', $data['reg']['Contrasena'], 'password', 60, FALSE, 'required', 'fas fa-lock'); 
				?>
			</div>
			<div class="row">
				<div class="col s12 m12 l12 ml-2 mt-1">
					<p>
						<label>
							<input type="checkbox">
							<span>Remember Me</span>
						</label>
					</p>
				</div>
			</div>
			<div class="container-login100-form-btn center-align">
				<button submit class="btn cyan darken-4 white-text border-round mr-1 mb-1">
					Ingresar
				</button>
			</div>
			<div class="row">
				<div class="input-field col s6 m6 l6">
					<p class="margin medium-small"><a href="user-register.html">Register Now!</a></p>
				</div>
				<div class="input-field col s6 m6 l6">
					<p class="margin right-align medium-small"><a href="user-forgot-password.html">Forgot password ?</a>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
<?php require_once('views/templates/footer.php'); ?>
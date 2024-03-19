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
					<h5 class="ml-4">Actualizar contraseña</h5>
				</div>
                <?php if ( !isset($data["nuewPass"]) ): ?>                
                    <div class="input-field col s12">
                        <?php get(label('Correo electrónico*'), 'Usuario', $data['reg']['Usuario'], 'text', 100, FALSE, 'required', 'far fa-user-circle'); ?>
                    </div>
                    <?php if ( isset($data['code']) ): ?>
                    <div class="input-field col s12">
                        <?php get(label('Codigo*'), 'Codigo', '', 'text', 100, FALSE, 'required', 'far fa-user-circle'); ?>
                    </div>
                    <h6 class="orange-text">
                        <strong>Advertencia!</strong> se envio un codigo de verificacion a tu correo:
                    </h6>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (isset($data["nuewPass"]) ): ?> 
                    <div class="input-field col s12">
                        <?php get(label('Correo electrónico*'), 'Usuario', $data['reg']['Usuario'], 'text', 100, FALSE, 'required', 'far fa-user-circle'); ?>
                    </div> 
                    <div class="input-field col s12">
                        <?php get(label('contraseña *'), 'nuevaContrasena', '', 'password', 100, FALSE, 'required', 'far fa-user-circle'); ?>
                    </div>
                    <div class="input-field col s12">
                        <?php get(label('Repetir contraseña*'), 'repiteNuevaContrasena', '', 'password', 100, FALSE, 'required', 'far fa-user-circle'); ?>
                    </div>
                <?php endif; ?>  
			</div>
			<div class="container-login100-form-btn center-align">
				<button submit class="btn cyan darken-4 white-text border-round mr-1 mb-1">
					Enviar
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
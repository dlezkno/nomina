<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	
	$SelectTipoContrato = getSelect('TipoContrato', '', '', 'PARAMETROS.Valor');
?>


<div id="main">
	<?php if ( isset($data['mensajeError']) ): ?>
		<?php if ( $data['mensajeError'] ): ?>
			<div class="card-content red white-text z-depth-2">
				<div class="row" id="mensajeError">
					<div class="col s12">
						<h5 class="white-text">
							<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por favor valídelas:
						</h5>
						<br>
						<h5 class="white-text">
						<?= $data['mensajeError'] ?>
						</h5>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<div class="container">
		<div class="row">
			<h3 style="text-align:center">SOLICITUD DE PERSONAL (Beta)</h3>
		</div>
		<div class="row">

			<div class="input-field col s12 m6">
				<input type="hidden" name="IdProyecto" id="IdProyecto" value="">
				<?php 
					get(label('Proyecto *'), 'NombreProyecto', "", 'text', 60, FALSE, '', 'textsms'); 
				?>
				<div id="suggestionsProyecto"></div>
			</div>

			<div class="input-field col s12 m6">
				<input type="hidden" name="IdCentro" id="IdCentro" value="">
				<?php 
					get(label('Centro de costos *'), 'NombreCentro', "", 'text', 60, FALSE, '', 'textsms'); 
				?>
				<div id="suggestionsCentro"></div>
			</div>			
				
		</div>
		<div class="row">

			<div class="input-field col s12 m6">
				<input type="hidden" name="IdCargo" id="IdCargo" value="">
				<?php 
					get(label('Cargo a suplir *'), 'NombreCargo', "", 'text', 60, FALSE, '', 'textsms'); 
				?>
				<div id="suggestionsCargo"></div>
			</div>

			<div class="input-field col s12 m3">
				<?php 
					get(label('Cantidad de vacantes *'), 'cantidad', "", 'number', 12, FALSE, '', 'textsms'); 
				?>
			</div>

			<div class="input-field col s12 m3">
				<?php
					$tipo = '<option value="Reemplazo">Reemplazo</option><option value="Nuevo">Nuevo</option>';
					get(label('Tipo de vacante *'), 'tipovacante', $tipo, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
				?>
			</div>
			
		</div>



		<div class="row">

			<div class="input-field col s12 m6">
				<?php 
					get(label('Tipo de contrato*'), 'TipoContrato', $SelectTipoContrato, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
				?>
			</div>

			<div class="input-field col s12 m3">
				<?php 
					get(label('Tiene bonificacion *'), 'tienebono', '', 'checkbox', '', FALSE, '', '');
				?>
			</div>

			<div class="input-field col s12 m3">
				<?php 
					get(label('Valor bonificacion * '), 'valorbono', '', 'number', 12, FALSE, '', 'fas fa-edit'); 
				?>
			</div>


		</div>

		<div class="row">

			<div class="input-field col s12 m3">
				<input type="hidden" name="IdSede" id="IdSede" value="">
				<?php 
					get(label('Sede*'), 'NombreSede', "", 'text', 60, FALSE, '', 'textsms'); 
				?>
				<div id="suggestionsSede"></div>
			</div>
			<div class="input-field col s12 m3">
				<input type="hidden" name="IdCiudad" id="IdCiudad" value="">
					<?php 
						get(label('Ciudad*'), 'Ciudad', "", 'text', 25, FALSE, '', 'textsms'); 
					?>
					<div id="suggestionsCiudad"></div>
				</div>
			</dvi>




			<div class="input-field col s12 m2">
				<?php 
					get(label('Requiere capacitacion *'), 'capacitacion', '', 'checkbox', '', FALSE, '', '');
				?>
			</div>

			<div class="input-field col s12 m2">
				<?php 
					get(label('Ini *'), 'FechaInicioCapacitacion', "", 'date', 0, FALSE, '', 'fas fa-calendar'); 
				?>
			</div>
			<div class="input-field col s12 m2">
				<?php 
					get(label('Fin *'), 'FechaFincioCapacitacion', "", 'date', 0, FALSE, '', 'fas fa-calendar'); 
				?>
			</div>
		
			

		</div>



		<div class="row">

			<div class="input-field col s12 m6">
				<?php 
					get(label('Fecha tentativa de ingreso*'), 'FechaIngreso', "", 'date', 0, FALSE, '', 'fas fa-calendar'); 
				?>
			</div>
			
			<div class="input-field col s12 m2">
				<label>Rango salarial *</label>
			</div>
			<div class="input-field col s12 m2">
				<?php 
					get(label('Min * '), 'salariominimo', '', 'number', 12, FALSE, '', 'fas fa-edit'); 
				?>
			</div>
			<div class="input-field col s12 m2">
				<?php 
					get(label('Max *'), 'salariomaximo', '', 'number', 12, FALSE, '', 'fas fa-edit'); 
				?>
			</div>
			

		</div>

		<div class="row">

			<div class="input-field col s12 m12">
				<?php 
					get(label('Caracteristicas del candidato *'), 'Caracteristicas', "", 'textarea', 2, FALSE, '', 'fas fa-edit'); 
				?>
			</div>

		</div>
		<div class="row">
			<div class="input-field col s12 m6">
				<button class="btn btn-sm cyan darken-4" type="submit"
					name="Action" value="GUARDAR_SOLICITUD">
					GUARDAR SOLICITUD
				</button>
			</div>
		</div>
	</div>	
</div>



<?php require_once('views/templates/footer.php'); ?>

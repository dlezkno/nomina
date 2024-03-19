<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	if (isset($_REQUEST['Documento']))
	{ 
		$Documento = $_REQUEST['Documento'];
		$NombreEmpleado = $_REQUEST['NombreEmpleado'];
		$Cargo = $_REQUEST['Cargo'];
	}
	else
	{
		$Documento = '';
		$NombreEmpleado = '';
		$Cargo = '';
	}

	$SelectTipoEmpleado = getSelect('TipoEmpleado', $data['reg']['TipoEmpleado'], '', 'PARAMETROS.Valor');
	$SelectVicepresidencia = getSelect('Vicepresidencia', $data['reg']['Vicepresidencia'], '', 'PARAMETROS.Valor');

	$cDirectorio = SERVERURL . '/documents/';
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
									<h3 class="white-text">Centros de costos</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="input-field col s12 m6">
									<?php get(label('Centro de costos*'), 'Centro', $data['reg']['Centro'], 'text', 10, FALSE, 'required', 'fas fa-pen'); ?>
								</div>
								<div class="input-field col s12 m6">
									<?php get(label('Nombre del centro de costos*'), 'Nombre', $data['reg']['Nombre'], 'text', 40, FALSE, 'required', 'fas fa-pen'); ?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Fecha vencimiento'), 'FechaVencimiento', $data['reg']['FechaVencimiento'], 'date', 10, FALSE, '', ''); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Tipo de empleado*'), 'TipoEmpleado', $SelectTipoEmpleado, 'select', 0, FALSE, '', ''); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 m2">
									<?php get(label('Empleado'), 'Documento', $Documento, 'text', 15, FALSE, 'onblur="ConsultaEmpleado2(this.value); return false"', ''); ?>
								</div>
								<div class="col s12 m3">
									<?php get('', 'NombreEmpleado', $NombreEmpleado, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s12 m3">
									<?php get('', 'Cargo', $Cargo, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s12 m1">
									<div class="row center-align">
										<img id="ImagenEmpleado" name="ImagenEmpleado" src="<?= $cDirectorio ?>"
											alt="Fotografia" class="circle responsive-img" width="50px" hidden>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Vicepresidencia'), 'Vicepresidencia', $SelectVicepresidencia, 'select', 0, FALSE, '', ''); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="card-alert card cyan darken-4">
									<div class="card-content white-text">
										<p>EMPLEADOS POR CENTRO DE COSTO</p>
									</div>
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
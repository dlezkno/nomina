<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	if (isset($_REQUEST['Documento']))
	{ 
		$Documento 			= $_REQUEST['Documento'];
		$NombreEmpleado 	= $_REQUEST['NombreEmpleado'];
		$Cargo 				= $_REQUEST['Cargo'];
		$Centro			 	= $_REQUEST['Centro'];
	}

	$CuotaVivienda 				= $_REQUEST['CuotaVivienda'];
	$SaludYEducacion 			= $_REQUEST['SaludYEducacion'];
	$Alimentacion 				= $_REQUEST['Alimentacion'];
	$DeduccionDependientes 		= $_REQUEST['DeduccionDependientes'];
	$FechaInicialDeducciones 	= $_REQUEST['FechaInicialDeducciones'];
	$FechaFinalDeducciones 		= $_REQUEST['FechaFinalDeducciones'];

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
									<h3 class="white-text">Deducciones Ret.Fte.</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s2">
									<?php get(label('Empleado*'), 'Documento', $Documento, 'text', 15, FALSE, 'onblur="ConsultaEmpleado(this.value); return false" required', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'NombreEmpleado', $NombreEmpleado, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'Cargo', $Cargo, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'Centro', $Centro, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s1">
									<div class="row center-align">
										<img id="ImagenEmpleado" name="ImagenEmpleado" src="<?= $cDirectorio ?>"
											alt="Fotografia" class="circle responsive-img" width="50px" hidden>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col s3">
									<?php get(label('Cuota vivienda'), 'CuotaVivienda', $CuotaVivienda, 'number', 10, FALSE, '', ''); ?>
								</div>
								<div class="col s3">
									<?php get(label('Salud'), 'SaludYEducacion', $SaludYEducacion, 'number', 10, FALSE, '', ''); ?>
								</div>
								<div class="col s3">
									<?php get(label('Alimentación'), 'Alimentacion', $Alimentacion, 'number', 10, FALSE, '', ''); ?>
								</div>
								<div class="col s3">
									<?php get(label('Deducción dependientes'), 'DeduccionDependientes', $DeduccionDependientes, 'checkbox', $DeduccionDependientes, FALSE, '', ''); ?>
								</div>
							</div>
							<div class="row">
								<div class="col s3">
									<?php get(label('Fecha inicial'), 'FechaInicialDeducciones', $FechaInicialDeducciones, 'date', 10, FALSE, '', ''); ?>
								</div>
								<div class="col s3">
									<?php get(label('Fecha final'), 'FechaFinalDeducciones', $FechaFinalDeducciones, 'date', 10, FALSE, '', ''); ?>
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
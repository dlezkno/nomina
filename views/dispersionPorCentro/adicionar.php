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

	if (isset($_REQUEST['Centro'])) 
	{
		$Centro = $_REQUEST['Centro'];
		$NombreCentro = $_REQUEST['NombreCentro'];
	}
	else
	{
		$Centro = '';
		$NombreCentro = '';
	}

	if (isset($_REQUEST['Porcentaje'])) 
		$Porcentaje = $_REQUEST['Porcentaje'];
	else
		$Porcentaje = 0;

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
									<h3 class="white-text">Dispersión por centro</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<h6 class="white-text"><?= 'PERÍODO: ' . $data['reg']['Periodo'] . '-' . $data['reg']['Ciclo'] ?></h6>
									<h6 class="white-text"><?= 'DESDE: ' . $data['reg']['FechaInicial'] . ' - ' . $data['reg']['FechaFinal'] ?></h6>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s2">
									<?php get(label('Empleado*'), 'Documento', $Documento, 'text', 15, FALSE, 'onblur="ConsultaEmpleado2(this.value); return false" required', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'NombreEmpleado', $NombreEmpleado, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'Cargo', $Cargo, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s1">
									<div class="row center-align">
										<img id="ImagenEmpleado" name="ImagenEmpleado" src="<?= $cDirectorio ?>"
											alt="Fotografia" class="circle responsive-img" width="50px" hidden>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col s2">
									<?php get(label('Centro de costos*'), 'Centro', $Centro, 'text', 5, FALSE, 'onblur="ConsultaCentro(this.value); return false" required', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'NombreCentro', $NombreCentro, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s1">
									<?php get(label('Porcentaje*'), 'Porcentaje', $data['reg']['Porcentaje'], 'number', 8, FALSE, '', ''); ?>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s3"><strong>Centro</strong></div>
								<div class="col s3"><strong>Nombre centro</strong></div>
								<div class="col s2 right-align"><strong>Porcentaje</strong></div>
								<div class="col s1"></div>
							</div>
							<hr/>
							<?php for ($i = 0; $i < count($data['Nov']); $i++): ?>
							<div class="row">
								<div class="col s3"><?= $data['Nov'][$i]['Centro'] ?></div>
								<div class="col s3"><?= $data['Nov'][$i]['NombreCentro'] ?></div>
								<div class="col s2 right-align"><?= number_format($data['Nov'][$i]['Porcentaje'], 0) ?></div>
								<div class="col s1">
									<div class="row center-align">
										<img id="ImagenEmpleado" name="ImagenEmpleado" src="<?= $cDirectorio ?>"
											alt="Fotografia" class="circle responsive-img" width="50px" hidden>
									</div>
								</div>
							</div>
							<hr />
							<?php endfor; ?>
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
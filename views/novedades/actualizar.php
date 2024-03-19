<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	if (isset($_REQUEST['Documento']))
	{ 
		$Documento = $_REQUEST['Documento'];
		$NombreEmpleado = $_REQUEST['NombreEmpleado'];
		$Cargo = $_REQUEST['Cargo'];
		$Centro = $_REQUEST['Centro'];
	}

	if (isset($_REQUEST['Concepto'])) 
	{
		$Concepto = $_REQUEST['Concepto'];
		$NombreConcepto = $_REQUEST['NombreConcepto'];
	}

	if (isset($_REQUEST['Tercero'])) 
	{
		$Tercero = $_REQUEST['Tercero'];
		$NombreTercero = $_REQUEST['NombreTercero'];
	}

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
									<h3 class="white-text">Novedades</h3>
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
								<div class="col s2">
									<?php get(label('Concepto*'), 'Concepto', $Concepto, 'text', 5, FALSE, 'onblur="ConsultaConcepto(this.value); return false" required', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'NombreConcepto',$NombreConcepto, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s1">
									<?php get(label('Horas'), 'Horas', $data['reg']['Horas'], 'number', 8, FALSE, '', ''); ?>
								</div>
								<div class="col s2">
									<?php get(label('Valor'), 'Valor', $data['reg']['Valor'], 'number', 12, FALSE, '', ''); ?>
								</div>
								<div class="col s1">
									<?php get(label('Fecha inicial'), 'FechaInicial', $data['reg']['FechaInicial'], 'date', 10, FALSE, '', ''); ?>
								</div>
								<div class="col s1">
									<?php get(label('Fecha final'), 'FechaFinal', $data['reg']['FechaFinal'], 'date', 10, FALSE, '', ''); ?>
								</div>
							</div>
							<div class="row">
								<div class="col s2">
									<?php get(label('Tercero'), 'Tercero', $Tercero, 'text', 10, FALSE, 'onblur="ConsultaTercero(this.value); return false" required', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'NombreTercero', $NombreTercero, 'text', 60, TRUE, '', ''); ?>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s1"><strong>Empleado</strong></div>
								<div class="col s2"><strong>Nombre</strong></div>
								<div class="col s2"><strong>Cargo</strong></div>
								<div class="col s2"><strong>Centro</strong></div>
								<div class="col s2"><strong>Concepto</strong></div>
								<div class="col s1 right-align"><strong>Horas</strong></div>
								<div class="col s1 right-align"><strong>Valor</strong></div>
								<div class="col s1"></div>
							</div>
							<hr/>
							<?php for ($i = 0; $i < count($data['Nov']); $i++): ?>
							<div class="row">
								<div class="col s1">
									<?= $data['Nov'][$i]['Documento'] ?>
								</div>
								<div class="col s2">
									<?= $data['Nov'][$i]['Apellido1'] . ' ' . $data['Nov'][$i]['Apellido2'] . ' ' . $data['Nov'][$i]['Nombre1'] . ' ' . $data['Nov'][$i]['Nombre2'] ?>
								</div>
								<div class="col s2">
									<?= $data['Nov'][$i]['NombreCargo'] ?>
								</div>
								<div class="col s2">
									<?= $data['Nov'][$i]['NombreCentro'] ?>
								</div>
								<div class="col s2">
									<?= $data['Nov'][$i]['NombreConcepto'] ?>
								</div>
								<div class="col s1 right-align">
									<?php
										if (is_null($data['Nov'][$i]['FechaInicial'])) 
											echo number_format($data['Nov'][$i]['Horas'], 0);
										else
											echo $data['Nov'][$i]['FechaInicial'];
									?>
								</div>
								<div class="col s1 right-align">
									<?php
										if (is_null($data['Nov'][$i]['FechaFinal'])) 
											echo number_format($data['Nov'][$i]['Valor'], 0);
										else
											echo $data['Nov'][$i]['FechaFinal'];
									?>
								</div>
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
										<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por
										favor
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
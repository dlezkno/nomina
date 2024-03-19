<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	if (isset($_REQUEST['Documento']))
	{ 
		$Documento 		= $_REQUEST['Documento'];
		$NombreEmpleado = $_REQUEST['NombreEmpleado'];
		$Cargo 			= $_REQUEST['Cargo'];
		$Centro 		= $_REQUEST['Centro'];
	}

	if (isset($_REQUEST['Concepto'])) 
	{
		$Concepto 		= $_REQUEST['Concepto'];
		$NombreConcepto = $_REQUEST['NombreConcepto'];
	}

	$FechaIncapacidad 	= $_REQUEST['FechaIncapacidad'];
	$FechaInicio 		= $_REQUEST['FechaInicio'];
	$DiasIncapacidad 	= $_REQUEST['DiasIncapacidad'];
	$PorcentajeAuxilio 	= $_REQUEST['PorcentajeAuxilio'];

	if (isset($_REQUEST['Diagnostico'])) 
	{
		$Diagnostico 		= $_REQUEST['Diagnostico'];
		$NombreDiagnostico 	= $_REQUEST['NombreDiagnostico'];
	}
	else
	{
		$Diagnostico 		= '';
		$NombreDiagnostico 	= '';
	}

	$EsProrroga 		= $_REQUEST['EsProrroga'];

	$SelectBaseLiquidacion 	= getSelect('BaseLiquidacionIncapacidad', $_REQUEST['BaseLiquidacion'], '', 'PARAMETROS.Valor');

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
									<h3 class="white-text">Incapacidades</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12 m2">
									<?php get(label('Empleado*'), 'Documento', $Documento, 'text', 15, FALSE, 'onblur="ConsultaEmpleado(this.value); return false" required', ''); ?>
								</div>
								<div class="col s12 m3">
									<?php get('Nombre empleado', 'NombreEmpleado', $NombreEmpleado, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s12 m3">
									<?php get('Cargo', 'Cargo', $Cargo, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s12 m3">
									<?php get('Centro de costos', 'Centro', $Centro, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s12 m1">
									<div class="row center-align">
										<img id="ImagenEmpleado" name="ImagenEmpleado" src="<?= $cDirectorio ?>"
											alt="Fotografia" class="circle responsive-img" width="50px" hidden>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col s12 m2">
									<?php get(label('Concepto*'), 'Concepto', $Concepto, 'text', 5, FALSE, 'onblur="ConsultaConceptoIncap(this.value); return false" required', ''); ?>
								</div>
								<div class="col s12 m3">
									<?php get('Descripción', 'NombreConcepto',$NombreConcepto, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s12 m3">
									<?php get(label('Fecha incapacidad'), 'FechaIncapacidad', $FechaIncapacidad, 'date', 10, FALSE, '', ''); ?>
								</div>
								<div class="col s12 m3">
									<?php get(label('Fecha inicio'), 'FechaInicio', $FechaInicio, 'date', 10, FALSE, '', ''); ?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 m2">
								</div>
								<div class="col s12 m3">
									<?php get(label('Es prórroga'), 'EsProrroga', $EsProrroga, 'checkbox', $EsProrroga, FALSE, '', ''); ?>
								</div>
								<div class="col s12 m2">
									<?php get(label('Días de incapacidad'), 'DiasIncapacidad', $DiasIncapacidad, 'number', 3, FALSE, '', ''); ?>
								</div>
								<div class="col s12 m2">
									<?php get(label('Porcentaje de auxilio'), 'PorcentajeAuxilio', $PorcentajeAuxilio, 'number', 6, FALSE, '', ''); ?>
								</div>
								<div class="col s12 m2">
									<?php get(label('Base liquidación*'), 'BaseLiquidacion', $SelectBaseLiquidacion, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); ?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 m2">
								</div>
								<div class="col s12 m3">
								</div>
								<div class="col s12 m1">
									<?php get(label('Diagnostico*'), 'Diagnostico', $Diagnostico, 'text', 10, FALSE, 'onblur="ConsultaDiagnostico(this.value); return false" required', ''); ?>
								</div>
								<div class="col s12 m5">
									<?php get(label('Descripción'), 'NombreDiagnostico', $NombreDiagnostico, 'text', 60, TRUE, '', ''); ?>
								</div>
							</div>
						</div>
						<div class="card-content">
							<table>
								<thead>
									<tr>
										<th>EMPLEADO</th>
										<th>NOMBRE</th>
										<th>CARGO</th>
										<th>CONCEPTO</th>
										<th>FECHA INC.</th>
										<th>FECHA INI.</th>
										<th>DÍAS INC.</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php for ($i = 0; $i < count($data['Nov']); $i++ ): ?>
									<tr>
										<td>
											<?= $data['Nov'][$i]['Documento'] ?>
										</td>
										<td>
											<?= $data['Nov'][$i]['Apellido1'] . ' ' . $data['Nov'][$i]['Apellido2'] . ' ' . $data['Nov'][$i]['Nombre1'] . ' ' . $data['Nov'][$i]['Nombre2'] ?>
										</td>
										<td>
											<?= $data['Nov'][$i]['NombreCargo'] ?>
										</td>
										<td>
											<?= $data['Nov'][$i]['NombreConcepto'] ?>
										</td>
										<td>
											<?= $data['Nov'][$i]['fechaincapacidad'] ?>
										</td>
										<td>
											<?= $data['Nov'][$i]['fechainicio'] ?>
										</td>
										<td>
											<?= number_format($data['Nov'][$i]['diasincapacidad'], 0) ?>
										</td>
										<td>
											<img id="ImagenEmpleado" name="ImagenEmpleado" src="<?= $cDirectorio ?>"
											alt="Fotografia" class="circle responsive-img" width="50px" hidden>
										</td>
									</tr>
									<?php endfor; ?>
								</tbody>
							</table>
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
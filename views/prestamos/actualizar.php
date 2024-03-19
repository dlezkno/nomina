<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$IdEstado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'ACTIVO'");

	$empleados = getTabla('EMPLEADOS', 'EMPLEADOS.Estado = ' . $IdEstado, 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2');

	$SelectEmpleado = '';
	
	for ($i=0; $i < count($empleados); $i++) 
	{ 
		if	($empleados[$i]['id'] == $data['reg']['IdEmpleado'])
			$SelectEmpleado .= '<option selected value=' . $empleados[$i]['id'] . '>' . $empleados[$i]['apellido1'] . ' ' . $empleados[$i]['apellido2'] . ' ' . $empleados[$i]['nombre1'] . ' ' . $empleados[$i]['nombre2'] . '</option>';
		else
			$SelectEmpleado .= '<option value=' . $empleados[$i]['id'] . '>' . $empleados[$i]['apellido1'] . ' ' . $empleados[$i]['apellido2'] . ' ' . $empleados[$i]['nombre1'] . ' ' . $empleados[$i]['nombre2'] . '</option>';
	}

	$IdTipoAuxiliar = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES PRÉSTAMO'");

	$conceptos = getTabla('AUXILIARES', 'AUXILIARES.TipoRegistroAuxiliar = ' . $IdTipoAuxiliar, 'AUXILIARES.Nombre');

	$SelectConcepto = '';
	
	for ($i=0; $i < count($conceptos); $i++) 
	{ 
		if	($conceptos[$i]['id'] == $data['reg']['IdConcepto'])
			$SelectConcepto .= '<option selected value=' . $conceptos[$i]['id'] . '>' . $conceptos[$i]['nombre'] . '</option>';
		else
			$SelectConcepto .= '<option value=' . $conceptos[$i]['id'] . '>' . $conceptos[$i]['nombre'] . '</option>';
	}

	$SelectTipoPrestamo = getSelect('TipoPrestamo', $data['reg']['TipoPrestamo'], '', 'PARAMETROS.Valor');
	$SelectEstadoPrestamo = getSelect('EstadoPrestamo', $data['reg']['Estado'], '', 'PARAMETROS.Valor');

	$terceros = getTabla('TERCEROS', '', 'TERCEROS.Nombre');

	$SelectTercero = '';
	
	for ($i=0; $i < count($terceros); $i++) 
	{ 
		if	($terceros[$i]['id'] == $data['reg']['IdTercero'])
			$SelectTercero .= '<option selected value=' . $terceros[$i]['id'] . '>' . $terceros[$i]['nombre'] . '</option>';
		else
			$SelectTercero .= '<option value=' . $terceros[$i]['id'] . '>' . $terceros[$i]['nombre'] . '</option>';
	}
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
									<h3 class="white-text">Préstamos a empleados</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Empleado*'), 'IdEmpleado', $SelectEmpleado, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); 
									?>
								</div>
								<div class="col s12 l6">
									<?php 
										get(label('Concepto*'), 'IdConcepto', $SelectConcepto, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); 
										?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Tipo préstamo*'), 'TipoPrestamo', $SelectTipoPrestamo, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); 
									?>
								</div>
								<div class="col s12 l6">
									<?php 
										get(label('Fecha*'), 'Fecha', $data['reg']['Fecha'], 'date', 10, FALSE, 'required', 'fas fa-calendar'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Valor préstamo*'), 'ValorPrestamo', $data['reg']['ValorPrestamo'], 'number', 12, FALSE, 'required', 'fas fa-edit'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Valor cuota'), 'ValorCuota', $data['reg']['ValorCuota'], 'number', 12, FALSE, '', 'fas fa-edit'); 
									?>
								</div>
								<div class="col s12 l6">
									<?php 
										get(label('Número de cuotas'), 'Cuotas', $data['reg']['Cuotas'], 'number', 3, FALSE, '', 'fas fa-edit'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Saldo préstamo'), 'SaldoPrestamo', $data['reg']['SaldoPrestamo'], 'number', 12, FALSE, '', 'fas fa-edit'); 
									?>
								</div>
								<div class="col s12 l6">
									<?php 
										get(label('Cuotas restantes'), 'SaldoCuotas', $data['reg']['SaldoCuotas'], 'number', 3, FALSE, '', 'fas fa-edit'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Estado préstamo*'), 'Estado', $SelectEstadoPrestamo, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
									?>
								</div>
								<div class="col s12 l6">
									<?php 
										get(label('Tercero'), 'IdTercero', $SelectTercero, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); 
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
				<?php if ( $data['mensajeError'] ): ?>
				<div class="row">
					<div class="col s12">
						<h6 class="orange-text">
							<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por favor
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

<?php require_once('views/templates/footer.php'); ?>
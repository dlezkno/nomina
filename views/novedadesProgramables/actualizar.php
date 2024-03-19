<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$IdModoLiquidacion = getId('PARAMETROS', "PARAMETROS.Parametro = 'ModoLiquidacion' AND PARAMETROS.Detalle = 'NOVEDADES'");

	$query = <<<EOD
		AUXILIARES.ModoLiquidacion = $IdModoLiquidacion AND 
		AUXILIARES.Borrado = 0 
	EOD;

	$conceptos = getTabla('AUXILIARES', $query, 'AUXILIARES.Nombre');

	$SelectConcepto = '';
	
	for ($i=0; $i < count($conceptos); $i++) 
	{ 
		$reg = getRegistro('MAYORES', $conceptos[$i]['idmayor']);
		$reg = getRegistro('PARAMETROS', $reg['tipoliquidacion']);

		if	($conceptos[$i]['id'] == $data['reg']['IdConcepto'])
			$SelectConcepto .= '<option selected value=' . $conceptos[$i]['id'] . '>' . $conceptos[$i]['nombre'] . ' (' . $reg['detalle'] . ')</option>';
		else
			$SelectConcepto .= '<option value=' . $conceptos[$i]['id'] . '>' . $conceptos[$i]['nombre'] . ' (' . $reg['detalle'] . ')</option>';
	}

	$SelectTipoEmpleado = getSelect('TipoEmpleado', $data['reg']['TipoEmpleado'], '', 'PARAMETROS.Valor');

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

	$centros = getTabla('CENTROS', '', 'CENTROS.Nombre');

	$SelectCentro = '';
	
	for ($i=0; $i < count($centros); $i++) 
	{ 
		if	($centros[$i]['id'] == $data['reg']['IdCentro'])
			$SelectCentro .= '<option selected value=' . $centros[$i]['id'] . '>' . $centros[$i]['nombre'] . '</option>';
		else
			$SelectCentro .= '<option value=' . $centros[$i]['id'] . '>' . $centros[$i]['nombre'] . '</option>';
	}

	$cargos = getTabla('CARGOS', '', 'CARGOS.Nombre');

	$SelectCargo = '';
	
	for ($i=0; $i < count($cargos); $i++) 
	{ 
		if	($cargos[$i]['id'] == $data['reg']['IdCargo'])
			$SelectCargo .= '<option selected value=' . $cargos[$i]['id'] . '>' . $cargos[$i]['nombre'] . '</option>';
		else
			$SelectCargo .= '<option value=' . $cargos[$i]['id'] . '>' . $cargos[$i]['nombre'] . '</option>';
	}

	$terceros = getTabla('TERCEROS', '', 'TERCEROS.Nombre');

	$SelectTercero = '';
	
	for ($i=0; $i < count($terceros); $i++) 
	{ 
		if	($terceros[$i]['id'] == $data['reg']['IdTercero'])
			$SelectTercero .= '<option selected value=' . $terceros[$i]['id'] . '>' . $terceros[$i]['nombre'] . '</option>';
		else
			$SelectTercero .= '<option value=' . $terceros[$i]['id'] . '>' . $terceros[$i]['nombre'] . '</option>';
	}

	$SelectAplicaNovedad = getSelect('AplicaNovedad', $data['reg']['Aplica'], '', 'PARAMETROS.Valor');
	$SelectModoLiquidacion = getSelect('ModoLiquidacionNP', $data['reg']['ModoLiquidacion'], '', 'PARAMETROS.Valor');
	$SelectEstado = getSelect('EstadoNovedad', $data['reg']['Estado'], '', 'PARAMETROS.Valor');
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
									<h3 class="white-text">Novedades programables</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Fecha inicio*'), 'Fecha', $data['reg']['Fecha'], 'date', 10, FALSE, 'required', 'fas fa-edit'); 
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
										get(label('Tipo empleado'), 'TipoEmpleado', $SelectTipoEmpleado, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
									?>
								</div>
								<div class="col s12 l6">
									<?php 
										get(label('Empleado'), 'IdEmpleado', $SelectEmpleado, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Centro de costos'), 'IdCentro', $SelectCentro, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
									?>
								</div>
								<div class="col s12 l6">
									<?php 
										get(label('Cargo'), 'IdCargo', $SelectCargo, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 l6 hidden">
									<?php 
										get(label('Horas'), 'Horas', $data['reg']['Horas'], 'number', 6, FALSE, '', 'fas fa-edit'); 
									?>
								</div>
								<div class="col s12 l6 hidden">
									<?php 
										get(label('Valor'), 'Valor', $data['reg']['Valor'], 'number', 12, FALSE, '', 'fas fa-edit'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Salario límite'), 'SalarioLimite', $data['reg']['SalarioLimite'], 'number', 12, FALSE, '', 'fas fa-edit'); 
									?>
								</div>
								<div class="col s12 l6">
									<?php 
										get(label('Fecha límite'), 'FechaLimite', $data['reg']['FechaLimite'], 'date', 10, FALSE, '', 'fas fa-edit'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Tercero'), 'IdTercero', $SelectTercero, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
									?>
								</div>
								<div class="col s12 l6">
									<?php 
										get(label('Aplica novedad*'), 'Aplica', $SelectAplicaNovedad, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s12 l6">
									<?php 
										get(label('Modo de liquidación*'), 'ModoLiquidacion', $SelectModoLiquidacion, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
									?>
								</div>
								<div class="col s12 l6">
									<?php 
										get(label('Estado novedad*'), 'Estado', $SelectEstado, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
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
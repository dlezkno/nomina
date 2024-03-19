<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	if (isset($_REQUEST['Concepto']))
	{ 
		$Concepto = $_REQUEST['Concepto'];
		$NombreConcepto = $_REQUEST['NombreConcepto'];
	}
	else
	{
		$Concepto = '';
		$NombreConcepto = '';
	}

	if (isset($_REQUEST['Tercero'])) 
	{
		$Tercero = $_REQUEST['Tercero'];
		$NombreTercero = $_REQUEST['NombreTercero'];
	}
	else
	{
		$Tercero = '';
		$NombreTercero = '';
	}

	$SelectTipoEmpleado = getSelect('TipoEmpleado', $data['reg']['TipoEmpleado'], '', 'PARAMETROS.Valor');
	$SelectTipoTercero = getSelect('TipoTercero', $data['reg']['TipoTercero'], '', 'PARAMETROS.Valor');

	$tipodoc = getTabla('TIPODOC', '', 'TIPODOC.TipoDocumento');

	$SelectTipoDocumento = '';
	
	for ($i=0; $i < count($tipodoc); $i++) 
	{ 
		if	($tipodoc[$i]['id'] == $data['reg']['IdTipoDoc'])
			$SelectTipoDocumento .= '<option selected value=' . $tipodoc[$i]['id'] . '>' . trim($tipodoc[$i]['nombre']) . '</option>';
		else
			$SelectTipoDocumento .= '<option value=' . $tipodoc[$i]['id'] . '>' . trim($tipodoc[$i]['nombre']) . '</option>';
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
									<h3 class="white-text">Comprobantes de diario</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="input-field col s6 m6">
									<?php 
										get(label('Tipo de documento*'), 'IdTipoDoc', $SelectTipoDocumento, 'select', 0, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s2 m2">
									<?php get(label('Concepto*'), 'Concepto', $Concepto, 'text', 5, FALSE, 'onblur="ConsultaConcepto2(this.value); return false" required', ''); ?>
								</div>
								<div class="input-field col s4 m4">
									<?php get('', 'NombreConcepto',$NombreConcepto, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="input-field col s6 m6">
									<?php 
										get(label('Tipo empleado'), 'TipoEmpleado', $SelectTipoEmpleado, 'select', 0, FALSE, '', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s6 m6">
									<?php 
										get(label('Cuenta Db.*'), 'CuentaDb', $data['reg']['CuentaDb'], 'text', 20, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
								<div class="input-field col s6 m6">
									<?php 
										get(label('Detalla por Centro'), 'DetallaCentroDb', $data['reg']['DetallaCentroDb'], 'checkbox', $data['reg']['DetallaCentroDb'], FALSE, '', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s6 m6">
									<?php 
										get(label('Cuenta Cr.*'), 'CuentaCr', $data['reg']['CuentaCr'], 'text', 20, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
								<div class="input-field col s6 m6">
									<?php 
										get(label('Detalla por Centro'), 'DetallaCentroCr', $data['reg']['DetallaCentroCr'], 'checkbox', $data['reg']['DetallaCentroCr'], FALSE, '', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s6 m6">
									<?php 
										get(label('Porcentaje*'), 'Porcentaje', $data['reg']['Porcentaje'], 'number', 10, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
								<div class="input-field col s6 m6">
									<?php 
										get(label('Tipo de Tercero.'), 'TipoTercero', $SelectTipoTercero, 'select', 0, FALSE, '', 'fas fa-pen'); 
									?>
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
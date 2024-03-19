<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$mayores = getTabla('MAYORES', '', 'MAYORES.Mayor');

	$SelectMayor = '';
	
	for ($i=0; $i < count($mayores); $i++) 
	{ 
		if	($mayores[$i]['id'] == $data['reg']['IdMayor'])
			$SelectMayor .= '<option selected value=' . $mayores[$i]['id'] . '>' . trim($mayores[$i]['nombre']) . '</option>';
		else
			$SelectMayor .= '<option value=' . $mayores[$i]['id'] . '>' . trim($mayores[$i]['nombre']) . '</option>';
	}

	$SelectTipoEmpleado = getSelect('TipoEmpleado', $data['reg']['TipoEmpleado'], '', 'PARAMETROS.Valor');
	$SelectImputacion = getSelect('Imputacion', $data['reg']['Imputacion'], '', 'PARAMETROS.Valor');
	$SelectModoLiquidacion = getSelect('ModoLiquidacion', $data['reg']['ModoLiquidacion'], '', 'PARAMETROS.Valor');
	$SelectTipoAuxiliar = getSelect('TipoAuxiliar', $data['reg']['TipoAuxiliar'], '', 'PARAMETROS.Valor');
	$SelectTipoRegistroAuxiliar = getSelect('TipoRegistroAuxiliar', $data['reg']['TipoRegistroAuxiliar'], '', 'PARAMETROS.Detalle');
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
									<h3 class="white-text">Conceptos auxiliares</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<section class="tabs-vertical mt-1 section">
								<div class="row">
									<div class="col l3 s12">
										<div class="card-panel">
											<ul class="tabs">
												<li class="tab">
													<a href="#pagConcepto">
														<i class="material-icons">error_outline</i>
														<span>Concepto</span>
													</a>
												</li>
												<li class="tab">
													<a href="#pagEstructura">
														<i class="material-icons">list</i>
														<span>Estructura</span>
													</a>
												</li>
											</ul>
										</div>
									</div>

									<div class="col s12 l9">
										<div id="pagConcepto" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>CONCEPTO AUXILIAR</p>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php 
															get(label('Concepto mayor*'), 'IdMayor', $SelectMayor, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php 
															get(label('Concepto auxiliar*'), 'Auxiliar', $data['reg']['Auxiliar'], 'text', 3, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
													<div class="col s12 l6">
														<?php 
															get(label('Nombre*'), 'Nombre', $data['reg']['Nombre'], 'text', 40, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
												</div>
											</div>
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>CLASIFICACIÓN</p>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php 
															get(label('Tipo empleado'), 'TipoEmpleado', $SelectTipoEmpleado, 'select', 0, FALSE, '', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php 
															get(label('Imputación*'), 'Imputacion', $SelectImputacion, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="col s12 l6">
														<?php 
															get(label('Modo de liquidación*'), 'ModoLiquidacion', $SelectModoLiquidacion, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php 
															get(label('Factor de conversión*'), 'FactorConversion', $data['reg']['FactorConversion'], 'number', 8, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
													<div class="col s12 l6">
														<?php 
															if ($data['NombreTipoLiquidacion'] == 'HORAS' OR $data['NombreTipoLiquidacion'] == 'PRODUCCIÓN') 
																get(label('Hora fija'), 'HoraFija', $data['reg']['HoraFija'], 'number', 8, FALSE, 'required', 'fas fa-pen'); 
															else
																get(label('Valor fijo'), 'ValorFijo', $data['reg']['ValorFijo'], 'number', 12, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php 
															get(label('Tipo auxiliar*'), 'TipoAuxiliar', $SelectTipoAuxiliar, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); 
														?>
													</div>
													<div class="col s12 l6">
														<?php 
															get(label('Tipo registro*'), 'TipoRegistroAuxiliar', $SelectTipoRegistroAuxiliar, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); 
														?>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php 
															get(label('Concepto se dispersa'), 'EsDispersable', $data['reg']['EsDispersable'], 'checkbox', $data['reg']['EsDispersable'], FALSE, '', ''); 
														?>
													</div>
													<div class="col s12 l6">
														<?php 
															get(label('Código nómina electrónica*'), 'Nombre', $data['reg']['CodigoNE'], 'text', 5, FALSE, 'required', 'fas fa-pen'); 
														?>
													</div>
												</div>
											</div>
										</div>

										<div id="pagEstructura" class="col s12">
											<div class="card-panel">
												<div class="card-alert card cyan darken-4">
													<div class="card-content white-text">
														<p>ESTRUCTURA</p>
													</div>
												</div>

												<div class="row">
												</div>
											</div>
										</div>
									</div>
								</div>
							</section>
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
	</div>
</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
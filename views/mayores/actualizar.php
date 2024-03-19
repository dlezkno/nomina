<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$SelectTipoLiquidacion = getSelect('TipoLiquidacion', $data['reg']['TipoLiquidacion'], '', 'PARAMETROS.Valor');
	$SelectClaseConcepto = getSelect('ClaseConcepto', $data['reg']['ClaseConcepto'], '', 'PARAMETROS.Valor');
	$SelectTipoRetencion = getSelect('TipoRetencion', $data['reg']['TipoRetencion'], '', 'PARAMETROS.Valor');

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
									<h3 class="white-text">Conceptos mayores</h3>
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
														<p>CONCEPTO MAYOR</p>
													</div>
												</div>
												<input type="hidden" name="Id" value="<?= $data['reg']['Id'] ?>" />
												<div class="row">
													<div class="col s12 l6">
														<?php 
															get(label('Concepto mayor*'), 'Mayor', $data['reg']['Mayor'], 'text', 2, FALSE, 'required', 'fas fa-pen'); 
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
															get(label('Tipo de liquidación*'), 'TipoLiquidacion', $SelectTipoLiquidacion, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
													<div class="col s12 l6">
														<?php
															get(label('Clase de concepto*'), 'ClaseConcepto', $SelectClaseConcepto, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php
															get(label('Base para primas'), 'BasePrimas', $data['reg']['BasePrimas'], 'checkbox', $data['reg']['BasePrimas'], FALSE, '', '');
														?>
													</div>
													<div class="col s12 l6">
														<?php
															get(label('Base para vacaciones'), 'BaseVacaciones', $data['reg']['BaseVacaciones'], 'checkbox', $data['reg']['BaseVacaciones'], FALSE, '', '');
														?>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php
															get(label('Base para cesantías'), 'BaseCesantias', $data['reg']['BaseCesantias'], 'checkbox', $data['reg']['BaseCesantias'], FALSE, '', '');
														?>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php
															get(label('Acumula días por sanción'), 'AcumulaSanciones', $data['reg']['AcumulaSanciones'], 'checkbox', $data['reg']['AcumulaSanciones'], FALSE, '', '');
														?>
													</div>
													<div class="col s12 l6">
														<?php
															get(label('Acumula días en licencia'), 'AcumulaLicencias', $data['reg']['AcumulaLicencias'], 'checkbox', $data['reg']['AcumulaLicencias'], FALSE, '', '');
														?>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php
															get(label('Controla saldos'), 'ControlaSaldos', $data['reg']['ControlaSaldos'], 'checkbox', $data['reg']['ControlaSaldos'], FALSE, '', '');
														?>
													</div>
													<div class="col s12 l6">
														<?php
															get(label('Excluido de nómina electrónica'), 'ExcluidoNE', $data['reg']['ExcluidoNE'], 'checkbox', $data['reg']['ExcluidoNE'], FALSE, '', '');
														?>
													</div>
												</div>
												<div class="row">
													<div class="col s12 l6">
														<?php
															get(label('Tipo de retención*'), 'TipoRetencion', $SelectTipoRetencion, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
														?>
													</div>
													<div class="col s12 l6">
														<?php 
															get(label('Renglon certificado'), 'RenglonCertificado', $data['reg']['RenglonCertificado'], 'text', 3, FALSE, '', 'fas fa-pen'); 
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
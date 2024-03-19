<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');
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
									<h3 class="white-text">Liquidación de prima de servicios</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col s6 right-align">
								<p>
									<label>
										<input name="CicloPrima" type="radio" value="97" checked />
										<span>Sin Icetex (97)</span>
									</label>
								</p>
							</div>
							<div class="col s6">
								<p>
									<label>
										<input name="CicloPrima" type="radio" value="96" />
										<span>Solo Icetex (96)</span>
									</label>
								</p>
							</div>
						</div>
						<div class="card-content pt-0 pb-0">
							<section class="tabs-vertical mt-1 section">
								<div class="card-panel">
									<div class="card-alert card cyan darken-4">
										<div class="card-content white-text">
											<p>LIQUIDACIÓN DE PRIMA DE SERVICIOS</p>
										</div>
									</div>
									<div class="row">
										<div class="col s12">
											<input type="hidden" name="LiqPrimaSave">
											<?php 
												get(label('Empleado'), 'Empleado', $data['reg']['Empleado'], 'text', 15, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
									</div>
								</div>
							</section>
						</div>
						<div class="card-content pt-0">
							<section class="tabs-vertical section">
								<div class="card-panel">
									<div class="card-alert card cyan darken-4">
										<div class="card-content white-text">
											<p>ACUMULAR PRIMA</p>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m12">
											<p>Este proceso se encarga de trasladar todo el pago de Prima a los Acumulados.</p>
										</div>
									</div>
									<br>
									<br>
									<div class="row">
										<div class="col s12 m6">
											<button class="btn btn-sm cyan darken-4 " type="submit" name="Action" value="Acumular">
												ACUMULAR PRIMA
											</button>
										</div>
										<div class="col s12 m6">
											<button class="btn btn-sm red darken-4 " type="submit" name="Action" value="Reversar">
												REVERSAR ACUMULADO DE PRIMA
											</button>
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

<?php require_once('views/templates/footer.php'); ?>
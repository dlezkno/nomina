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
									<h3 class="white-text">Acumular Nómina</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<h6 class="white-text"><?= 'PERÍODO: ' . $data['Periodo'] . '-' . $data['Ciclo'] ?></h6>
									<h6 class="white-text"><?= 'DESDE: ' . $data['FechaInicial'] . ' - ' . $data['FechaFinal'] ?></h6>
								</div>
							</div>
						</div>
						<div class="card-content">
							<section class="tabs-vertical mt-1 section">
								<div class="card-panel">
									<div class="card-alert card cyan darken-4">
										<div class="card-content white-text">
											<p>ACUMULAR NÓMINA</p>
										</div>
									</div>
                                    <div class="row">
                                        <div class="col s12 m12">
                                            <p>Este proceso se encarga de trasladar toda la liquidación de prenómina a los Acumulados, adicionalmente actualiza el libro de vacaciones, incapacidades, aumentos salariales, sanciones y licencias, préstamos y novedades programables.</p>
                                            <br>
                                            <p>Si requiere reversar el proceso encienda el check "Reversar acumulados de nómina" para dejar la liquidación tal cual como se encontraba antes de acumular la nómina</p>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="row">
                                        <div class="col s12 m6">
                                            <button class="btn btn-sm cyan darken-4 " type="submit" name="Action" value="Acumular">
                                                ACUMULAR NÓMINA
  			        						</button>									
                                    	</div>
                                        <div class="col s12 m6">
                                            <button class="btn btn-sm red darken-4 " type="submit" name="Action" value="Reversar">
                                                REVERSAR ACUMULADO DE NÓMINA
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
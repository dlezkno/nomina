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
									<h3 class="white-text">Reporte nómina electrónica</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<section class="tabs-vertical mt-1 section">
								<div class="card-panel">
									<div class="card-alert card cyan darken-4">
										<div class="card-content white-text">
											<p>REPORTE NÓMINA ELECTRÓNICA</p>
										</div>
									</div>
									<div class="row">
										<div class="col s12">
											<?php 
												get(label('Período*'), 'Periodo', $data['reg']['Periodo'], 'number', 2, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
									</div>
								</div>

								<div class="card-panel">
									<div class="row">
										<div class="col s12 center-align">
											<button class="btn btn-sm cyan darken-4 " type="submit" name="Action" value="DownloadConceptsNE">
												DESCARGAR INFORME DE CONCEPTOS USADOS EN NÓMINA ELECTRÓNICA
											</button>
										</div>
									</div>
								</div>
							</section>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
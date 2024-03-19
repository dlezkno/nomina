<?php 
	

	if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar')
	{
		$data = array();
		$cArchivo = 'plantillas/CANDIDATOS.xlsx'; 

		if (file_exists($cArchivo)) 
		{
			header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: application/vnd.ms-excel");
			header('Content-Disposition: attachment; filename="' . basename($cArchivo).'"');
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . @filesize($cArchivo));
            set_time_limit(0);
			ob_clean();
			flush();
			readfile($cArchivo);
		}
		else 
		{
			$data['mensajeError'] = 'Plantilla no existe';
		}
	}		

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
									<h3 class="white-text">Candidatos</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<h5>Importar archivo de Candidatos</h5>
							<br>
							<br>
							<div class="row">
								<div class="col s6">
									<br>
									<button class="btn btn-sm cyan darken-4 " type="submit" name="Action" value="Exportar">
										<i class="material-icons">cloud_download</i>   DESCARGAR PLANTILLA
  									</button>		
								</div>
								<div class="col s6">
									<?php 
										get(label('Archivo a importar*'), 'Archivo_candidatos', '.xlsx,.xls', 'file', 0, FALSE, '', ''); 
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
						<?php if ( $data ): ?>
							<?php if ( $data['mensajeError'] ): ?>
							<div class="row" id="mensajeError">
								<script>window.location.href = "#mensajeError";</script>
								<div class="col s12">
									<h6 class="orange-text">
										<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por
										favor valídelas:
									</h6>
									<br>
									<?= $data['mensajeError'] ?>
									<br>
								</div>
							</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
<?php 
	if (isset($_REQUEST['Action']))
	{
		if ($_REQUEST['Action'] == 'ExportarEmpleados')
			$cArchivo = 'plantillas/EMPLEADOS.xlsx'; 

		if ($_REQUEST['Action'] == 'ExportarNovedades')
			$cArchivo = 'plantillas/NOVEDADES_EMPLEADOS.xlsx'; 

		if ($_REQUEST['Action'] == 'ExportarRenovaciones')
			$cArchivo = 'plantillas/RENOVACIONES_CONTRATOS_EMPLEADOS.xlsx'; 

		if ($_REQUEST['Action'] == 'ExportarCentros')
			$cArchivo = 'plantillas/ACTUALIZACION_CENTROS_POR_EMPLEADO.xlsx'; 

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
	else
	{
		require_once('views/templates/header.php');
		require_once('views/templates/sideBar.php');
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
									<h3 class="white-text">Empleados</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<h5>Importar archivo de Empleados</h5>
							<br>
							<br>
							<div class="row">
								<div class="col s4">
								</div>
								<div class="col s4">
									<br>
									<button class="btn btn-sm cyan darken-4 " type="submit" name="Action" value="ExportarEmpleados">
										<i class="material-icons">cloud_download</i>   DESCARGAR PLANTILLA
  									</button>		
								</div>
								<div class="col s4">
									<?php 
										get(label('Archivo a importar*'), 'Archivo', '.xlsx,.xls', 'file', 0, FALSE, '', ''); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s4">
									<?php
										get(label('Novedades de empleados'), 'SonNovedades', FALSE, 'checkbox', FALSE, FALSE, '', '');
									?>
								</div>
								<div class="col s4">
									<br>
									<button class="btn btn-sm cyan darken-4 " type="submit" name="Action" value="ExportarNovedades">
										<i class="material-icons">cloud_download</i>   DESCARGAR PLANTILLA
  									</button>		
								</div>
							</div>
							<div class="row">
								<div class="col s4">
									<?php
										get(label('Renovaciones de contratos'), 'SonRenovaciones', FALSE, 'checkbox', FALSE, FALSE, '', '');
									?>
								</div>
								<div class="col s4">
									<br>
									<button class="btn btn-sm cyan darken-4 " type="submit" name="Action" value="ExportarRenovaciones">
										<i class="material-icons">cloud_download</i>   DESCARGAR PLANTILLA
  									</button>		
								</div>
							</div>
							<div class="row">
								<div class="col s4">
									<?php
										get(label('Centros y proyectos por empleado'), 'SonCentros', FALSE, 'checkbox', FALSE, FALSE, '', '');
									?>
								</div>
								<div class="col s4">
									<br>
									<button class="btn btn-sm cyan darken-4 " type="submit" name="Action" value="ExportarCentros">
										<i class="material-icons">cloud_download</i>   DESCARGAR PLANTILLA
  									</button>		
								</div>
							</div>
						</div>
					</div>
					<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
						<?php if ( ! empty($data['mensajeError']) ): ?>
						<div class="row">
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
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
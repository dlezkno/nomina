<?php 
	$lcFiltro = $_SESSION['DISPERSIONNOMINA']['Filtro'];
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	if (isset($_REQUEST['action']) AND $_REQUEST['action'] == 'ELIMINAR')
	{
		unlink($_REQUEST['File']);
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
									<h3 class="white-text">Dispersión de Nómina</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12 m6 l6">
									<h3>Archivos generados</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
							<hr/>
							<?php
								$cPathArchivo = array();
								$cPath = 'descargas/';
								
								if	( is_dir($cPath) )
								{
									$dir = opendir($cPath);
									
									while ( $elemento = readdir($dir) )
									{
										if	( $elemento == '.' OR $elemento == '..' OR is_dir($elemento) )
											continue;

										if	(strpos(pathinfo($elemento, PATHINFO_FILENAME), 'DispersionNomina') !== false)
										{
											$cPathArchivo[] = array($elemento, $cPath . $elemento);
										}
									}						
								}
							?>
							<?php for ($i = 0; $i < count($cPathArchivo); $i++): ?>
							<div class="row">
								<div class="col s12 m6">
									<strong>
										<?php echo $cPathArchivo[$i][0]; ?>
									</strong>
								</div>
								<div class="col s12 m6">
									<a href="../<?php echo $cPathArchivo[$i][1]; ?>" target="_blank" class="btn btn-sm orange darken-3 tooltipped" data-position="bottom" data-tooltip="Ver documento">
										<i class='fas fa-eye'></i>
									</a>
									<a href="../<?php echo $cPathArchivo[$i][1]; ?>" download class="btn btn-sm orange darken-3 tooltipped" data-position="bottom" data-tooltip="Descargar documento">
										<i class='fas fa-download'></i>
									</a>
									<button type="submit" class="btn btn-sm orange darken-3 tooltipped"
										formaction="?action=ELIMINAR&File=<?php echo $cPathArchivo[$i][1]; ?>" data-position="bottom" data-tooltip="Eliminar documento">
										<i class='far fa-trash-alt'></i>
									</button>
								</div>
							</div>
							<hr/>
							<?php endfor; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="content-overlay"></div>
		</div>
	</div>
</div>
<?php require_once('views/templates/footer.php'); ?>

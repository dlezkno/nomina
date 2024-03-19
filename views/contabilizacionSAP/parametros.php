<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$Referencia = $data['reg']['Referencia'];
	$SelectPeriodicidad = getSelect('Periodicidad', $data['reg']['Periodicidad'], '', 'PARAMETROS.Valor');
	$Periodo = $data['reg']['Periodo'];

	// $comprobantes = getTabla('TIPODOC', "TIPODOC.TipoDocumento <> 'PARAF'", 'TIPODOC.TipoDocumento');
	$comprobantes = getTabla('TIPODOC', '', 'TIPODOC.TipoDocumento');

	$SelectComprobante = '';

	for ($i = 0; $i < count($comprobantes); $i++) 
	{ 
		if	($comprobantes[$i]['id'] == $data['reg']['IdComprobante'])
			$SelectComprobante .= '<option selected value=' . $comprobantes[$i]['id'] . '>' . trim($comprobantes[$i]['nombre']) . '</option>';
		else
			$SelectComprobante .= '<option value=' . $comprobantes[$i]['id'] . '>' . trim($comprobantes[$i]['nombre']) . '</option>';
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
									<h3 class="white-text">Contabilización en SAP</h3>
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
											<p>CONTABILIZACIÓN EN SAP</p>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m4">
											<?php 
												get(label('Referencia*'), 'Referencia', $Referencia, 'number', 5, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
										<div class="col s12 m4">
											<?php 
												get(label('Periodicidad*'), 'Periodicidad', $SelectPeriodicidad, 'select', 0, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
										<div class="col s12 m4">
											<?php 
												get(label('Período*'), 'Periodo',  $Periodo, 'number', 5, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
									</div>
									<div class="row">
										<div class="col s12 m4">
											<?php 
												get(label('Comprobante*'), 'IdComprobante', $SelectComprobante, 'select', 0, FALSE, '', 'fas fa-pen'); 
											?>
										</div>
									</div>
								</div>
							</section>
							<section class="tabs-vertical mt-1 section">
								<table>
									<tbody>
										<?php
											$cDirectorio = './descargas/';

											if (is_dir($cDirectorio)):
												$dir = opendir($cDirectorio);

												while (($archivo = readdir($dir)) !== false):
													if ($archivo != '.' AND $archivo != '..' AND strpos($archivo, 'ComprobanteSAP') > 0):
														if (strpos($archivo, $_SESSION['Login']['Usuario']) !== FALSE):														
										?>
										<tr>
											<td>
												<?= $archivo ?>
											</td>
											<td>
												<button type="submit" class="btn btn-sm teal lighten-3 tooltipped"
													formaction="?Action=EXPORTAR&Archivo=<?= $cDirectorio . $archivo ?>"
													data-position="bottom" data-tooltip="Descargar documento">
													<i class="material-icons">cloud_download</i>
												</button>
											</td>
											<td>
												<button type="submit" class="btn btn-sm teal lighten-3 tooltipped"
													formaction="?Action=ELIMINAR&Archivo=<?= $cDirectorio . $archivo ?>"
													data-position="bottom" data-tooltip="Eliminar documento">
													<i class="material-icons">delete</i>
												</button>
											</td>
										</tr>
										<?php
														endif;
													endif;
												endwhile;
												closedir($dir);
											endif;
										?>
									</tbody>
								</table>
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
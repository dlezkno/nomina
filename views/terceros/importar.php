<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$data = array();
	$data['mensajeError'] = '';
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
									<h3 class="white-text">Terceros</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<h5>Importar archivo de terceros</h5>
							<p>Defina un archivo en Excel que contenga las siguientes columnas:</p>
							<table class="table">
								<thead>
									<tr>
										<th>A</th>
										<th>B</th>
										<th>C</th>
										<th>D</th>
										<th>E</th>
										<th>F</th>
										<th>G</th>
										<th>H</th>
										<th>I</th>
										<th>J</th>
										<th>K</th>
										<th>L</th>
										<th>M</th>
										<th>N</th>
										<th>O</th>
										<th>P</th>
										<th>Q</th>
										<th>R</th>
										<th>S</th>
										<th>T</th>
										<th>U</th>
										<th>V</th>
										<th>W</th>
										<th>X</th>
										<th>Y</th>
										<th>Z</th>
										<th>AA</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>TIPO</td>
										<td>DOC.</td>
										<td>NOMBRE</td>
										<td>DEU.</td>
										<td>ACR.</td>
										<td>DIR.</td>
										<td>CIUDAD</td>
										<td>PAIS</td>
										<td>TEL.</td>
										<td>CEL.</td>
										<td>E-MAIL</td>
										<td>FORMA PAGO</td>
										<td>BANCO</td>
										<td>CUENTA</td>
										<td>TIPO</td>
										<td>SINDICATO</td>
										<td>EPS</td>
										<td>CUENTA EPS</td>
										<td>ARL</td>
										<td>CUENTA ARL</td>
										<td>FC</td>
										<td>CUENTA FC</td>
										<td>FP</td>
										<td>CUENTA FP</td>
										<td>CCF</td>
										<td>CUENTA CCF</td>
										<td>COD. SAP</td>
									</tr>
									<tr>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>X</td>
										<td>X</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>X</td>
										<td>X</td>
										<td>texto</td>
										<td>X</td>
										<td>texto</td>
										<td>X</td>
										<td>texto</td>
										<td>X</td>
										<td>texto</td>
										<td>X</td>
										<td>texto</td>
										<td>texto</td>
									</tr>
									<tr>
									<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>X</td>
										<td>X</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>texto</td>
										<td>X</td>
										<td>X</td>
										<td>texto</td>
										<td>X</td>
										<td>texto</td>
										<td>X</td>
										<td>texto</td>
										<td>X</td>
										<td>texto</td>
										<td>X</td>
										<td>texto</td>
										<td>texto</td>
									</tr>
								</tbody>
							</table>
							<p>Y así sucesivamente...</p>
							<br>
							<br>
							<div class="row">
								<div class="file-field input-field col s6 m6 l6">
									<div class="btn teal lighten-3">
										<span>Archivo</span>
										<input type="file" accept=".xlsx,.xls" id="archivo" name="archivo">
									</div>
									<div class="file-path-wrapper">
										<input class="file-path validate" type="text" placeholder="Seleccione archivo para cargar">
									</div>
									<!-- <p>Maximo tamaño de archivo 2MB.</p> -->
								</div>
							</div>
						</div>
					</div>
					<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
						<?php if ( $data['mensajeError'] ): ?>
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
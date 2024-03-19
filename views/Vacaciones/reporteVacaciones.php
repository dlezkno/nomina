<?php 
	$lcFiltro = $_SESSION['REP_VAC']['Filtro'];
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$centros = getTabla('CENTROS', '', 'CENTROS.Nombre');

	$SelectCentro = '';
	
	for ($i=0; $i < count($centros); $i++) 
	{ 
		if	($centros[$i]['id'] == $_SESSION['REP_VAC']['reg']['IdCentro'])
			$SelectCentro .= '<option selected value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
		else
			$SelectCentro .= '<option value=' . $centros[$i]['id'] . '>' . trim($centros[$i]['nombre']) . '</option>';
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
									<h3 class="white-text">Libro de Vacaciones Disfrutadas</h3>
								</div><div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/Vacaciones/lista', $data['registros'], $_SESSION['REP_VAC']['Pagina'] );
									?>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12 m4">
									<?php 
										get(label('Inicio'), 'fecInicio', $_SESSION['REP_VAC']['reg']['fecInicio'], 'date', null, FALSE, '', 'fas fa-pen');
									?>
								</div>
								<div class="col s12 m4">
									<?php 
										get(label('Fin'), 'fecFin', $_SESSION['REP_VAC']['reg']['fecFin'], 'date', null, FALSE, '', 'fas fa-pen'); 
									?>
								</div>
								<div class="col s12 m4">
									<?php 
										get(label('Centro de costos'), 'IdCentro',  $SelectCentro, 'select', 0, FALSE, '', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="col s12">
									<table>
										<thead>
											<tr>
												<?php
													if (
														isset($data) AND
														isset($data['rows']) &&
														count($data['rows']) > 0
													) {
														foreach (array_keys($data["rows"][0]) as $key) {
															echo "<th class='center-align'>$key</th>";
														}
													}
												?>
											</tr>
										</thead>
										<tbody>
												<?php
													for ($i = 0; $i < count($data['rows']); $i++) {
														$reg = $data['rows'][$i];
														echo "<tr>";
														foreach (array_keys($reg) as $key) {
															echo "<td class='center-align'>".$reg[$key]."</td>";
														}
														echo "</tr>";
													}
												?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
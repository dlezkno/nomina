<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$IdProyecto = $data['reg']['IdProyecto'];
	if ($IdProyecto > 0)
	{
		$regProyecto = getRegistro('CENTROS', $IdProyecto);
		if ($regProyecto)
			$NombreProyecto = $regProyecto['nombre'];
		else
			$NombreProyecto = '';
	}
	else
		$NombreProyecto = '';

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
									<h3 class="white-text">Pronostico de contrataciones (Beta)</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="input-field col s12">
									<input type="hidden" name="IdProyecto" id="IdProyecto" value="<?= $IdProyecto ?>">
									<?php 
										get(label('Proyecto*'), 'NombreProyecto', $NombreProyecto, 'text', 60, FALSE, '', 'textsms'); 
									?>
									<div id="suggestionsProyecto"></div>
								</div>
							</div>
							<div class="row">
								<div class="col s12">
									<h6 class="orange-text" style="text-align: center;">
										<?php 
											if (isset($data['reg']['pronostico'])) echo "Se proyectan ".round($data['reg']['pronostico'], 2)." contrataciones";
											else if (isset($data['reg']['items'])) echo "No se puede hacer una proyección de contrataciones";
										?>
									</h6>
								</div>
							</div>
							<div class="row">
								<div class="col s12">
									<?php if ( isset($data['reg']['items']) ): ?>
										<table>
											<thead>
												<tr>
													<th>MES</th>
													<th>CONTRATACIONES</th>
													<th>RETIROS</th>
													<!-- <th>AUSENCIAS</th> -->
													<th>VACACIONES</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<?php for ($i = 0; $i < count($data['reg']['items']); $i++ ): ?>
												<tr>
													<td>
														<?= $data['reg']['items'][$i]['anio_mes'] ?>
													</td>
													<td>
														<?= $data['reg']['items'][$i]['contrataciones'] ?>
													</td>
													<td>
														<?= $data['reg']['items'][$i]['retiros'] ?>
													</td>
													<!-- <td>
														<?= $data['reg']['items'][$i]['ausencias'] ?>
													</td> -->
													<td>
														<?= $data['reg']['items'][$i]['vacaciones'] ?>
													</td>
												</tr>
												<?php endfor; ?>
											</tbody>
										</table>
									<?php endif; ?>
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

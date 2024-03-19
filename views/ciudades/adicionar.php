<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$paises = getTabla('PAISES', '', 'PAISES.Orden,PAISES.Nombre1');

	$SelectPais = '';
	
	for ($i = 0; $i < count($paises); $i++)
	{ 
		if	($paises[$i]['id'] == $data['reg'][3])
			$SelectPais .= '<option selected value=' . $paises[$i]['id'] . '>' . trim($paises[$i]['nombre1']) . '</option>';
		else
			$SelectPais .= '<option value=' . $paises[$i]['id'] . '>' . trim($paises[$i]['nombre1']) . '</option>';
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
									<h3 class="white-text">Ciudades</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Código ciudad*'), 'ciudad', $data['reg'][0], 'text', 5, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('Nombre de la ciudad*'), 'nombre', $data['reg'][1], 'text', 25, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12 m6">
									<?php 
										get(label('Departamento'), 'departamento', $data['reg'][2], 'text', 25, FALSE, 'required', 'fas fa-pen'); 
									?>
								</div>
								<div class="input-field col s12 m6">
									<?php 
										get(label('País*'), 'IdPais', $SelectPais, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v');
									?>
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
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
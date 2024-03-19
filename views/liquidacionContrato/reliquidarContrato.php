<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	if (isset($_REQUEST['Documento']))
	{ 
		$Documento 		= $_REQUEST['Documento'];
		$NombreEmpleado = $_REQUEST['NombreEmpleado'];
		$Cargo 			= $_REQUEST['Cargo'];
		$Centro 		= $_REQUEST['Centro'];
		$FechaRetiro 	= $_REQUEST['FechaRetiro'];
	}
	else
	{
		$Documento 		= '';
		$NombreEmpleado = '';
		$Cargo 			= '';
		$Centro 		= '';
		$FechaRetiro 	= NULL;
	}

	$Concepto = '';
	$NombreConcepto = '';
    $Horas = 0;
    $Valor = 0;
	$Tercero = '';
	$NombreTercero = '';

	$dir = '/documents/' . $Documento . '_' . str_replace(' ', '_', $NombreEmpleado) . '/HV/' . $Documento . '_FOTOGRAFIA.jpg';
	$cDirectorio = SERVERURL . $dir;
	$archivo = getImage($dir,$cDirectorio);

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
									<h3 class="white-text">Reliquidar contrato</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s2">
									<?php get(label('Empleado*'), 'Documento', $Documento, 'text', 15, FALSE, 'onblur="ConsultaEmpleadoRetirado(this.value); return false" required', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'NombreEmpleado', $NombreEmpleado, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'Cargo', $Cargo, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'Centro', $Centro, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s1">
									<div class="row center-align">
										<img id="ImagenEmpleado" name="ImagenEmpleado" src="<?= $archivo ?>"
											alt="Fotografia" class="circle responsive-img" width="50px">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col s2">
									<?php get('Fecha retiro', 'FechaRetiro', $FechaRetiro, 'date', 10, FALSE, '', ''); ?>
								</div>
							</div>
							<div class="row">
								<div class="col s1">
									<?php get(label('Concepto*'), 'Concepto', $Concepto, 'text', 5, FALSE, 'onblur="ConsultaConceptoReliquidacion(this.value); return false" required', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'NombreConcepto',$NombreConcepto, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s1">
									<?php get(label('Horas'), 'Horas', $Horas, 'number', 8, FALSE, '', ''); ?>
								</div>
								<div class="col s1">
									<?php get(label('Valor'), 'Valor', $Valor, 'number', 12, FALSE, '', ''); ?>
								</div>
								<div class="col s2">
									<?php get(label('Tercero'), 'Tercero', $Tercero, 'text', 10, FALSE, 'onblur="ConsultaTercero(this.value); return false" required', ''); ?>
								</div>
								<div class="col s3">
									<?php get('', 'NombreTercero', $NombreTercero, 'text', 60, TRUE, '', ''); ?>
								</div>
								<div class="col s1">
                                    <button type="button" name="AddNovedad" id="AddNovedad" class="btn teal">Adicionar</button>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s2"><strong>Concepto</strong></div>
								<div class="col s3"><strong>Descripción</strong></div>
								<div class="col s1"><strong>Horas</strong></div>
								<div class="col s1"><strong>Valor</strong></div>
								<div class="col s1"><strong>Tercero</strong></div>
								<div class="col s3"><strong>Nombre tercero</strong></div>
							</div>
							<hr/>
							<?php if (isset($_REQUEST['Documento'])): ?>
                                <div id="next">
									<?php for ($i = 0; $i < count($_REQUEST['aConcepto']); $i++): ?>
									<div class="row">
										<div class="col s2">
											<?php get('', 'aConcepto[]', $_REQUEST['aConcepto'][$i], 'text', 5, TRUE, '', ''); ?>
										</div>
										<div class="col s3">
											<?php get('', 'aNombreConcepto[]',$_REQUEST['aNombreConcepto'][$i], 'text', 60, TRUE, '', ''); ?>
										</div>
										<div class="col s1">
											<?php get('', 'aHoras[]', $_REQUEST['aHoras'][$i], 'number', 8, TRUE, '', ''); ?>
										</div>
										<div class="col s1">
											<?php get('', 'aValor[]', $_REQUEST['aValor'][$i], 'number', 12, TRUE, '', ''); ?>
										</div>
										<div class="col s1">
											<?php get('', 'aTercero[]', $_REQUEST['aTercero'][$i], 'text', 10, TRUE, '', ''); ?>
										</div>
										<div class="col s3">
											<?php get('', 'aNombreTercero[]', $_REQUEST['aNombreTercero'][$i], 'text', 60, TRUE, '', ''); ?>
										</div>
										<button type="button" class="btnRemove btn btn-success">Borrar</button>
									</div>
									<?php endfor; ?>
								</div>
                            </div>
							<?php else: ?>
                            <div class="row">
                                <div id="next"></div>
                            </div>
							<?php endif; ?>
						</div>
						<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
							<?php if ( $data['mensajeError'] ): ?>
							<div class="row">
								<div class="col s12">
									<h6 class="orange-text">
										<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por
										favor
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
</div>

<?php require_once('views/templates/footer.php'); ?>

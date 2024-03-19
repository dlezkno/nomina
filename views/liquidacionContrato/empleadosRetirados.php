<?php 
	$lcFiltro = $_SESSION['LIQ_CONTRATO']['Filtro'];
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
									<h3 class="white-text">Liquidación contrato de empleados</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/liquidacionContrato/lista', $data['registros'], $_SESSION['LIQ_CONTRATO']['Pagina'], $data['activar']);
									?>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12">
									<table>
										<thead>
											<tr>
												<th>
													<?php if ($data['activar']): ?>
														<a href="<?= SERVERURL ?>/liquidacionContrato/lista/<?= $_SESSION['LIQ_CONTRATO']['Pagina'] ?>/0" class="tooltipped" data-position="bottom" data-tooltip="Marcar/Desmarcar Todos">
															LIQ.
														</a>
													<?php else: ?>
														<a href="<?= SERVERURL ?>/liquidacionContrato/lista/<?= $_SESSION['LIQ_CONTRATO']['Pagina'] ?>/1" class="tooltipped" data-position="bottom" data-tooltip="Marcar/Desmarcar Todos">
															LIQ.
														</a>
													<?php endif; ?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['LIQ_CONTRATO']['Orden'] == 'EMPLEADOS.Documento' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('DOCUMENTO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Documento">';
																echo label('Documento');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['LIQ_CONTRATO']['Orden'] == 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('NOMBRE EMPLEADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2">';
																echo label('Nombre Empleado');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['LIQ_CONTRATO']['Orden'] == 'CARGOS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CARGO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="CARGOS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Cargo');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['LIQ_CONTRATO']['Orden'] == 'CENTROS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CENTRO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="CENTROS.Nombre,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Centro');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['LIQ_CONTRATO']['Orden'] == 'EMPLEADOS.FechaIngreso,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('FECHA ING.') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.FechaIngreso,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Fecha Ing.');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['LIQ_CONTRATO']['Orden'] == 'EMPLEADOS.FechaRetiro,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('FECHA RET.') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.FechaRetiro,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1">';
																echo label('Fecha Ret.');
															echo '</button>';
														}
													?>
												</th>
												<?php if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR): ?>
												<th class="right-align">Sueldo Básico</th>
												<?php endif; ?>
												<th></th>
												<th style="text-align:center;">PAGAR</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i=0; $i < count($data['rows']); $i++)
												{
													$reg = $data['rows'][$i];
													$NombreEmpleado = $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'];

													$NombreEmpleado = str_replace('/', ' ', $NombreEmpleado);
													
													$archivo = 'LiquidacionContrato_' . $reg['Documento'] . '_' . $NombreEmpleado . '.PDF';
											?>
											<tr>
												<td class="center-align">
													<?php 
														if (empty($data['mensajeError']) OR strpos($data['mensajeError'], $reg['Documento']) === false)
															if ($data['activar'])
																get('', 'okLiquidar[]', $reg['Id'], 'checkbox', 1, FALSE, '', ''); 
															else
																get('', 'okLiquidar[]', $reg['Id'], 'checkbox', 0, FALSE, '', ''); 
													?>
												</td>
												<td><?= $reg['Documento'] ?></td>
												<td><?= $NombreEmpleado ?></td>
												<td><?= $reg['NombreCargo'] ?></td>
												<td><?= $reg['NombreCentro'] ?></td>
												<td><?= $reg['FechaIngreso'] ?></td>
												<td><?= $reg['FechaRetiro'] ?></td>
												<?php if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR): ?>
												<td class="right-align"><?= number_format($reg['SueldoBasico'], 0) ?></td>
												<?php endif; ?>
												<?php if (file_exists('descargas/LiquidacionContrato_' . $reg['Documento'] . '_' . $NombreEmpleado . '.PDF')): ?>
												<td>
													<a href="<?= SERVERURL . '/descargas/' . $archivo; ?>" download="<?php echo $archivo; ?>" class="btn btn-small teal lighten-3 tooltipped" data-position="bottom" data-tooltip="Descargar documento"> 
													<i class="material-icons">cloud_download</i>
													</a>
												</td>
												<td class="center-align">
													<?php 
														if (empty($data['mensajeError']))
															get('', 'okPagar[]', $reg['Id'], 'checkbox', $reg['Id'], FALSE, '', ''); 
													?>
												</td>
												<?php endif; ?>
											</tr>
											<?php
												}
											?>
										</tbody>
									</table>
								</div>
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
			<div class="content-overlay"></div>
		</div>
	</div>
</div>
<?php require_once('views/templates/footer.php'); ?>

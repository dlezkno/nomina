<?php 
	$lcFiltro = $_SESSION['COMPROBANTES']['Filtro'];
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
									<h3 class="white-text">Comprobantes de diario</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/comprobantes/lista', $data['registros'], $_SESSION['COMPROBANTES']['Pagina'] );
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
												<th style="text-align:center;"></th>
												<th style="text-align:center;"></th>
												<th>
													<?php 
														if	( $_SESSION['COMPROBANTES']['Orden'] == 'TIPODOC.TipoDocumento,MAYORES.Mayor,AUXILIARES.Auxiliar,PARAMETROS1.Detalle' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('TIPO DOC.') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="TIPODOC.TipoDocumento,MAYORES.Mayor,AUXILIARES.Auxiliar,PARAMETROS1.Detalle">';
																echo label('Tipo doc.');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['COMPROBANTES']['Orden'] == 'MAYORES.Mayor,AUXILIARES.Auxiliar,TIPODOC.TipoDocumento,PARAMETROS1.Detalle' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CONCEPTO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="MAYORES.Mayor,AUXILIARES.Auxiliar,TIPODOC.TipoDocumento,PARAMETROS1.Detalle">';
																echo label('Concepto');
															echo '</button>';
														}
													?>
												</th>
												<th>Descripción</th>
												<th>Tipo Emp.</th>
												<th>Imputación</th>
												<th class="right-align">Porc.</th>
												<th>Cuenta Db</th>
												<th>x Centro</th>
												<th>Cuenta Cr</th>
												<th>x Centro</th>
												<th>Tipo Tercero</th>
												<th>Exonerable</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i = 0; $i < count($data['rows']); $i++)
												{
													$reg = $data['rows'][$i];
											?>
											<tr>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/comprobantes/editar/<?= $reg['Id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
												</td>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/comprobantes/borrar/<?= $reg['Id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a>
												</td>
												<td><?= $reg['TipoDocumento'] ?></td>
												<td><?= $reg['Mayor'] .  $reg['Auxiliar']?></td>
												<td><?= $reg['NombreConcepto'] ?></td>
												<td><?= $reg['TipoEmpleado'] ?></td>
												<td><?= $reg['Imputacion'] ?></td>
												<td class="right-align"><?= number_format($reg['Porcentaje'], 2) ?></td>
												<td><?= $reg['CuentaDb'] ?></td>
												<td class="center-align"><?= $reg['DetallaCentroDb'] ? 'SI' : '' ?></td>
												<td><?= $reg['CuentaCr'] ?></td>
												<td class="center-align"><?= $reg['DetallaCentroCr'] ? 'SI' : '' ?></td>
												<td><?= $reg['TipoTercero'] ?></td>
												<td class="center-align"><?= $reg['Exonerable'] ? 'SI' : '' ?></td>
											</tr>
											<?php
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
			<div class="content-overlay"></div>
		</div>
	</div>
</div>
<?php require_once('views/templates/footer.php'); ?>
<?php 
	$lcFiltro = $_SESSION['CONTABILIZACIONACUMULDOS']['Filtro'];
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
									<h3 class="white-text">Contabilización de acumulados</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/contabilizacionSAP/lista', $data['registros'], $_SESSION['CONTABILIZACIONSAP']['Pagina'] );
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
												<th>Cod. SAP</th>
												<th>
													<?php 
														if	( $_SESSION['CONTABILIZACIONSAP']['Orden'] == 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,DETALLESSAP.LineNum' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('EMPLEADO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,DETALLESSAP.LineNum">';
																echo label('Empleado');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['CONTABILIZACIONSAP']['Orden'] == 'DETALLESSAP.AccountCode,DETALLESSAP.CostingCode,DETALLESSAP.ProjectCode,DETALLESSAP.U_InfoCo01' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CUENTA') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="DETALLESSAP.AccountCode,DETALLESSAP.CostingCode,DETALLESSAP.ProjectCode,DETALLESSAP.U_InfoCo01">';
																echo label('Cuenta');
															echo '</button>';
														}
													?>
												</th>
												<th>Centro</th>
												<th>Proyecto</th>
												<th>Descripción</th>
												<th class="right-align">Valor Db.</th>
												<th class="right-align">Valor Cr.</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i = 0; $i < count($data['rows']); $i++)
												{
													$reg = $data['rows'][$i];
											?>
											<tr>
												<td><?= $reg['U_InfoCo01'] ?></td>
												<td><?= $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'] ?></td>
												<td><?= $reg['AccountCode'] ?></td>
												<td><?= $reg['CostingCode']?></td>
												<td><?= $reg['ProjectCode'] ?></td>
												<td><?= $reg['LineMemo'] ?></td>
												<td class="right-align"><?= $reg['Debit'] > 0 ? number_format($reg['Debit'], 2) : '' ?></td>
												<td class="right-align"><?= $reg['Credit'] > 0 ? number_format($reg['Credit'], 2) : '' ?></td>
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
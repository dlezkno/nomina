<?php 
	$lcFiltro = $_SESSION['TERCEROS']['Filtro'];
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
									<h3 class="white-text">Terceros</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/terceros/lista', $data['registros'], $_SESSION['TERCEROS']['Pagina'] );
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
														if	( $_SESSION['TERCEROS']['Orden'] == 'TERCEROS.Documento' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('DOCUMENTO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="TERCEROS.Documento">';
																echo label('Documento');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['TERCEROS']['Orden'] == 'TERCEROS.Codigo' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CÓDIGO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="TERCEROS.Codigo">';
																echo label('Código');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['TERCEROS']['Orden'] == 'TERCEROS.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('NOMBRE') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="TERCEROS.Nombre">';
																echo label('Nombre');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['TERCEROS']['Orden'] == 'TERCEROS.CodigoSAP' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CÓDIGO SAP') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="TERCEROS.CodigoSAP">';
																echo label('Código SAP');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['TERCEROS']['Orden'] == 'TERCEROS.EsEPS DESC,TERCEROS.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('EPS') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="TERCEROS.EsEPS DESC,TERCEROS.Nombre">';
																echo label('EPS');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['TERCEROS']['Orden'] == 'TERCEROS.EsFondoCesantias DESC,TERCEROS.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('FC') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="TERCEROS.EsFondoCesantias DESC,TERCEROS.Nombre">';
																echo label('FC');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['TERCEROS']['Orden'] == 'TERCEROS.EsFondoPensiones DESC,TERCEROS.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('FP') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="TERCEROS.EsFondoPensiones DESC,TERCEROS.Nombre">';
																echo label('FP');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['TERCEROS']['Orden'] == 'TERCEROS.EsCCF DESC,TERCEROS.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CCF') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="TERCEROS.EsCCF DESC,TERCEROS.Nombre">';
																echo label('CCF');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['TERCEROS']['Orden'] == 'TERCEROS.EsARL DESC,TERCEROS.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('ARL') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="TERCEROS.EsARL DESC,TERCEROS.Nombre">';
																echo label('ARL');
															echo '</button>';
														}
													?>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i=0; $i < count($data['rows']); $i++)
												{
													$reg = $data['rows'][$i];
											?>
											<tr>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/terceros/editar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
												</td>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/terceros/borrar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a>
												</td>
												<td><?= $reg['documento'] ?></td>
												<td><?= $reg['codigo'] ?></td>
												<td><?= $reg['nombre'] ?></td>
												<td><?= $reg['codigosap'] ?></td>
												<td><?= $reg['eseps'] == 1 ? 'SI' : '' ?></td>
												<td><?= $reg['esfondocesantias'] == 1 ? 'SI' : '' ?></td>
												<td><?= $reg['esfondopensiones'] == 1 ? 'SI' : '' ?></td>
												<td><?= $reg['esccf'] == 1 ? 'SI' : '' ?></td>
												<td><?= $reg['esarl'] == 1 ? 'SI' : '' ?></td>
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
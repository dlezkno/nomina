<?php 
	$lcFiltro = $_SESSION['CIUDADES']['Filtro'];
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
									<h3 class="white-text">Ciudades</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/ciudades/lista', $data['registros'], $_SESSION['CIUDADES']['Pagina'] );
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
														if	( $_SESSION['CIUDADES']['Orden'] == 'CIUDADES.Ciudad' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CÓDIGO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="CIUDADES.Ciudad">';
																echo label('Código');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['CIUDADES']['Orden'] == 'CIUDADES.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('CIUDAD') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="CIUDADES.Nombre">';
																echo label('Ciudad');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['CIUDADES']['Orden'] == 'CIUDADES.Departamento,CIUDADES.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('DEPARTAMENTO') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="CIUDADES.Departamento,CIUDADES.Nombre">';
																echo label('Departamento');
															echo '</button>';
														}
													?>
												</th>
												<th>
													<?php 
														if	( $_SESSION['CIUDADES']['Orden'] == 'PAISES.Nombre1,CIUDADES.Nombre' )
															echo '<a class="btn btn-block cyan darken-4" href="">' . label('PAÍS') . '</a>';
														else
														{
															echo '<button class="btn btn-block teal lighten-3" type="submit" name="Orden" value="PAISES.Nombre1,CIUDADES.Nombre">';
																echo label('País');
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
														href="<?= SERVERURL ?>/ciudades/editar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Editar" style="color:teal;">
														<i class="small material-icons">edit</i>
													</a>
												</td>
												<td class="center-align">
													<a class="tooltipped"
														href="<?= SERVERURL ?>/ciudades/borrar/<?= $reg['id'] ?>"
														data-position="bottom" data-tooltip="Borrar" style="color:teal;">
														<i class="small material-icons">delete</i>
													</a>
												</td>
												<td><?= $reg['ciudad'] ?></td>
												<td><?= $reg['nombre'] ?></td>
												<td><?= $reg['departamento'] ?></td>
												<td><?= $reg['NombrePais'] ?></td>
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
<?php 
	$lcFiltro = $_SESSION['CANDIDATOS']['Filtro'];
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');
?>
<div id="main">
	<div class="row">
		<div class="content-wrapper-before cyan darken-4"></div>
		<div class="col s12 m12 l12">
			<div class="container">
				<div class="section">
					<div class="card">
						<div class="card-content white-text z-depth-2" style="background-color:#1b2140">
							<div class="row">
								<div class="col s12 m6 l6">
									<h3 class="white-text">Candidatos (En Selección)</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/candidatos/lista', $datos['registros'], $_SESSION['CANDIDATOS']['Pagina'] );
									?>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12">

									<table id="TablaCandidatosEnSeleccion" class="display nowrap" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>OPCIONES</th>
												<th>PSICÓLOGO</th>
												<th>DOCUMENTO</th>
												<th>FECHA EXP.</th>
												<th>APELLIDOS</th>
												<th>NOMBRES</th>
												<th>CARGO</th>
												<th>DIRECCIÓN</th>
												<th>CIUDAD</th>
												<th>E-MAIL</th>
												<th>CELULAR</th>
												<th>ESTADO CIVIL</th>
												<th>FECHA NAC.</th>
												<th>POLÍTICA TD</th>
												<th>DATOS ACT.</th>
												<th>DOCUMENTOS ACT.</th>
												<th>COND. LABORALES</th>
												<th>ENTREVISTA TÉCNICA</th>
												<th>PRUEBA TÉCNICA</th>
												<th>ENTREVISTA PSICOLÓGICA</th>
												<th>REVISIÓN CLIENTE</th>
												<th>CARTA OFERTA</th>
												<th>EXÁMENES MÉDICOS</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i = 0; $i < count($data['rows']); $i++)
												{
													$reg = $data['rows'][$i];

													$Id					= $reg['id'];
													$Sicologo 			= $reg['Nombre1S'] . ' ' . $reg['Nombre2S'] . ' ' . $reg['Apellido1S'];
													$Documento 			= $reg['documento'];
													$FechaExpedicion	= $reg['fechaexpedicion'];
													$Apellidos 			= $reg['apellido1'] . ' ' . $reg['apellido2'];
													$Nombres 			= $reg['nombre1'] . ' ' . $reg['nombre2'];
													$Cargo 				= $reg['NombreCargo'];
													$Direccion 			= $reg['direccion'];
													$Ciudad 			= $reg['NombreCiudad'];
													$Email 				= $reg['email'];
													$Celular 			= $reg['celular'];
													$EstadoCivil 		= $reg['EstadoCivil'];
													$FechaNacimiento 	= $reg['fechanacimiento'];
													$solicitudfirma 	= $reg['solicitudfirma'];

													if ($reg['aceptapoliticatd'])
														$AceptaPoliticaTD = '<i class="material-icons cyan-text darken-4">check</i>';
													else
														$AceptaPoliticaTD = '';

													if ($reg['sel_datosactualizados'])
														$DatosActualizados = '<i class="material-icons cyan-text darken-4">check</i>';
													else
														$DatosActualizados = '';

													if ($reg['sel_documentosactualizados'])
														$DocumentosActualizados = '<i class="material-icons cyan-text darken-4">check</i>';
													else
														$DocumentosActualizados = '';

													if ($reg['sel_condicioneslaborales'])
														$CondicionesLaborales = '<i class="material-icons cyan-text darken-4">check</i>';
													else
														$CondicionesLaborales = '';

													if ($reg['sel_entrevistatecnica'] == 1)
													{
														$EntrevistaTecnica = '<a class="btn btn-sm cyan darken-4">REALIZADA</a>';

														$ServerURL = SERVERURL;
														$cDirectorio = 'documents/' . trim($Documento) . '_' . strtoupper(trim($reg['apellido1']) . '_' . trim($reg['apellido2']) . '_' . trim($reg['nombre1']) . '_' . trim($reg['nombre2'])) . '/PRUEBAS_SICOTECNICAS';
														$dir = $_SERVER['DOCUMENT_ROOT'].'/Nomina/documents/' . trim($Documento) . '_' . strtoupper(trim($reg['apellido1']) . '_' . trim($reg['apellido2']) . '_' . trim($reg['nombre1']) . '_' . trim($reg['nombre2'])) . '/PRUEBAS_SICOTECNICAS';
														$archivo = "";
														if (file_exists($dir)){
															$files = scandir($dir);
															for($k = 0; $k < count($files); $k ++){
																if(strrpos($files[$k], "_ENTREVISTA_TECNICA") !== FALSE){
																	$archivo = $files[$k];
																}
															}
														}
														

														$DescargarEntrevistaTecnica = <<<EOD
															<a href="$ServerURL/$cDirectorio/$archivo" target="_blank" class="btn btn-small teal lighten-3 tooltipped" data-position="bottom" data-tooltip="Ver documento">
																<i class='fas fa-eye'></i>
															</a>
														EOD;
													}
													else
													{
														$EntrevistaTecnica = '<a class="btn btn-sm red darken-4" href="' . SERVERURL . '/entrevista2/editar/' . $Id .'">PENDIENTE</a>';
														$DescargarEntrevistaTecnica = '';
													}

													if ($reg['sel_pruebatecnica'])
														$PruebaTecnica = '<i class="material-icons cyan-text darken-4">check</i>';
													else
														$PruebaTecnica = '';

													if ($reg['sel_entrevistasicologica'] == 1)
													{
														$EntrevistaSicologica = '<a class="btn btn-sm cyan darken-4">REALIZADA</a>';

														$ServerURL = SERVERURL;
														$cDirectorio = 'documents/' . trim($Documento) . '_' . strtoupper(trim($reg['apellido1']) . '_' . trim($reg['apellido2']) . '_' . trim($reg['nombre1']) . '_' . trim($reg['nombre2'])) . '/PRUEBAS_SICOTECNICAS';
														$dirpsico = $_SERVER['DOCUMENT_ROOT'].'/Nomina/documents/' . trim($Documento) . '_' . strtoupper(trim($reg['apellido1']) . '_' . trim($reg['apellido2']) . '_' . trim($reg['nombre1']) . '_' . trim($reg['nombre2'])) . '/PRUEBAS_SICOTECNICAS';
														$archivo = "";
														if (file_exists($dirpsico)){
															$files = scandir($dirpsico);
															for($j = 0; $j < count($files); $j ++){
																if(strrpos($files[$j], "ENTREVISTA_PSICOLOGICA") !== FALSE){
																	$archivo = $files[$j];
																}
															}
														}

														$DescargarEntrevistaSicologica = <<<EOD
															<a href="$ServerURL/$cDirectorio/$archivo" target="_blank" class="btn btn-small teal lighten-3 tooltipped" data-position="bottom" data-tooltip="Ver documento">
																<i class='fas fa-eye'></i>
															</a>
														EOD;
													}
													else
													{
														$EntrevistaSicologica = '<a class="btn btn-sm red darken-4" href="' . SERVERURL . '/entrevista1/editar/' . $Id .'">PENDIENTE</a>';
														$DescargarEntrevistaSicologica = '';
													}

													if ($reg['sel_revisioncliente'])
														$RevisionCliente = '<i class="material-icons cyan-text darken-4">check</i>';
													else
														$RevisionCliente = '';

													if ($reg['sel_cartaoferta'])
														$CartaOferta = '<i class="material-icons cyan-text darken-4">check</i>';
													else
														$CartaOferta = '';

													if ($reg['sel_examenesmedicos'])
														$ExamenesMedicos = '<i class="material-icons cyan-text darken-4">check</i>';
													else
														$ExamenesMedicos = '';

													echo "<tr>";
													if($solicitudfirma != '' && $solicitudfirma != '0' && $solicitudfirma != NULL){
														echo '<td><button class="btn btn-sm red darken-4" type="submit" name="Action" value="ELIFIRM_'.$Id.'">
															FIRM. PEND.
														</button></td>';	
													}else{
														echo "<td></td>";	
													}
													
													echo "<td>$Sicologo</td>";
													echo "<td>";
													if ($reg['aceptapoliticatd'] == 0)
													{
														echo '<a href="' . SERVERURL . "/candidatos/editar/$Id" . '">' . $Documento . '</a>';
													}
													else
													{
														echo '<a href="' . SERVERURL . "/misdatos/editar/$Id" . '">' . $Documento . '</a>';
													}

													echo "</td>";
													echo "<td>$FechaExpedicion</td>";
													echo "<td>$Apellidos</td>";
													echo "<td>$Nombres</td>";
													echo "<td>$Cargo</td>";
													echo "<td>$Direccion</td>";
													echo "<td>$Ciudad</td>";
													echo "<td>$Email</td>";
													echo "<td>$Celular</td>";
													echo "<td>$EstadoCivil</td>";
													echo "<td>$FechaNacimiento</td>";
													echo "<td>$AceptaPoliticaTD</td>";
													echo "<td>$DatosActualizados</td>";
													echo "<td>$DocumentosActualizados</td>";
													echo "<td>$CondicionesLaborales</td>";
													echo "<td>$EntrevistaTecnica $DescargarEntrevistaTecnica</td>";
													echo "<td>$PruebaTecnica</td>";
													echo "<td>$EntrevistaSicologica $DescargarEntrevistaSicologica</td>";
													echo "<td>$RevisionCliente</td>";
													echo "<td>$ExamenesMedicos</td>";
													echo "<td>$CartaOferta</td>";
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
			<div class="content-overlay"></div>
		</div>
	</div>
</div>
<?php require_once('views/templates/footer.php'); ?>

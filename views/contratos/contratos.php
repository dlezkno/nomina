<?php 
	$lcFiltro = NULL;
	if(isset($_SESSION['CANDIDATOS'])) $lcFiltro = $_SESSION['CANDIDATOS']['Filtro'];
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
									<h3 class="white-text">Candidatos (En Contratación)</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/contratos/lista', $datos['registros'], $_SESSION['CONTRATOS']['Pagina'] );
									?>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12">
									<button class="btn btn-block teal darken-3" type="submit" name="Action" value="DESCARGAS">
										DESCARGAR DOCUMENTOS FIRMADOS
									</button>									
								</div>
							</div>
							<div class="row">
								<div class="col s12">
									<table id="TablaCandidatosEnContratacion" class="display nowrap" cellspacing="0" width="100%">
										<thead>
											<tr>
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
												<th>SUELDO BÁSICO</th>
												<th>AFILIACIONES</th>
												<th>COND. LABORALES</th>
												<th>CONTRATOS ENVIADOS</th>
												<th>CONTRATOS FIRMADOS</th>
											</tr>
										</thead>
										<tbody>
											<?php
												for ($i = 0; $i < count($data['rows']); $i++)
												{
													$reg = $data['rows'][$i];

													$Id					= $reg['id'];
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
													$SueldoBasico 		= $reg['sueldobasico'];

													if ($reg['cnt_documentosactualizados'])
														$DocumentosActualizados = '<i class="material-icons cyan-text darken-4">check</i>';
													else
														$DocumentosActualizados = '';

													if ($reg['cnt_condicioneslaborales'])
														$CondicionesLaborales = '<i class="material-icons cyan-text darken-4">check</i>';
													else
														$CondicionesLaborales = '';

													if ($reg['cnt_contratosenviados'])
														$ContratosEnviados = '<i class="material-icons cyan-text darken-4">check</i>';
													else
														$ContratosEnviados = '';

													if ($reg['cnt_contratosfirmados'] == 1 &&
														$reg['cnt_contratosenviados'] == 1 &&
														$reg['cnt_condicioneslaborales'] == 1 &&
														$reg['cnt_documentosactualizados'] == 1){
														$ContratosFirmados = '<i class="material-icons cyan-text darken-4">check</i>';
													}
													else{
														if($reg['cnt_contratosfirmados'] == 0 &&
															$reg['cnt_contratosenviados'] == 1){
														    $ContratosFirmados = '<button class="btn btn-block teal darken-3" type="submit" name="Action" value="VALIDATE_FIRM_'.$Id.'">VALIDAR</button>';
														}else{
														    $ContratosFirmados = '';
														}
													}

													echo "<tr>";

													echo "<td>";
													echo '<a href="' . SERVERURL . "/contratos/editar/$Id" . '">' . $Documento . '</a>';
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
													echo "<td>" . number_format($SueldoBasico, 0) . "</td>";
													echo "<td style='text-align:center;'>$DocumentosActualizados</td>";
													echo "<td style='text-align:center;'>$CondicionesLaborales</td>";
													echo "<td style='text-align:center;'>$ContratosEnviados</td>";
													echo "<td style='text-align:center;'>$ContratosFirmados</td>";
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

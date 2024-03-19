<?php 
	$lcFiltro = $_SESSION['RENOVACIONES']['Filtro'];
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	$Select = '';
	foreach (array_keys($data['templates']) as $key) {
		$Select .= '<option value=' . $key . '>' . $key . '</option>';
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
									<h3 class="white-text">Renovaciones de contratos</h3>
								</div>
								<div class="col s12 m6 l6 right-align">
									<?php
										if	( isset($_SESSION['Paginar']) AND $_SESSION['Paginar'] )
											echo paginar(SERVERURL . '/renovaciones/lista', $data['registros'], $_SESSION['RENOVACIONES']['Pagina'] );
									?>
								</div>
							</div>
						</div>
						<div class="card-content">
							<div class="row">
								<div class="col s12">
									<!-- Modal Structure -->
									<div id="modal1" class="modal" style="width:90%; max-height: 85%;">
										<div class="modal-content">
											<input type="hidden" id="modal_input">
											<input type="hidden" id="modal_AcctionType">
											<div class="row">
												<div class="input-field col s6">
													<?php 
														get(label('Asunto*'), 'modal_subject', '', 'text', 255, FALSE, '', 'fas fa-pen'); 
													?>
												</div>
												<div class="input-field col s6">
													<?php 
														get(label('Para*'), 'modal_to', '', 'text', 255, FALSE, '', 'fas fa-pen'); 
													?>
												</div>

												<div class="row">
													<div class="input-field col s6">
														<button class="btn btn-sm cyan darken-4" type="button" id="modal_btn_edit">
															Editar EMAIL
														</button>
														<button class="btn btn-sm cyan darken-4" type="button" id="modal_btn_preview">
															Vista previa EMAIL
														</button>
													</div>
													<div class="input-field col s6" id="container_btsn">
														<button class="btn btn-sm cyan darken-4" type="button" id="modal_btn_edit_adjunto">
															Editar ADJUNTO
														</button>
														<button class="btn btn-sm cyan darken-4" type="button" id="modal_btn_preview_adjunto">
															Vista previa ADJUNTO
														</button>
													</div>
												</div>
												<div class="input-field col s6">
													<div  id="modal_template_edit">
														<?php 
															get(label(''), 'modal_template', '', 'textarea', 5, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
													<div id='modal_template_preview' style="all: revert;"></div>
												</div>
												<div class="input-field col s6">
													<div id="modal_adjunto_edit">
														<?php 
															get(label('Plantilla ADJUNTO*'), 'modal_adjunto', '', 'textarea', 5, FALSE, '', 'fas fa-pen'); 
														?>
													</div>
													<div id='modal_adjunto_preview' style="all: revert;"></div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<a
												href="#!"
												class="modal-close waves-effect waves-green btn-flat"
												onclick="handleClickModel()">
												GUARDAR
											</a>

											<script>
												document.getElementById('modal_btn_edit')
													.addEventListener('click', () => {
														$('#modal_template_edit').show();
														$('#modal_template_preview').hide();
													});
												document.getElementById('modal_btn_edit_adjunto')
													.addEventListener('click', () => {
														$('#modal_adjunto_edit').show();
														$('#modal_adjunto_preview').hide();
													});
												
												document.getElementById('modal_btn_preview_adjunto')
													.addEventListener('click', () => {
														CKEDITOR.instances.modal_template.updateElement();
														document.getElementById("modal_adjunto_preview").innerHTML = $('#modal_adjunto').val();
														$('#modal_adjunto_edit').hide();
														$('#modal_adjunto_preview').show();
													});

												document.getElementById('modal_btn_preview')
													.addEventListener('click', () => {
														CKEDITOR.instances.modal_template.updateElement();
														document.getElementById("modal_template_preview").innerHTML = $('#modal_template').val();
														$('#modal_template_edit').hide();
														$('#modal_template_preview').show();
													});

												function handleClickModel() {
													const inputModal = document.getElementById(`modal_input`).value;
													const typeModal = document.getElementById(`modal_AcctionType`).value;
													const subjectModal = document.getElementById(`modal_subject`).value;
													const modalto = document.getElementById(`modal_to`).value;
													const templateModal = document.getElementById(`modal_template`).value;													
													const templateModalAdjunto = document.getElementById(`modal_adjunto`).value;


													const to = document.getElementById(`${inputModal}-${typeModal}-to`);
													to.value = modalto;
													const subject = document.getElementById(`${inputModal}-${typeModal}-subject`);
													subject.value = subjectModal;
													const template = document.getElementById(`${inputModal}-${typeModal}-template`);
													template.value = templateModal;
													const templateAdjunto = document.getElementById(`${inputModal}-${typeModal}-adjunto`);
													templateAdjunto.value = templateModalAdjunto;
												}
											</script>
										</div>
									</div>
									<table>
										<thead>
											<tr>
												<th>GERENTE PROYECTO</th>
												<th>DOCUMENTO<br>NOMBRE EMPLEADO<br>TIPO CONTRATO</th>
												<th>CENTRO-PROYECTO/CARGO</th>
												<th>FECHA ING.<br>FECHA VCTO.</th>
												<th></th>
												<th>E-mail Adicional/OBSERVACIONES</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$inputAnterior = '';

												for ($i=0; $i < count($data['rows']); $i++)
												{
													$reg = $data['rows'][$i];
											?>
											<tr>
												<td>
													<?php 
														if (! empty($reg['Apellido1GP']))
															echo $reg['Apellido1GP'] . ' ' . $reg['Nombre1GP'];
													?>
												</td>
												<td><?= $reg['Documento'] . '<br>' . $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'] . '<br>' .  $reg['TipoContrato'] ?></td>
												<td><?= $reg['Centro'] . ' ' . $reg['NombreCentro'] . '<br>' . $reg['NombreCargo'] ?></td>
												<td><?= $reg['FechaIngreso'] . '<br>' .  $reg['FechaVencimiento'] ?></td>
												<td>
													<?php
														$FechaVcto = $reg['FechaVencimiento'];
														$datetime1 = date_create(date('Y-m-d'));
														$datetime2 = date_create($FechaVcto);
														$contador = date_diff($datetime1, $datetime2);
														if ($FechaVcto < date('Y-m-d'))
															echo '<span class="badge red">CRÍTICO</span>';
														elseif ($contador->format('%a') <= 30)
															echo '<span class="badge red">' . $contador->format('%a') . ' D</span>';
														elseif ($contador->format('%a') <= 45)
															echo '<span class="badge orange">' . $contador->format('%a') . ' D</span>';
														else
															echo '<span class="badge green">' . $contador->format('%a') . ' D</span>';
														echo '<br>';
														if (! is_null($reg['FechaRetiro'])) 
															echo '<span class="badge green">EN RETIRO</span>';
													?>
												</td>
												<td>
													<?php 
														$input = 'DataRenovations_' . $reg['IdEmpleado'] . '_' . $reg['IdGP'];

														if ($input <> $inputAnterior) {
															$Documento = $reg['Documento'];
															$NombreEmpleado = $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'];
															$NombreCargo = $reg['NombreCargo'];
															$TipoContrato = $reg['TipoContrato'];
															$FechaVencimiento = $reg['FechaVencimiento'];
															$Prorrogas = $reg['Prorrogas'];

															$cHTML = <<<EOD
															<table style='all: revert;'>
																<tr style='all: revert;'>
																	<td style='all: revert;'><b>Documento: </b></td>
																	<td style='all: revert;'>$Documento</td>
																</tr>
																<tr style='all: revert;'>
																	<td style='all: revert;'><b>Nombre Empleado: </b></td>
																	<td style='all: revert;'>$NombreEmpleado</td>
																</tr>
																<tr style='all: revert;'>
																	<td style='all: revert;'><b>Nombre Cargo: </b></td>
																	<td style='all: revert;'>$NombreCargo</td>
																</tr>
																<tr style='all: revert;'>
																	<td style='all: revert;'><b>Tipo Contrato: </b></td>
																	<td style='all: revert;'>$TipoContrato</td>
																</tr>
																<tr style='all: revert;'>
																	<td style='all: revert;'><b>Fecha Vencimiento: </b></td>
																	<td style='all: revert;'>$FechaVencimiento</td>
																</tr>
																<tr style='all: revert;'>
																	<td style='all: revert;'><b>Prorrogas: </b></td>
																	<td style='all: revert;'>$Prorrogas</td>
																</tr>
															</table>
															EOD;
															foreach ($data['templates'] as $key => $item) {
																$subject = $item['asunto'];
																$template = str_replace('"', '', $item['plantilla']);
																$template = str_replace('<<Logotipo>>', LOGOTIPO, $template);
																$templateAdjunto = "";
																if (! empty($reg['Apellido1GP']))
																	$template = str_replace('<<NombreGerente>>', $reg['Apellido1GP'] . ' ' . $reg['Nombre1GP'], $template);
																	$template = str_replace('<<RelacionEmpleados>>', $cHTML, $template);
																
																if($key == "Renovación" ){
																	$templateAdjunto = str_replace('"', '', $data["templatesAdjunto"]["AdjuntoRenovación"]['plantilla']);
																}
																if($key == "NoRenovación" ){
																	$templateAdjunto = str_replace('"', '', $data["templatesAdjunto"]["AdjuntoNoRenovación"]['plantilla']);		
																}
																echo <<<EOD
																	<input type="hidden" name="$input-$key-to" id="$input-$key-to" value="">
																	<input type="hidden" name="$input-$key-subject" id="$input-$key-subject" value="$subject">
																	<input type="hidden" name="$input-$key-template" id="$input-$key-template" value="$template">
																	<input type="hidden" name="$input-$key-adjunto" id="$input-$key-adjunto" value="$templateAdjunto">
																EOD;
															}
															get(label('Tipo de acción*'), $input . '-AcctionType', $Select, 'select', 0, FALSE, '', 'fas fa-ellipsis-v');
													?>

															<script>
																document.getElementById('<?php echo $input ?>-AcctionType')
																	.addEventListener('change', (e) => {
																		$('.modal')?.modal('open');
																		const inputName = '<?php echo $input ?>';
																		if(e.target.value == "Confirmación"){
																			$("#container_btsn").css("display","none");
																		}else{
																			$("#container_btsn").css("display","block");
																		}
																		const type = document.getElementById(`${inputName}-AcctionType`).value;
																		const subjectValue = document.getElementById(`${inputName}-${type}-subject`).value;
																		const toValue = document.getElementById(`${inputName}-${type}-to`).value;
																		const templateValue = document.getElementById(`${inputName}-${type}-template`).value;
																		const templateAdjuntoValue = document.getElementById(`${inputName}-${type}-adjunto`).value;

																		$(`#modal_input`).val(inputName);
																		$(`#modal_AcctionType`).val(type);
																		$(`#modal_subject`).val(subjectValue);
																		$(`#modal_to`).val(toValue);
																		$(`#modal_template`).val(templateValue);
																		$(`#modal_template_preview`).html(templateValue);

																		
																		$(`#modal_adjunto`).val(templateAdjuntoValue);
																		$(`#modal_adjunto_preview`).html(templateAdjuntoValue);

																		$('#modal_template_edit').show();
																		$('#modal_adjunto_edit').show();

																		$(`#modal_subject`).focus();
																		$(`#modal_template`).focus();
																		$(`#modal_adjunto`).focus();

																		$('#modal_template_edit').hide();
																		$('#modal_template_preview').show();

																		$('#modal_adjunto_edit').hide();
																		$('#modal_adjunto_preview').show();


																		function isWysiwygareaAvailable() {
																			if (CKEDITOR.revision == ('%RE' + 'V%')) {
																				return true;
																			}
																			return !!CKEDITOR.plugins.get('wysiwygarea');
																		}

																		var $id = "modal_template";
																		var editorElement = CKEDITOR.document.getById($id);

																		var $idAdjunto = "modal_adjunto";
																		var editorAdjunto = CKEDITOR.document.getById($id);

																		var wysiwygareaAvailable = isWysiwygareaAvailable(),
																		isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');
																		var obj = {
																			height: 350,
																			buttons: 'Link,Unlink',
																			language: 'fr',
																			uiColor: '#006064',
																			toolbar: [
																				{ name: 'document', 
																					items: [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates' , 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },	// Defines toolbar group with name (used to create voice label) and items in 3 subgroups.
																				{ name: 'basicstyles', items: [ 'Format', 'Font', 'FontSize', 'Bold', 'Italic' ] },
																				{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
																				{ name: 'paragraph', 
																					groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ],
																					 items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl',  ] }
																			]
																		}
																		if (wysiwygareaAvailable) {
																			CKEDITOR.replace($id, obj);																			
																			CKEDITOR.replace($idAdjunto, obj);
																		} else {
																			editorAdjunto.setAttribute('contenteditable', 'true');
																			editorElement.setAttribute('contenteditable', 'true');
																			CKEDITOR.inline($id, obj);																			
																			CKEDITOR.inline($idAdjunto, obj);
																		}

																	});
															</script>
													<?php 
															$inputAnterior = $input;
														}
													?>
													<br>
												</td>
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
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.5.4/ckeditor.js?cache=305T"></script>
<?php require_once('views/templates/footer.php'); ?>

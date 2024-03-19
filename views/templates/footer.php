				<footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow">
					<div class="footer-copyright">
						<div class="container">
							<span>&copy; 2022-<?php echo date('Y'); ?> <a href="" target="_blank"><?= COMPANY ?></a> Todos los
								derechos reservados.</span>
							<span class="hide-on-small-only">Versión <?= VERSION ?></span>
							<span class="right">Fecha: <?= script_fecha() ?></span>

						</div>
					</div>
				</footer>
			</form>
		</div>

  		<script src="<?= SERVERURL ?>/templates/materialize/app-assets/js/vendors.min.js"></script>

		<script src="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/data-tables/js/jquery.dataTables.min.js">
		</script>
		<script
			src="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js">
		</script>
		<script src="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/data-tables/js/dataTables.select.min.js">
		</script>

		<script src="<?= SERVERURL ?>/templates/materialize/app-assets/js/plugins.js"></script>
		<script src="<?= SERVERURL ?>/templates/materialize/app-assets/js/search.js"></script>
		<script src="<?= SERVERURL ?>/templates/materialize/app-assets/js/custom/custom-script.js"></script>
		<script src="<?= SERVERURL ?>/templates/materialize/app-assets/js/scripts/customizer.js"></script>
		<script src="<?= SERVERURL ?>/templates/materialize/app-assets/js/scripts/data-tables.js"></script>
		<script src="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/select2/select2.full.min.js"></script>
		<script src="<?= SERVERURL ?>/templates/materialize/app-assets/js/scripts/app-chat.js"></script>
		<script src="<?= SERVERURL ?>/assets/js/main.js"></script>

		<script>
			$('#AddNovedad').click(function(){
				Documento = document.getElementById('Documento').value;
				NombreEmpleado = document.getElementById('NombreEmpleado').value;
				Concepto = document.getElementById('Concepto').value;
				NombreConcepto = document.getElementById('NombreConcepto').value;
				Horas = document.getElementById('Horas').value;
				Valor = document.getElementById('Valor').value;
				Tercero = document.getElementById('Tercero').value;
				NombreTercero = document.getElementById('NombreTercero').value;

				if (Documento != '' && NombreEmpleado != 'EMPLEADO NO EXISTE   ' && Concepto != '' && NombreConcepto != 'CONCEPTO NO EXISTE' && (Horas > 0 || Valor > 0)){
					var newrow = $('#next').append(`
						<div class="row">
							<div class="col s2">
								<input type="text" class="validate" id="aConcepto[]" name="aConcepto[]" maxlength="5" value="` + Concepto + `" readonly>
							</div>
							<div class="col s3">
								<input type="text" class="validate" id="aNombreConcepto[]" name="aNombreConcepto[]" maxlength="60" value="` + NombreConcepto + `" readonly>
							</div>
							<div class="col s1">
								<input type="number" class="validate" id="aHoras[]" name="aHoras[]" maxlength="8" value="` + Horas + `" readonly>
							</div>
							<div class="col s1">
								<input type="number" class="validate" id="aValor[]" name="aValor[]" maxlength="12" value="` + Valor + `" readonly>
							</div>
							<div class="col s1">
								<input type="text" class="validate" id="aTercero[]" name="aTercero[]" maxlength="10" value="` + Tercero + `" readonly>
							</div>
							<div class="col s3">
								<input type="text" class="validate" id="aNombreTercero[]" name="aNombreTercero[]" maxlength="60" value="` + NombreTercero + `" readonly>
							</div>
							<button type="button" class="btnRemove btn btn-success">Borrar</button>
						</div>`);

					document.getElementById('Concepto').value = '';
					document.getElementById('NombreConcepto').value = '';
					document.getElementById('Horas').value = 0;
					document.getElementById('Valor').value = 0;
					document.getElementById('Tercero').value = '';
					document.getElementById('NombreTercero').value = '';
				}
			});
			
			// Removing event here
			$('body').on('click','.btnRemove',function() {
				$(this).closest('div').remove()
			});
		</script>

		<script>
			$(document).ready(function() 
			{

				$("#download_format").click(function(){
					window.open("<?php echo SERVERURL; ?>/assets/comprobantes.xlsx","_blank");
				});

				$('#checkall').on('click',function() {
					var check = $(".checkreli").prop("checked");
					if(check){
						$(".checkreli").prop('checked', false);
					}else{
						$(".checkreli").prop('checked', true);
					}
					
				});
				

				// if($("#nuevaContrasena").length > 0){
				// 	var regex = "^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])([A-Za-z\d$@$!%*?&]|[^ ]){8,15}$";
				// 	$("#nuevaContrasena").attr("pattern", regex)
				// 	$("#repiteNuevaContrasena").attr("pattern", regex)
				// 	document.getElementById("nuevaContrasena").oninvalid = function(){this.setCustomValidity("Lha contraseña debe tener minimo una letra en minuscula, una letra en mayuscula, un numero y un caracter especial")}
				// 	document.getElementById("repiteNuevaContrasena").oninvalid = function(){this.setCustomValidity("La contraseña debe tener minimo una letra en minuscula, una letra en mayuscula, un numero y un caracter especial")}
					
					
				// }
				



				var table = $('#TablaSolicitudPersonal').DataTable({
					"language": {
            			"sProcessing": "Procesando...",
            			"sLengthMenu": "Mostrar _MENU_ registros por página",
            			"sZeroRecords": "No se encontraron resultados",
            			"sEmptyTable": "Ningún dato disponible en esta tabla",
            			"sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            			"sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            			"sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            			"sInfoPostFix": "",
            			"sSearch": "Buscar:",
            			"sUrl": "",
            			"sInfoThousands": ",",
            			"sLoadingRecords": "Cargando...",
            			"oPaginate": {
							"sFirst": "Primero",
							"sLast": "Último",
							"sNext": "Siguiente",
							"sPrevious": "Anterior"
						},
						"oAria": {
							"sSortAscending": ": Activar para ordenar la columna de manera ascendente",
							"sSortDescending": ": Activar para ordenar la columna de manera descendente"
						}
					},				    
					// "responsive": true,
					"scrollX": true,
					"searching": true, 
					"lengthMenu": [10, 25, 50, 100],
					"pageLength": 10,
					dom: 'Bfrtip',
					buttons: [
						'colvis',
						'excel',
						'print'
					],
				});



				
				
				var table = $('#TablaCandidatosEnSeleccion').DataTable({
					"language": {
            			"sProcessing": "Procesando...",
            			"sLengthMenu": "Mostrar _MENU_ registros por página",
            			"sZeroRecords": "No se encontraron resultados",
            			"sEmptyTable": "Ningún dato disponible en esta tabla",
            			"sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            			"sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            			"sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            			"sInfoPostFix": "",
            			"sSearch": "Buscar:",
            			"sUrl": "",
            			"sInfoThousands": ",",
            			"sLoadingRecords": "Cargando...",
            			"oPaginate": {
							"sFirst": "Primero",
							"sLast": "Último",
							"sNext": "Siguiente",
							"sPrevious": "Anterior"
						},
						"oAria": {
							"sSortAscending": ": Activar para ordenar la columna de manera ascendente",
							"sSortDescending": ": Activar para ordenar la columna de manera descendente"
						}
					},				    
					// "responsive": true,
					"scrollX": true,
					"searching": true, 
					"lengthMenu": [10, 25, 50, 100],
					"pageLength": 10,
					dom: 'Bfrtip',
					buttons: [
						'colvis',
						'excel',
						'print'
					],
				});

				var table = $('#TablaCandidatosEnContratacion').DataTable({
					"language": {
            			"sProcessing": "Procesando...",
            			"sLengthMenu": "Mostrar _MENU_ registros por página",
            			"sZeroRecords": "No se encontraron resultados",
            			"sEmptyTable": "Ningún dato disponible en esta tabla",
            			"sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            			"sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            			"sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            			"sInfoPostFix": "",
            			"sSearch": "Buscar:",
            			"sUrl": "",
            			"sInfoThousands": ",",
            			"sLoadingRecords": "Cargando...",
            			"oPaginate": {
							"sFirst": "Primero",
							"sLast": "Último",
							"sNext": "Siguiente",
							"sPrevious": "Anterior"
						},
						"oAria": {
							"sSortAscending": ": Activar para ordenar la columna de manera ascendente",
							"sSortDescending": ": Activar para ordenar la columna de manera descendente"
						}
					},				    
					// "responsive": true,
					"scrollX": true,
					"searching": true, 
					"lengthMenu": [10, 25, 50, 100],
					"pageLength": 10,
					dom: 'Bfrtip',
					buttons: [
						'colvis',
						'excel',
						'print'
					],
				});

				$('#Documento').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Documento=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getEmpleados.php",
						data: dataString,
						success: function(data) {
							$('#suggestions').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('Id');
								$('#IdEmpleado').val(Id);
								$('#Documento').val($('#' + Id).attr('data'));
								$('#suggestions').fadeOut(1000);
								$('#Documento').focus();

								return false;
							});
						}
					});
				});				

				$('#Concepto').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Concepto=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getConceptos.php",
						data: dataString,
						success: function(data) {
							$('#suggestions').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('Id');
								$('#IdConcepto').val(Id);
								$('#Concepto').val($('#' + Id).attr('data'));
								$('#suggestions').fadeOut(1000);
								$('#Concepto').focus();

								return false;
							});
						}
					});
				});				

				$('#Tercero').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Tercero=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getTerceros.php",
						data: dataString,
						success: function(data) {
							$('#suggestions').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('Id');
								$('#IdTercero').val(Id);
								$('#Tercero').val($('#' + Id).attr('data'));
								$('#suggestions').fadeOut(1000);
								$('#Tercero').focus();

								return false;
							});
						}
					});
				});				

				$('#CiudadExpedicion').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Ciudad=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getCiudades.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsCiudadExpedicion').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreCiudad = this.innerText;
								$('#IdCiudadExpedicion').val(Id);
								$('#CiudadExpedicion').val($('#' + Id).attr('data'));
								$('#suggestionsCiudadExpedicion').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#CiudadNacimiento').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Ciudad=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getCiudades.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsCiudadNacimiento').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreCiudad = this.innerText;
								$('#IdCiudadNacimiento').val(Id);
								$('#CiudadNacimiento').val($('#' + Id).attr('data'));
								$('#suggestionsCiudadNacimiento').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#Ciudad').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Ciudad=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getCiudades.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsCiudad').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreCiudad = this.innerText;
								$('#IdCiudad').val(Id);
								$('#Ciudad').val($('#' + Id).attr('data'));
								$('#suggestionsCiudad').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#CiudadEmpresa').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Ciudad=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getCiudades.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsCiudadEmpresa').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreCiudad = this.innerText;
								$('#IdCiudadEmpresa').val(Id);
								$('#CiudadEmpresa').val($('#' + Id).attr('data'));
								$('#suggestionsCiudadEmpresa').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#CiudadTrabajo').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Ciudad=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getCiudades.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsCiudadTrabajo').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreCiudad = this.innerText;
								$('#IdCiudadTrabajo').val(Id);
								$('#CiudadTrabajo').val(NombreCiudad);
								$('#suggestionsCiudadTrabajo').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#NombreEPS').on('keyup', function() {
					
					event.preventDefault();
					$('#IdEPS').val("");
					var key = $(this).val();		
					var dataString = 'Tercero=' + key + '&EPS=1';
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getTerceros.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsEPS').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreEPS = this.innerText;
								$('#IdEPS').val(Id);
								$('#NombreEPS').val($('#' + Id).attr('data'));
								$('#suggestionsEPS').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#NombreFC').on('keyup', function() {
					event.preventDefault();
					$('#IdFondoCesantias').val("");
					var key = $(this).val();		
					var dataString = 'Tercero=' + key + '&FC=1';
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getTerceros.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsFC').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreFC = this.innerText;
								$('#IdFondoCesantias').val(Id);
								$('#NombreFC').val($('#' + Id).attr('data'));
								$('#suggestionsFC').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#NombreFP').on('keyup', function() {
					event.preventDefault();
					$('#IdFondoPensiones').val("");
					var key = $(this).val();		
					var dataString = 'Tercero=' + key + '&FP=1';
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getTerceros.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsFP').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreFP = this.innerText;
								$('#IdFondoPensiones').val(Id);
								$('#NombreFP').val($('#' + Id).attr('data'));
								$('#suggestionsFP').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#NombreCCF').on('keyup', function() {
					event.preventDefault();
					$('#IdCCF').val("");
					var key = $(this).val();		
					var dataString = 'Tercero=' + key + '&CCF=1';
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getTerceros.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsCCF').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreCCF = this.innerText;
								$('#IdCCF').val(Id);
								$('#NombreCCF').val($('#' + Id).attr('data'));
								$('#suggestionsCCF').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#NombreARL').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Tercero=' + key + '&ARL=1';
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getTerceros.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsARL').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreARL = this.innerText;
								$('#IdARL').val(Id);
								$('#NombreARL').val($('#' + Id).attr('data'));
								$('#suggestionsARL').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#NombreBanco').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Banco=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getBancos.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsBanco').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreBanco = this.innerText;
								$('#IdBanco').val(Id);
								$('#NombreBanco').val($('#' + Id).attr('data'));
								$('#suggestionsBanco').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#Idioma').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Idioma=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getIdiomas.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsIdioma').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreIdioma = this.innerText;
								$('#IdIdioma').val(Id);
								$('#Idioma').val($('#' + Id).attr('data'));
								$('#suggestionsIdioma').fadeOut(1000);

								return false;
							});
						}
					});
				});				



				$('#NombreJefe').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val().split(" ").join("");		
					var dataString = 'NombreJefe=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getBoss.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsJefe').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreJefe = this.innerText;
								$('#IdJefe').val(Id);
								$('#NombreJefe').val($('#' + Id).attr('data'));
								$('#suggestionsJefe').fadeOut(1000);
								return false;
							});
						}
					});
				});	




				if($("#NombreCargo").val() == "APRENDIZ"){
					$(".containerAprendiz").css("display","block");
				}else{
					$(".containerAprendiz").css("display","none");
				}

				$('#NombreCargo').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'NombreCargo=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getCargos.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsCargo').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreCargo = this.innerText;
								$('#IdCargo').val(Id);
								$('#NombreCargo').val($('#' + Id).attr('data'));
								$('#suggestionsCargo').fadeOut(1000);
								if(Id == "133"){
									$(".containerAprendiz").css("display","block")
								}else{
									$(".containerAprendiz").css("display","none")
								}
								return false;
							});
						}
					});
				});				

				$('#NombreCentro').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'NombreCentro=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getCentros.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsCentro').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreCentro = this.innerText;
								$('#IdCentro').val(Id);
								$('#NombreCentro').val(NombreCentro);
								$('#suggestionsCentro').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#NombreProyecto').on('keyup', function() {
					event.preventDefault();
					$('#IdProyecto').val("");
					var key = $(this).val();		
					var dataString = 'NombreProyecto=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getProyectos.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsProyecto').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreProyecto = this.innerText;
								$('#IdProyecto').val(Id);
								$('#NombreProyecto').val(NombreProyecto);
								$('#suggestionsProyecto').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('#NombreSede').on('keyup', function() {
					event.preventDefault();
					var key = $(this).val();		
					var dataString = 'Sede=' + key;
					
					$.ajax({
						type: "POST",
						url: "<?= SERVERURL ?>/helpers/getSedes.php",
						data: dataString,
						success: function(data) {
							$('#suggestionsSede').fadeIn(1000).html(data);
							$('.suggest-element').on('click', function(){
								var Id = $(this).attr('id');
								var NombreSede = this.innerText;
								$('#IdSede').val(Id);
								$('#NombreSede').val($('#' + Id).attr('data'));
								$('#suggestionsSede').fadeOut(1000);

								return false;
							});
						}
					});
				});				

				$('.modal').modal();
				$('.tabs').tabs();
			});
		</script>
	</body>
</html>
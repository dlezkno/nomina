<div class="modal fade" data-bs-backdrop="static" id="modalFormRol" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalCenterTitle">Nuevo Rol</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="tile">
					<h3 class="tile-title">Nuevo Rol</h3>
					<div class="tile-body">
						<form id="formRol" name="formRol">
							<div class="form-group">
								<label class="control-label">Nombre</label>
								<input class="form-control" id="txtNombre" name="txtNombre" type="text"
									placeholder="Nombre del rol" required="">
							</div>
							<div class="form-group">
								<label class="control-label">Descripción</label>
								<textarea class="form-control" rows="2" id="txtDescripcion" name="txtDescripcion"
									placeholder="Descripción del rol" required=""></textarea>
							</div>
							<div class="form-group">
								<label for="exampleSelect1">Estado</label>
								<select class="form-control" id="lisStatus" name="lisStatus" required="">
									<option value="1">Activo</option>
									<option value="2">Inactivo</option>
								</select>
							</div>
							<div class="tile-footer">
								<button class="btn btn-primary" type="submit"><i
										class="fa fa-fw fa-lg fa-check-circle"></i>Gardar</button>&nbsp;&nbsp;&nbsp;<a
									class="btn btn-secondary" href="#"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancelar</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		...
	</div>
</div>
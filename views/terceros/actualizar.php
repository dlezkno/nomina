<?php 
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');

	if ($data['reg'])
	{
		$SelectTipoIdentificacion = getSelect('TipoIdentificacion', $data['reg']['TipoIdentificacion'], '', 'PARAMETROS.Valor');

		$ciudades = getTabla('CIUDADES', '', 'CIUDADES.Orden,CIUDADES.Nombre');

		$SelectCiudad = '';
		
		for ($i = 0; $i < count($ciudades); $i++) 
		{ 
			if	($ciudades[$i]['id'] == $data['reg']['IdCiudad'])
				$SelectCiudad .= '<option selected value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
			else
				$SelectCiudad .= '<option value=' . $ciudades[$i]['id'] . '>' . trim($ciudades[$i]['nombre']) . '(' . trim($ciudades[$i]['departamento']) . ')</option>';
		}

		// $paises = getTabla('PAISES', '', 'PAISES.Orden,PAISES.Nombre1');

		// $SelectPais = '';
		
		// for ($i = 0; $i < count($paises); $i++)
		// { 
		// 	if	($paises[$i]['id'] == $data['reg']['IdPais'])
		// 		$SelectPais .= '<option selected value=' . $paises[$i]['id'] . '>' . trim($paises[$i]['nombre1']) . '</option>';
		// 	else
		// 		$SelectPais .= '<option value=' . $paises[$i]['id'] . '>' . trim($paises[$i]['nombre1']) . '</option>';
		// }

		$SelectFormaDePago = getSelect('FormaDePago', $data['reg']['FormaDePago'], '', 'PARAMETROS.Valor');
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
                                    <h3 class="white-text">Terceros</h3>
                                </div>
                                <div class="col s12 m6 l6 right-align">
                                </div>
                            </div>
                        </div>

                        <div class="card-content">
                            <section class="tabs-vertical mt-1 section">
                                <div class="row">
                                    <div class="col l3 s12">
                                        <div class="card-panel">
                                            <ul class="tabs">
                                                <li class="tab">
                                                    <a href="#pagTercero">
                                                        <i class="material-icons">error_outline</i>
                                                        <span>Tercero</span>
                                                    </a>
                                                </li>
                                                <li class="tab">
                                                    <a href="#pagContacto">
                                                        <i class="material-icons">list</i>
                                                        <span>Contacto</span>
                                                    </a>
                                                </li>
                                                <li class="tab">
                                                    <a href="#pagSAP">
                                                        <i class="material-icons">list</i>
                                                        <span>Cuentas SAP</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="col s12 l9">
                                        <div id="pagTercero" class="col s12">
                                            <div class="card-panel">
                                                <div class="card-alert card cyan darken-4">
                                                    <div class="card-content white-text">
                                                        <p>TERCERO</p>
                                                    </div>
                                                </div>
                                                <div class="card-content">
                                                    <div class="row">
														<div class="input-field col s12 m6">
															<?php get(label('Tipo de identificación*'), 'TipoIdentificacion', $SelectTipoIdentificacion, 'select', 0, FALSE, 'required', 'fas fa-ellipsis-v'); ?>
														</div>
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Documento*'), 'Documento', $data['reg']['Documento'], 'text', 15, FALSE, 'required', 'fas fa-pen'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Nombre del tercero*'), 'Nombre', $data['reg']['Nombre'], 'text', 100, FALSE, 'required', 'fas fa-pen'); ?>
                                                        </div>
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Nombre 2 del tercero*'), 'Nombre2', $data['reg']['Nombre2'], 'text', 100, FALSE, 'required', 'fas fa-pen'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Es deudor'), 'EsDeudor', $data['reg']['EsDeudor'], 'checkbox', $data['reg']['EsDeudor'], FALSE, '', 'fas fa-pen'); ?>
                                                        </div>
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Es acreedor'), 'EsAcreedor', $data['reg']['EsAcreedor'], 'checkbox', $data['reg']['EsAcreedor'], FALSE, '', 'fas fa-pen'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="pagContacto" class="col s12">
                                            <div class="card-panel">
                                                <div class="card-alert card cyan darken-4">
                                                    <div class="card-content white-text">
                                                        <p>CONTACTO</p>
                                                    </div>
                                                </div>
                                                <div class="card-content">
                                                    <div class="row">
                                                        <div class="input-field col s12 m12">
                                                            <?php get(label('Dirección*'), 'Direccion', $data['reg']['Direccion'], 'text', 60, FALSE, 'required', 'fas fa-pen'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Ciudad*'), 'IdCiudad', $SelectCiudad, 'select', 60, FALSE, 'required', ''); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Teléfono'), 'Telefono', $data['reg']['Telefono'], 'tel', 15, FALSE, '', 'fas fa-phone'); ?>
                                                        </div>
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Celular'), 'Celular', $data['reg']['Celular'], 'tel', 15, FALSE, 'required', 'fas fa-phone'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('E-mail*'), 'EMail', $data['reg']['Email'], 'email', 100, FALSE, '', 'fas fa-paper-plane'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="pagSAP" class="col s12">
                                            <div class="card-panel">
                                                <div class="card-alert card cyan darken-4">
                                                    <div class="card-content white-text">
                                                        <p>CUENTAS SAP</p>
                                                    </div>
                                                </div>
                                                <div class="card-content">
													<div class="row">
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Es sindicato'), 'EsSindicato', $data['reg']['EsSindicato'], 'checkbox', $data['reg']['EsSindicato'], FALSE, '', 'fas fa-pen'); ?>
                                                        </div>
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Es EPS'), 'EsEPS', $data['reg']['EsEPS'], 'checkbox', $data['reg']['EsEPS'], FALSE, '', 'fas fa-pen'); ?>
                                                        </div>
                                                    </div>
													<div class="row">
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Es Fondo Cesantías'), 'EsFondoCesantias', $data['reg']['EsFondoCesantias'], 'checkbox', $data['reg']['EsFondoCesantias'], FALSE, '', 'fas fa-pen'); ?>
                                                        </div>
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Es Fondo Pensiones'), 'EsFondoPensiones', $data['reg']['EsFondoPensiones'], 'checkbox', $data['reg']['EsFondoPensiones'], FALSE, '', 'fas fa-pen'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Es ARL'), 'EsARL', $data['reg']['EsARL'], 'checkbox', $data['reg']['EsARL'], FALSE, '', 'fas fa-pen'); ?>
                                                        </div>
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Es CCF'), 'EsCCF', $data['reg']['EsCCF'], 'checkbox', $data['reg']['EsCCF'], FALSE, '', 'fas fa-pen'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Código'), 'Codigo', $data['reg']['Codigo'], 'text', 10, FALSE, '', 'fas fa-pen'); ?>
                                                        </div>
                                                        <div class="input-field col s12 m6">
                                                            <?php get(label('Código SAP'), 'CodigoSAP', $data['reg']['CodigoSAP'], 'text', 10, FALSE, '', 'fas fa-pen'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
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
	</div>
</div>

<?php require_once('views/templates/footer.php'); ?>
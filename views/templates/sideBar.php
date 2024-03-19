<aside class="sidenav-main nav-expanded nav-collapsible nav-lock sidenav-dark sidenav-active-rounded white-text"
	style="background-color:#1b2140;">
	<div class="brand-sidebar">
		<!-- INCLUIR AQUI EL USUARIO -->
		<h1 class="logo-wrapper">
			<a class="brand-logo darken-1" href="<?= SERVERURL ?>/home/home">
				<img class="hide-on-med-and-down" src="<?= SERVERURL ?>/assets/images/logo.png" alt="<?= COMPANY ?>" width="200px" />
			</a>
		</h1>
	</div>
	<ul class="sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow" id="slide-out"
		data-menu="menu-navigation" data-collapsible="accordion">

		<?php 
			switch ($_SESSION['Login']['Perfil']):
			case EMPLEADO: 
		?>
				<!-- MIS DATOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">SELECCIÓN</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- ACTUALIZACION REGISTRO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'misdatos/editar') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="MisDatos">MIS DATOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange"
										href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
										<i class="material-icons">dvr</i>
										<span data-i18n="MisDatos">Mis datos</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>
				<?php break; ?>

		<?php case SELECCION: ?>
				<!-- DASHBOARD -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dashboard/dashboard') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">DASHBOARD</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Dashboard</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- PRONOSTICOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'pronosticos/contrataciones') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">PRONOSTICOS</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Pronosticos</span>
						</a>
					</li>
				<?php endif; ?>



				<!-- SOL.  PERSONAL -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'personal/solicitud') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">SOL.  PERSONAL</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">solicitar personal</span>
						</a>
					</li>
				<?php endif; ?>

				<li class="navigation-header">
					<a class="navigation-header-text">SELECCIÓN</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li>

				<!-- REGISTRO DE CANDIDATOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'candidatos/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/candidatos/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Candidatos">CANDIDATOS</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/candidatos/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Candidatos">Candidatos</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- LISTADO DE PERSONAL -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'personal/lista/1') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/personal/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">SOL.  PERSONAL</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/personal/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Solicitudes de personal</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- ENTREVISTA SICOLOGIA -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'entrevista1/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/entrevista1/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Candidatos">ENTREV. PSIC.</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/entrevista1/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Candidatos">Entrevista psicológica</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- ENTREVISTA TÉCNICA -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'entrevista2/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/entrevista2/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Candidatos">ENTREV. TÉC.</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/entrevista2/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Candidatos">Entrevista técnica</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- CARPETA DOCUMENTOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'documentos/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/documentos/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Candidatos">CARPETA DOCS.</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/documentos/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Candidatos">Carpeta docs.</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- ESTADO SOLICITUDES -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'estadoSolicitud/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/estadoSolicitud/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Estado solicitudes">ESTADO SOL.</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/estadoSolicitud/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Candidatos">Estado sol.</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- <li class="navigation-header">
					<a class="navigation-header-text">MIS DATOS</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li> -->

				<!-- ACTUALIZACION REGISTRO -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'misdatos/editar') !== false ): ?>
					<!-- <li class="active">
						<a class="active cyan darken-4 white-text"
							href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="MisDatos">DATOS PERSONALES</span>
						</a>
					</li> -->
				<?php else: ?>
					<!-- <li class="bold">
						<a class="waves-effect waves-orange"
							href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
							<i class="material-icons">dvr</i>
							<span data-i18n="MisDatos">Datos personales</span>
						</a>
					</li> -->
				<?php endif; ?>

				<?php break; ?>

			<?php case CONTRATACION: ?>
				<!-- DASHBOARD -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dashboard/dashboard') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">DASHBOARD</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Dashboard</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- PRONOSTICOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'pronosticos/contrataciones') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">PRONOSTICOS</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Pronosticos</span>
						</a>
					</li>
				<?php endif; ?>


				<!-- SOL.  PERSONAL -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'personal/solicitud') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">SOL.  PERSONAL</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Solicitud de personal</span>
						</a>
					</li>
				<?php endif; ?>

				<li class="navigation-header">
					<a class="navigation-header-text">CONTRATACIÓN</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li>

				<!-- REGISTRO DE CANDIDATOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'contratos/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/contratos/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Candidatos">CANDIDATOS</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/contratos/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Candidatos">Candidatos</span>
						</a>
					</li>
				<?php endif; ?>
							
				<!-- EMPLEADOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'empleados/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/empleados/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Empleados">EMP. ACTIVOS</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/empleados/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Empleados">Emp. activos</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- CARPETA DOCUMENTOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'documentos/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/documentos/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Candidatos">CARPETA DOCS.</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/documentos/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Candidatos">Carpeta docs.</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- ESTADO SOLICITUDES -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'estadoSolicitud/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/estadoSolicitud/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Estado solicitudes">ESTADO SOL.</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/estadoSolicitud/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Candidatos">Estado sol.</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- RENOVACIONES DE CONTRATOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'renovaciones/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/renovaciones/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Candidatos">RENOVACIONES</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/renovaciones/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Candidatos">Renovaciones</span>
						</a>
					</li>
					
				<?php endif; ?>
				<!-- CENTROS DE COSTO -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'centros/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/centros/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Centros">CENTROS DE COSTO</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/centros/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Centros">Centros de costo</span>
						</a>
					</li>
				<?php endif; ?>
				<!-- PLANTILLA -->
				<?php if (isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'plantillas/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/plantillas/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Plantillas">PLANTILLAS</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/plantillas/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Plantillas">Plantillas</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- <li class="navigation-header">
					<a class="navigation-header-text">MIS DATOS</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li> -->

				<!-- ACTUALIZACION REGISTRO -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'misdatos/editar') !== false ): ?>
					<!-- <li class="active">
						<a class="active cyan darken-4 white-text"
							href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="MisDatos">DATOS PERSONALES</span>
						</a>
					</li> -->
				<?php else: ?>
					<!-- <li class="bold">
						<a class="waves-effect waves-orange"
							href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
							<i class="material-icons">dvr</i>
							<span data-i18n="MisDatos">Datos personales</span>
						</a>
					</li> -->
				<?php endif; ?>

				<?php break; ?>

			<?php case CONTABILIDAD: ?>
				<!-- DASHBOARD -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dashboard/dashboard') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">DASHBOARD</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Dashboard</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- PRONOSTICOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'pronosticos/contrataciones') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">PRONOSTICOS</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Pronosticos</span>
						</a>
					</li>
				<?php endif; ?>


				<!-- SOL.  PERSONAL -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'personal/solicitud') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">SOL.  PERSONAL</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Solicitud de personal</span>
						</a>
					</li>
				<?php endif; ?>

				<li class="navigation-header">
					<a class="navigation-header-text">NÓMINA</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li>

				<!-- PRENOMINA -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">LIQ. PRENÓMINA</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- INFORMES DE NOMINA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'informesNomina/informes') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/informesNomina/informes">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Informes">INF. NÓMINA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/informesNomina/informes">
										<i class="material-icons">dvr</i>
										<span data-i18n="Informes">Inf. nómina</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INFORME RETENCION FUENTE -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionPrenomina/retencionFuente') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/liquidacionPrenomina/retencionFuente">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">INF. RET. FTE.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionPrenomina/retencionFuente">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Inf. Ret. Fte.</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- ACUMULADOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">ACUMULADOS</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- ACUMULADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">ACUMULADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Acumulados</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CALCULO RETENCION FUENTE METODO 1 -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/calculoRetFte') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/calculoRetFte">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">CÁLCULO RET. FTE.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/calculoRetFte">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Cálculo Ret. Fte.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CALCULO RETENCION FUENTE METODO 2 -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/calculoRetFte2') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/calculoRetFte2">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">CÁLCULO RET. FTE. 2</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/calculoRetFte2">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Cálculo Ret. Fte. 2</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- CONTABILIZACION DE NOMINA -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">CONTABILIZACIÓN</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- COMPROBANTES DE DIARIO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'comprobantes/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/comprobantes/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Comprobantes">COMPROBANTES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/comprobantes/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Comprobantes">Comprobantes</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CONTABILIZACION EN SAP -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'contabilizacionSAP/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/contabilizacionSAP/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="ContabilizacionSAP">CONTABILIZACIÓN SAP</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/contabilizacionSAP/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="ContabilizacionSAP">Contabilización SAP</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<?php break; ?>

			<?php case AUDITORIA: ?>
				<!-- DASHBOARD -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dashboard/dashboard') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">DASHBOARD</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Dashboard</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- PRONOSTICOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'pronosticos/contrataciones') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">PRONOSTICOS</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Pronosticos</span>
						</a>
					</li>
				<?php endif; ?>


				<!-- SOL.  PERSONAL -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'personal/solicitud') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">SOL.  PERSONAL</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Solicitud de personal</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- CARPETA DOCUMENTOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'documentos/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/documentos/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Candidatos">CARPETA DOCS.</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/documentos/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Candidatos">Carpeta docs.</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- ESTADO SOLICITUDES -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'estadoSolicitud/lista') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/estadoSolicitud/lista/1">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Estado solicitudes">ESTADO SOL.</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/estadoSolicitud/lista/1">
							<i class="material-icons">dvr</i>
							<span data-i18n="Candidatos">Estado sol.</span>
						</a>
					</li>
				<?php endif; ?>

				<?php break; ?>

			<?php case RRHH_AUX: ?>
				<!-- DASHBOARD -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dashboard/dashboard') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">DASHBOARD</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Dashboard</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- PRONOSTICOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'pronosticos/contrataciones') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">PRONOSTICOS</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Pronosticos</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- SOL.  PERSONAL -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'personal/solicitud') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">SOL.  PERSONAL</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Solicitud de personal</span>
						</a>
					</li>
				<?php endif; ?>

				<li class="navigation-header">
					<a class="navigation-header-text">NÓMINA</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li>

				<!-- EMPLEADOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">EMPLEADOS</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'empleados/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/empleados/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Empleados">EMP. ACTIVOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/empleados/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Empleados">Emp. activos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- RETIRADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'retirados/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/retirados/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Empleados">EMP. RETIRADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/retirados/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Empleados">Emp. retirados</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- PRESTAMOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'prestamos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/prestamos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Prestamos">PRÉSTAMOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/prestamos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Prestamos">Préstamos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INFORMES DE EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'informesEmpleados/informes') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/informesEmpleados/informes">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Informes">INF. EMPLEADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/informesEmpleados/informes">
										<i class="material-icons">dvr</i>
										<span data-i18n="Informes">Inf. empleados</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- NOVEDADES -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">NOVEDADES</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- NOVEDADES PROGRAMABLES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'novedadesProgramables/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/novedadesProgramables/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="NovedadesProgramables">NOV. PROGRAMABLES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange"
										href="<?= SERVERURL ?>/novedadesProgramables/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="NovedadesProgramables">Nov. programables</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- NOVEDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'novedades/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/novedades/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">NOVEDADES OCAS.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/novedades/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Novedades ocas.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INCAPACIDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'incapacidades/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/incapacidades/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">INCAPACIDADES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/incapacidades/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Incapacidades</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- AUMENTOS SALARIALES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'aumentosSaariales/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/aumentosSalariales/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">AUMENTOS SALARIALES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/aumentosSalariales/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Aumentos salariales</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- RETIROS EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'retirosEmpleados/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/retirosEmpleados/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">RETIROS EMPLEADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/retirosEmpleados/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Retiros empleados</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DEDUCCIONES RET.FTE. -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'deduccionesRetFte/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/deduccionesRetFte/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">DEDUCC. RET.FTE.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/deduccionesRetFte/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Deducc. Ret.Fte.</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- PRENOMINA -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">NÓMINA</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- INFORMES DE NOMINA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'informesNomina/informes') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/informesNomina/informes">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Informes">INF. NÓMINA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/informesNomina/informes">
										<i class="material-icons">dvr</i>
										<span data-i18n="Informes">Inf. nómina</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INFORME RETENCION FUENTE -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionPrenomina/retencionFuente') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/liquidacionPrenomina/retencionFuente">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">INF. RET. FTE.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionPrenomina/retencionFuente">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Inf. Ret. Fte.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DESPRENDIBLES DE NOMINA PARA ENVIO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'desprendiblesNomina/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/desprendiblesNomina/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">DESP. NÓMINA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/desprendiblesNomina/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Desp. nómina</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- PRESTACIONES SOCIALES -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">PREST. SOCIALES</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- LIQUIDACION PRIMA SEMESTRAL -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionPrima/liquidar') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionPrima/liquidar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">LIQ. PRIMA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionPrima/liquidar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Liq. prima</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- VACACIONES -->
							<li class="bold">
								<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
									<i class="material-icons">photo_filter</i>
									<span class="menu-title" data-i18n="MenuEmpleados">VACACIONES</span>
								</a>
								<div class="collapsible-body">
									<ul class="collapsible collapsible-sub" data-collapsible="accordion">
										<!-- LIQUIDACION VACACIONES EN TIEMPO -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/liquidarEnTiempo') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnTiempo">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">VAC. EN TIEMPO</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnTiempo">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Vac. en tiempo</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- LIQUIDACION VACACIONES EN DINERO -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/liquidarEnDinero') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnDinero">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">VAC. EN DINERO</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnDinero">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Vac. en dinero</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- LIBRO DE VACACIONES -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/listaLibroVacaciones') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/listaLibroVacaciones/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">LIBRO VAC.</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/listaLibroVacaciones/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Libro Vac.</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- LIBRO DE VACACIONES DISFRUTADAS-->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/lista') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/lista/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">LIBRO VAC. DISF.</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/lista/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Libro Vac. Disf.</span>
												</a>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>

							<!-- LIQUIDACION CESANTÍAS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionCesantias/liquidar') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionCesantias/liquidar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">LIQ. CESANTÍAS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionCesantias/liquidar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Liq. cesantías</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- LIQUIDACION CONTRATO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionContrato/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionContrato/lista/1/0">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">LIQ. CONTRATO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionContrato/lista/1/0">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Liq. contrato</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- RELIQUIDACION CONTRATO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionContrato/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionContrato/reliquidar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">RELIQ. CONTRATO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionContrato/reliquidar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Reliq. contrato</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- ACUMULADOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">ACUMULADOS</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- ACUMULADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">ACUMULADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Acumulados</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- DOTACIÓN -->
				<?php if (FALSE): ?>
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDotacion">DOTACIÓN</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- DOTACION -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dotacion/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dotacion/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Dotacion">DOTACIÓN</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dotacion/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Dotacion">Dotación</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- ENTREGA DOTACION -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'entregadotacion/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/entregadotacion/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="EntregaDotacion">ENTREGA DE DOTACIÓN</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/entregadotacion/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="EntregaDotacion">Entrega de dotación</span>
									</a>
								</li>
							<?php endif; ?>

						</ul>
					</div>
				</li>
				<?php endif; ?>

				<!-- ARCHIVOS BASICOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">ARCHIVOS BÁSICOS</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- CENTROS DE COSTO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'centros/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/centros/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Centros">CENTROS DE COSTO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/centros/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Centros">Centros de costo</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- PERIODOS DE PAGO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'periodos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/periodos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Periodos">PERÍODOS DE PAGO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/periodos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Periodos">Períodos de pago</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CONCEPTOS -->
							<li class="bold">
								<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
									<i class="material-icons">photo_filter</i>
									<span class="menu-title" data-i18n="MenuConceptos">CONCEPTOS</span>
								</a>
								<div class="collapsible-body">
									<ul class="collapsible collapsible-sub" data-collapsible="accordion">
										<!-- CONCEPTOS MAYORES -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'mayores/lista') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/mayores/lista/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="ConceptosMayores">MAYORES</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/mayores/lista/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="ConceptosMayores">Mayores</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- CONCEPTOS AUXILIARES -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'auxiliares/lista') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/auxiliares/lista/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="ConceptosAuxiliares">AUXILIARES</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/auxiliares/lista/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="ConceptosAuxiliares">Auxiliares</span>
												</a>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>

							<!-- CATEGORIAS DE EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'categorias/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/categorias/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="CategoriasEmpleados">CATEGORÍAS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/categorias/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="CategoriasEmpleados">Categorías</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CARGOS DE EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'cargos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/cargos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="CargosEmpleados">CARGOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/cargos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="CargosEmpleados">Cargos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- TERCEROS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'terceros/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/terceros/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Terceros">TERCEROS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/terceros/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Terceros">Terceros</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- TIPOS DE COMPROBANTES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'tipodoc/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/tipodoc/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="TipoDoc">TIPOS DE COMPR.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/tipodoc/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="TipoDoc">Tipos de compr.</span>
									</a>
								</li>
							<?php endif; ?>
							
							<!-- PLANTILLA -->
							<?php if (isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'plantillas/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/plantillas/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Plantillas">PLANTILLAS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/plantillas/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Plantillas">Plantillas</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DIAGNOSTICOS DE INCAPACIDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'diagnosticos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/diagnosticos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Bancos">DIAGNÓSTICOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/diagnosticos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Bancos">Diagnósticos</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- <li class="navigation-header">
					<a class="navigation-header-text">MIS DATOS</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li> -->

				<!-- ACTUALIZACION REGISTRO -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'misdatos/editar') !== false ): ?>
					<!-- <li class="active">
						<a class="active cyan darken-4 white-text"
							href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="MisDatos">DATOS PERSONALES</span>
						</a>
					</li> -->
				<?php else: ?>
					<!-- <li class="bold">
						<a class="waves-effect waves-orange"
							href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
							<i class="material-icons">dvr</i>
							<span data-i18n="MisDatos">Datos personales</span>
						</a>
					</li> -->
				<?php endif; ?>

				<?php break; ?>

			<?php case RRHH: ?>
				<!-- DASHBOARD -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dashboard/dashboard') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">DASHBOARD</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Dashboard</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- PRONOSTICOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'pronosticos/contrataciones') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">PRONOSTICOS</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Pronosticos</span>
						</a>
					</li>
				<?php endif; ?>


				<!-- SOL.  PERSONAL -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'personal/solicitud') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">SOL.  PERSONAL</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Solicitud de personal</span>
						</a>
					</li>
				<?php endif; ?>

				<li class="navigation-header">
					<a class="navigation-header-text">NÓMINA</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li>

				<!-- EMPLEADOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">EMPLEADOS</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'empleados/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/empleados/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Empleados">EMP. ACTIVOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/empleados/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Empleados">Emp. activos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- RETIRADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'retirados/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/retirados/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Empleados">EMP. RETIRADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/retirados/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Empleados">Emp. retirados</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- PRESTAMOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'prestamos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/prestamos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Prestamos">PRÉSTAMOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/prestamos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Prestamos">Préstamos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INFORMES DE EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'informesEmpleados/informes') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/informesEmpleados/informes">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Informes">INF. EMPLEADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/informesEmpleados/informes">
										<i class="material-icons">dvr</i>
										<span data-i18n="Informes">Inf. empleados</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- NOVEDADES -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">NOVEDADES</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- APERTURA NOVEDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'aperturaNovedades/editar') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/aperturaNovedades/editar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">APERTURA NOV.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange"
										href="<?= SERVERURL ?>/aperturaNovedades/editar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Apertura nov.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- NOVEDADES PROGRAMABLES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'novedadesProgramables/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/novedadesProgramables/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="NovedadesProgramables">NOV. PROGRAMABLES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange"
										href="<?= SERVERURL ?>/novedadesProgramables/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="NovedadesProgramables">Nov. programables</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- NOVEDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'novedades/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/novedades/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">NOVEDADES OCAS.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/novedades/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Novedades ocas.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DISPERSION POR CENTRO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dispersionPorCentro/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/dispersionPorCentro/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="DispesionPorCentro">DISP. POR CENTRO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dispersionPorCentro/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="DispersionPorCentro">Disp. por centro</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INCAPACIDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'incapacidades/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/incapacidades/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">INCAPACIDADES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/incapacidades/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Incapacidades</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- AUMENTOS SALARIALES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'aumentosSaariales/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/aumentosSalariales/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">AUMENTOS SALARIALES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/aumentosSalariales/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Aumentos salariales</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- RETIROS EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'retirosEmpleados/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/retirosEmpleados/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">RETIROS EMPLEADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/retirosEmpleados/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Retiros empleados</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DEDUCCIONES RET.FTE. -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'deduccionesRetFte/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/deduccionesRetFte/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">DEDUCC. RET.FTE.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/deduccionesRetFte/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Deducc. Ret.Fte.</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- PRENOMINA -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">NÓMINA</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- LIQUIDACION PRENOMINA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionPrenomina/liquidar') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/liquidacionPrenomina/liquidar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">LIQ. PRENÓMINA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionPrenomina/liquidar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Liq. prenómina</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INFORMES DE NOMINA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'informesNomina/informes') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/informesNomina/informes">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Informes">INF. NÓMINA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/informesNomina/informes">
										<i class="material-icons">dvr</i>
										<span data-i18n="Informes">Inf. nómina</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INFORME RETENCION FUENTE -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionPrenomina/retencionFuente') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/liquidacionPrenomina/retencionFuente">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">INF. RET. FTE.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionPrenomina/retencionFuente">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Inf. Ret. Fte.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DESPRENDIBLES DE NOMINA PARA ENVIO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'desprendiblesNomina/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/desprendiblesNomina/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">DESP. NÓMINA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/desprendiblesNomina/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Desp. nómina</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- ACUMULADOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">ACUMULADOS</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- ACUMULAR NOMINA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/acumularNomina') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/acumularNomina">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">ACUMULAR NÓMINA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/acumularNomina">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Acumular Nómina</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- ACUMULADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">ACUMULADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Acumulados</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CALCULO RETENCION FUENTE METODO 1 -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/calculoRetFte') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/calculoRetFte">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">CÁLCULO RET. FTE.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/calculoRetFte">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Cálculo Ret. Fte.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CALCULO RETENCION FUENTE METODO 2 -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/calculoRetFte2') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/calculoRetFte2">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">CÁLCULO RET. FTE. 2</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/calculoRetFte2">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Cálculo Ret. Fte. 2</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- PRESTACIONES SOCIALES -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">PREST. SOCIALES</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- LIQUIDACION PRIMA SEMESTRAL -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionPrima/liquidar') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionPrima/liquidar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">LIQ. PRIMA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionPrima/liquidar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Liq. prima</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- VACACIONES -->
							<li class="bold">
								<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
									<i class="material-icons">photo_filter</i>
									<span class="menu-title" data-i18n="MenuEmpleados">VACACIONES</span>
								</a>
								<div class="collapsible-body">
									<ul class="collapsible collapsible-sub" data-collapsible="accordion">
										<!-- LIQUIDACION VACACIONES EN TIEMPO -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/liquidarEnTiempo') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnTiempo">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">VAC. EN TIEMPO</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnTiempo">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Vac. en tiempo</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- LIQUIDACION VACACIONES EN DINERO -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/liquidarEnDinero') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnDinero">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">VAC. EN DINERO</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnDinero">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Vac. en dinero</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- LIBRO DE VACACIONES -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/listaLibroVacaciones') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/listaLibroVacaciones/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">LIBRO VAC.</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/listaLibroVacaciones/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Libro Vac.</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- LIBRO DE VACACIONES DISFRUTADAS-->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/lista') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/lista/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">LIBRO VAC. DISF.</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/lista/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Libro Vac. Disf.</span>
												</a>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>

							<!-- LIQUIDACION CESANTÍAS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionCesantias/liquidar') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionCesantias/liquidar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">LIQ. CESANTÍAS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionCesantias/liquidar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Liq. cesantías</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- SIMULACION DE LIQUIDACION CONTRATO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'simulacionLiquidacionContrato/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/simulacionLiquidacionContrato/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">SIMULACIÓN LIQ.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/simulacionLiquidacionContrato/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Simulación Liq.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- LIQUIDACION CONTRATO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionContrato/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionContrato/lista/1/0">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">LIQ. CONTRATO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionContrato/lista/1/0">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Liq. contrato</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- RELIQUIDACION CONTRATO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionContrato/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionContrato/reliquidar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">RELIQ. CONTRATO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionContrato/reliquidar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Reliq. contrato</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- CONTABILIZACION DE NOMINA -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">CONTABILIZACIÓN</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- COMPROBANTES DE DIARIO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'comprobantes/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/comprobantes/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Comprobantes">COMPROBANTES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/comprobantes/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Comprobantes">Comprobantes</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CONTABILIZACION EN SAP -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'contabilizacionSAP/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/contabilizacionSAP/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="ContabilizacionSAP">CONTABILIZACIÓN SAP</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/contabilizacionSAP/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="ContabilizacionSAP">Contabilización SAP</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DISPERSION BANCARIA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dispersionNomina/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dispersionNomina/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="ContabilizacionSAP">DISPERSIÓN BANCARIA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dispersionNomina/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="ContabilizacionSAP">Dispersión bancaria</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- TRASLADOS ENTRE CENTROS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'trasladosCentros/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/trasladosCentros/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="ContabilizacionSAP">TRASLADOS CENTROS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/trasladosCentros/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="ContabilizacionSAP">Traslados centros</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INFORME DE CONTABILIZACION EN SAP -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'informeContabilizacionAcumulados/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/informeContabilizacionAcumulados/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="ContabilizacionSAP">INF. CONTABILIZACIÓN</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/informeContabilizacionAcumulados/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="ContabilizacionSAP">Inf. contabilización</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- NOMINA ELECTRONICA -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">NÓMINA ELECT.</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- INICIO CONTADORES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'nominaElectronica/inicio') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/nominaElectronica/inicio">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="NominaElectronica">INICIO CONTADORES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/nominaElectronica/inicio">
										<i class="material-icons">dvr</i>
										<span data-i18n="NominaElectronica">Inicio contadores</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- NOMINA ELECTRONICA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'nominaElectronica/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/nominaElectronica/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="NominaElectronica">NÓM. ELECTRÓNICA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/nominaElectronica/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="NominaElectronica">Nóm. electrónica</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- REPORTE NOMINA ELECTRONICA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'nominaElectronica/report') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/nominaElectronica/report">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="NominaElectronica">REP. NÓM. ELECT</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/nominaElectronica/report">
										<i class="material-icons">dvr</i>
										<span data-i18n="NominaElectronica">Rep. Nóm. electrónica</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- DOTACIÓN -->
				<?php if (FALSE): ?>
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDotacion">DOTACIÓN</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- DOTACION -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dotacion/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dotacion/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Dotacion">DOTACIÓN</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dotacion/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Dotacion">Dotación</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- ENTREGA DOTACION -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'entregadotacion/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/entregadotacion/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="EntregaDotacion">ENTREGA DE DOTACIÓN</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/entregadotacion/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="EntregaDotacion">Entrega de dotación</span>
									</a>
								</li>
							<?php endif; ?>

						</ul>
					</div>
				</li>
				<?php endif; ?>

				<!-- ARCHIVOS BASICOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">ARCHIVOS BÁSICOS</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- CENTROS DE COSTO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'centros/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/centros/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Centros">CENTROS DE COSTO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/centros/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Centros">Centros de costo</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- PERIODOS DE PAGO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'periodos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/periodos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Periodos">PERÍODOS DE PAGO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/periodos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Periodos">Períodos de pago</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CONCEPTOS -->
							<li class="bold">
								<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
									<i class="material-icons">photo_filter</i>
									<span class="menu-title" data-i18n="MenuConceptos">CONCEPTOS</span>
								</a>
								<div class="collapsible-body">
									<ul class="collapsible collapsible-sub" data-collapsible="accordion">
										<!-- CONCEPTOS MAYORES -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'mayores/lista') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/mayores/lista/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="ConceptosMayores">MAYORES</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/mayores/lista/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="ConceptosMayores">Mayores</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- CONCEPTOS AUXILIARES -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'auxiliares/lista') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/auxiliares/lista/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="ConceptosAuxiliares">AUXILIARES</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/auxiliares/lista/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="ConceptosAuxiliares">Auxiliares</span>
												</a>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>

							<!-- CATEGORIAS DE EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'categorias/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/categorias/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="CategoriasEmpleados">CATEGORÍAS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/categorias/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="CategoriasEmpleados">Categorías</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CARGOS DE EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'cargos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/cargos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="CargosEmpleados">CARGOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/cargos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="CargosEmpleados">Cargos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- TERCEROS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'terceros/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/terceros/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Terceros">TERCEROS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/terceros/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Terceros">Terceros</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- TIPOS DE COMPROBANTES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'tipodoc/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/tipodoc/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="TipoDoc">TIPOS DE COMPR.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/tipodoc/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="TipoDoc">Tipos de compr.</span>
									</a>
								</li>
							<?php endif; ?>
							
							<!-- PLANTILLA -->
							<?php if (isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'plantillas/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/plantillas/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Plantillas">PLANTILLAS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/plantillas/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Plantillas">Plantillas</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DIAGNOSTICOS DE INCAPACIDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'diagnosticos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/diagnosticos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Bancos">DIAGNÓSTICOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/diagnosticos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Bancos">Diagnósticos</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- <li class="navigation-header">
					<a class="navigation-header-text">MIS DATOS</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li> -->

				<!-- ACTUALIZACION REGISTRO -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'misdatos/editar') !== false ): ?>
					<!-- <li class="active">
						<a class="active cyan darken-4 white-text"
							href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="MisDatos">DATOS PERSONALES</span>
						</a>
					</li> -->
				<?php else: ?>
					<!-- <li class="bold">
						<a class="waves-effect waves-orange"
							href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
							<i class="material-icons">dvr</i>
							<span data-i18n="MisDatos">Datos personales</span>
						</a>
					</li> -->
				<?php endif; ?>

				<?php break; ?>

			<?php case ADMINISTRADOR: ?>
				<!-- DASHBOARD -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dashboard/dashboard') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">DASHBOARD</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dashboard/dashboard">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Dashboard</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- PRONOSTICOS -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'pronosticos/contrataciones') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">PRONOSTICOS</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/pronosticos/contrataciones">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Pronosticos</span>
						</a>
					</li>
				<?php endif; ?>


				<!-- SOL.  PERSONAL -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'personal/solicitud') !== false ): ?>
					<li class="active">
						<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="Dashboard">SOL.  PERSONAL</span>
						</a>
					</li>
				<?php else: ?>
					<li class="bold">
						<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/personal/solicitud">
							<i class="material-icons">dvr</i>
							<span data-i18n="Dashboard">Solicitud de personal</span>
						</a>
					</li>
				<?php endif; ?>

				<li class="navigation-header">
					<a class="navigation-header-text">NÓMINA</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li>

				<!-- SELECCION DE CANDIDATOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">SELECCIÓN</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- REGISTRO DE CANDIDATOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'candidatos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/candidatos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Candidatos">CANDIDATOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/candidatos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Candidatos">Candidatos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- ENTREVISTA SICOLOGIA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'entrevista1/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/entrevista1/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Candidatos">ENTREV. PSIC.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/entrevista1/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Candidatos">Entrevista psicológica</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- ENTREVISTA TÉCNICA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'entrevista2/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/entrevista2/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Candidatos">ENTREV. TÉC.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/entrevista2/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Candidatos">Entrevista técnica</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CARPETA DOCUMENTOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'documentos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/documentos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Candidatos">CARPETA DOCS.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/documentos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Candidatos">Carpeta docs.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- ESTADO SOLICITUDES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'estadoSolicitud/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/estadoSolicitud/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Estado solicitudes">ESTADO SOL.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/estadoSolicitud/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Candidatos">Estado sol.</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- CONTRATACION DE CANDIDATOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">CONTRATACIÓN</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- REGISTRO DE CANDIDATOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'contratos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/contratos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Candidatos">CANDIDATOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/contratos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Candidatos">Candidatos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CARPETA DOCUMENTOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'documentos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/documentos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Candidatos">CARPETA DOCS.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/documentos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Candidatos">Carpeta docs.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- ESTADO SOLICITUDES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'estadoSolicitud/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-14 white-text" href="<?= SERVERURL ?>/estadoSolicitud/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Estado solicitudes">ESTADO SOL.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/estadoSolicitud/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Candidatos">Estado sol.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- RENOVACIONES DE CONTRATOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'renovaciones/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/renovaciones/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Candidatos">RENOVACIONES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/renovaciones/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Candidatos">Renovaciones</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- EMPLEADOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">EMPLEADOS</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'empleados/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/empleados/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Empleados">EMP. ACTIVOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/empleados/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Empleados">Emp. activos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- RETIRADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'retirados/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/retirados/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Empleados">EMP. RETIRADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/retirados/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Empleados">Emp. retirados</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- PRESTAMOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'prestamos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/prestamos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Prestamos">PRÉSTAMOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/prestamos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Prestamos">Préstamos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INFORMES DE EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'informesEmpleados/informes') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/informesEmpleados/informes">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Informes">INF. EMPLEADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/informesEmpleados/informes">
										<i class="material-icons">dvr</i>
										<span data-i18n="Informes">Inf. empleados</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- NOVEDADES -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">NOVEDADES</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- APERTURA NOVEDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'aperturaNovedades/editar') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/aperturaNovedades/editar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">APERTURA NOV.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange"
										href="<?= SERVERURL ?>/aperturaNovedades/editar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Apertura nov.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- NOVEDADES PROGRAMABLES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'novedadesProgramables/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/novedadesProgramables/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="NovedadesProgramables">NOV. PROGRAMABLES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange"
										href="<?= SERVERURL ?>/novedadesProgramables/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="NovedadesProgramables">Nov. programables</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- NOVEDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'novedades/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/novedades/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">NOVEDADES OCAS.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/novedades/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Novedades Ocas.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DISPERSION POR CENTRO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dispersionPorCentro/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/dispersionPorCentro/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="DispesionPorCentro">DISP. POR CENTRO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dispersionPorCentro/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="DispersionPorCentro">Disp. por centro</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INCAPACIDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'incapacidades/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/incapacidades/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">INCAPACIDADES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/incapacidades/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Incapacidades</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- AUMENTOS SALARIALES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'aumentosSaariales/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/aumentosSalariales/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">AUMENTOS SALARIALES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/aumentosSalariales/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Aumentos salariales</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- RETIROS EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'retirosEmpleados/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/retirosEmpleados/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">RETIROS EMPLEADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/retirosEmpleados/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Retiros empleados</span>
									</a>
								</li>
							<?php endif; ?>				

							<!-- DEDUCCIONES RET.FTE. -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'deduccionesRetFte/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/deduccionesRetFte/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Incapacidades">DEDUCC. RET.FTE.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/deduccionesRetFte/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Incapacidades">Deducc. Ret.Fte.</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- PRENOMINA -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">NÓMINA</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- LIQUIDACION PRENOMINA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionPrenomina/liquidar') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/liquidacionPrenomina/liquidar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">LIQ. PRENÓMINA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionPrenomina/liquidar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Liq. prenómina</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INFORMES DE NOMINA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'informesNomina/informes') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/informesNomina/informes">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Informes">INF. NÓMINA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/informesNomina/informes">
										<i class="material-icons">dvr</i>
										<span data-i18n="Informes">Inf. nómina</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INFORME RETENCION FUENTE -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionPrenomina/retencionFuente') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/liquidacionPrenomina/retencionFuente">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">INF. RET. FTE.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionPrenomina/retencionFuente">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Inf. Ret. Fte.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DESPRENDIBLES DE NOMINA PARA ENVIO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'desprendiblesNomina/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text"
										href="<?= SERVERURL ?>/desprendiblesNomina/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">DESP. NÓMINA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/desprendiblesNomina/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Desp. nómina</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- ACUMULADOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">ACUMULADOS</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- ACUMULAR NOMINA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/acumularNomina') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/acumularNomina">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">ACUMULAR NÓMINA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/acumularNomina">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Acumular Nómina</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- ACUMULADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">ACUMULADOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Acumulados</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CALCULO RETENCION FUENTE METODO 1 -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/calculoRetFte') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/calculoRetFte">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">CÁLCULO RET. FTE.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/calculoRetFte">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Cálculo Ret. Fte.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CALCULO RETENCION FUENTE METODO 2 -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'acumulados/calculoRetFte2') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/acumulados/calculoRetFte2">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Acumulados">CÁLCULO RET. FTE. 2</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/acumulados/calculoRetFte2">
										<i class="material-icons">dvr</i>
										<span data-i18n="Acumulados">Cálculo Ret. Fte. 2</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- PRESTACIONES SOCIALES -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuEmpleados">PREST. SOCIALES</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- LIQUIDACION PRIMA SEMESTRAL -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionPrima/liquidar') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionPrima/liquidar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">LIQ. PRIMA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionPrima/liquidar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Liq. prima</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- VACACIONES -->
							<li class="bold">
								<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
									<i class="material-icons">photo_filter</i>
									<span class="menu-title" data-i18n="MenuEmpleados">VACACIONES</span>
								</a>
								<div class="collapsible-body">
									<ul class="collapsible collapsible-sub" data-collapsible="accordion">
										<!-- LIQUIDACION VACACIONES EN TIEMPO -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/liquidarEnTiempo') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnTiempo">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">VAC. EN TIEMPO</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnTiempo">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Vac. en tiempo</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- LIQUIDACION VACACIONES EN DINERO -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/liquidarEnDinero') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnDinero">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">VAC. EN DINERO</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/liquidarEnDinero">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Vac. en dinero</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- LIBRO DE VACACIONES -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/listaLibroVacaciones') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/listaLibroVacaciones/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">LIBRO VAC.</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/listaLibroVacaciones/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Libro Vac.</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- LIBRO DE VACACIONES DISFRUTADAS-->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'Vacaciones/lista') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/Vacaciones/lista/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="Novedades">LIBRO VAC. DISF.</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange"
													href="<?= SERVERURL ?>/Vacaciones/lista/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="Novedades">Libro Vac. Disf.</span>
												</a>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>

							<!-- LIQUIDACION CESANTÍAS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionCesantias/liquidar') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionCesantias/liquidar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">LIQ. CESANTÍAS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionCesantias/liquidar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Liq. cesantías</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- SIMULACION DE LIQUIDACION CONTRATO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'simulacionLiquidacionContrato/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/simulacionLiquidacionContrato/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">SIMULACIÓN LIQ.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/simulacionLiquidacionContrato/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Simulación Liq.</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- LIQUIDACION CONTRATO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionContrato/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionContrato/lista/1/0">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">LIQ. CONTRATO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionContrato/lista/1/0">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Liq. contrato</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- RELIQUIDACION CONTRATO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'liquidacionContrato/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/liquidacionContrato/reliquidar">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Novedades">RELIQ. CONTRATO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/liquidacionContrato/reliquidar">
										<i class="material-icons">dvr</i>
										<span data-i18n="Novedades">Reliq. contrato</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- CONTABILIZACION DE NOMINA -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">CONTABILIZACIÓN</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- COMPROBANTES DE DIARIO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'comprobantes/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/comprobantes/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Comprobantes">COMPROBANTES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/comprobantes/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Comprobantes">Comprobantes</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CONTABILIZACION EN SAP -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'contabilizacionSAP/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/contabilizacionSAP/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="ContabilizacionSAP">CONTABILIZACIÓN SAP</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/contabilizacionSAP/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="ContabilizacionSAP">Contabilización SAP</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DISPERSION BANCARIA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dispersionNomina/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dispersionNomina/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="ContabilizacionSAP">DISPERSIÓN BANCARIA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dispersionNomina/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="ContabilizacionSAP">Dispersión bancaria</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- TRASLADOS ENTRE CENTROS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'trasladosCentros/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/trasladosCentros/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="ContabilizacionSAP">TRASLADOS CENTROS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/trasladosCentros/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="ContabilizacionSAP">Traslados centros</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- INFORME DE CONTABILIZACION EN SAP -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'informeContabilizacionAcumulados/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/informeContabilizacionAcumulados/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="ContabilizacionSAP">INF. CONTABILIZACIÓN</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/informeContabilizacionAcumulados/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="ContabilizacionSAP">Inf. contabilización</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- NOMINA ELECTRONICA -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">NÓMINA ELECT.</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- INICIO CONTADORES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'nominaElectronica/inicio') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/nominaElectronica/inicio">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="NominaElectronica">INICIO CONTADORES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/nominaElectronica/inicio">
										<i class="material-icons">dvr</i>
										<span data-i18n="NominaElectronica">Inicio contadores</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- NOMINA ELECTRONICA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'nominaElectronica/parametros') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/nominaElectronica/parametros">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="NominaElectronica">NÓM. ELECTRÓNICA</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/nominaElectronica/parametros">
										<i class="material-icons">dvr</i>
										<span data-i18n="NominaElectronica">Nóm. electrónica</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- REPORTE NOMINA ELECTRONICA -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'nominaElectronica/report') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/nominaElectronica/report">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="NominaElectronica">REP. NÓM. ELECT</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/nominaElectronica/report">
										<i class="material-icons">dvr</i>
										<span data-i18n="NominaElectronica">Rep. Nóm. electrónica</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- DOTACIÓN -->
				<?php if (FALSE): ?>
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDotacion">DOTACIÓN</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- DOTACION -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'dotacion/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/dotacion/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Dotacion">DOTACIÓN</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/dotacion/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Dotacion">Dotación</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- ENTREGA DOTACION -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'entregadotacion/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/entregadotacion/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="EntregaDotacion">ENTREGA DE DOTACIÓN</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/entregadotacion/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="EntregaDotacion">Entrega de dotación</span>
									</a>
								</li>
							<?php endif; ?>

						</ul>
					</div>
				</li>
				<?php endif; ?>

				<!-- ARCHIVOS BASICOS -->
				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">ARCHIVOS BÁSICOS</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- CENTROS DE COSTO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'centros/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/centros/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Centros">CENTROS DE COSTO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/centros/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Centros">Centros de costo</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- PERIODOS DE PAGO -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'periodos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/periodos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Periodos">PERÍODOS DE PAGO</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/periodos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Periodos">Períodos de pago</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CONCEPTOS -->
							<li class="bold">
								<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
									<i class="material-icons">photo_filter</i>
									<span class="menu-title" data-i18n="MenuConceptos">CONCEPTOS</span>
								</a>
								<div class="collapsible-body">
									<ul class="collapsible collapsible-sub" data-collapsible="accordion">
										<!-- CONCEPTOS MAYORES -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'mayores/lista') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/mayores/lista/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="ConceptosMayores">MAYORES</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/mayores/lista/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="ConceptosMayores">Mayores</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- CONCEPTOS AUXILIARES -->
										<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'auxiliares/lista') !== false ): ?>
											<li class="active">
												<a class="active cyan darken-4 white-text"
													href="<?= SERVERURL ?>/auxiliares/lista/1">
													<i class="material-icons white-text">dvr</i>
													<span data-i18n="ConceptosAuxiliares">AUXILIARES</span>
												</a>
											</li>
										<?php else: ?>
											<li class="bold">
												<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/auxiliares/lista/1">
													<i class="material-icons">dvr</i>
													<span data-i18n="ConceptosAuxiliares">Auxiliares</span>
												</a>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</li>

							<!-- CATEGORIAS DE EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'categorias/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/categorias/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="CategoriasEmpleados">CATEGORÍAS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/categorias/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="CategoriasEmpleados">Categorías</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CARGOS DE EMPLEADOS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'cargos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/cargos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="CargosEmpleados">CARGOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/cargos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="CargosEmpleados">Cargos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- TERCEROS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'terceros/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/terceros/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Terceros">TERCEROS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/terceros/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Terceros">Terceros</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- TIPOS DE COMPROBANTES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'tipodoc/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/tipodoc/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="TipoDoc">TIPOS DE COMPR.</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/tipodoc/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="TipoDoc">Tipos de compr.</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- SISTEMA -->
				<li class="navigation-header">
					<a class="navigation-header-text">SISTEMA</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li>

				<li class="bold">
					<a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
						<i class="material-icons">photo_filter</i>
						<span class="menu-title" data-i18n="MenuDocumentos">SISTEMA</span>
					</a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
							<!-- USUARIOS -->
							<?php if (isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'usuarios/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/usuarios/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Usuarios">USUARIOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/usuarios/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Usuarios">Usuarios</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- PLANTILLA -->
							<?php if (isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'plantillas/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/plantillas/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Plantillas">PLANTILLAS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/plantillas/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Plantillas">Plantillas</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- PARAMETROS -->
							<?php if (isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'parametros/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/parametros/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Parametros">PARÁMETROS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/parametros/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Parametros">Parámetros</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- CIUDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'ciudades/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/ciudades/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Ciudades">CIUDADES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/ciudades/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Ciudades">Ciudades</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- PAISES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'paises/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/paises/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Paises">PAÍSES</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/paises/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Paises">Países</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- ENTIDADES BANCARIAS -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'bancos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/bancos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Bancos">BANCOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/bancos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Bancos">Bancos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- DIAGNOSTICOS DE INCAPACIDADES -->
							<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'diagnosticos/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/diagnosticos/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Bancos">DIAGNÓSTICOS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/diagnosticos/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Bancos">Diagnósticos</span>
									</a>
								</li>
							<?php endif; ?>

							<!-- IDIOMAS -->
							<?php if (isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'idiomas/lista') !== false ): ?>
								<li class="active">
									<a class="active cyan darken-4 white-text" href="<?= SERVERURL ?>/idiomas/lista/1">
										<i class="material-icons white-text">dvr</i>
										<span data-i18n="Idiomas">IDIOMAS</span>
									</a>
								</li>
							<?php else: ?>
								<li class="bold">
									<a class="waves-effect waves-orange" href="<?= SERVERURL ?>/idiomas/lista/1">
										<i class="material-icons">dvr</i>
										<span data-i18n="Idiomas">Idiomas</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</li>

				<!-- <li class="navigation-header">
					<a class="navigation-header-text">MIS DATOS</a>
					<i class="navigation-header-icon material-icons">more_horiz</i>
				</li> -->

				<!-- ACTUALIZACION REGISTRO -->
				<?php if ( isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'misdatos/editar') !== false ): ?>
					<!-- <li class="active">
						<a class="active cyan darken-4 white-text"
							href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
							<i class="material-icons white-text">dvr</i>
							<span data-i18n="MisDatos">DATOS PERSONALES</span>
						</a>
					</li> -->
				<?php else: ?>
					<!-- <li class="bold">
						<a class="waves-effect waves-orange"
							href="<?= SERVERURL ?>/misdatos/editar/<?= $_SESSION['Login']['Documento'] ?>">
							<i class="material-icons">dvr</i>
							<span data-i18n="MisDatos">Datos personales</span>
						</a>
					</li> -->
				<?php endif; ?>

				<?php break; ?>
		<?php endswitch; ?>
	</ul>
	<div class="navigation-background"></div>
	<a class="sidenav-trigger btn-sidenav-toggle btn-floating btn-medium waves-effect waves-light hide-on-large-only orange darken-3"
		href="#" data-target="slide-out">
		<i class="material-icons">menu</i>
	</a>
</aside>

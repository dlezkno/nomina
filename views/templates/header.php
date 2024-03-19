<?php
	$imagen = "/assets/images/fondo" . rand(1, 15) . ".jpg";
?>
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
	<head>
		<meta name="Description" content="Nómina">
		<meta name="Author" content="César Gerardo García Mantilla">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

		<title><?= COMPANY ?></title>
		<link rel="shortcut icon" href="<?= media() ?>/images/icono1rrhh.png">

		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/vendors.min.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/animate-css/animate.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/chartist-js/chartist.min.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/chartist-js/chartist-plugin-tooltip.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/flag-icon/css/flag-icon.min.css">

		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/data-tables/css/jquery.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/data-tables/css/select.dataTables.min.css">

		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/css/themes/vertical-gradient-menu-template/materialize.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/css/themes/vertical-gradient-menu-template/style.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/css/pages/data-tables.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/css/custom/custom.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/css/pages/dashboard-modern.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/css/pages/intro.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/select2/select2.min.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/vendors/select2/select2-materialize.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/css/pages/page-account-settings.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/templates/materialize/app-assets/css/pages/app-chat.css">
		<link rel="stylesheet" type="text/css" href="<?= SERVERURL ?>/assets/css/style.css">
		<style>
			body {
				/* height: 60%; */
				background-size: cover;
				background-repeat:no-repeat;
  				background-position: center center;
				/* font-family: 'Rajdhani';font-size: 22px; */
			}		
		</style>
	</head>

	<?php if ( ! isset($_SESSION['Login']['Usuario']) AND ! isset($_SESSION['Login']['Perfil']) ): ?>
		<body class="vertical-layout vertical-menu-collapsible page-header-dark vertical-modern-menu preload-transitions 2-columns app-page" data-open="click" data-menu="vertical-modern-menu" data-col="2-columns" background="<?= SERVERURL . $imagen ?>">
		<div class="bg-img">
	<?php else: ?>
		<body class="vertical-layout vertical-menu-collapsible page-header-dark vertical-modern-menu 1-column login-bg blank-page" data-open="click" data-menu="vertical-modern-menu" data-col="1-column">
		<div class="bg-no-img">
	<?php endif; ?>
		<form id="formAct" method="post" enctype="multipart/form-data">
			<header class="page-topbar" id="header">
				<div class="navbar navbar-fixed">
					<nav class="navbar-main navbar-color nav-collapsible sideNav-lock no-shadow cyan darken-4 white-text">
						<div class="nav-wrapper">
							<?php if ((isset($_SESSION['Login']['Perfil']) AND $_SESSION['Login']['Perfil'] == EMPLEADO) OR ! isset($_SESSION['Login'])): ?>
								<img class="hide-on-med-and-down" src="<?= SERVERURL ?>/assets/images/logo.png" alt="<?= COMPANY ?>" height="50px" />
							<?php endif; ?>
							<!-- FILTRO DE LISTA -->
							<?php if ( isset($_SESSION['Login']['Id']) AND isset($_REQUEST['url']) AND strpos($_REQUEST['url'], 'lista') > 0): ?>
							<div class="header-search-wrapper" style="width:50%;">
								<i class="material-icons">search</i>
								<input id="Filtro" name="Filtro" class="header-search-input z-depth-2"
									type="text" placeholder="Buscar" data-search="template-list"
									value="<?= $lcFiltro ?>">
								<ul class="search-list collection display-none"></ul>
								<div style="display:none;">
									<button class="btn teal" type="submit" name="Filtrar">
										Filtrar
									</button>
								</div>
							</div>
							<?php endif; ?>

							<ul class="navbar-list right col s12 m12 l12">
								<!-- ADICIONAR -->
								<?php if	( isset($_SESSION['NuevoRegistro']) AND ! empty($_SESSION['NuevoRegistro']) ): ?>
								<li>
									<a href="<?php echo $_SESSION['NuevoRegistro']; ?>" class="tooltipped"
										data-position="bottom" data-tooltip="Nuevo registro">
										<i class="material-icons">add_circle</i>
									</a>
								</li>
								<?php endif; ?>

								<!-- INFORME -->
								<?php if	( isset($_SESSION['Informe']) AND ! empty($_SESSION['Informe']) ): ?>
								<li>
									<a target="_blank" href="<?= $_SESSION['Informe'] ?>" class="tooltipped"
										data-position="bottom" data-tooltip="Informe">
										<i class="material-icons">print</i>
									</a>
								</li>
								<?php endif; ?>

								<!-- GENERAR INFORME -->
								<?php if	( isset($_SESSION['GenerarInforme']) AND ! empty($_SESSION['GenerarInforme']) ): ?>
								<li>
									<a href="javascript:{}"
										onclick="document.getElementById('formAct').submit(); return false;"
										class="tooltipped" data-position="bottom" data-tooltip="Generar informe" target="_blank">
										<i class="material-icons">print</i>
									</a>
								</li>
								<?php endif; ?>

								<!-- IMPORTAR -->
								<?php if	( isset($_SESSION['Importar']) AND ! empty($_SESSION['Importar']) ): ?>
								<li>
									<a href="<?php echo $_SESSION['Importar']; ?>" class="tooltipped"
										data-position="bottom" data-tooltip="Importar">
										<i class="material-icons">input</i>
									</a>
								</li>
								<?php endif; ?>

								<!-- CARGAR ARCHIVO -->
								<?php if	( isset($_SESSION['ImportarArchivo']) AND ! empty($_SESSION['ImportarArchivo']) ): ?>
								<li>
									<a href="javascript:{}"
										onclick="document.getElementById('formAct').submit(); return false;"
										class="tooltipped" data-position="bottom" data-tooltip="Procesar importación">
										<i class="material-icons">cloud_upload</i>
									</a>
								</li>
								<?php endif; ?>

								<!-- EXPORTAR -->
								<?php if	( isset($_SESSION['Exportar']) AND ! empty($_SESSION['Exportar']) ): ?>
								<li>
									<!-- <a href="javascript:{}"
										onclick="document.getElementById('formAct').submit(); return false;"
										class="tooltipped" data-position="bottom" data-tooltip="Exportar" value="Exportar">
										<i class='far fa-file-excel'></i>
									</a> -->
									<button class="btn btn-sm cyan darken-4 " type="submit" name="Action" value="Exportar">
										Exportar
  									</button>									
								</li>
								<?php endif; ?>

								<!-- DESCARGAR ARCHIVO -->
								<?php if	( isset($_SESSION['ExportarArchivo']) AND ! empty($_SESSION['ExportarArchivo']) ): ?>
								<li>
									<button class="btn cyan darken-4" type="submit" name="Action" value="ExportarArchivo">
										Excel
  									</button>	
									<!-- <a href="<?php echo $_SESSION['ExportarArchivo']; ?>" class="tooltipped"
										data-position="bottom" data-tooltip="Exportar">
										<i class="fas fa-file-excel" style="font-size:26px"></i>
									</a> -->
								</li>
								<?php endif; ?>

								<!-- ACTUALIZAR -->
								<?php if ( isset($_SESSION['ActualizarRegistro']) AND ! empty($_SESSION['ActualizarRegistro']) ): ?>
								<li>
									<a href="javascript:{}"
										onclick="document.getElementById('formAct').submit(1); return false;"
										class="tooltipped" data-position="bottom" data-tooltip="Guardar">
										<i class="material-icons">save</i>
									</a>
								</li>
								<?php endif; ?>

								<!-- NOVEDADES -->
								<?php if	( isset($_SESSION['Novedades']) AND ! empty($_SESSION['Novedades']) ): ?>
								<li>
									<a href="<?php echo $_SESSION['Novedades']; ?>" class="tooltipped"
										data-position="bottom" data-tooltip="Novedades">
										<i class="material-icons">new_releases</i>
									</a>
								</li>
								<?php endif; ?>

								<!-- BORRAR -->
								<?php if	( isset($_SESSION['BorrarRegistro']) AND ! empty($_SESSION['BorrarRegistro']) ): ?>
								<li>
									<a href="javascript:{}"
										onclick="document.getElementById('formAct').submit(); return false;"
										class="tooltipped" data-position="bottom" data-tooltip="Borrar">
										<i class="material-icons">delete</i>
									</a>
								</li>
								<?php endif; ?>

								<!-- LISTA -->
								<?php if	( isset($_SESSION['Lista']) AND ! empty($_SESSION['Lista']) ): ?>
								<li>
									<a href="<?php echo $_SESSION['Lista']; ?>" class="tooltipped"
										data-position="bottom" data-tooltip="Lista">
										<i class="material-icons">list</i>
									</a>
								</li>
								<?php endif; ?>
								
								<!-- CORREOS -->
								<?php if ( isset($_SESSION['Correo']) AND ! empty($_SESSION['Correo']) ): ?>
								<li>
									<button class="btn btn-sm cyan darken-4 " type="submit" name="Action" value="Correo">
										ENVIAR CORREOS
  									</button>									
								</li>
								<?php endif; ?>
								
								<?php if ( isset($_SESSION['Login']['Perfil']) ): ?>
								<li>
									<a class="waves-effect waves-block waves-light notification-button"
										href="javascript:void(0);" data-target="notifications-dropdown">
									</a>
								</li>
								<li>
									<a class="waves-effect waves-block waves-light profile-button"
										href="javascript:void(0);" data-target="profile-dropdown">
										<?php
											$cPath = 'assets/images/users/';
							
											$dir = opendir($cPath);

											$llEncontrado = FALSE;
							
											while ( $elemento = readdir($dir) )
											{
												if	( $elemento == '.' OR $elemento == '..' OR is_dir($elemento) )
													continue;

												if	( pathinfo($elemento, PATHINFO_FILENAME) == $_SESSION['Login']['Usuario'] )
												{
													$llEncontrado = TRUE;
													break;
												}
											}

											if	( ! $llEncontrado )
											{
										?>
										<i class="material-icons">person_outline</i>
										<?php
											}
											else
											{
										?>
										<span class="avatar-status avatar-online">
											<img src="<?= SERVERURL ?>/assets/images/users/<?php echo $_SESSION['Login']['Usuario']; ?>.jpg"
												alt="avatar" width="200px">
										</span>
										<?php
											}
										?>
									</a>
								</li>
								<?php endif; ?>
							</ul>
							<ul class="dropdown-content" id="notifications-dropdown">
							</ul>

							<!-- PERFIL -->
							<?php if (isset($_SESSION['Login']['Id'])): ?>
							<ul class="dropdown-content" id="profile-dropdown">
								<?php if ($_SESSION['Login']['Perfil'] <> EMPLEADO): ?>
								<li>
									<a class="grey-text text-darken-1"
										href="<?= SERVERURL ?>/usuarios/consulta/<?= $_SESSION['Login']['Id'] ?>">
										<i class="material-icons">person_outline</i>
										<?php echo label('Perfil'); ?>
									</a>
								</li>
								<li class="divider"></li>
								<?php endif; ?>
								<li>
									<a class="grey-text text-darken-1" href="<?= SERVERURL ?>">
										<i class="material-icons">keyboard_tab</i>
										<?php echo label('Terminar'); ?>
									</a>
								</li>
							</ul>
							<?php endif; ?>
						</div>
					</nav>
				</div>
			</header>

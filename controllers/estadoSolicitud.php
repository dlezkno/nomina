<?php
	require_once('./templates/vendor/autoload.php');

	class EstadoSolicitud extends Controllers
	{
		public function lista($pagina)
		{
			$data['mensajeError'] = '';

			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['ESTADO_SOLICITUD']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['ESTADO_SOLICITUD']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['ESTADO_SOLICITUD']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['ESTADO_SOLICITUD']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['ESTADO_SOLICITUD']['Filtro']))
			{
				$_SESSION['ESTADO_SOLICITUD']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['ESTADO_SOLICITUD']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['ESTADO_SOLICITUD']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['ESTADO_SOLICITUD']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['ESTADO_SOLICITUD']['Orden'])) 
					$_SESSION['ESTADO_SOLICITUD']['Orden'] = 'PARAMETROS1.Detalle DESC,EMPLEADOS.FechaIngreso,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

			$query = "WHERE (PARAMETROS1.Detalle = 'ACTIVO' OR PARAMETROS1.Detalle = 'EN PROCESO DE SELECCION' OR PARAMETROS1.Detalle = 'EN PROCESO DE CONTRATACION') ";

			if	( ! empty($lcFiltro) )
			{
				$query .= "AND (UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(EMPLEADOS.CodigoSAP, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CARGOS.Cargo, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CENTROS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(PROYECTOS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%' ";
				$query .= "OR UPPER(REPLACE(PROYECTOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '" . mb_strtoupper($lcFiltro) . "%') ";
			}

			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['ESTADO_SOLICITUD']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarEmpleados($query);
			$this->views->getView($this, 'estadoSolicitud', $data);
		}	
	}
?>
<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Plantillas extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/plantillas/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/plantillas/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['PLANTILLAS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['PLANTILLAS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['PLANTILLAS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['PLANTILLAS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['PLANTILLAS']['Filtro']))
			{
				$_SESSION['PLANTILLAS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['PLANTILLAS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['PLANTILLAS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['PLANTILLAS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['PLANTILLAS']['Orden'])) 
					$_SESSION['PLANTILLAS']['Orden'] = 'PARAMETROS1.Detalle';

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= 'WHERE ';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(PLANTILLAS.Asunto, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['PLANTILLAS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarPlantillas($query);
			$this->views->getView($this, 'plantillas', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/plantillas/actualizarAuxiliar';
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
			$_SESSION['Lista'] = SERVERURL . '/plantillas/lista/' . $_SESSION['PLANTILLAS']['Pagina'];

			$data = array(
				'reg' => array(
					'EstadoEmpleado' => isset($_REQUEST['EstadoEmpleado']) ? $_REQUEST['EstadoEmpleado'] : '',
					'TipoPlantilla' => isset($_REQUEST['TipoPlantilla']) ? $_REQUEST['TipoPlantilla'] : '',
					'TipoContrato' => isset($_REQUEST['TipoContrato']) ? $_REQUEST['TipoContrato'] : 0,
					'Asunto' => isset($_REQUEST['Asunto']) ? $_REQUEST['Asunto'] : '',
					'Plantilla' => isset($_REQUEST['Plantilla']) ? $_REQUEST['Plantilla'] : '', 
					'CodigoDocumento' => isset($_REQUEST['CodigoDocumento']) ? $_REQUEST['CodigoDocumento'] : ''
				),
				'nombreTipoContrato' => '',
				'mensajeError' => ''
			);

			if (isset($_REQUEST['EstadoEmpleado'])) 
			{
				if	( empty($data['reg']['EstadoEmpleado']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Estado de empleado') . '</strong><br>';

				if	( empty($data['reg']['TipoPlantilla']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de plantilla') . '</strong><br>';

				// if	( empty($data['reg']['TipoContrato']) )
				// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de contrato') . '</strong><br>';

				if	( empty($data['reg']['Asunto']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Asunto') . '</strong><br>';

				if	( empty($data['reg']['Plantilla']) )
					$data['mensajeError'] .= label('Debe digitar un texto de') . ' <strong>' . label('Plantilla') . '</strong><br>';

				// if	( empty($data['reg']['CodigoDocumento']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Código de documento') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarPlantilla($data['reg']);

					if ($id) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
				$this->views->getView($this, 'adicionar', $data);
		}

		public function editar($id)
		{
			if (isset($_REQUEST['EstadoEmpleado']))
			{
				$data = array(
					'reg' => array(
						'EstadoEmpleado' => isset($_REQUEST['EstadoEmpleado']) ? $_REQUEST['EstadoEmpleado'] : '',
						'TipoPlantilla' => isset($_REQUEST['TipoPlantilla']) ? $_REQUEST['TipoPlantilla'] : '',
						'TipoContrato' => isset($_REQUEST['TipoContrato']) ? $_REQUEST['TipoContrato'] : 0,
						'Asunto' => isset($_REQUEST['Asunto']) ? $_REQUEST['Asunto'] : '',
						'Plantilla' => isset($_REQUEST['Plantilla']) ? $_REQUEST['Plantilla'] : '', 
						'CodigoDocumento' => isset($_REQUEST['CodigoDocumento']) ? $_REQUEST['CodigoDocumento'] : ''
					),
					'nombreEstadoEmpleado' => '',
					'nombreTipoContrato' => '',
					'mensajeError' => ''
				);

				if	( empty($data['reg']['EstadoEmpleado']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Estado de empleado') . '</strong><br>';

				if	( empty($data['reg']['TipoPlantilla']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de plantilla') . '</strong><br>';

				// if	( empty($data['reg']['TipoContrato']) )
				// 	$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de contrato') . '</strong><br>';

				if	( empty($data['reg']['Asunto']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Asunto') . '</strong><br>';

				if	( empty($data['reg']['Plantilla']) )
					$data['mensajeError'] .= label('Debe digitar un texto de') . ' <strong>' . label('Plantilla') . '</strong><br>';

				// if	( empty($data['reg']['CodigoDocument']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Código de documento') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarPlantilla($data['reg'], $id);

					if ($resp) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/plantillas/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/plantillas/lista/' . $_SESSION['PLANTILLAS']['Pagina'];

				$query = 'SELECT * FROM PLANTILLAS WHERE PLANTILLAS.Id = ' . $id;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'Id' => $reg['id'],
						'EstadoEmpleado' => $reg['estadoempleado'],
						'TipoPlantilla' => $reg['tipoplantilla'],
						'TipoContrato' => $reg['tipocontrato'],
						'Asunto' => $reg['asunto'],
						'Plantilla' => $reg['plantilla'], 
						'CodigoDocumento' => $reg['codigodocumento']
					),
					'nombreTipoContrato' => '',
					'mensajeError' => ''
				);

				if ($data) 
					$this->views->getView($this, 'actualizar', $data, $id);
			}
		}

		public function borrar($id)
		{
			$query = <<<EOD
				SELECT *
				FROM PLANTILLAS
				WHERE PLANTILLAS.Id = $id;
			EOD;
				
			$reg = $this->model->leer($query);

			$data = array(
				'reg' => array(
					'Id' => $reg['id'],
					'EstadoEmpleado' => $reg['estadoempleado'],
					'TipoPlantilla' => $reg['tipoplantilla'],
					'TipoContrato' => $reg['tipocontrato'],
					'Asunto' => $reg['asunto'],
					'Plantilla' => $reg['plantilla'], 
					'CodigoDocumento' => $reg['codigodocumento']
				),
				'nombreTipoContrato' => '',
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Id']))
			{
				// $query = 'SELECT COUNT(*) AS Registros ' .
				// 		'FROM AUXILIARES ' .
				// 		'WHERE AUXILIARES.IdMayor = ' . $id;

				// $reg = $this->model->buscarMayor($query);

				// if ($reg['registros'] > 0) 
				// {
				// 	$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Concepto mayor') . '</strong><br>';
				// }

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarPlantilla($id);

					if ($resp) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = SERVERURL . '/plantillas/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/plantillas/lista/' . $_SESSION['PLANTILLAS']['Pagina'];

				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function informe()
		{
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
			$_SESSION['Informe'] = 00;
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/plantillas/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['PLANTILLAS']['Filtro'];

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= 'WHERE ';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(PLANTILLAS.Asunto, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['PLANTILLAS']['Orden']; 
			$data['rows'] = $this->model->listarPlantillas($query);
			$this->views->getView($this, 'informe', $data);
		}
	}
?>
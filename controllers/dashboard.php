<?php
	class Dashboard extends Controllers
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function dashboard()
		{


			$FechaInicial = ComienzoMes(date('Y-m-d'));
			$FechaFinal = FinMes(date('Y-m-d'));

			$Ingresos = $this->model->contarIngresos($FechaInicial, $FechaFinal);			
			$Egresos = $this->model->contarEgresos($FechaInicial, $FechaFinal);	
			$TotalEmpleados = $this->model->contarEmpleados();	

			$EmpleadosAnt = $TotalEmpleados + $Egresos - $Ingresos;
			$VariacionIngresos = ($EmpleadosAnt>0) ? round($Ingresos / $EmpleadosAnt * 100, 0) : 0;
			$VariacionEgresos = ($EmpleadosAnt>0) ? round($Egresos / $EmpleadosAnt * 100, 0) : 0;
			$VariacionEmpleados = ($TotalEmpleados>0) ? round($EmpleadosAnt / $TotalEmpleados * 100, 0) : 0;

			$data = array(
				'Ingresos' => $Ingresos,
				'VariacionIngresos' => $VariacionIngresos,
				'Egresos' => $Egresos,
				'VariacionEgresos' => $VariacionEgresos,
				'TotalEmpleados' => $TotalEmpleados, 
				'VariacionEmpleados' => $VariacionEmpleados
			);

			$data['EmpleadosNuevos'] = array();

			$EmpleadosNuevos = $this->model->empleadosNuevos($FechaInicial, $FechaFinal);

			for	($i = 0; $i < count($EmpleadosNuevos); $i++)
			{
				$reg = $EmpleadosNuevos[$i];

				$data['EmpleadosNuevos'][] = array(
					'Empleado' => $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'],
					'Centro' => $reg['Centro'],
					'NombreCentro' => $reg['NombreCentro'],
					'Cargo' => $reg['Cargo'], 
					'NombreCargo' => $reg['NombreCargo'], 
					'FechaIngreso' => $reg['FechaIngreso']
				);
			}

			$data['EmpleadosRetirados'] = array();
			
			$EmpleadosRetirados = $this->model->empleadosRetirados($FechaInicial, $FechaFinal);

			for	($i = 0; $i < count($EmpleadosRetirados); $i++)
			{
				$reg = $EmpleadosRetirados[$i];

				$data['EmpleadosRetirados'][] = array(
					'Empleado' => $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'],
					'Centro' => $reg['Centro'],
					'NombreCentro' => $reg['NombreCentro'],
					'Cargo' => $reg['Cargo'], 
					'NombreCargo' => $reg['NombreCargo'], 
					'FechaRetiro' => $reg['FechaRetiro']
				);
			}

			$EmpleadosPorCentro = $this->model->empleadosPorCentro();

			for	($i = 0; $i < count($EmpleadosPorCentro); $i++)
			{
				$reg = $EmpleadosPorCentro[$i];

				$data['EmpleadosPorCentro'][] = array(
					'Centro' => $reg['Centro'],
					'NombreCentro' => $reg['NombreCentro'],
					'Empleados' => $reg['Registros']
				);
			}

			$CumpleanosEmpleados = $this->model->cumpleanosEmpleados();

			for	($i = 0; $i < count($CumpleanosEmpleados); $i++)
			{
				$reg = $CumpleanosEmpleados[$i];

				$data['CumpleanosEmpleados'][] = array(
					'Empleado' => $reg['apellido1'] . ' ' . $reg['apellido2'] . ' ' . $reg['nombre1'] . ' ' . $reg['nombre2'],
					'Cargo' => $reg['NombreCargo'],
					'Centro' => $reg['NombreCentro'],
					'FechaNacimiento' => $reg['fechanacimiento']
				);
			}

			$VencimientoContratos = $this->model->vencimientoContratos();

			for	($i = 0; $i < count($VencimientoContratos); $i++)
			{
				$reg = $VencimientoContratos[$i];

				$data['VencimientoContratos'][] = array(
					'Empleado' => $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'],
					'Cargo' 			=> $reg['NombreCargo'],
					'Centro' 			=> $reg['NombreCentro'],
					'Proyecto' 			=> $reg['NombreProyecto'], 
					'FechaRetiro'		=> $reg['FechaRetiro'], 
					'FechaVencimiento' 	=> $reg['FechaVencimiento']
				);
			}

			$data['InconsistenciasEmpleados'] = $this->model->inconsistenciasEmpleados();


			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'VERIFICAR'){
				$data['signscount'] = getLogs("FIRMA PLUS", $_REQUEST['fechaInit'], $_REQUEST['fechaEnd']);
			}else{
				$today = date('y-m-d');
				$dataDate = explode("-",$today);
				$data['signscount'] = getLogs("FIRMA PLUS","20".$dataDate[0]."-".$dataDate[1]."-01" ,"20".$today);
			}

			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
			$_SESSION['Paginar'] = FALSE;

			$this->views->getView($this, 'dashboard', $data);
		}	
	}
?>

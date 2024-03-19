<?php
	require_once('./templates/vendor/autoload.php');

	use Phpml\Regression\LeastSquares;

	class Pronosticos extends Controllers {
		public function contrataciones() {
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/pronosticos/contrataciones';
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

			$data = array(
				'reg' => array(
					'IdProyecto' 		=> isset($_REQUEST['IdProyecto']) ? $_REQUEST['IdProyecto'] : NULL,
				),	
				'mensajeError' => ''
			);

			if (isset($_REQUEST['IdProyecto']) AND !empty($_REQUEST['IdProyecto'])) {
				try {
					$idProyecto = $_REQUEST['IdProyecto'];
					$numHistoricosRL = NUM_HISTORICOS_RL;
	
					// Datos históricos de contratación, retiros, ausencias y vacaciones
					$query = <<<EOD
						SELECT
							FORMAT(per.fechainicial, 'yyyy-MM') anio_mes,
							(SELECT COUNT(*)
								FROM empleados emp
								WHERE FORMAT(per.fechainicial, 'yyyy-MM') = FORMAT(emp.fechaingreso, 'yyyy-MM') AND
									(emp.idcentro = $idProyecto OR emp.idproyecto = $idProyecto)) contrataciones,
							(SELECT COUNT(*)
								FROM empleados emp
								WHERE FORMAT(per.fechainicial, 'yyyy-MM') = FORMAT(emp.fecharetiro, 'yyyy-MM') AND
									(emp.idcentro = $idProyecto OR emp.idproyecto = $idProyecto)) retiros,
							(SELECT COUNT(DISTINCT acu.idempleado)
								FROM acumulados acu
								JOIN auxiliares aux ON aux.id = acu.idconcepto
								JOIN empleados emp ON emp.id = acu.idempleado
								WHERE per.fechainicial = acu.fechainicialperiodo AND
									(aux.nombre LIKE '%INCAPACIDAD%' OR aux.nombre LIKE '%ausencia%') AND
									(emp.idcentro = $idProyecto OR emp.idproyecto = $idProyecto)) ausencias,
							(SELECT COUNT(DISTINCT acu.idempleado)
								FROM acumulados acu
								JOIN auxiliares aux ON aux.id = acu.idconcepto
								JOIN empleados emp ON emp.id = acu.idempleado
								WHERE per.fechainicial = acu.fechainicialperiodo AND aux.nombre LIKE '%VACACIONES%' AND
									(emp.idcentro = $idProyecto OR emp.idproyecto = $idProyecto)) vacaciones
						FROM periodos per
						WHERE per.id in (SELECT DISTINCT idperiodo FROM acumulados)
						ORDER BY per.fechainicial DESC
						OFFSET 1 ROWS
						FETCH FIRST $numHistoricosRL ROWS ONLY;
					EOD;
	
					$datosHistoricos = $this->model->listar($query);
					$data['reg']['items'] = $datosHistoricos;
	
					// Preparar los datos para el modelo
					$samples = array_map(function ($datos) {
						return [$this->calcularCargaDeTrabajoAjustada($datos)];
					}, $datosHistoricos);
	
					$targets = array_map(function ($datos) {
						return $datos['contrataciones'];
					}, $datosHistoricos);
	
	
					// Valores proyectados para el próximo mes
					$retirosProyectados = 2; // Supongamos que se proyectan 2 retiros para el próximo mes
					$ausenciasProyectadas = 4; // Supongamos que se proyectan 4 ausencias para el próximo mes
					$vacacionesProyectadas = 3; // Supongamos que se proyectan 3 días de vacaciones para el próximo mes
	
					// Pronosticar el número de contrataciones para el próximo mes
					$cargaDeTrabajoAjustadaProyectada = 20 * ($retirosProyectados + $ausenciasProyectadas + $vacacionesProyectadas);
	
					$regression = new LeastSquares();
					$regression->train($samples, $targets);
					$data['reg']['pronostico'] = $regression->predict([$cargaDeTrabajoAjustadaProyectada]);
				} catch (\Throwable $th) {}
			}

			$this->views->getView($this, 'contrataciones', $data);
			exit;
		}

		// Calcular la carga de trabajo ajustada en función de los retiros, ausencias y vacaciones
		private function calcularCargaDeTrabajoAjustada($datos) {
			$retiros = $datos['retiros'];
			$ausencias = $datos['ausencias'];
			$vacaciones = $datos['vacaciones'];
		
			// Calcular la carga de trabajo ajustada
			$cargaDeTrabajoAjustada = 20 * ($retiros + $vacaciones); // Se asume 20 días laborables por mes
		
			return $cargaDeTrabajoAjustada < 0 ? 0 : $cargaDeTrabajoAjustada;
		}
	}
?>
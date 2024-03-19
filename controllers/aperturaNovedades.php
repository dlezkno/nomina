<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class AperturaNovedades extends Controllers
	{
		public function editar()
		{
			if (isset($_REQUEST['Periodicidad']))
			{
				$data = array(
					'reg' => array(
						'Referencia' 			=> isset($_REQUEST['Referencia']) ? $_REQUEST['Referencia'] : '',
						'Periodicidad' 			=> isset($_REQUEST['Periodicidad']) ? $_REQUEST['Periodicidad'] : '',
						'Periodo' 				=> isset($_REQUEST['Periodo']) ? $_REQUEST['Periodo'] : '',
						'Ciclo' 				=> isset($_REQUEST['Ciclo']) ? $_REQUEST['Ciclo'] : '',
						'SoloNovedades' 		=> isset($_REQUEST['SoloNovedades']) ? 1 : 0,
						'FechaLimiteNovedades' 	=> isset($_REQUEST['FechaLimiteNovedades']) ? $_REQUEST['FechaLimiteNovedades'] : ''
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['Referencia']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Referencia (año)') . '</strong><br>';
	
				if	( empty($data['reg']['Periodicidad']) )
					$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Periodicidad') . '</strong><br>';
	
				if	( empty($data['reg']['Periodo']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Período') . '</strong><br>';
			
				if	( empty($data['reg']['Ciclo']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Ciclo') . '</strong><br>';
			
				if	( empty($data['reg']['FechaLimiteNovedades']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha límite de novedades') . '</strong><br>';

				if ( empty($data['mensajeError']) ) 
				{
					// VALIDAR LOS DATOS
					$Referencia 	= $data['reg']['Referencia'];
					$Periodicidad 	= $data['reg']['Periodicidad'];
					$Periodo 		= $data['reg']['Periodo'];
					$Ciclo 			= $data['reg']['Ciclo'];
					$SoloNovedades  = $data['reg']['SoloNovedades'];

					$regPeriodicidad = getRegistro('PARAMETROS', $Periodicidad);
					$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);
					
					$query = <<<EOD
						PERIODOS.Referencia = $Referencia AND 
						PERIODOS.Periodicidad = $Periodicidad AND 
						PERIODOS.Periodo = $Periodo;
					EOD;

					$regPeriodo = getRegistro('PERIODOS', 0, $query);

					if (! $regPeriodo) 
						$data['mensajeError'] .= '<strong>' . label('Período') . '</strong> ' . label('definido no existe') . '<br>';
				}

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$Schema = 'nomina';
					$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;
					$Indice1 = $ArchivoNomina . '_IdEmpleado';
					$Indice2 = $ArchivoNomina . '_IdConcepto';

					$query = <<<EOD
						SELECT COUNT(*) AS Registros 
							FROM INFORMATION_SCHEMA.TABLES 
							WHERE TABLE_SCHEMA = '$Schema' AND 
								TABLE_NAME = '$ArchivoNomina';
					EOD;

					$tablas = $this->model->leer($query);

					if ($tablas['Registros'] == 0) 
					{
						$query = <<<EOD
							CREATE TABLE $Schema.$ArchivoNomina (
								id 							integer 		IDENTITY(1,1) NOT NULL,
								idperiodo 					integer 		default 0,
								ciclo 						integer 		default 0,
								idempleado 					integer 		default 0,
								idconcepto 					integer 		default 0,
								base						numeric(12, 2)	default 0,
								porcentaje					numeric(8, 4) 	default 0, 
								horas 						numeric(12, 4) 	default 0,
								valor 						numeric(12, 2) 	default 0,
								saldo 						numeric(12, 2) 	default 0,
								liquida 					varchar(1) 		default '',
								afecta 						integer 		default 0,
								clase_cr 					integer 		default 0,
								idcentro 					integer 		default 0,
								tipoempleado 				integer 		default 0,
								idcredito 					integer 		default 0,
								fecha 						date 			NULL,
								tiporegistro 				integer 		default 0,
								fechainicial 				date 			NULL,
								fechafinal 					date 			NULL,
								idtercero 					integer 		default 0,
								fechacreacion 				datetime2(7) 	default getdate(),
								fechaactualizacion 			datetime2(7) 	NULL,
								borrado 					bit 			default 0
							);
						
							CREATE INDEX $Indice1 ON $Schema.$ArchivoNomina
								(IdEmpleado ASC);
						
							CREATE INDEX $Indice2 ON $Schema.$ArchivoNomina 
								(IdConcepto ASC);
								
							CREATE SYNONYM dbo.$ArchivoNomina FOR $Schema.$ArchivoNomina;
						EOD;

						$ok = $this->model->query($query);

						$IdPeriodo = $regPeriodo['id'];

						$query = <<<EOD
							INSERT INTO PERIODOSACUMULADOS 
								(IdPeriodo, Ciclo, Acumulado, SoloNovedades) 
								VALUES (
									$IdPeriodo, 
									$Ciclo,
									0, 
									$SoloNovedades);
						EOD;

						$ok = $this->model->query($query);
					}
					else
					{
						$IdPeriodo = $regPeriodo['id'];

						$Id = getId('PERIODOSACUMULADOS', "PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo AND PERIODOSACUMULADOS.Ciclo = $Ciclo");

						if ($Id)
						{
							$query = <<<EOD
								UPDATE PERIODOSACUMULADOS 
									SET SoloNovedades = $SoloNovedades 
									WHERE PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo AND 
										PERIODOSACUMULADOS.Ciclo = $Ciclo;
							EOD;
						}
						else
						{
							$query = <<<EOD
								INSERT INTO PERIODOSACUMULADOS 
									(IdPeriodo, Ciclo, Acumulado, SoloNovedades) 
									VALUES (
										$IdPeriodo, 
										$Ciclo,
										0, 
										$SoloNovedades);
							EOD;
						}

						$ok = $this->model->query($query);
					}

					$resp = $this->model->actualizarNovedad($data['reg']);

					if ($resp) 
					{
						header('Location: ' . SERVERURL . '/novedades/lista/1');
						exit();
					}
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/aperturaNovedades/editar';
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

				$reg0 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
				$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
				$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");

				$Periodo = getRegistro('PERIODOS', $reg2['valor'])['periodo'];

				$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");
				$reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'");

				$data = array(
					'reg' => array(
						'Referencia' 			=> $reg0['valor'],
						'Periodicidad' 			=> $reg1['valor'],
						'Periodo' 				=> $Periodo,
						'Ciclo' 				=> $reg3['valor'],
						'SoloNovedades'			=> FALSE, 
						'FechaLimiteNovedades' 	=> $reg4['fecha']
					),
					'mensajeError' => ''
				);

				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}
	}
?>
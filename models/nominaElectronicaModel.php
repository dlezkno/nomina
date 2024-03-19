<?php
	class nominaElectronicaModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function getConcepts() {
			$query = <<<EOD
				SELECT
					aux.id,
					aux.auxiliar 'Auxiliar',
					aux.nombre 'Nombre Concepto',
					may.mayor 'Mayor',
					may.nombre 'Nombre Mayor',
					IIF ((
						(aux.auxiliar IN ('001', '002', '003', '005', '006', '007', '051') AND may.mayor = '01') -- DEVENGOSPARAMETROS DEVENGADOS TYPE
						OR (aux.auxiliar IN ('001') AND may.mayor = '04') -- TRANSPORTE
						OR (aux.auxiliar IN ('001', '051') AND may.mayor = '02') -- HORAS EXTRAS DIURNAS
						OR (aux.auxiliar IN ('002', '052') AND may.mayor = '02') -- HORAS EXTRAS NOCTURNAS
						OR (aux.auxiliar IN ('001', '051') AND may.mayor = '03') -- RECARGO NOCTURNO
						OR (aux.auxiliar IN ('003', '053') AND may.mayor = '02') -- HORAS EXTRAS DIURNAS FESTIVAS
						OR (aux.auxiliar IN ('005') AND may.mayor = '02') -- HORAS RECARGO DIURNO DOMINICAL FESTIVAS
						OR (aux.auxiliar IN ('004', '054') AND may.mayor = '02') -- HORAS EXTRAS NOCTURNAS FESTIVAS
						OR (aux.auxiliar IN ('002', '052') AND may.mayor = '03') -- HORAS RECARGO NOCTURNO DOMINICAL FESTIVAS
						OR (may.mayor = '50') -- VACACIONES EN TIEMPO
						OR (may.mayor = '51') -- VACACIONES EN DINERO
						OR (may.mayor = '52') -- PRIMA LEGAL
						OR (may.mayor IN ('53', '54')) -- CESANTIAS
						OR ((may.mayor = '01' AND aux.auxiliar IN ('008', '009', '014')) OR (may.mayor = '05' AND aux.auxiliar = '004')) -- INCAPACIDADES
						OR (aux.auxiliar IN ('010') AND may.mayor = '01') -- LICENCIA MATERNIDAD / PATERNIDAD
						OR (aux.auxiliar IN ('011', '012', '015') AND may.mayor = '01') -- LICENCIAS REMUNERADAS
						OR (aux.auxiliar <> '051' AND may.mayor = '10') -- BONIFICACIONES
						OR ((may.mayor = '12' AND aux.auxiliar NOT IN ('021', '028', '051', '052', '067')) OR (may.mayor = '09' AND aux.auxiliar = '001')) -- AUXILIOS
						OR ((may.mayor = '07' AND aux.auxiliar = '001') OR (may.mayor = '08' AND aux.auxiliar IN ('001', '002')) OR (may.mayor = '20' AND aux.auxiliar = '001') OR (may.mayor = '99' AND aux.auxiliar IN ('001', '010'))) -- OTRO CONCEPTO
						OR (may.mayor = '06') -- COMISIONES
						OR (aux.auxiliar IN ('061', '062') AND may.mayor = '20') -- PAGOS A TERCEROS
						OR (aux.auxiliar IN ('013') AND may.mayor = '99') -- DOTACION
						OR (aux.auxiliar IN ('004') AND may.mayor = '01') -- APOYO SOSTENIMIENTO
						OR (aux.auxiliar IN ('028') AND may.mayor = '12') -- BONIFICACION RETIRO
						OR (may.mayor = '55') -- INDEMNIZACIONES
						OR ((may.mayor = '07' AND aux.auxiliar = '051') OR (may.mayor = '08' AND aux.auxiliar IN ('051', '052')) OR (may.mayor = '12' AND aux.auxiliar IN ('021', '051')) OR (may.mayor = '15' AND aux.auxiliar = '051') OR (may.mayor = '17' AND aux.auxiliar = '054') OR (may.mayor = '20' AND aux.auxiliar IN ('002', '003'))) -- REINTEGROS
					), 'X', '') AS 'DEVENGOS',
					IIF ((
						(aux.auxiliar IN ('001') AND may.mayor = '11') -- SALUD
						OR (aux.auxiliar IN ('002') AND may.mayor = '11') -- PENSION
						OR (aux.auxiliar IN ('001') AND may.mayor = '13') -- FONDO DE SOLIDARIDAD
						OR (aux.auxiliar IN ('002') AND may.mayor = '13') -- 
						OR (may.mayor = '16') -- SANCIONES
						OR ((may.mayor = '18' AND aux.auxiliar = '052') OR (may.mayor = '20' AND aux.auxiliar =  '053')) -- LIBRANZAS
						OR ((may.mayor = '99' AND aux.auxiliar IN ('002', '004', '005', '006', '007', '008', '011', '012', '014', '015', '016')) OR (may.mayor = '04' AND aux.auxiliar = '051') OR (may.mayor = '10' AND aux.auxiliar = '051') OR (may.mayor = '11' AND aux.auxiliar = '003') OR (may.mayor = '17' AND aux.auxiliar IN ('001', '002')) OR (may.mayor = '05' AND aux.auxiliar = '051') OR (may.mayor = '01' AND aux.auxiliar = '053')) -- OTRAS DEDUCCIONES
						OR (aux.auxiliar IN ('002') AND may.mayor = '21') -- PENSION VOLUNTARIA
						OR (aux.auxiliar IN ('001', '005') AND may.mayor = '15') -- RETENCION FUENTE
						OR (aux.auxiliar IN ('001') AND may.mayor = '21') -- AFC
						OR (aux.auxiliar IN ('001', '051') AND may.mayor = '18') -- COOPERATIVA 
						OR (may.mayor = '19') -- EMBARGOS FISCALES
						OR (aux.auxiliar IN ('060') AND may.mayor = '20') -- EDUCACION
						OR ((may.mayor = '02' AND aux.auxiliar IN ('051', '052', '053', '054')) OR (may.mayor = '03' AND aux.auxiliar IN ('051', '052'))) -- REINTEGRO
						OR (aux.auxiliar IN ('051', '054', '055', '056', '057', '058', '059', '063') AND may.mayor = '20') -- DEUDA
					), 'X', '') AS 'DEDUCCIONES',
					IIF ((aux.auxiliar IN ('001', '002', '003', '005', '006', '007', '051') AND may.mayor = '01'), 'X', '') AS 'PARAMETROS DEVENGADOS TYPE',
					IIF ((aux.auxiliar IN ('001') AND may.mayor = '04'), 'X', '') AS 'TRANSPORTE',
					IIF ((aux.auxiliar IN ('001', '051') AND may.mayor = '02'), 'X', '') AS 'HORAS EXTRAS DIURNAS',
					IIF ((aux.auxiliar IN ('002', '052') AND may.mayor = '02'), 'X', '') AS 'HORAS EXTRAS NOCTURNAS',
					IIF ((aux.auxiliar IN ('001', '051') AND may.mayor = '03'), 'X', '') AS 'RECARGO NOCTURNO',
					IIF ((aux.auxiliar IN ('003', '053') AND may.mayor = '02'), 'X', '') AS 'HORAS EXTRAS DIURNAS FESTIVAS',
					IIF ((aux.auxiliar IN ('005') AND may.mayor = '02'), 'X', '') AS 'HORAS RECARGO DIURNO DOMINICAL FESTIVAS',
					IIF ((aux.auxiliar IN ('004', '054') AND may.mayor = '02'), 'X', '') AS 'HORAS EXTRAS NOCTURNAS FESTIVAS',
					IIF ((aux.auxiliar IN ('002', '052') AND may.mayor = '03'), 'X', '') AS 'HORAS RECARGO NOCTURNO DOMINICAL FESTIVAS',
					IIF ((may.mayor = '50'), 'X', '') AS 'VACACIONES EN TIEMPO',
					IIF ((may.mayor = '51'), 'X', '') AS 'VACACIONES EN DINERO',
					IIF ((may.mayor = '52'), 'X', '') AS 'PRIMA LEGAL',
					IIF ((may.mayor IN ('53', '54')), 'X', '') AS 'CESANTIAS',
					IIF (((may.mayor = '01' AND aux.auxiliar IN ('008', '009', '014')) OR (may.mayor = '05' AND aux.auxiliar = '004')), 'X', '') AS 'INCAPACIDADES',
					IIF ((aux.auxiliar IN ('010') AND may.mayor = '01'), 'X', '') AS 'LICENCIA MATERNIDAD / PATERNIDAD',
					IIF ((aux.auxiliar IN ('011', '012', '015') AND may.mayor = '01'), 'X', '') AS 'LICENCIAS REMUNERADAS',
					IIF (((may.mayor = '01' AND aux.auxiliar IN ('052', '054')) OR (may.mayor = '17' AND aux.auxiliar = '051')), 'X', '') AS 'LICENCIAS NO REMUNERADAS',
					IIF ((aux.auxiliar <> '051' AND may.mayor = '10'), 'X', '') AS 'BONIFICACIONES',
					IIF (((may.mayor = '12' AND aux.auxiliar NOT IN ('021', '028', '051', '052', '067')) OR (may.mayor = '09' AND aux.auxiliar = '001')), 'X', '') AS 'AUXILIOS',
					IIF (((may.mayor = '07' AND aux.auxiliar = '001') OR (may.mayor = '08' AND aux.auxiliar IN ('001', '002')) OR (may.mayor = '20' AND aux.auxiliar = '001') OR (may.mayor = '99' AND aux.auxiliar IN ('001', '010'))), 'X', '') AS 'OTRO CONCEPTO',
					IIF ((may.mayor = '06'), 'X', '') AS 'COMISIONES',
					IIF ((aux.auxiliar IN ('061', '062') AND may.mayor = '20'), 'X', '') AS 'PAGOS A TERCEROS',
					IIF ((aux.auxiliar IN ('013') AND may.mayor = '99'), 'X', '') AS 'DOTACION',
					IIF ((aux.auxiliar IN ('004') AND may.mayor = '01'), 'X', '') AS 'APOYO SOSTENIMIENTO',
					IIF ((aux.auxiliar IN ('028') AND may.mayor = '12'), 'X', '') AS 'BONIFICACION RETIRO',
					IIF ((may.mayor = '55'), 'X', '') AS 'INDEMNIZACIONES',
					IIF (((may.mayor = '07' AND aux.auxiliar = '051') OR (may.mayor = '08' AND aux.auxiliar IN ('051', '052')) OR (may.mayor = '12' AND aux.auxiliar IN ('021', '051')) OR (may.mayor = '15' AND aux.auxiliar = '051') OR (may.mayor = '17' AND aux.auxiliar = '054') OR (may.mayor = '20' AND aux.auxiliar IN ('002', '003'))), 'X', '') AS 'REINTEGROS',
					IIF ((aux.auxiliar IN ('001') AND may.mayor = '11'), 'X', '') AS 'SALUD',
					IIF ((aux.auxiliar IN ('002') AND may.mayor = '11'), 'X', '') AS 'PENSION',
					IIF ((aux.auxiliar IN ('001') AND may.mayor = '13'), 'X', '') AS 'FONDO DE SOLIDARIDAD',
					IIF ((aux.auxiliar IN ('002') AND may.mayor = '13'), 'X', '') AS '--',
					IIF ((may.mayor = '16'), 'X', '') AS 'SANCIONES',
					IIF (((may.mayor = '18' AND aux.auxiliar = '052') OR (may.mayor = '20' AND aux.auxiliar =  '053')), 'X', '') AS 'LIBRANZAS',
					IIF (((may.mayor = '99' AND aux.auxiliar IN ('002', '004', '005', '006', '007', '008', '011', '012', '014', '015', '016')) OR (may.mayor = '04' AND aux.auxiliar = '051') OR (may.mayor = '10' AND aux.auxiliar = '051') OR (may.mayor = '11' AND aux.auxiliar = '003') OR (may.mayor = '17' AND aux.auxiliar IN ('001', '002')) OR (may.mayor = '05' AND aux.auxiliar = '051') OR (may.mayor = '01' AND aux.auxiliar = '053')), 'X', '') AS 'OTRAS DEDUCCIONES',
					IIF ((aux.auxiliar IN ('002') AND may.mayor = '21'), 'X', '') AS 'PENSION VOLUNTARIA',
					IIF ((aux.auxiliar IN ('001', '005') AND may.mayor = '15'), 'X', '') AS 'RETENCION FUENTE',
					IIF ((aux.auxiliar IN ('001') AND may.mayor = '21'), 'X', '') AS 'AFC',
					IIF ((aux.auxiliar IN ('001', '051') AND may.mayor = '18'), 'X', '') AS 'COOPERATIVA ',
					IIF ((may.mayor = '19'), 'X', '') AS 'EMBARGOS FISCALES',
					IIF ((aux.auxiliar IN ('060') AND may.mayor = '20'), 'X', '') AS 'EDUCACION',
					IIF (((may.mayor = '02' AND aux.auxiliar IN ('051', '052', '053', '054')) OR (may.mayor = '03' AND aux.auxiliar IN ('051', '052'))), 'X', '') AS 'REINTEGRO',
					IIF ((aux.auxiliar IN ('051', '054', '055', '056', '057', '058', '059', '063') AND may.mayor = '20'), 'X', '') AS 'DEUDA'
				FROM
					auxiliares aux
					LEFT JOIN mayores may ON may.id = aux.idmayor;
			EOD;
			$request = $this->listar($query);
			return $request;
		}

		public function guardarLogNE(array $data){
			$query = <<<EOD
				INSERT INTO nomina.log_ne (
					idperiodo, idempleado, consecutivo, archivoPath, archivoMsterPath, idTrack,
					tipoDocumento, numeroDocumento, nit, periodoNomina, error, estado,
					fechaactualizacion )
					VALUES (
						:idperiodo,
						:idempleado,
						:consecutivo,
						:archivoPath,
						:archivoMsterPath,
						:idTrack,
						:tipoDocumento,
						:numeroDocumento,
						:nit,
						:periodoNomina,
						:error,
						:estado,
						getdate()); 
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}
	}
?>	

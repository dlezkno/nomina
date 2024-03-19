<?php
	class prestamosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
				FROM PRESTAMOS 
					INNER JOIN EMPLEADOS
						ON PRESTAMOS.IdEmpleado = EMPLEADOS.Id 
					INNER JOIN AUXILIARES
						ON PRESTAMOS.IdConcepto = AUXILIARES.Id 
				$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarPrestamos($query)
		{
			$query = <<<EOD
				SELECT PRESTAMOS.*, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						AUXILIARES.Nombre AS NombreConcepto, 
						PARAMETROS1.Detalle AS NombreTipoPrestamo,
						PARAMETROS2.Detalle AS EstadoPrestamo, 
						PARAMETROS3.Detalle AS EstadoEmpleado, 
						BANCOS.Nombre AS NombreBanco, 
						TERCEROS.Nombre AS NombreTercero  
					FROM PRESTAMOS 
						INNER JOIN EMPLEADOS
							ON PRESTAMOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN AUXILIARES
							ON PRESTAMOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1
							ON PRESTAMOS.TipoPrestamo = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2
							ON PRESTAMOS.Estado = PARAMETROS2.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3
							ON EMPLEADOS.Estado = PARAMETROS3.Id 
						LEFT JOIN BANCOS 
							ON PRESTAMOS.IdBanco = BANCOS.Id 
						LEFT JOIN TERCEROS 
							ON PRESTAMOS.IdTercero = TERCEROS.Id
					$query
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarPrestamo(array $data)
		{
			$query = <<<EOD
				INSERT INTO PRESTAMOS (
					IdEmpleado, IdConcepto, TipoPrestamo,  
					Fecha, ValorPrestamo, ValorCuota, Cuotas, SaldoPrestamo, SaldoCuotas, IdTercero, Estado) 
					VALUES (
					:IdEmpleado, 
					:IdConcepto,
					:TipoPrestamo, 
					:Fecha, 
					:ValorPrestamo, 
					:ValorCuota, 
					:Cuotas, 
					:SaldoPrestamo,
					:SaldoCuotas,
					:IdTercero, 
					:Estado);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarPrestamo($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarPrestamo(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE PRESTAMOS
					SET 
						IdEmpleado = :IdEmpleado,
						IdConcepto = :IdConcepto,
						TipoPrestamo = :TipoPrestamo, 
						Fecha = :Fecha, 
						ValorPrestamo = :ValorPrestamo, 
						ValorCuota = :ValorCuota, 
						Cuotas = :Cuotas, 
						SaldoPrestamo = :SaldoPrestamo,
						SaldoCuotas = :SaldoCuotas, 
						IdTercero = :IdTercero, 
						Estado = :Estado, 
						FechaActualizacion = getDate()
					WHERE PRESTAMOS.Id = $id
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarPrestamo(int $id)
		{
			$query = <<<EOD
				DELETE FROM PRESTAMOS 
					WHERE PRESTAMOS.Id = $id
			EOD;

			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>
<?php
	class liquidacionContratoModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	

		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM EMPLEADOS 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
				$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarEmpleadosRetirados($query)
		{
            $query = <<<EOD
				SELECT EMPLEADOS.Id, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.FechaIngreso, 
						EMPLEADOS.FechaRetiro, 
						EMPLEADOS.SueldoBasico, 
						EMPLEADOS.IdBanco, 
						EMPLEADOS.CuentaBancaria, 
						EMPLEADOS.TipoCuentaBancaria 
					FROM EMPLEADOS 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function contarReliquidados($query, $ArchivoNomina)
		{
			$query = <<<EOD
				SELECT DISTINCT $ArchivoNomina.IdEmpleado 
					FROM $ArchivoNomina 
						INNER JOIN EMPLEADOS 
							ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
					$query;
			EOD; 

			$request = $this->listar($query);
			return count($request);
		}

		public function listarEmpleadosReliquidados($query, $ArchivoNomina)
		{
            $query = <<<EOD
				SELECT DISTINCT EMPLEADOS.Id, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.FechaIngreso, 
						EMPLEADOS.FechaRetiro, 
						EMPLEADOS.SueldoBasico, 
						EMPLEADOS.IdBanco, 
						EMPLEADOS.CuentaBancaria, 
						EMPLEADOS.TipoCuentaBancaria 
					FROM $ArchivoNomina 
						INNER JOIN EMPLEADOS 
							ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function leerRegistro($query)
		{
			$request = $this->leer($query);
			return $request;
		}	

		public function listarRegistros($query)
		{
			$request = $this->listar($query);
			return $request;
		}	

		public function actualizarRegistros($query)
		{
			$request = $this->query($query);
			return $request;
		}	

		public function guardarNovedad($ArchivoNomina, $datos)
		{
			$query = <<<EOD
				INSERT INTO $ArchivoNomina 
					(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Base, Horas, Valor, Saldo, Liquida, Afecta, IdCentro, TipoEmpleado, IdTercero) 
					VALUES ( 
						:IdPeriodo, 
						:Ciclo, 
						:IdEmpleado, 
						:IdConcepto, 
						:Base, 
						:Horas, 
						:Valor, 
						:Saldo, 
						:Liquida, 
						:Afecta, 
						:IdCentro, 
						:TipoEmpleado, 
						:IdTercero);
			EOD;

			$id = $this->adicionar($query, $datos);
			return $id;
		}

		public function guardarNovedades($ArchivoNomina, $datos)
		{
			$IdPeriodo = $datos['IdPeriodo'];
			$Ciclo = $datos['Ciclo'];
			$IdEmpleado = $datos['IdEmpleado'];
			$IdCentro = $datos['IdCentro'];
			$TipoEmpleado = $datos['TipoEmpleado'];

			for ($i = 0; $i < count($datos['Conceptos']); $i++)
			{
				$IdConcepto = $datos['Conceptos'][$i]['IdConcepto'];
				$Base = $datos['Conceptos'][$i]['Base'];
				$Horas = $datos['Conceptos'][$i]['Horas'];
				$Valor = $datos['Conceptos'][$i]['Valor'];
				$IdTercero = $datos['Conceptos'][$i]['IdTercero'];
				$TipoRetencion = $datos['Conceptos'][$i]['TipoRetencion'];

				$query = <<<EOD
					INSERT INTO $ArchivoNomina 
						(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Base, Horas, Valor, Liquida, Afecta, IdCentro, TipoEmpleado, IdTercero) 
						VALUES ( 
							$IdPeriodo, 
							$Ciclo, 
							$IdEmpleado, 
							$IdConcepto, 
							$Base, 
							$Horas, 
							$Valor, 
							'N', 
							$TipoRetencion, 
							$IdCentro, 
							$TipoEmpleado, 
							$IdTercero);
				EOD;

				$id = $this->query($query);
			}
		}

		public function dispersionNomina($query)
		{
			$query = <<<EOD
				SELECT PARAMETROS1.Detalle AS TipoIdentificacion, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.Direccion, 
						EMPLEADOS.CuentaBancaria, 
						PARAMETROS2.Detalle AS TipoCuentaBancaria, 
						BANCOS.Banco, 
						SUM(IIF(PARAMETROS3.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS ValorPago 
					FROM ACUMULADOS 
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.TipoIdentificacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EMPLEADOS.TipoCuentaBancaria = PARAMETROS2.Id 
						INNER JOIN BANCOS 
							ON EMPLEADOS.IdBanco = BANCOS.Id 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3 
							ON AUXILIARES.Imputacion = PARAMETROS3.Id
					$query 
					GROUP BY PARAMETROS1.Detalle, EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, EMPLEADOS.Direccion, EMPLEADOS.CuentaBancaria, PARAMETROS2.Detalle, BANCOS.Banco;
			EOD;

			$request = $this->listar($query);

			return $request;
		}

	}
?>
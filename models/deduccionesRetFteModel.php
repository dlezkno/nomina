<?php
	class deduccionesRetFteModel extends pgSQL
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
                        INNER JOIN CARGOS 
                            ON EMPLEADOS.IdCargo = CARGOS.Id 
                        INNER JOIN PARAMETROS AS PARAMETROS1 
                            ON EMPLEADOS.Estado = PARAMETROS1.Id 
                    $query;
            EOD;

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarDeduccionesRetFte($query)
		{
            $query = <<<EOD
                SELECT EMPLEADOS.Id, 
                        EMPLEADOS.Documento, 
                        EMPLEADOS.Apellido1, 
                        EMPLEADOS.Apellido2, 
                        EMPLEADOS.Nombre1, 
                        EMPLEADOS.Nombre2, 
                        EMPLEADOS.CuotaVivienda, 
                        EMPLEADOS.SaludYEducacion, 
                        EMPLEADOS.Alimentacion, 
                        EMPLEADOS.DeduccionDependientes, 
                        EMPLEADOS.FechaInicialDeducciones, 
                        EMPLEADOS.FechaFinalDeducciones, 
                        CARGOS.Nombre AS NombreCargo, 
                        PARAMETROS1.Detalle AS EstadoEmpleado, 
                        PARAMETROS2.Detalle AS TipoContrato 
                    FROM EMPLEADOS 
                        INNER JOIN CARGOS
                            ON EMPLEADOS.IdCargo = CARGOS.Id 
                        INNER JOIN PARAMETROS AS PARAMETROS1 
                            ON EMPLEADOS.Estado = PARAMETROS1.Id 
                        INNER JOIN PARAMETROS AS PARAMETROS2  
                            ON EMPLEADOS.TipoContrato = PARAMETROS2.Id 
                    $query;
            EOD;

			$request = $this->listar($query);
			return $request;
		}

        public function adicionarDeduccionesRetFte($data)
        {
            $query = <<<EOD
                UPDATE EMPLEADOS
                    SET 
                        CuotaVivienda           = :CuotaVivienda, 
                        SaludYEducacion         = :SaludYEducacion, 
                        Alimentacion            = :Alimentacion, 
                        DeduccionDependientes   = :DeduccionDependientes, 
                        FechaInicialDeducciones = :FechaInicialDeducciones, 
                        FechaFinalDeducciones   = :FechaFinalDeducciones 
                    WHERE EMPLEADOS.Id = :IdEmpleado;
            EOD;

            $ok = $this->actualizar($query, $data);

            return $ok;
        }
		
        public function actualizarDeduccionesRetFte($data)
        {
            $query = <<<EOD
                UPDATE EMPLEADOS
                    SET 
                        CuotaVivienda           = :CuotaVivienda, 
                        SaludYEducacion         = :SaludYEducacion, 
                        Alimentacion            = :Alimentacion, 
                        DeduccionDependientes   = :DeduccionDependientes, 
                        FechaInicialDeducciones = :FechaInicialDeducciones, 
                        FechaFinalDeducciones   = :FechaFinalDeducciones  
                    WHERE EMPLEADOS.Id = :IdEmpleado;
            EOD;

            $ok = $this->actualizar($query, $data);

            return $ok;
        }

        public function borrarDeduccionesRetFte($Id)
        {
            $query = <<<EOD
                UPDATE EMPLEADOS
                    SET 
                        CuotaVivienda           = 0, 
                        SaludYEducacion         = 0, 
                        Alimentacion            = 0,
                        DeduccionDependientes   = 0, 
                        FechaInicialDeducciones = NULL, 
                        FechaFinalDeducciones   = NULL
                    WHERE EMPLEADOS.Id = $Id;
            EOD;

            $ok = $this->query($query);

            return $ok;
        }
	}
?>
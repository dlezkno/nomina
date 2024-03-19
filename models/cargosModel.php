<?php
	class cargosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = "SELECT COUNT(*) AS Registros FROM CARGOS $query";
			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarCargos($query)
		{
			$query = <<<EOD
				SELECT CARGOS.Id, 
						CARGOS.Nombre, 
						CARGOS.SueldoMinimo, 
						CARGOS.SueldoMaximo, 
						CARGOS.PorcentajeARL,  
						CARGOS.IdCargoSuperior, 
						CARGOS.IdCargoBase, 
						CARGOS_SUP.Nombre AS NombreCargoSuperior, 
						CARGOS_BASE.Nombre AS NombreCargoBase, 
						PERFILES.* 
					FROM CARGOS 
						LEFT JOIN CARGOS AS CARGOS_SUP 
							ON CARGOS.IdCargoSuperior = CARGOS_SUP.Id 
						LEFT JOIN CARGOS AS CARGOS_BASE  
							ON CARGOS.IdCargoBase = CARGOS_BASE.Id 
						LEFT JOIN PERFILES 
							ON CARGOS.IdPerfil = PERFILES.Id 
					$query;
			EOD;

			$request = $this->listar($query);

			return $request;
		}
		
		public function guardarCargo(array $data)
		{
			$query = <<<EOD
				INSERT INTO CARGOS 
					(Nombre, SueldoMinimo, SueldoMaximo, IdCargoSuperior, IdCargoBase, PorcentajeARL) 
					VALUES (
					:Nombre,
					:SueldoMinimo,
					:SueldoMaximo,
					:IdCargoSuperior, 
					:IdCargoBase, 
					:PorcentajeARL);
			EOD;

			$id = $this->adicionar($query, $data);
			
			return $id;
		}	
		
		public function buscarCargo($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarCargo(array $data, int $Id)
		{
			$query = <<<EOD
				UPDATE CARGOS 
					SET 
						Nombre = :Nombre, 
						SueldoMinimo = :SueldoMinimo, 
						SueldoMaximo = :SueldoMaximo, 
						IdCargoSuperior = :IdCargoSuperior, 
						IdCargoBase = :IdCargoBase, 
						PorcentajeARL = :PorcentajeARL, 
						FechaActualizacion = getDate() 
					WHERE CARGOS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function actualizarPerfil(array $data, int $Id)
		{
			if ($Id > 0)
			{
				$query = <<<EOD
					SELECT PERFILES.Id 
						FROM PERFILES 
						WHERE PERFILES.Id = $Id;
				EOD;

				$resp = $this->leer($query);

				if ($resp)
				{
					$query = <<<EOD
						UPDATE PERFILES 
							SET 
								IdCargoBase = :IdCargoBase, 
								IdDependencia = :IdDependencia, 
								NivelAcademico = :NivelAcademico, 
								Estudios = :Estudios, 
								ExperienciaLaboral = :ExperienciaLaboral, 
								FormacionAdicional = :FormacionAdicional, 
								Competencias = :Competencias, 
								CondicionesTrabajo = :CondicionesTrabajo, 
								FuncionesSGC = :FuncionesSGC, 
								MisionCargo = :MisionCargo, 
								Funciones = :Funciones, 
								FuncionesHSEQ = :FuncionesHSEQ, 
								GestionHS = :GestionHS, 
								GestionAmbiental = :GestionAmbiental, 
								GestionCalidad = :GestionCalidad, 
								GestionSI = :GestionSI, 
								Responsable = :Responsable, 
								Elabora = :Elabora
							WHERE PERFILES.Id = $Id;
					EOD;

					$resp = $this->actualizar($query, $data);
				}
				else
				{
					$query = <<<EOD
						INSERT INTO PERFILES 
							(IdCargoBase, IdDependencia, NivelAcademico, Estudios, ExperienciaLaboral, FormacionAdicional, CondicionesTrabajo, MisionCargo, Funciones, Responsable, Elabora) 
							VALUES (
								$Id, 
								:IdDependencia, 
								:NivelAcademico, 
								:Estudios, 
								:ExperienciaLaboral, 
								:FormacionAdicional, 
								:CondicionesTrabajo, 
								:MisionCargo, 
								:Funciones, 
								:Responsable, 
								:Elabora);
					EOD;

					$resp = $this->adicionar($query, $data);
				}
			}
			else
			{
				$query = <<<EOD
					INSERT INTO PERFILES 
						(IdCargoBase, IdDependencia, NivelAcademico, Estudios, ExperienciaLaboral, FormacionAdicional, MisionCargo, Funciones, Respponsable, Elabora) 
						VALUES (
							$Id, 
							:IdDependencia, 
							:NivelAcademico, 
							:Estudios, 
							:ExperienciaLaboral, 
							:FormacionAdicional, 
							:MisionCargo, 
							:Funciones, 
							:Responsable, 
							:Elabora);
				EOD;

				$resp = $this->adicionar($query, $data);
			}

			return $resp;
		}

		public function borrarCargo(int $Id)
		{
			$query = <<<EOD
				DELETE FROM PERFILES 
					WHERE PERFILES.IdCargoBase = $Id;
			EOD;

			$resp = $this->borrar($query);

			$query = <<<EOD
				DELETE FROM CARGOS 
					WHERE CARGOS.Id = $Id;
			EOD;

			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>
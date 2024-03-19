<?php
	class retiradosModel extends pgSQL
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
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						LEFT JOIN CENTROS
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query;
			EOD;

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarEmpleados($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.*, 
						CARGOS.Nombre AS NombreCargo, 
						CENTROS.Nombre AS NombreCentro, 
						PROYECTOS.Nombre AS NombreProyecto, 
						PARAMETROS1.Detalle AS EstadoEmpleado,    
						PARAMETROS2.Detalle AS TipoContrato 
					FROM EMPLEADOS 
						LEFT JOIN CARGOS
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						LEFT JOIN CENTROS
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CENTROS AS PROYECTOS
							ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EMPLEADOS.TipoContrato = PARAMETROS2.Id 
					$query
			EOD;

			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarEmpleado(array $data)
		{
			$query = <<<EOD
				INSERT INTO EMPLEADOS 
					(TipoIdentificacion, Documento, IdCiudadExpedicion, FechaExpedicion, Apellido1, Apellido2, Nombre1, Nombre2, Estado, Direccion, Barrio, Localidad, IdCiudad, Telefono, Celular, Email, EmailCorporativo, EmailProyecto, FechaNacimiento, IdCiudadNacimiento, IdCargo, IdCentro, IdSede, IdCategoria, PerfilProfesional, IdCiudadTrabajo, FechaIngreso, TipoContrato, FechaPeriodoPrueba, FechaVencimiento, Prorrogas, ModalidadTrabajo, SueldoBasico, SubsidioTransporte, PeriodicidadPago, HorasMes, DiasAno, IdEPS, RegimenCesantias, IdFondoCesantias, FactorPrestacional, IdFondoPensiones, IdCajaCompensacion, IdARL, NivelRiesgo, Genero, EstadoCivil, LibretaMilitar, DistritoMilitar, LicenciaConduccion, TarjetaProfesional, FactorRH, Profesion, Educacion, FormaDePago, IdBanco, CuentaBancaria, CuentaBancaria2, TipoCuentaBancaria, MetodoRetencion, PorcentajeRetencion, MayorRetencionFuente, CuotaVivienda, SaludYEducacion, DeduccionDependientes, PoliticamenteExpuesta, AceptaPoliticaTD, Observaciones, GrupoPoblacional, Vicepresidencia, CodigoSAP, IdProyecto) 
					VALUES (
						:TipoIdentificacion, 
						:Documento, 
						:IdCiudadExpedicion, 
						:FechaExpedicion, 
						:Apellido1, 
						:Apellido2, 
						:Nombre1, 
						:Nombre2, 
						:Estado, 
						:Direccion, 
						:Barrio, 
						:Localidad, 
						:IdCiudad, 
						:Telefono, 
						:Celular, 
						:Email, 
						:EmailCorporativo, 
						:EmailProyecto, 
						:FechaNacimiento, 
						:IdCiudadNacimiento, 
						:IdCargo, 
						:IdCentro, 
						:IdSede, 
						:IdCategoria, 
						:PerfilProfesional, 
						:IdCiudadTrabajo, 
						:FechaIngreso, 
						:TipoContrato, 
						:FechaPeriodoPrueba,  
						:FechaVencimiento, 
						:Prorrogas,
						:ModalidadTrabajo, 
						:SueldoBasico,
						:duracionContrato,
						:SubsidioTransporte, 
						:PeriodicidadPago, 
						:HorasMes, 
						:DiasAno, 
						:IdEPS, 
						:RegimenCesantias, 
						:IdFondoCesantias, 
						:FactorPrestacional, 
						:IdFondoPensiones, 
						:IdCajaCompensacion, 
						:IdARL, 
						:NivelRiesgo, 
						:Genero, 
						:EstadoCivil, 
						:LibretaMilitar, 
						:DistritoMilitar, 
						:LicenciaConduccion, 
						:TarjetaProfesional, 
						:FactorRH, 
						:Profesion, 
						:Educacion, 
						:FormaDePago, 
						:IdBanco, 
						:CuentaBancaria, 
						:CuentaBancaria2, 
						:TipoCuentaBancaria, 
						:MetodoRetencion, 
						:PorcentajeRetencion, 
						:MayorRetencionFuente, 
						:CuotaVivienda, 
						:SaludYEducacion, 
						:DeduccionDependientes, 
						:PoliticamenteExpuesta, 
						1, 
						:Observaciones, 
						:GrupoPoblacional, 
						:Vicepresidencia, 
						:CodigoSAP, 
						:IdProyecto);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function guardarLogEmpleado($data)
		{
			$query = <<<EOD
				INSERT INTO LOGEMPLEADOS 
					(IdEmpleado, Campo, ValorAnterior, ValorActual, IdUsuario)
					VALUES (
						:IdEmpleado, 
						:Campo, 
						:ValorAnterior, 
						:ValorActual,
						:IdUsuario);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarEmpleado($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarEmpleado(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE EMPLEADOS 
					SET 
						TipoIdentificacion 	= :TipoIdentificacion, 
						Documento 			= :Documento, 
						IdCiudadExpedicion 	= :IdCiudadExpedicion, 
						FechaExpedicion 	= :FechaExpedicion, 
						Apellido1 			= :Apellido1, 
						Apellido2 			= :Apellido2, 
						Nombre1 			= :Nombre1, 
						Nombre2 			= :Nombre2, 
						Estado 				= :Estado, 
						Direccion 			= :Direccion, 
						Barrio 				= :Barrio, 
						Localidad 			= :Localidad, 
						IdCiudad 			= :IdCiudad, 
						Telefono 			= :Telefono, 
						Celular 			= :Celular, 
						Email 				= :Email, 
						EmailCorporativo 	= :EmailCorporativo, 
						EmailProyecto 		= :EmailProyecto, 
						FechaNacimiento 	= :FechaNacimiento, 
						IdCiudadNacimiento 	= :IdCiudadNacimiento, 
						IdCargo 			= :IdCargo, 
						IdCentro 			= :IdCentro, 
						IdSede 				= :IdSede, 
						IdCategoria 		= :IdCategoria, 
						PerfilProfesional 	= :PerfilProfesional, 
						IdCiudadTrabajo 	= :IdCiudadTrabajo, 
						FechaIngreso 		= :FechaIngreso, 
						TipoContrato 		= :TipoContrato, 
						FechaPeriodoPrueba 	= :FechaPeriodoPrueba,  
						FechaVencimiento 	= :FechaVencimiento, 
						Prorrogas 			= :Prorrogas,
						ModalidadTrabajo 	= :ModalidadTrabajo, 
						SueldoBasico 		= :SueldoBasico, 						
						duracionContrato	= :duracionContrato,
						SubsidioTransporte 	= :SubsidioTransporte, 
						PeriodicidadPago 	= :PeriodicidadPago, 
						HorasMes 			= :HorasMes, 
						DiasAno 			= :DiasAno, 
						IdEPS 				= :IdEPS, 
						RegimenCesantias 	= :RegimenCesantias, 
						IdFondoCesantias 	= :IdFondoCesantias, 
						FactorPrestacional 	= :FactorPrestacional, 
						IdFondoPensiones 	= :IdFondoPensiones, 
						IdCajaCompensacion 	= :IdCajaCompensacion, 
						IdARL 				= :IdARL, 
						NivelRiesgo 		= :NivelRiesgo, 
						Genero 				= :Genero, 
						EstadoCivil 		= :EstadoCivil, 
						LibretaMilitar 		= :LibretaMilitar, 
						DistritoMilitar 	= :DistritoMilitar, 
						LicenciaConduccion 	= :LicenciaConduccion, 
						TarjetaProfesional 	= :TarjetaProfesional, 
						FactorRH 			= :FactorRH, 
						Profesion 			= :Profesion, 
						Educacion 			= :Educacion, 
						FormaDePago 		= :FormaDePago, 
						IdBanco 			= :IdBanco, 
						CuentaBancaria 		= :CuentaBancaria, 
						CuentaBancaria2 	= :CuentaBancaria2, 
						TipoCuentaBancaria 	= :TipoCuentaBancaria, 
						MetodoRetencion 	= :MetodoRetencion, 
						PorcentajeRetencion = :PorcentajeRetencion, 
						MayorRetencionFuente = :MayorRetencionFuente, 
						CuotaVivienda 		= :CuotaVivienda, 
						SaludYEducacion 	= :SaludYEducacion, 
						DeduccionDependientes = :DeduccionDependientes, 
						PoliticamenteExpuesta = :PoliticamenteExpuesta, 
						Observaciones 		= :Observaciones, 
						GrupoPoblacional	= :GrupoPoblacional, 
						Vicepresidencia 	= :Vicepresidencia, 
						CodigoSAP 			= :CodigoSAP, 
						IdProyecto 			= :IdProyecto, 
						FechaActualizacion 	= getDate() 
						WHERE EMPLEADOS.Id 	= $id;
			EOD;
			
			$resp = $this->actualizar($query, $data);
			
			return $resp;
		}
					
		public function actualizarCandidato(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE EMPLEADOS 
					SET 
						TipoIdentificacion 		= :TipoIdentificacion, 
						Documento 				= :Documento, 
						IdCiudadExpedicion 		= :IdCiudadExpedicion, 
						Apellido1 				= :Apellido1, 
						Apellido2 				= :Apellido2, 
						Nombre1 				= :Nombre1, 
						Nombre2 				= :Nombre2, 
						IdCargo 				= :IdCargo, 
						Email 					= :Email, 
						Celular 				= :Celular, 
						SueldoBasico 			= :SueldoBasico, 
						SubsidioTransporte 		= :SubsidioTransporte, 
						IdCiudadTrabajo 		= :IdCiudadTrabajo, 
						TipoContrato 			= :TipoContrato, 
						InstitutoFormacion		= :InstitutoFormacion, 
						EspecialidadAprendiz 	= :EspecialidadAprendiz,
						salarioPractica 		= :salarioPractica, 
						FechaIngreso 			= :FechaIngreso, 
						FechaPeriodoPrueba 		= :FechaPeriodoPrueba, 
						DuracionContrato		= :DuracionContrato, 
						FechaVencimiento 		= :FechaVencimiento, 
						ModalidadTrabajo 		= :ModalidadTrabajo, 
						PeriodicidadPago 		= :PeriodicidadPago, 
						MetodoRetencion 		= :MetodoRetencion, 
						Estado 					= :Estado,  
						FechaActualizacion 		= getDate() 
					WHERE EMPLEADOS.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function actualizarNovedadEmpleado(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE EMPLEADOS 
					SET 
						TipoContrato = :TipoContrato, 
						IdCargo = :IdCargo, 
						IdCentro = :IdCentro, 
						IdProyecto = :IdProyecto, 
						IdFondoPensiones = :IdFondoPensiones, 
						IdEPS = :IdEPS, 
						IdFondoCesantias = :IdFondoCesantias, 
						IdCajaCompensacion = :IdCajaCompensacion, 
						NivelRiesgo = :NivelRiesgo, 
						TipoCuentaBancaria = :TipoCuentaBancaria, 
						CuentaBancaria = :CuentaBancaria, 
						IdBanco = :IdBanco, 
						FechaActualizacion = getDate() 
					WHERE EMPLEADOS.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}
	}
?>
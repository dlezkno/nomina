USE master;

CREATE DATABASE NOMINA;

USE NOMINA;

CREATE TABLE ACUMULADOS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdPeriodo 					integer 		default 0,
	FechaInicialPeriodo 		date 			NULL,
	FechaFinalPeriodo 			date 			NULL,
	Ciclo 						integer 		default 0,
	IdEmpleado 					integer 		default 0,
	IdConcepto 					integer 		default 0,
	BaseCesantias				numeric(12,2)	default 0,
	Horas 						numeric(8, 4) 	default 0,
	Valor 						numeric(12, 2) 	default 0,
	Saldo 						numeric(12, 2) 	default 0,
	FechaInicial 				date 			NULL,
	FechaFinal 					date 			NULL,
	Liquida 					varchar(1) 		default '',
	Afecta 						integer 		default 0,
	ClaseCr 					varchar(1) 		default '',
	IdCentro 					integer 		default 0,
	TipoEmpleado 				integer 		default 0,
	IdCredito 					integer 		default 0,
	Fecha 						date 			NULL,
	IdBaremo 					integer 		default 0,
	Cantidad 					integer 		default 0,
	TipoRegistro 				integer 		default 0,
	ValorOriginal 				numeric(12, 2) 	default 0,
	IdTercero 					integer 		default 0, 
	PagoDispersado				bit				default 0, 
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Acumulados_IdEmpleado ON ACUMULADOS
(IdEmpleado ASC);

CREATE TABLE AUMENTOSSALARIALES (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	FechaAumento 				date 			NULL,
	SueldoBasicoAnterior 		numeric(12, 2) 	default 0,
	SubsidioTransporteAnterior	integer		 	default 0,
	SueldoBasico 				numeric(12, 2) 	default 0,
	SubsidioTransporte			integer		 	default 0,
	Procesado 					bit 			default 0, 
	IdPeriodo					integer			default 0, 
	Ciclo						integer			default 0, 
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX AumentosSalariales_IdEmpleado ON AUMENTOSSALARIALES
(IdEmpleado ASC, FechaAumento ASC);

CREATE TABLE AUXILIARES (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdMayor 					integer 		default 0,
	Auxiliar 					varchar(3) 		default '',
	TipoEmpleado 				integer 		default 0,
	Nombre 						varchar(60) 	default '',
	Imputacion 					integer 		default 0,
	ModoLiquidacion 			integer 		default 0,
	FactorConversion 			numeric(8, 4) 	default 0,
	HoraFija 					numeric(8, 2) 	default 0,
	ValorFijo 					numeric(12, 2) 	default 0,
	TipoAuxiliar 				integer 		default 0,
	TipoRegistroAuxiliar 		integer 		default 0,
	EsDispersable				bit				default 1,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Auxiliares_Concepto ON AUXILIARES
(IdMayor ASC, Auxiliar ASC);

CREATE TABLE BANCOS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Banco 						varchar(5) 		default '',
	Nombre 						varchar(60) 	default '',
	Nit 						varchar(15) 	default '',
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Bancos_Banco ON BANCOS
(Banco ASC);

CREATE TABLE CARGOS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Cargo 						varchar(10) 	default '',
	Nombre 						varchar(100) 	default '',
	SueldoMinimo 				numeric(12, 2) 	default 0,
	SueldoMaximo 				numeric(12, 2) 	default 0,
	IdCargoSuperior 			integer 		default 0, 
	IdCargoBase					integer			default 0,
	IdPerfil					integer			default 0,
	PorcentajeARL				numeric(8, 4)	default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Cargos_Cargo ON CARGOS
(Cargo ASC);

CREATE TABLE CATEGORIAS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Categoria 					varchar(5) 		default '',
	Nombre 						varchar(40) 	default '', 
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Categorias_Categoria ON CATEGORIAS
(Categoria ASC);

CREATE TABLE CENTROS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Centro 						varchar(10) 	default '',
	Nombre 						varchar(60) 	default '', 
	TipoEmpleado				integer			default 0, 
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Centros_Centro ON CENTROS
(Centro ASC);

CREATE TABLE CIUDADES (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Ciudad 						varchar(5) 		default '',
	Nombre 						varchar(25) 	default '',
	Departamento 				varchar(25) 	default '',
	IdPais 						integer 		default 0,
	Orden 						integer 		default 1,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Ciudades_Ciudad ON CIUDADES
(Orden DESC, Ciudad ASC);

CREATE TABLE COMPROBANTES (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdTipoDoc 					integer 		default 0,
	IdConcepto 					integer 		default 0, 
	Detalle						varchar(60)		default '', 
	TipoEmpleado 				integer 		default 0,
	Imputacion 					integer 		default 0,
	Porcentaje 					numeric(8, 4) 	default 0,
	CuentaDb					varchar(20) 	default '',
	DetallaCentroDb				bit 			default 0,
	CuentaCr					varchar(20) 	default '',
	DetallaCentroCr				bit 			default 0,
	TipoTercero 				integer 		default 0, 
	Exonerable					bit				default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Comprobantes_IdTipoDoc ON COMPROBANTES
(IdTipoDoc ASC, IdConcepto ASC, TipoEmpleado ASC);


CREATE TABLE CONTACTOSEMPLEADO (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	Nombre 						varchar(100) 	default '',
	Telefono 					varchar(15) 	default '',
	Parentesco 					integer default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX ContactosEmpleado_IdEmpleado ON CONTACTOSEMPLEADO
(IdEmpleado ASC);

CREATE TABLE CUENTASBANCARIAS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdBanco 					integer 		default 0,
	Cuenta 						varchar(20) 	default '',
	Nombre 						varchar(40) 	default '',
	Direccion 					varchar(60) 	default '',
	IdCiudad 					integer 		default 0,
	Telefono 					varchar(25) 	default '',
	Email 						varchar(100) 	default '',
	IdCuenta 					integer 		default 0,
	IdCentro 					integer 		default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX CuentasBancarias_IdBanco ON CUENTASBANCARIAS
(IdBanco ASC, Cuenta ASC);

CREATE TABLE DIASFESTIVOS(
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Fecha 						date	 		NULL,
	Nombre 						varchar(40) 	default '',
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX DiasFestivos_Fecha ON DIASFESTIVOS
(Fecha ASC);

CREATE TABLE EDUCACIONEMPLEADO(
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	TipoEducacion 				integer 		default 0,
	CentroEducativo 			varchar(100) 	default '',
	NivelAcademico 				integer 		default 0,
	Estudio 					varchar(100) 	default '',
	Estado 						integer 		default 0,
	AnoInicio 					integer 		default 0,
	MesInicio 					integer 		default 0,
	AnoFinalizacion 			integer 		default 0,
	MesFinalizacion 			integer 		default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX EducacionEmpleado_IdEmpleado ON EDUCACIONEMPLEADO
(IdEmpleado ASC);

CREATE TABLE EMPLEADOS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	TipoIdentificacion 			integer 		default 0,
	Documento 					varchar(20) 	default '',
	IdCiudadExpedicion 			integer 		default 0,
	FechaExpedicion 			date 			NULL,
	CodigoSAP					varchar(10)		default '',
	IdTitulo 					integer 		default 0,
	Apellido1 					varchar(25) 	default '',
	Apellido2 					varchar(25) 	default '',
	Nombre1 					varchar(25) 	default '',
	Nombre2 					varchar(25) 	default '',
	Estado 						integer 		default 0,
	Direccion 					varchar(60) 	default '',
	Barrio 						varchar(40) 	default '',
	Localidad 					varchar(25) 	default '',
	IdCiudad 					integer 		default 0,
	Telefono 					varchar(15) 	default '',
	Celular 					varchar(15) 	default '',
	Email 						varchar(100) 	default '',
	EmailCorporativo 			varchar(100) 	default '',
	EmailProyecto 				varchar(100) 	default '',
	FechaNacimiento 			date 			NULL,
	IdCiudadNacimiento 			integer 		default 0,
	IdCargo 					integer 		default 0,
	Vicepresidencia				integer			default 0,
	IdSede 						integer 		default 0,
	IdCentro 					integer 		default 0,
	IdProyecto 					integer 		default 0,
	IdCategoria 				integer 		default 0,
	IdCiudadTrabajo 			integer 		default 0,
	FechaIngreso 				date 			NULL,
	TipoContrato 				integer 		default 0,
	ActaIngreso 				varchar(10) 	default '',
	FechaActaIngreso 			date 			NULL,
	Contrato 					varchar(10) 	default '',
	FechaPeriodoPrueba 			date 			NULL,
	FechaVencimiento 			date 			NULL,
	Prorrogas 					integer 		default 0,
	FechaRegimenCesantias 		date 			NULL,
	ModalidadTrabajo 			integer 		default 0,
	SueldoBasico 				numeric(12, 2) 	default 0,
	SubsidioTransporte 			integer 		default 0,
	PeriodicidadPago 			integer 		default 0,
	HorasMes 					integer 		default 0,
	DiasAno 					integer 		default 0,
	IdSindicato 				integer 		default 0,
	IdEPS 						integer 		default 0,
	RegimenCesantias 			integer 		default 0,
	IdFondoCesantias 			integer 		default 0,
	FactorPrestacional 			numeric(8, 4) 	default 0,
	IdFondoPensiones 			integer 		default 0,
	SubtipoCotizante			integer			default 0, 
	IdCajaCompensacion 			integer 		default 0,
	IdARL						integer			default 0,
	NivelRiesgo					integer			default 0,
	IdTurno 					integer 		default 0,
	Genero 						integer 		default 0,
	EstadoCivil 				integer 		default 0,
	LibretaMilitar 				varchar(20) 	default '',
	DistritoMilitar 			varchar(3) 		default '',
	LicenciaConduccion 			varchar(20) 	default '',
	TarjetaProfesional 			varchar(40) 	default '',
	FactorRH 					integer 		default 0,
	Profesion 					varchar(100) 	default '',
	Educacion 					integer 		default 0,
	FormaDePago 				integer 		default 0,
	IdBanco 					integer 		default 0,
	CuentaBancaria 				varchar(30) 	default '',
	CuentaBancaria2				varchar(30) 	default '',
	TipoCuentaBancaria 			integer 		default 0,
	PersonasACargo 				integer 		default 0,
	MetodoRetencion 			integer 		default 0,
	MayorRetencionFuente 		numeric(12, 2) 	default 0,
	CuotaVivienda 				numeric(12, 2) 	default 0,
	SaludYEducacion 			numeric(12, 2) 	default 0,
	DeduccionDependientes 		bit 			default 0,
	FechaInicialDeducciones		date			NULL, 
	FechaFinalDeducciones		date			NULL, 
	PorcentajeRetencion 		numeric(6, 2) 	default 0,
	DerechoDotacion 			bit 			default 0,
	FechaUltimaDotacion 		date 			NULL,
	FechaAumento 				date 			NULL,
	SueldoAnterior 				numeric(12, 2) 	default 0,
	FechaRetiro 				date 			NULL,
	ActaRetiro 					varchar(15) 	default '',
	MotivoRetiro 				integer 		default 0,
	FechaRetiroSeguridadSocial 	date 			NULL,
	DiasSeguridadSocialEnRetiro integer 		default 0,
	DiasSancion 				integer 		default 0,
	DiasLicencia 				integer 		default 0,
	PoseeVehiculo 				bit 			default 0,
	CategoriaLicencia 			integer 		default 0,
	PerfilProfesional 			text 			default '',
	Estudios 					text 			default '',
	PoliticamenteExpuesta 		bit 			default 0,
	AceptaPoliticaTD 			bit 			default 0,
	Observaciones 				text 			default '',
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Empleados_Documento ON EMPLEADOS
(Documento ASC);

CREATE INDEX Empleados_CodigoSAP ON EMPLEADOS
(CodigoSAP ASC);

CREATE INDEX Empleados_Nombre ON EMPLEADOS
(Apellido1 ASC, Apellido2 ASC, Nombre1 ASC, Nombre2 ASC);

CREATE INDEX Empleados_IdCentro ON EMPLEADOS
(IdCentro ASC);

CREATE INDEX Empleados_IdProeycto ON EMPLEADOS
(IdProyecto ASC);

CREATE INDEX Empleados_IdCargo ON EMPLEADOS
(IdCargo ASC);

CREATE TABLE EXPERIENCIALABORAL (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	Empresa 					varchar(100) 	default '',
	IdCiudad 					integer 		default 0,
	Cargo 						varchar(100) 	default '',
	JefeInmediato 				varchar(100) 	default '',
	Telefono 					varchar(15) 	default '',
	FechaIngreso 				date 			NULL,
	FechaRetiro 				date 			NULL,
	Responsabilidades 			text 			default '',
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX ExperienciaLaboral_IdEmpleado ON EXPERIENCIALABORAL
(IdEmpleado ASC);

CREATE TABLE IDIOMAS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Idioma 						varchar(3) 		default '',
	Nombre 						varchar(40) 	default '',
	Orden 						integer 		default 1,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Idiomas_Idioma ON IDIOMAS
(Orden ASC, Idioma ASC);

CREATE TABLE IDIOMASEMPLEADO (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	IdIdioma 					integer 		default 0,
	Nivel 						integer 		default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX IdiomasEmpleado_Idioma ON IDIOMASEMPLEADO
(IdEmpleado ASC);

CREATE TABLE MAYORES (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Mayor 						varchar(2) 		default '',
	Nombre 						varchar(40) 	default '',
	TipoLiquidacion 			integer 		default 0,
	ClaseConcepto 				integer 		default 0,
	TipoRetencion 				integer 		default 0,
	BasePrimas 					bit 			default 0,
	BaseVacaciones 				bit 			default 0,
	BaseCesantias 				bit 			default 0,
	AcumulaSanciones 			bit 			default 0,
	AcumulaLicencias 			bit 			default 0,
	ControlaSaldos 				bit 			default 0,
	RenglonCertificado 			varchar(3) 		default '',
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Mayores_Mayor ON MAYORES
(Mayor ASC);

CREATE TABLE NOMINA (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdPeriodo 					integer 		default 0,
	Ciclo 						integer 		default 0,
	IdEmpleado 					integer 		default 0,
	IdConcepto 					integer 		default 0,
	Horas 						numeric(8, 4) 	default 0,
	Valor 						numeric(12, 2) 	default 0,
	Saldo 						numeric(12, 2) 	default 0,
	Liquida 					varchar(1) 		default '',
	Afecta 						integer 		default 0,
	Clase_Cr 					integer 		default 0,
	IdCentro 					integer 		default 0,
	TipoEmpleado 				integer 		default 0,
	IdCredito 					integer 		default 0,
	Fecha 						date 			NULL,
	TipoRegistro 				integer 		default 0,
	FechaInicial 				date 			NULL,
	FechaFinal 					date 			NULL,
	IdPersona 					integer 		default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Nomina_IdEmpleado ON NOMINA
(IdEmpleado ASC);

CREATE INDEX Nomina_IdConcepto ON NOMINA
(IdConcepto ASC);

CREATE TABLE NOVEDADESPROGRAMABLES (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdConcepto 					integer 		default 0,
	TipoEmpleado 				integer 		default 0,
	IdEmpleado 					integer 		default 0,
	IdCentro 					integer 		default 0,
	IdCargo 					integer 		default 0,
	Horas 						numeric(8, 2) 	default 0,
	Valor 						numeric(12, 2) 	default 0,
	SalarioLimite 				numeric(12, 2) 	default 0,
	FechaLimite					date, 
	IdTercero					integer			default 0,
	Aplica 						integer 		default 0,
	ModoLiquidacion 			integer 		default 0,
	Estado 						integer 		default 0, 
	IdPeriodoCierre				integer			default 0,
	CicloCierre					integer			default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX NovedadesProgramables_IdConcepto ON NOVEDADESPROGRAMABLES
(IdConcepto ASC);

CREATE INDEX NovedadesProgramables_IdEmpleado ON NOVEDADESPROGRAMABLES
(IdEmpleado ASC);

CREATE TABLE OTROSCONOCIMIENTOSEMPLEADO (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	Conocimiento 				varchar(100) 	default '',
	Nivel 						integer 		default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX OtrosConocimientosEmpleado_IdEmpleado ON OTROSCONOCIMIENTOSEMPLEADO
(IdEmpleado ASC);

CREATE TABLE PAISES (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Nombre1 					varchar(60) 	default '',
	Nombre2 					varchar(60) 	default '',
	Nombre3 					varchar(60) 	default '',
	Iso2 						varchar(2) 		default '',
	Iso3 						varchar(3) 		default '',
	PhoneCode 					varchar(10) 	default '',
	Orden 						integer 		default 1,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Paises_Nombre1 ON PAISES
(Orden ASC, Nombre1 ASC);

CREATE TABLE PARAMETROS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Parametro 					varchar(40) 	default '',
	Detalle 					varchar(100) 	default '',
	Valor 						integer 		default 0,
	Valor2 						numeric(16,4)	default 0,
	Texto						text			default '', 
	Fecha 						date 			NULL,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Parametros_Parametro ON PARAMETROS
(Parametro ASC);

CREATE TABLE PERIODOS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Referencia 					integer 		default 0,
	Periodicidad 				integer 		default 0,
	Periodo 					integer 		default 0,
	FechaInicial 				date 			NULL,
	FechaFinal 					date 			NULL,
	AcumuladoCiclo1 			bit 			default 0,
	AcumuladoCiclo2 			bit 			default 0,
	AcumuladoCiclo3 			bit 			default 0,
	AcumuladoCiclo4 			bit 			default 0,
	AcumuladoCiclo5 			bit 			default 0,
	AcumuladoCiclo6 			bit 			default 0,
	AcumuladoCiclo7 			bit 			default 0,
	AcumuladoCiclo8 			bit 			default 0,
	AcumuladoCiclo9 			bit 			default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Periodos_Periodo ON PERIODOS
(Referencia ASC, Periodicidad ASC, Periodo ASC);

CREATE TABLE PERIODOSACUMULADOS(
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdPeriodo 					integer 		default 0,
	Ciclo 						integer 		default 0,
	Acumulado 					bit 			default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX PeriodosAcumulados_IdPeriodo ON PERIODOSACUMULADOS 
(IdPeriodo ASC, Ciclo ASC);

CREATE TABLE PLANTILLAS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	EstadoEmpleado 				integer 		default 0,
	TipoPlantilla 				integer 		default 0,
	TipoContrato 				integer 		default 0,
	Asunto 						varchar(255) 	default '',
	Plantilla 					text 			default '',
	CodigoDocumento 			varchar(40) 	default '',
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE TABLE PRESTAMOS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	IdConcepto 					integer 		default 0,
	TipoPrestamo 				integer 		default 0,
	Fecha 						date 			NULL,
	ValorPrestamo 				numeric(12, 2) 	default 0,
	ValorCuota 					numeric(12, 2) 	default 0,
	Cuotas 						integer 		default 0,
	SaldoPrestamo 				numeric(12, 2) 	default 0,
	SaldoCuotas 				integer 		default 0,
	IdBanco						integer			default 0,
	IdTercero					integer			default 0,
	IdPeriodo 					integer 		default 0,
	Ciclo 						integer 		default 0,
	Estado 						integer 		default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Prestamos_IdEmpleado ON PRESTAMOS 
(IdEmpleado ASC);

CREATE TABLE PRIMASERVICIOS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	FechaCausacion 				date 			NULL,
	FechaInicial 				date 			NULL,
	FechaFinal 					date 			NULL,
	DiasSancionYLicencia 		integer 		default 0,
	DiasALiquidar 				integer 		default 0,
	SueldoBasico 				numeric(12, 2) 	default 0,
	SalarioBase 				numeric(12, 2) 	default 0,
	ValorPrima 					numeric(12, 2) 	default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX PrimaServicios_IdEmpleado ON PRIMASERVICIOS 
(IdEmpleado ASC);

CREATE TABLE SEDES (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Sede 						varchar(10) 	default '',
	Nombre 						varchar(60) 	default '',
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Sedes_Sede ON SEDES 
(Sede ASC);

CREATE TABLE TERCEROS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	TipoIdentificacion 			integer 		default 0,
	Documento 					varchar(20) 	default '',
	Codigo						varchar(20)		default '', 
	Nombre 						varchar(100) 	default '',
	EsDeudor 					bit 			default 0,
	EsAcreedor 					bit 			default 0,
	Direccion 					varchar(60) 	default '',
	IdCiudad 					integer 		default 0,
	Telefono 					varchar(15) 	default '',
	Celular 					varchar(15) 	default '',
	Email 						varchar(100) 	default '',
	FormaDePago 				integer 		default 0,
	IdBanco 					integer 		default 0,
	CuentaBancaria 				varchar(30) 	default '',
	TipoCuentaBancaria 			integer 		default 0,
	EsSindicato 				bit 			default 0,
	CuentaSindicato				varchar(20)		default '', 
	EsEPS 						bit 			default 0, 
	CuentaEPS					varchar(20)		default '', 
	EsARL 						bit 			default 0,
	CuentaARL					varchar(20)		default '',
	EsFondoCesantias 			bit 			default 0,
	CuentaFondoCesantias		varchar(20)		default '',
	EsFondoPensiones 			bit 			default 0,
	CuentaFondoPensiones		varchar(20)		default '',
	EsCCF 						bit 			default 0,
	CuentaCCF					varchar(20)		defualt '', 
	CodigoSAP					varchar(10)		default '',
	AceptaPoliticaTD 			bit 			default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Terceros_Documento ON TERCEROS 
(Documento ASC);

CREATE INDEX Terceros_Codigo ON TERCEROS 
(Codigo ASC);

CREATE INDEX Terceros_Nombre ON TERCEROS 
(Nombre ASC);

CREATE TABLE TIPODOC (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	TipoDocumento 				varchar(5) 		default '',
	Nombre 						varchar(40) 	default '',
	TipoNumeracion 				integer 		default 0,
	Prefijo 					varchar(5) 		default '',
	Secuencia 					integer 		default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);


CREATE INDEX TipoDoc_TipoDocumento ON TIPODOC  
(TipoDocumento ASC);

CREATE TABLE USUARIOS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Usuario 					varchar(10) 	default '',
	Nombre 						varchar(60) 	default '',
	TipoId 						integer 		default 0,
	Documento 					varchar(15) 	default '',
	Direccion 					varchar(60) 	default '',
	IdCiudad 					integer 		default 0,
	Telefono 					varchar(25) 	default '',
	Celular 					varchar(25) 	default '',
	Email 						varchar(100) 	default '',
	Perfil 						integer 		default 0,
	Registro 					varchar(32) 	default '',
	Vigencia 					integer 		default 0,
	Vence 						date 			NULL,
	IdIdioma 					integer 		default 0,
	IdPadre 					integer 		default 0,
	Link 						varchar(200) 	default '',
	Bloqueado 					bit 			default 0,
	LogIn 						datetime2(7) 	NULL,
	LogOut 						datetime2(7) 	NULL,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Usuarios_Usuario ON USUARIOS  
(Usuario ASC);

CREATE TABLE VACACIONES (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	SueldoBasico 				numeric(12, 2) 	default 0,
	RecargoNocturno 			numeric(12, 2) 	default 0,
	SalarioBase 				numeric(12, 2) 	default 0,
	DiasSancionYLicencia 		integer 		default 0,
	FechaCausacion 				date 			NULL,
	FechaLiquidacion 			date 			NULL,
	FechaInicio 				date 			NULL,
	FechaIngreso 				date 			NULL,
	DiasALiquidar 				integer 		default 0,
	DiasEnTiempo 				integer 		default 0,
	DiasEnDinero 				integer 		default 0,
	ValorEnTiempo 				numeric(12, 2) 	default 0,
	ValorEnDinero 				numeric(12, 2) 	default 0,
	DiasFestivos 				integer 		default 0,
	Dias31 						integer 		default 0,
	ValorLiquidado 				numeric(12, 2) 	default 0,
	ValorFestivos 				numeric(12, 2) 	default 0,
	ValorDia31 					numeric(12, 2) 	default 0,
	IdPeriodo 					integer 		default 0,
	Ciclo 						integer 		default 0,
	Observaciones 				varchar(200) 	default '',
	DiasProcesados				integer			default 0,
	Procesado 					bit 			default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Vacaciones_IdEmpleado ON VACACIONES  
(IdEmpleado ASC, FechaCausacion ASC, FechaInicio ASC);

CREATE TABLE INCAPACIDADES (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	IdConcepto					integer			default 0,	
	FechaIncapacidad			date			NULL, 
	FechaInicio 				date 			NULL,
	DiasIncapacidad				integer			default 0,
	DiasCausados				integer			default 0,
	CausaAusentismo				integer			default 0,
	TipoAusentismo				integer			default 0,
	ClaseAusentismo				integer			default 0,
	IdDiagnostico				integer			default 0,
	PorcentajeAuxilio			numeric(6,2)	default 0,
	BaseLiquidacion				integer			default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Incapacidades_IdEmpleado ON INCAPACIDADES  
(IdEmpleado ASC, FechaInicio ASC);

CREATE TABLE DIAGNOSTICOS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Diagnostico					varchar(10) 	default '',
	Nombre						varchar(200)	default '',
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Diagnosticos_Diagnostico ON DIAGNOSTICOS  
(Diagnostico ASC);

CREATE INDEX Diagnosticos_Nombre ON DIAGNOSTICOS  
(Nombre ASC);

CREATE TABLE LOGEMPLEADOS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	Campo						text			default '',
	ValorAnterior				text			default '',
	ValorActual					text			default '',
	IdUsuario					integer			default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX LogEmpleados_IdEmpleado ON LOGEMPLEADOS  
(IdEmpleado ASC, FechaCreacion ASC);

CREATE TABLE DISPERSIONPORCENTRO (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdPeriodo					integer			default 0,
	Ciclo						integer			default 0,
	IdEmpleado 					integer 		default 0,
	IdCentro					integer			default 0,
	Porcentaje					numeric(6,2)	default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX DispeersionPorCentro_IdEmpleado ON DISPESIONPORCENTRO  
(IdPeriodo ASC, Ciclo ASC, IdEmpleado ASC, IdCentro ASC);

CREATE SYNONYM dbo.DISPERSIONPORCENTRO  FOR nomina.DISPERSIONPORCENTRO;

CREATE TABLE DETALLESAP (
	ConsecID					integer			default 0,
	RecordKey					integer			default 0,
	LineNum						integer			default 0,
	AccountCode					varchar(16)		default '',
	ShortName					varchar(10)		default '',
	CostingCode					varchar(10)		default '',
	Projectcode	 				varchar(10) 	default '',
	Debit 						numeric(12)		default 0,
	Credit						numeric(12)		default 0, 
	DueDate						varchar(8)		default '',
	LineMemo					varchar(100)	default '',
	Reference2					varchar(100)	default '',
	ReferenceDate1				varchar(8)		default '',
	ReferenceDate2				varchar(8)		default '',
	TaxDate						varchar(8)		default '',
	U_infoco01					varchar(10)		default '',
	U_codRet					varchar(1)		default '',
	U_BaseRet					integer			default 0,
	U_TarifaRet					integer			default 0,
	Procesado					varchar(5)		default '',
	CodCompania					integer			default	0,
	OcrCode2					varchar(10)		default ''
);

CREATE SYNONYM dbo.DETALLESAP FOR nomina.DETALLESAP;

CREATE TABLE CESANTIAS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdEmpleado 					integer 		default 0,
	FechaLiquidacion			date 			NULL,
	FechaIngreso 				date 			NULL,
	FechaInicio					date 			NULL,
	DiasCesantias		 		integer 		default 0,
	DiasSancionYLicencias 		integer 		default 0,
	SueldoBasico 				numeric(12, 2) 	default 0,
	SalarioBase 				numeric(12, 2) 	default 0,
	ValorCesantias				numeric(12, 2) 	default 0,
	AnticipoCesantias			numeric(12, 2) 	default 0,
	InteresCesantias			numeric(12, 2) 	default 0,
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Cesantias_IdEmpleado ON CESANTIAS 
(IdEmpleado ASC);

CREATE TABLE EQUIV_CONCEPTOS (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	ConceptoDesigner			varchar(5)		default '',
	ConceptoComware				varchar(5)		default '',
	Nombre						varchar(60)		default '', 
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);

CREATE INDEX Equiv_Conceptos_ConceptoDesigner ON EQUIV_CONCEPTOS 
(ConceptoDesigner ASC);

CREATE INDEX Equiv_Conceptos_ConceptoComware ON EQUIV_CONCEPTOS 
(ConceptoComware ASC);

CREATE TABLE PERFILES (
	Id 							integer 		IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdCargoBase					integer			default 0, 
	IdDependencia				integer			default 0, 
	NivelAcademico				integer			default 0,
	Estudios					text			default '',
	ExperienciaLaboral			text			default '',
	FormacionAdicional			text			default '',
	Competencias				integer			default 0,
	CondicionesTrabajo			text			default '',
	FuncionesSGC				integer			default 0,
	MisionCargo					text			default '',
	Funciones					text			default '',
	FuncionesHSEQ				integer			default 0,
	GestionHS					integer			default 0,
	GestionAmbiental			integer			default 0,
	GestionCalidad				integer			default 0,
	GestionSI					integer			default 0,
	Responsable					varchar(100)	default '',
	Elabora						varchar(100)	default '',
	FechaCreacion 				datetime2(7) 	default getdate(),
	FechaActualizacion 			datetime2(7) 	NULL,
	Borrado 					bit 			default 0
);


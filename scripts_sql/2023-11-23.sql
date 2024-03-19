-- ------------------------------------------------------------------
-- Date: 23/11/2023
-- Responsible: Esteban Diaz
-- Executed in production: OK
-- Executed in beta: --
-- Executed in development: --
-- Tickets: DEV-673
-- ------------------------------------------------------------------
--
-- Communs is added to table detallessap
ALTER TABLE
    nomina.detallessap
ADD
    IdPeriodo numeric(38, 0) NOT NULL;

ALTER TABLE
    nomina.detallessap
ADD
    IdComprobante numeric(38, 0) NOT NULL;

ALTER TABLE
    nomina.detallessap
ADD
    IdLogPila numeric(38, 0) NOT NULL;

ALTER TABLE
    nomina.detallessap
ADD
    IdEmpleado numeric(38, 0) NOT NULL;

ALTER TABLE
    nomina.detallessap
ADD
    FechaCreacion datetime DEFAULT getdate() NULL;

-- Communs is delte to table detallessap
ALTER TABLE
    nomina.detallessap DROP COLUMN DocumentoEmpleado;

-- ------------------------------------------------------------------
--
-- Adjust table log_pila
-- Delete table log_pila
DROP TABLE nomina.log_pila;

-- Table is added to persist data from stack to carry to interface
CREATE TABLE nomina.log_pila (
    id integer IDENTITY(1, 1) NOT NULL,
    idperiodo integer NOT NULL,
    ciclo integer default 0,
    idempleado integer NOT NULL,
    archivo varchar(50) default 'ACUMULADOS' NOT NULL,
    idsarchivo text NOT NULL,
    idsconcepto text NOT NULL,
    dias float default 0,
    ibcpension float default 0,
    ibcsalud float default 0,
    ibcarl float default 0,
    ibcccf float default 0,
    ibcsena float default 0,
    ibcicbf float default 0,
    ibcsolidaridad float default 0,
    ibcsubsistencia float default 0,
    tarifapension float default 0,
    tarifasalud float default 0,
    tarifaarl float default 0,
    tarifaccf float default 0,
    tarifasena float default 0,
    tarifaicbf float default 0,
    tarifasolidaridad float default 0,
    tarifasubsistencia float default 0,
    valorpension float default 0,
    valorsalud float default 0,
    valorarl float default 0,
    valorccf float default 0,
    valorsena float default 0,
    valoricbf float default 0,
    valorsolidaridad float default 0,
    valorsubsistencia float default 0,
    fechacreacion datetime2(7) default getdate(),
    fechaactualizacion datetime2(7) default getdate(),
    linea text
);
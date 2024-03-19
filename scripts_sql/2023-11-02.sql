-- ------------------------------------------------------------------
-- Date: 02/11/2023
-- Responsible: Esteban Diaz
-- Executed in production: OK
-- Executed in beta: --
-- Executed in development: --
-- ------------------------------------------------------------------
--
-- Commune is added to table detallessap
ALTER TABLE
    nomina.detallessap
ADD
    DocumentoEmpleado varchar(20) NULL;

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
    tarifasalud float default 0,
    tarifaarl float default 0,
    tarifaccf float default 0,
    tarifasena float default 0,
    tarifaicbf float default 0,
    valorpension float default 0,
    valorsalud float default 0,
    valorarl float default 0,
    valorccf float default 0,
    valorsena float default 0,
    valoricbf float default 0,
    fechacreacion datetime2(7) default getdate(),
    fechaactualizacion datetime2(7) default getdate(),
    linea text
);
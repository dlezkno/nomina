-- ------------------------------------------------------------------
-- Date: 02/11/2023
-- Responsible: Esteban Diaz
-- Executed in production: OK
-- Executed in beta: --
-- Executed in development: --
-- ------------------------------------------------------------------
--
-- Table to save the electronic payroll logs
CREATE TABLE nomina.log_ne (
    id integer IDENTITY(1, 1) NOT NULL,
    idperiodo integer NOT NULL,
    idempleado integer NOT NULL,
    consecutivo integer NOT NULL,
    archivoPath text NOT NULL,
    archivoMsterPath text,
    idTrack text,
    tipoDocumento varchar(4) NOT NULL,
    numeroDocumento varchar(10) NOT NULL,
    nit varchar(10) NOT NULL,
    periodoNomina varchar(4) NOT NULL,
    error text,
    intentos integer default 1,
    estado text default 'EnProgreso',
    fechacreacion datetime2(7) default getdate(),
    fechaactualizacion datetime2(7) default getdate()
);
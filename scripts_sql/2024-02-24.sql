-- ------------------------------------------------------------------
-- Date: 24/02/2024
-- Responsible: Esteban Diaz
-- Executed in production: OK
-- Executed in beta: --
-- Executed in development: --
-- ------------------------------------------------------------------
--
-- Table is added to persist historical changes in the limits
CREATE TABLE nomina.log_topes (
    id integer IDENTITY(1, 1) NOT NULL,
    idperiodo integer NOT NULL,
    ciclo integer default 0,
    idempleado integer NOT NULL,
    ExencionAfcFvpAnual float default 0,
    ExencionAnual25 float default 0,
    ExencionAnual float default 0,
    ExencionAfcFvpRev float default 0,
    ExencionMes25Rev float default 0,
    ExencionMesRev float default 0,
    ExencionAfcFvpMes float default 0,
    ExencionMes25 float default 0,
    ExencionMes float default 0,
    fechacreacion datetime2(7) default getdate(),
    fechaactualizacion datetime2(7) default getdate(),
);

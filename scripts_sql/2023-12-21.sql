-- ------------------------------------------------------------------
-- Date: 21/12/2023
-- Responsible: David lezcano
-- Executed in production: OK
-- Executed in beta: --
-- Executed in development: --
-- Tickets: DEV-691
-- ------------------------------------------------------------------
--
-- Communs is added to table usuarios
ALTER TABLE 
    RRHH_Nomina.nomina.usuarios 
ALTER 
    COLUMN fechacambioregistro datetime2 NULL;

ALTER TABLE 
    RRHH_Nomina.nomina.usuarios 
ALTER 
    COLUMN coderegistro varchar(100) NULL;


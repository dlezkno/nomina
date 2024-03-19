-- ------------------------------------------------------------------
-- Date: 04/01/2024
-- Responsible: David lezcano
-- Executed in production: OK
-- Executed in beta: --
-- Executed in development: --
-- Tickets: DEV-815
-- ------------------------------------------------------------------
--
-- Communs is added to table empleados
ALTER TABLE 
    RRHH_Nomina.nomina.empleados 
ALTER 
    COLUMN salariopractica numeric NULL;

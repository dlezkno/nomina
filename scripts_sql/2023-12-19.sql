-- ------------------------------------------------------------------
-- Date: 23/11/2023
-- Responsible: David lezcano
-- Executed in production: OK
-- Executed in beta: --
-- Executed in development: --
-- Tickets: DEV-644
-- ------------------------------------------------------------------
--
-- Communs is added to table empleados
ALTER TABLE 
    RRHH_Nomina.nomina.empleados 
ALTER 
    COLUMN fechafinetapalectiva date NULL;

ALTER TABLE 
    RRHH_Nomina.nomina.empleados 
ALTER 
    COLUMN fechainicioetapaproductiva date NULL;


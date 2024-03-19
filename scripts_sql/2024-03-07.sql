-- ------------------------------------------------------------------
-- Date: 03/03/2024
-- Responsible: David lezcano
-- Executed in production: OK
-- Executed in beta: --
-- Executed in development: --
-- ------------------------------------------------------------------
--
-- Table is added to persist historical changes in the limits
ALTER TABLE RRHH_Nomina_Beta.nomina.auxiliares ADD idtercero int 0;
ALTER TABLE RRHH_Nomina_Beta.nomina.empleados ADD talla varchar(100) NULL;
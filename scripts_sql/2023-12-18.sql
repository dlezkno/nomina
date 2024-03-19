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
    nomina.empleados
ADD
    instituciondeformacion varchar(100);

ALTER TABLE
    nomina.empleados
ADD
    fechafinetapalectiva datetime DEFAULT getdate() NULL;

ALTER TABLE
    nomina.empleados
ADD
    fechainicioetapaproductiva datetime DEFAULT getdate() NULL;


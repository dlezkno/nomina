-- ------------------------------------------------------------------
-- Date: 09/02/2024
-- Responsible: David lezcano
-- Executed in production: OK
-- Executed in beta: --
-- Executed in development: --
-- Tickets: DEV-815
-- ------------------------------------------------------------------
--
-- Communs is added to table empleados

EXEC sys.sp_rename N'log_requests.[section]' , N'sectionlog', 'COLUMN';
EXEC sys.sp_rename N'log_requests.[date]' , N'datelog', 'COLUMN';
EXEC sys.sp_rename N'log_requests.[to]' , N'emailto', 'COLUMN';
EXEC sys.sp_rename N'log_requests.[type]' , N'typelog', 'COLUMN';


CREATE TABLE nomina.solicitudespersonal (
    id int IDENTITY(1, 1) NOT NULL,
    idusuario int,
    idproyecto int,
    idcentro int,
    idcargo int,
    cantidad int,
    idsede int,
    tipocontrato varchar(100),
    fechaingreso date(3),
    salariominimo int,
    salariomaximo int,
    caracteristicas text(16),
    fechasolicitud date(3),
    estado varchar(100),
    idpsicologo int,
    tipovacante varchar(100), 
    valorbono int, 
    idciudad int, 
    inicapa date, 
    fincapa date, 
    numrequerimiento varchar(100);
);


INSERT INTO nomina.parametros
(id, parametro, detalle, valor, fecha, fechacreacion, fechaactualizacion, borrado, valor2, texto)
VALUES(0, 'EstadoSolicitud', 'CREADO', 1, '', '', '', 0, 0, ''),
VALUES(0, 'EstadoSolicitud', 'EN PROECESO DE BUSQUEDA', 2, '', '', '', 0, 0, ''),
VALUES(0, 'EstadoSolicitud', 'EN PROCESO DE SELECCION', 3, '', '', '', 0, 0, ''),
VALUES(0, 'EstadoSolicitud', 'EN PROCESO DE CONTRATACION', 4, '', '', '', 0, 0, ''),
VALUES(0, 'EstadoSolicitud', 'CONTRATADO', 5, '', '', '', 0, 0, '');
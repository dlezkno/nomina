-- ------------------------------------------------------------------
-- Date: 16/01/2024
-- Responsible: David lezcano
-- Executed in production: OK
-- Executed in beta: --
-- Executed in development: --
-- ------------------------------------------------------------------
-- Commune is added to table log_requests
-- Table is added to persist data from stack to carry to interface
CREATE TABLE nomina.log_requests (
    id int IDENTITY(1, 1) NOT NULL,
    id_user int,
    [type] varchar(100),
    ip varchar(100),
    uri varchar(100),
    [section] varchar(100),
    body varchar(100),
    curl varchar(100),
    response varchar(100),
    [date] datetime2(7),
    data_user nvarchar(MAX) NULL
);
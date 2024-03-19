<?php
	class conexion
	{
		private $conn;

		public function __construct()
		{
			// POSTGRE SQL
			// $connStr = 'pgsql:host=' . DB_HOST . 
			// 			';port=' . DB_PORT .
			// 			';dbname=' . DB_NAME;

			// try 
			// {
			// 	$this->conn = new PDO($connStr, DB_USER, DB_PASSWORD);
			// 	$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// } 
			// catch (PDOException $e) 
			// {
			// 	$this->conn = 'Error de conexión';
			// 	echo 'ERROR: ' . $e->getMessage();
			// }

			// SQL SERVER
			try 
			{  
				$this->conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
				$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
				
				// $connInfo = array("Database" => DB_NAME, "UID" => DB_USER, "PWD" => DB_PASSWORD);
				// $this->conn = sqlsrv_connect(DB_HOST, $connInfo);
			}  
			catch( PDOException $e ) 
			{  
				$this->conn = 'Error de conexión';
				echo 'ERROR: ' . $e->getMessage();
			}  
		
		}

		public function connect()
		{
			return $this->conn;
		}
	}
?>

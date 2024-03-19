<?php
	class pgSQL extends conexion
	{
		private $conn;

		public function __construct()
		{
			$this->conn = new conexion();
			$this->conn = $this->conn->connect();
		}

		public function listar(string $query)
		{
			$this->logQuerySessionUser($query);
			$sql = $this->conn->prepare($query);
			$sql->execute();
			$resp = $sql->fetchall(PDO::FETCH_ASSOC);
			return $resp; 
		}

		public function adicionar(string $query, array $data)
		{
			$this->logQuerySessionUser($query);
			$sql = $this->conn->prepare($query);
			$sql->execute($data);

			$id = $this->conn->lastInsertId();

			return $id;	
		}	

		public function leer(string $query)
		{
			$this->logQuerySessionUser($query);
			$sql = $this->conn->query($query);
			$sql->execute();
			$resp = $sql->fetch(PDO::FETCH_ASSOC);
			return $resp; 
		}	

		public function actualizar(string $query, array $data)
		{
			$this->logQuerySessionUser($query);
			$sql = $this->conn->prepare($query);
			$resp = $sql->execute($data);
			return $resp; 
		}

		public function borrar(string $query)
		{
			$this->logQuerySessionUser($query);
			$sql = $this->conn->prepare($query);
			$resp = $sql->execute();
			return $resp; 
		}

		public function query(string $query)
		{
			$this->logQuerySessionUser($query);
			$ok = $this->conn->exec($query);
			// $sql->execute();
			// $resp = $sql->fetchall(PDO::FETCH_ASSOC);
			return $ok; 
		}

		private function logQuerySessionUser(string $query)
		{
			try {
				$isActionForLog = stripos(" $query", 'DELETE') OR stripos(" $query", 'DROP');
				if (!$isActionForLog) return;

				$dataLogin = '';
				if (isset($_SESSION['Login'])) {
					$dataLogin = implode(',', $_SESSION['Login']);
				}
				$data = array(
					'userData' => $dataLogin,
					'date' => date('Y-m-d H:i:s'),
					'ip' => $_SERVER['REMOTE_ADDR'],
					'host' => $_SERVER['HTTP_HOST'],
					'uri' => $_SERVER['REQUEST_URI'],
					'method' => $_SERVER['REQUEST_METHOD'],
					'query' => $query
				);

				$sql = $this->conn->prepare(
					<<<EOD
						INSERT INTO nomina.log_query
							([user], date_action, ip, host, uri, method, query)
						VALUES (:userData, :date, :ip, :host, :uri, :method, :query)
					EOD
				);

				$sql->execute($data);
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	}
?>

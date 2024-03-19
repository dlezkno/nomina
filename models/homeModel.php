<?php
	class homeModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		// public function listaUsuarios()
		// {
			// $query = "SELECT * FROM CIUDADES";
			// $request = $this->listar($query);
			// return $request;
		// }
		// 
		// // public function adicionarUsuario(string $ciudad, string $nombre)
		// {
			// // $query = 'INSERT INTO CIUDADES (ciudad, nombre) VALUES (?, ?)';
			// $aData = array($ciudad, $nombre);
			// // $request_insert = $this->adicionar($query, $aData);
			// return $request_insert;
		// }	
		// 
		// public function leerUsuario($id)
		// {
			// // $query = "SELECT * FROM CIUDADES WHERE CIUDADES.Id = $id";
			// $request = $this->leer($query);
			// return $request;
		// }	
		// 
		// // public function actualizarUsuario(int $id, string $ciudad, string $nombre)
		// {
			// // $query = "UPDATE CIUDADES SET Ciudad = ?, Nombre = ? WHERE Id = $id";
			// $aData = array($ciudad, $nombre);
			// $request = $this->actualizar($query, $aData);
			// return $request;
		// }	
// 
		// public function borrarUsuario(int $id)
		// {
			// $query = "DELETE FROM CIUDADES WHERE Id = $id";
			// $request = $this->borrar($query);
			// return $request;
		// }
		
	}
?>
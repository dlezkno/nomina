<?php
	class home extends Controllers
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function home()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
			$_SESSION['Paginar'] = TRUE;

			$this->views->getView($this, 'home');
		}	
		
		// public function listaUsuarios()
		// {
			// $data = $this->model->listaUsuarios();
			// print_r($data);
		// }
// 
		// public function adicionarUsuario()
		// {
			// // $data = $this->model->adicionarUsuario('A001', 'NUEVA YORK');
			// print_r($data);
		// }	
// 
		// public function leerUsuario($id)
		// {
			// $data = $this->model->leerUsuario($id);
			// print_r($data);
		// }	
// 
		// public function actualizarUsuario()
		// {
			// // $data = $this->model->actualizarUsuario(1124, 'A002', 'TORONTO');
			// print_r($data);
		// }	
// 
		// public function borrarUsuario($id)
		// {
			// $data = $this->model->borrarUsuario($id);
			// print_r($data);
		// }
	}
?>
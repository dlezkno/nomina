<?php
	class usuariosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros
					FROM USUARIOS 
						INNER JOIN PARAMETROS
							ON USUARIOS.Perfil = PARAMETROS.Id 
					$query;
			EOD;

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarUsuarios($query)
		{
			$query = <<<EOD
				SELECT USUARIOS.*,
						PARAMETROS.Detalle AS NombrePerfil 
					FROM USUARIOS 
						INNER JOIN PARAMETROS
							ON USUARIOS.Perfil = PARAMETROS.Id 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarRegistro(array $data)
		{
			$query = <<<EOD
				INSERT INTO PARAMETROS 
					( Parametro, Detalle, Valor )
					VALUES (
					'Perfil', 
					'ADMINISTRADOR',
					99);
			EOD;

			$this->query($query);

			$IdPerfil = getId('PARAMETROS', "PARAMETROS.Parametro = 'Perfil' AND PARAMETROS.Valor = 99");

			$Vence = "'" . date('Y-m-d', strtotime(date('Y-m-d')) + ($data['Vigencia'] * 24 * 60 * 60)) . "'";

			$data['Contrasena'] = md5($data['Contrasena']);

			$query = <<<EOD
				INSERT INTO USUARIOS
					(Usuario, Nombre, EMail, Perfil, Registro, Vigencia, Vence )
					VALUES (
						:Usuario, 
						:Nombre, 
						:Email, 
						$IdPerfil, 
						:Contrasena, 
						:Vigencia, 
						$Vence
					);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function guardarUsuario(array $data)
		{
			if ($data['Vigencia'] > 0)
				$data['Vence'] = "'" . date('Y-m-d', strtotime(date('Y-m-d')) + ($data['Vigencia'] * 24 * 60 * 60)) . "'";
			else
				$data['Vence'] = NULL;
			$data['Registro'] = md5($data['Registro']);

			$query = <<<EOD
				INSERT INTO USUARIOS
					(Usuario, Nombre, TipoId, Documento, Perfil, Registro, Vigencia, Vence, Direccion, IdCiudad, Telefono, Celular, EMail, IdIdioma, IdPadre, Link, Bloqueado)
					VALUES (
						:Usuario, 
						:Nombre, 
						:TipoId, 
						:Documento, 
						:Perfil, 
						:Registro, 
						:Vigencia, 
						:Vence, 
						:Direccion, 
						:IdCiudad, 
						:Telefono, 
						:Celular, 
						:Email,
						0, 0, '', 0
					);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarUsuario($query)
		{
			$request = $this->leer($query);
			return $request;
		}	

		public function actualizarLogin($usuario)
		{
			$query = <<<EOD
				UPDATE USUARIOS 
					SET 
					LogIn = getdate() 
					WHERE USUARIOS.Usuario = '$usuario' OR 
						USUARIOS.EMail = '$usuario';
			EOD;

			$resp = $this->query($query);

			return $resp;
		}
		
		public function actualizarUsuario(array $data, int $id)
		{
			$Vence = "'" . date('Y-m-d', strtotime(date('Y-m-d')) + ($data['vigencia'] * 24 * 60 * 60)) . "'";

			$query = <<<EOD
				UPDATE USUARIOS 
					SET 
					Usuario = :usuario, 
					Nombre = :nombre,
					TipoId = :tipoid,
					Documento = :documento,
					Perfil = :perfil,
					Vigencia = :vigencia, 
					Vence = $Vence, 
					Direccion = :direccion, 
					IdCiudad = :idciudad, 
					Telefono = :telefono, 
					Celular = :celular,
					Email = :email, 
					FechaActualizacion = getDate() 
					WHERE USUARIOS.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarUsuario(int $id)
		{
			$query = 'DELETE FROM USUARIOS WHERE USUARIOS.Id = ' . $id;
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>
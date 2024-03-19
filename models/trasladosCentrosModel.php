<?php
	class trasladosCentrosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	

		public function guardarLogEmpleado($data)
		{
			$query = <<<EOD
				INSERT INTO LOGEMPLEADOS 
					(IdEmpleado, Campo, ValorAnterior, ValorActual, IdUsuario)
					VALUES (
						:IdEmpleado, 
						:Campo, 
						:ValorAnterior, 
						:ValorActual,
						:IdUsuario);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
	}
?>
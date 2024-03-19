<?php
	class personalModel extends pgSQL
	{

        public function __construct()
		{
			parent::__construct();
		}

       public function crearSolicitud($data){

            $params = "";
            $values = "";
            $index = 0;
            foreach ($data as $key => $val) {
                $params .= $index == 0 ? $key : ",".$key;	
                $values .= $index == 0 ? "'".$val."'" : ",'".$val."'";	
                $index++;
            }
            $query = <<<EOD
                INSERT INTO nomina.solicitudespersonal 
                    ($params)
                    VALUES ($values);
            EOD;

            $rps = $this->query($query);
            return $rps;
       }


       public function editar($filtro, $id){


        $query = <<<EOD
            UPDATE nomina.solicitudespersonal 
                SET $filtro
                WHERE nomina.solicitudespersonal.Id = $id;
        EOD;

        $rps = $this->query($query);
        return $rps;

       }

       public function listaPsicologos(){

        $query = <<<EOD
            SELECT usuarios.nombre, usuarios.id FROM 
            nomina.usuarios
            INNER JOIN nomina.empleados ON USUARIOS.documento = empleados.documento
            WHERE 
            empleados.estado = '141' AND 
            perfil = '84'
        EOD;
            $request = $this->listar($query);
            return $request;

       }

       public function validatePsicologo($document){
            $query = <<<EOD
            SELECT COUNT(*) AS cantidad
            FROM nomina.empleados
            INNER JOIN CARGOS ON empleados.idcargo = CARGOS.id
            INNER JOIN PARAMETROS ON empleados.estado = PARAMETROS.id
            WHERE empleados.documento = '$document' AND 
            empleados.idcargo = '185' AND
            empleados.estado = '141'
            EOD;
            $request = $this->leer($query);
            return intval($request["cantidad"]);
       }


       public function listarPersonal(){
            $query = <<<EOD
                SELECT 
                PERSONAL.idpsicologo,
                USUARIOS.nombre AS solicitado_por, 
                CARGOS.nombre AS cargo,
                PROYECTO.nombre AS proyecto,
                CENTROS.nombre AS centro,
                SEDES.nombre AS sede,
                PARAMETROS.detalle AS tipo_de_contrato,   
                CIUDADES.nombre AS ciudad,
                PERSONAL.cantidad, 
                PERSONAL.fechaingreso AS fecha_de_ingreso, 
                PERSONAL.salariominimo AS salario_minimo, 
                PERSONAL.salariomaximo AS salario_maximo, 
                PERSONAL.caracteristicas, 
                PERSONAL.fechasolicitud AS fecha_de_solcitud,
                PERSONAL.id,
                PERSONAL.idusuario,
                PERSONAL.estado AS idestado,
                estadosolicitud.detalle AS estado,
                PERSONAL.tipovacante AS tipo_de_vacante,
                PERSONAL.valorbono AS valor_bono,
                PERSONAL.inicapa AS fecha_inicio_capacitacion,
                PERSONAL.fincapa AS fecha_fin_capacitacion,
                PERSONAL.numrequerimiento AS numero_de_requerimiento
                FROM 
                nomina.solicitudespersonal AS PERSONAL
                INNER JOIN nomina.USUARIOS ON PERSONAL.idusuario = USUARIOS.id
                INNER JOIN nomina.CIUDADES ON PERSONAL.idciudad = CIUDADES.id
                INNER JOIN nomina.CENTROS AS PROYECTO ON PERSONAL.idproyecto = PROYECTO.id
                INNER JOIN nomina.CENTROS ON PERSONAL.idcargo = CENTROS.id
                INNER JOIN nomina.CARGOS ON PERSONAL.idcentro = CARGOS.id
                INNER JOIN nomina.SEDES ON PERSONAL.idsede = SEDES.id
                INNER JOIN nomina.PARAMETROS AS estadosolicitud ON PERSONAL.estado = estadosolicitud.id
                INNER JOIN nomina.PARAMETROS ON PERSONAL.tipocontrato = PARAMETROS.id
                WHERE estadosolicitud.detalle  <> 'CONTRATADO'
            EOD;
            $request = $this->listar($query);
            return $request;
       }

    }

?>
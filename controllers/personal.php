<?php
	require_once('./templates/vendor/autoload.php');
    
	require './templates/PHPMailer-master/src/PHPMailer.php';
	require './templates/PHPMailer-master/src/SMTP.php';
	require './templates/PHPMailer-master/src/Exception.php';

    class Personal extends Controllers{

        function lista($pagina){
            
            $_SESSION['ActualizarRegistro'] = SERVERURL . '/personal/lista';
            $data = array();
            $data["mensajeError"] = "";

            if(count($_POST) > 0){
                foreach ($_POST as $key => $val) {  
                    if(strrpos($key, 'psicologo_') !== FALSE){
                        if($val != ""){
                            $info = explode('_',$key);                       
                            $id = $info[1]; 
                            $this->model->editar('idpsicologo = '.$val,$id);
                            $idusuario = $info[2];  
                            $cargo = str_replace("|"," ",$info[3]); 
                            $mail = getRegistro("USUARIOS",$idusuario);
                            $psicologa = getRegistro("USUARIOS",$id)['nombre'];
                            $emails = array($mail);
                            $rps = $this->enviarEmail(
                            'Se le ha asginado el proceso de seleccion para la busqueda del cargo '. $cargo. ' a la psicologa '. $psicologa, 
                            $emails, 
                            "SOLICITUD DE PERSONAL");
                            $data["mensajeError"] .= $rps;

                        }                        
                    }elseif(strrpos($key, 'cambioestado_') !== FALSE){
                        $info = explode('_',$key);
                        $id = $info[1];
                        $reg = getRegistro("solicitudespersonal",$id)['estado'];
                        if($val != $reg){                                                                                
                            $idusuario = $info[2];  
                            $cargo = str_replace("|"," ",$info[3]);                        
                            $this->model->editar('estado = '.$val,$id);
                            $mail = getRegistro("USUARIOS",$idusuario);
                            $emails = array($mail);
                            $estado = getRegistro("parametros",$val)['detalle'];
                            $rps = $this->enviarEmail(
                            'El estado de proceso de seleccion para la busqueda del cargo '. $cargo. ' cambio a '. $estado, 
                            $emails, 
                            "SOLICITUD DE PERSONAL");
                            $data["mensajeError"] .= $rps;

                        } 
                    }
                }
                $_POST = array();
            }
           
            $data["rows"] = $this->model->listarPersonal();
            $valid = $this->model->validatePsicologo($_SESSION['Login']["Documento"]);
            if($valid){
                $psico = $this->model->listaPsicologos();
                $listpsico = '<option value="">psicologos</option>';
                for($i =  0; $i < count($psico); $i++){
                    $listpsico .= '<option value="'.str_replace(" ",".",$psico[$i]['id']).'">'.$psico[$i]['nombre'].'</option>';
                }
                $data["psicologos"] = $listpsico;
            }
            
            $this->views->getView($this, 'personal', $data);
            
        }

        function solicitud(){
            $data = array();
            $data['mensajeError'] = "";
            if(isset($_REQUEST["Action"])){ 
                if(isset($_REQUEST['IdProyecto'])){
                if($_REQUEST['IdProyecto'] == ""){
                    $data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('proyecto.') . '</strong><br>';
                }
            }
                if(isset($_REQUEST['IdCentro'])){
                if($_REQUEST['IdCentro'] == ""){
                    $data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('centro de costos.') . '</strong><br>';
                }
            }
                if(isset($_REQUEST['IdCargo'])){
                if($_REQUEST['IdCargo'] == ""){
                    $data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('cargo.') . '</strong><br>';
                }
            }
                if(isset($_REQUEST['cantidad'])){
                if($_REQUEST['cantidad'] == ""){
                    $data['mensajeError'] .= label('Debe seleccionar la') . ' <strong>' . label('cantidad de vacantes.') . '</strong><br>';
                }
            }
                if(isset($_REQUEST['tipovacante'])){
                if($_REQUEST['tipovacante'] == ""){
                    $data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('tipo de vacante.') . '</strong><br>';
                }
            }
                if(isset($_REQUEST['IdSede'])){
                if($_REQUEST['IdSede'] == ""){
                    $data['mensajeError'] .= label('Debe seleccionar la') . ' <strong>' . label('sede.') . '</strong><br>';
                }
            }
                if(isset($_REQUEST['IdCiudad'])){
                if($_REQUEST['IdCiudad'] == ""){
                    $data['mensajeError'] .= label('Debe seleccionar la') . ' <strong>' . label('ciudad.') . '</strong><br>';
                }
            }
                if(isset($_REQUEST['TipoContrato'])){
                if($_REQUEST['TipoContrato'] == "0"){
                    $data['mensajeError'] .= label('Debe seleccionar el') . ' <strong>' . label('tipo de contrato.') . '</strong><br>';
                }
            }
                if(isset($_REQUEST['FechaIngreso'])){
                if($_REQUEST['FechaIngreso'] == ""){
                    $data['mensajeError'] .= label('Debe seleccionar la') . ' <strong>' . label('fecha de ingreso.') . '</strong><br>';
                }
            }
                if(isset($_REQUEST['salariominimo'])){
                if($_REQUEST['salariominimo'] == ""){
                    $data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('rango minimo salarial.') . '</strong><br>';
                }
            }
                if(isset($_REQUEST['salariomaximo'])){
                if($_REQUEST['salariomaximo'] == ""){
                    $data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('rango minimo maximo.') . '</strong><br>';
                }
            }
                if(isset($_REQUEST['Caracteristicas'])){
                    if($_REQUEST['Caracteristicas'] == ""){
                        $data['mensajeError'] .= label('Debe indicar las ') . ' <strong>' . label('caracteristicas.') . '</strong><br>';
                    }
                }
                if(isset($_REQUEST['tienebono'])){
                    if($_REQUEST['tienebono'] == "on"){
                        if($_REQUEST['valorbono'] == ""){
                            $data['mensajeError'] .= label('Debe indicar el valor del ') . ' <strong>' . label('Bono.') . '</strong><br>';
                        }
                    }
                }
                

                if(isset($_REQUEST['capacitacion'])){
                    if($_REQUEST['capacitacion'] == "on"){
                        if($_REQUEST['FechaInicioCapacitacion'] == ""){
                            $data['mensajeError'] .= label('Debe indicar la ') . ' <strong>' . label('Fecha de inicio capacitacion.') . '</strong><br>';
                        }
                        if($_REQUEST['FechaFincioCapacitacion'] == ""){
                            $data['mensajeError'] .= label('Debe indicar la ') . ' <strong>' . label('Fecha fin capacitacion.') . '</strong><br>';
                        }
                    }
                }
                

                if($data['mensajeError'] == ""){
                 
                    $arr = array();

                    $arr["idusuario"] = $_SESSION['Login']["Id"];
                    $arr["IdProyecto"] = $_REQUEST['IdProyecto'];
                    $arr["IdCentro"] = $_REQUEST['IdCentro'];
                    $arr["IdCargo"] = $_REQUEST['IdCargo'];
                    $arr["cantidad"] = $_REQUEST['cantidad'];
                    $arr["tipovacante"] = $_REQUEST['tipovacante'];
                    $arr["TipoContrato"] = $_REQUEST['TipoContrato'];
                    $arr["valorbono"] = $_REQUEST['valorbono'];
                    $arr["IdSede"] = $_REQUEST['IdSede'];                    
                    $arr["IdCiudad"] = $_REQUEST['IdCiudad'];
                    $arr["inicapa"] = $_REQUEST['FechaInicioCapacitacion'];
                    $arr["fincapa"] = $_REQUEST['FechaFincioCapacitacion'];
                    $arr["FechaIngreso"] = $_REQUEST['FechaIngreso'];
                    $arr["salariominimo"] = $_REQUEST['salariominimo'];
                    $arr["salariomaximo"] = $_REQUEST['salariomaximo'];
                    $arr["Caracteristicas"] = $_REQUEST['Caracteristicas'];
                    $arr["numrequerimiento"] = strtoupper(uniqid());
                    $arr["fechasolicitud"] = "20".date('y-m-d');
                    $arr["estado"] = "482";

                    $this->model->crearSolicitud($arr);
                    $docempleado =  getTabla('EMPLEADOS','idcargo = 185', 'nombre1')[0]['documento'];
                    $emails =  getTabla('USUARIOS',"documento = '".$docempleado."'" , 'nombre');
                    $rps = $this->enviarEmail(
                        'Se ha creado una nueva solicitud de personal por parte de '. $_SESSION['Login']["Nombre"], 
                        $emails, 
                        "SOLICITUD DE PERSONAL");
                    
                        $data['mensajeError'] .= ' <strong>' . label('se ha creado correctamente la solicitud y se ha enviado correo de notificacion al personal de seleccion.') . '</strong><br>';
                        $this->views->getView($this, 'solicitud', $data);
                } else{
                    $this->views->getView($this, 'solicitud', $data);
                } 

            }else{
                $this->views->getView($this, 'solicitud', $data);
            }

            
        }


        function enviarEmail($Plantilla, $emails, $asunto){

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
    
                $mail->SMTPDebug 		= 0;
                $mail->isSMTP();
                $mail->Host       		= HOST;
                $mail->Port       		= PORT;
                $mail->SMTPKeepAlive 	= true;          
                $mail->SMTPAuth   		= false;
                $mail->SMTPSecure 		= 'tls';  
                $mail->isHTML(true);
    
                $from = 'no-reply@comware.com.co';
                $fromName = 'CONTRATACIÓN DE PERSONAL - COMWARE';
    
                $mail->Subject = $asunto;
                $mail->addEmbeddedImage(LOGOTIPO, 'comware');
                $mail->Body = $Plantilla;
                $aMails = "";
                if (! empty($emails)){
                    for($j = 0; $j < count($emails); $j++){
                        $aMails .= $emails[$j]["email"].",";
                        $mail->AddAddress($emails[$j]["email"]);
                    }
                }
                    
                $response = new stdClass();
                try 
                {
                    $mail->setFrom($from, $fromName);
                    $obj = (object) array(
                        'CharSet' => $mail->CharSet,
                        'ContentType' => $mail->ContentType,
                        'Encoding' => $mail->Encoding,
                        'From' => $mail->From,
                        'FromName' => $mail->FromName,
                        'Sender' => $mail->Sender,
                        'Subject' => $mail->Subject,
                        'Mailer' => $mail->Mailer,
                        'Sendmail' => $mail->Sendmail,
                        'Host' => $mail->Host,
                        'SMTPOptions' => $mail->SMTPOptions,
                        'smtp' => $mail->smtp,
                        'to' => $mail->to);
                        $response = $mail->send();
                    logRequests($asunto,$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $aMails);
                    return "";
                } 
                catch (Exception $e) 
                {
                    logRequests($asunto,$Plantilla,json_encode($obj), json_encode($response), "ENVIO DE EMAIL", "", $aMails);
                    return "Error al enviar correo a $aMails <br>";
                    $mail->getSMTPInstance()->reset();
                }
    
                $mail->clearAddresses();
                $mail->clearAttachments();


        }

        

    }
?>

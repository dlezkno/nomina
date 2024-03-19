<?php 
    $lcFiltro = "";
	require_once('views/templates/header.php');
	require_once('views/templates/sideBar.php');
?>


<div id="main">
	<?php if ( isset($data['mensajeError']) ): ?>
		<?php if ( $data['mensajeError'] ): ?>
			<div class="card-content red white-text z-depth-2">
				<div class="row" id="mensajeError">
					<div class="col s12">
						<h5 class="white-text">
							<strong>Advertencia!</strong> Se han encontrado algunas inconsistencias, por favor valídelas:
						</h5>
						<br>
						<h5 class="white-text">
						<?= $data['mensajeError'] ?>
						</h5>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>


    <div class="container">
        <div class="section">
            <div class="card">
                <div class="card-content white-text z-depth-2" style="background-color:#1b2140">
                    <div class="row">
                        <div class="col s12 m6 l6">
                            <h3 class="white-text">Solicitud de personal (Beta)</h3>
                        </div>
                        
                    </div>
                </div>

                <div class="card-content">
                    <div class="row">

                        <div class="col s12">
                            <?php if ( count($data['rows']) > 0 ): ?>
                                <table id="TablaSolicitudPersonal" class="display nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <?php

                                                $head = isset($data["psicologos"]) ? '<th>OPCIONES</th><th>ASIGNAR PSICOLOGO</th>' : '<th>OPCIONES</th><th>CAMBIAR ESTADO DE SOLICITUD</th>';

                                                foreach ($data['rows'][0] as $key => $val) {
                                                    $head .= $key == 'idpsicologo' ? '<th>psicologo</th>' : ( $key == 'idusuario' || $key == 'id' || $key == 'idestado' ? '' : '<th>'.str_replace("_"," ",$key).'</th>');
                                                }
                                                echo $head;
                                            ?>                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $body = '';
                                            for ($i = 0; $i < count($data['rows']); $i++){
                                                $item = $data['rows'][$i];                                                
                                                $body .= '<tr><td><a class="waves-effect btn modal-trigger mb-2 mr-1" style="background-color: #006064" href="#modal_'.$item['id'].'">VER</a></td>';
                                                $body .=  isset($data["psicologos"]) ? '<td><select name="psicologo_'.$item['id'].'_'.$item['idusuario'].'_'.str_replace(" ","|",$item['cargo']).'">'.$data["psicologos"].'</select></td>' : ($_SESSION['Login']["Id"] == $item['idpsicologo'] ? '<td><select name="cambioestado_'.$item['id'].'_'.$item['idusuario'].'_'.str_replace(" ","|",$item['cargo']).'">'.getSelect('EstadoSolicitud', $item['idestado'], '', 'PARAMETROS.Valor').'</select></td>' : '<td></td>');
                                                $nombrepsicologo =  $item["idpsicologo"] != "" && $item["idpsicologo"] != "0" ? getRegistro("USUARIOS",$item["idpsicologo"])['nombre'] : '';
                                                foreach ($item as $key => $val) {                                                    
                                                    $body .=  $key == 'id' || $key == 'idusuario'  || $key == 'idestado' ? '' :( $key == 'idpsicologo' ? '<td>'.$nombrepsicologo.'</td>'  : '<td>'.$val.'</td>');
                                                }
                                                $body .= '</tr>'; 
                                            }
                                            echo $body;
                                        ?>
                                    </tbody>
                                </table>
                                
                            <?php else: ?>
                                <h3>NO SE TIENENE SOLICITUDES PENDIENTES</h3>
                            <?php endif; ?>
                        </div>
                        

                    </div>
                </div>
            </div>
        </div>
    </div>
    


    




</div>




<!-- Modal Example -->

<?php for ($i = 0; $i < count($data['rows']); $i++): ?>
    <?php $item = $data['rows'][$i]; ?>
    <div id="modal_<?php  echo $item['id']; ?>" class="modal modal-fixed-footer">
    
        <div class="modal-content">
            <h4 style="text-align: center;border-bottom: solid 1px black;padding: 1rem;">Solicitud</h4>
            <div class="row">
            <?php  foreach ($item as $key => $val): 
                $nombre = $key == 'idpsicologo' && $val != "" && $val != "0" ? getRegistro("USUARIOS",$item["idpsicologo"])['nombre'] : $val;    
            ?>
                <?php if ( $key != 'id' && $key != 'idusuario'  && $key != 'idestado' ): ?>
                    <div class="col s12 m6 l6">
                        <h6><strong><?php echo mb_convert_case(str_replace("_"," ",$key), MB_CASE_UPPER, "UTF-8"); ?>:</strong></h6>
                        <p><?php echo  mb_convert_case($nombre, MB_CASE_LOWER, "UTF-8"); ?></p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>                
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat ">Cerrar</a>
        </div>        
        
    </div>
<?php endfor; ?>





                                       




<?php require_once('views/templates/footer.php'); ?>

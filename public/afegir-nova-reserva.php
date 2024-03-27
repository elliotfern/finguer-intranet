<?php
require_once('inc/header.php');

if (isset($_GET['idReserva'])) {
    $idReserva_old = filter_input(INPUT_GET, 'idReserva', FILTER_SANITIZE_NUMBER_INT);
    
    if ( filter_var($idReserva_old, FILTER_VALIDATE_INT) ) {
        $codi_resposta = 2;

            echo "<h2>Afegir nova reserva al sistema</h2>";
            echo '<h3>Reserva núm. '.$idReserva_old.'</h3>';

            function data_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
              }
          
              if (isset($_POST["add-reserva"])) {
                global $pdo_conn; 
           
                $sql3 = "SELECT p.ID AS 'order', 
                MAX( CASE WHEN pm2.meta_key = '_billing_first_name' THEN pm2.meta_value END ) AS 'clientNom',
                MAX( CASE WHEN pm2.meta_key = '_billing_last_name' THEN pm2.meta_value END ) AS 'clientCognom',
                MAX( CASE WHEN pm2.meta_key = '_billing_phone' THEN pm2.meta_value END ) AS 'telefono',
                MAX( CASE WHEN pm.meta_key = '_booking_end' THEN pm.meta_value END ) AS 'DataSortida',
                MAX( CASE WHEN pm.meta_key = '_booking_start' THEN pm.meta_value END ) AS 'dataEntrada',
                MAX( CASE WHEN pm.meta_key = '_booking_resource_id' THEN pm.meta_value END ) AS 'tipo',
                MAX( CASE WHEN pm2.meta_key = 'additional_horario_entrada' THEN pm2.meta_value END ) AS 'HoraEntrada',
                MAX( CASE WHEN pm2.meta_key = 'additional_horario_salida' THEN pm2.meta_value END ) AS 'HoraSortida',
                MAX( CASE WHEN pm2.meta_key = 'additional_matricula' THEN pm2.meta_value END ) AS 'matricula',
                MAX( CASE WHEN pm2.meta_key = 'additional_modelo' THEN pm2.meta_value END ) AS 'modelo',
                MAX( CASE WHEN pm2.meta_key = 'additional_vuelo' THEN pm2.meta_value END ) AS 'vuelo',
                ( SELECT GROUP_CONCAT(CONCAT(i.order_item_name) )
                FROM wpfin_woocommerce_order_items i
                left join wpfin_woocommerce_order_itemmeta m ON i.order_item_id = m.order_item_id AND meta_key = '_qty'
                WHERE i.order_id = p.ID AND i.order_item_type = 'line_item') AS 'Limpieza'
                FROM wpfin_posts AS p
                left JOIN wpfin_postmeta AS pm2 ON p.ID = pm2.post_id
                left JOIN wpfin_posts AS p2 ON p.ID = p2.post_parent
                left JOIN wpfin_postmeta AS pm ON p2.ID = pm.post_id
                left JOIN wpfin_postmeta AS pm3 ON p2.ID = pm3.post_id
                left JOIN wpfin_woocommerce_order_items AS oi ON p.ID = oi.order_id
                WHERE pm3.meta_key='_booking_start' AND p.ID=$idReserva_old";

                global $pdo_conn;
                $pdo_statement = $pdo_conn->prepare($sql3);
                $pdo_statement->execute();
                $result3 = $pdo_statement->fetchAll();
                foreach($result3 as $row) {
                    $matricula1 = $row['matricula'];
                    $modelo1 = $row['modelo'];
                    $vuelo1 = $row['vuelo'];

                    $horaEntrada = $row['HoraEntrada'];
                    $horaEntrada2 = substr_replace($horaEntrada,':',-2,-2); //huevxos 0030

                    $horaSortida = $row['HoraSortida'];
                    $horaSortida2 = substr_replace($horaSortida,':',-2,-2); //huevxos 0030

                    $dataEntrada = $row['dataEntrada'];
                    $dataEntrada2 = substr_replace($dataEntrada, '', -6);
                    $dataEntrada4 = date("Y-m-d", strtotime($dataEntrada2));

                    $dataSortida = $row['DataSortida'];
                    $dataSortida2 = substr_replace($dataSortida, '', -6);
                    $dataSortida4 = date("Y-m-d", strtotime($dataSortida2));

                    $tipo = $row['tipo'];
                    if ($tipo == 1466) {
                        $tipoReserva2 = 1;
                    } elseif ($tipo == 1344) {
                        $tipoReserva2 = 1;
                    } elseif ($tipo == 1467) {
                        $tipoReserva2 = 2;
                    } elseif ($tipo == 1345) {
                        $tipoReserva2 = 2;
                    }
                    $limpieza = $row['Limpieza'];
                    $string0 = "Tu reserva";
                    $string1 = "Tu reserva,Servicio de limpieza exterior";
                    $string2 = "Tu reserva,Servicio de lavado exterior + aspirado tapicería interior";
                    $string3 = "Tu reserva,Limpieza PRO";
                    if (strpos($string0, $limpieza) !== false) {
                        $limpieza2 = 0;
                    } elseif (strpos($string1, $limpieza) !== false) {
                        $limpieza2 = 1;
                    } elseif (strpos($string2, $limpieza) !== false) {
                        $limpieza2 = 2;
                    } elseif (strpos($string3, $limpieza) !== false) {
                        $limpieza2 = 3;
                    } else {
                        $limpieza2 = 0;
                    }
                $clientNom = $row['clientNom'];
                $clientCognom = $row['clientCognom'];
                $telefono = $row['telefono'];
                }
         
               // Si no hi ha cap error, envia el formulari
                // 5) Enregistra les dades a la taula reservas_completas
        if (!empty($idReserva_old)) {
            $checkIn = 5;
            $checkOut = NULL;
            $notes = NULL;
            $buscadores = NULL;
            $tipo = $tipoReserva2;
            $firstName = $clientNom;
            $lastName = $clientCognom;
            $tel = $telefono;
            $horaEntrada = $horaEntrada2;
            $diaEntrada = $dataEntrada4;
            $horaSalida = $horaSortida2;
            $diaSalida = $dataSortida4;
            $vehiculo = $modelo1;
            $matricula = $matricula1;
            $vuelo = $vuelo1;
            $limpieza = $limpieza2;
    
            // Si no hi ha cap error, envia el formulari
            if (!isset($hasError)) {
                $emailSent = true;
    
            } else { // Error > bloqueja i mostra avis
                echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error!</h4></strong>';
                echo 'Controla que totes les dades siguin correctes.</div>';
            } 
    
                global $pdo_conn;
                $sql = "INSERT INTO reserves_parking SET idReserva=:idReserva, checkIn=:checkIn, checkOut=:checkOut, notes=:notes, buscadores=:buscadores, tipo=:tipo, firstName=:firstName, lastName=:lastName, tel=:tel, horaEntrada=:horaEntrada, diaEntrada=:diaEntrada, horaSalida=:horaSalida, diaSalida=:diaSalida, vehiculo=:vehiculo, matricula=:matricula, vuelo=:vuelo, limpieza=:limpieza";
                $stmt = $pdo_conn->prepare($sql);
                $stmt->bindParam(":idReserva", $idReserva_old, PDO::PARAM_INT);
                $stmt->bindParam(":checkIn", $checkIn, PDO::PARAM_INT);
                $stmt->bindParam(":checkOut", $checkOut, PDO::PARAM_NULL);
                $stmt->bindParam(":notes", $notes, PDO::PARAM_NULL);
                $stmt->bindParam(":buscadores", $buscadores, PDO::PARAM_NULL);
                $stmt->bindParam(":tipo", $tipo, PDO::PARAM_STR);
                $stmt->bindParam(":firstName", $firstName, PDO::PARAM_STR);
                $stmt->bindParam(":lastName", $lastName, PDO::PARAM_STR);
                $stmt->bindParam(":tel", $tel, PDO::PARAM_STR);
                $stmt->bindParam(":horaEntrada", $horaEntrada, PDO::PARAM_STR);
                $stmt->bindParam(":diaEntrada", $diaEntrada, PDO::PARAM_STR);
                $stmt->bindParam(":horaSalida", $horaSalida, PDO::PARAM_STR);
                $stmt->bindParam(":diaSalida", $diaSalida, PDO::PARAM_STR);
                $stmt->bindParam(":vehiculo", $vehiculo, PDO::PARAM_STR);
                $stmt->bindParam(":matricula", $matricula, PDO::PARAM_STR);
                $stmt->bindParam(":vuelo", $vuelo, PDO::PARAM_STR);
                $stmt->bindParam(":limpieza", $limpieza, PDO::PARAM_INT);
            
                if ($stmt->execute()) {
                    $codi_resposta = 1;
                } else {
                    $codi_resposta = 2;
                }

                echo "".$codi_resposta."";
                
                } else {
                    //nothing
                }
          
                    if ($codi_resposta == 1)  {
                    echo '<div class="alert alert-success" role="alert"><h4 class="alert-heading"><strong>Reserva afegida al sistema correctament.</h4></strong>';
                    echo "El número de reserva és ".$idReserva_old."</div>";
                    } else { // Error > bloqueja i mostra avis
                        echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error en la transmissió de les dades</h4></strong>';
                        echo 'Les dades no s\'han transmès correctament.</div>';
                    }
                }
            
                if ($codi_resposta == 2) { 
                    echo '<form action="" method="post" id="update-matricula" class="row g-3" style="background-color:#BDBDBD;padding:25px;margin-top:10px">';

                    echo '<div class="col-md-4">';
                    echo '<label>Vols afegir el número de reserva al sistema?</label>';
                    echo "<p><h5>".$idReserva_old."</h5>";
                    echo '</div>';
        
                    echo "<div class='md-12'>";
                    echo "<button id='add-reserva' name='add-reserva' type='submit' class='btn btn-primary'>Afegir reserva</button><a href='afegir-nova-reserva.php'></a>
                    </div>";
        
                    echo "</form>";
                } else {
                    echo '<a href="index.php" class="btn btn-dark menuBtn" role="button" aria-disabled="false">Tornar</a>';
                }
           
        
    } else {
        echo "Error: aquest ID no és vàlid";
    }
} else {
    echo "Error. No has seleccionat cap vehicle.";
}

require_once('inc/footer.php');
?>


<?php

// connexio a la base de dades

// 1) primer comprova quantes reserves pendents hi ha i extreu el numero:
$sql1 = "SELECT COUNT(p.ID) AS numero
FROM wpfin_posts as p
WHERE NOT EXISTS (SELECT c.idReserva
                   FROM reserves_parking AS c
                   WHERE p.ID = c.idReserva)
AND p.post_type='shop_order' AND p.post_status IN ('wc-completed', 'wc-on-hold')";

global $pdo_conn;
$pdo_statement = $pdo_conn->prepare($sql1);
$pdo_statement->execute();
$result = $pdo_statement->fetchAll();
foreach($result as $row) {
    $numero = $row['numero'];
}

// 2) si hi ha reserves per incorporar al sistema, activa el loop while.. Si no hi ha res, mostra un missatge
if ($numero >= 1) {
    
    // 3) comença el loop while... en funció del numero total de reserves:
        $sql2 = "SELECT p.ID, p.post_date
        FROM wpfin_posts as p
        WHERE NOT EXISTS (
            SELECT c.idReserva
            FROM reserves_parking AS c
            WHERE p.ID = c.idReserva
        )
        AND NOT EXISTS (
            SELECT c.idReserva
            FROM reserves_parking AS c
            WHERE p.ID = c.idReserva AND c.processed = 1
        )
        AND p.post_type='shop_order' AND p.post_status IN ('wc-completed', 'wc-on-hold')";

    $pdo_statement = $pdo_conn->prepare($sql2);
    $pdo_statement->execute();
    $result2 = $pdo_statement->fetchAll();

    // Itera sobre las reservas obtenidas
    foreach ($result2 as $row) {
        $idReserva_old = $row['ID'];

        // connexio a la API REST FULL Woocommerce (no es pot treure informacio de les reserves)

        // URL de la API de WooCommerce y ID del pedido
        $api_url = 'https://finguer.com/wp-json/wc/v3/orders/' . $idReserva_old;
        $consumer_key = 'ck_41bd6fded46162f87b9713ecbe8b879ad10b6a0e';
        $consumer_secret = 'cs_b2189c1579bb1ed428df3385a85b2f1b0cb416d2';

        // Construir la URL completa con las credenciales
        $url = $api_url . '?consumer_key=' . $consumer_key . '&consumer_secret=' . $consumer_secret;

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar las opciones de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Realizar la solicitud GET
        $response = curl_exec($ch);

        // Verificar si hay errores
        if (curl_errno($ch)) {
            echo 'Error al realizar la solicitud: ' . curl_error($ch);
        } else {
            // Decodificar la respuesta JSON
            $order_details = json_decode($response, true);

            // Obtener información específica del servicio de lavado
            $allowed_product_ids = [1705, 1704, 1694];
            $filtered_items = array_filter($order_details['line_items'], function ($item) use ($allowed_product_ids) {
                return in_array($item['product_id'], $allowed_product_ids);
            });

            if (!empty($filtered_items)) {
                foreach ($filtered_items as $item) {
                    $neteja_id = $item['product_id'];
                }
            }
        }

        // Cerrar la sesión cURL
        curl_close($ch);

        // 5) Extreu totes les dades relatives a la reserva
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
            MAX( CASE WHEN pm2.meta_key = 'additional_vuelo' THEN pm2.meta_value END ) AS 'vuelo'
            FROM wpfin_posts AS p
            left JOIN wpfin_postmeta AS pm2 ON p.ID = pm2.post_id
            left JOIN wpfin_posts AS p2 ON p.ID = p2.post_parent
            left JOIN wpfin_postmeta AS pm ON p2.ID = pm.post_id
            left JOIN wpfin_postmeta AS pm3 ON p2.ID = pm3.post_id
            left JOIN wpfin_woocommerce_order_items AS oi ON p.ID = oi.order_id
            WHERE pm3.meta_key='_booking_start' AND p.ID=$idReserva_old";

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

            if (isset($neteja_id)) {
                if ($neteja_id == 1704) {
                    $limpieza2 = 1;
                } elseif ($neteja_id == 1705) {
                    $limpieza2 = 2;
                } elseif ($neteja_id == 1694) {
                    $limpieza2 = 3;
                } else {
                    $limpieza2 = 0;
                }
            }
            $clientNom = $row['clientNom'];
            $clientCognom = $row['clientCognom'];
            $telefono = $row['telefono'];
        }

        // 6) Enregistra les dades a la taula reservas_completas
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
            $processed = 1;
            if (isset($limpieza2)) {
                $limpieza = $limpieza2;
            } else {
                $limpieza = 0;
            }

            $sql5 = "INSERT INTO reserves_parking SET idReserva=:idReserva, checkIn=:checkIn, checkOut=:checkOut, notes=:notes, buscadores=:buscadores, tipo=:tipo, firstName=:firstName, lastName=:lastName, tel=:tel, horaEntrada=:horaEntrada, diaEntrada=:diaEntrada, horaSalida=:horaSalida, diaSalida=:diaSalida, vehiculo=:vehiculo, matricula=:matricula, vuelo=:vuelo, limpieza=:limpieza, processed=:processed";
            
            $stmt = $pdo_conn->prepare($sql5);
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
            $stmt->bindParam(":processed", $processed, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $codi_resposta = 1;
            } else {
                $codi_resposta = 2;
            }

            if ($codi_resposta == 1)  {
                echo '<div class="alert alert-success" role="alert" id="missatgeNo"><h4 class="alert-heading"><strong>Nova reserva incorporada al sistema!</h4></strong>';
                echo "La reserva ".$idReserva_old." s'ha incoporat correctament.</div>";
            } else { // Error > bloqueja i mostra avis
                echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error en la transmissió de les dades</h4></strong>';
                echo 'Les dades no s\'han transmès correctament.</div>';
            }
        } else {
            //nothing
        }
    } // final loop foreach
} else {
    echo '<div class="alert alert-danger" role="alert" id="missatgeNo"><h6 class="alert-heading"><strong>No hi ha noves reserves per afegir a la intranet</h6></strong></div>';
}
?>
<script>
    setTimeout(function(){
        document.getElementById('missatgeNo').style.display = 'none';
    }, 3500); // 3500ms = 3.5s
</script>

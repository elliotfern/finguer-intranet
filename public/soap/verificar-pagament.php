<?php

use RedsysConsultasPHP\Client\Client;

global $conn;

$id = $params['id'];

if (is_numeric($id)) {
    $id_old = intval($id);
    
    if ( filter_var($id_old, FILTER_VALIDATE_INT) ) {
        $codi_resposta = 2;

        // consulta general reserves 
        $sql = "SELECT r.idReserva
        FROM reserves_parking AS r
        WHERE r.id = $id_old";

        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetchAll();
        
        foreach($result as $row) {
            $idReserva = $row['idReserva'];
        }

        echo "<div class='container'>
        <h2>Verificar el pagament de la reserva: ".$idReserva."</h2>";

        $token = $_ENV['MERCHANTCODE'];
        $token2 = $_ENV['KEY'];
        $token3 = $_ENV['TERMINAL'];
        $url_Ok = $_ENV['URLOK'];
        $url_Ko = $_ENV['URLKO'];
        $url = 'https://finguer.com/compra-realizada';

        $url = 'https://sis.redsys.es/apl02/services/SerClsWSConsulta';
        $client = new Client($url, $token2);

        $order = $idReserva;
        $terminal = '1';
        $merchant_code = $token;
        $response = $client->getTransaction($order, $terminal, $merchant_code);

        // Supongamos que $response contiene el objeto Transaction
        // Acceder a las propiedades protegidas mediante reflexión

        // Función para obtener el valor de una propiedad protegida de un objeto
        function getProtectedPropertyValue($object, $propertyName) {
            $reflection = new ReflectionClass($object);
            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);
            return $property->getValue($object);
        }

        // Acceder al valor de Ds_Response
        $ds_response = getProtectedPropertyValue($response, 'Ds_Response');

        // Verificar el valor de Ds_Response
        if ($ds_response === '9218') {
            echo "<div class='alert alert-danger text-center' role='alert'></p>
            <p><img src='".APP_WEB."/inc/img/warning.png' alt='Pagament Error'></p>
            <p><strong>Pagament fallit</strong>.
            </div>";
        } elseif ($ds_response === '0000') {
            echo "<div class='alert alert-success text-center' role='alert'>
            <p><img src='".APP_WEB."/inc/img/correct.png' alt='Pagament OK'></p>
            <p><strong>Pagament verificat correctament amb RedSys.</strong></p>
            </div>";

            // Ara camviem l'estat del pagament a la base de dades

            $processed = 1;

            $sql = "UPDATE reserves_parking SET processed=:processed
            WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":processed", $processed, PDO::PARAM_INT);
            $stmt->bindParam(":id", $id_old, PDO::PARAM_INT);
            $stmt->execute();

        } else {
            echo "<div class='alert alert-danger text-center' role='alert'>
            <p><img src='".APP_WEB."/inc/img/warning.png' alt='Pagament Error'></p>
            <p><strong>No s'ha pogut verificar aquest pagament. Pagament fallit o denegat amb RedSys.</strong></p>
            </div>";
        }

    } else {
        echo "Error: aquest ID no és vàlid";
    }
} else {
    echo "Error. No has seleccionat cap reserva.";
}

echo '<a href="'.APP_WEB.'/inici" class="btn btn-dark menuBtn" role="button" aria-disabled="false">Tornar</a>';
echo "</div>";

require_once(APP_ROOT . '/public/inc/footer.php');
?>
<?php
require_once('inc/header.php');

if (isset($_GET['id'])) {
    $id_old = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    if ( filter_var($id_old, FILTER_VALIDATE_INT) ) {
        $codi_resposta = 2;

        // consulta general reserves 
        $sql = "SELECT r.checkIn, r.idReserva
        FROM reserves_parking AS r
        WHERE r.id=$id_old";

        $pdo_statement = $pdo_conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetchAll();
        foreach($result as $row) {
            $checkIn_old = $row['checkIn'];
            $idReserva_old = $row['idReserva'];
        }

        if ($checkIn_old == 1) {
            $estat = "Reserva al parking pendent de check-Out";
        }

        if ($idReserva_old == 1) {
            echo "<h2>Fer Check-Out de reserva client anual amb ID: ".$id_old."</h2>";
        } else {
            echo "<h2>Fer Check-Out de reserva núm: ".$idReserva_old." </h2>";
            echo '<h4>Estat actual: '.$estat.'</h4>';
        }

            function data_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
              }
          
              if (isset($_POST["add-checkout"])) {
                global $pdo_conn;
                  
                $checkOut = 2;
                $checkIn = 3;
                  
               // Si no hi ha cap error, envia el formulari
                if (!isset($hasError)) {
                    $emailSent = true;
          
                } else { // Error > bloqueja i mostra avis
                    echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error!</h4></strong>';
                    echo 'Controla que totes les dades siguin correctes.</div>';
                } 
          
                    global $pdo_conn;
                    $sql = "UPDATE reserves_parking SET checkOut=:checkOut, checkIn=:checkIn
                    WHERE id=:id";
                    $stmt = $pdo_conn->prepare($sql);
                    $stmt->bindParam(":checkOut", $checkOut, PDO::PARAM_INT);
                    $stmt->bindParam(":checkIn", $checkIn, PDO::PARAM_INT);
                    $stmt->bindParam(":id", $id_old, PDO::PARAM_INT);
                    
                    if ($stmt->execute()) {
                        $codi_resposta = 1;
                    } else {
                        $codi_resposta = 2;
                    }
          
                    if ($codi_resposta == 1)  {
                    echo '<div class="alert alert-success" role="alert"><h4 class="alert-heading"><strong>Check-Out enregistrat correctament.</h4></strong>';
                    echo "Check-Out realizat.</div>";
                    } else { // Error > bloqueja i mostra avis
                        echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error en la transmissió de les dades</h4></strong>';
                        echo 'Les dades no s\'han transmès correctament.</div>';
                    }
                }
            
                if ($codi_resposta == 2) { 
                    echo '<form action="" method="post" id="checkout" class="row g-3" style="background-color:#BDBDBD;padding:25px;margin-top:10px">';

                    echo '<input type="hidden" id="checkOut" name="checkOut" value="2">';
                    echo '<input type="hidden" id="checkIn" name="checkIn" value="3">';

                    echo "<p>Estàs segur que vols fer el CHECK-OUT d'aquesta reserva i marcar-la com a completada?</p>";
        
                    echo "<div class='md-12'>";
                    echo "<button id='add-checkout' name='add-checkout' type='submit' class='btn btn-primary'>Fer check-Out</button><a href='fer-checkout.php'></a>
                    </div>";
        
                    echo "</form>";
                } else {
                    if ($idReserva_old == 1) {
                        echo '<a href="reserves-anuals-estat-parking.php" class="btn btn-dark menuBtn" role="button" aria-disabled="false">Tornar</a>';
                    } else {
                        echo '<a href="index.php" class="btn btn-dark menuBtn" role="button" aria-disabled="false">Tornar</a>';
                    }
                }
           
        
    } else {
        echo "Error: aquest ID no és vàlid";
    }
} else {
    echo "Error. No has seleccionat cap vehicle.";
}

require_once('inc/footer.php');
?>


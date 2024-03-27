<?php
require_once('inc/header.php');

/*
AQUESTA PÀGINA SERVEIX PER MODIFICAR EL NOM DEL CLIENT
UPDATE A LA TAULA: reserves_parking
COLUMNA: firstName, lastName
*/

if (isset($_GET['id'])) {
    $id_old = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    if ( filter_var($id_old, FILTER_VALIDATE_INT) ) {
        $codi_resposta = 2;

        // consulta general reserves 
        $sql = "SELECT r.idReserva, r.firstName, r.lastName
        FROM reserves_parking AS r
        WHERE r.id = $id_old";

        $pdo_statement = $pdo_conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetchAll();
        foreach($result as $row) {
            $idReserva_old = $row['idReserva'];
            $firstName_old = $row['firstName'];
            $lastName_old = $row['lastName'];
        }
            echo "<h2>Canvi nom del client</h2>";
            echo "<h3>Client: ".$firstName_old." ".$lastName_old." </h3>";

            function data_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
              }
          
              if (isset($_POST["update-client"])) {
                global $pdo_conn;
          
                  if (empty($_POST["firstName"])) {
                    $firstName = data_input($_POST["firstName"], ENT_NOQUOTES);
                  } else {
                    $firstName = data_input($_POST["firstName"], ENT_NOQUOTES);
                  }

                  if (empty($_POST["lastName"])) {
                    $lastName = data_input($_POST["lastName"], ENT_NOQUOTES);
                  } else {
                    $lastName = data_input($_POST["lastName"], ENT_NOQUOTES);
                  }
          
               // Si no hi ha cap error, envia el formulari
                if (!isset($hasError)) {
                    $emailSent = true;
          
                } else { // Error > bloqueja i mostra avis
                    echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error!</h4></strong>';
                    echo 'Controla que totes les dades siguin correctes.</div>';
                } 
          
                    global $pdo_conn;
                    $sql = "UPDATE reserves_parking SET lastName=:lastName, firstName=:firstName
                    WHERE id=:id";
                    $stmt = $pdo_conn->prepare($sql);
                    $stmt->bindParam(":firstName", $firstName, PDO::PARAM_STR);
                    $stmt->bindParam(":lastName", $lastName, PDO::PARAM_STR);
                    $stmt->bindParam(":id", $id_old, PDO::PARAM_INT);
                    
                    if ($stmt->execute()) {
                        $codi_resposta = 1;

                        if ($idReserva_old == 1) {
                            $to = "hello@finguer.com";
                            $subject = "Modificacio nom client";
                            $message = "Avis de modificacio del nom de client ABONAMENT ANUAL";
                            $from = "hello@finguer.com";
                            $headers = "De:" . $from;

                            mail($to,$subject,$message,$headers);
                            
                        } else {
                            $to = "hello@finguer.com";
                            $subject = "Modificacio nom client";
                            $message = "Avis de modificacio del nom de client: ".$firstName_old." ".$lastName_old." associat al num. de comanda: ".$idReserva_old;
                            $from = "hello@finguer.com";
                            $headers = "De:" . $from;

                            mail($to,$subject,$message,$headers);
                        }


                    } else {
                        $codi_resposta = 2;
                    }
          
                    if ($codi_resposta == 1)  {
                    echo '<div class="alert alert-success" role="alert"><h4 class="alert-heading"><strong>Dades processades correctament!</h4></strong>';
                    echo "L'actualització s'ha realitzat correctament</div>";
                    } else { // Error > bloqueja i mostra avis
                        echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error en la transmissió de les dades</h4></strong>';
                        echo 'Les dades no s\'han transmès correctament.</div>';
                    }
                }
            
                if ($codi_resposta == 2) { 
                    echo '<form action="" method="post" id="update-client" class="row g-3" style="background-color:#BDBDBD;padding:25px;margin-top:10px">';

                    echo '<div class="col-md-4">';
                    echo '<label>Nom client:</label>';
                    echo '<input type="text" class="form-control" id="firstName" name="firstName" value="'.$firstName_old.'">';
                    echo '</div>';
                    
                    echo '<div class="col-md-4">';
                    echo '<label>Cognoms client:</label>';
                    echo '<input type="text" class="form-control" id="lastName" name="lastName" value="'.$lastName_old.'">';
                    echo '</div>';
        
                    echo "<div class='md-12'>";
                    echo "<button id='update-client' name='update-client' type='submit' class='btn btn-primary'>Actualizar</button><a href='canvi-nom-client.php'></a>
                    </div>";
        
                    echo "</form>";
                } else {
                    echo '<a href="index.php" class="btn btn-dark menuBtn" role="button" aria-disabled="false">Tornar</a>';
                }
           
        
    } else {
        echo "Error: aquest ID no és vàlid";
    }
} else {
    echo "Error. No has seleccionat cap reserva.";
}

require_once('inc/footer.php');
?>


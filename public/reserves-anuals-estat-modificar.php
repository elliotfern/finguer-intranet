<?php
require_once('inc/header.php');

if (isset($_GET['id'])) {
    $id_old = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    if ( filter_var($id_old, FILTER_VALIDATE_INT) ) {
        $codi_resposta = 2;

        // consulta general reserves 
        $sql = "SELECT r.checkIn, r.checkOut, r.idReserva
        FROM reserves_parking AS r
        WHERE r.id = $id_old";

        $pdo_statement = $pdo_conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetchAll();
        foreach($result as $row) {
            $checkIn_old = $row['checkIn'];
            $checkOut_old = $row['checkOut'];
            $idReserva_old = $row['idReserva'];
        }

        if ($checkOut_old == 2) {
            $estat = "Reserva completada";
        } else {
            $estat = "Reserva en parking";
        }

        if ($idReserva_old == 1) {
            echo "<h2>Modificació estat reserva amb ID núm: ".$id_old." </h2>";
            echo "<h4>Estat de la reserva: ".$estat." </h4>";
        } else {
            echo "<h2>Modificació estat reserva amb número: ".$idReserva_old." </h2>";
        } 
        
            function data_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
              }
          
              if (isset($_POST["update-estat"])) {
                global $pdo_conn;

                if (empty($_POST["checkIn"])) {
                    $checkIn = data_input($_POST["checkIn"], ENT_NOQUOTES);
                } else {
                    $checkIn = data_input($_POST["checkIn"], ENT_NOQUOTES);
                }

                /* reserva en parking
               $checkIn = 1;
               $checkOut = NULL;

               // reserva pendent
               $checkIn = 5;
               $checkOut = NULL;

               // reserva anul·lada
               $checkIn = NULL;
               $checkOut = NULL;
               */
                
              $checkOut = NULL;

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
                    echo '<div class="alert alert-success" role="alert"><h4 class="alert-heading"><strong>Estat de la reserva actualizat correctament.</h4></strong>';
                    echo "Reserva actualitzada.</div>";
                    } else { // Error > bloqueja i mostra avis
                        echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error en la transmissió de les dades</h4></strong>';
                        echo 'Les dades no s\'han transmès correctament.</div>';
                    }
                }
            
                if ($codi_resposta == 2) { 
                    echo '<form action="" method="post" id="update-estat" class="row g-3" style="background-color:#BDBDBD;padding:25px;margin-top:10px">';

                    echo "<h3>Vols canviar l'estat de la reserva?:</h3>";
              
                    echo '<div class="col-md-4">';
                    echo '<label>Estat reserva:</label>';
                    echo '<select class="form-select" name="checkIn" id="checkIn">';
                    echo '<option selected>Selecciona una opció:</option>';
                    echo "<option value='5'>Estat 1: reserva pendent</option>"; 
                    echo "<option value='1'>Estat 2: reserva en parking</option>";
                    echo "<option value='NULL'>Esborrar reserva</option>";
                    echo '</select>';
                    echo '</div>';
        
                    echo "<div class='md-12'>";
                    echo "<button id='update-estat' name='update-estat' type='submit' class='btn btn-primary'>Modificació estat reserva</button><a href='modificar-buscador.php'></a>
                    </div>";
        
                    echo "</form>";
                } else {
                    echo '<a href="reserves-anuals-index.php" class="btn btn-dark menuBtn" role="button" aria-disabled="false">Tornar</a>';
                }
           
        
    } else {
        echo "Error: aquest ID no és vàlid";
    }
} else {
    echo "Error. No has seleccionat cap reserva vàlida.";
}

require_once('inc/footer.php');
?>


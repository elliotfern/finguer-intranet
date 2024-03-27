<?php
require_once('inc/header.php');

if (isset($_GET['id'])) {
    $id_old = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    if ( filter_var($id_old, FILTER_VALIDATE_INT) ) {
        $codi_resposta = 2;

        // consulta general reserves 
        $sql = "SELECT r.notes, r.idReserva
        FROM reserves_parking AS r
        WHERE r.id = $id_old";

        $pdo_statement = $pdo_conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetchAll();
        foreach($result as $row) {
            $notes_old = $row['notes'];
            $idReserva_old = $row['idReserva'];
        }

        if ($idReserva_old == 1) {
            echo "<h2>Notes reserva client anual amb ID núm: ".$id_old." </h2>";
        } else {
            echo "<h2>Notes reserva número: ".$idReserva_old." </h2>";
        }    
               
            echo '<h4>Modificació nota</h4>';

            function data_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
              }
          
              if (isset($_POST["update-notes"])) {
                global $pdo_conn;
                  
                if (empty($_POST["notes"])) {
                    $notes = data_input($_POST["notes"], ENT_NOQUOTES);
                  } else {
                    $notes = data_input($_POST["notes"], ENT_NOQUOTES);
                  }
                  
               // Si no hi ha cap error, envia el formulari
                if (!isset($hasError)) {
                    $emailSent = true;
          
                } else { // Error > bloqueja i mostra avis
                    echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error!</h4></strong>';
                    echo 'Controla que totes les dades siguin correctes.</div>';
                } 
          
                    global $pdo_conn;
                    $sql = "UPDATE reserves_parking SET notes=:notes
                    WHERE id=:id";
                    $stmt = $pdo_conn->prepare($sql);
                    $stmt->bindParam(":notes", $notes, PDO::PARAM_STR);
                    $stmt->bindParam(":id", $id_old, PDO::PARAM_INT);
                    
                    if ($stmt->execute()) {
                        $codi_resposta = 1;
                    } else {
                        $codi_resposta = 2;
                    }
          
                    if ($codi_resposta == 1)  {
                    echo '<div class="alert alert-success" role="alert"><h4 class="alert-heading"><strong>Nota actualizada correctament.</h4></strong>';
                    echo "Nota actualizada.</div>";
                    } else { // Error > bloqueja i mostra avis
                        echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error en la transmissió de les dades</h4></strong>';
                        echo 'Les dades no s\'han transmès correctament.</div>';
                    }
                }
            
                if ($codi_resposta == 2) { 
                    echo '<form action="" method="post" id="update-notes" class="row g-3" style="background-color:#BDBDBD;padding:25px;margin-top:10px">';

                    echo '<div class="col-md-4">';
                    echo '<label>Nota reserva:</label>';
                    echo '<input type="text" class="form-control" id="notes" name="notes" value="'.$notes_old.'">';
                    echo '</div>';
        
                    echo "<div class='md-12'>";
                    echo "<button id='update-notes' name='update-notes' type='submit' class='btn btn-primary'>Modificar nota</button><a href='modificar-nota.php'></a>
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


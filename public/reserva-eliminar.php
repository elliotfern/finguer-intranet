<?php
require_once('inc/header.php');

echo "<div class='container'>";
echo "<h3>Eliminar reserva del sistema</h3>";

if (isset($_GET['id'])) {
    $id_old = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    if ( filter_var($id_old, FILTER_VALIDATE_INT) ) {
        $codi_resposta = 1;

        // consulta general reserves 
        $sql = "SELECT r.id, r.idReserva
        FROM reserves_parking AS r
        WHERE r.id=$id_old";

        $pdo_statement = $pdo_conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetchAll();
        foreach($result as $row) {
            $idReserva = $row['idReserva'];
        }
        
        echo "<h4>ID Reserva: ".$idReserva." </h4>";
        echo "<h5>Eliminació de la reserva</h5>";

        function data_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        if (isset($_POST["remove-reserva"])) {
                            $emailSent = true;

                            global $pdo_conn;
                            $sql = "DELETE FROM reserves_parking
                            WHERE id=:id";
                            $stmt = $pdo_conn->prepare($sql);
                            $stmt->bindParam(":id", $id_old, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $codi_resposta = 3;
            } else {
                $codi_resposta = 2;
            }
        }
                        
            if ($codi_resposta == 3)  {
                            echo '<div class="alert alert-success" role="alert"><h4 class="alert-heading"><strong>Eliminació realizada correctament.</h4></strong>';
                            echo "Eliminació de la reserva amb èxit.</div>";
            } elseif ($codi_resposta == 2)  {
                                echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error en la transmissió de les dades</h4></strong>';
                                echo 'Les dades no s\'han transmès correctament.</div>';
            }
                    
            if ($codi_resposta == 1) { 
                            echo '<form action="" method="post" id="remove-client" class="row g-3" style="background-color:#BDBDBD;padding:25px;margin-top:10px">';
                    
                            echo "<hr>";
                            echo "<h4>Estàs segur que vols eliminar aquesta reserva?</h4>";
                            echo '<form method="post" action="">';

                            echo "<div class='md-12'>";
                            echo "<button id='remove-reserva' name='remove-reserva' type='submit' class='btn btn-primary'>Eliminar reserva</button><a href='reserva-eliminar.php'></a>
                            </div>";

                            echo "</form>";
                
            } else {
                echo '<a href="index.php" class="btn btn-dark menuBtn" role="button" aria-disabled="false">Tornar</a>';
            }
        
                        
    } else {
        echo "Error: aquest ID no és vàlid";
    }
        
}

echo "</div>";
echo '</div>
 </div>';

require_once('inc/footer.php');
?>


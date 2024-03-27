<?php
require_once('inc/header.php');
require_once('inc/header-reserves-anuals.php');

echo "<h3>Modificar dades client Abonament anual</h3>";

if (isset($_GET['idClient'])) {
    $idClient_old = filter_input(INPUT_GET, 'idClient', FILTER_SANITIZE_NUMBER_INT);
    
    if ( filter_var($idClient_old, FILTER_VALIDATE_INT) ) {
        $codi_resposta = 1;

        // consulta general reserves 
        $sql = "SELECT c.nombre
        FROM usuaris AS c
        WHERE c.id=$idClient_old";

        $pdo_statement = $pdo_conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetchAll();
        foreach($result as $row) {
            $nom_old = $row['nombre'];
        }
        
        echo "<h4>Client: ".$nom_old." </h4>";
        echo "<h5>Eliminació del client</h5>";

        function data_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        if (isset($_POST["remove-client"])) {
                            $emailSent = true;

                            global $pdo_conn;
                            $sql = "DELETE FROM usuaris
                            WHERE id=:id";
                            $stmt = $pdo_conn->prepare($sql);
                            $stmt->bindParam(":id", $idClient_old, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $codi_resposta = 3;
            } else {
                $codi_resposta = 2;
            }
        }
                        
            if ($codi_resposta == 3)  {
                            echo '<div class="alert alert-success" role="alert"><h4 class="alert-heading"><strong>Eliminació realizada correctament.</h4></strong>';
                            echo "Eliminació del client anual amb èxit.</div>";
            } elseif ($codi_resposta == 2)  {
                                echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error en la transmissió de les dades</h4></strong>';
                                echo 'Les dades no s\'han transmès correctament.</div>';
            }
                    
            if ($codi_resposta == 1) { 
                            echo '<form action="" method="post" id="remove-client" class="row g-3" style="background-color:#BDBDBD;padding:25px;margin-top:10px">';
                    
                            echo "<hr>";
                            echo "<h4>Estàs segur que vols eliminar aquest client?</h4>";
                            echo '<form method="post" action="">';

                            echo "<div class='md-12'>";
                            echo "<button id='remove-client' name='remove-client' type='submit' class='btn btn-primary'>Eliminar client</button><a href='reserves-anuals-eliminar-client.php'></a>
                            </div>";

                            echo "</form>";
                
            } else {
                echo '<a href="reserves-anuals-index.php" class="btn btn-dark menuBtn" role="button" aria-disabled="false">Tornar</a>';
            }
        
                        
    } else {
        echo "Error: aquest ID no és vàlid";
    }
        
}

echo '</div>
 </div>';

require_once('inc/footer.php');
?>


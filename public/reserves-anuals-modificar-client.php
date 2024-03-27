<?php
require_once('inc/header.php');
require_once('inc/header-reserves-anuals.php');

echo "<h3>Modificar dades client Abonament anual</h3>";

if (isset($_GET['idClient'])) {
    $idClient_old = filter_input(INPUT_GET, 'idClient', FILTER_SANITIZE_NUMBER_INT);
    
    if ( filter_var($idClient_old, FILTER_VALIDATE_INT) ) {
        $codi_resposta = 2;

        // consulta general reserves 
        $sql = "SELECT c.nombre AS nom, c.telefono AS telefon, c.id, c.anualitat
        FROM usuaris AS c
        WHERE c.id=$idClient_old";

        $pdo_statement = $pdo_conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetchAll();
        foreach($result as $row) {
            $nom_old = $row['nom'];
            $telefon_old = $row['telefon'];
            $anualitat_old = $row['anualitat'];
        }
    echo "<h4>Client: ".$nom_old." </h4>";


function data_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
$codi_resposta = 2;

              if (isset($_POST["update-client"])) {
                global $pdo_conn;
                  
                if (empty($_POST["nombre"])) {
                    $hasError = true;
                } else {
                    $nombre = data_input($_POST["nombre"], ENT_NOQUOTES);
                }
                

                if (empty($_POST["telefono"])) {
                    $telefono = data_input($_POST["telefono"], ENT_NOQUOTES);
                } else {
                    $telefono = data_input($_POST["telefono"], ENT_NOQUOTES);
                }

                if (empty($_POST["anualitat"])) {
                    $anualitat = NULL;
                } else {
                    $anualitat = data_input($_POST["anualitat"], ENT_NOQUOTES);
                }

               // Si no hi ha cap error, envia el formulari
                if (!isset($hasError)) {
                    $emailSent = true;

                    global $pdo_conn;
                    $sql = "UPDATE usuaris SET nombre=:nombre, telefono=:telefono, anualitat=:anualitat
                    WHERE id=:id";
                    $stmt = $pdo_conn->prepare($sql);
                    $stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
                    $stmt->bindParam(":telefono", $telefono, PDO::PARAM_STR);
                    $stmt->bindParam(":anualitat", $anualitat, PDO::PARAM_STR);
                    $stmt->bindParam(":id", $idClient_old, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $codi_resposta = 1;
                    } else {
                        $codi_resposta = 2;
                    }
          
                } else { // Error > bloqueja i mostra avis
                    echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error!</h4></strong>';
                    echo 'Controla que totes les dades siguin correctes.</div>';
                } 

                    if ($codi_resposta == 1)  {
                    echo '<div class="alert alert-success" role="alert"><h4 class="alert-heading"><strong>Alta realizada correctament.</h4></strong>';
                    echo "Alta client anual amb èxit.</div>";
                    } else { // Error > bloqueja i mostra avis
                        echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error en la transmissió de les dades</h4></strong>';
                        echo 'Les dades no s\'han transmès correctament.</div>';
                    }
                }
            
                if ($codi_resposta == 2) { 
                    echo '<form action="" method="post" id="update-client" class="row g-3" style="background-color:#BDBDBD;padding:25px;margin-top:10px">';
            
                    echo '<div class="col-md-4">';
                    echo '<label>Nom i cognoms client:</label>';
                    echo '<input type="text" class="form-control" id="nombre" name="nombre" value="'.$nom_old.'">';
                    echo '</div>';

                    echo '<div class="col-md-4">';
                    echo '<label>Telèfon client:</label>';
                    echo '<input type="text" class="form-control" id="telefono" name="telefono" value="'.$telefon_old.'">';
                    echo '</div>';

                    echo '<div class="col-md-6">';
                    echo '<label>Anualitat client:</label>';
                    echo '<input type="text" class="form-control" id="anualitat" name="anualitat" value="'.$anualitat_old.'">';
                    echo '</div>';
        
                    echo "<div class='md-12'>";
                    echo "<button id='update-client' name='update-client' type='submit' class='btn btn-primary'>Modifica client</button><a href='reserves-anuals-modificar-client.php'></a>
                    </div>";
        
                    echo "</form>";
                } else {
                    echo '<a href="reserves-anuals-index.php" class="btn btn-dark menuBtn" role="button" aria-disabled="false">Tornar</a>';
                }

                
            } else {
                echo "Error: aquest ID no és vàlid";
            }

} else {
   echo "Error. No has seleccionat cap vehicle.";
}

echo '</div>
 </div>';

require_once('inc/footer.php');
?>


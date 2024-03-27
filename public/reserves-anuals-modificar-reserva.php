<?php
require_once('inc/header.php');
require_once('inc/header-reserves-anuals.php');

if (isset($_GET['id'])) {
    $id_old = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    if ( filter_var($id_old, FILTER_VALIDATE_INT) ) {
        $codi_resposta = 2;

         // consulta general reserves 
         $sql = "SELECT rc1.diaSalida AS 'dataSortida',
         rc1.horaEntrada AS 'HoraEntrada',
         rc1.horaSalida AS 'HoraSortida',
         rc1.diaEntrada AS 'dataEntrada',
         rc1.matricula AS 'matricula',
         rc1.vehiculo AS 'modelo',
         rc1.vuelo AS 'vuelo',
         rc1.tipo AS 'tipo',
         rc1.limpieza,
         rc1.idClient,
         rc1.notes
         FROM reserves_parking AS rc1
         WHERE rc1.id = $id_old";
 
         $pdo_statement = $pdo_conn->prepare($sql);
         $pdo_statement->execute();
         $result = $pdo_statement->fetchAll();
         foreach($result as $row) {
            $dataSortida_old = $row['dataSortida'];
            $HoraEntrada_old = $row['HoraEntrada'];
            $HoraSortida_old = $row['HoraSortida'];
            $dataEntrada_old = $row['dataEntrada'];
            $matricula_old = $row['matricula'];
            $modelo_old = $row['modelo'];
            $vuelo_old = $row['vuelo'];
            $tipo_old = $row['tipo'];
            $limpieza_old = $row['limpieza'];
            $idClient_old = $row['idClient'];
            $notes_old = $row['notes'];
         }

        echo "<h3>Modificació de reserva de client amb abonament anual</h3>";
        echo "<h4>ID reserva número: ".$id_old ."</h4>";

        function data_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
            
        $codi_resposta = 2;

              if (isset($_POST["modifica-reserva"])) {
                global $pdo_conn;
                  
                if (empty($_POST["idClient"])) {
                    $hasError = true;
                } else {
                    $idClient = data_input($_POST["idClient"], ENT_NOQUOTES);
                }
                
                if (empty($_POST["diaEntrada"])) {
                    $diaEntrada = NULL;
                } else {
                    $diaEntrada = data_input($_POST["diaEntrada"], ENT_NOQUOTES);
                }

                if (empty($_POST["horaEntrada"])) {
                    $horaEntrada = NULL;
                } else {
                    $horaEntrada = data_input($_POST["horaEntrada"], ENT_NOQUOTES);
                }

                if (empty($_POST["diaSalida"])) {
                    $diaSalida = NULL;
                } else {
                    $diaSalida = data_input($_POST["diaSalida"], ENT_NOQUOTES);
                }

                if (empty($_POST["horaSalida"])) {
                    $horaSalida = NULL;
                } else {
                    $horaSalida = data_input($_POST["horaSalida"], ENT_NOQUOTES);
                }

                if (empty($_POST["vuelo"])) {
                   $vuelo = NULL;
                } else {
                    $vuelo = data_input($_POST["vuelo"], ENT_NOQUOTES);
                }

                if (empty($_POST["notes"])) {
                    $notes = NULL;
                } else {
                    $notes = data_input($_POST["notes"], ENT_NOQUOTES);
                }

                if (empty($_POST["tipo"])) {
                    $tipo = NULL;
                } else {
                    $tipo = data_input($_POST["tipo"], ENT_NOQUOTES);
                }

                if (empty($_POST["vehiculo"])) {
                    $vehiculo = NULL;
                } else {
                    $vehiculo = data_input($_POST["vehiculo"], ENT_NOQUOTES);
                }

                if (empty($_POST["matricula"])) {
                    $matricula = NULL;
                } else {
                    $matricula = data_input($_POST["matricula"], ENT_NOQUOTES);
                }

                $neteja = NULL;

               // Si no hi ha cap error, envia el formulari
                if (!isset($hasError)) {
                    $emailSent = true;
          
                } else { // Error > bloqueja i mostra avis
                    echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error!</h4></strong>';
                    echo 'Controla que totes les dades siguin correctes.</div>';
                } 

                    global $pdo_conn;
                    $sql = "UPDATE reserves_parking SET idClient=:idClient, diaEntrada=:diaEntrada, horaEntrada=:horaEntrada, diaSalida=:diaSalida, horaSalida=:horaSalida, vuelo=:vuelo, notes=:notes, tipo=:tipo, matricula=:matricula, vehiculo=:vehiculo
                    WHERE id=:id";
                    $stmt = $pdo_conn->prepare($sql);
                    $stmt->bindParam(":idClient", $idClient, PDO::PARAM_INT);
                    $stmt->bindParam(":diaEntrada", $diaEntrada, PDO::PARAM_STR);
                    $stmt->bindParam(":horaEntrada", $horaEntrada, PDO::PARAM_STR);
                    $stmt->bindParam(":diaSalida", $diaSalida, PDO::PARAM_STR);
                    $stmt->bindParam(":horaSalida", $horaSalida, PDO::PARAM_STR);
                    $stmt->bindParam(":vuelo", $vuelo, PDO::PARAM_STR);
                    $stmt->bindParam(":notes", $notes, PDO::PARAM_STR);
                    $stmt->bindParam(":tipo", $tipo, PDO::PARAM_INT);
                    $stmt->bindParam(":matricula", $matricula, PDO::PARAM_STR);
                    $stmt->bindParam(":vehiculo", $vehiculo, PDO::PARAM_STR);
                    $stmt->bindParam(":id", $id_old, PDO::PARAM_INT);
                   
                    if ($stmt->execute()) {
                        $codi_resposta = 1;
                    } else {
                        $codi_resposta = 2;
                    }
          
                    if ($codi_resposta == 1)  {
                    echo '<div class="alert alert-success" role="alert"><h4 class="alert-heading"><strong>Alta reserva realitzada correctament.</h4></strong>';
                    echo "Alta reserva amb èxit.</div>";
                    } else { // Error > bloqueja i mostra avis
                        echo '<div class="alert alert-danger" role="alert"><h4 class="alert-heading"><strong>Error en la transmissió de les dades</h4></strong>';
                        echo 'Les dades no s\'han transmès correctament.</div>';
                    }
                }
            
                if ($codi_resposta == 2) { 
                    echo '<form action="" method="post" id="alta-reserva" class="row g-3" style="background-color:#BDBDBD;padding:25px;margin-top:10px">';

                   echo '<input type="hidden" name="idReserva" value="0001" />';
                   
                   echo "<h5>Selecciona un client (camp obligatori):</h5>";

                    echo '<div class="col-md-4">';
                    echo '<label>Nom client:</label>';
                    echo '<select class="form-select" name="idClient" id="idClient">';
                    echo '<option selected disabled>Selecciona el client:</option>';
                    // consulta general reserves 
                    $sql = "SELECT c.nombre, c.id
                    FROM usuaris AS c
                    WHERE c.tipoUsuario = 3
                    ORDER BY c.nombre ASC";

                    $pdo_statement = $pdo_conn->prepare($sql);
                    $pdo_statement->execute();
                    $result = $pdo_statement->fetchAll();
                    foreach($result as $row) {
                        $nom_old = $row['nombre'];
                        $id_old = $row['id'];
                        if ($idClient_old == $id_old){
                          echo "<option value=".$idClient_old." selected>".$nom_old."</option>"; 
                        } else {
                          echo "<option value=".$id_old.">".$nom_old."</option>"; 
                        }
                      }
                    echo '</select>';
                    echo "</div>";

                    echo "<hr>";
                    echo "<h5>Aquests camps són opcionals, els pots modificar més endavant:</h5>";

                    echo '<div class="col-md-4">';
                    echo '<label>Tipo reserva:</label>';
                    echo '<select class="form-select" name="tipo" id="tipo">';
                    echo '<option selected disabled>Selecciona una opció:</option>';
                    echo "<option value='1' selected>Finguer class</option>"; 
                    echo "<option value='2'>Gold Finguer Class</option>"; 
                    echo '</select>';
                    echo "</div>";

                    echo '<div class="col-md-4">';
                    echo '<label>Data entrada:</label>';
                    echo '<input type="date" class="form-control" id="diaEntrada" name="diaEntrada" value="'.$dataEntrada_old.'">';
                    echo '</div>';

                    echo '<div class="col-md-4">';
                    echo '<label>Hora entrada:</label>';
                    echo '<input type="text" class="form-control" id="horaEntrada" name="horaEntrada" placeholder="00:00" value="'.$HoraEntrada_old.'">';
                    echo '</div>';

                    echo '<div class="col-md-4">';
                    echo '<label>Data sortida:</label>';
                    echo '<input type="date" class="form-control" id="diaSalida" name="diaSalida" value="'.$dataSortida_old.'">';
                    echo '</div>';

                    echo '<div class="col-md-4">';
                    echo '<label>Hora sortida:</label>';
                    echo '<input type="text" class="form-control" id="horaSalida" name="horaSalida" placeholder="00:00" value="'.$HoraSortida_old.'">';
                    echo '</div>';

                    echo '<div class="col-md-4">';
                    echo '<label>Vol:</label>';
                    echo '<input type="text" class="form-control" id="vuelo" name="vuelo" value="'.$vuelo_old.'">';
                    echo '</div>';

                    echo '<div class="col-md-4">';
                    echo '<label>Notes:</label>';
                    echo '<input type="text" class="form-control" id="notes" name="notes" value="'.$notes_old.'">';
                    echo '</div>';

                    echo '<div class="col-md-4">';
                    echo '<label>Model cotxe:</label>';
                    echo '<input type="text" class="form-control" id="vehiculo" name="vehiculo" value="'.$modelo_old.'">';
                    echo '</div>';

                    echo '<div class="col-md-4">';
                    echo '<label>Matrícula:</label>';
                    echo '<input type="text" class="form-control" id="matricula" name="matricula" value="'.$matricula_old.'">';
                    echo '</div>';
        
                    echo "<div class='md-12'>";
                    echo "<button id='modifica-reserva' name='modifica-reserva' type='submit' class='btn btn-primary'>Modifica reserva</button><a href='reserves-anuals-modificar-reserva.php'></a>
                    </div>";
        
                    echo "</form>";
                } else {
                    echo '<a href="reserves-anuals-index.php" class="btn btn-dark menuBtn" role="button" aria-disabled="false">Tornar</a>';
                }

                echo '</div>
                </div>';

        } else {
            echo "Error: aquest ID no és vàlid";
        }
} else {
    echo "Error. No has seleccionat cap reserva vàlida.";
}

require_once('inc/footer.php');
?>


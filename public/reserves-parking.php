<?php
require_once('inc/header.php');
?>

<h2>Estat 2: Reserves al parking</h2>
<h4>Ordenat segons data sortida vehicle</h4>

<?php
// consulta general reserves 
	$sql = "SELECT rc1.idReserva,
    rc1.firstName AS 'clientNom',
    rc1.lastName AS 'clientCognom',
    rc1.tel AS 'telefono',
    rc1.diaSalida AS 'dataSortida',
    rc1.horaEntrada AS 'HoraEntrada',
    rc1.horaSalida AS 'HoraSortida',
    rc1.diaEntrada AS 'dataEntrada',
    rc1.matricula AS 'matricula',
    rc1.vehiculo AS 'modelo',
    rc1.vuelo AS 'vuelo',
    rc1.tipo AS 'tipo',
    rc1.checkIn,
    rc1.checkOut,
    rc1.notes,
    rc1.buscadores,
    rc1.limpieza,
    rc1.id,
    u.nombre,
    u.telefono AS tel
    FROM reserves_parking AS rc1
    left join reservas_buscadores AS b ON rc1.buscadores = b.id
    left JOIN usuaris AS u ON rc1.idClient = u.id
    WHERE rc1.checkIn = 1
    GROUP BY rc1.id
    ORDER BY rc1.diaSalida ASC, rc1.horaSalida  ASC";
	
	/* Pagination Code starts */
	$per_page_html = '';
	$page = 1;
	$start=0;
	if(!empty($_POST["page"])) {
		$page = $_POST["page"];
		$start=($page-1) * ROW_PER_PAGE;
	}
	$limit=" limit " . $start . "," . ROW_PER_PAGE;
	$pagination_statement = $pdo_conn->prepare($sql);
	$pagination_statement->execute();

	$row_count = $pagination_statement->rowCount();
	if(!empty($row_count)){
		$per_page_html .= "<div style='text-align:center;margin:20px 0px;'>";
		$page_count=ceil($row_count/ROW_PER_PAGE);
		if($page_count>1) {
			for($i=1;$i<=$page_count;$i++){
				if($i==$page){
					$per_page_html .= '<input type="submit" name="page" value="' . $i . '" class="btn-page current" />';
				} else {
					$per_page_html .= '<input type="submit" name="page" value="' . $i . '" class="btn-page" />';
				}
			}
		}
		$per_page_html .= "</div>";
	}
	
	$query = $sql.$limit;
	$pdo_statement = $pdo_conn->prepare($query);
	$pdo_statement->execute();
	$result = $pdo_statement->fetchAll();
?>
<form name='frmSearch' action='' method='post'>
<div class="container-lg">
<div class='table-responsive'>
<table class='table table-striped'>
<thead class="table-dark">
    <tr>
                <th>Núm. Comanda</th>
                <th>Tipus</th>
                <th>Client // tel.</th>
                <th>Entrada</th>
                <th>Sortida &darr;</th>
                <th>Vehicle</th>
                <th>Vol tornada</th>
                <th>Neteja</th>
                <th>Accions</th>
                <th>Notes</th>
                <th>Cercadors</th>
                </tr>
                </thead>
                <tbody>

	<?php
	if(!empty($result)) { 
		foreach($result as $row) {

            $matricula1 = $row['matricula'];
            $modelo1 = $row['modelo'];
            $vuelo1 = $row['vuelo'];

            $horaEntrada2 = $row['HoraEntrada'];
            //$horaEntrada2 = substr_replace($horaEntrada,':',-2,-2); //huevxos 0030
    
            $horaSortida2 = $row['HoraSortida'];
            //$horaSortida2 = substr_replace($horaSortida,':',-2,-2); //huevxos 0030
    
            $dataEntrada = $row['dataEntrada'];
            $anyEntrada = date('Y', strtotime( $dataEntrada));
            $dataEntrada4 = date("d-m-Y", strtotime($dataEntrada));
        
            $dataSortida = $row['dataSortida'];
            $anySortida = date('Y', strtotime( $dataSortida));
            $dataSortida4 = date("d-m-Y", strtotime($dataSortida));
    
            $tipo = $row['tipo'];
            if ($tipo == 1) {
                $tipoReserva2 = "Finguer Class";
            } elseif ($tipo == 2) {
                 $tipoReserva2 = "Gold Finguer Class";
            } else {
                $tipoReserva2 = "Finguer Class";
            }
            $limpieza = $row['limpieza'];
            if ($limpieza == 1) {
                $limpieza2 = "Servicio de limpieza exterior";
            } elseif ($limpieza == 2) {
                 $limpieza2 = "Servicio de lavado exterior + aspirado tapicería interior";
            } elseif ($limpieza == 3) {
                $limpieza2 = "Limpieza PRO";
            } else {
                $limpieza2 = "-";
            }

           $idReserva = $row['idReserva'];
           $checkIn = $row['checkIn'];
           $checkOut = $row['checkOut'];
           $notes = $row['notes'];
           $buscadores = $row['buscadores'];
            if ($buscadores == 1) {
                $buscadores = "One park";
            } elseif ($buscadores == 2) {
                $buscadores = "Parkcloud";
            } elseif ($buscadores == 3) {
                $buscadores = "Travelcar";
            } elseif ($buscadores == 4) {
                $buscadores = "Looking 4 parking";
            } elseif ($buscadores == 5) {
                $buscadores = "icarous";
            }

            $clientNom = $row['clientNom'];
           $clientCognom = $row['clientCognom'];
           $telefono = $row['telefono'];
           $id = $row['id'];

           //$nom = $row['nom'];
           //$cognoms = $row['cognoms'];
           //$telefon = $row['telefon'];

           if (isset($row['nombre'])) {
                $nombre = $row['nombre'];
                $tel = $row['tel'];
           }

           echo "<tr>";
           echo "<td>";
           
           if ($idReserva == 1) {
                echo "<button type='button' class='btn btn-primary btn-sm'>Client anual</button>";
            } else {
                echo "".$idReserva."</a>";
            }
        
           echo "</td>";
           echo "<td><a href='canvi-tipus-reserva.php?&id=".$id."'>".$tipoReserva2."</a></td>";
           echo "<td>";

           if (isset($row['nombre']))  {
            echo "<a href='canvi-client-telefon_nou.php?&id=".$id."'>".$nombre."  // ".$tel." // ".$telefono."</a>";
            } else {             
                echo "<a href='canvi-nom-client.php?&id=".$id."'>".$clientNom."  ".$clientCognom."</a> // <a href='canvi-client-telefon.php?&id=".$id."'>".$telefono."</a>";
            } 


            /*if ($idReserva == 1) {
                echo " ".$nom."  ".$cognoms." // ".$telefon." ";
            } else {
                echo "<a href='canvi-nom-client.php?&id=".$id."'>".$clientNom."  ".$clientCognom."</a> // <a href='canvi-client-telefon.php?&id=".$id."'>".$telefono."</a>";
            } */

            echo "</td>";
           echo "<td>";
           if ($anyEntrada == 1970) {
            echo "Pendent";
           } else {
            echo "<a href='canvi-reserva-entrada.php?&id=".$id."'>".$dataEntrada4."</a> // <a href='canvi-reserva-entrada.php?&id=".$id."'>".$horaEntrada2."</a>";
           }
           echo "</td>";
           echo "<td>";
           if ($anySortida == 1970) {
            echo "Pendent";
           } else {
            echo "<a href='canvi-reserva-sortida.php?&id=".$id."'>".$dataSortida4."</a> // <a href='canvi-reserva-sortida.php?&id=".$id."'>".$horaSortida2."</a>";
           }
           echo "</td>";
           echo "<td><a href='canvi-matricula.php?&id=".$id."'>".$modelo1."</a>";
            if (!empty($matricula1)) {
                echo " // <a href='canvi-matricula.php?&id=".$id."'>".$matricula1."</a>";
            } else {
                echo "<p><a href='canvi-matricula.php?&id=".$id."' class='btn btn-secondary btn-sm' role='button' aria-pressed='true'>Afegir matrícula</a></p>";
            }
           echo "</td>";
           echo "<td>";
           if (empty($vuelo1)) {
               echo "<a href='afegir-vol.php?&id=".$id."' class='btn btn-secondary btn-sm' role='button' aria-pressed='true'>Afegir</a>";
           } else {
               echo "<a href='canvi-vol.php?&id=".$id."'>".$vuelo1."</a>";
           }
           echo "</td>";
           echo "<td>".$limpieza2."</td>";
           echo "<td>";
           if ($checkIn == 1) {
               echo "<a href='fer-checkout.php?&id=".$id."' class='btn btn-secondary btn-sm' role='button' aria-pressed='true'>Check-Out</a>";    
           }
           echo "</td>";
           echo "<td>";
           if (empty($idReserva)) {
               echo "<a href='afegir-nota.php?&id=".$id."' class='btn btn-info btn-sm' role='button' aria-pressed='true'>Crear notes</a>";    
           } elseif ( !empty($idReserva) && empty($notes) ) {
               echo "<a href='afegir-nota.php?&id=".$id."' class='btn btn-info btn-sm' role='button' aria-pressed='true'>Crear notes</a>";
           } elseif (!empty($notes) ) {
               echo "<a href='veure-nota.php?&id=".$id."' class='btn btn-danger btn-sm' role='button' aria-pressed='true'>Veure notes</a>";
           }

           echo "</td>";
           echo "<td>";
           if ($idReserva == 1) {
                echo "<a href='reserves-anuals-modificar-reserva.php?&id=".$id."' class='btn btn-dark btn-sm' role='button' aria-pressed='true'>Modificar reserva</a>";
           } else {
                if (empty($idReserva)) {
                    echo "<a href='afegir-buscador.php?&id=".$id."' class='btn btn-warning btn-sm' role='button' aria-pressed='true'>Alta en buscador</a>";    
                } elseif ( !empty($idReserva) && empty($buscadores) ) {
                    echo "<a href='afegir-buscador.php?&id=".$id."' class='btn btn-warning btn-sm' role='button' aria-pressed='true'>Alta en buscador</a>";
                } elseif (!empty($buscadores ) ) {
                    echo "".$buscadores." <a href='modificar-buscador.php?&id=".$id."'>(modificar)</a>";
                }
           } 
           echo "</td>";
           echo "</tr>";
           }
           echo "</tbody>";
           echo "</table>";
           echo "</div>";
      

	}
	?>

<?php echo $per_page_html; ?>
</form>

<?php 
$sql2 = "SELECT COUNT(r.idReserva) AS numero
    FROM reserves_parking as r
    WHERE r.checkIn = 1";

    global $pdo_conn;
        $pdo_statement = $pdo_conn->prepare($sql2);
        $pdo_statement->execute();
        $result = $pdo_statement->fetchAll();
        foreach($result as $row) {
            $numero = $row['numero'];
        }

        echo "<h5>Total reserves actualment al parking: ".$numero." </h5>";
?>

</div>

<?php 
require_once('inc/footer.php');
?>
<?php
require_once('inc/header.php');

require_once('inc/header-reserves-anuals.php');

echo "<h3>Clients amb Abonament anual</h3>";

// consulta general clients 
$sql = "SELECT c.nombre AS nom, c.telefono AS telefon, c.id, c.anualitat
FROM  usuaris AS c
WHERE c.tipoUsuario = 3
ORDER BY c.nombre ASC";

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

if (!empty($result)) { 
    ?>
    <form name='frmSearch' action='' method='post'>
    <div class='table-responsive'>
    <table class='table table-striped'>
    <thead class="table-dark">
    <tr>
            <th>Nom i cognoms &darr;</th>
            <th>Telèfon</th>
            <th>Anualitat</th>
            <th>Modificar dades</th>
            <th>Eliminar client</th>
            <th>Crear reserva</th>
            </tr>
            </thead>
            <tbody>

    <?php

    foreach($result as $row) {
        $nom = $row['nom'];
        $telefon = $row['telefon'];
        $id = $row['id'];
        $anualitat = $row['anualitat'];
        echo "<tr>";
        echo "<td>".$nom."</td>";
        echo "<td>".$telefon."</td>";
        echo "<td>".$anualitat."</td>";
        echo "<td><a href='reserves-anuals-modificar-client.php?&idClient=".$id."' class='btn btn-warning btn-sm' role='button' aria-pressed='true'>Actualitzar dades</a></td>";
        echo "<td><a href='reserves-anuals-eliminar-client.php?&idClient=".$id."' class='btn btn-danger btn-sm' role='button' aria-pressed='true'>Eliminar client</a></td>";
        echo "<td><a href='reserves-anuals-crear-reserva.php?&idClient=".$id."' class='btn btn-info btn-sm' role='button' aria-pressed='true'>Crear reserva</a></td>";
        echo "</tr>";
    }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
} else {
    echo "En aquests moments no hi ha cap client anual donat d'alta en el sistema. <a href='https://control.finguer.com/reserves-anuals-crear-client.php'>Vols donar d'alta un client?</a>";
}


echo $per_page_html;
echo '</form>';

echo '</div></div>';

require_once('inc/footer.php');
?>


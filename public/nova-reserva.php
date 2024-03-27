<?php
require_once('inc/header.php');
?>

<h2>Estat 0: Reserves sense entrar al sistema</h2>

<?php
// consulta general reserves 
	$sql = "SELECT p.ID, p.post_date
    FROM wpfin_posts  as p
    WHERE NOT EXISTS (SELECT c.idReserva
                   FROM reserves_parking AS c
                   WHERE p.ID = c.idReserva)
   AND p.post_type='shop_order' AND p.post_status IN ('wc-completed', 'wc-on-hold')
    ORDER BY p.ID ASC";
	
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
        <div class="container-lg">
        <div class='table-responsive'>
        <table class='table table-striped'>
        <thead class="table-dark">
        <tr>
        <th>Reserva</th>
        <th>Data comanda</th>
        <th>Entrada al sistema</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($result as $row) {
                   $ID = $row['ID'];
                   $date = $row['post_date'];
                   echo "<tr>";
                   echo "<td>".$ID."</td>";
                   echo "<td>".$date."</td>";
                   echo "<td> <a href='afegir-nova-reserva.php?&idReserva=".$ID."' class='btn btn-secondary btn-sm' role='button' aria-pressed='true'>Afegir al sistema</a></td>";
                   echo "</tr>";
        }
                   echo "</tbody>";
                   echo "</table>";
                   echo "</div>";
            ?>
        <?php echo $per_page_html; ?>
        </form>
    <?php 
    } else {
    echo "En aquest moment no hi ha cap reserva pendent d'entrar al sistema.";
    }

?>
</div>

<?php 
require_once('inc/footer.php');
?>
<?php 

// 1) primer comprova quantes reserves pendents hi ha i extreu el numero:
$sql1 = "SELECT COUNT(r.id) AS numero
FROM reserves_parking AS r
WHERE EXISTS (SELECT p.ID
                   FROM wpfin_posts as p
                   WHERE p.ID = r.idReserva
AND p.post_type='shop_order' AND p.post_status IN ('wc-completed') AND r.checkIn = 6 )";

global $pdo_conn;
$pdo_statement = $pdo_conn->prepare($sql1);
$pdo_statement->execute();
$result = $pdo_statement->fetchAll();
foreach($result as $row) {
    $numero = $row['numero'];
}

// 2) comença el loop en funció del numero total de reserves:
$i = 1;
while ($i <= $numero) {
    // 3) consulta general reserva no registrada en el sistema. Cada consulta extreu 1 reserva:
    $sql2 = "SELECT r.idReserva, r.id
    FROM reserves_parking AS r
    WHERE EXISTS 
    (SELECT p.ID FROM wpfin_posts as p
     WHERE p.ID = r.idReserva AND p.post_type='shop_order' AND p.post_status IN ('wc-completed') AND r.checkIn = 6 ) 
    LIMIT 1";

    $pdo_statement = $pdo_conn->prepare($sql2);
    $pdo_statement->execute();
    $result2 = $pdo_statement->fetchAll();
    foreach ($result2 as $row) {
        $id_old = $row['id'];
    }

    // 4) Canvia l'estat de la reserva a checkIn 6 > cancel·lada
    if (!empty($id_old)) {
        $checkIn = 5;

        $sql5 = "UPDATE reserves_parking SET checkIn=:checkIn
        WHERE id=:id";
        
        $stmt = $pdo_conn->prepare($sql5);
        $stmt->bindParam(":checkIn", $checkIn, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id_old, PDO::PARAM_INT);
                    
        $stmt->execute();
    } else {
        //nothing
    }
    // repetir loop
    $i=$i+1;    
}
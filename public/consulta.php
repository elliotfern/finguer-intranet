<?php

// URL de la API de WooCommerce y ID del pedido
$api_url = 'https://finguer.com/wp-json/wc/v3/orders/15553';
$consumer_key = 'ck_41bd6fded46162f87b9713ecbe8b879ad10b6a0e';
$consumer_secret = 'cs_b2189c1579bb1ed428df3385a85b2f1b0cb416d2';

// Construir la URL completa con las credenciales
$url = $api_url . '?consumer_key=' . $consumer_key . '&consumer_secret=' . $consumer_secret;

// Inicializar cURL
$ch = curl_init($url);

// Configurar las opciones de cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Realizar la solicitud GET
$response = curl_exec($ch);

// Verificar si hay errores
if (curl_errno($ch)) {
    echo 'Error al realizar la solicitud: ' . curl_error($ch);
} else {
     // Decodificar la respuesta JSON
     $order_details = json_decode($response, true);

     echo '<pre>';
     print_r($order_details);
     echo '</pre>';
 
     // Obtener información específica del servicio de lavado
    $allowed_product_ids = [1705, 1704, 1694];
    $filtered_items = array_filter($order_details['line_items'], function ($item) use ($allowed_product_ids) {
        return in_array($item['product_id'], $allowed_product_ids);
    });

    if (!empty($filtered_items)) {
        foreach ($filtered_items as $item) {
            echo 'Información del producto:<br>';
            echo 'Nombre: ' . $item['name'] . '<br>';
            echo 'Product ID: ' . $item['product_id'] . '<br>';
            // Agrega más información según sea necesario
            echo '--------------------------<br>';
        }
    } else {
        echo 'No se encontraron productos con los product_id permitidos en los elementos del pedido.';
    }
}

// Cerrar la sesión cURL
curl_close($ch);

?>
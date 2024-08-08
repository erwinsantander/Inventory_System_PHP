<?php
require_once('includes/load.php');
$response = ['success' => false];

if (isset($_POST['id'])) {
    $product_id = $db->escape($_POST['id']);
    $query = "SELECT price FROM products WHERE id = '{$product_id}' LIMIT 1";
    $result = $db->query($query);

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $response = [
            'success' => true,
            'price' => $product['price']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>

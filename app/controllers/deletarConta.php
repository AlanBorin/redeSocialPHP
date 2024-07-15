<?php
require '../models/config.php';
require '../models/perfilService.php';

autenticar();
$usuario_id = $_SESSION['id'];

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (deletarConta($db, $usuario_id)) {
        session_destroy();
        $response['success'] = true;
    }
}

echo json_encode($response);
?>

<?php
require '../models/config.php';
require '../models/perfilService.php';

autenticar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seguido_id = $_POST['seguido_id'];
    $seguidor_id = $_SESSION['id'];

    $segue = verificaSeUsuarioSegue($db, $seguidor_id, $seguido_id);

    if ($segue) {
        deixarDeSeguirUsuario($db, $seguidor_id, $seguido_id);
        echo json_encode(['status' => 'nao_seguido']);
    } else {
        seguirUsuario($db, $seguidor_id, $seguido_id);
        echo json_encode(['status' => 'seguido']);
    }
}
?>

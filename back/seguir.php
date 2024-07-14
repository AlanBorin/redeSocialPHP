<?php
require '../back/config.php';
require '../back/perfilService.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['seguido_id'])) {
        $seguido_id = $_POST['seguido_id'];
        $seguidor_id = $_SESSION['id'];
        seguirUsuario($db, $seguidor_id, $seguido_id);
        header('Location: ../front/index.php');
        exit;
    }
}
?>

<?php
require '../models/config.php';
require '../models/perfilService.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['seguido_id'])) {
        $seguido_id = $_POST['seguido_id'];
        $seguidor_id = $_SESSION['id'];
        deixarDeSeguirUsuario($db, $seguidor_id, $seguido_id);
        header('Location: ../../public/index.php');
        exit;
    }
}
?>

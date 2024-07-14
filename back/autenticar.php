<?php
session_start();

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../front/login.php');
    exit;
}

$email = $_POST['email'];
$senha = $_POST['senha'];

$stmt = $db->prepare('SELECT id, nome, email, senha FROM usuarios WHERE email = :email');
$stmt->bindParam(':email', $email);

$result = $stmt->execute();
$usuario = $result->fetchArray(SQLITE3_ASSOC);

if (!$usuario) {
    header('Location: ../front/login.php?msg=Usuário não encontrado');
    exit;
} else {
    if (password_verify($senha, $usuario['senha'])) {
        $_SESSION['id'] = $usuario['id']; 
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['auth'] = true;

        header('Location: ../front/index.php');
        exit;
    } else {
        header('Location: ../front/login.php?msg=Senha Incorreta');
        exit;
    }
}
?>

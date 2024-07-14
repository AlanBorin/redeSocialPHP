<?php
require 'config.php';
require 'perfilService.php';

autenticar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $biografia = $_POST['biografia'];
    $senha = $_POST['senha'];

    atualizarPerfil($db, $user_id, $nome, $email, $biografia, $senha);
    header('Location: ../front/configuracoes.php');
    exit;
}

function atualizarPerfil($db, $user_id, $nome, $email, $biografia, $senha) {
    $stmt = $db->prepare('UPDATE usuarios SET nome = :nome, email = :email, biografia = :biografia WHERE id = :id');
    $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':biografia', $biografia, SQLITE3_TEXT);
    $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
    $stmt->execute();

    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $db->prepare('UPDATE usuarios SET senha = :senha WHERE id = :id');
        $stmt->bindValue(':senha', $senha_hash, SQLITE3_TEXT);
        $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();
    }
}
?>

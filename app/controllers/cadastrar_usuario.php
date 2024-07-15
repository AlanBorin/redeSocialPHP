<?php
require '../models/config.php';

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];

$stmt = $db->prepare('SELECT email FROM usuarios WHERE email = :email');
$stmt->bindValue(':email', $email);
$result = $stmt->execute();
$usuario = $result->fetchArray(SQLITE3_ASSOC);

if ($usuario) {
    $error_message = 'Email já existe. Por favor, use outro email.';
    header('Location: ../views/criarUsuario.php?error=' . urlencode($error_message));
    exit();
} else {
    $senha = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $db->prepare('INSERT INTO usuarios (email, nome, senha) VALUES(:email, :nome, :senha)');
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':senha', $senha);

    $result = $stmt->execute();
    if (!$result) {
        echo 'Falha ao criar usuário.';
    } else {
        header('Location: ../views/login.php?success=' . urlencode('Cadastro realizado com sucesso. Faça login.'));
        exit();
    }
}
?>

<?php

require '../models/config.php';

$id = $_POST['id'];
$novoNome = $_POST['nome'];
$email = $_POST['email'];

$usuario = array();
$usuario = $_POST;

    if (!empty($novoNome)) {
        $usuario['nome'] = $novoNome;
    }
    if (!empty($email) && is_numeric($email)) {
        $usuario['email'] = $email;
    };
    
    $stmtUpdate = $db->prepare('UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id');
    $stmtUpdate->bindValue(':nome', $usuario['nome'], SQLITE3_TEXT);
    $stmtUpdate->bindValue(':email', $usuario['email'], SQLITE3_TEXT);
    $stmtUpdate->bindParam(':id', $id, SQLITE3_INTEGER);
    
    $result = $stmtUpdate->execute();
    if(! $result){
        echo 'falha ao criar';
    }else {
        header('Location: ../../public/index.php');
    };

    
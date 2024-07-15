<?php

require '../models/config.php';

$id = $_GET['id'];

    if (!is_numeric($id)) {
        echo "ID inválido. Por favor, insira um número.\n";
        return;
    }

    $stmt = $db->prepare('DELETE FROM usuarios WHERE id = :id');
    $stmt->bindValue(':id', $id);

    $result = $stmt->execute();
    if(! $result){
        echo 'falha ao criar';
    }else {
        header('Location: ../../public/index.php');
    };

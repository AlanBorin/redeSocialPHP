<?php
require 'config.php';

function obterPerfil($db, $usuario_id)
{
    $stmt = $db->prepare('SELECT * FROM usuarios WHERE id = :usuario_id');
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    return $result->fetchArray(SQLITE3_ASSOC);
}

function obterPostagensPerfil($db, $usuario_id)
{
    $stmt = $db->prepare('SELECT posts.id, posts.conteudo, posts.data_criacao FROM posts WHERE usuario_id = :usuario_id ORDER BY posts.data_criacao DESC');
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $postagens = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $postagens[] = $row;
    }
    return $postagens;
}

function atualizarBiografia($db, $usuario_id, $biografia)
{
    $stmt = $db->prepare('UPDATE usuarios SET biografia = :biografia WHERE id = :usuario_id');
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    $stmt->bindValue(':biografia', $biografia, SQLITE3_TEXT);
    return $stmt->execute();
}

function deletarConta($db, $usuario_id)
{
    // Excluir posts
    $stmt = $db->prepare('DELETE FROM posts WHERE usuario_id = :usuario_id');
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    $stmt->execute();

    // Excluir curtidas
    $stmt = $db->prepare('DELETE FROM curtidas WHERE usuario_id = :usuario_id');
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    $stmt->execute();

    // Excluir referências
    $stmt = $db->prepare('DELETE FROM seguidores WHERE seguidor_id = :usuario_id OR seguido_id = :usuario_id');
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    $stmt->execute();

    // Excluir o usuário
    $stmt = $db->prepare('DELETE FROM usuarios WHERE id = :usuario_id');
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    return $stmt->execute();
}

function registrarVisita($db, $perfil_id)
{
    $stmt = $db->prepare('INSERT INTO visitas_perfil (perfil_id) VALUES (:perfil_id)');
    $stmt->bindValue(':perfil_id', $perfil_id, SQLITE3_INTEGER);
    $stmt->execute();
}

function obterNumeroVisitas($db, $perfil_id)
{
    $stmt = $db->prepare('SELECT COUNT(*) as total_visitas FROM visitas_perfil WHERE perfil_id = :perfil_id');
    $stmt->bindValue(':perfil_id', $perfil_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $visitas = $result->fetchArray(SQLITE3_ASSOC);
    return $visitas['total_visitas'];
}

function seguirUsuario($db, $seguidor_id, $seguido_id)
{
    $stmt = $db->prepare('INSERT INTO seguidores (seguidor_id, seguido_id) VALUES (:seguidor_id, :seguido_id)');
    $stmt->bindValue(':seguidor_id', $seguidor_id, SQLITE3_INTEGER);
    $stmt->bindValue(':seguido_id', $seguido_id, SQLITE3_INTEGER);
    $stmt->execute();

    atualizarNumeroSeguidores($db, $seguido_id);
}

function atualizarNumeroSeguidores($db, $usuario_id)
{
    $stmt = $db->prepare('UPDATE usuarios SET seguidores = (SELECT COUNT(*) FROM seguidores WHERE seguido_id = :usuario_id) WHERE id = :usuario_id');
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    $stmt->execute();
}

function deixarDeSeguirUsuario($db, $seguidor_id, $seguido_id)
{
    $stmt = $db->prepare('DELETE FROM seguidores WHERE seguidor_id = :seguidor_id AND seguido_id = :seguido_id');
    $stmt->bindValue(':seguidor_id', $seguidor_id, SQLITE3_INTEGER);
    $stmt->bindValue(':seguido_id', $seguido_id, SQLITE3_INTEGER);
    $stmt->execute();

    atualizarNumeroSeguidores($db, $seguido_id);
}

function obterNumeroSeguidores($db, $perfil_id)
{
    $stmt = $db->prepare('SELECT COUNT(*) as total_seguidores FROM seguidores WHERE seguido_id = :perfil_id');
    $stmt->bindValue(':perfil_id', $perfil_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $seguidores = $result->fetchArray(SQLITE3_ASSOC);
    return $seguidores['total_seguidores'];
}

function verificaSeUsuarioSegue($db, $seguidor_id, $seguido_id)
{
    $stmt = $db->prepare('SELECT COUNT(*) AS segue FROM seguidores WHERE seguidor_id = :seguidor_id AND seguido_id = :seguido_id');
    $stmt->bindValue(':seguidor_id', $seguidor_id, SQLITE3_INTEGER);
    $stmt->bindValue(':seguido_id', $seguido_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);

    return $row['segue'] > 0;
}

function atualizarPerfil($db, $usuario_id, $nome, $email, $biografia)
{
    $stmt = $db->prepare('UPDATE usuarios SET nome = :nome, email = :email, biografia = :biografia WHERE id = :usuario_id');
    $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':biografia', $biografia, SQLITE3_TEXT);
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    return $stmt->execute();
}

function atualizarSenha($db, $usuario_id, $senha_atual, $nova_senha)
{
    $stmt = $db->prepare('SELECT senha FROM usuarios WHERE id = :usuario_id');
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $usuario = $result->fetchArray(SQLITE3_ASSOC);

    if (!$usuario || !password_verify($senha_atual, $usuario['senha'])) {
        return false;
    }

    $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    $stmt = $db->prepare('UPDATE usuarios SET senha = :nova_senha WHERE id = :usuario_id');
    $stmt->bindValue(':nova_senha', $nova_senha_hash, SQLITE3_TEXT);
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    return $stmt->execute();
}

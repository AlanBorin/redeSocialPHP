<?php
require '../models/config.php';
autenticar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];

    // Verifica curtida no post
    $stmt = $db->prepare('SELECT * FROM curtidas WHERE post_id = :post_id AND usuario_id = :usuario_id');
    $stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
    $stmt->bindValue(':usuario_id', $_SESSION['id'], SQLITE3_INTEGER);
    $result = $stmt->execute();

    if ($result->fetchArray()) {
        // Descurtir
        $stmt = $db->prepare('DELETE FROM curtidas WHERE post_id = :post_id AND usuario_id = :usuario_id');
        $stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
        $stmt->bindValue(':usuario_id', $_SESSION['id'], SQLITE3_INTEGER);
        $stmt->execute();

        // Contagem de curtidas no post
        $stmt = $db->prepare('UPDATE posts SET curtidas = curtidas - 1 WHERE id = :post_id');
        $stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
        $stmt->execute();
        
        echo json_encode(['status' => 'unliked']);
    } else {
        $stmt = $db->prepare('INSERT INTO curtidas (post_id, usuario_id) VALUES (:post_id, :usuario_id)');
        $stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
        $stmt->bindValue(':usuario_id', $_SESSION['id'], SQLITE3_INTEGER);
        $stmt->execute();

        $stmt = $db->prepare('UPDATE posts SET curtidas = curtidas + 1 WHERE id = :post_id');
        $stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
        $stmt->execute();
        
        echo json_encode(['status' => 'liked']);
    }
}

<?php
require '../models/config.php';
autenticar();

date_default_timezone_set('America/Sao_Paulo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['id'])) {
        $conteudo = $_POST['conteudo'];
        $usuario_id = $_SESSION['id'];
        $data_criacao = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s');

        $stmt = $db->prepare('INSERT INTO posts (usuario_id, conteudo, data_criacao) VALUES (:usuario_id, :conteudo, :data_criacao)');
        $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
        $stmt->bindValue(':conteudo', $conteudo, SQLITE3_TEXT);
        $stmt->bindValue(':data_criacao', $data_criacao, SQLITE3_TEXT);

        $result = $stmt->execute();

        if ($result) {
            header('Location: ../../public/index.php');
            exit();
        } else {
            echo "Erro ao inserir post no banco de dados.";
        }
    } else {
        echo "Usuário não autenticado.";
    }
} else {
    echo "Método de requisição inválido.";
}
?>

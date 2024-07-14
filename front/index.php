<?php
require '../back/config.php';
require '../back/perfilService.php';

autenticar();
echo "ID do usuário logado: " . $_SESSION['id'] . "<br>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['seguido_id'])) {
        $seguido_id = $_POST['seguido_id'];
        $seguidor_id = $_SESSION['id'];

        $segue = verificaSeUsuarioSegue($db, $seguidor_id, $seguido_id);

        if ($segue) {
            deixarDeSeguirUsuario($db, $seguidor_id, $seguido_id);
        } else {
            seguirUsuario($db, $seguidor_id, $seguido_id);
        }

        header('Location: index.php');
        exit;
    }
}

function obterPosts($db)
{
    $stmt = $db->prepare('SELECT posts.id, posts.conteudo, posts.data_criacao, usuarios.id as usuario_id, usuarios.nome FROM posts JOIN usuarios ON posts.usuario_id = usuarios.id ORDER BY posts.data_criacao DESC');
    $result = $stmt->execute();
    $posts = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $posts[] = $row;
    }
    return $posts;
}

function obterCurtidas($db, $post_id)
{
    $stmt = $db->prepare('SELECT COUNT(*) as total FROM curtidas WHERE post_id = :post_id');
    $stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    return $result->fetchArray(SQLITE3_ASSOC)['total'];
}

$posts = obterPosts($db);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rede Social</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Rede Social</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <?php if (isset($_SESSION['nome'])) { ?>
                        <a class="nav-link" href="#">Seja bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</a>
                    <?php } ?>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../front/configuracoes.php">Configurações</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../back/logout.php">Sair</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <h2>Feed</h2>
                <?php foreach ($posts as $post) { ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="../front/perfil.php?usuario_id=<?php echo $post['usuario_id']; ?>">
                                    <?php echo htmlspecialchars($post['nome']); ?>
                                </a>
                            </h5>
                            <p class="card-text"><?php echo htmlspecialchars($post['conteudo']); ?></p>
                            <p class="card-text">
                                <small class="text-muted">Postado em <?php echo $post['data_criacao']; ?></small>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">Curtidas: <span id="curtidas_<?php echo $post['id']; ?>"><?php echo obterCurtidas($db, $post['id']); ?></span></small>
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-primary curtir-btn" data-post-id="<?php echo $post['id']; ?>">Curtir</button>
                                </div>
                                <div class="col-md-6">
                                    <?php if ($_SESSION['id'] != $post['usuario_id']) : ?>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $seguido = verificaSeUsuarioSegue($db, $_SESSION['id'], $post['usuario_id']);
                                            ?>
                                            <?php if ($seguido) : ?>
                                                <form id="form-deixar-seguir-<?php echo $post['usuario_id']; ?>" class="form-deixar-seguir" action="../back/deixarSeguir.php" method="POST">
                                                    <input type="hidden" name="seguido_id" value="<?php echo $post['usuario_id']; ?>">
                                                    <button type="submit" class="btn btn-danger">Deixar de Seguir <i class="bi bi-person-dash-fill"></i></button>
                                                </form>
                                            <?php else : ?>
                                                <form id="form-seguir-<?php echo $post['usuario_id']; ?>" class="form-seguir" action="../back/seguir.php" method="POST">
                                                    <input type="hidden" name="seguido_id" value="<?php echo $post['usuario_id']; ?>">
                                                    <button type="submit" class="btn btn-primary">Seguir <i class="bi bi-person-plus-fill"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-4">
                <h2>Nova Postagem</h2>
                <form id="form-postar" method="POST" action="../back/postar.php">
                    <div class="form-group">
                        <textarea name="conteudo" class="form-control" rows="3" maxlength="100" placeholder="O que você está pensando?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Postar</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.curtir-btn').click(function() {
                var post_id = $(this).data('post-id');
                var btn = $(this);

                $.ajax({
                    method: 'POST',
                    url: '../back/curtir.php',
                    data: {
                        post_id: post_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'liked') {
                            var curtidas = parseInt($('#curtidas_' + post_id).text()) + 1;
                            $('#curtidas_' + post_id).text(curtidas);
                        } else if (response.status == 'unliked') {
                            var curtidas = parseInt($('#curtidas_' + post_id).text()) - 1;
                            $('#curtidas_' + post_id).text(curtidas);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao curtir post:', error);
                    }
                });
            });
        });
    </script>
</body>

</html>
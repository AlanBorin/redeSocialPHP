<?php
require '../app/models/config.php';
require '../app/models/perfilService.php';

autenticar();

function obterPosts($db, $usuario_id)
{
    $stmt = $db->prepare('
        SELECT posts.id, posts.conteudo, posts.data_criacao, usuarios.id as usuario_id, usuarios.nome, 
        (SELECT COUNT(*) FROM curtidas WHERE curtidas.post_id = posts.id AND curtidas.usuario_id = :usuario_id) AS curtiu 
        FROM posts 
        JOIN usuarios ON posts.usuario_id = usuarios.id 
        ORDER BY posts.data_criacao DESC
    ');
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
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

$posts = obterPosts($db, $_SESSION['id']);
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
                    <a class="nav-link" href="../app/views/configuracoes.php">Configurações</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../app/controllers/logout.php">Sair</a>
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
                                <a href="../app/views/perfil.php?usuario_id=<?php echo $post['usuario_id']; ?>">
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
                                    <button class="btn <?php echo $post['curtiu'] ? 'btn-danger' : 'btn-primary'; ?> curtir-btn" data-post-id="<?php echo $post['id']; ?>">
                                        <?php echo $post['curtiu'] ? 'Descurtir' : 'Curtir'; ?>
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <?php if ($_SESSION['id'] != $post['usuario_id']) : ?>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $seguido = verificaSeUsuarioSegue($db, $_SESSION['id'], $post['usuario_id']);
                                            ?>
                                            <button class="btn <?php echo $seguido ? 'btn-danger' : 'btn-primary'; ?> seguir-btn" data-usuario-id="<?php echo $post['usuario_id']; ?>">
                                                <?php echo $seguido ? 'Deixar de Seguir' : 'Seguir'; ?>
                                            </button>
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
                <form id="form-postar" method="POST" action="../app/controllers/postar.php">
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
                    url: '../app/controllers/curtir.php',
                    data: {
                        post_id: post_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'liked') {
                            btn.removeClass('btn-primary').addClass('btn-danger').text('Descurtir');
                            var curtidas = parseInt($('#curtidas_' + post_id).text()) + 1;
                            $('#curtidas_' + post_id).text(curtidas);
                        } else if (response.status == 'unliked') {
                            btn.removeClass('btn-danger').addClass('btn-primary').text('Curtir');
                            var curtidas = parseInt($('#curtidas_' + post_id).text()) - 1;
                            $('#curtidas_' + post_id).text(curtidas);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao curtir post:', error);
                    }
                });
            });

            $('.seguir-btn').click(function() {
                var usuario_id = $(this).data('usuario-id');
                var btn = $(this);

                $.ajax({
                    method: 'POST',
                    url: '../app/controllers/seguir.php',
                    data: {
                        seguido_id: usuario_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'seguido') {
                            btn.removeClass('btn-primary').addClass('btn-danger').text('Deixar de Seguir');
                        } else if (response.status == 'nao_seguido') {
                            btn.removeClass('btn-danger').addClass('btn-primary').text('Seguir');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao seguir/deixar de seguir usuário:', error);
                    }
                });
            });
        });
    </script>
</body>

</html>

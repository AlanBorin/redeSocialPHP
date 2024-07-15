<?php
require '../models/config.php';

if (!isset($_GET['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$usuario_id = $_GET['usuario_id'];

require '../models/perfilService.php';

registrarVisita($db, $usuario_id);

$num_visitas = obterNumeroVisitas($db, $usuario_id);
$numero_seguidores = obterNumeroSeguidores($db, $usuario_id);

$perfil = obterPerfil($db, $usuario_id);
$postagens = obterPostagensPerfil($db, $usuario_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo isset($perfil['nome']) ? htmlspecialchars($perfil['nome']) : ''; ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
                    <a class="nav-link" href="../controllers/logout.php">Sair</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <h2>Perfil de <?php echo isset($perfil['nome']) ? htmlspecialchars($perfil['nome']) : ''; ?></h2>
                <?php foreach ($postagens as $postagem) { ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p class="card-text"><?php echo isset($postagem['conteudo']) ? htmlspecialchars($postagem['conteudo']) : ''; ?></p>
                            <p class="card-text">
                                <small class="text-muted">Postado em <?php echo isset($postagem['data_criacao']) ? $postagem['data_criacao'] : ''; ?></small>
                            </p>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-4">
                <h3>Informações do Perfil</h3>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Nome:</strong> <?php echo isset($perfil['nome']) ? htmlspecialchars($perfil['nome']) : ''; ?></li>
                    <li class="list-group-item"><strong>Email:</strong> <?php echo isset($perfil['email']) ? htmlspecialchars($perfil['email']) : ''; ?></li>
                    <li class="list-group-item"><strong>Biografia:</strong> <?php echo isset($perfil['biografia']) ? htmlspecialchars($perfil['biografia']) : ''; ?></li>
                    <li class="list-group-item"><strong>Total de visitas:</strong> <?php echo $num_visitas; ?></li>
                    <li class="list-group-item"><strong>Seguidores:</strong> <?php echo $numero_seguidores; ?></li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
require '../back/config.php';
autenticar();

$usuario_id = $_SESSION['id'];
require '../back/perfilService.php';

$perfil = obterPerfil($db, $usuario_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações do Perfil</title>
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
                    <a class="nav-link" href="../back/logout.php">Sair</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Configurações do Perfil</h2>
        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo isset($perfil['nome']) ? htmlspecialchars($perfil['nome']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($perfil['email']) ? htmlspecialchars($perfil['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="biografia">Biografia</label>
                <textarea class="form-control" id="biografia" name="biografia"><?php echo isset($perfil['biografia']) ? htmlspecialchars($perfil['biografia']) : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
        <form id="form-deletar-conta" method="POST">
            <button type="submit" class="btn btn-danger mt-3">Deletar Conta</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $('#form-deletar-conta').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '../back/deletarConta.php',
                method: 'POST',
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        window.location.href = '../front/login.php';
                    } else {
                        alert('Erro ao deletar conta.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao deletar conta:', error);
                }
            });
        });
    </script>
</body>
</html>

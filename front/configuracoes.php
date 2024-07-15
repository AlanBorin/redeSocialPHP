<?php
require '../back/config.php';
autenticar();

$usuario_id = $_SESSION['id'];
require '../back/perfilService.php';

$perfil = obterPerfil($db, $usuario_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['atualizar_perfil'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $biografia = $_POST['biografia'];

        if (atualizarPerfil($db, $usuario_id, $nome, $email, $biografia)) {
            $_SESSION['nome'] = $nome;
            header('Location: configuracoes.php?msg=Perfil atualizado com sucesso');
            exit;
        } else {
            $erro = "Erro ao atualizar perfil.";
        }
    } elseif (isset($_POST['alterar_senha'])) {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];

        if ($nova_senha !== $confirmar_senha) {
            $erro_senha = "As senhas não coincidem.";
        } else {
            if (atualizarSenha($db, $usuario_id, $senha_atual, $nova_senha)) {
                header('Location: configuracoes.php?msg=Senha atualizada com sucesso');
                exit;
            } else {
                $erro_senha = "Erro ao atualizar senha.";
            }
        }
    }
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
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Voltar à Página Inicial</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Configurações do Perfil</h2>
        <?php if (isset($erro)) { ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
        <?php } ?>
        <?php if (isset($_GET['msg'])) { ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php } ?>
        <form method="POST">
            <input type="hidden" name="atualizar_perfil" value="1">
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
        <hr>
        <h2>Alterar Senha</h2>
        <?php if (isset($erro_senha)) { ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($erro_senha); ?></div>
        <?php } ?>
        <form method="POST">
            <input type="hidden" name="alterar_senha" value="1">
            <div class="form-group">
                <label for="senha_atual">Senha Atual</label>
                <input type="password" class="form-control" id="senha_atual" name="senha_atual" required>
            </div>
            <div class="form-group">
                <label for="nova_senha">Nova Senha</label>
                <input type="password" class="form-control" id="nova_senha" name="nova_senha" required>
            </div>
            <div class="form-group">
                <label for="confirmar_senha">Confirmar Nova Senha</label>
                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            <button type="submit" class="btn btn-primary">Alterar Senha</button>
        </form>
        <hr>
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

<?php
require '../back/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Cadastro</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        <h3 class="text-center">Cadastro de Nova Conta</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['error'])) {
                            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
                        }
                        ?>
                        <form method="POST" action="/back/cadastrar_usuario.php">
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="nome">Nome:</label>
                                <input type="text" name="nome" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="senha">Senha:</label>
                                <input type="password" name="senha" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
                        </form>
                        <div class="text-center mt-3">
                            <p>Já tem uma conta? <a href="login.php" class="btn btn-secondary">Fazer Login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

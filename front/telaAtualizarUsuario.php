<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar</title>
</head>

<body>
    <?php
    require '../back/config.php';

    $id = $_GET['id'];

    $stmt = $db->prepare('SELECT * FROM usuarios WHERE id = :id');
    $stmt->bindValue(':id', $id);
    $result = $stmt->execute();

    $usuario = $result->fetchArray(SQLITE3_ASSOC);
    $email = trim($usuario['email']);
    $nome = $usuario['nome'];
    ?>

    <h1> Formulário de Atualização </h1>
    <?php
    echo "<form method=\"POST\" action=\"atualizar_usuario.php?id=$id\"\>";
    ?>
    <label for="id">ID:</label>
    <input type="text" name="id" value="<?php echo $id; ?>" readonly required /> <br><br>

    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo $email; ?>" required /> <br><br>

    <label for="nome">Nome:</label>
    <input type="text" name="nome" value="<?php echo $nome ?>" required /> <br><br>

    <button type="submit">Atualizar</button>
    </form>
</body>

</html>
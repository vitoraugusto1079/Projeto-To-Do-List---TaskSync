<?php
require_once 'config/conexao.php';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);

    if (!empty($nome) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email) VALUES (:nome, :email)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $mensagem = "<p style='color: green;'>Usuário cadastrado com sucesso!</p>";
        } catch (PDOException $e) {
            $mensagem = "<p style='color: red;'>Erro ao cadastrar: " . $e->getMessage() . "</p>";
        }
    } else {
        $mensagem = "<p style='color: red;'>Por favor, preencha todos os campos.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Gerenciamento</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="topo-site">
        <img src="img/logo.png" alt="Logo EasyManager">
    </header>
    <h1>Cadastrar Novo Usuário</h1>
    
    <?= $mensagem ?>

    <form method="POST" action="cadastrar_usuario.php">
        <div>
            <label for="nome">Nome do Colaborador:</label>
            <input type="text" id="nome" name="nome" required>
        </div>
        <br>
        <div>
            <label for="email">E-mail Corporativo:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <br>
        <button type="submit">Cadastrar Usuário</button>
    </form>

    <br>
    <a href="index.php">Voltar para o Gerenciamento de Tarefas</a>
</body>
</html>
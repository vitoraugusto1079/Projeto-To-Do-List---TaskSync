<?php
require_once 'config/conexao.php';
$mensagem = '';

try {
    $stmt_usuarios = $pdo->query("SELECT id_usuario, nome FROM usuarios ORDER BY nome ASC");
    $usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar usuários: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'];
    $descricao = trim($_POST['descricao']);
    $setor = trim($_POST['setor']);
    $prioridade = $_POST['prioridade'];
    $data_cadastro = $_POST['data_cadastro'];

    if (!empty($id_usuario) && !empty($descricao) && !empty($setor) && !empty($prioridade) && !empty($data_cadastro)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tarefas (id_usuario, descricao, setor, prioridade, data_cadastro) VALUES (:id_usuario, :descricao, :setor, :prioridade, :data_cadastro)");
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':setor', $setor);
            $stmt->bindParam(':prioridade', $prioridade);
            $stmt->bindParam(':data_cadastro', $data_cadastro);
            $stmt->execute();
            
            $mensagem = "<p style='color: green;'>Tarefa cadastrada com sucesso!</p>";
        } catch (PDOException $e) {
            $mensagem = "<p style='color: red;'>Erro ao cadastrar tarefa: " . $e->getMessage() . "</p>";
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
    <h1>Cadastrar Nova Tarefa</h1>
    
    <?= $mensagem ?>

    <form method="POST" action="cadastrar_tarefa.php">
        <div>
            <label for="id_usuario">Usuário Responsável:</label>
            <select id="id_usuario" name="id_usuario" required>
                <option value="">Selecione um usuário...</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario['id_usuario'] ?>"><?= htmlspecialchars($usuario['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>
        <div>
            <label for="descricao">Descrição da Tarefa:</label><br>
            <textarea id="descricao" name="descricao" rows="4" cols="50" required></textarea>
        </div>
        <br>
        <div>
            <label for="setor">Setor:</label>
            <input type="text" id="setor" name="setor" required>
        </div>
        <br>
        <div>
            <label for="prioridade">Prioridade:</label>
            <select id="prioridade" name="prioridade" required>
                <option value="baixa">Baixa</option>
                <option value="média">Média</option>
                <option value="alta">Alta</option>
            </select>
        </div>
        <br>
        <div>
            <label for="data_cadastro">Data de Cadastro:</label>
            <input type="date" id="data_cadastro" name="data_cadastro" required>
        </div>
        <br>
        <button type="submit">Cadastrar Tarefa</button>
    </form>

    <br>
    <a href="index.php">Voltar para o Gerenciamento de Tarefas</a>
</body>
</html>
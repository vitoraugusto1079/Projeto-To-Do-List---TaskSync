<?php
require_once 'config/conexao.php';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tarefa = $_POST['id_tarefa'];
    $id_usuario = $_POST['id_usuario'];
    $descricao = trim($_POST['descricao']);
    $setor = trim($_POST['setor']);
    $prioridade = $_POST['prioridade'];

    if (!empty($id_usuario) && !empty($descricao) && !empty($setor) && !empty($prioridade)) {
        try {
            $stmt = $pdo->prepare("UPDATE tarefas SET id_usuario = :id_usuario, descricao = :descricao, setor = :setor, prioridade = :prioridade WHERE id_tarefa = :id_tarefa");
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':setor', $setor);
            $stmt->bindParam(':prioridade', $prioridade);
            $stmt->bindParam(':id_tarefa', $id_tarefa);
            $stmt->execute();
            
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $mensagem = "<p style='color: red;'>Erro ao atualizar a tarefa: " . $e->getMessage() . "</p>";
        }
    } else {
        $mensagem = "<p style='color: red;'>Por favor, preencha todos os campos.</p>";
    }
}

if (isset($_GET['id'])) {
    $id_tarefa = $_GET['id'];
    
    try {
        $stmt_tarefa = $pdo->prepare("SELECT * FROM tarefas WHERE id_tarefa = :id");
        $stmt_tarefa->bindParam(':id', $id_tarefa);
        $stmt_tarefa->execute();
        $tarefa = $stmt_tarefa->fetch(PDO::FETCH_ASSOC);

        if (!$tarefa) {
            die("Tarefa não encontrada.");
        }

        $stmt_usuarios = $pdo->query("SELECT id_usuario, nome FROM usuarios ORDER BY nome ASC");
        $usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Erro ao buscar dados: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit;
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
    <h1>Editar Tarefa</h1>
    
    <?= $mensagem ?>

    <form method="POST" action="editar_tarefa.php?id=<?= $tarefa['id_tarefa'] ?>">
        <input type="hidden" name="id_tarefa" value="<?= $tarefa['id_tarefa'] ?>">
        
        <div>
            <label for="id_usuario">Usuário Responsável:</label>
            <select id="id_usuario" name="id_usuario" required>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario['id_usuario'] ?>" <?= ($tarefa['id_usuario'] == $usuario['id_usuario']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($usuario['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>
        <div>
            <label for="descricao">Descrição da Tarefa:</label><br>
            <textarea id="descricao" name="descricao" rows="4" cols="50" required><?= htmlspecialchars($tarefa['descricao']) ?></textarea>
        </div>
        <br>
        <div>
            <label for="setor">Setor:</label>
            <input type="text" id="setor" name="setor" value="<?= htmlspecialchars($tarefa['setor']) ?>" required>
        </div>
        <br>
        <div>
            <label for="prioridade">Prioridade:</label>
            <select id="prioridade" name="prioridade" required>
                <option value="baixa" <?= ($tarefa['prioridade'] == 'baixa') ? 'selected' : '' ?>>Baixa</option>
                <option value="média" <?= ($tarefa['prioridade'] == 'média') ? 'selected' : '' ?>>Média</option>
                <option value="alta" <?= ($tarefa['prioridade'] == 'alta') ? 'selected' : '' ?>>Alta</option>
            </select>
        </div>
        <br>
        <button type="submit">Salvar Alterações</button>
    </form>

    <br>
    <a href="index.php">Cancelar e Voltar</a>
</body>
</html>
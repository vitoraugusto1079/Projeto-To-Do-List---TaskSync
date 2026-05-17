<?php
require_once 'config/conexao.php';

try {
    $sql = "SELECT t.*, u.nome AS nome_usuario 
            FROM tarefas t 
            JOIN usuarios u ON t.id_usuario = u.id_usuario 
            ORDER BY t.data_cadastro DESC";
    $stmt = $pdo->query($sql);
    $tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar tarefas: " . $e->getMessage());
}

$a_fazer = [];
$fazendo = [];
$concluido = [];

foreach ($tarefas as $tarefa) {
    if ($tarefa['status'] === 'a fazer') {
        $a_fazer[] = $tarefa;
    } elseif ($tarefa['status'] === 'fazendo') {
        $fazendo[] = $tarefa;
    } elseif ($tarefa['status'] === 'concluído') {
        $concluido[] = $tarefa;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>TaskSync - Gerenciamento</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #c6c6ff; margin: 20px; }
        .menu { margin-bottom: 20px; }
        .kanban-board { display: flex; gap: 20px; }
        .coluna { flex: 1; background-color: #ffffff; border-radius: 10px; padding: 10px; min-height: 400px; }
        .coluna h2 { text-align: center; font-size: 1.2rem; }
        .card { background-color: #fff; padding: 15px; margin-bottom: 10px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .prioridade-alta { border-left: 5px solid red; }
        .prioridade-média { border-left: 5px solid orange; }
        .prioridade-baixa { border-left: 5px solid green; }
        .acoes { margin-top: 10px; }
    </style>
</head>
<body>
    <header class="topo-site">
        <img src="img/logo.png" alt="Logo EasyManager">
    </header>
    <div class="conteudo">
        <h1>TaskSync</h1>
    <div class="menu">
        <a href="cadastrar_usuario.php"><button>+ Novo Usuário</button></a>
        <a href="cadastrar_tarefa.php"><button>+ Nova Tarefa</button></a>
    </div>

    <div class="kanban-board">
        <div class="coluna">
            <h2>A Fazer</h2>
            <?php foreach ($a_fazer as $t): ?>
                <div class="card prioridade-<?= $t['prioridade'] ?>">
                    <p><strong>Descrição:</strong> <?= htmlspecialchars($t['descricao']) ?></p>
                    <p><strong>Responsável:</strong> <?= htmlspecialchars($t['nome_usuario']) ?></p>
                    <p><strong>Setor:</strong> <?= htmlspecialchars($t['setor']) ?></p>
                    <p><strong>Prioridade:</strong> <?= ucfirst($t['prioridade']) ?></p>
                    <div class="acoes">
                        <a href="editar_tarefa.php?id=<?= $t['id_tarefa'] ?>">Editar</a> | 
                        <a href="#" onclick="confirmarExclusao(event, 'excluir_tarefa.php?id=<?= $t['id_tarefa'] ?>')">Excluir</a>
                        <a href="atualizar_status.php?id=<?= $t['id_tarefa'] ?>&status=fazendo">▶ Fazer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="coluna">
            <h2>Fazendo</h2>
            <?php foreach ($fazendo as $t): ?>
                <div class="card prioridade-<?= $t['prioridade'] ?>">
                    <p><strong>Descrição:</strong> <?= htmlspecialchars($t['descricao']) ?></p>
                    <p><strong>Responsável:</strong> <?= htmlspecialchars($t['nome_usuario']) ?></p>
                    <div class="acoes">
                        <a href="editar_tarefa.php?id=<?= $t['id_tarefa'] ?>">Editar</a> | 
                        <a href="#" onclick="confirmarExclusao(event, 'excluir_tarefa.php?id=<?= $t['id_tarefa'] ?>')">Excluir</a>
                        <a href="atualizar_status.php?id=<?= $t['id_tarefa'] ?>&status=concluído">✔ Concluir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="coluna">
            <h2>Concluído</h2>
            <?php foreach ($concluido as $t): ?>
                <div class="card prioridade-<?= $t['prioridade'] ?>">
                    <p><strong>Descrição:</strong> <?= htmlspecialchars($t['descricao']) ?></p>
                    <p><strong>Responsável:</strong> <?= htmlspecialchars($t['nome_usuario']) ?></p>
                    <div class="acoes">
                        <a href="#" onclick="confirmarExclusao(event, 'excluir_tarefa.php?id=<?= $t['id_tarefa'] ?>')">Excluir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmarExclusao(event, urlDestino) {
        event.preventDefault(); 

        Swal.fire({
            title: "Tem certeza?",
            text: "Você não poderá reverter essa exclusão!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#e53935", // Vermelho para a ação de perigo
            cancelButtonColor: "#6a1b9a",  // Roxo da sua paleta para cancelar
            confirmButtonText: "Sim, excluir!",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            // Se o usuário clicou em "Sim", a gente redireciona para o PHP fazer a exclusão
            if (result.isConfirmed) {
                window.location.href = urlDestino;
            }
        });
    }

    // 2. Lógica que já tínhamos para ler a URL (o "msg=excluido")
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'excluido'): ?>
                Swal.fire({
                    title: 'Excluída!',
                    text: 'A tarefa foi removida do sistema.',
                    icon: 'success',
                    confirmButtonColor: '#6a1b9a'
                });
            <?php endif; ?>
            window.history.replaceState(null, null, window.location.pathname);
        <?php endif; ?>
    });
</script>
</body>
</html>
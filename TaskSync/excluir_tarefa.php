<?php
require_once 'config/conexao.php';

if (isset($_GET['id'])) {
    $id_tarefa = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM tarefas WHERE id_tarefa = :id");
        $stmt->bindParam(':id', $id_tarefa);
        $stmt->execute();
    } catch (PDOException $e) {
        die("Erro ao excluir tarefa: " . $e->getMessage());
    }
}

header('Location: index.php?msg=excluido');
exit;
?>
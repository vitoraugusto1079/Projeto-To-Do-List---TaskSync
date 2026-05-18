<?php
require_once 'config/conexao.php';
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id_tarefa = $_GET['id'];
    $novo_status = $_GET['status'];

    try {
        $stmt = $pdo->prepare("UPDATE tarefas SET status = :status WHERE id_tarefa = :id");
        $stmt->bindParam(':status', $novo_status);
        $stmt->bindParam(':id', $id_tarefa);
        $stmt->execute();
    } catch (PDOException $e) {
        die("Erro ao atualizar status: " . $e->getMessage());
    }
}

header('Location: index.php');
exit;
?>
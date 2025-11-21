<?php
require_once 'auth.php';
require_login();
require_once 'pdo.php';

$pdo = getPDO();

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo "ID de ticket inválido.";
    exit;   
}

try {
    $pdo->beginTransaction();

    // Verificar que el ticket existe
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$id]);
    $ticket = $stmt->fetch();
    if (!$ticket) {
        throw new Exception("Ticket no encontrado.");
    }

    // Simulacion d fallo (descomentar para probar manejo de errores
    // throw new Exception("Error simulado para probar manejo de transacciones."

    
    // Marcar el ticket como borrado (soft delete)
    $stmt = $pdo->prepare("UPDATE tickets SET deleted_at = NOW() WHERE id = ?");
    $stmt->execute([$id]);

    // Auditoria
    $stmt = $pdo->prepare("INSERT INTO ticket_audit (ticket_id, action) VALUES (?, 'borrado')");
    $stmt->execute([$id]);

    $pdo->commit();
    header('Location: lista_tickets.php');
    echo "Ticket borrado correctamente.";
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Error al borrar el ticket: " . $e->getMessage();
}
?>
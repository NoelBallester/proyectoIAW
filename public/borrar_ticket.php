<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/csrf.php';

$pdo = getPDO();

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método no permitido.";
    exit;
}

// Comprobar CSRF
check_csrf();

$id = $_POST['id'] ?? null;
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
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$ticket) {
        throw new Exception("Ticket no encontrado.");
    }

    // Marcar el ticket como borrado (soft delete)
    $stmt = $pdo->prepare("UPDATE tickets SET deleted_at = NOW() WHERE id = ?");
    $stmt->execute([$id]);

    // Auditoria
    $stmt = $pdo->prepare("INSERT INTO ticket_audit (ticket_id, action) VALUES (?, 'borrado')");
    $stmt->execute([$id]);

    $pdo->commit();
    header('Location: lista_tickets.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Error al borrar el ticket: " . $e->getMessage();
}
?>
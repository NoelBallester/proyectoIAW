<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: borrar_ticket.php
// DESCRIPCIÓN: Marca un ticket como eliminado (Soft Delete) y audita la acción.
// ALUMNOS: Noel Ballester Baños y Ángela Navarro Nieto 2º ASIR
// =================================================================

require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/pdo.php';

// Iniciamos conexión
$pdo = getPDO();

// 1. RECOGIDA Y VALIDACIÓN DEL ID
// Usamos filter_input para mayor seguridad en parámetros GET
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Error: ID de ticket no especificado o inválido.");
}

try {
    // 2. INICIO DE TRANSACCIÓN (Requisito Clave)
    // Iniciamos un bloque donde "todo ocurre o nada ocurre".
    $pdo->beginTransaction();

    // 3. VERIFICAR EXISTENCIA
    // Solo borramos si el ticket existe y no ha sido borrado previamente.
    $stmt = $pdo->prepare("SELECT id FROM tickets WHERE id = :id AND deleted_at IS NULL");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        throw new Exception("El ticket no existe o ya ha sido eliminado.");
    }

    // 4. SOFT DELETE (BORRADO LÓGICO)
    // Actualizamos la fecha de borrado. El dato sigue ahí, pero oculto.
    $stmt = $pdo->prepare("UPDATE tickets SET deleted_at = NOW() WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // 5. AUDITORÍA (OBLIGATORIO PARA NOTA DE 10)
    // Como ya has creado la tabla, activamos este bloque.
    // Guardamos qué usuario ha borrado el ticket.
    $userId = $_SESSION['user_id'] ?? 0;

    $stmt = $pdo->prepare("INSERT INTO ticket_audit (ticket_id, action, user_id, created_at) VALUES (:id, 'borrado', :user, NOW())");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':user', $userId, PDO::PARAM_INT);
    $stmt->execute();

    // 6. CONFIRMAR CAMBIOS (COMMIT)
    // Si las dos operaciones anteriores (UPDATE e INSERT) salieron bien, guardamos.
    $pdo->commit();

    // Redirección a la lista
    header('Location: lista_tickets.php');
    exit;

} catch (Exception $e) {
    // 7. DESHACER CAMBIOS (ROLLBACK)
    // Si falló el borrado o falló la auditoría, cancelamos TODO.
    // El ticket volverá a estar activo como si nada hubiera pasado.
    $pdo->rollBack();
    die("Error al borrar el ticket: " . $e->getMessage());
}
?>
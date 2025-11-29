<?php
// =================================================================
// PROYECTO: Gestión de Incidencias
// FICHERO: borrar_ticket.php
// DESCRIPCIÓN: Marca un ticket como eliminado (Soft Delete).
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
    // 2. INICIO DE TRANSACCIÓN
    // Asegura que las operaciones sean atómicas (todo o nada)
    $pdo->beginTransaction();

    // 3. VERIFICAR EXISTENCIA
    // Solo borramos si existe y no está borrado ya
    $stmt = $pdo->prepare("SELECT id FROM tickets WHERE id = :id AND deleted_at IS NULL");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        throw new Exception("El ticket no existe o ya ha sido eliminado.");
    }

    // 4. SOFT DELETE
    // Actualizamos fecha de borrado en vez de eliminar el registro físico
    $stmt = $pdo->prepare("UPDATE tickets SET deleted_at = NOW() WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // 5. CONFIRMAR CAMBIOS
    $pdo->commit();

    // Redirección a la lista
    header('Location: lista_tickets.php');
    exit;

} catch (Exception $e) {
    // Si falla algo, deshacemos cambios
    $pdo->rollBack();
    die("Error al borrar el ticket: " . $e->getMessage());
}
?>
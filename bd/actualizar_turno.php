<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$id_col = $_POST['id_col'] ?? null;

if ($id_col) {
    try {
        $stmt = $conexion->prepare("UPDATE colaborador 
                                   SET ultima_asignacion = NOW() 
                                   WHERE id_col = ?");
        $stmt->execute([$id_col]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID de colaborador no recibido']);
}
?>
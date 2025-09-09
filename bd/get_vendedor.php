<?php

include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$id_col = isset($_POST['id_col']) ? $_POST['id_col'] : '';

try {
    if ($id_col != '' && $id_col != '0') {
        // Buscar un vendedor específico
        $query = "SELECT id_col, nombre FROM colaborador WHERE edo_col = 1 AND id_col = :id_col ORDER BY nombre ASC";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id_col', $id_col, PDO::PARAM_INT);
    } else {
        // Listar todos los vendedores activos
        $query = "SELECT id_col, nombre FROM colaborador WHERE edo_col = 1 ORDER BY nombre ASC";
        $stmt = $conexion->prepare($query);
    }

    $stmt->execute();
    $colaborador = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($colaborador);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
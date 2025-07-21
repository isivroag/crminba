<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$id_clie = $_POST['id_clie'] ?? '';

if ($id_clie) {
    try {
        $stmt = $conexion->prepare("SELECT * FROM cliente WHERE id_clie = :id_clie ");
        $stmt->execute(['id_clie' => $id_clie]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        
        echo json_encode($resultados);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>

<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$texto = $_POST['texto'] ?? '';

if ($texto) {
    try {
        $stmt = $conexion->prepare("SELECT id_pros, nombre, correo FROM prospecto 
                                    WHERE nombre LIKE ? OR correo LIKE ? OR telefono LIKE ?
                                    LIMIT 10");
        $texto = "%$texto%";
        $stmt->execute([$texto, $texto, $texto]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($resultados);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>

<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$id_pros = $_POST['id_pros'] ?? null;

if ($id_pros) {
    try {
        $sql = "SELECT fecha_seg, tipo_seg, realizado, observaciones, edo_pros, id_col, nom_col_seg
                FROM vseg_pros
                WHERE id_pros = ?
                ORDER BY fecha_seg DESC, realizado ASC";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_pros]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>

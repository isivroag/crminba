<?php
include_once 'conexion.php';
$objeto = new conn();
$conn = $objeto->connect();

$folio = $_POST['folio'] ?? '';

if ($folio) {
    $stmt = $conn->prepare("SELECT id_reg as numero,fecha,capital,interes,importe as total,tipo,saldo FROM detalle_pres WHERE id_pres = :folio ORDER BY id_reg");
    $stmt->bindParam(":folio", $folio);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Siempre devuelve un array, aunque esté vacío
    echo json_encode($result);
} else {
    echo json_encode([]);
}

$conn = null;
// End of bd/buscar_detallepres.php
?>
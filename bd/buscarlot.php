<?php
// Incluir la conexión a la base de datos
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recepción de los datos enviados mediante POST desde el JS
$id = (isset($_POST['id'])) ? $_POST['id'] : '';
$action = (isset($_POST['action'])) ? $_POST['action'] : '';

// Inicializar la variable de respuesta
$data = 0;

if ($action === 'getAllLotes') {
    // Consulta para obtener todos los lotes
    $sql = "SELECT id_mapa AS id, clave_manzana, clave_lote, superficie, valortotal, status FROM lote";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($resultado) > 0) {
        $data = $resultado; // Devolver todos los lotes
    } else {
        $data = array(); // Devolver un arreglo vacío si no hay lotes
    }
} elseif (!empty($id)) {
    // Consulta para obtener un lote específico por su ID (clave)
    $sql = "SELECT clave_manzana,manzana, clave_lote, superficie, valortotal,status,preciom,id_proy,id_man FROM lote WHERE id_mapa = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($resultado) > 0) {
        $data = $resultado[0]; // Devolver el primer resultado (único)
    } else {
        $data = 0; // No se encontró el lote
    }
} else {
    $data = array('error' => 'Acción no válida o ID no proporcionado');
}

// Devolver la respuesta en formato JSON
print json_encode($data, JSON_UNESCAPED_UNICODE);

// Cerrar la conexión
$conexion = NULL;
?>
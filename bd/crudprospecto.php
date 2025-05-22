<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$response = ['success' => false, 'message' => ''];

$opcion = $_POST['opcion'] ?? 0;

$id = $_POST['id'] ?? null;
$nombre = $_POST['nombre'];
$telefono = $_POST['telefono'];
$correo = $_POST['correo'];
$col_asignado = $_POST['col_asignado'];
$origen = $_POST['origen'];

try {
    switch ($opcion) {
        case 1: // Crear nuevo prospecto


            $consulta = "INSERT INTO prospecto (nombre, telefono, correo, col_asignado, edo_pros,origen) 
                         VALUES (?, ?, ?, ?, 1)";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$nombre, $telefono, $correo, $col_asignado, $origen]);

            $id_pros = $conexion->lastInsertId();

            // Obtener datos para mostrar en tabla
            $consulta = "SELECT p.*, c.nombre as nombre_colaborador 
            FROM prospecto p
            JOIN colaborador c ON p.col_asignado = c.id_col
            WHERE p.id_pros = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$id_pros]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            $response = [
                'success' => true,
                'message' => 'Prospecto creado exitosamente',
                'id_pros' => $id_pros,
                'nombre' => $data['nombre'],
                'telefono' => $data['telefono'],
                'correo' => $data['correo'],
                'nombre_colaborador' => $data['nombre_colaborador'],
                'fecha_registro' => date('d/m/Y', strtotime($data['fecha_registro'])),
                'origen' => $data['origen']
            ];
            break;

        case 2: // Editar prospecto


            $consulta = "UPDATE prospecto SET 
                         nombre = ?, telefono = ?, correo = ?, col_asignado = ?,origen = ? 
                         WHERE id_pros = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$nombre, $telefono, $correo, $col_asignado, $origen, $id ]);

            // Obtener datos actualizados
            $consulta = "SELECT p.*, c.nombre as nombre_colaborador 
                         FROM prospecto p
                         JOIN colaborador c ON p.col_asignado = c.id_col
                         WHERE p.id_pros = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            $response = [
                'success' => true,
                'message' => 'Prospecto actualizado',
                'id_pros' => $id,
                'nombre' => $data['nombre'],
                'telefono' => $data['telefono'],
                'correo' => $data['correo'],
                'nombre_colaborador' => $data['nombre_colaborador'],
                'fecha_registro' => date('d/m/Y', strtotime($data['fecha_registro'])),
                'origen' => $data['origen']
            ];
            break;

        case 3: // Descartar prospecto
            $consulta = "UPDATE prospecto SET edo_pros = 3 WHERE id_pros = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$id]);

            $response = [
                'success' => true,
                'message' => 'Prospecto descartado'
            ];
            break;

        default:
            $response['message'] = 'Operación no válida';
    }
} catch (PDOException $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

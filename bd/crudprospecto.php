<?php
include_once 'conexion.php';
include_once 'bitacora.php';
$objeto = new conn();
$conexion = $objeto->connect();
$bitacora = new Bitacora($conexion);

$response = ['success' => false, 'message' => ''];

$opcion = $_POST['opcion'] ?? 0;

$id = $_POST['id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$telefono = $_POST['telefono'] ?? null;
$correo = $_POST['correo'] ?? null;
$col_asignado = $_POST['col_asignado'] ?? null;
$origen = $_POST['origen'] ?? null;
$id_usuario = $_POST['id_usuario'] ?? null;
$interes = $_POST['interes'] ?? null; // Nuevo campo para interés


function mayusculasEspanol($texto) {
    return mb_strtoupper($texto, 'UTF-8');
}

$nombre = mayusculasEspanol($nombre);
try {
    switch ($opcion) {
        case 1: // Crear nuevo prospecto



            $consulta = "INSERT INTO prospecto (nombre, telefono, correo, col_asignado, origen,id_usuario_alta,interes)
                         VALUES (:nombre, :telefono, :correo, :col_asignado, :origen, :id_usuario, :interes)";

            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':col_asignado', $col_asignado);
            $stmt->bindParam(':origen', $origen);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':interes', $interes);
            $stmt->execute();




            $id_pros = $conexion->lastInsertId();

            if (!$bitacora->registrar(
                'PROSPECTO',
                'CREACIÓN',
                $id_pros,
                "Nuevo prospecto: $nombre, Tel: $telefono"
            )) {
                throw new Exception("Error al registrar en bitácora");
            }

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
                'origen' => $data['origen'],
                'interes' => $data['interes']
            ];
            break;

        case 2: // Editar prospecto
            // Obtener datos anteriores para comparar
            $sql_old = "SELECT * FROM prospecto WHERE id_pros = ?";
            $stmt_old = $conexion->prepare($sql_old);
            $stmt_old->execute([$id]);
            $old_data = $stmt_old->fetch(PDO::FETCH_ASSOC);



            $consulta = "UPDATE prospecto SET 
                         nombre =:nombre, telefono = :telefono, correo = :correo, col_asignado = :col_asignado,origen = :origen,
                         interes = :interes 
                         WHERE id_pros = :id";


            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':col_asignado', $col_asignado);
            $stmt->bindParam(':origen', $origen);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':interes', $interes);
            // Ejecutar la consulta
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                $response['message'] = 'No se encontraron cambios para actualizar';
                echo json_encode($response);
                exit;
            }

            // Obtener datos actualizados
            $consulta = "SELECT p.*, c.nombre as nombre_colaborador 
                         FROM prospecto p
                         JOIN colaborador c ON p.col_asignado = c.id_col
                         WHERE p.id_pros = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            // Registrar cambios en bitácora
            $cambios = [];
            if ($old_data['nombre'] != $nombre) $cambios[] = "Nombre: {$old_data['nombre']} → $nombre";
            if ($old_data['telefono'] != $telefono) $cambios[] = "Teléfono: {$old_data['telefono']} → $telefono";
            if ($old_data['correo'] != $correo) $cambios[] = "Correo: {$old_data['correo']} → $correo";
            if ($old_data['col_asignado'] != $col_asignado) $cambios[] = "Asignado a: ID {$old_data['col_asignado']} → $col_asignado";
            if ($old_data['origen'] != $origen) $cambios[] = "Origen: {$old_data['origen']} → $origen";
            if ($old_data['interes'] != $interes) $cambios[] = "Interés: {$old_data['interes']} → $interes";

            $descripcion = "Actualización: " . implode(", ", $cambios);

            if (!$bitacora->registrar(
                'PROSPECTO',
                'ACTUALIZACIÓN',
                $id,
                $descripcion
            )) {
                throw new Exception("Error al registrar en bitácora");
            }

            $response = [
                'success' => true,
                'message' => 'Prospecto actualizado',
                'id_pros' => $id,
                'nombre' => $data['nombre'],
                'telefono' => $data['telefono'],
                'correo' => $data['correo'],
                'nombre_colaborador' => $data['nombre_colaborador'],
                'fecha_registro' => date('d/m/Y', strtotime($data['fecha_registro'])),
                'origen' => $data['origen'],
                'interes' => $data['interes']
            ];
            break;

        case 3: // Descartar prospecto
            $consulta = "UPDATE prospecto SET edo_pros = 3 WHERE id_pros = :id";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Registrar en bitácora
            if (!$bitacora->registrar(
                'PROSPECTO',
                'DESCARTE',
                $id,
                "Prospecto descartado (ID: $id)"
            )) {
                throw new Exception("Error al registrar en bitácora");
            }


            $response = [
                'success' => true,
                'message' => 'Prospecto descartado'
            ];
            break;

        case 4: // Verificar seguimientos
            $consulta = "SELECT COUNT(*) as count FROM seg_pros WHERE id_pros = :id";
            $stmt = $conexion->prepare($consulta);

            // Asegurarse de que el ID sea un entero
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            $response = [
                'success' => true,
                'count' => $data['count']
            ];

            break;
        case 5: // Inactivar prospecto
            $consulta = "UPDATE prospecto SET edo_pros = 3 WHERE id_pros = :id";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Registrar en bitácora
            if (!$bitacora->registrar(
                'PROSPECTO',
                'INACTIVACIÓN',
                $id,
                "Prospecto inactivado (ID: $id)"
            )) {
                throw new Exception("Error al registrar en bitácora");
            }

            $response = [
                'success' => true,
                'message' => 'Prospecto inactivado'
            ];
            break;
            case 6: // Activar prospecto
                $consulta = "UPDATE prospecto SET edo_pros = 1 WHERE id_pros = :id";
                $stmt = $conexion->prepare($consulta);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                // Registrar en bitácora
                if (!$bitacora->registrar(
                    'PROSPECTO',
                    'ACTIVACIÓN',
                    $id,
                    "Prospecto activado (ID: $id)"
                )) {
                    throw new Exception("Error al registrar en bitácora");
                }

                $response = [
                    'success' => true,
                    'message' => 'Prospecto activado'
                ];
                break;

        default:
            $response['message'] = 'Operación no válida';
    }
} catch (PDOException $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

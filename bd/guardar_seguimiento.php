<?php
include_once 'conexion.php';
include_once 'bitacora.php';
$objeto = new conn();


$conexion = $objeto->connect();
$bitacora = new Bitacora($conexion);

// Recepción de datos POST desde el JS
$id_pros = $_POST['id_pros'] ?? '';
$tipo_seg = $_POST['tipo_seg'] ?? '';
$fecha_seg = $_POST['fecha_seg'] ?? '';
$realizado = $_POST['realizado'] ?? '';
$comentarios = $_POST['comentarios'] ?? '';
$id_col = $_POST['id_col'] ?? '';
$opcion = $_POST['opcion'] ?? '';
$id_seg = $_POST['id_seg'] ?? '';
$res_seg = $_POST['resultado'] ?? '';
$obs_cierre = $_POST['obs_cierre'] ?? '';


$response = ["success" => false, "message" => ""];

switch ($opcion) {
    case 1:
        try {
            $consulta = "INSERT INTO seg_pros (id_pros, tipo_seg, fecha_seg, realizado, observaciones, id_col)
                         VALUES (:id_pros, :tipo_seg, :fecha_seg, :realizado, :comentarios, :id_col)";
            $resultado = $conexion->prepare($consulta);
            $resultado->bindParam(':id_pros', $id_pros, PDO::PARAM_INT);
            $resultado->bindParam(':tipo_seg', $tipo_seg, PDO::PARAM_STR);
            $resultado->bindParam(':fecha_seg', $fecha_seg, PDO::PARAM_STR);
            $resultado->bindParam(':realizado', $realizado, PDO::PARAM_INT);
            $resultado->bindParam(':comentarios', $comentarios, PDO::PARAM_STR);
            $resultado->bindParam(':id_col', $id_col, PDO::PARAM_INT);
            $resultado->execute();
            $idGenerado = $conexion->lastInsertId();

            // Actualizar seguimiento en tabla prospecto
            $updateColab = "UPDATE prospecto SET edo_seguimiento = 2 WHERE id_pros = :id_pros";
            $stmtColab = $conexion->prepare($updateColab);
            $stmtColab->bindParam(':id_pros', $id_pros, PDO::PARAM_INT);
            $stmtColab->execute();


            // Registrar en bitácora

             if (!$bitacora->registrar(
                'SEGUIMIENTO',
                'CREACIÓN',
                $idGenerado,
                "Nuevo Seguimiento Folio: $idGenerado, Al prospecto ID: $id_pros, Tipo: $tipo_seg"
            )) {
                throw new Exception("Error al registrar en bitácora");
            }


            $response = [
                "success" => true,
                "message" => "Seguimiento guardado correctamente.",
                "id_seg" => $idGenerado
            ];
        } catch (PDOException $e) {
            $response = [
                "success" => false,
                "message" => "Error al guardar el seguimiento: " . $e->getMessage()
            ];
        }
        break;

    case 2:
        try {
            $consulta = "UPDATE seg_pros SET tipo_seg = :tipo_seg, fecha_seg = :fecha_seg, realizado = :realizado, observaciones = :comentarios,
            resultado = :resultado, obs_cierre = :obs_cierre, fecha_cierre = NOW()
            WHERE id_seg = :id_seg";
            $resultado = $conexion->prepare($consulta);
            $resultado->bindParam(':id_seg', $id_seg, PDO::PARAM_INT);
            $resultado->bindParam(':tipo_seg', $tipo_seg, PDO::PARAM_STR);
            $resultado->bindParam(':fecha_seg', $fecha_seg, PDO::PARAM_STR);
            $resultado->bindParam(':realizado', $realizado, PDO::PARAM_INT);
            $resultado->bindParam(':comentarios', $comentarios, PDO::PARAM_STR);
            $resultado->bindParam(':resultado', $res_seg, PDO::PARAM_STR);
            
            $resultado->bindParam(':obs_cierre', $obs_cierre, PDO::PARAM_STR);
            $resultado->execute();

             if (!$bitacora->registrar(
                'SEGUIMIENTO',
                'MODIFICACION',
                $id_seg,
                "Modificacion Seguimiento Folio: $id_seg"
            )) {
                throw new Exception("Error al registrar en bitácora");
            }

            if ($resultado->rowCount() > 0) {
                $response = [
                    "success" => true,
                    "message" => "Seguimiento actualizado correctamente.",
                    "id_seg" => $id_seg
                ];
            } else {
                $response = [
                    "success" => false,
                    "message" => "No se encontró el seguimiento para actualizar."
                ];
            }
        } catch (PDOException $e) {
            $response = [
                "success" => false,
                "message" => "Error al actualizar el seguimiento: " . $e->getMessage()
            ];
        }
        break;
}

// Devuelve una sola respuesta JSON válida
echo json_encode($response, JSON_UNESCAPED_UNICODE);
$conexion = null;


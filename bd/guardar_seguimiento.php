<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recepción de datos POST desde el JS
$id_pros = isset($_POST['id_pros']) ? $_POST['id_pros'] : '';
$tipo_seg = isset($_POST['tipo_seg']) ? $_POST['tipo_seg'] : '';
$fecha_seg = isset($_POST['fecha_seg']) ? $_POST['fecha_seg'] : '';
$realizado = isset($_POST['realizado']) ? $_POST['realizado'] : '';
$comentarios = isset($_POST['comentarios']) ? $_POST['comentarios'] : '';
$id_col = isset($_POST['id_col']) ? $_POST['id_col'] : '';
$opcion = isset($_POST['opcion']) ? $_POST['opcion'] : '';
$id_seg = isset($_POST['id_seg']) ? $_POST['id_seg'] : '';

// Variable de respuesta
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

            $response['success'] = true;
            $response['message'] = "Seguimiento registrado correctamente.";

        } catch (PDOException $e) {
            $response['message'] = "Error al guardar el seguimiento: " . $e->getMessage();
        }
case 2:
        try {
            $consulta = "UPDATE seg_pros SET tipo_seg = :tipo_seg, fecha_seg = :fecha_seg, realizado = :realizado, observaciones = :comentarios
                         WHERE id_seg = :id_seg";
            $resultado = $conexion->prepare($consulta);
            $resultado->bindParam(':id_seg', $id_seg, PDO::PARAM_INT);
            $resultado->bindParam(':tipo_seg', $tipo_seg, PDO::PARAM_STR);
            $resultado->bindParam(':fecha_seg', $fecha_seg, PDO::PARAM_STR);
            $resultado->bindParam(':realizado', $realizado, PDO::PARAM_INT);
            $resultado->bindParam(':comentarios', $comentarios, PDO::PARAM_STR);
            $resultado->execute();

            if ($resultado->rowCount() > 0) {
                $response['success'] = true;
                $response['message'] = "Seguimiento actualizado correctamente.";
            } else {
                $response['message'] = "No se encontraron registros para actualizar.";
            }
        } catch (PDOException $e) {
            $response['message'] = "Error al actualizar el seguimiento: " . $e->getMessage();
        }
        break;
}
    
 
 

echo json_encode($response, JSON_UNESCAPED_UNICODE);
$conexion = null;

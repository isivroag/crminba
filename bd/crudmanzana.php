<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recepción de los datos enviados mediante POST desde el JS   
$id = (isset($_POST['id'])) ? $_POST['id'] : '';
$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$id_proy = (isset($_POST['id_proy'])) ? $_POST['id_proy'] : '';
$clave_proy = (isset($_POST['clave_proy'])) ? $_POST['clave_proy'] : '';
$clave = (isset($_POST['clave'])) ? $_POST['clave'] : '';
$descripcion = (isset($_POST['descripcion'])) ? $_POST['descripcion'] : '';

switch($opcion){
    case 1: // alta
        $consulta = "INSERT INTO manzana (id_proy, clave_proyecto, clave_manzana, descripcion) VALUES (:id_proy, :clave_proy, :clave, :descripcion)";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id_proy', $id_proy);
        $resultado->bindParam(':clave_proy', $clave_proy);
        $resultado->bindParam(':clave', $clave);
        $resultado->bindParam(':descripcion', $descripcion);
        $resultado->execute();

        $consulta = "SELECT * FROM manzana ORDER BY id_man DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 2: // modificación
        $consulta = "UPDATE manzana SET id_proy = :id_proy, clave_proyecto = :clave_proy, clave_manzana = :clave, descripcion = :descripcion WHERE id_man = :id";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id_proy', $id_proy);
        $resultado->bindParam(':clave_proy', $clave_proy);
        $resultado->bindParam(':clave', $clave);
        $resultado->bindParam(':descripcion', $descripcion);
        $resultado->bindParam(':id', $id);
        $resultado->execute();

        $consulta = "SELECT * FROM manzana WHERE id_man = :id";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id', $id);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 3: // baja (eliminación lógica, si tienes un campo estado, si no, puedes usar DELETE)
        $consulta = "SELECT COUNT(*) as total FROM lote WHERE id_man = :id";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id', $id);
        $resultado->execute();
        $row = $resultado->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['total'] == 0) {
            // No hay lotes relacionados, se puede eliminar
            $consulta = "DELETE FROM manzana WHERE id_man = :id";
            $resultado = $conexion->prepare($consulta);
            $resultado->bindParam(':id', $id);
            $resultado->execute();
            $data[] = array('respuesta' => 'ok');
        } else {
            // Hay lotes relacionados, no se puede eliminar
            $data[] = array('respuesta' => 'no');
        }
        break;
}

print json_encode($data, JSON_UNESCAPED_UNICODE); //enviar el array final en formato json a JS
$conexion = NULL;

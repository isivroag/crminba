<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$response = ['success' => false, 'message' => ''];

try {
    // Obtener datos del POST
    $id_prospecto = $_POST['id_prospecto'] ?? null;
    $rfc = $_POST['rfc'] ?? null;
    $tipo_ide = $_POST['tipo_ide'] ?? null;
    $folio_ide = $_POST['folio_ide'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $tel_cel = $_POST['tel_cel'] ?? null;
    $email = $_POST['email'] ?? null;
    $nacionalidad = $_POST['nacionalidad'] ?? 'MEXICANA';
    $dir_calle = $_POST['dir_calle'] ?? null;
    $dir_colonia = $_POST['dir_colonia'] ?? null;
    $dir_ciudad = $_POST['dir_ciudad'] ?? null;
    $dir_edo = $_POST['dir_edo'] ?? null;
    $dir_cp = $_POST['dir_cp'] ?? null;
    $especial = $_POST['especial'] ?? 0;
   

    // Validar campos obligatorios
    if (!$id_prospecto || !$tipo_ide || !$folio_ide || !$nombre || !$tel_cel) {
        throw new Exception("Faltan campos obligatorios");
    }

    // Iniciar transacción
    $conexion->beginTransaction();

    // Verificar que el prospecto existe y está activo
    $consultaProspecto = "SELECT * FROM prospecto WHERE id_pros = :id_prospecto AND edo_pros = 1";
    $stmtProspecto = $conexion->prepare($consultaProspecto);
    $stmtProspecto->bindParam(':id_prospecto', $id_prospecto);
    $stmtProspecto->execute();
    $prospecto = $stmtProspecto->fetch(PDO::FETCH_ASSOC);

    if (!$prospecto) {
        throw new Exception("Prospecto no encontrado o ya fue procesado");
    }





 

    // Insertar el cliente
    $consultaCliente = "INSERT INTO cliente (
        nombre, 
        dir_calle, 
        dir_ciudad, 
        dir_colonia, 
        dir_cp, 
        folio, 
        nacionalidad, 
        tel_cel, 
        email, 
        rfc, 
        dir_edo, 
        status, 
        especial,
        tipo_ide,
        id_pros
       
    ) VALUES (
        :nombre,
        :dir_calle,
        :dir_ciudad,
        :dir_colonia,
        :dir_cp,
        :folio_ide,
        :nacionalidad,
        :tel_cel,
        :email,
        :rfc,
        :dir_edo,
        'CORRECTO',
        :especial,
        :tipo_ide,
        :id_prospecto
    )";

    $stmtCliente = $conexion->prepare($consultaCliente);
    $stmtCliente->bindParam(':nombre', $nombre);
    $stmtCliente->bindParam(':dir_calle', $dir_calle);
    $stmtCliente->bindParam(':dir_ciudad', $dir_ciudad);
    $stmtCliente->bindParam(':dir_colonia', $dir_colonia);
    $stmtCliente->bindParam(':dir_cp', $dir_cp);
    $stmtCliente->bindParam(':folio_ide', $folio_ide);
    $stmtCliente->bindParam(':nacionalidad', $nacionalidad);
    $stmtCliente->bindParam(':tel_cel', $tel_cel);
    $stmtCliente->bindParam(':email', $email);
    $stmtCliente->bindParam(':rfc', $rfc);
    $stmtCliente->bindParam(':dir_edo', $dir_edo);
    $stmtCliente->bindParam(':especial', $especial);
    $stmtCliente->bindParam(':tipo_ide', $tipo_ide);
    $stmtCliente->bindParam(':id_prospecto', $id_prospecto);

    if (!$stmtCliente->execute()) {
        throw new Exception("Error al crear el cliente");
    }

    $id_cliente = $conexion->lastInsertId();

    // Actualizar el estado del prospecto a "convertido" (edo_pros = 3)
    $consultaUpdateProspecto = "UPDATE prospecto SET 
                               edo_seguimiento = 3, fecha_conversion = NOW()                          
                               WHERE id_pros = :id_prospecto";
    
    $stmtUpdateProspecto = $conexion->prepare($consultaUpdateProspecto);

    $stmtUpdateProspecto->bindParam(':id_prospecto', $id_prospecto);

    if (!$stmtUpdateProspecto->execute()) {
        throw new Exception("Error al actualizar el estado del prospecto");
    }

    // Insertar registro en historial de conversión
    

    // Confirmar transacción
    $conexion->commit();

    $response['success'] = true;
    $response['message'] = 'Cliente creado exitosamente';
    $response['id_cliente'] = $id_cliente;

} catch (PDOException $e) {
    $conexion->rollBack();
    $response['message'] = 'Error en la base de datos: ' . $e->getMessage();
    error_log("Error PDO en guardarcliente.php: " . $e->getMessage());
} catch (Exception $e) {
    $conexion->rollBack();
    $response['message'] = $e->getMessage();
    error_log("Error en guardarcliente.php: " . $e->getMessage());
}

echo json_encode($response);
$conexion = null;
?>
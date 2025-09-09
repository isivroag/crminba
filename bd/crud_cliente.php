<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$id_clie = (isset($_POST['id_clie'])) ? $_POST['id_clie'] : '';
$nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';
$telefono = (isset($_POST['telefono'])) ? $_POST['telefono'] : '';
$correo = (isset($_POST['correo'])) ? $_POST['correo'] : '';
$rfc = (isset($_POST['rfc'])) ? $_POST['rfc'] : '';
$dir_calle = (isset($_POST['dir_calle'])) ? $_POST['dir_calle'] : '';
$dir_ciudad = (isset($_POST['dir_ciudad'])) ? $_POST['dir_ciudad'] : '';
$dir_colonia = (isset($_POST['dir_colonia'])) ? $_POST['dir_colonia'] : '';
$dir_edo = (isset($_POST['dir_edo'])) ? $_POST['dir_edo'] : '';
$dir_cp = (isset($_POST['dir_cp'])) ? $_POST['dir_cp'] : '';
$folio_ide = (isset($_POST['folio_ide'])) ? $_POST['folio_ide'] : '';
$especial = (isset($_POST['especial'])) ? $_POST['especial'] : '0';
$tipo_ide = (isset($_POST['tipo_ide'])) ? $_POST['tipo_ide'] : '';
$nacionalidad = $_POST['nacionalidad'] ?? 'MEXICANA';
$col_asignado = $_POST['col_asignado'] ?? '';
$origen = $_POST['origen'] ?? '';


$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';


function mayusculasEspanol($texto)
{
    return mb_strtoupper($texto, 'UTF-8');
}


$nombre = mayusculasEspanol($nombre);
$dir_calle = mayusculasEspanol($dir_calle);
$dir_ciudad = mayusculasEspanol($dir_ciudad);
$dir_colonia = mayusculasEspanol($dir_colonia);
$dir_edo = mayusculasEspanol($dir_edo);
$rfc = mayusculasEspanol($rfc);
$folio_ide = mayusculasEspanol($folio_ide);


switch ($opcion) {
    case 1: //alta
        $consulta = "INSERT INTO cliente (
                rfc, nombre, tel_cel, email, folio, nacionalidad, 
                tipo_ide, dir_calle, dir_ciudad, dir_colonia, 
                dir_edo, dir_cp, especial,  status, col_asignado, origen
            ) VALUES (
                :rfc, :nombre, :tel_cel, :email, :folio, :nacionalidad,
                :tipo_ide, :dir_calle, :dir_ciudad, :dir_colonia,
                :dir_edo, :dir_cp, :especial, 'CORRECTO', :col_asignado, :origen
            )";

        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':rfc', $rfc);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':tel_cel', $telefono);
        $resultado->bindParam(':email', $correo);
        $resultado->bindParam(':folio', $folio_ide);
        $resultado->bindParam(':nacionalidad', $nacionalidad);
        $resultado->bindParam(':tipo_ide', $tipo_ide);
        $resultado->bindParam(':dir_calle', $dir_calle);
        $resultado->bindParam(':dir_ciudad', $dir_ciudad);
        $resultado->bindParam(':dir_colonia', $dir_colonia);
        $resultado->bindParam(':dir_edo', $dir_edo);
        $resultado->bindParam(':dir_cp', $dir_cp);
        $resultado->bindParam(':especial', $especial);
        $resultado->bindParam(':col_asignado', $col_asignado);
        $resultado->bindParam(':origen', $origen);

        $resultado->execute();

        $consulta = "SELECT * FROM cliente ORDER BY id_clie DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 2: //modificación

        $consulta = "UPDATE cliente SET 
                rfc = :rfc,
                nombre = :nombre, 
                tel_cel = :tel_cel, 
                email = :email, 
                folio = :folio, 
                nacionalidad = :nacionalidad,
                tipo_ide = :tipo_ide, 
                dir_calle = :dir_calle, 
                dir_ciudad = :dir_ciudad, 
                dir_colonia = :dir_colonia, 
                dir_edo = :dir_edo, 
                dir_cp = :dir_cp,
                especial = :especial,
                col_asignado = :col_asignado,
                origen = :origen
                WHERE id_clie = :id_clie";

                $resultado = $conexion->prepare($consulta);

        $resultado->bindParam(':rfc', $rfc);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':tel_cel', $telefono);
        $resultado->bindParam(':email', $correo);
        $resultado->bindParam(':folio', $folio_ide);
        $resultado->bindParam(':nacionalidad', $nacionalidad);
        $resultado->bindParam(':tipo_ide', $tipo_ide);
        $resultado->bindParam(':dir_calle', $dir_calle);
        $resultado->bindParam(':dir_ciudad', $dir_ciudad);
        $resultado->bindParam(':dir_colonia', $dir_colonia);
        $resultado->bindParam(':dir_edo', $dir_edo);
        $resultado->bindParam(':dir_cp', $dir_cp);      
        $resultado->bindParam(':id_clie', $id_clie);
        $resultado->bindParam(':especial', $especial);
        $resultado->bindParam(':col_asignado', $col_asignado);
        $resultado->bindParam(':origen', $origen);
        

        $resultado->execute();

        $consulta = "SELECT * FROM cliente WHERE id_clie=:id_clie";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id_clie', $id_clie);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 3: //baja
        $consulta = "UPDATE cliente SET edo_clie=0 WHERE id_clie=:id_clie";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(['id_clie' => $id_clie]);
        $data = 1;
        break;
    case 4: // consulta  
        $consulta = "SELECT * FROM cliente WHERE id_clie = :id_clie";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(['id_clie' => $id_clie]);
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

        break;
}

print json_encode($data, JSON_UNESCAPED_UNICODE); //enviar el array final en formato json a JS
$conexion = NULL;

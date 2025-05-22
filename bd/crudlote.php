<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recibe los datos por POST
$id_lote = (isset($_POST['id_lote'])) ? $_POST['id_lote'] : '';
$id_proy = (isset($_POST['id_proy'])) ? $_POST['id_proy'] : '';
$id_man = (isset($_POST['id_man'])) ? $_POST['id_man'] : '';
$clave_lote = (isset($_POST['clave_lote'])) ? $_POST['clave_lote'] : '';
$id_mapa = (isset($_POST['id_mapa'])) ? $_POST['id_mapa'] : '';
$manzana = (isset($_POST['manzana'])) ? $_POST['manzana'] : '';
$noroeste = (isset($_POST['noroeste'])) ? $_POST['noroeste'] : '';
$norte = (isset($_POST['norte'])) ? $_POST['norte'] : '';
$noreste = (isset($_POST['noreste'])) ? $_POST['noreste'] : '';
$oeste = (isset($_POST['oeste'])) ? $_POST['oeste'] : '';
$este = (isset($_POST['este'])) ? $_POST['este'] : '';
$suroeste = (isset($_POST['suroeste'])) ? $_POST['suroeste'] : '';
$sur = (isset($_POST['sur'])) ? $_POST['sur'] : '';
$sureste = (isset($_POST['sureste'])) ? $_POST['sureste'] : '';
$id_tipo = (isset($_POST['id_tipo'])) ? $_POST['id_tipo'] : '';
$superficie = (isset($_POST['superficie'])) ? $_POST['superficie'] : '';
$precio = (isset($_POST['precio'])) ? $_POST['precio'] : '';
$valortotal = (isset($_POST['valortotal'])) ? $_POST['valortotal'] : '';
$frente = (isset($_POST['frente'])) ? $_POST['frente'] : '';
$fondo = (isset($_POST['fondo'])) ? $_POST['fondo'] : '';
$construido = (isset($_POST['construido'])) ? $_POST['construido'] : '';
$indiviso = (isset($_POST['indiviso'])) ? $_POST['indiviso'] : '';
$renta = (isset($_POST['renta'])) ? $_POST['renta'] : '';
$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';



switch ($opcion) {
    case 1: // alta
        $consulta = "INSERT INTO lote 
            (id_proy, id_man, clave_lote, id_mapa, manzana, noroeste, norte, noreste, 
            oeste, este, suroeste, sur, sureste, id_tipo, superficie, precio, valortotal, frente, fondo, construido, indiviso, renta) 
            VALUES 
            (:id_proy, :id_man, :clave_lote, :id_mapa, :manzana, :noroeste, :norte, :noreste, 
            :oeste, :este, :suroeste, :sur, :sureste, :id_tipo, :superficie, :precio, :valortotal, :frente, :fondo, :construido, :indiviso, :renta)";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id_proy', $id_proy);
        $resultado->bindParam(':id_man', $id_man);
        $resultado->bindParam(':clave_lote', $clave_lote);
        $resultado->bindParam(':id_mapa', $id_mapa);
        $resultado->bindParam(':manzana', $manzana);
        $resultado->bindParam(':noroeste', $noroeste);
        $resultado->bindParam(':norte', $norte);
        $resultado->bindParam(':noreste', $noreste);
        $resultado->bindParam(':oeste', $oeste);
        $resultado->bindParam(':este', $este);
        $resultado->bindParam(':suroeste', $suroeste);
        $resultado->bindParam(':sur', $sur);
        $resultado->bindParam(':sureste', $sureste);
        $resultado->bindParam(':id_tipo', $id_tipo);
        $resultado->bindParam(':superficie', $superficie);
        $resultado->bindParam(':precio', $precio);
        $resultado->bindParam(':valortotal', $valortotal);
        $resultado->bindParam(':frente', $frente);
        $resultado->bindParam(':fondo', $fondo);
        $resultado->bindParam(':construido', $construido);
        $resultado->bindParam(':indiviso', $indiviso);
        $resultado->bindParam(':renta', $renta);

        if ($resultado->execute()) {
            $consulta = "SELECT * FROM lote WHERE id_proy=:id_proy and id_man=:id_man order by id_lote DESC LIMIT 1";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        } 
        break;
    case 2: // modificación
        $consulta = "UPDATE lote SET 
            id_proy = :id_proy,";

        // Puedes agregar más casos para editar, eliminar, etc.
}

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = NULL;

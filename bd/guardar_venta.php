<?php

include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();
header('Content-Type: application/json');

// Buscar presupuestos disponibles para venta
if (isset($_GET['buscar_presupuestos'])) {
    $consulta = "SELECT 
        p.id_pres,
        p.fecha_pres,
        p.nombre_clie AS cliente,
        p.nproyecto AS proyecto,
        p.nmanzana AS manzana,
        p.nlote AS lote,
        p.valorop,
        p.enganche,
        p.nenganche,
        p.nmsi,
        p.nmci
    FROM vpresupuesto p
    WHERE p.folio_vta = 0 AND p.edo_pres = 1
    ORDER BY p.id_pres DESC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $presupuestos = $resultado->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'presupuestos' => $presupuestos]);
    exit;
}

// Consultar presupuesto por id_pres
if (isset($_GET['id_pres'])) {
    $id_pres = $_GET['id_pres'];
    $consulta = "SELECT * FROM vpresupuesto WHERE id_pres = :id_pres";
    $stmtPres = $conexion->prepare($consulta);
    $stmtPres->bindParam(':id_pres', $id_pres);
    $stmtPres->execute();
    $pres = $stmtPres->fetch(PDO::FETCH_ASSOC);

    $consultaDet = "SELECT * FROM detalle_pres WHERE id_pres = :id_pres";
    $stmtDet = $conexion->prepare($consultaDet);
    $stmtDet->bindParam(':id_pres', $id_pres);
    $stmtDet->execute();
    $detalle = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'presupuesto' => $pres, 'detalle' => $detalle]);
    exit;
}

// Consultar venta por folio_venta
if (isset($_GET['folio_venta'])) {
    $folio_venta = $_GET['folio_venta'];
    $stmtVenta = $conexion->prepare("SELECT * FROM vventa WHERE folio_venta = :folio_venta");
    $stmtVenta->bindParam(':folio_venta', $folio_venta);
    $stmtVenta->execute();
    $venta = $stmtVenta->fetch(PDO::FETCH_ASSOC);

    $stmtCuenta = $conexion->prepare("SELECT * FROM cuenta WHERE folio_venta = :folio_venta");
    $stmtCuenta->bindParam(':folio_venta', $folio_venta);
    $stmtCuenta->execute();
    $cuenta = $stmtCuenta->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'venta' => $venta, 'cuenta' => $cuenta]);
    exit;
}

// Guardar venta (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pres'])) {
    $id_pres = $_POST['id_pres'];
    $fecha = $_POST['fecha'] ?? date('Y-m-d');
    $id_vendedor = $_POST['id_vendedor'] ?? 0;
    $nombre_vendedor = $_POST['nombre_vendedor'] ?? '';
    $tipo_venta = $_POST['tipo_venta'] ?? '02';
    $tipo = 1;
    $saldo_mod_met = 0;
    $totala = 0;

    // Obtener presupuesto
    $consultaPres = "SELECT * FROM vpresupuesto WHERE id_pres = :id_pres";
    $stmtPres = $conexion->prepare($consultaPres);
    $stmtPres->bindParam(':id_pres', $id_pres);
    $stmtPres->execute();
    $pres = $stmtPres->fetch(PDO::FETCH_ASSOC);


    // Validar que el presupuesto exista

    if (!$pres) {
        echo json_encode(['success' => false, 'message' => 'Presupuesto no encontrado']);
        exit;
    }

    // Insertar venta
    $consultaVenta = "INSERT INTO venta (
        clave_proyecto, id_proy, clave_manzana, id_man, clave_lote, id_lote, id_pres, fecha, total, enganche, saldo, status, id_clie, tipo, tipo_venta, tipo_proyecto, saldo_mod_met, totala, vendedor, id_vendedor
    ) VALUES (
        :clave_proyecto, :id_proy, :clave_manzana, :id_man, :clave_lote, :id_lote, :id_pres, :fecha, :total, :enganche, :saldo, 'VENTA', :id_clie, :tipo, :tipo_venta, :tipo_proyecto, :saldo_mod_met, :totala, :vendedor, :id_vendedor
    )";
    $stmtVenta = $conexion->prepare($consultaVenta);
    $stmtVenta->bindParam(':clave_proyecto', $pres['clave_proyecto']);
    $stmtVenta->bindParam(':id_proy', $pres['id_proy']);
    $stmtVenta->bindParam(':clave_manzana', $pres['clave_manzana']);
    $stmtVenta->bindParam(':id_man', $pres['id_man']);
    $stmtVenta->bindParam(':clave_lote', $pres['clave_lote']);
    $stmtVenta->bindParam(':id_lote', $pres['id_lote']);
    $stmtVenta->bindParam(':id_pres', $id_pres);
    $stmtVenta->bindParam(':fecha', $fecha);
    $stmtVenta->bindParam(':total', $pres['valorop']);
    $stmtVenta->bindParam(':enganche', $pres['enganche']);
    $stmtVenta->bindParam(':saldo', $pres['valorop']);
    $stmtVenta->bindParam(':id_clie', $pres['id_clie']);
    $stmtVenta->bindParam(':tipo', $tipo);
    $stmtVenta->bindParam(':tipo_venta', $tipo_venta);
    $stmtVenta->bindParam(':tipo_proyecto', $pres['tipo_proy']);
    $stmtVenta->bindParam(':saldo_mod_met', $saldo_mod_met);
    $stmtVenta->bindParam(':totala', $totala);
    $stmtVenta->bindParam(':vendedor', $nombre_vendedor);
    $stmtVenta->bindParam(':id_vendedor', $id_vendedor);

    if ($stmtVenta->execute()) {
        $folio_venta = $conexion->lastInsertId();

        // Cambiar lote a vendido
        $conexion->prepare("UPDATE lote SET status='VENDIDO' WHERE id_lote=:id_lote")
            ->execute([':id_lote' => $pres['id_lote']]);

        // Copiar corrida financiera
        $consultaDet = "SELECT * FROM detalle_pres WHERE id_pres = :id_pres";
        $stmtDet = $conexion->prepare($consultaDet);
        $stmtDet->bindParam(':id_pres', $id_pres);
        $stmtDet->execute();
        $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

        foreach ($detalles as $det) {
            $consultaCuenta = "INSERT INTO cuenta (
                folio_venta, numero, importe, interes, fecha_corte, interes_mor, total_pago, status, saldo_capital, tipo, proyecto, id_proy
            ) VALUES (
                :folio_venta, :numero, :importe, :interes, :fecha_corte, 0, :total_pago, 'ACTIVO', :saldo_capital, :tipo, :proyecto, :id_proy
            )";
            $stmtCuenta = $conexion->prepare($consultaCuenta);
            $stmtCuenta->bindParam(':folio_venta', $folio_venta);
            $stmtCuenta->bindParam(':numero', $det['id_reg']);
            $stmtCuenta->bindParam(':importe', $det['importe']);
            $stmtCuenta->bindParam(':interes', $det['interes']);
            $stmtCuenta->bindParam(':fecha_corte', $det['fecha']);
            $stmtCuenta->bindParam(':total_pago', $det['importe']);
            $stmtCuenta->bindParam(':saldo_capital', $det['saldo']);
            $stmtCuenta->bindParam(':tipo', $det['tipo']);
            $stmtCuenta->bindParam(':proyecto', $pres['clave_proyecto']);
            $stmtCuenta->bindParam(':id_proy', $pres['id_proy']);
            $stmtCuenta->execute();
        }

        // Actualizar presupuesto: folio_vta y edo_pres
        $conexion->prepare("UPDATE presupuesto SET folio_vta=:folio_venta, edo_pres=2 WHERE id_pres=:id_pres")
            ->execute([':folio_venta' => $folio_venta, ':id_pres' => $id_pres]);

        echo json_encode(['success' => true, 'folio_venta' => $folio_venta]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la venta']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Petición inválida']);


<?php
$pagina = "cntaventa";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Consulta para obtener los presupuestos con datos relacionados
$consulta = "SELECT 
    v.folio_venta,
    v.fecha,
    v.nombre_clie AS cliente,
    v.nproyecto AS proyecto,
    v.nmanzana AS manzana,
    v.nlote AS lote,
    v.total,
    v.saldo,
    v.saldo_mod_met,
    v.vendedor
FROM vventa v
where v.edo_venta = 1
ORDER BY v.folio_venta DESC";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<div class="content-wrapper">
    <section class="content">
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">VENTAS</h1>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table name="tablaV" id="tablaV" class="table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:14px">
                                    <thead class="text-center bg-green">
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Proyecto</th>
                                            <th>Manzana</th>
                                            <th>Lote</th>
                                            <th>Valor Operación</th>
                                            <th>Saldo</th>
                                            <th>Saldo Mod. Met.</th>
                                            <th>Vendedor</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $row) { ?>
                                            <tr>
                                                <td class="text-center"><?php echo $row['folio_venta']; ?></td>
                                                <td class="text-center"><?php echo $row['fecha']; ?></td>
                                                <td><?php echo htmlspecialchars($row['cliente']); ?></td>
                                                <td><?php echo htmlspecialchars($row['proyecto']); ?></td>
                                                <td><?php echo htmlspecialchars($row['manzana']); ?></td>
                                                <td class="text-center"><?php echo htmlspecialchars($row['lote']); ?></td>
                                                <td class="text-right"><?php echo number_format($row['total'], 2); ?></td>
                                                <td class="text-right"><?php echo number_format($row['saldo'], 2); ?></td>
                                                <td class="text-center"><?php echo number_format($row['saldo_mod_met'], 2); ?></td>
                                                <td class="text-center"><?php echo $row['vendedor']; ?></td>
                                                <td class="text-center">
                                                    
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>

                                    <tfoot>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntaventa.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script>

</script>
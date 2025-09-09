
<?php
$pagina = "cntacot";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Consulta para obtener los presupuestos con datos relacionados
$consulta = "SELECT 
    p.id_pres,
    p.fecha_pres,
    p.nombre_pros AS prospecto,
    p.nproyecto AS proyecto,
    p.nmanzana AS manzana,
    p.nlote AS lote,
    p.valorop,
    p.enganche,
    p.nenganche,
    p.nmsi,
    p.nmci
FROM vcotizacion p
where p.edo_pres = 1
ORDER BY p.id_pres DESC";
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
                <h1 class="card-title mx-auto">COTIZACIONES</h1>
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
                                            <th>Prospecto</th>
                                            <th>Proyecto</th>
                                            <th>Manzana</th>
                                            <th>Lote</th>
                                            <th>Valor Operación</th>
                                            <th>Enganche</th>
                                            <th># MENG</th>
                                            <th># MSI</th>
                                            <th># MCI</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $row) { ?>
                                            <tr>
                                                <td class="text-center"><?php echo $row['id_pres']; ?></td>
                                                <td class="text-center"><?php echo $row['fecha_pres']; ?></td>
                                                <td><?php echo htmlspecialchars($row['prospecto']); ?></td>
                                                <td><?php echo htmlspecialchars($row['proyecto']); ?></td>
                                                <td><?php echo htmlspecialchars($row['manzana']); ?></td>
                                                <td class="text-center"><?php echo htmlspecialchars($row['lote']); ?></td>
                                                <td class="text-right"><?php echo number_format($row['valorop'], 2); ?></td>
                                                <td class="text-right"><?php echo number_format($row['enganche'], 2); ?></td>
                                                <td class="text-center"><?php echo number_format($row['nenganche'], 0); ?></td>
                                                <td class="text-center"><?php echo number_format($row['nmsi'], 0); ?></td>
                                                <td class="text-center"><?php echo number_format($row['nmci'], 0); ?></td>
                                                <td class="text-center">
                                                    
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
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
<script src="fjs/cntacot.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script>

</script>
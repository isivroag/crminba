<?php
$pagina = "cntaapartado";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Actualizar automáticamente los apartados vencidos (más de 9 días)
$updateVencidos = "UPDATE apartado SET status = 'CANCELADO' 
                   WHERE status = 'ACTIVO' 
                   AND DATEDIFF(NOW(), fecha_apartado) > 9";
$conexion->prepare($updateVencidos)->execute();

// Actualizar status de lotes cancelados
$updateLotesCancelados = "UPDATE lote l 
                         INNER JOIN apartado a ON l.id_lote = a.id_lote 
                         SET l.status = 'DISPONIBLE' 
                         WHERE a.status = 'CANCELADO' AND l.status = 'APARTADO'";
$conexion->prepare($updateLotesCancelados)->execute();

// Consulta para obtener apartados con información relacionada
$consulta = "SELECT * FROM vapartado";

$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener colaboradores activos
$consulta_colab = "SELECT * FROM colaborador WHERE edo_col = 1 ORDER BY nombre";
$resultado_colab = $conexion->prepare($consulta_colab);
$resultado_colab->execute();
$colaboradores = $resultado_colab->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<style>
.apartado-warning {
    background-color: #fff3cd !important;
    color: #856404 !important;
}
.apartado-danger {
    background-color: #f8d7da !important;
    color: #721c24 !important;
}
.multi-line {
    white-space: normal !important;
    max-width: 200px;
    word-break: break-word;
}
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">APARTADOS</h1>
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
                                            <th>FECHA</th>
                                            <th>CLIENTE</th>
                                            <th>TELÉFONO</th>
                                            <th>PROYECTO</th>
                                            <th>MANZANA</th>
                                            <th>LOTE</th>
                                            <th>SUPERFICIE</th>
                                            <th>VALOR TOTAL</th>
                                            <th>IMPORTE APARTADO</th>
                                            <th>COLABORADOR</th>
                                            <th>DÍAS</th>
                                            <th>STATUS</th>
                                            <th>OBSERVACIONES</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $row) { 
                                            $rowClass = '';
                                            if ($row['status'] == 'ACTIVO') {
                                                if ($row['dias_transcurridos'] >= 7 && $row['dias_transcurridos'] < 9) {
                                                    $rowClass = 'apartado-warning';
                                                } elseif ($row['dias_transcurridos'] >= 9) {
                                                    $rowClass = 'apartado-danger';
                                                }
                                            }
                                        ?>
                                            <tr class="<?php echo $rowClass; ?>">
                                                <td class="text-center"><?php echo $row['id_apartado']; ?></td>
                                                <td class="text-center"><?php echo date('d/m/Y', strtotime($row['fecha_apartado'])); ?></td>
                                                <td class="multi-line"><?php echo htmlspecialchars($row['cliente']); ?></td>
                                                <td class="text-center"><?php echo $row['tel_cel']; ?></td>
                                                <td><?php echo htmlspecialchars($row['proyecto']); ?></td>
                                                <td><?php echo htmlspecialchars($row['manzana']); ?></td>
                                                <td class="text-center"><?php echo $row['lote']; ?></td>
                                                <td class="text-right"><?php echo number_format($row['superficie'], 2); ?> m²</td>
                                                <td class="text-right">$<?php echo number_format($row['valortotal'], 2); ?></td>
                                                <td class="text-right">$<?php echo number_format($row['importe_apartado'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($row['colaborador']); ?></td>
                                                <td class="text-center">
                                                    <span class="badge <?php 
                                                        if ($row['dias_transcurridos'] >= 9) echo 'badge-danger';
                                                        elseif ($row['dias_transcurridos'] >= 7) echo 'badge-warning';
                                                        else echo 'badge-success';
                                                    ?>">
                                                        <?php echo $row['dias_transcurridos']; ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge <?php 
                                                        switch($row['status']) {
                                                            case 'ACTIVO': echo 'badge-success'; break;
                                                            case 'CANCELADO': echo 'badge-danger'; break;
                                                            case 'CONVERTIDO': echo 'badge-primary'; break;
                                                            default: echo 'badge-secondary';
                                                        }
                                                    ?>">
                                                        <?php echo $row['status']; ?>
                                                    </span>
                                                </td>
                                                <td class="multi-line"><?php echo htmlspecialchars($row['observaciones']); ?></td>
                                                <td class="text-center">
                                                    <!--
                                                    <?php if ($row['status'] == 'ACTIVO') { ?>
                                                        
                                                        <button class="btn btn-sm btn-warning btnEditar" data-id="<?php echo $row['id_apartado']; ?>" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger btnCancelar" data-id="<?php echo $row['id_apartado']; ?>" title="Cancelar">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-success btnConvertir" data-id="<?php echo $row['id_apartado']; ?>" title="Convertir a Venta">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    <?php } else { ?>
                                                        <button class="btn btn-sm btn-info btnVer" data-id="<?php echo $row['id_apartado']; ?>" title="Ver Detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    <?php } ?>
                                                    -->
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

<!-- Modal para editar apartado -->
<div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="modalCRUDLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCRUDLabel">EDITAR APARTADO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card card-widget" style="margin: 10px;">
                    <form id="formDatos" class="row p-2 g-2" action="" method="POST">
                        <input type="hidden" id="id_apartado" name="id_apartado">
                        
                        <div class="col-12 col-sm-6">
                            <div class="form-group input-group-sm">
                                <label for="fecha_apartado" class="col-form-label form-control-sm">*FECHA APARTADO:</label>
                                <input type="date" class="form-control form-control-sm" name="fecha_apartado" id="fecha_apartado" required>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="form-group input-group-sm">
                                <label for="importe_apartado" class="col-form-label form-control-sm">*IMPORTE APARTADO:</label>
                                <input type="text" class="form-control form-control-sm" name="importe_apartado" id="importe_apartado" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group input-group-sm">
                                <label for="col_asignado" class="col-form-label form-control-sm">COLABORADOR ASIGNADO:</label>
                                <select class="form-control form-control-sm selectpicker" name="col_asignado" id="col_asignado" data-live-search="true" required>
                                    <?php foreach ($colaboradores as $colab): ?>
                                        <option value="<?php echo $colab['id_col'] ?>"><?php echo $colab['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group input-group-sm">
                                <label for="observaciones" class="col-form-label form-control-sm">OBSERVACIONES:</label>
                                <textarea class="form-control form-control-sm" name="observaciones" id="observaciones" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardar">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntaapartado.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
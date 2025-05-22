<?php
$pagina = "cntaseguimiento";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

if (isset($_GET['id_pros'])) {
    $id_pros = $_GET['id_pros'];

    $consulta = "SELECT id_pros, nombre, correo, telefono FROM prospecto WHERE id_pros=:id_pros";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id_pros', $id_pros, PDO::PARAM_INT);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $prospecto = $data[0]['nombre'];
    $correo = $data[0]['correo'];
    $telefono = $data[0]['telefono'];

    $consultaseg = "SELECT id_seg, fecha_seg, tipo_seg, observaciones, realizado,nom_col_seg FROM vseg_pros WHERE id_pros=:id_pros ORDER BY fecha_seg DESC";
    $resultadoseg = $conexion->prepare($consultaseg);
    $resultadoseg->bindParam(':id_pros', $id_pros, PDO::PARAM_INT);
    $resultadoseg->execute();
    $dataseg = $resultadoseg->fetchAll(PDO::FETCH_ASSOC);
} else {
    $id_pros = NULL;
    $prospecto = "";
    $correo = "";
    $telefono = "";
    $consulta = "SELECT id_pros, nombre, correo, telefono FROM prospecto ORDER BY id_pros";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
}

$message = "";
?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<style>
    .starchecked {
        color: rgba(255, 195, 0, 100)
    }

    .multi-line {
        white-space: normal;
        width: 250px;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">HISTORIAL DE SEGUIMIENTO</h1>
            </div>

            <div class="card-body">
                
                

                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-sm-8">
                            <div class="card">
                                <div class="card-header bg-green">
                                    SELECCIONAR PROSPECTO
                                </div>
                                <div class="card-body">
                                    <div class="row justify-content-center mb-3">
                                        <div class="col-sm-8">
                                            <div class="input-group input-group-sm">
                                                <label for="prospecto" class="col-form-label">PROSPECTO:</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="hidden" class="form-control" name="id_pros" id="id_pros" value="<?php echo $id_pros; ?>">
                                                    <input type="text" class="form-control" name="prospecto" id="prospecto" disabled placeholder="SELECCIONAR PROSPECTO" value="<?php echo $prospecto; ?>">
                                                    <?php if ($id_pros == null) { ?>
                                                        <span class="input-group-append">
                                                            <button id="bprospecto" type="button" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                                                        </span>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($id_pros != null) { ?>
                                    <div class="row justify-content-center mb-3">
                                        <div class="col-sm-4">
                                            <div class="form-group input-group-sm">
                                                <label for="correo" class="col-form-label">Correo:</label>
                                                <input type="text" class="form-control" name="correo" id="correo" disabled value="<?php echo $correo; ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group input-group-sm">
                                                <label for="telefono" class="col-form-label">Teléfono:</label>
                                                <input type="text" class="form-control" name="telefono" id="telefono" disabled value="<?php echo $telefono; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($id_pros != null) { ?>
                        <div class="row justify-content-center">
                            <div class="col-sm-10">
                                <div class="table-responsive">
                                    <table name="tablaV" id="tablaV" class="table table-sm table-striped table-bordered table-condensed text-nowrap w-100" style="font-size:14px">
                                        <thead class="text-center bg-green">
                                            <tr>
                                                <th>ID</th>
                                                <th>FECHA</th>
                                                <th>TIPO</th>
                                                <th>OBSERVACIONES</th>
                                                <th>REALIZADO POR</th>
                                                <th>TAREA REALIZADA</th>
                                                <th>ACCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($dataseg as $dat) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $dat['id_seg'] ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($dat['fecha_seg'])) ?></td>
                                                    <td><?php echo $dat['tipo_seg'] ?></td>
                                                    <td class="multi-line"><?php echo $dat['observaciones'] ?></td>
                                                    <td><?php echo $dat['nom_col_seg'] ?></td>
                                                    <td><?php echo $dat['realizado'] ?></td>
                                                    <td></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>

    <!-- INICIA PROSPECTO -->
    <section>
        <div class="container-fluid">
            <!-- Default box -->
            <div class="modal fade" id="modalProspecto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-md" role="document">
                    <div class="modal-content w-auto">
                        <div class="modal-header bg-green">
                            <h5 class="modal-title" id="exampleModalLabel">BUSCAR PROSPECTO</h5>
                        </div>
                        <br>
                        <div class="table-hover table-responsive w-auto" style="padding:15px">
                            <table name="tablaProspecto" id="tablaProspecto" class="table table-sm table-striped table-bordered table-condensed" style="width:100%">
                                <thead class="text-center bg-green">
                                    <tr>
                                        <th>ID</th>
                                        <th>NOMBRE</th>
                                        <th>CORREO</th>
                                        <th>TELÉFONO</th>
                                        <th>ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($data as $datc) {
                                    ?>
                                        <tr>
                                            <td><?php echo $datc['id_pros'] ?></td>
                                            <td><?php echo $datc['nombre'] ?></td>
                                            <td><?php echo $datc['correo'] ?></td>
                                            <td><?php echo $datc['telefono'] ?></td>
                                            <td></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- TERMINA PROSPECTO -->

    <!-- INICIA MODAL SEGUIMIENTO -->
    <section>
        <div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-green">
                        <h5 class="modal-title" id="exampleModalLabel">NUEVO SEGUIMIENTO</h5>
                    </div>
                    <div class="card card-widget" style="margin: 10px;">
                        <form id="formDatos" action="" method="POST">
                            <div class="modal-body row">
                                <input type="hidden" id="id_seg" name="id_seg">
                                
                                <div class="col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="fecha_seg" class="col-form-label">Fecha:</label>
                                        <input type="date" class="form-control" name="fecha_seg" id="fecha_seg" required>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="tipo_seg" class="col-form-label">Tipo:</label>
                                        <select class="form-control" name="tipo_seg" id="tipo_seg" required>
                                            <option value="LLAMADA">LLAMADA</option>
                                            <option value="CORREO">CORREO</option>
                                            <option value="VISITA">VISITA</option>
                                            <option value="OTRO">OTRO</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12">
                                    <div class="form-group input-group-sm">
                                        <label for="observaciones" class="col-form-label">Observaciones:</label>
                                        <textarea class="form-control" name="observaciones" id="observaciones" rows="3" required></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12">
                                    <div class="form-group input-group-sm">
                                        <label for="realizado" class="col-form-label">Realizado por:</label>
                                        <input type="text" class="form-control" name="realizado" id="realizado" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
                                <button type="button" id="btnGuardar" name="btnGuardar" class="btn btn-success" value="btnGuardar"><i class="far fa-save"></i> Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- TERMINA MODAL SEGUIMIENTO -->
</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntahistorial.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script src="http://cdn.datatables.net/plug-ins/1.10.21/sorting/formatted-numbers.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
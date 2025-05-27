<?php
$pagina = "seguimiento";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

if (isset($_GET['id_seg'])) {
    $id_seg = $_GET['id_seg'];
    $sql = "SELECT * FROM vseg_pros Where id_seg = :id_seg";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_seg', $id_seg, PDO::PARAM_INT);
    $stmt->execute();
    $seguimiento = $stmt->fetch(PDO::FETCH_ASSOC);



    $tipo_seg = $seguimiento['tipo_seg'];
    $fecha_seg = $seguimiento['fecha_seg'];
    $realizado = $seguimiento['realizado'];
    $id_col = $seguimiento['id_col'];


    $id_pros = $seguimiento['id_pros'];
    $nombre_pros = $seguimiento['nombre'];
    $observaciones = $seguimiento['observaciones'];
    $res_seg = $seguimiento['resultado'];
    $obs_cierre = $seguimiento['obs_cierre'] ?? ''; // Manejo de observaciones de cierre, si existe



    $col_asignado = $seguimiento['id_col'];

    $stmt->closeCursor(); // Limpieza opcional si vas a seguir consultando con la misma conexión



} else {
    $id_seg = null;
    $tipo_seg = null;
    $fecha_seg = date("Y-m-d");
    $realizado = null;
    $res_seg = null;
    $obs_cierre = null; // Manejo de observaciones de cierre, si no existe

    $observaciones = null;




    if (!isset($_GET['id_pros'])) {
        echo "ID del prospecto no especificado.";
    }
    $id_pros = $_GET['id_pros'];
    $s_rol = $_SESSION['s_rol'];
    $id_col_sesion = $_SESSION['id_col'];

    // Consulta para obtener nombre del prospecto y colaborador asignado
    $sql = "SELECT nombre, col_asignado FROM vprospecto WHERE id_pros = :id_pros";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_pros', $id_pros, PDO::PARAM_INT);
    $stmt->execute();
    $prospecto = $stmt->fetch(PDO::FETCH_ASSOC);

    $nombre_pros = $prospecto['nombre'] ?? 'NO DEFINIDO';
    $col_asignado = $prospecto['col_asignado'] ?? null;
    $stmt->closeCursor(); // Limpieza opcional si vas a seguir consultando con la misma conexión

    if ($s_rol == 4) {
        $id_col = $id_col_sesion;
    } else {
        $id_col = $col_asignado;
    }
}






// Consultar lista de colaboradores
$sqlColabs = "SELECT id_col, nombre FROM colaborador WHERE edo_col = 1";
$stmtColabs = $conexion->prepare($sqlColabs);
$stmtColabs->execute();
$colaboradores = $stmtColabs->fetchAll(PDO::FETCH_ASSOC);
$stmtColabs->closeCursor(); // Limpieza opcional si vas a seguir consultando con la misma conexión



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

    .bootstrap-select .dropdown-menu li.selected a {
        background-color: #d4edda !important;
        /* verde claro */
        color: #212529 !important;
        /* color de texto Bootstrap por defecto */
    }

    .bootstrap-select .dropdown-menu li.selected a .text-success {
        color: #28a745 !important;
        /* verde Bootstrap */
    }

    .bootstrap-select .dropdown-menu li.selected a .text-primary {
        color: #007bff !important;
        /* azul Bootstrap */
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->


    <!-- Main content -->
    <section class="content">

        <div class="container-fluid">
            <div class="card card-widget">
                <div class="card-header bg-green">
                    <h4 class="card-title text-white">SEGUIMIENTO A PROSPECTO</h4>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-10 text-center">
                            <form id="formSeguimiento" class="row p-3" method="post" action="guardar_seguimiento.php">
                                <input type="hidden" id="id_pros" name="id_pros" value="<?= $id_pros ?>">
                                <input type="hidden" id="id_colaborador" name="id_colaborador" value="<?= $id_col ?>">
                                <input type="hidden" id="id_seg" name="id_seg" value="<?= $id_seg ?>">

                                <div class="col-md-2">
                                    <div class="form-group input-group-sm">
                                        <label for="tipo_seg" class="col-form-label">*TIPO DE SEGUIMIENTO:</label>
                                        <select name="tipo_seg" id="tipo_seg" class="form-control selectpicker form-control-sm" data-live-search="false" title="SELECCIONA TIPO" required>
                                            <option value="Llamada" data-content='<i class="fas fa-phone-alt text-success"></i> Llamada' <?= ($tipo_seg == "Llamada") ? "selected" : "" ?>>Llamada</option>
                                            <option value="Mensaje" data-content='<i class="fas fa-comment-dots text-info"></i> Mensaje' <?= ($tipo_seg == "Mensaje") ? "selected" : "" ?>>Mensaje</option>
                                            <option value="Correo" data-content='<i class="fas fa-envelope text-warning"></i> Correo' <?= ($tipo_seg == "Correo") ? "selected" : "" ?>>Correo</option>
                                            <option value="Reunión" data-content='<i class="fas fa-handshake text-primary"></i> Reunión' <?= ($tipo_seg == "Reunión") ? "selected" : "" ?>>Reunión</option>
                                            <option value="Otro" data-content='<i class="fas fa-ellipsis-h text-secondary"></i> Otro' <?= ($tipo_seg == "Otro") ? "selected" : "" ?>>Otro</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group input-group-sm">
                                        <label for="fecha_seg" class="col-form-label">*FECHA DE ACCIÓN:</label>
                                        <input type="date" name="fecha_seg" id="fecha_seg" class="form-control" required value="<?= htmlspecialchars($fecha_seg) ?>">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group input-group-sm">
                                        <label for="realizado" class="col-form-label">*¿YA SE REALIZÓ?</label>
                                        <select name="realizado" id="realizado" class="form-control selectpicker form-control-sm" data-live-search="false" title="SELECCIONA ESTADO" required>
                                            <option value="1" data-content='<i class="fas fa-check-circle text-success"></i> <span class="text-success">Sí (acción realizada)</span>' <?= ($realizado == 1) ? "selected" : "" ?>>Sí (acción realizada)</option>
                                            <option value="0" data-content='<i class="fas fa-calendar-alt text-primary"></i> <span class="text-primary" >No (agendar acción)</span>' <?= ($realizado == 0) ? "selected" : "" ?>>No (agendar acción)</option>
                                        </select>
                                    </div>
                                </div>




                                <div class="col-md-4">
                                    <div class="form-group input-group-sm">
                                        <label for="id_col" class="col-form-label">*RESPONSABLE DE ACCIÓN:</label>
                                        <?php $esEdicion = ($id_seg != null); ?>

                                        <select name="id_col_disabled" id="id_col" class="form-control selectpicker form-control-sm"
                                            data-live-search="false"
                                            title="SELECCIONA COLABORADOR"
                                            <?= $esEdicion ? 'disabled' : 'required' ?>>

                                            <?php foreach ($colaboradores as $col): ?>
                                                <?php
                                                $selected = ($col['id_col'] == $id_col) ? 'selected' : '';
                                                $nombrePlano = htmlspecialchars($col['nombre']);
                                                ?>
                                                <option value="<?= $col['id_col'] ?>" <?= $selected ?>>
                                                    <?= $nombrePlano ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <?php if ($esEdicion): ?>
                                            <!-- Campo oculto para enviar el valor si está deshabilitado -->
                                            <input type="hidden" name="id_col" id="id_col" value="<?= $id_col ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div id="campo_resultado" class=" form-group input-group-sm" style="display: none;">
                                        <label for="res_cierre" class="col-form-label">RESULTADO:</label>
                                        <select name="res_cierre" id="res_cierre"class="form-control form-control-sm">
                                            <option value="exito <?= ($res_seg == "exito") ? "selected" : "" ?>">Éxito</option>
                                            <option value="sin_respuesta" <?= ($res_seg == "sin_respuesta") ? "selected" : "" ?>>Sin respuesta</option>
                                            <option value="rechazado" <?= ($res_seg == "rechazado") ? "selected" : "" ?>>Rechazado</option>
                                            <option value="cancelado" <?= ($res_seg == "cancelado") ? "selected" : "" ?>>Cancelado</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="form-group input-group-sm">
                                        <label for="nombre_pros" class="col-form-label">PROSPECTO:</label>
                                        <input type="text" name="nombre_pros" id="nombre_pros" class="form-control" value="<?= htmlspecialchars($nombre_pros) ?>" readonly>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group input-group-sm">
                                        <label for="observaciones" class="col-form-label">OBSERVACIONES:</label>
                                        <textarea name="observaciones" id="observaciones" class="form-control" rows="4" placeholder="DETALLES ADICIONALES..."><?= $observaciones ?></textarea>
                                    </div>
                                </div>

                                 <div class="col-md-12">
                                    <div id="campoObseraciones" class="form-group input-group-sm" style="display: none;">
                                        <label for="obs_cierre" class="col-form-label">OBSERVACIONES CIERRE:</label>
                                        <textarea name="obs_cierre" id="obs_cierre" class="form-control" rows="4" placeholder="DETALLES DE CIERRE..."><?= $obs_cierre ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-12 text-right">
                                    <a href="home.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Regresar</a>
                                    <button type="submit" class="btn bg-green text-white"><i class="far fa-save"></i> Guardar Seguimiento</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>





    <!-- /.content -->
</div>






<?php include_once 'templates/footer.php'; ?>
<script src="fjs/seguimiento.js?v=<?php echo (rand()); ?>"></script>
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
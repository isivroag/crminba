<?php
ob_start();
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
    if (!$seguimiento) {
          header("Location: inicio.php");
    exit;
         
    }



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
        $id_pros = null;
        $nombre_pros = null;
        $fecha_seg = date("Y-m-d");
        $realizado = null;
        $id_col = $_SESSION['id_col'];

        $tipo_seg = null;
        $res_seg = null;
        $obs_cierre = null; // Manejo de observaciones de cierre, si no existe
        if ($id_col != null && $id_col != 0)    {
            
            $cnta="SELECT * from vprospecto where edo_pros = 1 and col_asignado=:id_col ORDER BY id_pros DESC ";
        } else {
            $id_col = $_SESSION['id_col']; // Asignar el id del colaborador actual si no se proporciona otro
            $cnta="SELECT * from vprospecto where edo_pros = 1 ORDER BY id_pros DESC ";
        }
        
        $stmtPros = $conexion->prepare($cnta);
        $stmtPros->bindParam(':id_col', $id_col, PDO::PARAM_INT);
        $stmtPros->execute();
        $data = $stmtPros->fetchAll(PDO::FETCH_ASSOC);
       // Limpieza opcional si vas a seguir consultando con la misma conexión


       

    }else{
        $id_pros = $_GET['id_pros'];
        $sqlPros = "SELECT id_pros, nombre,col_asignado FROM vprospecto WHERE id_pros = :id_pros";
        $stmtPros = $conexion->prepare($sqlPros);
        $stmtPros->bindParam(':id_pros', $id_pros, PDO::PARAM_INT);
        $stmtPros->execute();
        $prospecto = $stmtPros->fetch(PDO::FETCH_ASSOC);

        if ($prospecto) {
            $nombre_pros = $prospecto['nombre'];
            $id_col = $prospecto['col_asignado'];
            $stmtPros->closeCursor(); // Limpieza opcional si vas a seguir consultando con la misma conexión
        } else {
            echo "Prospecto no encontrado.";
            exit;
        }
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
                              
                                <input type="hidden" id="id_colaborador" name="id_colaborador" value="<?= $id_col ?>">
                                <input type="hidden" id="id_seg" name="id_seg" value="<?= $id_seg ?>">


                                <div class="col-md-12">
                                     <div class="input-group input-group-sm">
                                                <label for="prospecto" class="col-form-label">PROSPECTO:</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="hidden" class="form-control" name="id_pros" id="id_pros" value="<?php echo $id_pros; ?>">
                                                    <input type="text" class="form-control" name="nombre_prospecto" id="nombre_prospecto" disabled placeholder="SELECCIONAR PROSPECTO" value="<?php echo $nombre_pros; ?>">
                                                    <?php if ($id_pros == null) { ?>
                                                        <span class="input-group-append">
                                                            <button id="bprospecto" type="button" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                                                        </span>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group input-group-sm">
                                        <label for="tipo_seg" class="col-form-label">*TIPO DE SEG:</label>
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
                                        <label for="fecha_seg" class="col-form-label">*FECHA:</label>
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
                                        <label for="id_col" class="col-form-label">*RESPONSABLE:</label>
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

    <!-- INICIA PROSPECTO -->
    <section>
        <div class="container-fluid">
            <!-- Default box -->
            <div class="modal fade" id="modalProspectos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-xl" role="document">
                    <div class="modal-content w-auto">
                        <div class="modal-header bg-green">
                            <h5 class="modal-title" id="exampleModalLabel">BUSCAR PROSPECTO</h5>
                        </div>
                        <br>
                        <div class="table-hover table-responsive w-auto" style="padding:15px">
                            <table name="tablaProspecto" id="tablaProspecto" class="table table-sm table-striped table-bordered table-condensed" style="width:100%">
                                <thead class="text-center bg-green">
                                    <tr>
                                        <th style="min-width: 60px; max-width: 80px; width: 8%;">ID</th>
                                        <th style="min-width: 200px; max-width: 350px; width: 40%;">NOMBRE</th>
                                        <th style="min-width: 140px; max-width: 220px; width: 22%;">COL_ASIGANDO</th>
                                    
                                        <th style="min-width: 90px; max-width: 120px; width: 12%;">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($data as $datc) {
                                    ?>
                                        <tr>
                                            <td><?php echo $datc['id_pros'] ?></td>
                                            <td><?php echo $datc['nombre'] ?></td>
                                            <td><?php echo $datc['col_asignado'] ?></td>
                                            
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
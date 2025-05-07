<?php
$pagina = "prospecto";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Consulta para obtener prospectos activos (edo_pros = 1)
$consulta = "SELECT p.*, c.nombre as nombre_colaborador 
             FROM prospecto p
             JOIN colaborador c ON p.col_asignado = c.id_col
             WHERE p.edo_pros = 1 
             ORDER BY p.id_pros";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener colaboradores activos (para el select)
$consulta_colab = "SELECT * FROM colaborador WHERE edo_col = 1 ORDER BY id_col";
$resultado_colab = $conexion->prepare($consulta_colab);
$resultado_colab->execute();
$colaboradores = $resultado_colab->fetchAll(PDO::FETCH_ASSOC);

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
    .badge-asignado {
        background-color: #28a745;
    }
    .badge-convertido {
        background-color: #17a2b8;
    }
    .badge-descartado {
        background-color: #6c757d;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="card">
            <div class="card-header  bg-green text-light">
                <h1 class="card-title mx-auto">PROSPECTOS</h1>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <button id="btnNuevo" type="button" class="btn  bg-green btn-ms" data-toggle="modal">
                            <i class="fas fa-plus-square text-light"></i><span class="text-light"> Nuevo</span>
                        </button>
                    </div>
                </div>
                <br>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table name="tablaV" id="tablaV" class="table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:14px">
                                    <thead class="text-center  bg-green">
                                        <tr>
                                            <th>ID</th>
                                            <th>NOMBRE</th>
                                            <th>TELÉFONO</th>
                                            <th>CORREO</th>
                                            <th>ASIGNADO A</th>
                                            <th>FECHA REGISTRO</th>
                                            <th>ESTADO</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $dat): ?>
                                            <tr>
                                                <td><?php echo $dat['id_pros'] ?></td>
                                                <td><?php echo $dat['nombre'] ?></td>
                                                <td><?php echo $dat['telefono'] ?></td>
                                                <td><?php echo $dat['correo'] ?></td>
                                                <td><?php echo $dat['nombre_colaborador'] ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($dat['fecha_registro'])) ?></td>
                                                <td class="text-center">
                                                    <?php 
                                                    $badge_class = '';
                                                    $estado_text = '';
                                                    switch($dat['edo_pros']) {
                                                        case 1: 
                                                            $badge_class = 'badge-asignado';
                                                            $estado_text = 'Asignado';
                                                            break;
                                                        case 2: 
                                                            $badge_class = 'badge-convertido';
                                                            $estado_text = 'Convertido';
                                                            break;
                                                        case 3: 
                                                            $badge_class = 'badge-descartado';
                                                            $estado_text = 'Descartado';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $badge_class ?>"><?php echo $estado_text ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-primary btnEditar" data-toggle="tooltip" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-secondary btnEnviar" data-toggle="tooltip" title="Enviar">
                                                            <i class="fas fa-envelope"></i>
                                                        </button>
                                                        <?php if($dat['edo_pros'] == 1): ?>
                                                            <button class="btn btn-sm btn-success btnConvertir" data-toggle="tooltip" title="Convertir a Cliente">
                                                                <i class="fas fa-user-check"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button class="btn btn-sm btn-danger btnDescartar" data-toggle="tooltip" title="Descartar Prospecto">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para Prospectos -->
    <section>
        <div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="modalCRUDLabel" aria-modal="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header  bg-green">
                        <h5 class="modal-title" id="modalCRUDLabel">NUEVO PROSPECTO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card card-widget" style="margin: 10px;">
                            <form id="formDatos" class="row p-2" action="" method="POST">
                                <input type="hidden" id="id_pros" name="id_pros">
                                
                                <div class="col-sm-12">
                                    <div class="form-group input-group-sm">
                                        <label for="nombre" class="col-form-label">*NOMBRE:</label>
                                        <input type="text" class="form-control" name="nombre" id="nombre" autocomplete="off" placeholder="NOMBRE COMPLETO" required>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="telefono" class="col-form-label">*TELÉFONO:</label>
                                        <input type="text" class="form-control" name="telefono" id="telefono" autocomplete="off" placeholder="TELÉFONO" required>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="correo" class="col-form-label">*CORREO:</label>
                                        <input type="email" class="form-control" name="correo" id="correo" autocomplete="off" placeholder="CORREO ELECTRÓNICO" required>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12">
                                    <div class="form-group input-group-sm">
                                        <label for="col_asignado" class="col-form-label">ASIGNADO A:</label>
                                        <select class="form-control selectpicker" name="col_asignado" id="col_asignado" data-live-search="true" title="SELECCIONA COLABORADOR" required>
                                            <?php foreach ($colaboradores as $colab): ?>
                                                <option value="<?php echo $colab['id_col'] ?>"><?php echo $colab['nombre'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
                        <button type="button" id="btnGuardar" name="btnGuardar" class="btn  bg-green" value="btnGuardar"><i class="far fa-save"></i> Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para Convertir a Cliente -->
    <div class="modal fade" id="modalConvertir" tabindex="-1" role="dialog" aria-labelledby="modalConvertirLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-green text-white">
                    <h5 class="modal-title" id="modalConvertirLabel">CONVERTIR A CLIENTE</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formCliente" class="row p-3">
                        <input type="hidden" id="id_prospecto" name="id_prospecto">
                        
                        <div class="col-md-6 form-group">
                            <label for="rfc">RFC:</label>
                            <input type="text" class="form-control" id="rfc" name="rfc" placeholder="RFC">
                        </div>
                        
                        <div class="col-md-6 form-group">
                            <label for="fecha_nacimiento">FECHA DE NACIMIENTO:</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                        </div>
                        
                        <div class="col-12 form-group">
                            <label for="direccion">DIRECCIÓN COMPLETA:</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="3" placeholder="CALLE, NÚMERO, COLONIA, CÓDIGO POSTAL, CIUDAD, ESTADO"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnConvertir" class="btn btn-success">Guardar como Cliente</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntaprospecto.js?v=<?php echo (rand()); ?>"></script>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-es_ES.min.js"></script>
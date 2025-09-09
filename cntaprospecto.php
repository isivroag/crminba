<?php
$pagina = "prospecto";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Consulta para obtener prospectos activos (edo_pros = 1)

$filtro= $_GET['estado'] ?? 'activos';

if ($_SESSION['s_rol'] == 4) {
    $colaborador_id = $_SESSION['id_col'];

    if ($filtro == 'todos') {
        $consulta = "SELECT p.*, c.nombre as nombre_colaborador 
                     FROM prospecto p
                     JOIN colaborador c ON p.col_asignado = c.id_col
                     WHERE p.edo_pros IN (1,2,3)
                     and p.edo_seguimiento <> 3
                     AND p.col_asignado = :colaborador_id
                     ORDER BY p.id_pros";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':colaborador_id', $colaborador_id, PDO::PARAM_INT);
    } else {    
    $consulta = "SELECT p.*, c.nombre as nombre_colaborador 
                 FROM prospecto p
                 JOIN colaborador c ON p.col_asignado = c.id_col
                 WHERE p.edo_pros IN (1)
                 and p.edo_seguimiento <> 3
                 AND p.col_asignado = :colaborador_id
                 ORDER BY p.id_pros";
    }

    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':colaborador_id', $colaborador_id, PDO::PARAM_INT);
    // Si no es administrador, filtrar por el colaborador asignado

} else {
    if ($filtro == 'todos') {
        $consulta = "SELECT p.*, c.nombre as nombre_colaborador 
                     FROM prospecto p
                     JOIN colaborador c ON p.col_asignado = c.id_col
                     WHERE p.edo_pros IN (1,2,3)
                     and p.edo_seguimiento <> 3
                     ORDER BY p.id_pros";
    } else {
    $consulta = "SELECT p.*, c.nombre as nombre_colaborador 
                 FROM prospecto p
                 JOIN colaborador c ON p.col_asignado = c.id_col
                 WHERE p.edo_pros IN (1)
                 ORDER BY p.id_pros";
    }

    $resultado = $conexion->prepare($consulta);
}
// Administrador puede ver todos los prospectos activos
/*
$consulta = "SELECT p.*, c.nombre as nombre_colaborador 
             FROM prospecto p
             JOIN colaborador c ON p.col_asignado = c.id_col
             WHERE p.edo_pros = 1 
             ORDER BY p.id_pros";
$resultado = $conexion->prepare($consulta);
*/
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

    .badge-seguimiento {
        background-color: #17a2b8;
    }

    .badge-finalizado {
        background-color: #6c757d;
    }

    .badge-pendiente {
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
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="chkInactivos" <?php echo ($filtro == 'todos') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="chkInactivos">
                                        Mostrar prospectos inactivos
                                    </label>
                                </div>
                                <table name="tablaV" id="tablaV" class="table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:14px">
                                    <thead class="text-center  bg-green">
                                        <tr>
                                            <th>ID </th>
                                            <th>NOMBRE</th>
                                            <th>TELÉFONO</th>
                                            <th>CORREO</th>
                                            <th>ASIGNADO A</th>
                                            <th>FECHA REGISTRO</th>
                                            <th>ESTADO</th>
                                            <th>ORIGEN</th>
                                            <th>INTERES</th>
                                            <th>Estado</th>
                                            <th>ESTADO PROSPECTO</th>
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
                                                    switch ($dat['edo_seguimiento']) {
                                                        case 1:
                                                            $badge_class = 'bg-success';
                                                            $estado_text = 'Nuevo';
                                                            break;
                                                        case 2:
                                                            $badge_class = 'bg-primary';
                                                            $estado_text = 'Seguimiento';
                                                            break;
                                                        case 3:
                                                            $badge_class = 'bg-green';
                                                            $estado_text = 'Finalizado';
                                                            break;
                                                        case 4:
                                                            $badge_class = 'bg-secondary';
                                                            $estado_text = 'Pendiente';
                                                            break;
                                                        case 5:
                                                            $badge_class = 'bg-dark';
                                                            $estado_text = 'Inactivo';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $badge_class ?>"><?php echo $estado_text ?></span>
                                                </td>
                                                <td data-origen="<?php echo strtolower($dat['origen']); ?>">
                                                    <?php
                                                    $origen = strtolower($dat['origen']);
                                                    switch ($origen) {
                                                        case 'facebook':
                                                            echo '<i class="fab fa-facebook text-primary"></i> Facebook';
                                                            break;
                                                        case 'instagram':
                                                            echo '<i class="fab fa-instagram text-danger"></i> Instagram';
                                                            break;
                                                        case 'web':
                                                            echo '<i class="fas fa-globe text-info"></i> Web';
                                                            break;
                                                        case 'whatsapp':
                                                            echo '<i class="fab fa-whatsapp text-success"></i> WhatsApp';
                                                            break;
                                                        case 'llamada':
                                                            echo '<i class="fas fa-phone text-dark"></i> Llamada';
                                                            break;
                                                        case 'vendedor':
                                                            echo '<i class="fas fa-user text-green"></i> Vendedor';
                                                            break;
                                                        default:
                                                            echo ucfirst($origen);
                                                            break;
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo $dat['interes']; ?></td>
                                                <td><?php echo $dat['edo_pros']; ?></td>
                                                <td class="text-center">
                                                    <?php if ($dat['edo_pros'] == 1): ?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td></td>
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
                            <form id="formDatos" class="row p-2 g-2" action="" method="POST">
                                <input type="hidden" id="id_pros" name="id_pros">

                                <div class="col-12">
                                    <div class="form-group input-group-sm">
                                        <label for="nombre" class="col-form-label form-control-sm">*NOMBRE:</label>
                                        <input type="text" class="form-control form-control-sm" name="nombre" id="nombre" autocomplete="off" placeholder="NOMBRE COMPLETO" required>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="telefono" class="col-form-label form-control-sm">*TELÉFONO:</label>
                                        <input type="tel" class="form-control form-control-sm" name="telefono" id="telefono" autocomplete="off" placeholder="TELÉFONO (10 dígitos o +código)" maxlength="20">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="correo" class="col-form-label form-control-sm">*CORREO:</label>
                                        <input type="email" class="form-control form-control-sm" name="correo" id="correo" autocomplete="off" placeholder="CORREO ELECTRÓNICO">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="origen" class="col-form-label form-control-sm">*Origen:</label>
                                        <select id="origen" name="origen" class="selectpicker form-control form-control-sm" data-live-search="false" title="Seleccione el origen...">
                                            <option value="facebook" data-icon="fab fa-facebook text-primary">Facebook</option>
                                            <option value="instagram" data-icon="fab fa-instagram text-danger">Instagram</option>
                                            <option value="web" data-icon="fas fa-globe text-info">Web</option>
                                            <option value="whatsapp" data-icon="fab fa-whatsapp text-success">WhatsApp</option>
                                            <option value="llamada" data-icon="fas fa-phone text-dark">Llamada</option>
                                            <option value="vendedor" data-icon="fas fa-user text-green">Vendedor</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group input-group-sm">
                                        <label for="interes_select" class="col-form-label form-control-sm">*MOSTRÓ INTERÉS EN:</label>
                                        <select class="form-control form-control-sm" name="interes_select" id="interes_select" required>
                                            <option value="" disabled selected>Seleccione un proyecto...</option>
                                            <?php
                                            // Cargar proyectos desde la base de datos
                                            $consulta_proy = "SELECT nombre FROM proyecto ORDER BY nombre";
                                            $resultado_proy = $conexion->prepare($consulta_proy);
                                            $resultado_proy->execute();
                                            $proyectos = $resultado_proy->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($proyectos as $proy) {
                                                echo '<option value="' . htmlspecialchars($proy['nombre']) . '">' . htmlspecialchars($proy['nombre']) . '</option>';
                                            }
                                            ?>
                                            <option value="otro">Otro</option>
                                        </select>
                                        <input type="text" class="form-control form-control-sm mt-2" name="interes" id="interes" autocomplete="off" placeholder="Especifique el proyecto" style="display:none;">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group input-group-sm">
                                        <label for="col_asignado" class="col-form-label form-control-sm">ASIGNADO A:</label>
                                        <select class="form-control form-control-sm selectpicker" name="col_asignado" id="col_asignado" data-live-search="true" title="SELECCIONA COLABORADOR" required>
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
    <section>
        <!-- Modal para Convertir a Cliente -->
        <div class="modal fade" id="modalCliente" tabindex="-1" role="dialog" aria-labelledby="modalClienteLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header  bg-green text-white">
                        <h5 class="modal-title" id="modalClienteLabel">CONVERTIR A CLIENTE</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formCliente row" class="row p-3">
                            <input type="hidden" id="id_prospecto" name="id_prospecto">

                            <div class="col-12 col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="rfc" class="col-form-label form-control-sm">RFC:</label>
                                    <input type="text" class="form-control form-control-sm" name="rfc" id="rfc" autocomplete="off" placeholder="RFC" maxlength="13">
                                </div>
                            </div>

                            <div class="col-12 col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="tipo_ide" class="col-form-label form-control-sm">*TIPO IDENTIFICACIÓN:</label>
                                    <select class="form-control form-control-sm" name="tipo_ide" id="tipo_ide" require>
                                        <option value="INE">INE</option>
                                        <option value="PASAPORTE">PASAPORTE</option>
                                        <option value="CURP">CURP</option>
                                        <option value="LICENCIA">LICENCIA</option>
                                        <option value="CARTILLA MILITAR">CARTILLA MILITAR</option>
                                        <option value="TARJETA DE RESIDENCIA">TARJETA DE RESIDENCIA</option>
                                        <option value="OTRO">OTRO</option>
                                    </select>
                                </div>
                            </div>



                            <div class="col-12 col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="folio_ide" class="col-form-label form-control-sm">*FOLIO IDENTIFICACIÓN:</label>
                                    <input type="text" class="form-control form-control-sm" name="folio_ide" id="folio_ide" autocomplete="off" placeholder="FOLIO" required>
                                </div>
                            </div>



                            <div class="col-12 col-sm-12">
                                <div class="form-group input-group-sm">
                                    <label for="nombre_clie" class="col-form-label">*NOMBRE COMPLETO:</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre_clie" name="nombre_clie" placeholder="NOMBRE COMPLETO" required>
                                </div>
                            </div>


                            <div class="col-12 col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="tel_clie" class="col-form-label">*TELÉFONO:</label>
                                    <input type="text" class="form-control form-control-sm" id="tel_clie" name="tel_clie" placeholder="TELÉFONO" required>
                                </div>
                            </div>

                            <div class="col-12 col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="correo_clie" class="col-form-label">CORREO:</label>
                                    <input type="email" class="form-control form-control-sm" id="correo_clie" name="correo_clie" placeholder="CORREO">
                                </div>
                            </div>

                            <div class="col-12 col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="nacionalidad" class="col-form-label ">NACIONALIDAD:</label>
                                    <select class="form-control form-control-sm" name="nacionalidad" id="nacionalidad">
                                        <option value="MEXICANA">MEXICANA</option>
                                        <option value="EXTRANJERA">EXTRANJERA</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-8">
                                <div class="form-group input-group-sm">
                                    <label for="dir_calle" class="col-form-label form-control-sm">CALLE Y NÚMERO:</label>
                                    <input type="text" class="form-control form-control-sm" name="dir_calle" id="dir_calle" autocomplete="off" placeholder="CALLE Y NÚMERO">
                                </div>
                            </div>

                            <div class="col-12 col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="dir_colonia" class="col-form-label form-control-sm">COLONIA:</label>
                                    <input type="text" class="form-control form-control-sm" name="dir_colonia" id="dir_colonia" autocomplete="off" placeholder="COLONIA">
                                </div>
                            </div>

                            <div class="col-12 col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="dir_ciudad" class="col-form-label form-control-sm">CIUDAD:</label>
                                    <input type="text" class="form-control form-control-sm" name="dir_ciudad" id="dir_ciudad" autocomplete="off" placeholder="CIUDAD">
                                </div>
                            </div>

                            <div class="col-12 col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="dir_edo" class="col-form-label form-control-sm">ESTADO:</label>
                                    <input type="text" class="form-control form-control-sm" name="dir_edo" id="dir_edo" autocomplete="off" placeholder="ESTADO">
                                </div>
                            </div>

                            <div class="col-12 col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="dir_cp" class="col-form-label form-control-sm">CÓDIGO POSTAL:</label>
                                    <input type="text" class="form-control form-control-sm" name="dir_cp" id="dir_cp" autocomplete="off" placeholder="CP" maxlength="5">
                                </div>
                            </div>

                             <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="origenc" class="col-form-label form-control-sm">*ORIGEN:</label>
                                        <select id="origenc" name="origenc" class="selectpicker form-control form-control-sm" data-live-search="false" title="Seleccione el origen...">
                                            <option value="facebook" data-icon="fab fa-facebook text-primary">Facebook</option>
                                            <option value="instagram" data-icon="fab fa-instagram text-danger">Instagram</option>
                                            <option value="web" data-icon="fas fa-globe text-info">Web</option>
                                            <option value="whatsapp" data-icon="fab fa-whatsapp text-success">WhatsApp</option>
                                            <option value="llamada" data-icon="fas fa-phone text-dark">Llamada</option>
                                            <option value="vendedor" data-icon="fas fa-user text-green">Vendedor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <div class="form-group input-group-sm">
                                        <label for="col_asignadoc" class="col-form-label form-control-sm">*ASIGNADO A:</label>
                                        <select class="form-control form-control-sm selectpicker" name="col_asignadoc" id="col_asignadoc" data-live-search="true" title="SELECCIONA COLABORADOR" required>
                                            <?php foreach ($colaboradores as $colab): ?>
                                                <option value="<?php echo $colab['id_col'] ?>"><?php echo $colab['nombre'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                            <div class="col-12">
                                <div class="form-group input-group-sm">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="especial" id="especial" value="1">
                                        <label class="form-check-label" for="especial">
                                            <strong>CLIENTE ESPECIAL</strong> <small class="text-muted">(Requiere atención prioritaria)</small>
                                        </label>
                                    </div>
                                </div>
                            </div>



                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="btnGuardarCliente" class="btn btn-success">Guardar como Cliente</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
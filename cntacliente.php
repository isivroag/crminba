<?php
$pagina = "cntacliente";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$consulta = "SELECT * FROM cliente WHERE edo_clie=1 ORDER BY id_clie DESC";
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
        width: 550px;
        word-break: break-word;
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
                <h1 class="card-title mx-auto">CLIENTES</h1>
            </div>

            <div class="card-body">

                <div class="row">
                    <div class="col-lg-12">
                        <button id="btnNuevo" type="button" class="btn bg-green btn-ms" data-toggle="modal"><i class="fas fa-plus-square text-light"></i><span class="text-light"> Nuevo</span></button>
                    </div>
                </div>
                <br>
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table name="tablaV" id="tablaV" class="table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:14px">
                                    <thead class="text-center bg-green">
                                        <tr>
                                            <th>ID</th>
                                            <th>NOMBRE</th>
                                            <th>TELEFONO</th>
                                            <th>CORREO</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($data as $dat) {
                                        ?>
                                            <tr>
                                                <td><?php echo $dat['id_clie'] ?></td>
                                                <td class="multi-line"><?php echo $dat['nombre'] ?></td>
                                                <td><?php echo $dat['tel_cel'] ?></td>
                                                <td><?php echo $dat['email'] ?></td>
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
            <!-- /.card-body -->

            <!-- /.card-footer-->
        </div>
        <!-- /.card -->

    </section>

    <!-- PROVEEDOR -->
    <section>
        <div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="modalCRUDLabel" aria-modal="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-green">
                        <h5 class="modal-title" id="modalCRUDLabel">NUEVO CLIENTE</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card card-widget" style="margin: 10px;">
                            <form id="formDatos" class="row p-2 g-2" action="" method="POST">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="rfc" class="col-form-label form-control-sm">RFC:</label>
                                        <input type="text" class="form-control form-control-sm" name="rfc" id="rfc" autocomplete="off" placeholder="RFC" maxlength="16">
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
                                        <input type="text" class="form-control form-control-sm" id="tel_clie" name="tel_clie" placeholder="TELÉFONO" required maxlength="20">
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
                                        <label for="origen" class="col-form-label form-control-sm">*ORIGEN:</label>
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
                                <div class="col-12 col-sm-8">
                                    <div class="form-group input-group-sm">
                                        <label for="col_asignado" class="col-form-label form-control-sm">*ASIGNADO A:</label>
                                        <select class="form-control form-control-sm selectpicker" name="col_asignado" id="col_asignado" data-live-search="true" title="SELECCIONA COLABORADOR" required>
                                            <?php foreach ($colaboradores as $colab): ?>
                                                <option value="<?php echo $colab['id_col'] ?>"><?php echo $colab['nombre'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12">
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

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
                        <button type="button" id="btnGuardar" name="btnGuardar" class="btn bg-green" value="btnGuardar"><i class="far fa-save"></i> Guardar</button>
                    </div>
                </div>
            </div>
        </div>

    </section>





    <!-- /.content -->
</div>






<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntacliente.js?v=<?php echo (rand()); ?>"></script>
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
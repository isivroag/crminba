<?php
$pagina = "cntacliente";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$consulta = "SELECT * FROM cliente ORDER BY id_clie";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

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
                        <?php if ($_SESSION['s_rol'] == 2 || $_SESSION['s_rol'] == 3): ?>
                            <button id="btnNuevo" type="button" class="btn bg-green btn-ms" data-toggle="modal">
                                <i class="fas fa-plus-square text-light"></i>
                                <span class="text-light"> Nuevo</span>
                            </button>
                        <?php endif; ?>
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
                                            <th>DIRECCIÓN</th>
                                            <th>CIUDAD</th>
                                            <th>ESTADO</th>
                                            <th>CP</th>
                                            <th>FOLIO</th>
                                            <th>NACIONALIDAD</th>
                                            <th>TEL CELULAR</th>
                                            <th>TEL CASA</th>
                                            <th>EMAIL</th>
                                            <th>RFC</th>
                                            <th>BANCO</th>
                                            <th>CUENTA</th>
                                            <th>ESPECIAL</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $dat) { ?>
                                            <tr>
                                                <td><?php echo $dat['id_clie'] ?></td>
                                                <td class="multi-line"><?php echo $dat['nombre'] ?></td>
                                                <td class="multi-line"><?php echo $dat['dir_calle'] ?></td>
                                                <td><?php echo $dat['dir_ciudad'] ?></td>
                                                <td><?php echo $dat['dir_edo'] ?></td>
                                                <td><?php echo $dat['dir_cp'] ?></td>
                                                <td><?php echo $dat['folio'] ?></td>
                                                <td><?php echo $dat['nacionalidad'] ?></td>
                                                <td><?php echo $dat['tel_cel'] ?></td>
                                                <td><?php echo $dat['tel_casa'] ?></td>
                                                <td><?php echo $dat['email'] ?></td>
                                                <td><?php echo $dat['rfc'] ?></td>
                                                <td><?php echo $dat['banco'] ?></td>
                                                <td><?php echo $dat['cuenta'] ?></td>
                                                <td class="text-center">
                                                    <?php if ($dat['especial'] == 1): ?>
                                                        <span class="badge badge-warning">ESPECIAL</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">NORMAL</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($_SESSION['s_rol'] == 2 || $_SESSION['s_rol'] == 3): ?>
                                                        <div class="btn-group">
                                                            <button class="btn btn-warning btn-sm btnEditar" data-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-danger btn-sm btnBorrar" data-toggle="tooltip" title="Eliminar">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
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

    <!-- MODAL CLIENTE -->
    <section>
        <div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="modalCRUDLabel" aria-modal="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-green">
                        <h5 class="modal-title" id="modalCRUDLabel">NUEVO CLIENTE</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card card-widget" style="margin: 10px;">
                            <form id="formdatos" class="row p-2 g-2" action="" method="POST">
                                <!-- Información Personal -->
                                <div class="col-12">
                                    <h5 class="text-info"><i class="fas fa-user"></i> Información Personal</h5>
                                    <hr>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="nombre" class="col-form-label form-control-sm">*NOMBRE COMPLETO:</label>
                                        <input type="text" class="form-control form-control-sm" name="nombre" id="nombre" autocomplete="off" placeholder="NOMBRE COMPLETO" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2"></div>

                                <div class="col-12 col-sm-2">
                                    <div class="form-group input-group-sm">
                                        <label for="rfc" class="col-form-label form-control-sm">RFC:</label>
                                        <input type="text" class="form-control form-control-sm" name="rfc" id="rfc" autocomplete="off" placeholder="RFC" maxlength="13">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group input-group-sm">
                                        <label for="nacionalidad" class="col-form-label form-control-sm">NACIONALIDAD:</label>
                                        <select class="form-control form-control-sm" name="nacionalidad" id="nacionalidad">
                                            <option value="MEXICANA">MEXICANA</option>
                                            <option value="EXTRANJERA">EXTRANJERA</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-12 col-sm-3">
                                    <div class="form-group input-group-sm">
                                        <label for="tipo_ide" class="col-form-label form-control-sm">TIPO IDENTIFICACIÓN:</label>
                                        <select class="form-control form-control-sm" name="tipo_ide" id="tipo_ide">
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

                                

                                <div class="col-12 col-sm-2">
                                    <div class="form-group input-group-sm">
                                        <label for="folio" class="col-form-label form-control-sm">FOLIO IDENTIFICACIÓN:</label>
                                        <input type="text" class="form-control form-control-sm" name="folio" id="folio" autocomplete="off" placeholder="FOLIO">
                                    </div>
                                </div>





                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="email" class="col-form-label form-control-sm">*EMAIL:</label>
                                        <input type="email" class="form-control form-control-sm" name="email" id="email" autocomplete="off" placeholder="CORREO ELECTRÓNICO" required>
                                    </div>
                                </div>

                                <!-- Dirección -->
                                <div class="col-12">
                                    <h5 class="text-info"><i class="fas fa-map-marker-alt"></i> Dirección</h5>
                                    <hr>
                                </div>

                                <div class="col-12 col-sm-8">
                                    <div class="form-group input-group-sm">
                                        <label for="dir_calle" class="col-form-label form-control-sm">*CALLE Y NÚMERO:</label>
                                        <input type="text" class="form-control form-control-sm" name="dir_calle" id="dir_calle" autocomplete="off" placeholder="CALLE Y NÚMERO" required>
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

                                <!-- Teléfonos -->
                                <div class="col-12">
                                    <h5 class="text-info"><i class="fas fa-phone"></i> Teléfonos</h5>
                                    <hr>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="tel_cel" class="col-form-label form-control-sm">*TEL CELULAR:</label>
                                        <input type="text" class="form-control form-control-sm" name="tel_cel" id="tel_cel" autocomplete="off" placeholder="TELÉFONO CELULAR" required maxlength="10">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="tel_casa" class="col-form-label form-control-sm">TEL CASA:</label>
                                        <input type="text" class="form-control form-control-sm" name="tel_casa" id="tel_casa" autocomplete="off" placeholder="TELÉFONO CASA" maxlength="10">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="tel_trab" class="col-form-label form-control-sm">TEL TRABAJO:</label>
                                        <input type="text" class="form-control form-control-sm" name="tel_trab" id="tel_trab" autocomplete="off" placeholder="TELÉFONO TRABAJO" maxlength="10">
                                    </div>
                                </div>

                                <!-- Información Bancaria -->
                                <div class="col-12">
                                    <h5 class="text-info"><i class="fas fa-university"></i> Información Bancaria</h5>
                                    <hr>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="banco" class="col-form-label form-control-sm">BANCO:</label>
                                        <input type="text" class="form-control form-control-sm" name="banco" id="banco" autocomplete="off" placeholder="NOMBRE DEL BANCO">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="cuenta" class="col-form-label form-control-sm">NÚMERO DE CUENTA:</label>
                                        <input type="text" class="form-control form-control-sm" name="cuenta" id="cuenta" autocomplete="off" placeholder="NÚMERO DE CUENTA">
                                    </div>
                                </div>

                                <!-- Configuración Especial -->
                                <div class="col-12">
                                    <h5 class="text-info"><i class="fas fa-cogs"></i> Configuración</h5>
                                    <hr>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <i class="fas fa-ban"></i> Cancelar
                        </button>
                        <button type="button" id="btnGuardar" name="btnGuardar" class="btn bg-green" value="btnGuardar">
                            <i class="far fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntaclie.js?v=<?php echo (rand()); ?>"></script>
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
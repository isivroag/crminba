<?php
$pagina = "cntacliente";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$consulta = "SELECT * FROM cliente  ORDER BY clave";
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
                                                <td><?php echo $dat['clave'] ?></td>
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
                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="rfc" class="col-form-label form-control-sm">RFC:</label>
                                        <input type="text" class="form-control form-control-sm" name="rfc" id="rfc" autocomplete="off" placeholder="RFC">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="curp" class="col-form-label form-control-sm">CURP:</label>
                                        <input type="text" class="form-control form-control-sm" name="curp" id="curp" autocomplete="off" placeholder="CURP">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group input-group-sm">
                                        <label for="nombre" class="col-form-label form-control-sm">*NOMBRE:</label>
                                        <input type="text" class="form-control form-control-sm" name="nombre" id="nombre" autocomplete="off" placeholder="NOMBRE" required>
                                    </div>
                                </div>
                                <div class="col-8 col-sm-8">
                                    <div class="form-group input-group-sm">
                                        <label for="dir_calle" class="col-form-label form-control-sm">Calle:</label>
                                        <input type="text" class="form-control form-control-sm" name="dir_calle" id="dir_calle" autocomplete="off" placeholder="Calle">
                                    </div>
                                </div>
                                <div class="col-3 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="dir_ciudad" class="col-form-label form-control-sm">Ciudad:</label>
                                        <input type="text" class="form-control form-control-sm" name="dir_ciudad" id="dir_ciudad" autocomplete="off" placeholder="Ciudad">
                                    </div>
                                </div>
                                <div class="col-3 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="dir_colonia" class="col-form-label form-control-sm">Colonia:</label>
                                        <input type="text" class="form-control form-control-sm" name="dir_colonia" id="dir_colonia" autocomplete="off" placeholder="Colonia">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="dir_edo" class="col-form-label form-control-sm">Estado:</label>
                                        <input type="text" class="form-control form-control-sm" name="dir_edo" id="dir_edo" autocomplete="off" placeholder="Estado Dirección">
                                    </div>
                                </div>

                                <div class="col-3 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="dir_cp" class="col-form-label form-control-sm">Código Postal:</label>
                                        <input type="text" class="form-control form-control-sm" name="dir_cp" id="dir_cp" autocomplete="off" placeholder="Código Postal">
                                    </div>
                                </div>
                                <div class="col-3 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="folio" class="col-form-label form-control-sm">Folio INE:</label>
                                        <input type="text" class="form-control form-control-sm" name="folio" id="folio" autocomplete="off" placeholder="Folio">
                                    </div>
                                </div>
                                <div class="col-4 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="nacionalidad" class="col-form-label form-control-sm">Nacionalidad:</label>
                                        <input type="text" class="form-control form-control-sm" name="nacionalidad" id="nacionalidad" autocomplete="off" placeholder="Nacionalidad">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="estado" class="col-form-label form-control-sm">Estado Civil:</label>
                                        <input type="text" class="form-control form-control-sm" name="estado" id="estado" autocomplete="off" placeholder="Estado">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="tel_cel" class="col-form-label form-control-sm">Teléfono Celular:</label>
                                        <input type="text" class="form-control form-control-sm" name="tel_cel" id="tel_cel" autocomplete="off" placeholder="Teléfono Celular">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="tel_casa" class="col-form-label form-control-sm">Teléfono Casa:</label>
                                        <input type="text" class="form-control form-control-sm" name="tel_casa" id="tel_casa" autocomplete="off" placeholder="Teléfono Casa">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="tel_trab" class="col-form-label form-control-sm">Teléfono Trabajo:</label>
                                        <input type="text" class="form-control form-control-sm" name="tel_trab" id="tel_trab" autocomplete="off" placeholder="Teléfono Trabajo">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12">
                                    <div class="form-group input-group-sm">
                                        <label for="email" class="col-form-label form-control-sm">Email:</label>
                                        <input type="email" class="form-control form-control-sm" name="email" id="email" autocomplete="off" placeholder="Email">
                                    </div>
                                </div>


                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="cuenta" class="col-form-label form-control-sm">Cuenta:</label>
                                        <input type="text" class="form-control form-control-sm" name="cuenta" id="cuenta" autocomplete="off" placeholder="Cuenta">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="banco" class="col-form-label form-control-sm">Banco:</label>
                                        <input type="text" class="form-control form-control-sm" name="banco" id="banco" autocomplete="off" placeholder="Banco">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="status" class="col-form-label form-control-sm">Status:</label>
                                        <input type="text" class="form-control form-control-sm" name="status" id="status" autocomplete="off" placeholder="Status">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="especial" class="col-form-label form-control-sm">Especial:</label>
                                        <select class="form-control form-control-sm" name="especial" id="especial">
                                            <option value="0">No</option>
                                            <option value="1">Sí</option>
                                        </select>
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
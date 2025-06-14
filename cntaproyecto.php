<?php
$pagina = "cntaproyecto";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$consulta = "SELECT * FROM proyecto WHERE edo_proy=1 ORDER BY id_proy";
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
    <!-- Content Header (Page header) -->


    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">PROYECTOS</h1>
            </div>

            <div class="card-body">

                <div class="row">
                    <div class="col-lg-12">
                        <?php if($_SESSION['s_rol'] == 2 || $_SESSION['s_rol'] == 3): ?>
                        <button id="btnNuevo" type="button" class="btn bg-green btn-ms" data-toggle="modal"><i class="fas fa-plus-square text-light"></i><span class="text-light"> Nuevo</span></button>
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
                                            <th>CLAVE</th>
                                            <th>NOMBRE</th>
                                            <th>DESC</th>
                                            <th>MANZANAS</th>
                                            <th>UBICACION</th>
                                            <th>TIPO</th>
                                            <th>CREDITO</th>
                                            <th>CONTADO</th>
                                            <th>VENDIBLE</th>
                                            <th>CLAVE_EMP</th>
                                            <th>MAPA URL</th>
                                            <th>MAPA</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($data as $dat) {
                                        ?>
                                            <tr>
                                                <td><?php echo $dat['id_proy'] ?></td>
                                                <td><?php echo $dat['clave'] ?></td>
                                                <td><?php echo $dat['nombre'] ?></td>
                                                <td><?php echo $dat['desc'] ?></td>
                                                <td><?php echo $dat['manzanas'] ?></td>
                                                <td><?php echo $dat['ubicacion'] ?></td>
                                                <td><?php echo $dat['tipo'] ?></td>
                                                <td><?php echo $dat['credito'] ?></td>
                                                <td><?php echo $dat['contado'] ?></td>
                                                <td><?php echo $dat['vendible'] ?></td>
                                                <td><?php echo $dat['clave_emp'] ?></td>
                                                <td><?php echo $dat['mapa'] ?></td>
                                                <td class="text-center">
                                                    <?php if (!empty($dat['mapa'])): ?>
                                                        <a href="<?php echo $dat['mapa'].'?id_proy='.$dat['id_proy'] ?>" >
                                                            <i class="fas fa-map-marked-alt text-success"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <i class="fas fa-ban text-muted" title="No disponible"></i>
                                                    <?php endif; ?>
                                                </td>


                                                <td class="text-center"></td>
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
                        <h5 class="modal-title" id="modalCRUDLabel">NUEVO COLABORADOR</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card card-widget" style="margin: 10px;">
                            <form id="formDatos" class="row p-2 g-2" action="" method="POST">
                                <div class="col-12">
                                    <div class="form-group input-group-sm">
                                        <label for="nombre" class="col-form-label form-control-sm">*NOMBRE:</label>
                                        <input type="text" class="form-control form-control-sm" name="nombre" id="nombre" autocomplete="off" placeholder="NOMBRE" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="telefono" class="col-form-label form-control-sm">*TELEFONO:</label>
                                        <input type="text" class="form-control form-control-sm" name="telefono" id="telefono" autocomplete="off" placeholder="TELEFONO" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="correo" class="col-form-label form-control-sm">*CORREO:</label>
                                        <input type="email" class="form-control form-control-sm" name="correo" id="correo" autocomplete="off" placeholder="CORREO" required>
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
<script src="fjs/cntaproyecto.js?v=<?php echo (rand()); ?>"></script>
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
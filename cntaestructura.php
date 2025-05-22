<?php
$pagina = "colaborador";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

if (isset($_GET['id_proy'])) {
    $id_proy = $_GET['id_proy'];



    $consulta = "SELECT id_proy,clave,nombre FROM proyecto WHERE id_proy=:id_proy";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id_proy', $id_proy, PDO::PARAM_INT);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $proyecto = $data[0]['nombre'];
    $clave_proy = $data[0]['clave'];

    $consultamn = "SELECT * FROM manzana WHERE id_proy=:id_proy ORDER BY id_man";
    $resultadomn = $conexion->prepare($consultamn);
    $resultadomn->bindParam(':id_proy', $id_proy, PDO::PARAM_INT);
    $resultadomn->execute();
    $datamn = $resultadomn->fetchAll(PDO::FETCH_ASSOC);
} else {
    $id_proy = NULL;
    $proyecto = "";
    $consulta = "SELECT id_proy,clave,nombre FROM proyecto WHERE edo_proy=1 ORDER BY id_proy";
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
                <h1 class="card-title mx-auto">COLABORADORES</h1>
            </div>

            <div class="card-body">
            <?php if ($id_proy != null) { ?>
                <div class="row">
                    <div class="col-sm-12">
                        <button id="btnNuevo" type="button" class="btn bg-green btn-ms" data-toggle="modal"><i class="fas fa-plus-square text-light"></i><span class="text-light"> Nuevo</span></button>
                    </div>
                </div>
            <?php } ?>
                <br>

                <div class="container-fluid">
                    <div class="row justify-content-center  ">
                        <div class="col-sm-8">
                            <div class="card">
                                <div class="card-header bg-green">
                                    SELECCIONAR PROYECTO
                                </div>
                                <div class="card-body ">
                                    <div class="row justify-content-center mb-3">
                                        <div class="col-sm-8">
                                            <div class="input-group input-group-sm">
                                                <label for="obra" class="col-form-label">PROYECTO:</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="hidden" class="form-control" name="id_proy" id="id_proy" value="<?php echo $id_proy; ?>">
                                                    <input type="hidden" class="form-control" name="clave_proy" id="clave_proy" value="<?php echo $clave_proy; ?>">
                                                    <input type="text" class="form-control" name="proyecto" id="proyecto" disabled placeholder="SELECCIONAR PROYECTO" value="<?php echo $proyecto; ?>">
                                                    <?php if ($id_proy == null) { ?>
                                                        <span class="input-group-append">
                                                            <button id="bproyecto" type="button" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                                                        </span>
                                                    <?php } ?>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>


                    </div>
                    <?php if ($id_proy != null) { ?>
                        <div class="row justify-content-center">
                            <div class="col-sm-8">
                                <div class="table-responsive">
                                    <table name="tablaV" id="tablaV" class="table table-sm table-striped table-bordered table-condensed text-nowrap w-100" style="font-size:14px">

                                        <thead class="text-center bg-green">
                                            <tr>
                                                <th>ID</th>
                                                <th>CLAVE</th>
                                                <th>DESCRIPCION</th>
                                                <th>ACCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($datamn as $dat) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $dat['id_man'] ?></td>
                                                    <td><?php echo $dat['clave_manzana'] ?></td>
                                                    <td><?php echo $dat['descripcion'] ?></td>

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

                    <?php }  ?>
                </div>

            </div>
            <!-- /.card-body -->

            <!-- /.card-footer-->
        </div>
        <!-- /.card -->

    </section>

    <!-- INICIA PROYECTO -->
    <section>
        <div class="container-fluid">

            <!-- Default box -->
            <div class="modal fade" id="modalProyecto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-md" role="document">
                    <div class="modal-content w-auto">
                        <div class="modal-header bg-gradient-green">
                            <h5 class="modal-title" id="exampleModalLabel">BUSCAR PROYECTO</h5>

                        </div>
                        <br>
                        <div class="table-hover table-responsive w-auto" style="padding:15px">
                            <table name="tablaProyecto" id="tablaProyecto" class="table table-sm  table-striped table-bordered table-condensed" style="width:100%">
                                <thead class="text-center bg-gradient-green">
                                    <tr>
                                        <th>ID</th>
                                        <th>CLAVE</th>
                                        <th>NOMBRE</th>
                                        <th>ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($data as $datc) {
                                    ?>
                                        <tr>
                                            <td><?php echo $datc['id_proy'] ?></td>
                                            <td><?php echo $datc['clave'] ?></td>
                                            <td><?php echo $datc['nombre'] ?></td>
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
    <!-- TERMINA PROYECTO -->

  <section>
        <div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-green">
                        <h5 class="modal-title" id="exampleModalLabel">AGREGAR MANZANA, PISO, DIVISION...</h5>

                    </div>
                    <div class="card card-widget" style="margin: 10px;">
                        <form id="formDatos" action="" method="POST">
                            <div class="modal-body row">

                           
                             <div class="col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="clave" class="col-form-label">Clave:</label>
                                        <input type="text" class="form-control" name="clave" id="clave" autocomplete="off" placeholder="Clave" required>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group input-group-sm">
                                        <label for="descripcion" class="col-form-label">Descripcion:</label>
                                        <input type="text" class="form-control" name="descripcion" id="descripcion" autocomplete="off" placeholder="Descripcion" required>
                                    </div>
                                </div>

                      
                            </div>
                    </div>


                   
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
                        <button type="button" id="btnGuardarmn" name="btnGuardarmn" class="btn btn-success" value="btnGuardar"><i class="far fa-save"></i> Guardar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>


    <!-- /.content -->
</div>






<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntaestructura.js?v=<?php echo (rand()); ?>"></script>
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
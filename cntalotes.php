<?php
$pagina = "colaborador";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

if (isset($_GET['id_proy']) && isset($_GET['id_man'])) {
    $id_proy = $_GET['id_proy'];
    $id_man = $_GET['id_man'];


    $consulta = "SELECT id_proy,clave,nombre FROM proyecto WHERE id_proy=:id_proy";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id_proy', $id_proy, PDO::PARAM_INT);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $proyecto = $data[0]['nombre'];
    $clave_proy = $data[0]['clave'];

    $consultamn = "SELECT id_man, clave_manzana, descripcion FROM manzana WHERE id_proy=:id_proy and id_man=:id_man ORDER BY id_man";
    $resultadomn = $conexion->prepare($consultamn);
    $resultadomn->bindParam(':id_proy', $id_proy, PDO::PARAM_INT);
    $resultadomn->bindParam(':id_man', $id_man, PDO::PARAM_INT);
    $resultadomn->execute();
    $datamn = $resultadomn->fetchAll(PDO::FETCH_ASSOC);
    $descripcion = $datamn[0]['descripcion'];
    $clave_man = $datamn[0]['clave_manzana'];

    $cntamn = "SELECT id_man, clave_manzana, descripcion FROM manzana WHERE id_proy=:id_proy  ORDER BY id_man";
    $resmn = $conexion->prepare($cntamn);
    $resmn->bindParam(':id_proy', $id_proy, PDO::PARAM_INT);
  
    $resmn->execute();
    $dtman = $resmn->fetchAll(PDO::FETCH_ASSOC);



    $consultalt = "SELECT * FROM lote WHERE id_proy=:id_proy and id_man=:id_man ORDER BY id_lote";
    $resultadolt = $conexion->prepare($consultalt);
    $resultadolt->bindParam(':id_proy', $id_proy, PDO::PARAM_INT);
    $resultadolt->bindParam(':id_man', $id_man, PDO::PARAM_INT);
    $resultadolt->execute();
    $datalt = $resultadolt->fetchAll(PDO::FETCH_ASSOC);

    $consultatipo = "SELECT id_tipo, tipo, precio FROM tipo WHERE id_proy=:id_proy ORDER BY id_tipo";
    $resultadotipo = $conexion->prepare($consultatipo);
    $resultadotipo->bindParam(':id_proy', $id_proy, PDO::PARAM_INT);
    $resultadotipo->execute();
    $datatipo = $resultadotipo->fetchAll(PDO::FETCH_ASSOC);
} else {
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
                <h1 class="card-title mx-auto">LOTES, LOCALES, DEPTOS...</h1>
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
                                    INFORMACION
                                </div>
                                <div class="card-body ">
                                    <div class="row justify-content-center mb-3">
                                        <div class="col-sm-6">
                                            <div class="input-group input-group-sm">
                                                <label for="proyecto" class="col-form-label">PROYECTO:</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="hidden" class="form-control" name="id_proy" id="id_proy" value="<?php echo $id_proy; ?>">
                                                    <input type="hidden" class="form-control" name="clave_proy" id="clave_proy" value="<?php echo $clave_proy; ?>">
                                                    <input type="text" class="form-control" name="proyecto" id="proyecto" disabled placeholder="SELECCIONAR PROYECTO" value="<?php echo $proyecto; ?>">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-sm-6">
                                            <div class="input-group input-group-sm">
                                                <label for="manzana" class="col-form-label">SECCION,PISO MANZANA,ETC:</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="hidden" class="form-control" name="id_man" id="id_man" value="<?php echo $id_man; ?>">
                                                    <input type="hidden" class="form-control" name="clave_man" id="clave_man" value="<?php echo $clave_man; ?>">
                                                    <select class="form-control" name="manzana" id="manzana" required>
                                                        <?php foreach ($dtman as $man): ?>
                                                            <option value="<?php echo $man['id_man']; ?>" <?php echo ($man['id_man'] == $id_man) ? 'selected' : ''; ?>>
                                                                <?php echo $man['descripcion']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
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
                                                <th>LOTE</th>
                                                <th>CLAVE</th>
                                                <th>ESTADO</th>
                                                <th>ACCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($datalt as $dat) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $dat['clave_lote'] ?></td>
                                                    <td><?php echo $dat['id_lote'] ?></td>
                                                    <td><?php echo $dat['id_mapa'] ?></td>
                                                    <td><?php echo $dat['status'] ?></td>

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
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-green">
                        <h5 class="modal-title" id="exampleModalLabel">AGREGAR LOTE</h5>
                    </div>
                    <div class="card card-widget" style="margin: 10px;">
                        <form id="formDatos" action="" method="POST">
                            <div class="modal-body">

                                <!-- BLOQUE 1: CLAVE LOTE, ID MAPA, MANZANA -->
                                <div class="row mb-2">
                                    <div class="col-sm-4">
                                        <div class="form-group input-group-sm">
                                            <label for="clave_lote" class="col-form-label">Clave Lote:</label>
                                            <input type="text" class="form-control" name="clave_lote" id="clave_lote" autocomplete="off" placeholder="Clave Lote" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group input-group-sm">
                                            <label for="id_mapa" class="col-form-label">ID Mapa:</label>
                                            <input type="text" class="form-control" name="id_mapa" id="id_mapa" autocomplete="off" placeholder="ID Mapa" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group input-group-sm">
                                            <label for="manzana" class="col-form-label">Manzana:</label>
                                            <input type="text" class="form-control" name="manzana" id="manzana" autocomplete="off" placeholder="Manzana" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- BLOQUE 2: BRÚJULA (PUNTOS CARDINALES) -->
                                <div class="row  justify-content-center">
                                    <div class="col-sm-4 text-center">
                                        <div class="form-group input-group-sm">
                                            <label for="noroeste" class="col-form-label">Noroeste</label>
                                            <input type="text" class="form-control" name="noroeste" id="noroeste" autocomplete="off" placeholder="Noroeste">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 text-center">
                                        <div class="form-group input-group-sm">
                                            <label for="norte" class="col-form-label">Norte</label>
                                            <input type="text" class="form-control" name="norte" id="norte" autocomplete="off" placeholder="Norte">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 text-center">
                                        <div class="form-group input-group-sm">
                                            <label for="noreste" class="col-form-label">Noreste</label>
                                            <input type="text" class="form-control" name="noreste" id="noreste" autocomplete="off" placeholder="Noreste">
                                        </div>
                                    </div>
                                </div>


                                <div class="row  justify-content-center">

                                    <div class="col-sm-4 text-center">
                                        <div class="form-group input-group-sm">
                                            <label for="oeste" class="col-form-label">Oeste</label>
                                            <input type="text" class="form-control" name="oeste" id="oeste" autocomplete="off" placeholder="Oeste">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 text">
                                        <div class="form-group input-group-sm text-center mb-0">
                                            <img class="text-center " src="img/plot2.jpg" alt="" style="max-height: 80px">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 justify-content-center text-center">
                                        <div class="form-group input-group-sm">
                                            <label for="este" class="col-form-label">Este</label>
                                            <input type="text" class="form-control" name="este" id="este" autocomplete="off" placeholder="Este">
                                        </div>
                                    </div>
                                </div>
                                <div class="row  justify-content-center">

                                    <div class="col-sm-4 text-center">
                                        <div class="form-group input-group-sm">
                                            <label for="suroeste" class="col-form-label">Suroeste</label>
                                            <input type="text" class="form-control" name="suroeste" id="suroeste" autocomplete="off" placeholder="Suroeste">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 text-center">
                                        <div class="form-group input-group-sm">
                                            <label for="sur" class="col-form-label">Sur</label>
                                            <input type="text" class="form-control" name="sur" id="sur" autocomplete="off" placeholder="Sur">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 text-center">
                                        <div class="form-group input-group-sm">
                                            <label for="sureste" class="col-form-label">Sureste</label>
                                            <input type="text" class="form-control" name="sureste" id="sureste" autocomplete="off" placeholder="Sureste">
                                        </div>
                                    </div>
                                </div>


                                <!-- BLOQUE 3: ID TIPO, SUPERFICIE, PRECIO, VALOR TOTAL -->
                                <div class="row justify-content-center">

                                    <div class="col-sm-3">
                                        <div class="form-group input-group-sm">
                                            <label for="id_tipo" class="col-form-label">Tipo:</label>
                                            <select class="form-control" name="id_tipo" id="id_tipo" required>
                                                <option value="">Seleccione un tipo</option>
                                                <?php

                                                foreach ($datatipo as $row) {
                                                    echo '<option value="' . $row['id_tipo'] . '">' . $row['tipo'] . " (" . $row['precio'] . ")" . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group input-group-sm">
                                            <label for="superficie" class="col-form-label">Superficie:</label>
                                            <input type="number" step="any" class="form-control" name="superficie" id="superficie" autocomplete="off" placeholder="Superficie">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group input-group-sm">
                                            <label for="precio" class="col-form-label">Precio:</label>
                                            <input type="text" step="any" class="form-control" name="precio" id="precio" autocomplete="off" placeholder="Precio" disabled>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group input-group-sm">
                                            <label for="valortotal" class="col-form-label">Valor Total:</label>
                                            <input type="text" step="any" class="form-control" name="valortotal" id="valortotal" autocomplete="off" placeholder="Valor Total" disabled>
                                        </div>
                                    </div>
                                </div>

                                <!-- BLOQUE 4: FRENTE, FONDO, CONSTRUIDO, INDIVISO, RENTA -->
                                <div class="row mb-2">
                                    <div class="col-sm-2">
                                        <div class="form-group input-group-sm">
                                            <label for="frente" class="col-form-label">Frente:</label>
                                            <input type="text" class="form-control" name="frente" id="frente" autocomplete="off" placeholder="Frente">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group input-group-sm">
                                            <label for="fondo" class="col-form-label">Fondo:</label>
                                            <input type="text" class="form-control" name="fondo" id="fondo" autocomplete="off" placeholder="Fondo">
                                        </div>
                                    </div>
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-2">
                                        <div class="form-group input-group-sm">
                                            <label for="construido" class="col-form-label">Construido:</label>
                                            <input type="text" class="form-control" name="construido" id="construido" autocomplete="off" placeholder="Construido">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group input-group-sm">
                                            <label for="indiviso" class="col-form-label">Indiviso:</label>
                                            <input type="text" class="form-control" name="indiviso" id="indiviso" autocomplete="off" placeholder="Indiviso">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group input-group-sm">
                                            <label for="renta" class="col-form-label">Renta:</label>
                                            <input type="text" class="form-control" name="renta" id="renta" autocomplete="off" placeholder="Renta">
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
        </div>
    </section>


    <!-- /.content -->
</div>






<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntalotes.js?v=<?php echo (rand()); ?>"></script>
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
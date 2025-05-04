<?php
$pagina = "requisicion";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();
$fecha = date('Y-m-d');

$nreq = 0;


$consulta = "SELECT * FROM requisicion WHERE edo_req=1 ORDER BY id_req";

$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);


$message = "";



?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->


    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">REQUISICIONES</h1>
            </div>

            <div class="card-body">

                <div class="row justify-content-between">
                    <div class="col-sm-3">
                        <button id="btnNuevo" type="button" class="btn bg-green btn-ms" data-toggle="modal"><i class="fas fa-plus-square text-light"></i><span class="text-light"> Nuevo</span></button>
                    </div>

                </div>

                <br>
                <!--
                <div class="card">



                    <div class="card-header bg-gradient-green">
                        Filtro por rango de Fecha
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-lg-2">
                                <div class="form-group input-group-sm">
                                    <label for="fecha" class="col-form-label">Desde:</label>
                                    <input type="date" class="form-control" name="inicio" id="inicio">
                                    <input type="hidden" class="form-control" name="tipo_proy" id="tipo_proy" value=1>

                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="form-group input-group-sm">
                                    <label for="fecha" class="col-form-label">Hasta:</label>
                                    <input type="date" class="form-control" name="final" id="final">
                                </div>
                            </div>

                            <div class="col-lg-1 align-self-end text-center">
                                <div class="form-group input-group-sm">
                                    <button id="btnBuscar" name="btnBuscar" type="button" class="btn bg-gradient-success btn-ms"><i class="fas fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid">
-->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table name="tablaV" id="tablaV" class="table table-sm table-striped table-bordered table-condensed w-auto mx-auto " style="width:100%;font-size:15px">
                                <thead class="text-center bg-green">
                                    <tr>
                                        <th># CONTROL</th>
                                        <th>FECHA</th>
                                        <th>SOLICITANTE</th>
                                        <th>DESCRIPCION</th>
                                        <th>ESTADO</th>
                                        <th>ACCIONES</th>

                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    foreach ($data as $dat) {
                                    ?>
                                        <tr>
                                            <td><?php echo $dat['id_req'] ?></td>
                                           
                                           
                                            <td class="text-center"><?php echo $dat['fecha_req'] ?></td>
                                            <td><?php echo $dat['sol_req'] ?></td>
                                            <td><?php echo $dat['desc_req'] ?></td>
                                            <td><?php echo $dat['estado_req'] ?></td>
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

        <!-- /.card -->

    </section>

    <!-- INICIA ALTA DE FACTURAS -->
    <section>
        <div class=" " id="modalReq" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content w-auto">
                    <div class="modal-header bg-green">
                        <h5 class="modal-title" id="exampleModalLabel">ALTA DE REQUISICIONES</h5>

                    </div>
                    <form id="formReq" action="" method="POST" autocomplete="off">
                        <div class="card card-widget" style="margin: 10px;">

                            <div class="modal-body">
                                <div class="row justify-content-sm-center">

                                    <div class="col-sm-3">
                                        <div class="form-group input-group-sm">
                                            <label for="folioreq" class="col-form-label"># Control:</label>
                                            <input type="text" class="form-control" name="folioreq" id="folioreq" disabled>

                                        </div>
                                    </div>

                                    <div class="col-sm-6">

                                    </div>



                                    <div class="col-sm-3 ">
                                        <div class="form-group input-group-sm">
                                            <label for="fechareq" class="col-form-label">Fecha:</label>
                                            <input type="date" class="form-control" name="fechareq" id="fechareq" value="<?php echo $fecha; ?>">
                                        </div>
                                    </div>

                                </div>

                                <div class=" row justify-content-sm-center">


                                    <div class="col-sm-12">
                                        <div class="input-group input-group-sm">
                                            <label for="proveedor" class="col-form-label">Solicitante:</label>
                                            <div class="input-group input-group-sm">

                                                <input type="text" class="form-control" name="solicitante" id="solicitante" placeholder="Escriba el nombre del solicitante">

                                            </div>

                                        </div>
                                    </div>



                                </div>

                                <div class=" row justify-content-sm-center">

                                    <div class="col-sm-12">
                                        <div class="form-group input-group-sm">
                                            <label for="descripcionreq" class="col-form-label">Descripción:</label>
                                            <textarea row="2" type="text" class="form-control" name="descripcionreq" id="descripcionreq" placeholder="CONCEPTO"></textarea>
                                        </div>
                                    </div>

                                </div>


                                <div class="row justify-content-sm-center">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table name="tablaDet" id="tablaDet" class="table table-sm table-striped table-bordered table-condensed w-100 mx-auto " style="width:100%;font-size:15px">
                                                <thead class="text-center bg-green">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>CONCEPTO</th>
                                                        <th>CANTIDAD</th>
                                                        <th>U MEDIDA</th>
                                                        <th>ACCIONES</th>

                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class=" modal-footer">
                                <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
                                <button type="button" id="btnGuardarreq" name="btnGuardarreq" class="btn bg-green" value="btnGuardarreq"><i class="far fa-save"></i> Guardar</button>
                            </div>


                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- TERMINA ALTA DE FACTURAS -->

    <!-- INICIA OBRA -->
    <section>
        <div class="container">

            <!-- Default box -->
            <div class="modal fade" id="modalObra" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-md" role="document">
                    <div class="modal-content w-auto">
                        <div class="modal-header bg-gradient-green">
                            <h5 class="modal-title" id="exampleModalLabel">BUSCAR OBRA</h5>

                        </div>
                        <br>
                        <div class="table-hover table-responsive w-auto" style="padding:15px">
                            <table name="tablaObra" id="tablaObra" class="table table-sm text-nowrap table-striped table-bordered table-condensed" style="width:100%">
                                <thead class="text-center bg-gradient-green">
                                    <tr>
                                        <th>ID</th>
                                        <th>CLAVE</th>
                                        <th>NOMBRE CORTO</th>
                                        <th>ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($datacon as $datc) {
                                    ?>
                                        <tr>
                                            <td><?php echo $datc['id_obra'] ?></td>
                                            <td><?php echo $datc['clave_obra'] ?></td>
                                            <td><?php echo $datc['corto_obra'] ?></td>
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
    <!-- TERMINA OBRA -->

    <!-- INICIA PROVEEDOR -->
    <section>
        <div class="container">

            <!-- Default box -->
            <div class="modal fade" id="modalProveedor" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-md" role="document">
                    <div class="modal-content w-auto">
                        <div class="modal-header bg-gradient-green">
                            <h5 class="modal-title" id="exampleModalLabel">BUSCAR OBRA</h5>

                        </div>
                        <br>
                        <div class="table-hover table-responsive w-auto" style="padding:15px">
                            <table name="tablaProveedor" id="tablaProveedor" class="table table-sm text-nowrap table-striped table-bordered table-condensed" style="width:100%">
                                <thead class="text-center bg-gradient-green">
                                    <tr>
                                        <th>ID</th>
                                        <th>RFC</th>
                                        <th>RAZON SOCIAL</th>
                                        <th>ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($dataprov as $datp) {
                                    ?>
                                        <tr>
                                            <td><?php echo $datp['id_prov'] ?></td>
                                            <td><?php echo $datp['rfc_prov'] ?></td>
                                            <td><?php echo $datp['razon_prov'] ?></td>
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
    <!-- TERMINA PROVEEDOR -->





    <!-- INICIA PAGAR-->
    <section>
        <div class="modal fade" id="modalPago" tabindex="-2" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-green">
                        <h5 class="modal-title" id="exampleModalLabel">REGISTRAR PAGO</h5>

                    </div>
                    <form id="formPago" action="" method="POST">
                        <div class="modal-body">
                            <div class="row justify-content-sm-between my-auto">




                                <div class="col-sm-3 my-auto">
                                    <div class="form-group input-group-sm">
                                        <label for="foliovp" class="col-form-label">FOLIO:</label>
                                        <input type="text" class="form-control" name="foliovp" id="foliovp" disabled>

                                    </div>
                                </div>




                                <div class="col-sm-3 my-auto">
                                    <div class="form-group input-group-sm">
                                        <label for="fechavp" class="col-form-label ">FECHA DE PAGO:</label>
                                        <input type="date" id="fechavp" name="fechavp" class="form-control text-right" autocomplete="off" value="<?php echo date("Y-m-d") ?>" placeholder="FECHA">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group input-group-sm">
                                        <label for="referenciavp" class="col-form-label">REFERENCIA DE PAGO</label>
                                        <input type="text" class="form-control" name="referenciavp" id="referenciavp" autocomplete="off" placeholder="REFERENCIA (CHEQUE,#AUTORIZACIÓN)">
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-sm-center">
                                <div class="col-sm-12">
                                    <div class="form-group input-group-sm">
                                        <label for="observacionesvp" class="col-form-label">OBSERVACIONES:</label>
                                        <textarea class="form-control" name="observacionesvp" id="observacionesvp" rows="3" autocomplete="off" placeholder="OBSERVACIONES"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-sm-center">

                                <div class="col-lg-4 ">
                                    <label for="saldovp" class="col-form-label ">SALDO ACTUAL:</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control text-right" name="saldovp" id="saldovp" disabled>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <label for="montopagovp" class="col-form-label">MONTO DE PAGO:</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>

                                        </div>
                                        <input type="text" id="montopagovp" name="montopagovp" class="form-control text-right" autocomplete="off" placeholder="MONTO DEL PAGO">
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="input-group-sm">
                                        <label for="metodovp" class="col-form-label">METODO DE PAGO:</label>

                                        <select class="form-control" name="metodovp" id="metodovp">
                                            <option id="EFECTIVO" value="EFECTIVO">EFECTIVO</option>
                                            <option id="TRANSFERENCIA" value="TRANSFERENCIA">TRANSFERENCIA</option>
                                            <option id="DEPOSITO" value="DEPOSITO">DEPOSITO</option>
                                            <option id="CHEQUE" value="CHEQUE">CHEQUE</option>
                                            <option id="TARJETA DE CREDITO" value="TARJETA DE CREDITO">TARJETA DE CREDITO</option>
                                            <option id="TARJETA DE DEBITO" value="TARJETA DE DEBITO">TARJETA DE DEBITO</option>

                                        </select>
                                    </div>
                                </div>

                            </div>


                        </div>





                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
                            <button type="button" id="btnGuardarvp" name="btnGuardarvp" class="btn btn-success" value="btnGuardar"><i class="far fa-save"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- TERMINA PAGAR -->

    <!-- INICIA RESUMEN DE PAGOS -->
    <section>
        <div class="container">


            <!-- Default box -->
            <div class="modal fade" id="modalResumenp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-md" role="document">
                    <div class="modal-content w-auto">
                        <div class="modal-header bg-gradient-green">
                            <h5 class="modal-title" id="exampleModalLabel">Resumen de Pagos</h5>

                        </div>
                        <br>
                        <div class="table-hover responsive w-auto " style="padding:10px">
                            <table name="tablaResumenp" id="tablaResumenp" class="table table-sm table-striped table-bordered table-condensed display compact" style="width:100%">
                                <thead class="text-center bg-gradient-green">
                                    <tr>
                                        <th>FOLIO</th>
                                        <th>FECHA</th>
                                        <th>REFERENCIA</th>
                                        <th>MONTO</th>
                                        <th>METODO</th>
                                        <th>ACCIONES</th>

                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>

                                    <th></th>
                                    <th></th>
                                    <th class="text-right">TOTAL</th>
                                    <th class="text-right"></th>
                                    <th></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>


                    </div>

                </div>
                <!-- /.card-body -->

                <!-- /.card-footer-->
            </div>
            <!-- /.card -->

        </div>
    </section>
    <!-- TERMINA RESUMEN DE PAGOS -->



    <!-- INICIA CANCELAR -->
    <section>
        <div class="modal fade" id="modalcan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-danger">
                        <h5 class="modal-title" id="exampleModalLabel">CANCELAR</h5>
                    </div>
                    <div class="card card-widget" style="margin: 10px;">
                        <form id="formcan" action="" method="POST">
                            <div class="modal-body row">
                                <div class="col-sm-12">
                                    <div class="form-group input-group-sm">
                                        <label for="motivo" class="col-form-label">Motivo de Cancelacioón:</label>
                                        <textarea rows="3" class="form-control" name="motivo" id="motivo" placeholder="Motivo de Cancelación"></textarea>
                                        <input type="hidden" id="fecha" name="fecha" value="<?php echo $fecha ?>">
                                        <input type="hidden" id="foliocan" name="foliocan">
                                        <input type="hidden" id="tipodoc" name="tipodoc">
                                    </div>
                                </div>
                            </div>
                    </div>
                    <?php
                    if ($message != "") {
                    ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <span class="badge "><?php echo ($message); ?></span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
                        <button type="button" id="btnGuardarCAN" name="btnGuardarCAN" class="btn btn-success" value="btnGuardarCAN"><i class="far fa-save"></i> Guardar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- TERMINA CANCELAR -->

</div>


<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntarequisicion.js?v=<?php echo (rand()); ?>"></script>
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
<script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/formatted-numbers.js"></script>
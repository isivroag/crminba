<?php
$pagina = "proveedor";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$consulta = "SELECT * FROM proveedor WHERE edo_prov=1 ORDER BY id_prov";
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
                <h1 class="card-title mx-auto">PROVEEDORES</h1>
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
                                            <th>RFC</th>
                                            <th>RAZON SOCIAL</th>
                                            <th>TEL</th>
                                            <th>CONTACTO</th>
                                            <th>TEL CONTACTO</th>
                                            <th>CORREO</th>
                                            <th>C</th>
                                            <th>CALIFICACION</th>
                                            <th>TIPO</th>
                                            <th>ESTADO</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($data as $dat) {
                                        ?>
                                            <tr>
                                                <td><?php echo $dat['id_prov'] ?></td>
                                                <td><?php echo $dat['rfc_prov'] ?></td>
                                                <td><?php echo $dat['razon_prov'] ?></td>
                                                <td><?php echo $dat['tel_prov'] ?></td>
                                                <td><?php echo $dat['contacto_prov'] ?></td>
                                                <td><?php echo $dat['telcon_prov'] ?></td>
                                                <td><?php echo $dat['correo_prov'] ?></td>
                                                <td><?php echo $dat['puntaje'] ?></td>

                                                <td></td>
                                                <td><?php echo $dat['tipo_prov'] ?></td>
                                                <td><?php echo $dat['estado'] ?></td>
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
        <div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-green">
                        <h5 class="modal-title" id="exampleModalLabel">NUEVO PROVEEDOR</h5>

                    </div>
                    <div class="card card-widget" style="margin: 10px;">
                        <form id="formDatos" action="" method="POST">
                            <div class="modal-body row">

                                <div class="col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="rfc" class="col-form-label">*RFC:</label>
                                        <input type="text" class="form-control" name="rfc" id="rfc" autocomplete="off" placeholder="RFC">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group input-group-sm auto">
                                        <label for="tipo" class="col-form-label">*TIPO:</label>
                                        <select class="form-control" name="tipo" id="tipo">

                                            <option id="tipo1" value="PROVEEDOR"> PROVEEDOR</option>
                                            <option id="tipo2" value="SUBCONTRATISTA"> SUBCONTRATISTA</option>


                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="tel" class="col-form-label">TELEFONO:</label>
                                        <input type="text" class="form-control" name="tel" id="tel" autocomplete="off" placeholder="Teléfono">
                                    </div>
                                </div>


                                <div class="col-sm-12">
                                    <div class="form-group input-group-sm">
                                        <label for="razon" class="col-form-label">*NOMBRE / RAZON SOCIAL:</label>
                                        <input type="text" class="form-control" name="razon" id="razon" autocomplete="off" placeholder="RAZON SOCIAL">
                                    </div>
                                </div>



                                <div class="col-sm-8">
                                    <div class="form-group input-group-sm">
                                        <label for="contacto" class="col-form-label">CONTACTO:</label>
                                        <input type="text" class="form-control" name="contacto" id="contacto" autocomplete="off" placeholder="Contacto">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="tel_contacto" class="col-form-label">TELEFONO DE CONTACTO:</label>
                                        <input type="text" class="form-control" name="tel_contacto" id="tel_contacto" autocomplete="off" placeholder="Teléfono deContacto">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="correo" class="col-form-label">*CORREO:</label>
                                        <input type="mail" class="form-control" name="correo" id="correo" autocomplete="off" placeholder="Correo">
                                    </div>
                                </div>
                                <div class="col-sm-2">

                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group input-group-sm auto">
                                        <label for="puntaje" class="col-form-label">CALIFICACION:</label>
                                        <select class="form-control selectpicker bg-white" name="puntaje" id="puntaje">
                                            <option value='0' data-content="
                        <i class='fa-regular fa-star'></i>
                        <i class='fa-regular fa-star '></i>
                        <i class='fa-regular fa-star '></i>
                        <i class='fa-regular fa-star '></i>
                        <i class='fa-regular fa-star '></i>
                        ">
                                            </option>
                                            <option value='1' data-content="
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-regular fa-star '></i>
                        <i class='fa-regular fa-star '></i>
                        <i class='fa-regular fa-star '></i>
                        <i class='fa-regular fa-star '></i>
                        ">
                                            </option>
                                            <option value='2' data-content="
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-regular fa-star '></i>
                        <i class='fa-regular fa-star '></i>
                        <i class='fa-regular fa-star '></i>
                        ">
                                            </option>
                                            <option value='3' data-content="
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-regular fa-star '></i>
                        <i class='fa-regular fa-star '></i>
                        ">
                                            </option>
                                            <option value='4' data-content="
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-regular fa-star '></i>
                        ">
                                            </option>
                                            <option value='5' data-content="
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-solid fa-star starchecked'></i>
                        <i class='fa-solid fa-star starchecked'></i>
                        ">
                                            </option>

                                        </select>
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
                        <button type="submit" id="btnGuardar" name="btnGuardar" class="btn bg-green" value="btnGuardar"><i class="far fa-save"></i> Guardar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- CUENTA DE PROVEEDOR -->


    <!-- TABLA CUENTAS -->
    <section>
        <div class="container">


            <!-- Default box -->
            ...
            <!-- Modal Documentos -->
            <div class="modal fade" id="modalDocumentos" tabindex="-1" role="dialog" aria-labelledby="modalDocumentosLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-gradient-green">
                            <h5 class="modal-title" id="modalDocumentosLabel">Documentos</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Checklist para Subcontratistas -->
                            <div id="subcontratista-checklist" style="display: none;">
                                <div class="form-group">
                                    <label>Documentos requeridos:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Acta">
                                        <label class="form-check-label">Acta constitutiva</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Poder">
                                        <label class="form-check-label">Poder notarial del representante legal</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="INE_RL">
                                        <label class="form-check-label">INE Represéntate Legal</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="INE_C">
                                        <label class="form-check-label">INE de Contribuyente (Solo Personas Físicas)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="CIF">
                                        <label class="form-check-label">Constancia de Situación Fiscal no mayor a un mes de antigüedad</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Decl_Anual">
                                        <label class="form-check-label">Declaración anual (Ejercicio inmediato anterior)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Acuse_Decl_Anual">
                                        <label class="form-check-label">Acuse de la declaración anual (Ejercicio inmediato anterior)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Decl_Prov">
                                        <label class="form-check-label">Declaración y acuse provisional de los 3 últimos meses del ejercicio en curso</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Estados_Anual">
                                        <label class="form-check-label">Estados financieros ejercicio inmediato anterior, firmados por contador</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Estados_Mensual">
                                        <label class="form-check-label">Estados financieros del mes anterior firmados por contador</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Cedula_Contador">
                                        <label class="form-check-label">Cédula profesional del contador público que firma los estados financieros solicitados</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Registro_IMSS">
                                        <label class="form-check-label">Registro patronal IMSS</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Cumplimiento_IMSS">
                                        <label class="form-check-label">Opinión de cumplimiento POSITIVA IMSS</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Cumplimiento_INFONAVIT">
                                        <label class="form-check-label">Opinión de cumplimiento POSITIVA INFONAVIT</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="No_Adeudo_SEFIPLAN">
                                        <label class="form-check-label">Constancia de NO ADEUDO fiscal DE SEFIPLAN</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Cumplimiento_SAT">
                                        <label class="form-check-label">Opinión de cumplimiento POSITIVA SAT</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Estado_Cuenta">
                                        <label class="form-check-label">Caratula de ESTADO DE CUENTA de banco donde el SUBCONTRATISTA autoriza el pago</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="Domicilio">
                                        <label class="form-check-label">Comprobante de domicilio con antigüedad no mayor a tres meses</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Checklist para Proveedores -->
                            <div id="proveedor-checklist" style="display: none;">
                                <div class="form-group">
                                    <label>Documentos requeridos:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="CIF">
                                        <label class="form-check-label">Constancia de Situación Fiscal</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="documentos[]" value="INE_C">
                                        <label class="form-check-label">INE de Contribuyente (Solo Personas Físicas)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            ...
            <!-- /.card-body -->

            <!-- /.card-footer-->
        </div>
        <!-- /.card -->

</div>
</section>


<!-- /.content -->
</div>






<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntaproveedor.js?v=<?php echo (rand()); ?>"></script>
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
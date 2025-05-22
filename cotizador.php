<?php
$pagina = "cntaproyecto";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();
$mapa = 0;
$id_lote = "";
$id_proyecto = "";
$id_manzana = "";
$proyecto = "";
$manzana = "";
$clave_lote = "";
$valortotal = 0;


if (isset($_GET['id_proy'])  && isset($_GET['id_man']) && isset($_GET['id_lote'])) {
    $mapa = 1;
    $id_proyecto = $_GET['id_proy'];
    $id_manzana = $_GET['id_man'];
    $id_lote = $_GET['id_lote'];


    $resproyecto = $conexion->prepare("SELECT nombre FROM proyecto WHERE id_proy = :id_proy");
    $resproyecto->bindParam(':id_proy', $id_proyecto, PDO::PARAM_INT);
    $resproyecto->execute();
    $proyecto = $resproyecto->fetch(PDO::FETCH_ASSOC);

    $resmanzana = $conexion->prepare("SELECT descripcion FROM manzana WHERE id_proy = :id_proy and id_man=:id_man");
    $resmanzana->bindParam(':id_proy', $id_proyecto, PDO::PARAM_INT);
    $resmanzana->bindParam(':id_man', $id_manzana, PDO::PARAM_INT);
    $resmanzana->execute();
    $manzana = $resmanzana->fetch(PDO::FETCH_ASSOC);

    $consulta = "SELECT id_lote, clave_lote, superficie, preciom, valortotal, status FROM lote 
                WHERE id_proy = :id_proy and id_man=:id_man and clave_lote=:id_lote";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id_proy', $id_proyecto, PDO::PARAM_INT);
    $resultado->bindParam(':id_man', $id_manzana, PDO::PARAM_INT);
    $resultado->bindParam(':id_lote', $id_lote, PDO::PARAM_INT);
    $resultado->execute();
    $lote = $resultado->fetch(PDO::FETCH_ASSOC);

    $proyecto = $proyecto['nombre'];
    $manzana = $manzana['descripcion'];
    $clave_lote = $lote['clave_lote'];
    $valortotal = $lote['valortotal'];
}

$consulta = "SELECT id_proy,nombre FROM proyecto WHERE edo_proy=1 and vendible=1 ORDER BY id_proy";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$proyectos = $resultado->fetchAll(PDO::FETCH_ASSOC);


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





    .rounded-row {
        background-color: #e8f8f5;
    }

    .adjustment-detail {
        font-size: 0.8em;
        color: #27ae60;
    }

    .date-adjustment {
        font-size: 0.8em;
        color: #f39c12;
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
                <h1 class="card-title mx-auto">COTIZADOR</h1>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container justify-content-center">
                                    <h1>Cotizador de Inmuebles</h1>

                                    <form id="creditForm" class=" p-3">
                                        <?php if ($mapa == 0) { ?>
                                            <div class="row justify-content-center">
                                                <div class="col-sm-6 d-block">
                                                    <button class="btn btn-primary w-100" type="button" id="btnBuscar">
                                                        <i class="fas fa-search mr-2"></i>Seleccionar Inmueble
                                                    </button>
                                                </div>
                                            </div>
                                        <?php } ?>


                                        <div class="row ">
                                            <div class="col-sm-4 form-group">
                                                <label class="col-form-label" for="proyecto">Proyecto:</label>
                                                <input type="hidden" id="id_proyecto" name="id_proyecto" class="form-control" value="<?= $id_proyecto?>" disabled>
                                                <input type="text" id="proyecto" name="proyecto" class="form-control" value="<?= $proyecto ?>" disabled>
                                            </div>

                                            <div class="col-sm-4 form-group">
                                                <label class="col-form-label" for="manzana">Manzana:</label>
                                                <input type="hidden" id="id_manzana" name="id_manzana" class="form-control" value="<?= $id_manzana?>" disabled>
                                                <input type="text" id="manzana" name="manzana" class="form-control" value="<?= $manzana ?>" disabled>
                                            </div>

                                            <div class="col-sm-4 form-group">
                                                <label class="col-form-label" for="lote">Lote:</label>
                                                <input type="hidden" id="id_lote" name="id_lote" class="form-control" value="<?= $id_lote ?>" disabled>
                                                <input type="text" id="lote" name="lote" class="form-control" value="<?= $clave_lote ?>" disabled>
                                            </div>

                                        </div>

                                        <div class="row ">
                                            <div class="col-sm-2 form-group">
                                                <label for="folio" class="col-form-label">Folio:</label>
                                                <input type="text" id="folio" name="folio" class="form-control" required>
                                            </div>

                                            <div class=" col-sm-2 form-group ">
                                                <label for="fechaInicio" class="col-form-label">Inicio:</label>
                                                <input type="date" id="fechaInicio" name="fechaInicio" class="form-control" required>

                                            </div>

                                            <div class="form-group col-sm-4">
                                                <label for="montoTotal" class="col-form-label text-right">Valor Total:</label>
                                                <input type="number" id="montoTotal" name="montoTotal" class="form-control text-right" min="1" step="0.01" required value="<?= $valortotal?>" disabled>
                                            </div>

                                            <div class="col-sm-2"></div>
                                            <div class="form-group col-sm-2" class="col-form-label">
                                                <label for="tasaInteresAnual" class="col-form-label">T.I. Anual(%):</label>
                                                <input type="number" id="tasaInteresAnual" name="tasaInteresAnual" class="form-control" min="0" step="0.01" value="17.00">

                                            </div>

                                            <div class="form-group col-sm-3">
                                                <label for="montoEnganche" class="col-form-label">Monto de Enganche:</label>
                                                <input type="number" id="montoEnganche" name="montoEnganche" class="form-control text-right" min="0" step="0.01" required>
                                                <div id="engancheError" class="error"></div>
                                            </div>

                                            <div class="form-group col-sm-3">
                                                <label for="plazosEnganche" class="col-form-label">Plazos Enganche (meses):</label>
                                                <input type="number" id="plazosEnganche" name="plazosEnganche" class="form-control" min="0" value="0">
                                            </div>

                                            <div class="form-group col-sm-3">
                                                <label for="plazosSinInteres" class="col-form-label">Plazos sin Interés (meses):</label>
                                                <input type="number" id="plazosSinInteres" name="plazosSinInteres" class="form-control" min="0" value="0">
                                            </div>

                                            <div class="form-group col-sm-3">
                                                <label class="col-form-label" for="plazosConInteres">Plazos con Interés (meses):</label>
                                                <input type="number" id="plazosConInteres" name="plazosConInteres" class="form-control" min="0" value="0">
                                            </div>
                                        </div>

                                        <div class="row justify-content-center">
                                            <div class="col-sm-6 d-block">
                                                <button class="btn bg-green w-100 py-2" type="button" id="btnCalcular">
                                                    <i class="fas fa-calculator mr-2"></i> CALCULAR CORRIDA FINANCIERA
                                                </button>
                                            </div>
                                        </div>


                                        <div id="results" class="col-sm-12">
                                            <h2>Plan de Pagos</h2>
                                            <div id="paymentTable"></div>

                                            <div class="totals">

                                                <div class="row justify-content-center">
                                                    <div class="form-group col-sm-4">
                                                        <label class="col-form-label" for="totalCapital">Total Capital:</label>
                                                        <input type="text" id="totalCapital" name="totalCapital" class="form-control" min="0" value="0">
                                                    </div>
                                                    <div class="form-group col-sm-4">
                                                        <label class="col-form-label" for="totalCapital">Total Intereses:</label>
                                                        <input type="text" id="totalIntereses" name="totalIntereses" class="form-control" min="0" value="0">
                                                    </div>
                                                    <div class="form-group col-sm-4">
                                                        <label class="col-form-label" for="totalPagar">Total a Pagar:</label>
                                                        <input type="text" id="totalPagar" name="totalPagar" class="form-control" min="0" value="0">
                                                    </div>
                                                    <div class="form-group col-sm-3" style="display: none;">
                                                        <label class="col-form-label" for="cat">CAT:</label>
                                                        <input type="number" id="cat" name="cat" class="form-control" min="0" value="0">
                                                    </div>
                                                </div>


                                            </div>
                                        </div>



                                    </form>


                                </div>
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
    <section>
        <div class="container-fluid">
            <!-- Modal de Búsqueda de Lotes -->
            <div class="modal fade" id="modalLote" tabindex="-1" role="dialog" aria-labelledby="modalLoteLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-gradient-green">
                            <h5 class="modal-title" id="modalLoteLabel">BUSCAR LOTE</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Filtros -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bproyecto">Proyecto:</label>
                                        <select id="bproyecto" name="bproyecto" class="form-control">
                                            <option value="">-- Seleccione Proyecto --</option>
                                            <?php foreach ($proyectos as $proyecto): ?>
                                                <option value="<?= $proyecto['id_proy'] ?>"><?= htmlspecialchars($proyecto['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bmanzana">Manzana:</label>
                                        <select id="bmanzana" name="bmanzana" class="form-control" disabled>
                                            <option value="">-- Seleccione Manzana --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de Lotes -->
                            <div class="table-responsive">
                                <table id="tablaLote" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
                                    <thead class="text-center bg-gradient-green">
                                        <tr>
                                            <th>ID</th>
                                            <th>CLAVE</th>
                                            <th>SUPERFICIE</th>
                                            <th>PRECIO M²</th>
                                            <th>VALOR TOTAL</th>
                                            <th>ESTATUS</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>




    <!-- /.content -->
</div>





<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cotizador.js?v=<?php echo (rand()); ?>"></script>
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
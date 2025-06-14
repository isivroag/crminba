<?php
$pagina = "presupuesto";

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
$descuento = 0;
$valorop = 0;
$descuentopor = 0;
$fecha = date("Y-m-d");
$fechacot = date("Y-m-d");

if(isset($_GET['folio']) && !empty($_GET['folio'])) {
    $folio = $_GET['folio'];
    $mostrar = 1;
 } else {
   
    $mostrar = 0;
}


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

    $consulta = "SELECT id_lote, clave_lote, superficie, preciom, valortotal, status,superficie,tipo,frente,fondo FROM vlote 
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
    $frente = $lote['frente'];
    $fondo = $lote['fondo'];
    $superficie = $lote['superficie'];
    $preciom = $lote['preciom'];
    $tipolote = $lote['tipo'];
    $id_lote = $lote['id_lote'];
    
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
                        <div class="row justify-content-center mt-4">
                            <div class="col-sm-3">
                                <button class="btn btn-danger w-100 py-2" type="button" id="btnNuevo">
                                    <i class="fas fa-file-alt mr-2"></i> NUEVA COTIZACIÓN
                                </button>
                            </div>
                            <div class="col-sm-3">
                                <button class="btn bg-green w-100 py-2" type="button" id="btnGuardar" <?= $mostrar==0 ? '' : 'style="display:none;"' ?>>
                                    <i class="fas fa-save mr-2"></i> GUARDAR COTIZACIÓN 
                                </button>
                            </div>
                            <div class="col-sm-3">
                                <button class="btn btn-info w-100 py-2" type="button" id="btnImprimir">
                                    <i class="fas fa-print mr-2"></i> IMPRIMIR 
                                </button>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-sm-12">
                                <div class="container justify-content-center">
                                    <h1>Cotizador de Inmuebles</h1>

                                    <form id="creditForm" class=" p-3">
                                        <div class="row">


                                            <div class="col-sm-2 form-group form-group-sm">
                                                <label for="folio" class="col-form-label">Folio:</label>
                                                <input type="text" id="folio" name="folio" class="form-control form-control-sm" value="<?= isset($folio) ? $folio : '' ?>" readonly>
                                            </div>
                                            <div class="col-sm-6 form-group form-group-sm">
                                                <label for="nombre_prospecto" class="col-form-label">Prospecto:</label>
                                                <div class="input-group">
                                                    <input type="hidden" id="id_prospecto" name="id_prospecto" class="form-control">
                                                    <input type="text" id="nombre_prospecto" name="nombre_prospecto" class="form-control form-control-sm" placeholder="Buscar Prospecto">
                                                    <span class="input-group-append">
                                                        <button class="btn btn-success btn-sm" type="button" id="btnBuscarProspecto"><i class="fas fa-search"></i></button>
                                                    </span>
                                                </div>

                                            </div>

                                            <div class=" col-sm-2 form-group ">
                                                <label for="fechacot" class="col-form-label">Fecha:</label>
                                                <input type="date" id="fechacot" name="fechacot" class="form-control form-control-sm" value="<?= $fechacot ?>" required>

                                            </div>
                                            <div class="form-group col-sm-2" class="col-form-label">
                                                <label for="tasaInteresAnual" class="col-form-label">T.I. Anual(%):</label>
                                                <input type="number" id="tasaInteresAnual" name="tasaInteresAnual" class="form-control form-control-sm" min="0" step="0.01" value="17.00">

                                            </div>
                                        </div>


                                        <div class="container-fluid p-0 m-0">
                                            <h4><strong>INFORMACIÓN DEL INMUEBLE</strong></h4>
                                            <div class="row justify-content-center">
                                                <div class="col-sm-4 form-group">
                                                    <label class="col-form-label" for="proyecto">Proyecto:</label>
                                                    <input type="hidden" id="id_proyecto" name="id_proyecto" class="form-control form-control-sm" value="<?= $id_proyecto ?>" disabled>
                                                    <input type="text" id="proyecto" name="proyecto" class="form-control form-control-sm" value="<?= $proyecto ?>" disabled>
                                                </div>

                                                <div class="col-sm-4 form-group">
                                                    <label class="col-form-label" for="manzana">Manzana:</label>
                                                    <input type="hidden" id="id_manzana" name="id_manzana" class="form-control " value="<?= $id_manzana ?>" disabled>
                                                    <input type="text" id="manzana" name="manzana" class="form-control form-control-sm" value="<?= $manzana ?>" disabled>
                                                </div>

                                                <div class="col-sm-4 form-group">

                                                    <label class="col-form-label" for="lote">Lote:</label>
                                                    <div class="input-group ">
                                                        <input type="hidden" id="id_lote" name="id_lote" class="form-control" value="<?= $id_lote ?>" disabled>
                                                        <input type="text" id="lote" name="lote" class="form-control form-control-sm" value="<?= $clave_lote ?>" disabled>
                                                        <?php if ($mapa == 0) { ?>
                                                            <span class="input-group-append">
                                                                <button class="btn  btn-primary btn-sm" type="button" id="btnBuscar"><i class="fas fa-search "></i></button>
                                                            </span>

                                                        <?php } ?>

                                                    </div>

                                                </div>


                                            </div>
                                            <div class="row justify-content-center ">
                                                <div class="col-sm-2">
                                                    <label for="frente" class="col-form-label">Frente:</label>
                                                    <input type="text" id="frente" name="frente" class="form-control form-control-sm" value="<?= isset($frente) ? $frente : '' ?>" disabled>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="fondo" class="col-form-label">Fondo:</label>
                                                    <input type="text" id="fondo" name="fondo" class="form-control form-control-sm" value="<?= isset($fondo) ? $fondo : '' ?>" disabled>
                                                </div>
                                                <div class="col-sm-2 form-group">
                                                    <label for="superficie" class="col-form-label">Superficie:</label>
                                                    <input type="text" id="superficie" name="superficie" class="form-control form-control-sm" value="<?= isset($superficie) ? $superficie : '' ?>" disabled>

                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="tipolote" class="col-form-label">Tipo de Lote:</label>
                                                    <input type="text" id="tipolote" name="tipolote" class="form-control form-control-sm" value="<?= isset($tipolote) ? $tipolote : '' ?>" disabled>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="preciom" class="col-form-label">Precio m²:</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="preciom" name="preciom" class="form-control text-right form-control-sm" min="1" step="0.01" value="<?= isset($preciom) ? $preciom : '' ?>" disabled>
                                                    </div>
                                                </div>

                                                <div class="col-sm-2">
                                                    <label for="valortotal" class="col-form-label">Valor Lote:</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="valortotal" name="valortotal" class="form-control text-right form-control-sm" min="1" step="0.01" value="<?= $valortotal ?>" disabled>
                                                    </div>
                                                </div>



                                            </div>
                                        </div>

                                        <div class="container-fluid p-0 m-0">
                                            <h4><strong>INFORMACIÓN DEL PRESUPUESTO</strong></h4>
                                            <div class="row justify-content-center">


                                                <div class=" col-sm-2 form-group ">
                                                    <label for="fechaInicio" class="col-form-label">Inicio:</label>
                                                    <input type="date" id="fechaInicio" name="fechaInicio" class="form-control form-control-sm" required value="<?= $fecha ?>">

                                                </div>


                                                <div class="form-group col-sm-3">
                                                    <label for="montoTotal" class="col-form-label text-right">Importe:</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="montoTotal" name="montoTotal" class="form-control text-right form-control-sm" min="1" step="0.01" required value="<?= $valortotal ?>" disabled>
                                                    </div>

                                                </div>
                                                <div class="form-group col-sm-1">
                                                    <label for="descuentopor" class="col-form-label text-right">% Desc:</label>
                                                    <input type="number" id="descuentopor" name="descuentopor" class="form-control text-right form-control-sm" min="1" step="0.01" required value="<?= $descuentopor ?>">
                                                </div>
                                                <div class="form-group col-sm-3">
                                                    <label for="descuento" class="col-form-label text-right">Descuento:</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="descuento" name="descuento" class="form-control text-right form-control-sm" min="1" step="0.01" required value="<?= $descuento ?>">
                                                    </div>

                                                </div>
                                                
                                                <div class="form-group col-sm-3">
                                                    <label for="valorop" class="col-form-label text-right">Importe Total:</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="valorop" name="valorop" class="form-control text-right form-control-sm" min="1" step="0.01" required value="<?= $valorop ?>" disabled>
                                                    </div>

                                                </div>


                                                <div class="form-group col-sm-1">
                                                    <label for="enganchepor" class="col-form-label text-right">% Eng:</label>
                                                    <input type="number" id="enganchepor" name="enganchepor" class="form-control text-right form-control-sm" min="1" step="0.01" required value="<?= $enganchepor ?>">
                                                </div>

                                                <div class="form-group col-sm-3">
                                                    <label for="montoEnganche" class="col-form-label">Monto de Enganche:</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="montoEnganche" name="montoEnganche" class="form-control text-right form-control-sm" min="0" step="0.01" required>
                                                    </div>

                                                    <div id="engancheError" class="error"></div>
                                                </div>

                                                <div class="form-group col-sm-2">
                                                    <label for="plazosEnganche" class="col-form-label">MENG:</label>
                                                    <input type="number" id="plazosEnganche" name="plazosEnganche" class="form-control form-control-sm" min="0" value="0">
                                                </div>
                                              

                                                <div class="form-group col-sm-2">
                                                    <label for="plazosSinInteres" class="col-form-label">MSI:</label>
                                                    <input type="number" id="plazosSinInteres" name="plazosSinInteres" class="form-control form-control-sm" min="0" value="0">
                                                </div>

                                                <div class="form-group col-sm-2">
                                                    <label class="col-form-label" for="plazosConInteres">MCI:</label>
                                                    <input type="number" id="plazosConInteres" name="plazosConInteres" class="form-control form-control-sm" min="0" value="0">
                                                </div>
                                            </div>

                                            <div class="row justify-content-center">
                                                <div class="col-sm-6 d-block">
                                                    <button class="btn bg-green w-100 py-2" type="button" id="btnCalcular">
                                                        <i class="fas fa-calculator mr-2"></i> CALCULAR CORRIDA FINANCIERA
                                                    </button>
                                                </div>
                                            </div>

                                        </div>




                                        <div id="results" class="col-sm-12">
                                            <h2>Plan de Pagos</h2>
                                            <div id="paymentTable"></div>

                                            <div class="totals">

                                                <div class="row justify-content-center">
                                                    <div class="form-group col-sm-4">
                                                        <label class="col-form-label" for="totalCapital">Total Capital:</label>
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fas fa-dollar-sign"></i>
                                                                </span>
                                                            </div>
                                                            <input type="text" id="totalCapital" name="totalCapital" class="form-control form-control-sm text-right" min="0" value="0">
                                                        </div>


                                                    </div>
                                                    <div class="form-group col-sm-4">
                                                        <label class="col-form-label" for="totalIntereses">Total Intereses:</label>
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fas fa-dollar-sign"></i>
                                                                </span>
                                                            </div>
                                                            <input type="text" id="totalIntereses" name="totalIntereses" class="form-control form-control-sm text-right" min="0" value="0">
                                                        </div>

                                                    </div>
                                                    <div class="form-group col-sm-4">
                                                        <label class="col-form-label" for="totalPagar">Total a Pagar:</label>
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fas fa-dollar-sign"></i>
                                                                </span>
                                                            </div>
                                                            <input type="text" id="totalPagar" name="totalPagar" class="form-control form-control-sm text-right" min="0" value="0">
                                                        </div>

                                                    </div>
                                                    <div class="form-group col-sm-3" style="display: none;">
                                                        <label class="col-form-label" for="cat">CAT:</label>
                                                        <input type="number" id="cat" name="cat" class="form-control form-control-sm" min="0" value="0">
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
                                            <th>SUPERFICIE M²</th>
                                            <th>PRECIO M²</th>
                                            <th>VALOR TOTAL</th>
                                            <th>ESTATUS</th>
                                            <th>FRENTE M</th>
                                            <th>FONDO M</th>
                                            <th>TIPO</th>
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


    <section>
        <div class="container-fluid">
            <!-- Modal de Búsqueda de Prospectos -->
            <div class="modal fade" id="modalProspecto" tabindex="-1" role="dialog" aria-labelledby="modalProspectoLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-gradient-green">
                            <h5 class="modal-title" id="modalProspectoLabel">BUSCAR PROSPECTO</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table id="tablaProspecto" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
                                    <thead class="text-center bg-gradient-green">
                                        <tr>
                                            <th>ID</th>
                                            <th>NOMBRE</th>
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
<script src="fjs/cot2.js?v=<?php echo (rand()); ?>"></script>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
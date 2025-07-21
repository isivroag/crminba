<?php
$pagina = "apartado";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$folio = "";
$fecha = date("Y-m-d");

if(isset($_GET['folio']) && !empty($_GET['folio'])) {
    $folio = $_GET['folio'];
}

// Consulta para obtener proyectos
$consulta = "SELECT id_proy,nombre FROM proyecto WHERE edo_proy=1 and vendible=1 ORDER BY id_proy";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$proyectos = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener colaboradores activos
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
                <h1 class="card-title mx-auto">APARTADO DE LOTES</h1>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="container-fluid">
                        <div class="row justify-content-center mt-4">
                            <div class="col-sm-3">
                                <button class="btn btn-danger w-100 py-2" type="button" id="btnNuevo">
                                    <i class="fas fa-file-alt mr-2"></i> NUEVO APARTADO
                                </button>
                            </div>
                            <div class="col-sm-3">
                                <button class="btn bg-green w-100 py-2" type="button" id="btnGuardar">
                                    <i class="fas fa-save mr-2"></i> GUARDAR APARTADO
                                </button>
                            </div>
                        </div>
                        
                        <div class="row justify-content-center">
                            <div class="col-sm-12">
                                <div class="container justify-content-center">
                                    <h1>Apartado de Inmuebles</h1>

                                    <form id="apartadoForm" class="p-3">
                                        <div class="row">
                                            <div class="col-sm-2 form-group form-group-sm">
                                                <label for="folio" class="col-form-label">FOLIO:</label>
                                                <input type="text" id="folio" name="folio" class="form-control form-control-sm" value="<?= $folio ?>" readonly>
                                            </div>
                                            
                                            <div class="col-sm-4 form-group form-group-sm">
                                                <label for="nombre_prospecto" class="col-form-label">CLIENTE:</label>
                                                <div class="input-group">
                                                    <input type="hidden" id="id_prospecto" name="id_prospecto" class="form-control">
                                                    <input type="text" id="nombre_prospecto" name="nombre_prospecto" class="form-control form-control-sm" placeholder="BUSCAR CLIENTE..." readonly>
                                                    <span class="input-group-append">
                                                        <button class="btn btn-success btn-sm" type="button" id="btnBuscarProspecto"><i class="fas fa-search"></i></button>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-sm-2 form-group">
                                                <label for="fecha_apartado" class="col-form-label">FECHA:</label>
                                                <input type="date" id="fecha_apartado" name="fecha_apartado" class="form-control form-control-sm" value="<?= $fecha ?>" required>
                                            </div>

                                            <div class="col-sm-4 form-group">
                                                <label for="col_asignado" class="col-form-label">COLABORADOR:</label>
                                                <select class="form-control form-control-sm selectpicker" name="col_asignado" id="col_asignado" data-live-search="true" title="SELECCIONA COLABORADOR" required>
                                                    <?php foreach ($colaboradores as $colab): ?>
                                                        <option value="<?php echo $colab['id_col'] ?>"><?php echo $colab['nombre'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="container-fluid p-0 m-0">
                                            <h4><strong>INFORMACIÓN DEL INMUEBLE</strong></h4>
                                            <div class="row justify-content-center">
                                                <div class="col-sm-4 form-group">
                                                    <label class="col-form-label" for="proyecto">PROYECTO:</label>
                                                    <input type="hidden" id="id_proyecto" name="id_proyecto" class="form-control form-control-sm" disabled>
                                                    <input type="text" id="proyecto" name="proyecto" class="form-control form-control-sm" disabled>
                                                </div>

                                                <div class="col-sm-4 form-group">
                                                    <label class="col-form-label" for="manzana">MANZANA:</label>
                                                    <input type="hidden" id="id_manzana" name="id_manzana" class="form-control" disabled>
                                                    <input type="text" id="manzana" name="manzana" class="form-control form-control-sm" disabled>
                                                </div>

                                                <div class="col-sm-4 form-group">
                                                    <label class="col-form-label" for="lote">LOTE:</label>
                                                    <div class="input-group">
                                                        <input type="hidden" id="id_lote" name="id_lote" class="form-control" disabled>
                                                        <input type="text" id="lote" name="lote" class="form-control form-control-sm" disabled>
                                                        <span class="input-group-append">
                                                            <button class="btn btn-primary btn-sm" type="button" id="btnBuscarLote"><i class="fas fa-search"></i></button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row justify-content-center">
                                                <div class="col-sm-2">
                                                    <label for="superficie" class="col-form-label">SUPERFICIE:</label>
                                                    <input type="text" id="superficie" name="superficie" class="form-control form-control-sm" disabled>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="preciom" class="col-form-label">PRECIO M²:</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="preciom" name="preciom" class="form-control text-right form-control-sm" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="valortotal" class="col-form-label">VALOR TOTAL:</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="valortotal" name="valortotal" class="form-control text-right form-control-sm" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label for="importe_apartado" class="col-form-label">IMPORTE APARTADO:</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="importe_apartado" name="importe_apartado" class="form-control text-right form-control-sm" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="container-fluid p-0 m-0 mt-4">
                                            <h4><strong>OBSERVACIONES</strong></h4>
                                            <div class="row justify-content-center">
                                                <div class="col-sm-12 form-group">
                                                    <textarea id="observaciones" name="observaciones" class="form-control" rows="4" placeholder="OBSERVACIONES ADICIONALES..."></textarea>
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
        </div>
    </section>

    <!-- Modal de Búsqueda de Lotes -->
    <section>
        <div class="container-fluid">
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
                                <table id="tablaLote" class="table table-sm table-striped table-bordered table-hover table-condensed" style="width:100%;font-size: 0.9rem;">
                                    <thead class="text-center bg-gradient-green">
                                        <tr>
                                            <th>ID</th>
                                            <th>LOTE</th>
                                            <th>SUPERFICIE M²</th>
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

    <!-- Modal de Búsqueda de Prospectos -->
    <section>
        <div class="container-fluid">
            <div class="modal fade" id="modalProspecto" tabindex="-1" role="dialog" aria-labelledby="modalProspectoLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-gradient-green">
                            <h5 class="modal-title" id="modalProspectoLabel">BUSCAR CLIENTE</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table id="tablaProspecto" class="table table-sm table-striped table-bordered table-hover tabla-condensed" style="width:100%;font-size: 0.9rem;">
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
</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/apartado.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
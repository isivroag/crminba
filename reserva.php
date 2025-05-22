<?php
$pagina = "seguimiento";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();
$select = "SELECT count(*) as numero,sum(superficie) as ssuperficie,sum(valortotal) as svalortotal, status FROM lote WHERE id_proy=:id_proy GROUP BY status";

$resultado = $conexion->prepare($select);
$resultado->bindParam(':id_proy', $_GET['id_proy'], PDO::PARAM_STR);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Inicializar variables
$disponibles = 0;
$vendidos = 0;
$apartados = 0;
$total = 0;
$mdisponibles = 0;
$mvendidos = 0;
$mapartados = 0;
$vdisponibles = 0;
$vvendidos = 0;
$vapartados = 0;
$totalm = 0;
$totalv = 0;
// Asignar valores según status
foreach ($data as $row) {
    switch ($row['status']) {
        case 'DISPONIBLE':
            $disponibles = $row['numero'];
            $total += $row['numero'];
            $mdisponibles = $row['ssuperficie'];
            $vdisponibles = $row['svalortotal'];
            $totalm += $row['ssuperficie'];
            $totalv += $row['svalortotal'];

            break;
        case 'VENDIDO':
            $vendidos = $row['numero'];
            $total += $row['numero'];
            $mvendidos = $row['ssuperficie'];
            $vvendidos = $row['svalortotal'];
            $totalm += $row['ssuperficie'];
            $totalv += $row['svalortotal'];
            break;
        case 'APARTADO':
            $apartados = $row['numero'];
            $total += $row['numero'];
            $mapartados = $row['ssuperficie'];
            $vapartados = $row['svalortotal'];
            $totalm += $row['ssuperficie'];
            $totalv += $row['svalortotal'];
            break;
    }
}






?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<style>
    html,
    body {
        margin: 0;
        padding: 0;

        width: 100vw;
        height: 100vh;
    }

    .starchecked {
        color: rgba(255, 195, 0, 100)
    }

    .multi-line {
        white-space: normal;
        width: 250px;
    }

    .bootstrap-select .dropdown-menu li.selected a {
        background-color: #d4edda !important;
        /* verde claro */
        color: #212529 !important;
        /* color de texto Bootstrap por defecto */
    }

    .bootstrap-select .dropdown-menu li.selected a .text-success {
        color: #28a745 !important;
        /* verde Bootstrap */
    }

    .bootstrap-select .dropdown-menu li.selected a .text-primary {
        color: #007bff !important;
        /* azul Bootstrap */
    }

    .contenedor {
        width: 78vw;
        height: 78vh;
        overflow: hidden;
        /* evita que crezca al hacer pan/zoom */
        position: relative;
        overflow-x: 0;
        /* Permite hacer scroll horizontal si es necesario */
        text-align: center;
    }

    #map-container {
        height: calc(100vh - 130px);
        /* Ajusta 130px según el alto de tu header/nav */
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #fff;
        overflow: hidden;
    }

    #svg-overlay {
        width: 100%;
        height: 100%;
        max-width: 100%;
        max-height: 100%;
        display: block;
    }

    @media (max-width: 768px) {
        #acotaciones {
            font-size: 0.75rem;
            width: 90% !important;
            max-height: 50vh;
            overflow-y: auto;
        }
    }

    
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->


    <!-- Main content -->
    <section class="content">

        <div class="container-fluid">
            <div class="card card-widget">
                <div class="card-header bg-green">
                    <h4 class="card-title text-white">MAPA INTERACTIVO</h4>
                </div>
                <div class="card-body">
                    <button id="btnAcotaciones" class="btn btn-sm btn-dark position-absolute top-0 start-0 m-2" style="z-index: 1050;">
                                <i class="fas fa-bars"></i> Acotaciones
                            </button>

                    <div class="position-relative">
                        <!-- Acotaciones flotantes con más ancho -->
                        <div id="acotaciones" class="position-absolute top-0 start-0 bg-white border p-2 m-2 shadow-sm d-none" style="z-index: 1040; width: 420px; max-height: 90vh; overflow-y: auto;">
                            <h6 class="mb-2">Acotaciones</h6>
                            <div class="w-100">
                                <?php
                                $datos = [
                                    ['color' => '#4bae00', 'label' => 'Disponible', 'cant' => $disponibles, 'm2' => $mdisponibles, 'valor' => $vdisponibles],
                                    ['color' => '#f37a1b', 'label' => 'Apartado',   'cant' => $apartados,   'm2' => $mapartados,   'valor' => $vapartados],
                                    ['color' => '#c20000', 'label' => 'Vendido',    'cant' => $vendidos,    'm2' => $mvendidos,    'valor' => $vvendidos],
                                    ['color' => '#000000', 'label' => 'Total',      'cant' => $total,       'm2' => $totalm,       'valor' => $totalv],
                                ];
                                foreach ($datos as $item): ?>
                                    <div class="d-flex align-items-center small mb-1">
                                        <div class="d-flex align-items-center" style="width: 80px;">
                                            <div style="width: 10px; height: 10px; background-color: <?= $item['color'] ?>;" class="rounded mr-1"></div>
                                            <span><?= $item['label'] ?></span>
                                        </div>
                                        <div class="text-right font-weight-bold" style="width: 60px;"><?= number_format($item['cant'], 0) ?></div>
                                        <div class="text-right font-weight-bold" style="width: 110px;"><?= number_format($item['m2'], 2) ?> m²</div>
                                        <div class="text-right font-weight-bold" style="width: 130px;"><?= "$" . number_format($item['valor'], 2) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Mapa interactivo -->
                        <div class="contenedor">
                            <div id="div_carga">
                                <img id="cargador" src="img/loader.gif" />
                                <span class="" id="textoc"><strong>Cargando...</strong></span>
                            </div>
                            
                            <svg id="svg-overlay" viewBox="0 0 1000 1000" preserveAspectRatio="xMidYMid meet" style="width: 100%; height: 100%;">
                                <!-- SVG content here -->
                            </svg>
                        </div>
                    </div>


                </div>

            </div>
        </div>

    </section>
    <section>
        <div class="modal fade" id="loteModal" tabindex="-1" aria-labelledby="loteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-green">
                        <h5 class="modal-title" id="modalCRUDLabel">INFORMACION</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="modalManzana">Manzana:</label>
                                    <input type="hidden" class="form-control" id="idproy" readonly>
                                    <input type="hidden" class="form-control" id="idman" readonly>
                                    <input type="hidden" class="form-control" id="idlote" readonly>
                                    <input type="text" class="form-control" id="modalManzana" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="modalLote">Lote:</label>
                                    <input type="text" class="form-control" id="modalLote" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="modalSuperficie">Superficie:</label>
                                    <input type="text" class="form-control" id="modalSuperficie" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="modalPreciom">Precio m²:</label>
                                    <input type="text" class="form-control" id="modalPreciom" readonly>
                                </div>
                            </div>

                        </div>
                        <div class="row justify-content-center">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="modalValor">Valor:</label>
                                    <input type="text" class="form-control" id="modalValor" readonly>
                                </div>
                            </div>

                        </div>



                        <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
                        <!-- Imagen del path -->
                        <div id="modalPathImage"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="cotizador" class="btn btn-primary" data-bs-dismiss="modal">Cotizador</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </section>




    <!-- /.content -->
</div>






<?php include_once 'templates/footer.php'; ?>
<script src="fjs/reserva.js?v=<?php echo (rand()); ?>"></script>
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
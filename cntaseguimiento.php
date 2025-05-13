<?php
$pagina = "cntaseguimiento";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";


?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<style>
    .timeline {
        list-style: none;
        padding: 0;
        position: relative;
    }

    .timeline li {
        padding: 10px 20px;
        border-left: 3px solid #153510;
        position: relative;
        margin-bottom: 10px;
        background: #f2f7f0;
        border-radius: 4px;
    }

    .timeline-time {
        font-weight: bold;
        color: #153510;
    }

    .timeline-body {
        padding-left: 10px;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="card">
            <div class="card-header  bg-green text-light">
                <h1 class="card-title mx-auto">PROSPECTOS</h1>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <button id="btnNuevo" type="button" class="btn  bg-green btn-ms" data-toggle="modal">
                            <i class="fas fa-plus-square text-light"></i><span class="text-light"> Nuevo</span>
                        </button>
                    </div>
                </div>
                <br>
                <div class="container-fluid">
                    <div class="row">
                        <div class="container mt-4">
                            <h4>Seguimiento de Prospectos</h4>

                            <!-- Buscar prospecto -->
                            <div class="form-group">
                                <label for="buscar_prospecto">Buscar prospecto:</label>
                                <input type="text" id="buscar_prospecto" class="form-control" placeholder="Nombre, correo, teléfono...">
                            </div>

                            <!-- Tabla de resultados -->
                            <table id="tablaBusqueda" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Seleccionar</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <!-- Timeline de seguimientos -->
                            <div id="timelineSeguimientos" class="mt-5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>




</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntaseguimiento.js?v=<?php echo (rand()); ?>"></script>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-es_ES.min.js"></script>
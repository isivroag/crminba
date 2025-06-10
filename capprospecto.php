<?php
$pagina = "captura_prospecto";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Consulta para obtener colaboradores activos (para el select)
$consulta_colab = "SELECT * FROM colaborador WHERE edo_col = 1 ORDER BY id_col";
$resultado_colab = $conexion->prepare($consulta_colab);
$resultado_colab->execute();
$colaboradores = $resultado_colab->fetchAll(PDO::FETCH_ASSOC);

$message = "";
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<style>
    .content-wrapper {
        background-color: #f4f6f9;
    }
    .card-form {
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 10px;
    }
    .form-header {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
    .btn-submit {
        width: 100%;
        padding: 10px;
        font-size: 18px;
    }
    .form-control, .selectpicker {
        border-radius: 5px;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-form">
                        <div class="card-header bg-green text-white form-header">
                            <h3 class="card-title text-center">CAPTURA DE PROSPECTO</h3>
                        </div>
                        <div class="card-body">
                            <form id="formProspecto" class="row g-3 p-3" method="POST">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="nombre" class="form-label">* Nombre completo:</label>
                                        <input type="text" class="form-control form-control-sm" name="nombre" id="nombre" autocomplete="off" placeholder="Nombre completo del prospecto" required>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="telefono" class="form-label">* Teléfono:</label>
                                        <input type="text" class="form-control form-control-sm" name="telefono" id="telefono" autocomplete="off" placeholder="Número de teléfono"  maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="correo" class="form-label">* Correo electrónico:</label>
                                        <input type="email" class="form-control form-control-sm" name="correo" id="correo" autocomplete="off" placeholder="Correo electrónico" >
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="origen" class="form-label">* Origen:</label>
                                        <select id="origen" name="origen" class="selectpicker form-control form-control-sm" data-live-search="false" title="Seleccione el origen..." required>
                                            <option value="facebook" data-icon="fab fa-facebook text-primary">Facebook</option>
                                            <option value="instagram" data-icon="fab fa-instagram text-danger">Instagram</option>
                                            <option value="web" data-icon="fas fa-globe text-info">Web</option>
                                            <option value="whatsapp" data-icon="fab fa-whatsapp text-success">WhatsApp</option>
                                            <option value="llamada" data-icon="fas fa-phone text-dark">Llamada</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" style="display: none;">
                                        <label for="col_asignado" class="form-label">* Asignado a:</label>
                                        <select class="form-control form-control-sm selectpicker" name="col_asignado" id="col_asignado" data-live-search="true" title="Selecciona colaborador" required>
                                            <?php foreach ($colaboradores as $colab): ?>
                                                <option value="<?php echo $colab['id_col'] ?>"><?php echo $colab['nombre'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" id="btnGuardar" class="btn bg-green btn-submit">
                                        <i class="fas fa-save"></i> GUARDAR PROSPECTO
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include_once 'templates/footer.php'; ?>
<script src="fjs/capprospectos.js?v=<?php echo (rand()); ?>"></script>

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

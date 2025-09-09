<?php
$pagina = "venta";
include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";
include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$folio_venta = $_GET['folio'] ?? "";
$id_pres = $_GET['id_pres'] ?? "";
$modo_consulta = !empty($folio_venta) ? true : false;

$sql= "SELECT * FROM colaborador where edo_col=1";
$result= $conexion->query($sql);
$colaboradores= $result->fetchAll(PDO::FETCH_ASSOC);


?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<div class="content-wrapper">
  <section class="content">
    <div class="card">
      <div class="card-header bg-green text-light">
        <h1 class="card-title mx-auto">REGISTRO DE VENTA</h1>
      </div>
      <div class="card-body">
        <div class="container-fluid">
          <div class="row justify-content-center mt-4">
            <?php if (!$modo_consulta) { ?>
              <div class="col-sm-3">
                <button class="btn btn-info w-100 py-2" type="button" id="btnBuscarPresupuesto">
                  <i class="fas fa-search mr-2"></i> BUSCAR PRESUPUESTO
                </button>
              </div>
              <div class="col-sm-3">
                <button class="btn bg-green w-100 py-2" type="button" id="btnGuardarVenta" style="display:none;">
                  <i class="fas fa-save mr-2"></i> GUARDAR VENTA
                </button>
              </div>
            <?php } ?>
            <div class="col-sm-3">
              <button class="btn btn-primary w-100 py-2" type="button" id="btnConsultarVenta">
                <i class="fas fa-search mr-2"></i> CONSULTAR VENTA
              </button>
            </div>
          </div>
          <form id="formVenta" class="mt-4">
            <div class="row justify-content-center">
              <div class="col-sm-10">
                <div class="row justify-content-center">
                  <div class="col-sm-2 form-group form-group-sm">
                    <label for="folio_venta" class="col-form-label">Folio Vta:</label>
                    <input type="text" id="folio_venta" name="folio_venta" class="form-control form-control-sm" value="<?= $folio_venta ?>" <?= $modo_consulta ? 'readonly' : '' ?>>
                  </div>
                  <div class="col-sm-1 form-group form-group-sm">
                    <label for="id_pres" class="col-form-label">Folio Pres.:</label>
                    <input type="text" id="id_pres" name="id_pres" class="form-control form-control-sm" value="<?= $id_pres ?>" <?= $modo_consulta ? 'readonly' : '' ?>>
                  </div>

                  <div class="col-sm-2 form-group form-group-sm">
                    <label for="fecha_venta" class="col-form-label">Fecha Vta:</label>
                    <input type="date" id="fecha_venta" name="fecha_venta" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" <?= $modo_consulta ? 'readonly' : '' ?>>
                  </div>

                  <div class="col-sm-3 form-group form-group-sm">
                    <label for="nombre_vendedor" class="col-form-label">Vendedor:</label>
                    <div class="input-group">
                      <input type="hidden" id="id_vendedor" name="id_vendedor" class="form-control">
                      <input type="text" id="nombre_vendedor" name="nombre_vendedor" class="form-control form-control-sm" placeholder="Buscar Vendedor" <?= $modo_consulta ? 'readonly' : '' ?>>
                      <span class="input-group-append">
                        <button class="btn btn-primary btn-sm" type="button" id="btnBuscarVendedor"><i class="fas fa-search"></i></button>
                      </span>
                    </div>

                  </div>

                  <div class="col-sm-2 form-group form-group-sm">
                    <label for="tasaInteresAnual" class="col-form-label">T.I. Anual(%):</label>
                    <input type="number" id="tasaInteresAnual" name="tasaInteresAnual" class="form-control form-control-sm" min="0" step="0.01" value="17.00" readonly>
                  </div>
                  <div class="col-sm-2 form-group form-group-sm">
                    <label for="tipo" class="col-form-label">Tipo Vta:</label>
                    <select name="tipo" id="tipo" class="form-control form-control-sm" <?= $modo_consulta ? 'readonly' : '' ?>>
                      <option value="01">Contado</option>
                      <option value="02">Credito</option>
                    </select>
                  </div>

                </div>
                
                <div class="row justify-content-center">
                  <div class="col-sm-12">
                    <div class="form-group form-group-sm">
                      <label for="nombre_clie" class="col-form-label ">Cliente:</label>
                      <input type="hidden" id="id_clie" name="id_clie" class="form-control form-control-sm" disabled>
                      <input type="text" id="nombre_clie" name="nombre_clie" class="form-control form-control-sm" disabled>
                    </div>
                  </div>
                </div>

                
                  <h4><strong>INFORMACIÓN DEL INMUEBLE</strong></h4>
                  <div class="row justify-content-center">
                    <div class="col-sm-4 form-group">
                      <label class="col-form-label" for="proyecto">Proyecto:</label>
                      <input type="hidden" id="id_proyecto" name="id_proyecto" class="form-control form-control-sm" disabled>
                      <input type="text" id="proyecto" name="proyecto" class="form-control form-control-sm" disabled <?= $modo_consulta ? 'readonly' : '' ?>>
                    </div>

                    <div class="col-sm-4 form-group">
                      <label class="col-form-label" for="manzana">Manzana:</label>
                      <input type="hidden" id="id_manzana" name="id_manzana" class="form-control " disabled>
                      <input type="text" id="manzana" name="manzana" class="form-control form-control-sm" disabled>
                    </div>

                    <div class="col-sm-4 form-group">

                      <label class="col-form-label" for="lote">Lote:</label>
                      <div class="input-group ">
                        <input type="hidden" id="id_lote" name="id_lote" class="form-control" disabled>
                        <input type="text" id="lote" name="lote" class="form-control form-control-sm" disabled>

                      </div>

                    </div>


                  </div>
                  
                  <div class="row justify-content-center ">
                    <div class="col-sm-2">
                      <label for="frente" class="col-form-label">Frente:</label>
                      <input type="text" id="frente" name="frente" class="form-control form-control-sm" disabled>
                    </div>
                    <div class="col-sm-2">
                      <label for="fondo" class="col-form-label">Fondo:</label>
                      <input type="text" id="fondo" name="fondo" class="form-control form-control-sm" disabled>
                    </div>
                    <div class="col-sm-2 form-group">
                      <label for="superficie" class="col-form-label">Superficie:</label>
                      <input type="text" id="superficie" name="superficie" class="form-control form-control-sm" disabled>

                    </div>
                    <div class="col-sm-2">
                      <label for="tipolote" class="col-form-label">Tipo de Lote:</label>
                      <input type="text" id="tipolote" name="tipolote" class="form-control form-control-sm" disabled>
                    </div>
                    <div class="col-sm-2">
                      <label for="preciom" class="col-form-label">Precio m²:</label>
                      <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="fas fa-dollar-sign"></i>
                          </span>
                        </div>
                        <input type="text" id="preciom" name="preciom" class="form-control text-right form-control-sm" min="1" step="0.01" disabled>
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
                        <input type="text" id="valortotal" name="valortotal" class="form-control text-right form-control-sm" min="1" step="0.01" disabled>
                      </div>
                    </div>



                  </div>
               

                  <h4><strong>INFORMACIÓN DEL PRESUPUESTO</strong></h4>
                  <div class="row justify-content-center">


                    <div class=" col-sm-2 form-group ">
                      <label for="fechaInicio" class="col-form-label">Inicio:</label>
                      <input type="date" id="fechaInicio" name="fechaInicio" class="form-control form-control-sm" required disabled>

                    </div>


                    <div class="form-group col-sm-3">
                      <label for="montoTotal" class="col-form-label text-right">Importe:</label>
                      <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="fas fa-dollar-sign"></i>
                          </span>
                        </div>
                        <input type="text" id="montoTotal" name="montoTotal" class="form-control text-right form-control-sm" min="1" step="0.01" required disabled>
                      </div>

                    </div>
                    <div class="form-group col-sm-1">
                      <label for="descuentopor" class="col-form-label text-right">% Desc:</label>
                      <input type="number" id="descuentopor" name="descuentopor" class="form-control text-right form-control-sm" min="1" step="0.01" required disabled>
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="descuento" class="col-form-label text-right">Descuento:</label>
                      <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="fas fa-dollar-sign"></i>
                          </span>
                        </div>
                        <input type="text" id="descuento" name="descuento" class="form-control text-right form-control-sm" min="1" step="0.01" required disabled>
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
                        <input type="text" id="valorop" name="valorop" class="form-control text-right form-control-sm" min="1" step="0.01" required disabled>
                      </div>

                    </div>


                    <div class="form-group col-sm-1">
                      <label for="enganchepor" class="col-form-label text-right">% Eng:</label>
                      <input type="number" id="enganchepor" name="enganchepor" class="form-control text-right form-control-sm" min="1" step="0.01" required disabled>
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



                

                

              </div>
              <div class="col-sm-8">
                <div id="corridaTable" class="mt-4">
                  <!-- Aquí se mostrará la corrida financiera (solo lectura) -->
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal para buscar presupuestos -->
<div class="modal fade" id="modalPresupuestos" tabindex="-1" role="dialog" aria-labelledby="modalPresupuestosLabel" aria-hidden="true">
  <div class="modal-dialog modal-xxl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-gradient-green">
        <h5 class="modal-title" id="modalPresupuestosLabel">BUSCAR PRESUPUESTO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table id="tablaPresupuestos" class="table table-sm table-striped  table-condensed table-bordered table-hover" style="width:100%">
            <thead class="text-center bg-gradient-green">
              <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Proyecto</th>
                <th>Manzana</th>
                <th>Lote</th>
                <th>Valor Operación</th>
                <th>Enganche</th>
                <th># MENG</th>
                <th># MSI</th>
                <th># MCI</th>
                <th>Acciones</th>
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

<div class="modal fade" id="modalVendedor" tabindex="-1" role="dialog" aria-labelledby="modalVendedorLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-gradient-green">
        <h5 class="modal-title" id="modalVendedorLabel">BUSCAR VENDEDOR</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table id="tablaVendedores" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
            <thead class="text-center bg-gradient-green">
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($colaboradores as $colaborador) : ?>
                <tr>
                  <td class="text-center"><?= htmlspecialchars($colaborador['id_col']) ?></td>
                  <td><?= htmlspecialchars($colaborador['nombre']) ?></td>
                  <td class="text-center">
                    <button class="btn btn-primary btn-sm btnSeleccionarVendedor"><i class="fas fa-check"></i></button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include_once 'templates/footer.php'; ?>
<script src="fjs/venta.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
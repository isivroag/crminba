<?php
$pagina = 'home';
include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Consulta para obtener prospectos activos (edo_pros = 1)
if ($_SESSION['s_rol'] == 4) {
  $consulta = "SELECT * from vprospecto WHERE edo_pros = 1 AND col_asignado = :col_id AND edo_seguimiento = 1 ORDER BY id_pros";

  $colaborador_id = $_SESSION['id_col'];
  $resultado = $conexion->prepare($consulta);
  $resultado->bindParam(':col_id', $colaborador_id, PDO::PARAM_INT);
  $resultado->execute();
  $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
  $resultado->closeCursor();

  // Consulta 1: Agendados no realizados
  $consulta1 = "SELECT * FROM vseg_pros WHERE edo_pros = 1 AND col_asignado = :col_id AND realizado = 0 ORDER BY fecha_seg";
  $stmt1 = $conexion->prepare($consulta1);
  $stmt1->bindParam(':col_id', $colaborador_id, PDO::PARAM_INT);
  $stmt1->execute();
  $data_agendados = $stmt1->fetchAll(PDO::FETCH_ASSOC);
  $stmt1->closeCursor(); // Limpieza opcional si vas a seguir consultando con la misma conexión

  // Consulta 2: Acciones realizadas
  //$consulta2 = "SELECT * FROM vseg_pros WHERE edo_pros = 1 AND col_asignado = :col_id AND realizado = 1 ORDER BY fecha_seg";
  $consulta2 = "SELECT v.*
                FROM vseg_pros v
                INNER JOIN (
                  SELECT id_pros, MAX(fecha_seg) AS max_fecha
                  FROM vseg_pros
                  WHERE edo_pros = 1 AND col_asignado = :col_id AND realizado = 1
                  GROUP BY id_pros
                ) ult ON v.id_pros = ult.id_pros AND v.fecha_seg = ult.max_fecha
                WHERE v.edo_pros = 1 AND v.col_asignado = :col_id AND v.realizado = 1
                ORDER BY v.fecha_seg";
  $stmt2 = $conexion->prepare($consulta2);
  $stmt2->bindParam(':col_id', $colaborador_id, PDO::PARAM_INT);
  $stmt2->execute();
  $data_realizados = $stmt2->fetchAll(PDO::FETCH_ASSOC);
  $stmt2->closeCursor(); // Opcional

} else {
  $consulta = "SELECT * from vprospecto WHERE edo_pros = 1 AND edo_seguimiento = 1 ORDER BY id_pros";
  $resultado = $conexion->prepare($consulta);
  $resultado->execute();
  $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

  // Consulta 1: Agendados no realizados
  $consulta1 = "SELECT * FROM vseg_pros WHERE edo_pros = 1 AND realizado = 0 ORDER BY fecha_seg";
  $stmt1 = $conexion->prepare($consulta1);
  $stmt1->bindParam(':col_id', $colaborador_id, PDO::PARAM_INT);
  $stmt1->execute();
  $data_agendados = $stmt1->fetchAll(PDO::FETCH_ASSOC);
  $stmt1->closeCursor(); // Limpieza opcional si vas a seguir consultando con la misma conexión

  // Consulta 2: Acciones realizadas
  $consulta2 = "SELECT * FROM vseg_pros WHERE edo_pros = 1 AND realizado = 1 ORDER BY fecha_seg";
  $consulta2 = "SELECT v.*
                FROM vseg_pros v
                INNER JOIN (
                  SELECT id_pros, MAX(fecha_seg) AS max_fecha
                  FROM vseg_pros
                  WHERE edo_pros = 1 AND realizado = 1
                  GROUP BY id_pros
                ) ult ON v.id_pros = ult.id_pros AND v.fecha_seg = ult.max_fecha
                WHERE v.edo_pros = 1 AND v.realizado = 1
                ORDER BY v.fecha_seg";
  $stmt2 = $conexion->prepare($consulta2);
  $stmt2->execute();
  $data_realizados = $stmt2->fetchAll(PDO::FETCH_ASSOC);
  $stmt2->closeCursor(); // Opcional
}



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

  .badge-nuevo {
    background-color: #28a745;
  }

  .badge-seguimiento {
    background-color: #17a2b8;
  }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->


  <!-- Main content -->
  <section class="content ">

  </section>
  <section>
    <div class="container-fluid">

      <div class="jumbotron bg-white mb-0" style="padding: .5rem 2rem; ">
        <div class="row justify-content-center">
          <div class="col-lg-12 text-center">

            <br>
            <img src="img/logoverde.png" alt="">
          </div>
        </div>
      </div>
      <?php if ($_SESSION['s_rol'] != '5') { ?>
      <div class="card-deck">
        <div class="card text-center">
          <div class="card-header bg-green text-white">
            <h3 class="card-title">PROSPECTOS NUEVOS</h3>
          </div>
          <div class="card-body">
            <div class="container-fluid">
              <div class="row">
                <div class="col-lg-12">
                  <div class="table-responsive">
                    <table name="tablaNuevo" id="tablaNuevo" class="table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:14px">
                      <thead class="text-center  bg-green">
                        <tr>
                          <th>ID</th>
                          <th>NOMBRE</th>
                          <th>TELÉFONO</th>
                          <th>CORREO</th>
                          <th>ASIGNADO A</th>
                          <th>FECHA REGISTRO</th>
                          <th>SEGUMIENTO</th>
                          <th>ACCIONES</th>

                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($data as $dat): ?>
                          <tr>
                            <td><?php echo $dat['id_pros'] ?></td>
                            <td><?php echo $dat['nombre'] ?></td>
                            <td><?php echo $dat['telefono'] ?></td>
                            <td><?php echo $dat['correo'] ?></td>
                            <td><?php echo $dat['nombre_col'] ?></td>
                            <td><?php echo date('d/m/Y', strtotime($dat['fecha_registro'])) ?></td>

                            <td class="text-center">
                              <?php
                              $badge_class = '';
                              $estado_text = '';
                              switch ($dat['edo_seguimiento']) {
                                case 1:
                                  $badge_class = 'badge-nuevo';
                                  $estado_text = 'Nuevo';
                                  break;
                                case 2:
                                  $badge_class = 'badge-seguimiento';
                                  $estado_text = 'Seguimiento';
                                  break;
                              }
                              ?>
                              <span class="badge <?php echo $badge_class ?>"><?php echo $estado_text ?></span>
                            </td>
                            <td>
                              <div class="btn-group">
                                <button class="btn btn-sm btn-success btnSeguimiento" data-toggle="tooltip" title="Seguimiento">
                                  <i class="fa-duotone fa-solid fa-phone"></i>
                                </button>

                              </div>
                            </td>



                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="card-deck">
        <div class="card text-center">
          <div class="card-header bg-green text-white">
            <h3 class="card-title">SEGUIMIENTO</h3>
          </div>
          <div class="card-body">
            <div class="container-fluid">
              <div class="row">
                <div class="col-lg-12">
                  <div class="table-responsive">
                    <table name="tablaRealizado" id="tablaRealizado" class="table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:14px">
                      <thead class="text-center  bg-green">
                        <tr>
                          <th>ID</th>
                          <th>NOMBRE</th>
                          <th>TELÉFONO</th>
                          <th>CORREO</th>
                          <th>ASIGNADO A</th>
                          <th>ULTIMO SEG</th>
                          <th>ACCION SEG</th>
                          <th>OBSERVACIONES</th>
                          <th>RESULTADO</th>

                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($data_realizados as $dat):
                        ?>
                          <tr>
                            <td><?php echo $dat['id_pros'] ?></td>
                            <td><?php echo $dat['nombre'] ?></td>
                            <td><?php echo $dat['telefono'] ?></td>
                            <td><?php echo $dat['correo'] ?></td>
                            <td><?php echo $dat['nombre_col'] ?></td>
                            <td><?php echo date('d/m/Y', strtotime($dat['fecha_seg'])) ?></td>

                            <td class="text-center">
                              <?php
                              $icono_tipo = '';
                              $estado_text = $dat['tipo_seg']; // Muestra el texto como está en la opción

                              switch ($dat['tipo_seg']) {
                                case 'Llamada':
                                  $icono_tipo = '<i class="fas fa-phone-alt text-success"></i>';

                                  break;
                                case 'Mensaje':
                                  $icono_tipo = '<i class="fas fa-comment-dots text-info"></i>';
                                  break;
                                case 'Correo':
                                  $icono_tipo = '<i class="fas fa-envelope text-warning"></i>';
                                  break;
                                case 'Reunión':
                                  $icono_tipo = '<i class="fas fa-handshake text-primary"></i>';
                                  break;
                                case 'Otro':
                                  $icono_tipo = '<i class="fas fa-ellipsis-h text-secondary"></i>';
                                  break;
                              }
                              ?>

                              <span class=><?php echo $icono_tipo . " " . $dat['tipo_seg'] ?></span>
                            </td>
                            <td><?= $dat['obs_cierre'] ?></td>
                            <?php
                            $resultado = $dat['resultado'];
                            $clase = '';
                            $icono = '';

                            // Evaluar el resultado
                            switch (strtolower($resultado)) {
                              case 'exito':
                                $clase = 'text-success font-weight-bold';
                                $icono = '<i class="fas fa-check-circle"></i>';
                                break;
                              case 'sin_respuesta':
                                $clase = 'text-warning font-weight-bold';
                                $icono = '<i class="fas fa-exclamation-circle"></i>';
                                break;
                              case 'rechazado':
                              case 'cancelado':
                                $clase = 'text-danger font-weight-bold';
                                $icono = '<i class="fas fa-times-circle"></i>';
                                break;
                              default:
                                $clase = '';
                                $icono = '<i class="fas fa-info-circle text-muted"></i>';
                                break;
                            }
                            ?>
                            <td class="<?= $clase ?>"><?= $icono . ' ' . htmlspecialchars($resultado) ?></td>



                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="card-deck">
        <div class="card text-center">
          <div class="card-header bg-green text-white">
            <h3 class="card-title">AGENDA</h3>
          </div>
          <div class="card-body">
            <div class="container-fluid">
              <div class="row">
                <div class="col-lg-12">
                  <div class="table-responsive">
                    <table name="tablaAgenda" id="tablaAgenda" class="table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:14px">
                      <thead class="text-center  bg-green">
                        <tr>
                          <th>ID SEG</th>
                          <th>ID</th>
                          <th>NOMBRE</th>
                          <th>TELÉFONO</th>
                          <th>CORREO</th>
                          <th>ASIGNADO A</th>
                          <th>ULTIMO SEG</th>
                          <th>ACCION SEG</th>
                          <th>OBSERVACIONES</th>
                          <th>RESPONSABLE</th>
                          <th>ACCIONES</th>

                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($data_agendados as $dat): ?>
                          <tr>
                            <td><?= $dat['id_seg'] ?></td>


                            <td><?php echo $dat['id_pros'] ?></td>
                            <td><?php echo $dat['nombre'] ?></td>
                            <td><?php echo $dat['telefono'] ?></td>
                            <td><?php echo $dat['correo'] ?></td>
                            <td><?php echo $dat['nombre_col'] ?></td>
                            <td><?php echo date('d/m/Y', strtotime($dat['fecha_seg'])) ?></td>

                            <td class="text-center">
                              <?php
                              $icono_tipo = '';
                              $estado_text = $dat['tipo_seg']; // Muestra el texto como está en la opción

                              switch ($dat['tipo_seg']) {
                                case 'Llamada':
                                  $icono_tipo = '<i class="fas fa-phone-alt text-success"></i>';

                                  break;
                                case 'Mensaje':
                                  $icono_tipo = '<i class="fas fa-comment-dots text-info"></i>';
                                  break;
                                case 'Correo':
                                  $icono_tipo = '<i class="fas fa-envelope text-warning"></i>';
                                  break;
                                case 'Reunión':
                                  $icono_tipo = '<i class="fas fa-handshake text-primary"></i>';
                                  break;
                                case 'Otro':
                                  $icono_tipo = '<i class="fas fa-ellipsis-h text-secondary"></i>';
                                  break;
                              }
                              ?>

                              <span class=><?php echo $icono_tipo . " " . $dat['tipo_seg'] ?></span>
                            </td>
                            <td><?= $dat['observaciones'] ?></td>
                            <td class="text-left"><?= $dat['nom_col_seg'] ?></td>
                            <td>
                              <div class="btn-group">
                                <button class="btn btn-sm btn-primary btnSeguir" data-toggle="tooltip" title="Seguimiento">
                                  <i class="fa-duotone fa-solid fa-phone"></i>
                                </button>

                              </div>
                            </td>



                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      <?php }?>
    </div>
  </section>
  <!-- /.content -->
</div>


<?php
include_once 'templates/footer.php';
?>
<script src="fjs/inicio.js?v=<?= (rand()); ?>"></script>
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
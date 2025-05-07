<aside class="main-sidebar sidebar-dark-primary elevation-4 ">
  <!-- Brand Logo -->
  <a href="inicio.php" class="brand-link ">

<img src="img/logob.png" alt="Logo" class="brand-image img-circle " style="background-color: white; ">
<span class="brand-text font-weight-bold text-white" >BIENVENIDO</span>
</a>


  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex ">
      <div class="image">
        <img src="img/user.png" class="img-circle " alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo $_SESSION['s_nombre']; ?></a>
        <input type="hidden" id="iduser" name="iduser" value="<?php echo $_SESSION['s_id_usuario']; ?>">
        <input type="hidden" id="nameuser" name="nameuser" value="<?php echo $_SESSION['s_nombre']; ?>">
        <input type="hidden" id="tipousuario" name="tipousuario" value="<?php echo $_SESSION['s_rol']; ?>">
        <input type="hidden" id="idcol" name="idcol" value="<?php echo $_SESSION['id_col']; ?>">
        <input type="hidden" id="nomcol" name="nomcol" value="<?php echo $_SESSION['nom_col']; ?>">
        <input type="hidden" id="fechasys" name="fechasys" value="<?php echo date('Y-m-d') ?>">
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-compact nav-child-indent " data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


        <li class="nav-item ">
          <a href="inicio.php" class="nav-link <?php echo ($pagina == 'home') ? "active" : ""; ?> ">
            <i class="nav-icon fa-sharp-duotone fa-regular fa-house "></i>
            <p>
              Home
            </p>
          </a>
        </li>

        <?php if ($_SESSION['s_rol'] != '6') { ?>
          <!-- ABRE MENU CATALOGOS -->


          <li class="nav-item  has-treeview <?php echo ($pagina == 'prospecto' ||  $pagina == 'colaborador' ||  $pagina == 'cliente'  || $pagina == 'partidacto' || $pagina == 'subpartidacto' || $pagina == 'cntaclientecto'
                                              ||  $pagina == 'especialidad' ||  $pagina == 'proveedor' ||  $pagina == 'personal') ? "menu-open" : ""; ?>">
            <a href="#" class="nav-link  <?php echo ($pagina == 'prospecto' || $pagina == 'colaborador' ||  $pagina == 'obra'  || $pagina == 'partidacto' || $pagina == 'subpartidacto' || $pagina == 'cntaobracto'
                                            ||  $pagina == 'especialidad' ||  $pagina == 'proveedor' ||  $pagina == 'personal') ? "active" : ""; ?>">
              <i class="nav-icon  fa-sharp-duotone fa-regular fa-circle "></i>
              <p>
                Catalogos
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>


            <ul class="nav nav-treeview">



              <li class="nav-item">
                <a href="cntaprospecto.php" class="nav-link <?php echo ($pagina == 'prospecto') ? "seleccionado" : ""; ?>  ">
                  <i class=" fa-duotone fa-regular fa-users-viewfinder nav-icon"></i>
                  <p>Prospectos</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="cntacolaborador.php" class="nav-link <?php echo ($pagina == 'colaborador') ? "seleccionado" : ""; ?>  ">
                  <i class=" fa-duotone fa-regular fa-user-headset nav-icon"></i>
                  <p>Colaboradores</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="cntacliente.php" class="nav-link <?php echo ($pagina == 'cliente') ? "seleccionado" : ""; ?>  ">
                  <i class=" fa-duotone fa-regular fa-user-tie nav-icon"></i>
                  <p>Clientes</p>
                </a>
              </li>




            </ul>

          </li>

          <!-- CIERRA MENU CATALOGOS -->


          <li class="nav-item  has-treeview <?php echo ($pagina == 'requisicion' || $pagina == 'ordenes' || $pagina == 'obracto' ||
                                              $pagina === 'saldoseggral' || $pagina == "cntapagoordenes" || $pagina == "reportepagos") ? "menu-open" : ""; ?>">
            <a href="#" class="nav-link  <?php echo ($pagina == 'requisicion' || $pagina == 'ordenes' || $pagina == 'obracto' ||
                                            $pagina === 'saldoseggral' || $pagina == "cntapagocxpgral" || $pagina == "reportepagos") ? "active" : ""; ?>">
              <i class="fa-sharp-duotone fa-regular fa-circle  nav-icon"></i>
              <p>
                Operaciones
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>


            <ul class="nav nav-treeview">



              <li class="nav-item">
                <a href="cntarequisicion.php" class="nav-link <?php echo ($pagina == 'requisicion') ? " seleccionado" : ""; ?>  ">
                  <i class="fa-sharp-duotone fa-regular fa-circle nav-icon"></i>
                  <p>Requisiciones</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="cntaordenes.php" class="nav-link <?php echo ($pagina == 'ordenes') ? " seleccionado" : ""; ?>  ">
                  <i class="fa-sharp-duotone fa-regular fa-circle nav-icon"></i>
                  <p>Ordenes de Compra</p>
                </a>
              </li>


            </ul>

          </li>


          <!-- ABRE MENU INGRESOS -->
          <?php if ($_SESSION['s_rol'] != '4') { ?>
            <li class="nav-item has-treeview <?php echo ($pagina == 'cntaingresos' || $pagina == 'ingresos' || $pagina == 'cntacobros'
                                                || $pagina == 'cntacxc' || $pagina == 'recepcion' || $pagina == 'ingresos' || $pagina == 'diario' || $pagina == 'confirmar') ? "menu-open" : ""; ?>">


              <a href="#" class="nav-link <?php echo ($pagina == 'cntaingresos' || $pagina == 'ingresos' || $pagina == 'cntacobros'
                                            || $pagina == 'cntacxc' || $pagina == 'recepcion' || $pagina == 'ingresos' || $pagina == 'diario' || $pagina == 'confirmar') ? "active" : ""; ?>">

                <span class="fa-stack">
                  <i class=" fas fa-arrow-up "></i>
                  <i class=" fas fa-dollar-sign "></i>

                </span>
                <p>
                  Ingresos

                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <!--
            <li class="nav-item">
              <a href="ingresos.php" class="nav-link <?php echo ($pagina == 'ingresos') ? "active seleccionado" : ""; ?>  ">

                <i class=" text-green fas fa-file-invoice-dollar nav-icon"></i>
                <p>Registro de Facturas</p>
              </a>
            </li>
          -->

                <li class="nav-item">
                  <a href="cntacxc.php" class="nav-link <?php echo ($pagina == 'cntacxc') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-green fa-list nav-icon"></i>
                    <p>Cuentas x Cobrar</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="cntacobros.php" class="nav-link <?php echo ($pagina == 'cntacobros') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-green fa-file-invoice-dollar nav-icon"></i>
                    <p>Cobros-Det Partidas </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="cntaingresos.php" class="nav-link <?php echo ($pagina == 'cntaingresos') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-green fa-search-dollar nav-icon"></i>
                    <p>Consulta Ingresos</p>
                  </a>
                </li>


              </ul>
            </li>

            <!-- CIERRA MENU CATALOGOS -->
            <!-- ABRE MENU REQUISICIONES 
            <li class="nav-item has-treeview <?php echo ($pagina == 'requisicion' || $pagina == 'reportes') ? "menu-open" : ""; ?>">


              <a href="#" class="nav-link <?php echo ($pagina == 'requisicion' || $pagina == 'reportes') ? "active" : ""; ?>">

                <span class="fa-stack">
                  <i class="nav-icon fas fa-shield-alt "></i>
                </span>
                <p>
                  Requisiciones
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">

                <li class="nav-item">
                  <a href="cntareq.php" class="nav-link <?php echo ($pagina == 'requisicion') ? "active seleccionado" : ""; ?>  ">

                    <i class="fa-solid fa-hand-holding-dollar  nav-icon"></i>
                    <p>Requisiciones</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="cntareportes.php" class="nav-link <?php echo ($pagina == 'reportes') ? "active seleccionado" : ""; ?>  ">

                    <i class="fa-solid fa-file-invoice-dollar   nav-icon"></i>
                    <p>Reportes de Pagos</p>
                  </a>
                </li>


              </ul>
            </li>-->
            <!-- ABRE MENU REQUISICIONES -->

            <!-- ABRE MENU EGRESOS -->
            <li class="nav-item has-treeview <?php echo ($pagina == 'subcontrato' || $pagina == 'subcontratocto' || $pagina == 'cntaegresos' || $pagina == 'egresos' || $pagina == 'ioficina'
                                                || $pagina == 'cntacxp' || $pagina == 'cntacxpcto' || $pagina == 'cntapagocxp' || $pagina == 'pagoscxp' || $pagina == 'provision' || $pagina == 'provisioncto'  || $pagina == 'saldoseg'
                                                || $pagina == 'gastos' || $pagina == 'extrasub' ||  $pagina == 'rptpagoobra' ||  $pagina == 'cntapagosub') ? "menu-open" : ""; ?>">


              <a href="#" class="nav-link <?php echo ($pagina == 'subcontrato' || $pagina == 'subcontratocto' || $pagina == 'cntaegresos' || $pagina == 'egresos' || $pagina == 'ioficina'
                                            || $pagina == 'cntacxp' || $pagina == 'cntacxpcto' || $pagina == 'cntapagocxp' || $pagina == 'pagoscxp' || $pagina == 'provision' || $pagina == 'provisioncto'  || $pagina == 'saldoseg'
                                            || $pagina == 'gastos' || $pagina == 'extrasub' || $pagina == 'rptpagoobra' ||  $pagina == 'cntapagosub') ? "active" : ""; ?>">

                <span class="fa-stack">
                  <i class=" fas fa-arrow-down "></i>
                  <i class=" fas fa-dollar-sign "></i>

                </span>
                <p>
                  Egresos

                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">

                <li class="nav-item">
                  <a href="cntasubcontrato.php" class="nav-link <?php echo ($pagina == 'subcontrato') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-purple fa-industry nav-icon"></i>
                    <p>Subcontratos</p>
                  </a>
                </li>
                <?php if ($_SESSION['s_rol'] == '3' || $_SESSION['s_rol'] == '2' || $_SESSION['s_rol'] == '5') { ?>
                  <li class="nav-item">
                    <a href="cntasubcontratocto.php" class="nav-link <?php echo ($pagina == 'subcontratocto') ? "active seleccionado" : ""; ?>  ">

                      <i class="fas text-purple fa-industry nav-icon"></i>
                      <p>Subcontratos-Det Partidas</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="cntapagosub.php" class="nav-link <?php echo ($pagina == 'cntapagosub') ? "active seleccionado" : ""; ?>  ">

                      <i class="fas text-purple fa-file-invoice-dollar nav-icon"></i>
                      <p>Pagos de Subcontratos </p>
                    </a>
                  </li>
                <?php } ?>

                <li class="nav-item">
                  <a href="cntasubcontratosadd.php" class="nav-link <?php echo ($pagina == 'extrasub') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-purple fa-folder-plus nav-icon"></i>
                    <p>Adendas Subcontrato</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="cntacxp.php" class="nav-link <?php echo ($pagina == 'cntacxp') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-purple fa-pen-square nav-icon"></i>
                    <p>Cuentas x Pagar</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="cntacxpcto.php" class="nav-link <?php echo ($pagina == 'cntacxpcto') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-purple fa-pen-square nav-icon"></i>
                    <p>CxP-Det Partidas</p>
                  </a>
                </li>


                <li class="nav-item">
                  <a href="cntaprovision.php" class="nav-link <?php echo ($pagina == 'provision') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-purple fa-list nav-icon"></i>
                    <p>Cotizaciones</p>
                  </a>
                </li>
                <!--
                <li class="nav-item">
                  <a href="cntaprovisioncto.php" class="nav-link <?php echo ($pagina == 'provisioncto') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-purple fa-list nav-icon"></i>
                    <p>Cotizaciones-Det Partidas</p>
                  </a>
                </li>
                -->
                <li class="nav-item">
                  <a href="cntagastos.php" class="nav-link <?php echo ($pagina == 'gastos') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-purple fa-money-bill-wave nav-icon"></i>
                    <p>Otros Gastos</p>
                  </a>
                </li>

                <!--
                <li class="nav-item">
                  <a href="cntaioficina.php" class="nav-link <?php echo ($pagina == 'ioficina') ? "active seleccionado" : ""; ?>  ">

                    <i class=" text-purple fa-solid fa-city nav-icon"></i>
                    <p>Indirectos de Oficina</p>
                  </a>
                </li>
                -->

                <li class="nav-item">
                  <a href="cntaegresos.php" class="nav-link <?php echo ($pagina == 'cntapagocxp') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-purple fa-search-dollar nav-icon"></i>
                    <p>Consulta Egresos </p>
                  </a>
                </li>


                <?php if ($_SESSION['s_rol'] == '3' || $_SESSION['s_rol'] == '2') { ?>
                  <li class="nav-item">
                    <a href="cntasaldoseg.php" class="nav-link <?php echo ($pagina == 'saldoseg') ? "active seleccionado" : ""; ?>  ">

                      <i class="fas text-purple fa-coins nav-icon"></i>
                      <p>Saldos Pendientes </p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="prerptpagos.php" class="nav-link <?php echo ($pagina == 'rptpagoobra') ? "active seleccionado" : ""; ?>  ">

                      <i class="fas text-purple fa-file-invoice-dollar nav-icon"></i>
                      <p>Prereporte Pagos Obra </p>
                    </a>
                  </li>


                <?php } ?>

              </ul>
            </li>
            <!-- CIERRA MENU EGRESOS -->
          <?php } ?>


          <?php if ($_SESSION['s_rol'] != '5') { ?>
            <!-- ABRE MENU OPERACIONES -->
            <li class="nav-item has-treeview <?php echo ($pagina == 'nomina' || $pagina == 'otro' || $pagina == 'proveedorobra' || $pagina == 'cajaobra') ? "menu-open" : ""; ?>">


              <a href="#" class="nav-link <?php echo ($pagina == 'nomina' || $pagina == 'otro' || $pagina == 'proveedorobra' || $pagina == 'cajaobra') ? "active" : ""; ?>">

                <span class="fa-stack">

                  <i class=" fas fa-briefcase nav-icon "></i>

                </span>
                <p>
                  Operaciones

                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">

                <li class="nav-item">
                  <a href="cntacajaobra.php" class="nav-link <?php echo ($pagina == 'cajaobra') ? "active seleccionado" : ""; ?>  ">

                    <i class=" fas fa-briefcase nav-icon"></i>
                    <p>Caja</p>
                  </a>
                </li>



              </ul>
              <ul class="nav nav-treeview">

                <li class="nav-item">
                  <a href="cntaproveedorobra.php" class="nav-link <?php echo ($pagina == 'proveedorobra') ? "active seleccionado" : ""; ?>  ">

                    <i class=" fas fa-portrait nav-icon"></i>
                    <p>Proveedores</p>
                  </a>
                </li>



              </ul>
              <ul class="nav nav-treeview">

                <li class="nav-item">
                  <a href="cntanomina.php" class="nav-link <?php echo ($pagina == 'nomina') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas  fa-people-arrows nav-icon"></i>
                    <p>Nominas</p>
                  </a>
                </li>



              </ul>

              <ul class="nav nav-treeview">

                <li class="nav-item">
                  <a href="cntaotrosgastos.php" class="nav-link <?php echo ($pagina == 'otro') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas  fa-money-bill-wave nav-icon"></i>
                    <p>Gastos Obra</p>
                  </a>
                </li>



              </ul>
            </li>
            <!-- CIERRA MENU OPERACIONES -->
          <?php } ?>

          <!-- ABRE MENU REPORTES -->
          <?php if ($_SESSION['s_rol'] == '3' || $_SESSION['s_rol'] == '2' || $_SESSION['s_rol'] == '5') { ?>
            <li class="nav-item has-treeview <?php echo ($pagina == 'caja') ? "menu-open" : ""; ?>">


              <a href="#" class="nav-link <?php echo ($pagina == 'caja') ? "active" : ""; ?>">

                <span class="fa-stack">
                  <i class="nav-icon fas fa-shield-alt "></i>
                </span>
                <p>
                  Control
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">

                <li class="nav-item">
                  <a href="cntacaja.php" class="nav-link <?php echo ($pagina == 'caja') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas fa-briefcase   nav-icon"></i>
                    <p>Caja</p>
                  </a>
                </li>




              </ul>
            </li>

            <?php if ($_SESSION['s_rol'] != '5') { ?>
              <li class="nav-item has-treeview <?php echo ($pagina == 'rptobra' || $pagina == 'vrptpagos') ? "menu-open" : ""; ?>">


                <a href="#" class="nav-link <?php echo ($pagina == 'rptobra' || $pagina == 'vrptpagos') ? "active" : ""; ?>">

                  <span class="fa-stack">
                    <i class="nav-icon fas fa-file-contract "></i>
                  </span>
                  <p>
                    Reportes
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">

                  <li class="nav-item">
                    <a href="rptobra.php" class="nav-link <?php echo ($pagina == 'rptobra') ? "active seleccionado" : ""; ?>  ">

                      <i class="fas text-primary fa-building nav-icon"></i>
                      <p>Obra</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="vrptpagos.php" class="nav-link <?php echo ($pagina == 'vrptpagos') ? "active seleccionado" : ""; ?>  ">

                      <i class=" fa-solid fa-money-bill-transfer text-primary  nav-icon"></i>
                      <p>Pagos</p>
                    </a>
                  </li>


                </ul>
              </li>
            <?php } ?>




          <?php } ?>
          <!-- CIERRA MENU EGRESOS -->
        <?php } ?>
        <li class="nav-item has-treeview <?php echo ($pagina == 'rptproveedores') ? "menu-open" : ""; ?>">


          <a href="#" class="nav-link <?php echo ($pagina == 'rptproveedores') ? "active" : ""; ?>">

            <span class="fa-stack">
              <i class="nav-icon fa-solid fa-magnifying-glass"></i>
            </span>
            <p>
              Consultas
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">

            <li class="nav-item">
              <a href="rptproveedores.php" class="nav-link <?php echo ($pagina == 'rptproveedores') ? "active seleccionado" : ""; ?>  ">

                <i class="fas fa-portrait nav-icon"></i>
                <p>Proveedores</p>
              </a>
            </li>





          </ul>
        </li>

        <li class="nav-item has-treeview <?php echo ($pagina == 'saldosegop' || $pagina == 'verpagosop') ? "menu-open" : ""; ?>">


          <a href="#" class="nav-link <?php echo ($pagina == 'saldosegop' || $pagina == 'verpagosop') ? "active" : ""; ?>">

            <span class="fa-stack">
              <i class="nav-icon fa-solid fa-file-circle-exclamation "></i>
            </span>
            <p>
              Saldos de Obra
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">

            <li class="nav-item">
              <a href="cntasaldosegop.php" class="nav-link <?php echo ($pagina == 'saldosegop') ? "active seleccionado" : ""; ?>  ">

                <i class="text-primary fa-solid fa-file-invoice-dollar nav-icon"></i>
                <p>Saldos Pendientes</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="verpagos.php" class="nav-link <?php echo ($pagina == 'verpoagosop') ? "active seleccionado" : ""; ?>  ">

                <i class="text-primary fa-solid fa-magnifying-glass-dollar  nav-icon"></i>
                <p>Ver Pagos</p>
              </a>
            </li>



          </ul>
        </li>

        <?php if ($_SESSION['s_rol'] == '3') { ?>
          <hr class="sidebar-divider">
          <li class="nav-item">
            <a href="cntausuarios.php" class="nav-link <?php echo ($pagina == 'usuarios') ? "active" : ""; ?> ">
              <i class="fas fa-user-shield"></i>
              <p>Usuarios</p>
            </a>
          </li>
        <?php } ?>

        <hr class="sidebar-divider">
        <li class="nav-item">
          <a class="nav-link" href="bd/logout.php">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <p>Salir</p>
          </a>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
<!-- Main Sidebar Container -->
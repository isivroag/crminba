<aside class="main-sidebar sidebar-dark-primary elevation-4 ">
  <!-- Brand Logo -->

  <a href="inicio.php" class="brand-link">

    <img src="img/logoempresa.jpg" alt="Logo" class="brand-image   elevation-3">
    <span class="brand-text font-weight-bold text-white">CHECA</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex ">
      <div class="image">
        <img src="img/user.png" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="perfilprov.php" class="d-block text-wrap" style="white-space: normal;"><?php echo $_SESSION['razon']; ?></a>
        <input type="hidden" id="iduser" name="iduser" value="<?php echo $_SESSION['id_prov']; ?>">
        <input type="hidden" id="nameuser" name="nameuser" value="<?php echo $_SESSION['razon']; ?>">

        <input type="hidden" id="fechasys" name="fechasys" value="<?php echo date('Y-m-d') ?>">
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-compact nav-child-indent " data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


        <li class="nav-item ">
          <a href="dashboard_proveedor.php" class="nav-link <?php echo ($pagina == 'home') ? "active" : ""; ?> ">
            <i class="nav-icon fa-sharp-duotone fa-regular fa-circle "></i>
            <p>
              Home
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="perfilprov.php" class="nav-link <?php echo ($pagina == 'perfilprov') ? "active" : ""; ?> ">
            <i class="fas fa-user-shield"></i>
            <p>Perfil</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="docuementosprov.php" class="nav-link <?php echo ($pagina == 'perfilprov') ? "active" : ""; ?> ">
            <i class="fas fa-user-shield"></i>
            <p>Documentos</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="docuementosprov.php" class="nav-link <?php echo ($pagina == 'perfilprov') ? "active" : ""; ?> ">
            <i class="fas fa-user-shield"></i>
            <p>Documentos</p>
          </a>
        </li>






      
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
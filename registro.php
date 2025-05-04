<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CHECA | REGISTRO</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <link rel="apple-touch-icon" sizes="57x57" href="img/iconos/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="img/iconos/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="img/iconos/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="img/iconos/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="img/iconos/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="img/iconos/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="img/iconos/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="img/iconos/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="img/iconos/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href="img/iconos/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="img/iconos/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="img/iconos/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="img/iconos/favicon-16x16.png">
  <link rel="manifest" href="img/iconos/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">
</head>

<style>
  .bad {
    border: 2px solid red;
  }

  .good {
    border: 2px solid green;
  }
</style>

<body>
  <!--
    <h2>Registra tu cuenta</h2>
    <form id="registroForm">
        <input type="text" id="username" name="username" placeholder="Nombre de usuario" required>
        <input type="password" id="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrar</button>
    </form>
-->


  <body class="hold-transition register-page" style="background:white">
    <div class="register-box rounded-lg" style="background:#021b38">
      <div class="register-logo">
        <img src="img/logo2.png" alt="" style="width:50%">

      </div>
      <!-- /.login-logo -->
      <div class="card " style="background:#021b38">
        <div class="card-body login-card-body " style="background:#021b38">
          <p class="register-box-msg text-white font-weight-bold text-lg">REGISTRAR USUARIO</p>

          <form id="registroForm" name="registroForm" action="" method="post">
            <div class="input-group mb-3">
              <input type="text" class="form-control" id="username" name="username" placeholder="Escriba el nombre de Usuario" autocomplete="off">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user text-white "></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3" id="passwordc">
              <input type="password" class="form-control" id="password" name="password" placeholder="Escriba su Contraseña" autocomplete="new-password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock text-white"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3" id="passwordc2">
              <input type="password" class="form-control" id="password2" name="password2" placeholder="Confirme su Contraseña" autocomplete="new-password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock text-white"></span>
                </div>
              </div>
            </div>
            <div class="row justify-content-center">
              <div class="col-12 mb-3">
                <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#pdfModal">Ver Aviso</button>
              </div>

              <!-- /.col -->
              <div class="col-12">
                <button type="submit" class="btn btn-success btn-block">Registrar</button>
              </div>
              <!-- /.col -->
            </div>

          </form>

        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
    <!-- /.login-box -->

    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="pdfModalLabel">Aviso</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <embed src="files/aviso.pdf" type="application/pdf" width="100%" height="600px" />
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->


    <script src="js/adminlte.min.js"></script>
    <script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>



  </body>




  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const password = document.getElementById('password');
      const password2 = document.getElementById('password2');
      const passwordc = document.getElementById('passwordc');
      const passwordc2 = document.getElementById('passwordc2');

      function validatePasswords() {
        if (password.value !== password2.value) {
          passwordc.classList.add('bad');
          passwordc2.classList.add('bad');
        } else {
          passwordc.classList.remove('bad');
          passwordc2.classList.remove('bad');
        }
      }

      password.addEventListener('input', validatePasswords);
      password2.addEventListener('input', validatePasswords);
    });


    document.getElementById('registroForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const password = document.getElementById('password');
      const confirmPassword = document.getElementById('password2');
      console.log(password.value)
      console.log(confirmPassword.value)
      if (password.value === confirmPassword.value) {
        let formData = new FormData(this);
        let token = new URLSearchParams(window.location.search).get('token');

        password.classList.remove('bad');
        confirmPassword.classList.remove('bad');

        fetch(`api/registrotoken.php?token=${token}`, {
            method: 'POST',
            body: formData
          })
          .then(response => response.text())
          .then(data => {
            console.log(data);
            Swal.fire({
              title: data, // Usamos el mensaje devuelto por la API como título
              icon: "info",
              timer: 2000,
            });
          });
        window.location.href = "index.php";
      } else {
        password.classList.add('bad');
        confirmPassword.classList.add('bad');

        Swal.fire({
          title: "Error las contraseñas no coinciden",
          icon: "error",
        });
        window.location.href = "index.php";
      }


    });
  </script>
</body>

</html>
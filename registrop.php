<!DOCTYPE html>
<?php
$token = $_GET['token'];
?>
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

  <style>
    body {
      background-color: #1a1a2e !important;
      color: white !important;
    }

    .login-box {
      width: 450px;
    }

    .card {
      background: white !important;
      color: #1a1a2e !important;
      border-radius: 15px !important;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4) !important;
      padding: 20px !important;
    }

    .btn-primary {
      background-color: #1a1a2e !important;
      border-color: #1a1a2e !important;
      font-size: 1.2rem !important;
      padding: 10px !important;
    }

    .btn-info:hover {
      background-color: #0f0f1a !important;
      border-color: #0f0f1a !important;
    }

    .btn-info {
      background-color: #284697 !important;
      border-color: #1a1a2e !important;
      font-size: 1.2rem !important;
      padding: 10px !important;
    }

    .btn-info:hover {
      background-color: #213063 !important;
      border-color: #0f0f1a !important;
    }

    .input-group-text {
      background-color: #1a1a2e !important;
      color: white !important;
    }

    .login-logo img {
      max-width: 180px !important;
    }

    .bad {
      border: 2px solid red;
    }

    .good {
      border: 2px solid green;
    }

    .modal-body h5 {
      text-align: center;
      font-size: 1.5rem;
    }
  </style>
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="card shadow-lg">
      <div class="card-header text-center">
        <img src="img/logoempresa.jpg" alt="Logo" class="img-fluid login-logo">
        <h3 class="mt-3">Registro de Proveedores</h3>
      </div>
      <div class="card-body login-card-body">
        <form id="registroForm" name="registroForm" action="" method="post">
          <div class="input-group mb-3">
            <input type="hidden" class="form-control" id="token" name="token" value='<?php echo $token ?>' placeholder="Escriba el nombre de Usuario" autocomplete="off">
            <input type="text" class="form-control" id="username" name="username" placeholder="Escriba el nombre de Usuario" autocomplete="off">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3" id="passwordc">
            <input type="password" class="form-control" id="password" name="password" placeholder="Escriba su Contraseña" autocomplete="new-password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3" id="passwordc2">
            <input type="password" class="form-control" id="password2" name="password2" placeholder="Confirme su Contraseña" autocomplete="new-password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-12 mb-3">
              <!-- <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#pdfModal">Aviso de Privacidad</button>-->
              <a href="#" class="" data-toggle="modal" data-target="#pdfModal">
                Aviso de Privacidad
              </a>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block">Registrar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="pdfModalLabel">Aviso de Privacidad</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="avisoPrivacidad" style="user-select: none;">
          <div class="card">
            <div class="card-body">
              <h5 class="text-center text-bold">Términos y condiciones de uso</h5><br>

              <p><strong>1. Identidad y Domicilio del Responsable</strong><br>
                [Nombre de la empresa u organización], con domicilio en [Dirección completa], es responsable del tratamiento de sus datos personales conforme a este Aviso de Privacidad.</p>

              <p><strong>2. Datos Personales Recabados</strong><br>
                Podemos recopilar los siguientes datos personales de nuestros proveedores:</p>
              <ul>
                <li>Nombre completo</li>
                <li>Razón social</li>
                <li>RFC</li>
                <li>Correo electrónico</li>
                <li>Teléfono</li>
                <li>Dirección</li>
                <li>Información de facturación</li>
                <li>Documentos requeridos para procesos de cotización o contratación</li>
              </ul>

              <p><strong>3. Finalidad del Tratamiento de Datos</strong><br>
                Los datos personales que recabamos serán utilizados para:</p>
              <ul>
                <li>Gestionar el registro y alta de proveedores</li>
                <li>Validar la documentación requerida para cotizaciones y adquisiciones</li>
                <li>Contactarlo en relación con procesos de selección y contratación de servicios</li>
                <li>Cumplir con obligaciones legales y fiscales</li>
                <li>Mejorar nuestros procesos de adquisición y contratación</li>
              </ul>

              <p><strong>4. Protección y Seguridad de sus Datos</strong><br>
                Implementamos medidas de seguridad para proteger sus datos contra acceso no autorizado, alteración, pérdida o uso indebido.</p>

              <p><strong>5. Transferencia de Datos</strong><br>
                Sus datos personales no serán compartidos con terceros sin su consentimiento, salvo en los casos previstos por la ley.</p>

              <p><strong>6. Derechos ARCO (Acceso, Rectificación, Cancelación y Oposición)</strong><br>
                Usted tiene derecho a acceder, rectificar, cancelar u oponerse al uso de sus datos personales. Para ejercer estos derechos, puede contactarnos en [correo de contacto].</p>

              <p><strong>7. Cambios en el Aviso de Privacidad</strong><br>
                Nos reservamos el derecho de modificar este Aviso de Privacidad en cualquier momento. Cualquier cambio será notificado a través de nuestro sitio web.</p>

              <p><strong>8. Contacto</strong><br>
                Si tiene preguntas sobre este Aviso de Privacidad, puede escribirnos a [correo de contacto] o llamarnos al [número telefónico].</p>
            </div>
          </div>
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

  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const password = document.getElementById('password');
      const password2 = document.getElementById('password2');
      const passwordc = document.getElementById('passwordc');
      const passwordc2 = document.getElementById('passwordc2');
      const form = document.getElementById('registroForm');

      // Expresión regular para validar la contraseña
      const passwordRegex = /^(?=.*[A-Z])(?=.*[\W])(?=.*[a-zA-Z0-9]).{8,}$/;

      function validatePasswords() {
        if (!passwordRegex.test(password.value)) {
          passwordc.classList.add('bad');
          password.setCustomValidity(
            'La contraseña debe tener al menos 8 caracteres, una letra mayúscula y un carácter especial.'
          );
        } else {
          passwordc.classList.remove('bad');
          password.setCustomValidity('');
        }
      }

      function validatePasswordsMatch() {
        if (password.value !== password2.value) {
          passwordc2.classList.add('bad');
          password2.setCustomValidity('Las contraseñas no coinciden.');
        } else {
          passwordc2.classList.remove('bad');
          password2.setCustomValidity('');
        }
      }
      password.addEventListener('input', () => {
        validatePasswords();
        validatePasswordsMatch();
      });

      password2.addEventListener('input', validatePasswordsMatch);
      form.addEventListener('submit', (e) => {
        validatePasswords();
        validatePasswordsMatch();

        if (!form.checkValidity()) {
          e.preventDefault(); // Prevenir el envío si hay errores
          Swal.fire({
            title: 'Error en el formulario',
            text: 'Por favor, corrige los errores en el formulario.',
            icon: 'error',
          });
        }
      });

      const avisoPrivacidad = document.getElementById("avisoPrivacidad");

      // Bloquear clic derecho dentro del modal
      avisoPrivacidad.addEventListener("contextmenu", function(e) {
        e.preventDefault();
      });

      // Bloquear selección de texto dentro del modal
      avisoPrivacidad.addEventListener("mousedown", function(e) {
        if (e.detail > 1) { // Evita la selección múltiple con doble clic
          e.preventDefault();
        }
      });

      // Bloquear uso de teclas Ctrl + C (Copiar), Ctrl + U (Ver código fuente), Ctrl + P (Imprimir)
      document.addEventListener("keydown", function(e) {
        if ((e.ctrlKey && e.key === "c") || (e.ctrlKey && e.key === "u") || (e.ctrlKey && e.key === "p")) {
          e.preventDefault();
        }
      });
    });

    document.getElementById('registroForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const password = document.getElementById('password');
      const confirmPassword = document.getElementById('password2');
      const token = document.getElementById('token');
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
        //window.location.href = "index.php";
      } else {
        password.classList.add('bad');
        confirmPassword.classList.add('bad');

        Swal.fire({
          title: "Error las contraseñas no coinciden",
          icon: "error",
        });
        //window.location.href = "index.php";
      }
    });
  </script>
</body>

</html>
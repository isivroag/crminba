<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CHECA | Inicio de Sesión</title>

    <!-- Font Awesome -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesomep/css/all.min.css">

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../css/adminlte.css">
    <!-- SweetAlert -->
    <link rel="stylesheet" href="../plugins/sweetalert2/sweetalert2.min.css">


    
    <link rel="apple-touch-icon" sizes="57x57" href="img/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="img/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="img/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="img/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="img/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="img/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="img/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="img/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <style>
        body {
            background-color: #1a1a2e !important; /* Color de fondo diferente */
            color: white !important;
        }
        .login-box {
            width: 450px !important;
        }
        .card {
            background: white !important;
            color: #1a1a2e !important; /* Color de texto diferente */
            border-radius: 15px !important;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4) !important;
            padding: 20px !important;
        }
        .btn-primary {
            background-color: #1a1a2e !important; /* Color de botón diferente */
            border-color: #1a1a2e !important;
            font-size: 1.2rem !important;
            padding: 10px !important;
        }
        .btn-primary:hover {
            background-color: #0f0f1a !important; /* Color de botón hover diferente */
            border-color: #0f0f1a !important;
        }
        .input-group-text {
            background-color: #1a1a2e !important; /* Color de icono diferente */
            color: white !important; /* Color de icono diferente */
        }
        .login-logo img {
            max-width: 180px !important;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">

        <div class="card shadow-lg">
        <div class="card-header text-center">
                <img src="img/logoempresa.jpg" alt="Logo" class="img-fluid login-logo">
                <h3 class="mt-3">Proveedores</h3>
            </div>
            <div class="card-body login-card-body">
                

                <form id="formlogin" action="bd/login_proveedor.php" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Usuario" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
                        </div>
                    </div>
                </form>
                <div id="message" class="mt-3"></div>
            </div>
        </div>
    </div>
    <!--
    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                var username = $('#username').val();
                var password = $('#password').val();

                $.ajax({
                    type: 'POST',
                    url: 'bd/login_proveedor.php',
                    data: { username: username, password: password },
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            window.location.href = 'dashboard_proveedor.php'; // Redirigir a la página de dashboard del proveedor
                        } else {
                            $('#message').html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    }
                });
            });
        });
    </script>
    -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert -->
    <script src="../plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <!-- Código personalizado -->
    <script src="fjs/codigop.js?v=<?php echo (rand()); ?>"></script>

</body>

</html>
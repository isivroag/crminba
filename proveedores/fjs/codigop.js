$('#formlogin').submit(function(e) {
    e.preventDefault();
    var usuario = $.trim($('#username').val());
    var password = $.trim($('#password').val());
   

    if (usuario.length == 0 || password.length == 0) {
        Swal.fire({
            title: 'Usuario y/o Contraseña faltantes',
            text: "Debe ingresar un usuario y contraseña",
            icon: 'warning',
        })
        return false;
    } else {
        $.ajax({
            url: "bd/login_proveedor.php",
            type: "POST",
            datatype: "json",
            data: { usuario: usuario, password: password},
            success: function(data) {
                console.log(data);
                if (data == 1) {
                    Swal.fire({
                        title: 'Usuario no identificado',
                        text: "El usuario y/o la contraseña ingresado no coinciden",
                        icon: 'error',
                    })
                } else if (data == 0) {
                    Swal.fire({
                        title: 'NO DB',
                        text: "Base de datos desconectada",
                        icon: 'error',
                    })
                } else {
                    Swal.fire({
                        title: 'Conexion Exitosa',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ingresar',
                        icon: 'success',
                    }).then((result) => {
                        if (result.value) {
                            window.location.href = "dashboard_proveedor.php";
                        }
                    })
                }
            }


        });
    }
});



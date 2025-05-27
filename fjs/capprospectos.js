$(document).ready(function () {
  $('.selectpicker').selectpicker();
  
  // Manejar el envío del formulario
  $('#formProspecto').submit(function (e) {
    e.preventDefault();
    
    // Obtener valores del formulario
    var nombre = $.trim($('#nombre').val());
    var telefono = $.trim($('#telefono').val());
    var correo = $.trim($('#correo').val());
    var origen = $('#origen').val();
    var col_asignado = $('#col_asignado').val();
    var nombre_colaborador = $('#col_asignado option:selected').text();
    
    // Validación básica
    if (!nombre || !telefono || !correo || !origen || !col_asignado) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Todos los campos son obligatorios'
      });
      return;
    }
    
    // Validar correo electrónico
    var correoValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);
    if (!correoValido) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Por favor ingrese un correo electrónico válido'
      });
      return;
    }
    
    // Validar teléfono (10 dígitos)
    var telefonoValido = /^\d{10}$/.test(telefono);
    if (!telefonoValido) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'El teléfono debe tener 10 dígitos numéricos'
      });
      return;
    }
    
    // Crear objeto con los datos del formulario
    var formData = {
      nombre: nombre,
      telefono: telefono,
      correo: correo,
      origen: origen,
      col_asignado: col_asignado,
      opcion: 1 // Opción 1: Nuevo prospecto
    };
    
    // Enviar datos por AJAX
    $.ajax({
      url: 'bd/crudprospecto.php',
      type: 'POST',
      dataType: 'json',
      data: formData,
      success: function (data) {
        if (data.success) {
          // Enviar correo automáticamente
          enviarCorreoAutomatico(data.id_pros, nombre, telefono, correo, nombre_colaborador);
          
          // Mostrar mensaje de éxito
          Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: 'Prospecto registrado y notificación enviada',
            timer: 1500,
            timerProgressBar: true,
          }).then(() => {
            // Redirigir inicio
            window.location.href = 'inicio.php';
          });
          
          // Opcional: Actualizar turno del colaborador
          actualizarTurnoColaborador(col_asignado);
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Error al registrar prospecto'
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al conectar con el servidor'
        });
      }
    });
  });
  
  // Función para enviar correo automáticamente
  function enviarCorreoAutomatico(id_pros, nombre, telefono, correo, colaborador) {
    $.ajax({
      url: 'bd/usarapicorreo.php',
      type: 'POST',
      dataType: 'json',
      contentType: 'application/json',
      data: JSON.stringify({
        id_pros: id_pros,
        nombre: nombre,
        telefono: telefono,
        correo: correo,
        colaborador: colaborador
      }),
      success: function (response) {
        if (!response.success) {
          console.error("Error al enviar correo:", response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la solicitud de correo:", error);
      }
    });
  }
  
  // Función para actualizar turno del colaborador (opcional)
  function actualizarTurnoColaborador(id_col) {
    $.ajax({
      url: 'bd/actualizar_turno.php',
      type: 'POST',
      data: { id_col: id_col },
      success: function() {
        console.log('Turno actualizado para colaborador:', id_col);
      },
      error: function() {
        console.error('Error al actualizar turno');
      }
    });
  }
  
  // Función para autoasignar colaborador (si se desea)
  function obtenerSiguienteColaborador() {
    var tipousuario = $("#tipousuario").val();
    
    // Si es colaborador (tipo 4), se autoasigna
    if (tipousuario == 4) {
      var id_col = $("#idcol").val();
      $("#col_asignado").prop("disabled", true);
      $("#col_asignado").val(id_col);
      $("#col_asignado").selectpicker("refresh");
      return;
    }
    
    // Para otros usuarios, obtener siguiente en turno
    $.ajax({
      url: "bd/asignar_colaborador.php",
      type: "GET",
      dataType: "json",
      success: function (data) {
        if (data.success) {
          $("#col_asignado").val(data.id_col);
          $("#col_asignado").selectpicker("refresh");
        } else {
          console.error("Error al asignar colaborador:", data.error);
        }
      },
      error: function () {
        console.error("Error al conectar para asignar colaborador");
      }
    });
  }
  
  // Si quieres que la asignación sea automática, descomenta la siguiente línea:
  obtenerSiguienteColaborador();
});
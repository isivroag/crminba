$(document).ready(function () {
  var tablaVis;
  var id, opcion;
  opcion = 4;
  var fila;

  // Inicialización de DataTable
  tablaVis = $("#tablaV").DataTable({
    responsive: true,
    columnDefs: [
      { targets: [0, 8, 14, 15], className: "text-center" },
      { targets: 15, orderable: false, searchable: false },
      { targets: [2, 3, 4, 5, 6, 7, 9, 12, 13], className: "hide_column" },
    ],
    language: {
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      infoFiltered: "(filtrado de un total de _MAX_ registros)",
      sSearch: "Buscar:",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior",
      },
      sProcessing: "Procesando...",
    },
  });

  // Validación de teléfonos (solo números)
  $("#tel_cel, #tel_casa, #tel_trab").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });

  // Validación de CP (solo números)
  $("#dir_cp").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });

  // Validación de RFC (formato básico)
  $("#rfc").on("input", function () {
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, "");
  });

  // Función para convertir a mayúsculas automáticamente
  function mayusculasEspanol(texto) {
    return texto.toUpperCase();
  }

  // Aplicar mayúsculas a campos de texto
  $("#nombre, #dir_calle, #dir_colonia, #dir_ciudad, #dir_edo, #banco").on(
    "blur",
    function () {
      $(this).val(mayusculasEspanol($(this).val()));
    }
  );

  // Botón Nuevo
  $("#btnNuevo").click(function () {
    $("#formDatos").trigger("reset");
    $(".modal-header").css("background-color", "#28a745");
    $(".modal-title").text("NUEVO CLIENTE");
    $("#modalCRUD").modal("show");
    id = null;
    opcion = 1; // Crear
  });

  // Botón Editar
  $(document).on("click", ".btnEditar", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    var nombre = fila.find("td:eq(1)").text();
    var dir_calle = fila.find("td:eq(2)").text();
    var dir_ciudad = fila.find("td:eq(3)").text();
    var dir_edo = fila.find("td:eq(4)").text();
    var dir_cp = fila.find("td:eq(5)").text();
    var folio = fila.find("td:eq(6)").text();
    var nacionalidad = fila.find("td:eq(7)").text();
    var tel_cel = fila.find("td:eq(8)").text();
    var tel_casa = fila.find("td:eq(9)").text();
    var email = fila.find("td:eq(10)").text();
    var rfc = fila.find("td:eq(11)").text();
    var banco = fila.find("td:eq(12)").text();
    var cuenta = fila.find("td:eq(13)").text();
    var especial_badge = fila.find("td:eq(14)").text().trim();

    // Obtener datos adicionales via AJAX
    $.ajax({
      url: "bd/get_cliente.php",
      type: "POST",
      dataType: "json",
      data: { id_clie: id },
      success: function (response) {
        if (response.success) {
          var cliente = response.data;

          $("#nombre").val(cliente.nombre);
          $("#dir_calle").val(cliente.dir_calle);
          $("#dir_ciudad").val(cliente.dir_ciudad);
          $("#dir_colonia").val(cliente.dir_colonia);
          $("#dir_edo").val(cliente.dir_edo);
          $("#dir_cp").val(cliente.dir_cp);
          $("#folio").val(cliente.folio);
          $("#nacionalidad").val(cliente.nacionalidad);
          $("#tel_cel").val(cliente.tel_cel);
          $("#tel_casa").val(cliente.tel_casa);
          $("#tel_trab").val(cliente.tel_trab);
          $("#email").val(cliente.email);
          $("#rfc").val(cliente.rfc);
          $("#banco").val(cliente.banco);
          $("#cuenta").val(cliente.cuenta);
          $("#especial").prop("checked", cliente.especial == 1);
        }
      },
    });

    opcion = 2; // Editar
    $(".modal-header").css("background-color", "#17a2b8");
    $(".modal-title").text("EDITAR CLIENTE");
    $("#modalCRUD").modal("show");
  });

  // Botón Borrar
  $(document).on("click", ".btnBorrar", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());
    var nombre = fila.find("td:eq(1)").text();

    Swal.fire({
      title: "¿Eliminar cliente?",
      text: "Se eliminará el cliente: " + nombre,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "bd/crudcliente.php",
          type: "POST",
          dataType: "json",
          data: {
            id_clie: id,
            opcion: 3,
          },
          success: function (data) {
            if (data.success) {
              Swal.fire(
                "¡Eliminado!",
                "El cliente ha sido eliminado",
                "success"
              );
              tablaVis.row(fila).remove().draw();
            } else {
              Swal.fire("Error", data.message || "Error al eliminar", "error");
            }
          },
          error: function () {
            Swal.fire("Error", "Error de conexión", "error");
          },
        });
      }
    });
  });

  // Guardar Cliente (Crear o Editar)
  $("#btnGuardar").click(function () {
    var nombre = $.trim($("#nombre").val());
    var dir_calle = $.trim($("#dir_calle").val());
    var dir_ciudad = $.trim($("#dir_ciudad").val());
    var dir_colonia = $.trim($("#dir_colonia").val());
    var dir_edo = $.trim($("#dir_edo").val());
    var dir_cp = $.trim($("#dir_cp").val());
    var folio = $.trim($("#folio").val());
    var nacionalidad = $("#nacionalidad").val();
    var tel_cel = $.trim($("#tel_cel").val());
    var tel_casa = $.trim($("#tel_casa").val());
    var tel_trab = $.trim($("#tel_trab").val());
    var email = $.trim($("#email").val());
    var rfc = $.trim($("#rfc").val());
    var banco = $.trim($("#banco").val());
    var cuenta = $.trim($("#cuenta").val());
    var especial = $("#especial").is(":checked") ? 1 : 0;

    // Validaciones
    if (!nombre || !dir_calle || !email || !tel_cel) {
      Swal.fire("Error", "Los campos marcados con * son obligatorios", "error");
      return;
    }

    // Validar email
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      Swal.fire("Error", "Ingrese un email válido", "error");
      return;
    }

    // Validar teléfono celular
    var telValido = /^(\+[0-9]{1,3})?[0-9]{10}$/.test(tel_cel);
    if (!telValido) {
      Swal.fire(
        "Error",
        "El teléfono debe tener 10 dígitos para nacional o formato internacional con +código (ej: +521234567890)",
        "error"
      );
      return;
    } else {
      let telefonoNormalizado = tel_cel;
      if (!tel_cel.startsWith("+")) {
        telefonoNormalizado = "+52" + tel_cel;
        tel_cel = telefonoNormalizado; // Actualizar el teléfono con el normalizado
      }
    }

    // Validar RFC si se proporciona
    if (rfc && (rfc.length < 12 || rfc.length > 13)) {
      Swal.fire("Error", "El RFC debe tener 12 o 13 caracteres", "error");
      return;
    }
    console.log("Datos a enviar:", {
      nombre,
      dir_calle,
      dir_ciudad,
      dir_colonia,
      dir_edo,
      dir_cp,
      folio,
      nacionalidad,
      tel_cel,
      tel_casa,
      tel_trab,
      email,
      rfc,
      banco,
      cuenta,
      especial,
      id,
      opcion,
    });

    $.ajax({
      url: "bd/crudcliente.php",
      type: "POST",
      dataType: "json",
      data: {
        id_clie: id,
        nombre: nombre,
        dir_calle: dir_calle,
        dir_ciudad: dir_ciudad,
        dir_colonia: dir_colonia,
        dir_edo: dir_edo,
        dir_cp: dir_cp,
        folio: folio,
        nacionalidad: nacionalidad,
        tel_cel: tel_cel,
        tel_casa: tel_casa,
        tel_trab: tel_trab,
        email: email,
        rfc: rfc,
        banco: banco,
        cuenta: cuenta,
        especial: especial,
        opcion: opcion,
      },
      success: function (data) {
        if (data.success) {
          var especialBadge =
            especial == 1
              ? '<span class="badge badge-warning">ESPECIAL</span>'
              : '<span class="badge badge-secondary">NORMAL</span>';

          var acciones =
            '<div class="btn-group">' +
            '<button class="btn btn-warning btn-sm btnEditar" data-toggle="tooltip" title="Editar">' +
            '<i class="fas fa-edit"></i></button>' +
            '<button class="btn btn-danger btn-sm btnBorrar" data-toggle="tooltip" title="Eliminar">' +
            '<i class="fas fa-trash-alt"></i></button></div>';

          if (opcion == 1) {
            // Agregar nueva fila
            tablaVis.row
              .add([
                data.id_clie,
                nombre,
                dir_calle,
                dir_ciudad,
                dir_edo,
                dir_cp,
                folio,
                nacionalidad,
                tel_cel,
                tel_casa,
                email,
                rfc,
                banco,
                cuenta,
                especialBadge,
                acciones,
              ])
              .draw();
          } else {
            // Actualizar fila existente
            tablaVis
              .row(fila)
              .data([
                id,
                nombre,
                dir_calle,
                dir_ciudad,
                dir_edo,
                dir_cp,
                folio,
                nacionalidad,
                tel_cel,
                tel_casa,
                email,
                rfc,
                banco,
                cuenta,
                especialBadge,
                acciones,
              ])
              .draw();
          }

          $("#modalCRUD").modal("hide");
          Swal.fire("¡Éxito!", data.message, "success");
        } else {
          Swal.fire("Error", data.message || "Error al guardar", "error");
        }
      },
      error: function () {
        Swal.fire("Error", "Error de conexión", "error");
      },
    });
  });
});

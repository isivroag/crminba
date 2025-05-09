$(document).ready(function () {
  var id, opcion;
  opcion = 4; // Valor por defecto para operaciones

  // Inicialización de DataTable
  var tablaVis = $("#tablaV").DataTable({
    dom:
      "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    buttons: [
      {
        extend: "excelHtml5",
        text: "<i class='fas fa-file-excel'> Excel</i>",
        titleAttr: "Exportar a Excel",
        title: "Listado de Prospectos",
        className: "btn bg-success",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='far fa-file-pdf'> PDF</i>",
        titleAttr: "Exportar a PDF",
        title: "Listado de Prospectos",
        className: "btn bg-danger",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] },
      },
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

  // Tooltips
  $('[data-toggle="tooltip"]').tooltip();

  // Manejo del modal para accesibilidad
  $("#modalCRUD").on("show.bs.modal", function () {
    $(this).removeAttr("aria-hidden");
    $(this).attr("aria-modal", "true");
    $("body").addClass("modal-open");
  });

  $("#modalCRUD").on("shown.bs.modal", function () {
    $("#nombre").focus();
  });

  $("#modalCRUD").on("hidden.bs.modal", function () {
    $("body").removeClass("modal-open");
  });

  $("#modalConvertir").on("show.bs.modal", function () {
    $(this).removeAttr("aria-hidden");
    $(this).attr("aria-modal", "true");
    $("body").addClass("modal-open");
  });

  $("#modalConvertir").on("shown.bs.modal", function () {
    $("#rfc").focus();
  });

  $("#modalConvertir").on("hidden.bs.modal", function () {
    $("body").removeClass("modal-open");
  });

  // Botón Nuevo Prospecto
  $("#btnNuevo").click(function () {
    $("#formDatos").trigger("reset");
    $(".modal-header").css("background-color", "#007bff"); // Azul para nuevo
    $(".modal-title").text("NUEVO PROSPECTO");

    // Obtener el siguiente colaborador en el turno
    obtenerSiguienteColaborador();

    id = null;
    opcion = 1; // Opción 1: Crear nuevo
    $("#modalCRUD").modal("show");
  });

  // Función para obtener el siguiente colaborador en el turno
  function obtenerSiguienteColaborador() {
    var tipousuario = $("#tipousuario").val();

    // Si es colaborador (tipo 4), se autoasigna
    if (tipousuario == 4) {
      id_col = $("#idcol").val();
      console.log(id_col);
      $("#col_asignado").prop("disabled", true);
      $("#col_asignado").val(id_col);
      $("#col_asignado").selectpicker("refresh");
      return; // Termina la función aquí
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
          Swal.fire(
            "Error",
            data.error || "Error al asignar colaborador",
            "error"
          );
        }
      },
      error: function () {
        Swal.fire("Error", "No se pudo conectar al servidor", "error");
      },
    });
  }

  var fila;
  // Botón Editar Prospecto
  $(document).on("click", ".btnEditar", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    // Obtener datos de la fila
    var nombre = fila.find("td:eq(1)").text();
    var telefono = fila.find("td:eq(2)").text();
    var correo = fila.find("td:eq(3)").text();
    var col_asignado = fila.find("td:eq(4)").text();

    // Llenar el formulario
    $("#nombre").val(nombre);
    $("#telefono").val(telefono);
    $("#correo").val(correo);

    // Buscar el ID del colaborador asignado
    var select = $("#col_asignado");
    select.find("option").each(function () {
      if ($(this).text() === col_asignado) {
        select.val($(this).val());
        select.selectpicker("refresh");
        return false; // Salir del bucle
      }
    });

    opcion = 2; // Opción 2: Editar
    $(".modal-header").css("background-color", "#17a2b8"); // Turquesa para editar
    $(".modal-title").text("EDITAR PROSPECTO");
    $("#modalCRUD").modal("show");
  });

  // Botón Convertir a Cliente
  $(document).on("click", ".btnConvertir", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    $("#id_prospecto").val(id);
    $("#formCliente").trigger("reset");
    $("#modalConvertir").modal("show");
  });

  // Botón Descartar Prospecto
  $(document).on("click", ".btnDescartar", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    Swal.fire({
      title: "¿Descartar prospecto?",
      text: "Esta acción no se puede deshacer",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Sí, descartar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        opcion = 3; // Opción 3: Descartar

        $.ajax({
          url: "bd/crudprospecto.php",
          type: "POST",
          dataType: "json",
          data: { id: id, opcion: opcion },
          success: function (data) {
            if (data.success) {
              tablaVis.row(fila).remove().draw();
              Swal.fire("Éxito", "Prospecto descartado", "success");
            }
          },
        });
      }
    });
  });
  // Botón Enviar Correo
  $(document).on("click", ".btnEnviar", function () {
    fila = $(this).closest("tr");
    id_pros = parseInt(fila.find("td:eq(0)").text());
    nombre_pros = fila.find("td:eq(1)").text();
    telefono_pros = fila.find("td:eq(2)").text();
    correo_pros = fila.find("td:eq(3)").text();
    nombre_colab = fila.find("td:eq(4)").text();
    console.log(id_pros, nombre_pros, telefono_pros, correo_pros, nombre_colab);
    Swal.fire({
      title: "¿Enviar recordatorio?",
      text: "Se enviará un correo al colaborador asignado",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Sí, enviar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.value) {
        console.log("Enviando correo a: " + correo_pros);
        $.ajax({
            url: "bd/usarapicorreo.php",
            type: "POST",
            dataType: "json",
            contentType: "application/json", // <- esto es clave
            data: JSON.stringify({
              id_pros: id_pros,
              nombre: nombre_pros,
              telefono: telefono_pros,
              correo: correo_pros,
              colaborador: nombre_colab,
            }),
            success: function (response) {
              Swal.fire({
                title: response.message,
                icon: response.success ? "success" : "error",
              });
            },
            error: function () {
              Swal.fire({
                title: "Error",
                text: "No se pudo conectar al servidor",
                icon: "error",
              });
            },
          });
          
      }
    });
  });
  // Guardar Prospecto (Crear o Editar)
  $(document).on("click", "#btnGuardar", function () {
    var nombre = $.trim($("#nombre").val());
    var telefono = $.trim($("#telefono").val());
    var correo = $.trim($("#correo").val());
    var col_asignado = $("#col_asignado").val();

    // Validaciones...

    $.ajax({
      url: "bd/crudprospecto.php",
      type: "POST",
      dataType: "json",
      data: {
        nombre: nombre,
        telefono: telefono,
        correo: correo,
        col_asignado: col_asignado,
        id: id,
        opcion: opcion,
      },
      success: function (data) {
        if (data.success) {
          // Solo actualizar turno si no es autoasignación de colaborador
          var tipousuario = $("#tipousuario").val();
          if (tipousuario != 4) {
            actualizarTurnoColaborador(col_asignado);
          }

          // Resto del código de éxito...
          $("#modalCRUD").modal("hide");

          if (opcion == 1) {
            tablaVis.row
              .add([
                data.id_pros,
                data.nombre,
                data.telefono,
                data.correo,
                data.nombre_colaborador,
                data.fecha_registro,
                '<span class="badge badge-asignado">Asignado</span>',
                generarBotonesAccion(data.id_pros),
              ])
              .draw();
          } else {
            tablaVis
              .row(fila)
              .data([
                data.id_pros,
                data.nombre,
                data.telefono,
                data.correo,
                data.nombre_colaborador,
                data.fecha_registro,
                '<span class="badge badge-asignado">Asignado</span>',
                generarBotonesAccion(data.id_pros),
              ])
              .draw();
          }

          Swal.fire("Éxito", data.message, "success");
        }
      },
    });
  });

  function actualizarTurnoColaborador(id_col) {
    $.ajax({
      url: "bd/actualizar_turno.php",
      type: "POST",
      data: { id_col: id_col },
      success: function () {
        console.log("Turno actualizado para colaborador:", id_col);
      },
    });
  }

  // Convertir a Cliente
  $(document).on("click", "#btnConvertir", function () {
    var id_prospecto = $("#id_prospecto").val();
    var rfc = $.trim($("#rfc").val());
    var direccion = $.trim($("#direccion").val());
    var fecha_nacimiento = $("#fecha_nacimiento").val();

    $.ajax({
      url: "bd/convertir_cliente.php",
      type: "POST",
      dataType: "json",
      data: {
        id_prospecto: id_prospecto,
        rfc: rfc,
        direccion: direccion,
        fecha_nacimiento: fecha_nacimiento,
      },
      success: function (data) {
        if (data.success) {
          $("#modalConvertir").modal("hide");

          // Actualizar fila en la tabla
          var fila = tablaVis.row("#" + id_prospecto);
          fila.data()[6] =
            '<span class="badge badge-convertido">Convertido</span>';

          // Remover botones de acción
          fila.data()[7] =
            '<div class="btn-group">' +
            '<button class="btn btn-sm btn-primary btnEditar" data-toggle="tooltip" title="Editar">' +
            '<i class="fas fa-edit"></i>' +
            "</button>" +
            '<button class="btn btn-sm btn-secondary" disabled>' +
            '<i class="fas fa-user-check"></i>' +
            "</button>" +
            '<button class="btn btn-sm btn-secondary" disabled>' +
            '<i class="fas fa-trash-alt"></i>' +
            "</button>" +
            "</div>";

          tablaVis.row(fila).draw();

          Swal.fire(
            "Éxito",
            "Prospecto convertido a cliente exitosamente",
            "success"
          );
        }
      },
      error: function () {
        Swal.fire("Error", "Error al convertir a cliente", "error");
      },
    });
  });

  // Función para validar email
  function validarEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  // Función para generar botones de acción
  function generarBotonesAccion(id) {
    return (
      '<div class="btn-group">' +
      '<button class="btn btn-sm btn-primary btnEditar" data-toggle="tooltip" title="Editar">' +
      '<i class="fas fa-edit"></i>' +
      "</button>" +
      '<button class="btn btn-sm btn-success btnConvertir" data-toggle="tooltip" title="Convertir a Cliente">' +
      '<i class="fas fa-user-check"></i>' +
      "</button>" +
      '<button class="btn btn-sm btn-danger btnDescartar" data-toggle="tooltip" title="Descartar Prospecto">' +
      '<i class="fas fa-trash-alt"></i>' +
      "</button>" +
      "</div>"
    );
  }
});

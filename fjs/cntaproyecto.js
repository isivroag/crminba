$(document).ready(function () {
  var id, opcion;
  opcion = 4; // Valor por defecto para operaciones
var textcolumnas = permisos();

  function permisos() {
    var tipousuario = parseInt($("#tipousuario").val());
    var columnas = "";
    console.log("Tipo de usuario:", tipousuario);

    switch (tipousuario) {
      case 1: // usuario normal
        columnas =
         "";
        break;
      case 2: // usuario administrador
      case 3: // usuario supervisor
        columnas =
          "<div class='text-center btn-group'>\
          <button class='btn btn-sm btn-success btnEstructura' data-toggle='tooltip' data-placement='top' title='Estructura'><i class='fa-solid fa-map'></i></button>\
          </div>";
          /*<button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' data-placement='top' title='Editar'><i class='fas fa-edit'></i></button>\*/
        break;
      case 4: // usuario colaborador
        columnas =
         "";

        break;
      case 5: // usuario capturista
        columnas = "";
        break;
      default:
        columnas = "";

        break;
    }
    return columnas;
  }
  // Inicialización de DataTable
  var tablaVis = $("#tablaV").DataTable({
    dom:
      "<'row justify-content-between'<'col-sm-6'l><'col-sm-6'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-6'i>>",
   
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
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:textcolumnas
         
      },
      { className: "hide_column", targets: [3] },
      { className: "hide_column", targets: [4] },
      { className: "hide_column", targets: [5] },
      { className: "hide_column", targets: [6] },
      { className: "hide_column", targets: [7] },
      { className: "hide_column", targets: [8] },
      { className: "hide_column", targets: [9] },
      { className: "hide_column", targets: [10] },
      { className: "hide_column", targets: [11] },
    ],
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


  var fila;
  // Botón Editar Prospecto
  $(document).on("click", ".btnEstructura", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    window.location.href = "cntaestructura.php?id_proy=" + id;
  });

  // Botón Convertir a Cliente
  $(document).on("click", ".btnSegumiento", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

   
    window.location.href = "seguimiento.php?id_pros=" + id;
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
    var origen = $("#origen").val();

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
        origen: origen,
      },
      success: function (data) {
        if (data.success) {
          // var tipousuario = $("#tipousuario").val();
          if (opcion == 1) {
            actualizarTurnoColaborador(col_asignado);
          }

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
                formatoOrigen(origen),
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
                formatoOrigen(origen),
              ])
              .draw();
          }

          // Enviar correo automáticamente según sea alta o modificación
          Swal.fire({
            title: "¿Enviar correo al colaborador?",
            text: "¿Deseas notificar al colaborador sobre las modificaciones realizadas?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, enviar",
            cancelButtonText: "No enviar",
          }).then((result) => {
            if (result.value) {
              console.log("Enviando correo a: " + correo);
              let urlCorreo =
                opcion == 1 ? "bd/usarapicorreo.php" : "bd/usarapicorreo2.php";

              $.ajax({
                url: urlCorreo,
                type: "POST",
                dataType: "json",
                contentType: "application/json",
                data: JSON.stringify({
                  id_pros: data.id_pros,
                  nombre: data.nombre,
                  telefono: data.telefono,
                  correo: data.correo,
                  colaborador: data.nombre_colaborador,
                }),
                success: function (resp) {
                  Swal.fire({
                    title: "Éxito",
                    text: resp.message,
                    icon: resp.success ? "success" : "warning",
                  });
                },
                error: function () {
                  Swal.fire(
                    "Error",
                    "No se pudo enviar el correo al colaborador",
                    "error"
                  );
                },
              });
            } else {
              Swal.fire("Éxito", data.message, "success");
            }
          });
        }
      },
    });
  });


  // Función para generar botones de acción
});

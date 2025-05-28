$(document).ready(function () {
  var id, opcion;
  opcion = 4; // Valor por defecto para operaciones
  var textcolumnas = permisos();

  function permisos() {
    var tipousuario =parseInt( $("#tipousuario").val());
    var columnas = "";
    console.log("Tipo de usuario:", tipousuario);

    switch (tipousuario) {
      case 1: // usuario normal
        columnas =
          "<div class='text-center btn-group'>\
          <button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' data-placement='top' title='Editar'><i class='fas fa-edit'></i></button>\
           <button class='btn btn-sm btn-secondary btnEnviar' data-toggle='tooltip' data-placement='top' title='Correo'><i class='fas fa-envelope'></i></button>\
           </div>";
        break;
      case 2: // usuario administrador
      case 3: // usuario supervisor
        columnas =
          "<div class='text-center btn-group'>\
          <button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' data-placement='top' title='Editar'><i class='fas fa-edit'></i></button>\
           <button class='btn btn-sm btn-secondary btnEnviar' data-toggle='tooltip' data-placement='top' title='Correo'><i class='fas fa-envelope'></i></button>\
           <button class='btn btn-sm bg-purple btnSegumiento' data-toggle='tooltip' data-placement='top' title='Registrar Seguimiento'><i class='fas fa-phone'></i></button>\
           <button class='btn btn-sm bg-orange btnHistoria' data-toggle='tooltip' data-placement='top' title='Ver Historial'><i class='fa-solid fa-book'></i></button>\
           <button class='btn btn-sm bg-danger btnEliminar' data-toggle='tooltip' data-placement='top' title='Eliminar'><i class='fa-solid fa-trash'></i></button>\
           </div>";
           break;
      case 4: // usuario colaborador
        columnas =
          "<div class='text-center btn-group'>\
          <button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' data-placement='top' title='Editar'><i class='fas fa-edit'></i></button>\
           <button class='btn btn-sm bg-purple btnSegumiento' data-toggle='tooltip' data-placement='top' title='Registrar Seguimiento'><i class='fas fa-phone'></i></button>\
           <button class='btn btn-sm bg-orange btnHistoria' data-toggle='tooltip' data-placement='top' title='Ver Historial'><i class='fa-solid fa-book'></i></button>\
           </div>";

        break;
        case 5: // usuario capturista
        columnas ="";
        break;
      default:
        columnas ="";
       
        break;
    }
    return columnas;
  }

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
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent: textcolumnas,
      },
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
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    // Obtener datos de la fila
    var nombre = fila.find("td:eq(1)").text();
    var telefono = fila.find("td:eq(2)").text();
    var correo = fila.find("td:eq(3)").text();

    // Obtener valor del atributo data-origen del <td> que contiene el canal de comunicación
    var origen = fila.find("td[data-origen]").data("origen"); // Ej: "facebook"
    var col_asignado = fila.find("td:eq(4)").text();

    // Rellenar el formulario con los datos obtenidos
    $("#id_pros").val(id);
    $("#nombre").val(nombre);
    $("#telefono").val(telefono);
    $("#correo").val(correo);

    // Asignar el valor al selectpicker
    $("#origen").selectpicker("val", origen);

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
  $(document).on("click", ".btnSegumiento", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    window.location.href = "seguimiento.php?id_pros=" + id;
  });

  $(document).on("click", ".btnHistoria", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    window.location.href = "cntahistorial.php?id_pros=" + id;
  });

  // Botón Descartar Prospecto
  $(document).on("click", ".btnEliminar", function () {
    var fila = $(this).closest("tr");
    var id = parseInt(fila.find("td:eq(0)").text());
    console.log("ID a eliminar:", id);
    Swal.fire({
      title: "¿Eliminar prospecto?",
      text: "Esta acción no se puede deshacer",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.value) {
        // Primero verificar si tiene seguimientos
        $.ajax({
          url: "bd/crudprospecto.php",
          type: "POST",
          dataType: "json",
          data: {
            id: id,
            opcion: 4, // Nueva opción para verificar seguimientos
          },
          success: function (response) {
            console.log("Respuesta de verificación:", response);
            if (response.success && response.count > 0) {
              // Mostrar error si tiene seguimientos
              Swal.fire({
                icon: "error",
                title: "No se puede eliminar",
                html: `Este prospecto tiene <b>${response.count}</b> registros de seguimiento asociados.<br><br>Primero elimine los seguimientos antes de eliminar el prospecto.`,
              });
            } else {
              // Proceder con eliminación si no tiene seguimientos
              eliminarProspecto(id, fila);
            }
          },
          error: function (xhr, status, error) {
            console.error("Error AJAX:", xhr.responseText);
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Error al verificar seguimientos",
            });
          },
        });
      }
    });
  });

  function eliminarProspecto(id, fila) {
    $.ajax({
      url: "bd/crudprospecto.php",
      type: "POST",
      dataType: "json",
      data: {
        id: id,
        opcion: 3, // Opción 3: Eliminar
      },
      success: function (data) {
        if (data.success) {
          tablaVis.row(fila).remove().draw();
          Swal.fire("Éxito", "Prospecto eliminado correctamente", "success");
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: data.message || "Error al eliminar prospecto",
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error al conectar con el servidor",
        });
      },
    });
  }
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
    console.log(
      "Datos a enviar:",
      nombre,
      telefono,
      correo,
      col_asignado,
      origen
    );

    // Validación básica
    if (!nombre || !telefono || !correo || !col_asignado || !origen) {
      Swal.fire("Advertencia", "Todos los campos son obligatorios", "warning");
      return;
    }

    // Validar estructura del correo
    var correoValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);
    if (!correoValido) {
      Swal.fire("Correo inválido", "Ingresa un correo válido", "warning");
      return;
    }

    // Validar estructura del teléfono (ejemplo: solo 10 dígitos numéricos)
    var telefonoValido = /^[0-9]{10}$/.test(telefono);
    if (!telefonoValido) {
      Swal.fire(
        "Teléfono inválido",
        "El número debe tener 10 dígitos numéricos",
        "warning"
      );
      return;
    }

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

  function actualizarTurnoColaborador(id_col) {
    console.log("Actualizando turno para colaborador:", id_col);
    $.ajax({
      url: "bd/actualizar_turno.php",
      type: "POST",
      data: { id_col: id_col },
      success: function () {
        console.log("Turno actualizado para colaborador:", id_col);
      },
    });
  }

  function formatoOrigen(origen) {
    origen = origen.toLowerCase();
    switch (origen) {
      case "facebook":
        return '<td data-origen="facebook"><i class="fab fa-facebook text-primary"></i> Facebook</td>';
      case "instagram":
        return '<td data-origen="instagram"><i class="fab fa-instagram text-danger"></i> Instagram</td>';
      case "web":
        return '<td data-origen="web"><i class="fas fa-globe text-info"></i> Web</td>';
      case "whatsapp":
        return '<td data-origen="whatsapp"><i class="fab fa-whatsapp text-success"></i> WhatsApp</td>';
      case "llamada":
        return '<td data-origen="llamada"><i class="fas fa-phone text-dark"></i> Llamada</td>';
      default:
        return `<td data-origen="${origen}">${origen.charAt(0).toUpperCase() + origen.slice(1)}</td>`;
    }
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
});

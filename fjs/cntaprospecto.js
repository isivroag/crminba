$(document).ready(function () {
  var id, opcion;
  opcion = 4; // Valor por defecto para operaciones
  var textcolumnas = permisos();

  $("#telefono").on("input", function () {
    let $input = $(this);
    let value = $input.val();
    let cursorPos = this.selectionStart;

    // Limpiar (solo números y +)
    let newValue = value.replace(/[^0-9+]/g, "");

    // Asegurar que el + solo esté al inicio
    if (newValue.includes("+")) {
      newValue = "+" + newValue.replace(/\+/g, "");
    }

    // Actualizar solo si hubo cambios
    if (value !== newValue) {
      $input.val(newValue);
      // Mantener posición del cursor
      this.setSelectionRange(cursorPos, cursorPos);
    }
  });

  function permisos() {
    var tipousuario = parseInt($("#tipousuario").val());
    var columnas = "";

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
          <button class='btn btn-sm btn-success btnConvertir' data-toggle='tooltip' data-placement='top' title='Convertir a Cliente'><i class='fas fa-user'></i></button>\
           <button class='btn btn-sm bg-purple btnSegumiento' data-toggle='tooltip' data-placement='top' title='Registrar Seguimiento'><i class='fas fa-phone'></i></button>\
           <button class='btn btn-sm bg-orange btnHistoria' data-toggle='tooltip' data-placement='top' title='Ver Historial'><i class='fa-solid fa-book'></i></button>\
           </div>";
        //<button class='btn btn-sm btn-secondary btnEnviar' data-toggle='tooltip' data-placement='top' title='Correo'><i class='fas fa-envelope'></i></button>\
        //<button class='btn btn-sm bg-danger btnEliminar' data-toggle='tooltip' data-placement='top' title='Eliminar'><i class='fa-solid fa-trash'></i></button>\
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
    },{
      targets:9,className: "hide_column"
    },{
      targets: 10,className: "text-center"
    
      }
    

  ],
  rowCallback: function (row, data) {
    var telefono = data[2];
    if (telefono && typeof formatearTelefono === "function") {
      var telefonoFormateado = formatearTelefono(telefono);
      $("td:eq(2)", row).text(telefonoFormateado);
    }

    var estado = data[9];
    var botones = "";
    if (estado == 1) {
      botones += `
        <button class='btn btn-sm btn-danger btnInactivar' data-toggle='tooltip' title='Suspender Prospecto'>
          <i class='fas fa-ban'></i>
        </button>`;
    } else if (estado == 3) {
      botones += `
        <button class='btn btn-sm btn-primary btnActivar' data-toggle='tooltip' title='Activar Prospecto'>
          <i class='fas fa-check'></i>
        </button>`;
    }
    $("td:last", row).append(botones);
  },
});

$("#chkInactivos").on("change", function () {
  window.location.href = "cntaprospecto.php?estado=" + ($(this).prop("checked") ? "todos" : "activos");
});

   $(document).on("click", ".btnEliminar", function () {
    var fila = $(this).closest("tr");
    var id = parseInt(fila.find("td:eq(0)").text());

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
  // Inactivar prospecto
  $(document).on("click", ".btnInactivar", function () {
    var fila = $(this).closest("tr");
    var id = parseInt(fila.find("td:eq(0)").text());
  

    Swal.fire({
      title: "Suspender Prospecto?",
      text: "Este prospecto pasará a la lista de inactivos",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, suspender",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: "bd/crudprospecto.php",
          type: "POST",
          dataType: "json",
          data: {
            id: id,
            opcion: 5, // Opción personalizada para inactivar
          },
          success: function (response) {
            if (response.success ) {
         
              // Actualizar la columna de estado en la tabla
              fila.find("td:eq(10)").html('<span class="badge bg-danger">Inactivo</span>');
              Swal.fire("Hecho", "El prospecto fue inactivado", "success");
            } else {
              Swal.fire("Error", response.message || "No se pudo inactivar", "error");
            }
          },
           error: function (xhr, status, error) {
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

  // Activar prospecto
  $(document).on("click", ".btnActivar", function () {
    var fila = $(this).closest("tr");
    var id = parseInt(fila.find("td:eq(0)").text());
    opcion=6;

    Swal.fire({
      title: "¿Activar Prospecto?",
      text: "Este prospecto volverá a la lista de activos",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sí, activar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: "bd/crudprospecto.php",
          type: "POST",
          dataType: "json",
          data: {
            id: id,
            opcion: 6, // Opción personalizada para activar
          },
          success: function (response) {
            if (response.success) {
              console.log("Prospecto activado:", id);
              // Actualizar la columna de estado en la tabla
              fila.find("td:eq(10)").html('<span class="badge badge-asignado">Activo</span>');
              Swal.fire("Hecho", "El prospecto fue activado", "success");
            } else {
              Swal.fire("Error", response.message || "No se pudo activar", "error");
            }
          },
           error: function (xhr, status, error) {
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
    var interes_val = fila.find("td:eq(8)").text().trim();

    // Rellenar el formulario con los datos obtenidos
    $("#id_pros").val(id);
    $("#nombre").val(nombre);
    $("#telefono").val(telefono);
    $("#correo").val(correo);

    // Asignar el valor al selectpicker

    $("#origen").selectpicker("val", origen);
    /*
    var select = $("#origen");
    select.find("option").each(function () {
      if ($(this).text() === origen) {
        select.val($(this).val());
        select.selectpicker("refresh");
        return false; // Salir del bucle
      }
    });
*/
    // Buscar el ID del colaborador asignado
    var select = $("#col_asignado");
    select.find("option").each(function () {
      if ($(this).text() === col_asignado) {
        select.val($(this).val());
        select.selectpicker("refresh");
        return false; // Salir del bucle
      }
    });

    // --- Lógica para el campo interés ---
    var existeOpcion = false;
    $("#interes_select option").each(function () {
      if ($(this).val() === interes_val) {
        existeOpcion = true;
        return false; // break
      }
    });

    if (existeOpcion) {
      $("#interes_select").val(interes_val);
      $("#interes").val(interes_val).hide();
    } else {
      $("#interes_select").val("otro");
      $("#interes").val(interes_val).show();
    }

    // Refrescar selectpicker si usas bootstrap-select
    // $("#interes_select").trigger("change");

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

  $("#formDatos").on("submit", function (e) {
    e.preventDefault(); // Evita recargar la página
  });

  // Guardar Prospecto (Crear o Editar)
  $(document).on("click", "#btnGuardar", async function () {
    var nombre = $.trim($("#nombre").val());
    var telefono = $.trim($("#telefono").val());
    var correo = $.trim($("#correo").val());
    var col_asignado = $("#col_asignado").val();
    var origen = $("#origen").val();
    var id_usuario = $("#iduser").val();
    var interes = $("#interes").val();

    // Validación básica
    if (!nombre || !col_asignado || !origen) {
      Swal.fire(
        "Advertencia",
        "Los campos nombre, colaborador asignado y origen son obligatorios",
        "warning"
      );
      return;
    }

    // Validar correo solo si se proporcionó
    if (correo && correo.trim() !== "") {
      var correoValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);
      if (!correoValido) {
        Swal.fire("Correo inválido", "Ingresa un correo válido", "warning");
        return;
      }
    }

    // Validar teléfono solo si se proporcionó
    if (telefono && telefono.trim() !== "") {
      // Validar teléfono (10 dígitos o formato internacional con +)
      var telefonoValido = /^(\+[0-9]{1,3})?[0-9]{10}$/.test(telefono);
      if (!telefonoValido) {
        Swal.fire(
          "Teléfono inválido",
          "Formato aceptado: 10 dígitos para nacional o +código para internacional (ej: +521234567890)",
          "warning"
        );
        return;
      } else {
        try {
          // Normalizar el teléfono (agregar +52 si es nacional)
          let telefonoNormalizado = telefono;
          if (!telefono.startsWith("+")) {
            telefonoNormalizado = "+52" + telefono;
          }

          // Esperar la validación del teléfono
          const respuesta = await $.ajax({
            url: "bd/buscartelprospecto.php",
            type: "POST",
            dataType: "json",
            data: {
              telefono: telefonoNormalizado, // Enviamos el teléfono normalizado
              opcion: opcion,
              id: id,
            },
          });

          if (respuesta == 1) {
            await Swal.fire({
              icon: "warning",
              title: "Teléfono ya registrado",
              text: "El teléfono ingresado ya está asociado a otro prospecto.",
            });
            return; // Detener el proceso
          }
          telefono = telefonoNormalizado; // Actualizar el teléfono con el normalizado
        } catch (error) {
          console.error("Error al validar teléfono:", error);
          return;
        }
      }
    }

    // Validar que al menos uno (teléfono o correo) esté presente
    if (!telefono && !correo) {
      Swal.fire(
        "Advertencia",
        "Debes proporcionar al menos un teléfono o un correo",
        "warning"
      );
      return;
    }

    if (id === undefined || opcion === undefined) {
      Swal.fire("Error", "Falta información crítica (ID u opción)", "error");
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
        interes: interes, // Agregar interés
        id_usuario: id_usuario, // Agregar ID de usuario que realiza la acción
      },
      success: function (data) {
        if (data.success) {
          if (opcion == 1) {
            actualizarTurnoColaborador(col_asignado);
          }
          /*
                telefono_vendedor = "5212281199040";

                $.ajax({
                  url: "mensajes/enviarmensajeapi.php",
                  type: "POST",
                  dataType: "json",
                  contentType: "application/json",
                  data: JSON.stringify({
                    telefono_vendedor: telefono_vendedor,
                    nombre: nombre,
                    telefono: telefono,
                    correo: correo,
                    interes: interes,
                  }),
                  success: function (resp) {
                    console.log("WhatsApp:", resp.message);
                  },
                  error: function (xhr, status, error) {
                    console.error("Error en WhatsApp API:", error);
                    console.log("Respuesta completa:", xhr.responseText);
                  },
                });
*/
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
                formatoOrigen(data.origen),
                data.interes,
                data.edo_pros,
                formatoestado(data.edo_pros),
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
                formatoOrigen(data.origen),
                data.interes,
                data.edo_pros,
                formatoestado(data.edo_pros),
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
              let urlCorreo =
                opcion == 1 ? "bd/usarapicorreo.php" : "bd/usarapicorreo2.php";
              console.log(interes);

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
                  interes: data.interes,
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
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: data.message || "Error al guardar los datos",
          });
        }
      },
    });
  });

  // Mostrar input si selecciona "otro", ocultar y sincronizar si selecciona proyecto
  $("#interes_select").on("change", function () {
    var val = $(this).val();
    if (val === "otro") {
      $("#interes").val("").show().focus();
    } else {
      $("#interes").val(val).hide();
    }
  });

  // Si el usuario escribe en el input, ese valor se usará
  $("#interes").on("input", function () {
    // No es necesario sincronizar aquí, el valor se toma directo al guardar
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
      case "vendedor":
        return '<td data-origen="vendedor"><i class="fas fa-user text-green"></i> Vendedor</td>';
      default:
        return `<td data-origen="${origen}">${origen.charAt(0).toUpperCase() + origen.slice(1)}</td>`;
    }
  }

  function formatoestado(estado) {
    
    
      if (estado==1){
        return ' <span class="badge bg-success">Activo</span>';
      }else{
        return '<span class="badge bg-secondary">Inactivo</span>';
      }
      
        
      
   
  }

     
  // Convertir a Cliente
  $(document).on("click", ".btnConvertir", function () {
    fila = $(this).closest("tr");
    var id_prospecto = parseInt(fila.find("td:eq(0)").text());
    var nombre_prospecto = fila.find("td:eq(1)").text();
    var telefono_prospecto = fila.find("td:eq(2)").text();
    var correo_prospecto = fila.find("td:eq(3)").text();
      var origen = fila.find("td[data-origen]").data("origen"); // Ej: "facebook"
    var col_asignado = fila.find("td:eq(4)").text();
    console.log("Colaborador asignado:", col_asignado);
    console.log("Origen:", origen);

    // Prellenar campos del modal con datos del prospecto
    $("#id_prospecto").val(id_prospecto);
    $("#nombre_clie").val(nombre_prospecto);
    $("#tel_clie").val(telefono_prospecto);
    $("#correo_clie").val(correo_prospecto);
  


    $("#origenc").selectpicker("val", origen);
    /*
    var select = $("#origen");
    select.find("option").each(function () {
      if ($(this).text() === origen) {
        select.val($(this).val());
        select.selectpicker("refresh");
        return false; // Salir del bucle
      }
    });
*/
    // Buscar el ID del colaborador asignado
    var select = $("#col_asignadoc");
    select.find("option").each(function () {
      if ($(this).text() === col_asignado) {
        select.val($(this).val());
        select.selectpicker("refresh");
        return false; // Salir del bucle
      }
    });

    opcion = 5; // Opción 5: Convertir a Cliente
    $(".modal-header").css("background-color", "#17a2b8");
    $(".modal-title").text("ALTA DE CLIENTE");
    $("#modalCliente").modal("show");
  });

  $(document).on("click", "#btnGuardarCliente", function () {
    var id_prospecto = $("#id_prospecto").val();
    var rfc = $.trim($("#rfc").val()).toUpperCase();
    var tipo_ide = $("#tipo_ide").val();
    var folio_ide = $.trim($("#folio_ide").val()).toUpperCase();
    var nombre_clie = $.trim($("#nombre_clie").val()).toUpperCase();
    var tel_clie = $.trim($("#tel_clie").val());
    var correo_clie = $.trim($("#correo_clie").val()).toLowerCase();
    var nacionalidad = $("#nacionalidad").val();
    var dir_calle = $.trim($("#dir_calle").val()).toUpperCase();
    var dir_colonia = $.trim($("#dir_colonia").val()).toUpperCase();
    var dir_ciudad = $.trim($("#dir_ciudad").val()).toUpperCase();
    var dir_edo = $.trim($("#dir_edo").val()).toUpperCase();
    var dir_cp = $.trim($("#dir_cp").val());
    var especial = $("#especial").is(":checked") ? 1 : 0;
    var col_asignado = $("#col_asignadoc").val();
    var origen = $("#origenc").val();

    // Validaciones obligatorias
    if (!tipo_ide || !folio_ide || !nombre_clie || !tel_clie) {
      Swal.fire("Error", "Los campos marcados con * son obligatorios", "error");
      return;
    }

    // Validar RFC si se proporciona
    if (rfc && (rfc.length < 12 || rfc.length > 13)) {
      Swal.fire("Error", "El RFC debe tener 12 o 13 caracteres", "error");
      return;
    }

    // Validar email si se proporciona
    if (correo_clie && !validarEmail(correo_clie)) {
      Swal.fire("Error", "Ingrese un correo electrónico válido", "error");
      return;
    }

    // Validar teléfono
    // Validar teléfono (10 dígitos nacional o internacional con +)
    var telValido = /^(\+[0-9]{1,3})?[0-9]{10}$/.test(tel_clie);
    if (!telValido) {
      Swal.fire(
        "Error",
        "El teléfono debe tener 10 dígitos para nacional o formato internacional con +código (ej: +521234567890)",
        "error"
      );
      return;
    } else {
      let telefonoNormalizado = tel_clie;
      if (!tel_clie.startsWith("+")) {
        telefonoNormalizado = "+52" + tel_clie;
        tel_clie = telefonoNormalizado; // Actualizar el teléfono con el normalizado
      }
    }

    // Validar código postal si se proporciona
    if (dir_cp && (dir_cp.length !== 5 || !/^\d+$/.test(dir_cp))) {
      Swal.fire("Error", "El código postal debe tener 5 dígitos", "error");
      return;
    }

    // Mostrar loading
    Swal.fire({
      title: "Procesando...",
      text: "Convirtiendo prospecto a cliente",
      allowOutsideClick: false,
      showConfirmButton: false,
      willOpen: () => {
        Swal.showLoading();
      },
    });

    // Enviar datos al servidor
    $.ajax({
      url: "bd/guardarcliente.php",
      type: "POST",
      dataType: "json",
      data: {
        id_prospecto: id_prospecto,
        rfc: rfc,
        tipo_ide: tipo_ide,
        folio_ide: folio_ide,
        nombre: nombre_clie,
        tel_cel: tel_clie,
        email: correo_clie,
        nacionalidad: nacionalidad,
        dir_calle: dir_calle,
        dir_colonia: dir_colonia,
        dir_ciudad: dir_ciudad,
        dir_edo: dir_edo,
        dir_cp: dir_cp,
        especial: especial,
        col_asignado: col_asignado,
        origen: origen,
      },
      success: function (data) {
        Swal.close();

        if (data.success) {
          // Actualizar la fila en la tabla
          var estadoBadge = '<span class="badge bg-green">Finalizado</span>';
          fila.find("td:eq(6)").html(estadoBadge);

          // Remover botones de acción ya que está convertido
          fila
            .find("td:eq(9)")
            .html(
              '<div class="text-center btn-group">' +
                '<button class="btn btn-sm btn-info" data-toggle="tooltip" title="Cliente convertido" disabled>' +
                '<i class="fas fa-user-check"></i></button>' +
                "</div>"
            );

          $("#modalCliente").modal("hide");

          Swal.fire({
            icon: "success",
            title: "¡Éxito!",
            html: `Cliente creado correctamente<br><strong>ID Cliente:</strong> ${data.id_cliente}`,
            showConfirmButton: true,
            confirmButtonText: "Ver Cliente",
            showCancelButton: true,
            cancelButtonText: "Continuar",
          }).then((result) => {
            if (result.value) {
              // Redireccionar a la vista del cliente
              window.location.href = `cntacliente.php?highlight=${data.id_cliente}`;
            }
          });

          // Limpiar formulario
          $("#formCliente")[0].reset();
          $("#especial").prop("checked", false);
        } else {
          Swal.fire(
            "Error",
            data.message || "Error al convertir prospecto",
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        Swal.close();
        console.error("Error AJAX:", error);
        console.log("Respuesta completa:", xhr.responseText);
        Swal.fire("Error", "Error de conexión con el servidor", "error");
      },
    });
  });

  // Validación en tiempo real para campos del cliente
  $("#rfc").on("input", function () {
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, "");
  });

  $("#folio_ide").on("input", function () {
    this.value = this.value.toUpperCase();
  });

  $("#dir_cp").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "").substring(0, 5);
  });

  $("#tel_clie").on("input", function () {
    this.value = this.value.replace(/[^0-9+]/g, "");
  });

  // Convertir a mayúsculas automáticamente
  $("#nombre_clie, #dir_calle, #dir_colonia, #dir_ciudad, #dir_edo, #rfc").on(
    "blur",
    function () {
      $(this).val($(this).val().toUpperCase());
    }
  );

  // Función para validar email
  function validarEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  // Función para generar botones de acción
});

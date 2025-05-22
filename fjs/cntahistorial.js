$(document).ready(function () {
  $("#btnNuevo").click(function () {
    $("#formDatos").trigger("reset");
    $("#modalCRUD").modal("show");
    $("#id_seg").val("");
    $(".modal-header").css("background-color", "#007bff");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("Nuevo Seguimiento");
    $("#fecha_seg").val(getCurrentDate());
  });

  $("#bprospecto").click(function () {
    $("#modalProspecto").modal("show");
  });

  if ($.fn.DataTable.isDataTable('#tablaV')) {
    tablaV.destroy(); // Destruye la instancia existente
}
  // DataTable Prospectos
  var tablaV = $("#tablaV").DataTable({
    language: {
      lengthMenu: "Mostrar _MENU_ registros por página",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando página _PAGE_ de _PAGES_",
      infoEmpty: "No hay registros disponibles",
      infoFiltered: "(filtrado de _MAX_ registros totales)",
      search: "Buscar:",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior",
      },
    },
    columnDefs: [
      { className: "hide_column", targets: [0] },

      {
        targets: 2, // Columna de tipo_seg
        render: function (data, type, row) {
          // Asignar iconos según el tipo de seguimiento
          var icono = "";
          var clase = "";
          var texto = data;

          switch (data) {
            case "Llamada":
              icono = '<i class="fas fa-phone-alt text-success"></i>';
             
              break;
            case "Mensaje":
              icono = '<i class="fas fa-comment-dots text-info"></i>';
             
              texto = "Mensaje"; // Estandarizar texto
              break;
            case "Correo":
              icono = '<i class="fas fa-envelope text-warning"></i>';
             
              break;
            case "Reunión":
              icono = '<i class="fas fa-handshake text-primary"></i>';
             
              break;
            case "Otro":
              icono = '<i class="fas fa-ellipsis-h text-secondary"></i>';
             
              break;
            default:
              icono = '<i class="fas fa-question-circle text-secondary"></i>';
             
          }

          return (
            '<span>' + icono + " " + texto + "</span>"
          );
        },
      },
      {
        targets: 5, // Columna de realizado (asumiendo que es la 6ta columna, índice 5)
        render: function (data, type, row) {
          if (data == "1") {
            return '<span class="badge badge-success">REALIZADO</span>';
          } else {
            return '<span class="badge badge-primary">AGENDADO</span>';
          }
        },
      },
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><div class='btn-group'><button class='btn btn-sm btn-primary btnEditar'><i class='fas fa-edit'></i></button><button class='btn btn-sm btn-danger btnBorrar'><i class='fas fa-trash-alt'></i></button></div></div>",
        orderable: false,
      },
    ],
  });

  // Seleccionar prospecto


  // DataTable Seguimientos
  var tablaProspecto = $("#tablaProspecto").DataTable({
    language: {
      lengthMenu: "Mostrar _MENU_ registros por página",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando página _PAGE_ de _PAGES_",
      infoEmpty: "No hay registros disponibles",
      infoFiltered: "(filtrado de _MAX_ registros totales)",
      search: "Buscar:",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior",
      },
    },
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><div class='btn-group'><button class='btn btn-sm btn-primary btnSelPros'><i class='fas fa-hand-pointer'></i></button></div></div>",
        orderable: false,
      },
    ],
  });


    $(document).on("click", ".btnSelPros", function () {
    var data = tablaProspecto.row($(this).parents("tr")).data();
    $("#id_pros").val(data[0]);
    $("#prospecto").val(data[1]);
    $("#correo").val(data[2]);
    $("#telefono").val(data[3]);
    $("#modalProspecto").modal("hide");
    window.location.href = "cntahistorial.php?id_pros=" + data[0];
  });
  // Editar seguimiento
  $(document).on("click", ".btnEditar", function () {
    var data = tablaV.row($(this).parents("tr")).data();
    $("#id_seg").val(data[0]);
    $("#fecha_seg").val(formatDateForInput(data[1]));
    $("#tipo_seg").val(data[2]);
    $("#observaciones").val(data[3]);
    $("#realizado").val(data[4]);

    $("#modalCRUD").modal("show");
    $(".modal-header").css("background-color", "#007bff");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("Editar Seguimiento");
  });

  // Guardar seguimiento
  $("#btnGuardar").click(function () {
    var id_seg = $("#id_seg").val();
    var id_pros = $("#id_pros").val();
    var fecha_seg = $("#fecha_seg").val();
    var tipo_seg = $("#tipo_seg").val();
    var observaciones = $("#observaciones").val();
    var realizado = $("#realizado").val();

    if (
      fecha_seg == "" ||
      tipo_seg == "" ||
      observaciones == "" ||
      realizado == ""
    ) {
      Swal.fire({
        title: "Error",
        text: "Todos los campos son obligatorios",
        icon: "error",
        showConfirmButton: false,
        timer: 1500,
      });
      return false;
    }

    $.ajax({
      url: "bd/crudhistorial.php",
      type: "POST",
      datatype: "json",
      data: {
        id_seg: id_seg,
        id_pros: id_pros,
        fecha_seg: fecha_seg,
        tipo_seg: tipo_seg,
        observaciones: observaciones,
        realizado: realizado,
        action: id_seg == "" ? "nuevo" : "editar",
      },
      success: function (data) {
        data = JSON.parse(data);
        if (data == "ok") {
          $("#modalCRUD").modal("hide");
          Swal.fire({
            title: "Éxito",
            text:
              id_seg == ""
                ? "Seguimiento agregado correctamente"
                : "Seguimiento actualizado correctamente",
            icon: "success",
            showConfirmButton: false,
            timer: 1500,
          }).then(function () {
            location.reload();
          });
        } else {
          Swal.fire({
            title: "Error",
            text: data,
            icon: "error",
            showConfirmButton: false,
            timer: 1500,
          });
        }
      },
    });
  });

  // Eliminar seguimiento
  $(document).on("click", ".btnBorrar", function () {
    var data = tablaV.row($(this).parents("tr")).data();

    Swal.fire({
      title: "¿Estás seguro?",
      text: "¡No podrás revertir esto!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Sí, eliminarlo!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "bd/crudhistorial.php",
          type: "POST",
          datatype: "json",
          data: {
            id_seg: data[0],
            action: "eliminar",
          },
          success: function (data) {
            data = JSON.parse(data);
            if (data == "ok") {
              Swal.fire({
                title: "Eliminado!",
                text: "El seguimiento ha sido eliminado.",
                icon: "success",
                showConfirmButton: false,
                timer: 1500,
              }).then(function () {
                location.reload();
              });
            } else {
              Swal.fire({
                title: "Error",
                text: data,
                icon: "error",
                showConfirmButton: false,
                timer: 1500,
              });
            }
          },
        });
      }
    });
  });

  function getCurrentDate() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, "0");
    var mm = String(today.getMonth() + 1).padStart(2, "0");
    var yyyy = today.getFullYear();
    return yyyy + "-" + mm + "-" + dd;
  }

  function formatDateForInput(dateString) {
    var parts = dateString.split("/");
    return parts[2] + "-" + parts[1] + "-" + parts[0];
  }
});

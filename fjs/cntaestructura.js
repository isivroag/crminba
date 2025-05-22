$(document).ready(function () {
  var id, opcion, fila;
  opcion = 4;

  // TOOLTIP DATATABLE
  $('[data-toggle="tooltip"]').tooltip();

  $(document).off("show.bs.modal");
  $(document).on("show.bs.modal", function (e) {
    var $modal = $(e.target);
    $modal
      .removeAttr("aria-hidden")
      .attr("aria-modal", "true")
      .css("display", "block")
      .css("padding-right", "17px");

    $("body").addClass("modal-open");
    $(".modal-backdrop").attr("aria-hidden", "true");
  });

  tablaVis = $("#tablaV").DataTable({
    scrollY: "400px", // Scroll vertical
    scrollCollapse: true,
    paging: false, // Sin paginación
    autoWidth: false, // Evita cambios automáticos en el ancho
    ordering: true,
    info: true, // Muestra el resumen de registros
    searching: true, // Habilita la búsqueda

    dom:
      "<'row justify-content-between'<'col-sm-6'l><'col-sm-6'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-6'i>>",

    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' title='Editar'><i class='fas fa-edit'></i></button>\
          <button class='btn btn-sm btn-success btnLote' data-toggle='tooltip' title='Lotes'><i class='fa-duotone fa-solid fa-layer-group'></i></button>\
         <button class='btn btn-sm btn-danger btnBorrar' data-toggle='tooltip' title='Eliminar'><i class='fas fa-trash-alt'></i></button></div>",
      },
    ],

    language: {
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando _TOTAL_ registros",
      infoEmpty: "Mostrando 0 registros",
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

  tablaProyecto = $("#tablaProyecto").DataTable({
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><div class='btn-group'><button class='btn btn-sm btn-success btnSelProy' data-toggle='tooltip' data-placement='top' title='Seleccionar Obra'><i class='fas fa-hand-pointer'></i></button></div></div>",
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

  $(document).on("click", ".btnSelProy", function () {
    fila = $(this);
    id_proy = parseInt($(this).closest("tr").find("td:eq(0)").text());

    window.location.href = "cntaestructura.php?id_proy=" + id_proy;
  });

  //BONTON NUEVO
  $("#bproyecto").click(function () {
    $("#modalProyecto").modal("show");
  });
  $(document).on("click", "#btnNuevo", function () {
    $("#formDatos").trigger("reset");
    opcion = 1; // Opción 1: Crear nuevo
    $("#modalCRUD").modal("show");
  });

  $(document).on("click", ".btnEditar", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());
    clave = fila.find("td:eq(1)").text();
    descripcion = fila.find("td:eq(2)").text();
    $("#clave").val(clave);
    $("#descripcion").val(descripcion);
    opcion = 2; // editar
    $(".modal-title").text("EDITAR MANZANA");
    $("#modalCRUD").modal("show");
  });

  $(document).on("click", "#btnGuardarmn", function () {
    id_proy = $("#id_proy").val();
    clave_proy = $("#clave_proy").val();
    clave = $("#clave").val();
    descripcion = $("#descripcion").val();

    if (
      clave.length == 0 ||
      descripcion.length == 0 ||
      clave_proy.length == 0 ||
      id_proy.length == 0
    ) {
      Swal.fire({
        title: "Datos Faltantes",
        text: "Debe ingresar todos los datos del Requeridos",
        icon: "warning",
      });
      return false;
    } else {
      $.ajax({
        url: "bd/crudmanzana.php",
        type: "POST",
        dataType: "json",
        data: {
          id: id,
          clave: clave,
          descripcion: descripcion,
          clave_proy: clave_proy,
          id_proy: id_proy,
          opcion: opcion,
        },
        success: function (data) {
          id = data[0].id_man;
          clave = data[0].clave_manzana;
          descripcion = data[0].descripcion;

          if (opcion == 1) {
            tablaVis.row.add([id, clave, descripcion]).draw();
          } else {
            tablaVis.row(fila).data([id, clave, descripcion]).draw();
          }

          registroguardado();
        },
      });
      $("#modalCRUD").modal("hide");
    }
  });

  $(document).on("click", ".btnBorrar", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());
    opcion = 3; //borrar
    //agregar codigo de sweatalert2
    swal
      .fire({
        title: "ELIMINAR",
        text: "¿Desea eliminar el registro seleccionado?",
        showCancelButton: true,
        icon: "question",
        focusConfirm: true,
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#28B463",
        cancelButtonColor: "#d33",
      })
      .then(function (isConfirm) {
        if (isConfirm.value) {
          $.ajax({
            url: "bd/crudmanzana.php",
            type: "POST",
            dataType: "json",
            data: { id: id, opcion: opcion },
            success: function (data) {
              if (data[0].respuesta == "ok") {
                swal.fire({
                  title: "Registro Eliminado",
                  icon: "success",
                  focusConfirm: true,
                  confirmButtonText: "Aceptar",
                });
                tablaVis.row(fila.parents("tr")).remove().draw();
              } else {
                swal.fire({
                  title: "Error",
                  text: "No se puede eliminar el registro, ya que tiene datos relacionados.",
                  icon: "error",
                  focusConfirm: true,
                  confirmButtonText: "Aceptar",
                });
              }
            },
          });
        } else if (isConfirm.dismiss === swal.DismissReason.cancel) {
        }
      });
  });

  $(document).on("click", ".btnLote", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());
    clave = fila.find("td:eq(1)").text();
    id_proy = $("#id_proy").val();
     // Redirecciona a cntalotes.php con los parámetros necesarios
    window.location.href = "cntalotes.php?id_man=" + id + "&id_proy=" + id_proy;
  });

  function registroguardado() {
    swal.fire({
      title: "Registro Guardado con Exito",
      icon: "success",
      focusConfirm: true,
      confirmButtonText: "Aceptar",
    });
  }
});

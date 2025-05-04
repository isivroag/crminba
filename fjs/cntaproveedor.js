$(document).ready(function () {
  var id, opcion;
  opcion = 4;

  // TOOLTIP DATATABLE
  $('[data-toggle="tooltip"]').tooltip();

  tablaVis = $("#tablaV").DataTable({
    dom:
      "<'row justify-content-center'<'col-sm-12 col-md-4 form-group'l><'col-sm-12 col-md-4 form-group'B><'col-sm-12 col-md-4 form-group'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

    buttons: [
      {
        extend: "excelHtml5",
        text: "<i class='fas fa-file-excel'> Excel</i>",
        titleAttr: "Exportar a Excel",
        title: "Listado de Proveedores",
        className: "btn bg-success ",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='far fa-file-pdf'> PDF</i>",
        titleAttr: "Exportar a PDF",
        title: "Listado de Proveedores",
        className: "btn bg-danger",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] },
      },
    ],

    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' data-placement='top' title='Editar'><i class='fas fa-edit'></i></button>\
          <button class='btn btn-sm btn-secondary btnEnviar'><i class='fa-solid fa-envelope' data-toggle='tooltip' data-placement='top' title='Enviar Registro'></i></button>\
              <button class='btn btn-sm btn-info btnVerdocs'><i class='fa-solid fa-folder-magnifying-glass' data-toggle='tooltip' data-placement='top' title='Ver Documentos'></i></button>\
              <button class='btn btn-sm btn-success btnAprobar'><i class='fa-solid fa-circle-check' data-toggle='tooltip' data-placement='top' title='Aprobar'></i></button>\
              <button class='btn btn-sm btn-danger btnBorrar' data-toggle='tooltip' data-placement='top' title='Eliminar'><i class='fas fa-trash-alt'></i></button></div>",
      },

      { className: "hide_column", targets: [7] },
      {
        targets: [2],

        render: function (data, type, row, meta) {
          return '<div class="multi-line ">' + data + "</div>";
        },
      },
    ],

    //Para cambiar el lenguaje a español
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

    rowCallback: function (row, data) {
      valor = "";

      switch (parseInt(data[7])) {
        case 0:
          valor =
            "<div>\
           <i class='fa-regular fa-star '></i>\
           <i class='fa-regular fa-star '></i>\
           <i class='fa-regular fa-star '></i>\
           <i class='fa-regular fa-star '></i>\
           <i class='fa-regular fa-star '></i>\
           </div>";
          break;
        case 1:
          valor =
            "<div>\
            <i class='fa-solid fa-star starchecked'></i>\
            <i class='fa-regular fa-star '></i>\
            <i class='fa-regular fa-star '></i>\
            <i class='fa-regular fa-star '></i>\
            <i class='fa-regular fa-star '></i>\
            </div>";
          break;
        case 2:
          valor =
            "<div>\
            <i class='fa-solid fa-star starchecked'></i>\
            <i class='fa-solid fa-star starchecked'></i>\
            <i class='fa-regular fa-star '></i>\
            <i class='fa-regular fa-star '></i>\
            <i class='fa-regular fa-star '></i>\
            </div>";
          break;
        case 3:
          valor =
            "<div>\
            <i class='fa-solid fa-star starchecked'></i>\
            <i class='fa-solid fa-star starchecked'></i>\
            <i class='fa-solid fa-star starchecked'></i>\
            <i class='fa-regular fa-star '></i>\
            <i class='fa-regular fa-star '></i>\
            </div>";
          break;
        case 4:
          valor =
            "<div>\
            <i class='fa-solid fa-star starchecked'></i>\
            <i class='fa-solid fa-star starchecked'></i>\
            <i class='fa-solid fa-star starchecked'></i>\
            <i class='fa-solid fa-star starchecked'></i>\
            <i class='fa-regular fa-star '></i>\
            </div>";
          break;
        case 5:
          valor =
            "<div>\
           <i class='fa-solid fa-star starchecked'></i>\
           <i class='fa-solid fa-star starchecked'></i>\
           <i class='fa-solid fa-star starchecked'></i>\
           <i class='fa-solid fa-star starchecked'></i>\
           <i class='fa-solid fa-star starchecked'></i>\
           </div>";
          break;
      }

      $($(row).find("td")[8]).html(valor);
    },
  });

  /*FILTROS
    $('#tablaV thead tr').clone(true).appendTo('#tablaV thead')
    $('#tablaV thead tr:eq(1) th').each(function (i) {
      var title = $(this).text()
      $(this).html(
        '<input class="form-control form-control-sm" type="text" placeholder="' +
          title +
          '" />',
      )
  
      $('input', this).on('keyup change', function () {
        if (i == 4) {
          valbuscar = this.value
        } else {
          valbuscar = this.value
        }
  
        if (tablaVis.column(i).search() !== valbuscar) {
          tablaVis.column(i).search(valbuscar, true, true).draw()
        }
      })
    })
  */
  //BONTON NUEVO
  $("#btnNuevo").click(function () {
    //window.location.href = "prospecto.php";
    $("#formDatos").trigger("reset");
    $(".modal-header").css("background-color", "#28a745");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("NUEVO PROVEEDOR");

    $("#modalCRUD").removeAttr("aria-hidden");
    $("#modalCRUD").removeAttr("inert");
    $("#modalCRUD").modal("show");
    id = null;
    opcion = 1;
  });

  var fila;

  //BOTON EDITAR
  $(document).on("click", ".btnEditar", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    rfc = fila.find("td:eq(1)").text();
    razon = fila.find("td:eq(2)").text();

    tel = fila.find("td:eq(3)").text();
    contacto = fila.find("td:eq(4)").text();
    tel_contacto = fila.find("td:eq(5)").text();

    correo = fila.find("td:eq(6)").text();
    puntaje = parseInt(fila.find("td:eq(7)").text());
    valor = fila.find("td:eq(8)").html();
    tipo = fila.find("td:eq(9)").text();

    $("#rfc").val(rfc);
    $("#razon").val(razon);

    $("#tel").val(tel);
    $("#contacto").val(contacto);
    $("#tel_contacto").val(tel_contacto);
    $("#tipo").val(tipo);

    $("#correo").val(correo);

    $("#puntaje").val(puntaje);
    var select = $("#puntaje"); // elemento select en el HTML
    select.selectpicker("val", puntaje);

    opcion = 2; //editar

    $(".modal-header").css("background-color", "#007bff");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("EDITAR PROVEEDOR");
    $("#modalCRUD").removeAttr("aria-hidden");
    $("#modalCRUD").removeAttr("inert");
    $("#modalCRUD").modal("show");
  });

  //BOTON BORRAR
  $(document).on("click", ".btnBorrar", function () {
    fila = $(this);

    id = parseInt($(this).closest("tr").find("td:eq(0)").text());
    opcion = 3;
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
            url: "bd/crudproveedor.php",
            type: "POST",
            dataType: "json",
            data: { id: id, opcion: opcion },
            success: function (data) {
              tablaVis.row(fila.parents("tr")).remove().draw();
            },
          });
        } else if (isConfirm.dismiss === swal.DismissReason.cancel) {
        }
      });
  });

  //BOOTON CORREO

  $(document).on("click", ".btnEnviar", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());
    nombre = fila.find("td:eq(2)").text();
    email = fila.find("td:eq(6)").text();

    var formData = {
      id_prov: id,
      email: email,
      nombre: nombre,
    };

    console.log(formData);

    $.ajax({
      type: "POST",
      url: "bd/usarapicorreo.php",
      data: JSON.stringify(formData),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      encode: true,
    })
      .done(function (data) {
        // Mostrar mensaje de respuesta al usuario
        Swal.fire({
          title: data.message,
          icon: "info",
        });
        console.log(data.message);
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR, textStatus, errorThrown); // Debugging: imprimir el error
        // Mostrar mensaje de error si la solicitud falla
        Swal.fire({
          title: "Error al enviar los datos al servidor.",
          icon: "error",
        });

        console.log("Error al enviar los datos al servidor.");
      });
  });
  //GUARDAR PROVEEDOR

  $("#formDatos").submit(function (e) {
    e.preventDefault();
    var rfc = $("#rfc").val();
    var tipo = $("#tipo").val();
    var tel = $("#tel").val();
    var razon = $("#razon").val();
    var contacto = $("#contacto").val();
    var tel_contacto = $("#tel_contacto").val();
    var correo = $("#correo").val();
    var puntaje = $("#puntaje").val();

    if (
      razon.length == 0 ||
      rfc.length == 0 ||
      correo.length == 0 ||
      tipo.length == 0
    ) {
      Swal.fire({
        title: "Datos Faltantes",
        text: "Debe ingresar todos los datos marcados con *",
        icon: "warning",
      });
      return false;
    } else {
      $.ajax({
        url: "bd/buscarrfcprov.php",
        type: "POST",
        dataType: "json",
        async: false,
        data: {
          rfc: rfc,
          opcion: opcion,
        },
        success: function (data) {
          if (data == 0) {
            // funcion crud
            $.ajax({
              url: "bd/crudproveedor.php",
              type: "POST",
              dataType: "json",
              data: {
                razon: razon,
                tel: tel,
                rfc: rfc,
                tipo: tipo,
                id: id,
                contacto: contacto,
                tel_contacto: tel_contacto,
                correo: correo,
                puntaje: puntaje,
                opcion: opcion,
              },
              success: function (data) {
                id = data[0].id_prov;
                rfc = data[0].rfc_prov;
                razon = data[0].razon_prov;
                tel = data[0].tel_prov;
                contacto = data[0].contacto_prov;
                tel_contacto = data[0].telcon_prov;
                correo = data[0].correo_prov;
                puntaje = data[0].puntaje;
                tipo = data[0].tipo_prov;
                estado = data[0].estado;
                if (opcion == 1) {
                  tablaVis.row
                    .add([
                      id,
                      rfc,
                      razon,
                      tel,
                      contacto,
                      tel_contacto,
                      correo,
                      puntaje,
                      "",
                      tipo,
                      estado,
                    ])
                    .draw();
                } else {
                  tablaVis
                    .row(fila)
                    .data([
                      id,
                      rfc,
                      razon,
                      tel,
                      contacto,
                      tel_contacto,
                      correo,
                      puntaje,
                      "",
                      tipo,
                      estado,
                    ])
                    .draw();
                }
              },
            });
            $("#modalCRUD").attr("aria-hidden", "true");
            $("#modalCRUD").attr("inert", "");
            $("#modalCRUD").modal("hide");

            // funcion crud
          } else {
            Swal.fire({
              title: "El RFC ya se encuentra registrado",
              icon: "error",
            });
          }
        },
      });
    }
  });

  //TABLA CUENTAS

  tablacuenta = $("#tablaCuentas").DataTable({
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><button class='btn btn-sm bg-primary btnEditarcuenta' data-toggle='tooltip' data-placement='top' title='Editar Cuenta'><i class='fas fa-edit'></i></button>\
                       <button class='btn btn-sm bg-danger btnEliminarcuenta' data-toggle='tooltip' data-placement='top' title='Eliminar Cuenta'><i class='fas fa-trash-alt'></i></button>\
                      </div></div>",
      },
      { className: "hide_column", targets: [1] },
      { className: "hide_column", targets: [6] },
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
    rowCallback: function (row, data) {
      if (data[6] == "1") {
        //$($(row).find("td")[6]).css("background-color", "warning");
        $($(row).find("td")).addClass("bg-gradient-info");
        //$($(row).find('td')[4]).css('background-color','#EEA447');
        //$($(row).find('td')['4']).text('PENDIENTE')
      }
    },
  });

  //BOTON RESUMEN DE CUENTAS
  $(document).on("click", ".btnVercuentas", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());
    buscarcuentas(id);
    $("#modalCuentas").modal("show");
  });

  $(document).on("click", ".btnVerdocs", function () {
    var fila = $(this).closest("tr");
    var id_prov = parseInt(fila.find("td:eq(0)").text()); // Asumiendo que la columna 0 contiene el ID del proveedor
    var tipo = fila.find("td:eq(9)").text(); // Asumiendo que la columna 9 contiene el tipo de proveedor

    // Limpiar los checkboxes antes de rellenarlos
    $("#subcontratista-checklist input[type='checkbox']").prop('checked', false);
    $("#proveedor-checklist input[type='checkbox']").prop('checked', false);

    // Mostrar los checklists según el tipo de proveedor
    if (tipo === "SUBCONTRATISTA") {
        $("#subcontratista-checklist").show();
        $("#proveedor-checklist").hide();
    } else if (tipo === "PROVEEDOR") {
        $("#subcontratista-checklist").hide();
        $("#proveedor-checklist").show();
    } else {
        $("#subcontratista-checklist").hide();
        $("#proveedor-checklist").hide();
    }

    // Llamar al archivo PHP mediante AJAX para obtener los documentos del proveedor
    $.ajax({
        type: "POST",
        url: "bd/buscarDocumentos.php",
        data: { id_prov: id_prov },
        dataType: "json",
        success: function (data) {
            data.forEach(function (doc) {
                var checkbox = $("input[type='checkbox'][value='" + doc.nombre + "']");
                if (checkbox.length) {
                    checkbox.prop('checked', true);
                }
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown); // Debugging: imprimir el error
            Swal.fire({
                title: "Error al obtener los documentos del proveedor.",
                icon: "error",
            });
        }
    });

    $("#modalDocumentos").modal("show");
});

  // Función para mostrar/ocultar checklists en el formulario modal de nuevo/editar proveedor
  $("#tipo").change(function () {
    var tipo = $(this).val();
    if (tipo === "SUBCONTRATISTA") {
        $("#subcontratista-checklist").show();
        $("#proveedor-checklist").hide();
    } else if (tipo === "PROVEEDOR") {
        $("#subcontratista-checklist").hide();
        $("#proveedor-checklist").show();
    } else {
        $("#subcontratista-checklist").hide();
        $("#proveedor-checklist").hide();
    }
  });

  // FUNCION BUSCAR CUENTAS
  function buscarcuentas(id) {
    tablacuenta.clear();
    tablacuenta.draw();
    opcion = 2; // 2 para cuentas pagar
    $.ajax({
      type: "POST",
      url: "bd/buscarcuentasprov.php",
      dataType: "json",

      data: { id: id },

      success: function (res) {
        for (var i = 0; i < res.length; i++) {
          tablacuenta.row
            .add([
              res[i].id_cuentaprov,
              res[i].id_prov,
              res[i].banco,
              res[i].cuenta,
              res[i].clabe,
              res[i].tarjeta,
              res[i].cuentadefault,
            ])
            .draw();
        }
      },
    });
  }
});

$(document).ready(function () {
    var id, opcion, valor;
    opcion = 4;
    valor = 0;
    /*<button class='btn btn-sm btn-primary btnEditar'  data-toggle='tooltip' data-placement='top' title='Editar'><i class='fas fa-edit'></i></button>\
        <button class='btn btn-sm bg-info btnResumen'><i class='fas fa-bars'  data-toggle='tooltip' data-placement='top' title='Resumen de Pagos'></i></button>
      */
    var textcolumnas = permisos();
  
    function permisos() {
      var tipousuario = $("#tipousuario").val();
      var columnas = "";
  
      if (tipousuario == 1) {
        columnas =
          "<div class='text-center'><div class='btn-group'>\
            <button class='btn btn-sm btn-success btnAgregar' data-toggle='tooltip' data-placement='top' title='Orden de compra' ><i class='fa-solid fa-circle-check'></i></button>\
            <button class='btn btn-sm bg-danger btnCancelar'  data-toggle='tooltip' data-placement='top' title='Cancelar'><i class='fas fa-ban'></i></button>\
            </div></div>";
      } else {
        columnas =
          "<div class='text-center'><div class='btn-group'>\
            <button class='btn btn-sm btn-success btnAgregar' data-toggle='tooltip' data-placement='top' title='Orden de compra' ><i class='fa-solid fa-circle-check'></i></button>\
            <button class='btn btn-sm bg-danger btnCancelar'  data-toggle='tooltip' data-placement='top' title='Cancelar'><i class='fas fa-ban'></i></button>\
            </div></div>";
      }
      return columnas;
    }
  
    var textcolumnas3 = permisos3();
  
    function permisos3() {
      var tipousuario = $("#tipousuario").val();
      var columnas = "";
  
      if (tipousuario == 1) {
        columnas = "";
        /*"<div class='text-center'><div class='btn-group'><button class='btn btn-sm bg-danger btnCancelarpago' data-toggle='tooltip' data-placement='top' title='Cancelar'><i class='fas fa-ban'></i></button>\
            </div></div>"*/
      } else {
        columnas =
          "<div class='text-center'><div class='btn-group'><button class='btn btn-sm bg-danger btnCancelarpago' data-toggle='tooltip' data-placement='top' title='Cancelar'><i class='fas fa-ban'></i></button>\
            </div></div>";
      }
      return columnas;
    }
  
    // TOOLTIP DATATABLE
    $('[data-toggle="tooltip"]').tooltip();
  
    //FUNCION REDONDEAR
    function round(value, decimals) {
      return Number(Math.round(value + "e" + decimals) + "e-" + decimals);
    }
  
    //FUNCION FORMATO MONEDA
  
    //CALCULO TOTAL REQ
    function calculototalreq(valor) {
      subtotal = valor;
  
      total = round(subtotal * 1.16, 2);
      iva = total - subtotal;
  
      $("#ivareq").val(
        Intl.NumberFormat("es-MX", { minimumFractionDigits: 2 }).format(
          parseFloat(iva).toFixed(2)
        )
      );
      $("#montonom").val(
        Intl.NumberFormat("es-MX", { minimumFractionDigits: 2 }).format(
          parseFloat(total).toFixed(2)
        )
      );
    }
    //CALCULO SUBTOTAL REQ
    function calculosubtotalreq(valor) {
      total = valor;
  
      subtotal = round(total / 1.16, 2);
  
      iva = round(total - subtotal, 2);
  
      $("#ivareq").val(
        Intl.NumberFormat("es-MX", { minimumFractionDigits: 2 }).format(
          parseFloat(iva).toFixed(2)
        )
      );
      $("#subtotalreq").val(
        Intl.NumberFormat("es-MX", { minimumFractionDigits: 2 }).format(
          parseFloat(subtotal).toFixed(2)
        )
      );
    }
  
    // TABLA PRINCIPAL
  
    tablaVis = $("#tablaV").DataTable({
      fixedHeader: false,
      paging: false,
  
      dom:
        "<'row justify-content-center'<'col-sm-12 col-md-4 form-group'l><'col-sm-12 col-md-4 form-group'B><'col-sm-12 col-md-4 form-group'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
  
      buttons: [
        {
          extend: "excelHtml5",
          text: "<i class='fas fa-file-excel'> Excel</i>",
          titleAttr: "Exportar a Excel",
          title: "Reporte Gastos de Obra",
          className: "btn bg-green ",
          exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='far fa-file-pdf'> PDF</i>",
          titleAttr: "Exportar a PDF",
          title: "Reporte Gastos de Obra",
          className: "btn bg-danger",
          exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
        },
      ],
  
      columnDefs: [
        {
          targets: -1,
          data: null,
          defaultContent: textcolumnas,
        },
     
      ],
      rowCallback: function (row, data) {
        $($(row).find("td")["7"]).addClass("text-right");
  
        //$($(row).find("td")["7"]).addClass("currency");
        $($(row).find("td")["8"]).addClass("text-right");
  
        //$($(row).find("td")["8"]).addClass("currency");
  
        if (data[9] == 1) {
          // El índice 9 representa la columna 10 (las columnas son 0 indexadas)
          $(row).find("td")[9].innerHTML =
            "<div class='row justify-content-center text-green'><i class='text-center fa-solid fa-square-check'></i></div>";
        } else {
          $(row).find("td")[9].innerHTML = "";
        }
       
      },
  
      // SUMA DE TOTAL
      footerCallback: function (row, data, start, end, display) {
        var api = this.api(),
          data;
  
        var intVal = function (i) {
          return typeof i === "string"
            ? i.replace(/[\$,]/g, "") * 1
            : typeof i === "number"
            ? i
            : 0;
        };
        /*
              total = api
                .column(6)
                .data()
                .reduce(function (a, b) {
                  return intVal(a) + intVal(b)
                }, 0)*/
  
        total = api
          .column(7, { page: "current" })
          .data()
          .reduce(function (a, b) {
            return intVal(a) + intVal(b);
          }, 0);
  
        saldo = api
          .column(8, { page: "current" })
          .data()
          .reduce(function (a, b) {
            return intVal(a) + intVal(b);
          }, 0);
  
        $(api.column(7).footer()).html(
          Intl.NumberFormat("es-MX", { minimumFractionDigits: 2 }).format(
            parseFloat(total).toFixed(2)
          )
        );
        $(api.column(8).footer()).html(
          Intl.NumberFormat("es-MX", { minimumFractionDigits: 2 }).format(
            parseFloat(saldo).toFixed(2)
          )
        );
      },
  
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
  
    // TABLA BUSCAR PROVEEDOR
    tablaprov = $("#tablaProveedor").DataTable({
      columnDefs: [
        {
          targets: -1,
          data: null,
          defaultContent:
            "<div class='text-center'><div class='btn-group'><button class='btn btn-sm btn-success btnSelProveedor' data-toggle='tooltip' data-placement='top' title='Seleccionar Proveedor'><i class='fas fa-hand-pointer'></i></button></div></div>",
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
  
    //FILTROS
    $("#tablaV thead tr").clone(true).appendTo("#tablaV thead");
    $("#tablaV thead tr:eq(1) th").each(function (i) {
      var title = $(this).text();
      $(this).html(
        '<input class="form-control form-control-sm" type="text" placeholder="' +
          title +
          '" />'
      );
  
      $("input", this).on("keyup change", function () {
        if (i == 4) {
          valbuscar = this.value;
        } else {
          valbuscar = this.value;
        }
  
        if (tablaVis.column(i).search() !== valbuscar) {
          tablaVis.column(i).search(valbuscar, true, true).draw();
        }
      });
    });
  
    // TABLA BUSCAR OBRA
  
    tablaobra = $("#tablaObra").DataTable({
      columnDefs: [
        {
          targets: -1,
          data: null,
          defaultContent:
            "<div class='text-center'><div class='btn-group'><button class='btn btn-sm btn-success btnSelObra' data-toggle='tooltip' data-placement='top' title='Seleccionar Obra'><i class='fas fa-hand-pointer'></i></button></div></div>",
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
  contarvalores()
    function contarvalores(folio){
      var contador = 0;
  
      // Itera a través de todas las filas y verifica la columna 10
      tablaVis.rows().every(function() {
          var data = this.data();
          var valorColumna10 = data[9]; // La columna 10 es la columna 9 (las columnas son 0 indexadas)
          
          if (valorColumna10 == 1) {
              contador++;
          }
      });
    
      // Actualiza el valor del <span> con el conteo
      $('#valores').text(contador);
    }
    
  
    //BOTON SELECCIONAR PROVEEDOR
    $(document).on("click", ".btnSelProveedor", function () {
      fila = $(this);
      id_prov = parseInt($(this).closest("tr").find("td:eq(0)").text());
      proveedor = $(this).closest("tr").find("td:eq(2)").text();
  
      $("#id_prov").val(id_prov);
      $("#proveedor").val(proveedor);
      $("#modalProveedor").modal("hide");
    });
    //BOTON BUSCAR PROVEEDOR
    $(document).on("click", "#bproveedor", function () {
      $("#modalProveedor").modal("show");
    });
  
    //TABLA RESUMEN DE RESUMEN PAGOS
    tablaResumenp = $("#tablaResumenp").DataTable({
      rowCallback: function (row, data) {
        $($(row).find("td")["3"]).addClass("text-right");
        $($(row).find("td")["3"]).addClass("currency");
      },
      columnDefs: [
        {
          targets: -1,
          data: null,
          defaultContent: textcolumnas3,
          /*   "<div class='text-center'><button class='btn btn-sm bg-danger btnCancelarpago' data-toggle='tooltip' data-placement='top' title='Cancelar'><i class='fas fa-ban'></i></button>\
                        </div></div>"*/
        },
        {
          targets: 3,
          render: function (data, type, full, meta) {
            return new Intl.NumberFormat("es-MX", {
              minimumFractionDigits: 2,
            }).format(parseFloat(data).toFixed(2));
          },
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
      footerCallback: function (row, data, start, end, display) {
        var api = this.api(),
          data;
  
        var intVal = function (i) {
          return typeof i === "string"
            ? i.replace(/[\$,]/g, "") * 1
            : typeof i === "number"
            ? i
            : 0;
        };
  
        totalr = api
          .column(3)
          .data()
          .reduce(function (a, b) {
            return intVal(a) + intVal(b);
          }, 0);
  
        $(api.column(3).footer()).html(
          Intl.NumberFormat("es-MX", { minimumFractionDigits: 2 }).format(
            parseFloat(totalr).toFixed(2)
          )
        );
      },
    });
  
    //BOTON NUEVO
    $("#btnNuevo").click(function () {
      window.location.href = "requisicion.php";
    });
  
  
     //BOTON NUEVO
     $("#btnGenerar").click(function () {
      window.location.href = "cntareqrpt.php";
    });
  
   
    //BOTON BUSCAR OBRA
    $(document).on("click", "#bobra", function () {
      $("#modalObra").modal("show");
    });
    //BOTON SELECCIONAR OBRA
    //BOTON SELECCIONAR OBRA
    $(document).on("click", ".btnSelObra", function () {
      fila = $(this);
      id_obra = parseInt($(this).closest("tr").find("td:eq(0)").text());
      obra = $(this).closest("tr").find("td:eq(2)").text();
      $("#id_obra").val(id_obra);
      $("#obra").val(obra);
      $("#modalObra").modal("hide");
    });
  
    $("#facturado").on("click", function () {
      if ($("#facturado").prop("checked")) {
        $("#subtotalreq").prop("disabled", false);
        $("#ivareq").prop("disabled", false);
        $("#factura").prop("disabled", false);
        calculosubtotalreq($("#montonom").val().replace(/,/g, ""));
      } else {
        $("#subtotalreq").prop("disabled", true);
        $("#ivareq").prop("disabled", true);
        $("#factura").prop("disabled", true);
        $("#subtotalreq").val("0.00");
        $("#ivareq").val("0.00");
        $("#factura").val("");
      }
    });
  
   
    //BOTON VER PAGOS
    $(document).on("click", ".btnAgregar", function () {
      tablaVis.clear();
      tablaVis.draw();
      fila = $(this).closest("tr");
      folio = parseInt(fila.find("td:eq(0)").text());
      opcion = 1;
  
      $.ajax({
        type: "POST",
        url: "bd/movreq.php",
        dataType: "json",
  
        data: { folio: folio, opcion: opcion },
  
        success: function (res) {
          valor = 0;
  
          for (var i = 0; i < res.length; i++) {
        
  
            tablaVis.row
              .add([
                res[i].folio_req,
                res[i].id_obra,
                res[i].corto_obra,
                res[i].id_prov,
                res[i].razon_prov,
                res[i].fecha,
                res[i].desc_req,
                Intl.NumberFormat('es-MX', { minimumFractionDigits: 2 }).format(
                  parseFloat(res[i].monto_req).toFixed(2)),
                  Intl.NumberFormat('es-MX', { minimumFractionDigits: 2 }).format(
                    parseFloat(res[i].saldo_req).toFixed(2)),
                res[i].seleccion,
              ])
              .draw();
             
          }
  
          contarvalores()
        },
      });
      
    });
  
    $(document).on("click", ".btnQuitar", function () {
      tablaVis.clear();
      tablaVis.draw();
      fila = $(this).closest("tr");
      folio = parseInt(fila.find("td:eq(0)").text());
      opcion = 2;
  
      $.ajax({
        type: "POST",
        url: "bd/movreq.php",
        dataType: "json",
  
        data: { folio: folio, opcion: opcion },
  
        success: function (res) { 
          valor = 0;
          for (var i = 0; i < res.length; i++) {
           
           
            tablaVis.row
              .add([
                res[i].folio_req,
                res[i].id_obra,
                res[i].corto_obra,
                res[i].id_prov,
                res[i].razon_prov,
                res[i].fecha,
                res[i].desc_req,
                Intl.NumberFormat('es-MX', { minimumFractionDigits: 2 }).format(
                  parseFloat(res[i].monto_req).toFixed(2)),
                  Intl.NumberFormat('es-MX', { minimumFractionDigits: 2 }).format(
                    parseFloat(res[i].saldo_req).toFixed(2)),
                res[i].seleccion,
              ])
              .draw();
           
         
          }
          contarvalores()
        },
      });
     
    });
  
   
   
  
    
    
  
   
  
  
  
    function facturaexitosa() {
      swal.fire({
        title: "Registro Guardado",
        icon: "success",
        focusConfirm: true,
        confirmButtonText: "Aceptar",
      });
    }
  
    function facturaerror() {
      swal.fire({
        title: "Registro No Guardado",
        icon: "error",
        focusConfirm: true,
        confirmButtonText: "Aceptar",
      });
    }
    function operacionexitosa() {
      swal.fire({
        title: "Pago Registrado",
        icon: "success",
        focusConfirm: true,
        confirmButtonText: "Aceptar",
      });
    }
    function mensaje() {
      swal.fire({
        title: "Registro Cancelado",
        icon: "success",
        focusConfirm: true,
        confirmButtonText: "Aceptar",
      });
    }
  
    function mensajeerror() {
      swal.fire({
        title: "Error al Cancelar el Registro",
        icon: "error",
        focusConfirm: true,
        confirmButtonText: "Aceptar",
      });
    }
  
  
    var fila; //capturar la fila para editar o borrar el registro
  
    function startTime() {
      var today = new Date();
      var hr = today.getHours();
      var min = today.getMinutes();
      var sec = today.getSeconds();
      //Add a zero in front of numbers<10
      min = checkTime(min);
      sec = checkTime(sec);
      document.getElementById("clock").innerHTML = hr + " : " + min + " : " + sec;
      var time = setTimeout(function () {
        startTime();
      }, 500);
    }
  
    function checkTime(i) {
      if (i < 10) {
        i = "0" + i;
      }
      return i;
    }
  });
  
  function filterFloat(evt, input) {
    // Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
    var key = window.Event ? evt.which : evt.keyCode;
    var chark = String.fromCharCode(key);
    var tempValue = input.value + chark;
    var isNumber = key >= 48 && key <= 57;
    var isSpecial = key == 8 || key == 13 || key == 0 || key == 46;
    if (isNumber || isSpecial) {
      return filter(tempValue);
    }
  
    return false;
  }
  function filter(__val__) {
    var preg = /^([0-9]+\.?[0-9]{0,2})$/;
    return preg.te;
    st(__val__) === true;
  }
  
  $(".modal-header").on("mousedown", function (mousedownEvt) {
    var $draggable = $(this);
    var x = mousedownEvt.pageX - $draggable.offset().left,
      y = mousedownEvt.pageY - $draggable.offset().top;
    $("body").on("mousemove.draggable", function (mousemoveEvt) {
      $draggable.closest(".modal-dialog").offset({
        left: mousemoveEvt.pageX - x,
        top: mousemoveEvt.pageY - y,
      });
    });
    $("body").one("mouseup", function () {
      $("body").off("mousemove.draggable");
    });
    $draggable.closest(".modal").one("bs.modal.hide", function () {
      $("body").off("mousemove.draggable");
    });
  });
  
$(document).ready(function () {
  var id_concepto, opcion
  opcion = 4

  tablaCon = $('#tablaCon').DataTable({
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><div class='btn-group'><button class='btn btn-sm btn-success btnSelConcepto'><i class='fas fa-hand-pointer'></i></button></div></div>",
      },
      { className: 'text-right', targets: [2] },


    ],

    //Para cambiar el lenguaje a español
    language: {
      lengthMenu: 'Mostrar _MENU_ registros',
      zeroRecords: 'No se encontraron resultados',
      info:
        'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
      infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
      infoFiltered: '(filtrado de un total de _MAX_ registros)',
      sSearch: 'Buscar:',
      oPaginate: {
        sFirst: 'Primero',
        sLast: 'Último',
        sNext: 'Siguiente',
        sPrevious: 'Anterior',
      },
      sProcessing: 'Procesando...',
    },
  })

  tablaPX = $('#tablaPx').DataTable({
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><div class='btn-group'><button class='btn btn-sm btn-success btnSelPaciente'><i class='fas fa-hand-pointer'></i></button></div></div>",
      },
    ],

    //Para cambiar el lenguaje a español
    language: {
      lengthMenu: 'Mostrar _MENU_ registros',
      zeroRecords: 'No se encontraron resultados',
      info:
        'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
      infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
      infoFiltered: '(filtrado de un total de _MAX_ registros)',
      sSearch: 'Buscar:',
      oPaginate: {
        sFirst: 'Primero',
        sLast: 'Último',
        sNext: 'Siguiente',
        sPrevious: 'Anterior',
      },
      sProcessing: 'Procesando...',
    },
  })

  function commaSeparateNumber(val) {
    while (/(\d+)(\d{3})/.test(val.toString())) {
      val = val.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2')
    }

    return val
  }

  $('#btnNuevo').click(function () {
    window.location.href = 'registro.php'
  })

  var fila //capturar la fila para editar o borrar el registro

  //botón EDITAR
  $(document).on('click', '#btnGuardar', function () {
    idpx = $("#idpx").val();;
    fecha = $("#fecha").val();
    idconcepto = $("#idconcepto").val();
    concepto = $("#concepto").val();
    obs = $("#obs").val();
    subtotal = $("#subtotal").val();
    descuento = $("#descuento").val();
    total = $("#total").val();
    precio = $("#precio").val();
    registro = $("#registro").val();
    usuario = $("#nameuser").val();
    console.log(registro);

    if (idpx.length == 0 || fecha.length == 0 || concepto.length == 0 ||  total.length == 0) {
      Swal.fire({
        title: 'Datos Faltantes',
        text: "Debe ingresar todos los datos del Prospecto",
        icon: 'warning',
      })
      return false;
    } else {

      if (registro == 0) {
        opcion = 1;
        $.ajax({
          url: "bd/crudregistro.php",
          type: "POST",
          dataType: "json",
          data: {
            idpx: idpx, fecha: fecha,
            idconcepto: idconcepto, concepto: concepto,
            obs: obs, subtotal: subtotal, descuento: descuento,
            total: total, usuario: usuario, precio: precio, registro: registro, opcion: opcion
          },
          success: function (data) {
            if (data == 1) {
              window.location.href = 'cntadiario.php'
            }
            else {
              Swal.fire({
                title: 'Operacion No Exitosa',
                icon: 'warning',
              })
            }
          }
        });
      } else {

        opcion = 2;
        $.ajax({
          url: "bd/crudregistro.php",
          type: "POST",
          dataType: "json",
          data: {
            idpx: idpx, fecha: fecha,
            idconcepto: idconcepto, concepto: concepto,
            obs: obs, subtotal: subtotal, descuento: descuento,
            total: total, usuario: usuario, precio: precio, registro: registro, opcion: opcion
          },
          success: function (data) {
            if (data == 1) {
              window.location.href = 'cntadiario.php'
            }
            else {
              Swal.fire({
                title: 'Operacion No Exitosa',
                icon: 'warning',
              })
            }
          }
        });

      }


    }
  });




  $(document).on('click', '#bconcepto', function () {
    $('#modalConcepto').modal('show')
  })

  $(document).on('click', '#bpaciente', function () {
    $('#modalPx').modal('show')
  })

  //botón BORRAR
  $(document).on('click', '.btnSelConcepto', function () {
    fila = $(this)
    idconcepto = parseInt($(this).closest('tr').find('td:eq(0)').text())
    concepto = $(this).closest('tr').find('td:eq(1)').text()
    precio = $(this).closest('tr').find('td:eq(2)').text()
    $('#idconcepto').val(idconcepto)
    $('#concepto').val(concepto)
    $('#precio').val(precio)
    $('#total').val(precio)
    $('#subtotal').val(precio)
    $('#descuento').val(0)
    $('#modalConcepto').modal('hide')
  })

  $(document).on('click', '.btnSelPaciente', function () {
    fila = $(this)
    idpx = parseInt($(this).closest('tr').find('td:eq(0)').text())
    px = $(this).closest('tr').find('td:eq(1)').text()
    $('#idpx').val(idpx)
    $('#paciente').val(px)
    $('#modalPx').modal('hide')
  })



  $('#descuento').on('change keyup paste click', function () {
    descuento = $('#descuento').val()
    subtotal = $('#subtotal').val()
    if (descuento.length > 0) {
      if (parseFloat(descuento) > 0) {
        if (parseFloat(descuento) <= parseFloat(subtotal)) {
          calculodes()
        } else {
          $('#total').val(0)
        }
      } else {
        $('#total').val($('#subtotal').val())
      }
    } else {
      $('#total').val($('#subtotal').val())
    }
  })

  function calculodes() {
    descuento = $('#descuento').val()
    gtotal = $('#subtotal').val()
    gtotal = round(gtotal - descuento, 2)
    $('#total').val(gtotal)
  }

  function round(value, decimals) {
    return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals)
  }
})

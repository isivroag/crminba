$(document).ready(function () {
    var id_pres = $("#id_pres").val();
    var folio_venta = $("#folio_venta").val();

    if (id_pres && !folio_venta) {
        cargarPresupuesto(id_pres);
        console.log("Cargando presupuesto con ID:", id_pres);
    }
    if (folio_venta) {
        
        consultarVenta(folio_venta);
        console.log("Consultando venta con folio:", folio_venta);
        
    }

        $("#btnBuscarPresupuesto").click(function () {
            $("#modalPresupuestos").modal("show");
            cargarPresupuestos();
        });

    $("#btnBuscarVendedor").click(function () {
        $("#modalVendedor").modal("show");
        
    });

    $("#tablaVendedores").on("click", ".btnSeleccionarVendedor", function () {
        fila = $(this).closest("tr");
        id_vendedor = fila.find("td:eq(0)").text();
        nombre = fila.find("td:eq(1)").text();
        $("#id_vendedor").val(id_vendedor);
        $("#nombre_vendedor").val(nombre);
        $("#modalVendedor").modal("hide");
        
    });

    $("#tablaPresupuestos").on("click", ".btnSeleccionarPres", function () {
        const id_pres = $(this).data("id");
        $("#id_pres").val(id_pres);
        $("#modalPresupuestos").modal("hide");
        cargarPresupuesto(id_pres);
    });

    $("#btnGuardarVenta").click(function () {
        let datos = {
            id_pres: $("#id_pres").val(),
            fecha: $("#fecha_venta").val(),
            id_vendedor: $("#id_vendedor").val(),
            nombre_vendedor: $("#nombre_vendedor").val(),
            tipo_venta: $("#tipo").val()
        };
        $.ajax({
            url: "bd/guardar_venta.php",
            type: "POST",
            dataType: "json",
            data: datos,
            success: function (resp) {
                if (resp.success) {
                    Swal.fire("¡Venta registrada!", "Folio venta: " + resp.folio_venta, "success");
                    $("#folio_venta").val(resp.folio_venta);
                    $("#btnGuardarVenta").hide();
                    consultarVenta(resp.folio_venta);
                } else {
                    Swal.fire("Error", resp.message, "error");
                }
            },
            error: function () {
                Swal.fire("Error", "No se pudo guardar la venta", "error");
            }
        });
    });
/*
    $("#btnConsultarVenta").click(function () {
        let folio_venta = $("#folio_venta").val();
        if (!folio_venta) {
            Swal.fire("Error", "Ingresa el folio de venta", "error");
            return;
        }
        consultarVenta(folio_venta);
    });*/

    function cargarPresupuesto(id_pres) {
        $.get("bd/guardar_venta.php", { id_pres: id_pres }, function (resp) {
            if (resp.success) {
                console.log(resp.presupuesto);
                // Llena todos los campos del formulario con los datos del presupuesto
                $("#id_pres").val(resp.presupuesto.id_pres).prop("readonly", true);
                //$("#fecha_venta").val(resp.presupuesto.fecha_pres).prop("readonly", true);
                $("#id_proyecto").val(resp.presupuesto.id_proy).prop("readonly", true);
                $("#proyecto").val(resp.presupuesto.nproyecto).prop("readonly", true);
                $("#id_manzana").val(resp.presupuesto.id_man).prop("readonly", true);
                $("#manzana").val(resp.presupuesto.nmanzana).prop("readonly", true);
                $("#id_clie").val(resp.presupuesto.id_clie).prop("readonly", true);
                $("#nombre_clie").val(resp.presupuesto.nombre_clie).prop("readonly", true);
                $("#id_lote").val(resp.presupuesto.id_lote).prop("readonly", true);
                $("#lote").val(resp.presupuesto.nlote).prop("readonly", true);
                $("#frente").val(resp.presupuesto.frente).prop("readonly", true);
                $("#fondo").val(resp.presupuesto.fondo).prop("readonly", true);
                $("#superficie").val(resp.presupuesto.superficie).prop("readonly", true);
                $("#tipolote").val(resp.presupuesto.tipo).prop("readonly", true);
                $("#preciom").val(parseFloat(resp.presupuesto.preciom).toLocaleString("es-MX", { style: "currency", currency: "MXN" })).prop("readonly", true);
                $("#valortotal").val(parseFloat(resp.presupuesto.importe).toLocaleString("es-MX", { style: "currency", currency: "MXN" })).prop("readonly", true);
                $("#fechaInicio").val(resp.presupuesto.inicial).prop("readonly", true);
                $("#montoTotal").val(parseFloat(resp.presupuesto.importe).toLocaleString("es-MX", { style: "currency", currency: "MXN" })).prop("readonly", true);
                $("#descuentopor").val(resp.presupuesto.pordescuento).prop("readonly", true);
                $("#descuento").val(parseFloat(resp.presupuesto.descuento).toLocaleString("es-MX", { style: "currency", currency: "MXN" })).prop("readonly", true);
                $("#valorop").val(parseFloat(resp.presupuesto.valorop).toLocaleString("es-MX", { style: "currency", currency: "MXN" })).prop("readonly", true);
                $("#enganchepor").val(resp.presupuesto.enganchepor).prop("readonly", true);
                $("#montoEnganche").val(parseFloat(resp.presupuesto.enganche).toLocaleString("es-MX", { style: "currency", currency: "MXN" })   ).prop("readonly", true);
                $("#plazosEnganche").val(resp.presupuesto.nenganche).prop("readonly", true);
                $("#plazosSinInteres").val(resp.presupuesto.nmsi).prop("readonly", true);
                $("#plazosConInteres").val(resp.presupuesto.nmci).prop("readonly", true);
                $("#id_vendedor").val(resp.presupuesto.id_col).prop("readonly", true);
                $("#nombre_vendedor").val(resp.presupuesto.nombre_col).prop("readonly", true);

                // Si tienes más campos, agrégalos aquí

                $("#btnGuardarVenta").show();
                mostrarCorrida(resp.detalle);

                // Opcional: deshabilita todos los inputs excepto los necesarios para guardar
                $("#formVenta input").prop("readonly", true);
                if(!folio_venta){
                    $("#fecha_venta, #id_vendedor, #nombre_vendedor").prop("readonly", false);
                }else{
                    $("#fecha_venta, #id_vendedor, #nombre_vendedor").prop("readonly", true);
                    $("#tipo").prop("disabled", true);
                    $("#btnGuardarVenta").show();
                }
            } else {
                Swal.fire("Error", resp.message, "error");
            }
        }, "json");
    }

    function consultarVenta(folio_venta) {
        $.get("bd/guardar_venta.php", { folio_venta: folio_venta }, function (resp) {
            if (resp.success) {
                // Llena todos los campos del formulario con los datos de la venta
                $("#folio_venta").val(resp.venta.folio_venta).prop("readonly", true);
                $("#id_pres").val(resp.venta.id_pres).prop("readonly", true);
                id_pres = resp.venta.id_pres;
                $("#fecha_venta").val(resp.venta.fecha).prop("readonly", true);
                
                $("#id_vendedor").val(resp.venta.id_vendedor).prop("readonly", true);
                $("#nombre_vendedor").val(resp.venta.nombre_vendedor).prop("readonly", true);
                $("#tipo_venta").val(resp.venta.tipo_venta).prop("readonly", true);

                cargarPresupuesto(id_pres);

                $("#btnGuardarVenta").hide();
                //mostrarCorrida(resp.cuenta);

                // Deshabilita todos los campos
                $("#formVenta input").prop("readonly", true);
            } else {
                Swal.fire("Error", resp.message, "error");
            }
        }, "json");
    }

    function mostrarCorrida(detalle) {
        let html = `<table class="table table-striped table-bordered table-hover table-sm table-condensed">
            <thead class="bg-green">
                <tr>
                    <th>No.</th>
                    <th>Fecha</th>
                    <th>Capital</th>
                    <th>Interés</th>
                    <th>Importe</th>
                    <th>Tipo</th>
                    <th>Saldo Insoluto</th>
                </tr>
            </thead>
            <tbody>`;
        detalle.forEach(function (pago, idx) {
            html += `<tr>
                <td>${idx + 1}</td>
                <td>${pago.fecha}</td>
                <td class="text-right">${parseFloat(pago.capital).toLocaleString("es-MX", { style: "currency", currency: "MXN" })}</td>
                <td class="text-right">${parseFloat(pago.interes).toLocaleString("es-MX", { style: "currency", currency: "MXN" })}</td>
                <td class="text-right">${parseFloat(pago.importe).toLocaleString("es-MX", { style: "currency", currency: "MXN" })}</td>
                <td>${pago.tipo}</td>
                <td class="text-right">${parseFloat(pago.saldo).toLocaleString("es-MX", { style: "currency", currency: "MXN" })}</td>
            </tr>`;
        });
        html += `</tbody></table>`;
        $("#corridaTable").html(html);
    }

    function cargarPresupuestos() {
        $.get("bd/guardar_venta.php", { buscar_presupuestos: 1 }, function (resp) {
            if (resp.success) {
                let rows = "";
                resp.presupuestos.forEach(function (row) {
                    rows += `<tr>
                        <td class="text-center">${row.id_pres}</td>
                        <td class="text-center">${row.fecha_pres}</td>
                        <td>${row.cliente}</td>
                        <td>${row.proyecto}</td>
                        <td>${row.manzana}</td>
                        <td class="text-center">${row.lote}</td>
                        <td class="text-right">${parseFloat(row.valorop).toLocaleString()}</td>
                        <td class="text-right">${parseFloat(row.enganche).toLocaleString()}</td>
                        <td class="text-center">${row.nenganche}</td>
                        <td class="text-center">${row.nmsi}</td>
                        <td class="text-center">${row.nmci}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-success btnSeleccionarPres" data-id="${row.id_pres}">
                                <i class="fas fa-check"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                $("#tablaPresupuestos tbody").html(rows);
                $("#tablaPresupuestos").DataTable({
                    destroy: true,
                    responsive: true,
                    paging: true,
                    searching: true,
                    info: false,
                    ordering: false,
                    language: {
                        "decimal": "",
                        "emptyTable": "No hay datos disponibles en la tabla",
                        "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                        "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
                        "infoFiltered": "(filtrado de _MAX_ entradas totales)",
                        "lengthMenu": "Mostrar _MENU_ entradas",
                        "loadingRecords": "Cargando...",
                        "processing": "Procesando...",
                        "search": "Buscar:",
                        "zeroRecords": "No se encontraron registros coincidentes",
                        "paginate": {
                            "first": "Primero",
                            "last": "Último",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        }
                    }
                });
            } else {
                $("#tablaPresupuestos tbody").html('<tr><td colspan="12" class="text-center text-danger">No hay presupuestos disponibles</td></tr>');
            }
        }, "json");
    }
});
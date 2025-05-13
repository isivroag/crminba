$(document).ready(function () {
    // Buscar prospecto al escribir
    $('#buscar_prospecto').on('keyup', function () {
        var texto = $(this).val();
        if (texto.length >= 3) {
            $.ajax({
                url: 'bd/buscar_prospecto.php',
                type: 'POST',
                dataType: 'json',
                data: { texto: texto },
                success: function (data) {
                    var tabla = $('#tablaBusqueda tbody');
                    tabla.empty();
                    if (data.length > 0) {
                        data.forEach(function (row) {
                            tabla.append(`
                                <tr>
                                    <td>${row.id_pros}</td>
                                    <td>${row.nombre}</td>
                                    <td>${row.correo}</td>
                                    <td><button class="btn btn-success btn-sm seleccionar-prospecto" data-id="${row.id_pros}">Ver</button></td>
                                </tr>
                            `);
                        });
                    } else {
                        tabla.append('<tr><td colspan="4" class="text-center">Sin resultados</td></tr>');
                    }
                }
            });
        }
    });

    // Mostrar timeline al seleccionar prospecto
    $(document).on('click', '.seleccionar-prospecto', function () {
        var id_pros = $(this).data('id');
        $.ajax({
            url: 'bd/obtener_seguimientos.php',
            type: 'POST',
            dataType: 'json',
            data: { id_pros: id_pros },
            success: function (seguimientos) {
                var timeline = $('#timelineSeguimientos');
                timeline.empty();

                if (seguimientos.length === 0) {
                    timeline.append('<p>No hay seguimientos para este prospecto.</p>');
                    return;
                }

                var lista = $('<ul class="timeline"></ul>');
                seguimientos.forEach(function (seg) {
                    var estado = seg.edo_pros == 1 ? 'Activo' : 'Inactivo';
                    lista.append(`
                        <li>
                            <div class="timeline-time">${seg.fecha_seg} (${seg.tipo_seg})</div>
                            <div class="timeline-body">
                                <p><strong>Realizado:</strong> ${seg.realizado == 1 ? 'Sí' : 'No'}</p>
                                <p><strong>Colaborador:</strong> ${seg.nom_col_seg}</p>
                                <p><strong>Observaciones:</strong> ${seg.observaciones}</p>
                                <p><strong>Estado del prospecto:</strong> ${estado}</p>
                            </div>
                        </li>
                    `);
                });

                timeline.append(lista);
            },
            error: function () {
                Swal.fire('Error', 'No se pudieron obtener los seguimientos.', 'error');
            }
        });
    });
});

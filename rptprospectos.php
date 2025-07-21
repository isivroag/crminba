<?php
$pagina = "rptprospectos";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();
if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];
} else {
    $fecha = date('Y-m-d');
}


// Consulta para obtener prospectos activos (edo_pros = 1)
$consulta = "SELECT * from vtotalprosmes WHERE ejercicio = YEAR('$fecha') ";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

$consultaorg = "SELECT * from vtotalprosmesorg WHERE ejercicio = YEAR('$fecha') AND mes_id = MONTH('$fecha')";
$resultadoorg = $conexion->prepare($consultaorg);
$resultadoorg->execute();
$dataorg = $resultadoorg->fetchAll(PDO::FETCH_ASSOC);


// Nueva consulta para todos los meses y orígenes del año
$consultaOrgAnual = "SELECT * from vtotalprosmesorg WHERE ejercicio = YEAR('$fecha') ORDER BY mes_id, origen";
$resultadoOrgAnual = $conexion->prepare($consultaOrgAnual);
$resultadoOrgAnual->execute();
$dataOrgAnual = $resultadoOrgAnual->fetchAll(PDO::FETCH_ASSOC);

$origenesAnual = [];
$mesesAnual = [];
$totalPorOrigen = [];
$totalPorMes = [];

$origenes = [];
$meses = [];

foreach ($dataOrgAnual as $row) {
    $origen = $row['origen'];
    $mes = $row['mes_nombre'];
    $cantidad = $row['nprospectos'];

    $mesesAnual[$mes] = true;
    $origenesAnual[$origen][$mes] = $cantidad;

    // Calcular totales
    if (!isset($totalPorOrigen[$origen])) {
        $totalPorOrigen[$origen] = 0;
    }
    if (!isset($totalPorMes[$mes])) {
        $totalPorMes[$mes] = 0;
    }

    $totalPorOrigen[$origen] += $cantidad;
    $totalPorMes[$mes] += $cantidad;
}


$labels = [];
$values = [];

$mesesAnual = array_keys($mesesAnual);
$origenesLista = array_keys($origenesAnual);

foreach ($dataorg as $row) {
    $label = $row['mes_nombre'] . " - " . $row['origen'];
    $labels[] = $label;
    $values[] = $row['nprospectos'];
}
$meses = array_keys($meses);
sort($meses); // ordenar meses si es necesario

$message = "";
?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<style>
    .starchecked {
        color: rgba(255, 195, 0, 100)
    }

    .multi-line {
        white-space: normal;
        width: 250px;
    }

    .badge-asignado {
        background-color: #28a745;
    }

    .badge-seguimiento {
        background-color: #17a2b8;
    }

    .badge-finalizado {
        background-color: #6c757d;
    }

    .badge-pendiente {
        background-color: #6c757d;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="card">
            <div class="card-header  bg-green text-light">
                <h1 class="card-title mx-auto">ESTADISTICAS DE PROSPECTOS</h1>
            </div>

            <div class="card-body">

                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <!-- GRAFICA DE ML VENDIDOS-->
                        <div class="col-sm-12">
                            <div class="card ">
                                <div class="card-header bg-green color-palette border-0">
                                    <h3 class="card-title">
                                        <i class="fas fa-th mr-1"></i>
                                        Prospectos nuevos por mes Ejercicio <?php echo date('Y', strtotime($fecha)); ?>
                                    </h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn bg-green btn-sm" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>

                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row justify-content-center">
                                        <div class="col-sm-10">
                                            <canvas class="chart " id="line-chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row justify-content-center">
                                        <div class="col-sm-12 justify-content-center ">
                                            <div class="table-responsive d-flex justify-content-center">
                                                <table class="table table-responsive table-bordered table-hover table-sm w-auto">
                                                    <thead class="text-center bg-green">
                                                        <tr>
                                                            <th>Mes</th>
                                                            <?php foreach ($data as $rowml) : ?>
                                                                <th><?php echo $rowml['mes_nombre']; ?></th>
                                                            <?php endforeach; ?>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td># Prospectos</td>
                                                            <?php
                                                            $totalml = 0;
                                                            foreach ($data as $rowml) {
                                                                $totalml += $rowml['nprospectos'];
                                                            ?>
                                                                <td class="text-right"><?php echo $rowml['nprospectos']; ?></td>
                                                            <?php } ?>
                                                            <td class="text-right text-bold"><?php echo $totalml; ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->

                                <!-- /.card-footer -->
                            </div>
                        </div>
                        <!-- GRAFICA DE VENTAS-->
                        <div class="col-sm-6">
                            <div class="card ">
                                <div class="card-header bg-green color-palette border-0">
                                    <h3 class="card-title">
                                        <i class="fas fa-th mr-1"></i>
                                        Origen de Prospectos por Mes Ejercicio <?php echo date('Y', strtotime($fecha)); ?>
                                    </h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn bg-green btn-sm" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>

                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row justify-content-center">
                                        <div class="col-sm-2">
                                            <form id="formMes" method="get" action="">
                                                <select class="form-control" name="fecha" id="selectMes" onchange="document.getElementById('formMes').submit();">
                                                    <?php
                                                    $anioActual = date('Y', strtotime($fecha));
                                                    for ($m = 1; $m <= 12; $m++) {
                                                        setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
                                                        $mesNombre = strftime('%B', mktime(0, 0, 0, $m, 1, $anioActual));
                                                        $mesNombre = ucfirst($mesNombre);
                                                        $mesNombre = ucfirst($mesNombre);
                                                        $fechaMes = sprintf('%04d-%02d-01', $anioActual, $m);
                                                        $selected = (date('Y-m', strtotime($fecha)) == sprintf('%04d-%02d', $anioActual, $m)) ? 'selected' : '';
                                                        echo "<option value=\"$fechaMes\" $selected>$mesNombre</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </form>
                                        </div>

                                    </div>
                                    <br>
                                    <div class="row justify-content">
                                        <div class="col-sm-7">
                                            <canvas class="chart " id="line-chart2" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                        </div>
                                        <div class="col-sm-5 my-auto">
                                            <div class="table-responsive">
                                                <table class="table table-responsive table-bordered table-hover table-sm">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th>Origen</th>
                                                            <th>Prospectos</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $totalvtasml = 0;
                                                        foreach ($dataorg as $rowml) {
                                                            $totalvtasml += $rowml['nprospectos'];
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <?php
                                                                    switch (strtolower($rowml['origen'])) {
                                                                        case 'facebook':
                                                                            echo '<i class="fab fa-facebook text-primary"></i> Facebook';
                                                                            break;
                                                                        case 'instagram':
                                                                            echo '<i class="fab fa-instagram text-danger"></i> Instagram';
                                                                            break;
                                                                        case 'web':
                                                                            echo '<i class="fas fa-globe text-info"></i> Web';
                                                                            break;
                                                                        case 'whatsapp':
                                                                            echo '<i class="fab fa-whatsapp text-success"></i> WhatsApp';
                                                                            break;
                                                                        case 'llamada':
                                                                            echo '<i class="fas fa-phone text-dark"></i> Llamada';
                                                                            break;
                                                                        case 'vendedor':
                                                                            echo '<i class="fas fa-user text-green"></i> Vendedor';
                                                                            break;
                                                                        default:
                                                                            echo ucfirst($rowml['origen']);
                                                                            break;
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td class="text-right"><?php echo $rowml['nprospectos'] ?></td>
                                                            </tr>
                                                        <?php } ?>

                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td>Total Prospectos <?php echo $mes ?></td>
                                                            <td class="text-right text-bold"><?php echo $totalvtasml ?></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header bg-green color-palette border-0">
                                            <h3 class="card-title">
                                                <i class="fas fa-chart-bar mr-1"></i>
                                                Origen de Prospectos por Mes - Ejercicio <?php echo date('Y', strtotime($fecha)); ?>
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn bg-green btn-sm" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <!-- Gráfica -->
                                            <div class="row justify-content-center mb-4">
                                                <div class="col-sm-12">
                                                    <canvas class="chart" id="line-chart-anual" style="min-height: 400px; height: 400px; max-height: 400px; max-width: 100%;"></canvas>
                                                </div>
                                            </div>

                                            <!-- Tabla -->
                                            <div class="row justify-content-center">
                                                <div class="col-sm-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-hover table-sm">
                                                            <thead class="text-center bg-green">
                                                                <tr>
                                                                    <th rowspan="2" class="align-middle">ORIGEN</th>
                                                                    <th colspan="<?php echo count($mesesAnual); ?>" class="text-center">MESES</th>
                                                                    <th rowspan="2" class="align-middle">TOTAL</th>
                                                                </tr>
                                                                <tr>
                                                                    <?php foreach ($mesesAnual as $mes): ?>
                                                                        <th><?php echo substr($mes, 0, 3); ?></th>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($origenesLista as $origen): ?>
                                                                    <tr>
                                                                        <td class="font-weight-bold">
                                                                            <?php
                                                                            switch (strtolower($origen)) {
                                                                                case 'facebook':
                                                                                    echo '<i class="fab fa-facebook text-primary"></i> Facebook';
                                                                                    break;
                                                                                case 'instagram':
                                                                                    echo '<i class="fab fa-instagram text-danger"></i> Instagram';
                                                                                    break;
                                                                                case 'web':
                                                                                    echo '<i class="fas fa-globe text-info"></i> Web';
                                                                                    break;
                                                                                case 'whatsapp':
                                                                                    echo '<i class="fab fa-whatsapp text-success"></i> WhatsApp';
                                                                                    break;
                                                                                case 'llamada':
                                                                                    echo '<i class="fas fa-phone text-dark"></i> Llamada';
                                                                                    break;
                                                                                case 'vendedor':
                                                                                    echo '<i class="fas fa-user text-green"></i> Vendedor';
                                                                                    break;
                                                                                default:
                                                                                    echo ucfirst($origen);
                                                                                    break;
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <?php foreach ($mesesAnual as $mes): ?>
                                                                            <td class="text-center">
                                                                                <?php echo isset($origenesAnual[$origen][$mes]) ? $origenesAnual[$origen][$mes] : 0; ?>
                                                                            </td>
                                                                        <?php endforeach; ?>
                                                                        <td class="text-center font-weight-bold bg-light">
                                                                            <?php echo $totalPorOrigen[$origen]; ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                            <tfoot class="bg-light">
                                                                <tr class="font-weight-bold">
                                                                    <td>TOTAL POR MES</td>
                                                                    <?php
                                                                    $granTotal = 0;
                                                                    foreach ($mesesAnual as $mes):
                                                                        $total = isset($totalPorMes[$mes]) ? $totalPorMes[$mes] : 0;
                                                                        $granTotal += $total;
                                                                    ?>
                                                                        <td class="text-center"><?php echo $total; ?></td>
                                                                    <?php endforeach; ?>
                                                                    <td class="text-center bg-success text-white"><?php echo $granTotal; ?></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
    $(function() {
        var barChartCanvas = $('#line-chart').get(0).getContext('2d');

        // Paleta de colores (12 colores)
        var coloresMeses = [
            'rgba(255, 99, 132, 0.6)', // Enero
            'rgba(54, 162, 235, 0.6)', // Febrero
            'rgba(255, 206, 86, 0.6)', // Marzo
            'rgba(75, 192, 192, 0.6)', // Abril
            'rgba(153, 102, 255, 0.6)', // Mayo
            'rgba(255, 159, 64, 0.6)', // Junio
            'rgba(199, 199, 199, 0.6)', // Julio
            'rgba(255, 205, 86, 0.6)', // Agosto
            'rgba(54, 162, 100, 0.6)', // Septiembre
            'rgba(201, 203, 207, 0.6)', // Octubre
            'rgba(100, 149, 237, 0.6)', // Noviembre
            'rgba(255, 87, 34, 0.6)' // Diciembre
        ];

        var barChartData = {
            labels: [
                <?php foreach ($data as $d) : ?> "<?php echo $d['mes_nombre'] ?>",
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Prospectos Nuevos por Mes',
                data: [
                    <?php foreach ($data as $d) : ?>
                        <?php echo $d['nprospectos']; ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: coloresMeses,
                borderColor: coloresMeses.map(color => color.replace('0.6', '1')),
                borderWidth: 1
            }]
        };

        var barChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    }
                }
            }
        };

        new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        });

        var ctx2 = $('#line-chart2').get(0).getContext('2d');

        // Mapeo de colores por origen
        var origenColors = {
            'facebook': 'rgba(59, 89, 152, 0.7)', // Azul Facebook
            'instagram': 'rgba(225, 48, 108, 0.7)', // Rosa Instagram
            'web': 'rgba(23, 162, 184, 0.7)', // Azul info
            'whatsapp': 'rgba(37, 211, 102, 0.7)', // Verde WhatsApp
            'llamada': 'rgba(52, 58, 64, 0.7)', // Gris oscuro
            'vendedor': 'rgba(40, 167, 69, 0.7)', // Verde vendedor
            'default': 'rgba(153, 102, 255, 0.7)' // Default
        };

        // Generar los colores para cada barra según el origen
        var labels = <?php echo json_encode($labels); ?>;
        var data = <?php echo json_encode($values); ?>;
        var dataorg = <?php echo json_encode($dataorg); ?>;
        var backgroundColors = [];

        // labels = ["Enero - Facebook", "Enero - Instagram", ...]
        labels.forEach(function(label) {
            var origen = label.split(' - ').pop().toLowerCase();
            backgroundColors.push(origenColors[origen] || origenColors['default']);
        });

        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Prospectos por Mes y Origen',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(c => c.replace('0.7', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 90,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                }
            }
        });




     
var ctx3 = $('#line-chart-anual').get(0).getContext('2d');

// Preparar datos para la gráfica anual
var mesesAnual = <?php echo json_encode($mesesAnual); ?>;
var origenesLista = <?php echo json_encode($origenesLista); ?>;
var origenesAnual = <?php echo json_encode($origenesAnual); ?>;

// Colores para cada origen (más contrastantes)
var origenColores = {
    'facebook': {
        background: 'rgba(24, 119, 242, 0.8)',
        border: 'rgba(24, 119, 242, 1)'
    },
    'instagram': {
        background: 'rgba(225, 48, 108, 0.8)',
        border: 'rgba(225, 48, 108, 1)'
    },
    'web': {
        background: 'rgba(0, 123, 255, 0.8)',
        border: 'rgba(0, 123, 255, 1)'
    },
    'whatsapp': {
        background: 'rgba(37, 211, 102, 0.8)',
        border: 'rgba(37, 211, 102, 1)'
    },
    'llamada': {
        background: 'rgba(108, 117, 125, 0.8)',
        border: 'rgba(108, 117, 125, 1)'
    },
    'vendedor': {
        background: 'rgba(40, 167, 69, 0.8)',
        border: 'rgba(40, 167, 69, 1)'
    },
    'referido': {
        background: 'rgba(255, 193, 7, 0.8)',
        border: 'rgba(255, 193, 7, 1)'
    },
    'evento': {
        background: 'rgba(220, 53, 69, 0.8)',
        border: 'rgba(220, 53, 69, 1)'
    }
};

// Generar datasets para cada origen
var datasets = [];
origenesLista.forEach(function(origen, index) {
    var data = [];
    mesesAnual.forEach(function(mes) {
        data.push(origenesAnual[origen][mes] || 0);
    });

    var color = origenColores[origen.toLowerCase()] || {
        background: 'rgba(' + (index * 60 + 80) + ', ' + (index * 40 + 100) + ', ' + (index * 50 + 120) + ', 0.8)',
        border: 'rgba(' + (index * 60 + 80) + ', ' + (index * 40 + 100) + ', ' + (index * 50 + 120) + ', 1)'
    };

    datasets.push({
        label: origen.charAt(0).toUpperCase() + origen.slice(1),
        data: data,
        backgroundColor: color.background,
        borderColor: color.border,
        borderWidth: 1,
        borderRadius: 4,
        borderSkipped: false,
    });
});

new Chart(ctx3, {
    type: 'bar', // Cambio de 'line' a 'bar'
    data: {
        labels: mesesAnual,
        datasets: datasets
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Meses',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                },
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Número de Prospectos',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                },
                ticks: {
                    stepSize: 1,
                    precision: 0,
                    callback: function(value) {
                        return Number.isInteger(value) ? value : '';
                    }
                },
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 20,
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleColor: 'white',
                bodyColor: 'white',
                borderColor: 'rgba(255,255,255,0.2)',
                borderWidth: 1,
                callbacks: {
                    title: function(context) {
                        return 'Mes: ' + context[0].label;
                    },
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y + ' prospectos';
                    },
                    afterBody: function(context) {
                        var total = 0;
                        context.forEach(function(item) {
                            total += item.parsed.y;
                        });
                        return ['', 'Total del mes: ' + total + ' prospectos'];
                    }
                }
            }
        },
        // Animación suave
        animation: {
            duration: 1000,
            easing: 'easeOutQuart'
        }
    }
});
    });
</script>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/rptprospectos.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-es_ES.min.js"></script>
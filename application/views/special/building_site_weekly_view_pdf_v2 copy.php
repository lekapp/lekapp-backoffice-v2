<style>
    .text-center {
        text-align: center;
    }

    body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 12px;
        line-height: 1.42857143;
        color: #333;
        background-color: #fff;
    }

    .td-10 {
        width: 10%;
    }

    .td-15 {
        width: 15%;
    }

    .td-40 {
        width: 40%;
    }

    .td-25 {
        width: 25%;
    }

    .td-50 {
        width: 50%;
    }

    .td-100 {
        width: 100%;
    }

    .print-utility {
        page-break-after: always;
        /*page-break-before: always;*/
        page-break-inside: avoid;
    }

    .table {
        border: #333;
    }
</style>

<?php
// print_r($data->json_data);
$json_data = json_decode($data->json_data);
?>

<section class="main" id="printable">
    <div class="container-fluid">
        <div class="row ">
            <div class="col-sm-12">

                <div class="row">
                    <div class="d-md-none ">
                        <div class="row">
                            <div class="col-6 ">
                                <img class="col-4 img-fluid float-left" src="<?php echo base_url('assets/images/logo.png'); ?>">
                            </div>
                            <div class="col-6 ">
                                <img class="col-4 img-fluid float-right float-sm-right" src="<?php echo base_url('assets/images/_logo.png'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="d-none d-md-block col-md-2 offset-lg-1 col-lg-1 ">
                        <img class=" col-12" src="<?php echo base_url('assets/images/logo.png'); ?>" />
                    </div>
                    <div class="col-md-8 text-center">
                        <h3>
                            Reporte Semanal de Actividades -
                            <?php
                            $dt = new DateTime($data->activity_date, new DateTimeZone('America/Santiago'));
                            echo $dt->format('d-m-Y');
                            echo "/";
                            echo $data->report_no ?> (Semana <?php echo $json_data->cw ?>)
                        </h3>
                    </div>
                    <div class="d-none d-md-block col-md-2 col-lg-1 ">
                        <img class="col-12" src="<?php echo base_url('assets/images/_logo.png'); ?>" />
                    </div>
                </div>

                <div class="row ">
                    <div class="col-sm-1 ">
                    </div>
                    <div class="col-sm-6 ">
                        <label class="font-weight-bold">Nombre del Contrato</label>
                        <span><?php echo $data->building_site->name; ?></span>
                    </div>
                    <div class="col-sm-4">
                        <label class="font-weight-bold">Informe Nº</label>
                        <span><?php echo $data->report_no; ?></span>
                    </div>
                </div>
                <div class="row ">
                    <div class="col-sm-1 ">
                    </div>
                    <div class="col-sm-6">
                        <label class="font-weight-bold">Programa base</label>
                        <span><?php // echo $data->report_no->name;  
                                ?></span>
                    </div>
                    <div class="col-sm-4">
                        <label class="font-weight-bold">Inicio</label>
                        <span><?php echo $json_data->fd; ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-1 ">
                    </div>
                    <div class="col-sm-6">
                        <label class="font-weight-bold">T&eacute;rmino</label>
                        <span><?php echo $json_data->ld; ?></span>
                    </div>
                    <div class="col-sm-4">
                        <label class="font-weight-bold">Duraci&oacute;n</label>
                        <span><?php echo $json_data->days; ?> d&iacute;as</span>
                    </div>

                </div>

            </div>
        </div>

        <br>
        <hr>
        <br>

        <?php

        // LOGICA ACUMULADO
        $ts = 0;
        $pp = 0;
        $rp = 0;
        $vp = 0;
        $pa = 0;
        $ra = 0;
        $va = 0;
        foreach ($json_data->as as $s => $speciality) {
            $ts += $json_data->rst->{$s};
        }
        foreach ($json_data->as as $s => $speciality) {
            $pp += isset($speciality->info->prev_week) ? $speciality->info->prev_week * 100 : 0;
            $rp += isset($json_data->rr->{$s}) ? $json_data->rr->{$s}->prev_week * 100 : 0;
            $pa += isset($speciality->info->this_week) ? $speciality->info->this_week * 100 : 0;
            $ra += isset($json_data->rr->{$s}) ? $json_data->rr->{$s}->this_week * 100 : 0;
        }
        $vp = ($rp - $pp) / $ts;
        $pp /= $ts;
        $rp /= $ts;
        $va = ($ra - $pa) / $ts;
        $pa /= $ts;
        $ra /= $ts;
        $vpa = $vp + $va;
        ?>

        <?php
        /**
         * 
         */
        function table_data($title = '', $programado = 0, $real = 0, $variacion = 0)
        {
            echo " 
                <h3>{$title}</h3> 
                <center>
                    <table class=\"table table-responsive \">
                        <thead>
                            <tr>
                                <th>Programado</th>
                                <th>Real</th>
                                <th>Variaci&oacute;n</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>";
            echo round($programado, 2);
            echo "%</td>
                                <td>";
            echo round($real, 2);
            echo "% </td>
                                <td>";
            echo round($variacion, 2);
            echo "% </td>
                            </tr>
                        </tbody>
                    </table>
                </center>";
        }
        ?>

        <div class="row">
            <div class="col-12">
                <div class="row ">

                    <!-- Total Contrato -->
                    <div class="col-12 text-center">

                        <h1>Total Contrato</h1>

                        <label class="font-weight-bold">HH Total</label>
                        <span><?php echo $ts; ?></span>
                    </div>

                    <!-- Acumulado anterior -->
                    <div class="col-4 text-center">
                        <?php table_data("Acumulado anterior", $pp, $rp, $vp); ?>
                    </div>

                    <!-- Semana actual -->
                    <div class="col-4 text-center">
                        <?php table_data("Semana actual", $pa, $ra, $va); ?>
                    </div>
                    <!-- Acumulado actual -->
                    <div class="col-4 text-center">
                        <?php table_data("Acumulado actual", round($pp + $pa, 2), round($rp + $ra, 2), round($vpa, 2)); ?>
                    </div>
                </div>
            </div>

        </div>

        <br>
        <hr>
        <br>

        <div class="row print-utility">
            <div class="col-12">

                <?php foreach ($json_data->as as $s => $speciality) : ?>
                    <div class="row">
                        <!-- $speciality->name -->
                        <div class="col-12 text-center">

                            <h1><?php echo $speciality->name; ?></h1>

                            <label class="font-weight-bold">HH Total</label>

                            <span><?php echo $json_data->rst->{$s}; ?></span>

                        </div>

                        <!-- Acumulado anterior -->
                        <div class="col-4 text-center">
                            <?php
                            $pp = isset($speciality->info->prev_week) ? $speciality->info->prev_week / $json_data->rst->{$s} * 100 : 0;

                            $rp = 0;
                            $rp = isset($json_data->rr->{$s}) ? $json_data->rr->{$s}->prev_week / $json_data->rst->{$s} * 100 : 0;

                            $vp = $rp - $pp;

                            table_data("Acumulado anterior", round($pp, 2), round($rp, 2), round($vp, 2));
                            ?>
                        </div>

                        <!-- Semana actual -->
                        <div class="col-4 text-center">
                            <?php
                            $pa = isset($speciality->info->this_week) ? $speciality->info->this_week / $json_data->rst->{$s} * 100 : 0;

                            $ra = 0;
                            $ra = isset($json_data->rr->{$s}) ? $json_data->rr->{$s}->this_week / $json_data->rst->{$s} * 100 : 0;

                            $va = $ra - $pa;

                            table_data("Semana actual", round($pa, 2), round($ra, 2), round($va, 2));
                            ?>
                        </div>

                        <!-- Acumulado actual -->
                        <div class="col-4 text-center">
                            <?php
                            table_data("Acumulado actual", round($pp + $pa, 2), round($rp + $ra, 2), round($vp + $va, 2));
                            ?>
                        </div>

                    </div>
                <?php endforeach; ?>

            </div>
        </div>

        <br>
        <hr>
        <br>

        <div class="row ">

            <div class="col-sm-12">

                <!-- POM-->
                <div class="col-12 text-center">
                    <h1>POM</h1>
                </div>

                <div class="">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th colspan="2">

                                </th>
                                <th colspan="4">
                                    PO
                                </th>
                                <th colspan="5">
                                    Acumulado anterior
                                </th>
                                <th colspan="5">
                                    Semana actual
                                </th>
                                <th colspan="5">
                                    Acumulado actual
                                </th>
                            </tr>

                            <tr>
                                <th>ID</th>
                                <th>Descripci&oacute;n</th>

                                <th>Unid.</th>
                                <th>Cant.</th>
                                <th>Rend.</th>
                                <th>HH</th>

                                <th>Cant.</th>
                                <th>HH GAN.</th>
                                <th>% Av.</th>
                                <th>HH GAST.</th>
                                <th>Pf</th>

                                <th>Cant.</th>
                                <th>HH GAN.</th>
                                <th>% Av.</th>
                                <th>HH GAST.</th>
                                <th>Pf</th>

                                <th>Cant.</th>
                                <th>HH GAN.</th>
                                <th>% Av.</th>
                                <th>HH GAST.</th>
                                <th>Pf</th>

                            </tr>

                        </thead>
                        <tbody>
                            <?php
                            $suma_thh = 0;

                            $suma_p_cant = 0;
                            $suma_phh_gan = 0;
                            $suma_pav = 0;
                            $suma_phh_gas = 0;
                            $suma_phh_pf = 0;

                            $suma_c_cant = 0;
                            $suma_chh_gan = 0;
                            $suma_cav = 0;
                            $suma_chh_gas = 0;
                            $suma_chh_pf = 0;
                            ?>
                            <?php foreach ($json_data->ac as $kt => $tramo) : ?>

                                <tr>

                                    <td><strong><?php echo $tramo->id; ?></strong></td>
                                    <?php //d($tramo->actividad->{$kt}->p[0]->zone); 
                                    ?>
                                    <td><strong><?php echo $tramo->actividad->{$kt}->p[0]->zone; ?></strong></td>
                                    <td colspan="19"></td>

                                </tr>

                                <?php foreach ($tramo->actividad as $key => $actividad) : ?>
                                    <?php if (sizeof($actividad->p) > 0) : ?>

                                        <tr>

                                            <td class="text-right">
                                                <?php echo $key ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $actividad->p[0]->name;
                                                ?>
                                            </td>

                                            <td>
                                                <?php echo $actividad->p[0]->unid; ?>
                                            </td>
                                            <td>
                                                <?php echo $actividad->p[0]->cant; ?>
                                            </td>
                                            <td>
                                                <?php echo round($actividad->p[0]->rend, 2); ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($actividad->p[0]->thh, 2);
                                                $suma_thh += $actividad->p[0]->thh; ?>
                                            </td>

                                            <td>
                                                <?php
                                                $p_cant = ($json_data->ra->{$kt}->prev_week->{$key}->pAvance) != null ? ($json_data->ra->{$kt}->prev_week->{$key}->pAvance) : 0;
                                                $suma_p_cant += $p_cant;
                                                echo number_format($p_cant, 2);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $phh_gan = ($json_data->ra->{$kt}->prev_week->{$key}->pAvance) != null ? ($actividad->p[0]->thh * $json_data->ra->{$kt}->prev_week->{$key}->pAvance) / 100 : 0;
                                                $suma_phh_gan += $phh_gan;
                                                echo number_format($phh_gan, 2);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $ppav = ($json_data->ra->{$kt}->prev_week->{$key}->pAvance / $actividad->p[0]->cant) != null ? ($json_data->ra->{$kt}->prev_week->{$key}->pAvance / $actividad->p[0]->cant) * 100 : 0;
                                                echo number_format($ppav, 2);
                                                ?>%
                                            </td>
                                            <td>
                                                <?php
                                                $phh_gas = ($json_data->ra->{$kt}->prev_week->{$key}->hh) != null ? ($json_data->ra->{$kt}->prev_week->{$key}->hh) : 0;
                                                $suma_phh_gas += $phh_gas;
                                                echo number_format($phh_gas, 2);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($phh_gan == 0) {
                                                    $ppf = 0;
                                                } else {
                                                    $ppf = $phh_gas / $phh_gan;
                                                }
                                                echo number_format($ppf, 2);
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                $c_cant = ($json_data->ra->{$kt}->this_week->{$key}->pAvance) != null ? ($json_data->ra->{$kt}->this_week->{$key}->pAvance) : 0;
                                                $suma_c_cant += $c_cant;
                                                echo number_format($c_cant, 2);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $chh_gan = ($json_data->ra->{$kt}->this_week->{$key}->pAvance) != null ? ($actividad->p[0]->thh * $json_data->ra->{$kt}->this_week->{$key}->pAvance) / 100 : 0;
                                                $suma_chh_gan += $chh_gan;
                                                echo number_format($chh_gan, 2);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $cpav = ($json_data->ra->{$kt}->this_week->{$key}->pAvance / $actividad->p[0]->cant) != null ? ($json_data->ra->{$kt}->this_week->{$key}->pAvance / $actividad->p[0]->cant) * 100 : 0;
                                                echo number_format($cpav, 2);
                                                ?>%
                                            </td>
                                            <td>
                                                <?php
                                                $chh_gas = ($json_data->ra->{$kt}->this_week->{$key}->hh) != null ? ($json_data->ra->{$kt}->this_week->{$key}->hh) : 0;
                                                $suma_chh_gas += $chh_gas;
                                                echo number_format($chh_gas, 2);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($chh_gan == 0) {
                                                    $cpf = 0;
                                                } else {
                                                    $cpf = $chh_gas / $chh_gan;
                                                }
                                                echo number_format($cpf, 2);
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo number_format($p_cant + $c_cant, 2);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo number_format($phh_gan + $chh_gan, 2);
                                                ?>
                                            </td>
                                            <td>

                                                <?php
                                                echo number_format($ppav + $cpav, 2);
                                                ?>%
                                            </td>
                                            <td>
                                                <?php
                                                echo number_format($phh_gas + $chh_gas, 2);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo number_format($ppf + $cpf, 2);
                                                ?>
                                            </td>

                                        </tr>

                                    <?php endif; ?>
                                <?php endforeach; ?>



                            <?php endforeach; ?>

                        </tbody>

                        <tfooter>
                            <tr>

                                <th colspan="5" class="text-center">
                                    TOTAL CONSTRUCCI&Oacute;N
                                </th>
                                <th>
                                    <?php echo number_format($suma_thh, 2) ?>
                                </th>

                                <th>
                                    <?php echo number_format($suma_p_cant, 2) ?>
                                </th>
                                <th>
                                    <?php echo number_format($suma_phh_gan, 2) ?>
                                </th>
                                <th>
                                    <?php echo number_format($suma_thh > 0 ? $suma_phh_gan / $suma_thh : 0, 2) ?>%
                                </th>
                                <th>
                                    <?php echo number_format($suma_phh_gas, 2) ?>
                                </th>
                                <th>
                                    <?php echo number_format($suma_phh_gan > 0 ? $suma_phh_gas / $suma_phh_gan : 0, 2) ?>
                                </th>

                                <th>
                                    <?php echo number_format($suma_c_cant, 2) ?>
                                </th>
                                <th>
                                    <?php echo number_format($suma_chh_gan, 2) ?>
                                </th>
                                <th>
                                    <?php echo number_format($suma_thh > 0 ? $suma_chh_gan / $suma_thh : 0, 2) ?>%
                                </th>
                                <th>
                                    <?php echo number_format($suma_chh_gas, 2) ?>
                                </th>
                                <th>
                                    <?php echo $suma_chh_gan > 0 ? number_format($suma_phh_gan > 0 ? $suma_chh_gas / $suma_chh_gan : 0, 2) : number_format(0, 2) ?>
                                </th>

                                <th>
                                    <?php echo number_format($suma_p_cant + $suma_c_cant, 2) ?>
                                </th>
                                <th>
                                    <?php echo number_format($suma_phh_gan + $suma_chh_gan, 2) ?>
                                </th>
                                <th>
                                    <?php echo number_format($suma_thh > 0 ? ($suma_chh_gan + $suma_phh_gan) / $suma_thh : 0, 2) ?>%
                                </th>
                                <th>
                                    <?php echo number_format($suma_phh_gas + $suma_chh_gas, 2) ?>
                                </th>
                                <th>
                                    <?php
                                    $tppf = $suma_phh_gan > 0 ? $suma_phh_gas / $suma_phh_gan : 0;
                                    $tcpf = $suma_chh_gan > 0 ? $suma_chh_gas / $suma_chh_gan : 0;
                                    echo number_format($tppf + $tcpf, 2);
                                    ?>
                                </th>

                            </tr>
                        </tfooter>
                    </table>

                </div>
            </div>

        </div>

        <br>
        <hr>
        <br>

        <div class="row print-utility ">
        </div>

        <!-- <div class="row">

            <div class="col-12 text-center">
                <h3>
                    Gr&aacute;fico Semanal de Actividades
                </h3>
            </div>

            <div class="col-12  ">
            <! -- <div class="col-sm-9 offset-sm-1 col-md-10 offset-md-0 col-lg-10 offset-lg-1  "> -- >
                <canvas id="role_chart"></canvas>
            </div>

        </div> 
    -->

        <div class="row">
            <div class="offset-sm-1 col-sm-10">

                <div class="row">

                    <div class="col-12 text-center">
                        <h3>
                            Gr&aacute;fico Semanal de Actividades
                        </h3>
                    </div>

                </div>

                <div class="row">

                    <div class="col-10">
                        <canvas id="role_chart"></canvas>
                    </div>

                </div>

            </div>
        </div>

    </div>
</section>

<script type="text/javascript">
    <?php

    $ndate_p_list = array();
    $date_p_list = array();
    $progress_p_list = array();
    foreach ($json_data->g_prog as $k => $v) {
        $ndate_p_list[] = gmdate("d-m-Y", $k * 86400);
        $date_p_list[] = $k;
        $progress_p_list[] = round($v, 2);
    }
    $ndate_r_list = array();
    $date_r_list = array();
    $progress_r_list = array();
    foreach ($json_data->g_real as $k => $v) {
        $ndate_r_list[] = gmdate("d-m-Y", $k * 86400);
        $date_r_list[] = $k;
        $progress_r_list[] = round($v, 2);
    }
    ?>

    var ctx = document.getElementById("role_chart").getContext('2d');
    var pnu = <?= json_encode($progress_p_list) ?>;
    var pna = <?= json_encode($ndate_p_list) ?>;
    var rnu = <?= json_encode($progress_r_list) ?>;
    var rna = <?= json_encode($ndate_r_list) ?>;
    var internalDataLength = <?= sizeof($json_data->weeks) ?>;
    var graphColorsP = [];
    var graphColorsR = [];
    i = 0;
    while (i <= internalDataLength) {
        var graphOutlineP = "#000000";
        graphColorsP.push(graphOutlineP);
        var graphOutlineR = "#00ffff";
        graphColorsR.push(graphOutlineR);
        i++;
    };
    data = {
        datasets: [{
                data: pnu,
                borderColor: "#000000",
                backgroundColor: "#ffffff00",
                label: 'Avance programado'
            },
            {
                data: rnu,
                borderColor: "#0000ff",
                backgroundColor: "#ffffff00",
                label: 'Avance real'
            }
        ],
        labels: pna
    };
    var options = {
        cutoutPercentage: 25,
        // responsive: true,
    };
    var myChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: options,
    });
</script>

<script type="text/javascript">
    /*
    var document_focus = false;
    $(document).ready(function() {
        window.print();
        document_focus = true;
    });
    setInterval(function() {
        if (document_focus === true) {
            window.close();
        }
    }, 300);
    */
</script>
<?php
/*
<link rel="stylesheet" href="<?php base_url('assets/bootstrap-table-master/dist/bootstrap-table.min.css'); ?>">
<link rel="stylesheet" href="<?php base_url('assets/bootstrap-table-master/dist/extensions/sticky-header/bootstrap-table-sticky-header.css'); ?>">
<link rel="stylesheet" href="<?php base_url('assets/bootstrap-table-master/dist/extensions/fixed-columns/bootstrap-table-fixed-columns.css'); ?>">
<script src="<?php base_url('assets/bootstrap-table-master/dist/bootstrap-table.min.js'); ?>"></script>
<script src="<?php base_url('assets/bootstrap-table-master/dist/extensions/sticky-header/bootstrap-table-sticky-header.js'); ?>"></script>
<script src="<?php base_url('assets/bootstrap-table-master/dist/extensions/fixed-columns/bootstrap-table-fixed-columns.js'); ?>"></script>}

*/
?>
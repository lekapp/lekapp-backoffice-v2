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
        <div class="row">
            <div class="col-sm-12">
                <div class="">
                    <div class="">
                        <h3 class="h4">
                            Reporte Semanal de Actividades -
                            <?php
                            $dt = new DateTime($data->activity_date, new DateTimeZone('America/Santiago'));
                            echo $dt->format('d-m-Y');
                            ?>
                            / <?= $data->report_no ?> (Semana <?= $json_data->cw ?>)
                        </h3>
                    </div>
                    <br>
                    <div class="">
                        <div class="col-sm-12 row print-utility">
                            <div class="col-sm-6">
                                <strong>Nombre del Contrato: </strong>
                                <span><?= $data->building_site->name ?></span>
                            </div>

                            <div class="col-sm-6">
                                <strong>Informe Nº: </strong>
                                <span><?= $data->report_no ?></span>
                            </div>

                            <div class="col-sm-6">
                                <strong>Programa base</strong>
                            </div>

                            <div class="col-sm-6">
                                <strong>Inicio</strong>
                                <span>
                                    <?= $json_data->fd ?>
                                </span>
                            </div>

                            <div class="col-sm-6">
                                <strong>Término</strong>
                                <span>
                                    <?= $json_data->ld ?>
                                </span>
                            </div>

                            <div class="col-sm-6">
                                <strong>Duración</strong>
                                <span>
                                    <?= $json_data->days ?> días
                                </span>
                            </div>

                        </div>
                    </div>
                    <br>
                    <div class="">
                        <div class="col-sm-12 row print-utility">

                            <table class="table">
                                <thead>

                                    <tr>
                                        <th>
                                        </th>
                                        <th>

                                        </th>
                                        <th colspan="3" style="color:red;background-color:lightcyan;">
                                            Acumulado anterior
                                        </th>
                                        <th colspan="3" style="background-color:lightcyan;">
                                            Semana actual
                                        </th>
                                        <th colspan="3" style="color:red;background-color:lightcyan;">
                                            Acumulado actual
                                        </th>
                                    </tr>

                                    <tr>
                                        <th>

                                        </th>
                                        <th>
                                            HH. Total
                                        </th>
                                        <th>Programado</th>
                                        <th>Real</th>
                                        <th style="background-color:lightcyan;">Variacion</th>

                                        <th>Programado</th>
                                        <th>Real</th>
                                        <th style="background-color:lightcyan;">Variacion</th>

                                        <th>Programado</th>
                                        <th>Real</th>
                                        <th style="background-color:lightcyan;">Variacion</th>

                                    </tr>

                                </thead>
                                <tbody>

                                    <?php
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

                                    <tr>

                                        <td class="text-right">
                                            TOTAL CONTRATO
                                        </td>
                                        <td>
                                            <?php
                                            echo $ts;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo round($pp, 2);
                                            ?>%
                                        </td>
                                        <td>
                                            <?php
                                            echo round($rp, 2);
                                            ?>%
                                        </td>
                                        <td>
                                            <?php
                                            echo round($vp, 2);
                                            ?>%
                                        </td>

                                        <td>
                                            <?php
                                            echo round($pa, 2);
                                            ?>%
                                        </td>
                                        <td>
                                            <?php
                                            echo round($ra, 2);
                                            ?>%
                                        </td>
                                        <td>
                                            <?php
                                            echo round($va, 2);
                                            ?>%
                                        </td>

                                        <td>
                                            <?php echo round($pp + $pa, 2); ?>%
                                        </td>
                                        <td>
                                            <?php echo round($rp + $ra, 2); ?>%
                                        </td>
                                        <td>
                                            <?php
                                            echo round($vpa, 2);
                                            ?>%
                                        </td>

                                    </tr>

                                </tbody>
                            </table>

                        </div>
                    </div>
                    <br>
                    <div class="">
                        <div class="col-sm-12 row print-utility">

                            <table class="table">
                                <thead>

                                    <tr>
                                        <th>
                                        </th>
                                        <th>

                                        </th>
                                        <th colspan="3" style="color:red;background-color:lightcyan;">
                                            Acumulado anterior
                                        </th>
                                        <th colspan="3" style="background-color:lightcyan;">
                                            Semana actual
                                        </th>
                                        <th colspan="3" style="color:red;background-color:lightcyan;">
                                            Acumulado actual
                                        </th>
                                    </tr>

                                    <tr>
                                        <th>

                                        </th>
                                        <th>
                                            HH. Total
                                        </th>
                                        <th>Programado</th>
                                        <th>Real</th>
                                        <th style="background-color:lightcyan;">Variacion</th>

                                        <th>Programado</th>
                                        <th>Real</th>
                                        <th style="background-color:lightcyan;">Variacion</th>

                                        <th>Programado</th>
                                        <th>Real</th>
                                        <th style="background-color:lightcyan;">Variacion</th>

                                    </tr>

                                </thead>
                                <tbody>

                                    <?php foreach ($json_data->as as $s => $speciality) : ?>

                                        <tr>

                                            <td class="text-right">
                                                <?php echo $speciality->name ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $json_data->rst->{$s};
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $pp = isset($speciality->info->prev_week) ? $speciality->info->prev_week / $json_data->rst->{$s} * 100 : 0;
                                                echo round($pp, 2);
                                                ?>%
                                            </td>
                                            <td>
                                                <?php
                                                $rp = 0;
                                                $rp = isset($json_data->rr->{$s}) ? $json_data->rr->{$s}->prev_week / $json_data->rst->{$s} * 100 : 0;
                                                echo round($rp, 2);
                                                ?>%
                                            </td>
                                            <td>
                                                <?php
                                                $vp = $rp - $pp;
                                                echo round($vp, 2);
                                                ?>%
                                            </td>

                                            <td>
                                                <?php
                                                $pa = isset($speciality->info->this_week) ? $speciality->info->this_week / $json_data->rst->{$s} * 100 : 0;
                                                echo round($pa, 2);
                                                ?>%
                                            </td>
                                            <td>
                                                <?php
                                                $ra = 0;
                                                $ra = isset($json_data->rr->{$s}) ? $json_data->rr->{$s}->this_week / $json_data->rst->{$s} * 100 : 0;
                                                echo round($ra, 2);
                                                ?>%
                                            </td>
                                            <td>
                                                <?php
                                                $va = $ra - $pa;
                                                echo round($va, 2);
                                                ?>%
                                            </td>

                                            <td>
                                                <?php echo round($pp + $pa, 2) ?>%
                                            </td>
                                            <td>
                                                <?php echo round($rp + $ra, 2) ?>%
                                            </td>
                                            <td>
                                                <?php
                                                $vpa = $vp + $va;
                                                echo round($vpa, 2);
                                                ?>%
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>
                            </table>

                        </div>
                    </div>
                    <br>
                    <div class="">
                        <div class="col-sm-12 row print-utility">

                            <table class="table">
                                <thead>

                                    <tr>
                                        <th colspan="6">

                                        </th>
                                        <th colspan="15" style="color:red;background-color:lightcyan;">
                                            POM
                                        </th>
                                    </tr>

                                    <tr>
                                        <th colspan="2">

                                        </th>
                                        <th colspan="4" style="color:red;background-color:silver;">
                                            PO
                                        </th>
                                        <th colspan="5" style="color:red;background-color:lightcyan;">
                                            Acumulado anterior
                                        </th>
                                        <th colspan="5" style="background-color:lightcyan;">
                                            Semana actual
                                        </th>
                                        <th colspan="5" style="color:red;background-color:lightcyan;">
                                            Acumulado actual
                                        </th>
                                    </tr>

                                    <tr>
                                        <th style="background-color:silver;">ID</th>
                                        <th style="background-color:silver;">Descripción</th>

                                        <th style="background-color:silver;">Unid.</th>
                                        <th style="color:red;background-color:silver;">Cant.</th>
                                        <th style="color:red;background-color:silver;">Rend.</th>
                                        <th style="color:red;background-color:silver;">HH</th>

                                        <th style="color:red;background-color:lightcyan;">Cant.</th>
                                        <th style="background-color:lightcyan;">HH GAN.</th>
                                        <th style="background-color:lightcyan;">% Av.</th>
                                        <th style="color:red;background-color:lightcyan;">HH GAST.</th>
                                        <th style="background-color:lightcyan;">Pf</th>

                                        <th style="color:red;background-color:lightcyan;">Cant.</th>
                                        <th style="background-color:lightcyan;">HH GAN.</th>
                                        <th style="background-color:lightcyan;">% Av.</th>
                                        <th style="color:red;background-color:lightcyan;">HH GAST.</th>
                                        <th style="background-color:lightcyan;">Pf</th>

                                        <th style="color:red;background-color:lightcyan;">Cant.</th>
                                        <th style="background-color:lightcyan;">HH GAN.</th>
                                        <th style="background-color:lightcyan;">% Av.</th>
                                        <th style="color:red;background-color:lightcyan;">HH GAST.</th>
                                        <th style="background-color:lightcyan;">Pf</th>

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

                                        <tr style="background-color:lawngreen;">

                                            <td><strong><?php echo $tramo->id ?></strong></td>
                                            <td><strong><?php /*echo $tramo->descripcion*/ ?></strong></td>
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
                                                        ?>
                                                        %
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
                                                        ?>
                                                        %
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
                                                        ?>
                                                        %
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

                                    <tr style="background-color:lawngreen;">

                                        <td colspan="5" class="text-center">
                                            TOTAL CONSTRUCCIÓN
                                        </td>
                                        <td>
                                            <?= number_format($suma_thh, 2) ?>
                                        </td>

                                        <td>
                                            <?= number_format($suma_p_cant, 2) ?>
                                        </td>
                                        <td>
                                            <?= number_format($suma_phh_gan, 2) ?>
                                        </td>
                                        <td>
                                            <?= number_format($suma_thh > 0 ? $suma_phh_gan / $suma_thh : 0, 2) ?> %
                                        </td>
                                        <td>
                                            <?= number_format($suma_phh_gas, 2) ?>
                                        </td>
                                        <td>
                                            <?= number_format($suma_phh_gan > 0 ? $suma_phh_gas / $suma_phh_gan : 0, 2) ?>
                                        </td>

                                        <td>
                                            <?= number_format($suma_c_cant, 2) ?>
                                        </td>
                                        <td>
                                            <?= number_format($suma_chh_gan, 2) ?>
                                        </td>
                                        <td>
                                            <?= number_format($suma_thh > 0 ? $suma_chh_gan / $suma_thh : 0, 2) ?> %
                                        </td>
                                        <td>
                                            <?= number_format($suma_chh_gas, 2) ?>
                                        </td>
                                        <td>
                                            <?= $suma_chh_gan > 0 ? number_format($suma_phh_gan > 0 ? $suma_chh_gas / $suma_chh_gan : 0, 2) : number_format(0, 2) ?>
                                        </td>

                                        <td>
                                            <?= number_format($suma_p_cant + $suma_c_cant, 2) ?>
                                        </td>
                                        <td>
                                            <?= number_format($suma_phh_gan + $suma_chh_gan, 2) ?>
                                        </td>
                                        <td>
                                            <?= number_format($suma_thh > 0 ? ($suma_chh_gan + $suma_phh_gan) / $suma_thh : 0, 2) ?> %
                                        </td>
                                        <td>
                                            <?= number_format($suma_phh_gas + $suma_chh_gas, 2) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $tppf = $suma_phh_gan > 0 ? $suma_phh_gas / $suma_phh_gan : 0;
                                            $tcpf = $suma_chh_gan > 0 ? $suma_chh_gas / $suma_chh_gan : 0;
                                            echo number_format($tppf + $tcpf, 2);
                                            ?>
                                        </td>

                                    </tr>

                                </tbody>
                            </table>

                        </div>
                    </div>
                    <br>
                    <div class="">
                        <div class="col-sm-6 offset-sm-3 row print-utility">
                            <canvas id="role_chart"></canvas>
                        </div>
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
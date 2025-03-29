<style type="text/css">
.print-utility {
    page-break-after: always;
    /* page-break-before: always; */
    page-break-inside: avoid;
}
</style>

<?php
$json_data = json_decode($data->json_data);
?>

<?php
/**
 * formatea la tabla 
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
    echo ClassStatic::NumberFormat($programado);
    echo "%</td>
                                <td>";
    echo ClassStatic::NumberFormat($real);
    echo "% </td>
                                <td>";
    echo ClassStatic::NumberFormat($variacion);
    echo "% </td>
                            </tr>
                        </tbody>
                    </table>
                </center>";
}
?>

<?php
/**
 * modificar el arreglo $tramo, para ordenar las actividades por zone
 * ordenarlas por cardinalidad ascendente
 */
$iContador = 0;
foreach ($json_data->ac as $kt => $tramo) :
    foreach ($tramo->actividad as $key => $actividad) :
        $arrayDataTablaPOM[$actividad->p[0]->zone][$tramo->id] = $actividad;
        $iContador++;
    endforeach;
endforeach;

/**
 * ordena por la llave el arreglo
 */
foreach ($arrayDataTablaPOM as $key => $actividad) :
    ksort($actividad);
    $arrayDataTablaPOM[$key] = $actividad;
endforeach;


// LOGICA ACUMULADO
$total_contrato = 0; // total 
$pp = 0; // programado pasado
$rp = 0; // real programado
$vp = 0; // variacion programado
$pa = 0; // programado actual
$ra = 0; // real actual
$va = 0; // variacion actual

/**
 * convierte en un array el objeto json_data->rst y suma los valores
 */
// $total_contrato = array_sum(json_decode(json_encode ( $json_data->rst ) , true)); 
foreach ($json_data->as as $s => $speciality) {
    $total_contrato += $json_data->rst->{$s};
}

foreach ($json_data->as as $s => $speciality) {
    // $pp += isset($speciality->info->prev_week) ? $speciality->info->prev_week * 100 : 0;
    //C ??? que significa la "C"Avance 0??? cantidad avance? horas?
    // $pp += isset($speciality->info->prev_week) ? $speciality->info->prev_week * 100 : (isset($json_data->ra->{$s}) ? $json_data->ra->{$s}->prev_week->{$s}->cAvance * 100 : 0);
    // PORCENTAJE
    $pp += isset($speciality->info->prev_week)
        ? $speciality->info->prev_week * 100
        : (isset($json_data->ra->{$s})
            ? $json_data->ra->{$s}->prev_week->{$s}->pAvance * 100
            : 0);

    $rp += isset($json_data->rr->{$s})
        ? $json_data->rr->{$s}->prev_week * 100
        : 0;

    $pa += isset($speciality->info->this_week)
        ? $speciality->info->this_week * 100
        : 0;

    $ra += isset($json_data->rr->{$s})
        ? $json_data->rr->{$s}->this_week * 100
        : 0;
}

// d($pp);

$vp = ($rp - $pp) / $total_contrato;
$pp /= $total_contrato;
$rp /= $total_contrato;
$va = ($ra - $pa) / $total_contrato;
$pa /= $total_contrato;
$ra /= $total_contrato;
$vpa = $vp + $va;

ob_start();
?>
<section class="main" id="printable">

    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12">

                <div class="row">
                    <div class="d-md-none ">
                        <div class="row">
                            <div class="col-6 ">
                                <img class="col-6 img-fluid float-left"
                                    src="<?php echo base_url('assets/images/' . $data->building_site->client->avatar_url); ?>">
                            </div>
                            <div class="col-6 ">
                                <img class="col-6 img-fluid float-right"
                                    src="<?php echo base_url('assets/images/' . $data->building_site->contractor->avatar_url); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="d-none d-md-block offset-md-1 col-md-2  ">
                        <img class=" col-12"
                            src="<?php echo base_url('assets/images/' . $data->building_site->client->avatar_url); ?>" />
                    </div>
                    <div class="col-md-6 text-center">
                        <h3>
                            Reporte Semanal de Actividades -
                            <?php
                            $dt = new DateTime($data->activity_date, new DateTimeZone('America/Santiago'));
                            echo $dt->format('d-m-Y');
                            echo "/";
                            echo $data->report_no ?> (Semana <?php echo $json_data->cw ?>)
                        </h3>
                    </div>
                    <div class="d-none d-md-block col-md-2 ">
                        <img class="col-12"
                            src="<?php echo base_url('assets/images/' . $data->building_site->contractor->avatar_url); ?>" />
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
                        <span><?php echo $data->report_no->name;
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

        <hr>

        <?php $html_cabecera = ob_get_clean(); ?>

        <?php ob_start(); ?>

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
                                    Actual
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

                            $iContador = 0;
                            foreach ($arrayDataTablaPOM as $kt => $tramo) : ?>
                            <?php
                                if (0 !== $iContador) {
                                    echo "<tr class=\"print-utility\"><td colspan=\"21\" style=\"padding: 0px;\"></td></tr>";
                                }
                                $iContador++;
                                ?>
                            <tr>
                                <td colspan="6">
                                    <strong>
                                        <?php
                                            echo $kt;
                                            ?>
                                    </strong>
                                </td>
                                <td colspan="15">
                                </td>
                            </tr>

                            <?php foreach ($tramo as $key => $actividad) :  ?>

                            <?php if (sizeof($actividad->p) > 0) : ?>

                            <tr>
                                <!-- ID -->
                                <td class="text-right">
                                    <?php echo $key; ?>
                                </td>
                                <!-- Descripción -->
                                <td>
                                    <?php echo $actividad->p[0]->name; ?>
                                </td>
                                <!-- Unid -->
                                <td>
                                    <?php echo $actividad->p[0]->unid; ?>
                                </td>
                                <!-- Cant -->
                                <td>
                                    <?php echo ClassStatic::NumberFormat($actividad->p[0]->cant); ?>
                                </td>
                                <!-- Rend -->
                                <td>
                                    <?php echo ClassStatic::NumberFormat($actividad->p[0]->rend); ?>
                                </td>
                                <!-- HH -->
                                <td>
                                    <?php
                                                echo  ClassStatic::NumberFormat($actividad->p[0]->thh);
                                                $suma_thh += $actividad->p[0]->thh;
                                                ?>
                                </td>
                                <!-- Acumulado anterior Cant -->
                                <td>
                                    <?php
                                                $p_cant = ($json_data->ra->{$key}->prev_week->{$key}->pAvance) != null ? ($json_data->ra->{$key}->prev_week->{$key}->pAvance) : 0;
                                                $suma_p_cant += $p_cant;
                                                $arr_suma_p_cant[$actividad->p[0]->fk_speciality] += $p_cant;
                                                echo ClassStatic::NumberFormat($p_cant);
                                                ?>
                                </td>
                                <!-- Acumulado anterior HH GAN -->
                                <td>
                                    <?php
                                                $phh_gan = ($json_data->ra->{$key}->prev_week->{$key}->pAvance) != null ? ($actividad->p[0]->thh * $json_data->ra->{$key}->prev_week->{$key}->pAvance) / 100 : 0;
                                                $suma_phh_gan += $phh_gan;
                                                $arr_suma_phh_gan[$actividad->p[0]->fk_speciality] += $phh_gan;
                                                echo ClassStatic::NumberFormat($phh_gan);
                                                ?>
                                </td>
                                <!-- Acumulado anterior % Av -->
                                <td>
                                    <?php
                                                $ppav = ($json_data->ra->{$key}->prev_week->{$key}->pAvance / $actividad->p[0]->cant) != null ? ($json_data->ra->{$key}->prev_week->{$key}->pAvance / $actividad->p[0]->cant) * 100 : 0;
                                                $arr_ppav[$actividad->p[0]->fk_speciality] += $ppav;
                                                echo ClassStatic::NumberFormat($ppav);
                                                ?>%
                                </td>
                                <!-- Acumulado anterior HH GAST -->
                                <td>
                                    <?php
                                                $phh_gas = ($json_data->ra->{$key}->prev_week->{$key}->hh) != null ? ($json_data->ra->{$key}->prev_week->{$key}->hh) : 0;
                                                $suma_phh_gas += $phh_gas;
                                                echo ClassStatic::NumberFormat($phh_gas);
                                                ?>
                                </td>
                                <!-- Acumulado anterior Pf -->
                                <td>
                                    <?php
                                                if ($phh_gan == 0) {
                                                    $ppf = 0;
                                                } else {
                                                    $ppf = $phh_gas / $phh_gan;
                                                }
                                                echo ClassStatic::NumberFormat($ppf);
                                                ?>
                                </td>
                                <!-- Actual Cant -->
                                <td>
                                    <?php
                                                $c_cant = ($json_data->ra->{$key}->this_week->{$key}->pAvance) != null ? ($json_data->ra->{$key}->this_week->{$key}->pAvance) : 0;
                                                $suma_c_cant += $c_cant;
                                                $arr_suma_c_cant[$actividad->p[0]->fk_speciality] += $c_cant;
                                                echo ClassStatic::NumberFormat($c_cant);
                                                ?>
                                </td>
                                <!-- Actual HH GAN -->
                                <td>
                                    <?php
                                                $chh_gan = ($json_data->ra->{$key}->this_week->{$key}->pAvance) != null ? ($actividad->p[0]->thh * $json_data->ra->{$key}->this_week->{$key}->pAvance) / 100 : 0;
                                                $suma_chh_gan += $chh_gan;
                                                $arr_suma_chh_gan[$actividad->p[0]->fk_speciality] += $chh_gan;
                                                echo ClassStatic::NumberFormat($chh_gan);
                                                ?>
                                </td>
                                <!-- Actual % Av. -->
                                <td>
                                    <?php
                                                $cpav = ($json_data->ra->{$key}->this_week->{$key}->pAvance / $actividad->p[0]->cant) != null ? ($json_data->ra->{$key}->this_week->{$key}->pAvance / $actividad->p[0]->cant) * 100 : 0;
                                                echo ClassStatic::NumberFormat($cpav);
                                                ?>%
                                </td>
                                <!-- Actual HH GAST. -->
                                <td>
                                    <?php
                                                $chh_gas = ($json_data->ra->{$key}->this_week->{$key}->hh) != null ? ($json_data->ra->{$key}->this_week->{$key}->hh) : 0;
                                                $suma_chh_gas += $chh_gas;
                                                echo ClassStatic::NumberFormat($chh_gas);
                                                ?>
                                </td>
                                <!-- Actual Pf -->
                                <td>
                                    <?php
                                                if ($chh_gan == 0) {
                                                    $cpf = 0;
                                                } else {
                                                    $cpf = $chh_gas / $chh_gan;
                                                }
                                                echo ClassStatic::NumberFormat($cpf);
                                                ?>
                                </td>
                                <!-- HH -->
                                <td>
                                    <?php
                                                echo ClassStatic::NumberFormat($p_cant + $c_cant);
                                                ?>
                                </td>
                                <!-- HH -->
                                <td>
                                    <?php
                                                echo ClassStatic::NumberFormat($phh_gan + $chh_gan);
                                                ?>
                                </td>
                                <!-- HH -->
                                <td>
                                    <?php
                                                echo ClassStatic::NumberFormat($ppav + $cpav);
                                                ?>%
                                </td>
                                <!-- HH -->
                                <td>
                                    <?php
                                                echo ClassStatic::NumberFormat($phh_gas + $chh_gas);
                                                ?>
                                </td>
                                <!-- HH -->
                                <td>
                                    <?php
                                                echo ClassStatic::NumberFormat($ppf + $cpf);
                                                ?>
                                </td>

                            </tr>

                            <?php endif; ?>

                            <?php endforeach; ?>

                            <?php endforeach; ?>


                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-center">
                                    TOTAL CONSTRUCCI&Oacute;N
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_thh) ?>
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_p_cant); ?>
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_phh_gan); ?>
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_thh > 0 ? ($suma_phh_gan / $suma_thh * 100) : 0); ?>%
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_phh_gas); ?>
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_phh_gan > 0 ? $suma_phh_gas / $suma_phh_gan : 0); ?>
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_c_cant); ?>
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_chh_gan); ?>
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_thh > 0 ? ($suma_chh_gan / $suma_thh * 100) : 0); ?>%
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_chh_gas); ?>
                                </th>
                                <th>
                                    <?php echo $suma_chh_gan > 0 ? ClassStatic::NumberFormat($suma_phh_gan > 0 ? $suma_chh_gas / $suma_chh_gan : 0) : ClassStatic::NumberFormat(0) ?>
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_p_cant + $suma_c_cant); ?>
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_phh_gan + $suma_chh_gan); ?>
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_thh > 0 ? (($suma_chh_gan + $suma_phh_gan) / $suma_thh * 100) : 0); ?>%
                                </th>
                                <th>
                                    <?php echo ClassStatic::NumberFormat($suma_phh_gas + $suma_chh_gas); ?>
                                </th>
                                <th>
                                    <?php
                                    $tppf = $suma_phh_gan > 0 ? $suma_phh_gas / $suma_phh_gan : 0;
                                    $tcpf = $suma_chh_gan > 0 ? $suma_chh_gas / $suma_chh_gan : 0;
                                    echo ClassStatic::NumberFormat($tppf + $tcpf);;
                                    ?>
                                </th>

                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>

        </div>

        <?php $html_pom = ob_get_clean(); ?>

        <?php ob_start(); ?>

        <div class="row">
            <div class="col-12">
                <div class="row ">

                    <!-- Total Contrato -->
                    <div class="col-12 text-center">

                        <h1>Total Contrato</h1>

                        <label class="font-weight-bold">HH Total</label>
                        <span><?php echo $total_contrato; ?></span>
                    </div>

                    <!-- Acumulado anterior -->
                    <div class="col-4 text-center">
                        <?php table_data("Acumulado anterior", $pp, $rp, $vp); ?>
                    </div>

                    <!-- Actual -->
                    <div class="col-4 text-center">
                        <?php table_data("Actual", $pa, $ra, $va); ?>
                    </div>
                    <!-- Acumulado actual -->
                    <div class="col-4 text-center">
                        <?php table_data("Acumulado actual",  $pp + $pa,  $rp + $ra,  $vpa); ?>
                    </div>
                </div>
            </div>

        </div>

        <hr>

        <div class="row print-utility">
            <div class="col-12">
                <?php foreach ($json_data->as as $s => $speciality) : ?>
                <div class="row">
                    <!-- $speciality->name -->
                    <div class="col-12 text-center">

                        <h1><?php echo $speciality->name; ?></h1>

                        <label class="font-weight-bold">HH Total</label>

                        <span><?php echo $json_data->rst->{$s}; ?></span>
                        <span><?php echo $s; ?></span>

                    </div>

                    <!-- Acumulado anterior -->
                    <div class="col-4 text-center">
                        <?php
                            // $pp = isset($speciality->info->prev_week) ? $speciality->info->prev_week / $json_data->rst->{$s} * 100 : 0;

                            // $rp = 0;
                            // $rp = isset($json_data->rr->{$s}) ? $json_data->rr->{$s}->prev_week / $json_data->rst->{$s} * 100 : 0;

                            // $vp = $rp - $pp;
                            // $vp_arr = $arr_suma_phh_gan[$s] - $arr_suma_p_cant[$s];

                            // d($rp);
                            // d($pp);
                            // d($vp);
                            // d($arr_suma_p_cant[$s]);
                            // d($arr_suma_phh_gan[$s]);
                            $vp_arr[$s] = $arr_suma_phh_gan[$s] - $arr_suma_p_cant[$s];
                            // d($vp_arr[$s]);
                            // table_data("Acumulado anterior", $pp, $rp, $vp);

                            table_data("Acumulado anterior", $arr_suma_p_cant[$s], $arr_suma_phh_gan[$s], $vp_arr[$s]);
                            ?>
                    </div>

                    <!-- Actual -->
                    <div class="col-4 text-center">
                        <?php
                            // $pa = isset($speciality->info->this_week) ? $speciality->info->this_week / $json_data->rst->{$s} * 100 : 0;

                            // $ra = 0;
                            // $ra = isset($json_data->rr->{$s}) ? $json_data->rr->{$s}->this_week / $json_data->rst->{$s} * 100 : 0;

                            // $va = $ra - $pa;
                            // $va = $ra - $pa;
                            // d($ra);
                            // d($pa);
                            // d($va);
                            // d($arr_suma_c_cant[$s]);
                            // d($arr_suma_chh_gan[$s]);
                            $va_arr[$s] = $arr_suma_chh_gan[$s] - $arr_suma_c_cant[$s];
                            // d($va_arr[$s]);

                            // table_data("Actual", round($pa, 2), round($ra, 2), round($va, 2));

                            table_data("Actual", $arr_suma_c_cant[$s], $arr_suma_chh_gan[$s], $va_arr[$s]);
                            ?>
                    </div>

                    <!-- Acumulado actual -->
                    <div class="col-4 text-center">
                        <?php
                            // table_data("Acumulado actual", round($pp + $pa, 2), round($rp + $ra, 2), round($vp + $va, 2));
                            table_data(
                                "Acumulado actual",
                                round($arr_suma_p_cant[$s] + $arr_suma_c_cant[$s], 2),
                                round($arr_suma_phh_gan[$s] + $arr_suma_chh_gan[$s], 2),
                                round($vp_arr[$s] + $va_arr[$s], 2)
                            );
                            ?>
                    </div>

                </div>
                <?php endforeach; ?>

            </div>
        </div>

        <?php $html_totales = ob_get_clean(); ?>

        <?php echo $html_cabecera; ?>

        <?php echo $html_totales; ?>

        <?php echo $html_pom; ?>



        <hr>

        <div style="page-break-after: always; page-break-before: always;  ">
            <div class="col-12">
                &nbsp;
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <div class="row">

                    <div class="col-12 text-center">
                        <h3>
                            Gr&aacute;fico Semanal de Actividades
                        </h3>
                    </div>

                </div>

                <div class="row">

                    <div class=" col-12 ">
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
var pnu = <?php echo json_encode($progress_p_list); ?>;
var pna = <?php echo json_encode($ndate_p_list); ?>;
var rnu = <?php echo json_encode($progress_r_list); ?>;
var rna = <?php echo json_encode($ndate_r_list); ?>;
var internalDataLength = <?php echo sizeof($json_data->weeks); ?>;
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
    responsive: true,
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
<link rel="stylesheet"
    href="<?php base_url('assets/bootstrap-table-master/dist/extensions/sticky-header/bootstrap-table-sticky-header.css'); ?>">
<link rel="stylesheet"
    href="<?php base_url('assets/bootstrap-table-master/dist/extensions/fixed-columns/bootstrap-table-fixed-columns.css'); ?>">
<script src="<?php base_url('assets/bootstrap-table-master/dist/bootstrap-table.min.js'); ?>"></script>
<script
    src="<?php base_url('assets/bootstrap-table-master/dist/extensions/sticky-header/bootstrap-table-sticky-header.js'); ?>">
</script>
<script
    src="<?php base_url('assets/bootstrap-table-master/dist/extensions/fixed-columns/bootstrap-table-fixed-columns.js'); ?>">
</script>}

*/
?>
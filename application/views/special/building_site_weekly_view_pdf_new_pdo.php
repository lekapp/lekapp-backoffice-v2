<style type="text/css">
    .print-utility {
        page-break-after: always;
        /* page-break-before: always; */
        page-break-inside: avoid;
    }

    #printable {
        font-size: small;
    }
</style>

<?php
$json_data = json_decode($data->json_data);

$selectedDate = $data->activity_date;
//Convert selectedDate to week number
$weekNumber = date("YW", strtotime($selectedDate));
$weekNumber = $json_data->highestWeek - $weekNumber;

//Lowest day in timestamp format
$lowestDay = ($json_data->lowestDay);

//Turn $lowestDay into a Y-m-d format

$dtld = new DateTime();
$lowestDay = $dtld->setTimestamp($lowestDay)->format('d-m-Y');
//$lowestDay = $dtld->format('d-m-Y');

//Highest day in timestamp format
$highestProgrammedDay = ($json_data->highestProgrammedDay);

//Turn $highestProgrammedDay into a Y-m-d format

$dthpd = new DateTime();
$highestProgrammedDay = $dthpd->setTimestamp($highestProgrammedDay)->format('d-m-Y');
//$highestProgrammedDay = $dthpd->format('d-m-Y');

//d($json_data);

?>

<div class="row d-print-none">
    <div class="col-md-12 text-right">
        <a href="<?= base_url('building_sites/weekly/' . $data->building_site->id) ?>"
            class="dropdown-item d-print-none">
            <i class="fa fa-arrow-left"></i>
        </a>
    </div>
</div>

<section class="main">

    <div class="">

        <div class="row">
            <div class="d-md-none">
                <div class="row">
                    <div class="col-6">
                        <img class="col-6 img-fluid float-left"
                            src="<?php echo base_url('assets/images/' . $data->building_site->client->avatar_url); ?>">
                    </div>
                    <div class="col-6">
                        <img class="col-6 img-fluid float-right"
                            src="<?php echo base_url('assets/images/' . $data->building_site->contractor->avatar_url); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="d-none d-md-block offset-md-1 col-md-2">
                <img class="col-12"
                    src="<?php echo base_url('assets/images/' . $data->building_site->client->avatar_url); ?>" />
            </div>
            <div class="col-md-6 text-center">
                <table width=100% height=100% class="mt-4">
                    <thead>
                        <td></td>
                        <td></td>
                        <td></td>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>
                                <h3>
                                    Reporte Semanal de Actividades -
                                    <?php
                                    $dt = new DateTime($data->activity_date, new DateTimeZone('America/Santiago'));
                                    echo $dt->format('d-m-Y');
                                    ?>
                                </h3>
                            </td>
                            <td></td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="d-none d-md-block col-md-2">
                <img class="col-12"
                    src="<?php echo base_url('assets/images/' . $data->building_site->contractor->avatar_url); ?>" />
            </div>
        </div>

        <hr>

        <div class="row">

            <div class="offset-md-1 col-md-5">
                <p>
                    <strong>
                        Nombre del Contrato
                    </strong>
                    <span>
                        <?= $json_data->contractName ?>
                    </span>
                </p>
                <p>
                    <strong>
                        Programa base
                    </strong>
                    <span>
                        <?= $json_data->contractName ?> -
                        <?= $data->report_no ?>
                    </span>
                </p>
                <p>
                    <strong>
                        Término
                    </strong>
                    <span>
                        <?= $highestProgrammedDay ?>
                    </span>
                </p>
            </div>
            <div class="col-md-5">
                <p>
                    <strong>
                        Informe Nº
                    </strong>
                    <span>
                        <?= $json_data->reportNumber ?>
                    </span>
                </p>
                <p>
                    <strong>
                        Inicio
                    </strong>
                    <span>
                        <?= $lowestDay ?>
                    </span>
                </p>
                <p>
                    <strong>
                        Duración
                    </strong>
                    <span>
                        <?= $json_data->projectDurationInDays ?>
                    </span>
                </p>
            </div>

        </div>

        <hr>

        <div class="row mt-4">

            <div class="col-md-12 text-center">

                <h1>
                    POM
                </h1>

                <table class="table table-stripped table-bordered table-hover" id="printable">

                    <thead>
                        <tr>
                            <th colspan="4"></th>
                            <th colspan="4" class="text-center">
                                PO
                            </th>
                            <th colspan="5" class="text-center">
                                Acumulado anterior
                            </th>
                            <th colspan="5" class="text-center">
                                Actual
                            </th>
                            <th colspan="5" class="text-center">
                                Acumulado actual
                            </th>
                        </tr>
                        <tr>
                            <th>
                                Zona
                            </th>
                            <th>
                                Área
                            </th>
                            <th>
                                ID
                            </th>
                            <th>
                                Descripción
                            </th>
                            <th>
                                Unid.
                            </th>
                            <th>
                                Cant.
                            </th>
                            <th>
                                Rend.
                            </th>
                            <th>
                                HH
                            </th>
                            <th>
                                Cant.
                            </th>
                            <th>
                                HH GAN.
                            </th>
                            <th>
                                % Av.
                            </th>
                            <th>
                                HH GAST.
                            </th>
                            <th>
                                Pf
                            </th>
                            <th>
                                Cant.
                            </th>
                            <th>
                                HH GAN.
                            </th>
                            <th>
                                % Av.
                            </th>
                            <th>
                                HH GAST.
                            </th>
                            <th>
                                Pf
                            </th>
                            <th>
                                Cant.
                            </th>
                            <th>
                                HH GAN.
                            </th>
                            <th>
                                % Av.
                            </th>
                            <th>
                                HH GAST.
                            </th>
                            <th>
                                Pf
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $aAn1 = 0;
                        $aAn2 = 0;
                        $aAn3 = 0;
                        $aAn4 = 0;
                        $aAn5 = 0;
                        $a1 = 0;
                        $a2 = 0;
                        $a3 = 0;
                        $a4 = 0;
                        $a5 = 0;
                        $aAc1 = 0;
                        $aAc2 = 0;
                        $aAc3 = 0;
                        $aAc4 = 0;
                        $aAc5 = 0;
                        ?>
                        <?php
                        foreach ($json_data->activities as $activity):
                            ?>
                            <tr>
                                <td>
                                    <?= $activity->zName ?>
                                </td>
                                <td>
                                    <?= $activity->arName ?>
                                </td>
                                <td>
                                    <?= $activity->code ?>
                                </td>
                                <td>
                                    <?= $activity->aName ?>
                                </td>
                                <td>
                                    <?= $activity->unt ?>
                                </td>
                                <td>
                                    <?= $activity->qty ?>
                                </td>
                                <td>
                                    <?= $activity->eff ?>
                                </td>
                                <td>
                                    <?=
                                        $activity->activityProjectProgrammedWorkHours
                                        ?>
                                </td>



                                <td>
                                    <?php
                                    echo $activity->activityTotalQuantityBeforeCurrentWeek;
                                    $aAn1 += $activity->activityTotalQuantityBeforeCurrentWeek;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo round($activity->activityProjectProgrammedWorkHours * $activity->activityTotalRealAdvanceBeforeCurrentWeek / 100, 2);
                                    $aAn2 += round($activity->activityProjectProgrammedWorkHours * $activity->activityTotalRealAdvanceBeforeCurrentWeek / 100, 2);
                                    ?>

                                </td>
                                <td>
                                    <?php 
                                        echo $activity->activityTotalQuantityBeforeCurrentWeek / $activity->qty * 100;
                                        $aAn3 += $activity->activityTotalRealAdvanceBeforeCurrentWeek;
                                    ?>
                                </td>
                                <td>
                                    <?= $activity->activityTotalRealWorkHoursBeforeCurrentWeek ?>
                                </td>
                                <td>
                                    <?= $activity->activityTotalPFBeforeCurrentWeek ?>
                                </td>



                                <td>
                                    <?php
                                    echo $activity->activityTotalQuantityInCurrentWeek;
                                    $a1 += $activity->activityTotalQuantityInCurrentWeek;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo round($activity->activityProjectProgrammedWorkHours * $activity->activityTotalRealAdvanceInCurrentWeek / 100, 2);
                                    $a2 += round($activity->activityProjectProgrammedWorkHours * $activity->activityTotalRealAdvanceInCurrentWeek / 100, 2);
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo ($activity->activityTotalQuantityInCurrentWeek / $activity->qty * 100);
                                    $a3 += $activity->activityTotalRealAdvanceInCurrentWeek;
                                    ?>
                                </td>
                                <td>
                                    <?= $activity->activityTotalRealWorkHoursInCurrentWeek ?>
                                </td>
                                <td>
                                    <?= $activity->activityTotalPFInCurrentWeek ?>
                                </td>



                                <td>
                                    <?php
                                    echo $activity->activityTotalQuantityBeforeCurrentWeek + $activity->activityTotalQuantityInCurrentWeek;
                                    $aAc1 += $activity->activityTotalQuantityBeforeCurrentWeek + $activity->activityTotalQuantityInCurrentWeek;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo round($activity->activityProjectProgrammedWorkHours * ($activity->activityTotalRealAdvanceBeforeCurrentWeek + $activity->activityTotalRealAdvanceInCurrentWeek) / 100, 2);
                                    $aAc2 += round($activity->activityProjectProgrammedWorkHours * ($activity->activityTotalRealAdvanceBeforeCurrentWeek + $activity->activityTotalRealAdvanceInCurrentWeek) / 100, 2);
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        echo ($activity->activityTotalQuantityBeforeCurrentWeek + $activity->activityTotalQuantityInCurrentWeek) / $activity->qty * 100;
                                        $aAc3 += $activity->activityTotalRealAdvanceBeforeCurrentWeek + $activity->activityTotalRealAdvanceInCurrentWeek;
                                    ?>
                                </td>
                                <td>
                                    <?= $activity->activityTotalRealWorkHoursBeforeCurrentWeek + $activity->activityTotalRealWorkHoursInCurrentWeek ?>
                                </td>
                                <td>
                                    <?= $activity->activityTotalPFBeforeCurrentWeek + $activity->activityTotalPFInCurrentWeek ?>
                                </td>


                            </tr>
                            <?php
                        endforeach;
                        ?>
                        <tr>
                            <td colspan="7">
                                <strong>
                                    Total Construcción
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?= $json_data->activitiesResume->activityProjectProgrammedWorkHours ?>
                                </strong>
                            </td>



                            <td>
                                <strong>
                                    <?php echo $aAn1 ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?php echo $aAn2 ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?php echo round($aAn2 / $json_data->activitiesResume->activityProjectProgrammedWorkHours, 2) ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?= $json_data->activitiesResume->activityProjectWorkHoursBeforeCurrentWeek ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?= $json_data->activitiesResume->activityPFBeforeCurrentWeek ?>
                                </strong>
                            </td>



                            <td>
                                <strong>
                                    <?php echo $a1 ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?php echo $a2 ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?php echo round($a2 / $json_data->activitiesResume->activityProjectProgrammedWorkHours, 2) ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?= $json_data->activitiesResume->activityProjectWorkHoursInCurrentWeek ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?= $json_data->activitiesResume->activityPFInCurrentWeek ?>
                                </strong>
                            </td>



                            <td>
                                <strong>
                                    <?php echo $aAc1 ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?php echo $aAc2 ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?php echo round($aAc2 / $json_data->activitiesResume->activityProjectProgrammedWorkHours, 2) ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?= $json_data->activitiesResume->activityProjectWorkHours ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?= $json_data->activitiesResume->activityPF ?>
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </div>

    </div>

</section>
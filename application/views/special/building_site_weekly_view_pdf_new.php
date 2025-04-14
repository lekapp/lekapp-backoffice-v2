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

        <div class="col-md-12 text-center" style="margin-bottom: 30px">

            <h1>
                Hitos
            </h1>

            <table class="table table-bordered  table-bordered table-hover" id="printable">

                <thead>
                    <th class="text-center">
                        Nombre
                    </th>
                    <th class="text-center">
                        Tipo
                    </th>
                    <th class="text-center">
                        Fecha
                    </th>
                </thead>
                <tbody>
                    <?php
                    $dtz = new DateTimeZone('America/Santiago');
                    if (sizeof($json_data->milestones) == 0) {
                        echo "<tr><td colspan='3'>No hay hitos para mostrar</td></tr>";
                    }
                    foreach ($json_data->milestones as $milestone):
                        ?>
                        <tr>
                            <td>
                                <?= $milestone->name ?>
                            </td>
                            <td>
                                <?= $milestone->type ?>
                            </td>
                            <td>
                                <?php
                                $dt = new DateTime($milestone->date, $dtz);
                                echo $dt->format('d-m-Y');
                                ?>
                            </td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>

        </div>

        <div class="col-md-12 text-center" style="margin-bottom: 30px">

            <h1>
                Anexos
            </h1>

            <table class="table table-bordered  table-bordered table-hover" id="printable">

                <tbody>
                    <?php if ($data->field1 != NULL && $data->field1 != "") {
                        echo "<tr><td width='20%'>Campo 1</td><td>" . $data->field1 . "</td></tr>";
                    }

                    if ($data->field2 != NULL && $data->field2 != "") {
                        echo "<tr><td width='20%'>Campo 2</td><td>" . $data->field2 . "</td></tr>";
                    }

                    if ($data->field3 != NULL && $data->field3 != "") {
                        echo "<tr><td width='20%'>Campo 3</td><td>" . $data->field3 . "</td></tr>";
                    }

                    if ($data->field4 != NULL && $data->field4 != "") {
                        echo "<tr><td width='20%'>Campo 4</td><td>" . $data->field4 . "</td></tr>";
                    }

                    ?>
                </tbody>
            </table>

        </div>

        <div class="row">

            <div class="col-md-12 text-center">
                <h1>Total Contrato</h1>
                <p>
                    <strong>HH Total</strong>
                    <span>
                        <?= $json_data->projectTotalProgrammedWorkHoursMax ?>
                    </span>
                </p>
            </div>

            <div class="col-md-4 text-center px-4">
                <h3>
                    <strong>
                        Acumulado anterior
                    </strong>
                </h3>
                <table class="col-md-12 table table-bordered ">
                    <thead>
                        <th>
                            <strong>
                                Programado
                            </strong>
                        </th>
                        <th>
                            <strong>
                                Real
                            </strong>
                        </th>
                        <th>
                            <strong>
                                Variación
                            </strong>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $aa = $json_data->projectTotalProgrammedWorkHoursMax > 0 ? round(100 * $json_data->projectTotalProgrammedWorkHoursBeforeCurrentWeek / $json_data->projectTotalProgrammedWorkHoursMax, 2) : 0;
                            $ab = $json_data->projectTotalRealWorkHours > 0 ? round(100 * $json_data->projectTotalRealWorkHoursBeforeCurrentWeek / $json_data->projectTotalRealWorkHours, 2) : 0;
                            $ac = round($ab - $aa, 2);
                            ?>
                            <td>
                                <?= $aa ?>%
                            </td>
                            <td>
                                <?= $ab ?>%
                            </td>
                            <td>
                                <?= $ac ?>%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 text-center px-4">
                <h3>
                    <strong>
                        Actual
                    </strong>
                </h3>
                <table class="col-md-12 table table-bordered ">
                    <thead>
                        <th>
                            <strong>
                                Programado
                            </strong>
                        </th>
                        <th>
                            <strong>
                                Real
                            </strong>
                        </th>
                        <th>
                            <strong>
                                Variación
                            </strong>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $ba = $json_data->projectTotalProgrammedWorkHoursMax > 0 ? round(100 * $json_data->projectTotalProgrammedWorkHoursInCurrentWeek / $json_data->projectTotalProgrammedWorkHoursMax, 2) : 0;
                            $bb = round($json_data->projectTotalRealWorkHoursInCurrentWeek, 2);
                            $bc = round($bb - $ba, 2);
                            ?>
                            <td>
                                <?= $ba ?>%
                            </td>
                            <td>
                                <?= $bb ?>%
                            </td>
                            <td>
                                <?= $bc ?>%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 text-center px-4">
                <h3>
                    <strong>
                        Acumulado actual
                    </strong>
                </h3>
                <table class="col-md-12 table table-bordered ">
                    <thead>
                        <th>
                            <strong>
                                Programado
                            </strong>
                        </th>
                        <th>
                            <strong>
                                Real
                            </strong>
                        </th>
                        <th>
                            <strong>
                                Variación
                            </strong>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $ca = $aa + $ba;
                            $cb = $ab + $bb;
                            $cc = round($cb - $ca, 2);
                            ?>
                            <td>
                                <?= $ca ?>%
                            </td>
                            <td>
                                <?= $cb ?>%
                            </td>
                            <td>
                                <?= $cc ?>%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <?php
        foreach ($json_data->specialities as $speciality):
            ?>

            <hr>

            <div class="row">

                <div class="col-md-12 text-center">
                    <h1>
                        <?= $speciality->name ?>
                    </h1>
                    <p>
                        <strong>HH Total</strong>
                        <span>
                            <?= $speciality->specialityTotalProgrammedWorkHoursMax ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-4 text-center px-4">
                    <h3>
                        <strong>
                            Acumulado anterior
                        </strong>
                    </h3>
                    <table class="col-md-12 table table-bordered ">
                        <thead>
                            <th>
                                <strong>
                                    Programado
                                </strong>
                            </th>
                            <th>
                                <strong>
                                    Real
                                </strong>
                            </th>
                            <th>
                                <strong>
                                    Variación
                                </strong>
                            </th>
                        </thead>
                        <tbody>
                            <tr>
                                <?php
                                $aa = $speciality->specialityTotalProgrammedWorkHoursMax > 0 ? round(100 * $speciality->specialityTotalProgrammedWorkHoursBeforeCurrentWeek / $speciality->specialityTotalProgrammedWorkHoursMax, 2) : 0;
                                $ab = round($speciality->specialityTotalRealWorkHoursBeforeCurrentWeek, 2);
                                $ac = round($ab - $aa, 2);
                                ?>
                                <td>
                                    <?= $aa ?>%
                                </td>
                                <td>
                                    <?= $ab ?>%
                                </td>
                                <td>
                                    <?= $ac ?>%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 text-center px-4">
                    <h3>
                        <strong>
                            Actual
                        </strong>
                    </h3>
                    <table class="col-md-12 table table-bordered ">
                        <thead>
                            <th>
                                <strong>
                                    Programado
                                </strong>
                            </th>
                            <th>
                                <strong>
                                    Real
                                </strong>
                            </th>
                            <th>
                                <strong>
                                    Variación
                                </strong>
                            </th>
                        </thead>
                        <tbody>
                            <tr>
                                <?php
                                $ba = $speciality->specialityTotalProgrammedWorkHoursMax > 0 ? round(100 * $speciality->specialityTotalProgrammedWorkHoursInCurrentWeek / $speciality->specialityTotalProgrammedWorkHoursMax, 2) : 0;
                                $bb = round($speciality->specialityTotalRealWorkHoursInCurrentWeek, 2);
                                $bc = round($bb - $ba, 2);
                                ?>
                                <td>
                                    <?= $ba ?>%
                                </td>
                                <td>
                                    <?= $bb ?>%
                                </td>
                                <td>
                                    <?= $bc ?>%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 text-center px-4">
                    <h3>
                        <strong>
                            Acumulado actual
                        </strong>
                    </h3>
                    <table class="col-md-12 table table-bordered ">
                        <thead>
                            <th>
                                <strong>
                                    Programado
                                </strong>
                            </th>
                            <th>
                                <strong>
                                    Real
                                </strong>
                            </th>
                            <th>
                                <strong>
                                    Variación
                                </strong>
                            </th>
                        </thead>
                        <tbody>
                            <tr>
                                <?php
                                $ca = $aa + $ba;
                                $cb = $ab + $bb;
                                $cc = round($cb - $ca, 2);
                                ?>
                                <td>
                                    <?= $ca ?>%
                                </td>
                                <td>
                                    <?= $cb ?>%
                                </td>
                                <td>
                                    <?= $cc ?>%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <?php
        endforeach;
        ?>

    </div>

</section>

<style>
    .table-bordered td,
    .table-bordered th {
        border: 1px solid #818487;
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #818487;
    }
</style>
<style>
    .photo-title {
        height: 65px;
    }
</style>

<!-- Feeds Section-->
<section class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="">

                    <div class="row mb-4">
                        <div class="d-md-none col-md-12">
                            <div class="row d-flex justify-content-between">
                                <div class="col-6 ">
                                    <img class="col-6 img-fluid float-left"
                                        src="<?php echo base_url('assets/images/' . $data->building_site->client->avatar_url); ?>"
                                        style="max-width: 250px; max-height: 250px;" />
                                </div>
                                <div class="col-6 ">
                                    <img class="col-6 img-fluid float-right"
                                        src="<?php echo base_url('assets/images/' . $data->building_site->contractor->avatar_url); ?>"
                                        style="max-width: 250px; max-height: 250px;" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 d-flex h-100 align-items-center justify-items-center">
                        <div class="d-none d-md-block offset-md-1 col-md-2">
                            <img class="col-12"
                                src="<?php echo base_url('assets/images/' . $data->building_site->client->avatar_url); ?>"
                                style="max-width: 250px; max-height: 250px;" />
                        </div>
                        <div class="col-md-6 text-center mt-4">
                            <h3>
                                Reporte Diario de Actividades
                                <?php
                                $dt = new DateTime($data->activity_date, new DateTimeZone('America/Santiago'));
                                //echo $dt->format('d-m-Y');
                                //echo "/";
                                //echo $data->report_no 
                                ?>
                            </h3>
                        </div>
                        <div class="d-none d-md-block col-md-2">
                            <img class="col-12"
                                src="<?php echo base_url('assets/images/' . $data->building_site->contractor->avatar_url); ?>"
                                style="max-width: 250px; max-height: 250px;" />
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-12 row">
                            <div class="col-sm-6">
                                <strong>Nombre del Contrato: </strong>
                                <span><?= $data->contract_name ?></span>
                            </div>
                            <div class="col-sm-6">
                                <strong>Adm. del Contrato: </strong>
                                <span><?= $data->admin_name ?></span>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-sm-6">
                                <strong>Nº del Contrato: </strong>
                                <span><?= $data->contract_no ?></span>
                            </div>
                            <div class="col-sm-6">
                                <strong>Jefe de Oficina: </strong>
                                <span><?= $data->office_chief ?></span>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-sm-6">
                                <strong>Fecha Control: </strong>
                                <span>
                                    <?php
                                    $dt = new DateTime($data->control_date, new DateTimeZone('America/Santiago'));
                                    echo $dt->format('d-m-Y');
                                    ?>
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <strong>Jefe de Terreno: </strong>
                                <span><?= $data->terrain_chief ?></span>
                            </div>
                        </div>
                        <br>

                        <div class="col-md-12 row  mt-4">
                            <div class="col-md-12">
                                <h4>ACTIVIDADES REELEVANTES</h4>
                                <table class="table">
                                    <thead>
                                        <th>
                                            Cod.
                                        </th>
                                        <th>
                                            Nombre
                                        </th>
                                        <th>
                                            Trabajadores
                                        </th>
                                        <th>
                                            HH Gastadas
                                        </th>
                                        <th>
                                            % Avance
                                        </th>
                                        <th>
                                            Notas
                                        </th>
                                        <th>
                                            Maquinarias
                                        </th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $worker_list_assisted = [];
                                        $data->important_activities = json_decode($data->important_activities);
                                        foreach ($data->important_activities as $zone_activities): ?>
                                            <tr>
                                                <td colspan="4">
                                                    <h4>
                                                        <strong>
                                                            <p>AREA: <?php echo $zone_activities->area; ?></p>
                                                            <p>ZONA: <?php echo $zone_activities->name; ?></p>
                                                        </strong>
                                                    </h4>
                                                </td>
                                            </tr>
                                            <?php
                                            foreach ($zone_activities->activities as $activity):
                                                foreach ($activity->worker_list as $worker) {
                                                    $worker_list_assisted[$worker->worker_id] = $worker;
                                                }
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $activity->activity_code; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $activity->activity; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $activity->workers; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $activity->hh; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $activity->p_avance; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $activity->comment; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $activity->machinery; ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            endforeach;
                                            ?>
                                        <?php
                                        endforeach;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="print-utility"></div>

                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <h4>DISCURSO DE SEGURIDAD</h4>
                                <p><?= $data->security_speech ?></p>
                            </div>
                        </div>

                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <h4>CALIDAD</h4>
                                <p><?= $data->quality ?></p>
                            </div>
                        </div>

                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <h4>INTERFERENCIAS</h4>
                                <p><?= $data->interferences ?></p>
                            </div>
                        </div>

                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <h4>VISITAS</h4>
                                <p><?= $data->visits ?></p>
                            </div>
                        </div>

                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <h4>OTROS</h4>
                                <p><?= $data->others ?></p>
                            </div>
                        </div>

                        <br />
                        <br />

                        <div class="col-md-12 row">

                            <div class="col-md-12">
                                <h4>TRABAJOS EN EJECUCIÓN</h4>
                            </div>
                            <div class="row col-md-12">
                                <?php
                                foreach ($data->photos as $k => $entry): ?>
                                    <div class="col-sm-3 my-3">
                                        <div class="row">
                                            <div class="col-12 mb-2 photo-title">

                                                <?= $data->photos_data[$k]->name ?>

                                            </div>
                                            <div class="col-12">
                                                <img width="100%" height="auto" class="img-fluid"
                                                    src="<?php echo asset_img($entry->photo) ?>" />
                                            </div>
                                            <div class="photo-title">
                                                &nbsp;
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <script>
                                let rotated = document.querySelectorAll('.rotated');
                                rotated.forEach((el) => {
                                    el.style.transform = 'rotate(90deg)';
                                });
                            </script>

                        </div>

                        <div class="clearfix"></div>
                        <div class="print-utility"></div>

                        <div class="col-md-12">
                            <br />
                            <h4>CONSUMO DE RECURSOS</h4>
                        </div>

                        <div class="row col-md-12">
                            <div class="row col-md-12 mt-3">
                                <table class="table">
                                    <thead>
                                        <th scope="col">#</th>
                                        <th scope="col">Rol</th>
                                        <th scope="col">Trabajadores</th>
                                        <th scope="col">HH Gastadas</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $x = 1;
                                        foreach ($data->hh_role as $k => $v): ?>

                                            <tr>
                                                <th scope="row"><?= $x ?></th>
                                                <td><?= $v->name ?></td>
                                                <td><?= $v->workers ?></td>
                                                <td><?= $v->hh ?></td>
                                            </tr>
                                            <?php $x++; ?>

                                        <?php
                                        endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <div class="clearfix"></div>
                        <div class="print-utility"></div>

                        <div class="row col-md-12">

                            <div class="row col-md-12 ">
                                <table class="table">
                                    <thead>
                                        <th scope="col">#</th>
                                        <th scope="col">HH Acum. Anterior</th>
                                        <th scope="col">HH Actual</th>
                                        <th scope="col">HH Acum. Total</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">#</th>
                                            <td><?= $data->p_hh ?></td>
                                            <td><?= $data->c_hh ?></td>
                                            <td><?= $data->p_hh + $data->c_hh ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="print-utility"></div>
                        <div class="worker_list col-md-12">

                            <h4>
                                <br />
                                ASISTENCIA DE TRABAJADORES
                            </h4>

                            <table class="table">

                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>RUT</th>
                                        <th>Especialidad</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    foreach ($data->workers_in_site as $worker): ?>
                                        <tr>
                                            <td><?= $worker->name ?></td>
                                            <td><?= $worker->dni ?></td>
                                            <td><?= $worker->speciality_name ?></td>
                                            <td>
                                                <?php
                                                if (isset($worker_list_assisted[$worker->id])) {
                                                    echo "<span class='text-success'>Asistió</span>";
                                                } else {
                                                    echo "No Asistió";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>

                        </div>

                        <div class="col-md-12 row text-center">
                            <div class="col-sm-6" style="padding-top: 250px;">
                                <hr style="border-color: #000; width: 80%;">
                                <p style="text-align: center;">
                                    <?php echo $data->b1_n; ?>
                                </p>
                                <p style="text-align: center;">
                                    <?php echo $data->b1_ne; ?>
                                </p>
                                <label class="font-weight-bold">
                                    <?php echo $data->b1_c; ?>
                                </label>
                            </div>
                            <div class="col-sm-6" style="padding-top: 250px;">
                                <hr style="border-color: #000; width: 80%;">
                                <p style="text-align: center;">
                                    <?php echo $data->b2_n; ?>
                                </p>
                                <p style="text-align: center;">
                                    <?php echo $data->b2_ne; ?>
                                </p>
                                <label class="font-weight-bold">
                                    <?php echo $data->b2_c; ?>
                                </label>
                            </div>
                            <div class="col-sm-6" style="padding-top: 250px;">
                                <hr style="border-color: #000; width: 80%;">
                                <p style="text-align: center;">
                                    <?php echo $data->b3_n; ?>
                                </p>
                                <p style="text-align: center;">
                                    <?php echo $data->b3_ne; ?>
                                </p>
                                <label class="font-weight-bold">
                                    <?php echo $data->b3_c; ?>
                                </label>
                            </div>
                            <div class="col-sm-6" style="padding-top: 250px;">
                                <hr style="border-color: #000; width: 80%;">
                                <p style="text-align: center;">
                                    <?php echo $data->b4_n; ?>
                                </p>
                                <p style="text-align: center;">
                                    <?php echo $data->b4_ne; ?>
                                </p>
                                <label class="font-weight-bold">
                                    <?php echo $data->b4_c; ?>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
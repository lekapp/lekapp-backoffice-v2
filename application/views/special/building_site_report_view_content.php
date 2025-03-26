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
                <div class="card">
                    <div class="card-header">
                        <span>
                            <h3 class="h4">
                                Reporte Diario de Actividades
                                <?php
                                $dt = new DateTime($data->activity_date, new DateTimeZone('America/Santiago'));
                                //echo $dt->format('d-m-Y');
                                ?>
                            </h3>
                        </span>
                    </div>
                    <div class="card-close">
                        <a href="<?= base_url('building_sites/report/' . $data->fk_building_site) ?>"
                            class="dropdown-item">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="card-body">

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
                                    /*
                                        $arr = explode('-', $data->control_date);
                                        echo $arr[2] . '/' . $arr[1] . '/' . $arr[0]
                                        */
                                    ?>

                                </span>
                            </div>
                            <div class="col-sm-6">
                                <strong>Jefe de Terreno: </strong>
                                <span><?= $data->terrain_chief ?></span>
                            </div>
                        </div>

                        <br>

                        <div class="col-md-12 row">
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
                                                            AREA: <?php echo $zone_activities->area . " / "; ?>ZONA:
                                                            <?php echo $zone_activities->name; ?>
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
                                                <img class="img-fluid" width="100%" height="auto"
                                                    src="<?php echo asset_img($entry->photo) ?>" />
                                            </div>
                                            <div class="photo-title">
                                                &nbsp;
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12">
                                <br />
                                <h4>CONSUMO DE RECURSOS</h4>
                            </div>
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

                            <div class="clearfix"></div>

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

                        <div class="worker_list">

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

                        <br /><br /><br />

                        <div class="row text-center">
                            <div class="col-sm-6" style="padding-top: 200px;">
                                <hr style="border-color: #000; width: 80%;">
                                <p style="text-align: center;">
                                    <?php echo $data->admin_name; ?>
                                </p>
                                <label class="font-weight-bold">Adm. del Contrato</label>
                            </div>
                            <div class="col-sm-6" style="padding-top: 200px;">
                                <hr style="border-color: #000; width: 80%;">
                                <p style="text-align: center;">
                                    <?php echo $data->office_chief; ?>
                                </p>
                                <label class="font-weight-bold">Jefe de Oficina</label>
                            </div>
                            <div class="col-sm-6" style="padding-top: 200px;">
                                <hr style="border-color: #000; width: 80%;">
                                <p style="text-align: center;">
                                    <?php echo $data->terrain_chief; ?>
                                </p>
                                <label class="font-weight-bold">Jefe de Terreno</label>
                            </div>
                            <div class="col-sm-6" style="padding-top: 200px;">
                                <hr style="border-color: #000; width: 80%;">
                                <p style="text-align: center;">
                                    <?php echo $data->b4_n; ?>
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
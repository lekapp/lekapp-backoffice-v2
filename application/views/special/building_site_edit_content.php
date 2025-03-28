<!-- Feeds Section-->
<section class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="h4">Editar obra</h3>
                    </div>
                    <div class="card-close">
                        <a href="<?= base_url('building_sites') ?>" class="dropdown-item">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <?php
                        $attr = array(
                            'method' => 'post',
                            'class' => 'form-horizontal'
                        );
                        echo form_open_multipart('building_sites/edit/' . $data->id, $attr);
                        ?>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label" for="Glosa">Nombre obra</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder' => 'Nombre',
                                    'class' => 'form-control p_input',
                                    'name' => 'name',
                                    'value' => $data->name
                                );
                                echo form_input($attr);
                                echo form_error('name');
                                ?>
                            </div>
                            <label class="col-sm-2 form-control-label" for="Glosa">Código</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder' => 'Código',
                                    'class' => 'form-control p_input',
                                    'name' => 'code',
                                    'value' => $data->code
                                );
                                echo form_input($attr);
                                echo form_error('code');
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label" for="Especialidad">Cliente</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = "class='form-control p_input'";
                                echo form_dropdown('fk_client', $clients, $data->fk_client, $attr);
                                echo form_error('fk_client');
                                ?>
                            </div>
                            <label class="col-sm-2 form-control-label" for="Especialidad">Mandante</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = "class='form-control p_input'";
                                echo form_dropdown('fk_contractor', $contractors, $data->fk_contractor, $attr);
                                echo form_error('fk_contractor');
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label" for="Glosa">Dirección</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder' => 'Calle',
                                    'class' => 'form-control p_input',
                                    'name' => 'address_street',
                                    'value' => $data->address_street
                                );
                                echo form_input($attr);
                                echo form_error('address_street');
                                ?>
                            </div>
                            <div class="col-sm-2">
                                <?php
                                $attr = array(
                                    'placeholder' => 'Número',
                                    'class' => 'form-control p_input',
                                    'name' => 'address_number',
                                    'value' => $data->address_number
                                );
                                echo form_input($attr);
                                echo form_error('date_end');
                                ?>
                            </div>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder' => 'Ciudad',
                                    'class' => 'form-control p_input',
                                    'name' => 'address_city',
                                    'value' => $data->address_city
                                );
                                echo form_input($attr);
                                echo form_error('address_city');
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label" for="Glosa">Línea base</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $attr = array(
                                            'class' => '',
                                            'name' => $data->data_file
                                        );
                                        echo form_upload($attr);
                                        echo form_error($data->data_file);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label" for="Glosa">Línea de trabajadores</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $attr = array(
                                            'class' => '',
                                            'name' => $data->data_file_workers
                                        );
                                        echo form_upload($attr);
                                        echo form_error($data->data_file_workers);
                                        ?>
                                    </div>
                                </div>

                            </div>
                            <div class="line"></div>
                        </div>

                        <?php if ($user->fk_role < 3): ?>

                            <div class="form-group row">
                                <div class="col-sm-10 offset-sm-2">
                                    <?php
                                    $attr = array(
                                        'class' => 'btn btn-primary'
                                    );
                                    //echo form_submit('update', 'Actualizar', $attr);
                                    if ($data->current_version == 0) {
                                        echo form_submit('update', 'Subir', $attr);
                                    } else {
                                        echo form_submit('update', 'Actualizar', $attr);
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>


                        <?php echo form_close() ?>

                        <div class="justify-content-between d-flex">
                            <div>
                                <?php if ($user->fk_role < 3): ?>
                                    <a class="btn btn-danger my-2"
                                        href="<?php echo base_url('building_sites/p_remove/' . $data->id) ?>">
                                        Limpiar
                                    </a>
                                <?php endif; ?>
                                <a class="btn btn-info my-2"
                                    href="<?php echo base_url('building_sites/weekly/' . $data->id) ?>">
                                    Reportes Semanales
                                </a>
                                <a class="btn btn-info my-2"
                                    href="<?php echo base_url('building_sites/report/' . $data->id) ?>">
                                    Reportes Diarios
                                </a>
                                <a class="btn btn-info my-2"
                                    href="<?php echo base_url('building_sites/list_activities/' . $data->id) ?>">
                                    Ver actividades
                                </a>
                            </div>
                            <div>
                                <a class="btn btn-success my-2"
                                    href="<?php echo base_url('building_sites/reverse_report_activity/' . $data->id) ?>">
                                    Descarga Avance
                                </a>
                                <a class="btn btn-success my-2"
                                    href="<?php echo base_url('building_sites/reverse_report_activity_hh/' . $data->id) ?>">
                                    Desc. Av. HH
                                </a>
                                <a class="btn btn-success my-2"
                                    href="<?php echo base_url('building_sites/reverse_report_activity_comments/' . $data->id) ?>">
                                    Desc. Av. Notas
                                </a>
                                <a class="btn btn-success my-2"
                                    href="<?php echo base_url('building_sites/reverse_report_activity_machinery/' . $data->id) ?>">
                                    Desc. Av. Maquinarias
                                </a>
                                <a class="btn btn-success my-2"
                                    href="<?php echo base_url('building_sites/reverse_report_workers/' . $data->id) ?>">
                                    Descarga Esfuerzo
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <section class="dashboard-counts">
                    <div class="row bg-white has-shadow">
                        <!-- Item -->
                        <div class="col-xl-3 col-sm-6">
                            <div class="item d-flex align-items-center justify-content-around m-2">
                                <div class="icon bg-violet"><i class="icon-user"></i></div>
                                <div class="title"><span>HH<br>Obra</span>
                                    <!--
                                        <div class="progress">
                                            <div role="progressbar" style="width: 25%; height: 4px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-violet"></div>
                                        </div>
                                    -->
                                </div>
                                <div class="number">
                                    <h3>
                                        <?= ($data->hh_total) != 0 ? number_format($data->hh_total, 2, ",", ".") : '-' ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <!-- Item -->
                        <div class="col-xl-3 col-sm-6">
                            <div class="item d-flex align-items-center justify-content-around m-2">
                                <div class="icon bg-violet"><i class="icon-user"></i></div>
                                <div class="title"><span>Areas<br>de trabajo</span>
                                    <!--
                                        <div class="progress">
                                            <div role="progressbar" style="width: 25%; height: 4px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-violet"></div>
                                        </div>
                                    -->
                                </div>
                                <div class="number">
                                    <h3>
                                        <?= sizeof($areas) != 0 ? sizeof($areas) : '-' ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <!-- Item -->
                        <div class="col-xl-3 col-sm-6">
                            <div class="item d-flex align-items-center justify-content-around m-2">
                                <div class="icon bg-violet"><i class="icon-user"></i></div>
                                <div class="title"><span>Inicio<br>de obra</span>
                                    <!--
                                        <div class="progress">
                                            <div role="progressbar" style="width: 25%; height: 4px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-violet"></div>
                                        </div>
                                    -->
                                </div>
                                <div class="number">
                                    <h3>
                                        <?= isset($data->first_activity) ? gmdate("d-m-Y", $data->first_activity->activity_date * 86400) : '-' ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <!-- Item -->
                        <div class="col-xl-3 col-sm-6">
                            <div class="item d-flex align-items-center justify-content-around m-2">
                                <div class="icon bg-violet"><i class="icon-user"></i></div>
                                <div class="title"><span>Fin<br>de obra</span>
                                    <!--
                                        <div class="progress">
                                            <div role="progressbar" style="width: 25%; height: 4px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-violet"></div>
                                        </div>
                                    -->
                                </div>
                                <div class="number">
                                    <h3>
                                        <?= isset($data->first_activity) ? gmdate("d-m-Y", $data->last_activity->activity_date * 86400) : '-' ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="clearfix"></div>
            <br>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h3 class="h4">Hitos</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($user->fk_role < 3): ?>
                                    <div class="text-right">
                                        <a class="btn btn-success"
                                            href="<?= base_url('building_sites/add_milestone/' . $data->id) ?>">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="clearfix"></div>
                                    <br>
                                <?php endif; ?>
                                <?php
                                if (sizeof($milestones) > 0):
                                    ?>
                                    <table class="table table-hover" id="data2">
                                        <thead>
                                            <tr class="">
                                                <th>#</th>
                                                <th>Hito</th>
                                                <th>Tipo</th>
                                                <th>Fecha</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $x = 1;
                                            $dtz = new DateTimeZone("America/Santiago");
                                            foreach ($milestones as $entry):
                                                ?>
                                                <tr>
                                                    <th scope="row">
                                                        <?php echo $x ?>
                                                    </th>
                                                    <td>
                                                        <?= $entry->name ?>
                                                    </td>
                                                    <td>
                                                        <?= $entry->type ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $dt = new DateTime($entry->date, $dtz);
                                                        echo $dt->format('d-m-Y');
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($user->fk_role < 3): ?>
                                                            <a class="btn btn-primary btn-sm"
                                                                href="<?= base_url('building_sites/edit_milestone/' . $entry->id) ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                            <a class="btn btn-danger btn-sm"
                                                                href="<?= base_url('building_sites/remove_milestone/' . $entry->id) ?>">
                                                                <i class="fa fa-trash-o"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                $x++;
                                            endforeach;
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php
                                else:
                                    ?>
                                    <p>No hay areas disponibles para administrar</p>
                                    <?php
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h3 class="h4">Trabajadores</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($user->fk_role < 3): ?>
                                    <div class="text-right">
                                        <a class="btn btn-success"
                                            href="<?= base_url('building_sites/add_worker/' . $data->id) ?>">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="clearfix"></div>
                                    <br>
                                <?php endif; ?>
                                <?php
                                if (sizeof($workers) > 0):
                                    ?>
                                    <table class="table table-hover" id="data2">
                                        <thead>
                                            <tr class="">
                                                <th>#</th>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>RUT</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $x = 1;
                                            foreach ($workers as $entry):
                                                ?>
                                                <tr>
                                                    <th scope="row">
                                                        <?php echo $x ?>
                                                    </th>
                                                    <td>
                                                        <?= $entry->name ?>
                                                    </td>
                                                    <td>
                                                        <?= $entry->email ?>
                                                    </td>
                                                    <td>
                                                        <?= $entry->dni ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($user->fk_role < 3): ?>
                                                            <a class="btn btn-primary btn-sm"
                                                                href="<?= base_url('building_sites/edit_worker/' . $entry->id) ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                            <a class="btn btn-danger btn-sm"
                                                                href="<?= base_url('building_sites/remove_worker/' . $entry->id) ?>">
                                                                <i class="fa fa-trash-o"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                $x++;
                                            endforeach;
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php
                                else:
                                    ?>
                                    <p>No hay trabajadores disponibles para administrar</p>
                                    <?php
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             
            <div class="col-sm-12">
                <div class="row">

                    <?php if (sizeof($data->statistics) > 0): ?>
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <span>Curva óptima de trabajo</span>
                                </div>
                                <div class="card-body">
                                    <canvas id="role_chart2"></canvas>
                                </div>
                            </div>
                            <script type="text/javascript">
                                <?php
                                $ndate_list = array();
                                $date_list = array();
                                $hh_list = array();
                                foreach ($data->statistics as $k => $v) {
                                    $ndate_list[] = gmdate("d-m-Y", $k * 86400);
                                    $date_list[] = $k;
                                    $hh_list[] = $v;
                                }
                                ?>
                                var ctx = document.getElementById("role_chart2").getContext('2d');
                                var rnu = <?= json_encode($hh_list) ?>;
                                var rna = <?= json_encode($ndate_list) ?>;
                                var internalDataLength = <?= sizeof($data->statistics) ?>;
                                var graphColors = [];
                                var graphOutlines = [];
                                var hoverColor = [];
                                i = 0;
                                var randomR = Math.floor((Math.random() * 100));
                                var randomG = Math.floor((Math.random() * 100));
                                var randomB = Math.floor((Math.random() * 100));
                                while (i <= internalDataLength) {
                                    var graphBackground = "rgba(" +
                                        randomR + ", " +
                                        randomG + ", " +
                                        randomB + ", 0.2)";
                                    graphColors.push(graphBackground);
                                    var graphOutline = "rgba(" +
                                        (randomR) + ", " +
                                        (randomG) + ", " +
                                        (randomB) + ", 0.2)";
                                    graphOutlines.push(graphOutline);
                                    var hoverColors = "rgba(" +
                                        (randomR + 25) + ", " +
                                        (randomG + 25) + ", " +
                                        (randomB + 25) + ", 0.2)";
                                    hoverColor.push(hoverColors);
                                    i++;
                                };
                                data = {
                                    datasets: [{
                                        data: rnu,
                                        //backgroundColor: graphColors,
                                        //hoverBackgroundColor: hoverColor,
                                        //borderColor: graphOutlines,
                                        backgroundColor: graphBackground,
                                        label: 'HH óptimo diario'
                                    }],
                                    labels: rna
                                };
                                var options = {
                                    cutoutPercentage: 25,
                                    borderColor: hoverColor
                                };
                                var myChart = new Chart(ctx, {
                                    type: 'line',
                                    data: data,
                                    options: options,
                                });
                            </script>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="h4">Especialidades</h3>
                    </div>
                    <div class="card-body">

                        <?php if ($user->fk_role < 3): ?>
                            <div class="text-right">
                                <a class="btn btn-success"
                                    href="<?php echo base_url('building_sites/add_speciality/' . $data->id) ?>">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                            <br>
                        <?php endif; ?>
                        <?php
                        if (sizeof($specialities) > 0):
                            ?>
                            <table class="table table-hover" id="data1">
                                <thead>
                                    <tr class="">
                                        <th>#</th>
                                        <th>Especialidad</th>
                                        <th>Roles</th>
                                        <th>HH acumuladas</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $x = 1;
                                    foreach ($specialities as $entry):
                                        ?>
                                        <tr>
                                            <th scope="row">
                                                <?php echo $x ?>
                                            </th>
                                            <td>
                                                <?= $entry->name ?>
                                            </td>
                                            <td>
                                                <?= $entry->speciality_roles_quantity ?>
                                            </td>
                                            <td>
                                                <?= number_format($entry->hh, 2, ",", ".") ?>
                                            </td>
                                            <td>
                                                <?php if ($user->fk_role < 3): ?>
                                                    <a class="btn btn-primary btn-sm"
                                                        href="<?php echo base_url('building_sites/edit_speciality/' . $entry->id) ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a class="btn btn-danger btn-sm"
                                                        href="<?php echo base_url('building_sites/remove_speciality/' . $entry->id) ?>">
                                                        <i class="fa fa-trash-o"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $x++;
                                    endforeach;
                                    ?>
                                </tbody>
                            </table>
                            <?php
                        else:
                            ?>
                            <p>No hay roles de especialidad disponibles para administrar</p>
                            <?php
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="h4">Areas</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($user->fk_role < 3): ?>
                            <div class="text-right">
                                <a class="btn btn-success" href="<?= base_url('building_sites/add_area/' . $data->id) ?>">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                            <br>
                        <?php endif; ?>
                        <?php
                        if (sizeof($areas) > 0):
                            ?>
                            <table class="table table-hover" id="data2">
                                <thead>
                                    <tr class="">
                                        <th>#</th>
                                        <th>Area</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $x = 1;
                                    foreach ($areas as $entry):
                                        ?>
                                        <tr>
                                            <th scope="row">
                                                <?php echo $x ?>
                                            </th>
                                            <td>
                                                <?= $entry->name ?>
                                            </td>
                                            <td>
                                                <?php if ($user->fk_role < 3): ?>
                                                    <a class="btn btn-primary btn-sm"
                                                        href="<?= base_url('building_sites/edit_area/' . $entry->id) ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a class="btn btn-danger btn-sm"
                                                        href="<?= base_url('building_sites/remove_area/' . $entry->id) ?>">
                                                        <i class="fa fa-trash-o"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $x++;
                                    endforeach;
                                    ?>
                                </tbody>
                            </table>
                            <?php
                        else:
                            ?>
                            <p>No hay areas disponibles para administrar</p>
                            <?php
                        endif;
                        ?>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function () {
        $('#data1, #data2').DataTable({
            "responsive": true,
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });
    });
</script>
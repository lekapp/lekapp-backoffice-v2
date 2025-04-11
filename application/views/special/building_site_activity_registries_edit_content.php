<!-- Feeds Section-->
<section class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="h4">Editar actividad</h3>
                    </div>
                    <div class="card-close">
                        <a href="<?= base_url('building_sites/list_activities/' . $building_site->id) ?>" class="dropdown-item">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="card-body">

                        <div>
                            <p>
                                Area: <?= $data->activity->zone->area->name ?>
                            </p>
                            <p>
                                Zona: <?= $data->activity->zone->name ?>
                            </p>
                        </div>

                        <hr>

                        <?php
                        $attr = array(
                            'method' =>    'post',
                            'class'    => 'forms-sample'
                        );
                        echo form_open_multipart('building_sites/edit_activities/' . $data->id, $attr);
                        ?>
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label" for="HH">HH</label>
                            <div class="col-sm-8">
                                <?php
                                $attr = array(
                                    'placeholder'    =>    '#',
                                    'class'            =>    'form-control p_input',
                                    'name'            =>    'hh',
                                    'value'            =>    $data->hh
                                );
                                echo form_input($attr);
                                echo form_error('h');
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label" for="Workers">Trabajadores</label>
                            <div class="col-sm-8">
                                <?php
                                $attr = array(
                                    'placeholder'    =>    '#',
                                    'class'            =>    'form-control p_input',
                                    'name'            =>    'workers',
                                    'value'            =>    $data->workers
                                );
                                echo form_input($attr);
                                echo form_error('workers');
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label" for="activity_date">Fecha actividad</label>
                            <div class="col-sm-8">
                                <?php
                                $attr = array(
                                    'placeholder'    =>    '#',
                                    'class'            =>    'form-control p_input',
                                    'name'            =>    'activity_date',
                                    'value'            =>    $data->activity_date
                                );
                                echo form_input($attr);
                                echo form_error('activity_date');
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label" for="avance">
                                Cantidad (Max:
                                <?= $data->activity->qty ?>
                                [<?= $data->activity->unt ?>])
                            </label>
                            <div class="col-sm-3">
                                <?php
                                $attr = array(
                                    'placeholder'    =>    '#',
                                    'class'            =>    'form-control p_input',
                                    'name'            =>    'avance',
                                    'type'            =>    'number',
                                    'value'            =>    $data->avance,
                                    'max'            =>    $data->activity->qty,
                                    'min'            =>    0,
                                    'step'            =>    0.0001,
                                    'id'            =>    'avance'
                                );
                                echo form_input($attr);
                                echo form_error('avance');
                                ?>
                            </div>
                            <div class="col-sm-5 text-left" id="p_avance" style="margin: auto;">
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('#avance').on('change', function() {
                                        var avance = $(this).val();
                                        var qty = <?= $data->activity->qty ?>;
                                        var porcentaje = (avance * 100) / qty;
                                        $('#p_avance').html('Porcentaje: ' + porcentaje.toFixed(2) + '%');
                                    }).trigger('change');
                                });
                            </script>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label" for="comment">Notas</label>
                            <div class="col-sm-8">
                                <?php
                                $attr = array(
                                    'placeholder'    =>    '#',
                                    'class'            =>    'form-control p_input',
                                    'name'            =>    'comment',
                                    'value'            =>    $data->comment
                                );
                                echo form_input($attr);
                                echo form_error('comment');
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label" for="machinery">Maquinaria</label>
                            <div class="col-sm-8">
                                <?php
                                $attr = array(
                                    'placeholder'    =>    '#',
                                    'class'            =>    'form-control p_input',
                                    'name'            =>    'machinery',
                                    'value'            =>    $data->machinery
                                );
                                echo form_input($attr);
                                echo form_error('machinery');
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label" for="fk_image">Imagen</label>
                            <div class="col-sm-8">
                                <?php
                                $attr = array(
                                    'placeholder'    =>    '#',
                                    'class'            =>    'form-control p_input',
                                    'name'            =>    'fk_image',
                                    'value'            =>    $data->fk_image
                                );
                                echo form_input($attr);
                                echo form_error('fk_image');
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label" for="activity_code">Código de Actividad</label>
                            <div class="col-sm-8">
                                <?php
                                $attr = array(
                                    'placeholder'    =>    '#',
                                    'class'            =>    'form-control p_input',
                                    'name'            =>    'activity_code',
                                    'value'            =>    $data->activity_code
                                );
                                echo form_input($attr);
                                echo form_error('activity_code');
                                ?>
                            </div>
                        </div>
                        <?php
                        if ($data->activity_report_file_url != '') :
                        ?>
                            <div class="form-group text-center">
                                <img class="col-xl-4 col-lg-6 col-md-6 col-sm-12" src="<?php echo asset_img($data->activity_report_file_url) ?>" />
                            </div>
                        <?php
                        else :
                        ?>
                            <div class="form-group row">
                                <label class="col-sm-4 form-control-label">Imagen actual</label>
                                <div class="col-sm-8">
                                    <div class="clearfix"></div>
                                    <div class="alert alert-danger" role="alert">
                                        No hay imagen actual
                                    </div>
                                </div>
                            </div>
                        <?php
                        endif;
                        ?>
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label" for="activity_report_file">Insertar o Reemplazar
                                imagen reporte</label>
                            <div class="col-sm-8">
                                <div class="clearfix"></div>
                                <?php
                                $attr = array(
                                    'class'            =>    'form-control p_input',
                                    'name'            =>    $data->activity_report_file
                                );
                                echo form_upload($attr);
                                echo form_error($data->activity_report_file);
                                ?>
                            </div>
                        </div>
                        <div class="line"></div>
                        <div class="form-group">
                            <div class="text-right">
                                <?php
                                $attr = array(
                                    'class'    =>    'btn btn-primary'
                                );
                                echo form_submit('update', 'Actualizar', $attr);
                                ?>
                            </div>
                        </div>
                        <?php echo form_close() ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
        </div>
    </div>
</section>
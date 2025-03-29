<!-- Feeds Section-->
<section class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="h4">Añadir hito</h3>
                    </div>
                    <div class="card-close">
                        <a href="<?= base_url('building_sites') ?>" class="dropdown-item">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <?php
                        $attr = array(
                            'method' =>    'post',
                            'class'    => 'form-horizontal'
                        );
                        echo form_open_multipart('building_sites/add_milestone/' . $user->building_site_id, $attr);
                        ?>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label" for="Nombre">Nombre</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder'    =>    'Nombre',
                                    'class'            =>    'form-control p_input',
                                    'name'            =>    'name',
                                    'value'            =>    set_value('name')
                                );
                                echo form_input($attr);
                                echo form_error('name');
                                ?>
                            </div>
                            <label class="col-sm-2 form-control-label" for="Especialidad">Obra</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = "class='form-control p_input' disabled='disabled'";
                                echo form_dropdown('fk_building_site', $building_sites, $user->building_site_id, $attr);
                                echo form_hidden('fk_building_site', $user->building_site_id);
                                echo form_error('fk_building_site');
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label" for="Tipo">Tipo</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = "class='form-control p_input'";
                                echo form_dropdown(
                                    'type',
                                    [
                                        'Inicio' => 'Inicio',
                                        'Término' => 'Término'
                                    ],
                                    null,
                                    $attr
                                );
                                echo form_error('type');
                                ?>
                            </div>
                            <label class="col-sm-2 form-control-label" for="Especialidad">Fecha</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder'    =>    'Fecha',
                                    'class'            =>    'form-control p_input',
                                    'name'            =>    'date',
                                    'value'            =>    set_value('date'),
                                    'type'             =>    'date'
                                );
                                echo form_input($attr);
                                echo form_error('date');
                                ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group row">
                            <div class="col-sm-4 offset-sm-2">
                                <?php
                                $attr = array(
                                    'class'    =>    'btn btn-primary'
                                );
                                echo form_submit('add', 'Añadir', $attr);
                                ?>
                            </div>
                        </div>
                        <?php echo form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
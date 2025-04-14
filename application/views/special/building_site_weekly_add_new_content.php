<!-- Feeds Section-->
<section class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="h4">Nuevo Reporte Semanal de Actividades</h3>
                    </div>
                    <div class="card-body">

                        <?php
                        $attr = array(
                            'method' => 'post',
                            'class' => 'form-horizontal'
                        );
                        echo form_open_multipart('building_sites/weekly_add_new/' . $user->building_site_id, $attr);
                        ?>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label" for="Nombre">Fecha</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder' => 'Fecha',
                                    'id' => 'datefield',
                                    'class' => 'form-control p_input datepicker',
                                    'name' => 'date',
                                    'type' => 'date',
                                    'value' => set_value('date')
                                );
                                echo form_input($attr);
                                echo form_error('date');
                                ?>
                            </div>
                            <div class="col-sm-6">
                                <p id="after7Days"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Administración</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder' => 'Administración',
                                    'class' => 'form-control',
                                    'name' => 'campo1',
                                    'value' => set_value('text')
                                );
                                echo form_textarea($attr);
                                echo form_error('text');
                                ?>
                            </div>
                            <label class="col-sm-2 form-control-label">Oficina Técnica</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder' => 'Oficina Técnica',
                                    'class' => 'form-control',
                                    'name' => 'campo2',
                                    'value' => set_value('text')
                                );
                                echo form_textarea($attr);
                                echo form_error('text');
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Prevención</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder' => 'Prevención',
                                    'class' => 'form-control',
                                    'name' => 'campo3',
                                    'value' => set_value('text')
                                );
                                echo form_textarea($attr);
                                echo form_error('text');
                                ?>
                            </div>
                            <label class="col-sm-2 form-control-label">Calidad</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder' => 'Calidad',
                                    'class' => 'form-control',
                                    'name' => 'campo4',
                                    'value' => set_value('text')
                                );
                                echo form_textarea($attr);
                                echo form_error('text');
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-5 offset-sm-2">
                                <?php
                                $attr = array(
                                    'class' => 'btn btn-primary'
                                );
                                echo form_submit('add', 'Siguiente', $attr);
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

<script type="text/javascript">
var today = new Date();
var dd = today.getDate();
var mm = today.getMonth() + 1; //January is 0!
var yyyy = today.getFullYear();
if (dd < 10) {
    dd = '0' + dd
}
if (mm < 10) {
    mm = '0' + mm
}

today = yyyy + '-' + mm + '-' + dd;

$('#datefield').on("change", function() {
    //if value is not empty, write the day + 7 in the after7Days paragraph

    var date = new Date($(this).val());
    var newDate = new Date(date);
    newDate.setDate(newDate.getDate() + 7);
    var dd = newDate.getDate();
    var mm = newDate.getMonth() + 1; //January is 0!
    var yyyy = newDate.getFullYear();

    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }

    newDate = dd + '-' + mm + '-' + yyyy;

    if ($(this).val() != '') {
        $('#after7Days').html('La fecha limite del reporte será: ' + newDate);
    } else {
        $('#after7Days').html('');
    }

});

//document.getElementById("datefield").setAttribute("max", today);
</script>
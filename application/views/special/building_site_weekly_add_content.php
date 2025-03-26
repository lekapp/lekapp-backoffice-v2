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
                            'method' =>    'post',
                            'class'    => 'form-horizontal'
                        );
                        echo form_open_multipart('building_sites/weekly_add/' . $user->building_site_id, $attr);
                        ?>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label" for="Nombre">Fecha</label>
                            <div class="col-sm-4">
                                <?php
                                $attr = array(
                                    'placeholder'    =>    'Fecha',
                                    'id'            =>  'datefield',
                                    'class'            =>    'form-control p_input datepicker',
                                    'name'            =>    'date',
                                    'type'             =>   'date',
                                    'value'            =>    set_value('date')
                                );
                                echo form_input($attr);
                                echo form_error('date');
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">
							<div class="col-sm-5 offset-sm-2">
								<?php
									$attr = array(
										'class'	=>	'btn btn-primary'
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
    //document.getElementById("datefield").setAttribute("max", today);
</script>
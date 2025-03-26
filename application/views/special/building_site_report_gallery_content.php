<!-- Feeds Section-->
<section class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="h4">Galería de Reporte Diario de Actividades</h3>
                    </div>
                    <div class="card-body">

                        <?php
                        $attr = array(
                            'method' =>    'post',
                            'class'    => 'form-horizontal'
                        );
                        echo form_open_multipart('building_sites/report_gallery/' . $user->daily_report_id, $attr);
                        ?>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label" for="Nombre">Fecha</label>
                            <div class="col-sm-4">
                                <?php
                                echo $data->daily_report->control_date;
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">

                            <?php foreach ($data->activity_registry as $gallery_image) : ?>

                                <?php if ($gallery_image->fk_image > 0 && $gallery_image->image->name != '') : ?>

                                    <div class="col-sm-4 col-md-2">
                                        <?php
                                        $attr = [
                                            'name'    => 'images[]',
                                            'value'   => $gallery_image->id,
                                        ];
                                        echo form_checkbox($attr);
                                        ?>
                                    </div>
                                    <div class="col-sm-8 col-md-4">
                                        <img src="<?= asset_img( $gallery_image->image->url . $gallery_image->image->id . "/" . $gallery_image->image->name . $gallery_image->image->ext ) ?>" width="100%">
                                    </div>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </div>

                        <div class="line"></div>
                        <div class="form-group row">
                            <div class="col-sm-5 offset-sm-2">
                                <?php
                                $attr = array(
                                    'class'    =>    'btn btn-primary'
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
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy'
        });
    });
</script>
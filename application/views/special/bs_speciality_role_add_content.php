<!-- Feeds Section-->

<section class="main">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-12">

                <div class="card">



                    <div class="card-header d-flex align-items-center">

                        <h3 class="h4">Añadir rol de especialidad</h3>

                    </div>



                    <div class="card-close">

                        <a href="<?= base_url('edit_speciality/' . $user->speciality_id) ?>" class="dropdown-item">

                            <i class="fa fa-arrow-left"></i>

                        </a>

                    </div>



                    <div class="card-body">



                        <?php

						$attr = array(

							'method' =>	'post',

							'class'	=> 'form-horizontal'

						);

						echo form_open_multipart('building_sites/add_speciality_role/' . $user->speciality_id, $attr);

						?>



                        <div class="form-group row">

                            <label class="col-lg-2 form-control-label" for="Nombre">Rol Especialidad</label>

                            <div class="col-lg-4">

                                <?php

								$attr = array(

									'placeholder'	=>	'Nombre',

									'class'			=>	'form-control p_input',

									'name'			=>	'name',

									'value'			=>	set_value('name')

								);

								echo form_input($attr);

								echo form_error('name');

								?>

                            </div>

                            <label class="col-lg-2 form-control-label" for="Rol">Rol</label>

                            <div class="col-lg-4">

                                <?php

								$attr = "class='form-control p_input' disabled='disabled'";

								echo form_dropdown('fk_speciality', $specialities, $user->speciality_id, $attr);

								echo form_hidden('fk_speciality', $user->speciality_id);

								echo form_error('fk_speciality');

								?>

                            </div>

                        </div>

                        <input type="hidden" name="hh" value="0">

                        <input type="hidden" name="fk_building_site" value="<?= $user->building_site_id ?>">

                        <div class="line"></div>

                        <div class="form-group row">

                            <div class="col-sm-5 offset-sm-2">

                                <?php

								$attr = array(

									'class'	=>	'btn btn-primary'

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
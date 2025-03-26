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

						<a href="<?= base_url('speciality_roles') ?>" class="dropdown-item"> 

							<i class="fa fa-arrow-left"></i> 

						</a>

					</div>



					<div class="card-body">



						<?php

						$attr = array(

							'method' =>	'post',

							'class'	=> 'form-horizontal'

						);

						echo form_open_multipart('speciality_roles/add', $attr); 

						?>



						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Glosa">Glosa Rol</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'Maestro',

									'class'			=>	'form-control p_input',

									'name'			=>	'name',

									'value'			=>	set_value('name')

								);

								echo form_input($attr);

								echo form_error('name');

								?>

							</div>



							<label class="col-sm-2 form-control-label" for="Especialidad">Especialidad</label>

							<div class="col-sm-4">

								<?php

								$attr = "class='form-control p_input'";

								echo form_dropdown('fk_speciality', $specialities , 0, $attr);

								echo form_error('fk_speciality');

								?>

							</div>

						</div>



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
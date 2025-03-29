<!-- Feeds Section-->

<section class="main">

	<div class="container-fluid">

		<div class="row">

			<div class="col-lg-12">

				<div class="card">



					<div class="card-header d-flex align-items-center">

						<h3 class="h4">Editar Supervisor</h3>

					</div>

					<?php // d($user); ?>
					<?php // d($data); ?>

					<div class="card-close">

						<a href="<?php echo base_url('building_sites/edit_speciality/' . $data->speciality->id); ?>" class="dropdown-item">

							<i class="fa fa-arrow-left"></i>

						</a>

					</div>

					<div class="card-body">



						<?php

						$attr = array(

							'method' =>	'post',

							'class'	=> 'form-horizontal'

						);

						echo form_open_multipart('building_sites/edit_supervisor/' . $data->id, $attr);

						?>


						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Obra">Obra</label>

							<div class="col-sm-4">

								<?php echo $data->speciality->building_site->name; ?>

							</div>

							<label class="col-sm-2 form-control-label" for="Especialidad">Especialidad</label>

							<div class="col-sm-4">
								<?php echo $data->speciality->name; ?>
							</div>

						</div>

						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Nombre">Nombre</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'Nombre',

									'class'			=>	'form-control p_input',

									'name'			=>	'first_name',

									'value'			=>	$data->user->first_name

								);

								echo form_input($attr);

								echo form_error('first_name');

								?>

							</div>

							<label class="col-sm-2 form-control-label" for="Apellido">Apellido</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'Apellido',

									'class'			=>	'form-control p_input',

									'name'			=>	'last_name',

									'value'			=>	$data->user->last_name

								);

								echo form_input($attr);

								echo form_error('last_name');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Email">Email</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'Email',

									'class'			=>	'form-control p_input',

									'name'			=>	'email',

									'value'			=>	$data->user->email

								);

								echo form_input($attr);

								echo form_error('email');

								?>

							</div>

							<label class="col-sm-2 form-control-label" for="DNI">RUT/DNI</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'XX.XXX.XXX-X',

									'class'			=>	'form-control p_input',

									'name'			=>	'dni',

									'value'			=>	$data->user->dni

								);

								echo form_input($attr);

								echo form_error('dni');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="DNI">Dirección 1</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'Dirección',

									'class'			=>	'form-control p_input',

									'name'			=>	'address_1',

									'value'			=>	$data->user->address_1

								);

								echo form_input($attr);

								echo form_error('address_1');

								?>

							</div>

							<label class="col-sm-2 form-control-label" for="DNI">Dirección 2</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'Dirección',

									'class'			=>	'form-control p_input',

									'name'			=>	'address_2',

									'value'			=>	$data->user->address_2

								);

								echo form_input($attr);

								echo form_error('address_2');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Phone">Fono 1</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'+56 XXXXXXXXX',

									'class'			=>	'form-control p_input',

									'name'			=>	'phone_1',

									'value'			=>	$data->user->phone_1

								);

								echo form_input($attr);

								echo form_error('phone_1');

								?>

							</div>

							<label class="col-sm-2 form-control-label" for="Phone">Fono 2</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'+56 XXXXXXXXX',

									'class'			=>	'form-control p_input',

									'name'			=>	'phone_2',

									'value'			=>	$data->user->phone_2

								);

								echo form_input($attr);

								echo form_error('phone_2');

								?>

							</div>

						</div>





						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Rol">Rol</label>

							<div class="col-sm-4">

								Supervisor

							</div>

						</div>



						<div class="line"></div>



						<div class="form-group row">

							<div class="col-sm-5 offset-sm-2">

								<?php

								$attr = array(

									'class'	=>	'btn btn-primary'

								);

								echo form_submit('update_1', 'Editar', $attr);

								?>

							</div>

						</div>



						<?php echo form_close() ?>



						<hr>



						<?php

						$attr = array(

							'method' =>	'post',

							'class'	=> 'form-horizontal'

						);

						echo form_open_multipart('building_sites/edit_supervisor/' . $data->id, $attr);

						?>



						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Contraseña">Contraseña</label>

							<div class="col-sm-10">

								<?php

								$attr = array(

									'placeholder'	=>	'Contraseña',

									'class'			=>	'form-control p_input',

									'name'			=>	'password',

									'value'			=>	''

								);

								echo form_password($attr);

								echo form_error('password');

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

								echo form_submit('update_2', 'Editar Contraseña', $attr);

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
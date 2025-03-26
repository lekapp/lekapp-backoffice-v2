<!-- Feeds Section-->

<section class="main">

	<div class="container-fluid">

		<div class="row">

			<div class="col-lg-12">

				<div class="card">



					<div class="card-header d-flex align-items-center">

						<h3 class="h4">Ingresa tus datos</h3>

					</div>



					<div class="card-close">

						<a href="<?= base_url('login') ?>" class="dropdown-item"> 

							<i class="fa fa-arrow-left"></i>

						</a>

					</div>



					<div class="card-body">



						<?php

						$attr = array(

							'method' =>	'post',

							'class'	=> 'form-horizontal'

						);

						echo form_open_multipart('profile/add', $attr); 

						?>



						<div class="form-group row">

							<label class="col-sm-1 form-control-label" for="Nombre">Nombre</label>

							<div class="col-sm-5">

								<?php

								$attr = array(

									'placeholder'	=>	'Nombre',

									'class'			=>	'form-control p_input',

									'name'			=>	'first_name',

									'value'			=>	set_value('first_name')

								);

								echo form_input($attr);

								echo form_error('first_name');

								?>

							</div>

							<label class="col-sm-1 form-control-label" for="Apellido">Apellido</label>

							<div class="col-sm-5">

								<?php

								$attr = array(

									'placeholder'	=>	'Apellido',

									'class'			=>	'form-control p_input',

									'name'			=>	'last_name',

									'value'			=>	set_value('last_name')

								);

								echo form_input($attr);

								echo form_error('last_name');

								?>

							</div>

						</div>



						<div class="form-group row">

							

						</div>



						<div class="form-group row">

							<label class="col-sm-1 form-control-label" for="Email">Email</label>

							<div class="col-sm-5">

								<?php

								$attr = array(

									'placeholder'	=>	'Email',

									'class'			=>	'form-control p_input',

									'name'			=>	'email',

									'value'			=>	set_value('email')

								);

								echo form_input($attr);

								echo form_error('email');

								?>

							</div>

							<label class="col-sm-1 form-control-label" for="Contraseña">Contraseña</label>

							<div class="col-sm-5">

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



						<div class="form-group row">

							

						</div>



						<div class="form-group row">

							<label class="col-sm-1 form-control-label" for="RUT/DNI">RUT/DNI</label>

							<div class="col-sm-5">

								<?php

								$attr = array(

									'placeholder'	=>	'XX.XXX.XXX-X',

									'class'			=>	'form-control p_input',

									'name'			=>	'dni',

									'value'			=>	set_value('dni')

								);

								echo form_input($attr);

								echo form_error('dni');

								?>

							</div>

							<label class="col-sm-1 form-control-label" for="Dirección 1">Dirección 1</label>

							<div class="col-sm-5">

								<?php

								$attr = array(

									'placeholder'	=>	'Dirección',

									'class'			=>	'form-control p_input',

									'name'			=>	'address_1',

									'value'			=>	set_value('address_1')

								);

								echo form_input($attr);

								echo form_error('address_1');

								?>

							</div>

						</div>



						<div class="form-group row">

							

						</div>



						<div class="form-group row">

							<label class="col-sm-1 form-control-label" for="Dirección 1">Dirección 2</label>

							<div class="col-sm-5">

								<?php

								$attr = array(

									'placeholder'	=>	'Dirección',

									'class'			=>	'form-control p_input',

									'name'			=>	'address_2',

									'value'			=>	set_value('address_2')

								);

								echo form_input($attr);

								echo form_error('address_2');

								?>

							</div>

							<label class="col-sm-1 form-control-label" for="Fono 1">Fono 1</label>

							<div class="col-sm-5">

								<?php

								$attr = array(

									'placeholder'	=>	'+56 XXXXXXXXX',

									'class'			=>	'form-control p_input',

									'name'			=>	'phone_1',

									'value'			=>	set_value('phone_1')

								);

								echo form_input($attr);

								echo form_error('phone_1');

								?>

							</div>

						</div>



						<div class="form-group row">

							

						</div>



						<div class="form-group row">

							<label class="col-sm-1 form-control-label" for="Fono 2">Fono 2</label>

							<div class="col-sm-5">

								<?php

								$attr = array(

									'placeholder'	=>	'+56 XXXXXXXXX',

									'class'			=>	'form-control p_input',

									'name'			=>	'phone_2',

									'value'			=>	set_value('phone_2')

								);

								echo form_input($attr);

								echo form_error('phone_2');

								?>

							</div>

							<label class="col-sm-1 form-control-label" for="Rol">Rol</label>

							<div class="col-sm-5">

								<?php

								$attr = "class='form-control p_input'";

								echo form_dropdown('fk_role', $roles, 0, $attr);

								echo form_error('fk_role');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-1 form-control-label" for="Avatar">Avatar</label>

							<div class="col-sm-11">

								<?php

								$attr = array(

									'class'			=>	'form-control p_input',

									'name'			=>	$data->avatar_file

								);

								echo form_upload($attr);

								echo form_error( $data->avatar_file );

								?>

								<?php

								if( $error['code'] != 0 ):

									?>

									<span class="btn btn-outline-warning">

										<?= $error['message'] ?>

									</span>

									<?php

								endif;

								?>

							</div>

						</div>



						<div class="line"></div>



						<div class="form-group row">

							<div class="col-sm-1 offset-sm-1">

								<?php

								$attr = array(

									'class'	=>	'btn btn-primary'

								);

								echo form_submit('add', 'Registrate', $attr);

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
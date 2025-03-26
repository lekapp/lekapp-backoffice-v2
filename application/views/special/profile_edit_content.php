<!-- Feeds Section-->

<section class="main">

	<div class="container-fluid">

		<div class="row">

			<div class="col-lg-12">



				<div class="card">



					<div class="card-close">

						<a href="<?= base_url('profile') ?>" class="dropdown-item"> 

							<i class="fa fa-arrow-left"></i>

						</a>

					</div>



					<div class="card-header d-flex align-items-center">

						<h3 class="h4">Editar perfil</h3>

					</div>



					<div class="card-body">



						<?php

						$attr = array(

							'method' =>	'post',

							'class'	=> 'form-horizontal'

						);

						echo form_open_multipart('profile/edit', $attr); 

						?>



						<div class="form-group row">

							<label class="col-sm-3 form-control-label" for="Nombre">Nombre</label>

							<div class="col-sm-9">

								<?php

								$attr = array(

									'placeholder'	=>	'Nombre',

									'class'			=>	'form-control p_input',

									'name'			=>	'first_name',

									'value'			=>	$user->first_name

								);

								echo form_input($attr);

								echo form_error('first_name');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-3 form-control-label" for="Apellido">Apellido</label>

							<div class="col-sm-9">

								<?php

								$attr = array(

									'placeholder'	=>	'Apellido',

									'class'			=>	'form-control p_input',

									'name'			=>	'last_name',

									'value'			=>	$user->last_name

								);

								echo form_input($attr);

								echo form_error('last_name');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-3 form-control-label" for="Email">Email</label>

							<div class="col-sm-9">

								<?php

								$attr = array(

									'placeholder'	=>	'Email',

									'class'			=>	'form-control p_input',

									'name'			=>	'email',

									'value'			=>	$user->email

								);

								echo form_input($attr);

								echo form_error('email');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-3 form-control-label" for="DNI">RUT/DNI</label>

							<div class="col-sm-9">

								<?php

								$attr = array(

									'placeholder'	=>	'XX.XXX.XXX-X',

									'class'			=>	'form-control p_input',

									'name'			=>	'dni',

									'value'			=>	$user->dni

								);

								echo form_input($attr);

								echo form_error('dni');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-3 form-control-label" for="Dir1">Dirección 1</label>

							<div class="col-sm-9">

								<?php

								$attr = array(

									'placeholder'	=>	'Dirección',

									'class'			=>	'form-control p_input',

									'name'			=>	'address_1',

									'value'			=>	$user->address_1

								);

								echo form_input($attr);

								echo form_error('address_1');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-3 form-control-label" for="Dir2">Dirección 2</label>

							<div class="col-sm-9">

								<?php

								$attr = array(

									'placeholder'	=>	'Dirección',

									'class'			=>	'form-control p_input',

									'name'			=>	'address_2',

									'value'			=>	$user->address_2

								);

								echo form_input($attr);

								echo form_error('address_2');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-3 form-control-label" for="Pho1">Phone 1</label>

							<div class="col-sm-9">

								<?php

								$attr = array(

									'placeholder'	=>	'+56 XXXXXXXXX',

									'class'			=>	'form-control p_input',

									'name'			=>	'phone_1',

									'value'			=>	$user->phone_1

								);

								echo form_input($attr);

								echo form_error('phone_1');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-3 form-control-label" for="Pho1">Phone 2</label>

							<div class="col-sm-9">

								<?php

								$attr = array(

									'placeholder'	=>	'+56 XXXXXXXXX',

									'class'			=>	'form-control p_input',

									'name'			=>	'phone_2',

									'value'			=>	$user->phone_2

								);

								echo form_input($attr);

								echo form_error('phone_2');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-3 form-control-label" for="Genders">Género</label>

							<div class="col-sm-9">

								<?php

								$attr = "class='form-control p_input'";

								echo form_dropdown('fk_gender', $genders, $user->fk_gender, $attr);

								echo form_error('fk_gender');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-3 form-control-label" for="Ava">Avatar</label>

							<div class="clearfix"></div>

							<div class="col-md-3">

								<img class="img-fluid" src="<?php echo asset_img( $user->avatar_url ) ?>" />

							</div>

							<!--<div class="clearfix"></div>-->

							<?php

							$attr = array(

								'class'			=>	'',

								'name'			=>	$user->avatar_file

							);

							echo form_upload($attr);

							echo form_error( $user->avatar_file );

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



						<div class="line"></div>



						<div class="form-group row">

							<div class="col-sm-4 offset-sm-3">

								<?php

								$attr = array(

									'class'	=>	'btn btn-primary'

								);

								echo form_submit('update_1', 'Guardar', $attr);

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

						echo form_open_multipart('profile/edit', $attr); 

						?>



						<div class="form-group row">

							<label class="col-sm-3 form-control-label" for="Contraseña">Contraseña</label>

							<div class="col-sm-9">

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

							<div class="col-sm-4 offset-sm-3">

								<?php

								$attr = array(

									'class'	=>	'btn btn-primary'

								);

								echo form_submit('update_2', 'Guardar', $attr);

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
<!-- Feeds Section-->

<section class="main">

	<div class="container-fluid">

		<div class="row">

			<div class="col-lg-12">

				<div class="card">



					<div class="card-header d-flex align-items-center">

						<h3 class="h4">Añadir obra</h3>

					</div>



					<div class="card-close">

						<a href="<?= base_url('building_sites') ?>" class="dropdown-item"> 

							<i class="fa fa-arrow-left"></i> 

						</a>

					</div>



					<div class="card-body">



						<?php

						$attr = array(

							'method' =>	'post',

							'class'	=> 'form-horizontal'

						);

						echo form_open_multipart('building_sites/add', $attr); 

						?>



						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Glosa">Nombre obra</label>

							<div class="col-sm-4">

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



							<label class="col-sm-2 form-control-label" for="Glosa">Código</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'Código',

									'class'			=>	'form-control p_input',

									'name'			=>	'code',

									'value'			=>	set_value('code')

								);

								echo form_input($attr);

								echo form_error('code');

								?>

							</div>



						</div>



						<div class="form-group row">



							<label class="col-sm-2 form-control-label" for="Especialidad">Cliente</label>

							<div class="col-sm-4">

								<?php

								$attr = "class='form-control p_input'";

								echo form_dropdown('fk_client', $clients , 0, $attr);

								echo form_error('fk_client');

								?>

							</div>



							<label class="col-sm-2 form-control-label" for="Especialidad">Mandante</label>

							<div class="col-sm-4">

								<?php

								$attr = "class='form-control p_input'";

								echo form_dropdown('fk_contractor', $contractors , 0, $attr);

								echo form_error('fk_contractor');

								?>

							</div>



						</div>



						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Glosa">Dirección</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'Calle',

									'class'			=>	'form-control p_input',

									'name'			=>	'address_street',

									'value'			=>	set_value('address_street')

								);

								echo form_input($attr);

								echo form_error('address_street');

								?>

							</div>



							<div class="col-sm-2">

								<?php

								$attr = array(

									'placeholder'	=>	'Número',

									'class'			=>	'form-control p_input',

									'name'			=>	'address_number',

									'value'			=>	set_value('address_number')

								);

								echo form_input($attr);

								echo form_error('date_end');

								?>

							</div>



							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'Ciudad',

									'class'			=>	'form-control p_input',

									'name'			=>	'address_city',

									'value'			=>	set_value('address_city')

								);

								echo form_input($attr);

								echo form_error('address_city');

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
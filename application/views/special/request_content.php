<!-- Feeds Section-->

<section class="main">

	<div class="container-fluid">

		<div class="row">

			<div class="col-lg-12">

				<div class="card">



					<div class="card-header">

						<h4>

							Actividad: <?= $data->activity->name ?> ( <?= $data->activity->activity_code ?> )

						</h4>

						<h4>

							Especialidad: <?= $data->activity->speciality->name ?>

						</h4>

						<h4>

							ROL: <?= $data->activity->speciality_role->name ?>

						</h4>

					</div>



					<div class="card-close">

						<a href="<?= base_url('users') ?>" class="dropdown-item"> 

							<i class="fa fa-arrow-left"></i> 

						</a>
					</div>



					<div class="card-body">



						<?php

						$attr = array(

							'method' =>	'post',

							'class'	=> 'form-horizontal'

						);

						echo form_open_multipart('dashboard/request/' . $data->activity->id, $attr); 

						?>



						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Nombre">HH</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'HH',

									'class'			=>	'form-control p_input',

									'name'			=>	'hh',

									'type'			=>	"number",

									'step'			=>	0.01,

									'min'			=>	0,

									'value'			=>	set_value('hh', 0)

								);

								echo form_input($attr);

								echo form_error('hh');

								?>

							</div>

							<label class="col-sm-2 form-control-label" for="Nombre">Comentario</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'Comentario',

									'class'			=>	'form-control p_input',

									'name'			=>	'comment',

									'value'			=>	set_value('comment')

								);

								echo form_input($attr);

								echo form_error('comment');

								?>

							</div>

						</div>



						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Nombre">Fecha</label>

							<div class="col-sm-4">

								<?php

								$attr = array(

									'placeholder'	=>	'Fecha',

									'class'			=>	'form-control p_input datepicker',

									'name'			=>	'date',

									'value'			=>	set_value('date')

								);

								echo form_input($attr);

								echo form_error('date');

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



<script type="text/javascript">

	$(document).ready(function(){

		$('.datepicker').datepicker();

	});

</script>
<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Agregar actividad</h3>
					</div>
					<div class="card-close">
						<a href="<?= base_url('building_sites/list_activities/' . $building_site->id) ?>" class="dropdown-item">
							<i class="fa fa-arrow-left"></i>
						</a>
					</div>
					<div class="card-body">
						<?php
						$attr = array(
							'method' =>	'post',
							'class'	=> 'forms-sample'
						);
						echo form_open_multipart('building_sites/add_activities/' . $building_site->id, $attr);
						?>
						<div class="form-group row">
							<label class="col-sm-4 form-control-label" for="HH">HH</label>
							<div class="col-sm-8">
								<?php
								$attr = array(
									'placeholder'	=>	'#',
									'class'			=>	'form-control p_input',
									'name'			=>	'hh',
									'value'			=>	$data->hh
								);
								echo form_input($attr);
								echo form_error('h');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-4 form-control-label" for="Workers">Trabajadores</label>
							<div class="col-sm-8">
								<?php
								$attr = array(
									'placeholder'	=>	'#',
									'class'			=>	'form-control p_input',
									'name'			=>	'workers',
									'value'			=>	$data->workers
								);
								echo form_input($attr);
								echo form_error('workers');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-4 form-control-label" for="activity_date">Fecha actividad</label>
							<div class="col-sm-8">
								<?php
								$attr = array(
									'placeholder'	=>	'#',
									'class'			=>	'form-control p_input',
									'name'			=>	'activity_date',
									'value'			=>	$data->activity_date
								);
								echo form_input($attr);
								echo form_error('activity_date');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-4 form-control-label" for="avance">Avance (Max:
								<?= $data->activity->qty ?>
								[<?= $data->activity->unt ?>])</label>
							<div class="col-sm-8">
								<?php
								$attr = array(
									'placeholder'	=>	'#',
									'class'			=>	'form-control p_input',
									'name'			=>	'avance',
									'type'			=>	'number',
									'value'			=>	$data->avance,
									'max'			=>	$data->activity->qty,
									'min'			=>	0,
									'step'			=>	0.0001
								);
								echo form_input($attr);
								echo form_error('avance');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-4 form-control-label" for="comment">Notas</label>
							<div class="col-sm-8">
								<?php
								$attr = array(
									'placeholder'	=>	'#',
									'class'			=>	'form-control p_input',
									'name'			=>	'comment',
									'value'			=>	$data->comment
								);
								echo form_input($attr);
								echo form_error('comment');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-4 form-control-label" for="machinery">Maquinaria</label>
							<div class="col-sm-8">
								<?php
								$attr = array(
									'placeholder'	=>	'#',
									'class'			=>	'form-control p_input',
									'name'			=>	'machinery',
									'value'			=>	$data->machinery
								);
								echo form_input($attr);
								echo form_error('machinery');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-4 form-control-label" for="fk_image">Imagen</label>
							<div class="col-sm-8">
								<?php
								$attr = array(
									'placeholder'	=>	'#',
									'class'			=>	'form-control p_input',
									'name'			=>	'fk_image',
									'value'			=>	$data->fk_image
								);
								echo form_input($attr);
								echo form_error('fk_image');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-4 form-control-label" for="activity_code">Código de Actividad</label>
							<div class="col-sm-8">
								<?php
								$attr = array(
									'placeholder'	=>	'#',
									'class'			=>	'form-control p_input',
									'name'			=>	'activity_code',
									'value'			=>	$data->activity_code
								);
								echo form_input($attr);
								echo form_error('activity_code');
								?>
							</div>
						</div>

						<div class="line"></div>
						<div class="form-group">
							<div class="text-right">
								<?php
								$attr = array(
									'class'	=>	'btn btn-primary'
								);
								echo form_submit('add', 'Agregar', $attr);
								?>
							</div>
						</div>
						<?php echo form_close() ?>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<br>
		</div>
	</div>
</section>
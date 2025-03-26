<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">

					<div class="card-header">
						<h3 class="h4">Chequeo</h3>
						<h4>
							<?= gmdate( "d-m-Y", $data->activity_data->activity_date * 86400 ) ?> - <?= $data->activity_data->activity->speciality_role->name ?> - <?= $data->activity_data->activity->zone->name ?> - <?= $data->activity_data->activity->activity_code ?>
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
						echo form_open_multipart('dashboard/check/' . $data->activity_data->id, $attr); 
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
							
							<label class="col-sm-2 form-control-label" for="Avatar">Avatar</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'class'			=>	'form-control p_input',
									'name'			=>	$data->avatar_file,
									'accept'		=>	"image/*",
									'capture'		=>	"camera",
									'onchange'		=>	"javascript:readURL(this);"
								);
								echo form_upload($attr);
								echo form_error( $data->avatar_file );
								?>
								<img id="myid" src ="#" alt="Nueva imagen" width="50%" />
								<script type="text/javascript">
									function readURL(input) {
										if (input.files && input.files[0]) {
											var reader = new FileReader();
											reader.onload = function (e) {
												$('#myid')
												.attr('src', e.target.result)
											};

											reader.readAsDataURL(input.files[0]);
										}
									}  
								</script>
							</div>
							<label class="col-sm-2 form-control-label" for="Nombre">Estado</label>
							<div class="col-sm-4">
								<?php
								$attr = "class='form-control p_input'";
								echo form_dropdown('fk_activity_state', $data->states , 0, $attr);
								echo form_error('fk_activity_state');
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
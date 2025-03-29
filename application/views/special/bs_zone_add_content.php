<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Añadir zona (Sub area)</h3>
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
						echo form_open_multipart('building_sites/add_zone/' . $user->building_site_id, $attr);
						?>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Nombre">Nombre</label>
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
						</div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="">Area</label>
							<div class="col-sm-4">
								<?php
								echo $user->area->name;
								echo form_hidden('fk_area', $user->area_id);
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="">Obra</label>
							<div class="col-sm-4">
								<?php
								echo $user->building_site->name;
								echo form_hidden('fk_building_site', $user->building_site_id);
								?>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="form-group row">
							<div class="col-sm-4 offset-sm-2">
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
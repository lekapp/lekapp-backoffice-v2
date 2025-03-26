<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">

					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Añadir trabajador</h3>
					</div>

					<div class="card-close">
						<a href="<?php echo base_url('building_sites/edit_speciality_role/' . $user->speciality_role[0]->id) ?>" class="dropdown-item">
							<i class="fa fa-arrow-left"></i>
						</a>
					</div>

					<div class="card-body"> 
						<?php
						$attr = array(
							'method' =>	'post',
							'class'	=> 'form-horizontal'
						);
						echo form_open_multipart('building_sites/add_worker/' . $user->speciality_role[0]->id, $attr); 

						echo form_hidden('fk_building_site', $user->speciality_role[0]->fk_building_site);
						echo form_hidden('fk_speciality', $user->speciality_role[0]->fk_speciality);
						echo form_hidden('fk_speciality_role', $user->speciality_role[0]->id);
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
							<label class="col-sm-2 form-control-label" for="Email">Email</label>
							<div class="col-sm-4">
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
						</div>

						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Contraseña">Contraseña</label>
							<div class="col-sm-4">
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
							<label class="col-sm-2 form-control-label" for="RUT/DNI">RUT/DNI</label>
							<div class="col-sm-4">
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
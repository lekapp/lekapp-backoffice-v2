<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<h3 class="h4">Nuevo Reporte Diario de Actividades</h3>
					</div>
					<div class="card-body">

						<?php
						$attr = array(
							'method' => 'post',
							'class' => 'form-horizontal'
						);
						echo form_open_multipart('building_sites/report_add/' . $user->building_site_id, $attr);
						?>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Nombre">Fecha</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => 'Fecha',
									'class' => 'form-control p_input datepicker',
									'name' => 'date',
									'value' => set_value('date')
								);
								echo form_input($attr);
								echo form_error('date');
								?>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<strong>Nombre del Contrato: </strong>
								<span><?= $data->building_site->name ?></span>
							</div>
							<div class="col-lg-6">
								<strong>Código del Contrato: </strong>
								<span><?= $data->building_site->code ?></span>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Nombre administrador de
								contrato</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'admin_name',
									'value' => $data->building_site->b1_n
								);
								echo form_input($attr);
								echo form_error('admin_name');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Nombre jefe de oficina</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'office_chief',
									'value' => set_value('office_chief')
								);
								echo form_input($attr);
								echo form_error('office_chief');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Nombre jefe de terreno</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'terrain_chief',
									'value' => $data->building_site->b3_n
								);
								echo form_input($attr);
								echo form_error('terrain_chief');
								?>
							</div>
						</div>

						<br>
						<div class="form-group row">
							<div class="col-lg-6">
								<h4>ACTIVIDADES REELEVANTES</h4>
								<p>...</p>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Discurso de seguridad</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'security_speech',
									'value' => set_value('security_speech')
								);
								echo form_input($attr);
								echo form_error('security_speech');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Calidad</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'quality',
									'value' => set_value('quality')
								);
								echo form_input($attr);
								echo form_error('quality');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Interferencias</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'interferences',
									'value' => set_value('interferences')
								);
								echo form_input($attr);
								echo form_error('interferences');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Visitas</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'visits',
									'value' => set_value('visits')
								);
								echo form_input($attr);
								echo form_error('visits');
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Otros</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'others',
									'value' => set_value('others')
								);
								echo form_input($attr);
								echo form_error('others');
								?>
							</div>
						</div>
						<hr>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Empresa Contratista</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b1_ne',
									'value' => '' /*$data->building_site->b1_ne*/
								);
								echo form_input($attr);
								echo form_error('b1_ne');
								?>
							</div>
							<label class="col-sm-2 form-control-label" for="Glosa">Nombre</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b1_n',
									'value' => '' /*$data->building_site->b1_n*/
								);
								echo form_input($attr);
								echo form_error('b1_n');
								?>
							</div>
							<label class="col-sm-2 form-control-label" for="Glosa">Cargo</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b1_c',
									'value' => '' /*$data->building_site->b1_c*/
								);
								echo form_input($attr);
								echo form_error('b1_c');
								?>
							</div>
						</div>
						<hr>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Empresa Contratista</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b2_ne',
									'value' => '' /*$data->building_site->b2_ne*/
								);
								echo form_input($attr);
								echo form_error('b2_ne');
								?>
							</div>
							<label class="col-sm-2 form-control-label" for="Glosa">Nombre</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b2_n',
									'value' => '' /*$data->building_site->b2_n*/
								);
								echo form_input($attr);
								echo form_error('b2_n');
								?>
							</div>
							<label class="col-sm-2 form-control-label" for="Glosa">Cargo</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b2_c',
									'value' => '' /*$data->building_site->b2_c*/
								);
								echo form_input($attr);
								echo form_error('b2_c');
								?>
							</div>
						</div>
						<hr>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Empresa Contratista</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b3_ne',
									'value' => '' /*$data->building_site->b3_ne*/
								);
								echo form_input($attr);
								echo form_error('b3_ne');
								?>
							</div>
							<label class="col-sm-2 form-control-label" for="Glosa">Nombre</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b3_n',
									'value' => '' /*$data->building_site->b3_n*/
								);
								echo form_input($attr);
								echo form_error('b3_n');
								?>
							</div>
							<label class="col-sm-2 form-control-label" for="Glosa">Cargo</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b3_c',
									'value' => '' /*$data->building_site->b3_c*/
								);
								echo form_input($attr);
								echo form_error('b3_c');
								?>
							</div>
						</div>
						<hr>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Glosa">Empresa Mandante</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b4_ne',
									'value' => '' /*$data->building_site->b4_ne*/
								);
								echo form_input($attr);
								echo form_error('b4_ne');
								?>
							</div>
							<label class="col-sm-2 form-control-label" for="Glosa">Nombre</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b4_n',
									'value' => '' /*$data->building_site->b4_n*/
								);
								echo form_input($attr);
								echo form_error('b4_n');
								?>
							</div>
							<label class="col-sm-2 form-control-label" for="Glosa">Cargo</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder' => '',
									'class' => 'form-control p_input',
									'name' => 'b4_c',
									'value' => '' /*$data->building_site->b4_c*/
								);
								echo form_input($attr);
								echo form_error('b4_c');
								?>
							</div>
						</div>
						<div class="line"></div>
						<div class="form-group row">
							<div class="col-sm-5 offset-sm-2">
								<?php
								$attr = array(
									'class' => 'btn btn-primary'
								);
								echo form_submit('add', 'Siguiente', $attr);
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
	$(document).ready(function() {
		$('.datepicker').datepicker({
			format: 'dd/mm/yyyy'
		});
	});
</script>
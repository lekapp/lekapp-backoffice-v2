<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xl-12 col-sm-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Editar area</h3>
					</div>
					<div class="card-close">
						<a href="<?= base_url('building_sites/edit/' . $data->building_site->id) ?>" class="dropdown-item">
							<i class="fa fa-arrow-left"></i>
						</a>
					</div>
					<div class="card-body">
						<?php
						$attr = array(
							'method' =>	'post',
							'class'	=> 'forms-sample'
						);
						echo form_open_multipart('building_sites/edit_area/' . $data->id, $attr);
						?>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Nombre">Nombre</label>
							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder'	=>	'Nombre',
									'class'			=>	'form-control p_input',
									'name'			=>	'name',
									'value'			=>	$data->name
								);
								echo form_input($attr);
								echo form_error('name');
								?>
							</div>
							<label class="col-sm-2 form-control-label" for="Especialidad">Obra</label>
							<div class="col-sm-4">
								<?php
								$attr = "class='form-control p_input' disabled='disabled'";
								echo form_dropdown('fk_building_site', $building_sites, $data->fk_building_site, $attr);
								echo form_hidden('fk_building_site', $data->fk_building_site);
								echo form_error('fk_building_site');
								?>
							</div>
						</div>
						<div class="line"></div>
						<div class="form-group row">
							<div class="col-sm-4 offset-sm-2">
								<?php
								$attr = array(
									'class'	=>	'btn btn-primary'
								);
								echo form_submit('update', 'Actualizar', $attr);
								?>
							</div>
						</div>
						<?php echo form_close() ?>
					</div>
				</div>
			</div>

			<div class="col-xl-12 col-sm-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Zonas (Sub áreas)</h3>
					</div>
					<div class="card-body">
						<?php if ($user->fk_role < 3) : ?>
							<div class="text-right">
								<a class="btn btn-success" href="<?= base_url('building_sites/add_zone/' . $data->id) ?>">
									<i class="fa fa-plus"></i>
								</a>
							</div>
							<div class="clearfix"></div>
							<br>
						<?php endif; ?>
						<?php
						if (sizeof($zones) > 0) :
						?>
							<table class="table table-hover" id="data">
								<thead>
									<tr class="">
										<th>#</th>
										<th>Zona</th>
										<th>Acción</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$x = 1;
									foreach ($zones as $entry) :
									?>
										<tr>
											<th scope="row"><?php echo $x ?></th>
											<td><?= $entry->name ?></td>
											<td>
												<?php if ($user->fk_role < 3) : ?>
													<a class="btn btn-primary btn-sm" href="<?= base_url('building_sites/edit_zone/' . $entry->id) ?>">
														<i class="fa fa-pencil"></i>
													</a>
													<a class="btn btn-danger btn-sm" href="<?= base_url('building_sites/remove_zone/' . $entry->id) ?>">
														<i class="fa fa-trash-o"></i>
													</a>
												<?php endif; ?>
											</td>
										</tr>
									<?php
										$x++;
									endforeach;
									?>
								</tbody>
							</table>
						<?php
						else :
						?>
							<p>No hay zonas disponibles para administrar</p>
						<?php
						endif;
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Editar especialidad</h3>
					</div>
					<div class="card-close">
						<div class="dropdown">
							<a href="<?= base_url('building_sites/edit/' . $data->building_site->id) ?>" class="dropdown-item">
								<i class="fa fa-arrow-left"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<?php
						$attr = array(
							'method' =>	'post',
							'class'	=> 'forms-sample'
						);
						echo form_open_multipart('building_sites/edit_speciality/' . $data->id, $attr);
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
			<div class="col-lg-12">
				<div class="row">

				</div>
			</div>

			<div class="clearfix"></div>

			<br>
			<div class="col-xl-6 col-sm-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Lista de Roles de Especialidad</h3>
					</div>
					<div class="card-body">

						<div class="text-right">
							<a class="btn btn-success" href="<?php echo base_url('building_sites/add_speciality_role/' . $data->id) ?>">
								<i class="fa fa-plus"></i>
							</a>
						</div>
						<div class="clearfix"></div>
						<br>
						<?php
						if (sizeof($speciality_roles) > 0) :
						?>
							<table class="table table-hover" id="data">
								<thead>
									<tr class="">
										<th>#</th>
										<th>Rol</th>
										<th>HH acumuladas</th>
										<th>Acción</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($speciality_roles as $entry) :
									?>
										<tr>
											<th scope="row"><?php echo $entry->id ?></th>
											<td><?= $entry->name ?></td>
											<td><?= $entry->hh ?></td>
											<td>
												<a class="btn btn-primary btn-sm" href="<?php echo base_url('building_sites/edit_speciality_role/' . $entry->id) ?>">
													<i class="fa fa-pencil"></i>
												</a>
												<a class="btn btn-danger btn-sm" href="<?php echo base_url('building_sites/remove_speciality_role/' . $entry->id) ?>">
													<i class="fa fa-trash-o"></i>
												</a>
											</td>
										</tr>
									<?php
									endforeach;
									?>
								</tbody>
							</table>
						<?php
						else :
						?>
							<p>No hay roles de especialidad disponibles para administrar</p>
						<?php
						endif;
						?>
					</div>
				</div>
			</div>



			<div class="col-xl-6 col-sm-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Lista de Supervisores</h3>
					</div>
					<div class="card-body">

						<div class="text-right">
							<a class="btn btn-success" href="<?php echo base_url('building_sites/add_supervisor/' . $data->id); ?>">
								<i class="fa fa-plus"></i>
							</a>
						</div>
						<div class="clearfix"></div>
						<br>
						<?php
						// d($data);
						// d($supervisors);
						if (sizeof($supervisors) > 0) :
						?>
							<table class="table table-hover" id="data">
								<thead>
									<tr class="">
										<!-- <th>#</th> -->
										<th>Nombre</th>
										<th>RUT</th>
										<th>Tel&eacute;fono</th>
										<!-- <th>Email</th> -->
										<th>Acción</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($supervisors as $supervisor) :
									?>
										<tr>
											<!-- <th scope="row"><?php //echo $supervisor->id; 
																	?></th> -->
											<td><?php echo $supervisor->user->first_name . " " . $supervisor->user->last_name; ?></td>
											<td><?php echo $supervisor->user->dni; ?></td>
											<td><?php echo $supervisor->user->phone_1; ?></td>
											<!-- <td><?php // echo $supervisor->user->email; 
														?></td> -->
											<td>
												<a class="btn btn-primary btn-sm" href="<?php echo base_url('building_sites/edit_supervisor/' . $supervisor->id) ?>">
													<i class="fa fa-pencil"></i>
												</a>
												<a class="btn btn-danger btn-sm" href="<?php echo base_url('building_sites/remove_supervisor/' . $supervisor->id . '/' . $data->id) ?>">
													<i class="fa fa-trash-o"></i>
												</a>
											</td>
										</tr>
									<?php
									endforeach;
									?>
								</tbody>
							</table>
						<?php
						else :
						?>
							<p>No hay Supervisores disponibles para administrar</p>
						<?php
						endif;
						?>
					</div>
				</div>
			</div>



			<!-- Item -->
			<div class="col-12">

				<div class="card">
					<div class="card-header">
						<span>Curva óptima de trabajo</span>
					</div>
					<div class="card-body">
						<canvas id="role_chart2"></canvas>
					</div>
				</div>

				<?php
				$ndate_list = array();
				$date_list = array();
				$hh_list = array();
				foreach ($data->statistics as $k => $v) {
					$ndate_list[] = gmdate("d-m-Y", $k * 86400);
					$date_list[] = $k;
					$hh_list[] = $v;
				}
				?>
				<?php
				$role_data = array();
				foreach ($data->role_statistics as $k => $role) {
					$role_data[$k] = new stdClass;
					$role_data[$k]->name = $role->name;
					$role_data[$k]->thh = array();
					$role_data[$k]->tnd = array();
					foreach ($role->dates as $date => $hh) {
						$role_data[$k]->thh[] = $hh;
						$role_data[$k]->tnd[] = gmdate("d-m-Y", $date * 86400);
					}
				}
				//d($role_data);
				?>
				<script type="text/javascript">
					var lrnu = [];
					var lrna = [];
					var llb = [];
					<?php
					foreach ($data->role_statistics as $k => $r) :
					?>
						llb[<?= $k ?>] = "<?= $role_data[$k]->name; ?>";
						lrna[<?= $k ?>] = <?= json_encode($role_data[$k]->tnd) ?>;
						lrnu[<?= $k ?>] = <?= json_encode($role_data[$k]->thh) ?>;
					<?php
					endforeach;
					?>
					var ctx = document.getElementById("role_chart2").getContext('2d');
					var rnu = <?= json_encode($hh_list) ?>;
					var rna = <?= json_encode($ndate_list) ?>;
					var internalDataLength = <?= sizeof($data->role_statistics) ?>;
					var graphColors = [];
					var graphOutlines = [];
					var hoverColor = [];
					i = 0;
					while (i <= internalDataLength) {
						var randomR = Math.floor((Math.random() * 130) + 100);
						var randomG = Math.floor((Math.random() * 130) + 100);
						var randomB = Math.floor((Math.random() * 130) + 100);
						var graphColor = "rgb(" +
							randomR + ", " +
							randomG + ", " +
							randomB + ")";
						graphColors.push(graphColor);
						i++;
					};
					data = {
						datasets: [{
								data: rnu,
								//backgroundColor: graphColors,
								//hoverBackgroundColor: hoverColor,
								borderColor: graphColors[0],
								label: 'HH total óptimo diario'
							}
							<?php
							$i = 1;
							foreach ($data->role_statistics as $k => $r) :
							?>, {
									data: lrnu[<?= $k ?>],
									//backgroundColor: graphColors,
									//hoverBackgroundColor: hoverColor,
									borderColor: graphColors[<?= $i++ ?>],
									label: llb[<?= $k ?>]
								}
							<?php
							endforeach;
							?>
						],
						labels: rna
					};
					var options = {
						cutoutPercentage: 25,
					};
					var myChart = new Chart(ctx, {
						type: 'line',
						data: data,
						options: options,
					});
				</script>
			</div>
		</div>
	</div>
</section>
<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xl-6 col-sm-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Editar zona (sub área)</h3>
					</div>
					<div class="card-close">
						<a href="<?= base_url('building_sites/edit_area/' . $data->area->id) ?>" class="dropdown-item">
							<i class="fa fa-arrow-left"></i>
						</a>
					</div>
					<div class="card-body">
						<?php
						$attr = array(
							'method' =>	'post',
							'class'	=> 'forms-sample'
						);
						echo form_open_multipart('building_sites/edit_zone/' . $data->id, $attr);
						?>
						<div class="form-group row">
							<label class="col-sm-4 form-control-label" for="Nombre">Nombre</label>
							<div class="col-sm-8">
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
						</div>
						<div class="form-group row">
							<label class="col-sm-4 form-control-label" for="Especialidad">Obra</label>
							<div class="col-sm-8">
								<?php
								echo $building_sites[$data->fk_building_site];
								echo form_hidden('fk_building_site', $data->fk_building_site);
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
								echo form_submit('update', 'Actualizar', $attr);
								?>
							</div>
						</div>
						<?php echo form_close() ?>
					</div>
				</div>
			</div>
			<!-- Item -->
			<div class="col-xl-6 col-sm-12">
				<div class="card">
					<div class="card-header">
						<span>Curva óptima de trabajo</span>
					</div>
					<div class="card-body">
						<canvas id="role_chart2"></canvas>
					</div>
				</div>
				<script type="text/javascript">
					<?php
					$tndate_list = array();
					$tdate_list = array();
					$thh_list = array();
					foreach ($data->statistics as $k => $v) {
						$tndate_list[] = gmdate("d-m-Y", $k * 86400);
						$tdate_list[] = $k;
						$thh_list[] = $v;
					}
					?>
					var ctx = document.getElementById("role_chart2").getContext('2d');
					var rnu = <?= json_encode($thh_list) ?>;
					var rna = <?= json_encode($tndate_list) ?>;
					var internalDataLength = <?= sizeof($data->statistics) ?>;
					var graphColors = [];
					var graphOutlines = [];
					var hoverColor = [];
					i = 0;
					while (i <= internalDataLength) {
						var randomR = Math.floor((Math.random() * 130) + 100);
						var randomG = Math.floor((Math.random() * 130) + 100);
						var randomB = Math.floor((Math.random() * 130) + 100);
						var graphBackground = "rgb(" +
							randomR + ", " +
							randomG + ", " +
							randomB + ")";
						graphColors.push(graphBackground);
						var graphOutline = "rgb(" +
							(randomR) + ", " +
							(randomG) + ", " +
							(randomB) + ")";
						graphOutlines.push(graphOutline);
						var hoverColors = "rgb(" +
							(randomR + 25) + ", " +
							(randomG + 25) + ", " +
							(randomB + 25) + ")";
						hoverColor.push(hoverColors);
						i++;
					};
					data = {
						datasets: [{
							data: rnu,
							//backgroundColor: graphColors,
							//hoverBackgroundColor: hoverColor,
							borderColor: graphOutlines,
							label: 'HH total óptimo diario'
						}],
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
			<div class="clearfix"></div>
			<br>
		</div>
	</div>
</section>
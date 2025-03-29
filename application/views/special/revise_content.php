<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">

					<div class="card-header">
						<div class="text-left">
							<h3 class="h4">
								Reportes de actividad <?= $data->activity->name ?> ( <?= $data->activity->activity_code ?> )
							</h3>
							<h4>
								<?= $data->activity->speciality_role->name ?>
							</h4>
						</div>
						<div class="text-right">
							Solicitudes pendientes: <?= sizeof( $data->r_activity_data ) ?>
							<a class="btn btn-info" href="<?= base_url('dashboard/request/' . $data->activity->id ) ?>"> <i class="fa fa-plus"></i> </a>
						</div>
					</div>

					<div class="card-body table-responsive">
						<?php if( $user->role->value_p == 'Supervisores' ): ?>

							<table class="table table-hover" id="data">
								<thead>
									<tr>
										<th>#</th>
										<th>Día</th>
										<th>HH</th>
										<th>HH usadas</th>
										<th>Estado</th>
										<th>Acción</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$x = 1;
										foreach( $data->activity_data as $activity_data ):
									?>
									<tr>
										<td><?= $x ?></td>
										<td><?= gmdate( "d-m-Y", $activity_data->activity_date * 86400 ) ?></td> 
										<td><?= $activity_data->hh ?></td> 
										<td><?= $activity_data->uhh ?></td> 
										<td><?= $activity_data->activity_state->value ?></td> 
										<td>
											<?php if( $activity_data->activity_state->value != "Completo" ): ?>
												<a class="btn btn-info" href="<?= base_url('dashboard/check/' . $activity_data->id ) ?>"> <i class="fa fa-edit"></i> </a>
											<?php endif; ?>
										</td>
									</tr>
									<?php
										$x++;
										endforeach; 
									?>
								</tbody>
							</table>

						<?php endif; ?>

						<?php if( $user->role->value_p == 'Administradores de Contratos' ): ?>

							<table class="table table-hover" id="data">
								<thead>
									<tr>
										<th>#</th>
										<th>Día</th>
										<th>HH</th>
										<th>HH usadas</th>
										<th>Estado</th>
										<th>Acción</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$x = 1;
										foreach( $data->activity_data as $activity_data ):
									?>
									<tr>
										<td><?= $x ?></td>
										<td><?= gmdate( "d-m-Y", $activity_data->activity_date * 86400 ) ?></td> 
										<td><?= $activity_data->hh ?></td> 
										<td><?= $activity_data->uhh ?></td> 
										<td><?= $activity_data->activity_state->value ?></td> 
										<td>
											<?php if( $activity_data->activity_state->value != "Completo" ): ?>
												<a class="btn btn-info" href="<?= base_url('dashboard/check/' . $activity_data->id ) ?>"> <i class="fa fa-edit"></i> </a>
											<?php endif; ?>
										</td>
									</tr>
									<?php
										$x++;
										endforeach; 
									?>
								</tbody>
							</table>

						<?php endif; ?>

					</div>

				</div>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	$(document).ready(function(){
		$('#data').DataTable({
			"language": {
				"sProcessing":     "Procesando...",
				"sLengthMenu":     "Mostrar _MENU_ registros",
				"sZeroRecords":    "No se encontraron resultados",
				"sEmptyTable":     "Ningún dato disponible en esta tabla",
				"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
				"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
				"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
				"sInfoPostFix":    "",
				"sSearch":         "Buscar:",
				"sUrl":            "",
				"sInfoThousands":  ",",
				"sLoadingRecords": "Cargando...",
				"oPaginate": {
					"sFirst":    "Primero",
					"sLast":     "Último",
					"sNext":     "Siguiente",
					"sPrevious": "Anterior"
				},
				"oAria": {
					"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
					"sSortDescending": ": Activar para ordenar la columna de manera descendente"
				}
			},
		});
	});
</script>
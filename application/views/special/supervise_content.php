<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">           
						<h2 class="h3">Actividades en </h2>
					</div>
					<div class="card-body table-responsive">
						<?php if( $user->role->value_p == 'Supervisores' ): ?>

							<table class="table table-hover" id="data">
								<thead>
									<tr>
										<th>#</th>
										<th>Zona</th>
										<th>Rol</th>
										<th>Actividad</th>
										<th>Código</th>
										<th>Acción</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$x = 1;
										foreach( $data as $actividad ):
									?>
									<tr>
										<td><?= $x ?></td>
										<td><?= $actividad->zone->name ?></td> 
										<td><?= $actividad->speciality_role->name ?></td> 
										<td><?= $actividad->name ?></td> 
										<td><?= $actividad->activity_code ?></td> 
										<td>
											<a class="btn btn-info" href="<?= base_url('dashboard/revise/' . $actividad->id ) ?>"> <i class="fa fa-edit"></i> </a>
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
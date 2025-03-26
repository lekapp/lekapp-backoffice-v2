<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Lista de Reportes Diarios</h3>
					</div>
					<div class="card-close">
						<a href="<?= base_url('dashboard') ?>" class="dropdown-item">
							<i class="fa fa-arrow-left"></i>
						</a>
					</div>
					<div class="card-body">
						<div class="text-right">
							<a class="btn btn-success" href="<?php echo base_url('building_sites/report_add/' . $user->building_site_id) ?>">
								<i class="fa fa-plus"></i>
							</a>
						</div>
						<div class="clearfix"></div>
						<br>
						<?php
						if (sizeof($data) > 0) :
						?>
							<table class="table table-hover" id="data">
								<thead>
									<tr class="">
										<th>#</th>
										<th>Nº Informe</th>
										<th>Fecha actividad</th>
										<th>Acción</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($data as $entry) :
									?>
										<tr>
											<th scope="row"><?php echo $entry->id ?></th>
											<td><?= $entry->report_no ?></td>
											<td>
												<?php
												$dt = new DateTime($entry->activity_date, new DateTimeZone('America/Santiago'));
												echo $dt->format('d-m-Y');
												?>
											</td>
											<td>
												<?php if ($entry->images != "0") : ?>
													<a class="btn btn-info btn-sm" href="<?php echo base_url('building_sites/report_view/' . $entry->id) ?>">
														<i class="fa fa-eye"></i>
													</a>
													<a class="btn btn-secondary btn-sm" href="<?php echo base_url('building_sites/report_view_pdf/' . $entry->id) ?>" target="_blank">
														<i class="fa fa-print"></i>
													</a>
												<?php else : ?>
													<a class="btn btn-warning btn-sm" href="<?php echo base_url('building_sites/report_gallery/' . $entry->id) ?>">
														<i class="fa fa-edit"></i>
													</a>
												<?php endif; ?>
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
							<p>No hay reportes disponibles para administrar</p>
						<?php
						endif;
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript">
	$(document).ready(function() {
		$('#data').DataTable({
			"language": {
				"sProcessing": "Procesando...",
				"sLengthMenu": "Mostrar _MENU_ registros",
				"sZeroRecords": "No se encontraron resultados",
				"sEmptyTable": "Ningún dato disponible en esta tabla",
				"sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
				"sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
				"sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
				"sInfoPostFix": "",
				"sSearch": "Buscar:",
				"sUrl": "",
				"sInfoThousands": ",",
				"sLoadingRecords": "Cargando...",
				"oPaginate": {
					"sFirst": "Primero",
					"sLast": "Último",
					"sNext": "Siguiente",
					"sPrevious": "Anterior"
				},
				"oAria": {
					"sSortAscending": ": Activar para ordenar la columna de manera ascendente",
					"sSortDescending": ": Activar para ordenar la columna de manera descendente"
				}
			}
		});
	});
</script>
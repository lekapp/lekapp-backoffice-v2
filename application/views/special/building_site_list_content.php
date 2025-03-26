<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Lista de Obras</h3>
					</div>
					<div class="card-close">
						<a href="<?= base_url('dashboard') ?>" class="dropdown-item"> 
							<i class="fa fa-arrow-left"></i> 
						</a>
					</div>
					<div class="card-body">
						
						<?php if( $user->fk_role < 3 ): ?>

						<div class="text-right">
							<a class="btn btn-success" href="<?php echo base_url( 'building_sites/add' ) ?>">
								<i class="fa fa-plus"></i>
							</a>
						</div>
						<div class="clearfix"></div>
						<br>

						<?php endif; ?>

						<?php 
						if( sizeof($data) > 0 ):
							?>
							<table class="table table-hover" id="data">
								<thead>
									<tr class="">
										<th>#</th>
										<th>Nombre</th>
										<th>Código</th>
										<th>Cliente</th>
										<th>Mandante</th>
										<th>Acción</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									foreach($data as $entry):
										?>
										<tr>
											<th scope="row"><?php echo $entry->id ?></th>
											<td><?= $entry->name ?></td>
											<td><?= $entry->code ?></td>
											<td>
												<a href="<?= base_url('users/view/' . $entry->client->id ) ?>" target="_blank"> 
													<?= $entry->client->first_name ?> <?= $entry->client->last_name ?>
												</a>
											</td>
											<td>
												<a href="<?= base_url('users/view/' . $entry->contractor->id ) ?>" target="_blank"> 
													<?= $entry->contractor->first_name ?> <?= $entry->contractor->last_name ?>
												</a>
											</td>
											<td>
												<a class="btn btn-primary btn-sm" href="<?php echo base_url( 'building_sites/edit/' . $entry->id ) ?>">
													<i class="fa fa-pencil"></i>
												</a>
												<button class="btn btn-danger btn-sm confirm-button-form" data-bid="<?php echo $entry->id ?>">
													<i class="fa fa-trash-o"></i>
												</button>
											</td>
										</tr>
										<?php 
									endforeach;
									?>
								</tbody>
							</table>
							<?php 
						else:
							?>
							<p>No hay obras disponibles para administrar</p>
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
	
	$(document).ready(function(){
		$('.confirm-button-form').on('click', function () {
			var base_url = "<?php echo base_url( 'building_sites/remove/' ); ?>";
			var btn_id = $(this).data('bid');
			swal({
				title: '¿Quieres continuar?',
				text: '¡No podrás revertir esta operación!',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				cancelButtonText: 'Cancelar',
				confirmButtonText: 'Borrar'
			  }).then(function (e) {
			  	if( e.value == true ){
			  		swal({
						//position: 'top-end',
						type: 'success',
						title: '¡Borrado!',
						text: '¡El formulario ha sido borrado!',
						showConfirmButton: true,
					}).then(function(){
						window.location.href = base_url + btn_id;
						//swal('URL', base_url + btn_id, 'success');
					});
			  	} else {
			  		swal({
						type: 'success',
						title: '¡Todo bien!',
						text: 'No ha pasado nada',
						showConfirmButton: true,
					});
			  		//swal('¡Todo bien!', 'No ha pasado nada', 'success');
			  	}
			  }).catch(swal.noop)
		});
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
			}
		});
	});
</script>
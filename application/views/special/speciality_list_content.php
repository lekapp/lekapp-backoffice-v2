<!-- Feeds Section-->

<section class="main">

	<div class="container-fluid">

		<div class="row">

			<div class="col-lg-12">



				<?php if( $this->session->flashdata('alert_msg') != "" ): ?>

					<div class="alert alert-success fade show" role="alert">

						<button type="button" class="close" data-dismiss="alert" aria-label="Close">

							<span aria-hidden="true">&times;</span>

						</button>

						<h4 class="alert-heading">¡Muy bien!</h4>

						<p class="mb-0"><?= $this->session->flashdata('alert_msg') ?></p>

					</div>

				<?php endif; ?>

				

				<div class="card">



					<div class="card-header d-flex align-items-center">

						<h3 class="h4">Lista de Especialidades</h3>

					</div>



					<div class="card-close">

						<a href="<?= base_url('dashboard') ?>" class="dropdown-item"> 

							<i class="fa fa-arrow-left"></i>

						</a>

					</div>



					<div class="card-body">



						<div class="text-right">

							<a class="btn btn-primary" href="<?php echo base_url( 'specialities/add' ) ?>">

								<i class="fa fa-user-plus"></i> Nueva especialidad

							</a>

						</div>



						<div class="clearfix"></div>



						<br>



						<?php 

						if( sizeof($data) > 0 ):

							?>



							<table class="table table-hover" id="data">

								<thead>

									<tr class="">

										<th>#</th>

										<th>Nombre</th>

										<th>Obra</th>

										<th>Acción</th>

									</tr>

								</thead>

								<tbody>



									<?php 

									foreach($data as $entry):

										?>



										<tr>

											<th scope="row"><?php echo $entry->id ?></th>

											<td><?php echo $entry->name ?></td>

											<td><?php echo $entry->building_site->name ?></td>

											<td>

												<a class="btn btn-primary btn-sm" href="<?php echo base_url( 'specialities/edit/' . $entry->id ) ?>">

													<i class="fa fa-pencil-square-o"></i> Editar

												</a>

												<a class="btn btn-danger btn-sm" href="<?php echo base_url( 'specialities/remove/' . $entry->id ) ?>">

													<i class="fa fa-trash-o"></i> Borrar

												</a>

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



							<p>No hay especialidades disponibles para administrar</p>



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
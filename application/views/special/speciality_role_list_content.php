<!-- Feeds Section-->

<section class="main">

	<div class="container-fluid">

		<div class="row">

			<div class="col-lg-12">

				<div class="card">



					<div class="card-header d-flex align-items-center">

						<h3 class="h4">Lista de Roles de Especialidad</h3>

					</div>



					<div class="card-close">

						<a href="<?= base_url('dashboard') ?>" class="dropdown-item"> 

							<i class="fa fa-arrow-left"></i> 

						</a>
					</div>



					<div class="card-body">

						

						<div class="text-right">

							<a class="btn btn-primary" href="<?php echo base_url( 'speciality_roles/add' ) ?>">

								<i class="fa fa-user-plus"></i> Nuevo rol de especialidad

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

										<th>Rol</th>

										<th>Especialidad</th>

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

											<td><?= $entry->name ?></td>

											<td><?= $entry->speciality->name ?></td>

											<td><?= $entry->speciality->building_site->name ?></td>

											<td>

												<a class="btn btn-primary btn-sm" href="<?php echo base_url( 'speciality_roles/edit/' . $entry->id ) ?>">

													<i class="fa fa-pencil-square-o"></i> Editar

												</a>

												<a class="btn btn-danger btn-sm" href="<?php echo base_url( 'speciality_roles/remove/' . $entry->id ) ?>">

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



							<p>No hay roles de especialidad disponibles para administrar</p>



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
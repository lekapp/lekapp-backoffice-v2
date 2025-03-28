<!-- Feeds Section-->

<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">

					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Editar rol de especialidad</h3>
					</div>

					<div class="card-close">
						<a href="<?= base_url('building_sites/edit_speciality/' . $data->speciality->id) ?>" class="dropdown-item">
							<i class="fa fa-arrow-left"></i>
						</a>
					</div>

					<div class="card-body">
						<?php
						$attr = array(
							'method' =>	'post',
							'class'	=> 'form-horizontal'
						);
						echo form_open_multipart('building_sites/edit_speciality_role/' . $data->id, $attr);
						?>

						<div class="form-group row">

							<label class="col-sm-2 form-control-label" for="Nombre">Rol Especialidad</label>

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

							<label class="col-sm-2 form-control-label" for="Nombre">HH acumuladas</label>

							<div class="col-sm-4">
								<?php
								$attr = array(
									'placeholder'	=>	'HH',
									'class'			=>	'form-control p_input',
									'name'			=>	'hh',
									'value'			=>	$data->hh
								);
								echo form_input($attr);
								echo form_error('hh');
								?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-2 form-control-label" for="Rol">Rol</label>
							<div class="col-sm-4">
								<?php
								$attr = "class='form-control p_input' disabled='disabled'";
								echo form_dropdown('fk_speciality', $specialities, $data->fk_speciality, $attr);
								echo form_hidden('fk_speciality', $data->fk_speciality);
								echo form_error('fk_speciality');
								?>
							</div>
						</div>

						<div class="line"></div>

						<div class="form-group row">
							<div class="col-sm-5 offset-sm-2">
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
		</div>
	</div>
</section>
 
<script type="text/javascript">
	$(document).ready(function() {

		$('#data1').DataTable({

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
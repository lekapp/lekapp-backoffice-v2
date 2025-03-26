<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
						<h2 class="h3">App </h2>
					</div>
					<div class="card-body">

						<div class="col-md-12 mb-2">
							<?= $user->first_name ?> <?= $user->last_name ?>
						</div>

						<?php
						switch ($user->extra['action']):
							case 'specialities':
						?>
								<?php foreach ($user->supervisor as $v) : ?>
									<div class="col-md-12 mb-2">
										<a href="<?= base_url('dashboard/search_activities/' . $v->speciality->id) ?>" class="btn btn-success"><?= $v->speciality->name ?></a>
									</div>
								<?php endforeach; ?>

								<?php
								break;
								?>

							<?php
							case 'activities':
							?>

								Actividades de <?= $user->speciality ?> <br><br>

								<?php
								if (sizeof($user->data) > 0) :
								?>

									<div class="table-responsive">
										<table class="table table-hover" id="data">
											<thead>
												<tr class="">
													<th>#</th>
													<th>Código</th>
													<th>Actividad</th>
													<th>Área</th>
													<!--<th>Rol</th>-->
													<th>Acción</th>
												</tr>
											</thead>
											<tbody>

												<?php
												$x = 1;
												foreach ($user->data as $entry) :
												?>

													<tr>
														<th scope="row"><?php echo $x ?></th>
														<td><?php echo $entry->code ?></td>
														<td><?php echo $entry->name ?></td>
														<td><?php echo $entry->zone ?></td>
														<!--<td><?php echo $entry->speciality_role->name ?></td>-->
														<td>
															<a class="btn btn-primary btn-sm" href="<?php echo base_url('dashboard/set/' . base64_encode($entry->speciality_role->building_site->id . '-' . $entry->code)) ?>">
																<i class="fa fa-plus"></i>
															</a>
														</td>
													</tr>

												<?php
													$x++;
												endforeach;
												?>

											</tbody>
										</table>
									</div>

								<?php
								else :
								?>

									<p>No hay especialidades disponibles para administrar</p>

								<?php
								endif;
								?>

								<?php
								break;
								?>

							<?php
							case 'set':
							?>

								<?php
								$attr = array(
									'method' =>	'post',
									'class'	=> 'form-horizontal'
								);
								echo form_open_multipart('dashboard/set/' . $user->extra['code'], $attr);
								?>

								<?php if ($exists) : ?>

									<div class="form-group row">
										<div class="col-md-12">
											<h2>
												Insertar registro de actividad <?= ($user->code) ?>: <?= $user->activity_name ?> <br><br>
											</h2>
										</div>
									</div>

									<div class="form-group row">

										<label class="col-sm-2 form-control-label" for="">Zona</label>
										<div class="col-sm-4">
											<?php
											$attr = "class='form-control p_input'";
											echo form_dropdown('fk_zone', $user->zones, 0, $attr);
											echo form_error('fk_zone');
											?>
										</div>

										<label class="col-sm-2 form-control-label" for="">Roles</label>
										<div class="col-sm-4">
											<?php
											$attr = "class='form-control p_input'";
											echo form_dropdown('fk_speciality_role', $user->speciality_roles, 0, $attr);
											echo form_error('fk_speciality_role');
											?>
										</div>

									</div>

									<div class="form-group row">

										<label class="col-sm-2 form-control-label" for="">¿Cuántos <?= $user->data[0]->activity->unt ?> de avance del total estimado?</label>
										<div class="col-sm-4">
											<?php
											$attr = array(
												'placeholder'	=>	"Avance ({$user->data[0]->activity->unt})",
												'class'			=>	'form-control p_input',
												'name'			=>	'avance',
												'value'			=>	set_value('avance'),
												'type'			=>	'number',
												'min'			=>	'0',
												'step'			=>	'0.001'
											);
											echo form_input($attr);
											echo form_error('avance');
											?>
										</div>

									</div>

									<div class="form-group row">

										<label class="col-sm-2 form-control-label"> Notas</label>
										<div class="col-sm-4">
											<?php
											$attr = array(
												'placeholder'	=>	'...',
												'class'			=>	'form-control p_input',
												'name'			=>	'comment',
												'value'			=>	set_value('comment'),
												'rows'			=>	4
											);
											echo form_textarea($attr);
											echo form_error('comment');
											?>
										</div>

										<label class="col-sm-2 form-control-label"> Maquinarias</label>
										<div class="col-sm-4">
											<?php
											$attr = array(
												'placeholder'	=>	'...',
												'class'			=>	'form-control p_input',
												'name'			=>	'machinery',
												'value'			=>	set_value('machinery'),
												'rows'			=>	4
											);
											echo form_textarea($attr);
											echo form_error('machinery');
											?>
										</div>

									</div>

									<div class="form-group row">

										<label class="col-sm-2 form-control-label" for="Avatar">Foto</label>
										<div class="col-sm-4">
											<?php
											$attr = array(
												'class'			=>	'form-control p_input',
												'name'			=>	'userfile',
												'accept'		=>	'image/*;capture=camera'
											);
											echo form_upload($attr);
											echo form_error('userfile');
											?>
										</div>
									</div>

									<?php
									if (sizeof($user->data) > 0) :
									?>
										<div class="form-group row">

											<div class="col-sm-12 col-md-4 offset-md-4">
												<img width="100%" src="<?= base_url('assets/cache/qr/' . $user->file) ?>" />
											</div>
										</div>
									<?php
									endif;
									?>

									<div class="clearfix"></div>

									<div class="form-group row">
										<div class="col-md-12 text-right">
											<?php
											$attr = array(
												'class'	=>	'btn btn-primary'
											);
											echo form_submit('add', 'Registrar y cerrar actividad', $attr);
											?>
										</div>
									</div>

								<?php else : ?>

									<?php if ($valid) : ?>

										<div class="form-group row">
											<div class="col-md-12">
												<p>
													<strong>Actividad válida, pero aún no abierta el día de hoy</strong>. Para comenzar la actividad y desplegar el código de asistencia de trabajadores, presione el siguiente botón:
												</p>
											</div>
										</div>

										<div class="form-group row">
											<div class="col-md-4">
												<?php
												$attr = array(
													'class'	=>	'btn btn-primary'
												);
												echo form_submit('start', 'Iniciar actividad', $attr);
												?>
											</div>
										</div>

									<?php else : ?>

										<div class="form-group row">
											<div class="col-md-12">
												<p>
													<strong>Actividad inválida</strong>. El código no existe para la zona y/o especialidad designada. Puede volver a la ventana anterior <a href="<?= base_url("dashboard/search_activities/" . $speciality_id) ?>">acá</a>.
												</p>
											</div>
										</div>

									<?php endif; ?>

								<?php endif; ?>

								<?php echo form_close() ?>

								<hr>

								<?php
								if (sizeof($user->data) > 0) :
								?>

									<div class="table-responsive">
										<table class="table table-hover" id="data">
											<thead>
												<tr class="">
													<th>#</th>
													<th>Rol</th>
													<th>HH</th>
													<th>Avance (De <?= $user->data[0]->activity->qty ?> <?= $user->data[0]->activity->unt ?>)</th>
													<th>Fecha</th>
													<th>Detalles</th>
													<th>Foto</th>
												</tr>
											</thead>
											<tbody>

												<?php
												$x = 1;
												foreach ($user->data as $entry) :
												?>

													<tr>
														<th scope="row"><?php echo $x ?></th>
														<td><?php echo $entry->speciality_role->name ?></td>
														<td><?php echo $entry->hh ?></td>
														<td><?php echo $entry->p_avance ?></td>
														<td>
															<?php
															$dt = new DateTime($entry->activity_date, new DateTimeZone('America/Santiago'));
															echo $dt->format('d-m-Y');
															?>
														</td>
														<td>

															<!-- Button to Open the Modal -->
															<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal<?= $entry->id ?>">
																<i class="fa fa-eye"></i>
															</button>

															<!-- The Modal -->
															<div class="modal fade" id="myModal<?= $entry->id ?>">
																<div class="modal-dialog modal-lg">
																	<div class="modal-content">

																		<!-- Modal Header -->
																		<div class="modal-header">
																			<h4 class="modal-title">Detalles <?= $entry->activity->name ?> durante <?php $dt = new DateTime($entry->activity_date, new DateTimeZone('America/Santiago'));
																																					echo $dt->format('d-m-Y') ?></h4>
																			<button type="button" class="close" data-dismiss="modal">&times;</button>
																		</div>

																		<!-- Modal body -->
																		<div class="modal-body">

																			<h3 class="col-md-4">
																				Rol
																			</h3>
																			<p class="col-md-8">
																				<?= $entry->speciality_role->name ?>
																			</p>

																			<h3 class="col-md-4">
																				Notas
																			</h3>
																			<p class="col-md-8">
																				<?= $entry->comment ?>
																			</p>

																			<h3 class="col-md-4">
																				Maquinarias
																			</h3>
																			<p class="col-md-8">
																				<?= $entry->machinery ?>
																			</p>

																			<h3 class="col-md-4">
																				Avance (<?= $user->data[0]->activity->unt ?>)
																			</h3>
																			<p class="col-md-8">
																				<?= $entry->p_avance ?>
																			</p>


																		</div>

																		<!-- Modal footer -->
																		<div class="modal-footer">
																			<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
																		</div>

																	</div>
																</div>
															</div>

														</td>
														<td>
															<!--
													<a class="btn btn-primary btn-sm" href="<?php echo base_url('dashboard/set/' . base64_encode($entry->code)) ?>">
														<i class="fa fa-plus"></i>
													</a>
													-->

															<?php if ($entry->fk_image != 0 && $entry->image->name != "") : ?>
																<a href="<?= asset_img('../uploads/images/activity_report/' . $entry->image->id) . '/' . $entry->image->name . $entry->image->ext ?>"><img src="<?= asset_img('../uploads/images/activity_report/' . $entry->image->id) . '/' . $entry->image->name . $entry->image->ext ?>" width="100$" /></a>
															<?php endif; ?>
														</td>

													</tr>

												<?php
													$x++;
												endforeach;
												?>

											</tbody>
										</table>
									</div>

								<?php
								endif;
								?>

								<?php
								break;
								?>

						<?php
						endswitch;
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
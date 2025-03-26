<!-- Feeds Section-->
<section class="main">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">

				<?php if ($this->session->flashdata('alert_msg') != "") : ?>
					<div class="alert alert-success fade show" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="alert-heading">¡Muy bien!</h4>
						<p class="mb-0"><?= $this->session->flashdata('alert_msg') ?></p>
					</div>
				<?php endif; ?>

				<div class="card">

					<div class="card-close">
						<div class="dropdown">
							<button type="button" id="closeCard5" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
							<div aria-labelledby="closeCard5" class="dropdown-menu dropdown-menu-right has-shadow">
								<a href="<?= base_url('users') ?>" class="dropdown-item">
									<i class="fa fa-arrow-left"></i> Volver
								</a>
								<a href="<?= base_url('users/edit/' . $data->id) ?>" class="dropdown-item edit">
									<i class="fa fa-gear"></i> Editar
								</a>
							</div>
						</div>
					</div>

					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Ver usuario</h3>
					</div>

					<div class="card-body">

						<div class="row">
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
								<h5>
									Nombre
								</h5>
								<p>
									<?php
									echo $data->first_name . ' ' . $data->last_name;
									?>
								</p>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
								<h5>
									DNI/RUT
								</h5>
								<p>
									<?php
									echo $data->dni;
									?>
								</p>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
								<h5>
									Email
								</h5>
								<p>
									<?php
									echo $data->email;
									?>
								</p>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
								<h5>
									Rol
								</h5>
								<p>
									<?php
									echo $data->role->name;
									?>
								</p>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
								<h5>
									Teléfono 1
								</h5>
								<p>
									<?php
									echo $data->phone_1;
									?>
								</p>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
								<h5>
									Teléfono 2
								</h5>
								<p>
									<?php
									echo $data->phone_2;
									?>
								</p>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
								<h5>
									Dirección 1
								</h5>
								<p>
									<?php
									echo $data->address_1;
									?>
								</p>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
								<h5>
									Dirección 2
								</h5>
								<p>
									<?php
									echo $data->address_2;
									?>
								</p>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
								<h5>
									Avatar
								</h5>
								<p>
									<img class="col-xl-4 col-lg-6 col-md-6 col-sm-12" src="<?php echo asset_img($data->avatar_url) ?>" />
								</p>
							</div>



						</div>



					</div>

				</div>

			</div>
		</div>
	</div>
	</div>
</section>
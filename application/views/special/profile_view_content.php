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

					<div class="card-close">
						<div class="dropdown">
							<button type="button" id="closeCard5" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
							<div aria-labelledby="closeCard5" class="dropdown-menu dropdown-menu-right has-shadow">
								<a href="<?= base_url('dashboard') ?>" class="dropdown-item"> 
									<i class="fa fa-arrow-left"></i> Volver
								</a>
								<a href="<?= base_url('profile/edit') ?>" class="dropdown-item edit"> 
									<i class="fa fa-gear"></i> Editar
								</a>
							</div>
						</div>
					</div>

					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Mi Perfil</h3>
					</div>

					<div class="card-body">
						<div class="form-group row">
							<label class="col-sm-3 form-control-label">Nombre</label>
							<div class="col-sm-3">
								<?= $user->first_name . ' ' . $user->last_name ?>
							</div>
							<label class="col-sm-3 form-control-label">DNI/RUT</label>
							<div class="col-sm-3">
								<?= $user->dni ?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 form-control-label">Email</label>
							<div class="col-sm-3">
								<?= $user->email ?>
							</div>
							<label class="col-sm-3 form-control-label">Rol</label>
							<div class="col-sm-3">
								<?= $user->role->name ?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 form-control-label">Teléfono 1</label>
							<div class="col-sm-3">
								<?= $user->phone_1 ?>
							</div>
							<label class="col-sm-3 form-control-label">Teléfono 2</label>
							<div class="col-sm-3">
								<?= $user->phone_2 ?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 form-control-label">Dirección 1</label>
							<div class="col-sm-3">
								<?= $user->address_1 ?>
							</div>
							<label class="col-sm-3 form-control-label">Dirección 2</label>
							<div class="col-sm-3">
								<?= $user->address_2 ?>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</section>
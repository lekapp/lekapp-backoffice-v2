<?php if ($this->session->userdata('logged_in')): ?>



<!-- Sidebar Header-->

<div class="sidebar-header d-flex align-items-center">

    <div class="avatar">
        <img src="<?php /* echo asset_img($user->avatar_url)*/ echo asset_img('4_logo.png') ?>" alt="..."
            class="img-fluid">
    </div>

    <div class="title">

        <h1 class="h4"><?= $user->first_name . ' ' . $user->last_name ?></h1>

        <p><?= $user->role->name ?></p>

    </div>

</div>



<span class="heading">Principal</span>



<ul class="list-unstyled">

    <li>

        <a href="<?= base_url() ?>"> <i class="icon-home"></i>Inicio </a>

    </li>

    <li>

        <a href="<?= base_url('profile') ?>"> <i class="icon-interface-windows"></i>Mi perfil </a>

    </li>

</ul>



<?php if ($user->fk_role < 3): ?>

<span class="heading">Gestión</span>



<ul class="list-unstyled">

    <li>

        <a href="<?= base_url('users') ?>"> <i class="icon-flask"></i>Usuarios </a>

    </li>

</ul>

<?php endif; ?>

<?php if ($user->fk_role < 3): ?>

<span class="heading">Sistema</span>

<ul class="list-unstyled">

    <li>

        <a href="#exampledropdownDropdown1" aria-expanded="false" data-toggle="collapse" class="collapsed"> <i
                class="icon-flask"></i>Obras </a>

        <ul id="exampledropdownDropdown1" class="list-unstyled collapse" style="">

            <li><a href="<?= base_url('building_sites') ?>">Administrar</a></li>

        </ul>

    </li>

</ul>

<?php else: ?>

<span class="heading">Sistema</span>

<ul class="list-unstyled">

    <li>

        <a href="#exampledropdownDropdown1" aria-expanded="false" data-toggle="collapse" class="collapsed"> <i
                class="icon-flask"></i>Obras </a>

        <ul id="exampledropdownDropdown1" class="list-unstyled collapse" style="">

            <li><a href="<?= base_url('building_sites') ?>">Revisar</a></li>

        </ul>

    </li>

</ul>

<?php endif; ?>



<?php else: ?>



<!-- Sidebar Header-->

<div class="sidebar-header d-flex align-items-center">

    <div class="avatar"><img src="<?php echo asset_img($user->avatar_url) ?>" alt="..."
            class="img-fluid rounded-circle"></div>

    <div class="title">

        <h1 class="h4"><?= $user->first_name . ' ' . $user->last_name ?></h1>

        <p><?= $user->role->value_p ?></p>

    </div>

</div>



<?php endif; ?>
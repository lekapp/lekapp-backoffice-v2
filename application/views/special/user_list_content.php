<!-- Feeds Section-->
<section class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="h4">Lista de Usuarios</h3>
                    </div>
                    <div class="card-close">
                        <a href="<?= base_url('dashboard') ?>" class="dropdown-item">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="card-body">

                        <div class="text-right">
                            <a class="btn btn-success" href="<?php echo base_url('users/add') ?>">
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
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
									foreach ($data as $entry) :
									?>
                                <tr>
                                    <th scope="row"><?php echo $entry->id ?></th>
                                    <td><?php echo $entry->first_name ?> <?php echo $entry->last_name ?></td>
                                    <td><?php echo $entry->email ?></td>
                                    <td><?php echo $entry->role->name ?></td>
                                    <td>
                                        <a class="btn btn-secondary btn-sm"
                                            href="<?php echo base_url('users/edit/' . $entry->id) ?>">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn btn-primary btn-sm"
                                            href="<?php echo base_url('users/view/' . $entry->id) ?>">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm confirm-button-form"
                                            data-bid="<?php echo $entry->id ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
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
                        <p>No hay usuarios disponibles para administrar</p>
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
    $('.confirm-button-form').on('click', function() {
        var base_url = "<?php echo base_url('users/remove/'); ?>";
        var baseCheck_url = "<?php echo base_url('users/checkUser/'); ?>";
        var btn_id = $(this).data('bid');
        swal({
            title: '¿Quieres continuar?',
            text: '¡Borrarás un usuario!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Borrar'
        }).then(function(e) {
            /*
            if (e.value == true) {
                swal({
                    //position: 'top-end',
                    type: 'success',
                    title: '¡Borrado!',
                    text: '¡El usuario ha sido borrado!',
                    showConfirmButton: true,
                }).then(function() {
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
			*/
            $.ajax({
                url: baseCheck_url + btn_id,
                type: 'POST',
                dataType: 'json',
                data: {
                    bid: btn_id
                },
                success: function(data) {
                    if (data.status == 'success') {
                        swal({
                            //position: 'top-end',
                            type: 'success',
                            title: '¡Borrado!',
                            text: '¡El usuario ha sido borrado!',
                            showConfirmButton: true,
                        }).then(function() {
                            window.location.href = base_url + btn_id;
                        });
                    } else {
                        swal({
                            type: 'error',
                            title: '¡El usuario no puede ser borrado!',
                            text: data.message,
                            showConfirmButton: true,
                        });
                    }
                }
            })

        }).catch(swal.noop)
    });
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
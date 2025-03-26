<!-- Feeds Section-->
<section class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="h4">Lista de Registro de Actividades de la Obra</h3>
                    </div>
                    <div class="card-close">
                        <a href="<?= base_url('building_sites/edit/' . $building_site->id) ?>" class="dropdown-item">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="card-body">

                        <?php
                        $x = 0;
                        if (sizeof($data) > 0):
                            ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="data">
                                <thead>
                                    <tr class="">
                                        <th>Código</th>
                                        <th>Actividad</th>
                                        <th>Especialidad</th>
                                        <!-- <th>Rol</th> -->
                                        <th>Fecha</th>
                                        <th>Fecha (OF)</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($data as $entry):
                                            $x++;
                                            ?>
                                    <tr>
                                        <th><?= $entry->activity->activity_code ?></th>
                                        <td><?= $entry->activity->name ?></td>
                                        <td><?= $entry->activity->speciality->name ?></td>
                                        <!-- <td><?php //echo $entry->activity->speciality_role->name ?></td> -->
                                        <td>
                                            <?php
                                                    $date = new DateTime($entry->activity_date, new DateTimeZone('America/Santiago'));
                                                    echo $date->format('d-m-Y');
                                                    ?>
                                        </td>
                                        <td>
                                            <?php
                                                    $date = new DateTime($entry->activity_date, new DateTimeZone('America/Santiago'));
                                                    echo $date->format('Ymd');
                                                    ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-primary btn-sm"
                                                href="<?php echo base_url('building_sites/edit_activities/' . $entry->id) ?>">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm confirm-button-form"
                                                data-bid="<?php echo $entry->id ?>">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                        endforeach;
                                        ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        else:
                            ?>
                        <p>No hay registros disponibles para administrar</p>
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
        var base_url = "<?php echo base_url('building_sites/remove_activities/'); ?>";
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
        }).then(function(e) {
            if (e.value == true) {
                swal({
                    //position: 'top-end',
                    type: 'success',
                    title: '¡Borrado!',
                    text: '¡La actividad ha sido borrada!',
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
        }).catch(swal.noop)
    });

    //add filter if header is not Acción

    var n = $('#data thead tr')
        .clone(true)
        .addClass('filters')
        .appendTo('#data thead');


    var table = $('#data').DataTable({
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
        },
        orderCellsTop: true,
        fixedHeader: true,
        initComplete: function() {
            var api = this.api();

            // For each column
            api
                .columns()
                .eq(0)
                .each(function(colIdx) {
                    // Set the header cell to contain the input element
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    var title = $(cell).text();
                    var cursorPosition;
                    if (title != 'Acción' && title != '#') {
                        $(cell).html('<input type="text" placeholder="' + title + '" />');
                        // On every keypress in this input
                        $('input', $('.filters th').eq($(api.column(colIdx).header()).index()))
                            .off('keyup change')
                            .on('change', function(e) {
                                // Get the search value
                                $(this).attr('title', $(this).val());
                                var regexr =
                                    '({search})'; //$(this).parents('th').find('select').val();

                                cursorPosition = this.selectionStart;
                                // Search the column for that value
                                api
                                    .column(colIdx)
                                    .search(
                                        this.value != '' ?
                                        regexr.replace('{search}', '(((' + this.value +
                                            ')))') :
                                        '',
                                        this.value != '',
                                        this.value == ''
                                    )
                                    .draw();
                            })
                            .on('keyup', function(e) {
                                e.stopPropagation();

                                $(this).trigger('change');
                                $(this)
                                    .focus()[0]
                                    .setSelectionRange(cursorPosition, cursorPosition);
                            });
                    } else {
                        $(cell).html('');
                    }
                });
        }
    });
});
</script>
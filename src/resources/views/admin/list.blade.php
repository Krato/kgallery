@extends('layouts.default')
@section('styles')
    <link type="text/css" rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link media="screen" type="text/css" rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.dataTables.min.css">
    <link href="//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.0.0/pnotify.min.css" integrity="sha256-6N5jjMWxse9ctjpl9BXZOd811lGA2+MRswGwHpQ9ZaI=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.0.0/pnotify.brighttheme.min.css" integrity="sha256-FOfyh9yXiYNhKvUKFXYZvMQnuHohW8VFaW+0F4wJ5ls=" crossorigin="anonymous" />
    <style>
        .image_thumb{
            height: 140px;
            background-size: cover;
            border: 5px solid #ddd;
        }
    </style>
@stop

@section('content')

    <div class="box container">
        <div class="box-header with-border">
            <div class="box-title m-b-20">
                <h3>{{ trans('kgallery.title') }}</h3>
            </div>
            <a href="{{ route('galleries.create') }}" class="btn btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label"><i class="fa fa-plus"></i> {{ trans('kgallery.add_new') }}</span></a>
            <div class="row col-md-3 pull-right no-margin no-padding">
                <div class="col-xs-12 no-margin no-padding">
                    <input type="text" id="search-table" class="form-control pull-right" placeholder="{{ trans('kgallery.search') }}">
                </div>
            </div>
        </div>
        <div class="box-body">

            <table class="table table-hover demo-table-search" id="gallery-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ trans('kgallery.headers.image') }}</th>
                    <th>{{ trans('kgallery.headers.title') }}</th>
                    <th>{{ trans('kgallery.headers.categories') }}</th>
                    <th>{{ trans('kgallery.headers.options') }}</th>
                </tr>
                </thead>
                <tbody>

                </tbody>

            </table>

        </div>
    </div>
@stop

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.0.0/pnotify.js" integrity="sha256-GAfixIoH2j0mOpCXEYzQsHafULOiY3/pwkbVYTUFZgg=" crossorigin="anonymous"></script>
    <script type="application/javascript">

        table = $('#gallery-table');

        var settings = {
            sDom: "<'table-responsive't><'row'<F ip>>",
            processing: true,
            serverSide: true,
            ajax: '{{ route('galleries-data') }}',
            autoWidth: true,
            columns: [
                {data: 'id', name: 'gallery.id'},
                {data: 'image', name: 'image', orderable: false, searchable: false},
                {data: 'title', name: 'gallery_translations.title'},
                {data: 'categoryList', name: 'gallery_categories_translations.title'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        };

        table.dataTable(settings);
        $('#search-table').keyup(function() {
            table.fnFilter($(this).val());
        });



        // make the delete button work in the first result page
        register_delete_button_action();

        // make the delete button work on subsequent result pages
        $('#gallery-table').on( 'draw.dt',   function () {
            register_delete_button_action();
        } ).dataTable();

        function register_delete_button_action() {
            $("[data-button-type=delete]").unbind('click');
            // CRUD Delete
            // ask for confirmation before deleting an item
            $("[data-button-type=delete]").click(function(e) {
                e.preventDefault();
                var delete_button = $(this);
                var delete_url = $(this).attr('href');


                swal({  title: "{!! trans('kgallery.delete_gal.title') !!}",
                        text: "{!! trans('kgallery.delete_gal.info') !!}",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "",
                        cancelButtonText: "{{ trans('kgallery.delete_gal.cancel') }}",
                        closeOnConfirm: true
                    }, function(isConfirm){
                        if (isConfirm) {

                            $.ajax({
                                url: delete_url,
                                beforeSend: function (request){
                                    request.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                type: 'DELETE',
                                success: function(result) {
                                    // Show an alert with the result
                                    new PNotify({
                                        title: "{{ trans('kgallery.delete_gal.deleted_t') }}",
                                        text: "{{ trans('kgallery.delete_gal.deleted') }}",
                                        type: "info"
                                    });
                                    // delete the row from the table
                                    delete_button.parentsUntil('tr').parent().remove();
                                },
                                error: function(result) {
                                    // Show an alert with the result
                                    new PNotify({
                                        title: "{{ trans('kgallery.delete_gal.cancel') }}",
                                        text: "{{ trans('kgallery.delete_gal.secure') }}",
                                        type: "dark"
                                    });
                                }
                            });

                        } else {

                            new PNotify({
                                title: "{{ trans('kgallery.delete_gal.cancel') }}",
                                text: "{{ trans('kgallery.delete_gal.secure') }}",
                                type: "dark"
                            });

                        }
                    });


            });
        }

    </script>
@stop

@section('custom_css')
    <link type="text/css" rel="stylesheet" href="{{ asset('admin_theme/assets/plugins/jquery-datatable/media/css/jquery.dataTables.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('admin_theme/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css') }}">
    <link media="screen" type="text/css" rel="stylesheet" href="{{ asset('admin_theme/assets/plugins/datatables-responsive/css/datatables.responsive.css') }}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet"/>
    <style>
        .image_thumb{
            height: 140px;
            background-size: cover;
            border: 5px solid #ddd;
        }
    </style>
@stop

@section('content')

    <div class="box">
        <div class="box-header with-border">
            <div class="box-title m-b-20">
                <h3>{{ trans('kgallery.title') }}</h3>
            </div>
            <a href="{{ url('admin/galleries/create') }}" class="btn btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label"><i class="fa fa-plus"></i> {{ trans('kgallery.add_new') }}</span></a>
            <div class="row col-md-3 pull-right no-margin no-padding">
                <div class="col-xs-12 no-margin no-padding">
                    <input type="text" id="search-table" class="form-control pull-right" placeholder="{{ trans('kgallery.search') }}">
                </div>
            </div>
        </div>
        <div class="box--body" >

            <table class="table table-hover demo-table-search" id="tableWithSearch">
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

@section('custom_js')
    <script src="{{ asset('/admin_theme/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/admin_theme/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js') }}" type="text/javascript" ></script>
    <script src="{{ asset('/admin_theme/assets/plugins/datatables-responsive/js/datatables.responsive.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/admin_theme/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/admin_theme/assets/plugins/datatables-responsive/js/lodash.min.js') }}" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script type="application/javascript">
        table = $('#tableWithSearch');
        var settings = {
            sDom: "<'table-responsive't><'row'<F ip>>",
            sPaginationType: "bootstrap",
            processing: true,
            serverSide: true,
            ajax: '{{ url('admin/galleries/data') }}',
            autoWidth: true,
            columns: [
                {data: 'id', name: 'id'},
                {data: 'image', name: 'image'},
                {data: 'title', name: 'title'},
                {data: 'categories', name: 'categories'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        };

        table.dataTable(settings);
        $('#search-table').keyup(function() {
            table.fnFilter($(this).val());
        });



        $(document).on('click', '.trash', function(e){

            e.preventDefault();
            var delete_url = $(this).attr('href');
            var item = $(this).parentsUntil('tr').parent();

            swal({
                title: "{!! trans('kgallery.delete_gal.title') !!}",
                text: "{!! trans('kgallery.delete_gal.info') !!}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{!! trans('kgallery.delete_gal.delete') !!}",
                cancelButtonText: "{!! trans('kgallery.delete_gal.cancel') !!}",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    $.ajax({
                        url: delete_url,
                        beforeSend: function (request){
                            request.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
                        },
                        type: 'DELETE',
                        success: function(result) {
                            console.log("ok");
                            swal("{!! trans('kgallery.delete_gal.deleted_t') !!}", "{!! trans('kgallery.delete_gal.deleted') !!}", "success");
                            item.remove();
                        },
                        error: function(result) {
                            swal("Error!", "{!! trans('kgallery.delete_gal.secure') !!}", "error");
                        }
                    });



                }
            });
        });
    </script>
@stop
@include('admin.layout')
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


    <!-- Modal -->
    <div class="modal fade slide-up disable-scroll" id="createModal" tabindex="-1" role="dialog" aria-labelledby="modalSlideUpLabel" aria-hidden="false">
        <div class="modal-dialog ">
            <div class="modal-content-wrapper">
                <div class="modal-content">
                    <div class="modal-header clearfix text-left">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="pg-close fs-14"></i>
                        </button>
                        <h5>{{ trans('kgallery.categories.add') }}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {!! Form::open(array('url' => 'admin/k_categories', 'method' =>  'POST' )) !!}
                            <div class="col-sm-8">
                                <div class="form-group form-group-default required" aria-required="true">
                                    <input type="text" class="form-control" name="title" value="" required="" aria-required="true">
                                </div>
                            </div>
                            <div lass="col-sm-4">
                                <button class="btn btn-success  m-l-10" type="submit">{{ trans('kgallery.categories.create') }}</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.modal-dialog -->

    <!-- Modal -->
    <div class="modal fade slide-up disable-scroll" id="editModal" tabindex="-1" role="dialog" aria-labelledby="modalSlideUpLabel" aria-hidden="false">
        <div class="modal-dialog ">
            <div class="modal-content-wrapper">
                <div class="modal-content">
                    <div class="modal-header clearfix text-left">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="pg-close fs-14"></i>
                        </button>
                        <h5>{{ trans('kgallery.categories.edit') }}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {!! Form::open(array('url' => 'admin/k_categories', 'method' =>  'PUT' )) !!}
                            <div class="col-sm-8">
                                <div class="form-group form-group-default required" aria-required="true">
                                    <input type="text" class="form-control" name="title" value="" required="" aria-required="true">
                                    <input type="hidden" class="form-control" name="cat_id" value="">
                                </div>
                            </div>
                            <div lass="col-sm-4">
                                <button class="btn btn-success  m-l-10" type="submit">{{ trans('kgallery.categories.update') }}</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.modal-dialog -->

    <div class="box">
        <div class="box-header with-border">
            <div class="box-title m-b-20">
                <h3>{{ trans('kgallery.title') }}</h3>
            </div>
            <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-primary" data-style="zoom-in"><span class="ladda-label"><i class="fa fa-plus"></i> {{ trans('kgallery.categories.add') }}</span></a>
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
                    <th>{{ trans('kgallery.headers.title') }}</th>
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
            ajax: '{{ url('admin/k_categories/data') }}',
            autoWidth: true,
            columns: [
                {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        };

        table.dataTable(settings);
        $('#search-table').keyup(function() {
            table.fnFilter($(this).val());
        });


        $('#editModal').on('shown.bs.modal', function (e) {
            e.preventDefault();
            var categoryRow = $(e.relatedTarget).parentsUntil('tr').parent();
            var categoryId = categoryRow.find('td').eq(0).text();
            var categoryTitle = categoryRow.find('td').eq(1).text();

            $("#editModal").find('input[name=cat_id]').val(categoryId);
            $("#editModal").find('input[name=title]').val(categoryTitle);
        });

        $('#editModal').on('hidden.bs.modal', function (e) {
            $("#editModal").find('input[name=cat_id]').val('');
            $("#editModal").find('input[name=title]').val('');
        });




        $(document).on('click', '.trash', function(e){

            e.preventDefault();
            var delete_url = $(this).attr('href');
            var item = $(this).parentsUntil('tr').parent();

            swal({
                title: "{!! trans('kgallery.delete_cat.title') !!}",
                text: "{!! trans('kgallery.delete_cat.info') !!}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{!! trans('kgallery.delete_cat.delete') !!}",
                cancelButtonText: "{!! trans('kgallery.delete_cat.cancel') !!}",
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
                            swal("{!! trans('kgallery.delete_cat.deleted_t') !!}", "{!! trans('kgallery.delete_cat.deleted') !!}", "success");
                            item.remove();
                        },
                        error: function(result) {
                            swal("Error!", "{!! trans('kgallery.delete_cat.secure') !!}", "error");
                        }
                    });



                }
            });
        });
    </script>
@stop
@include('admin.layout')
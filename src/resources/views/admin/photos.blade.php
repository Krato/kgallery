@extends('layouts.default')
@section('styles')
    <link type="text/css" rel="stylesheet" href="{{ asset('/gallery_assets/vendor/dmuploader/css/uploader.css') }}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <link href="//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet"/>
    <link type="text/css" rel="stylesheet" href="{{ asset('/gallery_assets/css/gallery.css') }}">
    <link href="//cdn.rawgit.com/noelboss/featherlight/1.7.0/release/featherlight.min.css" type="text/css" rel="stylesheet" />
@stop
@section('content')
<div class="container">
    <div class="panel panel-default ">
        <div class="panel-heading with-border">
            <div class="box-title m-b-20">
                <div class="panel-title pull-left">
                    <h3 style="margin: 0;">{{ trans('kgallery.gallery') }}: {{ $gallery->title }}</h3>
                </div>
                
                <div class="pull-right">
                    <a href="{{ route('galleries.index') }}" class="btn btn-info"><i class="fa fa-angle-left mr10"></i> {{ trans('kgallery.title') }}</a>
                </div>
                <div class="clearfix"></div>
            </div>
            
        </div>
        <div class="panel-body" >

            <div id="drag-and-drop-zone" class="uploader col-md-12 m-b-20">
                {!! trans('kgallery.upload.info') !!}}
                <div class="browser">
                    <label>
                        <span>{{ trans('kgallery.upload.button') }}</span>
                        <input type="file" name="files[]" multiple="multiple" title="Click to add Files">
                    </label>
                </div>
                <div class="uploading-files row no-margin">
                    <div id="images-container" class="m-l-20 m-b-5 m-t-20" file-counter="0">
                    </div>
                </div>
            </div>
            <div class="gallery-images full-width p-t-20" style="width:100%">
                @foreach($gallery->photos->chunk(4) as $photos)
                    @foreach($photos as $photo)
                        <div class="col-xs-6 col-md-3 photo-item sortable" id="photo_{{ $photo->id }}">
                            <div class="panel panel-default panel-photo">
                                <div class="panel-image">
                                    <figure class="gallery-photo">
                                        <img data-src="{{ $photo->getUrl() }}" class="panel-image-preview loading-gif lazyload" />
                                        <figcaption>
                                            <ul class="list-unstyled image-tools">
                                                <li><a href="{{ $photo->getUrl() }}"  class="lightbox" rel="gallery"><span class="glyphicon glyphicon-zoom-in fs-24"></span></a></li>
                                                <li><a href="{{ route('delete-photo') }}" data-photoid="{{ $photo->id }}" class="trash"><span class="glyphicon glyphicon-trash fs-24"></span></a></li>
                                                <li><a href="#" class="move"><span class="glyphicon glyphicon-move fs-24"></span></a></li>
                                            </ul>
                                        </figcaption>
                                    </figure>

                                    <label for="toggle-{{ $photo->id }}" class=""></label>
                                </div>
                                <input type="checkbox" id="toggle-{{ $photo->id }}" class="panel-image-toggle hidden">
                                <div class="panel-body tab-block">
                                    <ul class="nav nav-tabs nav-justified ">
                                        @foreach($locales as $locale)
                                            <li class="{{ (App::getLocale() == $locale->iso) ? 'active' : '' }}">
                                                <a data-toggle="tab" href="#{{ $locale->iso }}-{{$photo->id}}">{{ $locale->language }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content">
                                        @foreach($locales as $key => $locale)
                                            <div class="tab-pane {{ (App::getLocale() == $locale->iso) ? 'active' : '' }}" id="{{ $locale->iso }}-{{$photo->id}}">
                                                <h4 data-pk="{{ $photo->id }}" data-lang="{{ $locale->iso }}" class="name">{{ $photo->translate($locale->iso)->name }}</h4>
                                                <p data-pk="{{ $photo->id }}" data-lang="{{ $locale->iso }}" class="description">{{ $photo->translate($locale->iso)->description }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</div>
    <div id="zoomModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-t-25">
                    <img src="//placehold.it/1000x600" id="image-holder" class="img-responsive">
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/2.0.7/lazysizes.min.js" integrity="sha256-5lWpzLGGPdMqO+0DUKHARGGsmfUZTuBZZDDtPmflOEw=" crossorigin="anonymous"></script>
    <script src="{{ asset('/gallery_assets/vendor/jquery.lightbox/js/lightbox/jquery.lightbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/gallery_assets/vendor/dmuploader/js/dmuploader.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/gallery_assets/vendor/dmuploader/js/gallery.js') }}" type="text/javascript"></script>
    <script src="//cdn.rawgit.com/noelboss/featherlight/1.7.0/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
    <script type="application/javascript">

        // $('.lightbox').lightbox();
        $('.lightbox').featherlight({
            targetAttr: 'href',
            type: {image: true}
        });
        var locales = {!! json_encode($locales->toArray()) !!};
        var currentLocale = "{{ App::getLocale() }}";

        function editables(){

            $.fn.editable.defaults.mode = 'inline';
            $.fn.editable.defaults.params = function (params) {
                params._token = "{{ csrf_token() }}";
                return params;
            };

            $('.name').editable({
                type: 'text',
                pk: 1,
                name: 'name',
                url: '{{ route('info-photo') }}',
                ajaxOptions: {
                    type: 'put',
                    dataType: 'json'
                },
                params: function(params) {
                    params.lang = $(this).data('lang');
                    params._token = "{{ csrf_token() }}";
                    return params;
                },
                emptytext: '{{ trans('kgallery.editable.name') }}',
                placeholder: '{{ trans('kgallery.editable.name') }}',
                title: '{{ trans('kgallery.editable.name') }}',
                error: function(response) {
                    if(response.status === 500) {
                        return 'Service unavailable. Please try later.';
                    } else {
                        return 'Error DB';
                    }
                }
            });

            $('.description').editable({
                type: 'textarea',
                name: 'description',
                url: '{{ route('info-photo') }}',
                emptytext: '{{ trans('kgallery.editable.desc') }}',
                placeholder: '{{ trans('kgallery.editable.desc') }}',
                title: '{{ trans('kgallery.editable.desc') }}',
                ajaxOptions: {
                    type: 'put',
                    dataType: 'json'
                },
                error: function(response) {
                    if(response.status === 500) {
                        return 'Service unavailable. Please try later.';
                    } else {
                        return 'Error DB';
                    }
                }
            });

        }

        editables();



        $('#drag-and-drop-zone').dmUploader({
            url: '{{ route('upload-photo') }}',
            beforeSend: {"X-CSRF-TOKEN" : "{{ csrf_token() }}"},
            extraData: {'gallery_id' : {{ $gallery->id }} },
            dataType: 'json',
            allowedTypes: 'image/*',
            onInit: function(){
//                $.gallery.addLog("Init");
            },
            onBeforeUpload: function(id){
//                $.gallery.addLog(id);
            },
            onNewFile: function(id, file){

                $.gallery.addFile('#images-container', id, file);

                /*** Begins Image preview loader ***/
                if (typeof FileReader !== "undefined"){

                    var reader = new FileReader();

                    // Last image added
                    var img = $('#images-container').find('.img-responsive').eq(0);

                    reader.onload = function (e) {
                        img.attr('src', e.target.result);
                    }

                    reader.readAsDataURL(file);

                } else {
                    // Hide/Remove all Images if FileReader isn't supported
                    $('#images-container').find('.img-responsive').remove();
                }
                /*** Ends Image preview loader ***/

            },
            onComplete: function(){

            },
            onUploadProgress: function(id, percent){
                var percentStr = percent + '%';
                $.gallery.updateFileProgress(id, percentStr);
            },
            onUploadSuccess: function(id, data){
                $("#file-"+id).remove();

                $.gallery.addFileUploaded(data.id, data.file, data.delete_url, locales);
                editables();
                $('.lightbox').featherlight({
                    targetAttr: 'href',
                    type: {image: true}
                });
            },
            onUploadError: function(id, message){
//                $.gallery.addLog(message);
            },
            onFileTypeError: function(file){
//                $.gallery.addLog(file);
            },
            onFileSizeError: function(file){
//                $.gallery.addLog(file);
            },
            onFallbackMode: function(message){
//                $.gallery.addLog(message);
            }
        });


        //Sortable
        $( ".gallery-images" ).sortable({
            cursor: "move",
            handle: ".move",
            helper:'clone',
            start: function(e, ui){
                ui.placeholder.html('<div class="col-xs-6 col-md-3 photo-item mh"><div class="panel panel-default"></div></div>');
                console.log(ui.item.height());
               ui.placeholder.height(ui.item.height());
            },
            stop: function( event, ui ) {

                var items = [];
                var x = 0;

                $(".photo-item").each(function() {
                    var valueToPush = { }; // or "var valueToPush = new Object();" which is the same
                    valueToPush["photo"] = $(this).attr('id').substr(6);
                    valueToPush["position"] = x;
                    items.push(valueToPush);
                    x++;
                });


                $.ajax({
                    url: "{{ route('reorder-photo') }}",
                    beforeSend: function (request){
                        request.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    type: 'POST',
                    data: {'ids' : items },
                    success: function(result) {
                        new PNotify({
                            title: "{!! trans('kgallery.reorder.title_ok') !!}",
                            text: "{{ trans('kgallery.reorder.success') }}",
                            type: "info"
                        });
                    },
                    error: function(result) {
                        new PNotify({
                            title: "Error",
                            text: "{{ trans('kgallery.reorder.error') }}",
                            type: "dark"
                        });
                    }
                });

            },
            create: function( event, ui ) {
//                console.log(ui);
            }
        });


        $(document).on('click', '.trash', function(e){

            e.preventDefault();
            var delete_url = $(this).attr('href');
            var item = $(this).parentsUntil('.photo-item').parent();
            var photoId = $(this).data('photoid');

            swal({
                title: "{!! trans('kgallery.popup.title') !!}",
                text: "{!! trans('kgallery.popup.info') !!}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{!! trans('kgallery.popup.delete') !!}",
                cancelButtonText: "{!! trans('kgallery.popup.cancel') !!}",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    $.ajax({
                        url: delete_url,
                        data: {
                            'photo-id': photoId
                        },
                        beforeSend: function (request){
                            request.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
                        },
                        type: 'POST',
                        success: function(result) {
                            swal("{!! trans('kgallery.popup.deleted_t') !!}", "{!! trans('kgallery.popup.deleted') !!}", "success");
                            item.remove();
                        },
                        error: function(result) {
                            swal("Error!", "{!! trans('kgallery.popup.secure') !!}", "error");
                        }
                    });



                }
            });
        });

    </script>
@stop
@section('custom_css')
    <style>
        .w100{
            width:150px;
        }
    </style>
@stop

@section('content')
    @if($action == 'edit')
        <?php $formAction = 'put'; ?>
    @else
        <?php $formAction = 'post'; ?>
    @endif
    {!! Form::open(array('url' => 'admin/galleries', 'method' =>  $formAction )) !!}
    <div class="row">
        <div class="col-sm-10">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title m-b-20">
                        <h4 class="m-l-20">
                            @if($action == 'edit')
                                {{ trans('kgallery.edit') }}
                            @else
                                {{ trans('kgallery.add_new') }}
                            @endif

                        </h4>
                    </div>
                </div>
                <div class="box--body" >
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if($action == 'edit')
                        <div class="col-sm-7">
                            <ul class="nav nav-tabs nav-tabs-fillup">
                                @foreach($locales as $local)
                                    <li class="{{(strpos(App::getLocale, $local->iso )!== false) ? 'active' : '' }}">
                                        <a data-toggle="tab" href="#{{strtolower($local->language)}}">{{ $local->language }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <h5>{{ trans('kgallery.name') }}</h5>
                            <div class="tab-content bg-transparent">
                            @foreach($locales as $local)
                                <div class="tab-pane {{(strpos(LaravelLocalization::getCurrentLocale(), $localeCode )!== false) ? 'active' : '' }}" id="{{strtolower($properties['name'])}}">
                                    <div class="form-group form-group-default required" aria-required="true">
                                        <label>{{ trans('kgallery.gallery_name') }}</label>
                                        <input type="text" class="form-control" name="title-{{ $localeCode }}" value="{{ $gallery->translate($localeCode)->title }}" required="" aria-required="true">
                                        <input type="hidden" class="form-control" name="gallery_id" value="{{ $gallery->id }}">
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <h5>{{ trans('kgallery.categories.title') }}</h5>
                            <div class="form-group">
                                @if(count($categories) > 0)
                                    @foreach ($categories as $category)
                                        <div class="checkbox check-primary inline w100">
                                            <input type="checkbox" id="cat_{{ $category->id }}" name="cat[]" value="{{ $category->id }}" {{ ($galleryCategories->contains($category->id)) ? 'checked' : '' }}>
                                            <label for="cat_{{ $category->id }}">{{ $category->title }}</label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-12 m-t-15">
                            <button class="btn btn-success  m-l-10" type="submit">{{ trans('kgallery.update') }}</button>
                        </div>
                    @else
                        
                        <div class="col-sm-7">
                            <h5>{{ trans('kgallery.name') }}</h5>
                            <ul class="nav nav-tabs nav-tabs-fillup">
                                @foreach($locales as $local)
                                    <li class="{{(strpos(App::getLocale, $local->iso)!== false) ? 'active' : '' }}">
                                        <a data-toggle="tab" href="#{{strtolower($local->language)}}">{{ $local->language }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content bg-transparent">
                            @foreach($locales as $local)
                                <div class="tab-pane {{(strpos(App::getLocale, $local->iso) !== false) ? 'active' : '' }}" id="{{strtolower($local->language)}}">
                                    <div class="form-group form-group-default required" aria-required="true">
                                        <label>{{ trans('kgallery.name_place') }}</label>
                                        <input type="text" class="form-control" name="title-{{ $local->iso }}" required="" aria-required="true">
                                    </div>
                                </div>
                            @endforeach
                            </div>

                        </div>
                        <div class="col-sm-5">
                            <h5>{{ trans('kgallery.categories.title') }}</h5>
                            <div class="form-group">
                            @if(count($categories) > 0)
                                @foreach ($categories as $category)
                                    <div class="checkbox check-primary inline w100">
                                        <input type="checkbox" id="cat_{{ $category->id }}" name="cat[]" value="{{ $category->id }}">
                                        <label for="cat_{{ $category->id }}">{{ $category->title }}</label>
                                    </div>
                                @endforeach
                            @endif
                            </div>
                        </div>
                        <div class="col-sm-12 m-t-15">
                            <button class="btn btn-success  m-l-10" type="submit">{{ trans('kgallery.create') }}</button>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop
@include('admin.layout')
@extends('restaurant.lteLayout.master')

@section('title')
    @lang('messages.edit') @lang('messages.sub_categories')
@endsection

@section('styles')
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> @lang('messages.edit') @lang('messages.sub_categories') </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/restaurant/home') }}">@lang('messages.control_panel')</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ route('sub_categories.index', $sub_category->restaurant_category->id) }}">
                                @lang('messages.sub_categories')
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-8">
                    @include('flash::message')
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">@lang('messages.edit') @lang('messages.sub_categories') </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('sub_categories.update', $sub_category->id) }}"
                            method="post" enctype="multipart/form-data">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>

                            <div class="card-body">
                                @if (Auth::guard('restaurant')->user()->ar == 'true')
                                    <div class="form-group">
                                        <label class="control-label"> @lang('messages.name_ar') </label>
                                        <input name="name_ar" type="text" class="form-control"
                                            value="{{ $sub_category->name_ar }}" placeholder="@lang('messages.name_ar')">
                                        @if ($errors->has('name_ar'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('name_ar') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                @if (Auth::guard('restaurant')->user()->en == 'true')
                                    <div class="form-group">
                                        <label class="control-label"> @lang('messages.name_en') </label>
                                        <input name="name_en" type="text" class="form-control"
                                            value="{{ $sub_category->name_en }}" placeholder="@lang('messages.name_en')">
                                        @if ($errors->has('name_en'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('name_en') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                {{-- image editor --}}
                                <div class="form-group image-editor-preview">
                                    <label for="">{{ trans('messages.photo') }}</label>
                                    <label class="custom-label" data-toggle="tooltip"
                                        title="{{ trans('dashboard.change_image') }}">
                                        <img class="rounded" id="avatar" src="{{ asset($restaurant->image_path) }}"
                                            alt="avatar">
                                        <input type="file" class="sr-only" id="image-uploader" data-product_id=""
                                            name="image" accept="image/*">
                                    </label>

                                    @error('image_name')
                                        <p class="text-center text-danger">{{ $message }}</p>
                                    @enderror
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%
                                        </div>
                                    </div>
                                    <div class="alert text-center" role="alert"></div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

        </div><!-- /.container-fluid -->
    </section>
    @php
        $itemId = $sub_category->id ;
        $imageUploaderUrl = route('restaurant.sub_menu_category.update_image');
    @endphp
    @include('restaurant.products.product_image_modal')
@endsection

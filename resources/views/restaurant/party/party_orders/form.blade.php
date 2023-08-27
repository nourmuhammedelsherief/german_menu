@extends('restaurant.lteLayout.master')

@section('title')
    @lang('messages.' . $action) @lang('dashboard.party')
@endsection

@section('style')
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" /> --}}
    <style>
        .card-header>h3 {
            float: right;
        }

        .list .item {
            position: relative;
            padding: 10px 10px;
            margin: 10px 0;
            border: 1px solid #CCC;
            border-radius: 5px;
            border-bottom: 3px solid #2d2d2d;
        }

        .list .item .delete {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 100;
            cursor: pointer;
        }

        .list .item .delete:hover i {
            color: red;

        }

        .field-options {
            margin: 10px 20px;
        }

        .field-options .list .item {
            border-color: #6f42c1;
            border-bottom: 1px solid;
        }

        .add-option button {
            background-color: #6f42c1;
            border-color: #6f42c1;
        }

        .select2-container {
            width: 100% !important;
        }
    </style>
@endsection


@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> @lang('messages.' . $action) @lang('dashboard.party') </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/restaurant/home') }}">@lang('messages.control_panel')</a>
                        </li>


                        <li class="breadcrumb-item active">
                            <a href="{{ route('restaurant.party-branch.index') }}">
                                @lang('dashboard.parties')
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
                <div class="col-md-12">
                    @include('flash::message')
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">@lang('messages.' . $action) @lang('dashboard.party') </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" id="post-form" action="{{ $action == 'edit' ? route('restaurant.party.update' , $party->id) : route('restaurant.party.store') }}" method="post"
                            enctype="multipart/form-data">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>
                            <div class="card-body">
                                @if ($errors->any())
                                    <p class="alert alert-danger text-center">{{ $errors->first() }}</p>
                                @endif
                                @if($action == 'edit')
                                    @method('put')
                                @endif
                                <div class="row">
                                    {{-- title_ar --}}
                                    <div class="form-group col-md-6">
                                        <label class="control-label"> @lang('dashboard.entry.title_ar') </label>
                                        <input type="text" name="title_ar" class="form-control"
                                            value="{{ $action == 'edit' ? $party->title_ar : old('title_ar') }}">
                                        @if ($errors->has('title_ar'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('title_ar') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    {{-- title_en --}}
                                    <div class="form-group col-md-6">
                                        <label class="control-label"> @lang('dashboard.entry.title_en') </label>
                                        <input type="text" name="title_en" class="form-control"
                                            value="{{ $action == 'edit' ? $party->title_en : old('title_en') }}">
                                        @if ($errors->has('title_en'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('title_en') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    {{-- description_ar --}}
                                    <div class="form-group col-md-6">
                                        <label class="control-label"> @lang('dashboard.entry.description_ar') </label>
                                        <textarea type="text" name="description_ar" class="form-control">{{ $action == 'edit' ? $party->description_ar : old('description_ar') }}</textarea>
                                        @if ($errors->has('description_ar'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('description_ar') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    {{-- description_en --}}
                                    <div class="form-group col-md-6">
                                        <label class="control-label"> @lang('dashboard.entry.description_en') </label>
                                        <textarea type="text" name="description_en" class="form-control">{{ $action == 'edit' ? $party->description_en : old('description_en') }}</textarea>
                                        @if ($errors->has('description_en'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('description_en') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    {{-- branches --}}
                                    <div class="form-group col-md-6">
                                        <label class="control-label"> @lang('dashboard.entry.branch') </label>
                                        <select name="branch_id" id="branch_id" class="form-control select2">
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ ($action == 'edit' and $party->branch_id == $branch->id) ? 'selected' : '' }}>
                                                    {{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('branch_id'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('branch_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    {{-- price --}}
                                    <div class="form-group col-md-6">
                                        <label class="control-label"> @lang('dashboard.entry.price') </label>
                                        <input type="number" step="0.1" name="price" class="form-control"
                                            value="{{ $action == 'edit' ? $party->price : old('price') }}">
                                        @if ($errors->has('price'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('price') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                </div>
                                {{-- end row --}}
                                {{-- dates --}}
                                <div class="card" id="dates">
                                    <div class="card-header">
                                        <h4 class="text-center">{{ trans('dashboard.schedules') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="add-date">
                                            <button type="button"
                                                data-count="{{ $action == 'edit' ? $party->days->count() : 0 }}"
                                                class="btn btn-info"><i class="fas fa-plus"></i>
                                                {{ trans('dashboard.add_date') }}</button>
                                        </div>
                                        <div class="list">
                                            @if ($action == 'create')
                                                <div class="item">
                                                    <span class="delete-date delete" data-id="0"><i
                                                            class="fas fa-times"></i></span>
                                                    <div class="row">
                                                        {{-- date --}}
                                                        <div class="form-group col-md-4">
                                                            <label class="control-label"> @lang('dashboard.entry.date') </label>
                                                            <input type="date" name="dates[0][date]"
                                                                class="form-control" value="">
                                                        </div>
                                                        {{-- time_from --}}
                                                        <div class="form-group col-md-4">
                                                            <label class="control-label"> @lang('dashboard.entry.time_from') </label>
                                                            <input type="time" name="dates[0][time_from]"
                                                                class="form-control" value="{{ old('time_from') }}">

                                                        </div>
                                                        {{-- time_to --}}
                                                        <div class="form-group col-md-4">
                                                            <label class="control-label"> @lang('dashboard.entry.time_to') </label>
                                                            <input type="time" name="dates[0][time_to]"
                                                                class="form-control" value="{{ old('time_to') }}">

                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($action == 'edit')
                                                @foreach ($party->days as $index => $item)
                                                    @php
                                                        $time = $item->periods()->first();
                                                    @endphp
                                                  
                                                    <div class="item">
                                                        <input type="hidden" name="dates[{{ $index }}][id]" value="{{$item->id}}">
                                                        <span class="delete-date delete" data-id="{{ $index }}"><i
                                                                class="fas fa-times"></i></span>
                                                        <div class="row">
                                                            {{-- date --}}
                                                            <div class="form-group col-md-4">
                                                                <label class="control-label"> @lang('dashboard.entry.date') </label>
                                                                <input type="date"
                                                                    name="dates[{{ $index }}][date]"
                                                                    class="form-control" value="{{ $item->date }}">
                                                            </div>
                                                            {{-- time_from --}}
                                                            <div class="form-group col-md-4">
                                                                <label class="control-label"> @lang('dashboard.entry.time_from') </label>
                                                                <input type="time"
                                                                    name="dates[{{ $index }}][time_from]"
                                                                    class="form-control" value="{{ $time->time_from }}">

                                                            </div>
                                                            {{-- time_to --}}
                                                            <div class="form-group col-md-4">
                                                                <label class="control-label"> @lang('dashboard.entry.time_to') </label>
                                                                <input type="time"
                                                                    name="dates[{{ $index }}][time_to]"
                                                                    class="form-control" value="{{ $time->time_to }}">

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- addition --}}
                                <div class="card" id="additions">
                                    <div class="card-header">
                                        <h4 class="text-center">{{ trans('dashboard.additions') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="add-addition">
                                            <button type="button"
                                                data-count="{{ $action == 'edit' ? $party->additions->count() : '0' }}"
                                                class="btn btn-info"><i class="fas fa-plus"></i>
                                                {{ trans('dashboard.add_new') }}</button>
                                        </div>
                                        <div class="list">
                                            @if ($action == 'edit')
                                                @foreach ($party->additions as $index => $item)
                                                 
                                                    <div class="item">
                                                        <input type="hidden" name="additions[{{ $index }}][id]"
                                                        value="{{ $item->id }}">
                                                        <span class="delete-date delete" data-id="{{ $index }}"><i
                                                                class="fas fa-times"></i></span>
                                                        <div class="row">
                                                            {{-- name_ar --}}
                                                            <div class="form-group col-md-4">
                                                                <label class="control-label"> @lang('dashboard.entry.name_ar') </label>
                                                                <input type="text"
                                                                    name="additions[{{ $index }}][name_ar]"
                                                                    class="form-control" required
                                                                    value="{{ $item->name_ar }}">
                                                            </div>
                                                            {{-- name_en --}}
                                                            <div class="form-group col-md-4">
                                                                <label class="control-label"> @lang('dashboard.entry.name_en') </label>
                                                                <input type="text"
                                                                    name="additions[{{ $index }}][name_en]"
                                                                    class="form-control" required
                                                                    value="{{ $item->name_en }}">
                                                            </div>
                                                            {{-- price --}}
                                                            <div class="form-group col-md-4">
                                                                <label class="control-label"> @lang('dashboard.entry.price') </label>
                                                                <input type="number"
                                                                    name="additions[{{ $index }}][price]"
                                                                    step="0.1" class="form-control" required
                                                                    value="{{ $item->price }}">
                                                            </div>

                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <h5 class="text-center">لا يوجد اضافات</h5>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                                {{-- fields --}}
                                <div class="card" id="fields">
                                    <div class="card-header">
                                        <h4 class="text-center">{{ trans('dashboard.fields') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="add-field">
                                            <button type="button"
                                                data-count="{{ $action == 'edit' ? $party->fields->count() : '0' }}"
                                                class="btn btn-info"><i class="fas fa-plus"></i>
                                                {{ trans('dashboard.add_new') }}</button>
                                        </div>
                                        <div class="list">
                                            @if ($action == 'edit')
                                                @foreach ($party->fields as $index => $field)
                                                    <div class="item" data-id="{{ $index }}">
                                                        <span class="delete-date delete" data-id="{{ $index }}"><i
                                                                class="fas fa-times"></i></span>
                                                        <div class="row">
                                                            {{-- type --}}
                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"> @lang('dashboard.entry.name_ar') </label>
                                                                <select type="text"
                                                                    name="fields[{{ $index }}][type]"
                                                                    class="form-control select2 type" required>
                                                                    <option {{ $field->type == 'text' ? 'selected' : '' }}
                                                                        value="text">
                                                                        {{ trans('dashboard._field_type.text') }}</option>
                                                                    <option
                                                                        {{ $field->type == 'checkbox' ? 'selected' : '' }}
                                                                        value="checkbox">
                                                                        {{ trans('dashboard._field_type.checkbox') }}
                                                                    </option>
                                                                    <option
                                                                        {{ $field->type == 'select' ? 'selected' : '' }}
                                                                        value="select">
                                                                        {{ trans('dashboard._field_type.select') }}
                                                                    </option>

                                                                </select>
                                                            </div>
                                                            {{-- name_ar --}}
                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"> @lang('dashboard.entry.name_ar') </label>
                                                                <input type="text"
                                                                    name="fields[{{ $index }}][name_ar]"
                                                                    class="form-control" required
                                                                    value="{{ $field->name_ar }}">
                                                            </div>
                                                            {{-- name_en --}}
                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"> @lang('dashboard.entry.name_en') </label>
                                                                <input type="text"
                                                                    name="fields[{{ $index }}][name_en]"
                                                                    class="form-control" required
                                                                    value="{{ $field->name_en }}">
                                                            </div>
                                                            {{-- is_required --}}
                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"> @lang('dashboard.entry.is_required') </label>
                                                                <select type="text"
                                                                    name="fields[{{ $index }}][is_required]"
                                                                    class="form-control select2" required>
                                                                    <option value="0"
                                                                        {{ $field->is_required == 0 ? 'selected' : '' }}>
                                                                        {{ trans('dashboard.no') }}</option>
                                                                    <option value="1"
                                                                        {{ $field->is_required == 1 ? 'selected' : '' }}>
                                                                        {{ trans('dashboard.yes') }}</option>


                                                                </select>
                                                            </div>

                                                        </div>
                                                        <div
                                                            class="field-options {{ in_array($field->type, ['checkbox', 'select']) ? '' : 'display-none' }}">
                                                            <div class="add-option">
                                                                <button type="button"
                                                                    data-count="{{ $field->options->count() }}"
                                                                    data-field_id="{{ $index }}"
                                                                    class="btn btn-info"><i class="fas fa-plus"></i>
                                                                    {{ trans('dashboard.add_option') }}</button>
                                                            </div>
                                                            <div class="list">
                                                                @foreach ($field->options as $in => $option)
                                                                    <div class="item">
                                                                        <input type="hidden"
                                                                            name="fields[{{ $index }}][options][{{ $in }}][id]"
                                                                            value="{{ $option->id }}">
                                                                        <span class="delete-date delete"
                                                                            data-id="{{ $in }}"><i
                                                                                class="fas fa-times"></i></span>
                                                                        <div class="row">
                                                                            {{-- name_ar --}}
                                                                            <div class="form-group col-md-4">
                                                                                <label class="control-label">
                                                                                    @lang('dashboard.entry.name_ar')
                                                                                </label>
                                                                                <input type="text"
                                                                                    name="fields[{{ $index }}][options][{{ $in }}][name_ar]"
                                                                                    class="form-control" required
                                                                                    value="{{ $option->name_ar }}">
                                                                            </div>
                                                                            {{-- name_en --}}
                                                                            <div class="form-group col-md-4">
                                                                                <label class="control-label">
                                                                                    @lang('dashboard.entry.name_en')
                                                                                </label>
                                                                                <input type="text"
                                                                                    name="fields[{{ $index }}][options][{{ $in }}][name_en]"
                                                                                    class="form-control" required
                                                                                    value="{{ $option->name_en }}">
                                                                            </div>
                                                                            {{-- price --}}
                                                                            <div class="form-group col-md-4">
                                                                                <label class="control-label">
                                                                                    @lang('dashboard.entry.is_default')
                                                                                </label>
                                                                                <select
                                                                                    name="fields[{{ $index }}][options][{{ $in }}][is_default]"
                                                                                    class="form-control select2">
                                                                                    <option
                                                                                        {{ $option->is_default == 1 ? 'selected' : '' }}
                                                                                        value="0">
                                                                                        {{ trans('dashboard.no') }}
                                                                                    </option>
                                                                                    <option
                                                                                        {{ $option->is_default == 1 ? 'selected' : '' }}
                                                                                        value="1">
                                                                                        {{ trans('dashboard.yes') }}
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
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
        // $itemId = $ads->id ;
        $editorRate = [3, 4];
        $imageUploaderUrl = route('restaurant.ads.update_image');
    @endphp
    @include('restaurant.products.product_image_modal')
@endsection
@section('scripts')
    <script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>
    <script>
        $('.select2').select2();
        $(document).ready(function() {
            $('.add-date').on('click', function() {
                var tag = $(this);
                var count = tag.find('button').data('count') + 1;
                var content =
                    '  <div class="item">\
                                                                                                                    <span class="delete-date delete" data-id="' +
                    count +
                    '"><i class="fas fa-times"></i></span>\
                                                                                                                    <div class="row">\
                                                                                                                        {{-- date --}}\
                                                                                                                        <div class="form-group col-md-4">\
                                                                                                                            <label class="control-label"> @lang('dashboard.entry.date') </label>\
                                                                                                                            <input type="date" name="dates[' +
                    count +
                    '][date]" class="form-control"\
                                                                                                                                value="">\
                                                                                                                        </div>\
                                                                                                                        {{-- time_from --}}\
                                                                                                                        <div class="form-group col-md-4">\
                                                                                                                            <label class="control-label"> @lang('dashboard.entry.time_from') </label>\
                                                                                                                            <input type="time" name="dates[' +
                    count +
                    '][time_from]" class="form-control"\
                                                                                                                                value="{{ old('time_from') }}">\
                                                                                                                        </div>\
                                                                                                                        {{-- time_to --}}\
                                                                                                                        <div class="form-group col-md-4">\
                                                                                                                            <label class="control-label"> @lang('dashboard.entry.time_to') </label>\
                                                                                                                            <input type="time" name="dates[' +
                    count + '][time_to]" class="form-control"\
                                                                                                                                value="{{ old('time_to') }}">\
                                                                                                                            \
                                                                                                                        </div>\
                                                                                                                    </div>\
                                                                                                                </div>';
                $('.add-date > button').data('count', count);
                $('#dates .list').append(content);
                $('.select2').select();
            });
            $('.add-addition').on('click', function() {
                var tag = $(this);
                var count = tag.find('button').data('count') + 1;
                var content =
                    '        <div class="item">\
                                                                                                                <span class="delete-date delete" data-id="' +
                    count +
                    '"><i\
                                                                                                                        class="fas fa-times"></i></span>\
                                                                                                                <div class="row">\
                                                                                                                    {{-- name_ar --}}\
                                                                                                                    <div class="form-group col-md-4">\
                                                                                                                        <label class="control-label"> @lang('dashboard.entry.name_ar') </label>\
                                                                                                                        <input type="text" name="additions[' +
                    count +
                    '][name_ar]" class="form-control" required\
                                                                                                                            >\
                                                                                                                    </div>\
                                                                                                                    {{-- name_en --}}\
                                                                                                                    <div class="form-group col-md-4">\
                                                                                                                        <label class="control-label"> @lang('dashboard.entry.name_en') </label>\
                                                                                                                        <input type="text" name="additions[' +
                    count +
                    '][name_en]" class="form-control" required\
                                                                                                                            >\
                                                                                                                    </div>\
                                                                                                                    {{-- price --}}\
                                                                                                                    <div class="form-group col-md-4">\
                                                                                                                        <label class="control-label"> @lang('dashboard.entry.price') </label>\
                                                                                                                        <input  type="number" name="additions[' +
                    count + '][price]"  step="0.1" class="form-control" required\
                                                                                                                            >\
                                                                                                                    </div>\
                                                                                                                \
                                                                                                                </div>\
                                                                                                            </div>';
                $('.add-addition > button').data('count', count);
                $('#additions .list').append(content);
                $('.select2').select();
            });
            $('.add-field').on('click', function() {
                var tag = $(this);
                var count = tag.find('button').data('count') + 1;
                var content = '        <div class="item" data-id="' + count +
                    '">\
                                                                                                <span class="delete-date delete" data-id="' +
                    count +
                    '"><i\
                                                                                                        class="fas fa-times"></i></span>\
                                                                                                <div class="row">\
                                                                                                    {{-- type --}}\
                                                                                                    <div class="form-group col-md-3">\
                                                                                                        <label class="control-label"> @lang('dashboard.entry.name_ar') </label>\
                                                                                                        <select type="text" name="fields[' +
                    count +
                    '][type]"\
                                                                                                            class="form-control select2 type" required>\
                                                                                                            <option value="text">\
                                                                                                                {{ trans('dashboard._field_type.text') }}</option>\
                                                                                                            <option value="checkbox">\
                                                                                                                {{ trans('dashboard._field_type.checkbox') }}</option>\
                                                                                                            <option value="select">\
                                                                                                                {{ trans('dashboard._field_type.select') }}</option>\
                                                                                                        </select>\
                                                                                                    </div>\
                                                                                                    {{-- name_ar --}}\
                                                                                                    <div class="form-group col-md-3">\
                                                                                                        <label class="control-label"> @lang('dashboard.entry.name_ar') </label>\
                                                                                                        <input type="text" name="fields[' +
                    count +
                    '][name_ar]"\
                                                                                                            class="form-control" required>\
                                                                                                    </div>\
                                                                                                    {{-- name_en --}}\
                                                                                                    <div class="form-group col-md-3">\
                                                                                                        <label class="control-label"> @lang('dashboard.entry.name_en') </label>\
                                                                                                        <input type="text" name="fields[' +
                    count +
                    '][name_en]"\
                                                                                                            class="form-control" required>\
                                                                                                    </div>\
                                                                                                    {{-- is_required --}}\
                                                                                                    <div class="form-group col-md-3">\
                                                                                                        <label class="control-label"> @lang('dashboard.entry.is_required') </label>\
                                                                                                        <select type="text" name="fields[' +
                    count +
                    '][is_required]"\
                                                                                                            class="form-control select2" required>\
                                                                                                            <option value="' +
                    count +
                    '">\
                                                                                                                {{ trans('dashboard.no') }}</option>\
                                                                                                            <option value="1">\
                                                                                                                {{ trans('dashboard.yes') }}</option>\
                                                                                                        </select>\
                                                                                                    </div>\
                                                                                                </div>\
                                                                                                <div class="field-options display-none" >\
                                                                                                    <div class="add-option">\
                                                                                                        <button type="button" data-count="0" data-field_id="' +
                    count + '"\
                                                                                                            class="btn btn-info"><i class="fas fa-plus"></i>\
                                                                                                            {{ trans('dashboard.add_option') }}</button>\
                                                                                                    </div>\
                                                                                                    <div class="list">\
                                                                                                    </div>\
                                                                                                </div>\
                                                                                            </div>';
                $('.add-field > button').data('count', count);
                $('#fields .card-body > .list').append(content);
                $('.select2').select();
            });
            $('#fields').on('change', 'select.type', function() {
                var tag = $(this);
                if (tag.val() != 'text') {
                    tag.parent().parent().parent().find('.field-options').fadeIn(300);
                } else {
                    tag.parent().parent().parent().find('.field-options').fadeOut(300);
                }
            });
            $('#fields').on('click', '.field-options .add-option', function() {
                var tag = $(this);
                var count = tag.find('button').data('count') + 1;
                var fieldCount = tag.find('button').data('field_id');
                var content =
                    '      <div class="item">\
                                           <span class="delete-date delete" data-id="' +
                    count +
                    '"><i\
                                                   class="fas fa-times"></i></span>\
                                           <div class="row">\
                                               {{-- name_ar --}}\
                                               <div class="form-group col-md-4">\
                                                   <label class="control-label"> @lang('dashboard.entry.name_ar')\
                                                   </label>\
                                                   <input type="text" name="fields[' +
                    fieldCount +
                    '][options][' + count +
                    '][name_ar]"\
                                                       class="form-control" required>\
                                               </div>\
                                               {{-- name_en --}}\
                                               <div class="form-group col-md-4">\
                                                   <label class="control-label"> @lang('dashboard.entry.name_en')\
                                                   </label>\
                                                   <input type="text" name="fields[' +
                    fieldCount +
                    '][options][' + count +
                    '][name_en]"\
                                                       class="form-control" required>\
                                               </div>\
                                               {{-- price --}}\
                                               <div class="form-group col-md-4">\
                                                   <label class="control-label"> @lang('dashboard.entry.is_default')\
                                                   </label>\
                                                   <select\
                                                       name="fields[' +
                    fieldCount +
                    '][options][' +
                    count + '][is_default]" class="form-control select2">\
                                                       <option value="0">{{ trans('dashboard.no') }}</option>\
                                                       <option value="1">{{ trans('dashboard.yes') }}</option>\
                                                   </select>\
                                               </div>\
                                           </div>\
                                       </div>';
                tag.find('button').data('count', count);
                tag.parent().find('.list').append(content);
                $('.select2').select();
            });
            $('.card').on('click', '.list .item .delete', function() {
                var tag = $(this);
                console.log('delete');
                tag.parent().remove();
            });
        });
    </script>
@endsection

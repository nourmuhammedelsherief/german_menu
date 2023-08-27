@extends('restaurant.lteLayout.master')

@section('title')
    @lang('dashboard.party_settings')
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <!-- Theme style -->
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>@lang('dashboard.party_settings')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/restaurant/home') }}">
                                @lang('messages.control_panel')
                            </a>
                        </li>

                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    @include('flash::message')

    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">
                        <form role="form" id="post-form" action="{{ route('restaurant.party.settings') }}" method="post"
                            enctype="multipart/form-data">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>

                            <div class="card-body">
                                <p class="text-danger">{{ $errors->first() }}</p>
                                <div class="form-group" style="margin-bottom:30px;">

                                    <div id="barcode-svg"
                                        style="width: 240px;
                                height: 274px;
                                margin: auto;
                                padding: 20px;">
                                        <?php $name = $restaurant->name_barcode == null ? $restaurant->name_en : $restaurant->name_barcode; ?>
                                        {!! QrCode::size(200)->generate(route('party.page1', $restaurant->id)) !!}
                                        <div class="description" style="margin-top:10px;">
                                            <img width="20px" height="20px" src="{{ asset('uploads/img/logo.png') }}">

                                            <p class="footer-copyright pb-3 mb-1 pt-0 mt-0 font-13 font-600"
                                                style="    text-align: center;font-size:12px;display:inline; margin-right:5px;">
                                                {{ trans('messages.made_love') }}
                                                <i class="fa fa-heart font-14 color-red1-dark" style="color:#000;"></i>
                                                <a style="color:#000" href="{{ url('/') }}">

                                                    {{ trans('messages.at_easy_menu') }}
                                                </a>
                                            </p>
                                        </div>
                                    </div>

                                    <h3 class="text-center" style="margin-top:10px;">
                                        <a href="#" id="printPage"
                                            class="printPage btn btn-info">@lang('messages.downloadQr')</a>
                                        <a href="{{ route('party.page1', $restaurant->id) }}" target="__blank"
                                            id="" class=" btn btn-primary">@lang('messages.view_barcode')</a>
                                        {{--                            <a class="btn btn-primary" href="{{ URL::to('/hotel/create_pdf') }}"> @lang('messages.saveAsPdf')</a> --}}
                                    </h3>


                                </div>
                                @if ($errors->any())
                                    <p class="text-danger">{{ $errors->first() }}</p>
                                @endif
                                <div class="form-group col-md-12">

                                    <label class="control-label"> @lang('dashboard.entry.party_description_ar') </label>
                                    <textarea name="party_description_ar" class="form-control">{{ $restaurant->party_description_ar }}</textarea>
                                    @error('party_description_ar')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group col-md-12">

                                    <label class="control-label"> @lang('dashboard.entry.party_description_en') </label>
                                    <textarea name="party_description_en" class="form-control">{{ $restaurant->party_description_en }}</textarea>
                                    @error('party_description_en')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">

                                    <label class="control-label"> @lang('dashboard.enable_party') </label>

                                    <select name="enable_party" id="" class="form-control">
                                        <option value=""></option>
                                        <option value="true" {{ $restaurant->enable_party == 'true' ? 'selected' : '' }}>
                                            {{ trans('dashboard.yes') }}</option>

                                        <option value="false"{{ $restaurant->enable_party == 'false' ? 'selected' : '' }}>
                                            {{ trans('dashboard.no') }}</option>
                                    </select>


                                </div>
                                {{-- party_to_restaurant --}}
                                <div class="form-group">

                                    <label class="control-label"> @lang('dashboard.party_to_restaurant') </label>

                                    <select name="party_to_restaurant" id="" class="form-control">
                                        <option value=""></option>
                                        <option value="true"
                                            {{ $restaurant->party_to_restaurant == 'true' ? 'selected' : '' }}>
                                            {{ trans('dashboard.yes') }}</option>

                                        <option
                                            value="false"{{ $restaurant->party_to_restaurant == 'false' ? 'selected' : '' }}>
                                            {{ trans('dashboard.no') }}</option>
                                    </select>


                                </div>

                                <div class="row">
                                    {{-- party_tax --}}
                                    <div class="form-group col-md-12">
                                        <label class="control-label"> @lang('dashboard.entry.is_tax') </label>
                                        <select name="party_tax" id="" class="form-control select2 checkhidden"
                                            data-target=".form-group.tax">
                                            <option value="true"
                                                {{ $restaurant->party_tax == 'true' ? 'selected' : '' }}>
                                                {{ trans('dashboard.yes') }}</option>

                                            <option
                                                value="false"{{ $restaurant->party_tax == 'false' ? 'selected' : '' }}>
                                                {{ trans('dashboard.no') }}</option>
                                        </select>


                                    </div>

                                    {{-- party_tax_value --}}
                                    <div class="form-group col-md-12 tax">

                                        <label class="control-label"> @lang('dashboard.entry.tax_value') </label>
                                        <input type="number" step="0.01" name="party_tax_value" class="form-control"
                                            value="{{ $restaurant->party_tax_value }}">
                                        @error('party_tax_value')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- party call_phone --}}
                                    <div class="form-group col-md-12">
                                        <label class="control-label"> @lang('dashboard.entry.call_phone') </label>
                                        <select name="party_is_call_phone" id=""
                                            class="form-control select2 checkhidden" data-target=".form-group.call_phone">
                                            <option value="true"
                                                {{ $restaurant->party_is_call_phone == 'true' ? 'selected' : '' }}>
                                                {{ trans('dashboard.yes') }}</option>

                                            <option
                                                value="false"{{ $restaurant->party_is_call_phone == 'false' ? 'selected' : '' }}>
                                                {{ trans('dashboard.no') }}</option>
                                        </select>


                                    </div>

                                    {{-- party_call_phone --}}
                                    <div class="form-group col-md-12 call_phone">

                                        <label class="control-label"> @lang('dashboard.entry.call_phone_') </label>
                                        <input type="text" name="party_call_phone" class="form-control"
                                            value="{{ $restaurant->party_call_phone }}">
                                        @error('party_call_phone')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    {{-- party_whatsapp --}}
                                    <div class="form-group col-md-12">
                                        <label class="control-label"> @lang('dashboard.entry.is_whatsapp') </label>
                                        <select name="party_is_whatsapp" id=""
                                            class="form-control select2 checkhidden" data-target=".form-group.whatsapp">
                                            <option value="true"
                                                {{ $restaurant->party_is_whatsapp == 'true' ? 'selected' : '' }}>
                                                {{ trans('dashboard.yes') }}</option>

                                            <option
                                                value="false"{{ $restaurant->party_is_whatsapp == 'false' ? 'selected' : '' }}>
                                                {{ trans('dashboard.no') }}</option>
                                        </select>


                                    </div>

                                    {{-- party_call_phone --}}
                                    <div class="form-group col-md-12 whatsapp">

                                        <label class="control-label"> @lang('dashboard.entry.whatsapp_number') </label>
                                        <input type="text" name="party_whatsapp_number" class="form-control"
                                            value="{{ $restaurant->party_whatsapp_number }}">
                                        @error('party_whatsapp_number')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label class="control-label"> @lang('dashboard.entry.enable_reservation_email_notification') </label>
                                        <select name="enable_party_email_notification" id=""
                                            class="form-control select2 checkhidden"
                                            data-target=".form-group.email_notification">
                                            <option
                                                value="false"{{ $restaurant->enable_party_email_notification == 'false' ? 'selected' : '' }}>
                                                {{ trans('dashboard.no') }}</option>
                                            <option value="true"
                                                {{ $restaurant->enable_party_email_notification == 'true' ? 'selected' : '' }}>
                                                {{ trans('dashboard.yes') }}</option>


                                        </select>


                                    </div>

                                    <div class="form-group col-md-12 email_notification">

                                        <label class="control-label"> @lang('dashboard.entry.reservation_email_notification') </label>
                                        <input type="email" name="party_email_notification" class="form-control"
                                            value="{{ $restaurant->party_email_notification }}">
                                        @error('party_email_notification')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                                </div>

                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.col -->
        </div>


        <!-- /.row -->
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('admin/js/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/ui-sweetalert.min.js') }}"></script>
    <script src="{{ asset('dist/js/html2canvas.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.checkhidden').on('change', function() {
                var tag = $(this);
                console.log(typeof tag.val(), tag.val());
                if (tag.val() == 'false')
                    $(tag.data('target')).fadeOut(400);
                else
                    $(tag.data('target')).fadeIn(400);
            });
            $('.checkhidden').trigger('change');
            document.getElementById("printPage").addEventListener("click", function() {
                html2canvas(document.getElementById("barcode-svg")).then(function(canvas) {
                    var anchorTag = document.createElement("a");
                    document.body.appendChild(anchorTag);
                    // document.getElementById("previewImg").appendChild(canvas);
                    anchorTag.download = "{{ $name }} Contact Us Page.jpg";
                    anchorTag.href = canvas.toDataURL();
                    anchorTag.target = '_blank';
                    anchorTag.click();
                });
            });

        });
    </script>
@endsection

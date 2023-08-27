@extends('restaurant.lteLayout.master')

@section('title')
    @lang('messages.add') @lang('dashboard.sms_settings')
@endsection

@section('style')
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">

    <style>
        .hide{
            display: none;
        }
    </style>
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> @lang('messages.add') @lang('dashboard.sms_settings') </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/restaurant/home')}}">@lang('messages.control_panel')</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{route('restaurant.sms.settings')}}">
                                @lang('dashboard.sms_settings')
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
                            <h3 class="card-title">@lang('messages.add') @lang('dashboard.sms_settings') </h3>
                        </div>
                        <!-- /.card-header -->
                        @if(!empty($restaurant->sms_method))
                            <div class="sms-balance">
                                <h2 class="text-center mt-3 mb-3">{{ trans('dashboard._sms_method.' . $restaurant->sms_method) }}</h2>
                                @if($restaurant->sms_method == 'taqnyat')
                                    @if(isset($smsBalance['statusCode']) and $smsBalance['statusCode'] == 200)
                                        <p class="text-center alert alert-{{$smsBalance['accountStatus'] == 'active' ? 'info' : 'error'}}">{{ trans('dashboard.sms_taqnyat_success_balance'  , [
                                            'balance' => $smsBalance['balance']  , 
                                            'points' => $smsBalance['points']  , 
                                            "currency" => $smsBalance['currency'], 
                                            'expire' => $smsBalance['accountExpiryDate']  , 
                                        ]) }}</p>
                                    @elseif(isset($smsBalance['message']) )
                                        <p class="text-center alert alert-error">{{$smsBalance['message']}}</p>
                                    @endif
                                @endif
                            </div>
                        @endif
                        <!-- form start -->
                        <form role="form" action="{{route('restaurant.sms.settings')}}" method="post"
                              enctype="multipart/form-data">
                            <input type='hidden' name='_token' value='{{Session::token()}}'>

                            <div class="card-body">
                                {{-- sms_method --}}
                                    <div class="form-group">
                                        <label class="control-label"> @lang('dashboard.entry.sms_method') </label>
                                       <select name="sms_method" id="sms_method" class="form-control select2" data-placeholder="اختر ">
                                        <option value="" disabled selected></option>
                                        <option value="taqnyat" {{$restaurant->sms_method == 'taqnyat' ? 'selected' : '' }}>{{ trans('dashboard._sms_method.taqnyat') }}</option>
                                       </select>
                                        @if ($errors->has('sms_method'))
                                            <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('sms_method') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                
                                {{-- sms_sender --}}
                                <div class="form-group hide sms_sender">
                                    <label class="control-label"> @lang('dashboard.entry.sms_sender') </label>
                                    <input name="sms_sender" type="text" class="form-control"
                                            value="{{$restaurant->sms_sender}}" placeholder="@lang('dashboard.entry.sms_sender')">
                                    @if ($errors->has('sms_sender'))
                                        <span class="help-block">
                                        <strong style="color: red;">{{ $errors->first('sms_sender') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group hide sms_token">
                                    <label class="control-label"> @lang('dashboard.entry.sms_token') </label>
                                    <input name="sms_token" type="text" class="form-control"
                                            value="{{$restaurant->sms_token}}" placeholder="@lang('dashboard.entry.sms_token')">
                                    @if ($errors->has('sms_token'))
                                        <span class="help-block">
                                        <strong style="color: red;">{{ $errors->first('sms_token') }}</strong>
                                    </span>
                                    @endif
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
@endsection
@section('scripts')
    <script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>

    <script>
        $('.select2').select2();
        $(function(){
            $('select[name=sms_method]').on('change' , function(){
                var tag = $(this);
                $('.hide').hide(1);
                if(tag.val() == 'taqnyat'){
                    $('.sms_sender').fadeIn(400);
                    $('.sms_token').fadeIn(400);
                }
            });
            $('select[name=sms_method]').trigger('change');
        });
    </script>
@endsection

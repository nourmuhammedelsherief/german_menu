@extends('restaurant.lteLayout.master')

@section('title')
     @lang('dashboard.send_sms')
@endsection

@section('style')
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>  @lang('dashboard.send_sms') </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/restaurant/home')}}">@lang('messages.control_panel')</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{route('restaurant.sms.sendSms')}}">
                                @lang('dashboard.send_sms')
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
                            <h3 class="card-title"> @lang('dashboard.send_sms') </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="{{route('restaurant.sms.sendSms' )}}" method="post"
                              enctype="multipart/form-data">
                            <input type='hidden' name='_token' value='{{Session::token()}}'>

                            <div class="card-body">
                                @if(!empty($restaurant->sms_method))
                                <div class="sms-balance">
                                    <h2 class="text-center mt-3 mb-3">{{ trans('dashboard._sms_method.' . $restaurant->sms_method) }}</h2>
                                    @if($restaurant->sms_method == 'taqnyat')
                                        @if(isset($smsBalance['statusCode']) and $smsBalance['statusCode'] == 200)
                                            <p class="text-center alert alert-{{$smsBalance['accountStatus'] == 'active' ? 'info' : 'error'}}">{{ trans('dashboard.sms_taqnyat_success_balance'  , [
                                                'balance' => $smsBalance['balance']  , 
                                                "currency" => $smsBalance['currency'], 
                                                'points' => $smsBalance['points']  , 
                                                'expire' => $smsBalance['accountExpiryDate']  , 
                                            ]) }}</p>
                                        @elseif(isset($smsBalance['message']) )
                                            <p class="text-center alert alert-error">{{$smsBalance['message']}}</p>
                                        @endif
                                    @endif
                                </div>
                            @endif
                            {{-- phones --}}
                                <div class="form-group">
                                    <label class="control-label"> @lang('dashboard.phones') </label>
                                    <select name="phones[]" multiple id="phones" class="form-control select2" data-placeholder="اضف ارقام التي تود الارسال اليها" data-tags="true">
                                        
                                    </select>
                                    @if ($errors->has('phones'))
                                        <span class="help-block">
                                        <strong style="color: red;">{{ $errors->first('phones') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('phones.*'))
                                        <span class="help-block">
                                        <strong style="color: red;">{{ $errors->first('phones.*') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                {{-- messages --}}
                                <div class="form-group">
                                    <label class="control-label"> @lang('dashboard.entry.message') </label>
                                    <textarea name="message" id="message" cols="30" rows="4" class="form-control"></textarea>

                                    <p class="sms-count"><span class="count">0</span> /160 (<span class="smsc">1</span>)</p>
                                    @if ($errors->has('message'))
                                        <span class="help-block">
                                        <strong style="color: red;">{{ $errors->first('message') }}</strong>
                                    </span>
                                    @endif
                                </div>
                             


                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">@lang('messages.send')</button>
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
            $('#message').on('keyup' , function(){
                var tag = $(this);
                var smsCount = Math.ceil( tag.val().length  / 160);
                console.log(tag.val().length , smsCount);
                $('.sms-count .count').html(tag.val().length % 160);
                $('.sms-count .smsc').html(smsCount);
            });
        });
    </script>
@endsection

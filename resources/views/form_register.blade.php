@extends('restaurant.authAdmin.master')

@section('style')
<title>التسجيل</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
    

    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    @if (app()->getLocale() == 'en')
        <link rel="stylesheet" href="{{ asset('dist/css/style_ltr.css') }}">
    @endif

    <style>
        .error {
            color: red;
            display: block !important;
        }

        label {
            display: block;
            width: 100%;
            font-size: 15px;
        }

        .country-flag .select2.select2-container.select2-container--default {
            width: 100% !important;
        }

        .country-flag .select2-container .select2-selection--single {
            font-size: 13px;
            /* display: block; */

            height: 50px;
            line-height: 50px;
            margin-bottom: 10px;
            /* padding-right: 20px; */
            border: none;
            background-color: transparent;
            /* border: solid 1px #f7b538!important; */
            transition: all 250ms ease;
        }

        .country-flag .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 50px !important;
        }

        .country-flag .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 5px;
        }

        .form-control.x-input-style {
            font-size: 13px;
            display: block;
            width: 100%;
            height: 50px;
            line-height: 50px;
            margin-bottom: 10px;
            padding-right: 20px;
            border: none;
            background-color: rgb(0 0 0 / 7%);
            border: solid 1px #f7b538 !important;
            transition: all 250ms ease;
        }

        .mobile-number-col {
            position: relative;
        }

        .mobile-number-col .country-flag {
            width: 62px;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 9;
        }

        .mobile-number-col .form-control {
            padding-left: 62px;
            border: 1px solid #ced4da !important;
        }

        .mobile-number-col .country-flag .select2-container .select2-selection--single {
            padding: 0;
        }

        .mobile-number-col img.img-flag {
            width: 35px !important;
            float: right;
            margin-top: 9px;
            height: auto !important;
            margin-right: 7px;
            /* max-height: 35px !important; */
        }

        .mobile-number-col .select2-container[dir="rtl"] .select2-selection--single .select2-selection__rendered {
            padding: 0 !important;
        }

        p.message {
            font-size: 0.8rem;
            padding: 0;
            margin-bottom: 5px;
            text-align: right;
            width: 100%;
        }

        .mobile-code {
            display: none;
        }

        .toggle-content {
            display: none;
            padding: 2rem 1rem;
            background: #f1f1f1;
            text-align: right
        }

        .title_celeste {
            /*background: #111;*/
            /*color: #fff;*/
            padding: 1rem 0;
            cursor: pointer;
            margin: 0;
        }

        .title_celeste {
            text-align: right
        }

        .title_celeste i {
            transition: transform .3s ease;
            float: left;
        }

        .title_celeste.active i {
            transform: rotate(-180deg)
        }

        .questions-content {
            padding: 1.25rem;
        }

        .question-header {
            background-color: #e9ecef;
            height: 50px;
            ;
        }

        [dir=rtl] label.error {
            text-align: right;
        }

        [dir=rtl] .alert-danger p {
            text-align: right;
        }

        .login-logo {
            position: relative;
        }

        .login-logo .change-lang {
            position: absolute;
            top: 0px;
            left: -8px;
            font-size: 1rem;
            font-weight: bold;
        }
    </style>
@endsection
@section('content')
    <div class="login-box">
        <div class="login-logo">
            @include('flash::message')
            <a href="#"><b>التسجيل</b></a>
        </div>
        <div class="card">
            @if (session('An_error_occurred'))
                <div class="alert alert-success">
                    {{ session('An_error_occurred') }}
                </div>
            @endif
            @if (session('warning_login'))
                <div class="alert alert-danger">
                    {{ session('warning_login') }}
                </div>
            @endif
            <div class="alert alert-danger" style="display:none"></div>

            <div class="card-body login-card-body">
                <p class="login-box-msg">التسجيل</p>
                <form method="post" action="{{ route('form_register_post') }}" class="step1">
                    @csrf
                    <input type="hidden" name="recapcha_token" value="">
                    @if (Request::is('restaurant/register-gold/step1'))
                        <input type="hidden" name="package" value="gold">
                    @endif
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-success d-none" id="msg_div">
                                    <span id="res_message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3 form-group">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="form-control" placeholder="@lang('messages.name')">


                        </div>
                        @if ($errors->has('name'))
                            <div class="alert alert-danger">
                                <button class="close" data-close="alert"></button>
                                <span> {{ $errors->first('name') }}</span>
                            </div>
                        @endif
                        <div class="input-group mb-3">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                            <input type="email" name="email" class="form-control" id="email"
                                placeholder="@lang('messages.email')">

                            @if ($errors->has('email'))
                                <div class="alert alert-danger">
                                    <button class="close" data-close="alert"></button>
                                    <span> {{ $errors->first('email') }}</span>
                                </div>
                            @endif
                            <br>
                            <p class="message email"></p>
                        </div>
                        
                        <div class="input-group mb-3">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                            <select name="type" class="form-control">
                                <option disabled selected> نوع النشاط </option>
                                <option value="cafe">مقهي</option>
                                <option value="restaurant">مطعم</option>
                            </select>

                            @if ($errors->has('type'))
                                <div class="alert alert-danger">
                                    <button class="close" data-close="alert"></button>
                                    <span> {{ $errors->first('type') }}</span>
                                </div>
                            @endif
                            <br>
                            <p class="message type"></p>
                        </div>


                        <div class="input-group mb-3 mobile-number-col">

                            {{-- <label class="font-14 font-600">@lang('messages.phone_number')</label> --}}
                            <div class="country-flag">
                                <select name="country_id" class="country">
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            title="{{ $country->flag == null ? null : asset($country->flag_path) }}"
                                            data_flag="@php
if($country->id == 1) echo '01xxxxxxxx'; 
                                                elseif($country->code == 973) echo '3xxxxxxx'; 
                                                elseif($country->id == 2) echo '05xxxxxxxx'; 
                                                else echo $country->code . 'xxxxxxxx'; @endphp">
                                            {{ $country->code }} +
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <input class="form-control" type="number" name="phone_number"
                                pattern="[0-9]*" inputmode="numeric" autocomplete="off" placeholder="@lang('messages.phone_number')">
                            @if ($errors->has('phone_number'))
                                <span class="help-block">
                                    <strong style="color: red;">{{ $errors->first('phone_number') }}</strong>
                                </span>
                            @endif
                            <p class="message phone"></p>
                        </div>

                        {{-- <div class="input-group mb-3">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-flag"></span>
                                </div>
                            </div>
                            <select class="form-control"  name="country_id">
                                <option disabled selected> @lang('messages.choose_country') </option>
                                @foreach ($countries as $country)
                                    <option value="{{$country->id}}">
                                        @if (app()->getLocale() == 'ar')
                                            {{$country->name_ar}}
                                        @else
                                            {{$country->name_en}}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @if ($errors->has('country_id'))
                                <div class="alert alert-danger">
                                    <button class="close" data-close="alert"></button>
                                    <span> {{ $errors->first('country_id') }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-phone"></span>
                                </div>
                            </div>
                            <input type="text" name="phone_number"  class="form-control" placeholder="@lang('messages.phone_number')">

                            @if ($errors->has('phone_number'))
                                <div class="alert alert-danger">
                                    <button class="close" data-close="alert"></button>
                                    <span> {{ $errors->first('phone_number') }}</span>
                                </div>
                            @endif
                        </div> 
                        
                    </div>
                    {{-- end first step --}}

                    <div class="row">
                        <div class="col-3"></div>
                        <div class="col-6">
                            <button type="submit" id="send_form" class="btn btn-primary btn-block"> التسجيل</button>
                        </div>
                        <div class="col-3"></div>
                    </div>
                </form>



            </div>

         


        </div>
        <p class="footer-copyright pb-3 mb-1 pt-0 mt-0 font-13 font-600" style="    text-align: center;">
            {{ trans('messages.made_love') }}
            <i class="fa fa-heart font-14 color-red1-dark" style="color:red;"></i>
            <a style="color: blue" href="{{ url('/') }}">

                {{ trans('messages.at_easy_menu') }}
            </a>
        </p>

    </div>

    <script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>

    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recapcha.client_key') }}"></script>
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2.category').select2({
                theme: 'bootstrap4'
            })
        })
    </script>
    <script>
        var restaurantId = 0;
        var countries = {!! json_encode($countries) !!};
        var registerStep = 'step1';

        function displayErrors(errors) {
            $.each(errors, function(key, all) {
                console.log(key);
                var item = $('.form-control.' + key);
                // if(item.length == 0) var item = $('.form-control.' + key);
                // console.log(item);
                if (item) {
                    var count = 0;
                    $.each(all, function(k, v) {
                        // console.log(k +' :  ' +v);
                        if (count == 0) {
                            var error = $('#' + key + '-error');
                            if (error.length > 0) {
                                error.text(v);
                                console.log('exists');
                                console.log(error);
                            } else {
                                console.log('created');
                                var d = item.parent().append('<label id="' + key +
                                    '-error" class="error">' + v + '</label>');
                                console.log(item.parent());
                            }
                        }

                        count += 1;
                    });
                }

            });
        }
        $(function() {
            var input = $('input.form-control , select.form-control');
            $.each(input, function(k, value) {
                var item = $(value);
                item.addClass(item.prop('name'));
            });
        });
        if ($("#post-form.step1").length > 0) {
            $("#post-form.step1").validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 191,
                        // unique: true,
                    },
                   
                    country_id: {
                        required: true,
                    },
                   

                },
                messages: {
                    name_ar: {
                        required: "{{ trans('messages.name_ar') }}" + " " + "{{ trans('messages.required') }}",
                        maxlength: "{{ trans('messages.max_length') }}" + " " +
                            "{{ trans('messages.name_ar') }}" + "191",
                    },
                    name_en: {
                        required: "{{ trans('messages.name_en') }}" + " " + "{{ trans('messages.required') }}",
                        maxlength: "{{ trans('messages.max_length') }}" + " " +
                            "{{ trans('messages.name_en') }}" + "191",
                    },
                    email: {
                        required: "{{ trans('messages.email') }}" + " " + "{{ trans('messages.required') }}",
                        maxlength: "{{ trans('messages.max_length') }}" + " " + "{{ trans('messages.email') }}" +
                            "191",
                    },
                    country_id: {
                        required: "{{ trans('messages.country') }}" + " " + "{{ trans('messages.required') }}",
                    },
                    phone_number: {
                        required: "{{ trans('messages.phone_number') }}" + " " +
                            "{{ trans('messages.required') }}",
                        maxlength: "{{ trans('messages.max_length') }}" + " " +
                            "{{ trans('messages.phone_number') }}" + "11",
                    },
                    password: {
                        required: "{{ trans('messages.password') }}" + " " + "{{ trans('messages.required') }}",
                        minlength: "{{ trans('messages.min_length') }}" + " " +
                            "{{ trans('messages.password') }}" + "8",
                    },
                    password_confirmation: {
                        required: "{{ trans('messages.password_confirmation') }}" + " " +
                            "{{ trans('messages.required') }}",
                        minlength: "{{ trans('messages.min_length') }}" + " " +
                            "{{ trans('messages.password_confirmation') }}" + "8",
                    },

                },
            })
        }



        function formatState(state) {
            if (!state.id) {
                return state.text;
            }
            // console.log(state);
            // console.log(state.element.attributes.data_flag.value);
            if (state.title.length > 10) {
                var image = '<img width="30" height="30" src="' + state.title + '" class="img-flag" />';
            } else var image = '<span>' + state.text + '</span>';
            var $state = $(
                image
            );
            return $state;
        };

        function formatState2(state) {
            if (!state.id) {
                return state.text;
            }
            // console.log(state);
            // console.log(state.element.attributes.data_flag.value);
            if (state.title.length > 10) {
                var image = '<img width="30" height="30" src="' + state.title + '" class="img-flag" />';
            } else var image = '<span>' + state.text + '</span>';
            var $state = $(
                image
            );

            console.log(state.element.attributes.data_flag.value);
            $('.mobile-number-col input[name=phone_number]').prop('placeholder', state.element.attributes
                .data_flag.value);

            return $state;
        };
        var checktimeout = null;
        $(function() {
            $('select[name=country_id]').on('change', function() {
                var content = '';
                var tag = $(this);
                $.each(countries, function(key, country) {
                    if (country.id == tag.val()) {
                        content +=
                            '<option disabled selected> @lang('messages.choose_city') </option>';
                        $.each(country.cities, function(k, city) {
                            content += '<option value="' + city.id + '">' + city
                                .name + '</option>';
                        });
                        $('select[name=city_id]').html(content);
                        // $('select[name=city_id]').select2();
                    }
                });
            });
            $('select[name=country_id]').trigger('change');
            $("select.country").select2({
                templateResult: formatState,
                templateSelection: formatState2,
                // dir: "rtl",
                dropdownAutoWidth: true,
                dropdownParent: $('.country-flag')

            });
            $('input[name=email]').on('keyup', function() {
                var tag = $(this);
                if (checktimeout) clearTimeout(checktimeout);
                checktimeout = setTimeout(function() {

                    $.ajax({
                        url: "{{ route('restaurant.check') }}",
                        method: 'get',
                        data: {
                            email: tag.val()
                        },
                        headers: {
                            accept: 'application/json'
                        },
                        success: function(json) {
                            console.log(json);
                            if (json.status == true) {
                                $('p.message.email').removeClass('text-danger')
                                    .addClass('text-success').text(
                                        '{{ trans('messages.email_success') }}'
                                    );
                            } else if (json.status == false) {
                                $('p.message.email').addClass('text-danger')
                                    .removeClass('text-success').text(json.data
                                        .email);
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr);
                            $('p.message.email').addClass('text-danger')
                                .removeClass('text-success').text('');
                        }
                    });
                }, 1000);
            });
            $('input[name=phone_number], select[name=country_id]').on('keyup change', function() {
                var tag = $('input[name=phone_number]');
                if (checktimeout) clearTimeout(checktimeout);
                checktimeout = setTimeout(function() {
                    var countryId = tag.parent().find('select[name=country_id]').val();
                    $.ajax({
                        url: "{{ route('restaurant.check') }}",
                        method: 'get',
                        data: {
                            phone_number: tag.val(),
                            country_id: countryId
                        },
                        headers: {
                            accept: 'application/json'
                        },
                        success: function(json) {
                            console.log(json);
                            if (json.status == true) {
                                $('p.message.phone').removeClass('text-danger')
                                    .addClass('text-success').text(
                                        '{{ trans('messages.phone_success') }}'
                                    );
                            } else if (json.status == false) {
                                $('p.message.phone').addClass('text-danger')
                                    .removeClass('text-success').text(json.data
                                        .phone_number);
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr);
                        }
                    });
                }, 1000);
            });

            // forget code
            $('body').on('click', 'a.forget-password', function() {
                var tag = $(this);
                console.log(restaurantId);
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ config('services.recapcha.client_key') }}', {
                        action: 'restaurantRegisterResend'
                    }).then(function(token) {
                        // Add your logic to submit to your backend server here.
                        // $('input[name=recapcha_token]').val(token);
                        $.ajax({
                            url: "{{ url('restaurant/resend_code') }}/" +
                                restaurantId,
                            method: 'POST',
                            data: {
                                recapcha_token : token ,
                            } , 
                            headers: {
                                accept: 'application/json'
                            },
                            success: function(json) {
                                console.log(json);
                                if (json.status == true) {
                                    toastr.success(json.message);
                                }
                            },
                            error: function(xhr) {
                                console.log(xhr);
                            }
                        });
                    });
                });

            });
            // verification phone
            $('body').on('click', '#post-form.step2 button', function() {
                console.log('test');
                var form = $('#post-form');

                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ config('services.recapcha.client_key') }}', {
                        action: 'restaurantRegisterStep2'
                    }).then(function(token) {
                        // Add your logic to submit to your backend server here.
                        $('input[name=recapcha_token]').val(token);
                        console.log('restaurantRegisterStep2', token);
                        var formData = new FormData(form[0]);
                        $.ajax({
                            url: "{{ url('restaurant/phone_verification') }}/" +
                                restaurantId,
                            method: "POST",
                            headers: {
                                Accept: 'application/json'
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(json) {
                                $('#send_form').html(
                                    "{{ trans('messages.confirm') }}");
                                console.log(json);

                                if (json.status == true) {
                                    toastr.success(json.message);
                                    window.location.replace(
                                        "{{ url('restaurant/home') }}");
                                } else {
                                    toastr.info(json.message);
                                }
                            },
                            error: function(xhr) {
                                $('#send_form').html(
                                    "{{ trans('messages.confirm') }}");
                                console.log(xhr);
                                toastr.error('لم يتم التحقق من الكود');
                            }
                        });
                    })
                });

            });
        });
    </script>
    <script>
        $('.title_celeste').on("click", function(evt) {
            evt.preventDefault();

            let _this = $(this),
                ct = _this.next('.toggle-content');

            // check if clicked title has active class
            if (_this.hasClass('active')) {

                _this.removeClass('active');
                ct.slideUp();

            } else {
                $('.title_celeste.active').removeClass('active');
                $('.toggle-content').slideUp();
                _this.addClass('active');
                ct.slideDown();

            }

        });
    </script>
@endsection

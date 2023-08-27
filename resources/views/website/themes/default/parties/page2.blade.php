@push('styles')
    {{-- <link rel="stylesheet" href="{{asset('plugins/color-calender/theme-basic.css')}}"> --}}
    <link rel="stylesheet" href="{{ asset('plugins/color-calender/theme-glass.css') }}">
@endpush
@include('website.' . session('theme_path') . 'silver.layout.header')


<style>
    :root {
        --rest-background-color: #ebebeb;
    }

    html {
        background-color: #FFF !important;
    }

    body {
        position: relative;
        background-color: #FFF !important;
        /* margin: 0px !important; */
    }

    a {
        color: #000;
    }

    .x-card-header {
        width: 100%;
        height: 40px;
        background-color: {{ $restaurant->color == null ? '' : $restaurant->color->background }}
    }

    .x-card {
        min-height: 400px;
        /* padding-top: 40px; */
        border: 0 !important;
        border-radius: 0 !important;
        border-top-left-radius: 47px !important;
        border-top-right-radius: 47px !important;
        /* background-color:
    {{ $restaurant->color == null ? '' : $restaurant->color->background }} , */
    }

    .x-card .icons {
        margin: 20px 2px 10px 2px;
    }

    .icons .div-icon {
        display: inline-block;
        border-left: 1px solid #CCC;
        padding: 10px 0;
        width: calc(33% - 5px);
        height: 73px !important;
        box-sizing: border-box;
        overflow: hidden;
    }

    .icons .div-icon:last-child {
        border-left: 0;
    }

    .icons i {
        font-size: 22px;
    }

    .details {
        margin: 5%;
        background-color: #f7f7f7;
        padding: 10px;
        padding-bottom: 34px;
        /* clear: both; */
        border: 3px solid var(--rest-background-color);
        border-style: dashed;
    }

    .details .data {
        clear: both;
    }

    .details .data label {
        float: right;
    }

    .details .data p {
        float: left;
    }

    .description-content {
        position: relative;
        padding: 10px;
        border: 2px dashed var(--rest-background-color);
    }

    .description-content .title {
        position: absolute;
        top: -18px;
        right: 10px;
        border-bottom: 2px dashed var(--rest-background-color);
        background-color: #FFF;
        padding: 0 10px;
    }

    .description-content .body {
        padding: 10px;
        clear: both;
    }

    .client-note textarea {
        border: 0;
        /* background-color: var(--rest-background-color) */
    }

    label.dashed {
        border-bottom: 2px dashed var(--rest-background-color);
    }

    .parties .list {
        max-height: 80px;
        overflow-x: scroll;
        white-space: nowrap;
    }

    .parties .list .one {
        padding: 10px 20px;
        border: 1px solid var(--rest-background-color);
        border-radius: 20px;
        display: inline-block;
        cursor: pointer;
    }

    .div-icon p {
        height: 31px;
    }

    .date-icon p {
        margin: 0;
        line-height: 1.2;
        font-size: 11px;
    }

    .payment.form-group {
        margin-top: 20px;
    }

    .payment.form-group label {
        font-size: 14px;
    }

    .footer-button {
        margin-top: 40px;
        margin-bottom: 30px;
        clear: both;
    }

    .footer-button a {
        font-size: 14px;
    }

    .heighlight {

        height: 81px !important;
        border-left: 1px solid #000;
        width: 0px;
    }

    /* new */
    html {
        position: relative;
    }

    body {
        position: initial !important;
        margin-bottom: 50px !important;
    }

    .footer-description {
        position: absolute;
        padding: 10px 0;
        bottom: 10px;
        left: 0;
        width: 100% !important;
        text-align: center;
    }
    .required{
        font-size: 1.3rem;
        color: red;
    }
</style>


<div class="x-card-header"></div>
<div class="x-card card mr-0 ml-0 rounded-l" style="">
    <div class="container">
        <h1 class="text-center" style="font-size: 1.1rem !important;margin-top: 15px;">
            {{ trans('messages.parties') }} - {{ $restaurant->name }}</h1>
        @if ($errors->any())
            <p class="alert alert-danger mt-5">{{ $errors->first() }}</p>
        @endif
        <div class="icons">
            {{-- <div class="div-icon text-center">
                <div class=" text-center">
                    <i class="fas fa-user-alt"></i>
                </div>
                <p>{{$party->people_count * ($party->chairs >0 ? $party->chairs : 1)}}  </p>
                
            </div> --}}


            <div class="div-icon text-center">
                <div class=" text-center">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <p>
                    {{ $day->date }}
                </p>
            </div>

            <div class="div-icon text-center date-icon">
                <div class=" text-center">
                    <i class="fas fa-clock" style=""></i>
                </div>
                <p>
                    @lang('dashboard.from')
                    {{ $period->from_string }}<br>
                    @lang('dashboard.to')
                    {{ $period->to_string }}
                </p>

            </div>

            <div class="div-icon text-center">
                <div class=" text-center">
                    <i class="fas fa-building"></i>
                </div>
                <p>
                    {{ isset($branch->id) ? $branch->name : '' }}
                </p>
            </div>
        </div>

        <div class="details">


            <div class="data">
                <label for="">
                    @lang('messages.name')
                </label>
                <p>{{ $party->title }} </p>
            </div>

            <div class="data">
                <label for="">
                    @lang('messages.price')
                </label>
                <p>{{ $party->price }} {{ $country->currency }}</p>
            </div>
            {{-- @if ($party->tax > 0)
                <div class="data" style="clear: both">
                    <label for="">{{ trans('messages.tax_') }}</label>
                    <p>{{ $party->tax }} {{ $country->name }}</p>
                </div>
            @endif --}}
            @if ($restaurant->online_payment_fees > 0)
                <div class="data  " id="online_payment_fees"
                    data-price="{{ ($party->total_price * $restaurant->online_payment_fees) / 100 }}"
                    style="clear: both;display:none;">
                    <label for="">{{ trans('messages.online_payment_fees') }}</label>
                    <p>{{ $restaurant->online_payment_fees }}%</p>
                </div>
            @endif
            <div class="data" style="position: relative;">
                <label for="">@lang('messages.total')</label>
                <p style=""><span class="total-price">{{ $party->total_price }}</span>
                    {{ $country->currency }} <br>


                </p>
                @if ($restaurant->total_tax_price == 'true')
                    <span
                        style="font-size: 0.5rem;
                        position: absolute;
                        top: 13px;
                        left: -12px;
                        width: 56px;">{{ trans('messages.tax_des') }}</span>
                @endif
            </div>

        </div>

        @if ($party->description != null)
            <div class="title">@lang('messages.details')</div>
            <div class="description-content">

                <div class="body">
                    {!! $party->description !!}

                    @if ($party->additions->count() > 0)
                        <h3 class="mt-3">{{ trans('messages.additions') }}</h3>
                        <div class="row">
                            @foreach ($party->additions as $item)
                                <div class="col-8 text-right">{{ $item->name }}</div>
                                <div class="col-4 text-left">{{ $item->price }} {{ $country->currency }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
        {{-- <div class="required-check">
            <input type="checkbox" name="policy" id="policy" value="1"
                style="width: 15px;
            height: 15px;
            vertical-align: middle;"> <label
                for="policy">{{ trans('messages.agree_policy') }}</label>
        </div> --}}
        <form action="{{ route('party.store', [$restaurant->id]) }}" method="POST" id="reservationForm"
            enctype="multipart/form-data">
            @csrf
            <!--<div class="client-note">-->
            <!--    <label for="" class="dashed">-->
            <!--        @lang('messages.notes')-->
            <!--    </label>-->
            <!--    <textarea name="note" class="form-control" style="border:1px solid #CCC;" id="" cols="30"
                rows="5" placeholder="{{ trans('messages.note_hint') }}"></textarea>-->
            <!--</div>-->

            <input type="hidden" name="period_id" value="{{$period->id}}">
            @foreach ($party->fields as $item)
                @if ($item->type == 'text')
                    <div class="form-group ">
                        <label for="fields{{ $item->id }}">
                            {{ $item->name }} @if ($item->is_required)
                                <span class="required">*</span>
                            @endif
                        </label>
                        <input type="text" name="fields[{{ $item->id }}]" id="fields{{ $item->id }}"
                            {{ $item->is_required ? 'required' : '' }} class="form-control">
                    </div>
                @elseif($item->type == 'checkbox')
                    <div class="form-group ">
                        <label for="fields{{ $item->id }}">
                            {{ $item->name }} @if ($item->is_required)
                                <span class="required">*</span>
                            @endif
                        </label>
                        <div class="row">
                            @foreach ($item->options as $tt)
                                <div class="col-md-4 col-sm-6">

                                    <input type="checkbox" id="fields{{ $tt->id }}" class="form-checkbox"
                                        name="fields[{{ $item->id }}][]" value="{{ $tt->id }}"
                                        style="width:15px;height:15px;">
                                    <label class="check-label" style="margin-bottom: 0;"
                                        for="fields{{ $tt->id }}">{{ $tt->name }}</label>

                                </div>
                            @endforeach

                        </div>
                    </div>
                @elseif($item->type == 'select')
                    <div class="form-group ">
                        <label for="fields{{ $item->id }}">
                            {{ $item->name }} @if ($item->is_required)
                                <span class="required">*</span>
                            @endif
                        </label>
                        <select name="fields[{{ $item->id }}]" id="fields{{ $item->id }}"
                            class="form-control select2" {{ $item->is_required ? 'required' : '' }}>
                            @foreach ($item->options as $op)
                                <option value="{{ $op->id }}" {{ $op->is_default == 1 ? 'selected' : '' }}>
                                    {{ $op->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @endforeach
            <div class="form-group payment">
                <label for="payment_type">
                    @lang('messages.payment_method')
                </label>
                <select name="payment_type" id="payment_type" class="form-control">
                    {{-- @if ($restaurant->enable_reservation_cash == 'true')
                        <option value="cash" selected>
                            @lang('dashboard.cash_on_delivery')
                        </option>
                    @endif --}}
                    @if ($restaurant->enable_party_payment_bank == 'true')
                        <option value="bank">
                            @lang('messages.transfer_bank')
                        </option>
                    @endif
                    @if ($restaurant->enable_party_payment_online == 'true')
                        <option value="online">
                            @lang('messages.online')
                        </option>
                    @endif
                </select>
            </div>

            <div class="footer-button text-center">
                <button type="button" class="btn btn-primary"
                    id="save-all">{{ trans('messages.next_step') }}</button>

                <button href="{{ route('page.page1', $restaurant->id) . '?branch_id=' . $branch->Id }}"
                    class="btn btn-secondary return" type="button">{{ trans('messages.return') }}</button>
            </div>
        </form>





    </div>

</div>



<div class="footer footer-description text-center">
    {!! trans('messages.reservation_footer') !!}
</div>

@push('scripts')
    <script>
        var totalPrice = {{ $party->total_price }};
        var onlineFees = {{ $restaurant->online_payment_fees > 0 ? $restaurant->online_payment_fees : 0 }};
        $(function() {
            $('.save-page').on('click', function(event) {
                event.preventDefault();
                if ($('select[name=payment_type]').val())
                    $('form#reservationForm').submit();
            });
            $('input[name=policy]').on('change', function() {
                if ($(this).prop('checked'))
                    $(this).parent().find('label').css('color', '#000');
            });
            $('#save-all').on('click', function() {
                
                $('form#reservationForm').submit();
            });
            $('button.return').on('click', function() {
                window.location.replace($(this).attr('href'));
            });
            $('#payment_type').on('change', function() {
                var tag = $(this);
                var online = $('#online_payment_fees');
                if (tag.val() == 'online') {
                    var t = Math.round(onlineFees > 0 ? (((totalPrice * onlineFees) / 100) + totalPrice) :
                        totalPrice);
                    var fees = Math.round(((totalPrice * onlineFees) / 100));
                    $('#online_payment_fees p').text(fees);
                    $('.total-price').text(t);
                    online.fadeIn(300);
                } else {
                    $('.total-price').text(totalPrice);
                    online.fadeOut(300);
                }
            });
            $('#payment_type').trigger('change');
        });
    </script>
@endpush

@include('website.' . session('theme_path') . 'silver.layout.scripts')

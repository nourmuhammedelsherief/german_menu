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
        /* background-color: {{ $restaurant->color == null ? '' : $restaurant->color->background }}, */
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

    .client-note textarea {
        border: 0;
        background-color: var(--rest-background-color)
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

    .details h1 {
        font-size: 18px !important;
        line-height: 1.3;
    }

    .footer-button {
        margin-top: 40px;
        margin-bottom: 30px;
        clear: both;
    }

    .footer-button a {
        font-size: 14px;
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

    #barcode-svg {
        width: 130px;
        margin: auto;
        padding: 20px 0;
    }

    .dd>a>i {
        font-size: 16px;
        padding: 0 5px;
    }
</style>




<div class="x-card-header"></div>
<div class="x-card card mr-0 ml-0 rounded-l" style="">
    <div class="container">
        @include('flash::message')
        <div id="summery">
            <div class="text-center" style="font-size: 1.1rem !important;margin-top: 15px;font-weight:bold;">
                {{ trans('messages.parties') }} - {{ $restaurant->name }}</div>
            <div class="icons">


                <div class="div-icon text-center">
                    <div class=" text-center">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <p>
                        {{ $reservation->date }}
                    </p>
                </div>

                <div class="div-icon text-center date-icon">
                    <div class=" text-center">
                        <i class="fas fa-clock" style=""></i>
                    </div>
                    <p>
                        @lang('dashboard.from')
                        {{ $reservation->from_string }}<br>
                        @lang('dashboard.to')
                        {{ $reservation->to_string }}
                    </p>

                </div>

                <div class="div-icon text-center">
                    <div class=" text-center">
                        <i class="fas fa-building"></i>
                    </div>
                    <p>
                        {{ isset($reservation->branch->id) ? $reservation->branch->name : '' }}
                    </p>
                </div>
            </div>

            <div class="details">
                <div class="card-body" id="barcode-svg">


                    {!! QrCode::size(130)->generate(route('party.summery', [$restaurant->id, $reservation->id])) !!}


                </div>
                <div class="text-center dd">
                    <a href="javascript:;" id="share-reservation" style="font-size: 11px;margin-bottom: 15px;"
                        class="btn btn-info"><i class="fas fa-share-square"></i>
                        {{ trans('messages.share_reservation') }}</a>
                    @if (!empty($branch->location_link))
                        <a href="{{ $branch->location_link }}" target="__blank" id="go-location"
                            style="font-size: 11px;margin-bottom: 15px;" class="btn btn-info"><i
                                class="fas fa-map-marked-alt"></i> {{ trans('messages.go_location') }}</a>
                    @endif
                </div>
                <div class="text-center" style="font-size: 18px;
                font-weight: bold;">
                    {{ trans('messages.order_number') }}
                    <span>#{{ $reservation->id }}</span>
                </div>
                @if (!empty($reservation->payment_type))
                    <div class="data">
                        <label for="">
                            @lang('messages.payment_status')
                        </label>
                        <p>
                            @if (in_array($reservation->payment_status, ['paid', 'completed']))
                                <span class="text-success">{{ trans('messages.paid') }}</span>
                            @else
                                <span class="text-danger">{{ trans('messages.not_paid') }}</span>
                            @endif
                        </p>
                    </div>
                @endif
                @if (!empty($reservation->num))
                    <div class="data">
                        <label for="">
                            @lang('messages.reservation_num')
                        </label>
                        <p>{{ $reservation->num }}</p>
                    </div>
                @endif
                @if (!empty($reservation->payment_type))
                    <div class="data">
                        <label for="">
                            @lang('messages.payment_type')
                        </label>
                        <p>{{ trans('messages._payment_type.' . $reservation->payment_type) }}
                            @if ($reservation->payment_type == 'online' and !empty($reservation->online_payment_type))
                                {{ trans('messages._online_payment.' . $reservation->online_payment_type) }}
                            @endif
                        </p>
                    </div>
                @endif
                @if (!empty($reservation->invoice_id))
                    <div class="data">
                        <label for="">
                            @lang('messages.paid_number')
                        </label>
                        <p>{{ $reservation->invoice_id }}</p>
                    </div>
                @endif
                {{-- <div class="data">
                    <label for="">
                        @lang('messages.reservation_num')
                    </label>
                    <p>{{$reservation->num}}</p>
                </div>
                 --}}

                @if (!empty($reservation->user->name))
                    <div class="data">
                        <label for="">
                            @lang('messages.name')
                        </label>
                        <p>{{ $reservation->user->name }}</p>
                    </div>
                @endif
                @if (!empty($reservation->user->phone_number))
                    <div class="data">
                        <label for="">
                            @lang('messages.phone_number')
                        </label>
                        <p>{{ $reservation->user->phone_number }}</p>
                    </div>
                @endif

                <div class="data">
                    <label for="">
                        @lang('messages.branch')
                    </label>
                    <p>{{ $reservation->branch->name }}</p>
                </div>


                <div class="data">
                    <label for="">
                        @lang('messages.price')
                    </label>
                    <p>{{ $reservation->price }} {{ $country->currency }}</p>
                </div>
                @if ($reservation->tax > 0)
                    <div class="data">
                        <label for="">{{ trans('messages.tax') }}</label>
                        <p>{{ $reservation->tax }} {{ $country->currency }}</p>
                    </div>
                @endif
                @if ($restaurant->online_payment_fees > 0 and $reservation->payment_type == 'online')
                    <div class="data  " id="online_payment_fees" style="clear: both;">
                        <label for="">{{ trans('messages.online_payment_fees') }}</label>
                        <p>{{ round(($restaurant->online_payment_fees * $reservation->total_price) / 100) }}</p>
                    </div>
                @endif
                <div class="data" style="position: relative;">
                    <label for="">@lang('messages.total')</label>
                    <p style="">{{ round($reservation->total_price) }} {{ $country->currency }} <br></p>
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
        </div>
        <div class="footer-button text-center">
            {{-- <a  href="javascript:;" id="share-reservation"  class="btn btn-info">{{ trans('messages.share_reservation') }}</a> --}}
            <a href="{{ route('party.page1', $restaurant->id) }}"
                class="btn btn-success">{{ trans('messages.back_to_party') }}</a>
            @if ($restaurant->party_to_restaurant == 'true')
                <a href="{{ route('sliverHome', $restaurant->name_barcode) }}"
                    class="btn btn-primary">{{ trans('messages.to_to_restaurant') }}</a>
            @endif

        </div>
        <input src="" id="share-file" alt="" style="display:none;">
    </div>

</div>
<div class="footer footer-description text-center">
    {!! trans('messages.reservation_footer') !!}
</div>




@push('scripts')
    <script src="{{ asset('dist/js/html2canvas.min.js') }}"></script>
    <script>
        $(function() {
            $('.save-page').on('click', function() {
                $('#reservationForm').submit();
            });

            // document.getElementById("share-reservation").addEventListener("click", function() {
            //     html2canvas(document.getElementById("summery")).then(function (canvas) {			var anchorTag = document.createElement("a");
            //         // $('#share-file').attr('src' , canvas.toDataURL());
            //         document.body.appendChild(anchorTag);
            //         // document.getElementById("previewImg").appendChild(canvas);
            //         anchorTag.download = "reservation-{{ $reservation->id }}.jpg";
            //         anchorTag.href = canvas.toDataURL();
            //         anchorTag.target = '_blank';
            //         anchorTag.click();
            //         // share it
            //         // canvas.toBlob(async (blob) => {
            //         //     // Even if you want to share just one file you need to
            //         //     // send them as an array of files.
            //         //     const files = [new File([blob], 'image.jpg', { type: blob.type })]
            //         //     const shareData = {
            //         //         text: '{{ $restaurant->name }}',
            //         //         title: '{{ $restaurant->name }}',
            //         //         files,
            //         //     }
            //         //     if (navigator.share) {
            //         //         try {
            //         //         await navigator.share(shareData)
            //         //         } catch (err) {
            //         //         if (err.name !== 'AbortError') {
            //         //             console.error(err.name, err.message)
            //         //         }
            //         //         }
            //         //     } else {
            //         //         console.warn('Sharing not supported', shareData)
            //         //     }
            //         // });

            //     });
            // });

            $('#share-reservation').on('click', function() {
                if (navigator.share) {
                    navigator.share({
                            title: "{{ trans('messages.parties') }}",
                            // text : "{{ trans('messages.parties') }}" , 
                            url: "{{ route('party.summery', [$restaurant->id, $reservation->id]) }}"
                        })
                        .then(() => console.log('Successful share'))
                        .catch((error) => console.log('Error sharing', error));
                } else {
                    console.log('Share not supported on this browser, do it the old way.');
                }
            });
        });
    </script>
@endpush

@include('website.' . session('theme_path') . 'silver.layout.scripts')

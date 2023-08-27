@push('styles')
    {{-- <link rel="stylesheet" href="{{asset('plugins/color-calender/theme-basic.css')}}"> --}}
    <link rel="stylesheet" href="{{ asset('plugins/color-calender/theme-glass.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/vanilla-calendar/vanilla-calendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">

    <style>
        :root {
            --p-main-color: #f1a311;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('styles/party.css') }}">
@endpush
@include('website.' . session('theme_path') . 'silver.layout.header')


<div class="x-card-header"></div>
<div class="x-card card mr-0 ml-0 rounded-l" style="">
    <div class="container">
        <div class="image-preview text-center">
            <img src="{{ asset($restaurant->image_path) }}" style="width:100px;height:100px;" alt="">
        </div>
        <h1 class="text-center" style="font-size: 1.1rem !important;margin-top: 15px;">
            {{ trans('messages.parties') }} - {{ $restaurant->name }}</h1>
        {{-- @if ($restaurant->reservation_is_call_phone == 'true' || $restaurant->reservation_is_whatsapp == 'true')
            <div id="box" class=" mt-3 mb-n1" style="min-height: 100px;">
                @if ($restaurant->reservation_is_call_phone == 'true')
                    <div class="icon-user itemCatTop">

                        <a href="tel:{{ $restaurant->reservation_call_number }}"
                            class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                            <i class="fas fa-phone"
                                style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                        </a>
                        <p class="font-600 font-13 mt-2"
                            style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                            @lang('messages.call')</p>

                    </div>
                @endif
                @if ($restaurant->reservation_is_whatsapp == 'true')
                    <div class="icon-user itemCatTop">

                        <a href="https://wa.me/{{ $restaurant->reservation_whatsapp_number }}" target="__blank"
                            class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                            <i class="fab fa-whatsapp"
                                style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                        </a>
                        <p class="font-600 font-13 mt-2"
                            style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                            @lang('messages.whatsapp')</p>

                    </div>
                @endif
            </div>
        @endif --}}
        @if (!empty($restaurant->party_description))
            <p class="party-description"
                style="    margin: 10px 0 20px !important;
            text-align: center;
            font-size: 0.8rem;
            font-weight: bold;">
                {{ $restaurant->party_description }}
            </p>
        @endif
        @if ($restaurant->party_is_call_phone == 'true' || $restaurant->party_is_whatsapp == 'true')
            <div id="box" class=" mt-3 mb-n1" style="min-height: 100px;">
                @if ($restaurant->party_is_call_phone == 'true')
                    <div class="icon-user itemCatTop">

                        <a href="tel:{{ $restaurant->party_call_number }}"
                            class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                            <i class="fas fa-phone"
                                style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                        </a>
                        <p class="font-600 font-13 mt-2"
                            style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                            @lang('messages.call')</p>

                    </div>
                @endif
                @if ($restaurant->party_is_whatsapp == 'true')
                    <div class="icon-user itemCatTop">

                        <a href="https://wa.me/{{ $restaurant->party_whatsapp_number }}" target="__blank"
                            class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                            <i class="fab fa-whatsapp"
                                style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                        </a>
                        <p class="font-600 font-13 mt-2"
                            style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                            @lang('messages.whatsapp')</p>

                    </div>
                @endif
            </div>
        @endif
        @if ($errors->any())
            <p class="alert alert-danger mt-5">{{ $errors->first() }}</p>
        @endif
        @if ($restaurant->enable_party != 'true')
            <h4 class="text-center alert alert-warning mt-5">{{ trans('messages.party_not_available') }}</h4>
            <div class="footer-button text-center">
                @if ($restaurant->party_to_restaurant == 'true')
                    <a href="{{ route('sliverHome', $restaurant->name_barcode) }}"
                        class="btn btn-primary">{{ trans('messages.to_to_restaurant') }}</a>
                @endif
                @if (!auth('web')->check())
                    <a href="{{ route('showUserLogin', $restaurant->id) }}"
                        class="btn btn-primary">{{ trans('messages.login') }}</a>
                @endif
            </div>
        @elseif(auth('web')->check())
            @if ($branches->count() > 0)
                <div class="">
                    <div class="row">
                        <div class="col-12">
                            <p>{{ trans('messages.branch') }}</p>
                        </div>
                        <div class="col-12">

                            <select name="branch_id" id="branch_id" class="form-control select2"
                                {{ $branches->count() == 1 ? 'disabled' : '' }}>
                                @foreach ($branches as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $branch->id == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                </div>
            @endif
            @if ($isParty and $parties->count() > 0)

                <form action="{{ route('party.store', $restaurant->id) }}" method="post" id="reservationForm">
                    @csrf
                    <input type="hidden" name="party_id">
                    <div class="parties">

                        <div class="party-slider mainsld owl-carousel">
                            @foreach ($parties as $item)
                                <div>
                                    <div class="party-image">
                                        <img src="{{ asset($item->image_path) }}" alt="">
                                    </div>
                                    <div class=" party" data-id="{{ $item->id }}"
                                        data-price="{{ $item->price }}">
                                        <div class="title">{{ $item->title }}</div>

                                        <div class="description">
                                            {!! $item->description !!}
                                        </div>

                                        <div class="price">
                                            {{ $item->price }} {{ $country->currency }}

                                        </div>

                                        <div class="action">
                                            <button type="button" class="btn ">{{ trans('messages.choose_party') }}
                                                <span><i class="fas fa-check"></i></span></button>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>


                    </div>

                    <div id="dates" class=" ">

                    </div>
                    <div id="periods" class=" ">

                    </div>
                    <div id="fields" class=" ">

                    </div>

                </form>
            @else
                <h5 class="text-center alert alert-warning">{{ trans('messages.party_not_available') }}</h5>
            @endif
        @else
            <h4 class="text-center alert alert-info mt-5">{{ trans('messages.login_required') }}</h4>
            <div class="footer-button text-center">
                @if ($restaurant->party_to_restaurant == 'true')
                    <a href="{{ route('sliverHome', $restaurant->name_barcode) }}"
                        class="btn btn-primary">{{ trans('messages.to_to_restaurant') }}</a>
                @endif
                <a href="{{ route('showUserLogin', $restaurant->id) }}"
                    class="btn btn-primary">{{ trans('messages.login') }}</a>
            </div>
        @endif
        {{-- end auth web --}}
    </div>

</div>



<div class="footer footer-description text-center">
    {!! trans('messages.reservation_footer') !!}
</div>
<div id="menu-package-details" style="border-top-left-radius: 15px;border-top-right-radius: 15px;"
    class="menu menu-box-bottom menu-box-detached" data-menu-height="100%" data-menu-effect="menu-over"
    data-menu-load="">

</div>

@push('scripts')
    {{-- <script src="{{asset('plugins/color-calender/bundle.min.js')}}"></script> --}}
    <script src="{{ asset('plugins/vanilla-calendar/vanilla-calendar.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/js/select2.js') }}"></script>
    <script>
        var places = null;

        var price = 0;
        var periodId = 0;
        $('.select2').select2();

        function getTotalPrice() {
            var totalPrice = price;
            var checked = $('.additions input[type=checkbox]:checked');
            $.each(checked, function(k, v) {
                var t = $(v);
                totalPrice += t.data('price');
            });
            console.log("total price: " + totalPrice);
            $('.total-price span').html(totalPrice);
            $('.total-price').fadeIn(200);
        }
        $(function() {
            $('form').on('click', 'button.return', function() {
                if ($(this).data('href')) {
                    window.location.replace($(this).data('href'));
                }
            });
            // when change additions
            $('form').on('click', '.additions input[type=checkbox]', function() {
                getTotalPrice();
            });
            // party active
            $('.parties .party button').on('click', function() {
                var tag = $(this).parent().parent();

                $('.parties .party').removeClass('active');
                tag.addClass('active');
                $('input[name=party_id]').val(tag.data('id'));
                periodId = tag.data('id');
                $.ajax({
                    url: "{{ route('party.dates', $restaurant->id) }}",
                    method: "GET",
                    data: {
                        party_id: tag.data('id')
                    },
                    success: function(json) {
                        $('.total_price span').html(tag.data('price'));
                        $('.total_price').fadeIn(200);
                        price = tag.data('price');
                        console.log(json);
                        if (json.status) {
                            var content =
                                '<div class="form-group"><label for="">{{ trans('messages.date') }}</label><select type="text" name="date" class="form-control  select2" data-placeholder="اختر التاريخ"><option selected value="" disabled ></option>';
                            $.each(json.data, function(k, value) {
                                content += '<option value="' + value + '">' + value +
                                    '</option>'
                            });
                            content += '</select></div>'
                            $('#dates').html(content);
                            $('#periods').html('');
                            $('#fields').html('');
                            var n = $(document).height();
                            $('html, body').animate({
                                scrollTop: n
                            }, 1000);
                        }
                        $('.select2').select2();
                    },
                    error: function(xhr) {
                        console.log(xhr);

                    }
                });
            });
            // change date , get periods
            $('form').on('change', 'select[name=date]', function() {
                var tag = $(this);
                var partId = $('input[name=party_id]').val();
                $.ajax({
                    url: "{{ route('party.periods', $restaurant->id) }}",
                    method: "GET",
                    data: {
                        party_id: partId,
                        date: tag.val(),
                    },
                    success: function(json) {

                        if (json.status) {
                            var content =
                                '<div class="form-group">\
                                                                                                                            <label for="">{{ trans('messages.periods') }}</label>\
                                                                                                                            <select type="text" name="period_id" class="form-control  select2"><option selected value="" disabled >اختر موعد</option>';
                            $.each(json.data, function(k, value) {
                                content += '<option value="' + value.id + '">' + value
                                    .from_string + " {{ trans('dashboard.to') }} " +
                                    value.to_string +
                                    '</option>'
                            });
                            content +=
                                '</select>\
                                                                                                                        </div>'
                            $('#periods').html(content);
                            $('.select2').select2();
                            var n = $(document).height();
                            $('html, body').animate({
                                scrollTop: n
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);

                    }
                });

            });

            $('form').on('change', 'select[name=period_id]', function() {
                var tag = $(this);
                var partId = $('input[name=party_id]').val();
                $.ajax({
                    url: "{{ route('party.fields', $restaurant->id) }}",
                    method: "GET",
                    data: {
                        party_id: partId,
                    },
                    success: function(json) {
                        console.log(json);
                        if (json.status) {
                            $('#fields').html(json.data);
                            getTotalPrice();
                            $('.select2').select2();
                            var n = $('#dates').offset().top;
                            $('html, body').animate({
                                scrollTop: n
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);

                    }
                });


            });

            $('select[name=branch_id]').on('change', function() {
                window.location.replace("{{ route('party.page1', $restaurant->id) }}?branch_id=" + $(this)
                    .val());
            });
            $('.party-slider').owlCarousel({
                rtl: true,
                loop: true,
                margin: 20,
                nav: false,
                lazyLoad: true,
                items: 1,
                autoplay: true,
                autoplayTimeout: 3000
            });
        });
    </script>
@endpush

@include('website.' . session('theme_path') . 'silver.layout.scripts')

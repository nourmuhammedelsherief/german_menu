@php
    $table = isset($table->id) ? $table : null;
    $hideBranches = true;
@endphp
@include('website.' . session('theme_path') . 'silver.layout.header')


<style>
    body {
        background-color: {{ $restaurant->bio_color != null ? $restaurant->bio_color->background : '#FFF' }}  !important;
        background-image: url({{$restaurant->bio_color != null ? asset('/uploads/bio_backgrounds/' . $restaurant->bio_color->background_image) : ''}});
        /* Full height */
        height: 100%;

        /* Center and scale the image nicely */
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }

    #page-1 {
        clear: both;
    }

    .page-header {
        padding-top: 60px;
        position: relative;
    }

    #change-lang {
        position: absolute;
        top: 0px;
        left: 17px;
        display: flex;
        z-index: 2;;
        justify-content: center;
        /* font-size:18px; */
        color: #0f1117;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s ease;
    }

    body[dir=ltr] #change-lang {
        left: unset;
        right: 17px;
    }

    body[dir=ltr] .site-title {
        display: flex !important;
    }

    body[dir=ltr] .site-title a.img {
        float: unset;
        position: absolute;
        top: 0px;
        left: 10px;
    }

    body[dir=ltr] .site-title .restaurant-title {
        width: calc(100% - 30px);
    }

    .page-header .share-btn {
        position: absolute;
        top: 17px;
        right: 17px;
        display: flex;
        width: 40px;
        height: 40px;
        background-color: rgb(242, 242, 242);
        justify-content: center;
        padding-top: 10px;
        border-radius: 100%;
        border: 1px solid rgb(226, 226, 226);
        cursor: pointer;
        transition: 0.3s ease;
    }

    .page-header .share-btn:hover {
        background-color: #eaeaea;
        box-shadow: 1px 1px 10px #CCC;
        transition: 0.3s ease;
    }

    .page-header .share-btn i {
        font-size: 18px;
        color: rgb(79 79 79);


    }

    .page-header .log-container {
        width: 100px;
        height: 100px;
        margin: auto;
    }

    .page-header .log-container img {
        width: 100%;
        height: 100%;
        border-radius: 100%;
    }

    .page-body .item {
        background-color: {{ $restaurant->bio_color != null ? $restaurant->bio_color->sub_cats : '#FFF' }};
        color: {{ $restaurant->color != null ? $restaurant->color->main_heads : '#000' }};
        display: block;
        border-radius: 10px;
        margin: 20px 10px;
        padding: 5px 10px;
        position: relative;
    }

    .page-body .item :hover {
    }

    .page-body .item .image {
        position: absolute;
        top: 8px;
        left: 10px;
        width: 40px;
        height: 40px;
    }

    .page-body .item .image img {
        width: 100%;
        height: 100%;
        border-radius: 10px;
    }

    .page-body .item .description {

        text-align: center;
        /* width: calc(100% - 50px); */
        margin-top: 10px;
        margin-bottom: 10px;
        background-color: transparent;
        font-size: 1.2rem;
        vertical-align: middle;
        color: {{ $restaurant->bio_color != null ? $restaurant->bio_color->sub_cats_line : ($restaurant->color != null ? $restaurant->color->main_heads : '#000') }};
    }

    .dropdown {
        margin: 10px;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 10px;
        background-color: {{ $restaurant->color != null ? $restaurant->color->product_background : '#FFF' }};
    }

    .hide {
        display: none;
    }

    .item.main {
        position: relative;
    }

    .item.main .drop-icon {
        position: absolute;
        top: 15px;
    }

    .content {
        margin-top: 25px;;
    }

    .slider-description {
        font-size: 1.3rem;
        margin-bottom: 10px;
        position: absolute;
        bottom: 50px;
        width: 100%;
        z-index: 3;
        background-color: #ffffff78;
    }

    .page-header > .content {
        z-index: 2;
    }

    .new-slider.mainsld {
        margin-top: -100px;
    }

    .new-slider.mainsld .owl-dots {
        top: -41px !important;

    }

    [dir=ltr] .content .restaurant-title > h5 {
        text-align: right;
        margin-right: 10px;
    }

    .site-title {
        margin-top: -2rem !important;
    }
</style>

<div id="page">
    @php
        $sliderCount = $restaurant
            ->sliders()
            ->where('slider_type', 'contact_us')
            ->count();
    @endphp
    @include('website.' . session('theme_path') . 'silver.layout.head')
    <div class="page-header">

        <div class="single-slider mainsld owl-carousel new-slider"
             style="margin-top:{{ $sliderCount == 0 ? '25px !important' : '' }}">
            @include('website.' . session('theme_path') . 'silver.accessories.new_slider')
        </div>


        <div class="content">
            <div class="d-flex mt-n5 site-title">
                <div class="mt-n5">
                    @if ($table == null)
                        @if ($branch->main == 'true')
                            <a href="{{ url('/restaurants/' . $restaurant->name_barcode) }}" class=" shadow-xl ">
                                <img src="{{ asset('/uploads/restaurants/logo/' . $restaurant->logo) }}"
                                     style="width: 90px; height:90px; position: relative; z-index: 1;border: 1px dashed #f7b538;"/>
                            </a>
                        @else
                            <a href="{{ route('sliverHomeBranch', [$restaurant->name_barcode, $branch->name_barcode]) }}"
                               class=" shadow-xl ">
                                <img src="{{ asset('/uploads/restaurants/logo/' . $restaurant->logo) }}"
                                     style="width: 90px; height:90px; position: relative; z-index: 1;border: 1px dashed #f7b538;"/>
                            </a>
                        @endif
                    @else
                        @if ($table != null)
                            <a href="{{ route('sliverHomeTableBranch', [$restaurant->name_barcode, $table->foodics_id != null ? $table->foodics_id : $table->name_barcode]) }}"
                               class=" shadow-xl ">
                                <img src="{{ asset('/uploads/restaurants/logo/' . $restaurant->logo) }}"
                                     style="width: 90px; height:90px; position: relative; z-index: 1;border: 1px dashed #f7b538;"/>
                            </a>
                        @elseif($branch->main == 'true')
                            <a href="{{ route('sliverHomeTable', [$restaurant->name_barcode, $table->name_barcode]) }}"
                               class=" shadow-xl ">
                                <img src="{{ asset('/uploads/restaurants/logo/' . $restaurant->logo) }}"
                                     style="width: 90px; height:90px; position: relative; z-index: 1;border: 1px dashed #f7b538;"/>
                            </a>
                        @else
                            <a href="{{ route('sliverHomeTableBranch', [$restaurant->name_barcode, $table->name_barcode, $branch->name_barcode]) }}"
                               class=" shadow-xl ">
                                <img src="{{ asset('/uploads/restaurants/logo/' . $restaurant->logo) }}"
                                     style="width: 90px; height:90px; position: relative; z-index: 1;border: 1px dashed #f7b538;"/>
                            </a>
                        @endif
                    @endif

                    @if ($branch->state == 'open')
                        <p class="text-center"
                           style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }}">
                            <i class="fa fa-check-circle color-green1-dark "></i> @lang('messages.open')
                        </p>
                    @elseif($branch->state == 'closed')
                        <p class="text-center"
                           style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }}">
                            <i class="fa fa-check-circle color-red"></i>
                            @lang('messages.closed')
                        </p>
                    @elseif($branch->state == 'busy')
                        <p class="text-center"
                           style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }}">
                            <i class="fa fa-check-circle color-dark"></i>
                            @lang('messages.busy')
                        </p>
                    @endif
                </div>
                <div class="align-self-center pr-3 mt-1 restaurant-title" style="line-height: 23px;">
                    <h5 class="font-600 font-18 mb-1"
                        style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }}">
                        {{ isset($branch->id) ? $branch->name : (app()->getLocale() == 'ar' ? $restaurant->name_ar : $restaurant->name_en) }}
                    </h5>

                </div>

            </div>
            @include('website.' . session('theme_path') . 'silver.accessories.ads_popup')
            @if (
                $restaurant->socials->count() == 0 ||
                    $restaurant->socials->count() > 0 ||
                    $restaurant->deliveries->count() > 0 ||
                    $restaurant->sensitivities->count() > 0 ||
                    $restaurant->offers->count() > 0 ||
                    $restaurant->information_ar != null ||
                    $restaurant->information_en != null ||
                    $restaurant->res_branches->count() > 0)
                <div id="box" class=" mt-3 mb-n1" style="min-height: 100px;">

                    @php
                        $icons = isset($restaurant->id)
                            ? App\Models\RestaurantIcon::where('restaurant_id', $restaurant->id)
                                ->orderBy('sort', 'asc')
                                ->get()
                            : [];

                        $checkFixedIcons =
                            (isset($restaurant->id) and
                            App\Models\RestaurantIcon::where('restaurant_id', $restaurant->id)
                                ->whereNotNull('code')
                                ->count() >
                                0)
                                ? false
                                : true;
                    @endphp
                    @foreach ($icons as $item)
                        @if ($item->code == null)
                            <div class="icon-user itemCatTop">
                                <a href="{{ $item->link }}" target="_blank"
                                   class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                                    <img src="{{ asset($item->image_path) }}" class="home-custom-image" alt="">
                                </a>
                                <p class="font-600 font-13 mt-2"
                                   style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                    {{ $item->title }}</p>
                            </div>
                        @else
                            @switch($item->code)
                                @case('feedback')
                                @if ($restaurant->enable_feedback == 'true')
                                    <div class="icon-user itemCatTop">
                                        <a href="#" data-menu="menu-rate"
                                           class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                                            @if (!empty($item->image))
                                                <img src="{{ asset($item->image_path) }}" class="home-custom-image"
                                                     alt="">
                                            @else
                                                <i class="far fa-comment-alt"
                                                   style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                                            @endif
                                        </a>
                                        <p class="font-600 font-13 mt-2"
                                           style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                            {{ $item->title }}</p>
                                    </div>
                                @endif
                                @break

                                @case('call_phone')
                                @if ($restaurant->is_call_phone == 'true')
                                    <div class="icon-user itemCatTop">

                                        <a href="tel:{{ $restaurant->call_phone }}"
                                           class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                                            @if (!empty($item->image))
                                                <img src="{{ asset($item->image_path) }}" class="home-custom-image"
                                                     alt="">
                                            @else
                                                <i class="fas fa-phone"
                                                   style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                                            @endif

                                        </a>
                                        <p class="font-600 font-13 mt-2"
                                           style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                            {{ $item->title }}</p>

                                    </div>
                                @endif
                                @break

                                @case('whatsapp')
                                @if ($restaurant->is_whatsapp == 'true')
                                    <div class="icon-user itemCatTop">

                                        <a href="https://wa.me/{{ $restaurant->whatsapp_number }}" target="__blank"
                                           class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                                            @if (!empty($item->image))
                                                <img src="{{ asset($item->image_path) }}" class="home-custom-image"
                                                     alt="">
                                            @else
                                                <i class="fab fa-whatsapp"
                                                   style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                                            @endif

                                        </a>
                                        <p class="font-600 font-13 mt-2"
                                           style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                            {{ $item->title }}</p>

                                    </div>
                                @endif
                                @break

                                @case('party')
                                @if ($restaurant->enable_party == 'true' and empty($table))
                                    <div class="icon-user itemCatTop">

                                        <a href="{{ route('party.page1', $restaurant->id) }}"
                                           class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                                            @if (!empty($item->image))
                                                <img src="{{ asset($item->image_path) }}" class="home-custom-image"
                                                     alt="">
                                            @else
                                                <i class="fas fa-gift"
                                                   style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                                            @endif

                                        </a>
                                        <p class="font-600 font-13 mt-2"
                                           style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                            {{ $item->title }}</p>

                                    </div>
                                @endif
                                @break

                                @case('reservation')
                                @if ($restaurant->reservation_service == 'true' and empty($table))
                                    <div class="icon-user itemCatTop">

                                        <a href="{{ route('reservation.page1', $restaurant->id) }}"
                                           class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                                            @if (!empty($item->image))
                                                <img src="{{ asset($item->image_path) }}" class="home-custom-image"
                                                     alt="">
                                            @else
                                                <i class="far fa-ticket-alt"
                                                   style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                                            @endif

                                        </a>
                                        <p class="font-600 font-13 mt-2"
                                           style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                            {{ $item->title }}</p>

                                    </div>
                                @endif
                                @break

                                @default
                            @endswitch
                        @endif
                    @endforeach



                    @if ($checkFixedIcons)
                        @if ($restaurant->enable_feedback == 'true')
                            <div class="icon-user itemCatTop">
                                <a href="#" data-menu="menu-rate"
                                   class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                                    <i class="far fa-comment-alt"
                                       style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                                </a>
                                <p class="font-600 font-13 mt-2"
                                   style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                    @lang('messages.restaurant_feedback')</p>
                            </div>
                        @endif
                        @if ($restaurant->is_call_phone == 'true')
                            <div class="icon-user itemCatTop">

                                <a href="tel:{{ $restaurant->call_phone }}"
                                   class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                                    <i class="fas fa-phone"
                                       style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                                </a>
                                <p class="font-600 font-13 mt-2"
                                   style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                    @lang('messages.call')</p>

                            </div>
                        @endif
                        @if ($restaurant->is_whatsapp == 'true')
                            <div class="icon-user itemCatTop">

                                <a href="https://wa.me/{{ $restaurant->whatsapp_number }}" target="__blank"
                                   class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                                    <i class="fab fa-whatsapp"
                                       style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                                </a>
                                <p class="font-600 font-13 mt-2"
                                   style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                    @lang('messages.whatsapp')</p>

                            </div>
                        @endif
                        @if ($restaurant->reservation_service == 'true' and empty($table))
                            <div class="icon-user itemCatTop">

                                <a href="{{ route('reservation.page1', $restaurant->id) }}"
                                   class="shadow-xl icon icon-l rounded-xl color-white icon-border color-yellow2-dark">
                                    <i class="far fa-ticket-alt"
                                       style="color: {{ $restaurant->color == null ? '' : $restaurant->color->icons }} !important;"></i>
                                </a>
                                <p class="font-600 font-13 mt-2"
                                   style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                    @lang('messages.reservations')</p>

                            </div>
                        @endif

                    @endif


                </div>
            @endif
        </div>
    <!--<div class="share-btn"><img src="{{ asset('images/image.svg') }}" style="width:20px; height:20px; " alt=""></div>-->

        {{-- <a id="change-lang" href="{{ url('locale/' . (app()->getLocale() == 'ar' ? 'en' : 'ar')) }}"
            style="color : {{ $restaurant->color == null ? '' : $restaurant->color->options_description }} !important;">
            @if (app()->getLocale() == 'ar')
                English
            @else
                عربي
            @endif
        </a> --}}
    </div>
    {{-- <div class="page-header">
        <div class="log-container">
            <img src="{{asset($restaurant->image_path)}}" alt="">
        </div>
        <h1 class="text-center mt-3">{{ $restaurant->name }}</h1>
        <p class="text-center description px-2">{!! $restaurant->description !!}</p>
        <div class="share-btn"><img src="{{asset('images/image.svg')}}" style="width:20px; height:20px; " alt=""></div>
    </div> --}}

    <div class="page-body">
        @if (isset($contact->id))
            <h3 class="text-center"
                style="color : {{ $restaurant->color == null ? '' : $restaurant->color->options_description }} !important;">
                {{ $contact->name }}</h3>
        @endif
        @php
            if (isset($contact->id)):
                $items = $contact
                    ->items()
                    ->whereNull('main_id')
                    ->with('childs')
                    ->orderBy('sort')
                    ->get();
            else:
                $items = $restaurant
                    ->contactUsItems()
                    ->whereNull('link_id')
                    ->whereNull('main_id')
                    ->with('childs')
                    ->orderBy('sort')
                    ->get();
            endif;

        @endphp
        @foreach ($items as $item)
            @php
                $url = empty($item->url) ? '#' : $item->url;
                if ($item->childs->count() > 0 or !empty($item->description)) {
                    $url = '#';
                }
            @endphp
            @if ($item->status == 'true')
                <a class="item main dclose" href="{{ $url }}" target="_blank"
                   data-id="{{ $item->id }}"
                   style="background-color: {{ $restaurant->bio_color == null ? ($restaurant->color != null ? $restaurant->color->category_background : '#FFF') : $restaurant->bio_color->main_cats }}">
                    <div class="image">
                        <img src="{{ asset($item->image) }}" alt="">
                    </div>
                    <div class="description"
                         style="color : {{ $restaurant->bio_color == null ? ($restaurant->color != null ? $restaurant->color->main_heads : '#000') : $restaurant->bio_color->main_line }} !important;">
                        {{ $item->title }}</div>
                    @if ($item->childs->count() > 0 or !empty($item->description))
                        <span class="drop-icon"><i class="fas fa-chevron-down"></i></span>
                    @endif
                </a>
                @if ($item->childs->count() > 0 or !empty($item->description))
                    <div class="dropdown dropdown-{{ $item->id }} hide"
                         style="background-color: {{$restaurant->bio_color == null ? ($restaurant->color != null ? $restaurant->color->category_background : '#FFF') : $restaurant->bio_color->sub_background}}">
                        <p class="text-center"
                           style="color : {{ $restaurant->bio_color == null ? ($restaurant->color != null ? $restaurant->color->main_heads : '#000') : $restaurant->bio_color->sub_cats_line }} !important;">
                            {{ $item->description }}</p>
                        @foreach ($item->childs as $t)
                            <a class="item" href="{{ empty($t->url) ? '#' : $t->url }}" target="_blank">
                                <div class="image">
                                    <img src="{{ asset($t->image) }}" alt="">
                                </div>
                                <div class="description"
                                     style="color : {{ $restaurant->color == null ? '' : $restaurant->color->options_description }} !important;">
                                    {{ $t->title }}</div>
                                <p class="text-center">{{ $t->description }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endif
        @endforeach
        @php
            $sliders = $restaurant
                ->sliders()
                ->where('slider_type', 'contact_us_client')
                ->get();
        @endphp
        @if ($sliders->count() > 0)
            <a class="item main dclose" href="#" target="_blank" data-id="tt"
               style="background-color: {{ $restaurant->bio_color == null ? ($restaurant->color != null ? $restaurant->color->category_background : '#FFF') : $restaurant->bio_color->main_cats }}">
                <div class="image">
                    {{-- <img src="{{ asset($item->image) }}" alt=""> --}}
                </div>
                <div class="description"
                     style="color : {{ $restaurant->bio_color == null ? ($restaurant->color != null ? $restaurant->color->main_heads : '#000') : $restaurant->bio_color->main_line }} !important;">
                    {{ empty($restaurant->slider_down_contact_us_title) ? trans('dashboard.slider_contact_us_client') : $restaurant->slider_down_contact_us_title }}
                </div>

                <span class="drop-icon"><i class="fas fa-chevron-down"></i></span>

            </a>
            <div class="dropdown dropdown-tt hide"
                 style="background-color: {{$restaurant->bio_color == null ? null : $restaurant->bio_color->sub_background}}">
                <div class="single-slider mainsld owl-carousel new-slider">
                    @include('website.' . session('theme_path') . 'silver.accessories.contact_us_client_slider')
                </div>
            </div>
        @endif

    </div>
    <div class="menu-hider"></div>
</div>

@include('website.' . session('theme_path') . 'silver.layout.footer')
@include('website.' . session('theme_path') . 'silver.accessories.res_branches')
@include('website.' . session('theme_path') . 'silver.accessories.related')
@include('website.' . session('theme_path') . 'silver.layout.scripts')
<script>
    $(function () {
        console.log($(window).height());

        console.log($('#page-1').attr('style', 'min-height:' + ($(window).height() - 40) + "px !important"));
        $('.item.main').on('click', function () {
            var tag = $(this);
            console.log(tag.data());
            if (tag.hasClass('dclose')) {
                $('.dropdown-' + tag.data('id')).slideDown(200);
                tag.removeClass('dclose');
                tag.addClass('dopen');
                tag.find('.drop-icon').html('<i class="fas fa-chevron-up"></i>');
            } else {
                $('.dropdown-' + tag.data('id')).slideUp(200);
                tag.removeClass('dopen');
                tag.addClass('dclose');
                tag.find('.drop-icon').html('<i class="fas fa-chevron-down"></i>');
            }
        });
        $('.share-btn').on('click', function () {
            var url = "{{ route('contactUs', $restaurant->name_barcode) }}";
            if (navigator.share) {
                navigator.share({
                    title: '{{ $restaurant->name }}',
                    url: url,
                }).then(() => {
                    console.log('Thanks for sharing!');
                })
                    .catch(console.error);
            } else {
                console.log('share on web')
            }
        });
    });
</script>

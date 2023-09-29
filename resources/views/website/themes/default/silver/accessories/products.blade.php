<div class="card products  mb-0"
     style="background-color: {{ $restaurant->color == null ? '' : $restaurant->color->background }} !important">
    <script>
        $('ul.pagination').hide();
        $(function() {

            // $('.scrolling-pagination').jscroll({
            //     autoTrigger: true,
            //     padding: 0,
            //     nextSelector: 'a.t-page:last',
            //     contentSelector: 'div.scrolling-pagination',
            //     loadingFunction: function() {
            //         console.log('url : ' + $('.t-page').attr('href'));
            //         console.log('products count : ' + {{ $products->count() }});
            //     },
            //     callback: function() {
            //         // console.log($( '.pagination li.active + li a'));
            //         // $('#next-product-theme-1').remove();
            //         console.log($('.t-page').attr('href'));
            //         // $('.t-page').remove();
            //         $('[data-src]').lazy();
            //         // console.log('scrolling products');
            //     }
            // });
            // // console.log($( '.scrolling-pagination2 .pagination li.active + li a'));
            // $('.scrolling-pagination2').jscroll({
            //     autoTrigger: true,
            //     padding: 0,
            //     nextSelector: '.t-theme-2 .pagination li.active + li a',
            //     contentSelector: 'div.scrolling-pagination2',
            //     callback: function() {
            //         // console.log($('.scrolling-pagination2 ul.pagination'));
            //         $('.t-theme-2 ul.pagination').remove();
            //         $('[data-src]').lazy();
            //         // console.log('scrolling products');
            //     }
            // });

        });
    </script>

    <div class=" prodcontent product-theme-1 ">
        @if (isset($sCat->id))
            <h3 class="text-center mb-2 mt-1">{{ $sCat->name }}</h3>
        @else
            {{-- <h3 class="text-center mb-2 mt-1">No Cat</h3> --}}
        @endif
        @if ($products->count() > 0)
            <div class="scrolling-pagination">
                <div class="t-theme-1">
                    @foreach ($products as $productIndex => $product)
                        @if ($product->time == 'true')
                            <?php
                            $carbon = \Carbon\Carbon::now();
                            $current_day = $carbon->format('l');
                            $checkCategoryDay = \App\Models\ProductDay::where('product_id', $product->id)->first();
                            $categoryDay = \App\Models\ProductDay::with('day')
                                ->whereHas('day', function ($q) use ($current_day) {
                                    $q->where('name_en', $current_day);
                                })
                                ->where('product_id', $product->id)
                                ->first();
                            ?>
                            @if (($checkCategoryDay != null && $categoryDay != null) || $checkCategoryDay == null)
                                @if (check_time_between($product->start_at, $product->end_at))
                                    <div data-url="{{ route('product.show', [$restaurant->name_barcode, $product->id, $table != null ? $table->id : '']) }}"
                                         data-menu="menu-prodact-{{ $product->id }}"
                                         class="prod prod-theme-1 {{ (!$products->hasMorePages() and $products->count() == $productIndex + 1) ? 'last-product' : '' }} d-flex mb-3 shadow-l rounded-m"
                                         data-category_id="{{ $product->menu_category_id }}"
                                         style="max-height:150px; background-color: {{ $restaurant->color == null ? '' : $restaurant->color->product_background }} !important;">
                                        <div class="pb-1 p-2 product-redirect " onclick="console.log('test');"
                                             style=" width: calc(100% - 127px);"
                                             data-url="{{ route('product.show', [$restaurant->name_barcode, $product->id, $table != null ? $table->id : '']) }}"
                                             data-x-menu="menu-prodact-{{ $product->id }}">
                                            <a href="#"
                                               data-url="{{ route('product.show', [$restaurant->name_barcode, $product->id, $table != null ? $table->id : '']) }}"
                                               data-x-menu="menu-prodact-{{ $product->id }}" class="link-to-product">
                                                <h5 class="font-15 product-name font-600 pb-1"
                                                    style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                    {{ app()->getLocale() == 'ar' ? ($product->name_ar == null ? $product->name_en : $product->name_ar) : ($product->name_en == null ? $product->name_ar : $product->name_en) }}
                                                    @if ($product->poster != null)
                                                        <img data-src="{{ asset('/uploads/posters/' . $product->poster->poster) }}"
                                                             height="30" width="30" class="poster-image">
                                                    @endif
                                                </h5>
                                            </a>
                                            <input type="hidden" id="btnClickedValue" name="btnClickedValue"
                                                   value="{{ $product->id }}" />

                                            <p class="font-13 mb-1 link-to-product"
                                               style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                                {!! app()->getLocale() == 'ar' ? strip_tags($product->description_ar) : strip_tags($product->description_en) !!}
                                            </p>
                                            <div class="d-flex link-to-product">
                                                {{-- @if ($product->poster != null)
                                                <i>
                                                    <img
                                                        data-src="{{asset('/uploads/posters/' . $product->poster->poster)}}"
                                                        height="40"
                                                        width="40">
                                                </i>
                                            @endif --}}
                                                <p class="mr-auto font-12 font-600 mb-0 ">

                                                <div class="oldprice font-600">
                                                    @if ($product->price_before_discount != null)
                                                        <span
                                                                style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                            <del> {{ $product->price_before_discount }}
                                                                {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}</del>
                                                        </span>
                                                    @endif
                                                    @php
                                                        $product_sizes = \App\Models\ProductSize::whereProductId($product->id)->get();
                                                    @endphp
                                                    @if ($product->price != 0 && $product_sizes->count() == 0)
                                                        <span
                                                                style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                            {{ $product->price }}
                                                            {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                        </span>
                                                    @elseif($product->price != 0 && $product_sizes->count() > 0)
                                                        <span
                                                                style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                            {{ $product->price }}
                                                            {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }},
                                                            @foreach ($product_sizes as $product_size)
                                                                {{ $product_size->price }}
                                                                {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                                ,
                                                            @endforeach
                                                        </span>
                                                    @elseif($product->price == 0 && $product_sizes->count() > 0)
                                                        <span
                                                                style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                            @foreach ($product_sizes as $product_size)
                                                                {{ $product_size->price }}
                                                                {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                                ,
                                                            @endforeach
                                                        </span>
                                                    @endif

                                                </div>
                                                </p>
                                            </div>

                                        </div>
                                        <div class="mr-auto product-image"
                                             style=" overflow: hidden;   width: 127px;  text-align: center;  border-radius: 15px 15px 15px 15px;">
                                            @if ($product->foodics_image != null)
                                                <img src="{{ $product->foodics_image }}" class=" shadow-xl rounded-m"
                                                     width="127" height="127" style="    ">
                                            @else
                                                <img data-src="{{ empty($product->photo) ? asset($restaurant->image_path) : asset($product->image_path) }}"
                                                     class=" shadow-xl rounded-m" width="127" height="127"
                                                     style="    ">
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @else
                            <div data-url="{{ route('product.show', [$restaurant->name_barcode, $product->id, $table != null ? $table->id : '']) }}"
                                 data-menu="menu-prodact-{{ $product->id }}"
                                 class="prod prod-theme-1 {{ (!$products->hasMorePages() and $products->count() == $productIndex + 1) ? 'last-product' : '' }}  d-flex mb-3 shadow-l rounded-m"
                                 data-category_id="{{ $product->menu_category_id }}"
                                 style="max-height:150px; background-color: {{ $restaurant->color == null ? '' : $restaurant->color->product_background }} !important;">
                                <div class="pb-1 p-2   product-redirect" style="width: calc(100% - 127px);"
                                     data-url="{{ route('product.show', [$restaurant->name_barcode, $product->id, $table != null ? $table->id : '']) }}"
                                     data-x-menu="menu-prodact-{{ $product->id }}">
                                    <a href="#" class="link-to-product"
                                       data-url="{{ route('product.show', [$restaurant->name_barcode, $product->id, $table != null ? $table->id : '']) }}"
                                       data-x-menu="menu-prodact-{{ $product->id }}">
                                        <h5 class="font-15 product-name font-600 pb-1"
                                            style="margin-bottom: 0;
                                                    padding-bottom: 0 !important;color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                            {{ app()->getLocale() == 'ar' ? ($product->name_ar == null ? $product->name_en : $product->name_ar) : ($product->name_en == null ? $product->name_ar : $product->name_en) }}

                                            @if ($product->poster != null)
                                                <img data-src="{{ asset('/uploads/posters/' . $product->poster->poster) }}"
                                                     height="30" width="30" class="poster-image">
                                            @endif
                                        </h5>
                                        </h5>
                                    </a>
                                    <input type="hidden" id="btnClickedValue" name="btnClickedValue"
                                           value="{{ $product->id }}" />
                                    @if ($product->calories != null)
                                        <span class="pl-1 calories" style="margin:0 6px;">
                                            <span
                                                    style="color: {{ $product->restaurant->color == null ? '' : $product->restaurant->color->main_heads }} !important">
                                                {{ trans('messages.calories_des', ['num' => $product->calories]) }}
                                            </span>
                                        </span>
                                    @endif
                                    <p class="font-13 mb-1 link-to-product"
                                       style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                        {!! app()->getLocale() == 'ar' ? strip_tags($product->description_ar) : strip_tags($product->description_en) !!}
                                    </p>
                                    <div class="d-flex link-to-product">
                                        {{-- @if ($product->poster != null)
                                        <i>
                                            <img data-src="{{asset('/uploads/posters/' . $product->poster->poster)}}"
                                                 height="40"
                                                 width="40">
                                        </i>
                                    @endif --}}
                                        @php
                                            $product_sensitivities = \App\Models\ProductSensitivity::whereProductId($product->id)->get();
                                        @endphp
                                        @if ($product_sensitivities->count() > 0)
                                            @foreach ($product_sensitivities as $product_sensitivity)
                                                <i>
                                                    <img data-src="{{ asset('/uploads/sensitivities/' . $product_sensitivity->sensitivity->photo) }}"
                                                         height="25" width="25" class="sens-image">
                                                </i>
                                            @endforeach
                                        @endif

                                        <p class="mr-auto font-12 font-600 mb-0 ">
                                        {{-- @if ($product->calories != null)
                                    <span class="pl-4"
                                          style="color: {{$restaurant->color == null ? '' : $restaurant->color->main_heads}} !important">
                            {{$product->calories}}
                            <i class="fa fa-fire pr-1 color-red2-dark"></i>
                        </span>
                            @endif --}}
                                        <div class="oldprice font-600">
                                            @if ($product->price_before_discount != null)
                                                <span
                                                        style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                    <del> {{ $product->price_before_discount }}
                                                        {{ app()->getLocale() == 'ar' ? $product->branch->country->currency_ar : $product->branch->country->currency_en }}</del>
                                                </span>
                                            @endif
                                            @php
                                                $product_sizes = \App\Models\ProductSize::whereProductId($product->id)->get();
                                            @endphp
                                            @if ($product->price != 0 && $product_sizes->count() == 0)
                                                <span
                                                        style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                    {{ $product->price }}
                                                    {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                </span>
                                            @elseif($product->price != 0 && $product_sizes->count() > 0)
                                                <span
                                                        style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                    {{ $product->price }}
                                                    {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }},
                                                    @foreach ($product_sizes as $product_size)
                                                        {{ $product_size->price }}
                                                        {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                        ,
                                                    @endforeach
                                                </span>
                                            @elseif($product->price == 0 && $product_sizes->count() > 0)
                                                <span
                                                        style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                    @foreach ($product_sizes as $product_size)
                                                        {{ $product_size->price }}
                                                        {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                        ,
                                                    @endforeach
                                                </span>
                                            @endif

                                        </div>
                                        </p>
                                    </div>

                                </div>
                                <div class="mr-auto product-image"
                                     style=" overflow: hidden;   width: 127px;  text-align: center;  border-radius: 15px 15px 15px 15px;display: flex;
                                 justify-content: center;
                                 align-items: center;">

                                    @if ($product->foodics_image != null)
                                        <img src="{{ $product->foodics_image }}" class=" shadow-xl rounded-m"
                                             width="127" height="127" style="    ">
                                    @else
                                        <img data-src="{{ empty($product->photo) ? asset($restaurant->image_path) : asset($product->image_path) }}"
                                             class=" shadow-xl rounded-m" width="127" height="127" style="    ">
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($table)
                            <div id="menu-prodact-{{ $product->id }}"
                                 class="menu  menu-box-bottom product-menu menu-box-detached "
                                 data-menu-load="{{ route('loadMenuProduct', [$product->id, $table->id]) }}"
                                 data-menu-height="100%" data-menu-effect="menu-over"></div>
                        @else
                            <div id="menu-prodact-{{ $product->id }}"
                                 class="menu menu-box-bottom product-menu menu-box-detached "
                                 data-menu-load="{{ route('loadMenuProduct', $product->id) }}"></div>
                        @endif
                    @endforeach
                    @if ($products->hasMorePages())
                        <div class="paginate-t">
                            <a class="t-page" href="{{ $products->nextPageUrl() }}&theme=1"></a>
                        </div>
                    @endif
                </div>



            </div>
        @else
            <br>
            <h3 style="background-color: {{ $restaurant->color == null ? '' : $restaurant->color->product_background }} !important;"
                class="text-center">@lang('messages.no_products')</h3>
        @endif
    </div>

    <div class="product-theme-2">
        @if ($products->count() > 0)
            <div class="scrolling-pagination">
                <div class="t-theme-2">
                    @foreach ($products as $product)
                        @if ($product->isTime() and (isset($product->menu_category->id) and $product->menu_category->isTime()))
                            <div class="product-item prod-theme-2"
                                 style="background-color: {{ isset($restaurant->color->id) ? $restaurant->color->product_background : 'transparent' }}"
                                 data-url="{{ route('product.show', [$restaurant->name_barcode, $product->id, $table != null ? $table->id : '']) }}"
                                 data-menu="menu-prodact2-{{ $product->id }}"
                                 data-category_id="{{ $product->menu_category_id }}">
                                <div class="product-image">
                                    @if ($product->foodics_image != null)
                                        <img data-src="{{ $product->foodics_image }}">
                                    @else
                                        <img
                                                data-src="{{ empty($product->photo) ? asset($restaurant->image_path) : asset($product->image_path) }}">
                                    @endif
                                </div>
                                <div class="product-content">
                                    <div class="row">
                                        <div class="col-7">
                                            <h3 class="title"
                                                style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                {{ $product->name }}</h3>
                                            @if ($product->calories != null)
                                                <span class="pl-1 calories">
                                                    <span
                                                            style="color: {{ $product->restaurant->color == null ? '' : $product->restaurant->color->main_heads }} !important">
                                                        {{ trans('messages.calories_des', ['num' => $product->calories]) }}

                                                    </span>
                                                </span>
                                            @endif
                                        </div>
                                        @if ($product->sensitivities()->count() > 0)
                                            <div class="col-5 sens text-left">
                                                @foreach ($product->sensitivities as $item)
                                                    <i>
                                                        <img data-src="{{ asset('/uploads/sensitivities/' . $item->sensitivity->photo) }}"
                                                             height="25" width="25" class="sens-image">
                                                    </i>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="col-7">
                                            <p class="description"
                                               style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                                {!! strip_tags($product->description) !!}</p>
                                        </div>

                                        <div class="col-5 text-left">
                                            @if ($product->poster != null)
                                                <img data-src="{{ asset('/uploads/posters/' . $product->poster->poster) }}"
                                                     height="30" width="30" class="poster-image">
                                            @endif
                                            <div class="oldprice font-600">
                                                @if ($product->price_before_discount != null)
                                                    <span
                                                            style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                        <del> {{ $product->price_before_discount }}
                                                            {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}</del>
                                                    </span>
                                                @endif
                                                @php
                                                    $product_sizes = \App\Models\ProductSize::whereProductId($product->id)->get();
                                                @endphp
                                                @if ($product->price != 0 && $product_sizes->count() == 0)
                                                    <span
                                                            style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                        {{ $product->price }}
                                                        {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                    </span>
                                                @elseif($product->price != 0 && $product_sizes->count() > 0)
                                                    <span
                                                            style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                        {{ $product->price }}
                                                        {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }},

                                                        @foreach ($product_sizes as $index => $product_size)
                                                            @if ($index == 0)
                                                                <br>
                                                            @endif
                                                            {{ $product_size->price }}
                                                            {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                            ,
                                                        @endforeach
                                                    </span>
                                                @elseif($product->price == 0 && $product_sizes->count() > 0)
                                                    <span
                                                            style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                        @foreach ($product_sizes as $index => $product_size)
                                                            @if ($index == 0)
                                                                <br>
                                                            @endif
                                                            {{ $product_size->price }}
                                                            {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                            ,
                                                            @if ($index + 1 != $product_sizes->count())
                                                                <br>
                                                            @endif
                                                        @endforeach
                                                    </span>
                                                @endif

                                            </div>



                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($table)
                                <div id="menu-prodact2-{{ $product->id }}"
                                     class="menu  menu-box-bottom product-menu menu-box-detached "
                                     data-menu-load="{{ route('loadMenuProduct', [$product->id, $table->id]) }}"
                                     data-menu-height="100%" data-menu-effect="menu-over"></div>
                            @else
                                <div id="menu-prodact2-{{ $product->id }}"
                                     class="menu menu-box-bottom product-menu menu-box-detached "
                                     data-menu-load="{{ route('loadMenuProduct', $product->id) }}"></div>
                            @endif
                        @endif
                    @endforeach
                    @if ($products->hasMorePages())
                        <div class="paginate-t">
                            <a class="t-page" href="{{ $products->nextPageUrl() }}&theme=2"></a>
                        </div>
                    @endif
                </div>

            </div>
        @else
            <br>
            <h3 style="background-color: {{ $restaurant->color == null ? '' : $restaurant->color->product_background }} !important;"
                class="text-center">@lang('messages.no_products')</h3>
        @endif
    </div>

    <div class="product-theme-3">

        @if ($products->count() > 0)

            <div class="scrolling-pagination">
                <div class="row">
                    @foreach ($products as $product)
                        @if ($product->isTime() and (isset($product->menu_category->id) and $product->menu_category->isTime()))
                            <div class="product-item col-6 prod-theme-3"
                                 data-url="{{ route('product.show', [$restaurant->name_barcode, $product->id, $table != null ? $table->id : '']) }}"
                                 data-menu="menu-prodact3-{{ $product->id }}"
                                 data-category_id="{{ $product->menu_category_id }}">
                                <div class="content">
                                    <div
                                            style="background-color: {{ isset($restaurant->color->id) ? $restaurant->color->product_background : 'transparent' }}">
                                        <div class="product-image">
                                            @if ($product->foodics_image != null)
                                                <img src="{{ $product->foodics_image }}">
                                            @else
                                                <img
                                                        data-src="{{ empty($product->photo) ? asset($restaurant->image_path) : asset($product->image_path) }}">
                                            @endif
                                        </div>
                                        <div class="product-content">
                                            <div class="c">
                                                <div class="">
                                                    <h3 class="title"
                                                        style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                        {{ $product->name }}</h3>
                                                    @if ($product->calories != null)
                                                        <span class="pl-1 calories">
                                                            <span
                                                                    style="color: {{ $product->restaurant->color == null ? '' : $product->restaurant->color->main_heads }} !important">
                                                                {{ trans('messages.calories_des', ['num' => $product->calories]) }}

                                                            </span>
                                                        </span>
                                                    @endif
                                                </div>
                                                @if ($product->sensitivities()->count() > 0)
                                                    <div class=" sens text-left">
                                                        @foreach ($product->sensitivities as $item)
                                                            <i>
                                                                <img data-src="{{ asset('/uploads/sensitivities/' . $item->sensitivity->photo) }}"
                                                                     height="25" width="25" class="sens-image">
                                                            </i>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if (!empty($product->description))
                                                    <div class="">
                                                        <p class="description"
                                                           style="color: {{ $restaurant->color == null ? '' : $restaurant->color->options_description }}">
                                                            {!! strip_tags($product->description) !!}</p>
                                                    </div>
                                                @endif
                                                <div class=" text-left"
                                                     style="margin-top: 14px;
                                                position: relative;">
                                                    @if ($product->poster != null)
                                                        <img data-src="{{ asset('/uploads/posters/' . $product->poster->poster) }}"
                                                             height="30" width="30" class="poster-image">
                                                    @endif
                                                    <div class="oldprice font-600">
                                                        @if ($product->price_before_discount != null)
                                                            <span
                                                                    style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                                <del> {{ $product->price_before_discount }}
                                                                    {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}</del>
                                                            </span>
                                                        @endif
                                                        @php
                                                            $product_sizes = \App\Models\ProductSize::whereProductId($product->id)->get();
                                                        @endphp
                                                        @if ($product->price != 0 && $product_sizes->count() == 0)
                                                            <span
                                                                    style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                                {{ $product->price }}
                                                                {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                            </span>
                                                        @elseif($product->price != 0 && $product_sizes->count() > 0)
                                                            <span
                                                                    style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                                {{ $product->price }}
                                                                {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }},
                                                                @foreach ($product_sizes as $index => $product_size)
                                                                    @if ($index == 0)
                                                                        <br>
                                                                    @endif
                                                                    {{ $product_size->price }}
                                                                    {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                                    ,
                                                                @endforeach
                                                            </span>
                                                        @elseif($product->price == 0 && $product_sizes->count() > 0)
                                                            <span
                                                                    style="color: {{ $restaurant->color == null ? '' : $restaurant->color->main_heads }} !important">
                                                                @foreach ($product_sizes as $index => $product_size)
                                                                    @if ($index == 0)
                                                                        <br>
                                                                    @endif
                                                                    {{ $product_size->price }}
                                                                    {{ app()->getLocale() == 'ar' ? $restaurant->country->currency_ar : $restaurant->country->currency_en }}
                                                                    ,
                                                                @endforeach
                                                            </span>
                                                        @endif

                                                    </div>



                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @if ($table)
                                <div id="menu-prodact3-{{ $product->id }}"
                                     class="menu  menu-box-bottom product-menu menu-box-detached "
                                     data-menu-load="{{ route('loadMenuProduct', [$product->id, $table->id]) }}"
                                     data-menu-height="100%" data-menu-effect="menu-over"></div>
                            @else
                                <div id="menu-prodact3-{{ $product->id }}"
                                     class="menu menu-box-bottom product-menu menu-box-detached "
                                     data-menu-load="{{ route('loadMenuProduct', $product->id) }}"></div>
                            @endif
                        @endif
                    @endforeach
                </div>
                @if ($products->hasMorePages())
                    <div class="paginate-t">
                        <a class="t-page" href="{{ $products->nextPageUrl() }}&theme=3"></a>
                    </div>
                @endif
            </div>
        @else
            <br>
            <h3 style="background-color: {{ $restaurant->color == null ? '' : $restaurant->color->product_background }} !important;"
                class="text-center">@lang('messages.no_products')</h3>
        @endif
    </div>

</div>
<style>
    .product-image {
        background-color: #EEE;

    }

    .product-name {
        position: relative;
        padding-left: 30px;
    }

    [dir=ltr] .product-name {
        padding-left: 0;
        padding-right: 30px;
    }

    .product-name .poster-image {
        position: absolute;
        top: 3px;
        left: 2px;
    }

    .sens-image {
        border-radius: 100%;
        border: 1px solid #CCC;
        box-shadow: 1px 1px 10px #CCC;
    }
</style>
<script>
    var checkIsMore = false;
    $(function() {

        $('[data-src]').lazy();
        console.log('here');
        @if ($restaurant->enable_fixed_category == 'true')
        $('.prodcontent .prod[data-menu]').on('click', function() {
            $('#xcategories').addClass('fixedIndex-2').removeClass('fixedIndex-3');
            $('#footer-bar').addClass('fixedIndex-2').removeClass('fixedIndex-3');
            console.log('category -x');

        });
        @endif
        $('.link-to-product').on('click', function() {
            var item = $(this).parent().parent();
            if (item.data('xmenu') && item.data('url'))
                window.location.replace(item.data('url'));
        });


    });
</script>

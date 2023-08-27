@include('website.' . session('theme_path') . 'silver.layout.header')

<div id="preloader">
    <div class="spinner-border color-highlight" role="status"></div>
</div>

<div id="page">


    <div class="card header-card shape-rounded" data-card-height="100">
        <div class="card-overlay bg-highlight opacity-95"></div>
        <div class="card-overlay dark-mode-tint"></div>
        <div class="card-bg"></div>


    </div>

    <div class="pb-5">

        @if ($branch->main == 'true')
            <a href="{{ route('sliverHome', $restaurant->name_barcode) }}"
                class="icon icon-xs rounded-xl color-white border-gray1-dark icon-border backwhiteop color-black"
                style="
                     position: absolute;
                     left: 11px;
                     top: 29px;z-index: 999;">
                <i class="fa fa-chevron-left"></i>
            </a>
        @else
            <a href="{{ route('sliverHomeBranch', [$restaurant->name_barcode, $branch->name_barcode]) }}"
                class="icon icon-xs rounded-xl color-white border-gray1-dark icon-border backwhiteop color-black"
                style="
                     position: absolute;
                     left: 11px;
                     top: 29px;z-index: 999;">
                <i class="fa fa-chevron-left"></i>
            </a>
        @endif


    </div>

    <div class="card mr-0 ml-0 rounded-l">

        <h1 class="text-right title">طلباتي</h1>
        {{-- <p class="mb-1 mt-4 text-center"><img src="{{asset('images/success.gif')}}" style="max-width: 100px;"/></p> --}}


        <div class="content mt-0 pt-1">
            @php
                // if (count($orders) == 0) {
                //     $orders[] = $order;
                // }
            @endphp
            @foreach ($orders as $order)
                @php
                    $details = $order->details[0];
                    $branch = $details->foodics_branch;
                    $items = $order->details;
                    
                @endphp

                <div class="xorder">
                    <a href="{{ route('silverfoodicsOrderDetails', $order->id) }}">
                        <h3>رقم الطلب #{{ $order->id }}</h3>
                        <p class="datetime">{{ date('Y-m-d - h:i A', strtotime($order->created_at)) }}</p>
                        <div class="order-status">
                            {!! $order->getFoodicsStatusHtml() !!}
                        </div>
                        <div class="products">
                            <table>
                                <tbody>
                                    @php
                                        $totalPrice = 0;
                                    @endphp
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>{{ $item->product_count }} - {{ $item->product->name }}</td>

                                            <td>{{ $item->total_price }}</td>
                                            @php
                                                $totalPrice += $item->total_price;
                                            @endphp
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="final-price text-left">المجموع <span>{{ $totalPrice }}</span> </div>
                    </a>
                </div>
            @endforeach
            @if(count($orders) == 0):
                <h3 class="text-center" style="margin-top:100px;margin-bottom:100px;">لا يوجد طلبات</h3>
            @endif
            <div class="text-center">
                @if ($branch->main == 'true')
                    <a href="{{ route('sliverHome', $restaurant->name_barcode) }}"
                        class="btn btn-l bg-highlight rounded-sm shadow-xl text-uppercase font-900">
                        @lang('messages.menu_back')
                    </a>
                @else
                    <a href="{{ route('sliverHomeBranch', [$restaurant->name_barcode, $branch->name_barcode]) }}"
                        class="btn btn-l bg-highlight rounded-sm shadow-xl text-uppercase font-900">
                        @lang('messages.menu_back')
                    </a>
                @endif
            </div>


        </div>


    </div>


</div>


<style>
    .card {
        background-color: {{ !isset($restaurant->color->id) ? '#FFF' : $restaurant->color->background }} !important;
    }

    .title {
        margin-right: 15px;
        margin-bottom: 30px;
    }

    .xorder,
    .xorder *,
    .title {
        color: {{ !isset($restaurant->color->id) ? '#000' : $restaurant->color->main_heads }} !important;
    }

    .xorder {
        padding: 20px;
        border-radius: 10px;
        /* margin-left:20px;
            margin-right:20px; */
        position: relative;
        margin-bottom: 30px;
        border: 1px solid {{ !isset($restaurant->color->id) ? '#000' : $restaurant->color->main_heads }};


    }

    .xorder .products table {
        width: 100%;
    }

    .xorder .products table td:last-child {
        text-align: left;
    }

    .xorder .products table td {
        /* color :  */
    }

    .datetime {
        margin-bottom: 10px;
    }

    .final-price {
        margin-top: 20px;
    }

    .final-price span {
        font-size: 22px;
    }

    .order-status {
        position: absolute;
        top: 19px;
        left: 18px;

    }
    .badge-primary , 
    .badge-success , 
    .badge-info , 
    .badge-danger , 
    .badge-warning{
        color : #FFF !important;
    }

    .order-status>span {
        font-size: 16px;
        padding: 0.25em 10px;
        /* border-radius: 20px; */
    }
</style>
@include('website.' . session('theme_path') . 'silver.layout.scripts')

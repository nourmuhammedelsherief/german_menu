<!DOCTYPE html>
<html dir="rtl">

<head>
    <title>طلب حفلة رقم {{ $order->id }}</title>
    <style>
        * {
            text-align: right;
        }

        .small {
            font-weight: normal;
        }

        .go-btn {
            padding: 10px 20px;
            background-color: #2370d5;
            color: #FFF !important;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>

</head>

<body>
    <h1 class="">طلب حفلة رقم {{ $order->id }}</h1>
    <h3> اسم الحفلة : <span class="small">{{ $order->party->title }}</span></h3>
    <h3> الفرع : <span class="small">{{ $order->branch->name }}</span></h3>
    
    <h3> تاريخ الحفلة : <span class="small">{{ $order->date }}</span></h3>
    <h3> وقت الحفلة : <span class="small">{{ $order->from_string }} الي {{ $order->to_string }}</span></h3>
    <h3>رقم الهاتف : <span class="small">{{ $order->user->phone_number }}</span></h3>
    <a class="go-btn" target="__blank" href="{{ route('restaurant.party-order.index') }}">عرض طلبات الحفلات</a>
    <p style="margin-top:100px;">Thank you</p>
</body>

</html>

<!DOCTYPE html>
<html dir="rtl">

<head>
    <title>طلب حجز رقم {{ $order->id }}</title>
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
    <h1 class="">طلب حجز رقم {{ $order->id }}</h1>
    <h3> الفرع : <span class="small">{{ $order->table->branch->name }}</span></h3>
    <h3> المكان : <span class="small">{{ $order->table->place->name }}</span></h3>
    <h3> تاريخ الحجز : <span class="small">{{ $order->date }}</span></h3>
    <h3> وقت الحجز : <span class="small">{{ $order->from_string }} الي {{ $order->to_string }}</span></h3>
    <h3>رقم الهاتف : <span class="small">{{ $order->user_phone }}</span></h3>
    <a class="go-btn" target="__blank" href="{{ route('restaurant.reservation.index') }}">عرض طلبات الحجوزات</a>
    <p>Thank you</p>
</body>

</html>

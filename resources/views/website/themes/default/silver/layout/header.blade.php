@php
    if(isset($restaurant->id)){
        $pageTitle = app()->getLocale() == 'ar' ? $restaurant->name_ar : $restaurant->name_en;
        $pageIcon = asset('uploads/restaurants/logo/' . $restaurant->logo);
    }
@endphp
<!DOCTYPE HTML>
<html lang="ar" style="background: #f5f5f5;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover"/>
   
    <title>{{(isset($pageTitle) and strlen($pageTitle) > 1) ? $pageTitle : trans('messages.appName')}} </title>
    <meta name="_token" content="{{ csrf_token() }}" />

    @if(isset($pageIcon) and !empty($pageIcon))
        <link rel="icon" href="{{ $pageIcon }}"   type="image/x-icon">
        <link rel="icon" href="{{ $pageIcon }}" type="image/png">
        
    @else
        <link rel="icon" href="{{ URL::asset('/uploads/img/logo.png') }}" type="image/x-icon">
    @endif
    <script type="text/javascript" src="{{asset('scripts/jquery.js')}}"></script>
    @if(app()->getLocale() == 'en')
    {{-- <link rel="stylesheet" type="text/css" href="{{asset('styles/bootstrapx.css')}}"> --}}
    <link rel="stylesheet" type="text/css" href="{{asset('styles/bootstrap/css/bootstrap-rtl.css')}}">
    

    @else
    <link rel="stylesheet" type="text/css" href="{{asset('styles/bootstrap.css')}}">
    @endif
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/splide/css/splide.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('styles/toastr.css')}}">
    <style>
         :root {
            --rest-icon-color : {{!empty($restaurant->color) ? $restaurant->color->icon : '#f7b538'}}; 
            --rest-background-color : {{!empty($restaurant->color) ? $restaurant->color->background : '#FFF'}} ;
            --rest-main-header-color : {{!empty($restaurant->color) ? $restaurant->color->main_heads : '#FFF'}} ;
            --rest-logo : url('{{asset($restaurant->image_path)}}') ;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{asset('styles/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('styles/style_ltr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('styles/global.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('fonts/css/fontawesome-all.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/vanilla-calendar/vanilla-calendar.min.css')}}">


    @stack('styles')
    
    <style>
       
        
        .menu , 
        .menu .form-control , 
        .menu .sensitive .card{
            background-color: var(--rest-background-color) !important;
        }
        .menu.ad-menu{
            background-color: transparent !important;
        }
        .menu .footer-content{
            border: 0;
        }
    .lds-ripple div {
        border: 4px solid {{(!isset($restaurant->color) or empty($restaurant->color)) ? '' : $restaurant->color->icons }} !important;
    }
    .mobile-width{
        max-width: 600px !important;
    }
    </style>
    @yield('style')
    
        <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-T9PFN6PT');</script>
<!-- End Google Tag Manager -->
</head>
    @php
        $tt = isset($restaurant->id) ? App\Models\RestaurantCode::where('restaurant_id' , $restaurant->id)->get() : [];
    @endphp
    @foreach ($tt as $item)
        {!! $item->header !!}
    @endforeach
    <!-- Restaurant tags end -->

@if($restaurant != null)
    <body class="theme-light" data-highlight="yellow2" dir="ltr" style="max-width: 1000px; width:99%; margin:auto; background-color: {{$restaurant->color == null ? '' : $restaurant->color->background}};">
@else
    <body class="theme-light" data-highlight="yellow2" dir="ltr" >
@endif
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T9PFN6PT"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->


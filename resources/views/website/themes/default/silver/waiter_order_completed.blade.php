@include('website.'.session('theme_path').'silver.layout.header')
{{--@include('website.'.session('theme_path').'silver.layout.head')--}}


<style>
    body {
        position: relative;
        background-color: transparent;
    }

    .header-card {
        z-index: 1;
        top: -40px;
    }
</style>


<div class="content" style="
width: 100%;
/* height: 40vh; */
margin: 0;margin-bottom:10%;background-color:transparent !important">
    <p style="margin-top: 60%;
    font-size: 1.4rem;
    font-weight: bold;
    text-align: center;
    margin-bottom: 50px;">تم ارسال طلبك الي الويتر
        وسيتم تلبية طلبكم في اسرع وقت ممكن 🌹🙏
            </p>
        <div class="text-center">
            <a href="{{route('sliverHomeTable' , [$restaurant->name_barcode , $table->name_barcode])}}" class="btn btn-primary">{{ trans('messages.menu_back') }}</a>
        </div>
</div>
@include('website.'.session('theme_path').'silver.layout.footer')
@include('website.'.session('theme_path').'silver.layout.scripts')

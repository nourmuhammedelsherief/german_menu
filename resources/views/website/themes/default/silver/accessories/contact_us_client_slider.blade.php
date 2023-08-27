
@if($sliders->count() > 0)
    @foreach($sliders as $slider)
        @if($slider->type == 'youtube' )
        <div id="slider-{{$slider->id}}"></div>
        <div class="slider-description text-center">
            {{$slider->description}}
        </div>
        @elseif($slider->type == 'image')
        <div>
            <div class="card shadow-xl" data-card-height="210" style="background-image: url({{asset('/uploads/sliders/' . $slider->photo)}});background-size: cover;    background-repeat: no-repeat;margin-bottom:10px;">
                <div class="card-overlay "></div>
                <div class="card-overlay"></div>
            </div>
            <div class="slider-description text-center">
                {{$slider->description}}
            </div>
        </div>
        @endif

       
    @endforeach
@endif


@push('scripts')
<script>
       
    $(function(){
      
     
        
    });
</script>
    
@endpush

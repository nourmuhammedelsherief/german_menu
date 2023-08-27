@php
    $sliderType = 'slider';
    
    $sliders = $restaurant
        ->sliders()
        ->where('slider_type', 'contact_us')
        ->orderBy('created_at', 'desc')
        ->get();
    foreach ($sliders as $item):
        if ($item->type == 'local_video'):
            $sliderType = $item->type;
            $sliderVideo = $item;
            break;
        elseif ($item->type == 'youtube'):
            $sliderType = $item->type;
            $sliderVideo = $item;
            break;
        endif;
    endforeach;
    
@endphp
@if ($sliderType == 'local_video')
    <div>
        <video src="{{ asset($sliderVideo->photo) }}" controls autoplay
            style="width:100%;max-height:270px;margin-bottom:80px;">
            <source src="{{ asset($sliderVideo->photo) }}" type="video/mp4">
        </video>
    </div>
    
@elseif($sliderType == 'youtube')
    <div id="slider-{{ $sliderVideo->id }}" style="margin-bottom:80px;">

    </div>
@elseif ($sliders->count() > 0)
    @foreach ($sliders as $slider)
        @if ($slider->type == 'youtube')
            <div id="slider-{{ $slider->id }}"></div>
            <div class="slider-description text-center">
                {{ $slider->description }}
            </div>
        @elseif($slider->type == 'image' or $slider->type == 'gif')
            <div>
                <div class="card shadow-xl" data-card-height="270"
                    style="background-image: url({{ asset('/uploads/sliders/' . $slider->photo) }});background-size: cover;    background-repeat: no-repeat;margin-bottom:10px;">
                    {{-- <div class="card-overlay "></div>
                <div class="card-overlay"></div> --}}
                </div>
                <div class="slider-description text-center">
                    {{ $slider->description }}
                </div>
            </div>
        @endif
    @endforeach
@endif


@push('scripts')
    <script>
        function onYouTubeIframeAPIReady(videoId, tagId) {
            console.log('interval');

            window.YT.ready(function() {
                console.log($(tagId));
                var player = new YT.Player(tagId, {
                    height: '270',
                    width: '100%',
                    videoId: videoId,
                    playerVars: {
                        'playsinline': 1,
                        'fs': 1,
                        'disablekb': 1,
                        'rel': 0,
                        'showinfo': 0,
                        'ecver': 2
                    },
                    events: {
                        'onReady': onPlayerReady,
                        'onStateChange': onPlayerStateChange
                    }
                });



                console.log('done');

            });



        }

        // 4. The API will call this function when the video player is ready.
        function onPlayerReady(event) {
            console.log('ready video');
            // event.target.playVideo();
        }

        // 5. The API calls this function when the player's state changes.
        //    The function indicates that when playing a video (state=1),
        //    the player should play for six seconds and then stop.
        var done = false;

        function onPlayerStateChange(event) {
            console.log('change : ' + event.data);

        }


        $(function() {
            @foreach ($restaurant->sliders as $slider)
                @if ($slider->type == 'youtube')
                    setTimeout(() => {
                        onYouTubeIframeAPIReady("{{ $slider->youtube }}", 'slider-{{ $slider->id }}');
                    }, 1);
                @endif
            @endforeach


        });
    </script>
@endpush

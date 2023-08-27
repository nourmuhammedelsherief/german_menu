<?php

namespace App\Http\Controllers\RestaurantController;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantSlider;
use App\Models\TemporaryFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee'):
            if (check_restaurant_permission($restaurant->id , 5) == false):
                abort(404);
            endif;
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $type = in_array($request->type , ['home' , 'contact_us' , 'contact_us_client']) ? $request->type : 'home';
        $sliders  = RestaurantSlider::whereRestaurant_id($restaurant->id)
            ->orderBy('id' , 'desc')
            ->where('slider_type' , $type)
            ->paginate(100);
        $this->deleteTemporaryFiles();
        return view('restaurant.sliders.index' , compact('sliders' , 'restaurant'));
    }
    protected function deleteTemporaryFiles(){
        $currentDate = Carbon::now()->subDay();
        $items = TemporaryFile::where('created_at' , '<=' , $currentDate->format('Y-m-d H:i:s'))->get();
        foreach($items as $item):
            if(Storage::disk('public_storage')->exists($item->path)):
                Storage::disk('public_storage')->delete($item->path);
            endif;
            $item->delete();
        endforeach;
        return $items;
    }

    public function storeSliderTitle(Request $request ){
        $data = $request->validate([
            'slider_down_contact_us_title' =>'required|min:1|max:190' 
        ]);
        $restaurant = auth('restaurant')->user();
        $restaurant->update($data);
        flash(trans('messages.updated'))->success();
        return redirect()->back();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('restaurant.sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee'):
            if (check_restaurant_permission($restaurant->id , 5) == false):
                abort(404);
            endif;
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $data = $request->validate( [
            'type' => 'required|in:image,youtube,local_video,gif' , 
            'youtube' => 'nullable|required_if:type,youtube|min:1' , 
            'photo' => 'nullable|required_if:type,image|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:20000' , 
            'video_path' => 'required_if:type,local_video|nullable|min:10' , 
            'description_en'  => 'nullable|min:1' ,
            'description_ar'  => 'nullable|min:1' ,
            'slider_type' => 'required|in:contact_us,home,contact_us_client'
        ]);
        
        $image = ($request->hasFile('photo') and $request->type == 'image') ? UploadImage($request->file('photo')  , 'photo' , '/uploads/sliders') : null;
        
        if($request->type == 'local_video' and !$temp = TemporaryFile::where('path' , $request->video_path)->first()):
            flash('يرجي ارفاق الفيديو اولا !!')->error();
            return redirect()->back();
        endif;
        if($request->type == 'gif' and !$temp = TemporaryFile::where('path' , $request->video_path)->first()):
            flash('يرجي ارفاق الفيديو اولا !!')->error();
            return redirect()->back();
        endif;
        if(isset($temp->id)):
            $image = $request->type == 'gif' ? basename($temp->path) : $temp->path;
            $temp->delete();
        endif;
        // create new slider
        $item = RestaurantSlider::create([
            'restaurant_id'  => $restaurant->id,
            'photo'  => $image  , 
            'type' => $request->type , 
            'youtube' => $request->youtube , 
            'description_ar' => $request->description_ar , 
            'description_en' => $request->description_en , 
            'slider_type' => $request->slider_type , 
        ]);
        flash(trans('messages.created'))->success();
        return redirect(route('sliders.index') . '?type=' . $item->slider_type);
    }

    
    public function uploadVideo(Request $request){
        $request->validate([
            'id' => 'nullable|integer' , 
            'video' => 'required|mimes:mp4,gif' , 
            'type' => 'required|in:local_video,gif'
        ]);
        if($request->type == 'gif'){
            $request->validate([
                'video' => 'required|mimes:gif' , 
            ]);
        }
        if(!empty($request->id) and !$slider = RestaurantSlider::find($request->id)):
            return trans('dashboard.errors.slider_not_found');
        endif;
        
        if(isset($slider->id) and $slider->type == 'local_video' and !empty($slider->photo) and Storage::disk('public_storage')->exists($slider->photo)):
            Storage::disk('public_storage')->delete($slider->photo);
        
        elseif(isset($slider->id) and $slider->type == 'gif' and !empty($slider->photo) and Storage::disk('public_storage')->exists( 'uploads/sliders/' . $slider->photo)):
            Storage::disk('public_storage')->delete( 'uploads/sliders/' . $slider->photo);
        endif;
        $videoPath = Storage::disk('public_storage')->put('uploads/sliders' ,$request->file('video'));
        if(isset($slider->id) and $request->type == 'local_video'){
            $slider->update([
                'type' => 'local_video' , 
                'photo' => $videoPath,
            ]);
        }elseif(isset($slider->id) and $request->type == 'gif'){
            $slider->update([
                'type' => 'gif' , 
                'photo' => basename($videoPath),
            ]);
        }else{
            $temp = TemporaryFile::create([
                'type' => 'slider' , 
                'path' => $videoPath
            ]);
        }
       
        return response([
            'status' => 1 , 
            'video_path' => $videoPath , 
            'temp_id' => isset($temp->id) ? $temp->id : null  , 
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $slider = RestaurantSlider::findOrFail($id);
        return view('restaurant.sliders.edit' , compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $slider = RestaurantSlider::findOrFail($id);
        
        $data = $request->validate( [
            'type' => 'required|in:image,youtube,local_video,gif' , 
            'youtube' => 'nullable|required_if:type,youtube|min:1' , 
            'photo' => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:20000' , 
            'description_en'  => 'nullable|min:1' ,
            'description_ar'  => 'nullable|min:1' ,
        ]);
        $image = $request->file('photo') == null ? $slider->photo : UploadImageEdit($request->file('photo')  , 'photo' , '/uploads/sliders' , $slider->photo);
        if($request->type == 'local_video' and empty($slider->photo)):
            flash('يرجي ارفاق الفيديو اولا !!')->error();
            return redirect()->back();
        elseif($request->type == 'local_video'):
            $image = $slider->photo;
        endif;
        
        // return UploadImageEdit($request->file('photo')  , 'photo' , '/uploads/sliders' , $slider->photo);
        
        $slider->update([
            'photo'  => $image , 
            'type' => $request->type , 
            'youtube' => $request->youtube , 
            'description_ar' => $request->description_ar , 
            'description_en' => $request->description_en , 
        ]);
        flash(trans('messages.updated'))->success();
        return redirect(route('sliders.index') . '?type=' . $slider->slider_type);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $slider = RestaurantSlider::findOrFail($id);

        if(RestaurantSlider::where('restaurant_id' , $slider->restaurant_id)->count() <= 1):
            flash(trans('messages.error_slider_count'))->error();
            return redirect(route('sliders.index') . '?type=' . $slider->slider_type);
        endif;
        if ($slider->photo != null and !in_array($slider->photo , ['slider2.png' , 'slider1.png']))
        {
            @unlink(public_path('/uploads/sliders/' . $slider->photo));
        }
        $slider->delete();
        flash(trans('messages.deleted'))->success();
        return redirect(route('sliders.index') . '?type=' . $slider->slider_type);
    }
}

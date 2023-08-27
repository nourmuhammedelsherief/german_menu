<?php

namespace App\Http\Controllers\RestaurantController;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\RestaurantCategory;
use App\Models\RestaurantSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $category = MenuCategory::findOrFail($id);
        $sub_categories = RestaurantSubCategory::where('menu_category_id' , $id)
            ->orderBy('id' , 'desc')
            ->paginate(100);
        return view('restaurant.sub_categories.index' , compact('sub_categories' , 'category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $category = MenuCategory::findOrFail($id);
        $restaurant = $category->restaurant;
        return view('restaurant.sub_categories.create' , compact('category' , 'restaurant'));
    }
    public function uploadImage(Request $request){
        if(!auth('restaurant')->check()):
            return redirect(url('restaurant/login'));
        endif;
        $request->validate([
            'photo' => 'required|mimes:png,jepg,jpg,svg' ,
            'action' => 'required|in:create,edit' ,
            'item_id' => 'required_if:action,edit|integer|exists:restaurant_sub_categories,id' ,
        ]);
        if($request->action == 'edit')
            $item = RestaurantSubCategory::findOrFail($request->item_id);

        if ($request->photo != null)
        {
            $photo = UploadImageEdit($request->file('photo'),'photo' , '/uploads/sub_menu_categories' , (isset($item->photo) ? $item->photo : null));
            if(isset($item->id))
                $item->update([
                    'image' => $photo ,
                ]);
            return response([
                'photo' =>  $photo,
                'status' => true ,
            ]);
        }
        return response('error' , 500);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $id)
    {
        $category = MenuCategory::findOrFail($id);
        $this->validate($request , [
            'name_ar'  => 'nullable|string|max:191',
            'name_en'  => 'nullable|string|max:191' , 
        ]);
        RestaurantSubCategory::create([
            'menu_category_id' => $category->id,
            'name_ar'  => $request->name_ar,
            'name_en'  => $request->name_en , 
            'image' => $request->image_name , 
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('sub_categories.index' , $category->id);
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
        $sub_category = RestaurantSubCategory::findOrFail($id);
        $restaurant = $sub_category->restaurant_category->restaurant;
        return view('restaurant.sub_categories.edit' , compact('sub_category' , 'restaurant'));
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
        $sub_category = RestaurantSubCategory::findOrFail($id);
        $this->validate($request , [
            'name_ar'  => 'nullable|string|max:191',
            'name_en'  => 'nullable|string|max:191'
        ]);
        $sub_category->update([
            'name_ar'  => $request->name_ar,
            'name_en'  => $request->name_en
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->route('sub_categories.index' , $sub_category->menu_category_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sub_category = RestaurantSubCategory::findOrFail($id);
        $sub_category->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('sub_categories.index', $sub_category->menu_category_id);
    }
}

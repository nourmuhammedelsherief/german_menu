<?php

namespace App\Http\Controllers\websiteController\Silver;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\WaiterItem;
use App\Models\WaiterOrder;
use Illuminate\Http\Request;

class WaiterOrderController extends Controller
{
    //
    public function completedOrder(Request $request , Restaurant $restaurant , Table $table){
       
        
        return view('website.' . session('theme_path') . 'silver.waiter_order_completed', compact('restaurant' , 'table'));
    }
    public function store(Request $request , Restaurant $restaurant){
        $request->validate([
            'table_id' => 'required|integer|exists:tables,id' ,
            'items' =>'nullable|array' , 
            'items.*' => 'integer|exists:restaurant_waiter_items,id' , 
            'note' => 'nullable' ,  
        ]);

        $table = Table::where('restaurant_id' , $restaurant->id)->where('service_id' , 14)->findOrFail($request->table_id);

        $order = WaiterOrder::create([
            'table_id' => $table->id , 
            'restaurant_id' => $restaurant->id , 
            'note' => $request->note , 
        ]);
        if(!empty($request->items)):
            foreach($request->items as $val):
                if($item = WaiterItem::find($val)):
                    $order->items()->create([
                        'item_id' => $item->id , 
                        'name' => $item->name_ar ,
                    ]);
                endif;
            endforeach;
        endif;
        return redirect(route('web.waiter.thank' , [$restaurant->id , $table->id]));
        
    }
}

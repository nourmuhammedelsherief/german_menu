<?php

namespace App\Http\Controllers\RestaurantController;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FoodicsDiscount;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantOrderPeriod;
use App\Models\RestaurantOrderSetting;
use App\Models\ServiceSubscription;
use App\Models\SilverOrderFoodics;
use App\Models\Table;
use App\Models\TableOrder;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:restaurant')->only(['createFoodicsOrder']);
    }

    public function foodicsOrder()
    {
        if (!auth('restaurant')->check()) :
            return redirect(url('restaurant/login'));
        endif;
        $restaurant = auth('restaurant')->user();
        $orders = SilverOrderFoodics::where('restaurant_id' , $restaurant->id)->with(['details' => function ($query) {
            $query->with(['product'])->where('status', '!=', 'in_cart');
        }])->whereHas('details')->orderBy('created_at', 'desc')->paginate(500);
        // return $orders;
        return view('restaurant.orders.foodics_orders', compact('orders'));
    }

    public function getFoodicsDetails(Request $request)
    {
        if (!auth('restaurant')->check()) :
            return response('unauth', 401);
        endif;
        $restaurant = auth('restaurant')->user();
        $request->validate([
            'order_id' => 'required|integer',
        ]);
        $order = SilverOrderFoodics::findOrFail($request->order_id);
        $foodics = null;
        $res = null;
        if (!empty($order->foodics_id)) :
            $res = getFoodicsOrder($order->foodics_id, $restaurant->foodics_access_token);
            // file_put_contents(storage_path('foodics_t.txt') ,  '\n' . date('Y-m-d h:i A') .' \n ' .   $res , FILE_APPEND);
            $foodics = $res->json();
        endif;

        return response([
            'status' => true,
            'data' => [
                'content' => view('restaurant.orders.include.foodics_info', compact('restaurant', 'foodics', 'res', 'order'))->render()
            ]
        ]);
    }


    public function orderDetails(Request $request)
    {
        if (!auth('restaurant')->check()) :
            return response('unauth', 401);
        endif;
        $restaurant = auth('restaurant')->user();
        $request->validate([
            'order_id' => 'required|integer',
        ]);
        $order = SilverOrderFoodics::findOrFail($request->order_id);


        return response([
            'status' => true,
            'data' => [
                'content' => view('restaurant.orders.include.order_details', compact('restaurant', 'order'))->render()
            ]
        ]);
    }

    public function createFoodicsOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer'
        ]);
        $restaurant = auth('restaurant')->user();
        if (!$order = SilverOrderFoodics::find($request->order_id) or !$details = $order->details()->whereHas('foodics_branch')->first()) {
            return response([
                'status' => false,
                'message' => 'الطلب غير موجود',
            ]);
        }
        $foodicsBranch = $details->foodics_branch;
        if (isset($foodicsBranch->id)) {
            $branch_id = $foodicsBranch->foodics_id;
            if ($request->discount_name != null) {
                if ($order->details->count() > 0) {
                    foreach ($order->details as $item) {
                        $discount = FoodicsDiscount::whereBranchId($order->branch->id)
                            ->whereNameEn($request->discount_name)
                            ->first();
                        if ($discount) {
                            checkTableProductDiscount($item->id, $discount->id);
                        }
                    }
                }
            }
            $count = $order->restaurant->foodics_orders + 1;
            $order->restaurant->update([
                'foodics_orders' => $count,
            ]);
            // $foodics = create_foodics_table_order($order->restaurant_id, $branch_id, $order->details, 'EasyMenu-cash', $order->table_id);
            $user = $order->user;
            $period = null;
            $day_id = null;
            $previous_type = null;
            if ($order->period_id and $order->day_id and $order->previous_order_type) {
                $period = RestaurantOrderPeriod::find($order->period_id)->start_at;
                $day_id = $order->day_id;
                $previous_type = $order->previous_order_type;
            }
            $foodics = create_foodics_order($foodicsBranch->restaurant->id, $branch_id, $order->details, $details->user, $order->order_type, $details->payment_type, $user->latitude, $user->longitude, $period, $day_id, $previous_type);

            $order = SilverOrderFoodics::find($order->id);
         
            $foodics = json_decode($foodics, true);
            if (isset($foodics['data']['id']) ) :
                $order->update([
                    'foodics_id' => $foodics['data']['id'] , 
                    'foodics_status' => $foodics['data']['status']
                ]);
                return response([
                    'status' => true,
                    'message' => 'تم إرسال الطلب بنجاح',
                    'content' =>  view('restaurant.tables.include.foodics_info', compact('restaurant', 'foodics', 'order'))->render()
                ]);
            else :
                return response([
                    'status' => false,
                    'message' => 'فشلت العملية الارسال',
                    'content' =>  view('restaurant.tables.include.foodics_info', compact('restaurant', 'foodics', 'order'))->render()
                ]);
            endif;
        } else {
            return response([
                'status' => false,
                'message' => 'لا يمكن انشاء فودكس لهذا الفرع',
                
            ]);
        }
    }
}

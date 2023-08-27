<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyPoint;
use App\Models\LoyaltyPointHistory;
use App\Models\Order;
use App\Models\ServiceSubscription;
use App\Models\TableOrder;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\FormRegister;
use App\Models\MenuCategory;
use App\Models\Modifier;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\SilverOrder;
use App\Models\SilverOrderFoodics;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     *  webhook function
     */
    public function webhook(Request $request)
    {
        app('log')->debug('RECEIVED CALL BACK FROM Foodics');

        // get all data passed by foodics
        $d = json_decode(json_encode($request->all()), true);
        $dd = $request->all();
        file_put_contents(storage_path('app/foodics_change_status.txt'), 'new_order_status' . date('Y-m-d h:i A') . '  ' .  json_encode($request->all()), FILE_APPEND);
        $event = in_array($request->event, ['application.order.updated', 'menu.updated']) ? $request->event  : null;
        if ($event == 'application.order.updated') {
            if (is_array($dd)  and isset($dd['order']) and isset($dd['order']['id'])) {

                $code = $dd['order']['id'];
                // file_put_contents('log.txt', $code, FILE_APPEND);

                // get order
                $order = Order::where('foodics_order_id', $code)->first();

                if ($order and false) // check if there any gold order exist with the same id
                {
                    $restaurant = $order->restaurant;
                    $branch = $order->branch;
                    $orderStatus = null;
                    if (in_array($dd['order']['status'], [2, 6])) $orderStatus = 'active';
                    elseif (in_array($dd['order']['status'], [4])) $orderStatus = 'completed';
                    elseif (in_array($dd['order']['status'], [3, 5, 7])) $orderStatus = 'canceled';
                    // return $orderStatus;
                    $order->update([
                        'foodics_status' => $dd['order']['status'],
                        'status' => empty($orderStatus) ? $order->status : $orderStatus,
                    ]);
                    // check loyalty_points system 
                    $loyaltySubscription =  ServiceSubscription::whereRestaurantId($restaurant->id)->whereHas('service', function ($query) {
                        $query->where('id', 11);
                    })
                        ->whereIn('status', ['active', 'tentative'])
                        ->where('branch_id', $branch->id)
                        ->first();
                    if ($restaurant->enable_loyalty_point == 'true' and isset($loyaltySubscription->id)) {
                        $points = 0;
                        foreach ($order->order_items as $t) :

                            if ($t->loyalty_points > 0) $points += ($t->loyalty_points * $t->product_count);

                        endforeach;
                        if ($points > 0) :
                            LoyaltyPointHistory::create([
                                'restaurant_id' => $order->restaurant_id,
                                'order_id' => $order->id,
                                'user_id' => $order->user_id,
                                'points' => $points,
                            ]);
                            if ($balance = LoyaltyPoint::where('type', 'point')->where('user_id', $order->user_id)->where('restaurant_id', $order->restaurant_id)->first()) :
                                $balance->update([
                                    'amount' => ($balance->amount + $points),
                                ]);
                            else :
                                LoyaltyPoint::create([
                                    'type' => 'point',
                                    'restaurant_id' => $order->restaurant_id,
                                    'user_id' => $order->user_id,
                                    'amount' => $points,
                                ]);
                            endif;
                        endif; // if there any points 
                    } // end loyalty points system

                } // end gold order
                elseif ($order = SilverOrderFoodics::where('foodics_id', $code)->first()) {
                    $restaurant = $order->restaurant;
                    $branch = $order->branch;
                    $orderStatus = 'pending';
                    // if (in_array($dd['order']['status'], [2])) $orderStatus = 'active';
                    // if (in_array($dd['order']['status'], [4])) $orderStatus = 'in_way';
                    // elseif (in_array($dd['order']['status'], [3])) $orderStatus = 'completed';
                    // elseif (in_array($dd['order']['status'], [3, 5, 7])) $orderStatus = 'canceled';
                    // return $orderStatus;
                    $order->update([
                        'foodics_status' => $dd['order']['status'],
                        // 'status' => empty($orderStatus) ? $order->status : $orderStatus,
                    ]);
                } // end silver order foodics
                else { // check if foodics id is exists in table_orders table

                    $order = TableOrder::where('foodics_order_id', $code)->first();

                    if ($order) // check if there any table order exist with the same id
                    {
                        $restaurant = $order->restaurant;
                        $branch = $order->branch;
                        $orderStatus = null;
                        if (in_array($dd['order']['status'], [2, 6])) $orderStatus = 'active';
                        elseif (in_array($dd['order']['status'], [4])) $orderStatus = 'completed';
                        elseif (in_array($dd['order']['status'], [3, 5, 7])) $orderStatus = 'canceled';
                        // return $orderStatus;
                        $order->update([
                            'foodics_status' => $dd['order']['status'],
                            'status' => empty($orderStatus) ? $order->status : $orderStatus,
                        ]);
                    }
                }
            }
        } // end updated orders
        elseif ($event == 'menu.updated') {

            if ($request->entity['type'] == 'product') :
                return $this->updateFoodicsProduct($request->entity['id'], $request->business['reference']);
            elseif ($request->entity['type'] == 'category') :
                return $this->updateFoodicsCategory($request->entity['id'], $request->business['reference']);
            elseif ($request->entity['type'] == 'modifier') :
                return $this->updateFoodicsModifier($request->entity['id'], $request->business['reference']);
            endif;
        } // end menu updated
        return 0;
    }

    private function updateFoodicsProduct($foodicsId, $reference)
    {


        $product = Product::where('foodics_id', $foodicsId)->first();
        if (isset($product->id)) $restaurant = $product->restaurant;
        else    $restaurant = Restaurant::where('foodics_referance', $reference)->first();

        if (!isset($restaurant->id)) return 'false';
        $branch = $restaurant->branches()->where('foodics_status', 'true')->orderBy('id', 'desc')->first();
        if (!isset($branch->id)) return 'false';
        $foodicsProduct = getFoodicsProduct($restaurant, $foodicsId);
        $dd = array_values(json_decode($foodicsProduct, true))[0];
        if (isset($dd['id'])) { // to create or update

            return productAndModifierCreationAndDelete($dd, $restaurant->id, $branch->id);
        } elseif (isset($product->id) and $dd == 'The requested entity was not found.') { // delete

            $product->delete();
        }
        return 'true';
    }
    private function updateFoodicsCategory($foodicsId, $reference)
    {
        $category = MenuCategory::where('foodics_id', $foodicsId)->whereHas('restaurant', function ($query) use ($reference) {
            $query->where('foodics_referance', $reference);
        })->first();
        if (isset($category->id)) $restaurant = $category->restaurant;
        else    $restaurant = Restaurant::where('foodics_referance', $reference)->first();

        if (!isset($restaurant->id)) return 'restaurant not found';
        $branch = $restaurant->branches()->where('foodics_status', 'true')->orderBy('id', 'desc')->first();
        if (!isset($branch->id)) return 'branch not found';
        $foodicsProduct = getFoodicsCategory($restaurant->id, $foodicsId);
        $dd = array_values(json_decode($foodicsProduct, true))[0];
        if (isset($dd['id'])) { // to create or update

            return updateCategories($restaurant->id, $dd, $branch->id);
        } elseif (isset($category->id) and $dd == 'The requested entity was not found.') { // delete
            $category->delete();
            return 'delete';
        }
    }
    private function updateFoodicsModifier($foodicsId, $reference)
    {
        $modifier = Modifier::where('foodics_id', $foodicsId)->whereHas('restaurant', function ($query) use ($reference) {
            $query->where('foodics_referance', $reference);
        })->first();
        if (isset($modifier->id)) $restaurant = $modifier->restaurant;
        else    $restaurant = Restaurant::where('foodics_referance', $reference)->first();

        if (!isset($restaurant->id)) return 'restaurant not found';
        $branch = $restaurant->branches()->where('foodics_status', 'true')->orderBy('id', 'desc')->first();
        if (!isset($branch->id)) return 'branch not found';
        $foodicsProduct = getFoodicsModifier($restaurant->id, $foodicsId);
        $dd = array_values(json_decode($foodicsProduct, true))[0];
        if (isset($dd['id'])) { // to create or update

            return updateFoodicsModifier( $dd, $restaurant->id,$branch->id);
        } elseif (isset($modifier->id) and $dd == 'The requested entity was not found.') { // delete
            $modifier->delete();
            return 'delete';
        }
    }

    // form register

    public function form_register()
    {
        $countries = Country::with('cities')
            ->where('active', 'true')
            ->orderBy('created_at', 'asc')
            ->get();
        return view('form_register', compact('countries'));
    }
    public function form_register_post(Request $request)
    {
        $this->validate($request, [
            'name'       => 'required|string|max:191',
            'email'      => 'required|email|max:191',
            'country_id' => 'required|exists:countries,id',
            'phone_number' => 'required',
            'type'        => 'required|in:cafe,restaurant'
        ]);
        // create new form register
        FormRegister::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'country_id'    => $request->country_id,
            'phone_number'  => $request->phone_number,
            'type'          => $request->type,
        ]);
        flash('تم ارسال بياناتك الي الاداره بنجاح')->success();
        return redirect()->back();
    }
}

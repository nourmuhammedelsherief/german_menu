<?php

namespace App\Http\Controllers\RestaurantController\Waiter;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FoodicsDiscount;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantOrderSetting;
use App\Models\ServiceSubscription;
use App\Models\Table;
use App\Models\TableOrder;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaiterTableController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:restaurant');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth('restaurant')->check()) :
            return redirect(url('restaurant/login'));
        endif;
        $restaurant = Auth::guard('restaurant')->user();
        if ($restaurant->type == 'employee') :
            // if (check_restaurant_permission($restaurant->id, 3) == false) :
            //     abort(404);
            // endif;
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $checkTableService = ServiceSubscription::whereRestaurantId($restaurant->id)
            ->where('service_id', 14)
            ->whereIn('status', ['active' , 'tentative'])
            ->first();
        if ($checkTableService == null) {

            abort(404);
        }
        if ($restaurant->status == 'finished' || $restaurant->subscription->status == 'tentative_finished') {
            return redirect()->route('RestaurantProfile');
        }
        $tables = Table::whereRestaurantId($restaurant->id)
            
            ->where('service_id', 14)
            ->paginate(500);
        $service_id = null;
        return view('restaurant.waiters.tables.index', compact('tables', 'service_id'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth('restaurant')->check()) :
            return redirect(url('restaurant/login'));
        endif;
        $restaurant = Auth::guard('restaurant')->user();
        if ($restaurant->type == 'employee') :
        
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        //        $checkTableService = ServiceSubscription::whereRestaurantId($restaurant->id)
        //            ->where('service_id' , 8)
        //            ->where('status' , 'active')
        //            ->first();
        //        if ($checkTableService == null)
        //        {
        //            abort(404);
        //        }
        $branches = Branch::with('subscription')
            ->whereHas('subscription', function ($q) {
                $q->where('end_at', '!=', null);
                // $q->where('foodics_status', 'false');
            })
            ->whereRestaurantId($restaurant->id)
            ->whereStatus('active')
            ->get();
        $service_id = null;
        return view('restaurant.waiters.tables.create', compact('branches', 'service_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth('restaurant')->check()) :
            return redirect(url('restaurant/login'));
        endif;
        $this->validate($request, [
            'branch_id' => 'nullable',
            'name_ar' => 'nullable|string|max:191',
            'name_en' => 'nullable|string|max:191',
            'name_barcode' => 'required|string|max:191|unique:restaurants|regex:/(^([\pL\s\-\_\@\#\$\%\^\&\*]+)([a-zA-Z]+)(\d+)?$)/u',
            'code' => 'nullable|numeric',
        ]);
        $restaurant = Auth::guard('restaurant')->user();
        if ($restaurant->type == 'employee') :
            if (check_restaurant_permission($restaurant->id, 3) == false) :
                abort(404);
            endif;
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        if ($request->name_ar == null && $request->name_en == null) {
            flash(trans('messages.name_required'))->error();
            return redirect()->back();
        }
        // create new table
        $barcode = str_replace(' ', '-', $request->name_barcode);
        if ($request->service_id != null and $request->branch_id == null) {
            $branch = ServiceSubscription::whereRestaurantId($restaurant->id)
                ->where('service_id', $request->service_id)
                ->whereStatus('active')
                ->first()->branch_id;
        } else {
            $branch = $request->branch_id;
        }
        
        Table::create([
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'name_barcode' => $barcode,
            'code' => $request->code,
            'service_id' => 14 , 
        ]);
        flash(trans('messages.created'))->success();
        if ($request->service_id != null) {
            if ($request->service_id == 9) {
                return redirect()->route('WhatsAppTable', 9);
            } elseif ($request->service_id == 10) {
                return redirect()->route('EasyMenuTable', 10);
            }
     
        } else {
            return redirect()->route('restaurant.waiter.tables.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth('restaurant')->check()) :
            return redirect(url('restaurant/login'));
        endif;
        $restaurant = Auth::guard('restaurant')->user();
        if ($restaurant->type == 'employee') :
            if (check_restaurant_permission($restaurant->id, 3) == false) :
                abort(404);
            endif;
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        //        $checkTableService = ServiceSubscription::whereRestaurantId($restaurant->id)
        //            ->where('service_id' , 8)
        //            ->where('status' , 'active')
        //            ->first();
        //        if ($checkTableService == null)
        //        {
        //            abort(404);
        //        }
        $table = Table::findOrFail($id);
        $branches = Branch::with('subscription')
            ->whereHas('subscription', function ($q) {
                $q->where('end_at', '!=', null);
            })
            ->whereRestaurantId($restaurant->id)
            ->whereIn('status', ['active', 'tentative'])
            ->get();
        return view('restaurant.waiters.tables.edit', compact('table', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        if (!auth('restaurant')->check()) :
            return redirect(url('restaurant/login'));
        endif;
        $restaurant = Auth::guard('restaurant')->user();
        if ($restaurant->type == 'employee') :
           
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $table = Table::findOrFail($id);
        $this->validate($request, [
            'branch_id' => 'nullable',
            'name_ar' => 'nullable|string|max:191',
            'name_en' => 'nullable|string|max:191',
            'name_barcode' => 'required|string|max:191|unique:restaurants|regex:/(^([\pL\s\-\_\@\#\$\%\^\&\*]+)([a-zA-Z]+)(\d+)?$)/u',
            'code' => 'nullable|numeric',
        ]);
        
        if ($request->name_ar == null && $request->name_en == null) {
            flash(trans('messages.name_required'))->error();
            return redirect()->back();
        }
        $barcode = str_replace(' ', '-', $request->name_barcode);
        $table->update([
            'restaurant_id' => $restaurant->id,
            'branch_id' => $request->branch_id == null ? $table->branch_id : $request->branch_id,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'name_barcode' => $barcode,
            'code' => $request->code,
        ]);
        flash(trans('messages.updated'))->success();
        
        
            return redirect()->route('restaurant.waiter.tables.index');
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth('restaurant')->check() and !auth('admin')->check()) :
            return redirect(url('restaurant/login'));
        endif;
        $table = Table::findOrFail($id);
        if ($table->service_id != null) {
            $table->delete();
            flash(trans('messages.deleted'))->success();

            return redirect(route('restaurant.waiter.table.index'));
        } else {
            $table->delete();
            flash(trans('messages.deleted'))->success();
            return redirect()->route('restaurant.waiter.tables.index');
        }
    }

    public function show_barcode($id)
    {
        if (!auth('restaurant')->check() and !auth('admin')->check()) :
            return redirect(url('restaurant/login'));
        endif;
        $table = Table::findOrFail($id);
        return view('restaurant.waiters.tables.show_barcode', compact('table'));
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
        $order = TableOrder::findOrFail($request->order_id);


        return response([
            'status' => true,
            'data' => [
                'content' => view('restaurant.waiters.tables.include.order_details', compact('restaurant', 'order'))->render()
            ]
        ]);
    }

    public function createFoodicsOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer'
        ]);
        $restaurant = auth('restaurant')->user();
        if (!$order = TableOrder::find($request->order_id)) {
            return response([
                'status' => false,
                'message' => 'الطلب غير موجود',
            ]);
        }
        if ($order->branch->foodics_status == 'true') {
            $branch_id = $order->table->foodics_branch->foodics_id;
            if ($request->discount_name != null) {
                if ($order->order_items->count() > 0) {
                    foreach ($order->order_items as $item) {
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
            $foodics = create_foodics_table_order($order->restaurant_id, $branch_id, $order->order_items, 'EasyMenu-cash', $order->table_id);
            $order = Order::find($order->id);
            $foodics = json_decode($foodics, true);
            if (isset($foodics['data']['id']) or true) :
                return response([
                    'status' => true,
                    'message' => 'تم إرسال الطلب بنجاح',
                    'content' =>  view('restaurant.waiters.tables.include.foodics_info', compact('restaurant', 'foodics', 'order'))->render()
                ]);
            else :
                return response([
                    'status' => false,
                    'message' => 'فشلت العملية الارسال',
                    'content' =>  view('restaurant.waiters.tables.include.foodics_info', compact('restaurant', 'foodics', 'order'))->render()
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

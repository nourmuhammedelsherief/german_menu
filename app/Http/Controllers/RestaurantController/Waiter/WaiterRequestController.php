<?php

namespace App\Http\Controllers\RestaurantController\Waiter;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Party;
use App\Models\PartyAddition;
use App\Models\PartyBranch;
use App\Models\PartyDay;
use App\Models\PartyDayPeriod;
use App\Models\PartyField;
use App\Models\PartyFieldOption;
use App\Models\PartyOrder;
use App\Models\Reservation\ReservationOrder;
use App\Models\Reservation\ReservationTable;
use App\Models\Restaurant;
use App\Models\ServiceSubscription;
use App\Models\WaiterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaiterRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:restaurant');
        $this->middleware(function ($request, $next) {
            $restaurant = auth('restaurant')->user();
            $checkOrderService = ServiceSubscription::whereRestaurantId($restaurant->id)
                ->whereIn('service_id', [14])
                ->first();
            if ($checkOrderService == null) {
                abort(404);
            }
           
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
      
       $requests  = WaiterItem::where('restaurant_id' , $restaurant->id)->orderBy('id' , 'desc')->get();
        return view('restaurant.waiters.items.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $action = 'create';
        $branches = Branch::where('restaurant_id', $restaurant->id)->get();
        return view('restaurant.waiters.items.create', compact('branches', 'action'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $data = $this->validate($request, [
            'name_ar' => 'required|string|max:191',
            'name_en' => 'required|string|max:191',
            'branch_id' => 'required|integer',
            'sort' => 'nullable|integer|min:1' , 
            'status' => 'required|in:true,false' , 
        ]);


        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $data['restaurant_id'] = $restaurant->id;
        $party = WaiterItem::create($data);
       
        flash(trans('messages.created'))->success();
        return redirect()->route('restaurant.waiter.items.index');
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
        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $action = 'edit';
        $request = WaiterItem::where('restaurant_id', $restaurant->id)->findOrFail($id);
        $branches = Branch::where('restaurant_id', $restaurant->id)->get();
        return view('restaurant.waiters.items.edit', compact('branches', 'action', 'request'));
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
        
        $data = $this->validate($request, [
            'name_ar' => 'required|string|max:191',
            'name_en' => 'required|string|max:191',
            'branch_id' => 'required|integer',
            'sort' => 'nullable|integer|min:1' , 
            'status' => 'required|in:true,false' , 
        ]);


        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $item  = WaiterItem::where('restaurant_id' , $restaurant->id)->findOrFail($id);
        
        $item->update($data);
        flash(trans('messages.created'))->success();
        return redirect()->route('restaurant.waiter.items.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :

            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $party = WaiterItem::where('restaurant_id', $restaurant->id)->findOrFail($id);
        try {
            $party->delete();
        } catch (\Throwable $th) {

            flash('لا يمكن الحذف لوجود طلبات')->error();
            return redirect()->route('restaurant.waiter.items.index');
        }

        flash(trans('messages.deleted'))->success();
        return redirect()->route('restaurant.waiter.items.index');
    }
    public function servicesIndex(Request $request)
    {
        $restaurant = Auth::guard('restaurant')->user();
        if ($restaurant->type == 'employee') :
            // if (check_restaurant_permission($restaurant->id , 3) == false):
            //     abort(404);
            // endif;
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        if ($request->method() == 'POST' and $request->has('enable_party')) :
            $request->validate([
                'enable_party' => 'required|in:true,false',

            ]);
            $restaurant->update([
                'enable_party' => $request->enable_party,
                'party_description_ar' => $request->party_description_ar,
                'party_description_en' => $request->party_description_en,
            ]);
            flash(trans('messages.updated'))->success();
        endif;
        $action = 'party';

        return view('restaurant.waiters.items.payment_settings', compact('restaurant'));
    }

    public function cashSettings(Request $request)
    {
        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :

            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        if ($request->method() == 'POST') :
            $data = $request->validate([
                'enable_party_payment_cash' => 'required|in:true,false',
            ]);
            $restaurant->update($data);
            flash(trans('messages.updated'))->success();
            return redirect(route('restaurant.waiter.items.setting.payment'));
        endif;

        return view('restaurant.waiters.items.cash_payment', compact('restaurant'));
    }

    public function getSettings(Request $request)
    {
        $restaurant = auth('restaurant')->user();
        if ($request->method() == 'POST') :
            // return $request->all();
            $data = $request->validate([
                'enable_waiter' => 'required|in:true,false',
            
            ]);
    

            flash(trans('messages.updated'))->success();
            $restaurant->update($data);
        endif;
        // return $restaurant;
        return view('restaurant.waiters.settings', compact('restaurant'));
    }

    public function confirmOrder(Request $request, $id, $code)
    {
        if ($order = PartyOrder::where('num', $code)->find($id)) :
            $order->update([
                'status' => 'active',
                'payment_status' => 'paid'
            ]);
            return response([
                'status' => true,
                'message' => trans('dashboard.messages.reservation_confirmed')
            ]);
        endif;
        return response([
            'status' => false,
            'message' => trans('dashboard.errors.reservation_num_fail')
        ]);
    }
    public function cancelOrder(Request $request, $id)
    {
        if ($order = PartyOrder::findOrFail($id)) :
            $order->update([
                'status' => 'canceled',
                'cancel_reason' => $request->reason,
            ]);
            return redirect()->back();
        endif;
        return response([
            'status' => false,

        ]);
    }
}

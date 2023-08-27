<?php

namespace App\Http\Controllers\WaiterController;

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
use App\Models\WaiterOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaiterOrderController extends Controller
{
    private $restaurant, $employee;
    public function __construct()
    {
        $this->middleware('auth:waiter');

        $this->middleware(function ($request, $next) {
            $this->employee = auth('waiter')->user();
            $restaurant = $this->employee->restaurant;
            $checkOrderService = ServiceSubscription::whereRestaurantId($restaurant->id)
                ->whereIn('service_id', [14])
                ->first();
            if ($checkOrderService == null) {
                abort(404);
            }
            $this->restaurant = $restaurant;
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $restaurant = $this->restaurant;

        $request->validate([
            'status' => 'nullable|in:pending,in_progress,completed,canceled'
        ]);
        $emp = $this->employee;
        $pendingCount =WaiterOrder::whereIn('status', ['pending', 'in_progress'])->whereHas('table', function ($query) use ($emp) {
            $query->where('branch_id', $emp->branch_id);
        })->count();
        if ($request->wantsJson()) :
            $ord   = WaiterOrder::whereIn('status', ['pending', 'in_progress'])->whereHas('table', function ($query) use ($emp) {
                $query->where('branch_id', $emp->branch_id);
            });
            if (!empty($request->status)) {
                $ord = $ord->where('status', $request->status);
            }
            $orders = $ord->orderBy('id', 'desc')->get();

            return response([
                'status' => true,
                'pending_count' => $pendingCount,
                'data' => view('waiter.orders.includes.updated_table', compact('orders'))->render()
            ]);
        endif;
        $orders  = WaiterOrder::whereIn('status', ['pending', 'in_progress'])->whereHas('table', function ($query) use ($emp) {
            $query->where('branch_id', $emp->branch_id);
        });
        if (!empty($request->status)) {
            $orders = $orders->where('status', $request->status);
        }
       
        $orders = $orders->orderBy('id', 'desc')->get();
      
        return view('waiter.orders.index', compact('orders', 'pendingCount'));
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
            'sort' => 'nullable|integer|min:1',
            'status' => 'required|in:true,false',
        ]);


        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $data['restaurant_id'] = $restaurant->id;
        $party = WaiterOrder::create($data);

        flash(trans('messages.created'))->success();
        return redirect()->route('restaurant.waiter.orders.index');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request)
    {

        $data = $this->validate($request, [
            'order_id' => 'required|integer',
            'status' => 'required|in:pending,in_progress,completed,canceled',
        ]);


        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $item  = WaiterOrder::where('restaurant_id', $restaurant->id)->findOrFail($request->order_id);
        if (in_array($item->status, ['completed', 'canceled'])) :
            return response([
                'status' => false,
                'message' => trans('dashboard.order_has_been_finished'),
            ]);
        endif;
        $item->update([
            'status' => $request->status
        ]);
        return response([
            'status' => true,
            'message' => trans('messages.updated'),
        ]);
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
        $party = WaiterOrder::where('restaurant_id', $restaurant->id)->findOrFail($id);
        try {
            $party->delete();
        } catch (\Throwable $th) {

            flash('لا يمكن الحذف لوجود طلبات')->error();
            return redirect()->route('restaurant.waiter.orders.index');
        }

        flash(trans('messages.deleted'))->success();
        return redirect()->route('restaurant.waiter.orders.index');
    }
}

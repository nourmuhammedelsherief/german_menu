<?php

namespace App\Http\Controllers\RestaurantController\Party;

use App\Http\Controllers\Controller;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartyOrderController extends Controller
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
    public function index(Request $request)
    {
        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $checkReservation = ServiceSubscription::whereRestaurantId($restaurant->id)
            ->where('service_id', 13)
            ->whereIn('status', ['active', 'tentative'])
            ->first(); // for test
        if ($checkReservation == null) {
            abort(404);
        }
        $orders = PartyOrder::where('restaurant_id', $restaurant->id)->where('status', '!=', 'cart');
        if ($request->status == 'pending') :
            $orders = $orders->where('status', $request->status);

        elseif ($request->status == 'active') :
            $orders = $orders->where('status', $request->status);

        elseif ($request->status == 'canceled') :
            $orders = $orders->where('status', $request->status);
        elseif ($request->status == 'expire-date') :
            $orders = $orders->where('date', '<',date('Y-m-d'));
        elseif ($request->status == 'not-expire-date') :
            $orders = $orders->where('date', '>',date('Y-m-d'));
        endif;
        $orders = $orders->orderBy('id', 'desc')->get();
        return view('restaurant.party.party_orders.index', compact('orders', 'restaurant'));
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
        $branches = PartyBranch::where('restaurant_id', $restaurant->id)->get();
        return view('restaurant.party.party_orders.form', compact('branches', 'action'));
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
            'title_ar' => 'required|string|max:191',
            'title_en' => 'required|string|max:191',
            'description_ar' => 'required|string|min:1',
            'description_en' => 'nullable|string|min:1',
            'branch_id' => 'required|integer|exists:restaurant_party_branches,id',
            'price' => 'required|numeric',
            'dates' => 'required|array|min:1',
            'dates.*.date' => 'required|date|after:yesterday',
            'dates.*.time_from' => 'required',
            'dates.*.time_to' => 'required',
            'additions' => 'nullable|array|min:1',
            'additions.*.name_ar' => 'min:1|max:190',
            'additions.*.name_en' => 'min:1|max:190',
            'fields' => 'nullable|array',
            'fields.*.type' => 'in:select,text,checkbox',
            'fields.*.name_ar' => 'min:1|max:190',
            'fields.*.name_en' => 'min:1|max:190',
            'fields.*.options' => 'array',
            'fields.*.options.*.name_ar' => 'min:1|max:190',
            'fields.*.options.*.name_en' => 'min:1|max:190',
            'fields.*.options.*.is_default' => 'boolean',
        ]);


        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        // create new bank
        $data['restaurant_id'] = $restaurant->id;
        $data['total_price'] = $data['price'];
        $party = Party::create($data);
        foreach ($request->dates as $k => $date) :
            if (isset($date['date'])) :
                $day = PartyDay::create([
                    'party_id' => $party->id,
                    'date' => $date['date'],
                ]);
                PartyDayPeriod::create([
                    'party_day_id' => $day->id,
                    'time_from' => $date['time_from'],
                    'time_to' => $date['time_to'],
                ]);
            endif;
        endforeach;
        $totalPrice = $data['price'];
        if (!empty($request->additions) and is_array($request->additions)) :
            foreach ($request->additions as $k => $addition) :
                PartyAddition::create([
                    'party_id' => $party->id,
                    'name_ar' => $addition['name_ar'],
                    'name_en' => $addition['name_en'],
                    'price' => $addition['price'],
                ]);
                $totalPrice += $addition['price'];
            endforeach;
        endif;

        if (!empty($request->fields) and is_array($request->fields)) :
            foreach ($request->fields as $k => $addition) :
                $field = PartyField::create([
                    'party_id' => $party->id,
                    'name_ar' => $addition['name_ar'],
                    'name_en' => $addition['name_en'],
                    'is_required' => $addition['is_required'],
                    'type' => $addition['type'],
                ]);
                if ($addition['type'] == 'checkbox' or $addition['type'] == 'select') :
                    foreach ($addition['options'] as $kk => $option) :
                        $option1 = PartyFieldOption::create([
                            'field_id' => $field->id,
                            'name_ar' => $option['name_ar'],
                            'name_en' => $option['name_en'],
                            'is_default' => $option['is_default'],
                        ]);
                    endforeach;
                endif;
            endforeach;
        endif;
        $party->update([
            'total_price' => $totalPrice,
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('restaurant.party.index');
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
        $party = Party::where('restaurant_id', $restaurant->id)->findOrFail($id);
        $branches = PartyBranch::where('restaurant_id', $restaurant->id)->get();
        return view('restaurant.party.party_orders.form', compact('branches', 'action', 'party'));
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
        // return $request->all();
        $data = $this->validate($request, [
            'title_ar' => 'required|string|max:191',
            'title_en' => 'required|string|max:191',
            'description_ar' => 'required|string|min:1',
            'description_en' => 'nullable|string|min:1',
            'branch_id' => 'required|integer|exists:restaurant_party_branches,id',
            'price' => 'required|numeric',
            'dates' => 'required|array|min:1',
            'dates.*.date' => 'required|date|after:yesterday',
            'dates.*.time_from' => 'required',
            'dates.*.time_to' => 'required',
            'additions' => 'nullable|array|min:1',
            'additions.*.name_ar' => 'min:1|max:190',
            'additions.*.name_en' => 'min:1|max:190',
            'fields' => 'nullable|array',
            'fields.*.type' => 'in:select,text,checkbox',
            'fields.*.name_ar' => 'min:1|max:190',
            'fields.*.name_en' => 'min:1|max:190',
            'fields.*.options' => 'array',
            'fields.*.options.*.name_ar' => 'min:1|max:190',
            'fields.*.options.*.name_en' => 'min:1|max:190',
            'fields.*.options.*.is_default' => 'boolean',
        ]);

        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $party = Party::where('restaurant_id', $restaurant->id)->findOrFail($id);
        // create new bank
        // return $request->all();
        $party->update($data);
        $selectedIds = [];
        $party->days()->delete();
        foreach ($request->dates as $k => $date) :

            if (isset($date['date'])) :

                $day = PartyDay::create([
                    'party_id' => $party->id,
                    'date' => $date['date'],
                ]);
                PartyDayPeriod::create([
                    'party_day_id' => $day->id,
                    'time_from' => $date['time_from'],
                    'time_to' => $date['time_to'],
                ]);
            endif;
        endforeach;
        $totalPrice = $data['price'];
        $party->additions()->delete();
        if (!empty($request->additions) and is_array($request->additions)) :
            foreach ($request->additions as $k => $addition) :
                PartyAddition::create([
                    'party_id' => $party->id,
                    'name_ar' => $addition['name_ar'],
                    'name_en' => $addition['name_en'],
                    'price' => $addition['price'],
                ]);
                $totalPrice += $addition['price'];
            endforeach;
        endif;
        $party->fields()->delete();
        if (!empty($request->fields) and is_array($request->fields)) :
            foreach ($request->fields as $k => $addition) :
                $field = PartyField::create([
                    'party_id' => $party->id,
                    'name_ar' => $addition['name_ar'],
                    'name_en' => $addition['name_en'],
                    'is_required' => $addition['is_required'],
                    'type' => $addition['type'],
                ]);
                if ($addition['type'] == 'checkbox' or $addition['type'] == 'select') :
                    foreach ($addition['options'] as $kk => $option) :
                        $option1 = PartyFieldOption::create([
                            'field_id' => $field->id,
                            'name_ar' => $option['name_ar'],
                            'name_en' => $option['name_en'],
                            'is_default' => $option['is_default'],
                        ]);
                    endforeach;
                endif;
            endforeach;
        endif;
        $party->update([
            'total_price' => $totalPrice,
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('restaurant.party.index');
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
        $branch = Party::where('restaurant_id', $restaurant->id)->findOrFail($id);

        $branch->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('restaurant.party.index');
    }
    public function servicesIndex()
    {
        $restaurant = Auth::guard('restaurant')->user();
        if ($restaurant->type == 'employee') :
            // if (check_restaurant_permission($restaurant->id , 3) == false):
            //     abort(404);
            // endif;
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $action = 'party';
        return view('restaurant.party.party_orders.payment_services', compact('restaurant'));
    }
    public function settings(Request $request)
    {

        if (!$restaurant = auth('restaurant')->user()) {
            abort(422);
        }
        if ($restaurant->type == 'employee') :
            // if (check_restaurant_permission($restaurant->id , 3) == false):
            //     abort(404);
            // endif;
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        if ($request->method() == 'POST' and $request->has('reservation_service')) :
            $request->validate([
                'reservation_service' => 'required|in:true,false'
            ]);
            $restaurant->update([
                'reservation_service' => $request->reservation_service
            ]);
        endif;

        return view('restaurant.reservations.orders.settings', compact('restaurant'));
    }

    public function acceptBankOrder(Request $request , PartyOrder $order)
    {

        if (!$restaurant = auth('restaurant')->user()) {
            abort(422);
        }
        if ($restaurant->type == 'employee') :
            // if (check_restaurant_permission($restaurant->id , 3) == false):
            //     abort(404);
            // endif;
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        if($order->payment_status == 'unpaid' and $order->status == 'pending' and $order->payment_type == 'bank'):
            $order->update([
                'payment_status' => 'paid' 
            ]);
            flash('تم التعديل بنجاح')->success();
        else:
            flash('لا يمكن تعديل حالة الطلب')->error();
        endif;
        

        return redirect(route('restaurant.party-order.index'));
    }
}

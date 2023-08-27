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

class PartyController extends Controller
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
        $branches = Party::where('restaurant_id', $restaurant->id)->orderBy('id', 'desc')->get();
        return view('restaurant.party.party.index', compact('branches'));
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
        return view('restaurant.party.party.form', compact('branches', 'action'));
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
            'image'   => 'required|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
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
            'additions.*.price' => 'numeric|min:0',
            'additions.*.is_required' => 'boolean',
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
        
        if ($request->hasFile('image')) {
            $data['image'] =  UploadImage($request->file('image'), 'parties', '/uploads/parties');
        }

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
        if (!empty($request->additions) and is_array($request->additions)) :
            foreach ($request->additions as $k => $addition) :
                PartyAddition::create([
                    'party_id' => $party->id,
                    'name_ar' => $addition['name_ar'],
                    'name_en' => $addition['name_en'],
                    'price' => $addition['price'],
                    'is_required' => $addition['is_required'],
                ]);
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
        return view('restaurant.party.party.form', compact('branches', 'action', 'party'));
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
            'image'   => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
            'description_ar' => 'required|string|min:1',
            'description_en' => 'nullable|string|min:1',
            'branch_id' => 'required|integer|exists:restaurant_party_branches,id',
            'price' => 'required|numeric',
            'dates' => 'required|array|min:1',
            'dates.*.date' => 'required|date',
            'dates.*.time_from' => 'required',
            'dates.*.time_to' => 'required',
            'additions' => 'nullable|array|min:1',
            'additions.*.name_ar' => 'min:1|max:190',
            'additions.*.name_en' => 'min:1|max:190',
            'additions.*.is_required' => 'boolean',
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
         
        if ($request->hasFile('image')) {
            deleteImageFile($party->image_path);
            
            $data['image'] =  UploadImage($request->file('image'), 'parties', '/uploads/parties');
        }
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
                    'is_required' => $addition['is_required'],
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
        $party = Party::where('restaurant_id', $restaurant->id)->findOrFail($id);
        try {
            $party->delete();
        } catch (\Throwable $th) {

            flash('لا يمكن الحذف لوجود طلبات')->error();
            return redirect()->route('restaurant.party.index');
        }

        flash(trans('messages.deleted'))->success();
        return redirect()->route('restaurant.party.index');
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

        return view('restaurant.party.party.payment_settings', compact('restaurant'));
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
            return redirect(route('restaurant.party.setting.payment'));
        endif;

        return view('restaurant.party.party.cash_payment', compact('restaurant'));
    }

    public function getSettings(Request $request)
    {
        $restaurant = auth('restaurant')->user();
        if ($request->method() == 'POST') :
            // return $request->all();
            $data = $request->validate([
                'enable_party' => 'required|in:true,false',
                'party_to_restaurant' => 'required|in:true,false',
                'party_is_call_phone' => 'required|in:true,false',
                'party_is_whatsapp' => 'required|in:true,false',
                'party_tax' => 'required|in:true,false',
                'party_tax_value' => 'required_if:party_tax,true|nullable|numeric|min:0.01|max:100',
                'party_call_phone' => 'required_if:party_is_call_phone,true|nullable|numeric',
                'party_whatsapp_number' => 'required_if:party_is_whatsapp,true|nullable|numeric',
                'party_description_ar' => 'nullable|min:1',
                'party_description_en' => 'nullable|min:1',
                'enable_party_email_notification' => 'required|in:true,false', 
                'party_email_notification' => 'nullable|email'
            ]);
            if($request->enable_party_email_notification == 'true'){
                $request->validate([
                    'party_email_notification' => 'required|min:1' , 
                ]);
            }

            flash(trans('messages.updated'))->success();
            $restaurant->update($data);
        endif;
        // return $restaurant;
        return view('restaurant.party.party.settings', compact('restaurant'));
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

<?php

namespace App\Http\Controllers\websiteController\Silver;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailPartyNewRequest;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Party;
use App\Models\PartyBranch;
use App\Models\PartyDay;
use App\Models\PartyDayPeriod;
use App\Models\PartyOrder;
use App\Models\PartyOrderAddition;
use App\Models\PartyOrderField;
use App\Models\PartyOrderFieldOption;
use App\Models\Reservation\ReservationBranch;
use App\Models\Reservation\ReservationOrder;
use App\Models\Reservation\ReservationPlace;
use App\Models\Reservation\ReservationTable;
use App\Models\Reservation\ReservationTableDate;
use App\Models\Reservation\ReservationTablePeriod;
use App\Models\Restaurant;
use App\Models\ServiceSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class PartyController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:web');
    }



    public function page1(Request $request, Restaurant $restaurant)
    {
        if (!auth('web')->check()) :
            session()->put('redirect_to', route('party.page1', $restaurant->id));
            return redirect(route('showUserLogin', [$restaurant->id]));
        endif;
        $this->checkTheme($restaurant);

        $country = $restaurant->country;
        if(!empty($request->branch_id)):
            $request->validate([
                'branch_id' => 'required|integer' 
            ]);
            $branch = PartyBranch::where('restaurant_id' , $restaurant->id)->findOrFail($request->branch_id);
        else:
            $branch = $restaurant->partyBranches()->first();
        endif;

        
        $compact = ['restaurant', 'country', 'branches' , 'branch'];
        $date = date('Y-m-d');
        $branches = PartyBranch::where('restaurant_id', $restaurant->id)->get();
        $parties = Party::where('branch_id', $branch->id)->whereHas('days', function ($query) use ($date) {
            $query->where('date', '>=', $date);
        })->get();
        $isParty = ($restaurant->enable_party == 'true' and (ServiceSubscription::whereRestaurantId($restaurant->id)
            ->whereHas('service', function ($query) {
                $query->where('id', 13);
            })
            ->whereIn('status', ['active', 'tentative'])
            ->first())) ? true : false;

        $compact[]  = 'parties';
        $compact[] = 'isParty';
        return view('website.' . session('theme_path') . 'parties.page1', compact($compact));
    }

    public function getDates(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'party_id' => 'required|integer',
        ]);
        $date = date('Y-m-d');
        $party = Party::where('restaurant_id', $restaurant->id)->with(['days' => function ($query) use ($date) {
            $query->where(DB::raw('date'), '>=', $date)->orderBy('date');
            // ->selectRaw('DISTINCT date');
        }])->find($request->party_id);

        $dates = [];
        if (isset($party->id)) $dates =  array_unique($party->days->pluck('date')->toArray());

        return response([
            'status' => true,
            'data' => $dates,
        ]);
    }

    public function getPeriods(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'party_id' => 'required|integer',
            'date' => 'required|date'
        ]);
        $date = $request->date;
        $party = Party::where('restaurant_id', $restaurant->id)->with(['days' => function ($query) use ($date) {
            $query->where(DB::raw('date'), '=', $date)->orderBy('date');
            // ->selectRaw('DISTINCT date');
        }])->find($request->party_id);
        $periods = PartyDayPeriod::whereHas('day', function ($q) use ($date, $request) {
            $q->where('date', $date)->where('party_id', $request->party_id);
        })->get();

        return response([
            'status' => true,
            'data' => $periods,
        ]);
    }
    public function getFields(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'party_id' => 'required|integer',

        ]);
        $date = $request->date;
        $party = Party::where('restaurant_id', $restaurant->id)->with('additions', 'fields.options')->find($request->party_id);

        $country = $restaurant->country;
        return response([
            'status' => true,
            'data' => view('website.' . session('theme_path') . 'parties.fields', compact('party', 'country', 'restaurant'))->render(),
        ]);
    }

    public function getPartiesDate(Restaurant $restaurant, PartyBranch $branch, $date)
    {
        if (!auth('web')->check()) :
            session()->put('redirect_to', route('party.page1', $restaurant->id));
            return redirect(route('showUserLogin', [$restaurant->id]));
        endif;
        validator([
            'date' => $date
        ], [
            'date' => 'required|date',
        ])->validate();
        $this->checkTheme($restaurant);
        if ($branchx = $restaurant->partyBranches()
            // ->where('status', 1)
            ->findOrFail($branch->id)
        ) $branch = $branchx;



        $country = $restaurant->country;
        $times = PartyDayPeriod::whereHas('day', function ($query) use ($branch, $date) {
            $query->where(DB::raw('date(date)'), '=', $date)->whereHas('party', function ($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            });
        })->with(['day' => function ($query) use ($branch, $date) {
            $query->where(DB::raw('date(date)'), '=', $date)->with('party')->whereHas('party', function ($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            });
        }])->get();;

        return response([
            'status' => true,
            'data' => view('website.' . session('theme_path') . 'parties.dates', compact(['branch', 'times', 'country']))->render()
        ]);
    }

    public function page2(Request $request, Restaurant $restaurant)
    {


        if (!auth('web')->check()) :
            session()->put('redirect_to', route('party.page1', $restaurant->id));
            return redirect(route('showUserLogin', [$restaurant->id]));
        endif;
        $this->checkTheme($restaurant);
        $request->validate([
            'branch_id' => 'required|integer',
            'period_id' => 'required|integer',
        ]);

        $branch = PartyBranch::where('restaurant_id', $restaurant->id)->findOrFail($request->branch_id);
        $country = $restaurant->country;
        $period = PartyDayPeriod::findOrFail($request->period_id);
        $day = $period->day;
        $party = $day->party;
        if ($party->branch_id != $branch->id) :
            abort(404);
        endif;
        return view('website.' . session('theme_path') . 'parties.page2', compact('restaurant', 'branch', 'party', 'period', 'day', 'country'));
    }

    public function storeOrder(Request $request, Restaurant $restaurant)
    {
        $period = PartyDayPeriod::findOrFail($request->period_id);
        $party  = $period->day->party;
        $taxValue = 0;
        $user = auth('web')->user();
        $request->validate([
            'period_id' => 'required|integer',
            'fields' => 'array',
            'additions' => 'array',
            'payment_type' => 'required|in:online,bank,cash',
        ]);

        if ($party->restaurant_id != $restaurant->id) :
            abort(404);
        endif;
        $fields = [];
        if (!empty($request->fields) and is_array($request->fields)) :
            foreach ($request->fields as $key => $value) :
                $f = $party->fields()->findOrFail($key);
                $fields[$key] = [
                    'id' => $f->id,
                    'name_ar' => $f->name_ar,
                    'name_en' => $f->name_en,
                    'f' => $f,
                ];
                if ($f->type == 'checkbox') :
                    if (!empty($value) and is_array($value) and count($value) > 0) :
                        foreach ($value as $k => $v) :
                            $o = $f->options()->findOrFail($v);
                            $fields[$key]['options'][] = [
                                'id' => $o->id,
                                'name_ar' => $o->name_ar,
                                'name_en' => $o->name_en,
                                'f' => $f,
                            ];
                        endforeach;
                    elseif ($f->is_required == 1) :
                        throw ValidationException::withMessages([
                            'fields' => 'يرجي اختيار علي الاقل واحد '
                        ]);
                    endif;
                elseif ($f->type == 'select') :
                    if (!empty($value)) :

                        $o = $f->options()->findOrFail($value);
                        $fields[$key]['options'] = [
                            'id' => $o->id,
                            'name_ar' => $o->name_ar,
                            'name_en' => $o->name_en,
                        ];

                    elseif ($f->is_required == 1) :
                        throw ValidationException::withMessages([
                            'fields' => 'يرجي اختيار علي الاقل واحد '
                        ]);
                    endif;
                else :
                    if (!empty($value)) :


                        $fields[$key]['options'] = [

                            'name_ar' => $value,
                            'name_en' => $value,
                        ];

                    elseif ($f->is_required == 1) :
                        throw ValidationException::withMessages([
                            'fields' => 'يرجي اختيار علي الاقل واحد '
                        ]);
                    endif;
                endif;
            endforeach;
        endif;
        PartyOrder::where('restaurant_id', $restaurant->id)->where('user_id', $user->id)->where('status', 'cart')->delete();
        $order = PartyOrder::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'branch_id' => $party->branch_id,
            'party_id' => $party->id,
            'name_ar' => $party->title_ar,
            'name_en' => $party->title_en,
            'status' => 'cart',
            'payment_type' => $request->payment_type,
            'price' => $party->price,
            'total_price' => $party->price,
            'date' => $period->day->date,
            'time_from' => $period->time_from,
            'time_to' => $period->time_to,
            'num' => rand(1000, 9999)
        ]);
        if (!empty($request->additions) and is_array($request->additions) and count($request->additions) > 0)
            $additions = $party->additions()->whereIn('id', $request->additions)->get();
        else $additions = [];
        $totalPrice = $party->price;
        foreach ($additions as $add) :
            $add->order_id = $order->id;
            $add = $add->toArray();
            PartyOrderAddition::create($add);
            $totalPrice += $add['price'];
        endforeach;

        if ($restaurant->party_tax == 'true' and $restaurant->party_tax_value > 0) :
            $taxValue = ($restaurant->party_tax_value * $totalPrice) / 100;
            $totalPrice += $taxValue;
        endif;

        $order->update([
            'total_price' => $totalPrice,
            'tax' => $taxValue,
        ]);


        foreach ($fields as $field) :
            $d = PartyOrderField::create([
                'order_id' => $order->id,
                'name_ar' => $field['name_ar'],
                'name_en' => $field['name_en'],
                'type' => $field['f']->type,
                'is_required' => $field['f']->is_required,
            ]);
            if ($field['f']->type == 'checkbox') :
                foreach ($field['options'] as $dd) :
                    PartyOrderFieldOption::create([
                        'field_id' => $d->id,
                        'name_ar' => $dd['name_ar'],
                        'name_en' => $dd['name_en'],
                    ]);
                endforeach;
            else :
                PartyOrderFieldOption::create([
                    'field_id' => $d->id,
                    'name_ar' => $field['options']['name_ar'],
                    'name_en' => $field['options']['name_en'],
                ]);
            endif;
        endforeach;

        return redirect(route('party.payment', [$restaurant->id, $order->id]));
    }

    public function page3Payment(Request $request, Restaurant $restaurant, PartyOrder $order)
    {

        if (!auth('web')->check()) :
            session()->put('redirect_to', route('party.page1', $restaurant->id));
            return redirect(route('showUserLogin', [$restaurant->id]));
        endif;
        $this->checkTheme($restaurant);
        /**
         *  1- get request data from user (notes , payment_method)
         *  2- take the reservation_id from hidden input or path at url
         */
        $country = $restaurant->country;
        $branch = $order->branch;

        $reservation = $order; // static
        $reservation->update([
            'online_payment_fees' => null
        ]);
        if ($reservation->payment_type == 'bank') {
            // get the bank_id and transfer_photo from user
            // make the restaurant confirm the -> after confirmed transfer_photo will be null , status->paid
            $banks = Bank::where('restaurant_id', $restaurant->id)->get();
            $type = 'bank';

            return view('website.' . session('theme_path') . 'parties.payment', compact('restaurant', 'branch', 'banks', 'reservation', 'type', 'country'));
        } elseif ($reservation->payment_type == 'online') {
            $type = 'online';
            $banks = Bank::where('restaurant_id', $restaurant->id)->get();
            if ($restaurant->online_payment_fees > 0) {
                $totalPrice = (($reservation->total_price * $restaurant->online_payment_fees) / 100) + $reservation->total_price;
                $reservation->update([
                    'online_payment_fees' => $restaurant->online_payment_fees
                ]);
            } else $totalPrice = $reservation->total_price;
            $totalPrice = round($totalPrice);
            return view('website.' . session('theme_path') . 'parties.payment', compact('restaurant', 'banks', 'branch', 'reservation', 'type', 'country', 'totalPrice'));
        } else { // cash
            $type = $reservation->payment_type;
            $banks = Bank::where('restaurant_id', $restaurant->id)->get();
            return view('website.' . session('theme_path') . 'parties.payment', compact('restaurant', 'banks', 'branch', 'reservation', 'type', 'country'));
        }
        $banks = Bank::where('restaurant_id', $restaurant->id)->get();

        return view('website.' . session('theme_path') . 'parties.payment', compact('restaurant', 'branch', 'banks', 'reservation', 'country'));
    }

    public function storePaymentOrder(Request $request, Restaurant $restaurant, PartyOrder $order)
    {

        if (!auth('web')->check()) :
            session()->put('redirect_to', route('party.page1', $restaurant->id));
            return redirect(route('showUserLogin', [$restaurant->id]));
        endif;
        $this->checkTheme($restaurant);
        // show the order details to user
        $reservation = $order;
        $branch = $order->branch;
        if ($request->method() == 'POST') :
            if ($reservation->payment_type == 'online') :
                if($restaurant->enable_party_email_notification == 'true'):
                    dispatch(new SendMailPartyNewRequest($restaurant->party_email_notification , $order));
                endif;
                if ($restaurant->online_payment_fees > 0) {
                    $totalPrice = (($reservation->total_price * $restaurant->online_payment_fees) / 100) + $reservation->total_price;
                } else $totalPrice = $reservation->total_price;
                $totalPrice = round($totalPrice);
                $amount = $totalPrice;
                // check restaurants payment company
                if ($restaurant->payment_company == 'tap') {
                    return redirect()->to(tap_payment($restaurant->online_token, $amount, $reservation->user->name, $reservation->user->email, $reservation->user->country->code, $reservation->user->phone_number, 'checkReservationTapStatus', $reservation->id));
                } elseif ($restaurant->payment_company == 'express') {
                    $amount = number_format((float)$amount, 2, '.', '');
                    return redirect()->to(express_payment($restaurant->merchant_key, $restaurant->express_password, $amount, 'checkReservationExpressStatus', $reservation->id, $reservation->user->name, $reservation->user->email));
                } elseif ($restaurant->payment_company == 'myFatoourah') {
                    $request->validate([
                        'payment_method' => 'required|in:visa,mada,apple_pay'
                    ]);
                    // get the payment type from user
                    if ($request->payment_method == 'visa') {
                        $charge = 2;
                    } elseif ($request->payment_method == 'mada') {
                        $charge = 6;
                    } elseif ($request->payment_method == 'apple_pay') {
                        $charge = 11;
                    } else {
                        $charge = 2;
                    }
                    $name = !empty($reservation->user->name) ? $reservation->user->name : $restaurant->name;

                    $data = array(
                        'PaymentMethodId' => $charge,
                        'CustomerName' => $name,
                        'DisplayCurrencyIso' => $restaurant->country->currency_code,
                        'MobileCountryCode' => $restaurant->country->code,
                        'CustomerMobile' => $reservation->user->phone_number,
                        'CustomerEmail' => $reservation->user->email,
                        'InvoiceValue' => $amount,
                        'CallBackUrl' => route('checkReservationStatus', $restaurant->id),
                        'ErrorUrl' => url('/error'),
                        'Language' => app()->getLocale(),
                        'CustomerReference' => 'ref 1',
                        'CustomerCivilId' => '12345678',
                        'UserDefinedField' => 'Custom field',
                        'ExpireDate' => '',
                        'CustomerAddress' => array(
                            'Block' => '',
                            'Street' => '',
                            'HouseBuildingNo' => '',
                            'Address' => '',
                            'AddressInstructions' => '',
                        ),
                        'InvoiceItems' => [array(
                            'ItemName' => $name,
                            'Quantity' => '1',
                            'UnitPrice' => $amount,
                        )],
                    );
                    $data = json_encode($data);
                    // $restaurant->online_token
                    $fatooraRes = MyFatoorah($restaurant->online_token, $data);
                    $result = json_decode($fatooraRes);
                    if ($result != null) {
                        if ($result->IsSuccess === true) {
                            if ($restaurant->online_payment_fees > 0) {
                                $totalPrice = (($reservation->total_price * $restaurant->online_payment_fees) / 100) + $reservation->total_price;
                            } else $totalPrice = $reservation->total_price;
                            $totalPrice = round($totalPrice);
                            $reservation->update([
                                'invoice_id' => $result->Data->InvoiceId,
                                'payment_type' => 'online',
                                'online_payment_type' => $request->payment_method,
                                // 'total_price' => $totalPrice ,
                                'online_payment_fees' => $restaurant->online_payment_fees
                            ]);
                            return redirect()->to($result->Data->PaymentURL);
                        } else {
                            return redirect()->to(url('/error'));
                        }
                    } else {
                        return redirect()->to(url('/error'));
                    }
                } else {
                    flash('الدفع الاونلاين غير متاح لدي المطعم')->error();
                }

            elseif ($reservation->payment_type == 'cash') :
                $reservation->update([
                    'payment_type' => 'cash',
                    'bank_id' => null,
                    'transfer_photo' => null,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'online_payment_fees' => null,

                ]);
                if($restaurant->enable_party_email_notification == 'true'):
                    dispatch(new SendMailPartyNewRequest($restaurant->party_email_notification , $order));
                endif;
            else : // bank

                $request->validate([
                    'bank_id' => 'required|integer',
                    'photo' => 'required|image',

                ], [
                    'bank_id.*' => trans('messages.bank_not_found'),
                    'photo.*' => trans('messages.transfer_photo'),
                ]);

                if (!$bank = $restaurant->banks()->find($request->bank_id)) :
                    throw ValidationException::withMessages([
                        'bank_id' => trans('messages.bank_not_found')
                    ]);
                endif;

                $reservation->update([
                    'payment_type' => 'bank',
                    'bank_id' => $bank->id,
                    'bank_photo' => UploadImage($request->file('photo'), 'bank', 'uploads/transfers'),
                    'is_order' => 1,
                    'status' => 'pending',
                    'online_payment_fees' => null,
                ]);
                if($restaurant->enable_party_email_notification == 'true'):
                    dispatch(new SendMailPartyNewRequest($restaurant->party_email_notification , $order));
                endif;
            endif;
        endif;
        $country = $restaurant->country;
        return redirect(route('party.summery', [$restaurant->id, $order->id]));
    }

    public function summery(Request $request, Restaurant $restaurant, PartyOrder $order)
    {

        // $restaurant = $branch->restaurant;
        // if (!auth('web')->check()):
        //     session()->put('redirect_to', route('party.page1', $restaurant->id));
        //     return redirect(route('showUserLogin', [$restaurant->id]));
        // endif;
        $this->checkTheme($restaurant);
        // show the order details to user
        $reservation = $order;
        $branch = $order->branch;
        $country = $restaurant->country;
        return view('website.' . session('theme_path') . 'parties.page4', compact('restaurant', 'branch', 'reservation', 'country'));
    }

    public function check_status(Request $request, $res_id)
    {
        $restaurant = Restaurant::find($res_id);
        $PaymentId = \Request::query('paymentId');
        $token = $restaurant->online_token;
        // $token = self::myFatoorahTestToken;
        $resData = MyFatoorahStatus($token, $PaymentId);
        $result = json_decode($resData);
        if ($result->IsSuccess === true && $result->Data->InvoiceStatus === "Paid") {
            $InvoiceId = $result->Data->InvoiceId;
            $order = ReservationOrder::where('invoice_id', $InvoiceId)->first();
            if (!empty($order->online_payment_fees)) :
                $totalPrice = (($order->total_price * $order->online_payment_fees / 100) + $order->total_price);
            else :
                $totalPrice = $order->total_price;
            endif;
            $order->update([
                // 'invoice_id' => null,
                'status' => 'paid',
                'is_order' => 1,
                // 'is_confirm' => 1 , 
                'total_price' => $totalPrice
            ]);
            if ($order->table->isExpire()) :
                $order->table->update([
                    'is_available' => 0,
                ]);
            endif;

            // $order->period->update([
            //     'status' => 'not_available',
            // ]);
            flash(trans('messages.reservationOnlinePaymentDone'))->success();
            return redirect()->route('reservation.page4', [$order->table->reservation_branch_id, $order->id]);
        } else {
            return 'fail payment';
        }
    }

    public function check_tap_status(Request $request, $order_id)
    {
        $input = $request->all();
        $tap_id = $input['tap_id'];
        $basURL = "https://api.tap.company/v2/charges/" . $tap_id;
        $order = ReservationOrder::findOrFail($order_id);
        $restaurant = $order->restaurant;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $basURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "{}",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $restaurant->online_token
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response);
            if ($response->response->code == '000') {
                $order->update([
                    'invoice_id' => null,
                    'status' => 'paid',
                    'is_order' => 1,
                ]);
                $isExpire = $order->table->isExpire();
                if ($isExpire == true) :
                    $order->table->update([
                        'is_available' => 0,
                    ]);
                else :
                    $order->table->update([
                        'is_available' => 1,
                    ]);
                endif;
                flash(trans('messages.reservationOnlinePaymentDone'))->success();
                return redirect()->route('reservation.page4', [$order->table->reservation_branch_id, $order->id]);
            }
        }
    }
    public function check_express_status($order_id)
    {
        $order = ReservationOrder::findOrFail($order_id);
        $order->update([
            'invoice_id' => null,
            'status' => 'paid',
            'is_order' => 1,
        ]);
        flash(trans('messages.reservationOnlinePaymentDone'))->success();
        return redirect()->route('reservation.page4', [$order->table->reservation_branch_id, $order->id]);
    }


    public function loadPackageDetails(Restaurant $restaurant, $id, $date)
    {
        $period = ReservationTablePeriod::with('table')->findOrFail($id);
        $table = $period->table;
        $country = $restaurant->country;
        $order = DB::select('select sum(chairs) as quantity  from reservation_orders as ro where ro.reservation_table_id = ' . $table->id . ' and ro.period_id = ' . $period->id . ' and date = "' . $date . '" and ro.is_order = 1 group by ro.period_id');

        if (isset($order[0]->quantity)) :
            $quantity = $table->chair_max - $order[0]->quantity;
        else :
            $quantity = $table->chair_max;
        endif;
        return view('website.' . session('theme_path') . 'parties.package-details', compact('period', 'table', 'date', 'restaurant', 'country', 'quantity'));
    }
}

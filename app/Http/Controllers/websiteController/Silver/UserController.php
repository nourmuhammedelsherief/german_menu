<?php

namespace App\Http\Controllers\websiteController\Silver;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Country;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyPointHistory;
use App\Models\LoyaltyPointPrice;
use App\Models\Restaurant;
use App\Models\ServiceSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show_register($id = null)
    {
        if (auth('web')->check()) :
        // return 
        endif;
        if ($id != null) {
            $restaurant = Restaurant::findOrFail($id);
        } else {
            $restaurant = Restaurant::findOrFail(session('current_restaurant', 276));
        }
        if (auth('web')->check()) {
            return redirect(route('sliverHome', [$restaurant->name_barcode]));
        }
        $verifiyCode = true;
        if ($restaurant->serviceSubscriptions()->whereIn('service_id', [4, 9, 10])->where('status', 'active')->whereNotNull('paid_at')->count() > 0) $verifiyCode = true; // for test
        $this->checkTheme($restaurant);
        $countries = Country::orderBy('created_at', 'asc')->get();
        return view('website.' . session('theme_path') . 'silver.accessories.user.register', compact('restaurant', 'countries', 'verifiyCode'));
    }
    public function register(Request $request, $id)
    {
        $restaurant = Restaurant::find($id);
        // test
        $this->checkTheme($restaurant);
        $this->validate($request  , [
            'country_id' => 'required|exists:countries,id',
            'phone_number' => ['required', 'regex:/^((05)|(01)|())[0-9]{8}/'],
        ]);

        $check_user = User::whereCountryId($request->country_id)
            ->where('phone_number', $request->phone_number)
            ->first();
        if ($check_user == null) {
            // create new user
            $user = User::create([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'country_id'  => $request->country_id,
                'active' => 'true',
                'register_restaurant_id' => $restaurant->id,
            ]);
        } else {
            $user = $check_user;
        }
        Auth::guard('web')->login($user, true);
        return redirect()->route('sliverHome', $restaurant->name_barcode);
    }

    public function verify(Request $request, $id, $res)
    {
        $user = User::find($id);
        $restaurant = Restaurant::find($res);
        $this->validate($request, [
            'code' => 'required',
        ]);
        $code = $request->code;
        // return session()->all();
        $check = false;
        // check recapcha google 
        $recapchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recapcha.secret_key'),
            'response' => $request->recapcha_token,
            'remoteip' => request()->ip(),
        ]);

        $dd  = $recapchaResponse->json();
        if (!isset($dd['success']) or $dd['success'] !== true) :
            return response([
                'status' => false,
                'msg' => trans('messages.recapcha_fail'),
            ]);
        endif;
        if ($user->verification_code == $code) {
            $check = true;
            $user->update([
                'active' => 'true'
            ]);

            Auth::guard('web')->login($user, true);
            if (session()->has('last_order')) :
                $myRequest = new Request(session('last_order'));
                $con = new OrderController();
                session()->flash('come_from_login', true);
                return $con->add_to_cart($myRequest);
            endif;
        } else {
            return response([
                'status' => false,
                'msg' => trans('messages.verify_code_incorrect'),
            ]);
        }
        if (session()->has('redirect_to')) :
            $url = session('redirect_to');
            session()->forget('redirect_to');
            if ($request->wantsJson()) :
                return response([
                    'status' => $check,
                    'redirect_to' => $url,
                    'msg' => $check == true ? trans('messages.login_success') : trans('messages.login_fail')
                ]);
            endif;
            return redirect($url);
        endif;
        if ($request->wantsJson()) :
            return response([
                'status' => $check,
                'redirect_to' => route('sliverHome', $restaurant->name_barcode),
                'msg' => $check == true ? trans('messages.login_success') : trans('messages.login_fail')
            ]);
        endif;
        return redirect()->route('sliverHome', $restaurant->name_barcode);
    }

    public function show_forget_password($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $this->checkTheme($restaurant);
        return view('website.' . session('theme_path') . 'silver.accessories.user.forget_password', compact('restaurant'));
    }
    public function forget_password(Request $request, $res)
    {
        $restaurant = Restaurant::find($res);
        $this->checkTheme($restaurant);
        $this->validate($request, [
            'phone_number' => ['required', 'regex:/^((05)|(01))[0-9]{8}/'],
        ]);
        $user = User::where('phone_number', $request->phone_number)->first();
        if ($user) {
            $code = mt_rand(1000, 9999);
            if (env('APP_SMS_TEST', false)) {
                $code = '2222';
            }
            $country = $user->country->code;
            // send code to phone_number
            $msg = app()->getLocale() == 'ar' ? 'كود التحقق الخاص بك في أيزي منيو للزبون هو' . ' : ' . $code . '  ' . 'مؤسسة تقني' : 'EasyMenu verification code is : ' . $code . '  ' . 'مؤسسة تقني';
            $check = substr($request->phone_number, 0, 2) === '05';
            if ($check == true) {
                $phone = $country . ltrim($request->phone_number, '0');
            } else {
                $phone = $country . $request->phone_number;
            }
            taqnyatSms($msg, $phone);
            $user->update([
                'verification_code' => $code
            ]);
            return view('website.' . session('theme_path') . 'silver.accessories.user.forget_verify', compact('user', 'res'));
        } else {
            flash(trans('messages.phoneNotFound'))->error();
            return redirect()->route('sliverHome', $restaurant->name_barcode);
        }
    }

    public function forget_verify(Request $request, $user, $res)
    {
        $user = User::find($user);
        $restaurant = Restaurant::find($res);
        $this->checkTheme($restaurant);
        $this->validate($request, [
            'code' => 'required',
        ]);
        $code = $request->code;
        if ($user->verification_code == $code) {
            return view('website.' . session('theme_path') . 'silver.accessories.user.reset_password', compact('user', 'res'));
        } else {
            return redirect()->route('sliverHome', $restaurant->name_barcode);
        }
    }
    public function reset_password(Request $request, $user, $res)
    {
        $user = User::find($user);
        $restaurant = Restaurant::find($res);
        $this->checkTheme($restaurant);
        $this->validate($request, [
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        Auth::login($user);
        return redirect()->route('sliverHome', $restaurant->name_barcode);
    }
    public function show_login($res_id, $branch_id = null)
    {
        $restaurant = Restaurant::findOrFail($res_id);
        $this->checkTheme($restaurant);
        if ($branch_id != null) {
            $branch = Branch::find($branch_id);
        } else {
            $branch = Branch::whereRestaurantId($restaurant->id)
                ->where('main', 'true')
                ->first();
        }
        $this->saveLastUrl();
        // return session()->all();
        return view('website.' . session('theme_path') . 'silver.accessories.user.login', compact('restaurant', 'branch'));
    }
    private function saveLastUrl()
    {
        $url = session('_previous.url');

        $data = [
            'ser/login', 'ser/register', 'ser/forget_password'
        ];
        foreach ($data as $item) :
            if (isUrlActive($item)) return false;
        endforeach;
        session()->put('redirect_to', $url);
        return true;
    }
    public function login(Request $request, $res_id, $branch_id = null)
    {
        $restaurant = Restaurant::findOrFail($res_id);
        $this->checkTheme($restaurant);
        if ($branch_id != null) {
            $branch = Branch::find($branch_id);
        } else {
            $branch = Branch::whereRestaurantId($restaurant->id)
                ->where('main', 'true')
                ->first();
        }
        $this->validate($request, [
            'phone_number' => ['required', 'regex:/^((05)|(01))[0-9]{8}/'],
            'password' => 'required|min:6',
        ]);
        $credential = [
            'phone_number' => $request->phone_number,
            'password' => $request->password,
            'active' => 'true',
        ];
        // test-code

        if (Auth::guard('web')->attempt($credential, true)) {
            if (session()->has('redirect_to')) return redirect(session('redirect_to'));
            if ($branch->main == 'true') {
                return redirect()->route('sliverHome', $restaurant->name_barcode);
            } else {
                return redirect()->route('sliverHomeBranch', [$restaurant->name_barcode, $branch->name_barcode]);
            }
        } else {
            flash(trans('messages.error_login'))->error();
            return redirect()->back();
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        return redirect()->back();
    }

    public function loyalty_points($id, $branch)
    {
        $user = auth('web')->user();
        $restaurant = Restaurant::findOrFail($id);
        // return $restaurant;
        $branch = $restaurant->branches()
            // ->where('status' , 'active')
            ->findOrFail($branch);
        // $branch = $restaurant->branches()->where('main' , 'true')->first();

        // check loyalty_points
        $loyaltySubscription =  ServiceSubscription::whereRestaurantId($restaurant->id)->whereHas('service', function ($query) {
            $query->where('id', 11);
        })
            ->whereIn('status', ['active', 'tentative'])
            ->where('branch_id', $branch->id)
            ->first();
        if ($restaurant->enable_loyalty_point != 'true' or !isset($loyaltySubscription->id)) {
            abort(404);
        }

        $table = null;
        if ($checkPoints = LoyaltyPoint::where('restaurant_id', $restaurant->id)->where('user_id', $user->id)->where('type', 'point')->first()) {
            $points = $checkPoints->amount;
        } else $points = 0;

        $ordersCount = LoyaltyPointHistory::where('restaurant_id', $restaurant->id)->where('user_id', $user->id)->whereHas('order', function ($query) use ($branch) {
            $query->where('branch_id', $branch->id);
        })->count();
        if ($checkBalance = LoyaltyPoint::where('restaurant_id', $restaurant->id)->where('user_id', $user->id)->where('type', 'balance')->first()) {
            $totalBalance = $checkBalance->amount;
        } else $totalBalance = 0;
        $priceList = LoyaltyPointPrice::orderBy('points')->get();
        // get hint of What balance have when conver points to rayal
        $priceItem = null;
        foreach ($priceList as $item) :
            if ($item->points <= $points) :
                $priceItem = $item;
            endif;
        endforeach;

        $hint = null;
        if (isset($priceItem->id)) :
            $quantity = 0;
            $tempPoints = $points;
            do {
                $tempPoints -= $priceItem->points;
                $quantity++;
            } while ($tempPoints > $priceItem->points);
            $expectedPrice = $priceItem->price * $quantity;
            $expectedLostPoints = $priceItem->points * $quantity;
            $hint = trans('messages.loyalty_point_expected_convert', ['points' => $expectedLostPoints, 'price' => $expectedPrice]);
        endif;

        return view('website.' . session('theme_path') . 'silver.accessories.loyalty_point', compact('restaurant', 'branch', 'table', 'points', 'totalBalance', 'priceList', 'hint', 'ordersCount'));
    }
    public function convertLoyaltyPoint($id)
    {
        $user = auth('web')->user();
        $restaurant = Restaurant::findOrFail($id);
        $branch = $restaurant->branches()->where('main', 'true')->first();
        $table = null;
        if ($checkPoints = LoyaltyPoint::where('restaurant_id', $restaurant->id)->where('user_id', $user->id)->where('type', 'point')->first()) {
            $points = $checkPoints->amount;
        } else {
            $points = 0;
            $checkPoints = LoyaltyPoint::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'type' => 'point',
                'amount' => 0
            ]);
        };
        if ($checkBalance = LoyaltyPoint::where('restaurant_id', $restaurant->id)->where('user_id', $user->id)->where('type', 'balance')->first()) {
            $totalBalance = $checkBalance->amount;
        } else {
            $totalBalance = 0;
            $checkBalance = LoyaltyPoint::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'type' => 'balance',
                'amount' => 0
            ]);
        }
        $priceList = LoyaltyPointPrice::orderBy('points')->get();
        // get hint of What balance have when conver points to rayal
        $priceItem = null;
        foreach ($priceList as $item) :
            if ($item->points <= $points) :
                $priceItem = $item;
            endif;
        endforeach;

        if (isset($priceItem->id)) :
            $quantity = 0;
            $tempPoints = $points;
            do {
                $tempPoints -= $priceItem->points;
                $quantity++;
            } while ($tempPoints > $priceItem->points);
            $expectedPrice = $priceItem->price * $quantity;
            $expectedLostPoints = $priceItem->points * $quantity;
            $checkBalance->update([
                'amount' => $totalBalance + $expectedPrice,
            ]);
            $checkPoints->update([
                'amount' => $points - $expectedLostPoints,
            ]);

            return redirect()->back()->withSuccess(trans('messages.loyalty_point_convert_balance_success', ['points' => $expectedLostPoints, 'price' => $expectedPrice]));
        else :
            return redirect()->back()->withError(trans('messages.error_loyalty_points_not_enough'));
        endif;
    }
}

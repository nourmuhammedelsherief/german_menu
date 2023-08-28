<?php

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Poster;
use App\Models\Restaurant;
use App\Models\RestaurantEmployee;
use App\Models\RestaurantSensitivity;
use App\Models\RestaurantSlider;
use App\Models\Sensitivity;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use App\Models\Setting;
use App\Models\TableOrder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;


//use FCM;
function SetUserName($id)
{
    \Illuminate\Support\Facades\Session::forget('pid');
    \Illuminate\Support\Facades\Session::put('pid', $id);
}


function explodeByComma($str)
{
    return explode(",", $str);
}

function explodeByDash($str)
{
    return explode("-", $str);
}

function imgPath($folderName)
{

    //عشان ال sub domain  بس هيشها مؤقتا
    //    return '/uploads/' . $folderName . '/';
    return '/public/uploads/' . $folderName . '/';
}

function settings()
{

    return Setting::where('id', 1)->first();
}

function validateRules($errors, $rules)
{

    $error_arr = [];

    foreach ($rules as $key => $value) {

        if ($errors->get($key)) {

            array_push($error_arr, array('key' => $key, 'value' => $errors->first($key)));
        }
    }

    return $error_arr;
}

//function randNumber($userId, $length) {
//
//    $seed = str_split('0123456789');
//
//    shuffle($seed);
//
//    $rand = '';
//
//    foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];
//
////    return $userId * $userId . $rand;
//    return $userId . $rand;
//}

function randNumber($length)
{

    $seed = str_split('0123456789');

    shuffle($seed);

    $rand = '';

    foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];

    return $rand;
}

function generateApiToken($userId, $length)
{

    $seed = str_split('abcdefghijklmnopqrstuvwxyz' . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . '0123456789');

    shuffle($seed);

    $rand = '';

    foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];

    return $userId * $userId . $rand;
}

function UploadBase64Image($base64Str, $prefix, $folderName)
{

    $image = base64_decode($base64Str);
    $image_name = $prefix . '_' . time() . '.png';
    $path = public_path('uploads') . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . $image_name;

    $saved = file_put_contents($path, $image);

    return $saved ? $image_name : NULL;
}


function gold_services($id, $service_id, $end_at)
{
    $restaurant = Restaurant::find($id);
    $service = \App\Models\Service::find($service_id);
    $check_subscription = \App\Models\ServiceSubscription::whereRestaurantId($restaurant->id)
        ->whereServiceId($service->id)
        ->first();
    if ($check_subscription == null) {
        \App\Models\ServiceSubscription::create([
            'restaurant_id' => $restaurant->id,
            'service_id' => $service->id,
            'restaurant_name' => $restaurant->name_ar,
            'restaurant_phone' => $restaurant->phone_number,
            'price'  => $service->price,
            'paid_at' => \Carbon\Carbon::now(),
            'type'  => 'online',
            'end_at' => $end_at,
            'status' => 'active',
        ]);
    } else {
        $check_subscription->update([
            'end_at' => $end_at,
            'status' => 'active',
        ]);
    }
}


function UploadImage($inputRequest, $prefix, $folderNam)
{

    if (in_array($inputRequest->getClientOriginalExtension(), ['gif'])) :
        return basename(Storage::disk('public_storage')->put($folderNam, $inputRequest));
    endif;
    $folderPath = public_path($folderNam);
    if (!File::isDirectory($folderPath)) {

        File::makeDirectory($folderPath, 0777, true, true);
    }
    $image = time() . '' . rand(11111, 99999) . '.' . $inputRequest->getClientOriginalExtension();
    $destinationPath = public_path('/' . $folderNam);
    $img = Image::make($inputRequest->getRealPath());
    $img->resize(500, 500, function ($constraint) {
        $constraint->aspectRatio();
    })->save($destinationPath . '/' . $image);

    return $image ? $image : false;
}

function copyImage($filename, $prefix, $folderNam)
{
    if (!Storage::disk('public_storage')->exists($filename)) return '';
    $temp = explode('.', $filename);
    $ext = $temp[count($temp) - 1];
    $image = 'copy_' . time() . '' . rand(11111, 99999) . '.' . $ext;
    $destinationPath = public_path('/' . $folderNam);
    if (!Storage::disk('public_storage')->exists($folderNam)) :
        File::isDirectory($destinationPath) or File::makeDirectory($destinationPath, 0777, true, true);
    endif;
    $img = Image::make(public_path($filename));
    $img->save($destinationPath . '/' . $image);

    return $image ? $image : false;
}

function UploadFile($inputRequest, $prefix, $folderNam)
{

    $imageName = $prefix . '_' . time() . '.' . $inputRequest->getClientOriginalExtension();

    $destinationPath = public_path('/' . $folderNam);
    $inputRequest->move($destinationPath, $imageName);
    // dd($destinationPath);

    return $imageName ? $imageName : false;
}

function UploadFileEdit($inputRequest, $prefix, $folderNam, $old = null)
{
    if ($old) {
        @unlink(public_path('/uploads/files/' . $old));
    }

    $imageName = $prefix . '_' . time() . '.' . $inputRequest->getClientOriginalExtension();

    $destinationPath = public_path('/' . $folderNam);
    $inputRequest->move($destinationPath, $imageName);
    // dd($destinationPath);

    return $imageName ? $imageName : false;
}

function UploadVideo($file)
{
    if ($file) {
        $filename = $file->getClientOriginalName();
        $path = public_path() . '/uploads/videos';
        $file->move($path, $filename);
        return $filename;
    }
}

function UploadVideoEdit($file, $old)
{
    if ($old) {
        @unlink(public_path('/uploads/videos/' . $old));
    }
    if ($file) {
        $filename = $file->getClientOriginalName();
        $path = public_path() . '/uploads/videos';
        $file->move($path, $filename);
        return $filename;
    }
}

function UploadImageEdit($inputRequest, $prefix, $folderNam, $oldImage, $height = null, $width = 1500)
{
    if ($oldImage != 'logo.png' && $oldImage != 'slider2.png' && $oldImage != 'slider1.png' && $oldImage != 'fish.png' && $oldImage != 'egg.png' && $oldImage != 'hop.png' && $oldImage != 'aqra.png' && $oldImage != 'milk.png' && $oldImage != 'kardal.png' && $oldImage != 'raky.png' && $oldImage != 'butter.png' && $oldImage != 'capret.png' && $oldImage != 'rfs.png' && $oldImage != 'kago.png' && $oldImage != 'smsm.png' && $oldImage != 'soia.png' && $oldImage != 'terms.png') {
        @unlink(public_path('/' . $folderNam . '/' . $oldImage));
    }
    $path = public_path() . $folderNam;


    if (!file_exists($path)) :
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
    endif;
    if (in_array($inputRequest->getClientOriginalExtension(), ['gif'])) {
        return basename(Storage::disk('public_storage')->put($folderNam, $inputRequest));
    }
    $image = time() . '' . rand(11111, 99999) . '.' . $inputRequest->getClientOriginalExtension();
    $destinationPath = public_path('/' . $folderNam);
    $img = Image::make($inputRequest->getRealPath());
    $img->resize($height, $width, function ($constraint) {
        $constraint->aspectRatio();
        // $constraint->upsize();
    })->save($destinationPath . '/' . $image);
    return $image ? $image : false;
}



function sendNotification($notificationTitle, $notificationBody, $deviceToken)
{

    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(60 * 20);

    $notificationBuilder = new PayloadNotificationBuilder($notificationTitle);
    $notificationBuilder->setBody($notificationBody)
        ->setSound('default');

    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData(['a_data' => 'my_data']);

    $option = $optionBuilder->build();
    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();

    $token = $deviceToken;

    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

    $downstreamResponse->numberSuccess();
    $downstreamResponse->numberFailure();
    $downstreamResponse->numberModification();

    //return Array - you must remove all this tokens in your database
    $downstreamResponse->tokensToDelete();

    //return Array (key : oldToken, value : new token - you must change the token in your database )
    $downstreamResponse->tokensToModify();

    //return Array - you should try to resend the message to the tokens in the array
    $downstreamResponse->tokensToRetry();

    // return Array (key:token, value:errror) - in production you should remove from your database the tokens
}

function sendMultiNotification($notificationTitle, $notificationBody, $devicesTokens)
{

    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(60 * 20);

    $notificationBuilder = new PayloadNotificationBuilder($notificationTitle);
    $notificationBuilder->setBody($notificationBody)
        ->setSound('default');

    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData(['a_data' => 'my_data']);

    $option = $optionBuilder->build();
    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();

    // You must change it to get your tokens
    $tokens = $devicesTokens;

    $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

    $downstreamResponse->numberSuccess();
    $downstreamResponse->numberFailure();
    $downstreamResponse->numberModification();

    //return Array - you must remove all this tokens in your database
    $downstreamResponse->tokensToDelete();

    //return Array (key : oldToken, value : new token - you must change the token in your database )
    $downstreamResponse->tokensToModify();

    //return Array - you should try to resend the message to the tokens in the array
    $downstreamResponse->tokensToRetry();

    // return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
    $downstreamResponse->tokensWithError();

    return ['success' => $downstreamResponse->numberSuccess(), 'fail' => $downstreamResponse->numberFailure()];
}

function saveNotification($userId, $title, $message, $type, $order_id = null, $device_token = null)
{

    $created = \App\UserNotification::create([
        'user_id' => $userId,
        'title' => $title,
        'type' => $type,
        'message' => $message,
        'order_id' => $order_id,
        'device_token' => $device_token,
    ]);
    return $created;
}

function check_time_between($start_at, $end_at)
{
    if ($start_at == null and $end_at == null) {
        return true;
    }
    $now = \Carbon\Carbon::now()->format('H:i:s');
    if ($start_at > $end_at) {
        // the end at another day
        if ($start_at < $now) {
            $start = \Carbon\Carbon::now()->format('Y-m-d' . ' ' . $start_at);
            $end  = \Carbon\Carbon::now()->addDay()->format('Y-m-d' . ' ' . $end_at);
            $check = \Carbon\Carbon::now()->between($start, $end, true);
        } else {
            $start = \Carbon\Carbon::now()->addDays(-1)->format('Y-m-d' . ' ' . $start_at);
            $end  = \Carbon\Carbon::now()->format('Y-m-d' . ' ' . $end_at);
            $check = \Carbon\Carbon::now()->between($start, $end, true);
        }
    } else {
        $start = \Carbon\Carbon::now()->format('Y-m-d' . ' ' . $start_at);
        $end  = \Carbon\Carbon::now()->format('Y-m-d' . ' ' . $end_at);
        $check = \Carbon\Carbon::now()->between($start, $end, true);
    }
    return $check;
}


####### Check Payment Status ######
function MyFatoorahStatus($api, $PaymentId)
{
    // dd($PaymentId);
    $token = $api;
    $basURL = "https://api-sa.myfatoorah.com";
    if (env('APP_PAYMENT_TEST', false)) {
        $basURL = myfatooraUrlTest;
        $token = myfatooraTokenTest;
    }
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "$basURL/v2/GetPaymentStatus",
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"Key\": \"$PaymentId\",\"KeyType\": \"PaymentId\"}",
        CURLOPT_HTTPHEADER => array("Authorization: Bearer $token", "Content-Type: application/json"),
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {

        return $err;
    } else {
        return $response;
    }
}

// ===============================  MyFatoorah public  function  =========================
function MyFatoorah($api, $userData)
{
    $token = $api;
    // $token = Controller
    $basURL = "https://api-sa.myfatoorah.com";
    if (env('APP_PAYMENT_TEST', false)) {
        $basURL = myfatooraUrlTest;
        $token = myfatooraTokenTest;
    }
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "$basURL/v2/ExecutePayment",
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $userData,
        CURLOPT_HTTPHEADER => array("Authorization: Bearer $token", "Content-Type: application/json"),
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        return $response;
    }
}

/**
 * calculate the distance between tow places on the earth
 *
 * @param latitude $latitudeFrom
 * @param longitude $longitudeFrom
 * @param latitude $latitudeTo
 * @param longitude $longitudeTo
 * @return double distance in KM
 */
function distanceBetweenTowPlaces($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
{
    $long1 = deg2rad($longitudeFrom);
    $long2 = deg2rad($longitudeTo);
    $lat1 = deg2rad($latitudeFrom);
    $lat2 = deg2rad($latitudeTo);
    //Haversine Formula
    $dlong = $long2 - $long1;
    $dlati = $lat2 - $lat1;
    $val = pow(sin($dlati / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($dlong / 2), 2);
    $res = 2 * asin(sqrt($val));
    $radius = 6367.756;
    return ($res * $radius);
}


/**
 *  Taqnyat sms to send message
 */
function taqnyatSms($msgBody, $reciver)
{
    $setting = Setting::find(1);
    $bearer = $setting->bearer_token;
    $sender = $setting->sender_name;
    $taqnyt = new TaqnyatSms($bearer);

    $body = $msgBody;
    $recipients = $reciver;
    $message = $taqnyt->sendMsg($body, $recipients, $sender);
    return $message;
}

function checkOrderService($restaurant_id, $service_id, $branch_id = null)
{
    if ($branch_id) {
        $service = \App\Models\ServiceSubscription::whereRestaurantId($restaurant_id)
            ->where('service_id', $service_id)
            ->whereIn('status', ['active', 'tentative'])
            ->whereBranchId($branch_id)
            ->first();
    } else {
        $service = \App\Models\ServiceSubscription::whereRestaurantId($restaurant_id)
            ->where('service_id', $service_id)
            ->whereIn('status', ['active', 'tentative'])
            ->first();
    }
    return !($service == null) ? true : false;
}
function checkOrderSetting($restaurant_id, $type, $branch_id = null)
{
    if ($branch_id) {
        $setting = \App\Models\RestaurantOrderSetting::whereRestaurantId($restaurant_id)
            ->where('order_type', $type)
            ->whereBranchId($branch_id)
            ->first();
    } else {
        $setting = \App\Models\RestaurantOrderSetting::whereRestaurantId($restaurant_id)
            ->where('order_type', $type)
            ->first();
    }
    return !($setting == null) ? true : false;
}


function check_branch_periods($id)
{
    $branch = \App\Models\Branch::find($id);
    // check if the branch has periods or not
    $current_day = \Carbon\Carbon::now()->format('l');
    $day = \App\Models\Day::whereNameEn($current_day)->first()->id;
    $periods = \App\Models\RestaurantPeriod::with('days')
        ->whereHas('days', function ($q) use ($day) {
            $q->where('day_id', $day);
        })
        ->where('restaurant_id', $branch->restaurant_id)
        ->where('branch_id', $branch->id)
        ->get();
    if ($periods->count() > 0) {
        foreach ($periods as $period) {
            $state = check_time_between($period->start_at, $period->end_at);
            if ($state == true) {
                return $state;
            }
        }
        return false;
    } else {
        $check_period = \App\Models\RestaurantPeriod::where('restaurant_id', $branch->restaurant_id)
            ->where('branch_id', $branch->id)
            ->count();
        return $check_period > 0 ? false : true;
    }
}

function check_restaurant_permission($res_id, $permission_id)
{
    $permission = \App\Models\RestaurantPermission::whereRestaurantId($res_id)
        ->wherePermissionId($permission_id)
        ->first();
    return !($permission == null) ? true : false;
}


function auth_paymob()
{
    $basURL = "https://accept.paymob.com/api/auth/tokens";
    $headers = array(
        'Content-type: application/json',
        'Accept: application/json',
    );

    $order = array(
        "api_key" => "ZXlKaGJHY2lPaUpJVXpVeE1pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SmpiR0Z6Y3lJNklrMWxjbU5vWVc1MElpd2libUZ0WlNJNklqRTJOVGd6TWpjeE9URXVNamczT1RreElpd2ljSEp2Wm1sc1pWOXdheUk2TWpRd05UWTRmUS5Zb1lOY3ZOenN6aVltLS1WaDlnalFVdzR5dDk4N3U0Q0hwalZJVVpoallvNmdST1lPVlpBNW1feDQzLWZjdlY2ME1VejhCTXM0VFdLaWtvQmN4UWZLQQ=="
    );
    $order = json_encode($order);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $basURL,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $order,
        CURLOPT_HTTPHEADER => $headers,
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        dd($response);
        return $response;
    }
}

function paymob()
{
    $basURL = "https://accept.paymob.com/api/ecommerce/orders";
    $headers = array(
        'Content-type: application/json',
        'Accept: application/json',
    );

    $order = array(
        "auth_token" => "ZXlKaGJHY2lPaUpJVXpVeE1pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SmpiR0Z6Y3lJNklrMWxjbU5vWVc1MElpd2ljR2hoYzJnaU9pSmpZelV5TTJFNVptVXdZVFEzTmpCbFlqTm1NRFkyWkRReU9XSXdabVppWXpabU1EUXlOemhqWWpoak16SXhOV0ZqTkdSaFpUUTJNV0ZqTldFd1lUVmpJaXdpWlhod0lqb3hOalU0TXpNM05qSTJMQ0p3Y205bWFXeGxYM0JySWpveU5EQTFOamg5LjhxTDUxV0VIeUsxNUZHZWxMd09rWU9mTHJFSEdsdU1jY2JmYjNCcjZuNG5HY21rT0NuaWpYU3lWdHhSZFl6RFc4QW9nRnlIeENVT2J4WGtvcEZPVG1R",
        "delivery_needed" => "false",
        "amount_cents" => "100",
        "currency" => "EGP",
        "merchant_order_id" => 10045,
        "items" => array(
            array(
                "name" => "ASC1515",
                "amount_cents" => "500000",
                "description" => "Smart Watch",
                "quantity" => "1"
            ),
        ),
        "shipping_data" => array(
            "apartment" => "803",
            "email" => "claudette09@exa.com",
            "floor" => "42",
            "first_name" => "Clifford",
            "street" => "Ethan Land",
            "building" => "8028",
            "phone_number" => "+86(8)9135210487",
            "postal_code" => "01898",
            "extra_description" => "8 Ram , 128 Giga",
            "city" => "Jaskolskiburgh",
            "country" => "CR",
            "last_name" => "Nicolas",
            "state" => "Utah"
        ),
        "shipping_details" => array(
            "notes" => " test",
            "number_of_packages" => 1,
            "weight" => 1,
            "weight_unit" => "Kilogram",
            "length" => 1,
            "width" => 1,
            "height" => 1,
            "contents" => "product of some sorts"
        )
    );
    $order = json_encode($order);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $basURL,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $order,
        CURLOPT_HTTPHEADER => $headers,
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        $response = array_values(json_decode($response, true));
        dd($response);
        return $response;
    }
}

function meal_accessories($id, $product)
{
    if (get_meals_photos($id) != [] && get_meals_photos($id) != null) {
        if (is_array(get_meals_photos($id))) {
            if (is_countable(get_meals_photos($id))) {
                foreach (get_meals_photos($id) as $photo) {
                    $infoM = pathinfo('https://old.easymenu.site/uploads/meals/' . $photo['image']);
                    if ($infoM['extension'] != 'jpg_1700Wx1700H' && $infoM['extension'] != 'crdownload' && $infoM['extension'] != 'application/x-empty') {
                        if ($infoM['extension'] == 'JPG' || $infoM['extension'] == 'JPEG' || $infoM['extension'] == 'PNG' || $infoM['extension'] == 'wepb' || $infoM['extension'] == 'png' || $infoM['extension'] == 'jpg' || $infoM['extension'] == 'gif' || $infoM['extension'] == 'tif') {
                            $contentsM = file_get_contents('https://old.easymenu.site/uploads/meals/' . $photo['image']);
                            $fileM = '/tmp/' . $infoM['basename'];
                            file_put_contents($fileM, $contentsM);
                            $imageM = $infoM['basename'];
                            $destinationPathM = public_path('/' . 'uploads/products');
                            $imgM = Image::make($fileM);
                            $imgM->resize(500, 500, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save($destinationPathM . '/' . $imageM);
                            // \App\Models\ProductPhoto::create([
                            //     'product_id' => $product->id,
                            //     'photo' => $imageM,
                            // ]);
                            $product->update([
                                'photo' => $imageM,
                            ]);
                        }
                    }
                }
            }
        }
    }
    if (get_meals_sizes($id) != []) {
        if (is_array(get_meals_sizes($id)) || is_object(get_meals_sizes($id)) && count(get_meals_sizes($id)) > 0) {
            foreach (get_meals_sizes($id) as $size) {
                // create meal sizes
                \App\Models\ProductSize::create([
                    'name_ar' => $size['size_ar'],
                    'name_en' => $size['size'],
                    'price' => $size['price'],
                    'calories' => $size['calories'],
                    'product_id' => $product->id,
                ]);
            }
        }
    }
    if (get_meals_modifiers($id) != []) {
        if (is_array(get_meals_modifiers($id)) || is_object(get_meals_modifiers($id)) && count(get_meals_modifiers($id)) > 0) {

            foreach (get_meals_modifiers($id) as $pm) {
                // create meal sizes
                \App\Models\ProductModifier::create([
                    'product_id' => $product->id,
                    'modifier_id' => $pm['main_addition_id']
                ]);
            }
        }
    }
    if (get_meals_options($id) != []) {
        if (is_array(get_meals_options($id)) || is_object(get_meals_options($id)) && get_meals_options($id) > 0) {
            foreach (get_meals_options($id) as $po) {
                // create meal sizes
                \App\Models\ProductOption::create([
                    'product_id' => $product->id,
                    'modifier_id' => \App\Models\Option::where('old_id', $po['addition_id'])->first() == null ? null : \App\Models\Option::where('old_id', $po['addition_id'])->first()->modifier_id,
                    'option_id' => \App\Models\Option::where('old_id', $po['addition_id'])->first() == null ? null : \App\Models\Option::where('old_id', $po['addition_id'])->first()->id,
                    'min' => '1',
                    'max' => '5',
                ]);
            }
        }
    }
    if (get_meals_days($id) != []) {
        if (is_array(get_meals_days($id)) || is_object(get_meals_days($id))) {
            if (count(get_meals_days($id)) > 0) {
                foreach (get_meals_days($id) as $md) {
                    // create meal days
                    \App\Models\ProductDay::create([
                        'product_id' => $product->id,
                        'day_id' => $md['day_id']
                    ]);
                }
            }
        }
    }
}

function create_restaurant_sensitivity($restaurant)
{
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'الأسماك ومنتجاتها',
        'name_en' => 'Fish and its products',
        'photo' => 'fish.png',
        'details_ar' => 'مثل لحوم الأسماك وزيت السمك',
        'details_en' => 'Like fish and fish oil',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'البيض ومنتجاته',
        'name_en' => 'eggs and its products',
        'photo' => 'egg.png',
        'details_ar' => 'مثل المايونيز',
        'details_en' => 'Like mayonnaise',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'الحبوب التي تحتوي على مادة الجلوتين',
        'name_en' => 'Cereals that contain gluten',
        'photo' => 'hop.png',
        'details_ar' => 'مثل (القمح والشعير والشوفان والشيلم ســـواء الأنواع الأصلية منها أو المهجنة أو منتجاتها).',
        'details_en' => 'Such as (wheat, barley, oats and rye, whether original or hybrid types or their products).',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'القشـــريات ومنتجاتها',
        'name_en' => 'Crustaceans and their products',
        'photo' => 'aqra.png',
        'details_ar' => 'مثل (ربيان، ســـرطان البحر أو ما يعرف بالسلطعون، جراد البحر أو ما يعرف باللوبستر).',
        'details_en' => 'Such as (prawns, crabs or what is known as crab, lobster or what is known as lobster).',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'الحليب ومنتجاته (التـــي تحتوي على ال?كتوز)',
        'name_en' => 'Milk and milk products (containing lactose)',
        'photo' => 'milk.png',
        'details_ar' => 'مثل الحليب والحليب المنكه',
        'details_en' => 'Like milk and flavored milk',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'الخردل ومنتجاته',
        'name_en' => 'Mustard and its products',
        'photo' => 'kardal.png',
        'details_ar' => 'مثل بـــذور الخردل، زيـــتالخردل، صلصة الخردل',
        'details_en' => 'Like mustard seeds, mustard oil, mustard sauce',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'الرخويات ومنتجاتها',
        'name_en' => 'Mollusks and their products',
        'photo' => 'raky.png',
        'details_ar' => 'مثل (الحبار، الحلـــزون البحري، بلح البحر، واأسكالوب)',
        'details_en' => 'Such as (squid, sea snail, mussels, and scallops)',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'الفول السوداني ومنتجاته',
        'name_en' => 'Peanut and its products',
        'photo' => 'butter.png',
        'details_ar' => 'مثل زبدة الـفول السوداني',
        'details_en' => 'Like peanut butter',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'الكبريتيت',
        'name_en' => 'sulfites',
        'photo' => 'capret.png',
        'details_ar' => 'بتركيز 10 جزء في المليون أو أكثر',
        'details_en' => 'At a concentration of 10 ppm or more',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'الكرفس ومنتجاته',
        'name_en' => 'Celery and its products',
        'photo' => 'rfs.png',
        'details_ar' => 'مثل بذور الكرفس وملح الكرفس',
        'details_en' => 'Like celery seeds and celery salt',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'المكسرات ومنتجاتها',
        'name_en' => 'Nuts and their products',
        'photo' => 'kago.png',
        'details_ar' => 'مثـــل الكاجو والفســـتق وغيرها',
        'details_en' => 'Like cashews, pistachios, etc',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'بذور السمسم ومتجاتها',
        'name_en' => 'Sesame seeds and their products',
        'photo' => 'smsm.png',
        'details_ar' => 'مثل زيت السمسم',
        'details_en' => 'like sesame oil',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'فول الصويا ومنتجاته',
        'name_en' => 'Soybean and its products',
        'photo' => 'soia.png',
        'details_ar' => 'مثل حليب الصويا ',
        'details_en' => 'like soy milk',
    ]);
    RestaurantSensitivity::create([
        'restaurant_id' => $restaurant,
        'name_ar' => 'لوبين (الترمس ومنتجاتها)',
        'name_en' => 'Lupine (lupine and its products)',
        'photo' => 'terms.png',
        'details_ar' => 'مثل زيت الترمس',
        'details_en' => 'like lupine oil',
    ]);
}
function check_restaurant_branches($id)
{
    $restaurant = \App\Models\Restaurant::find($id);
    $chech_branches = \App\Models\Subscription::whereRestaurantId($restaurant->id)
        ->where('type', 'branch')
        ->where('package_id', '!=', 1)
        ->count();
    return $chech_branches;
}
function check_restaurant_amount($id, $amount)
{
    $branch = \App\Models\Branch::find($id);
    if ($branch->country->name_ar == 'مصر') {
        return $amount * 0.20;
    } elseif ($branch->country->name_ar == 'السعودية') {
        return $amount * 1;
    } elseif ($branch->country->name_ar == 'البحرين') {
        return $amount * 10;
    } elseif ($branch->country->name_ar == 'الكويت') {
        return $amount * 13;
    } elseif ($branch->country->name_ar == 'عمان') {
        return $amount * 10;
    } elseif ($branch->country->name_ar == 'الامارات') {
        return $amount * 1.3;
    } elseif ($branch->country->name_ar == 'اليمن') {
        return $amount * 0.10;
    } else {
        return $amount * 1;
    }
}

function checkRestaurantPackageId($restaurant = null, $type = 'restaurant', $packageId = null)
{
    $packageId = is_array($packageId) ? $packageId : [$packageId];
    if ($check = $restaurant->subscription()->where('type', $type)->orderBy('created_at', 'desc')->whereIn('package_id', $packageId)->first()) return true;

    return false;
}

function restaurantPackageId($restaurant)
{

    if ($check = $restaurant->subscription()->orderBy('created_at', 'desc')->first() and isset($check->subscription->package_id)) return $check->subscription->package_id;

    return false;
}
function employeeGetPackageId(RestaurantEmployee $employee = null)
{
    $employee = $employee == null ? auth('employee')->user() : $employee;
    if (!isset($employee->id)) return false;
    $branch = $employee->branch()->with('subscription')->first();
    if (isset($branch->subscription->id)) return $branch->subscription->package_id;
    return false;
}


function isUrlActive($url, $checkFull = false, $data = [])
{
    $check = true;
    $path = Request::path();
    if (count($data) > 0) {
        foreach ($data as $key => $value) {
            if (!Request::has($key) or Request::get($key) != $value) $check = false;
        }
    }
    if (!$checkFull and (!strpos($path, $url, 0) and $path != $url)) return false;
    elseif ($checkFull and $path != $url) return false;
    return $check;
}

function defaultResturantData(Restaurant $restaurant)
{
    // update data

    $restaurant->update([
        'logo' => 'logo.png',

        'status' => 'tentative',
        'menu_arrange'  => 'false',
        'product_arrange' => 'false',
        'logo' => 'logo.png',
        'menu' => 'vertical',
        'description_ar' => 'نبذة عن المطعم . محتوي  يتم تغييره من خلال لوحة تحكم المطعم',
        'description_en' => 'A Brief About Restaurant Can Be Changed From Restaurant Control Panel',
        'information_ar' => ' يحتاج البالغون الى 2000 سعر حراري في المتوسط يومياً
يحتاج غير البالغون الى 1400 سعر حراري في المتوسط يومياً',
        'information_en' => 'Adults need an average of 2,000 calories per day',
    ]);
    // slider
    RestaurantSlider::create([
        'restaurant_id' => $restaurant->id,
        'photo' => 'slider2.png'
    ]);
    RestaurantSlider::create([
        'restaurant_id' => $restaurant->id,
        'photo' => 'slider1.png'
    ]);
    // sensitivities
    defaultPostersAndSens($restaurant);

    return true;
}
function defaultPostersAndSens($restaurant)
{
    $sent = Sensitivity::all();
    foreach ($sent as $temp) :
        $data = $temp->only([
            'name_en', 'name_ar', 'details_en', 'details_ar', 'photo'
        ]);
        $image = copyImage($temp->image_path, 'sensitivities', 'uploads/sensitivities');
        $data['photo'] = $image;
        $data['restaurant_id'] = $restaurant->id;
        RestaurantSensitivity::create($data);
    endforeach;

    $posters = Poster::all();
    foreach ($posters as $poster) :
        $image = copyImage($poster->image_path, 'poster', 'uploads/posters');
        $restaurant->posters()->create([
            'name_en' => $poster->name_en,
            'name_ar' => $poster->name_ar,
            'poster' => $image,
        ]);
    endforeach;
}

/**
 * @check order discount
 * @checkProductDiscount
 */
function apply_discount($order, $discount)
{
    if ($discount->minimum_order_price != null) {
        if ($discount->minimum_order_price >= $order->order_price) {
            if ($discount->minimum_product_price != null) {
                if ($discount->minimum_product_price >= $order->product->price) {
                    if ($discount->is_percentage == 'true') {
                        $discount_value = ($order->order_price * $discount->amount) / 100;
                        if ($discount_value < $order->order_price) {
                            $order->update([
                                'discount_id' => $discount->id,
                                'discount_value' => $discount_value,
                            ]);
                        }
                    } else {
                        if ($discount->amount < $order->order_price) {
                            $order->update([
                                'discount_id' => $discount->id,
                                'discount_value' => $discount->amount,
                            ]);
                        }
                    }
                }
            } else {
                if ($discount->is_percentage == 'true') {
                    $discount_value = ($order->order_price * $discount->amount) / 100;
                    if ($discount_value < $order->order_price) {
                        $order->update([
                            'discount_id' => $discount->id,
                            'discount_value' => $discount_value,
                        ]);
                    }
                } else {
                    if ($discount->amount < $order->order_price) {
                        $order->update([
                            'discount_id' => $discount->id,
                            'discount_value' => $discount->amount,
                        ]);
                    }
                }
            }
        }
    } else {
        if ($discount->minimum_product_price != null) {
            if ($discount->minimum_product_price >= $order->product->price) {
                if ($discount->is_percentage == 'true') {
                    $discount_value = ($order->order_price * $discount->amount) / 100;
                    if ($discount_value < $order->order_price) {
                        $order->update([
                            'discount_id' => $discount->id,
                            'discount_value' => $discount_value,
                        ]);
                    }
                } else {
                    if ($discount->amount < $order->order_price) {
                        $order->update([
                            'discount_id' => $discount->id,
                            'discount_value' => $discount->amount,
                        ]);
                    }
                }
            }
        } else {
            if ($discount->is_percentage == 'true') {
                $discount_value = ($order->order_price * $discount->amount) / 100;
                if ($discount_value < $order->order_price) {
                    $order->update([
                        'discount_id' => $discount->id,
                        'discount_value' => $discount_value,
                    ]);
                }
            } else {
                if ($discount->amount < $order->order_price) {
                    $order->update([
                        'discount_id' => $discount->id,
                        'discount_value' => $discount->amount,
                    ]);
                }
            }
        }
    }
}

function apply_table_discount($order, $discount)
{
    if ($discount->minimum_order_price != null) {
        if ($discount->minimum_order_price >= $order->price) {
            if ($discount->minimum_product_price != null) {
                if ($discount->minimum_product_price >= $order->product->price) {
                    if ($discount->is_percentage == 'true') {
                        $discount_value = ($order->price * $discount->amount) / 100;
                        if ($discount_value < $order->price) {
                            $discount_value = $order->table_order->discount_value + $discount_value;
                            $order->table_order->update([
                                'discount_id' => $discount->id,
                                'discount_value' => $discount_value,
                            ]);
                        }
                    } else {
                        if ($discount->amount < $order->price) {
                            $discount_value = $order->table_order->discount_value + $discount->amount;
                            $order->table_order->update([
                                'discount_id' => $discount->id,
                                'discount_value' => $discount_value,
                            ]);
                        }
                    }
                }
            } else {
                if ($discount->is_percentage == 'true') {
                    $discount_value = ($order->price * $discount->amount) / 100;
                    if ($discount_value < $order->price) {
                        $discount_value = $order->table_order->discount_value + $discount_value;
                        $order->table_order->update([
                            'discount_id' => $discount->id,
                            'discount_value' => $discount_value,
                        ]);
                    }
                } else {
                    if ($discount->amount < $order->price) {
                        $discount_value = $order->table_order->discount_value + $discount->amount;
                        $order->table_order->update([
                            'discount_id' => $discount->id,
                            'discount_value' => $discount_value,
                        ]);
                    }
                }
            }
        }
    } else {
        if ($discount->minimum_product_price != null) {
            if ($discount->minimum_product_price >= $order->product->price) {
                if ($discount->is_percentage == 'true') {
                    $discount_value = ($order->price * $discount->amount) / 100;
                    if ($discount_value < $order->price) {
                        $discount_value = $order->table_order->discount_value + $discount_value;
                        $order->table_order->update([
                            'discount_id' => $discount->id,
                            'discount_value' => $discount_value,
                        ]);
                    }
                } else {
                    if ($discount->amount < $order->price) {
                        $discount_value = $order->table_order->discount_value + $discount->amount;
                        $order->table_order->update([
                            'discount_id' => $discount->id,
                            'discount_value' => $discount_value,
                        ]);
                    }
                }
            }
        } else {
            if ($discount->is_percentage == 'true') {
                $discount_value = ($order->price * $discount->amount) / 100;
                if ($discount_value < $order->price) {
                    $discount_value = $order->table_order->discount_value + $discount_value;
                    $order->table_order->update([
                        'discount_id' => $discount->id,
                        'discount_value' => $discount_value,
                    ]);
                }
            } else {
                if ($discount->amount < $order->price) {
                    $discount_value = $order->table_order->discount_value + $discount->amount;
                    $order->table_order->update([
                        'discount_id' => $discount->id,
                        'discount_value' => $discount_value,
                    ]);
                }
            }
        }
    }
}



function updateCurrency()
{
    $currecyCode = Country::whereNotNull('currency_code')->get()->pluck('currency_code')->toArray();

    $dataCurrency = implode('%2C', $currecyCode);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.apilayer.com/exchangerates_data/latest?symbols=" . $dataCurrency . "&base=SAR",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: text/plain",
            "apikey: JwbjW97j0ViX6LYdjzFbvhNsYEudSOMw"
        ),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $data =  json_decode($response, true);
    if (isset($data['rates']) and is_array($data['rates']) and count($data['rates'])) :
        foreach ($data['rates'] as $key => $value) :
            if ($country = Country::where('currency_code', $key)->first()) :
                $country->update([
                    'riyal_value' => $value
                ]);
            endif;
        endforeach;
    endif;
}


function checkWordsCount($string, $count, $isTag = false)
{
    $words  = $isTag == true ? explode(' ', strip_tags($string)) : explode(' ', $string);
    if (count($words) > $count) return true;
    return false;
}
function getShortDescription($string, $start, $last =  0, $isTag = false)
{
    $words  = $isTag == true ? explode(' ', strip_tags($string)) : explode(' ', $string);
    $results = '';
    foreach ($words as $index => $temp) :
        if ($index >= $start and ($last == 0 or $index <= $last)) $results .= $temp . ' ';
    endforeach;

    return $results;
}

function tap_payment($token, $amount, $user_name, $email, $country_code, $phone, $callBackUrl, $order_id)
{
    $basURL = "https://api.tap.company/v2/charges";
    $headers = array(
        'Content-type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token,
    );

    $data = array(
        "amount" => $amount,
        "currency" => "SAR",
        "customer" => array(
            "first_name" => $user_name,
            "email" => $email,
            "phone" => array(
                "country_code" => $country_code,
                "number" => $phone
            ),
        ),
        "source" => array(
            "id" => "src_card"
        ),
        "redirect" => array(
            "url" => route($callBackUrl, [$order_id, $token]),
        )
    );
    $order = json_encode($data);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $basURL,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $order,
        CURLOPT_HTTPHEADER => $headers,
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        $response = json_decode($response);
        return $response->transaction->url;
    }
}

function express_payment($merchant_key, $password, $amount, $success_url, $orderId, $user_name = null, $email = null)
{
    $basURL = "https://pay.expresspay.sa/api/v1/session";
    $headers = array(
        'Content-type: application/json',
        'Accept: application/json',
    );
    $order_id = 'order-' . mt_rand(1000, 9999);
    $hash = array(
        "number" => $order_id,
        "amount" => $amount,
        "currency" => "SAR",
        "description" => "pay order value",
        'password' => $password
    );
    $hash = implode($hash);
    $hash = strtoupper($hash);
    $hash = md5($hash);
    $hash = sha1($hash);
    //    dd($hash);
    $data = array(
        "merchant_key" => $merchant_key,
        "operation" => "purchase",
        "methods" => array(
            "card"
        ),
        "order" => array(
            "number" => $order_id,
            "amount" => $amount,
            "currency" => "SAR",
            "description" => "pay order value",
        ),
        "cancel_url" => route('express_error'),
        "success_url" => route($success_url, $orderId),
        "customer" => array(
            "name" => $user_name == null ? "John Doe" : $user_name,
            "email" => $email == null ? "test@email.com" : $email,
        ),
        "billing_address" => array(
            "country" => "SA",
            "state" => "CA",
            "city" => "Los Angeles",
            "address" => "Moor Building 35274",
            "zip" => "123456",
            "phone" => "347771112233"
        ),
        "recurring_init" => "true",
        "hash" => $hash
    );

    $order = json_encode($data);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $basURL,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $order,
        CURLOPT_HTTPHEADER => $headers,
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        $response = json_decode($response);
        return $response->redirect_url;
    }
}


function checkUrlAdsType()
{

    if (auth('restaurant')->check()) :
        if (isUrlActive('restaurant/home', true)) :
            return 'home';
        elseif (isUrlActive('restaurant/profile', true)) :
            return 'profile';
        elseif (isUrlActive('services_store', false) or isUrlActive('integration', false)) :
            return 'service';
        elseif (isUrlActive('reservation', false)) :
            return 'reservation';

        elseif (isUrlActive('product', false)) :
            return 'product';
        elseif (isUrlActive('menu_categor', false) or isUrlActive('sub_categor', false)) :
            return 'menu_category';
        else :
            return 'all';
        endif;
    endif;

    return '';
}


function deleteImageFile($path)
{
    if (!empty($path) and Storage::disk('public_storage')->exists($path)) :
        Storage::disk('public_storage')->delete($path);
        return true;
    endif;

    return false;
}

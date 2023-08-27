<?php

namespace App\Http\Controllers\RestaurantController;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantPoster;
use App\Models\SmsHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TaqnyatSms;

class SmsController extends Controller
{

    public function settings(Request $request){
        $restaurant = auth('restaurant')->user();
        if($request->method() == 'POST'):
            $data = $request->validate([
                'sms_method' => 'required|in:taqnyat' , 
                'sms_sender' => 'required|min:1|max:190' , 
                'sms_token' => 'required|min:1|max:190' , 
            ]);
            $restaurant->update($data);

            flash(trans('messages.updated'))->success();
            return redirect()->route('restaurant.sms.settings');
        endif;
        $smsBalance = null ;
        if(!empty($restaurant->sms_method)):
            $sms = new TaqnyatSms($restaurant->sms_token);
            $smsBalance = json_decode($sms->balance() , true);
        endif;
        // return $smsBalance['points'];
        return view('restaurant.sms_methods.settings' , compact('restaurant' , 'smsBalance'));
    }
    public function sendSms(Request $request){
        $restaurant = auth('restaurant')->user();
        $smsBalance = null ;
        if(!empty($restaurant->sms_method) and $restaurant->sms_method == 'taqnyat'):
            $sms = new TaqnyatSms($restaurant->sms_token);
            $smsBalance = json_decode($sms->balance() , true);
        endif;
        
        if($request->method() == 'POST'):
            if(!isset($smsBalance['statusCode']) or $smsBalance['statusCode'] != 200):
                flash('خدمة الارسال غير متاحة')->error();
                return redirect()->route('restaurant.sms.sendSms');
            endif;

            
            $data = $request->validate([
                'phones' => 'required|array|min:1' , 
                'phones.*' => ['required' , 'regex:(^((9665)|(05)|(201))[0-9]{8})'] , 
                'message' => 'required|min:1' , 
            ] , [
                'phones.*.*' => 'يجب ان يبداء رقم الجوال : 201 او 05 او 9665'
            ]);
            // return $request->all();
            
            if($restaurant->sms_method == 'taqnyat'):
                $res= json_decode($sms->sendMsg($request->message , $request->phones , $restaurant->sms_sender) , true);
                
                if($res['statusCode'] ==  201):
                    $accepted = explode(',' , str_replace(['[' , ']'] , '' , $res['accepted']));
                    $rejected = explode(',' , str_replace(['[' , ']'] , '' , $res['rejected']));
                    
                    $item = SmsHistory::create([
                        'restaurant_id' => $restaurant->id , 
                        'message_id' => $res['messageId'] , 
                        'message_count' => ($res['totalCount'] * $res['msgLength']) , 
                        'message' => $request->message
                    ]);
                    foreach($accepted as $t):
                        if(strlen($t) > 8):
                            $item->phones()->create([
                                'phone' => $t , 
                                'is_sent' => 1 , 
                            ]);
                        endif;
                    endforeach;
                    foreach($rejected as $t):
                        if(strlen($t) > 8):
                            $item->phones()->create([
                                'phone' => $t , 
                                'is_sent' => 0 , 
                            ]);
                        endif;
                    endforeach;

                    flash(trans('dashboard.sms_send_success' , ['count' => ($res['totalCount'] * $res['msgLength'])]))->success();
                elseif(isset($res['message'])):
                    flash($res['message'])->error();
                endif;
            endif;
           

            return redirect()->route('restaurant.sms.sendSms');
            
        endif;
        
       
        // return $smsBalance;
        return view('restaurant.sms_methods.send_sms' , compact('restaurant' , 'smsBalance'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee'):
          
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        $items = SmsHistory::whereRestaurantId($restaurant->id)
            ->orderBy('id' , 'desc')->with('phones')
            ->paginate(500);
        return view('restaurant.sms_methods.index' , compact('items'));
    }
    public function showDetails(Request $request){
        $request->validate([
            'id' => 'required|integer'
        ]);
        $phones = [];
        if($item = SmsHistory::find($request->id)):
            $phones = $item->phones;
        endif;

        return response([
            'data' => view('restaurant.sms_methods.phones' , compact('phones'))->render()
        ]);

    }
    public function delete($id)
    {
        $poster = SmsHistory::findOrFail($id);
 
        $poster->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('restaurant.sms.index');
    }
}

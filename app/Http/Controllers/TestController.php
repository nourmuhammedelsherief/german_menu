<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailPartyNewRequest;
use App\Jobs\SendMailReservationNewRequest;
use App\Jobs\TestJob;
use App\Mail\PartyEmail;
use App\Models\Branch;
use App\Models\History;
use App\Models\MenuCategory;
use App\Models\Modifier;
use App\Models\PartyOrder;
use App\Models\Poster;
use App\Models\Product;
use App\Models\Reservation\ReservationOrder;
use App\Models\Reservation\ReservationTable;
use App\Models\Reservation\ReservationTablePeriod;
use App\Models\Restaurant;
use App\Models\RestaurantCode;
use App\Models\RestaurantPoster;
use App\Models\RestaurantSensitivity;
use App\Models\Sensitivity;
use App\Models\Service;
use App\Models\SilverOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use TaqnyatSms;

class TestController extends Controller
{
    //

    public function index(Request $request)
    {

        $restaurant = auth('restaurant')->user();

       return route('waiter.login');
        $order = ReservationOrder::orderBy('id' , 'desc')->first();
        $order = PartyOrder::orderBy('id' , 'desc')->first();
        
        
        dispatch(new SendMailPartyNewRequest('saif2nemr@gmail.com' , $order));
        
        return 'true';
        $t = Auth::guard('web')->login(User::find(24));
        return 'logined';
        return SilverOrder::find(88032);
        return isFoodicsSandbox();
        // $u = auth('web')->user();

        // export data with files
        // first make new folder and check if exists before
        $count = 0;
        $folderName = 'backups/backup_' . $branch->id;
        $temp = $folderName;
        while (Storage::disk('storage')->exists($temp)) {
            $count++;
            $temp = $folderName . '_'  . $count;
        }
        $folderName = $temp;

        // store categories and sub categories
        $categories = MenuCategory::where('branch_id', $branch->id)->with('sub_categories')->get();

        Storage::disk('storage')->put($folderName . '/database/menu_categories.json', json_encode($categories)); // save data
        $filesPath = $folderName . '/files';
        Storage::disk('storage')->makeDirectory($filesPath);
        Storage::disk('storage')->makeDirectory($filesPath . '/menu_categories');
        foreach ($categories as $category) :
            if (Storage::disk('public_storage')->exists($category->image_path)) {
                try {
                    File::copy(public_path($category->image_path), storage_path($filesPath . '/menu_categories/' . $category->photo));
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        endforeach;

        // store product options
        $items = Modifier::where('restaurant_id', $branch->restaurant_id)->with('options')->get();

        Storage::disk('storage')->put($folderName . '/database/modifier_options.json', json_encode($items)); // save data

        // store posters 
        $posters = RestaurantPoster::where('restaurant_id', $branch->restaurant_id)->get();

        Storage::disk('storage')->put($folderName . '/database/posters.json', json_encode($posters)); // save data
        $filesPath = $folderName . '/files';
        Storage::disk('storage')->makeDirectory($filesPath);
        Storage::disk('storage')->makeDirectory($filesPath . '/posters');
        foreach ($posters as $item) :
            if (Storage::disk('public_storage')->exists($item->image_path)) {
                File::copy(public_path($item->image_path), storage_path($filesPath . '/posters/' . $item->poster));
            }
        endforeach;
        // store products
        $products = Product::where('branch_id', $branch->id)->with('sizes', 'options')->get();

        Storage::disk('storage')->put($folderName . '/database/products.json', json_encode($products)); // save data
        $filesPath = $folderName . '/files';
        Storage::disk('storage')->makeDirectory($filesPath);
        Storage::disk('storage')->makeDirectory($filesPath . '/products');
        foreach ($products as $item) :
            if (Storage::disk('public_storage')->exists($item->image_path)) {
                File::copy(public_path($item->image_path), storage_path($filesPath . '/products/' . $item->photo));
            }
        endforeach;
        return 'done';
    }
    private function foodicsTest()
    {
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjZlZWY1ODA4Yzk3YjhlYzVlMTZhYTZmMWNiYzdlZjk4ZDc1M2I5ZTZjOTZiNDk2MDc3OTgxOWQ5ZWFhOWU1MWJhM2FiMTYxM2Y3NDRlM2QzIn0.eyJhdWQiOiI5NGE3ZWVhYy01ODgxLTRiMTktYWU2YS0wMjRkZWJkOWFjMDUiLCJqdGkiOiI2ZWVmNTgwOGM5N2I4ZWM1ZTE2YWE2ZjFjYmM3ZWY5OGQ3NTNiOWU2Yzk2YjQ5NjA3Nzk4MTlkOWVhYTllNTFiYTNhYjE2MTNmNzQ0ZTNkMyIsImlhdCI6MTY4NDc4NTAxMSwibmJmIjoxNjg0Nzg1MDExLCJleHAiOjE4NDI2Mzc4MTEsInN1YiI6Ijk4N2IzNjBjLTRhMTItNDA5Zi04NTc2LWE4ZjRkODQ0NjBhMiIsInNjb3BlcyI6W10sImJ1c2luZXNzIjoiOTg3YjM2MGMtNTIyYS00NTA3LThiMGQtOTE5ODNhZWU3ZTFmIiwicmVmZXJlbmNlIjoiNTk3NjEyIn0.YXQXUQXqy1TnDhvS4WOb6yfv5WCh7iqIQ4LdmbA2ZysMxAKOhNM1WlAyX8-DRm4DVLACzDeMNbom_s7OEC7NLiMlAFpLTn0BJv83sRoPsJdRFaHS3l2QNiuDNpajfb6d1IhACVotdln8Et5etZ7fKRTA5YwxuRILc6HMg540CnSly6oJR0fZjZ7ck74bmKODhpUvrUHiWhYrtucKiU7SRT3qsJb24Ec0-TE9VrASZPwLTcALhhddhwCVQU5bFnKJBMdcxa8Mhu2ciHU3Xi8AAHV3U5IhpS8SgiLwsDRlNK27j082HsqrIZCIvY09W8BNCAPDed-0yN4cdVMxEDEacuSId6J1FXSTXl7Eg4V6mpsB5OfBipXOK5pxVlLnQAz5enwIUb2Dsvfh7d5Nqla7DHB3DY6D2auz2rp8ScHMFIPtj-0lja2MeGFlLbm_rD9zTvcJv5tT_pW6dgvL1Aruv3pNSMu6fpHHiJMuMzA_fKi0Bj2hs04PHhIkT0-HPRG28qAps5Rtbqax1kVYNTTho7UboQ3NA2VXpRDOHtOL5PvHVUyeh2RzHJeMR-EsGOI9sABcmdlRLhLFqSzoQEKpuo9mk-hpgJKZirqIavVIa3rsELhokWUqMejFOzFTI8UUxXskEZl7Njj7GE5DkG3k1EE2xa1eYf7hoBdRTDi1hRY";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            // 'Accept' => 'application/json' , 
        ])->get('https://api.foodics.com/v5/orders/98f1c668-0231-46d4-ab28-6c496c675c72');
        $response = getFoodicsOrder('98f1c668-0231-46d4-ab28-6c496c675c72', $token);
        return $response->json();
    }




    private function storeDefaultSenistive()
    {
        $restaurant_sensitivities = array(
            array('id' => '9504', 'restaurant_id' => '976', 'name_ar' => 'الأسماك ومنتجاتها', 'name_en' => 'Fish and its products', 'photo' => 'fish.png', 'details_ar' => 'مثل لحوم الأسماك وزيت السمك', 'details_en' => 'Like fish and fish oil', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9505', 'restaurant_id' => '976', 'name_ar' => 'البيض ومنتجاته', 'name_en' => 'eggs and its products', 'photo' => 'egg.png', 'details_ar' => 'مثل المايونيز', 'details_en' => 'Like mayonnaise', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9506', 'restaurant_id' => '976', 'name_ar' => 'الحبوب التي تحتوي على مادة الجلوتين', 'name_en' => 'Cereals that contain gluten', 'photo' => 'hop.png', 'details_ar' => 'مثل (القمح والشعير والشوفان والشيلم ســـواء الأنواع الأصلية منها أو المهجنة أو منتجاتها).', 'details_en' => 'Such as (wheat, barley, oats and rye, whether original or hybrid types or their products).', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9507', 'restaurant_id' => '976', 'name_ar' => 'القشـــريات ومنتجاتها', 'name_en' => 'Crustaceans and their products', 'photo' => 'aqra.png', 'details_ar' => 'مثل (ربيان، ســـرطان البحر أو ما يعرف بالسلطعون، جراد البحر أو ما يعرف باللوبستر).', 'details_en' => 'Such as (prawns, crabs or what is known as crab, lobster or what is known as lobster).', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9508', 'restaurant_id' => '976', 'name_ar' => 'الحليب ومنتجاته (التـــي تحتوي على ال?كتوز)', 'name_en' => 'Milk and milk products (containing lactose)', 'photo' => 'milk.png', 'details_ar' => 'مثل الحليب والحليب المنكه', 'details_en' => 'Like milk and flavored milk', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9509', 'restaurant_id' => '976', 'name_ar' => 'الخردل ومنتجاته', 'name_en' => 'Mustard and its products', 'photo' => 'kardal.png', 'details_ar' => 'مثل بـــذور الخردل، زيـــتالخردل، صلصة الخردل', 'details_en' => 'Like mustard seeds, mustard oil, mustard sauce', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9510', 'restaurant_id' => '976', 'name_ar' => 'الرخويات ومنتجاتها', 'name_en' => 'Mollusks and their products', 'photo' => 'raky.png', 'details_ar' => 'مثل (الحبار، الحلـــزون البحري، بلح البحر، واأسكالوب)', 'details_en' => 'Such as (squid, sea snail, mussels, and scallops)', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9511', 'restaurant_id' => '976', 'name_ar' => 'الفول السوداني ومنتجاته', 'name_en' => 'Peanut and its products', 'photo' => 'butter.png', 'details_ar' => 'مثل زبدة الـفول السوداني', 'details_en' => 'Like peanut butter', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9512', 'restaurant_id' => '976', 'name_ar' => 'الكبريتيت', 'name_en' => 'sulfites', 'photo' => 'capret.png', 'details_ar' => 'بتركيز 10 جزء في المليون أو أكثر', 'details_en' => 'At a concentration of 10 ppm or more', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9513', 'restaurant_id' => '976', 'name_ar' => 'الكرفس ومنتجاته', 'name_en' => 'Celery and its products', 'photo' => 'rfs.png', 'details_ar' => 'مثل بذور الكرفس وملح الكرفس', 'details_en' => 'Like celery seeds and celery salt', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9514', 'restaurant_id' => '976', 'name_ar' => 'المكسرات ومنتجاتها', 'name_en' => 'Nuts and their products', 'photo' => 'kago.png', 'details_ar' => 'مثـــل الكاجو والفســـتق وغيرها', 'details_en' => 'Like cashews, pistachios, etc', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9515', 'restaurant_id' => '976', 'name_ar' => 'بذور السمسم ومتجاتها', 'name_en' => 'Sesame seeds and their products', 'photo' => 'smsm.png', 'details_ar' => 'مثل زيت السمسم', 'details_en' => 'like sesame oil', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9516', 'restaurant_id' => '976', 'name_ar' => 'فول الصويا ومنتجاته', 'name_en' => 'Soybean and its products', 'photo' => 'soia.png', 'details_ar' => 'مثل حليب الصويا ', 'details_en' => 'like soy milk', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13'),
            array('id' => '9517', 'restaurant_id' => '976', 'name_ar' => 'لوبين (الترمس ومنتجاتها)', 'name_en' => 'Lupine (lupine and its products)', 'photo' => 'terms.png', 'details_ar' => 'مثل زيت الترمس', 'details_en' => 'like lupine oil', 'created_at' => '2022-11-09 20:22:13', 'updated_at' => '2022-11-09 20:22:13')
        );
        foreach ($restaurant_sensitivities as $temp) :
            Sensitivity::create($temp);
        endforeach;
        return 'done';
    }
}

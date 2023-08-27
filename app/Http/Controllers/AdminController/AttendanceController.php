<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ArchiveCategory;
use App\Models\Attendance;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    private $admin;
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request , $type = null)
    {
        $admin = auth('admin')->user();
        if ($admin->role != 'admin' and $type != 'mine') :
            abort(401);
        endif;
        $request->validate([
            'admin_id' => 'nullable|integer' , 
            'start_date' => 'nullable|date' , 
            'end_date' => 'nullable|date' , 
            'page' => 'nullable|integer'
        ]);
        $page = $request->page > 1 ? $request->page : 1;
        $items = Attendance::orderBy('created_at', 'desc');
        $check = false;
        $userTimer = null;
        if($type == 'online'){
            $items  = $items->whereNull('end_date');
        } elseif($type == 'mine'){
            $items  = $items->whereNotNull('end_date')->where('admin_id' , $admin->id);
        }else{
            $items  = $items->whereNotNull('end_date');
        }
        if(!empty($request->admin_id)):
            $items = $items->where('admin_id' , $request->admin_id);
            $check = true;
        endif;
        if(!empty($request->start_date)):
            $items = $items->where(DB::raw('date(start_date)') ,'>=' , $request->start_date);
        endif;
        if(!empty($request->end_date)):
            $items = $items->where(DB::raw('date(end_date)') ,'<=' , $request->end_date);
        endif;
        if(!empty($request->year)):
            $items  = $items->where('start_date' , 'like' , '' .$request->year . '%');
            $check = true;
        endif;
        if(!empty($request->month)):
            $month = $request->month < 10 ? '-0' . $request->month . '-' : '-' . $request->month . '-';
            $items  = $items->where('start_date' , 'like' , '%' .$month . '%');
            $check = true;
        endif;
        if(!empty($request->day)):
            $day = $request->day < 10 ? '-0' . $request->day . '' : '-' . $request->day . '';
            $items  = $items->where('start_date' , 'like' , '%' .$day . '%');
            $check = true;
        endif;
        $seconds = 0;
        $items = $items->get()->map(function ($item) {
            $start = Carbon::parse($item->start_date);
            $end = Carbon::parse($item->end_date);
            $item->timer = $start->diffInSeconds($end);
            
            $item->day = $start->format('Y-m-d');
            $item->day_name = $start->getTranslatedDayName();
            $item->timer_format = sprintf('%d:%d', $item->timer / 3600, floor($item->timer / 60) % 60);
            return $item;
        });
        if($check == true):
            
            foreach($items as $t):
                $seconds += $t->timer;
            endforeach;
            $userTimer =  sprintf('%dh  %dm', $seconds / 3600, floor($seconds / 60) % 60);
        endif;
        $employeeOnline = Attendance::whereNull('end_date')->count();
        $firstYear = Attendance::orderBy('created_at')->first();
        $firstYear = isset($firstYear->id) ? $firstYear->created_at->format('Y') : date('Y');
        // return $items;
        $admins = Admin::orderBy('role')->get();
        return  view('admin.attendances.index', compact('items' , 'admins' , 'userTimer' , 'employeeOnline' , 'type' , 'firstYear'));
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $admin = auth('admin')->user();
        $work  = $admin->attendances()->whereNull('end_date')->orderBy('id', 'desc')->first();
        return  view('admin.attendances.create', compact('work'));
    }

    public function startWork()
    {
        $admin = auth('admin')->user();
        $item  = $admin->attendances()->whereNull('end_date')->orderBy('id', 'desc')->first();

        if (isset($item->id)) :
            $item->delete();
        endif;
        $item = $admin->attendances()->create([
            'start_date'  => date('Y-m-d H:i:s'),
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('admin.attendance.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'details' => 'required|string',
            'id' => 'required|integer',
        ]);
        $admin = auth('admin')->user();
        if (!$item = $admin->attendances()->where('id', $request->id)->whereNull('end_date')->orderBy('id', 'desc')->first()) :
            flash('اضغط علي بداء العمل اولا')->error();
        else :
            $item->update([
                'end_date' => date('Y-m-d H:i:s'),
                'details' => $request->details,
            ]);
            flash(trans('messages.created'))->success();
        endif;

        return redirect()->route('admin.attendance.create');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = auth('admin')->user();
        if ($admin->role != 'admin') abort(401);
        $category = Attendance::findOrFail($id);

        $category->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('admin.attendance.index');
    }
}

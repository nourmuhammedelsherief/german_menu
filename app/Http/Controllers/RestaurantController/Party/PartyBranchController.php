<?php

namespace App\Http\Controllers\RestaurantController\Party;

use App\Http\Controllers\Controller;
use App\Models\PartyBranch;
use App\Models\Reservation\ReservationOrder;
use App\Models\Reservation\ReservationTable;
use App\Models\Restaurant;
use App\Models\ServiceSubscription;
use Illuminate\Http\Request;

class PartyBranchController extends Controller
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
            ->where('service_id', 1)
            ->whereIn('status', ['active', 'tentative'])
            ->first(); // for test
        if ($checkReservation == null and false) {
            abort(404);
        }
        $branches = PartyBranch::where('restaurant_id', $restaurant->id)->withCount('parties')->get();
        return view('restaurant.party.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('restaurant.party.branches.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [

            'name_ar' => 'required|string|max:191',
            'name_en' => 'required|string|max:191',

        ]);
        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            // if (check_restaurant_permission($restaurant->id , 3) == false):
            //     abort(404);
            // endif;
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;
        // create new bank
        PartyBranch::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,

            'restaurant_id' => $restaurant->id,
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('restaurant.party-branch.index');
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
        $branch = PartyBranch::findOrFail($id);
        return view('restaurant.party.branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $branch)
    {

        $this->validate($request, [

            'name_ar' => 'required|string|max:191',
            'name_en' => 'required|string|max:191',

        ]);
        $restaurant = auth('restaurant')->user();
        if ($restaurant->type == 'employee') :
            // if (check_restaurant_permission($restaurant->id , 3) == false):
            //     abort(404);
            // endif;
            $restaurant = Restaurant::find($restaurant->restaurant_id);
        endif;

        $branch = PartyBranch::where('restaurant_id', $restaurant->id)->findOrFail($branch);

        $branch->update([

            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->route('restaurant.party-branch.index');
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
        $branch = PartyBranch::where('restaurant_id', $restaurant->id)->findOrFail($id);
        $check = $branch->partys()->count();
        if ($check) {
            flash(trans('messages.party_exists_count'))->error();
            return redirect()->route('restaurant.party-branch.index');
        }
        $branch->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('restaurant.party-branch.index');
    }
}

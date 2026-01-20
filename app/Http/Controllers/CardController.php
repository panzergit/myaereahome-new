<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Session;

use App\Models\v7\Card;
use App\Models\v7\Unit;
use App\Models\v7\Building;
use App\Models\v7\Property;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use App\Models\v7\UserCard;
use DB;
use Auth;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        session()->forget('current_page');

        $q = $option = $card = $status  = $unit ='';

        // $cards = $user->role_id == 1 ? UserCard::get() : 
        //     UserCard::where('property_id', $user->account_id)->paginate(env('PAGINATION_ROWS'));
        
        $cards = Card::where('status',1)
            ->when($user->role_id != 1, fn($q) => $q->where('account_id', $user->account_id))
            ->paginate(env('PAGINATION_ROWS'));

        return view('admin.card.index', compact('cards','q','option','card','status','unit'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $account_id = Auth::user()->account_id;

        $properties = Property::pluck('company_name', 'id')->all();
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        $units = Unit::select('unit', 'id')->where('account_id',$account_id)->get();
        $unites = array();
        if(isset($units)){
            foreach($units as $unit){
                $unites[$unit->id] = Crypt::decryptString($unit->unit);
            }
        }
        return view('admin.card.create', compact('properties','buildings','unites'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // print_r($request->card);
        $input = $request->all();

       if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'card' =>[
                'required', 
                Rule::unique('cards')
            ],
            
        ]);
        if ($validator->fails()) { 

             return redirect('opslogin/card/create')->with('status', 'Card already exist!');         
        }

        $card = Card::create($input);
        /*$auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();  
        $api_obj = new \App\Models\v7\Card();
        $card_info= $api_obj->card_add_api($thinmoo_access_token,$card);
        print_r($card_info);
        exit;*/
        return redirect('opslogin/card')->with('status', 'Card has been added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Respons
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $user = $request->user();
        $account_id = $user->account_id;

        $cardObj = Card::find($id);
        $properties = Property::pluck('company_name', 'id')->all();
        $buildings = Building::where('account_id', $account_id)->pluck('building', 'id')->all();
        $units = Unit::select('unit', 'id')->where('account_id', $account_id)->get();
        $unites = [];

        foreach($units as $unit) $unites[$unit->id] = Crypt::decryptString($unit->unit);

        return view('admin.card.edit', compact('cardObj','properties','unites','buildings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $cardObj = Card::find($id);

        if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'card' =>[
                'required', 
                Rule::unique('cards')
                    ->where('account_id', $input['account_id'])
                    ->whereNotIn('id',[$id])
            ],
            
        ]);

        if ($validator->fails()) { 
             return redirect("opslogin/card/$id/edit")->with('status', 'Card already exist!');         
        }

        

        $cardObj->card = $request->input('card');
        $cardObj->building_no = $request->input('building_no');
        $cardObj->unit_no = $request->input('unit_no');
        $cardObj->status = $request->input('status');
        $cardObj->remarks = $request->input('remarks');
        
        
        $cardObj->save();

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$cardObj->unit_no.'/11';
            return redirect($return_url)->with('status', 'Card has been updated!');
        }
        else
            return redirect('opslogin/card')->with('status', 'Card has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $cardObj = Card::find($id);

        Card::findOrFail($id)->delete();
        
        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$cardObj->unit_no.'/11';
            return redirect($return_url)->with('status', 'Card has been updated!');
        }
        else
            return redirect('opslogin/card')->with('status', 'Card deleted successfully!');
    }

     public function search(Request $request){

        $q= $option = $card = $status  = $unit ='';
        $option = $request->input('option'); 
        $card = $request->input('card');
        $unit = $request->input('unit');
        $building = $request->input('building');
        $status = $request->input('status');

        $account_id = Auth::user()->account_id;
        $buildings = array();
        if($building !=''){
            $BuildingObj = Building::select('id','building')->where('account_id',$account_id)->where('building', 'LIKE', '%' . $building . '%')->get();
            if(isset($BuildingObj)){
                foreach($BuildingObj as $buildingid){
                        $buildings[] = $buildingid->id;
                }
            }
        }

        $units = array();
        if($unit !=''){   
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
            })->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
                        $units[] = $unitid->id;
                }
            }
        }

        $cards = UserCard::where('property_id',$account_id)->where(function ($query) use ($card,$unit,$units,$building,$buildings,$status) {
            if($card !='')
                $query->where('card_no', 'LIKE', '%' . $card . '%');
            if($unit !='')
                $query->whereIn('unit_id', $units);
            if($building !='')
                $query->whereIn('building_id', $buildings);
            if($status !='')
                $query->where('status',$status);
        })->paginate(env('PAGINATION_ROWS'));

        
            return view('admin.card.index', compact('cards','q','option','card','status','unit','building'));

   }


   public function getcards(Request $request)
    {
        
        $cards = array();

        $unit = $request->unit;
        
        $cards = DB::table("cards")->where("status",1)->where('unit_no',$unit)->orderby('card','asc')->pluck("card","id");

         return json_encode($cards);

       /*

        $employees =   DB::table('users')->select('id','name')->where('role_id', $role)->orderBy('name','asc')->get();
       // $employees = User::where('name', 'LIKE', "%" . $term . "%")->take(10)->get();

        $data = [];

        foreach ($employees as $key => $value) {
            $empname = $value->name;
            $data[] = ['id' => $value->id, 'value' => $empname];
        }
        return response()->json($data);
        $data = []; 
        */
    }

    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public function importcsv()
    {
       
        $file = public_path('import/'.Auth::user()->account_id.'/cards.csv');

        $cardArr = $this->csvToArray($file);
      

        for ($i = 0; $i < count($cardArr); $i ++)
        {
            $cardArr[$i]['card'] = str_replace("#",'',$cardArr[$i]['card']);
            $cardArr[$i]['account_id'] = Auth::user()->account_id;
            $userinfo = Card::create($cardArr[$i]);
        }

        return redirect('opslogin/card')->with('status', 'Records has been imported!');
    }

}

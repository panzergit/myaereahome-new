<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;

use App\Models\v7\FeedbackOption;
use App\Models\v7\FeedbackSubmission;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use Auth;
use DB;

class FeedbackOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

   

     public function index()
    {
        $q ='';
        $account_id = Auth::user()->account_id;
        $feedbacks = FeedbackOption::where('account_id',$account_id)->paginate(150);   
        return view('admin.feedback.index', compact('feedbacks','q'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::pluck('name','id')->all();
        return view('admin.feedback.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // print_r($request->unit);
       $input = $request->all();

       if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;


       $validator = Validator::make($request->all(), [ 
        'feedback_option' =>[
            'required', 
            Rule::unique('feedback_options')
                   ->where('account_id', $input['account_id'])
        ],
        
        ]);
        if ($validator->fails()) { 

             return redirect('opslogin/configuration/feedback/create#settings')->with('status', 'Feedback option already exist!');         
        }


       $input['account_id'] = Auth::user()->account_id;

        FeedbackOption::create($input);
        
        return redirect('opslogin/configuration/feedback#settings')->with('status', 'Option has been added!');
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
    public function edit($id)
    {
        //

        $feedbackObj = FeedbackOption::find($id);
        return view('admin.feedback.optionedit', compact('feedbackObj'));
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

        $unitObj = FeedbackOption::find($id);

        $input = $request->all();


        if($request->input('account_id') !='')
            $unitObj->account_id = $request->input('account_id');
        else
            $unitObj->account_id= Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'feedback_option' =>[
                'required', 
                Rule::unique('feedback_options')
                    ->where('account_id', $unitObj->account_id)
                    ->whereNotIn('id',[$id])
            ],
            
        ]);

        if ($validator->fails()) { 
            return redirect("opslogin/configuration/feedback/$id/edit")->with('status', 'Feedback option already exist!');         
        }


        $unitObj->feedback_option = $request->input('feedback_option');
        
        
        $unitObj->save();
        return redirect('opslogin/configuration/feedback#settings')->with('status', 'Feedback option has been updated!');
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

        FeedbackOption::findOrFail($id)->delete();
        return redirect('opslogin/configuration/feedback#settings')->with('status', 'Feedback option deleted successfully!');
    }

     public function search(Request $request){
        $q = $request->input('q');
        if($q != "" ){

        $units = FeedbackOption::where('unit', 'LIKE', '%'.$q .'%')->paginate(50);
         
        return view('admin.feedback.index', compact('units','q'));
        }
       
        else{
         return redirect('opslogin/project');
        }
   }


   public function submit()
   {
       $q ='';
       $account_id = Auth::user()->account_id;
       $feedbacks = FeedbackOption::where('account_id', $account_id)->pluck('feedback_option', 'id')->all();
       //$feedbacks = FeedbackOption::paginate(150);   
       return view('user.feedbacksubmit', compact('feedbacks','q'));
   }

   public function save(Request $request)
   {
      

        $input = $request->all();

        $ticket = new \App\Models\v7\FeedbackSubmission();
        $input['user_id'] = Auth::user()->id;
        $input['account_id'] = Auth::user()->account_id;
        $input['ticket'] = $ticket->ticketgen();
        
        if ($request->file('upload') != null) {
            $input['upload'] = $request->file('upload')->store(upload_path('feedback'));
        }
        $input['user_id'] = Auth::user()->id;
        $input['status'] = 0;
        FeedbackSubmission::create($input);

        return redirect('opslogin/feedback/lists#settings')->with('status', 'Feedback has been sent!');
   }

   public function submitlists()
   {
       $user = Auth::user()->id;
       $feedbacks = FeedbackSubmission::where('user_id',$user)->orderby('id','desc')->paginate(50);  

       return view('user.feedbacklists', compact('feedbacks'));
   }
}

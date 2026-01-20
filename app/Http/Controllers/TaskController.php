<?php

namespace App\Http\Controllers;

use App\Models\v7\Task;
use App\Models\v7\User;
use Illuminate\Http\Request;
use Validator;
use Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::paginate(50);   
        return view('admin.task.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $q ='';
        $users = User::pluck('name','id')->all();
        return view('admin.task.create', compact('status','users','q'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'title' => 'required|unique:tasks' 
        ]);
        if ($validator->fails()) { 
             return redirect('opslogin/task/create')->with('status', 'Task already exist!');         
        }
        
        $user = Auth::user();
        $input = $request->all();  
        $input['added_by'] = $user->id;

        Task::create($input);

        return redirect('opslogin/task')->with('status', 'Task has been created!');
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
        $users = User::pluck('name','id')->all();
        $taskObj = Task::find($id);
        

        return view('admin.task.edit', compact('taskObj','status','users'));
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

        $taskObj = Task::find($id);

        $validator = Validator::make($request->all(), [ 

            'title' => 'required|unique:tasks,title,'.$id

        ]);
        if ($validator->fails()) { 
             return redirect("opslogin/task/$id/edit")->with('status', 'Task already exist!');         
        }


        $taskObj->title = $request->input('title');
        $taskObj->assigned_to = $request->input('assigned_to');
        $taskObj->start_on = $request->input('start_on');
        $taskObj->deadline = $request->input('deadline');
        $taskObj->notes = $request->input('notes');
        
        $taskObj->save();
        return redirect('opslogin/task')->with('status', 'Task has been updated!');
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

        Task::findOrFail($id)->delete();
        return redirect('opslogin/task')->with('status', 'Task deleted successfully!');
    }

    public function search(Request $request){
        $q = $request->input('q');
        if($q != "" ){

        $tasks = Task::where('title', 'LIKE', '%'.$q .'%')->paginate(50);
         
        return view('admin.task.index', compact('tasks','q'));
        }
       
        else{
         return redirect('opslogin/task');
        }
   }
}

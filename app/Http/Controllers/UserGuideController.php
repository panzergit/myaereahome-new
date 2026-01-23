<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;

use App\Models\v7\UserGuide;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use Auth;
use DB;

class UserGuideController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $q ='';
       // $account_id = Auth::user()->account_id;
        $fileObj =UserGuide::where('id',1)->first();
        //$files = UserGuide::orderby('id','desc')->paginate(150);   
        return view('admin.userguide.index', compact('fileObj','q'));
    }

    public function viewfiles($id)
    {
        $q ='';
        $account_id = Auth::user()->account_id;
        $category = MagazineCategory::where('id',$id)->first();   
        $files =UserGuide::where('cat_id',$id)->get();   
        return view('admin.userguide.viewfiles', compact('category','files','q'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addfiles($id)
    {   
        $account_id = Auth::user()->account_id;

        $category = MagazineCategory::where('account_id',$account_id)->pluck('docs_category', 'id')->all();
        
        return view('admin.userguide.create', compact('category','id'));
    }

    public function create()
    {    
        return view('admin.userguide.create');
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

        /*$validator = Validator::make($request->all(), [ 
            'docs_category' =>[
                'required', 
                Rule::unique('condodoc_categories')
                    ->where('account_id', $input['account_id'])
            ],
            
        ]); 
       
        if ($validator->fails()) { 

             return redirect('opslogin/docs-category/create')->with('status', 'Condo document category already exist!');         
        }
        */


         /********** INSERT Defect Type******************/
         for ($i = 1; $i <= 1; $i++) {
            $file = 'file_' . $i;
            $file_name = 'file_name_' . $i;
            $file_image = 'file_image_' . $i;
            $type['created_at'] = date("Y-m-d H:i:s");
            if ($request->file($file) != null) {
                $type['docs_file'] = $request->file($file)->store(upload_path('userguide'));
            }
            if ($request->file($file) != null) {
                $type['docs_file'] = $request->file($file)->store(upload_path('userguide'));
            }
            if ($request->file($file_image) != null) {
                $type['file_image'] = $request->file($file_image)->store(upload_path('userguide'));
            }
            if ($input[$file_name] != null) { 
                $type['docs_file_name'] = $input[$file_name];
                $condo_files[] = $type;
            }
           
        }

        if (isset($condo_files)) {
            UserGuide::insert($condo_files);
        }

        
        return redirect('opslogin/userguide')->with('status', 'User Guide added'); 
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
        $account_id = Auth::user()->account_id;
        $fileObj =UserGuide::find($id);
        $img_full_path = env('APP_URL')."/storage/app/";
        return view('admin.userguide.edit', compact('fileObj','img_full_path'));
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

        
        $FileObj =UserGuide::find($id);

        $input = $request->all();

        if($request->input('account_id') !='')
            $FileObj->account_id = $request->input('account_id');
        else
            $FileObj->account_id= Auth::user()->account_id;

       
        if ($request->file('docs_file') != null) {
            $FileObj->docs_file = $request->file('docs_file')->store(upload_path('userguide'));
        }
        $FileObj->docs_file_name = $request->input('file_name');
        $FileObj->url_type = $request->input('url_type');
        $FileObj->url_link = $request->input('url_link');
        //echo $request->input('file_name');

        $FileObj->save();


        return redirect('opslogin/userguide')->with('status', 'User Guide has been updated!'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $FileObj =UserGuide::find($id);
         UserGuide::findOrFail($id)->delete();
        return redirect('opslogin/userguide')->with('status', 'User Guide deleted successfully!');
    }

   
}

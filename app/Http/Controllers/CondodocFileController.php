<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;

use App\Models\v7\DocsCategory;
use App\Models\v7\CondodocFile;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use Auth;
use DB;

class CondodocFileController extends Controller
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
        $category = DocsCategory::where('id',$id)->where('account_id',$account_id)->paginate(150);   
        return view('admin.condodoc_file.index', compact('category','q'));
    }

    public function viewfiles($id)
    {
        $q ='';
        $account_id = Auth::user()->account_id;
        $category = DocsCategory::where('id',$id)->first();   
        $files = CondodocFile::where('cat_id',$id)->get();   
        return view('admin.condodoc_file.viewfiles', compact('category','files','q'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addfiles($id)
    {   
        $account_id = Auth::user()->account_id;


        $category = DocsCategory::where('account_id',$account_id)->pluck('docs_category', 'id')->all();
        
        return view('admin.condodoc_file.create', compact('category','id'));
    }

    public function create()
    {   
        $account_id = Auth::user()->account_id;


        $category = DocsCategory::where('account_id',$account_id)->pluck('docs_category', 'id')->all();
        
        return view('admin.condodoc_file.create', compact('category'));
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

       $input['account_id'] = Auth::user()->account_id;

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
         for ($i = 1; $i <= 10; $i++) {
            $file = 'file_' . $i;
            $file_name = 'file_name_' . $i;

            if ($request->file($file) != null) {
                $type['docs_file'] = $request->file($file)->store('condofile');
            }

            if ($input[$file_name] != null) { 
                //$type['account_id'] = $input['cat_id'];
                $type['cat_id'] = $input['cat_id'];
                //$type['docs_file'] = $input[$file_name];
                $type['docs_file_name'] = $input[$file_name];
                //$type['created_at'] = $category->created_at;
                //$type['updated_at'] = $category->updated_at;
                $condo_files[] = $type;
            }
        }

        if (isset($condo_files)) {
            CondodocFile::insert($condo_files);
        }

        
        return redirect('opslogin/docs-files/view/'.$input['cat_id'])->with('status', 'Condo document files added'); 
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

        $category = DocsCategory::where('account_id',$account_id)->pluck('docs_category', 'id')->all();

        $fileObj = CondodocFile::find($id);

        $img_full_path = env('APP_URL')."/storage/app/";
        return view('admin.condodoc_file.edit', compact('category','fileObj','img_full_path'));
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

        
        $FileObj = CondodocFile::find($id);

        $input = $request->all();



        if($request->input('account_id') !='')
            $FileObj->account_id = $request->input('account_id');
        else
            $FileObj->account_id= Auth::user()->account_id;


        /* $validator = Validator::make($request->all(), [ 
            'docs_category' =>[
                'required', 
                Rule::unique('condodoc_categories')
                       ->where('account_id', $DocObj->account_id)
                       ->whereNotIn('id',[$id])
            ],
            
        ]);
        if ($validator->fails()) { 
             return redirect("opslogin/docs-category/$id/edit")->with('status', 'Condo document category already exist!');         
        } */

      

        $cat_id = $request->input('cat_id');
        $FileObj->cat_id = $request->input('cat_id');
        
        if ($request->file('docs_file') != null) {
            $FileObj->docs_file = $request->file('docs_file')->store('condofile');
        }
        $FileObj->docs_file_name = $request->input('docs_file_name');

        $FileObj->save();


        return redirect('opslogin/docs-files/view/'.$cat_id)->with('status', 'Condo document file has been updated!'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $FileObj = CondodocFile::find($id);
        CondodocFile::findOrFail($id)->delete();
        return redirect('opslogin/docs-files/view/'.$FileObj->cat_id)->with('status', 'File deleted successfully!');
    }

   
}

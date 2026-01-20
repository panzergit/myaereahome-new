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

class DocsCategoryController extends Controller
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
        $types = DocsCategory::where('account_id',$account_id)->paginate(150);   
        return view('admin.condodoc_category.index', compact('types','q'));
    }

    public function viewfiles($id)
    {
        $q ='';
        $account_id = Auth::user()->account_id;
        $category = DocsCategory::where('id',$id)->where('account_id',$account_id)->paginate(150);   
        return view('admin.condodoc_category.viewfiles', compact('category','q'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::pluck('name','id')->all();
        return view('admin.condodoc_category.create', compact('users'));
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

        $validator = Validator::make($request->all(), [ 
            'docs_category' =>[
                'required', 
                Rule::unique('condodoc_categories')
                    ->where('account_id', $input['account_id'])
            ],
            
        ]);
       
        if ($validator->fails()) { 

             return redirect('opslogin/docs-category/create')->with('status', 'Condo document category already exist!');         
        }


        $category = DocsCategory::create($input);

         /********** INSERT Defect Type******************/
         for ($i = 1; $i <= 10; $i++) {
            $file = 'file_' . $i;
            $file_name = 'file_name_' . $i;
            $original_file = 'original_file_name_'.$i;

            if ($request->file($file) != null) {
                $type['docs_file'] = $request->file($file)->store('condofile');
            }

            if ($input[$file_name] != null) { 
                $type['account_id'] = $input['account_id'];
                $type['cat_id'] = $category->id;
                //$type['docs_file'] = $input[$file_name];
                $type['docs_file_name'] = $input[$file_name];
                $type['original_file_name'] = $input[$original_file];
                $type['created_at'] = $category->created_at;
                $type['updated_at'] = $category->updated_at;
                $condo_files[] = $type;
            }
        }

        if (isset($condo_files)) {
            CondodocFile::insert($condo_files);
        }

        
        return redirect('opslogin/docs-category')->with('status', 'Condo document category added'); 
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

        $docsObj = DocsCategory::find($id);
       
        $doc_files =array();
        if (isset($docsObj->files)) {
            foreach ($docsObj->files as $k => $files) {
                $doc_file['key'] = $k + 1;
                $doc_file['id'] = $files['id'];
                $doc_file['docs_file'] = $files['docs_file'];
                $doc_file['docs_file_name'] = $files['docs_file_name'];
                $doc_file['original_file_name'] = $files['original_file_name'];
                $doc_files[$k + 1] = $doc_file;
            }
        }
        $img_full_path = env('APP_URL')."/storage/app/";


        return view('admin.condodoc_category.edit', compact('docsObj','doc_files','img_full_path'));
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

        $DocObj = DocsCategory::find($id);

        $input = $request->all();

        if($request->input('account_id') !='')
            $DocObj->account_id = $request->input('account_id');
        else
            $DocObj->account_id= Auth::user()->account_id;


        $validator = Validator::make($request->all(), [ 
            'docs_category' =>[
                'required', 
                Rule::unique('condodoc_categories')
                       ->where('account_id', $DocObj->account_id)
                       ->whereNotIn('id',[$id])
            ],
            
        ]);
        if ($validator->fails()) { 
             return redirect("opslogin/docs-category/$id/edit")->with('status', 'Condo document category already exist!');         
        }


        $DocObj->docs_category = $request->input('docs_category');
        
        $defectObj =  $DocObj->save();

         /********** INSERT documents START******************/
         for ($i = 1; $i <= 10; $i++) {
            $file = 'file_' . $i;
            $file_name = 'file_name_' . $i;
            $file_id = 'file_id_' . $i;
            $original_file_name = 'original_file_name_'.$i;
            //$request->file($file);
            
            //echo $request->input($file_name);

            if ($request->input($file_id) != null) {

                /*echo "ID: ".$request->input($file_id);
                echo "NAme :".$request->input($file_name);
                echo "<hr />";*/
                if($request->input($file_name) ==''){
                    CondodocFile::findOrFail($request->input($file_id))->delete();
                }else{
                    $fileObj = CondodocFile::find($request->input($file_id));
                    $fileObj->cat_id = $id;

                    if ($request->file($file) != null) {
                        $fileObj->docs_file = $request->file($file)->store('condofile');
                    }
                    $fileObj->original_file_name= $request->input($original_file_name);

                    $fileObj->docs_file_name = $request->input($file_name);
                    $fileObj->created_at = $DocObj->created_at;
                    $fileObj->updated_at = $DocObj->updated_at;
                    $fileObj->save();
                }

            } else if ($request->input($file_name) != null) {
                $type = array();
                $type['account_id'] = $DocObj->account_id;
                $type['cat_id'] = $id;

                if ($request->file($file) != null) {
                    $type['docs_file'] = $request->file($file)->store('condofile');
                }
                $type['original_file_name']= $input[$original_file_name]; 
                $type['docs_file_name'] = $input[$file_name];
                $type['created_at'] = $DocObj->created_at;
                $type['updated_at'] = $DocObj->updated_at;
                CondodocFile::insert($type);
                
            }

            
        }

       // exit;

        return redirect('opslogin/docs-category')->with('status', 'Condo document category updated'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        CondodocFile::where('cat_id', $id)->delete();
        DocsCategory::findOrFail($id)->delete();
        return redirect('opslogin/docs-category')->with('status', 'Condo document deleted successfully!');
    }

    

    public function deleteCondoFile(Request $request)
    {
        $id = $request->fid;
        $result = CondodocFile::where('id', $id)->delete();
        return json_encode(1);

       
    }
   
}

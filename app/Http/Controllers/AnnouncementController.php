<?php

namespace App\Http\Controllers;

use App\Models\v7\Announcement;
use Illuminate\Http\Request;
use App\Models\v7\User;
use App\Models\v7\Role;
use App\Models\v7\InboxMessage;
use App\Models\v7\UserLog;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\AnnouncementCron;
use App\Models\v7\AnnouncementDetail;
use App\Models\v7\UserNotificationSetting;
use App\Models\v7\Property;
use App\Models\v7\Announcementtest;
use App\Models\v7\AnnouncementtestDetail;
use Auth;
use Mail;
use App\Models\v7\Mail\AnnouncementNotification;
use App\Models\v7\Setting;
use DB, Storage;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $startdate = $enddate = '';
        $user = $request->user();
        $whereCondition = ['account_id' => $user->account_id];

        $announcements = Announcement::where($whereCondition)->orderByDesc('id')->paginate(50);
        $roles = Role::where($whereCondition)->orWhere('type',1)->pluck('name', 'id')->all();

        AnnouncementDetail::where('user_id', $user->id)->update(['status' => '1']);
        
        $file_path = image_storage_domain();
        $icon_path = url('assets/admin/');

        return $user->role_id == 2 ? view('user.announcement', compact('announcements','file_path','icon_path')) 
            : view('admin.announcement.index', compact('roles','startdate','role','enddate','announcements','file_path','icon_path'));
    }

    public function viewdetails($id)
    {
        $announcement = Announcement::find($id);

        return view('admin.announcement.view', compact('announcement'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $env_roles 	= env('USER_APP_ROLE');
        $roles = Role::whereIn('id', explode(",",$env_roles))->pluck('name', 'id')->all();
        return view('admin.announcement.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $input['account_id'] = !isset($input['account_id']) ? $request->user()->account_id : $input['account_id'];

        foreach (range(1,5) as $i) {
            if ($request->hasFile("upload_$i")) {
                $key = $i === 1 ? 'upload' : "upload_$i";
                $input[$key] = remove_upload_path($request->file("upload_$i")->store(upload_path('announcement')));
            }
        }

        unset($input['upload_1']);

        $input['roles'] = implode(",",$input['role_array']);
        $announcement = Announcement::create($input);

        if($announcement) AnnouncementCron::create([
                'a_id' => $announcement->id,
                'account_id' => $announcement->account_id,
                'roles' => $announcement->roles
            ]);

        return redirect('opslogin/announcement')->with('status', 'Announcement has been uploaded!');;
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\v7\Announcement  $sharingAnnouncement
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $sharingAnnouncement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\v7\Announcement  $sharingAnnouncement
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $newsObj = Announcement::find($id);
        $roles = Role::pluck('name', 'id')->all();      
        $img_full_path = url('storage/app');
        return view('admin.announcement.edit', compact('newsObj','roles','img_full_path'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\v7\Announcement  $sharingAnnouncement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $newsObj = Announcement::find($id);

        $newsObj->title = $request->input('title');
        $newsObj->roles = $request->input('announcement_to');
        $newsObj->notes = $request->input('notes');
        $newsObj->save();

        /************* sending emails START ****************************/
        if($_ENV['MAIL_SEND'] == 1){

            $info = Announcement::findOrFail($id); 
            $subject = 'HR Announcement: '.$info->title." updated";
            
            if($info->announcement_to ==0)
                $users = DB::table('users as u')->select('u.email')->get(); 
            else {
                $users = DB::table('users as u')
                ->select('u.email')
                ->join("user_more_infos AS um", "um.user_id", "=", "u.id")
                ->where('um.department','=', $info->announcement_to)->get(); 

            }

            $admin = Setting::findOrFail(1);
            Mail::to($admin->company_email)->cc($users)->send(new AnnouncementNotification($info,$subject,'Admin'));

        }

        /************* Sending emails END ****************************/

         return redirect('opslogin/announcement')->with('status', 'Announcement has been updated!');;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Announcement  $sharingAnnouncement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AnnouncementDetail::where('a_id',$id)->delete();
        Announcement::findOrFail($id)->delete();
        return redirect('opslogin/announcement')->with('status', 'Announcement deleted successfully!');
    }

    public function deleteAnnouncement(Request $request)
    {
        $id = $request->input('id');
        $file_path = $request->input('file_path');

        $fileObj = Announcement::find($id);
        $fileObj->document = '';
        $fileObj->save();
        
        $file_path = $file_path;  // Value is not URL but directory file path
            if(Announcement::exists($file_path)) {
                Announcement::delete($file_path);
            }
        
        
         $success['message'] = "success";

          $request->session()->flash('message', " Announcement has been removed.");
          $request->session()->flash('message-type', 'success');

         return response()->json(['success'=>$success], 200); 
    }

    public function search(Request $request)
    {
       $role = $startdate = $enddate='';
      $account_id = Auth::user()->account_id;
      $file_path = url('storage/app');
      $icon_path = url('assets/admin');

      $roles = Role::where('account_id',$account_id)->orWhere('type',1)->pluck('name', 'id')->all();

       $startdate = $request->input('startdate');
        if($request->input('enddate') !='')
            $enddate = $request->input('enddate');
        else
             $enddate =$request->input('startdate');

        if($request->input('roles') !='') 
            $role = $request->input('roles');



            $announcements = Announcement::where('account_id',$account_id)->where(function($query) use ($startdate,$enddate,$role ){

                 if($role !='' && $role !='a'){
                   $query->where('roles',$role);
                }
              
                if($startdate !=''){
                    $query->whereBetween('created_at',array($startdate,$enddate));
                }
                
            })->orderby('id','desc')->paginate(50);



        if($role != "" ){

         
        return view('admin.announcement.index', compact('roles','startdate','role','enddate','announcements','file_path','icon_path'));
        }
       
        else{
         return redirect('opslogin/announcement');
        }
   }
}

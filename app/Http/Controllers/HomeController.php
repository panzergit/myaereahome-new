<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\v7\Announcement;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $announcements = Announcement::orderBy('id','desc')->paginate(50);
        return view(($user->role_id != 2) ? 'admin.index' : 'home',compact('announcements'));
    }
}
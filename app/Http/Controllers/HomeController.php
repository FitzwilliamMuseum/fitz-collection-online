<?php

namespace App\Http\Controllers;

use App\Models\Api\ApiLog;
use Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        return view('home');
    }

    public function api()
    {
        return view('welcome');
    }

    public function activity()
    {
        $activity = ApiLog::where('user_id', '=', Auth::user()->id)->paginate(10);
        return view('home',compact('activity'));
    }
}

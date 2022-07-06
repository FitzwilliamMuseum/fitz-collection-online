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
        $totals = ApiLog::groupBy('request_method')->selectRaw('count(*) as total, request_method')->get();
        $codes = ApiLog::groupBy('response_status_code')->selectRaw('count(*) as total, response_status_code')->get();

        return view('home',compact('activity','totals','codes'));
    }
}

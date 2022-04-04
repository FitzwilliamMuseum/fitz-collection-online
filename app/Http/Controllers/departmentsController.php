<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class departmentsController extends Controller
{

    /**
     * @param Request $request
     * @return View
     */
    public function record(Request $request): View
    {
        $id = urlencode($request->segment(3));
        $count = Departments::count($id);
        $name = urldecode($id);
        $connected = Departments::connected($request, $id);
        return view('departments.record', compact('count', 'name', 'connected'));
    }
}

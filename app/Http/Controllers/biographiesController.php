<?php

namespace App\Http\Controllers;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Biographies;

class biographiesController extends Controller
{

    /**
     * @param Request $request
     * @return View
     */
    public function record(Request $request): View
    {
        $id = $request->segment(3);
        $data = Biographies::list($id);
        $count = Biographies::count($id);
        $connected = Biographies::connected($request, $id);
        return view('biographies.record', compact('data', 'connected', 'count'));
    }
}

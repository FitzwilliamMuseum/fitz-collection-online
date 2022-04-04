<?php

namespace App\Http\Controllers;

use App\Models\Publications;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class publicationsController extends Controller
{

    /**
     * @param Request $request
     * @return View
     */
    public function record(Request $request): View
    {
        $id = $request->segment(3);
        $data = Publications::list($id);
        $count = Publications::count($id);
        $connected = Publications::connected($request, $id);
        return view('publications.record', compact('data', 'count', 'connected'));
    }
}

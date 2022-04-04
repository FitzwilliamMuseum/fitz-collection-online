<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Exhibitions;

class exhibitionsController extends Controller
{

    /**
     * @param Request $request
     * @return View
     */
    public function exhibition(Request $request): View
    {
        $id         =  $request->segment(3);
        $exhibition =  Exhibitions::list($id);
        $connected  =  Exhibitions::connected($request,$id);
        return view('exhibitions.exhibition', compact('exhibition', 'connected'));
    }
}

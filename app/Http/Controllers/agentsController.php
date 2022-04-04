<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Agents;

class agentsController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function record(Request $request): View
    {
        $id = $request->segment(3);
        $data = Agents::list($id);
        $count = Agents::count($id);
        $connected = Agents::connected($request, $id);
        return view('agents.record', compact('data', 'connected',  'count'));
    }
}

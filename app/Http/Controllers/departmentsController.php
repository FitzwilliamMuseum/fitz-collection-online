<?php

namespace App\Http\Controllers;

use App\LinkedArt\Department;
use App\Models\Departments;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class departmentsController extends Controller
{

    /**
     * @return View
     */
    public function index(): View
    {
        $departments = Departments::listDepartments();
        return view('departments.index', compact('departments'));
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Application|ResponseFactory|Factory|View|JsonResponse|Response
     */
    public function record(Request $request, string $id): View|Factory|Response|JsonResponse|Application|ResponseFactory
    {
        $department = urlencode($id);
        $name = urldecode($department);
        $connected = Departments::connected($request, $department);
        return $this->machineResponse($request, $name, $connected);
    }

    /**
     * @param Request $request
     * @param string $name
     * @param LengthAwarePaginator $connected
     * @return View|Factory|Response|JsonResponse|Application|ResponseFactory
     */
    public function machineResponse(Request $request, string $name, LengthAwarePaginator $connected): View|Factory|Response|JsonResponse|Application|ResponseFactory
    {
        $formats = array(null, 'json', 'html', 'linked-art');
        $validator = Validator::make(['format' => $request->get('format')], [
            "format" => "in:" . implode(",", $formats)
        ]);

        if ($validator->fails()) {
            abort(500, $validator->errors());
        }

        switch ($request) {
            case $request->get('format') == 'json' || $request->header('Accept') == 'application/json':
                return response()->json(array($name, $connected));
            case $request->get('format') == 'linked-art' || $request->header('Accept') === 'application/ld+json;profile=\"https://linked.art/ns/v1/linked-art.json\"':
                return response()->json(Department::createLinkedArtDepartment($name));
               case $request->get('format') == 'html' || $request->header('Accept') == 'text/html':
            default:
                return view('departments.record', compact( 'name', 'connected'));
        }
    }
}

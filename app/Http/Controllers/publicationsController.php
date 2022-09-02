<?php

namespace App\Http\Controllers;

use App\CollectionXML;
use App\LinkedArt\References;
use App\Models\Publications;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class publicationsController extends Controller
{

    public function index(Request $request)
    {
        $agents = Publications::listPaginated($request);
        $paginator = new LengthAwarePaginator($agents['hits']['hits'], $agents['hits']['total']['value'],50, LengthAwarePaginator::resolveCurrentPage());
        $paginator->setPath(route('publications'));
        $paginator->appends(request()->except('page'));
        return view('publications.index', compact('paginator'));
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Application|ResponseFactory|Factory|View|JsonResponse|Response
     * @throws \DOMException
     */
    public function record(Request $request, string $id)
    {
        $data = Publications::list($id);
        $connected = Publications::connected($request, $id);
        return $this->machineResponse($request, $data, $connected);
//        return view('publications.record', compact('data', 'connected'));
    }

    /**
     * @param Request $request
     * @param array $data
     * @param LengthAwarePaginator $connected
     * @return View|Factory|Response|JsonResponse|Application|ResponseFactory
     * @throws \DOMException
     */
    public function machineResponse(Request $request, array $data, LengthAwarePaginator $connected): View|Factory|Response|JsonResponse|Application|ResponseFactory
    {
        $formats = array(null, 'json', 'html', 'xml', 'linked-art');
        $validator = Validator::make(['format' => $request->get('format')], [
            "format" => "in:" . implode(",", $formats)
        ]);

        if ($validator->fails()) {
            abort(500, $validator->errors());
        }
        switch ($request) {
            case $request->get('format') == 'json' || $request->header('Accept') == 'application/json':
                return response()->json(array($data, $connected));
            case $request->get('format') == 'linked-art' || $request->header('Accept') === 'application/ld+json;profile=\"https://linked.art/ns/v1/linked-art.json\"':
                return response()->json(References::createLinkedArtPublication(Collect($data)->toArray(), $connected));
            case $request->get('format') == 'xml' || $request->header('Accept') == 'application/xml':
                $xml = CollectionXML::createXML($data);
                return response($xml, 200)->header('Content-Type', 'application/xml');
            case $request->get('format') == 'html' || $request->header('Accept') == 'text/html':
            default:
            return view('publications.record', compact('data', 'connected'));
        }
    }
}

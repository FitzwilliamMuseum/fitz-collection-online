<?php

namespace App\Http\Controllers;

use App\CollectionXML;
use App\LinkedArt\Exhibition;
use DOMException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Exhibitions;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class exhibitionsController extends Controller
{

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $exhibitions = Exhibitions::listPaginated($request);
        $paginator = new LengthAwarePaginator(
            $exhibitions['hits']['hits'],
            $exhibitions['hits']['total']['value'],
            50,
            LengthAwarePaginator::resolveCurrentPage()
        );
        $paginator->setPath(route('exhibitions'));
        $paginator->appends(request()->except('page'));
        return view('exhibitions.index', compact('paginator'));
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Application|ResponseFactory|Factory|View|JsonResponse|Response
     * @throws DOMException
     */
    public function exhibition(Request $request, string $id): View|Factory|Response|JsonResponse|Application|ResponseFactory
    {
        $exhibition = Exhibitions::show($id)['_source'];
        $connected = Exhibitions::connected($request, $id);
        return $this->machineResponse($request, $exhibition, $connected);
    }


    /**
     * @param Request $request
     * @param array $exhibition
     * @param LengthAwarePaginator $connected
     * @return View|Factory|Response|JsonResponse|Application|ResponseFactory
     * @throws DOMException
     */
    public function machineResponse(Request $request, array $exhibition, LengthAwarePaginator $connected): View|Factory|Response|JsonResponse|Application|ResponseFactory
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
                return response()->json($exhibition);
            case $request->get('format') == 'linked-art' || $request->header('Accept') === 'application/ld+json;profile=\"https://linked.art/ns/v1/linked-art.json\"':
                return response()->json(Exhibition::createLinkedArtExhibition(Collect($exhibition)->toArray(), $connected));
            case $request->get('format') == 'xml' || $request->header('Accept') == 'application/xml':
                $xml = CollectionXML::createXML($exhibition);
                return response($xml, 200)->header('Content-Type', 'application/xml');
            case $request->get('format') == 'html' || $request->header('Accept') == 'text/html':
            default:
                return view('exhibitions.exhibition', [
                    'exhibition' => $exhibition,
                    'connected' => $connected
                ]);
        }
    }
}

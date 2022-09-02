<?php

namespace App\Http\Controllers;

use App\CollectionXML;
use App\LinkedArt\Place;
use DOMException;
use App\RdfConvertor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Terminology;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\AxiellTerm;
use App\Models\LinkedArtIdentifiers;
use Illuminate\Support\Facades\Validator;
use stdClass;

class terminologyController extends Controller
{

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $agents = Terminology::listPaginated($request);
        $paginator = new LengthAwarePaginator(
            $agents['hits']['hits'],
            $agents['hits']['total']['value'],
            50,
            LengthAwarePaginator::resolveCurrentPage()
        );
        $paginator->setPath(route('terminologies'));
        $paginator->appends(request()->except('page'));
        return view('terminology.index', compact('paginator'));
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Application|ResponseFactory|Factory|View|JsonResponse|Response
     * @throws DOMException
     */
    public function record(Request $request, string $id): View|Factory|Response|JsonResponse|Application|ResponseFactory
    {
        $data = Terminology::show($id);
        $connected = Terminology::connected($request, $id);
        $identifiers = LinkedArtIdentifiers::find($id);
        $axiell = AxiellTerm::find(str_replace('term-', '', $id));
        return $this->machineResponse($request, $data, $connected, $identifiers, $axiell);
    }

    /**
     * @param Request $request
     * @param array $data
     * @param LengthAwarePaginator $connected
     * @param array|null $identifiers
     * @param stdClass $axiell
     * @return View|Factory|Response|JsonResponse|Application|ResponseFactory
     * @throws DOMException
     */
    public function machineResponse(Request $request, array $data, LengthAwarePaginator $connected, ?array $identifiers, stdClass $axiell): View|Factory|Response|JsonResponse|Application|ResponseFactory
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
                return response()->json($data);
            case $request->get('format') == 'linked-art' || $request->header('Accept') === 'application/ld+json;profile=\"https://linked.art/ns/v1/linked-art.json\"':
                return response()->json(Place::createLinkedArtPlace($data));
            case $request->get('format') == 'xml' || $request->header('Accept') == 'application/xml':
                $xml = CollectionXML::createXML($data);
                return response($xml, 200)->header('Content-Type', 'application/xml');
            case $request->get('format') == 'html' || $request->header('Accept') == 'text/html':
            default:
                return view('terminology.term', [
                    'data' => $data,
                    'connected' => $connected,
                    'axiell' => $axiell,
                    'identifiers' => $identifiers
                ]);
        }
    }
}

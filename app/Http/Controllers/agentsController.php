<?php

namespace App\Http\Controllers;

use DOMException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Agents;
use App\LinkedArt\Person;
use App\Models\LinkedArtIdentifiers;
use App\Models\AxiellAgent;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use App\CollectionXML;

class agentsController extends Controller
{

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $agents = Agents::listPaginated($request);
        $paginator = new LengthAwarePaginator(
            $agents['hits']['hits'],
            $agents['hits']['total']['value'],
            50,
            LengthAwarePaginator::resolveCurrentPage()
        );
        $paginator->setPath(route('agents'));
        $paginator->appends(request()->except('page'));
        return view('agents.index', compact('paginator'));
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Application|ResponseFactory|Factory|View|JsonResponse|Response
     * @throws DOMException
     */
    public function record(Request $request, string $id): View|Factory|Response|JsonResponse|Application|ResponseFactory
    {
        $data = Agents::show($id);
        $connected = Agents::connected($request, $id);
        $makers = Agents::getMakerUsage($id);
        $acquisition = Agents::getAcquisitionUsage($id);
        $owner = Agents::getOwnerUsage($id);
        $identifiers = LinkedArtIdentifiers::find($id);
        $axiell = AxiellAgent::find($id);
        return $this->machineResponse(
            $request, $data, $connected, $makers, $acquisition, $owner, $identifiers, $axiell
        );
    }

    /**
     * @param Request $request
     * @param array $data
     * @param LengthAwarePaginator $connected
     * @param int $makers
     * @param int $acquisition
     * @param int $owner
     * @param array|null $identifiers
     * @param $axiell
     * @return View|Factory|Response|JsonResponse|Application|ResponseFactory
     * @throws DOMException
     */
    public function machineResponse(Request $request, array $data, LengthAwarePaginator $connected, int $makers, int $acquisition, int $owner, ?array $identifiers, $axiell ): View|Factory|Response|JsonResponse|Application|ResponseFactory
    {
        $formats = array(null, 'json', 'xml', 'html', 'linked-art');
        $validator = Validator::make(['format' => $request->get('format')], [
            "format" => "in:" . implode(",", $formats)
        ]);

        if ($validator->fails()) {
            abort(500, $validator->errors());
        }
        switch ($request) {
            case $request->get('format') == 'json' || $request->header('Accept') == 'application/json':
                return response()->json(Collect($data)->toArray());
            case $request->get('format') == 'linked-art' || $request->header('Accept') === 'application/ld+json;profile=\"https://linked.art/ns/v1/linked-art.json\"':
                return response()->json(Person::createLinkedArtPerson(Collect($data)->toArray()));
            case $request->get('format') == 'xml' || $request->header('Accept') == 'application/xml':
                $xml = CollectionXML::createXML($data);
                return response($xml, 200)->header('Content-Type', 'application/xml');
            case $request->get('format') == 'html' || $request->header('Accept') == 'text/html':
            default:
                return view('agents.record', [
                    'data' => $data,
                    'connected' => $connected,
                    'makers' => $makers,
                    'acquisition' => $acquisition,
                    'owner' => $owner,
                    'identifiers' => $identifiers,
                    'axiell' => $axiell
                ]);
        }
    }


}

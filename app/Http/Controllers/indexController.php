<?php

namespace App\Http\Controllers;

use App\LinkedArt\ObjectOrArtwork;
use App\Models\Objects;
use DOMException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Redirect;
use stdClass;
use App\Models\CIIM;
use App\Models\AxiellLocation;
use App\Models\SpoliationClaims;
use App\CollectionXML;


class indexController extends Controller
{

    /**
     * @param Request $request
     * @param int $priref
     * @return Application|ResponseFactory|Factory|\Illuminate\Contracts\View\View|JsonResponse|Response
     * @throws DOMException
     */
    public function record(Request $request, int $priref): \Illuminate\Contracts\View\View|Factory|Response|JsonResponse|Application|ResponseFactory
    {
        $data       = Objects::find($priref);
        $spoliation = SpoliationClaims::find($priref)['data'];
        $location   = AxiellLocation::find($priref);
        return $this->machineResponse($request, $data, $spoliation, $location, $priref);
    }

    /**
     * @param Request $request
     * @param array $data
     * @param array $spoliation
     * @param stdClass $location
     * @return \Illuminate\Contracts\View\View|Factory|Response|JsonResponse|Application|ResponseFactory
     * @throws DOMException
     */
    public function machineResponse(Request $request, array $data, array $spoliation, stdClass $location, string $priref): \Illuminate\Contracts\View\View|Factory|Response|JsonResponse|Application|ResponseFactory
    {
        $formats = array(null, 'json', 'xml', 'html','linked-art','txt','qr');
        $validator = Validator::make(['format' => $request->get('format')], [
            "format" => "in:" . implode(",", $formats)
        ]);

        if ($validator->fails()) {
            abort(500, $validator->errors());
        }
        switch($request) {
            case $request->get('format') == 'json' || $request->header('Accept') == 'application/json':
                return response()->json(Collect($data)->toArray());
            case $request->get('format') == 'linked-art' || $request->header('Accept') === 'application/ld+json;profile=\"https://linked.art/ns/v1/linked-art.json\"':
                return response()->json(ObjectOrArtwork::createLinkedArt(Collect($data), $priref));
            case $request->get('format') == 'qr':
                return response(view('record.qr', array('data' => $data)), 200);
            case $request->get('format') == 'xml' || $request->header('Accept') == 'application/xml':
                $xml = CollectionXML::createXML($data);
                return response($xml, 200)->header('Content-Type', 'application/xml');
            case $request->get('format') =='txt' || $request->header('Accept') == 'text/plain':
                return response(view('record.txt', array('data' => $data)), 200, ['Content-Type' => 'text/plain']);
            case $request->get('format') == 'html' || $request->header('Accept') == 'text/html':
            default:
                return view('record.index', [
                    'data' => $data,
                    'spoliation' => $spoliation,
                    'location' => $location
                ]);
        }
    }


    /**
     * @return RedirectResponse
     */
    public function search(): RedirectResponse
    {
        return Redirect::to(env('MAIN_URL') . '/objects-and-artworks');
    }

    /**
     * @param Request $request
     * @return View|Response
     * @throws ValidationException
     */
    public function results(Request $request): View|Response
    {
        $this->validate($request, [
            'query' => 'required|max:200|min:3',
        ]);
        $queryString = Purifier::clean($request->get('query'), array('HTML.Allowed' => ''));
        $response = CIIM::getSearchResults( $request);
        $records = $response['records'];
        $facets = $response['aggregations'];
        if (is_null($request->get('format'))) {
            return view('search.results', compact('records', 'queryString', 'facets'));
        } else {
            return response(view('search.searchJson', array('data' => array('results' => $records->items(), 'total' => $records->items()['hits']['total']))), 200, ['Content-Type' => 'application/json']);
        }
    }


    /**
     * @param array|Collection $items
     * @param int $perPage
     * @param int|null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    public function paginate(array|Collection $items, int $perPage = 24, int|null $page = NULL, array $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }


    /**
     * @return array
     */
    public function randomsearch(): array
    {
        $random = new stdClass();
        $random->seed = time();
        $params = [
            'index' => 'ciim',
            'size' => 1,
            'body' => []
        ];
        $params['body']['query']['function_score']['functions'][]['random_score'] = $random;
        $params['body']['query']['function_score']['query']['bool']['must'] = [
            [
                "match" => [
                    "department.value" => "Paintings, Drawings and Prints"
                ]
            ],
            [
                "term" => ["type.base" => 'object']
            ],
            [
                "exists" => ['field' => 'multimedia']
            ],
        ];

        $response = $this->getElastic()->setParams($params)->getSearch();
        return $response['hits']['hits'][0]["_source"];
    }

    /**
     * @return array
     */
    public function randomsearchapp(): array
    {
        $random = new stdClass();
        $random->seed = time();
        $params = [
            'index' => 'ciim',
            'size' => 1,
            'body' => []
        ];
        $params['body']['query']['function_score']['functions'][]['random_score'] = $random;
        $params['body']['query']['function_score']['query']['bool']['must'] = [
            [
                "match" => [
                    "department.value" => "Paintings, Drawings and Prints"
                ]
            ],
            [
                "term" => ["type.base" => 'object']
            ],
            [
                "exists" => ['field' => 'multimedia']
            ],
        ];

        $response = $this->getElastic()->setParams($params)->getSearch();
        $data = array();
        $data['data'][] = $response['hits']['hits'][0]["_source"];
        return $data;
    }
}

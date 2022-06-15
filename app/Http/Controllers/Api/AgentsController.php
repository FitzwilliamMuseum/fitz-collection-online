<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AgentsController extends ApiController
{
    /**
     * @var string $_fields
     */
    private string $_fields = 'admin.id,admin.created,admin.modified,name,summary_title';

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'size' => $this->getSize($request),
            'from' => $this->getFrom($request),
            'track_total_hits' => true,
            'body' => [
                'sort' => $this->getSort($request),
                'query' => [
                    "bool" => [
                        "must" => [
                            [
                                "term" => ["type.base" => 'agent']
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                $this->getFields($request, $this->_fields)
            ],
        ];
        if (!is_null($request->query('q'))) {
            $params['body']['query']['bool']['must'][] = ["multi_match" => [
                "fields" => "_generic_all_std",
                "query" => $request->query('q'),
                "operator" => "AND",
            ]
            ];
        }
        $response = $this->searchAndCache($params);

        $data = $this->parseData($response);
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->parseAgents($data);
        }
        $paginator = new LengthAwarePaginator(
            $data,
            $response['hits']['total']['value'],
            $this->getSize($request),
            LengthAwarePaginator::resolveCurrentPage()
        );
        $paginator->setPath(route('api.agents.index'));
        return $this->jsonGenerate($request, $paginator, $paginator->total());
    }


    /**
     * @param Request $request
     * @param string $agent
     * @return JsonResponse
     */
    public function show(Request $request, string $agent): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'match' => [
                        'admin.id' => $agent
                    ]
                ]
            ],
            '_source' => [
                $this->getFields($request, $this->_fields)
            ],
        ];
        $data = Collect($this->parse($this->searchAndCache($params)))->first();
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->enrichAgent($data);
            return $this->jsonSingle($data);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TerminologyController extends ApiController
{
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
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "term" => ["type.base" => 'term']
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                'admin.id,summary_title,related'
            ],
        ];
        if(!is_null($request->query('q'))) {
            $params['body']['query']['bool']['must'][] = ["multi_match" => [
                "fields" => "_generic_all_std",
                "query" => $request->query('q'),
                "operator" => "AND",
            ]
            ];
        }
        $response = $this->searchAndCache($params);
        $data = $this->parseData($response);
        if(empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->parseTerms($data);
        }
        $paginator = new LengthAwarePaginator($data, $response['hits']['total']['value'], $this->getSize($request), LengthAwarePaginator::resolveCurrentPage());
        $paginator->setPath(route('api.terminology.index'));
        return $this->jsonGenerate($request, $paginator, $response['hits']['total']['value']);
    }

    /**
     * @param string $term
     * @return JsonResponse
     */
    public function show(string $term): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.id" => $term
                                ]
                            ],
                            [
                                "term" => ["type.base" => 'term']
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
                'admin.id,admin.created,admin.modified,name,summary_title,type.base,related'
            ],
        ];
        $data = Collect($this->parse($this->searchAndCache($params)))->first();
        if(empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->enrichTerms($data);
            return $this->jsonSingle($data);
        }
    }
}

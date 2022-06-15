<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PlacesController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'aggregations' => [
                    'places' => [
                        'terms' => [
                            'field' => 'lifecycle.creation.places.admin.id',
                            'size' => 3800,
                            'order' => [
                                '_count' => $this->getSortParam($request),
                            ],
                        ],
                        'aggs' => [
                            'place' => [
                                'top_hits' => [
                                    'size' => 1,
                                    '_source' => [
                                        'include' =>
                                            [
                                                'lifecycle.creation.places.summary_title',
                                                'lifecycle.creation.places.admin.id',
                                            ]
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
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
        $data = $this->parseTerminologyAggPlace($response);
        if (empty($data)) {
            return $this->jsonError('500', $this->_error);
        }
        $items = $this->paginate($data, $this->getSize($request), LengthAwarePaginator::resolveCurrentPage());
        $items->setPath(route('api.places.index'));
        return $this->jsonAggGenerate($request, $items, $items->values(), count($response['aggregations']['places']['buckets']));
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.id" => $id
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
                'admin.id,admin.created,admin.modified,name,summary_title,parent.admin.id,parent.summary_title,parent.description'
            ],
        ];
        $data = Collect($this->parse($this->getClient()->search($params)))->first();
        if (!empty($data)) {
            return $this->jsonSingle($this->enrichPlace($data));
        } else {
            return $this->jsonError(400, $this->_notFound);
        }
    }
}

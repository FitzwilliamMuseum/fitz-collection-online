<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


class MakersController extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $params = [
            'index' => 'ciim',
            'track_total_hits' => true,
            'size' => $this->getSize($request),
            'from' => $this->getFrom($request),
            'body' => [
                'aggregations' => [
                    'records' => [
                        'terms' => [
                            'field' => 'lifecycle.creation.maker.admin.id',
                            'size' => 6000,
                            'order' => [
                                '_count' => $this->getSortParam($request),
                            ],
                        ],
                        'aggs' => [
                            'maker' => [
                                'top_hits' => [
                                    'size' => 1,
                                    '_source' => [
                                        'include' => [
                                            'lifecycle.creation.maker.summary_title',
                                            'lifecycle.creation.maker.admin.id'
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if (!is_null($request->query('q'))) {
            $params['body']['query']['bool']['must'][] = [
                [
                    "match" => [
                        "_generic_all_std" => [
                            'query' => $request->query('q'),
                            "operator" => "AND"
                            ]
                    ],

                ]
            ];
        }
        $response = $this->searchAndCache($params);
        $data = $this->parseTerminologyAggMakers($response);
        if(empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        }
        $items = $this->paginate($data, $this->getSize($request), LengthAwarePaginator::resolveCurrentPage());
        $items->setPath(route('api.makers.index'));
        if ($items->items()) {
            return $this->jsonAggGenerate($request, $items, $items->values(), count($response['aggregations']['records']['buckets']));
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }

    /**
     * @param string $maker
     * @return JsonResponse
     */
    public function show(string $maker): JsonResponse
    {
        $response = $this->searchAndCache([
            'index' => 'ciim',
            'body' => [
                'query' => [
                    'match' => [
                        'admin.id' => $maker
                    ]
                ]
            ],
            '_source' => [
                'admin.id,admin.created,admin.modified,name,summary_title'
            ],
        ]);
        $data = Collect($this->parse($response))->first();
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        } else {
            $data = $this->enrichMaker($data);
            return $this->jsonSingle($data);
        }

    }

}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Fitzwilliam Museum Collection Database API"
 * )
 */
class PeriodsController extends ApiController
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
                            'field' => 'lifecycle.creation.periods.admin.id',
                            'size' => 4000,
                            'order' => [
                                '_count' => $this->getSortParam($request),
                            ],
                        ],
                        'aggs' => [
                            'period' => [
                                'top_hits' => [
                                    'size' => 10,
                                    '_source' => [
                                        'include' => [
                                            'lifecycle.creation.periods.summary_title',
                                            'lifecycle.creation.periods.admin.id',
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
            $params['body']['query']['bool']['must'][] = ["multi_match" => [
                "fields" => "_generic_all_std",
                "query" => $request->query('q'),
                "operator" => "AND",
            ]
            ];
        }
        $response = $this->searchAndCache($params);
        $data = $this->parseTerminologyAggPeriods($response);
        if (empty($data)) {
            return $this->jsonError(404, $this->_notFound);
        }
        $items = $this->paginate($data, $this->getSize($request), LengthAwarePaginator::resolveCurrentPage());
        $items->setPath(route('api.periods.index'));
        if ($items->items()) {
            return $this->jsonAggGenerate($request, $items, $items->values(), count($response['aggregations']['records']['buckets']));
        } else {
            return $this->jsonError(404, $this->_notFound);
        }
    }

    /**
     * @param string $period
     * @return JsonResponse
     */
    public function show(string $period): JsonResponse
    {

        $params = [
            'index' => 'ciim',
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.id" => urlencode($period)
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
                'admin.id,admin.created,admin.modified,name,summary_title,description,equivalent,parent,related,identifier'
            ],
        ];
        $data = Collect($this->parse($this->searchAndCache($params)))->first();
        if (!empty($data)) {
            $data = $this->enrichPeriod($data);
            return $this->jsonSingle($data);
        } else {
            return $this->jsonError(400, $this->_notFound);
        }
    }

}

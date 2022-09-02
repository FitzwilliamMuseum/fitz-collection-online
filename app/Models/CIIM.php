<?php

namespace App\Models;

use App\FitzElastic\Elastic;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Mews\Purifier\Facades\Purifier;

class CIIM extends Model
{
    /**
     * @param Request $request
     * @return array|LengthAwarePaginator
     */
    #[ArrayShape(['records' => "\Illuminate\Pagination\LengthAwarePaginator", 'aggregations' => "mixed"])] public static function getSearchResults(Request $request): LengthAwarePaginator|array
    {
        $queryString = Purifier::clean($request->get('query'), array('HTML.Allowed' => ''));
        $from = ($request->get('page', 1) - 1) * 24;
        if (!is_null($request->get('operator'))) {
            $operator = $request->get('operator');
        } else {
            $operator = 'AND';
        }
        $params = [
            'index' => 'ciim',
            'size' => 24,
            'track_total_hits' => true,
            'from' => $from,
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "multi_match" => [
                                    "fields" => ["_generic_all_std", "accession_number^3"],
                                    "query" => $queryString,
                                    "operator" => $operator,
                                ],

                            ],

                        ],
                        "filter" =>
                            [
                                "term" => ["type.base" => 'object'],
                            ],

                    ]
                ],
            ],
        ];

        $params['body']['aggs'] = array(
            'material' => [
                'terms' =>
                    [
                        "field" => 'materials.reference.summary_title.keyword',
                        "size" => 10
                    ]
            ],
            'period' => [
                'terms' =>
                    [
                        "field" => 'lifecycle.creation.periods.summary_title.keyword',
                        "size" => 10
                    ]
            ],
            'object_type' => [
                'terms' =>
                    [
                        "field" => 'name.reference.summary_title.keyword',
                        "size" => 10
                    ]
            ],
            'maker' => [
                'terms' =>
                    [
                        "field" => 'lifecycle.creation.maker.summary_title.keyword',
                        "size" => 10
                    ]
            ],
            'department' => [
                'terms' =>
                    [
                        "field" => 'department.value.keyword',
                        "size" => 10
                    ]
            ]
        );
        // Add images filter
        if (!is_null($request->get('images'))) {
            $filter = array(
                "exists" => [
                    "field" => "multimedia"
                ]
            );
            $params['body']['query']['bool']['must'][] = [$filter];
        }
        // Maker filter
        if (!is_null($request->get('maker'))) {
            $filter = array(
                "term" => [
                    "lifecycle.creation.maker.summary_title.keyword" => $request->get('maker')
                ]
            );
            $params['body']['query']['bool']['must'][] = [$filter];
        }
        // Add iiif filter
        if (!is_null($request->get('iiif'))) {
            $filter = [
                "exists" => [
                    "field" => "multimedia.processed.zoom"
                ]
            ];
            $params['body']['query']['bool']['must'][] = [$filter];
        }
        //  Material filter
        if (!is_null($request->get('material'))) {
            $filter = [
                "term" => [
                    "materials.reference.summary_title.keyword" => $request->get('material')
                ]
            ];
            $params['body']['query']['bool']['must'][] = [$filter];
        }
        // Period filter
        if (!is_null($request->get('period'))) {
            $filter = [
                "term" => [
                    "lifecycle.creation.periods.summary_title.keyword" => $request->get('period')
                ]
            ];
            $params['body']['query']['bool']['must'][] = [$filter];
        }

        // Period filter
        if (!is_null($request->get('department'))) {
            $filter = [
                "term" => [
                    "department.value.keyword" => $request->get('department')
                ]
            ];
            $params['body']['query']['bool']['must'][] = [$filter];
        }

        // Accession filter
        if (!is_null($request->get('accession_number'))) {
            $filter = [
                "term" => [
                    "identifier.accession_number" => $request->get('accession_number')
                ]
            ];
            $params['body']['query']['bool']['must'][] = [$filter];
        }

        // Period filter
        if (!is_null($request->get('object_type'))) {
            $filter = [
                "term" => [
                    "name.reference.summary_title.keyword" => $request->get('object_type')
                ]
            ];
            $params['body']['query']['bool']['must'][] = [$filter];
        }

        $order = $request->get('sort');
        $sort = [
            "multimedia.admin.id" => [
                "order" => $order ?? 'asc',
                "missing" => '_last'
            ]
        ];
        $params['body']['sort'] = $sort;
        $response = self::getElastic()->setParams($params)->getSearch();
        $records =  self::paginate($response)->setPath($request->getBaseUrl());
        $aggregations = $response['aggregations'];
        return array('records' => $records, 'aggregations' => $aggregations);
    }

    /**
     * @return Elastic
     */
    #[Pure] public static function getElastic(): Elastic
    {
        return new Elastic();
    }

    /**
     * @param array|Collection $items
     * @param int $perPage
     * @param int|null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    public static function paginate(array|Collection $items, int $perPage = 24, int|null $page = NULL, array $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);
        $records = $items['hits']['hits'];
        return new LengthAwarePaginator($records, $items['hits']['total']['value'], $perPage, $page, $options);
    }
}

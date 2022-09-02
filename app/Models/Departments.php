<?php

namespace App\Models;

use App\FitzElastic\Elastic;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use JetBrains\PhpStorm\Pure;

class Departments extends Model
{

    /**
     * @return Elastic
     */
    #[Pure] public static function getElastic(): Elastic
    {
        return new Elastic();
    }

    /**
     *
     * @return array
     */
    public static function listDepartments(): array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                'size' => 0,
                'aggregations' => [
                    'department' => [
                        'terms' => [
                            'field' => 'department.value.keyword',
                            'size' => 10,
                        ],
                    ],
                ],
            ]
        ];
        return parent::searchAndCache($params);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function list(string $id): array
    {
        $params = [
            'index' => 'ciim',
            'track_total_hits' => true,
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
                                "term" => ["type.base" => 'object']
                            ]
                        ]
                    ]
                ]
            ],
        ];
        $response = self::getElastic()->setParams($params)->getSearch();
        return $response['hits']['hits'];
    }

    /**
     * @param Request $request
     * @param string $id
     * @return LengthAwarePaginator
     */
    public static function connected(Request $request, string $id): LengthAwarePaginator
    {
        $perPage = 24;
        $params = [
            'index' => 'ciim',
            'track_total_hits' => true,
            'size' => $perPage,
            'from' => ($request->get('page', 1) - 1) * $perPage,
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "department.value" => $id
                                ]
                            ],
                            [
                                "term" => ["type.base" => 'object']
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    [
                        "admin.modified" => [
                            "order" => "desc"
                        ]
                    ]
                ]
            ],

        ];

        $response = self::getElastic()->setParams($params)->getSearch();
        $paginate = new LengthAwarePaginator(
            $response['hits']['hits'],
            $response['hits']['total']['value'],
            $perPage,
            LengthAwarePaginator::resolveCurrentPage()
        );
        $paginate->setPath($request->getBaseUrl());
        return $paginate;
    }
}

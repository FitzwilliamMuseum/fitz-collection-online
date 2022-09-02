<?php

namespace App\Models;
use App\FitzElastic\Elastic;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use JetBrains\PhpStorm\Pure;

class Exhibitions extends Model
{

    private static int $_perPage = 24;

    /**
     * @return Elastic
     */
    #[Pure] public static function getElastic(): Elastic
    {
        return new Elastic();
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function listPaginated(Request $request): array
    {
        $params = [
            'index' => 'ciim',
            'size' => 50,
            'from' => parent::getFrom($request),
            'track_total_hits' => true,
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "term" => ["type.base" => 'exhibition']
                            ]
                        ]
                    ]
                ]
            ],
            '_source' => [
            ],
        ];
        $params['body']['sort'] = 'summary_title.keyword';
        return parent::searchAndCache($params);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function show(string $id): array
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
                                "term" => ["type.base" => 'exhibition']
                            ]
                        ]
                    ]
                ]
            ],
        ];
        $response = self::getElastic()->setParams($params)->getSearch();
        return Collect($response['hits']['hits'])->first();
    }

    /**
     * @param Request $request
     * @param string $id
     * @return LengthAwarePaginator
     */
    public static function connected(Request $request, string $id): LengthAwarePaginator
    {
        $paramsTerm = [
            'index' => 'ciim',
            'size' => self::$_perPage,
            'from' => ($request->get('page', 1) - 1) * self::$_perPage,
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "reference_links" => $id
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
        $response = self::getElastic()->setParams($paramsTerm)->getSearch();
        $paginate = new LengthAwarePaginator($response['hits']['hits'], $response['hits']['total']['value'], self::$_perPage, LengthAwarePaginator::resolveCurrentPage());
        $paginate->setPath($request->getBaseUrl());
        return $paginate;
    }
}

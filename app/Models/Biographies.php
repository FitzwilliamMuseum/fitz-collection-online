<?php

namespace App\Models;
use App\FitzElastic\Elastic;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use JetBrains\PhpStorm\Pure;

class Biographies extends Model
{

    /**
     * @return Elastic
     */
    #[Pure] public static function getElastic(): Elastic
    {
        return new Elastic();
    }

    /**
     * @param string $id
     * @return array
     */
    public static function count( string $id): array
    {
        $json = '{
          "query": {
            "match": {
              "reference_links" : "' . $id . '"
            }
          }
        }';
        $params = [
            'index' => 'ciim',
            'body' => $json
        ];
        return self::getElastic()->setParams($params)->getCount();

    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function list(string $id): array
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
                                "term" => ["type.base" => 'biography']
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
        $from = ($request->get('page', 1) - 1) * $perPage;

        $paramsTerm = [
            'index' => 'ciim',
            'size' => $perPage,
            'from' => $from,
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
                            ],
                            [
                                "exists" => ['field' => 'multimedia']
                            ],
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
        $response = self::getElastic()->setParams($paramsTerm)->getSearch();
        $number = $response['hits']['total']['value'];
        $records = $response['hits']['hits'];
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $paginate = new LengthAwarePaginator($records, $number, $perPage, $currentPage);
        $paginate->setPath($request->getBaseUrl());
        return $paginate;
    }
}

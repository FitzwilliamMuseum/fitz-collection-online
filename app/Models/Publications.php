<?php

namespace App\Models;
use App\FitzElastic\Elastic;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use JetBrains\PhpStorm\Pure;

class Publications extends Model
{

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
                                "term" => ["type.base" => 'publication']
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
                                "term" => [
                                    "type.base" => 'publication'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
        $response = self::getElastic()->setParams($params)->getSearch();
        if(!empty($response['hits']['hits'])) {
            return Collect($response['hits']['hits'])->first()['_source'];
        } else {
            return [];
        }

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
        $params = [
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
                            ]
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

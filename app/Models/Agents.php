<?php

namespace App\Models;

use App\FitzElastic\Elastic;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use JetBrains\PhpStorm\Pure;
use Mews\Purifier\Facades\Purifier;

class Agents extends Model
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
                                "term" => ["type.base" => 'agent']
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
            'track_total_hits' => true,
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.id" => Purifier::clean($id, array('HTML.Allowed' => ''))
                                ]
                            ],
                            [
                                "term" => [
                                    "type.base" => 'agent'
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
            abort(404);
        }
    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function findByUuid(string $id): array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.uuid" => Purifier::clean($id, array('HTML.Allowed' => ''))
                                ]
                            ],
                            [
                                "term" => [
                                    "type.base" => 'agent'
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
            abort(404);
        }
    }

    /**
     * @param Request $request
     * @param string $id
     * @return LengthAwarePaginator
     */
    public static function connected(Request $request, string $id): LengthAwarePaginator
    {
        $from = ($request->get('page', 1) - 1) * self::$_perPage;

        $params = [
            'index' => 'ciim',
            'size' => self::$_perPage,
            'track_total_hits' => true,
            'from' => $from,
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "reference_links" => Purifier::clean($id, array('HTML.Allowed' => ''))
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
        $paginate = new LengthAwarePaginator($response['hits']['hits'], $response['hits']['total']['value'], self::$_perPage, LengthAwarePaginator::resolveCurrentPage());
        $paginate->setPath($request->getBaseUrl());
        return $paginate;
    }

    public static function getMakerUsage(string $id)
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
                                    "lifecycle.creation.maker.admin.id" => Purifier::clean($id, array('HTML.Allowed' => ''))
                                ]
                            ],
                            [
                                "term" => [
                                    "type.base" => 'object'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
        $response = self::getElastic()->setParams($params)->getSearch();
        return $response['hits']['total']['value'];
    }

    public static function getAcquisitionUsage(string $id)
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
                                    "lifecycle.acquisition.agents.admin.id" => Purifier::clean($id, array('HTML.Allowed' => ''))
                                ]
                            ],
                            [
                                "term" => [
                                    "type.base" => 'object'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
        $response = self::getElastic()->setParams($params)->getSearch();
        return $response['hits']['total']['value'];
    }

    public static function getOwnerUsage(string $id)
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
                                    "owners.admin.id" => Purifier::clean($id, array('HTML.Allowed' => ''))
                                ]
                            ],
                            [
                                "term" => [
                                    "type.base" => 'object'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
        $response = self::getElastic()->setParams($params)->getSearch();
        return $response['hits']['total']['value'];
    }
}

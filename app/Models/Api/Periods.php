<?php

namespace App\Models\Api;


use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class Periods extends Model
{

    private static array $_fields = array(
        'admin.id','admin.created','admin.modified',
        'name','summary_title','description',
        'equivalent','parent','related','identifier'
    );

    private static array $_mandatory = array('admin.id','admin.created','admin.modified','summary_title','name');
    /**
     * @param Request $request
     * @return array
     */
    public static function list(Request $request): array
    {
        $params = [
            'index' => 'ciim',
            'track_total_hits' => true,
            'size' => self::getSize($request),
            'from' => self::getFrom($request),
            'body' => [
                'aggregations' => [
                    'records' => [
                        'terms' => [
                            'field' => 'lifecycle.creation.periods.admin.id',
                            'size' => 4000,
                            'order' => [
                                '_count' =>  self::getSortParam($request),
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
        $createdBefore = self::createdBeforeParam($request);
        $createdAfter = self::createdAfterParam($request);
        $modifiedBefore = self::modifiedBeforeParam($request);
        $modifiedAfter = self::modifiedAfterParam($request);
        $combined = array_merge_recursive(
            $params, self::createQueryPeriod($request), $createdBefore,
            $createdAfter, $modifiedBefore, $modifiedAfter
        );
        return self::searchAndCache($combined);
    }

    /**
     * @param Request $request
     * @param string $period
     * @return array|NULL
     */
    public static function show(Request $request, string $period): ?array
    {
        $params = [
            'index' => 'ciim',
            'body' => [
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [
                                    "admin.id" => Purifier::clean($period, array('HTML.Allowed' => ''))
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
                self::getSourceFields($request, self::$_fields, self::$_mandatory)
            ],
        ];
        return Collect(self::parse(self::searchAndCache($params)))->first();
    }


    /**
     * @param Request $request
     * @return array
     */
    public static function listNumbers(Request $request): array
    {
        $params = [
            'index' => 'ciim',
            'track_total_hits' => true,
            'size' => self::getSizeID($request),
            'from' => self::getFromID($request),
            'body' => [
                'aggregations' => [
                    'records' => [
                        'terms' => [
                            'field' => 'lifecycle.creation.periods.admin.id',
                            'size' => 4000,
                            'order' => [
                                '_count' =>  self::getSortParam($request),
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
        $createdBefore = self::createdBeforeParam($request);
        $createdAfter = self::createdAfterParam($request);
        $modifiedBefore = self::modifiedBeforeParam($request);
        $modifiedAfter = self::modifiedAfterParam($request);
        $combined = array_merge_recursive(
            $params, self::createQueryPeriod($request), $createdBefore,
            $createdAfter, $modifiedBefore, $modifiedAfter
        );
        return self::searchAndCache($combined);
    }
}

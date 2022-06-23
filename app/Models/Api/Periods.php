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
        $params = self::createQuery($request, $params);
        return self::searchAndCache($params);
    }

    /**
     * @param Request $request
     * @param string $period
     * @return array
     */
    public static function show(Request $request, string $period): array
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
}
